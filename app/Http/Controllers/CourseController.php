<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\CourseCategory;

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
        $categories = CourseCategory::orderBy('name')->get();
        return view('courses.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'                 => 'required|string|max:255',
            'code'                 => 'nullable|string|max:100',
            'status'               => 'required|in:0,1',
            'course_type'          => 'required|in:manual,elearning',
            'slug'                 => 'nullable|string|max:255|unique:courses,slug',
            'banner_image'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $data = $request->only([
            'name', 'code', 'slug', 'category', 'category_id', 'status', 'course_type',
            'delivery_type', 'language', 'duration', 'cpd_hours',
            'short_description', 'full_description', 'learning_objectives',
            'course_outline', 'who_should_attend', 'prerequisites',
            'certificate_type', 'certification_remarks', 'certification_info',
            'course_fee', 'public_price',
            'is_public', 'is_featured', 'display_order', 'featured_order',
            'course_video_url', 'faq', 'seo_title', 'seo_description', 'seo_keywords',
        ]);

        $data['is_public']   = $request->boolean('is_public');
        $data['is_featured'] = $request->boolean('is_featured');

        if ($request->hasFile('banner_image')) {
            $data['banner_image'] = $request->file('banner_image')->store('courses', 'public');
        }

        Course::create($data);

        return redirect('/courses')->with('success', 'Course Added Successfully');
    }

    public function edit($id)
    {
        $course     = Course::findOrFail($id);
        $categories = CourseCategory::orderBy('name')->get();

        return view('courses.edit', compact('course', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        $request->validate([
            'name'         => 'required|string|max:255',
            'code'         => 'nullable|string|max:100',
            'status'       => 'required|in:0,1',
            'course_type'  => 'required|in:manual,elearning',
            'slug'         => 'nullable|string|max:255|unique:courses,slug,' . $id,
            'banner_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $data = $request->only([
            'name', 'code', 'slug', 'category', 'category_id', 'status', 'course_type',
            'delivery_type', 'language', 'duration', 'cpd_hours',
            'short_description', 'full_description', 'learning_objectives',
            'course_outline', 'who_should_attend', 'prerequisites',
            'certificate_type', 'certification_remarks', 'certification_info',
            'course_fee', 'public_price',
            'is_public', 'is_featured', 'display_order', 'featured_order',
            'course_video_url', 'faq', 'seo_title', 'seo_description', 'seo_keywords',
        ]);

        $data['is_public']   = $request->boolean('is_public');
        $data['is_featured'] = $request->boolean('is_featured');

        if ($request->hasFile('banner_image')) {
            $data['banner_image'] = $request->file('banner_image')->store('courses', 'public');
        }

        $course->update($data);

        return redirect('/courses')->with('success', 'Course Updated Successfully');
    }

    public function delete($id)
    {
        $course = Course::findOrFail($id);
        $course->delete();

        return redirect('/courses')->with('success', 'Course Deleted Successfully');
    }
}
