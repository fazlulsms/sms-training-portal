<?php

namespace App\Jobs;

use App\Models\AiUsageLog;
use App\Models\Course;
use App\Services\CourseImageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GenerateCourseImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 180;
    public int $tries   = 2;

    public function __construct(
        private int    $courseId,
        private string $prompt,
        private int    $adminUserId,
    ) {}

    public function handle(): void
    {
        $cacheKey = "course_cover_gen_{$this->courseId}";

        try {
            Cache::put($cacheKey, ['status' => 'processing'], now()->addMinutes(10));

            [$imageUrl, $revisedPrompt] = CourseImageService::callDalle3($this->prompt);
            [$coverPath, $thumbPath]   = CourseImageService::downloadAndProcess($imageUrl, $this->courseId);

            $course = Course::findOrFail($this->courseId);

            // Delete old files before overwriting
            CourseImageService::deleteFiles($course);

            $course->update([
                'cover_image'          => $coverPath,
                'cover_thumbnail'      => $thumbPath,
                'cover_generated_by_ai'=> true,
                'cover_prompt'         => $this->prompt,
            ]);

            AiUsageLog::create([
                'user_id'           => $this->adminUserId,
                'feature_name'      => 'course_cover_generator',
                'model'             => 'dall-e-3',
                'prompt_tokens'     => 0,
                'completion_tokens' => 0,
                'total_tokens'      => 0,
                'estimated_cost_usd'=> CourseImageService::DALLE_COST_USD,
                'request_status'    => 'success',
                'error_message'     => null,
            ]);

            Cache::put($cacheKey, [
                'status'       => 'done',
                'cover_url'    => asset('storage/' . $coverPath),
                'thumb_url'    => asset('storage/' . $thumbPath),
                'revised_prompt' => $revisedPrompt,
            ], now()->addMinutes(15));

        } catch (\Throwable $e) {
            Log::error('GenerateCourseImageJob failed', [
                'course_id' => $this->courseId,
                'error'     => $e->getMessage(),
            ]);

            AiUsageLog::create([
                'user_id'           => $this->adminUserId,
                'feature_name'      => 'course_cover_generator',
                'model'             => 'dall-e-3',
                'prompt_tokens'     => 0,
                'completion_tokens' => 0,
                'total_tokens'      => 0,
                'estimated_cost_usd'=> 0,
                'request_status'    => 'error',
                'error_message'     => $e->getMessage(),
            ]);

            Cache::put($cacheKey, [
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], now()->addMinutes(5));

            $this->fail($e);
        }
    }
}
