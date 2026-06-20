<?php

namespace App\Http\Controllers;

use App\Models\CourseBlueprintModule;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\ElearningLesson;
use App\Models\ElearningQuiz;
use App\Models\ElearningQuizQuestion;
use App\Models\LessonAudio;
use App\Models\LessonBlock;
use App\Models\PptCourse;
use App\Models\PptModule;
use App\Models\PptSlide;
use App\Services\OpenAIService;
use App\Services\PptExtractionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PptElearningBuilderController extends Controller
{
    private const TTS_ENDPOINT = 'https://api.openai.com/v1/audio/speech';
    private const TTS_MODEL    = 'gpt-4o-mini-tts';

    public function __construct(private OpenAIService $ai) {}

    // ──────────────────────────────────────────────────────────
    // Index — list all PPT courses
    // ──────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $courses = PptCourse::with('creator')
            ->when($request->filled('search'), fn ($q) => $q->where('title', 'like', '%' . $request->input('search') . '%'))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('ppt-builder.index', compact('courses'));
    }

    // ──────────────────────────────────────────────────────────
    // Create — upload form
    // ──────────────────────────────────────────────────────────

    public function create()
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
        return view('ppt-builder.create');
    }

    // ──────────────────────────────────────────────────────────
    // Store — upload PPTX and trigger extraction
    // ──────────────────────────────────────────────────────────

    public function store(Request $request, PptExtractionService $extractor)
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'file'        => 'required|file|mimes:pptx,ppt|max:51200', // 50 MB
        ]);

        $file = $request->file('file');
        $path = $file->store('ppt-builder/uploads/' . now()->format('Y/m'), 'local');

        $pptCourse = PptCourse::create([
            'title'             => $validated['title'],
            'description'       => $validated['description'] ?? null,
            'status'            => 'processing',
            'original_filename' => Str::limit($file->getClientOriginalName(), 255, ''),
            'file_path'         => $path,
            'file_size'         => $file->getSize(),
            'created_by'        => auth()->id(),
        ]);

        $extractor->extract($pptCourse);
        $pptCourse->refresh();

        if ($pptCourse->status === 'ready') {
            return redirect()
                ->route('ppt-builder.editor', $pptCourse)
                ->with('success', "Presentation uploaded. {$pptCourse->total_slides} slides extracted. Start building your eLearning course.");
        }

        return redirect()
            ->route('ppt-builder.index')
            ->with('error', 'Upload succeeded but extraction failed: ' . $pptCourse->processing_error);
    }

    // ──────────────────────────────────────────────────────────
    // Editor — main workspace
    // ──────────────────────────────────────────────────────────

    public function editor(PptCourse $pptCourse)
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $pptCourse->load([
            'modules',
            'slides',
        ]);

        $slidesData  = $pptCourse->slides->map(fn($s) => $this->slidePayload($s))->values();
        $modulesData = $pptCourse->modules->values();

        return view('ppt-builder.editor', compact('pptCourse', 'slidesData', 'modulesData'));
    }

    // ──────────────────────────────────────────────────────────
    // Publish PPT course → eLearning Course
    // ──────────────────────────────────────────────────────────

    public function publish(Request $request, PptCourse $pptCourse): JsonResponse
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $validated = $request->validate([
            'title'           => 'required|string|max:255',
            'description'     => 'nullable|string|max:3000',
            'language'        => 'required|string|max:50',
            'target_audience' => 'nullable|string|max:500',
            'category'        => 'nullable|string|max:100',
        ]);

        $slides = $pptCourse->slides()->orderBy('slide_order')->get();

        if ($slides->isEmpty()) {
            return response()->json(['error' => 'No slides to publish. Upload a PPTX first.'], 422);
        }

        // If already published, unlink old course first (allow re-publish)
        if ($pptCourse->course_id) {
            return response()->json([
                'error'      => 'This PPT course is already published.',
                'course_url' => url('/admin/courses/edit/' . $pptCourse->course_id),
            ], 409);
        }

        try {
            $course = DB::transaction(function () use ($pptCourse, $slides, $validated) {
                return $this->buildCourse($pptCourse, $slides, $validated);
            });

            $pptCourse->update(['status' => 'published', 'course_id' => $course->id]);

            return response()->json([
                'success'    => true,
                'course_url' => url('/courses/edit/' . $course->id),
                'course_id'  => $course->id,
                'message'    => "eLearning course \"{$course->name}\" created with {$slides->count()} lessons.",
            ]);

        } catch (\Throwable $e) {
            Log::error('PptBuilder publish failed', [
                'ppt_course_id' => $pptCourse->id,
                'error'         => $e->getMessage(),
            ]);
            return response()->json(['error' => 'Publish failed: ' . $e->getMessage()], 500);
        }
    }

    private function buildCourse(PptCourse $pptCourse, $slides, array $validated): Course
    {
        // ── 1. Resolve category ──────────────────────────────
        $categoryId = null;
        if (!empty($validated['category'])) {
            $cat = CourseCategory::where('name', $validated['category'])
                ->orWhere('name', 'like', '%' . $validated['category'] . '%')
                ->first();
            $categoryId = $cat?->id;
        }

        // ── 2. Estimate duration (2 min/slide) ───────────────
        $estimatedMinutes = $slides->count() * 2;

        // ── 3. Create Course ─────────────────────────────────
        $course = Course::create([
            'name'             => $validated['title'],
            'course_type'      => 'elearning',
            'delivery_type'    => 'eLearning',
            'language'         => $validated['language'],
            'status'           => 0,
            'is_public'        => false,
            'full_description' => $validated['description'] ?? $pptCourse->description,
            'short_description'=> Str::limit(strip_tags($validated['description'] ?? $pptCourse->description ?? ''), 200),
            'description'      => $validated['description'] ?? $pptCourse->description,
            'who_should_attend' => $validated['target_audience'] ?? null,
            'category'         => $validated['category'] ?? null,
            'category_id'      => $categoryId,
            'target_learning_minutes' => $estimatedMinutes,
            'ai_generated'     => false,
        ]);

        // ── 4. Group slides by module ────────────────────────
        $modules    = $pptCourse->modules()->orderBy('module_order')->get();
        $lessonOrder = 1;

        if ($modules->isNotEmpty()) {
            // Has modules — create blueprint modules + lessons per module
            foreach ($modules as $pptModule) {
                $moduleSlides = $slides->where('ppt_module_id', $pptModule->id)->values();
                if ($moduleSlides->isEmpty()) continue;

                $bpModule = CourseBlueprintModule::create([
                    'course_id'    => $course->id,
                    'title'        => $pptModule->title,
                    'module_order' => $pptModule->module_order,
                ]);

                foreach ($moduleSlides as $slide) {
                    $lessonOrder = $this->createSlideLesson($course, $slide, $lessonOrder, $bpModule->id);
                }
            }

            // Unassigned slides → extra module at end
            $unassigned = $slides->whereNull('ppt_module_id')->values();
            if ($unassigned->isNotEmpty()) {
                $bpModule = CourseBlueprintModule::create([
                    'course_id'    => $course->id,
                    'title'        => 'Additional Slides',
                    'module_order' => $modules->count() + 1,
                ]);
                foreach ($unassigned as $slide) {
                    $lessonOrder = $this->createSlideLesson($course, $slide, $lessonOrder, $bpModule->id);
                }
            }
        } else {
            // No modules — flat lesson list
            foreach ($slides as $slide) {
                $lessonOrder = $this->createSlideLesson($course, $slide, $lessonOrder, null);
            }
        }

        return $course;
    }

    private function createSlideLesson(Course $course, PptSlide $slide, int $lessonOrder, ?int $blueprintModuleId): int
    {
        $hasAudio = $slide->isAudioReady();
        $hasCheck = !empty($slide->knowledge_check);
        $hasContent = !empty($slide->ai_explanation) || !empty($slide->content_text);

        $lesson = ElearningLesson::create([
            'course_id'               => $course->id,
            'blueprint_module_id'     => $blueprintModuleId,
            'title'                   => $slide->title ?: "Slide {$slide->slide_number}",
            'short_description'       => $hasContent
                ? Str::limit(strip_tags($slide->ai_explanation ?? $slide->content_text ?? ''), 200)
                : null,
            'lesson_order'            => $lessonOrder,
            'status'                  => 'draft',
            'lesson_type'             => $hasAudio ? 'audio' : 'mixed',
            'completion_rule'         => 'manual',
            'require_audio_completion'=> $hasAudio,
            'duration_minutes'        => $slide->audio_duration ? (int) ceil($slide->audio_duration / 60) : 1,
        ]);

        $blockOrder = 1;

        // ── Slide image block ────────────────────────────────
        if ($slide->image_path) {
            LessonBlock::create([
                'lesson_id'   => $lesson->id,
                'block_type'  => 'image',
                'title'       => $slide->title ?: null,
                'content'     => $slide->image_path,
                'media_path'  => $slide->image_path,
                'sort_order'  => $blockOrder++,
                'status'      => 'active',
            ]);
        }

        // ── AI explanation block ─────────────────────────────
        $explanationText = $slide->ai_explanation ?: $slide->content_text;
        $richTextBlockId = null;
        if ($explanationText) {
            $html = '<p>' . nl2br(e($explanationText)) . '</p>';

            // Append key points if present
            if (!empty($slide->ai_key_points)) {
                $html .= '<ul>';
                foreach ($slide->ai_key_points as $kp) {
                    $html .= '<li>' . e($kp) . '</li>';
                }
                $html .= '</ul>';
            }

            $richTextBlock = LessonBlock::create([
                'lesson_id'    => $lesson->id,
                'block_type'   => 'rich_text',
                'title'        => null,
                'content'      => $html,
                'sort_order'   => $blockOrder++,
                'status'       => 'active',
                'audio_enabled'=> $hasAudio, // enable audio on this block if audio exists
            ]);
            $richTextBlockId = $richTextBlock->id;
        }

        // ── Narration audio — linked to the rich_text block ──
        if ($hasAudio) {
            LessonAudio::create([
                'lesson_id'        => $lesson->id,
                'block_id'         => $richTextBlockId, // links audio to the content block
                'audio_type'       => 'ai_coach',
                'voice'            => 'nova',
                'language'         => 'en',
                'file_path'        => $slide->audio_path,
                'duration_seconds' => $slide->audio_duration,
                'status'           => 'ready',
                'generated_at'     => $slide->audio_generated_at ?? now(),
            ]);
        }

        // ── Knowledge check quiz ─────────────────────────────
        if ($hasCheck) {
            $kc   = $slide->knowledge_check;
            $type = $kc['type'] ?? 'multiple_choice';

            $quiz = ElearningQuiz::create([
                'lesson_id'   => $lesson->id,
                'title'       => "Check: " . ($slide->title ?: "Slide {$slide->slide_number}"),
                'description' => 'Knowledge check for this slide.',
                'pass_mark'   => 70,
                'max_attempt' => 3,
                'status'      => 'active',
            ]);

            $options = $kc['options'] ?? [];
            ElearningQuizQuestion::create([
                'quiz_id'       => $quiz->id,
                'question_text' => $kc['question'] ?? 'Answer the following:',
                'question_type' => $type === 'true_false' ? 'truefalse' : ($type === 'reflection' ? 'truefalse' : 'mcq'),
                'option_a'      => $options[0] ?? 'True',
                'option_b'      => $options[1] ?? 'False',
                'option_c'      => $options[2] ?? null,
                'option_d'      => $options[3] ?? null,
                'correct_answer'=> $this->mapCorrectAnswer($kc['correct'] ?? 'A', $options),
                'explanation'   => $kc['explanation'] ?? null,
                'difficulty'    => 'medium',
                'marks'         => 1,
                'status'        => 'active',
            ]);
        }

        return $lessonOrder + 1;
    }

    private function mapCorrectAnswer(string $correct, array $options): string
    {
        // correct might be "A", "B", "True", "False", or index
        $correct = strtoupper(trim($correct));
        if (in_array($correct, ['A', 'B', 'C', 'D'], true)) {
            return strtolower($correct);
        }
        // Handle True/False
        if ($correct === 'TRUE')  return 'a';
        if ($correct === 'FALSE') return 'b';
        // Fallback: try to match by option text
        foreach ($options as $i => $opt) {
            if (strtoupper(trim($opt)) === $correct) {
                return ['a','b','c','d'][$i] ?? 'a';
            }
        }
        return 'a';
    }

    // ──────────────────────────────────────────────────────────
    // Destroy
    // ──────────────────────────────────────────────────────────

    public function destroy(PptCourse $pptCourse)
    {
        abort_unless(auth()->user()?->isSuperAdmin(), 403);

        // Clean up stored files
        foreach ($pptCourse->allSlides as $slide) {
            if ($slide->image_path) Storage::disk('public')->delete($slide->image_path);
            if ($slide->audio_path) Storage::disk('public')->delete($slide->audio_path);
        }
        if ($pptCourse->file_path) Storage::disk('local')->delete($pptCourse->file_path);

        $pptCourse->delete();

        return redirect()->route('ppt-builder.index')->with('success', 'PPT course deleted.');
    }

    // ══════════════════════════════════════════════════════════
    // SLIDE AJAX ENDPOINTS
    // ══════════════════════════════════════════════════════════

    public function getSlide(PptCourse $pptCourse, PptSlide $pptSlide): JsonResponse
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
        $this->assertBelongs($pptSlide, $pptCourse);

        return response()->json([
            'slide' => $this->slidePayload($pptSlide),
        ]);
    }

    public function updateSlide(Request $request, PptCourse $pptCourse, PptSlide $pptSlide): JsonResponse
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
        $this->assertBelongs($pptSlide, $pptCourse);

        $validated = $request->validate([
            'title'              => 'nullable|string|max:255',
            'content_text'       => 'nullable|string',
            'speaker_notes'      => 'nullable|string',
            'discussion_points'  => 'nullable|string|max:3000',
            'ai_narration_script'=> 'nullable|string',
            'ai_explanation'     => 'nullable|string',
            'ai_trainer_notes'   => 'nullable|string',
            'trainer_notes'      => 'nullable|string',
            'ai_key_points'      => 'nullable|array',
            'knowledge_check'    => 'nullable|array',
        ]);

        $pptSlide->update($validated);

        return response()->json(['success' => true, 'slide' => $this->slidePayload($pptSlide->refresh())]);
    }

    public function removeSlide(Request $request, PptCourse $pptCourse, PptSlide $pptSlide): JsonResponse
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
        $this->assertBelongs($pptSlide, $pptCourse);

        $pptSlide->update(['is_removed' => !$pptSlide->is_removed]);

        return response()->json(['success' => true, 'is_removed' => $pptSlide->is_removed]);
    }

    // ──────────────────────────────────────────────────────────
    // Reorder slides within a module (or unassigned pool)
    // ──────────────────────────────────────────────────────────

    public function reorderSlides(Request $request, PptCourse $pptCourse): JsonResponse
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $request->validate([
            'slides'           => 'required|array',
            'slides.*.id'      => 'required|integer',
            'slides.*.order'   => 'required|integer',
            'slides.*.module'  => 'nullable|integer',
        ]);

        foreach ($request->input('slides') as $item) {
            PptSlide::where('id', $item['id'])
                ->where('ppt_course_id', $pptCourse->id)
                ->update([
                    'slide_order'   => $item['order'],
                    'ppt_module_id' => $item['module'] ?? null,
                ]);
        }

        return response()->json(['success' => true]);
    }

    // ──────────────────────────────────────────────────────────
    // Assign slide to module (or unassign)
    // ──────────────────────────────────────────────────────────

    public function assignSlide(Request $request, PptCourse $pptCourse, PptSlide $pptSlide): JsonResponse
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
        $this->assertBelongs($pptSlide, $pptCourse);

        $request->validate(['ppt_module_id' => 'nullable|integer|exists:ppt_modules,id']);

        $moduleId = $request->input('ppt_module_id');

        if ($moduleId && !PptModule::where('id', $moduleId)->where('ppt_course_id', $pptCourse->id)->exists()) {
            return response()->json(['error' => 'Module not found.'], 422);
        }

        $pptSlide->update(['ppt_module_id' => $moduleId]);

        return response()->json(['success' => true]);
    }

    // ══════════════════════════════════════════════════════════
    // AI — Generate slide explanation & narration
    // ══════════════════════════════════════════════════════════

    public function aiExplain(Request $request, PptCourse $pptCourse, PptSlide $pptSlide): JsonResponse
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
        $this->assertBelongs($pptSlide, $pptCourse);

        if (!config('ai.enabled', false)) {
            return response()->json(['error' => 'AI feature is disabled.'], 422);
        }

        $speakerNotesPriority = $pptSlide->speaker_notes
            ? "SPEAKER NOTES (highest priority — use these as the primary source for the narration):\n{$pptSlide->speaker_notes}\n\n"
            : '';

        $discussionGuidance = $pptSlide->discussion_points
            ? "TRAINER DISCUSSION POINTS (use these as emphasis guidance):\n{$pptSlide->discussion_points}\n\n"
            : '';

        $prompt = <<<PROMPT
