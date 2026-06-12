<?php

namespace App\Http\Controllers;

use App\Models\AiPromptTemplate;
use App\Models\Course;
use App\Models\ElearningLesson;
use App\Services\OpenAIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
        ]);

        // Load JSON template
        $template = AiPromptTemplate::where('template_code', self::TEMPLATE_CODE)
            ->where('is_active', true)
            ->first();

        if (! $template) {
            return response()->json([
                'success' => false,
                'error'   => 'AI template "' . self::TEMPLATE_CODE . '" not found or inactive. Go to AI → Prompt Templates and ensure it exists and is active.',
            ]);
        }

        // Build structured input for {input} placeholder
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

        // Call AI
        $result = $this->ai->generateFromTemplate($template, $input, auth()->id());

        if (! $result['success']) {
            return response()->json([
                'success' => false,
                'error'   => $result['error'] ?? 'AI generation failed. Please try again.',
            ]);
        }

        // Parse JSON response (strip markdown fences if present)
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

        // Ensure required keys present
        $required = ['course_description', 'learning_objectives', 'modules', 'assessment_plan', 'public_summary', 'seo_title', 'seo_meta_description'];
        foreach ($required as $key) {
            if (empty($parsed[$key])) {
                return response()->json([
                    'success' => false,
                    'error'   => "AI response is missing the '{$key}' field. Please try regenerating.",
                ]);
            }
        }

        // Store in session
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

        $formData = $draft['form_data'];
        $aiOutput = $draft['ai_output'];
        $courseType = $formData['course_type'];

        // Allow user edits from the preview form
        $editedDescription  = $request->input('course_description',   $aiOutput['course_description'] ?? '');
        $editedObjectives   = $request->input('learning_objectives',   is_array($aiOutput['learning_objectives'] ?? '') ? implode("\n", $aiOutput['learning_objectives']) : ($aiOutput['learning_objectives'] ?? ''));
        $editedAudience     = $request->input('target_audience',       $aiOutput['target_audience'] ?? '');
        $editedPrereqs      = $request->input('prerequisites',         is_array($aiOutput['prerequisites'] ?? '') ? implode("\n", $aiOutput['prerequisites']) : ($aiOutput['prerequisites'] ?? ''));
        $editedAssessment   = $request->input('assessment_plan',       $aiOutput['assessment_plan'] ?? '');
        $editedCertCriteria = $request->input('certificate_criteria',  $aiOutput['certificate_criteria'] ?? '');
        $editedSummary      = $request->input('public_summary',        $aiOutput['public_summary'] ?? '');
        $editedSeoTitle     = $request->input('seo_title',             $aiOutput['seo_title'] ?? '');
        $editedSeoDesc      = $request->input('seo_meta_description',  $aiOutput['seo_meta_description'] ?? '');

        // Build course outline from modules
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
                $editedSeoTitle, $editedSeoDesc, $courseOutline, &$course
            ) {
                $course = Course::create([
                    'name'                => $formData['course_name'],
                    'status'              => 0,
                    'course_type'         => $courseType === 'elearning' ? 'elearning' : 'manual',
                    'delivery_type'       => $courseType === 'elearning' ? 'eLearning' : null,
                    'language'            => $formData['language'],
                    'duration'            => $formData['duration'],
                    'full_description'    => $editedDescription,
                    'short_description'   => Str::limit(strip_tags($editedDescription), 200),
                    'learning_objectives' => $editedObjectives,
                    'who_should_attend'   => $editedAudience,
                    'prerequisites'       => $editedPrereqs,
                    'course_outline'      => $courseOutline,
                    'certification_info'  => $editedCertCriteria,
                    'seo_title'           => $editedSeoTitle,
                    'seo_description'     => $editedSeoDesc,
                    'description'         => $editedSummary,
                    'is_public'           => false,
                    'is_featured'         => false,
                    'ai_generated'        => true,
                    'ai_course_structure' => $aiOutput,
                ]);

                // For eLearning: create lesson placeholders from modules
                if ($courseType === 'elearning') {
                    $lessonOrder = 1;
                    foreach ($aiOutput['modules'] ?? [] as $module) {
                        foreach ($module['lessons'] ?? [] as $lesson) {
                            ElearningLesson::create([
                                'course_id'    => $course->id,
                                'title'        => $lesson['title'] ?? 'Untitled Lesson',
                                'lesson_order' => $lessonOrder++,
                                'status'       => 'draft',
                                'lesson_type'  => 'mixed',
                                'completion_rule' => 'manual',
                            ]);
                        }
                    }
                }
            });

            session()->forget(self::SESSION_KEY);

            $editUrl = $courseType === 'elearning'
                ? route('elearning.courses.edit', $course->id)
                : url('/admin/courses/edit/' . $course->id);

            return redirect($editUrl)
                ->with('success', '✨ AI-generated course "' . $formData['course_name'] . '" saved as draft. Review and publish when ready.');

        } catch (\Throwable $e) {
            return redirect()->back()
                ->with('error', 'Failed to save course: ' . $e->getMessage() . '. Please try again.');
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
