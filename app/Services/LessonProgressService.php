<?php

namespace App\Services;

use App\Models\ElearningEnrollment;
use App\Models\ElearningLesson;
use App\Models\LessonAudioProgress;
use App\Models\LessonProgress;
use App\Models\QuizAttempt;

class LessonProgressService
{
    /**
     * Mark a lesson as in_progress when the participant opens it.
     * Only creates a new record — never downgrades a completed lesson.
     */
    public function markInProgress(int $userId, ElearningEnrollment $enrollment, ElearningLesson $lesson): LessonProgress
    {
        $existing = LessonProgress::where('enrollment_id', $enrollment->id)
            ->where('lesson_id', $lesson->id)
            ->first();

        if ($existing) {
            return $existing; // never downgrade
        }

        return LessonProgress::create([
            'user_id'      => $userId,
            'enrollment_id' => $enrollment->id,
            'course_id'    => $enrollment->course_id,
            'lesson_id'    => $lesson->id,
            'status'       => 'in_progress',
            'started_at'   => now(),
        ]);
    }

    /**
     * Mark a lesson as completed, then recalculate course progress.
     */
    public function markCompleted(int $userId, ElearningEnrollment $enrollment, ElearningLesson $lesson): LessonProgress
    {
        $record = LessonProgress::firstOrCreate(
            ['enrollment_id' => $enrollment->id, 'lesson_id' => $lesson->id],
            [
                'user_id'    => $userId,
                'course_id'  => $enrollment->course_id,
                'status'     => 'in_progress',
                'started_at' => now(),
            ]
        );

        if ($record->status !== 'completed') {
            $record->update([
                'status'       => 'completed',
                'completed_at' => now(),
            ]);
        }

        $this->recalculateProgress($enrollment);

        return $record->fresh();
    }

    /**
     * Recalculate and save progress_percentage on the enrollment.
     * If all lessons are done and payment is cleared, set completion + certificate eligibility.
     */
    public function recalculateProgress(ElearningEnrollment $enrollment): void
    {
        $enrollment->loadMissing('course');

        $totalLessons = ElearningLesson::where('course_id', $enrollment->course_id)->count();

        if ($totalLessons === 0) {
            $enrollment->update(['progress_percentage' => 0]);
            return;
        }

        $completedCount = LessonProgress::where('enrollment_id', $enrollment->id)
            ->where('status', 'completed')
            ->count();

        $percentage = (int) round(($completedCount / $totalLessons) * 100);

        $updates = ['progress_percentage' => $percentage];

        if ($percentage === 100 && $enrollment->completion_status !== 'completed') {
            $paymentCleared = in_array($enrollment->payment_status, [
                'paid', 'manual_approved', 'waived', 'free',
            ]);

            if ($paymentCleared) {
                $updates['completion_status']  = 'completed';
                $updates['certificate_status'] = 'eligible';
                if (empty($enrollment->completion_date)) {
                    $updates['completion_date'] = now();
                }
            }
        }

        $enrollment->update($updates);
    }

    /**
     * Check whether all ready audio files on a lesson have been completed
     * by this enrollment. Returns true if audio completion is not required,
     * or if there is no ready audio on the lesson.
     */
    public function audioCompletionPassed(ElearningEnrollment $enrollment, ElearningLesson $lesson): bool
    {
        if (!$lesson->require_audio_completion) {
            return true;
        }

        $readyAudioIds = $lesson->readyAudios()->pluck('id');

        if ($readyAudioIds->isEmpty()) {
            return true; // no audio to require
        }

        $completedCount = LessonAudioProgress::where('enrollment_id', $enrollment->id)
            ->whereIn('audio_id', $readyAudioIds)
            ->where('is_completed', true)
            ->count();

        return $completedCount >= $readyAudioIds->count();
    }

    /**
     * Check whether all quizzes on a lesson have been passed by this enrollment.
     */
    public function lessonQuizzesPassed(ElearningEnrollment $enrollment, ElearningLesson $lesson): bool
    {
        $quizzes = $lesson->quizzes()->where('status', 'active')->get();

        if ($quizzes->isEmpty()) {
            return true; // no quiz requirement
        }

        foreach ($quizzes as $quiz) {
            $passed = QuizAttempt::where('elearning_enrollment_id', $enrollment->id)
                ->where('quiz_id', $quiz->id)
                ->where('score', '>=', $quiz->pass_mark)
                ->exists();

            if (!$passed) {
                return false;
            }
        }

        return true;
    }
}
