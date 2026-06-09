<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\ParticipantTestAnswer;
use App\Models\ParticipantTestAttempt;
use App\Models\ParticipantTestResult;
use App\Models\TrainingQuestionAssignment;
use App\Models\TrainingSchedule;
use App\Services\ExamService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrainingExamController extends Controller
{
    // ── Assign exam to a training schedule ────────────────────────────────

    public function assignExam(Request $request, $scheduleId)
    {
        $schedule = TrainingSchedule::findOrFail($scheduleId);

        if ($request->input('require_exam') === '0' || $request->input('require_exam') === 'no') {
            // Remove assignment
            TrainingQuestionAssignment::where('training_schedule_id', $scheduleId)->delete();
            return back()->with('success', 'Exam requirement removed from this schedule.');
        }

        $validated = $request->validate([
            'question_set_id'              => 'required|exists:question_sets,id',
            'allowed_attempts'             => 'nullable|integer|min:1|max:10',
            'exam_active_after_attendance' => 'nullable|boolean',
        ]);

        $assignment = TrainingQuestionAssignment::updateOrCreate(
            ['training_schedule_id' => $scheduleId],
            [
                'question_set_id'              => $validated['question_set_id'],
                'allowed_attempts'             => $validated['allowed_attempts'] ?? null,
                'exam_active_after_attendance' => $request->boolean('exam_active_after_attendance', true),
            ]
        );

        // ── Retroactively send exam emails to already-attended participants ──
        if ($assignment->exam_active_after_attendance) {
            $attended = ['Present', 'Partial', 'Late', 'Attended'];
            $pending  = Enrollment::where('training_schedule_id', $scheduleId)
                ->whereIn('attendance_status', $attended)
                ->where(function ($q) {
                    $q->where('exam_email_sent', false)->orWhereNull('exam_email_sent');
                })
                ->get();

            $sent = 0;
            foreach ($pending as $enrollment) {
                try {
                    ExamService::sendExamEmail($enrollment->fresh());
                    $sent++;
                } catch (\Throwable $e) {
                    // log but don't break
                    \Log::error('ExamService::sendExamEmail failed for enrollment '.$enrollment->id.': '.$e->getMessage());
                }
            }

            $msg = 'Exam assigned to training schedule.';
            if ($sent > 0) {
                $msg .= " Exam invitation sent to {$sent} already-attended participant(s).";
            }
            return back()->with('success', $msg);
        }

        return back()->with('success', 'Exam assigned to training schedule.');
    }

    // ── All exam results across all schedules (index) ────────────────────

    public function index(Request $request)
    {
        $search  = $request->input('search');
        $status  = $request->input('status');
        $course  = $request->input('course_id');

        $query = ParticipantTestAttempt::with([
            'enrollment.trainingSchedule.course',
            'questionSet',
        ])
        ->whereIn('status', ['submitted','passed','failed','pending_review','attempt_limit_reached','in_progress'])
        ->orderByDesc('submitted_at');

        if ($search) {
            $query->whereHas('enrollment', function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%");
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($course) {
            $query->whereHas('enrollment.trainingSchedule', function ($q) use ($course) {
                $q->where('course_id', $course);
            });
        }

        $attempts = $query->paginate(25)->withQueryString();

        // Stats
        $stats = [
            'total'          => ParticipantTestAttempt::whereNotIn('status',['not_started'])->count(),
            'pending_review' => ParticipantTestAttempt::where('status','pending_review')->count(),
            'passed'         => ParticipantTestAttempt::where('status','passed')->count(),
            'failed'         => ParticipantTestAttempt::whereIn('status',['failed','attempt_limit_reached'])->count(),
            'in_progress'    => ParticipantTestAttempt::where('status','in_progress')->count(),
        ];

        $courses = \App\Models\Course::orderBy('name')->get();

        return view('training-exams.index', compact('attempts', 'stats', 'courses', 'search', 'status', 'course'));
    }

    // ── Results for a schedule ────────────────────────────────────────────

    public function scheduleResults($scheduleId)
    {
        $schedule   = TrainingSchedule::with(['course', 'trainer'])->findOrFail($scheduleId);
        $assignment = TrainingQuestionAssignment::with('questionSet')
            ->where('training_schedule_id', $scheduleId)
            ->first();

        $enrollments = Enrollment::with(['testResult', 'testAttempts'])
            ->where('training_schedule_id', $scheduleId)
            ->orderBy('full_name')
            ->get();

        return view('training-exams.results', compact('schedule', 'assignment', 'enrollments'));
    }

    // ── View a participant's answers for an attempt ───────────────────────

    public function viewAnswers($attemptId)
    {
        $attempt = ParticipantTestAttempt::with([
            'enrollment',
            'questionSet.questions.options',
            'answers.question.options',
        ])->findOrFail($attemptId);

        return view('training-exams.view-answers', compact('attempt'));
    }

    // ── Grade manually ────────────────────────────────────────────────────

    public function grade(Request $request, $attemptId)
    {
        $attempt = ParticipantTestAttempt::with('answers.question', 'questionSet')->findOrFail($attemptId);

        $grades = $request->input('grades', []); // [answer_id => ['marks' => x, 'notes' => '...']]

        foreach ($grades as $answerId => $grade) {
            $answer = ParticipantTestAnswer::where('attempt_id', $attemptId)->find($answerId);
            if (!$answer) continue;

            $maxMarks = $answer->question->marks ?? 0;
            $awarded  = min((int) ($grade['marks'] ?? 0), $maxMarks);

            $answer->update([
                'marks_awarded'  => $awarded,
                'is_correct'     => $awarded >= $maxMarks,
                'manual_graded'  => true,
                'reviewer_notes' => $grade['notes'] ?? null,
            ]);
        }

        // Recalculate total score
        $attempt->load('answers', 'questionSet');
        $totalScore    = $attempt->answers->sum('marks_awarded');
        $passMark      = $attempt->questionSet->effectivePassMark();
        $percentage    = $attempt->questionSet->total_marks > 0
            ? round(($totalScore / $attempt->questionSet->total_marks) * 100, 2)
            : 0;
        $stillPending  = $attempt->answers->where('manual_graded', false)
            ->whereIn('question.question_type', ['paragraph','file_upload','short_answer'])
            ->count();

        $passFail = $totalScore >= $passMark;
        $status   = $stillPending > 0 ? 'pending_review' : ($passFail ? 'passed' : 'failed');

        $attempt->update([
            'score'                 => $totalScore,
            'percentage'            => $percentage,
            'pass_fail'             => $passFail,
            'manual_review_pending' => $stillPending > 0,
            'status'                => $status,
        ]);

        ExamService::updateResultSummary($attempt->enrollment_id, $attempt->question_set_id);

        if (in_array($status, ['passed', 'failed'])) {
            ExamService::sendResultEmail($attempt->fresh()->load('enrollment.trainingSchedule.course', 'questionSet'));
        }

        $scheduleId = $attempt->enrollment->training_schedule_id;
        return redirect("/admin/training-exams/{$scheduleId}/results")
            ->with('success', 'Manual grading saved and result updated.');
    }

    // ── Reset an attempt (give fresh attempt slot) ────────────────────────

    public function resetAttempt($enrollmentId)
    {
        $enrollment = Enrollment::findOrFail($enrollmentId);
        $assignment = TrainingQuestionAssignment::where('training_schedule_id', $enrollment->training_schedule_id)->first();
        if (!$assignment) {
            return back()->with('error', 'No exam assigned to this schedule.');
        }

        // Send a new exam email (creates new attempt token)
        ExamService::sendExamEmail($enrollment);

        $scheduleId = $enrollment->training_schedule_id;
        return redirect("/admin/training-exams/{$scheduleId}/results")
            ->with('success', 'New attempt created and exam link sent to participant.');
    }

    // ── Send reminder email ───────────────────────────────────────────────

    public function sendReminder($enrollmentId)
    {
        $enrollment = Enrollment::findOrFail($enrollmentId);

        // Find the most recent not_started attempt to use its token
        $attempt = ParticipantTestAttempt::where('enrollment_id', $enrollmentId)
            ->where('status', 'not_started')
            ->latest()
            ->first();

        if (!$attempt) {
            // Create fresh attempt
            ExamService::sendExamEmail($enrollment);
        } else {
            // Resend with the existing token
            $enrollment->load('trainingSchedule.course');
            $courseName = $enrollment->trainingSchedule?->course?->name ?? 'Training Programme';
            $qs = $attempt->questionSet ?? $attempt->load('questionSet')->questionSet;
            $passMark = $qs->effectivePassMark();
            $passText = $qs->pass_percentage
                ? "{$qs->pass_percentage}% ({$passMark}/{$qs->total_marks})"
                : "{$passMark}/{$qs->total_marks} marks";

            $data = [
                'participant_name' => $enrollment->full_name,
                'course_name'      => $courseName,
                'exam_title'       => $qs->title,
                'exam_url'         => url('/exam/' . $attempt->exam_token),
                'pass_mark_text'   => $passText,
                'allowed_attempts' => $qs->allowed_attempts ?? 1,
                'time_limit'       => $qs->time_limit_minutes ? "{$qs->time_limit_minutes} minutes" : 'No time limit',
                'cert_note'        => '',
                'is_reminder'      => true,
            ];

            try {
                \Illuminate\Support\Facades\Mail::to($enrollment->email)->send(new \App\Mail\TrainingMail(
                    "Reminder: Please Complete Your Knowledge Test – {$courseName}",
                    'emails.exam-invitation',
                    ['emailData' => $data],
                    []
                ));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Reminder email failed', ['error' => $e->getMessage()]);
            }
        }

        $scheduleId = $enrollment->training_schedule_id;
        return redirect("/admin/training-exams/{$scheduleId}/results")
            ->with('success', 'Reminder email sent to ' . $enrollment->full_name);
    }
}
