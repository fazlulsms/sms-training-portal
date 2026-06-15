<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\ElearningEnrollment;
use App\Models\ElearningLesson;
use App\Models\LessonAudio;
use App\Models\LessonBlock;
use App\Services\LessonAudioService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LessonAudioController extends Controller
{
    // ──────────────────────────────────────────────────────────
    // Participant — generate block AI Coach audio (synchronous)
    // ──────────────────────────────────────────────────────────

    public function participantGenerateBlock(
        Request $request,
        ElearningEnrollment $enrollment,
        ElearningLesson $lesson,
        LessonBlock $block,
        LessonAudioService $service
    ): JsonResponse {
        abort_unless($enrollment->user_id === Auth::id(), 403);
        abort_unless($lesson->course_id === $enrollment->course_id, 403);
        abort_unless($block->lesson_id === $lesson->id, 403);

        if (!$block->isAudioEligible()) {
            return response()->json(['error' => 'This block type does not support AI Coach.'], 422);
        }

        $audio = LessonAudio::where('lesson_id', $lesson->id)
            ->where('block_id', $block->id)
            ->where('audio_type', 'ai_coach')
            ->where('language', 'en')
            ->first();

        if ($audio && $audio->isReady()) {
            return response()->json(['status' => 'ready', 'url' => $audio->publicUrl(), 'id' => $audio->id]);
        }

        if ($audio && $audio->status === 'processing') {
            return response()->json(['status' => 'processing']);
        }

        $audio = LessonAudio::updateOrCreate(
            ['lesson_id' => $lesson->id, 'block_id' => $block->id, 'audio_type' => 'ai_coach', 'language' => 'en'],
            ['status' => 'processing', 'voice' => 'nova', 'error_message' => null, 'file_path' => null, 'generated_at' => null]
        );

        $service->generateBlockCoach($audio);
        $audio->refresh();

        if ($audio->isReady()) {
            return response()->json(['status' => 'ready', 'url' => $audio->publicUrl(), 'id' => $audio->id]);
        }

        return response()->json(['status' => 'failed', 'error' => $audio->error_message ?? 'Generation failed.'], 500);
    }

    // ──────────────────────────────────────────────────────────
    // Participant — generate AI Lesson Recap (synchronous)
    // ──────────────────────────────────────────────────────────

    public function participantGenerateRecap(
        Request $request,
        ElearningEnrollment $enrollment,
        ElearningLesson $lesson,
        LessonAudioService $service
    ): JsonResponse {
        abort_unless($enrollment->user_id === Auth::id(), 403);
        abort_unless($lesson->course_id === $enrollment->course_id, 403);

        $audio = LessonAudio::where('lesson_id', $lesson->id)
            ->whereNull('block_id')
            ->where('audio_type', 'lesson_recap')
            ->where('language', 'en')
            ->first();

        if ($audio && $audio->isReady()) {
            return response()->json(['status' => 'ready', 'url' => $audio->publicUrl(), 'id' => $audio->id]);
        }

        if ($audio && $audio->status === 'processing') {
            return response()->json(['status' => 'processing']);
        }

        // Use update-or-insert with whereNull (MySQL can't match NULL via = operator)
        if ($audio) {
            $audio->update(['status' => 'processing', 'voice' => 'nova', 'error_message' => null, 'file_path' => null, 'generated_at' => null]);
        } else {
            $audio = LessonAudio::create([
                'lesson_id'  => $lesson->id,
                'block_id'   => null,
                'audio_type' => 'lesson_recap',
                'language'   => 'en',
                'status'     => 'processing',
                'voice'      => 'nova',
            ]);
        }

        $service->generateLessonRecap($audio);
        $audio->refresh();

        if ($audio->isReady()) {
            return response()->json(['status' => 'ready', 'url' => $audio->publicUrl(), 'id' => $audio->id]);
        }

        return response()->json(['status' => 'failed', 'error' => $audio->error_message ?? 'Generation failed.'], 500);
    }

    // ──────────────────────────────────────────────────────────
    // Admin — generate lesson recap (queued)
    // ──────────────────────────────────────────────────────────

    public function generateRecap(Request $request, Course $course, ElearningLesson $lesson, LessonAudioService $service): JsonResponse
    {
        $existing = LessonAudio::where('lesson_id', $lesson->id)
            ->whereNull('block_id')
            ->where('audio_type', 'lesson_recap')
            ->first();

        if ($existing && $existing->status === 'ready') {
            return response()->json(['error' => 'Recap already generated. Use regenerate to replace it.'], 409);
        }

        if ($existing && $existing->status === 'processing') {
            return response()->json(['error' => 'Recap generation is already in progress.'], 409);
        }

        // Use update-or-insert with whereNull (MySQL can't match NULL via = operator)
        if ($existing) {
            $existing->update(['status' => 'processing', 'voice' => 'nova', 'error_message' => null, 'file_path' => null, 'generated_at' => null]);
            $audio = $existing;
        } else {
            $audio = LessonAudio::create([
                'lesson_id'  => $lesson->id,
                'block_id'   => null,
                'audio_type' => 'lesson_recap',
                'language'   => 'en',
                'status'     => 'processing',
                'voice'      => 'nova',
            ]);
        }

        $service->generateLessonRecap($audio);
        $audio->refresh();

        return response()->json([
            'message' => $audio->isReady() ? 'Recap generated.' : 'Generation failed.',
            'status'  => $audio->status,
            'url'     => $audio->publicUrl(),
            'error'   => $audio->error_message,
        ]);
    }

    // ──────────────────────────────────────────────────────────
    // Admin — delete lesson recap
    // ──────────────────────────────────────────────────────────

    public function destroyRecap(Course $course, ElearningLesson $lesson, LessonAudio $audio): JsonResponse
    {
        if ($audio->file_path) {
            Storage::disk('public')->delete($audio->file_path);
        }
        $audio->delete();

        return response()->json(['message' => 'Recap audio deleted.']);
    }

    // ──────────────────────────────────────────────────────────
    // Admin — status poll (lesson_recap only now)
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
                'id'            => $r->id,
                'status'        => $r->status,
                'voice'         => $r->voice,
                'url'           => $r->publicUrl(),
                'generated_at'  => $r->generated_at?->diffForHumans(),
                'error_message' => $r->error_message,
            ];
        }

        return response()->json($data);
    }
}
