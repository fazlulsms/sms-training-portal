<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateAiExplanationJob;
use App\Jobs\GenerateLessonNarrationJob;
use App\Models\Course;
use App\Models\ElearningLesson;
use App\Models\LessonAudio;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LessonAudioController extends Controller
{
    // ──────────────────────────────────────────────────────────
    // Admin — generate (creates record + queues job)
    // ──────────────────────────────────────────────────────────

    public function generate(Request $request, Course $course, ElearningLesson $lesson): JsonResponse
    {
        $type  = $request->input('type', 'narration');
        $voice = $request->input('voice', 'nova');

        if (!in_array($type, ['narration', 'ai_explanation'])) {
            return response()->json(['error' => 'Invalid audio type.'], 422);
        }

        if (!in_array($voice, ['alloy', 'nova', 'echo', 'fable', 'onyx', 'shimmer'])) {
            $voice = 'nova';
        }

        // If a record already exists and is ready, require explicit regeneration
        $existing = LessonAudio::where('lesson_id', $lesson->id)
            ->where('audio_type', $type)
            ->first();

        if ($existing && $existing->status === 'ready') {
            return response()->json(['error' => 'Audio already generated. Use regenerate to replace it.'], 409);
        }

        if ($existing && $existing->status === 'processing') {
            return response()->json(['error' => 'Audio generation is already in progress.'], 409);
        }

        $audio = LessonAudio::updateOrCreate(
            ['lesson_id' => $lesson->id, 'audio_type' => $type, 'language' => 'en'],
            ['status' => 'pending', 'voice' => $voice, 'error_message' => null, 'file_path' => null, 'generated_at' => null]
        );

        $this->dispatch($audio);

        return response()->json(['message' => 'Audio generation queued.', 'status' => 'pending']);
    }

    // ──────────────────────────────────────────────────────────
    // Admin — regenerate
    // ──────────────────────────────────────────────────────────

    public function regenerate(Request $request, Course $course, ElearningLesson $lesson, LessonAudio $audio): JsonResponse
    {
        // Delete old file
        if ($audio->file_path) {
            Storage::disk('public')->delete($audio->file_path);
        }

        $voice = $request->input('voice', $audio->voice);
        if (!in_array($voice, ['alloy', 'nova', 'echo', 'fable', 'onyx', 'shimmer'])) {
            $voice = $audio->voice;
        }

        $audio->update([
            'status'        => 'pending',
            'voice'         => $voice,
            'file_path'     => null,
            'error_message' => null,
            'generated_at'  => null,
        ]);

        $this->dispatch($audio);

        return response()->json(['message' => 'Regeneration queued.', 'status' => 'pending']);
    }

    // ──────────────────────────────────────────────────────────
    // Admin — delete
    // ──────────────────────────────────────────────────────────

    public function destroy(Course $course, ElearningLesson $lesson, LessonAudio $audio): JsonResponse
    {
        if ($audio->file_path) {
            Storage::disk('public')->delete($audio->file_path);
        }
        $audio->delete();

        return response()->json(['message' => 'Audio deleted.']);
    }

    // ──────────────────────────────────────────────────────────
    // Admin — status poll
    // ──────────────────────────────────────────────────────────

    public function status(Course $course, ElearningLesson $lesson): JsonResponse
    {
        $records = LessonAudio::where('lesson_id', $lesson->id)->get();

        $data = [];
        foreach ($records as $r) {
            $data[$r->audio_type] = [
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

    // ──────────────────────────────────────────────────────────
    // Participant — get audio URL for enrolled learner
    // ──────────────────────────────────────────────────────────

    public function participantAudio(Request $request, $enrollment, ElearningLesson $lesson): JsonResponse
    {
        $type = $request->input('type', 'narration');

        $audio = LessonAudio::where('lesson_id', $lesson->id)
            ->where('audio_type', $type)
            ->where('status', 'ready')
            ->first();

        if (!$audio) {
            return response()->json(['error' => 'Audio not available.'], 404);
        }

        return response()->json([
            'url'           => $audio->publicUrl(),
            'generated_at'  => $audio->generated_at?->format('d M Y'),
        ]);
    }

    // ──────────────────────────────────────────────────────────
    // Dispatch appropriate job
    // ──────────────────────────────────────────────────────────

    private function dispatch(LessonAudio $audio): void
    {
        if ($audio->audio_type === 'narration') {
            GenerateLessonNarrationJob::dispatch($audio->id);
        } else {
            GenerateAiExplanationJob::dispatch($audio->id);
        }
    }
}
