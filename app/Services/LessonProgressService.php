<?php

namespace App\Services;

use App\Models\ElearningEnrollment;
use App\Models\ElearningLesson;
use App\Models\FeedbackAssignment;
use App\Models\FeedbackResponse;
use App\Models\LessonAudioProgress;
use App\Models\LessonProgress;
use App\Models\QuizAttempt;
use App\Services\TrainingNotificationService;

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
     * Handles completion, feedback-response creation, and certificate issuance.
     * Safe to call multiple times — re-evaluates certificate when feedback is submitted.
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

        $updates      = ['progress_percentage' => $percentage];
        $justCompleted = false;

        $paymentCleared = in_array($enrollment->payment_status, [
            'paid', 'manual_approved', 'waived', 'free',
        ]);

        if ($percentage === 100 && $paymentCleared) {

            // Step 1 — mark completion on first reach
            if ($enrollment->completion_status !== 'completed') {
                $updates['completion_status'] = 'completed';
                if (empty($enrollment->completion_date)) {
                    $updates['completion_date'] = now();
                }
                // Create a FeedbackResponse record for every active assignment on this course
                // so the learner receives (or can see) their personalised feedback link.
                $this->ensureFeedbackResponses($enrollment);
            }

            // Step 2 — certificate decision (re-evaluated on every call, so feedback
            // submission can upgrade pending_feedback → eligible / issued without
            // needing a separate code path).
            if ($enrollment->certificate_status !== 'issued') {
                $course = $enrollment->course;

                if ($this->feedbackBlocksCertificate($enrollment)) {
                    // Mandatory feedback not yet submitted — hold the certificate
                    $updates['certificate_status'] = 'pending_feedback';
                } elseif ($course->require_admin_approval) {
                    // Feedback done (or not required); waiting for admin sign-off
                    $updates['certificate_status'] = 'eligible';
                } else {
                    // Auto-issue
                    $updates['certificate_status']     = 'issued';
                    $updates['certificate_number']     = $enrollment->certificate_number
                        ?? ('EL-' . date('Y') . '-' . str_pad($enrollment->id, 5, '0', STR_PAD_LEFT));
                    $updates['certificate_issue_date'] = now()->toDateString();
                    $justCompleted = true;
                }
            }
        }

        $enrollment->update($updates);

        if ($justCompleted) {
            try {
                TrainingNotificationService::certificateIssued($enrollment->fresh(), 'ElearningEnrollment');
            } catch (\Throwable $e) {
                \Log::error('Auto-issue notification failed', ['enrollment_id' => $enrollment->id, 'error' => $e->getMessage()]);
            }
        }
    }

    /**
     * Create FeedbackResponse records for every active assignment on this course,
     * so each learner has a unique token URL waiting for them.
     * Uses firstOrCreate — safe to call repeatedly.
     */
    private function ensureFeedbackResponses(ElearningEnrollment $enrollment): void
    {
        $assignments = FeedbackAssignment::where('assignable_type', 'elearning_course')
            ->where('assignable_id', $enrollment->course_id)
            ->where('is_active', true)
            ->get();

        foreach ($assignments as $assignment) {
            FeedbackResponse::firstOrCreate(
                [
                    'assignment_id'           => $assignment->id,
                    'elearning_enrollment_id' => $enrollment->id,
                ],
                [
                    'user_id'          => $enrollment->user_id,
                    'respondent_name'  => $enrollment->participant_name,
                    'respondent_email' => $enrollment->email,
                ]
            );
        }
    }

    /**
     * Returns true when at least one active assignment on this course requires
     * feedback for the certificate AND the learner has not yet submitted it.
     */
    private function feedbackBlocksCertificate(ElearningEnrollment $enrollment): bool
    {
        return FeedbackAssignment::where('assignable_type', 'elearning_course')
            ->where('assignable_id', $enrollment->course_id)
            ->where('require_for_certificate', true)
            ->where('is_active', true)
            ->whereDoesntHave('responses', function ($q) use ($enrollment) {
                $q->where('elearning_enrollment_id', $enrollment->id)
                  ->where('is_complete', true);
            })
            ->exists();
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
