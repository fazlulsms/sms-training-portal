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

        $template = AiPromptTemplate::where('template_code', 'lesson_content_generator_json_v2')
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

        $raw = trim($result['text'] ?? '');
        $raw = preg_replace('/^```json\s*/i', '', $raw);
        $raw = preg_replace('/```\s*$/',       '', trim($raw));

        $ai = json_decode($raw, true);
        if (!is_array($ai) || empty($ai['blocks'])) {
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
    // SAVE — create LessonBlock records from preview edits
    // ─────────────────────────────────────────────────────────────
    public function save(Request $request, Course $course, ElearningLesson $lesson)
    {
        $this->guard();

        $draft = session('ai_lesson_draft');

        if (!$draft || ($draft['lesson_id'] ?? null) !== $lesson->id) {
            return redirect()->route('elearning.lessons.edit', [$course, $lesson])
                ->with('error', 'Session expired. Please regenerate lesson content.');
        }

        if ($request->boolean('clear_existing')) {
            $lesson->allBlocks()->delete();
        }

        // Apply edits from preview form over AI blocks
        $aiBlocks = $draft['ai']['blocks'] ?? [];
        $edited   = $request->input('blocks', []);

        foreach ($aiBlocks as $i => &$block) {
            if (!isset($edited[$i])) continue;
            $e = $edited[$i];

            switch ($block['type']) {
                case 'rich_text':
                    if (isset($e['content'])) $block['content'] = $e['content'];
                    break;
                case 'fun_fact':
                    if (isset($e['ff_title']))   $block['ff_title']   = $e['ff_title'];
                    if (isset($e['ff_content']))  $block['ff_content'] = $e['ff_content'];
                    break;
                case 'myth_fact':
                    if (isset($e['myth'])) $block['myth'] = $e['myth'];
                    if (isset($e['fact'])) $block['fact'] = $e['fact'];
                    break;
                case 'click_reveal':
                    if (isset($e['question']))    $block['question']    = $e['question'];
                    if (isset($e['answer']))      $block['answer']      = $e['answer'];
                    if (isset($e['explanation'])) $block['explanation'] = $e['explanation'];
                    break;
                case 'reflection':
                    if (isset($e['prompt'])) $block['prompt'] = $e['prompt'];
                    break;
                case 'scenario':
                    if (isset($e['text'])) $block['text'] = $e['text'];
                    break;
                case 'knowledge_check':
                    if (isset($e['question']))    $block['question']    = $e['question'];
                    if (isset($e['explanation'])) $block['explanation'] = $e['explanation'];
                    break;
                case 'case_study':
                    if (isset($e['case_description']))  $block['case_description']  = $e['case_description'];
                    if (isset($e['expected_response'])) $block['expected_response'] = $e['expected_response'];
                    break;
                case 'workplace_example':
                    // examples are read-only in preview
                    break;
            }

            if (isset($e['title'])) $block['title'] = $e['title'];
        }
        unset($block);

        $created = static::createBlocksFromAi($lesson, $aiBlocks);

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
    // STATIC HELPERS — called by AiCourseGeneratorController (Mode B)
    // ─────────────────────────────────────────────────────────────

    /**
     * Generate AI content and save blocks for a lesson — used by course generator Mode B.
     * Returns number of blocks created, or 0 on failure.
     */
    public static function generateAndSaveBlocks(Course $course, ElearningLesson $lesson, string $level): int
    {
        try {
            $template = AiPromptTemplate::where('template_code', 'lesson_content_generator_json_v2')
                ->where('is_active', true)
                ->first();

            if (!$template) return 0;

            $targetWords = match ($level) {
                'Awareness' => '500–800',
                'Advanced'  => '1000–2000',
                default     => '800–1500',
            };

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
            ])->filter()->implode("\n");

            $ai = app(OpenAIService::class)->generateFromTemplate($template, $input, null);

            if (!$ai['success']) return 0;

            $raw = trim($ai['text'] ?? '');
            $raw = preg_replace('/^```json\s*/i', '', $raw);
            $raw = preg_replace('/```\s*$/',       '', trim($raw));

            $decoded = json_decode($raw, true);
            if (!is_array($decoded) || empty($decoded['blocks'])) return 0;

            return static::createBlocksFromAi($lesson, $decoded['blocks']);
        } catch (\Throwable $e) {
            Log::error('AiLessonContent generateAndSaveBlocks failed', [
                'lesson_id' => $lesson->id,
                'error'     => $e->getMessage(),
            ]);
            return 0;
        }
    }

    /**
     * Persist an array of AI blocks as LessonBlock records.
     * Returns number of blocks created.
     */
    public static function createBlocksFromAi(ElearningLesson $lesson, array $blocks): int
    {
        $order   = ($lesson->allBlocks()->max('sort_order') ?? -1) + 1;
        $created = 0;

        foreach ($blocks as $block) {
            $type    = $block['type'] ?? null;
            $title   = $block['title'] ?? null;
            $content = static::blockToContent($type, $block);

            if ($content === null || !isset(LessonBlock::TYPES[$type])) continue;

            LessonBlock::create([
                'lesson_id'  => $lesson->id,
                'block_type' => $type,
                'title'      => $title,
                'content'    => $content,
                'sort_order' => $order++,
                'status'     => 'active',
            ]);
            $created++;
        }

        return $created;
    }

    /**
     * Convert an AI block array into the stored content string for a given type.
     * Returns null if the block should be skipped.
     */
    public static function blockToContent(string $type, array $block): ?string
    {
        return match ($type) {

            'rich_text' => (function () use ($block): ?string {
                $html = $block['content'] ?? '';
                return trim(strip_tags((string) $html)) ? (string) $html : null;
            })(),

            'fun_fact' => json_encode([
                'icon'    => $block['icon']       ?? '💡',
                'title'   => $block['ff_title']   ?? ($block['title'] ?? 'Did You Know?'),
                'content' => $block['ff_content'] ?? '',
            ]),

            'reflection' => json_encode([
                'prompt'    => $block['prompt']    ?? '',
                'questions' => $block['questions'] ?? [],
            ]),

            'click_reveal' => json_encode([
                'question'    => $block['question']    ?? '',
                'answer'      => $block['answer']      ?? '',
                'explanation' => $block['explanation'] ?? '',
            ]),

            'myth_fact' => json_encode([
                'myth' => $block['myth'] ?? '',
                'fact' => $block['fact'] ?? '',
            ]),

            'workplace_example' => json_encode([
                'examples' => $block['examples'] ?? [],
            ]),

            'scenario' => (function () use ($block): ?string {
                if (empty($block['options'])) return null;
                return json_encode([
                    'text'    => $block['text']    ?? '',
                    'options' => $block['options'],
                ]);
            })(),

            'knowledge_check' => (function () use ($block): ?string {
                if (empty($block['question']) || empty($block['options'])) return null;
                return json_encode([
                    'question'    => $block['question'],
                    'type'        => $block['kc_type']     ?? $block['type_hint'] ?? 'single',
                    'options'     => $block['options'],
                    'explanation' => $block['explanation'] ?? '',
                ]);
            })(),

            'case_study' => json_encode([
                'case_description'  => $block['case_description']  ?? '',
                'questions'         => $block['questions']          ?? [],
                'expected_response' => $block['expected_response']  ?? '',
            ]),

            default => null,
        };
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
