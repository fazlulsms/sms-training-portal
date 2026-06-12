<?php

namespace App\Services;

use App\Models\AiPromptTemplate;
use App\Models\AiUsageLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAIService
{
    private const API_BASE = 'https://api.openai.com/v1';

    // ──────────────────────────────────────────────────────────
    // Public API
    // ──────────────────────────────────────────────────────────

    /**
     * Send a plain-text prompt and return the generated text.
     *
     * @param  string   $prompt
     * @param  string   $feature   Feature name used for usage logging (e.g. 'test', 'quiz_generator')
     * @param  int|null $userId    Authenticated user ID for audit trail
     * @param  int      $maxTokens Maximum tokens in the response
     * @return array{success: bool, text: string|null, error: string|null, usage: array}
     */
    public function generateText(
        string $prompt,
        string $feature = 'general',
        ?int   $userId  = null,
        int    $maxTokens = 1000
    ): array {
        // 1. Master feature flag
        if (! config('ai.enabled')) {
            $this->log($userId, $feature, 'disabled', error: 'AI feature is disabled.');
            return $this->result(false, error: 'AI feature is currently disabled. Enable AI_FEATURE_ENABLED in settings.');
        }

        // 2. API key present
        if (empty(config('ai.api_key'))) {
            $this->log($userId, $feature, 'failed', error: 'API key not configured.');
            return $this->result(false, error: 'OpenAI API key is not configured.');
        }

        // 3. Daily request limit
        $todayCount = AiUsageLog::today()->count();
        if ($todayCount >= config('ai.daily_request_limit', 100)) {
            $this->log($userId, $feature, 'limit_reached', error: 'Daily request limit reached.');
            return $this->result(false, error: 'Daily AI request limit reached. Try again tomorrow.');
        }

        // 4. Call OpenAI
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('ai.api_key'),
                'Content-Type'  => 'application/json',
            ])
            ->timeout(config('ai.timeout', 30))
            ->post(self::API_BASE . '/chat/completions', [
                'model'      => config('ai.model', 'gpt-4o-mini'),
                'messages'   => [
                    ['role' => 'system', 'content' => 'You are an expert training content developer for SMS Training Academy, specialising in ISO standards, HSE, quality management, and professional certification programmes.'],
                    ['role' => 'user',   'content' => $prompt],
                ],
                'max_tokens' => $maxTokens,
                'temperature'=> 0.7,
            ]);

            if ($response->failed()) {
                $errorBody = $response->json('error.message') ?? $response->body();
                Log::error('OpenAI API error', ['status' => $response->status(), 'body' => $errorBody]);
                $this->log($userId, $feature, 'failed', error: $errorBody);
                return $this->result(false, error: 'OpenAI API error: ' . $errorBody);
            }

            $data  = $response->json();
            $text  = $data['choices'][0]['message']['content'] ?? '';
            $usage = $data['usage'] ?? [];

            $cost = $this->estimateCost(
                $usage['prompt_tokens']     ?? 0,
                $usage['completion_tokens'] ?? 0,
                config('ai.model')
            );

            $this->log($userId, $feature, 'success',
                promptTokens:     $usage['prompt_tokens']     ?? 0,
                completionTokens: $usage['completion_tokens'] ?? 0,
                totalTokens:      $usage['total_tokens']      ?? 0,
                costUsd:          $cost,
            );

            return $this->result(true, text: $text, usage: [
                'prompt_tokens'     => $usage['prompt_tokens']     ?? 0,
                'completion_tokens' => $usage['completion_tokens'] ?? 0,
                'total_tokens'      => $usage['total_tokens']      ?? 0,
                'estimated_cost'    => $cost,
                'model'             => config('ai.model'),
            ]);

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('OpenAI connection timeout', ['message' => $e->getMessage()]);
            $this->log($userId, $feature, 'failed', error: 'Connection timeout: ' . $e->getMessage());
            return $this->result(false, error: 'Request timed out. The OpenAI API did not respond in time.');
        } catch (\Throwable $e) {
            Log::error('OpenAI unexpected error', ['message' => $e->getMessage()]);
            $this->log($userId, $feature, 'failed', error: $e->getMessage());
            return $this->result(false, error: 'An unexpected error occurred. Please try again.');
        }
    }

    /**
     * Execute a saved prompt template with a variable test input.
     *
     * Builds the full system + user prompt from the template, respects the
     * template's model/temperature/max_tokens overrides, and returns the same
     * result shape as generateText() plus 'response_time_ms'.
     */
    public function generateFromTemplate(
        AiPromptTemplate $template,
        string           $testInput,
        ?int             $userId = null
    ): array {
        if (! config('ai.enabled')) {
            $this->log($userId, $template->template_code, 'disabled', error: 'AI feature is disabled.');
            return $this->result(false, error: 'AI feature is currently disabled. Enable AI_FEATURE_ENABLED in settings.');
        }

        if (empty(config('ai.api_key'))) {
            $this->log($userId, $template->template_code, 'failed', error: 'API key not configured.');
            return $this->result(false, error: 'OpenAI API key is not configured.');
        }

        $todayCount = AiUsageLog::today()->count();
        if ($todayCount >= config('ai.daily_request_limit', 100)) {
            $this->log($userId, $template->template_code, 'limit_reached', error: 'Daily request limit reached.');
            return $this->result(false, error: 'Daily AI request limit reached. Try again tomorrow.');
        }

        try {
            $model       = $template->effectiveModel();
            $maxTokens   = $template->effectiveMaxTokens();
            $temperature = $template->effectiveTemperature();

            $startMs = (int) round(microtime(true) * 1000);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('ai.api_key'),
                'Content-Type'  => 'application/json',
            ])
            ->timeout(config('ai.timeout', 30))
            ->post(self::API_BASE . '/chat/completions', [
                'model'       => $model,
                'messages'    => [
                    ['role' => 'system', 'content' => $template->fullSystemPrompt()],
                    ['role' => 'user',   'content' => $template->buildUserPrompt($testInput)],
                ],
                'max_tokens'  => $maxTokens,
                'temperature' => $temperature,
            ]);

            $responseTimeMs = (int) round(microtime(true) * 1000) - $startMs;

            if ($response->failed()) {
                $errorBody = $response->json('error.message') ?? $response->body();
                Log::error('OpenAI template error', ['status' => $response->status(), 'body' => $errorBody]);
                $this->log($userId, $template->template_code, 'failed', error: $errorBody);
                return $this->result(false, error: 'OpenAI API error: ' . $errorBody);
            }

            $data  = $response->json();
            $text  = $data['choices'][0]['message']['content'] ?? '';
            $usage = $data['usage'] ?? [];

            $cost = $this->estimateCost(
                $usage['prompt_tokens']     ?? 0,
                $usage['completion_tokens'] ?? 0,
                $model
            );

            $this->log($userId, $template->template_code, 'success',
                promptTokens:     $usage['prompt_tokens']     ?? 0,
                completionTokens: $usage['completion_tokens'] ?? 0,
                totalTokens:      $usage['total_tokens']      ?? 0,
                costUsd:          $cost,
            );

            return $this->result(true, text: $text, usage: [
                'prompt_tokens'     => $usage['prompt_tokens']     ?? 0,
                'completion_tokens' => $usage['completion_tokens'] ?? 0,
                'total_tokens'      => $usage['total_tokens']      ?? 0,
                'estimated_cost'    => $cost,
                'model'             => $model,
                'response_time_ms'  => $responseTimeMs,
            ]);

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('OpenAI template timeout', ['message' => $e->getMessage()]);
            $this->log($userId, $template->template_code, 'failed', error: 'Connection timeout: ' . $e->getMessage());
            return $this->result(false, error: 'Request timed out. The OpenAI API did not respond in time.');
        } catch (\Throwable $e) {
            Log::error('OpenAI template unexpected error', ['message' => $e->getMessage()]);
            $this->log($userId, $template->template_code, 'failed', error: $e->getMessage());
            return $this->result(false, error: $e->getMessage());
        }
    }

    // ──────────────────────────────────────────────────────────
    // Future methods — placeholders (Phase 2+)
    // ──────────────────────────────────────────────────────────

    /**
     * Generate a full course outline from a topic and level.
     * TODO: Implement in AI Course Builder phase.
     */
    public function generateCourse(string $topic, string $level = 'intermediate', ?int $userId = null): array
    {
        throw new \BadMethodCallException('generateCourse() is not yet implemented. Coming in AI Course Builder phase.');
    }

    /**
     * Generate structured lesson content for a given topic.
     * TODO: Implement in AI Lesson Builder phase.
     */
    public function generateLesson(string $topic, string $courseContext = '', ?int $userId = null): array
    {
        throw new \BadMethodCallException('generateLesson() is not yet implemented. Coming in AI Lesson Builder phase.');
    }

    /**
     * Generate multiple-choice quiz questions for a lesson or topic.
     * TODO: Implement in AI Quiz Generator phase.
     */
    public function generateQuiz(string $topic, int $questionCount = 5, ?int $userId = null): array
    {
        throw new \BadMethodCallException('generateQuiz() is not yet implemented. Coming in AI Quiz Generator phase.');
    }

    /**
     * Generate a training case study for a given scenario.
     * TODO: Implement in AI Case Study Generator phase.
     */
    public function generateCaseStudy(string $scenario, string $industry = '', ?int $userId = null): array
    {
        throw new \BadMethodCallException('generateCaseStudy() is not yet implemented. Coming in AI Case Study Generator phase.');
    }

    // ──────────────────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────────────────

    private function result(
        bool    $success,
        ?string $text  = null,
        ?string $error = null,
        array   $usage = []
    ): array {
        return compact('success', 'text', 'error', 'usage');
    }

    private function estimateCost(int $promptTokens, int $completionTokens, string $model): float
    {
        $rates = config('ai.cost_per_million.' . $model)
              ?? config('ai.cost_per_million.gpt-4o-mini');

        $inputCost  = ($promptTokens     / 1_000_000) * $rates['input'];
        $outputCost = ($completionTokens / 1_000_000) * $rates['output'];

        return round($inputCost + $outputCost, 6);
    }

    private function log(
        ?int   $userId,
        string $feature,
        string $status,
        int    $promptTokens     = 0,
        int    $completionTokens = 0,
        int    $totalTokens      = 0,
        float  $costUsd          = 0.0,
        ?string $error           = null,
    ): void {
        try {
            AiUsageLog::create([
                'user_id'           => $userId,
                'feature_name'      => $feature,
                'model'             => config('ai.model', 'gpt-4o-mini'),
                'prompt_tokens'     => $promptTokens,
                'completion_tokens' => $completionTokens,
                'total_tokens'      => $totalTokens,
                'estimated_cost_usd'=> $costUsd,
                'request_status'    => $status,
                'error_message'     => $error,
            ]);
        } catch (\Throwable $e) {
            // Never let logging failure break the main request
            Log::error('Failed to write AI usage log', ['error' => $e->getMessage()]);
        }
    }
}
