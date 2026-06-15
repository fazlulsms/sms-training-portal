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

class GenerateAiExplanationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300;
    public int $tries   = 1;

    public function __construct(public readonly int $audioId) {}

    public function handle(LessonAudioService $service): void
    {
        $audio = LessonAudio::find($this->audioId);

        if (!$audio) {
            Log::warning('GenerateAiExplanationJob: audio record not found', ['audio_id' => $this->audioId]);
            return;
        }

        if ($audio->status === 'ready') return;

        try {
            $service->generateAiExplanation($audio);
        } catch (\Throwable $e) {
            Log::error('GenerateAiExplanationJob failed', [
                'audio_id'  => $this->audioId,
                'lesson_id' => $audio->lesson_id,
                'error'     => $e->getMessage(),
            ]);
            $audio->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
        }
    }
}
