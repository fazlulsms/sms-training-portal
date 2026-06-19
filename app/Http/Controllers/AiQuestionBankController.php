<?php

namespace App\Http\Controllers;

use App\Models\AiQuestionBank;
use App\Models\Course;
use Illuminate\Http\Request;

class AiQuestionBankController extends Controller
{
    private function guard(): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
    }

    public function index(Request $request)
    {
        $this->guard();
        $questions = AiQuestionBank::with(['resource', 'lesson', 'course'])
            ->when($request->filled('course_id'), fn ($query) => $query->whereHas('courses', fn ($courseQuery) => $courseQuery->where('courses.id', $request->integer('course_id'))))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')))
            ->when($request->filled('difficulty'), fn ($query) => $query->where('difficulty', $request->input('difficulty')))
            ->latest()->paginate(30)->withQueryString();
        $courses = Course::whereHas('knowledgeResources')->orderBy('name')->get(['id', 'name']);
        return view('ai.question-bank.index', compact('questions', 'courses'));
    }

    public function updateStatus(Request $request, AiQuestionBank $question)
    {
        $this->guard();
        $data = $request->validate(['status' => 'required|in:draft,approved,archived']);
        $question->update($data);
        return back()->with('success', 'Question status updated.');
    }
}
