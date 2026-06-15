<?php

namespace App\Console\Commands;

use App\Models\ElearningLesson;
use App\Models\LessonAudio;
use App\Services\LessonAudioService;
use Illuminate\Console\Command;

class GenerateLessonAudioCommand extends Command
{
    protected $signature = 'audio:generate
                            {lesson : Lesson ID}
                            {--type=both : narration, ai_explanation, or both}
                            {--voice=nova : TTS voice (nova, alloy, echo, fable, onyx, shimmer)}';

    protected $description = 'Generate AI audio for a lesson (runs synchronously, no queue worker needed)';

    public function handle(LessonAudioService $service): int
    {
        $lesson = ElearningLesson::with(['course', 'blocks'])->find($this->argument('lesson'));

        if (!$lesson) {
            $this->error('Lesson not found.');
            return self::FAILURE;
        }

        $this->info("Lesson: [{$lesson->id}] {$lesson->title}");
        $this->info("Course: {$lesson->course?->name}");
        $this->line('');

        $types = match ($this->option('type')) {
            'narration'      => ['narration'],
            'ai_explanation' => ['ai_explanation'],
            default          => ['narration', 'ai_explanation'],
        };

        $voice  = $this->option('voice');
        $labels = ['narration' => 'Lesson Narration', 'ai_explanation' => 'AI Teacher Mode'];

        foreach ($types as $type) {
            $this->line("▶  Generating {$labels[$type]}…");

            $audio = LessonAudio::updateOrCreate(
                ['lesson_id' => $lesson->id, 'audio_type' => $type, 'language' => 'en'],
                ['status' => 'pending', 'voice' => $voice, 'error_message' => null, 'file_path' => null, 'generated_at' => null]
            );

            $start = microtime(true);

            if ($type === 'narration') {
                $service->generateNarration($audio);
            } else {
                $service->generateAiExplanation($audio);
            }

            $audio->refresh();
            $elapsed = round(microtime(true) - $start, 1);

            if ($audio->status === 'ready') {
                $this->info("   ✓ Done in {$elapsed}s → storage/app/public/{$audio->file_path}");
                $this->line("   URL: " . $audio->publicUrl());
            } else {
                $this->error("   ✗ Failed: " . ($audio->error_message ?? 'Unknown error'));
            }

            $this->line('');
        }

        $this->info('Audio generation complete. Refresh the lesson edit page to see the player.');
        return self::SUCCESS;
    }
}
