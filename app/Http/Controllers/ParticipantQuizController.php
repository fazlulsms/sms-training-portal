<?php

namespace App\Http\Controllers;

use App\Models\ElearningEnrollment;
use App\Models\ElearningLesson;
use App\Models\ElearningQuiz;
use App\Models\QuizAttempt;
use App\Models\QuizAttemptOverride;
use App\Models\QuizReviewGate;
use App\Services\LessonProgressService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ParticipantQuizController extends Controller
{
    public function __construct(private LessonProgressService $progressService) {}

    public function start(ElearningEnrollment $enrollment, ElearningQuiz $quiz)
    {
        abort_unless($enrollment->user_id === Auth::id(), 403);

        // Payment / access gate
        abort_unless(
            $enrollment->isAccessible(),
            403,
            'Course access is locked. Please complete payment to access quizzes.'
        );

        // Expiry gate
        abort_if(
            $enrollment->expires_at && $enrollment->expires_at->isPast(),
            403,
            'Your enrollment has expired. Please contact the administrator.'
        );

        // Quiz must belong to the enrolled course
        $quiz->load('lesson');
        abort_unless(
            $quiz->lesson && $quiz->lesson->course_id === $enrollment->course_id,
            403,
            'This quiz does not belong to your enrolled course.'
        );

        $attemptCount = QuizAttempt::where('elearning_enrollment_id', $enrollment->id)
            ->where('quiz_id', $quiz->id)
            ->count();

        // Respect per-enrollment override before applying global max_attempt limit
        $override     = QuizAttemptOverride::where('enrollment_id', $enrollment->id)
            ->where('quiz_id', $quiz->id)
            ->first();
        $effectiveMax = $quiz->max_attempt + ($override?->extra_attempts ?? 0);

        if ($effectiveMax > 0 && $attemptCount >= $effectiveMax) {
            // Check if there's a completed review gate that granted extra attempts — already factored in via override
            // Check if there's a pending review gate (learner must review first)
            $pendingGate = QuizReviewGate::where('enrollment_id', $enrollment->id)
                ->where('quiz_id', $quiz->id)
                ->where('status', 'pending')
                ->latest()
                ->first();

            if ($pendingGate) {
                $reviewed  = $pendingGate->reviewedCount();
                $required  = $pendingGate->requiredCount();
                return redirect()
                    ->route('participant.elearning-details', $enrollment->id)
                    ->with('review_required', [
                        'quiz_id'      => $quiz->id,
                        'quiz_title'   => $quiz->title,
                        'reviewed'     => $reviewed,
                        'required'     => $required,
                        'enrollment_id'=> $enrollment->id,
                    ]);
            }

            $requiresReview = $enrollment->course->require_module_review ?? true;
            $isFinalExam    = str_contains($quiz->lesson->title ?? '', 'Final Course Assessment');

            if ($requiresReview && !$isFinalExam) {
                return redirect()
                    ->route('participant.elearning-details', $enrollment->id)
                    ->with('error', 'You have used all attempts for this knowledge check. Please review this module again to unlock more attempts.');
            }

            return redirect()
                ->route('participant.elearning-details', $enrollment->id)
                ->with('error', 'Maximum quiz attempts reached. Please contact your administrator.');
        }

        $quiz->load('questions');

        return view('participant.quiz-start', compact('enrollment', 'quiz', 'attemptCount'));
    }

    public function submit(Request $request, ElearningEnrollment $enrollment, ElearningQuiz $quiz)
    {
        abort_unless($enrollment->user_id === Auth::id(), 403);

        // Payment / access gate
        abort_unless(
            $enrollment->isAccessible(),
            403,
            'Course access is locked. Please complete payment to submit quizzes.'
        );

        // Expiry gate
        abort_if(
            $enrollment->expires_at && $enrollment->expires_at->isPast(),
            403,
            'Your enrollment has expired. Please contact the administrator.'
        );

        // Quiz must belong to the enrolled course
        $quiz->load('lesson');
        abort_unless(
            $quiz->lesson && $quiz->lesson->course_id === $enrollment->course_id,
            403,
            'This quiz does not belong to your enrolled course.'
        );

        $quiz->load('questions');

        $answers        = $request->input('answers', []);
        $totalQuestions = $quiz->questions->count();
        $correctAnswers = 0;

        foreach ($quiz->questions as $question) {
            $submitted = strtolower(trim($answers[$question->id] ?? ''));
            $correct   = strtolower(trim($question->correct_answer ?? ''));

            if ($submitted === $correct) {
                $correctAnswers++;
            }
        }

        $score = $totalQuestions > 0
            ? round(($correctAnswers / $totalQuestions) * 100, 2)
            : 0;

        // Wrap attempt count re-check + creation in a transaction to prevent race conditions
        $attempt = DB::transaction(function () use ($enrollment, $quiz, $totalQuestions, $correctAnswers, $score) {
            $attemptCount = QuizAttempt::where('elearning_enrollment_id', $enrollment->id)
                ->where('quiz_id', $quiz->id)
                ->lockForUpdate()
                ->count();

            $override     = QuizAttemptOverride::where('enrollment_id', $enrollment->id)
                ->where('quiz_id', $quiz->id)
                ->first();
            $effectiveMax = $quiz->max_attempt + ($override?->extra_attempts ?? 0);

            if ($effectiveMax > 0 && $attemptCount >= $effectiveMax) {
                return null; // Signal over-limit
            }

            return QuizAttempt::create([
                'enrollment_id'           => null,
                'elearning_enrollment_id' => $enrollment->id,
                'quiz_id'                 => $quiz->id,
                'total_questions'         => $totalQuestions,
                'correct_answers'         => $correctAnswers,
                'score'                   => $score,
            ]);
        });

        if ($attempt === null) {
            return redirect()
                ->route('participant.elearning-details', $enrollment->id)
                ->with('error', 'Maximum quiz attempts reached. Please contact your administrator.');
        }

        // If quiz passed → auto-complete the parent lesson via LessonProgressService
        if ($score >= $quiz->pass_mark) {
            $lesson = ElearningLesson::find($quiz->lesson_id);
            if ($lesson) {
                $this->progressService->markCompleted(Auth::id(), $enrollment, $lesson);
            }
        } else {
            // Check if attempts are now exhausted — create a module review gate if applicable
            $totalAttempts = QuizAttempt::where('elearning_enrollment_id', $enrollment->id)
                ->where('quiz_id', $quiz->id)
                ->count();

            $latestOverride  = QuizAttemptOverride::where('enrollment_id', $enrollment->id)->where('quiz_id', $quiz->id)->first();
            $currentMax      = $quiz->max_attempt + ($latestOverride?->extra_attempts ?? 0);
            $isFinalExam     = str_contains($quiz->lesson->title ?? '', 'Final Course Assessment');
            $requiresReview  = $enrollment->course->require_module_review ?? true;

            if ($currentMax > 0 && $totalAttempts >= $currentMax && $requiresReview && !$isFinalExam) {
                // No pending gate already exists
                $alreadyGated = QuizReviewGate::where('enrollment_id', $enrollment->id)
                    ->where('quiz_id', $quiz->id)
                    ->where('status', 'pending')
                    ->exists();

                if (!$alreadyGated) {
                    // Identify module lessons: non-assessment lessons between previous check and this check
                    $quizLesson     = ElearningLesson::find($quiz->lesson_id);
                    $prevAssessment = ElearningLesson::where('course_id', $quizLesson->course_id)
                        ->where('lesson_type', 'assessment')
                        ->where('lesson_order', '<', $quizLesson->lesson_order)
                        ->orderBy('lesson_order', 'desc')
                        ->first();

                    $minOrder = $prevAssessment ? $prevAssessment->lesson_order : 0;

                    $moduleLessonIds = ElearningLesson::where('course_id', $quizLesson->course_id)
                        ->where('lesson_type', '!=', 'assessment')
                        ->where('lesson_order', '>', $minOrder)
                        ->where('lesson_order', '<', $quizLesson->lesson_order)
                        ->pluck('id')
                        ->toArray();

                    if (!empty($moduleLessonIds)) {
                        QuizReviewGate::create([
                            'enrollment_id'        => $enrollment->id,
                            'quiz_id'              => $quiz->id,
                            'required_lesson_ids'  => $moduleLessonIds,
                            'reviewed_lesson_ids'  => [],
                            'extra_attempts_granted'=> 3,
                            'status'               => 'pending',
                            'triggered_at'         => now(),
                        ]);
                    }
                }
            }
        }

        $attempts = QuizAttempt::where('elearning_enrollment_id', $enrollment->id)
            ->where('quiz_id', $quiz->id)
            ->latest()
            ->get();

        return view('participant.quiz-result', compact(
            'enrollment',
            'quiz',
            'totalQuestions',
            'correctAnswers',
            'score',
            'attempt',
            'attempts'
        ));
    }
}
