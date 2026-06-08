<?php

namespace App\Http\Controllers;

use App\Models\ElearningEnrollment;
use App\Models\ElearningQuiz;
use App\Models\ElearningQuizAttempt;
use App\Models\ElearningQuizAnswer;
use Illuminate\Http\Request;

class ElearningQuizAttemptController extends Controller
{
    public function start(ElearningEnrollment $enrollment, ElearningQuiz $quiz)
    {
        $questions = $quiz->questions()
            ->where('status', 'active')
            ->get();

        if ($questions->count() == 0) {
            return back()->with('error', 'No active questions found.');
        }

        $attemptCount = ElearningQuizAttempt::where('enrollment_id', $enrollment->id)
            ->where('quiz_id', $quiz->id)
            ->count();

        if ($attemptCount >= $quiz->max_attempt) {
            return back()->with('error', 'Maximum attempts reached.');
        }

        $attempt = ElearningQuizAttempt::create([
            'enrollment_id' => $enrollment->id,
            'quiz_id' => $quiz->id,
            'attempt_no' => $attemptCount + 1,
            'started_at' => now(),
        ]);

        return view('elearning.quiz_attempts.take', compact(
            'enrollment',
            'quiz',
            'questions',
            'attempt'
        ));
    }

    public function submit(Request $request, ElearningEnrollment $enrollment, ElearningQuiz $quiz, ElearningQuizAttempt $attempt)
    {
        $questions = $quiz->questions()->where('status', 'active')->get();

        $score = 0;
        $totalMarks = 0;

        foreach ($questions as $question) {
            $selected = $request->input('question_' . $question->id);

            $isCorrect = strtolower(trim($selected)) === strtolower(trim($question->correct_answer));
            $marks = $isCorrect ? $question->marks : 0;

            ElearningQuizAnswer::create([
                'attempt_id' => $attempt->id,
                'question_id' => $question->id,
                'selected_answer' => $selected,
                'is_correct' => $isCorrect,
                'marks_obtained' => $marks,
            ]);

            $score += $marks;
            $totalMarks += $question->marks;
        }

        $percentage = $totalMarks > 0 ? ($score / $totalMarks) * 100 : 0;
        $passed = $percentage >= $quiz->pass_mark;

        $attempt->update([
            'score' => $score,
            'total_marks' => $totalMarks,
            'passed' => $passed,
            'submitted_at' => now(),
        ]);

        return view('elearning.quiz_attempts.result', compact(
            'enrollment',
            'quiz',
            'attempt',
            'percentage'
        ));
    }
}