You are a professional eLearning content developer at SMS Training Academy.

COURSE: {$pptCourse->title}
SLIDE {$pptSlide->slide_number}: {$pptSlide->title}

SLIDE CONTENT:
{$pptSlide->content_text}

{$speakerNotesPriority}{$discussionGuidance}INSTRUCTIONS:
Generate educational content for this presentation slide. Priority order: Speaker Notes > Discussion Points > Slide Content.
The trainer is the expert — AI only enhances and narrates their material.

Return a JSON object (no markdown fences):
{
  "explanation": "Clear explanation of this slide content for learners (200-350 words). Connect concepts to real workplace application. Use the speaker notes and discussion points to guide emphasis.",
  "narration_script": "Spoken narration script (150-250 words) that an AI voice will read aloud. Natural spoken prose only — no bullet points, no markdown, no slide numbers. Warm, professional, direct. Start with the topic, not with 'Welcome'.",
  "key_points": ["Key learning point 1", "Key learning point 2", "Key learning point 3"],
  "trainer_notes": "2-3 sentences for the trainer on how to supplement or discuss this slide in live or hybrid sessions."
}
PROMPT;

        $result = $this->ai->generateText($prompt, 'ppt_slide_explain', auth()->id(), 1200);

        if (!$result['success']) {
            return response()->json(['error' => $result['error'] ?? 'AI generation failed.'], 422);
        }

        $raw = trim($result['text'] ?? '');
        $raw = preg_replace('/^```(?:json)?\s*/i', '', $raw);
        $raw = preg_replace('/\s*```$/', '', $raw);
        $decoded = json_decode($raw, true);

        if (!is_array($decoded)) {
            return response()->json(['error' => 'AI returned unexpected format. Try again.'], 422);
        }

        $pptSlide->update([
            'ai_explanation'      => $decoded['explanation']     ?? null,
            'ai_narration_script' => $decoded['narration_script'] ?? null,
            'ai_key_points'       => $decoded['key_points']      ?? [],
            'ai_trainer_notes'    => $decoded['trainer_notes']   ?? null,
            'ai_generated_at'     => now(),
        ]);

        return response()->json([
            'success' => true,
            'slide'   => $this->slidePayload($pptSlide->refresh()),
        ]);
    }

    // ──────────────────────────────────────────────────────────
    // AI — Generate knowledge check
    // ──────────────────────────────────────────────────────────

    public function aiKnowledgeCheck(Request $request, PptCourse $pptCourse, PptSlide $pptSlide): JsonResponse
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
        $this->assertBelongs($pptSlide, $pptCourse);

        if (!config('ai.enabled', false)) {
            return response()->json(['error' => 'AI feature is disabled.'], 422);
        }

        $type = $request->input('type', 'multiple_choice');
        if (!in_array($type, ['multiple_choice', 'true_false', 'reflection'], true)) {
            $type = 'multiple_choice';
        }

        $typeInstructions = match ($type) {
            'true_false' => 'Generate a True/False question. options: ["True", "False"]. correct: "True" or "False".',
            'reflection' => 'Generate an open-ended reflection question for the learner to consider. No correct answer needed. Set correct to null and options to [].',
            default      => 'Generate a Multiple Choice question with 4 options (A, B, C, D). correct: "A", "B", "C", or "D".',
        };

        $context = implode("\n", array_filter([
            $pptSlide->speaker_notes ? 'Speaker Notes: ' . $pptSlide->speaker_notes : '',
            $pptSlide->content_text  ? 'Slide Content: ' . $pptSlide->content_text  : '',
            $pptSlide->discussion_points ? 'Discussion Points: ' . $pptSlide->discussion_points : '',
        ]));

        $prompt = <<<PROMPT
