<?php

namespace App\Services;

use App\Mail\TrainingMail;
use App\Models\Enrollment;
use App\Models\ParticipantTestAnswer;
use App\Models\ParticipantTestAttempt;
use App\Models\ParticipantTestResult;
use App\Models\QuestionSet;
use App\Models\TrainingQuestionAssignment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ExamService
{
    // ── Send exam invitation to a participant ─────────────────────────────

    public static function sendExamEmail(Enrollment $enrollment): void
    {
        $assignment = TrainingQuestionAssignment::with('questionSet')
            ->where('training_schedule_id', $enrollment->training_schedule_id)
            ->first();

        if (!$assignment) return;

        $questionSet = $assignment->questionSet;

        // Get or create the attempt record (next attempt)
        $attemptsUsed  = ParticipantTestAttempt::where('enrollment_id', $enrollment->id)->count();
        $maxAttempts   = $assignment->effectiveAttempts();

        if ($attemptsUsed >= $maxAttempts) return; // no more attempts

        // Create a fresh attempt slot with a unique token
        $attempt = ParticipantTestAttempt::create([
            'enrollment_id'  => $enrollment->id,
            'question_set_id'=> $questionSet->id,
            'exam_token'     => Str::random(48),
            'attempt_number' => $attemptsUsed + 1,
            'status'         => 'not_started',
        ]);

        // Ensure result record exists
        ParticipantTestResult::firstOrCreate(
            ['enrollment_id' => $enrollment->id],
            [
                'question_set_id' => $questionSet->id,
                'overall_status'  => 'not_started',
                'attempts_used'   => 0,
            ]
        );

        // Mark exam email sent
        $enrollment->update([
            'exam_email_sent'    => true,
            'exam_email_sent_at' => now(),
        ]);

        // Build exam URL
        $examUrl   = url('/exam/' . $attempt->exam_token);
        $enrollment->load('trainingSchedule.course');
        $courseName = $enrollment->trainingSchedule?->course?->name ?? 'Training Programme';

        $passMark = $questionSet->effectivePassMark();
        $passText = $questionSet->pass_percentage
            ? "{$questionSet->pass_percentage}% ({$passMark}/{$questionSet->total_marks})"
            : "{$passMark}/{$questionSet->total_marks} marks";

        $data = [
            'participant_name' => $enrollment->full_name,
            'course_name'      => $courseName,
            'exam_title'       => $questionSet->title,
            'exam_url'         => $examUrl,
            'pass_mark_text'   => $passText,
            'allowed_attempts' => $maxAttempts,
            'time_limit'       => $questionSet->time_limit_minutes
                ? "{$questionSet->time_limit_minutes} minutes"
                : 'No time limit',
            'cert_note'        => $questionSet->allow_certificate_after_pass
                ? 'Your certificate will be issued automatically upon passing.'
                : '',
        ];

        try {
            Mail::to($enrollment->email)->send(new TrainingMail(
                "Action Required: Complete Your Knowledge Test – {$courseName}",
                'emails.exam-invitation',
                $data,
                []
            ));
        } catch (\Throwable $e) {
            Log::error('ExamService: exam invitation email failed', [
                'enrollment_id' => $enrollment->id,
                'error'         => $e->getMessage(),
            ]);
        }
    }

    // ── Grade an attempt automatically ────────────────────────────────────

    public static function autoGrade(ParticipantTestAttempt $attempt): void
    {
        $attempt->load('answers.question.options', 'questionSet');
        $questionSet = $attempt->questionSet;
        $totalScore  = 0;
        $pendingManual = false;

        foreach ($attempt->answers as $answer) {
            $q = $answer->question;

            if (!$q->isAutoGradable()) {
                $pendingManual = true;
                continue;
            }

            $awarded   = 0;
            $isCorrect = false;

            switch ($q->question_type) {
                case 'mcq_single':
                    $selectedId = is_array($answer->answer_options) ? ($answer->answer_options[0] ?? null) : null;
                    if ($selectedId) {
                        $correctOpt = $q->options->where('is_correct', true)->first();
                        $isCorrect  = $correctOpt && (int) $correctOpt->id === (int) $selectedId;
                    }
                    $awarded = $isCorrect ? $q->marks : 0;
                    break;

                case 'mcq_multiple':
                    $selected = array_map('intval', (array) ($answer->answer_options ?? []));
                    $correct  = $q->options->where('is_correct', true)->pluck('id')->map(fn($id) => (int) $id)->toArray();
                    sort($selected); sort($correct);
                    $isCorrect = ($selected === $correct);
                    $awarded   = $isCorrect ? $q->marks : 0;
                    break;

                case 'true_false':
                    $selectedId = is_array($answer->answer_options) ? ($answer->answer_options[0] ?? null) : null;
                    if ($selectedId) {
                        $correctOpt = $q->options->where('is_correct', true)->first();
                        $isCorrect  = $correctOpt && (int) $correctOpt->id === (int) $selectedId;
                    }
                    $awarded = $isCorrect ? $q->marks : 0;
                    break;

                case 'short_answer':
                    if ($q->correct_answer && $q->exact_match_required) {
                        $isCorrect = strtolower(trim($answer->answer_text ?? '')) === strtolower(trim($q->correct_answer));
                        $awarded   = $isCorrect ? $q->marks : 0;
                    } else {
                        // Not exact → goes to manual review
                        $pendingManual = true;
                        continue 2;
                    }
                    break;

                case 'date':
                    if ($q->correct_answer) {
                        $isCorrect = trim($answer->answer_text ?? '') === trim($q->correct_answer);
                        $awarded   = $isCorrect ? $q->marks : 0;
                    }
                    break;

                case 'declaration':
                    // Declaration is always 'correct' if answered
                    $isCorrect = !empty($answer->answer_text);
                    $awarded   = $isCorrect ? $q->marks : 0;
                    break;
            }

            $answer->update([
                'is_correct'    => $isCorrect,
                'marks_awarded' => $awarded,
                'manual_graded' => false,
            ]);

            $totalScore += $awarded;
        }

        $passMark   = $questionSet->effectivePassMark();
        $percentage = $questionSet->total_marks > 0
            ? round(($totalScore / $questionSet->total_marks) * 100, 2)
            : 0;

        $status = 'submitted';
        $passFail = null;

        if ($pendingManual) {
            $status = 'pending_review';
        } else {
            $passFail = $totalScore >= $passMark;
            $status   = $passFail ? 'passed' : 'failed';
        }

        $attempt->update([
            'score'                 => $totalScore,
            'total_marks'           => $questionSet->total_marks,
            'percentage'            => $percentage,
            'pass_fail'             => $pendingManual ? null : $passFail,
            'manual_review_pending' => $pendingManual,
            'status'                => $status,
            'submitted_at'          => now(),
        ]);

        self::updateResultSummary($attempt->enrollment_id, $attempt->question_set_id);
        self::sendResultEmail($attempt);
    }

    // ── Update the result summary record ──────────────────────────────────

    public static function updateResultSummary(int $enrollmentId, int $questionSetId): void
    {
        $attempts = ParticipantTestAttempt::where('enrollment_id', $enrollmentId)
            ->where('question_set_id', $questionSetId)
            ->get();

        $attemptsUsed = $attempts->count();
        $qs           = QuestionSet::find($questionSetId);
        $maxAttempts  = $qs?->allowed_attempts ?? 1;

        // Determine overall status
        $hasPassed    = $attempts->where('status', 'passed')->count() > 0;
        $hasPending   = $attempts->where('status', 'pending_review')->count() > 0;
        $allDone      = $attempts->whereNotIn('status', ['not_started','in_progress'])->count() === $attemptsUsed;

        $bestScore      = $attempts->max('score');
        $bestPct        = $attempts->max('percentage');
        $certEligible   = false;

        $overallStatus = 'in_progress';
        $passedAt      = null;

        if ($hasPassed) {
            $overallStatus = 'passed';
            $certEligible  = (bool) $qs?->allow_certificate_after_pass;
            $passedAt      = $attempts->where('status', 'passed')->min('submitted_at');
        } elseif ($hasPending) {
            $overallStatus = 'pending_review';
        } elseif ($allDone && $attemptsUsed >= $maxAttempts) {
            $overallStatus = 'attempt_limit_reached';
        } elseif ($allDone && !$hasPassed) {
            $overallStatus = 'failed';
        }

        ParticipantTestResult::updateOrCreate(
            ['enrollment_id' => $enrollmentId],
            [
                'question_set_id'     => $questionSetId,
                'overall_status'      => $overallStatus,
                'attempts_used'       => $attemptsUsed,
                'best_score'          => $bestScore,
                'best_percentage'     => $bestPct,
                'certificate_eligible'=> $certEligible,
                'passed_at'           => $passedAt,
            ]
        );
    }

    // ── Send result email ─────────────────────────────────────────────────

    public static function sendResultEmail(ParticipantTestAttempt $attempt): void
    {
        $attempt->load('enrollment.trainingSchedule.course', 'questionSet');
        $enrollment = $attempt->enrollment;
        if (!$enrollment->email) return;

        $courseName = $enrollment->trainingSchedule?->course?->name ?? 'Training Programme';
        $qs         = $attempt->questionSet;

        // Get assignment for max attempts
        $assignment  = TrainingQuestionAssignment::where('training_schedule_id', $enrollment->training_schedule_id)->first();
        $maxAttempts = $assignment ? $assignment->effectiveAttempts() : ($qs->allowed_attempts ?? 1);
        $remaining   = $maxAttempts - $attempt->attempt_number;

        if ($attempt->status === 'passed') {
            self::sendPassedEmail($enrollment, $attempt, $courseName);
        } elseif ($attempt->status === 'failed') {
            if ($remaining > 0) {
                self::sendFailedWithRetryEmail($enrollment, $attempt, $courseName, $remaining);
            } else {
                self::sendFailedFinalEmail($enrollment, $attempt, $courseName);
            }
        }
    }

    private static function sendPassedEmail(Enrollment $enrollment, ParticipantTestAttempt $attempt, string $courseName): void
    {
        $data = [
            'participant_name' => $enrollment->full_name,
            'course_name'      => $courseName,
            'exam_title'       => $attempt->questionSet->title,
            'score'            => $attempt->score,
            'total_marks'      => $attempt->total_marks,
            'percentage'       => number_format($attempt->percentage, 1),
        ];
        try {
            Mail::to($enrollment->email)->send(new TrainingMail(
                "Congratulations! You Passed the Knowledge Test – {$courseName}",
                'emails.exam-passed',
                $data,
                []
            ));
        } catch (\Throwable $e) {
            Log::error('ExamService: passed email failed', ['enrollment_id' => $enrollment->id, 'error' => $e->getMessage()]);
        }
    }

    private static function sendFailedWithRetryEmail(Enrollment $enrollment, ParticipantTestAttempt $attempt, string $courseName, int $remaining): void
    {
        // Create next attempt token
        $nextAttempt = ParticipantTestAttempt::create([
            'enrollment_id'   => $enrollment->id,
            'question_set_id' => $attempt->question_set_id,
            'exam_token'      => Str::random(48),
            'attempt_number'  => $attempt->attempt_number + 1,
            'status'          => 'not_started',
        ]);

        $data = [
            'participant_name' => $enrollment->full_name,
            'course_name'      => $courseName,
            'exam_title'       => $attempt->questionSet->title,
            'score'            => $attempt->score,
            'total_marks'      => $attempt->total_marks,
            'percentage'       => number_format($attempt->percentage, 1),
            'remaining_attempts'=> $remaining,
            'retry_url'        => url('/exam/' . $nextAttempt->exam_token),
        ];
        try {
            Mail::to($enrollment->email)->send(new TrainingMail(
                "Knowledge Test Result – Please Try Again – {$courseName}",
                'emails.exam-failed-retry',
                $data,
                []
            ));
        } catch (\Throwable $e) {
            Log::error('ExamService: failed-retry email failed', ['enrollment_id' => $enrollment->id, 'error' => $e->getMessage()]);
        }
    }

    private static function sendFailedFinalEmail(Enrollment $enrollment, ParticipantTestAttempt $attempt, string $courseName): void
    {
        $data = [
            'participant_name' => $enrollment->full_name,
            'course_name'      => $courseName,
            'exam_title'       => $attempt->questionSet->title,
            'score'            => $attempt->score,
            'total_marks'      => $attempt->total_marks,
            'percentage'       => number_format($attempt->percentage, 1),
        ];
        try {
            Mail::to($enrollment->email)->send(new TrainingMail(
                "Knowledge Test Result – {$courseName}",
                'emails.exam-failed-final',
                $data,
                []
            ));
        } catch (\Throwable $e) {
            Log::error('ExamService: failed-final email failed', ['enrollment_id' => $enrollment->id, 'error' => $e->getMessage()]);
        }
    }
}
