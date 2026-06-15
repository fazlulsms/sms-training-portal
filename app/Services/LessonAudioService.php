<?php

namespace App\Services;

use App\Models\AiUsageLog;
use App\Models\ElearningLesson;
use App\Models\LessonAudio;
use App\Models\LessonBlock;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class LessonAudioService
{
    private const TTS_ENDPOINT = 'https://api.openai.com/v1/audio/speech';
    private const CHAT_ENDPOINT = 'https://api.openai.com/v1/chat/completions';
    private const TTS_MODEL = 'gpt-4o-mini-tts';
    private const AI_TEACHER_MODEL = 'gpt-4o-mini';

    // ──────────────────────────────────────────────────────────
    // Phase 1 — Lesson Narration
    // ──────────────────────────────────────────────────────────

    public function generateNarration(LessonAudio $audio): void
    {
        $lesson = $audio->lesson;
        $script = $this->extractNarrationScript($lesson);

        if (empty(trim($script))) {
            $this->markFailed($audio, 'No readable content found in this lesson to narrate.');
            return;
        }

        $this->runTts($audio, $script);
    }

    // ──────────────────────────────────────────────────────────
    // Phase 2 — AI Teacher Explanation
    // ──────────────────────────────────────────────────────────

    public function generateAiExplanation(LessonAudio $audio): void
    {
        $lesson = $audio->lesson;
        $lessonSummary = $this->extractNarrationScript($lesson);

        if (empty(trim($lessonSummary))) {
            $this->markFailed($audio, 'No readable content found in this lesson.');
            return;
        }

        $teachingScript = $this->buildTeachingScript($lesson, $lessonSummary);
        if ($teachingScript === null) {
            $this->markFailed($audio, 'AI teaching script generation failed.');
            return;
        }

        $this->runTts($audio, $teachingScript);
    }

    // ──────────────────────────────────────────────────────────
    // AI Coach — per-block explanation (synchronous, no queue)
    // ──────────────────────────────────────────────────────────

    public function generateBlockCoach(LessonAudio $audio): void
    {
        $block = $audio->block ?? LessonBlock::find($audio->block_id);
        if (!$block) {
            $this->markFailed($audio, 'Block not found.');
            return;
        }

        $lesson = $audio->lesson;
        $script = $this->buildBlockCoachScript($lesson, $block);

        if (!$script || empty(trim($script))) {
            $this->markFailed($audio, 'Failed to build AI Coach script for this block.');
            return;
        }

        $this->runTts($audio, $script);
    }

    // ──────────────────────────────────────────────────────────
    // AI Lesson Recap — end-of-lesson summary (synchronous)
    // ──────────────────────────────────────────────────────────

    public function generateLessonRecap(LessonAudio $audio): void
    {
        $lesson = $audio->lesson;
        $script = $this->buildLessonRecapScript($lesson);

        if (!$script || empty(trim($script))) {
            $this->markFailed($audio, 'Failed to build lesson recap script.');
            return;
        }

        $this->runTts($audio, $script);
    }

    // ──────────────────────────────────────────────────────────
    // Text extraction from lesson blocks
    // ──────────────────────────────────────────────────────────

    private function extractNarrationScript(ElearningLesson $lesson): string
    {
        $parts = [];

        // Opening
        $parts[] = 'Lesson: ' . $lesson->title . '.';
        if ($lesson->short_description) {
            $parts[] = $lesson->short_description;
        }
        if ($lesson->learning_objectives) {
            $parts[] = 'By the end of this lesson, you will: ' . $lesson->learning_objectives;
        }

        // Blocks
        $blocks = $lesson->blocks()->where('status', 'active')->orderBy('sort_order')->get();

        foreach ($blocks as $block) {
            $text = $this->extractBlockText($block);
            if ($text) $parts[] = $text;
        }

        return implode("\n\n", array_filter($parts));
    }

    private function extractBlockText(\App\Models\LessonBlock $block): string
    {
        $title   = $block->title ? ($block->title . ': ') : '';
        $content = $block->content ?? '';
        $data    = $block->getDecodedContent();

        return match ($block->block_type) {
            'rich_text', 'workplace_example' =>
                $title . strip_tags($content),

            'fun_fact' =>
                $title . ($data['ff_title'] ?? '') . '. ' . strip_tags($data['ff_content'] ?? ''),

            'reflection' => implode(' ', array_filter([
                $title . strip_tags($data['prompt'] ?? ''),
                isset($data['questions']) ? 'Consider: ' . implode('. ', (array) $data['questions']) : '',
            ])),

            'knowledge_check' => implode(' ', array_filter([
                $title . strip_tags($data['question'] ?? ''),
                isset($data['explanation']) ? 'Explanation: ' . strip_tags($data['explanation']) : '',
            ])),

            'myth_fact' => implode(' ', array_filter([
                $title,
                'Myth: ' . strip_tags($data['myth'] ?? ''),
                'Fact: ' . strip_tags($data['fact'] ?? ''),
            ])),

            'case_study' => implode(' ', array_filter([
                $title . strip_tags($data['scenario'] ?? $content),
                isset($data['outcome']) ? 'Outcome: ' . strip_tags($data['outcome']) : '',
            ])),

            'click_reveal' =>
                $title . strip_tags($data['question'] ?? '') . ' ' . strip_tags($data['reveal'] ?? ''),

            'accordion' => $title . implode('. ', array_map(
                fn($item) => ($item['heading'] ?? '') . ': ' . strip_tags($item['body'] ?? ''),
                (array) ($data['items'] ?? [])
            )),

            'slides' => $title . implode('. ', array_map(
                fn($s) => strip_tags($s['heading'] ?? '') . ': ' . strip_tags($s['body'] ?? ''),
                (array) ($data['slides'] ?? [])
            )),

            // Skip media-only blocks
            'video', 'audio', 'image', 'gallery', 'pdf', 'download', 'scenario', 'matching' => '',

            default => $title . strip_tags($content),
        };
    }

    // ──────────────────────────────────────────────────────────
    // AI teaching script generation (Phase 2)
    // ──────────────────────────────────────────────────────────

    private function buildTeachingScript(ElearningLesson $lesson, string $lessonContent): ?string
    {
        if (!config('ai.enabled') || empty(config('ai.api_key'))) {
            return null;
        }

        $courseName = $lesson->course?->name ?? 'this course';
        $prompt = <<<PROMPT
You are an expert trainer at SMS Training Academy specialising in ISO standards, HSE, quality management, and professional development.

Your task: Create a natural, engaging spoken audio explanation of the following lesson — as if you are a knowledgeable trainer speaking directly to a learner. This will be converted to speech, so write in a clear, conversational, spoken style.

Guidelines:
- Speak directly to the learner using "you" and "we".
- Explain WHY each concept matters in the real workplace.
- Use simple, clear language. No bullet points or markdown — write in natural prose paragraphs.
- Include helpful transitions like "Now, let's look at...", "This is important because...", "Think about it this way..."
- Total length: 400–600 words. Suitable for a 3–5 minute audio.
- Do NOT say "in this lesson" repeatedly. Vary your phrasing.

Course: {$courseName}
Lesson: {$lesson->title}

Lesson Content Summary:
{$lessonContent}

Now write the trainer's spoken explanation:
PROMPT;

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('ai.api_key'),
                'Content-Type'  => 'application/json',
            ])
            ->timeout(config('ai.timeout', 60))
            ->post(self::CHAT_ENDPOINT, [
                'model'       => self::AI_TEACHER_MODEL,
                'messages'    => [
                    ['role' => 'user', 'content' => $prompt],
                ],
                'max_tokens'  => 900,
                'temperature' => 0.7,
            ]);

            if ($response->failed()) {
                Log::error('LessonAudioService: teaching script generation failed', [
                    'lesson_id' => $lesson->id,
                    'status'    => $response->status(),
                    'body'      => $response->body(),
                ]);
                return null;
            }

            $script = $response->json('choices.0.message.content', '');
            $this->logUsage($response->json('usage', []), $lesson->id, 'ai_explanation_script');

            return trim($script);

        } catch (\Throwable $e) {
            Log::error('LessonAudioService: teaching script exception', [
                'lesson_id' => $lesson->id,
                'error'     => $e->getMessage(),
            ]);
            return null;
        }
    }

    // ──────────────────────────────────────────────────────────
    // AI Coach script builder (90-150 words, block-type-aware)
    // ──────────────────────────────────────────────────────────

    private function buildBlockCoachScript(ElearningLesson $lesson, LessonBlock $block): ?string
    {
        if (!config('ai.enabled') || empty(config('ai.api_key'))) {
            return null;
        }

        $blockContent = $this->extractBlockText($block);
        $courseName   = $lesson->course?->name ?? 'this course';

        $typeInstruction = match ($block->block_type) {
            'case_study'        => 'Approach this from an audit and compliance perspective. Highlight what practitioners should watch for and what real-world implications the case has.',
            'scenario'          => 'Guide the learner through the thinking process. Explain the key factors, what the right approach would be, and why.',
            'rich_text'         => 'Explain the concepts in an engaging, conversational way. Connect the theory to real workplace situations the learner would encounter.',
            'workplace_example' => 'Bring this real-world example to life. Explain why this situation matters and what the learner should take away from it.',
            'fun_fact'          => 'Share this fact with enthusiasm, then briefly explain why it matters in professional practice.',
            'myth_fact'         => 'Address the common misconception clearly and directly. Reinforce the correct understanding with a practical reason why it matters.',
            default             => 'Explain the content in a clear, engaging way that connects to real workplace situations.',
        };

        $blockTitle = $block->title ? "Block title: {$block->title}" : '';

        $prompt = <<<PROMPT
You are an AI Learning Coach at SMS Training Academy, a specialist in ISO standards, HSE, and professional development.

Lesson: "{$lesson->title}" (Course: "{$courseName}")
{$blockTitle}
Content: {$blockContent}

Your task: Write a short spoken coaching explanation (90-150 words) about this content for the learner. This will be converted to speech — write in natural spoken prose only, no bullet points or markdown.

Coaching approach: {$typeInstruction}

Rules:
- Speak warmly and directly to the learner using "you".
- 90-150 words maximum. Do not exceed this.
- Natural prose paragraphs only.
- Do NOT reference "this block" or the structural format — just teach naturally.

Write the coaching explanation now:
PROMPT;

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('ai.api_key'),
                'Content-Type'  => 'application/json',
            ])
            ->timeout(config('ai.timeout', 60))
            ->post(self::CHAT_ENDPOINT, [
                'model'       => self::AI_TEACHER_MODEL,
                'messages'    => [['role' => 'user', 'content' => $prompt]],
                'max_tokens'  => 250,
                'temperature' => 0.7,
            ]);

            if ($response->failed()) {
                Log::error('LessonAudioService: block coach script failed', [
                    'block_id' => $block->id,
                    'status'   => $response->status(),
                    'body'     => $response->body(),
                ]);
                return null;
            }

            $script = $response->json('choices.0.message.content', '');
            $this->logUsage($response->json('usage', []), $lesson->id, 'ai_coach_script');

            return trim($script);

        } catch (\Throwable $e) {
            Log::error('LessonAudioService: block coach script exception', [
                'block_id' => $block->id,
                'error'    => $e->getMessage(),
            ]);
            return null;
        }
    }

    // ──────────────────────────────────────────────────────────
    // Lesson recap script builder (200-300 words)
    // ──────────────────────────────────────────────────────────

    private function buildLessonRecapScript(ElearningLesson $lesson): ?string
    {
        if (!config('ai.enabled') || empty(config('ai.api_key'))) {
            return null;
        }

        $lessonContent = $this->extractNarrationScript($lesson);
        $courseName    = $lesson->course?->name ?? 'this course';

        $prompt = <<<PROMPT
You are an expert trainer at SMS Training Academy. You've just finished teaching a lesson called "{$lesson->title}" in the course "{$courseName}".

Your task: Record a closing lesson recap (200-300 words) covering:
1. What the learner has covered in this lesson
2. The most important concepts to remember
3. Common mistakes or misunderstandings to avoid
4. Practical tips for applying this knowledge in the workplace
5. A brief motivational closing

This will be converted to speech. Write in natural spoken prose only — no bullet points, no markdown, no lists.

Lesson content covered:
{$lessonContent}

Rules:
- Speak directly to the learner: "You've now learned...", "Remember that...", "When you apply this at work..."
- 200-300 words. Natural, warm, and motivational.
- End with an encouraging closing line.

Write the lesson recap now:
PROMPT;

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('ai.api_key'),
                'Content-Type'  => 'application/json',
            ])
            ->timeout(config('ai.timeout', 60))
            ->post(self::CHAT_ENDPOINT, [
                'model'       => self::AI_TEACHER_MODEL,
                'messages'    => [['role' => 'user', 'content' => $prompt]],
                'max_tokens'  => 600,
                'temperature' => 0.7,
            ]);

            if ($response->failed()) {
                Log::error('LessonAudioService: lesson recap script failed', [
                    'lesson_id' => $lesson->id,
                    'status'    => $response->status(),
                    'body'      => $response->body(),
                ]);
                return null;
            }

            $script = $response->json('choices.0.message.content', '');
            $this->logUsage($response->json('usage', []), $lesson->id, 'lesson_recap_script');

            return trim($script);

        } catch (\Throwable $e) {
            Log::error('LessonAudioService: lesson recap script exception', [
                'lesson_id' => $lesson->id,
                'error'     => $e->getMessage(),
            ]);
            return null;
        }
    }

    // ──────────────────────────────────────────────────────────
    // TTS API call
    // ──────────────────────────────────────────────────────────

    private function runTts(LessonAudio $audio, string $script): void
    {
        if (!config('ai.enabled')) {
            $this->markFailed($audio, 'AI feature is disabled.');
            return;
        }

        if (empty(config('ai.api_key'))) {
            $this->markFailed($audio, 'OpenAI API key is not configured.');
            return;
        }

        // Truncate to ~4000 chars to stay within TTS limits
        if (mb_strlen($script) > 4000) {
            $script = mb_substr($script, 0, 4000) . '... That concludes this lesson summary.';
        }

        $audio->update(['status' => 'processing']);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('ai.api_key'),
                'Content-Type'  => 'application/json',
            ])
            ->timeout(120)
            ->post(self::TTS_ENDPOINT, [
                'model'          => self::TTS_MODEL,
                'input'          => $script,
                'voice'          => $audio->voice,
                'response_format'=> 'mp3',
            ]);

            if ($response->failed()) {
                $errorMsg = $response->json('error.message') ?? $response->body();
                Log::error('LessonAudioService: TTS API failed', [
                    'lesson_id'  => $audio->lesson_id,
                    'audio_type' => $audio->audio_type,
                    'status'     => $response->status(),
                    'error'      => $errorMsg,
                ]);
                $this->markFailed($audio, 'TTS API error: ' . $errorMsg);
                return;
            }

            $filePath = $this->saveMp3($audio, $response->body());

            $audio->update([
                'status'       => 'ready',
                'file_path'    => $filePath,
                'generated_at' => now(),
                'error_message'=> null,
            ]);

        } catch (\Throwable $e) {
            Log::error('LessonAudioService: TTS exception', [
                'lesson_id' => $audio->lesson_id,
                'error'     => $e->getMessage(),
            ]);
            $this->markFailed($audio, $e->getMessage());
        }
    }

    // ──────────────────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────────────────

    private function saveMp3(LessonAudio $audio, string $mp3Bytes): string
    {
        $filename = ($audio->audio_type === 'ai_coach' && $audio->block_id)
            ? 'block_' . $audio->block_id . '.mp3'
            : $audio->audio_type . '.mp3';

        $path = 'lesson-audio/' . $audio->lesson_id . '/' . $filename;
        Storage::disk('public')->put($path, $mp3Bytes);
        return $path;
    }

    private function markFailed(LessonAudio $audio, string $message): void
    {
        $audio->update([
            'status'        => 'failed',
            'error_message' => $message,
        ]);
    }

    private function logUsage(array $usage, int $lessonId, string $feature): void
    {
        try {
            AiUsageLog::create([
                'user_id'            => null,
                'feature_name'       => $feature,
                'model'              => self::AI_TEACHER_MODEL,
                'prompt_tokens'      => $usage['prompt_tokens']     ?? 0,
                'completion_tokens'  => $usage['completion_tokens'] ?? 0,
                'total_tokens'       => $usage['total_tokens']      ?? 0,
                'estimated_cost_usd' => 0,
                'request_status'     => 'success',
                'error_message'      => null,
            ]);
        } catch (\Throwable) {}
    }
}
