<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateModeBCourseJob;
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
                    $courseName  = $formData['course_name'];

                    // Course Introduction shell — content generated by background job
                    ElearningLesson::create([
                        'course_id'         => $course->id,
                        'title'             => 'Course Introduction: ' . $courseName,
                        'short_description' => 'Welcome to this course. An overview of what you will learn and how to get the most from it.',
                        'lesson_order'      => $lessonOrder++,
                        'status'            => 'draft',
                        'lesson_type'       => 'mixed',
                        'completion_rule'   => 'manual',
                    ]);

                    foreach ($aiOutput['modules'] ?? [] as $module) {
                        foreach ($module['lessons'] ?? [] as $lessonData) {
                            $rawObjs = $lessonData['learning_objectives'] ?? null;
                            ElearningLesson::create([
                                'course_id'           => $course->id,
                                'title'               => $lessonData['title'] ?? 'Untitled Lesson',
                                'short_description'   => $lessonData['description'] ?? null,
                                'learning_objectives' => $rawObjs
                                    ? (is_array($rawObjs) ? implode("\n", $rawObjs) : $rawObjs)
                                    : null,
                                'duration_minutes'    => isset($lessonData['duration_minutes'])
                                    ? (int) $lessonData['duration_minutes']
                                    : null,
                                'lesson_order'        => $lessonOrder++,
                                'status'              => 'draft',
                                'lesson_type'         => 'mixed',
                                'completion_rule'     => 'manual',
                            ]);
                        }
                    }

                    // Course Conclusion shell — content generated by background job
                    ElearningLesson::create([
                        'course_id'         => $course->id,
                        'title'             => 'Course Conclusion: ' . $courseName,
                        'short_description' => 'Congratulations on completing this course. A summary of your achievement and next steps.',
                        'lesson_order'      => $lessonOrder++,
                        'status'            => 'draft',
                        'lesson_type'       => 'mixed',
                        'completion_rule'   => 'manual',
                    ]);
                }
            });

            session()->forget(self::SESSION_KEY);

            // Mode B — dispatch background job; admin can close browser
            if ($courseType === 'elearning' && $generationMode === 'complete') {
                $contentLevel = match ($formData['learning_level']) {
                    'Beginner' => 'Awareness',
                    'Advanced' => 'Advanced',
                    default    => 'Professional',
                };

                $totalLessons = ElearningLesson::where('course_id', $course->id)
                    ->where('lesson_type', '!=', 'assessment')
                    ->count();

                $course->update([
                    'gen_status'     => 'pending',
                    'gen_started_at' => now(),
                    'gen_progress'   => [
                        'phase'            => 'queued',
                        'current_step'     => 'Course generation queued — will start shortly…',
                        'lessons_done'     => 0,
                        'total_lessons'    => $totalLessons,
                        'blocks_generated' => 0,
                        'quizzes_done'     => 0,
                        'total_modules'    => count($aiOutput['modules'] ?? []),
                        'final_questions'  => 0,
                    ],
                ]);

                GenerateModeBCourseJob::dispatch($course->id, $contentLevel);

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

    // ── Mode B: Progress Page (polling UI) ──────────────────────
    // GET /admin/ai/course-generator/{course}/progress?level=Professional

    public function generationProgress(Request $request, Course $course)
    {
        $this->guardSuperAdmin();

        $level = $request->input('level', 'Professional');
        if (!in_array($level, ['Awareness', 'Professional', 'Advanced'])) {
            $level = 'Professional';
        }

        $editUrl = route('elearning.courses.edit', $course->id);

        return view('ai.course-generator.generation-progress', compact('course', 'level', 'editUrl'));
    }

    // ── Mode B: Generation Status (JSON polling) ─────────────────
    // GET /admin/ai/course-generator/{course}/generation-status

    public function generationStatus(Request $request, Course $course)
    {
        $this->guardSuperAdmin();

        $course->refresh();

        return response()->json([
            'gen_status'   => $course->gen_status   ?? 'none',
            'gen_progress' => $course->gen_progress  ?? [],
            'course_id'    => $course->id,
            'edit_url'     => route('elearning.courses.edit', $course->id),
        ]);
    }

    // ── Mode B: Generate One Lesson (AJAX) ───────────────────────
    // POST /admin/ai/course-generator/{course}/generate-next

    public function generateNext(Request $request, Course $course)
    {
        $this->guardSuperAdmin();

        $lessonId     = (int) $request->input('lesson_id');
        $level        = $request->input('level', 'Professional');
        $lessonType   = $request->input('lesson_type', 'concept');
        $lessonNumber = max(1, (int) $request->input('lesson_number', 1));
        $totalLessons = max(1, (int) $request->input('total_lessons', 1));

        if (!in_array($level, ['Awareness', 'Professional', 'Advanced'])) {
            $level = 'Professional';
        }

        $validTypes = ['concept', 'process', 'skill', 'compliance', 'case_study', 'awareness', 'technical'];
        if (!in_array($lessonType, $validTypes)) {
            $lessonType = 'concept';
        }

        $lesson = ElearningLesson::where('course_id', $course->id)
            ->where('id', $lessonId)
            ->first();

        if (!$lesson) {
            return response()->json(['success' => false, 'error' => 'Lesson not found'], 404);
        }

        $count = AiLessonContentController::generateAndSaveBlocks(
            $course, $lesson, $level, $lessonType, $lessonNumber, $totalLessons
        );

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
      "explanation": "Why A is correct, with specific reference to the lesson content."
    }
  ]
}

