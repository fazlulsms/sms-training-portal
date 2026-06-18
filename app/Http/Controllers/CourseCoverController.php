<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateCourseImageJob;
use App\Models\Course;
use App\Services\CourseImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CourseCoverController extends Controller
{
    // POST /elearning/courses/{course}/cover/generate
    public function generate(Request $request, Course $course)
    {
        $request->validate([
            'style'      => 'nullable|in:modern,corporate,premium',
            'complexity' => 'nullable|in:simple,standard,premium',
            'prompt'     => 'nullable|string|max:3000',
        ]);

        $style      = $request->input('style', 'modern');
        $complexity = $request->input('complexity', 'standard');

        // Use custom prompt if provided, otherwise build from course data
        $prompt = $request->filled('prompt')
            ? $request->input('prompt')
            : CourseImageService::buildPrompt($course, $style, $complexity);

        $cacheKey = "course_cover_gen_{$course->id}";
        Cache::put($cacheKey, ['status' => 'queued'], now()->addMinutes(10));

        GenerateCourseImageJob::dispatch($course->id, $prompt, auth()->id());

        return response()->json(['status' => 'queued', 'message' => 'Image generation started']);
    }

    // GET /elearning/courses/{course}/cover/status
    public function status(Course $course)
    {
        $cacheKey = "course_cover_gen_{$course->id}";
        $state    = Cache::get($cacheKey);

        if (!$state) {
            // No job in flight — return current cover if it exists
            return response()->json([
                'status'    => 'idle',
                'cover_url' => $course->cover_image ? asset('storage/' . $course->cover_image) : null,
                'thumb_url' => $course->cover_thumbnail ? asset('storage/' . $course->cover_thumbnail) : null,
            ]);
        }

        return response()->json($state);
    }

    // POST /elearning/courses/{course}/cover/upload
    public function upload(Request $request, Course $course)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        $tmpPath = $request->file('image')->getPathname();

        CourseImageService::deleteFiles($course);
        [$coverPath, $thumbPath] = CourseImageService::processUpload($tmpPath, $course->id);

        $course->update([
            'cover_image'           => $coverPath,
            'cover_thumbnail'       => $thumbPath,
            'cover_generated_by_ai' => false,
            'cover_prompt'          => null,
        ]);

        return response()->json([
            'success'   => true,
            'cover_url' => asset('storage/' . $coverPath),
            'thumb_url' => asset('storage/' . $thumbPath),
        ]);
    }

    // DELETE /elearning/courses/{course}/cover
    public function delete(Course $course)
    {
        CourseImageService::deleteFiles($course);

        $course->update([
            'cover_image'           => null,
            'cover_thumbnail'       => null,
            'cover_generated_by_ai' => false,
            'cover_prompt'          => null,
        ]);

        return response()->json(['success' => true]);
    }

    // POST /elearning/courses/{course}/cover/preview-prompt  (AJAX)
    public function previewPrompt(Request $request, Course $course)
    {
        $style      = $request->input('style', 'modern');
        $complexity = $request->input('complexity', 'standard');
        $prompt     = CourseImageService::buildPrompt($course, $style, $complexity);

        return response()->json(['prompt' => $prompt]);
    }
}
