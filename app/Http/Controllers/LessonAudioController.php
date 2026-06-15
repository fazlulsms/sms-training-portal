<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\ElearningLesson;
use App\Models\LessonAudio;
use App\Models\LessonBlock;
use App\Services\LessonAudioService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LessonAudioController extends Controller
{
    // ──────────────────────────────────────────────────────────
    // Admin — generate block audio
    // ──────────────────────────────────────────────────────────

    public function generateBlock(Request $request, Course $course, ElearningLesson $lesson, LessonBlock $block, LessonAudioService $service): JsonResponse
    {
        abort_unless($block->lesson_id === $lesson->id, 403);

        if (!$block->audio_enabled) {
            return response()->json(['error' => 'Audio is not enabled for this block.'], 422);
        }

        $audio = LessonAudio::where('lesson_id', $lesson->id)
            ->where('block_id', $block->id)
            ->where('audio_type', 'ai_coach')
            ->where('language', 'en')
            ->first();

        if ($audio && $audio->status === 'processing') {
            return response()->json(['error' => 'Audio generation is already in progress.'], 409);
        }

        if ($audio) {
            // Delete old file if regenerating
            if ($audio->file_path) {
                Storage::disk('public')->delete($audio->file_path);
            }
            $audio->update([
                'status'           => 'processing',
                'voice'            => $request->input('voice', 'nova'),
                'error_message'    => null,
                'file_path'        => null,
                'duration_seconds' => null,
                'generated_at'     => null,
            ]);
        } else {
            $audio = LessonAudio::create([
                'lesson_id'  => $lesson->id,
                'block_id'   => $block->id,
                'audio_type' => 'ai_coach',
                'language'   => 'en',
                'status'     => 'processing',
                'voice'      => $request->input('voice', 'nova'),
            ]);
        }

        $service->generateBlockCoach($audio);
        $audio->refresh();

        return response()->json([
            'message'  => $audio->isReady() ? 'Audio generated successfully.' : 'Generation failed.',
            'status'   => $audio->status,
            'url'      => $audio->publicUrl(),
            'duration' => $audio->duration_seconds,
            'error'    => $audio->error_message,
        ]);
    }

    // ──────────────────────────────────────────────────────────
    // Admin — delete block audio
    // ──────────────────────────────────────────────────────────

    public function destroyBlock(Course $course, ElearningLesson $lesson, LessonAudio $audio): JsonResponse
    {
        if ($audio->file_path) {
            Storage::disk('public')->delete($audio->file_path);
        }
        $audio->delete();

        return response()->json(['message' => 'Block audio deleted.']);
    }

    // ──────────────────────────────────────────────────────────
    // Admin — generate lesson audio summary
    // ──────────────────────────────────────────────────────────

    public function generateRecap(Request $request, Course $course, ElearningLesson $lesson, LessonAudioService $service): JsonResponse
    {
        $existing = LessonAudio::where('lesson_id', $lesson->id)
            ->whereNull('block_id')
            ->where('audio_type', 'lesson_recap')
            ->first();

        if ($existing && $existing->status === 'processing') {
            return response()->json(['error' => 'Audio generation is already in progress.'], 409);
        }

        if ($existing) {
            // Delete old file if regenerating
            if ($existing->file_path) {
                Storage::disk('public')->delete($existing->file_path);
            }
            $existing->update([
                'status'           => 'processing',
                'voice'            => $request->input('voice', 'nova'),
                'error_message'    => null,
                'file_path'        => null,
                'duration_seconds' => null,
                'generated_at'     => null,
            ]);
            $audio = $existing;
        } else {
            $audio = LessonAudio::create([
                'lesson_id'  => $lesson->id,
                'block_id'   => null,
                'audio_type' => 'lesson_recap',
                'language'   => 'en',
                'status'     => 'processing',
                'voice'      => $request->input('voice', 'nova'),
            ]);
        }

        $service->generateLessonRecap($audio);
        $audio->refresh();

        return response()->json([
            'message'  => $audio->isReady() ? 'Audio summary generated.' : 'Generation failed.',
            'status'   => $audio->status,
            'url'      => $audio->publicUrl(),
            'duration' => $audio->duration_seconds,
            'error'    => $audio->error_message,
        ]);
    }

    // ──────────────────────────────────────────────────────────
    // Admin — delete lesson audio summary
    // ──────────────────────────────────────────────────────────

    public function destroyRecap(Course $course, ElearningLesson $lesson, LessonAudio $audio): JsonResponse
    {
        if ($audio->file_path) {
            Storage::disk('public')->delete($audio->file_path);
        }
        $audio->delete();

        return response()->json(['message' => 'Audio summary deleted.']);
    }

    // ──────────────────────────────────────────────────────────
    // Admin — status of all audio for a lesson
    // ──────────────────────────────────────────────────────────

    public function status(Course $course, ElearningLesson $lesson): JsonResponse
    {
        $records = LessonAudio::where('lesson_id', $lesson->id)->get();

        $data = [];
        foreach ($records as $r) {
            $key = $r->audio_type === 'ai_coach'
                ? 'block_' . $r->block_id
                : $r->audio_type;

            $data[$key] = [
                'id'               => $r->id,
                'status'           => $r->status,
                'voice'            => $r->voice,
                'url'              => $r->publicUrl(),
                'duration_seconds' => $r->duration_seconds,
                'generated_at'     => $r->generated_at?->diffForHumans(),
                'error_message'    => $r->error_message,
            ];
        }

        return response()->json($data);
    }
}
