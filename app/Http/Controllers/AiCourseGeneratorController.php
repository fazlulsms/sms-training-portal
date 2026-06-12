<?php

namespace App\Http\Controllers;

use App\Models\AiPromptTemplate;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\ElearningLesson;
use App\Models\ElearningQuiz;
use App\Models\ElearningQuizQuestion;
use App\Models\LessonBlock;
use App\Services\OpenAIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class AiCourseGeneratorController extends Controller
{
    private const TEMPLATE_CODE  = 'course_generator_json_v1';
    private const SESSION_KEY    = 'ai_course_draft';

    public function __construct(private OpenAIService $ai) {}

    private function guardSuperAdmin(): void
    {
        if (! auth()->user()?->isSuperAdmin()) {
            abort(403, 'AI Course Generator is restricted to Super Admins.');
        }
    }

    // ── Step 1: Generate ─────────────────────────────────────────
    // POST /admin/ai/course-generator/generate

    public function generate(Request $request)
    {
        $this->guardSuperAdmin();

        $data = $request->validate([
            'course_name'     => 'required|string|max:255',
            'duration'        => 'required|string|max:100',
            'language'        => 'required|string|max:50',
            'target_audience' => 'required|string|max:500',
            'industry'        => 'required|string|max:100',
            'learning_level'  => 'required|in:Beginner,Intermediate,Advanced,Expert',
            'standard'        => 'nullable|string|max:200',
            'instructions'    => 'nullable|string|max:1000',
            'course_type'     => 'required|in:ilt,elearning',
            'generation_mode' => 'nullable|in:structure,complete',
        ]);

        $data['generation_mode'] = $data['generation_mode'] ?? 'structure';

        $template = AiPromptTemplate::where('template_code', self::TEMPLATE_CODE)
            ->where('is_active', true)
            ->first();

        if (! $template) {
            return response()->json([
                'success' => false,
                'error'   => 'AI template "' . self::TEMPLATE_CODE . '" not found or inactive. Go to AI → Prompt Templates and ensure it exists and is active.',
            ]);
        }

        $courseTypeLabel = $data['course_type'] === 'elearning'
            ? 'eLearning (self-paced online)'
            : 'Instructor-Led Training (ILT)';

        $input  = "Course Name: {$data['course_name']}\n";
        $input .= "Course Type: {$courseTypeLabel}\n";
        $input .= "Duration: {$data['duration']}\n";
        $input .= "Language: {$data['language']}\n";
        $input .= "Target Audience: {$data['target_audience']}\n";
        $input .= "Industry: {$data['industry']}\n";
        $input .= "Learning Level: {$data['learning_level']}\n";

        if (! empty($data['standard'])) {
            $input .= "Relevant Standard/Framework: {$data['standard']}\n";
        }
        if (! empty($data['instructions'])) {
            $input .= "Additional Instructions: {$data['instructions']}\n";
        }

        $result = $this->ai->generateFromTemplate($template, $input, auth()->id());

        if (! $result['success']) {
            return response()->json([
                'success' => false,
                'error'   => $result['error'] ?? 'AI generation failed. Please try again.',
            ]);
        }

        $raw    = trim($result['text'] ?? '');
        $raw    = preg_replace('/^```(?:json)?\s*/i', '', $raw);
        $raw    = preg_replace('/\s*```$/', '', $raw);
        $raw    = trim($raw);
        $parsed = json_decode($raw, true);

        if (json_last_error() !== JSON_ERROR_NONE || ! is_array($parsed)) {
            return response()->json([
                'success' => false,
                'error'   => 'AI returned an unexpected format. Please try regenerating. (Parse error: ' . json_last_error_msg() . ')',
                'raw'     => $raw,
            ]);
        }

        $required = ['course_description', 'learning_objectives', 'modules', 'assessment_plan', 'public_summary', 'seo_title', 'seo_meta_description'];
        foreach ($required as $key) {
            if (empty($parsed[$key])) {
                return response()->json([
                    'success' => false,
                    'error'   => "AI response is missing the '{$key}' field. Please try regenerating.",
                ]);
            }
        }

        session()->put(self::SESSION_KEY, [
            'form_data'   => $data,
            'ai_output'   => $parsed,
            'ai_usage'    => $result['usage'] ?? [],
            'template_id' => $template->id,
        ]);

        return response()->json([
            'success'      => true,
            'redirect_url' => route('ai.course-generator.preview'),
        ]);
    }

    // ── Step 2: Preview ──────────────────────────────────────────
    // GET /admin/ai/course-generator/preview

    public function preview(Request $request)
    {
        $this->guardSuperAdmin();

        $draft = session(self::SESSION_KEY);

        if (! $draft) {
            return redirect()->route('admin.courses.create')
                ->with('warning', 'No AI draft found. Please generate a course first.');
        }

        return view('ai.course-generator.preview', [
            'draft'      => $draft,
            'formData'   => $draft['form_data'],
            'aiOutput'   => $draft['ai_output'],
            'aiUsage'    => $draft['ai_usage'] ?? [],
            'courseType' => $draft['form_data']['course_type'],
        ]);
    }

    // ── Step 3: Save ─────────────────────────────────────────────
    // POST /admin/ai/course-generator/save

    public function save(Request $request)
    {
        $this->guardSuperAdmin();

        $draft = session(self::SESSION_KEY);

        if (! $draft) {
            return redirect()->back()->with('error', 'Session expired. Please generate again.');
        }

        $formData        = $draft['form_data'];
        $aiOutput        = $draft['ai_output'];
        $courseType      = $formData['course_type'];
        $generationMode  = $formData['generation_mode'] ?? 'structure';

        if (empty($formData['course_name'])) {
            return redirect()->back()->with('error', 'Course name is missing from the draft. Please regenerate.');
        }
        if (empty($formData['language'])) {
            return redirect()->back()->with('error', 'Language is missing from the draft. Please regenerate.');
        }

        // ── Existing editable fields ──────────────────────────────
        $editedDescription  = $request->input('course_description',   $aiOutput['course_description'] ?? '');
        $editedObjectives   = $request->input('learning_objectives',   is_array($aiOutput['learning_objectives'] ?? '') ? implode("\n", $aiOutput['learning_objectives']) : ($aiOutput['learning_objectives'] ?? ''));
        $editedAudience     = $request->input('target_audience',       $aiOutput['target_audience'] ?? '');
        $editedPrereqs      = $request->input('prerequisites',         is_array($aiOutput['prerequisites'] ?? '') ? implode("\n", $aiOutput['prerequisites']) : ($aiOutput['prerequisites'] ?? ''));
        $editedAssessment   = $request->input('assessment_plan',       $aiOutput['assessment_plan'] ?? '');
        $editedCertCriteria = $request->input('certificate_criteria',  $aiOutput['certificate_criteria'] ?? '');
        $editedSummary      = $request->input('public_summary',        $aiOutput['public_summary'] ?? '');
        $editedSeoTitle     = $request->input('seo_title',             $aiOutput['seo_title'] ?? '');
        $editedSeoDesc      = $request->input('seo_meta_description',  $aiOutput['seo_meta_description'] ?? '');

        // ── New editable fields ───────────────────────────────────
        $editedCourseCode   = $request->input('course_code',              $aiOutput['course_code'] ?? '');
        $editedCategoryText = $request->input('category_text',            $aiOutput['category_text'] ?? '');
        $editedCpdHours     = $request->input('cpd_hours',                $aiOutput['cpd_hours'] ?? null);
        $editedCertInfo     = $request->input('certification_information', $aiOutput['certification_information'] ?? '');
        $editedSeoKeywords  = $request->input('seo_keywords',             $aiOutput['seo_keywords'] ?? '');
        $editedFaqJson      = $request->input('faq_json',                 '');
        $editedTargetMarket = $request->input('target_market',            '');

        // Default target_market textarea from AI array output
        if (empty($editedTargetMarket) && ! empty($aiOutput['target_market'])) {
            $tm = $aiOutput['target_market'];
            $editedTargetMarket = is_array($tm) ? implode("\n", $tm) : $tm;
        }

        // Default faq_json from AI array output
        if (empty($editedFaqJson) && ! empty($aiOutput['faq'])) {
            $editedFaqJson = json_encode($aiOutput['faq'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }

        // ── CPD hours: derive fallback from duration if AI didn't provide ──
        if (empty($editedCpdHours) && ! empty($formData['duration'])) {
            if (preg_match('/(\d+)\s*(hour|hr)/i', $formData['duration'], $m)) {
                $editedCpdHours = (int) $m[1];
            } elseif (preg_match('/(\d+)\s*day/i', $formData['duration'], $m)) {
                $editedCpdHours = (int) $m[1] * 8;
            }
        }

        // ── Category auto-match ───────────────────────────────────
        $matchedCategoryId = null;
        if (! empty($editedCategoryText)) {
            try {
                $matched = CourseCategory::where('name', $editedCategoryText)
                    ->orWhere('name', 'LIKE', '%' . $editedCategoryText . '%')
                    ->first();
                $matchedCategoryId = $matched?->id;
            } catch (\Throwable $e) {
                // Table may not exist in all environments
            }
        }

        // ── Build course outline ──────────────────────────────────
        $outlineLines = [];
        foreach ($aiOutput['modules'] ?? [] as $module) {
            $outlineLines[] = $module['title'] ?? '';
            foreach ($module['lessons'] ?? [] as $lesson) {
                $outlineLines[] = '  • ' . ($lesson['title'] ?? '');
            }
        }
        $courseOutline = implode("\n", $outlineLines);

        try {
            DB::transaction(function () use (
                $formData, $aiOutput, $courseType,
                $editedDescription, $editedObjectives, $editedAudience, $editedPrereqs,
                $editedAssessment, $editedCertCriteria, $editedSummary,
                $editedSeoTitle, $editedSeoDesc, $courseOutline,
                $editedCourseCode, $editedCategoryText, $editedCpdHours, $matchedCategoryId,
                $editedCertInfo, $editedSeoKeywords, $editedFaqJson, $editedTargetMarket,
                &$course
            ) {
                $course = Course::create([
                    // ── Identity ──────────────────────────────────
                    'name'                => $formData['course_name'],
                    'code'                => $editedCourseCode ?: null,
                    'status'              => 0,
                    'course_type'         => $courseType === 'elearning' ? 'elearning' : 'manual',
                    'delivery_type'       => $courseType === 'elearning' ? 'eLearning' : 'Instructor-Led',
                    'language'            => $formData['language'] ?? 'English',
                    'duration'            => $formData['duration'],
                    // ── Category ──────────────────────────────────
                    'category'            => $editedCategoryText ?: null,
                    'category_id'         => $matchedCategoryId,
                    'cpd_hours'           => $editedCpdHours ? (int) $editedCpdHours : null,
                    // ── Descriptions ──────────────────────────────
                    'full_description'    => $editedDescription,
                    'short_description'   => Str::limit(strip_tags($editedDescription), 200),
                    'description'         => $editedSummary,
                    'learning_objectives' => $editedObjectives,
                    // ── Audience ──────────────────────────────────
                    'who_should_attend'   => $editedTargetMarket ?: $editedAudience,
                    'prerequisites'       => $editedPrereqs,
                    // ── Structure ─────────────────────────────────
                    'course_outline'      => $courseOutline,
                    // ── Assessment & Certification ─────────────────
                    'certification_info'  => $editedCertInfo ?: $editedCertCriteria,
                    // ── Public / SEO ──────────────────────────────
                    'faq'                 => $editedFaqJson ?: null,
                    'seo_title'           => $editedSeoTitle,
                    'seo_description'     => $editedSeoDesc,
                    'seo_keywords'        => $editedSeoKeywords ?: null,
                    // ── Flags ─────────────────────────────────────
                    'is_public'           => false,
                    'is_featured'         => false,
                    'ai_generated'        => true,
                    'ai_course_structure' => $aiOutput,
                ]);

                if ($courseType === 'elearning') {
                    $lessonOrder = 1;
                    foreach ($aiOutput['modules'] ?? [] as $module) {
                        foreach ($module['lessons'] ?? [] as $lessonData) {
                            ElearningLesson::create([
                                'course_id'           => $course->id,
                                'title'               => $lessonData['title'] ?? 'Untitled Lesson',
                                'short_description'   => $lessonData['description'] ?? null,
                                'learning_objectives' => isset($lessonData['learning_objectives'])
                                    ? (is_array($lessonData['learning_objectives'])
                                        ? implode("\n", $lessonData['learning_objectives'])
                                        : $lessonData['learning_objectives'])
                                    : null,
                                'lesson_order'        => $lessonOrder++,
                                'status'              => 'draft',
                                'lesson_type'         => 'mixed',
                                'completion_rule'     => 'manual',
                            ]);
                        }
                    }
                }
            });

            session()->forget(self::SESSION_KEY);

            // Mode B — hand off to progress page; AJAX drives per-lesson generation
            if ($courseType === 'elearning' && $generationMode === 'complete') {
                $contentLevel = match ($formData['learning_level']) {
                    'Beginner' => 'Awareness',
                    'Advanced' => 'Advanced',
                    default    => 'Professional',
                };
                return redirect()->route('ai.course-generator.progress', [
                    'course' => $course->id,
                    'level'  => $contentLevel,
                ]);
            }

            // Mode A — structure only, go straight to editor
            $successMsg = '✨ AI-generated course "' . $formData['course_name'] . '" saved as draft. Add content to each lesson.';
            $editUrl    = $courseType === 'elearning'
                ? route('elearning.courses.edit', $course->id)
                : url('/admin/courses/edit/' . $course->id);

            return redirect($editUrl)->with('success', $successMsg);

        } catch (\Throwable $e) {
            return redirect()->back()
                ->with('error', 'Failed to save course: ' . $e->getMessage() . '. Please try again.');
        }
    }

    // ── Mode B: Progress Page ────────────────────────────────────
    // GET /admin/ai/course-generator/{course}/progress?level=Professional

    public function generationProgress(Request $request, Course $course)
    {
        $this->guardSuperAdmin();

        $level = $request->input('level', 'Professional');
        if (!in_array($level, ['Awareness', 'Professional', 'Advanced'])) {
            $level = 'Professional';
        }

        $lessons = ElearningLesson::where('course_id', $course->id)
            ->orderBy('lesson_order')
            ->get(['id', 'title', 'lesson_order']);

        if ($lessons->isEmpty()) {
            return redirect()->route('elearning.courses.edit', $course->id)
                ->with('warning', 'No lessons found for this course.');
        }

        // Build module → lessons mapping from stored AI structure
        $aiStructure = $course->ai_course_structure ?? [];
        $modules     = [];
        $lessonIdx   = 0;

        if (!empty($aiStructure['modules'])) {
            foreach ($aiStructure['modules'] as $modIndex => $moduleData) {
                $count         = count($moduleData['lessons'] ?? []);
                $moduleLessons = [];
                for ($i = 0; $i < $count; $i++) {
                    if ($lessons->has($lessonIdx)) {
                        $moduleLessons[] = $lessons->values()->get($lessonIdx);
                        $lessonIdx++;
                    }
                }
                if (!empty($moduleLessons)) {
                    $modules[] = [
                        'index'   => $modIndex + 1,
                        'title'   => $moduleData['title'] ?? 'Module ' . ($modIndex + 1),
                        'lessons' => $moduleLessons,
                    ];
                }
            }
        }

        if (empty($modules)) {
            $modules = [['index' => 1, 'title' => 'Course Lessons', 'lessons' => $lessons->all()]];
        }

        return view('ai.course-generator.generation-progress', compact(
            'course', 'lessons', 'level', 'modules'
        ));
    }

    // ── Mode B: Generate One Lesson (AJAX) ───────────────────────
    // POST /admin/ai/course-generator/{course}/generate-next

    public function generateNext(Request $request, Course $course)
    {
        $this->guardSuperAdmin();

        $lessonId = (int) $request->input('lesson_id');
        $level    = $request->input('level', 'Professional');

        if (!in_array($level, ['Awareness', 'Professional', 'Advanced'])) {
            $level = 'Professional';
        }

        $lesson = ElearningLesson::where('course_id', $course->id)
            ->where('id', $lessonId)
            ->first();

        if (!$lesson) {
            return response()->json(['success' => false, 'error' => 'Lesson not found'], 404);
        }

        $count = AiLessonContentController::generateAndSaveBlocks($course, $lesson, $level);

        return response()->json([
            'success'        => $count > 0,
            'lesson_id'      => $lesson->id,
            'lesson_title'   => $lesson->title,
            'blocks_created' => $count,
        ]);
    }

    // ── Mode B: Generate Module Quiz (AJAX) ──────────────────────
    // POST /admin/ai/course-generator/{course}/generate-module-quiz

    public function generateModuleQuiz(Request $request, Course $course)
    {
        $this->guardSuperAdmin();

        if (!config('ai.enabled', false)) {
            return response()->json(['success' => false, 'error' => 'AI disabled'], 403);
        }

        $moduleTitle   = $request->input('module_title', 'Module');
        $moduleIndex   = (int) $request->input('module_index', 1);
        $lessonIds     = $request->input('lesson_ids', []);
        $level         = $request->input('level', 'Professional');

        if (!in_array($level, ['Awareness', 'Professional', 'Advanced'])) {
            $level = 'Professional';
        }

        // Collect lesson summaries to ground the quiz in real content
        $lessonSummaries = ElearningLesson::where('course_id', $course->id)
            ->whereIn('id', $lessonIds)
            ->orderBy('lesson_order')
            ->get(['title', 'learning_objectives'])
            ->map(fn($l) => "- {$l->title}" . ($l->learning_objectives ? ": {$l->learning_objectives}" : ''))
            ->implode("\n");

        $questionCount = match ($level) {
            'Awareness' => 3,
            'Advanced'  => 5,
            default     => 4,
        };

        $userPrompt = <<<USR
Generate {$questionCount} multiple-choice quiz questions for this eLearning module.

Course: {$course->name}
Module {$moduleIndex}: {$moduleTitle}
Level: {$level}

Lessons covered:
{$lessonSummaries}

Return a JSON object:
{
  "questions": [
    {
      "question_text": "Clear question testing understanding of the lesson content?",
      "options": {
        "a": "Option A text",
        "b": "Option B text",
        "c": "Option C text",
        "d": "Option D text"
      },
      "correct_answer": "a",
      "explanation": "Why A is correct, with reference to the lesson."
    }
  ]
}

Rules:
- Questions must be answerable from the lesson content above
- One clearly correct answer per question
- Make distractors plausible but wrong
- correct_answer must be "a", "b", "c", or "d"
- Include one true/false question (use "a": "True", "b": "False", "c": "Sometimes", "d": "Never")
USR;

        try {
            $ai     = app(OpenAIService::class);
            $fullPrompt = "ROLE: You are an eLearning assessment designer. Output ONLY valid JSON — no markdown fences, no prose.\n\n" . $userPrompt;
            $result = $ai->generateText($fullPrompt, 'module_quiz', auth()->id(), 2000);

            if (!$result['success']) {
                return response()->json(['success' => false, 'error' => $result['error'] ?? 'AI failed']);
            }

            $raw = trim($result['text'] ?? '');
            $raw = preg_replace('/^```json\s*/i', '', $raw);
            $raw = preg_replace('/```\s*$/',       '', trim($raw));

            $decoded = json_decode($raw, true);
            if (!is_array($decoded) || empty($decoded['questions'])) {
                return response()->json(['success' => false, 'error' => 'AI returned invalid format']);
            }

            // Create a "Module Quiz" lesson
            $quizLesson = ElearningLesson::create([
                'course_id'       => $course->id,
                'title'           => "Module {$moduleIndex} Knowledge Check: {$moduleTitle}",
                'short_description' => "Test your understanding of {$moduleTitle}.",
                'lesson_order'    => ElearningLesson::where('course_id', $course->id)->max('lesson_order') + 1,
                'status'          => 'draft',
                'lesson_type'     => 'assessment',
                'completion_rule' => 'pass_quiz',
            ]);

            // Create ElearningQuiz attached to the quiz lesson
            $quiz = ElearningQuiz::create([
                'lesson_id'   => $quizLesson->id,
                'title'       => "Module {$moduleIndex} Knowledge Check",
                'description' => "Test your understanding of key concepts from {$moduleTitle}.",
                'pass_mark'   => 70,
                'max_attempt' => 2,
                'status'      => 'active',
            ]);

            $created = 0;
            foreach ($decoded['questions'] as $q) {
                if (empty($q['question_text']) || empty($q['options'])) continue;
                ElearningQuizQuestion::create([
                    'quiz_id'       => $quiz->id,
                    'question_text' => $q['question_text'],
                    'question_type' => 'mcq',
                    'option_a'      => $q['options']['a'] ?? '',
                    'option_b'      => $q['options']['b'] ?? '',
                    'option_c'      => $q['options']['c'] ?? '',
                    'option_d'      => $q['options']['d'] ?? '',
                    'correct_answer' => strtolower($q['correct_answer'] ?? 'a'),
                    'marks'         => 1,
                    'status'        => 'active',
                ]);
                $created++;
            }

            return response()->json([
                'success'          => true,
                'module_title'     => $moduleTitle,
                'quiz_lesson_id'   => $quizLesson->id,
                'questions_created' => $created,
            ]);

        } catch (\Throwable $e) {
            Log::error('AiCourseGenerator: module quiz failed', [
                'course_id' => $course->id,
                'module'    => $moduleTitle,
                'error'     => $e->getMessage(),
            ]);
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    // ── Cancel: clear session ────────────────────────────────────
    // POST /admin/ai/course-generator/cancel

    public function cancel(Request $request)
    {
        $this->guardSuperAdmin();
        session()->forget(self::SESSION_KEY);

        $type = $request->input('course_type', 'ilt');
        return redirect(
            $type === 'elearning'
                ? route('elearning.courses.create')
                : url('/admin/courses/create')
        );
    }
}
