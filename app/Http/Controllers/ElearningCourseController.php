<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseCategory;
use Illuminate\Http\Request;

class ElearningCourseController extends Controller
{
    public function index()
    {
        $courses = Course::where(function ($q) {
                $q->where('course_type', 'elearning')
                  ->orWhere('delivery_type', 'eLearning');
            })
            ->latest()
            ->paginate(15);

        return view('elearning.courses.index', compact('courses'));
    }

    public function create()
    {
        try {
            $categories = CourseCategory::orderBy('name')->get();
        } catch (\Exception $e) {
            $categories = collect();
        }
        return view('elearning.courses.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'code'         => 'nullable|string|max:100',
            'status'       => 'required|in:0,1',
            'slug'         => 'nullable|string|max:255|unique:courses,slug',
            'banner_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'passing_score'=> 'nullable|integer|min:1|max:100',
        ]);

        $data = $request->only([
            'name', 'code', 'slug', 'category', 'category_id', 'status',
            'language', 'duration', 'cpd_hours',
            'short_description', 'full_description', 'learning_objectives',
            'course_outline', 'who_should_attend', 'prerequisites',
            'certificate_type', 'certification_remarks', 'certification_info',
            'course_fee', 'public_price', 'access_days', 'passing_score',
            'is_public', 'is_featured', 'display_order', 'featured_order',
            'course_video_url', 'faq', 'seo_title', 'seo_description', 'seo_keywords',
        ]);

        $data['course_type']   = 'elearning';
        $data['delivery_type'] = 'eLearning';
        $data['is_public']     = $request->boolean('is_public');
        $data['is_featured']   = $request->boolean('is_featured');

        if ($request->hasFile('banner_image')) {
            $data['banner_image'] = $request->file('banner_image')->store('courses', 'public');
        }

        Course::create($data);

        return redirect()
            ->route('elearning.courses.index')
            ->with('success', 'eLearning course created successfully.');
    }

    public function show(Course $course)
    {
        return redirect()->route('elearning.lessons.index', $course->id);
    }

    public function edit(Course $course)
    {
        try {
            $categories = CourseCategory::orderBy('name')->get();
        } catch (\Exception $e) {
            $categories = collect();
        }
        return view('elearning.courses.edit', compact('course', 'categories'));
    }

    public function update(Request $request, Course $course)
    {
        $request->validate([
            'name'                      => 'required|string|max:255',
            'code'                      => 'nullable|string|max:100',
            'status'                    => 'required|in:0,1',
            'slug'                      => 'nullable|string|max:255|unique:courses,slug,' . $course->id,
            'banner_image'              => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'passing_score'             => 'nullable|integer|min:1|max:100',
            'assessment_policy'         => 'nullable|in:normal,auditor,custom',
            'module_check_max_attempts' => 'nullable|integer|min:1|max:10',
            'final_exam_max_attempts'   => 'nullable|integer|min:1|max:10',
        ]);

        $data = $request->only([
            'name', 'code', 'slug', 'category', 'category_id', 'status',
            'language', 'duration', 'cpd_hours',
            'short_description', 'full_description', 'learning_objectives',
            'course_outline', 'who_should_attend', 'prerequisites',
            'certificate_type', 'certification_remarks', 'certification_info',
            'course_fee', 'public_price', 'access_days', 'passing_score',
            'is_public', 'is_featured', 'display_order', 'featured_order',
            'course_video_url', 'faq', 'seo_title', 'seo_description', 'seo_keywords',
            'assessment_policy', 'module_check_max_attempts', 'final_exam_max_attempts',
        ]);

        $data['course_type']            = 'elearning';
        $data['delivery_type']          = 'eLearning';
        $data['require_module_review']  = $request->boolean('require_module_review');
        $data['require_admin_approval'] = $request->boolean('require_admin_approval');
        $data['is_public']     = $request->boolean('is_public');
        $data['is_featured']   = $request->boolean('is_featured');

        if ($request->hasFile('banner_image')) {
            $data['banner_image'] = $request->file('banner_image')->store('courses', 'public');
        }

        $course->update($data);

        $action = $request->input('_action', 'save');
        $tab    = in_array($request->input('_tab'), ['basic', 'content', 'seo'])
                  ? $request->input('_tab')
                  : 'basic';

        if ($action === 'back') {
            return redirect()->route('elearning.courses.index')
                ->with('success', 'Course updated successfully.');
        }

        if ($action === 'lessons') {
            return redirect()->route('elearning.lessons.index', $course->id)
                ->with('success', 'Course updated successfully. Now manage your lessons.');
        }

        return redirect()->route('elearning.courses.edit', $course->id)
            ->with('success', 'Course updated successfully.')
            ->with('active_tab', $tab);
    }

    public function destroy(Course $course)
    {
        $course->delete();

        return redirect()
            ->route('elearning.courses.index')
            ->with('success', 'Course deleted successfully.');
    }
}