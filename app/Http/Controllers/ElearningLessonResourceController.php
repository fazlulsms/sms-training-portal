<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\ElearningLesson;
use App\Models\ElearningLessonResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ElearningLessonResourceController extends Controller
{
    public function index(Course $course, ElearningLesson $lesson)
    {
        $resources = $lesson->resources()->latest()->paginate(15);

        return view('elearning.resources.index', compact('course', 'lesson', 'resources'));
    }

    public function create(Course $course, ElearningLesson $lesson)
    {
        return view('elearning.resources.create', compact('course', 'lesson'));
    }

    public function store(Request $request, Course $course, ElearningLesson $lesson)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'resource_type' => 'required|in:file,link',
            'resource_file' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,jpg,jpeg,png|max:10240',
            'external_url' => 'nullable|url',
            'status' => 'required|in:active,inactive',
        ]);

        $filePath = null;

        if ($request->hasFile('resource_file')) {
            $filePath = $request->file('resource_file')->store('elearning/resources', 'public');
        }

        ElearningLessonResource::create([
            'lesson_id' => $lesson->id,
            'title' => $request->title,
            'resource_type' => $request->resource_type,
            'file_path' => $filePath,
            'external_url' => $request->external_url,
            'status' => $request->status,
        ]);

        return redirect()
            ->route('elearning.resources.index', [$course, $lesson])
            ->with('success', 'Resource added successfully.');
    }

    public function destroy(Course $course, ElearningLesson $lesson, ElearningLessonResource $resource)
    {
        if ($resource->file_path && Storage::disk('public')->exists($resource->file_path)) {
            Storage::disk('public')->delete($resource->file_path);
        }

        $resource->delete();

        return back()->with('success', 'Resource deleted successfully.');
    }
}