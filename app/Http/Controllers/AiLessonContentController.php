<?php

namespace App\Http\Controllers;

use App\Models\AiPromptTemplate;
use App\Models\Course;
use App\Models\ElearningLesson;
use App\Models\LessonBlock;
use App\Services\OpenAIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AiLessonContentController extends Controller
{
    public function __construct(private OpenAIService $ai) {}

    // ─────────────────────────────────────────────────────────────
    // GENERATE — POST from lesson builder inline form
    // ─────────────────────────────────────────────────────────────
    public function generate(Request $request, Course $course, ElearningLesson $lesson)
    {
        $this->guard();

        $request->validate([
            'learning_level' => 'required|in:Awareness,Professional,Advanced',
            'extra_notes'    => 'nullable|string|max:600',
        ]);

        $template = AiPromptTemplate::where('template_code', 'lesson_content_generator_json_v1')
            ->where('is_active', true)
            ->first();

        if (!$template) {
            return back()->with('error', 'AI template not found. Run: php artisan db:seed --class=AiLessonContentTemplateSeeder');
        }

        $level       = $request->learning_level;
        $targetWords = match ($level) {
            'Awareness' => '500–800',
            'Advanced'  => '1000–2000',
            default     => '800–1500',
        };

        // Parse learning objectives into a clean semicolon list
        $rawObjectives = $lesson->learning_objectives ?? '';
        $objectives    = collect(preg_split('/[\n,]+/', $rawObjectives))
            ->map(fn($s) => trim($s))
            ->filter()
            ->implode('; ') ?: 'Not specified';

        $input = collect([
            "Course Title: {$course->name}",
            "Lesson Title: {$lesson->title}",
            "Lesson Description: " . ($lesson->short_description ?: 'Not provided'),
            "Learning Objectives: {$objectives}",
            "Course Level: {$level}",
            "Target Audience: " . ($course->who_should_attend ?: 'Professionals and practitioners'),
            "Lesson Duration: " . ($lesson->duration_minutes ? $lesson->duration_minutes . ' minutes' : 'Not specified'),
            "Target Word Count: {$targetWords} words",
            $request->filled('extra_notes') ? "Additional Instructions: {$request->extra_notes}" : null,
        ])->filter()->implode("\n");

        $started = microtime(true);
        $result  = $this->ai->generateFromTemplate($template, $input, auth()->id());

        if (!$result['success']) {
            return back()->with('error', 'AI generation failed: ' . ($result['error'] ?? 'Unknown error'));
        }

        // Strip markdown fences if model wraps output
        $raw = trim($result['text'] ?? '');
        $raw = preg_replace('/^```json\s*/i', '', $raw);
        $raw = preg_replace('/```\s*$/',       '', trim($raw));

        $ai = json_decode($raw, true);
        if (!is_array($ai)) {
            Log::error('AiLessonContent: invalid JSON from AI', ['raw' => substr($raw, 0, 500)]);
            return back()->with('error', 'AI returned invalid JSON. Please try again.');
        }

        session(['ai_lesson_draft' => [
            'course_id'      => $course->id,
            'lesson_id'      => $lesson->id,
            'learning_level' => $level,
            'ai'             => $ai,
            'usage'          => $result['usage'] ?? [],
            'template_id'    => $template->id,
            'generated_at'   => now()->toIso8601String(),
            'duration_ms'    => (int) round((microtime(true) - $started) * 1000),
        ]]);

        return redirect()->route('elearning.ai-lesson-content.preview', [$course, $lesson]);
    }

    // ─────────────────────────────────────────────────────────────
    // PREVIEW — editable review before saving
    // ─────────────────────────────────────────────────────────────
    public function preview(Course $course, ElearningLesson $lesson)
    {
        $this->guard();

        $draft = session('ai_lesson_draft');

        if (!$draft || ($draft['lesson_id'] ?? null) !== $lesson->id) {
            return redirect()->route('elearning.lessons.edit', [$course, $lesson])
                ->with('error', 'No AI draft found. Please generate lesson content first.');
        }

        return view('ai.lesson-content.preview', compact('course', 'lesson', 'draft'));
    }

    // ─────────────────────────────────────────────────────────────
    // SAVE — create LessonBlock records from preview form
    // ─────────────────────────────────────────────────────────────
    public function save(Request $request, Course $course, ElearningLesson $lesson)
    {
        $this->guard();

        $draft = session('ai_lesson_draft');

        if (!$draft || ($draft['lesson_id'] ?? null) !== $lesson->id) {
            return redirect()->route('elearning.lessons.edit', [$course, $lesson])
                ->with('error', 'Session expired. Please regenerate lesson content.');
        }

        $ai = $draft['ai'];

        // Optionally wipe existing blocks first
        if ($request->boolean('clear_existing')) {
            $lesson->allBlocks()->delete();
        }

        $order   = ($lesson->allBlocks()->max('sort_order') ?? -1) + 1;
        $created = 0;

        // 1 — Introduction (rich_text)
        $intro = $request->input('introduction', $ai['introduction']['html'] ?? '');
        if (trim(strip_tags((string) $intro))) {
            LessonBlock::create([
                'lesson_id'  => $lesson->id,
                'block_type' => 'rich_text',
                'title'      => $ai['introduction']['title'] ?? 'Introduction',
                'content'    => $intro,
                'sort_order' => $order++,
                'status'     => 'active',
            ]);
            $created++;
        }

        // 2 — Main sections (rich_text per section)
        $sections = $ai['main_sections'] ?? [];
        foreach ($sections as $i => $section) {
            $heading = $request->input("main_sections.{$i}.heading", $section['heading'] ?? '');
            $html    = $request->input("main_sections.{$i}.html",    $section['html']    ?? '');
            if (trim(strip_tags((string) $html))) {
                $body = $heading ? "<h3>{$heading}</h3>{$html}" : $html;
                LessonBlock::create([
                    'lesson_id'  => $lesson->id,
                    'block_type' => 'rich_text',
                    'title'      => $heading ?: 'Section ' . ($i + 1),
                    'content'    => $body,
                    'sort_order' => $order++,
                    'status'     => 'active',
                ]);
                $created++;
            }
        }

        // 3 — Practical Example (rich_text)
        $example = $request->input('practical_example', $ai['practical_example']['html'] ?? '');
        if (trim(strip_tags((string) $example))) {
            LessonBlock::create([
                'lesson_id'  => $lesson->id,
                'block_type' => 'rich_text',
                'title'      => $ai['practical_example']['title'] ?? 'Practical Example',
                'content'    => $example,
                'sort_order' => $order++,
                'status'     => 'active',
            ]);
            $created++;
        }

        // 4 — Scenario (scenario block — options come from AI, text is editable)
        $scenarioData = $ai['scenario'] ?? null;
        if ($scenarioData && !empty($scenarioData['options'])) {
            $scenarioText = $request->input('scenario_text', $scenarioData['text'] ?? '');
            if (trim((string) $scenarioText)) {
                LessonBlock::create([
                    'lesson_id'  => $lesson->id,
                    'block_type' => 'scenario',
                    'title'      => $scenarioData['title'] ?? 'Scenario Exercise',
                    'content'    => json_encode([
                        'text'    => $scenarioText,
                        'options' => $scenarioData['options'],
                    ]),
                    'sort_order' => $order++,
                    'status'     => 'active',
                ]);
                $created++;
            }
        }

        // 5 — Knowledge Checks (knowledge_check blocks)
        foreach ($ai['knowledge_checks'] ?? [] as $i => $kc) {
            if (empty($kc['question']) || empty($kc['options'])) continue;

            $question    = $request->input("kc_question.{$i}",    $kc['question']    ?? '');
            $explanation = $request->input("kc_explanation.{$i}", $kc['explanation'] ?? '');

            LessonBlock::create([
                'lesson_id'  => $lesson->id,
                'block_type' => 'knowledge_check',
                'title'      => $kc['title'] ?? 'Knowledge Check ' . ($i + 1),
                'content'    => json_encode([
                    'question'    => $question,
                    'type'        => $kc['type'] ?? 'single',
                    'options'     => $kc['options'],
                    'explanation' => $explanation,
                ]),
                'sort_order' => $order++,
                'status'     => 'active',
            ]);
            $created++;
        }

        // 6 — Summary (rich_text)
        $summary = $request->input('summary', $ai['summary']['html'] ?? '');
        if (trim(strip_tags((string) $summary))) {
            LessonBlock::create([
                'lesson_id'  => $lesson->id,
                'block_type' => 'rich_text',
                'title'      => $ai['summary']['title'] ?? 'Lesson Summary',
                'content'    => $summary,
                'sort_order' => $order++,
                'status'     => 'active',
            ]);
            $created++;
        }

        session()->forget('ai_lesson_draft');

        return redirect()->route('elearning.lessons.edit', [$course, $lesson])
            ->with('success', "✅ {$created} AI-generated content blocks added to this lesson.");
    }

    // ─────────────────────────────────────────────────────────────
    // CANCEL
    // ─────────────────────────────────────────────────────────────
    public function cancel(Course $course, ElearningLesson $lesson)
    {
        session()->forget('ai_lesson_draft');
        return redirect()->route('elearning.lessons.edit', [$course, $lesson]);
    }

    // ─────────────────────────────────────────────────────────────
    private function guard(): void
    {
        if (!config('ai.enabled', false)) {
            abort(403, 'AI features are disabled.');
        }
        if (!in_array(auth()->user()?->role, ['super_admin', 'admin'])) {
            abort(403);
        }
    }
}