Generate a knowledge check question from ONLY the slide content below. Do not add outside knowledge.

Course: {$pptCourse->title}
Slide: {$pptSlide->title}
Content:
{$context}

Question type: {$typeInstructions}

Return JSON only (no markdown):
{
  "type": "{$type}",
  "question": "The question text",
  "options": ["Option A", "Option B", "Option C", "Option D"],
  "correct": "A",
  "explanation": "Brief explanation of why this is correct, referencing the slide content."
}
PROMPT;

        $result = $this->ai->generateText($prompt, 'ppt_knowledge_check', auth()->id(), 500);

        if (!$result['success']) {
            return response()->json(['error' => $result['error'] ?? 'AI generation failed.'], 422);
        }

        $raw = trim($result['text'] ?? '');
        $raw = preg_replace('/^```(?:json)?\s*/i', '', $raw);
        $raw = preg_replace('/\s*```$/', '', $raw);
        $decoded = json_decode($raw, true);

        if (!is_array($decoded) || empty($decoded['question'])) {
            return response()->json(['error' => 'AI returned unexpected format. Try again.'], 422);
        }

        $pptSlide->update(['knowledge_check' => $decoded]);

        return response()->json([
            'success'         => true,
            'knowledge_check' => $decoded,
        ]);
    }

    // ══════════════════════════════════════════════════════════
    // Audio generation
    // ══════════════════════════════════════════════════════════

    public function generateAudio(Request $request, PptCourse $pptCourse, PptSlide $pptSlide): JsonResponse
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
        $this->assertBelongs($pptSlide, $pptCourse);

        if (!config('ai.enabled', false)) {
            return response()->json(['error' => 'AI feature is disabled.'], 422);
        }

        if (empty(config('ai.api_key'))) {
            return response()->json(['error' => 'OpenAI API key is not configured.'], 422);
        }

        $script = $pptSlide->ai_narration_script;
        if (!$script) {
            return response()->json(['error' => 'Generate an AI narration script first before creating audio.'], 422);
        }

        if ($pptSlide->audio_status === 'processing') {
            return response()->json(['error' => 'Audio generation is already in progress.'], 409);
        }

        // Delete existing audio file
        if ($pptSlide->audio_path) {
            Storage::disk('public')->delete($pptSlide->audio_path);
        }

        $pptSlide->update(['audio_status' => 'processing', 'audio_path' => null]);

        $voice = $request->input('voice', 'nova');
        if (!in_array($voice, ['alloy', 'echo', 'fable', 'nova', 'onyx', 'shimmer'], true)) {
            $voice = 'nova';
        }

        // Truncate to TTS limit
        if (mb_strlen($script) > 4000) {
            $script = mb_substr($script, 0, 4000) . '...';
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('ai.api_key'),
                'Content-Type'  => 'application/json',
            ])
            ->timeout(120)
            ->post(self::TTS_ENDPOINT, [
                'model'           => self::TTS_MODEL,
                'input'           => $script,
                'voice'           => $voice,
                'response_format' => 'mp3',
            ]);

            if ($response->failed()) {
                $errorMsg = $response->json('error.message') ?? $response->body();
                $pptSlide->update(['audio_status' => 'failed']);
                Log::error('PptBuilder TTS failed', ['slide_id' => $pptSlide->id, 'error' => $errorMsg]);
                return response()->json(['error' => 'TTS error: ' . $errorMsg], 422);
            }

            $mp3Path = "ppt-builder/{$pptCourse->id}/audio/slide-{$pptSlide->slide_number}.mp3";
            Storage::disk('public')->put($mp3Path, $response->body());

            // Estimate duration: ~150 words/min
            $wordCount = str_word_count($script);
            $duration  = max(1, (int) round($wordCount / 2.5));

            $pptSlide->update([
                'audio_path'         => $mp3Path,
                'audio_status'       => 'ready',
                'audio_duration'     => $duration,
                'audio_generated_at' => now(),
            ]);

            return response()->json([
                'success'  => true,
                'audio_url'=> $pptSlide->refresh()->audioUrl(),
                'duration' => $duration,
            ]);

        } catch (\Throwable $e) {
            $pptSlide->update(['audio_status' => 'failed']);
            Log::error('PptBuilder TTS exception', ['slide_id' => $pptSlide->id, 'error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function deleteAudio(PptCourse $pptCourse, PptSlide $pptSlide): JsonResponse
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
        $this->assertBelongs($pptSlide, $pptCourse);

        if ($pptSlide->audio_path) {
            Storage::disk('public')->delete($pptSlide->audio_path);
        }

        $pptSlide->update([
            'audio_path'         => null,
            'audio_status'       => 'none',
            'audio_duration'     => null,
            'audio_generated_at' => null,
        ]);

        return response()->json(['success' => true]);
    }

    // ──────────────────────────────────────────────────────────
    // Upload manually recorded audio for a slide
    // ──────────────────────────────────────────────────────────

    public function uploadAudio(Request $request, PptCourse $pptCourse, PptSlide $pptSlide): JsonResponse
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
        $this->assertBelongs($pptSlide, $pptCourse);

        $request->validate(['audio' => 'required|file|mimes:mp3,wav,ogg,m4a|max:20480']);

        if ($pptSlide->audio_path) {
            Storage::disk('public')->delete($pptSlide->audio_path);
        }

        $file    = $request->file('audio');
        $ext     = $file->getClientOriginalExtension();
        $mp3Path = "ppt-builder/{$pptCourse->id}/audio/slide-{$pptSlide->slide_number}-manual.{$ext}";
        Storage::disk('public')->putFileAs(dirname($mp3Path), $file, basename($mp3Path));

        $pptSlide->update([
            'audio_path'         => $mp3Path,
            'audio_status'       => 'ready',
            'audio_duration'     => null,
            'audio_generated_at' => now(),
        ]);

        return response()->json([
            'success'   => true,
            'audio_url' => $pptSlide->refresh()->audioUrl(),
        ]);
    }

    // ══════════════════════════════════════════════════════════
    // MODULE ENDPOINTS
    // ══════════════════════════════════════════════════════════

    public function storeModule(Request $request, PptCourse $pptCourse): JsonResponse
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $request->validate(['title' => 'required|string|max:255', 'description' => 'nullable|string|max:1000']);

        $maxOrder = PptModule::where('ppt_course_id', $pptCourse->id)->max('module_order') ?? 0;

        $module = PptModule::create([
            'ppt_course_id' => $pptCourse->id,
            'title'         => $request->input('title'),
            'description'   => $request->input('description'),
            'module_order'  => $maxOrder + 1,
        ]);

        return response()->json(['success' => true, 'module' => $module]);
    }

    public function updateModule(Request $request, PptCourse $pptCourse, PptModule $pptModule): JsonResponse
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
        abort_unless($pptModule->ppt_course_id === $pptCourse->id, 404);

        $request->validate(['title' => 'required|string|max:255', 'description' => 'nullable|string|max:1000']);

        $pptModule->update($request->only('title', 'description'));

        return response()->json(['success' => true, 'module' => $pptModule->fresh()]);
    }

    public function destroyModule(PptCourse $pptCourse, PptModule $pptModule): JsonResponse
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
        abort_unless($pptModule->ppt_course_id === $pptCourse->id, 404);

        // Unassign slides from this module
        PptSlide::where('ppt_module_id', $pptModule->id)->update(['ppt_module_id' => null]);

        $pptModule->delete();

        return response()->json(['success' => true]);
    }

    public function reorderModules(Request $request, PptCourse $pptCourse): JsonResponse
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $request->validate([
            'modules'      => 'required|array',
            'modules.*.id' => 'required|integer',
        ]);

        foreach ($request->input('modules') as $idx => $item) {
            PptModule::where('id', $item['id'])
                ->where('ppt_course_id', $pptCourse->id)
                ->update(['module_order' => $idx + 1]);
        }

        return response()->json(['success' => true]);
    }

    // ──────────────────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────────────────

    private function assertBelongs(PptSlide $slide, PptCourse $course): void
    {
        abort_unless($slide->ppt_course_id === $course->id, 404);
    }

    private function slidePayload(PptSlide $slide): array
    {
        return [
            'id'                  => $slide->id,
            'slide_number'        => $slide->slide_number,
            'slide_order'         => $slide->slide_order,
            'ppt_module_id'       => $slide->ppt_module_id,
            'title'               => $slide->title,
            'content_text'        => $slide->content_text,
            'speaker_notes'       => $slide->speaker_notes,
            'discussion_points'   => $slide->discussion_points,
            'ai_explanation'      => $slide->ai_explanation,
            'ai_narration_script' => $slide->ai_narration_script,
            'ai_key_points'       => $slide->ai_key_points ?? [],
            'ai_trainer_notes'    => $slide->ai_trainer_notes,
            'ai_generated_at'     => $slide->ai_generated_at?->toDateTimeString(),
            'trainer_notes'       => $slide->trainer_notes,
            'audio_status'        => $slide->audio_status,
            'audio_url'           => $slide->audioUrl(),
            'audio_duration'      => $slide->audio_duration,
            'knowledge_check'     => $slide->knowledge_check,
            'image_url'           => $slide->imageUrl(),
            'is_removed'          => $slide->is_removed,
            'status_badge'        => $slide->getStatusBadge(),
        ];
    }
}
