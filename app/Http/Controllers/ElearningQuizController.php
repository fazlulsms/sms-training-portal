<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\ElearningLesson;
use App\Models\ElearningQuiz;
use Illuminate\Http\Request;

class ElearningQuizController extends Controller
{
    public function index(Course $course, ElearningLesson $lesson)
    {
        $quizzes = $lesson->quizzes()->latest()->paginate(10);

        return view('elearning.quizzes.index', compact('course', 'lesson', 'quizzes'));
    }

    public function create(Course $course, ElearningLesson $lesson)
    {
        return view('elearning.quizzes.create', compact('course', 'lesson'));
    }

    public function store(Request $request, Course $course, ElearningLesson $lesson)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'pass_mark' => 'required|integer|min:1|max:100',
            'max_attempt' => 'required|integer|min:1',
            'status' => 'required|in:active,inactive',
        ]);

        ElearningQuiz::create([
            'lesson_id' => $lesson->id,
            'title' => $request->title,
            'description' => $request->description,
            'pass_mark' => $request->pass_mark,
            'max_attempt' => $request->max_attempt,
            'status' => $request->status,
        ]);

        return redirect()
            ->route('elearning.quizzes.index', [$course, $lesson])
            ->with('success', 'Quiz created successfully.');
    }

    public function edit(Course $course, ElearningLesson $lesson, ElearningQuiz $quiz)
    {
        return view('elearning.quizzes.edit', compact('course', 'lesson', 'quiz'));
    }

    public function update(Request $request, Course $course, ElearningLesson $lesson, ElearningQuiz $quiz)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'pass_mark' => 'required|integer|min:1|max:100',
            'max_attempt' => 'required|integer|min:1',
            'status' => 'required|in:active,inactive',
        ]);

        $quiz->update([
            'title' => $request->title,
            'description' => $request->description,
            'pass_mark' => $request->pass_mark,
            'max_attempt' => $request->max_attempt,
            'status' => $request->status,
        ]);

        return redirect()
            ->route('elearning.quizzes.index', [$course, $lesson])
            ->with('success', 'Quiz updated successfully.');
    }

    public function destroy(Course $course, ElearningLesson $lesson, ElearningQuiz $quiz)
    {
        $quiz->delete();

        return back()->with('success', 'Quiz deleted successfully.');
    }

    // ── Admin preview — reads questions without creating any attempt ────
    public function preview(Course $course, ElearningLesson $lesson, ElearningQuiz $quiz)
    {
        $quiz->load('questions');
        return view('elearning.quizzes.preview', compact('course', 'lesson', 'quiz'));
    }
}