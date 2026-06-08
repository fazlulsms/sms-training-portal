<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class ElearningCourseController extends Controller
{
    public function index()
    {
        $courses = Course::where('course_type', 'elearning')
            ->latest()
            ->paginate(10);

        return view('elearning.courses.index', compact('courses'));
    }

    public function create()
    {
        return view('elearning.courses.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:100',
            'description' => 'nullable|string',
            'course_fee' => 'nullable|numeric',
            'access_days' => 'nullable|integer',
            'passing_score' => 'required|integer|min:1|max:100',
            'duration' => 'nullable|string|max:100',
            'cpd_hours' => 'nullable|integer|min:0',
            'status' => 'required|in:1,0',
        ]);

        Course::create([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'course_fee' => $request->course_fee,
            'access_days' => $request->access_days,
            'passing_score' => $request->passing_score,
            'duration' => $request->duration,
            'cpd_hours' => $request->cpd_hours,
            'status' => $request->status,
            'course_type' => 'elearning',
        ]);

        return redirect()
            ->route('elearning.courses.index')
            ->with('success', 'eLearning course created successfully.');
    }

    public function edit(Course $course)
    {
        return view('elearning.courses.edit', compact('course'));
    }

    public function update(Request $request, Course $course)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:100',
            'description' => 'nullable|string',
            'course_fee' => 'nullable|numeric',
            'access_days' => 'nullable|integer',
            'passing_score' => 'required|integer|min:1|max:100',
            'duration' => 'nullable|string|max:100',
            'cpd_hours' => 'nullable|integer|min:0',
            'status' => 'required|in:1,0',
        ]);

        $course->update([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'course_fee' => $request->course_fee,
            'access_days' => $request->access_days,
            'passing_score' => $request->passing_score,
            'duration' => $request->duration,
            'cpd_hours' => $request->cpd_hours,
            'status' => (int) $request->status,
            'course_type' => 'elearning',
        ]);

        return redirect()
            ->route('elearning.courses.index')
            ->with('success', 'eLearning course updated successfully.');
    }

    public function destroy(Course $course)
    {
        $course->delete();

        return redirect()
            ->route('elearning.courses.index')
            ->with('success', 'Course deleted successfully.');
    }public function show(Course $course)
{
    return redirect()->route('elearning.lessons.index', $course->id);
}
}