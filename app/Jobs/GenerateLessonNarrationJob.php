<?php

namespace App\Jobs;

use App\Models\LessonAudio;
use App\Services\LessonAudioService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateLessonNarrationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300;
    public int $tries   = 1;

    public function __construct(public readonly int $audioId) {}

    public function handle(LessonAudioService $service): void
    {
        $audio = LessonAudio::find($this->audioId);

        if (!$audio) {
            Log::warning('GenerateLessonNarrationJob: audio record not found', ['audio_id' => $this->audioId]);
            return;
        }

        // Guard: skip if already ready or if another worker picked it up
        if ($audio->status === 'ready') return;

        try {
            $service->generateNarration($audio);
        } catch (\Throwable $e) {
            Log::error('GenerateLessonNarrationJob failed', [
                'audio_id'  => $this->audioId,
                'lesson_id' => $audio->lesson_id,
                'error'     => $e->getMessage(),
            ]);
            $audio->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
        }
    }
}
