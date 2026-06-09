<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $q           = $request->input('q', $request->input('search'));
        $courseType  = $request->input('course_type');
        $status      = $request->input('status');

        $courses = Course::query()
            ->when($q, fn($query) => $query->where(fn($sub) =>
                $sub->where('name', 'like', "%$q%")
                    ->orWhere('code', 'like', "%$q%")
            ))
            ->when($courseType, fn($query) => $query->where('course_type', $courseType))
            ->when($status !== null && $status !== '', fn($query) => $query->where('status', $status === 'active' ? 1 : 0))
            ->orderBy('id', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('courses.index', compact('courses'));
    }

    public function exportCsv(Request $request)
    {
        $q          = $request->input('q');
        $courseType = $request->input('course_type');
        $status     = $request->input('status');

        $courses = Course::query()
            ->when($q, fn($query) => $query->where(fn($sub) =>
                $sub->where('name', 'like', "%$q%")
                    ->orWhere('code', 'like', "%$q%")
            ))
            ->when($courseType, fn($query) => $query->where('course_type', $courseType))
            ->when($status !== null && $status !== '', fn($query) => $query->where('status', $status === 'active' ? 1 : 0))
            ->orderBy('name')
            ->get();

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="courses_' . now()->format('Ymd') . '.csv"',
        ];

        return response()->stream(function () use ($courses) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Name', 'Code', 'Type', 'Status']);
            foreach ($courses as $c) {
                fputcsv($handle, [
                    $c->id,
                    $c->name,
                    $c->code,
                    $c->course_type,
                    $c->status ? 'Active' : 'Inactive',
                ]);
            }
            fclose($handle);
        }, 200, $headers);
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