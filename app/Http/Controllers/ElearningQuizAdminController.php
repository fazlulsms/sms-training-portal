<?php

namespace App\Http\Controllers;

use App\Models\ElearningEnrollment;
use App\Models\ElearningLesson;
use App\Models\ElearningQuiz;
use App\Models\QuizAdminAction;
use App\Models\QuizAttempt;
use App\Models\QuizAttemptOverride;
use App\Services\LessonProgressService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ElearningQuizAdminController extends Controller
{
    public function __construct(private LessonProgressService $progressService) {}

    // ── Attempt history + admin actions page ───────────────────────────
    public function attempts(ElearningEnrollment $enrollment, ElearningQuiz $quiz)
    {
        $quiz->load(['lesson', 'questions']);

        $attempts = QuizAttempt::where('elearning_enrollment_id', $enrollment->id)
            ->where('quiz_id', $quiz->id)
            ->orderByDesc('created_at')
            ->get();

        $override = QuizAttemptOverride::where('enrollment_id', $enrollment->id)
            ->where('quiz_id', $quiz->id)
            ->first();

        $auditLog = QuizAdminAction::where('enrollment_id', $enrollment->id)
            ->where('quiz_id', $quiz->id)
            ->with('admin')
            ->orderByDesc('created_at')
            ->get();

        $effectiveMax   = $quiz->max_attempt + ($override?->extra_attempts ?? 0);
        $attemptsTaken  = $attempts->count();
        $bestScore      = $attempts->max('score');
        $passed         = $attempts->where('score', '>=', $quiz->pass_mark)->isNotEmpty();
        $blocked        = !$passed && ($effectiveMax > 0) && ($attemptsTaken >= $effectiveMax);

        return view('elearning.quizzes.attempts', compact(
            'enrollment',
            'quiz',
            'attempts',
            'override',
            'auditLog',
            'effectiveMax',
            'attemptsTaken',
            'bestScore',
            'passed',
            'blocked',
        ));
    }

    // ── Reset all attempts ─────────────────────────────────────────────
    public function resetAttempts(Request $request, ElearningEnrollment $enrollment, ElearningQuiz $quiz)
    {
        $request->validate(['reason' => 'required|string|max:1000']);

        DB::transaction(function () use ($request, $enrollment, $quiz) {
            $prevBest = QuizAttempt::where('elearning_enrollment_id', $enrollment->id)
                ->where('quiz_id', $quiz->id)
                ->max('score');

            QuizAttempt::where('elearning_enrollment_id', $enrollment->id)
                ->where('quiz_id', $quiz->id)
                ->delete();

            // Also clear any existing override — fresh start
            QuizAttemptOverride::where('enrollment_id', $enrollment->id)
                ->where('quiz_id', $quiz->id)
                ->delete();

            QuizAdminAction::log(
                QuizAdminAction::RESET_ATTEMPTS,
                Auth::id(),
                $enrollment->id,
                $quiz->id,
                $request->reason,
                $prevBest
            );
        });

        return back()->with('success',
            "Attempts reset for {$enrollment->participant_name}. Learner may retake from attempt 1."
        );
    }

    // ── Grant one extra attempt ────────────────────────────────────────
    public function addExtraAttempt(Request $request, ElearningEnrollment $enrollment, ElearningQuiz $quiz)
    {
        $request->validate(['reason' => 'required|string|max:1000']);

        DB::transaction(function () use ($request, $enrollment, $quiz) {
            $override = QuizAttemptOverride::firstOrNew([
                'enrollment_id' => $enrollment->id,
                'quiz_id'       => $quiz->id,
            ]);

            $override->extra_attempts = ($override->extra_attempts ?? 0) + 1;
            $override->admin_user_id  = Auth::id();
            $override->reason         = $request->reason;
            $override->save();

            $currentCount = QuizAttempt::where('elearning_enrollment_id', $enrollment->id)
                ->where('quiz_id', $quiz->id)
                ->count();

            QuizAdminAction::log(
                QuizAdminAction::ADD_EXTRA_ATTEMPT,
                Auth::id(),
                $enrollment->id,
                $quiz->id,
                $request->reason,
                null,
                null,
                [
                    'current_attempt_count' => $currentCount,
                    'extra_attempts_total'  => $override->extra_attempts,
                    'effective_max'         => $quiz->max_attempt + $override->extra_attempts,
                ]
            );
        });

        return back()->with('success',
            "Extra attempt granted to {$enrollment->participant_name}."
        );
    }

    // ── Mark quiz as passed (admin override) ───────────────────────────
    public function markPassed(Request $request, ElearningEnrollment $enrollment, ElearningQuiz $quiz)
    {
        $request->validate(['reason' => 'required|string|max:1000']);

        DB::transaction(function () use ($request, $enrollment, $quiz) {
            $prevBest = QuizAttempt::where('elearning_enrollment_id', $enrollment->id)
                ->where('quiz_id', $quiz->id)
                ->max('score');

            $totalQ = $quiz->questions()->where('status', 'active')->count() ?: 1;

            // Synthetic passed attempt — score=100 always exceeds any pass_mark
            QuizAttempt::create([
                'enrollment_id'           => null,
                'elearning_enrollment_id' => $enrollment->id,
                'quiz_id'                 => $quiz->id,
                'total_questions'         => $totalQ,
                'correct_answers'         => $totalQ,
                'score'                   => 100.00,
            ]);

            QuizAdminAction::log(
                QuizAdminAction::MARK_PASSED,
                Auth::id(),
                $enrollment->id,
                $quiz->id,
                $request->reason,
                $prevBest,
                'passed',
                ['override_by' => Auth::user()->name ?? ('User #' . Auth::id())]
            );

            // Auto-complete the lesson and recalculate course progress
            $lesson = ElearningLesson::find($quiz->lesson_id);
            if ($lesson && $enrollment->user_id) {
                $this->progressService->markCompleted(
                    $enrollment->user_id,
                    $enrollment,
                    $lesson
                );
            } elseif ($lesson) {
                // Enrollment has no linked user account yet — still recalculate progress
                $this->progressService->recalculateProgress($enrollment);
            }
        });

        return back()->with('success',
            "Quiz marked as passed for {$enrollment->participant_name}. Progress updated."
        );
    }
}
