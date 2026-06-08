<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\ElearningLesson;
use App\Models\ElearningQuiz;
use App\Models\ElearningQuizQuestion;
use Illuminate\Http\Request;

class ElearningQuizQuestionController extends Controller
{
    public function index(Course $course, ElearningLesson $lesson, ElearningQuiz $quiz)
    {
        $questions = $quiz->questions()->latest()->paginate(20);

        return view('elearning.quiz_questions.index', compact(
            'course',
            'lesson',
            'quiz',
            'questions'
        ));
    }

    public function create(Course $course, ElearningLesson $lesson, ElearningQuiz $quiz)
    {
        return view('elearning.quiz_questions.create', compact(
            'course',
            'lesson',
            'quiz'
        ));
    }

    public function store(Request $request, Course $course, ElearningLesson $lesson, ElearningQuiz $quiz)
    {
        $request->validate([
            'question_text' => 'required|string',
            'question_type' => 'required|in:mcq,true_false',
            'option_a' => 'nullable|string|max:255',
            'option_b' => 'nullable|string|max:255',
            'option_c' => 'nullable|string|max:255',
            'option_d' => 'nullable|string|max:255',
            'correct_answer' => 'required|string|max:10',
            'marks' => 'required|integer|min:1',
            'status' => 'required|in:active,inactive',
        ]);

        ElearningQuizQuestion::create([
            'quiz_id' => $quiz->id,
            'question_text' => $request->question_text,
            'question_type' => $request->question_type,
            'option_a' => $request->option_a,
            'option_b' => $request->option_b,
            'option_c' => $request->option_c,
            'option_d' => $request->option_d,
            'correct_answer' => $request->correct_answer,
            'marks' => $request->marks,
            'status' => $request->status,
        ]);

        return redirect()
            ->route('elearning.quiz-questions.index', [$course, $lesson, $quiz])
            ->with('success', 'Question added successfully.');
    }

    public function edit(Course $course, ElearningLesson $lesson, ElearningQuiz $quiz, ElearningQuizQuestion $question)
    {
        return view('elearning.quiz_questions.edit', compact(
            'course',
            'lesson',
            'quiz',
            'question'
        ));
    }

    public function update(Request $request, Course $course, ElearningLesson $lesson, ElearningQuiz $quiz, ElearningQuizQuestion $question)
    {
        $request->validate([
            'question_text' => 'required|string',
            'question_type' => 'required|in:mcq,true_false',
            'correct_answer' => 'required|string|max:10',
            'marks' => 'required|integer|min:1',
            'status' => 'required|in:active,inactive',
        ]);

        $question->update($request->all());

        return redirect()
            ->route('elearning.quiz-questions.index', [$course, $lesson, $quiz])
            ->with('success', 'Question updated successfully.');
    }

    public function destroy(Course $course, ElearningLesson $lesson, ElearningQuiz $quiz, ElearningQuizQuestion $question)
    {
        $question->delete();

        return back()->with('success', 'Question deleted successfully.');
    }
}