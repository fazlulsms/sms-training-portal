<?php

namespace App\Http\Controllers;

use App\Models\ElearningEnrollment;
use App\Models\ElearningLesson;
use App\Models\ElearningQuiz;
use App\Models\QuizAttempt;
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

        if ($quiz->max_attempt > 0 && $attemptCount >= $quiz->max_attempt) {
            return redirect()
                ->route('participant.elearning-details', $enrollment->id)
                ->with('error', 'Maximum quiz attempts reached.');
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

            if ($quiz->max_attempt > 0 && $attemptCount >= $quiz->max_attempt) {
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
                ->with('error', 'Maximum quiz attempts reached.');
        }

        // If quiz passed → auto-complete the parent lesson via LessonProgressService
        if ($score >= $quiz->pass_mark) {
            $lesson = ElearningLesson::find($quiz->lesson_id);
            if ($lesson) {
                $this->progressService->markCompleted(Auth::id(), $enrollment, $lesson);
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
