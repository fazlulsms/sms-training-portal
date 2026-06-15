<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\LtfCourseType;
use App\Models\LtfLearningFramework;
use App\Models\LtfStandard;
use App\Models\LtfIndustry;
use App\Models\LtfAudienceType;

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
        try {
            $categories = CourseCategory::orderBy('name')->get();
        } catch (\Exception $e) {
            $categories = collect();
        }
        $ltfCourseTypes = LtfCourseType::groupedForSelect();
        $ltfFrameworks  = LtfLearningFramework::forSelect();
        $ltfStandards   = LtfStandard::groupedForSelect();
        $ltfIndustries  = LtfIndustry::forSelect();
        $ltfAudiences   = LtfAudienceType::forSelect();
        return view('courses.create', compact(
            'categories', 'ltfCourseTypes', 'ltfFrameworks',
            'ltfStandards', 'ltfIndustries', 'ltfAudiences'
        ));
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
            'ltf_course_type_id', 'ltf_learning_framework_id',
        ]);

        $data['is_public']   = $request->boolean('is_public');
        $data['is_featured'] = $request->boolean('is_featured');
        $data['ltf_course_type_id']        = $request->filled('ltf_course_type_id') ? $request->input('ltf_course_type_id') : null;
        $data['ltf_learning_framework_id'] = $request->filled('ltf_learning_framework_id') ? $request->input('ltf_learning_framework_id') : null;

        if ($request->hasFile('banner_image')) {
            $data['banner_image'] = $request->file('banner_image')->store('courses', 'public');
        }

        $course = Course::create($data);
        $course->ltfStandards()->sync($request->input('ltf_standard_ids', []));
        $course->ltfIndustries()->sync($request->input('ltf_industry_ids', []));
        $course->ltfAudiences()->sync($request->input('ltf_audience_ids', []));

        return redirect('/admin/courses')->with('success', 'Course added successfully.');
    }

    public function edit($id)
    {
        $course = Course::with(['ltfStandards', 'ltfIndustries', 'ltfAudiences'])->findOrFail($id);
        try {
            $categories = CourseCategory::orderBy('name')->get();
        } catch (\Exception $e) {
            $categories = collect();
        }
        $ltfCourseTypes      = LtfCourseType::groupedForSelect();
        $ltfFrameworks       = LtfLearningFramework::forSelect();
        $ltfStandards        = LtfStandard::groupedForSelect();
        $ltfIndustries       = LtfIndustry::forSelect();
        $ltfAudiences        = LtfAudienceType::forSelect();
        $selectedStandardIds = $course->ltfStandards->pluck('id')->toArray();
        $selectedIndustryIds = $course->ltfIndustries->pluck('id')->toArray();
        $selectedAudienceIds = $course->ltfAudiences->pluck('id')->toArray();
        return view('courses.edit', compact(
            'course', 'categories',
            'ltfCourseTypes', 'ltfFrameworks', 'ltfStandards', 'ltfIndustries', 'ltfAudiences',
            'selectedStandardIds', 'selectedIndustryIds', 'selectedAudienceIds'
        ));
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
            'ltf_course_type_id', 'ltf_learning_framework_id',
        ]);

        $data['is_public']   = $request->boolean('is_public');
        $data['is_featured'] = $request->boolean('is_featured');
        $data['ltf_course_type_id']        = $request->filled('ltf_course_type_id') ? $request->input('ltf_course_type_id') : null;
        $data['ltf_learning_framework_id'] = $request->filled('ltf_learning_framework_id') ? $request->input('ltf_learning_framework_id') : null;

        if ($request->hasFile('banner_image')) {
            $data['banner_image'] = $request->file('banner_image')->store('courses', 'public');
        }

        $course->update($data);
        $course->ltfStandards()->sync($request->input('ltf_standard_ids', []));
        $course->ltfIndustries()->sync($request->input('ltf_industry_ids', []));
        $course->ltfAudiences()->sync($request->input('ltf_audience_ids', []));


        $action = $request->input('_action', 'save');
        $tab    = in_array($request->input('_tab'), ['basic', 'content', 'seo'])
                  ? $request->input('_tab')
                  : 'basic';

        if ($action === 'back') {
            return redirect('/admin/courses')
                ->with('success', 'Course updated successfully.');
        }

        if ($action === 'lessons' && $course->course_type === 'elearning') {
            return redirect()->route('elearning.lessons.index', $id)
                ->with('success', 'Course updated successfully.');
        }

        return redirect('/admin/courses/edit/' . $id . '?tab=' . $tab)
            ->with('success', 'Course updated successfully.');
    }

    public function delete($id)
    {
        $course = Course::findOrFail($id);
        $course->delete();

        return redirect('/courses')->with('success', 'Course Deleted Successfully');
    }
}
