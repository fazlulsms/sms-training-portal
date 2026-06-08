<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('code', 'like', '%' . $request->search . '%');
            });
        }

        $courses = $query->orderBy('id', 'desc')->paginate(10);

        return view('courses.index', compact('courses'));
    }

    public function create()
    {
        return view('courses.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:100',
            'status' => 'required|in:0,1',
            'course_type' => 'required|in:manual,elearning',
            'certification_remarks' => 'nullable|string',
        ]);

        Course::create([
            'name' => $request->name,
            'code' => $request->code,
            'status' => $request->status,
            'course_type' => $request->course_type,
            'certification_remarks' => $request->certification_remarks,
        ]);

        return redirect('/courses')->with('success', 'Course Added Successfully');
    }

    public function edit($id)
    {
        $course = Course::findOrFail($id);

        return view('courses.edit', compact('course'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:100',
            'status' => 'required|in:0,1',
            'course_type' => 'required|in:manual,elearning',
            'certification_remarks' => 'nullable|string',
        ]);

        $course = Course::findOrFail($id);

        $course->update([
            'name' => $request->name,
            'code' => $request->code,
            'status' => $request->status,
            'course_type' => $request->course_type,
            'certification_remarks' => $request->certification_remarks,
        ]);

        return redirect('/courses')->with('success', 'Course Updated Successfully');
    }

    public function delete($id)
    {
        $course = Course::findOrFail($id);
        $course->delete();

        return redirect('/courses')->with('success', 'Course Deleted Successfully');
    }
}