<?php

namespace App\Http\Controllers;

use App\Models\ElearningEnrollment;
use App\Models\ElearningLesson;
use App\Models\LessonAudio;
use App\Models\LessonAudioProgress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LessonAudioProgressController extends Controller
{
    private const COMPLETION_THRESHOLD = 0.90; // 90% listened = complete

    /**
     * Save (upsert) audio listening progress for a single audio file.
     * Called by the frontend every ~15 s and on pause / page unload.
     */
    public function upsert(Request $request, ElearningEnrollment $enrollment, ElearningLesson $lesson): JsonResponse
    {
        abort_unless($enrollment->user_id === Auth::id(), 403);
        abort_unless($lesson->course_id === $enrollment->course_id, 403);

        $data = $request->validate([
            'audio_id'         => 'required|integer|exists:lesson_audio,id',
            'high_water_mark'  => 'required|numeric|min:0',
            'seconds_listened' => 'required|numeric|min:0',
            'naturally_ended'  => 'boolean',
        ]);

        $audio = LessonAudio::findOrFail($data['audio_id']);

        // Verify audio belongs to this lesson
        abort_unless($audio->lesson_id === $lesson->id, 403);

        $duration  = $audio->duration_seconds;
        $listened  = (float) $data['seconds_listened'];
        $hwm       = (float) $data['high_water_mark'];
        $naturalEnd = (bool) ($data['naturally_ended'] ?? false);

        // Sanity cap: cannot have listened more than the audio duration
        if ($duration) {
            $listened = min($listened, $duration);
            $hwm      = min($hwm, $duration);
        }

        $pct = $duration > 0 ? round(($listened / $duration) * 100, 2) : 0;

        $existing = LessonAudioProgress::where('enrollment_id', $enrollment->id)
            ->where('audio_id', $audio->id)
            ->first();

        // Never allow backend to reduce progress (guard against replay attacks)
        $hwm      = max($hwm,     (float) ($existing->high_water_mark  ?? 0));
        $listened = max($listened,(float) ($existing->seconds_listened  ?? 0));
        $pct      = max($pct,     (float) ($existing->completion_percentage ?? 0));

        $isCompleted = $existing?->is_completed
            || $naturalEnd
            || ($duration && ($listened / $duration) >= self::COMPLETION_THRESHOLD);

        $progress = LessonAudioProgress::updateOrCreate(
            ['enrollment_id' => $enrollment->id, 'audio_id' => $audio->id],
            [
                'lesson_id'             => $lesson->id,
                'user_id'               => Auth::id(),
                'high_water_mark'       => $hwm,
                'seconds_listened'      => $listened,
                'duration_seconds'      => $duration,
                'completion_percentage' => $pct,
                'is_completed'          => $isCompleted,
                'completed_at'          => $isCompleted && !$existing?->completed_at ? now() : ($existing?->completed_at ?? null),
                'last_listened_at'      => now(),
            ]
        );

        return response()->json([
            'is_completed'          => $progress->is_completed,
            'completion_percentage' => $progress->completion_percentage,
            'seconds_listened'      => $progress->seconds_listened,
            'high_water_mark'       => $progress->high_water_mark,
        ]);
    }

    /**
     * Return audio completion status for all ready audio on this lesson.
     * Called once when the lesson page loads.
     */
    public function status(Request $request, ElearningEnrollment $enrollment, ElearningLesson $lesson): JsonResponse
    {
        abort_unless($enrollment->user_id === Auth::id(), 403);
        abort_unless($lesson->course_id === $enrollment->course_id, 403);

        $readyAudios = $lesson->readyAudios()->get();

        $progressMap = LessonAudioProgress::where('enrollment_id', $enrollment->id)
            ->whereIn('audio_id', $readyAudios->pluck('id'))
            ->get()
            ->keyBy('audio_id');

        $audios = $readyAudios->map(function ($audio) use ($progressMap) {
            $p = $progressMap->get($audio->id);
            return [
                'audio_id'              => $audio->id,
                'audio_type'            => $audio->audio_type,
                'block_id'              => $audio->block_id,
                'duration_seconds'      => $audio->duration_seconds,
                'is_completed'          => (bool) ($p?->is_completed ?? false),
                'completion_percentage' => (float) ($p?->completion_percentage ?? 0),
                'high_water_mark'       => (float) ($p?->high_water_mark ?? 0),
                'seconds_listened'      => (float) ($p?->seconds_listened ?? 0),
            ];
        });

        $allCompleted = $readyAudios->isNotEmpty()
            && $audios->every(fn ($a) => $a['is_completed']);

        return response()->json([
            'requires_audio_completion' => $lesson->require_audio_completion,
            'has_audio'                 => $readyAudios->isNotEmpty(),
            'all_completed'             => $allCompleted,
            'audios'                    => $audios->values(),
        ]);
    }
}
