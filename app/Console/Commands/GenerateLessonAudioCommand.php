<?php

namespace App\Console\Commands;

use App\Models\ElearningLesson;
use App\Models\LessonAudio;
use App\Models\LessonBlock;
use App\Services\LessonAudioService;
use Illuminate\Console\Command;

class GenerateLessonAudioCommand extends Command
{
    protected $signature = 'audio:generate
                            {lesson : Lesson ID}
                            {--type=recap : recap, blocks, or all}
                            {--voice=nova : TTS voice (nova, alloy, echo, fable, onyx, shimmer)}
                            {--block= : Specific block ID (when --type=blocks)}';

    protected $description = 'Generate AI audio for a lesson (runs synchronously, no queue worker needed)';

    public function handle(LessonAudioService $service): int
    {
        $lesson = ElearningLesson::with(['course', 'blocks' => fn ($q) => $q->where('status', 'active')->orderBy('sort_order')])->find($this->argument('lesson'));

        if (!$lesson) {
            $this->error('Lesson not found.');
            return self::FAILURE;
        }

        $this->info("Lesson: [{$lesson->id}] {$lesson->title}");
        $this->info("Course: {$lesson->course?->name}");
        $this->line('');

        $type  = $this->option('type');
        $voice = $this->option('voice');

        if ($type === 'recap' || $type === 'all') {
            $this->line('▶  Generating Lesson Audio Summary…');
            // Use whereNull — MySQL cannot match NULL via = operator in updateOrCreate
            $existing = LessonAudio::where('lesson_id', $lesson->id)
                ->whereNull('block_id')
                ->where('audio_type', 'lesson_recap')
                ->where('language', 'en')
                ->first();

            if ($existing) {
                $existing->update(['status' => 'processing', 'voice' => $voice, 'error_message' => null, 'file_path' => null, 'duration_seconds' => null, 'generated_at' => null]);
                $audio = $existing;
            } else {
                $audio = LessonAudio::create(['lesson_id' => $lesson->id, 'block_id' => null, 'audio_type' => 'lesson_recap', 'language' => 'en', 'status' => 'processing', 'voice' => $voice]);
            }

            $start = microtime(true);
            $service->generateLessonRecap($audio);
            $audio->refresh();
            $elapsed = round(microtime(true) - $start, 1);

            if ($audio->status === 'ready') {
                $this->info("   ✓ Recap done in {$elapsed}s → " . $audio->publicUrl());
            } else {
                $this->error("   ✗ Failed: " . ($audio->error_message ?? 'Unknown error'));
            }
            $this->line('');
        }

        if ($type === 'blocks' || $type === 'all') {
            $specificBlock = $this->option('block');
            $blocks = $lesson->blocks->filter(fn ($b) => $b->audio_enabled);

            if ($specificBlock) {
                $blocks = $blocks->filter(fn ($b) => $b->id == $specificBlock);
            }

            if ($blocks->isEmpty()) {
                $this->warn('No blocks have audio enabled. Edit blocks and set Audio: Enabled first.');
            } else {
                $this->line("▶  Generating AI Coach for {$blocks->count()} eligible block(s)…");
                foreach ($blocks as $block) {
                    $this->line("   Block #{$block->id} [{$block->block_type}]: {$block->title}");

                    $audio = LessonAudio::updateOrCreate(
                        ['lesson_id' => $lesson->id, 'block_id' => $block->id, 'audio_type' => 'ai_coach', 'language' => 'en'],
                        ['status' => 'processing', 'voice' => $voice, 'error_message' => null, 'file_path' => null, 'generated_at' => null]
                    );

                    $start = microtime(true);
                    $service->generateBlockCoach($audio);
                    $audio->refresh();
                    $elapsed = round(microtime(true) - $start, 1);

                    if ($audio->status === 'ready') {
                        $this->info("   ✓ Done in {$elapsed}s");
                    } else {
                        $this->error("   ✗ Failed: " . ($audio->error_message ?? 'Unknown error'));
                    }
                }
                $this->line('');
            }
        }

        $this->info('Done. Refresh the lesson page to see the audio players.');
        return self::SUCCESS;
    }
}
