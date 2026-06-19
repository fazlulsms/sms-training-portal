<?php

namespace App\Jobs;

use App\Http\Controllers\AiLessonContentController;
use App\Models\Course;
use App\Models\ElearningLesson;
use App\Models\ElearningQuiz;
use App\Models\ElearningQuizQuestion;
use App\Models\AiQuestionBank;
use App\Services\CourseQualityService;
use App\Services\AiQuestionBankService;
use App\Services\OpenAIService;
use App\Support\LtfContextBuilder;
use App\Support\LtfGenerationContext;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateModeBCourseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 10800; // 3 hours — enough for any course size
    public int $tries   = 1;     // No auto-retry; we handle failures internally

    public int    $courseId;
    public string $level;

    public function __construct(int $courseId, string $level)
    {
        $this->courseId = $courseId;
        $this->level    = $level;
    }

    // ─────────────────────────────────────────────────────────────────
    public function handle(): void
    {
        // Override PHP execution limit — the queue worker --timeout handles the real limit
        ini_set('max_execution_time', 0);
        set_time_limit(0);

        $course = Course::findOrFail($this->courseId);
        if ($course->ai_generation_version === 2 && $course->blueprint_status !== 'approved') {
            throw new \RuntimeException('V2 generation is blocked until the course blueprint is approved.');
        }

        $this->snap($course, 'running', 'Starting course generation…', [
            'phase'            => 'starting',
            'lessons_done'     => 0,
            'total_lessons'    => 0,
            'blocks_generated' => 0,
            'quizzes_done'     => 0,
            'total_modules'    => 0,
            'final_questions'  => 0,
        ]);

        try {
            $this->run($course);
        } catch (\Throwable $e) {
            Log::error('GenerateModeBCourseJob failed', [
                'course_id' => $this->courseId,
                'error'     => $e->getMessage(),
                'trace'     => substr($e->getTraceAsString(), 0, 500),
            ]);

            $failedProgress = array_merge((array) ($course->gen_progress ?? []), [
                'error'     => $e->getMessage(),
                'failed_at' => now()->toIso8601String(),
            ]);
            \Illuminate\Support\Facades\DB::table('courses')
                ->where('id', $course->id)
                ->update([
                    'gen_status'   => 'failed',
                    'gen_progress' => json_encode($failedProgress),
                ]);

            throw $e;
        }
    }

    // ─────────────────────────────────────────────────────────────────
    private function run(Course $course): void
    {
        // Build LTF context once — all generation phases share it
        $ltfContext = LtfContextBuilder::fromCourse($course);

        $aiStructure  = $course->ai_course_structure ?? [];
        $aiModules    = $aiStructure['modules'] ?? [];
        $totalModules = count($aiModules);

        // Collect all content lessons (skip existing assessments / quiz lessons)
        $allLessons = ElearningLesson::where('course_id', $course->id)
            ->where('lesson_type', '!=', 'assessment')
            ->orderBy('lesson_order')
            ->get();

        $totalLessons     = $allLessons->count();
        $lessonsDone      = 0;
        $blocksDone       = 0;
        $quizzesDone      = 0;
        $lessonsAttempted = 0; // lessons where AI was actually called (not skipped)

        // ── Phase 1 & 2: Lesson content + module quizzes ─────────────
        // Use ->values() to get a 0-based indexed collection (fixes ->has(idx) bug)
        $lessonsArr = $allLessons->values();
        $lessonPtr  = 0;

        if (!empty($aiModules)) {
            // Module-aware generation: preserves lesson_type hints from AI structure
            foreach ($aiModules as $modIdx => $moduleData) {
                $moduleTitle  = $moduleData['title'] ?? 'Module ' . ($modIdx + 1);
                $aiLessons    = $moduleData['lessons'] ?? [];
                $modLessonIds = [];

                foreach ($aiLessons as $i => $aiLesson) {
                    if (!$lessonsArr->has($lessonPtr)) break;

                    $lesson     = $lessonsArr->get($lessonPtr);
                    $lessonType = $aiLesson['lesson_type'] ?? 'concept';
                    $lessonPtr++;
                    $lessonsDone++;

                    // Skip if already generated (makes job resumable)
                    if ($lesson->allBlocks()->exists()) {
                        $modLessonIds[] = $lesson->id;
                        $this->snap($course, 'running',
                            "Lesson {$lessonsDone}/{$totalLessons} already done — skipping",
                            $this->prog($lessonsDone, $totalLessons, $blocksDone, $quizzesDone, $totalModules, 'lessons'));
                        continue;
                    }

                    $this->snap($course, 'running',
                        "Generating Lesson {$lessonsDone} of {$totalLessons}: {$lesson->title}",
                        $this->prog($lessonsDone, $totalLessons, $blocksDone, $quizzesDone, $totalModules, 'lessons'));

                    $count = $this->generateOneLessonContent($course, $lesson, $lessonType, $lessonsDone, $totalLessons, $ltfContext);

                    $lessonsAttempted++;
                    $blocksDone    += $count;
                    $modLessonIds[] = $lesson->id;

                    if ($count > 0) {
                        \Illuminate\Support\Facades\DB::table('elearning_lessons')
                            ->where('id', $lesson->id)
                            ->update(['status' => 'published']);
                    }

                    // Early quota detection: if first 3 real attempts all return 0, abort
                    if ($lessonsAttempted === 3 && $blocksDone === 0) {
                        throw new \RuntimeException(
                            'AI quota exhausted — first 3 lesson generations all returned 0 blocks. ' .
                            'Check your OpenAI usage at platform.openai.com/usage and try again when quota resets.'
                        );
                    }
                }

                // Module quiz
                if (!empty($modLessonIds)) {
                    $quizzesDone++;
                    $this->snap($course, 'running',
                        "Generating Module Quiz {$quizzesDone} of {$totalModules}: {$moduleTitle}",
                        $this->prog($lessonsDone, $totalLessons, $blocksDone, $quizzesDone, $totalModules, 'module_quiz'));

                    $this->generateModuleQuiz($course, $modIdx + 1, $moduleTitle, $modLessonIds, $ltfContext);
                }
            }

            // Handle any extra lessons not matched to AI modules (shouldn't happen, but safe)
            while ($lessonsArr->has($lessonPtr)) {
                $lesson = $lessonsArr->get($lessonPtr++);
                $lessonsDone++;
                if (!$lesson->allBlocks()->exists()) {
                    $count            = $this->generateOneLessonContent($course, $lesson, 'concept', $lessonsDone, $totalLessons, $ltfContext);
                    $lessonsAttempted++;
                    $blocksDone      += $count;
                    if ($count > 0) {
                        \Illuminate\Support\Facades\DB::table('elearning_lessons')->where('id', $lesson->id)->update(['status' => 'published']);
                    }
                }
            }

        } else {
            // Flat fallback (no AI module structure — generate all lessons sequentially)
            foreach ($lessonsArr as $lesson) {
                $lessonsDone++;
                if ($lesson->allBlocks()->exists()) continue;

                $this->snap($course, 'running',
                    "Generating Lesson {$lessonsDone} of {$totalLessons}: {$lesson->title}",
                    $this->prog($lessonsDone, $totalLessons, $blocksDone, $quizzesDone, $totalModules, 'lessons'));

                $count            = $this->generateOneLessonContent($course, $lesson, 'concept', $lessonsDone, $totalLessons, $ltfContext);
                $lessonsAttempted++;
                $blocksDone      += $count;

                if ($count > 0) {
                    \Illuminate\Support\Facades\DB::table('elearning_lessons')->where('id', $lesson->id)->update(['status' => 'published']);
                }

                if ($lessonsAttempted === 3 && $blocksDone === 0) {
                    throw new \RuntimeException(
                        'AI quota exhausted — first 3 lesson generations all returned 0 blocks. ' .
                        'Check your OpenAI usage at platform.openai.com/usage and try again when quota resets.'
                    );
                }
            }
        }

        // ── Guard: fail fast if AI quota exhausted (every attempted lesson returned 0 blocks) ──
        if ($lessonsAttempted > 0 && $blocksDone === 0) {
            throw new \RuntimeException(
                'AI quota exhausted — all lesson generations returned 0 blocks. ' .
                'Check your OpenAI usage at platform.openai.com/usage and try again when quota resets.'
            );
        }

        // ── Phase 3: Final Assessment ─────────────────────────────────
        $this->snap($course, 'running', 'Generating Final Course Assessment…',
            $this->prog($lessonsDone, $totalLessons, $blocksDone, $quizzesDone, $totalModules, 'final_assessment'));

        $finalQs = $this->generateFinalAssessment($course, $ltfContext);

        $estimatedMinutes = (int) ElearningLesson::where('course_id', $course->id)->sum('estimated_learning_minutes');
        $course->update(['estimated_learning_minutes' => $estimatedMinutes]);
        if ($course->ai_generation_version === 2) {
            $quality = app(CourseQualityService::class)->evaluate($course->refresh());
            $course->update([
                'content_quality_score' => $quality['score'],
                'content_quality_report' => $quality['checks'],
            ]);
        }

        // ── Done ─────────────────────────────────────────────────────
        $doneProgress = json_encode([
            'phase'            => 'completed',
            'current_step'     => 'Course generation complete! ' . $totalLessons . ' lessons · ' . $blocksDone . ' blocks · ' . $quizzesDone . ' quizzes · ' . $finalQs . ' exam questions',
            'lessons_done'     => $lessonsDone,
            'total_lessons'    => $totalLessons,
            'blocks_generated' => $blocksDone,
            'quizzes_done'     => $quizzesDone,
            'total_modules'    => $totalModules,
            'final_questions'  => $finalQs,
            'completed_at'     => now()->toIso8601String(),
        ]);
        \Illuminate\Support\Facades\DB::table('courses')
            ->where('id', $course->id)
            ->update([
                'gen_status'       => 'completed',
                'gen_completed_at' => now(),
                'gen_progress'     => $doneProgress,
            ]);
    }

    // ─────────────────────────────────────────────────────────────────
    // Generate content for one lesson (detects special lesson types by title)
    // ─────────────────────────────────────────────────────────────────
    private function generateOneLessonContent(
        Course $course,
        ElearningLesson $lesson,
        string $lessonType,
        int $num,
        int $total,
        ?LtfGenerationContext $ltfContext = null,
    ): int {
        if (str_starts_with($lesson->title, 'Course Introduction:')) {
            return $this->generateSpecialLesson($course, $lesson, 'course_intro');
        }
        if (str_starts_with($lesson->title, 'Course Conclusion:')) {
            return $this->generateSpecialLesson($course, $lesson, 'course_conclusion');
        }

        return AiLessonContentController::generateAndSaveBlocks(
            $course, $lesson, $this->level, $lessonType, $num, $total, $ltfContext
        );
    }

    // ─────────────────────────────────────────────────────────────────
    // Special lessons: Course Introduction and Course Conclusion
    // ─────────────────────────────────────────────────────────────────
    private function generateSpecialLesson(Course $course, ElearningLesson $lesson, string $type): int
    {
        try {
            $isIntro = $type === 'course_intro';

            $objectives = collect(preg_split('/[\n,]+/', $course->learning_objectives ?? ''))
                ->map(fn($s) => trim($s))->filter()->implode('; ') ?: 'Not specified';

            $moduleList = collect(($course->ai_course_structure ?? [])['modules'] ?? [])
                ->map(fn($m, $i) => 'Module ' . ($i + 1) . ': ' . ($m['title'] ?? ''))
                ->implode(' | ');
            $sourceContext = '';
            if ($course->ai_generation_version === 2) {
                $sources = $lesson->knowledgeResources()->where('status', 'approved')->whereNotNull('extracted_text')->get();
                if ($sources->isEmpty()) return 0;
                $sourceContext = "\nUse ONLY these approved Knowledge Hub sources:\n".$sources->map(
                    fn ($source) => "[RESOURCE {$source->id}] {$source->title}\n".\Illuminate\Support\Str::limit($source->extracted_text, 12000)."\n[/RESOURCE]"
                )->implode("\n");
            }

            if ($isIntro) {
                $prompt = <<<PROMPT
You are an expert eLearning instructional designer. Output ONLY valid JSON.

Generate a course INTRODUCTION lesson for:
Course: {$course->name}
Duration: {$course->duration}
Level: {$this->level}
Target Audience: {$course->who_should_attend}
Learning Objectives: {$objectives}
Modules: {$moduleList}
{$sourceContext}

Create 8–10 blocks that welcome learners, explain why this course matters, overview the modules,
set expectations, and include a pre-assessment reflection. Tone: professional, encouraging, motivating.

Return a SINGLE valid JSON object:
{"lesson_type":"awareness","blocks":[{"type":"rich_text","title":"Welcome","content":"<p>...</p>"},{"type":"fun_fact","title":"Did You Know?","icon":"💡","ff_title":"...","ff_content":"..."},{"type":"rich_text","title":"What You Will Learn","content":"<ul><li>✅ ...</li></ul>"},{"type":"rich_text","title":"Your Learning Journey","content":"<p>...</p>"},{"type":"reflection","title":"Before You Begin","prompt":"...","questions":["...","...","..."]},{"type":"knowledge_check","title":"Quick Pre-Check","question":"...","kc_type":"single","options":[{"text":"...","correct":true},{"text":"...","correct":false},{"text":"...","correct":false},{"text":"...","correct":false}],"explanation":"..."},{"type":"rich_text","title":"How to Complete This Course","content":"<ul>...</ul>"},{"type":"rich_text","title":"Let's Begin","content":"<p>...</p>"}]}
PROMPT;
            } else {
                $certInfo = $course->certification_info ?? 'Certificate of Completion';
                $prompt = <<<PROMPT
You are an expert eLearning instructional designer. Output ONLY valid JSON.

Generate a course CONCLUSION lesson for:
Course: {$course->name}
Level: {$this->level}
Certificate: {$certInfo}
Learning Objectives achieved: {$objectives}
Modules completed: {$moduleList}
{$sourceContext}

Create 8–10 blocks that congratulate learners, recap skills gained, explain real-world application,
describe the certificate, and provide clear next professional development steps. Tone: celebratory, motivating, professional.

Return a SINGLE valid JSON object:
{"lesson_type":"awareness","blocks":[{"type":"rich_text","title":"Congratulations!","content":"<p>...</p>"},{"type":"rich_text","title":"What You Have Achieved","content":"<ul><li>✅ ...</li></ul>"},{"type":"fun_fact","title":"Your Achievement","icon":"🏆","ff_title":"...","ff_content":"..."},{"type":"rich_text","title":"Applying Your Knowledge","content":"<p>...</p>"},{"type":"reflection","title":"Your Professional Plan","prompt":"...","questions":["...","...","..."]},{"type":"rich_text","title":"Your Certificate","content":"<p>...</p>"},{"type":"rich_text","title":"Continue Your Development","content":"<ul>...</ul>"},{"type":"rich_text","title":"Thank You","content":"<p>...</p>"}]}
PROMPT;
            }

            $ai     = app(OpenAIService::class);
            $result = $ai->generateText($prompt, 'special_lesson', null, 4000);

            if (!$result['success']) return 0;

            $raw     = preg_replace(['/^```json\s*/i', '/```\s*$/'], '', trim($result['text'] ?? ''));
            $decoded = json_decode($raw, true);

            if (!is_array($decoded) || empty($decoded['blocks'])) return 0;

            return AiLessonContentController::createBlocksFromAi($lesson, $decoded['blocks']);

        } catch (\Throwable $e) {
            Log::warning('generateSpecialLesson failed', ['lesson_id' => $lesson->id, 'type' => $type, 'error' => $e->getMessage()]);
            return 0;
        }
    }

    // ─────────────────────────────────────────────────────────────────
    // Module Quiz — mixed question types, framework-aware, placement-aware
    // ─────────────────────────────────────────────────────────────────
    private function generateModuleQuiz(
        Course $course,
        int $modIndex,
        string $moduleTitle,
        array $lessonIds,
        ?LtfGenerationContext $ltfContext = null,
    ): void {
        // Skip if quiz already generated for this module (resumability guard)
        $alreadyExists = ElearningLesson::where('course_id', $course->id)
            ->where('lesson_type', 'assessment')
            ->where('title', 'like', "Module {$modIndex} Knowledge Check%")
            ->exists();
        if ($alreadyExists) return;

        try {
            $moduleLessons = ElearningLesson::where('course_id', $course->id)
                ->whereIn('id', $lessonIds)
                ->orderBy('lesson_order')
                ->get(['id', 'title', 'learning_objectives', 'blueprint_module_id']);

            // Build lesson summaries for the AI prompt
            $summaries = $moduleLessons
                ->map(fn($l) => "- {$l->title}" . ($l->learning_objectives ? ": {$l->learning_objectives}" : ''))
                ->implode("\n");
            $sourceResources = $moduleLessons->flatMap(fn ($lesson) => $lesson->knowledgeResources)
                ->unique('id')->where('status', 'approved')->values();
            $sourceText = $sourceResources->map(
                fn ($source) => "[RESOURCE {$source->id}] {$source->title}\n".\Illuminate\Support\Str::limit($source->extracted_text, 10000)."\n[/RESOURCE]"
            )->implode("\n");

            $qCount = match ($this->level) { 'Awareness' => 3, 'Advanced' => 5, default => 4 };

            // Question distribution by level (Phase 3)
            $tfCount       = 1;
            $scenarioCount = match ($this->level) { 'Awareness' => 0, 'Advanced' => 2, default => 1 };
            $mcqCount      = $qCount - $tfCount - $scenarioCount;

            $frameworkHint  = $ltfContext?->frameworkHint ?? null;
            $isAuditor      = in_array($frameworkHint, ['lead_auditor', 'internal_auditor', 'auditor_conversion', 'experienced_auditor']);
            $isAwareness    = in_array($frameworkHint, ['awareness', 'foundation', 'community_awareness']);

            $auditorGuidance = $isAuditor
                ? "\nFor auditor questions: include evidence identification, finding classification, scenario decision-making, and practical audit application. Use realistic audit scenario narratives."
                : ($isAwareness
                    ? "\nFor awareness questions: focus on recognising concepts, understanding why something matters, and simple workplace scenarios. Avoid auditor-level terminology."
                    : '');

            $ltfAssessment = ($ltfContext && $ltfContext->hasContext())
                ? "\n\n" . $ltfContext->toAssessmentInstructions()
                : '';

            $distLine = ($scenarioCount > 0)
                ? "- {$mcqCount} MCQ (4 options: a, b, c, d)\n- {$tfCount} True/False (a=True, b=False; set options c and d to null)\n- {$scenarioCount} Scenario MCQ (workplace or audit scenario with 4 options)"
                : "- {$mcqCount} MCQ (4 options: a, b, c, d)\n- {$tfCount} True/False (a=True, b=False; set options c and d to null)";

            $result = app(OpenAIService::class)->generateText(
                "ROLE: eLearning assessment designer. Output ONLY valid JSON (no markdown fences).\n\n" .
                "Generate {$qCount} quiz questions for Module {$modIndex}: {$moduleTitle}.\n" .
                "Course: {$course->name} | Level: {$this->level}\n" .
                "Lessons covered:\n{$summaries}{$ltfAssessment}\n\nAPPROVED SOURCE TEXT:\n{$sourceText}\n\n" .
                "CONTENT INTEGRITY RULE (mandatory): Generate questions ONLY from concepts explicitly taught " .
                "within the lesson content listed above. Do not introduce concepts, clauses, requirements, " .
                "terminology, or facts that were not covered in these specific lessons. Every question must " .
                "be answerable from the lesson content — not from prior knowledge or external sources.\n\n" .
                "QUESTION DISTRIBUTION:\n{$distLine}\n{$auditorGuidance}\n\n" .
                "Each question MUST include 'question_type': 'mcq' | 'truefalse' | 'scenario'.\n" .
                "For True/False: options c and d must be null. Correct answer is 'a' (True) or 'b' (False).\n\n" .
                "Each question must include source_resource_id using one of these IDs: ".$sourceResources->pluck('id')->implode(', ').".\n" .
                "Return ONLY: {\"questions\":[{\"question_text\":\"...\",\"question_type\":\"mcq\",\"source_resource_id\":1,\"options\":{\"a\":\"...\",\"b\":\"...\",\"c\":\"...\",\"d\":\"...\"},\"correct_answer\":\"a\",\"explanation\":\"...\"}]}",
                'module_quiz', null, 2000
            );

            if (!$result['success']) return;

            $raw     = preg_replace(['/^```json\s*/i', '/```\s*$/'], '', trim($result['text'] ?? ''));
            $decoded = json_decode($raw, true);
            if (!is_array($decoded) || empty($decoded['questions'])) return;

            // ── Placement fix: insert check immediately after this module's last lesson ──
            $maxModuleOrder = ElearningLesson::whereIn('id', $lessonIds)->max('lesson_order') ?? 0;
            $checkOrder     = $maxModuleOrder + 1;

            // Shift everything that comes after this module up by 1 to create a gap
            ElearningLesson::where('course_id', $course->id)
                ->where('lesson_order', '>', $maxModuleOrder)
                ->increment('lesson_order');

            // Course assessment policy for attempt limit
            $maxAttempts = $course->module_check_max_attempts ?? 3;

            $quizLesson = ElearningLesson::create([
                'course_id'         => $course->id,
                'title'             => "Module {$modIndex} Knowledge Check: {$moduleTitle}",
                'short_description' => "Test your understanding of {$moduleTitle}.",
                'lesson_order'      => $checkOrder,
                'status'            => 'draft',
                'lesson_type'       => 'assessment',
                'completion_rule'   => 'pass_quiz',
            ]);

            $quiz = ElearningQuiz::create([
                'lesson_id'   => $quizLesson->id,
                'title'       => "Module {$modIndex} Knowledge Check",
                'description' => "Key concepts from {$moduleTitle}.",
                'pass_mark'   => 70,
                'max_attempt' => $maxAttempts,
                'status'      => 'active',
            ]);

            // Map lesson index to actual lesson ID for source tracking
            $lessonIdByIndex = $moduleLessons->values()->mapWithKeys(fn($l, $i) => [$i => $l->id]);

            foreach ($decoded['questions'] as $idx => $q) {
                if (empty($q['question_text']) || empty($q['options'])) continue;
                $qType = in_array($q['question_type'] ?? '', ['truefalse', 'scenario']) ? $q['question_type'] : 'mcq';
                $isTF  = ($qType === 'truefalse');
                // Best-effort source: distribute questions across module lessons proportionally
                $srcIdx      = count($lessonIds) > 0 ? (int) floor($idx / max(1, $qCount / count($lessonIds))) : 0;
                $sourceLsnId = $lessonIdByIndex->get(min($srcIdx, count($lessonIds) - 1));
                $resourceId = (int) ($q['source_resource_id'] ?? 0);
                if (!$sourceResources->contains('id', $resourceId)) {
                    $resourceId = $sourceResources->count() === 1 ? $sourceResources->first()->id : null;
                }
                if (!$resourceId && $course->ai_generation_version === 2) continue;
                $bank = $resourceId ? app(AiQuestionBankService::class)->store([
                    'course_id' => $course->id,
                    'blueprint_module_id' => $moduleLessons->first()?->blueprint_module_id,
                    'lesson_id' => $sourceLsnId,
                    'knowledge_resource_id' => $resourceId,
                    'question_text' => $q['question_text'],
                    'question_type' => $qType,
                    'difficulty' => 'medium',
                    'options' => $q['options'],
                    'correct_answer' => strtolower($q['correct_answer'] ?? 'a'),
                    'explanation' => $q['explanation'] ?? null,
                    'status' => 'approved',
                ]) : null;
                if ($course->ai_generation_version === 2 && !$bank) continue;
                if ($bank) $course->questionBank()->syncWithoutDetaching([$bank->id]);
                $questionData = [
                    'question_text'    => $q['question_text'],
                    'question_type'    => $qType,
                    'option_a'         => $q['options']['a'] ?? '',
                    'option_b'         => $q['options']['b'] ?? '',
                    'option_c'         => $isTF ? null : ($q['options']['c'] ?? ''),
                    'option_d'         => $isTF ? null : ($q['options']['d'] ?? ''),
                    'correct_answer'   => strtolower($q['correct_answer'] ?? 'a'),
                    'explanation'      => $q['explanation'] ?? null,
                    'difficulty'       => 'medium',
                    'module_index'     => $modIndex,
                    'source_lesson_id' => $sourceLsnId,
                    'knowledge_resource_id' => $resourceId,
                    'marks'            => 1,
                    'status'           => 'active',
                ];
                $bank
                    ? ElearningQuizQuestion::firstOrCreate(['quiz_id' => $quiz->id, 'question_bank_id' => $bank->id], $questionData)
                    : ElearningQuizQuestion::create(['quiz_id' => $quiz->id, ...$questionData]);
            }

        } catch (\Throwable $e) {
            Log::warning('GenerateModeBCourseJob: module quiz failed', ['module' => $moduleTitle, 'error' => $e->getMessage()]);
        }
    }

    // ─────────────────────────────────────────────────────────────────
    // Final Assessment — Phase 4: dedup-aware, framework-styled
    // ─────────────────────────────────────────────────────────────────
    private function generateFinalAssessment(Course $course, ?LtfGenerationContext $ltfContext = null): int
    {
        // Skip if final assessment already exists (resumability guard)
        $exists = ElearningLesson::where('course_id', $course->id)
            ->where('lesson_type', 'assessment')
            ->where('title', 'like', 'Final Course Assessment%')
            ->exists();
        if ($exists) {
            $quiz = ElearningQuiz::whereHas('lesson', fn($q) => $q->where('course_id', $course->id)->where('title', 'like', 'Final Course Assessment%'))->first();
            return $quiz ? ElearningQuizQuestion::where('quiz_id', $quiz->id)->count() : 0;
        }

        try {
            $qCount = $course->assessment_policy === 'auditor'
                ? 40
                : match ($this->level) { 'Awareness' => 15, 'Advanced' => 20, default => 20 };
            $blueprintModules = $course->blueprintModules()->orderBy('module_order')->get();
            $basePerModule = $blueprintModules->isNotEmpty() ? intdiv($qCount, $blueprintModules->count()) : 0;
            $remainder = $blueprintModules->isNotEmpty() ? $qCount % $blueprintModules->count() : 0;
            $moduleDistribution = $blueprintModules->map(
                fn ($module, $index) => "Module {$module->module_order}: ".($basePerModule + ($index < $remainder ? 1 : 0))." questions"
            )->implode("\n");

            $aiStructure = $course->ai_course_structure ?? [];
            $modulesText = collect($aiStructure['modules'] ?? [])
                ->map(fn($m, $i) => 'Module ' . ($i + 1) . ': ' . ($m['title'] ?? '') .
                    ' — ' . collect($m['lessons'] ?? [])->pluck('title')->implode('; '))
                ->implode("\n") ?: 'All course modules';
            $courseSources = $course->knowledgeResources()->where('status', 'approved')->whereNotNull('extracted_text')->get();
            $sourceText = $courseSources->map(
                fn ($source) => "[RESOURCE {$source->id}] {$source->title}\n".\Illuminate\Support\Str::limit($source->extracted_text, 10000)."\n[/RESOURCE]"
            )->implode("\n");

            $objectives = collect(preg_split('/[\n,]+/', $course->learning_objectives ?? ''))
                ->map(fn($s) => trim($s))->filter()->implode('; ') ?: 'Not specified';

            $mcq      = (int) round($qCount * 0.60);
            $tf       = (int) round($qCount * 0.20);
            $scenario = $qCount - $mcq - $tf;

            // Phase 4: Pull module quiz question texts as deduplication context
            $moduleQuizTopics = ElearningQuizQuestion::whereHas('quiz.lesson', function ($q) use ($course) {
                $q->where('course_id', $course->id)
                  ->where('lesson_type', 'assessment')
                  ->where('title', 'like', 'Module%Knowledge Check%');
            })->pluck('question_text')->take(40);

            $dedupContext = $moduleQuizTopics->isNotEmpty()
                ? "\nMODULE QUIZ TOPICS (do NOT repeat these verbatim — create new questions that assess the same learning outcomes at a final-exam level with greater depth and integration):\n" .
                  $moduleQuizTopics->map(fn($t) => '- ' . $t)->implode("\n")
                : '';

            // Phase 4: Framework-aware question style guidance
            $frameworkHint = $ltfContext?->frameworkHint ?? null;
            $isAuditor     = in_array($frameworkHint, ['lead_auditor', 'internal_auditor', 'auditor_conversion', 'experienced_auditor']);
            $isAwareness   = in_array($frameworkHint, ['awareness', 'foundation', 'community_awareness']);

            $styleGuidance = $isAuditor
                ? "\nAUDITOR COURSE STYLE: Prioritise scenario-based questions, evidence evaluation, finding classification, conformity/nonconformity determination, and audit decision-making at professional-practitioner level."
                : ($isAwareness
                    ? "\nAWARENESS COURSE STYLE: Use simple workplace scenario questions. Focus on recognition, understanding, and practical relevance. Avoid auditor-level terminology or technical specifications."
                    : "\nInclude applied scenario questions that test practical application and integration of concepts across modules.");

            $ltfFinalExam = ($ltfContext && $ltfContext->hasContext())
                ? "\n" . $ltfContext->toFinalExamInstructions()
                : '';

            $result = app(OpenAIService::class)->generateText(
                "ROLE: Expert eLearning assessment designer. Output ONLY valid JSON (no markdown).\n\n" .
                "Generate a final assessment with EXACTLY {$qCount} questions for: {$course->name}\n" .
                "Level: {$this->level} | Objectives: {$objectives}\n" .
                "Modules:\n{$modulesText}{$ltfFinalExam}\n\nAPPROVED SOURCE TEXT:\n{$sourceText}\n\n" .
                "Distribution: {$mcq} MCQ | {$tf} True/False (a=True, b=False; c and d null) | {$scenario} Scenario MCQ\n" .
                "Difficulty: ~30% easy, ~50% medium, ~20% hard. Cover ALL modules proportionally.\n" .
                "MANDATORY MODULE DISTRIBUTION:\n{$moduleDistribution}\n" .
                "{$styleGuidance}\n" .
                "{$dedupContext}\n\n" .
                "CONTENT INTEGRITY RULE (mandatory): Generate questions ONLY from concepts, examples, scenarios, " .
                "and explanations explicitly taught within the course modules listed above. Do not introduce " .
                "standard clauses, requirements, terminology, or facts that were not covered in the course content. " .
                "Every correct answer must be traceable to the course lessons.\n\n" .
                "Each question must include a valid module_index and source_resource_id. Source IDs: ".$courseSources->pluck('id')->implode(', ').".\n" .
                "Return: {\"questions\":[{\"question_text\":\"...\",\"question_type\":\"mcq|truefalse|scenario\",\"difficulty\":\"easy|medium|hard\",\"module_index\":1,\"source_resource_id\":1,\"options\":{\"a\":\"...\",\"b\":\"...\",\"c\":\"...\",\"d\":\"...\"},\"correct_answer\":\"a\",\"explanation\":\"...\"}]}",
                'final_assessment', null, 6000
            );

            if (!$result['success']) return 0;

            $raw     = preg_replace(['/^```json\s*/i', '/```\s*$/'], '', trim($result['text'] ?? ''));
            $decoded = json_decode($raw, true);
            if (!is_array($decoded) || empty($decoded['questions'])) return 0;

            $passMark = (int) ($course->passing_score ?: 70);
            $finalLesson = ElearningLesson::create([
                'course_id'              => $course->id,
                'title'                  => 'Final Course Assessment: ' . $course->name,
                'short_description'      => "Comprehensive final assessment. Pass mark: {$passMark}%.",
                'lesson_order'           => ElearningLesson::where('course_id', $course->id)->max('lesson_order') + 1,
                'status'                 => 'draft',
                'lesson_type'            => 'assessment',
                'completion_rule'        => 'pass_quiz',
                'required_passing_score' => $passMark,
            ]);

            $finalMaxAttempts = $course->final_exam_max_attempts ?? 3;

            $quiz = ElearningQuiz::create([
                'lesson_id'   => $finalLesson->id,
                'title'       => 'Final Assessment — ' . $course->name,
                'description' => "{$qCount}-question assessment covering all modules. Pass mark {$passMark}%, {$finalMaxAttempts} attempts.",
                'pass_mark'   => $passMark,
                'max_attempt' => $finalMaxAttempts,
                'status'      => 'active',
            ]);

            $created = 0;
            foreach ($decoded['questions'] as $idx => $q) {
                if (empty($q['question_text']) || empty($q['options'])) continue;
                $qType = $q['question_type'] ?? 'mcq';
                $moduleIndex = (int) ($q['module_index'] ?? 0);
                $blueprintModule = $blueprintModules->firstWhere('module_order', $moduleIndex);
                if ($course->ai_generation_version === 2 && !$blueprintModule) continue;
                $resourceId = (int) ($q['source_resource_id'] ?? 0);
                if (!$courseSources->contains('id', $resourceId)) {
                    $resourceId = $courseSources->count() === 1 ? $courseSources->first()->id : null;
                }
                if (!$resourceId && $course->ai_generation_version === 2) continue;
                $bank = $resourceId ? app(AiQuestionBankService::class)->store([
                    'course_id' => $course->id,
                    'blueprint_module_id' => $blueprintModule?->id,
                    'knowledge_resource_id' => $resourceId,
                    'question_text' => $q['question_text'],
                    'question_type' => $qType,
                    'difficulty' => in_array($q['difficulty'] ?? '', ['easy','medium','hard']) ? $q['difficulty'] : 'medium',
                    'options' => $q['options'],
                    'correct_answer' => strtolower($q['correct_answer'] ?? 'a'),
                    'explanation' => $q['explanation'] ?? null,
                    'status' => 'approved',
                ]) : null;
                if ($course->ai_generation_version === 2 && !$bank) continue;
                if ($bank) $course->questionBank()->syncWithoutDetaching([$bank->id]);
                $questionData = [
                    'question_text'  => $q['question_text'],
                    'question_type'  => $qType,
                    'option_a'       => $q['options']['a'] ?? '',
                    'option_b'       => $q['options']['b'] ?? '',
                    'option_c'       => $qType === 'truefalse' ? null : ($q['options']['c'] ?? ''),
                    'option_d'       => $qType === 'truefalse' ? null : ($q['options']['d'] ?? ''),
                    'correct_answer' => strtolower($q['correct_answer'] ?? 'a'),
                    'explanation'    => $q['explanation'] ?? null,
                    'difficulty'     => in_array($q['difficulty'] ?? '', ['easy', 'medium', 'hard']) ? $q['difficulty'] : 'medium',
                    'module_index'   => $moduleIndex ?: null,
                    'knowledge_resource_id' => $resourceId,
                    'marks'          => 1,
                    'status'         => 'active',
                ];
                $bank
                    ? ElearningQuizQuestion::firstOrCreate(['quiz_id' => $quiz->id, 'question_bank_id' => $bank->id], $questionData)
                    : ElearningQuizQuestion::create(['quiz_id' => $quiz->id, ...$questionData]);
                $created = $quiz->questions()->count();
            }

            return $created;

        } catch (\Throwable $e) {
            Log::warning('GenerateModeBCourseJob: final assessment failed', ['error' => $e->getMessage()]);
            return 0;
        }
    }

    // ─────────────────────────────────────────────────────────────────
    private function prog(int $done, int $total, int $blocks, int $quizzes, int $modules, string $phase): array
    {
        return compact('done', 'total', 'blocks', 'quizzes', 'modules', 'phase') + [
            'lessons_done'     => $done,
            'total_lessons'    => $total,
            'blocks_generated' => $blocks,
            'quizzes_done'     => $quizzes,
            'total_modules'    => $modules,
            'final_questions'  => 0,
        ];
    }

    private function snap(Course $course, string $status, string $stepLabel, array $extra = []): void
    {
        $progress = array_merge($extra, [
            'current_step' => $stepLabel,
            'updated_at'   => now()->toIso8601String(),
        ]);

        // Use raw DB update to bypass any model caching / event issues
        \Illuminate\Support\Facades\DB::table('courses')
            ->where('id', $course->id)
            ->update([
                'gen_status'   => $status,
                'gen_progress' => json_encode($progress),
            ]);

        // Keep in-memory model in sync
        $course->gen_status   = $status;
        $course->gen_progress = $progress;
    }
}