Rules:
- Questions must be directly answerable from the lesson content above
- One clearly correct answer per question
- Make distractors plausible but wrong
- correct_answer must be "a", "b", "c", or "d"
- Include one true/false question (use "a": "True", "b": "False", "c": "Sometimes", "d": "Not applicable")
- Every question must include an explanation field
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
                    'quiz_id'        => $quiz->id,
                    'question_text'  => $q['question_text'],
                    'question_type'  => 'mcq',
                    'option_a'       => $q['options']['a'] ?? '',
                    'option_b'       => $q['options']['b'] ?? '',
                    'option_c'       => $q['options']['c'] ?? '',
                    'option_d'       => $q['options']['d'] ?? '',
                    'correct_answer' => strtolower($q['correct_answer'] ?? 'a'),
                    'explanation'    => $q['explanation'] ?? null,
                    'difficulty'     => 'medium',
                    'module_index'   => $moduleIndex,
                    'marks'          => 1,
                    'status'         => 'active',
                ]);
                $created++;
            }

            return response()->json([
                'success'           => true,
                'module_title'      => $moduleTitle,
                'quiz_lesson_id'    => $quizLesson->id,
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

    // ── Mode B: Generate Final Assessment (AJAX) ─────────────────
    // POST /admin/ai/course-generator/{course}/generate-final-assessment

    public function generateFinalAssessment(Request $request, Course $course)
    {
        $this->guardSuperAdmin();

        if (!config('ai.enabled', false)) {
            return response()->json(['success' => false, 'error' => 'AI disabled'], 403);
        }

        $level = $request->input('level', 'Professional');
        if (!in_array($level, ['Awareness', 'Professional', 'Advanced'])) {
            $level = 'Professional';
        }

        $questionCount = match ($level) {
            'Awareness' => 15,
            'Advanced'  => 25,
            default     => 20,
        };

        // Collect module summaries from AI structure
        $aiStructure = $course->ai_course_structure ?? [];
        $moduleSummaries = [];
        foreach ($aiStructure['modules'] ?? [] as $idx => $mod) {
            $lessonTitles = collect($mod['lessons'] ?? [])->pluck('title')->implode('; ');
            $moduleSummaries[] = 'Module ' . ($idx + 1) . ': ' . ($mod['title'] ?? '') . ' — Lessons: ' . $lessonTitles;
        }
        $modulesText = implode("\n", $moduleSummaries) ?: 'All course modules';

        // Collect course learning objectives
        $objectives = collect(preg_split('/[\n,]+/', $course->learning_objectives ?? ''))
            ->map(fn($s) => trim($s))
            ->filter()
            ->implode('; ') ?: 'Not specified';

        $mcqCount      = (int) round($questionCount * 0.60);
        $tfCount       = (int) round($questionCount * 0.25);
        $scenarioCount = $questionCount - $mcqCount - $tfCount;

        $userPrompt = <<<USR
Generate a comprehensive final course assessment with EXACTLY {$questionCount} questions.

Course: {$course->name}
Level: {$level}
Course Learning Objectives: {$objectives}

Modules Covered:
{$modulesText}

Question Distribution (EXACTLY):
- {$mcqCount} Multiple Choice (MCQ) — 4 options (a, b, c, d), one correct
- {$tfCount} True/False — options a=True, b=False
- {$scenarioCount} Scenario-Based — realistic workplace scenario followed by 4 options

For each question, include:
- question_text: Clear, specific question from the course content
- question_type: "mcq" | "truefalse" | "scenario"
- difficulty: "easy" | "medium" | "hard" (distribute evenly across the assessment)
- module_index: integer (1-based) indicating which module this question covers
- options: {"a": "...", "b": "...", "c": "...", "d": "..."} (truefalse uses a=True, b=False only)
- correct_answer: "a" | "b" | "c" | "d"
- explanation: 1–2 sentences explaining why the answer is correct, with reference to specific course content

RULES:
- Questions must cover ALL modules proportionally
- Every question must be answerable from the course content above
- One clearly correct answer per question; make distractors plausible but wrong
- Scenario questions: 2–3 sentence workplace situation followed by "What should you do?"
- Do NOT repeat questions from module quizzes; focus on synthesis and application
- Difficulty distribution: ~30% easy, ~50% medium, ~20% hard

Return ONLY a valid JSON object (no markdown, no code fences):
{
  "questions": [
    {
      "question_text": "...",
      "question_type": "mcq|truefalse|scenario",
      "difficulty": "easy|medium|hard",
      "module_index": 1,
      "options": {"a": "...", "b": "...", "c": "...", "d": "..."},
      "correct_answer": "a",
      "explanation": "..."
    }
  ]
}
USR;

        try {
            $ai         = app(OpenAIService::class);
            $sysPrompt  = "ROLE: You are an expert eLearning assessment designer. Output ONLY valid JSON — no markdown fences, no prose before or after the JSON.\n\n";
            $result     = $ai->generateText($sysPrompt . $userPrompt, 'final_assessment', auth()->id(), 5000);

            if (!$result['success']) {
                return response()->json(['success' => false, 'error' => $result['error'] ?? 'AI failed']);
            }

            $raw = trim($result['text'] ?? '');
            $raw = preg_replace('/^```json\s*/i', '', $raw);
            $raw = preg_replace('/```\s*$/', '', trim($raw));

            $decoded = json_decode($raw, true);
            if (!is_array($decoded) || empty($decoded['questions'])) {
                return response()->json(['success' => false, 'error' => 'AI returned invalid format for final assessment']);
            }

            // Create Final Assessment lesson
            $finalLesson = ElearningLesson::create([
                'course_id'              => $course->id,
                'title'                  => 'Final Course Assessment: ' . $course->name,
                'short_description'      => 'Comprehensive final assessment covering all modules. Pass mark: 70%. Maximum 2 attempts.',
                'lesson_order'           => ElearningLesson::where('course_id', $course->id)->max('lesson_order') + 1,
                'status'                 => 'draft',
                'lesson_type'            => 'assessment',
                'completion_rule'        => 'pass_quiz',
                'required_passing_score' => 70,
            ]);

            // Create quiz
            $quiz = ElearningQuiz::create([
                'lesson_id'   => $finalLesson->id,
                'title'       => 'Final Assessment — ' . $course->name,
                'description' => "This comprehensive assessment tests your understanding of all course modules. You must score at least 70% to pass. You have {$questionCount} questions and 2 attempts.",
                'pass_mark'   => 70,
                'max_attempt' => 2,
                'status'      => 'active',
            ]);

            $created = 0;
            foreach ($decoded['questions'] as $q) {
                if (empty($q['question_text']) || empty($q['options'])) continue;

                $opts        = $q['options'];
                $correctKey  = strtolower($q['correct_answer'] ?? 'a');
                $qType       = $q['question_type'] ?? 'mcq';
                $difficulty  = in_array($q['difficulty'] ?? 'medium', ['easy', 'medium', 'hard'])
                    ? $q['difficulty']
                    : 'medium';

                ElearningQuizQuestion::create([
                    'quiz_id'        => $quiz->id,
                    'question_text'  => $q['question_text'],
                    'question_type'  => $qType,
                    'option_a'       => $opts['a'] ?? '',
                    'option_b'       => $opts['b'] ?? '',
                    'option_c'       => $qType === 'truefalse' ? null : ($opts['c'] ?? ''),
                    'option_d'       => $qType === 'truefalse' ? null : ($opts['d'] ?? ''),
                    'correct_answer' => $correctKey,
                    'explanation'    => $q['explanation'] ?? null,
                    'difficulty'     => $difficulty,
                    'module_index'   => isset($q['module_index']) ? (int)$q['module_index'] : null,
                    'marks'          => 1,
                    'status'         => 'active',
                ]);
                $created++;
            }

            return response()->json([
                'success'            => true,
                'quiz_lesson_id'     => $finalLesson->id,
                'questions_created'  => $created,
                'pass_mark'          => 70,
                'max_attempts'       => 2,
            ]);

        } catch (\Throwable $e) {
            Log::error('AiCourseGenerator: final assessment failed', [
                'course_id' => $course->id,
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
