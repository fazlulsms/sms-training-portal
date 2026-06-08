<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\ElearningLesson;
use App\Models\LessonBlock;
use Illuminate\Http\Request;

class ElearningLessonController extends Controller
{
    public function index(Course $course)
    {
        $lessons = $course->elearningLessons()
            ->withCount('allBlocks as block_count')
            ->paginate(20);

        return view('elearning.lessons.index', compact('course', 'lessons'));
    }

    public function create(Course $course)
    {
        return view('elearning.lessons.create', compact('course'));
    }

    public function store(Request $request, Course $course)
    {
        $validated = $request->validate([
            'title'                  => 'required|string|max:255',
            'short_description'      => 'nullable|string|max:1000',
            'learning_objectives'    => 'nullable|string',
            'lesson_order'           => 'required|integer|min:1',
            'duration_minutes'       => 'nullable|integer|min:1',
            'lesson_type'            => 'nullable|string|in:' . implode(',', array_keys(ElearningLesson::lessonTypes())),
            'completion_rule'        => 'required|in:manual,pass_quiz',
            'required_passing_score' => 'nullable|integer|min:1|max:100',
            'certificate_eligible'   => 'nullable|boolean',
            'status'                 => 'required|in:active,inactive',
            // Legacy fields — form uses legacy_ prefix to avoid cross-mapping with lesson_type select
            'legacy_video_url'       => 'nullable|string|max:2048',
            'legacy_lesson_notes'    => 'nullable|string',
        ]);

        $lesson = ElearningLesson::create([
            'course_id'              => $course->id,
            'title'                  => $validated['title'],
            'short_description'      => $validated['short_description']      ?? null,
            'learning_objectives'    => $validated['learning_objectives']    ?? null,
            'lesson_order'           => $validated['lesson_order'],
            'duration_minutes'       => $validated['duration_minutes']       ?? null,
            'lesson_type'            => $validated['lesson_type']            ?? 'mixed',
            'completion_rule'        => $validated['completion_rule'],
            'required_passing_score' => $validated['required_passing_score'] ?? null,
            'certificate_eligible'   => $request->boolean('certificate_eligible', true),
            'status'                 => $validated['status'],
            // Map legacy_ form fields → DB column names
            'video_url'              => $validated['legacy_video_url']       ?? null,
            'lesson_content'         => $validated['legacy_lesson_notes']    ?? null,
        ]);

        // If a legacy_video_url was supplied on create, auto-seed a Video block
        if (!empty($validated['legacy_video_url'])) {
            LessonBlock::create([
                'lesson_id'  => $lesson->id,
                'block_type' => 'video',
                'title'      => 'Lesson Video',
                'content'    => $validated['legacy_video_url'],
                'sort_order' => 0,
                'status'     => 'active',
            ]);
        }

        return redirect()
            ->route('elearning.lessons.edit', [$course, $lesson])
            ->with('success', '✅ Lesson created. Now add content blocks below.');
    }

    public function edit(Course $course, ElearningLesson $lesson)
    {
        $blocks    = $lesson->allBlocks()->get();
        $types     = LessonBlock::TYPES;
        $rules     = ElearningLesson::completionRules();
        $lessonTypes = ElearningLesson::lessonTypes();

        $addType   = request('add_type');
        $editBlock = request('edit_block')
            ? $lesson->allBlocks()->find(request('edit_block'))
            : null;

        return view('elearning.lessons.edit', compact(
            'course', 'lesson', 'blocks', 'types', 'rules', 'lessonTypes', 'addType', 'editBlock'
        ));
    }

    public function update(Request $request, Course $course, ElearningLesson $lesson)
    {
        $validated = $request->validate([
            'title'                  => 'required|string|max:255',
            'short_description'      => 'nullable|string|max:1000',
            'learning_objectives'    => 'nullable|string',
            'lesson_order'           => 'required|integer|min:1',
            'duration_minutes'       => 'nullable|integer|min:1',
            'lesson_type'            => 'nullable|string|in:' . implode(',', array_keys(ElearningLesson::lessonTypes())),
            'completion_rule'        => 'required|in:manual,pass_quiz',
            'required_passing_score' => 'nullable|integer|min:1|max:100',
            'certificate_eligible'   => 'nullable|boolean',
            'status'                 => 'required|in:active,inactive',
            'legacy_video_url'       => 'nullable|string|max:2048',
            'legacy_lesson_notes'    => 'nullable|string',
        ]);

        $lesson->update([
            'title'                  => $validated['title'],
            'short_description'      => $validated['short_description']      ?? null,
            'learning_objectives'    => $validated['learning_objectives']    ?? null,
            'lesson_order'           => $validated['lesson_order'],
            'duration_minutes'       => $validated['duration_minutes']       ?? null,
            'lesson_type'            => $validated['lesson_type']            ?? 'mixed',
            'completion_rule'        => $validated['completion_rule'],
            'required_passing_score' => $validated['required_passing_score'] ?? null,
            'certificate_eligible'   => $request->boolean('certificate_eligible', true),
            'status'                 => $validated['status'],
            'video_url'              => $validated['legacy_video_url']       ?? null,
            'lesson_content'         => $validated['legacy_lesson_notes']    ?? null,
        ]);

        return redirect()
            ->route('elearning.lessons.edit', [$course, $lesson])
            ->with('success', '✅ Lesson settings saved.');
    }

    public function destroy(Course $course, ElearningLesson $lesson)
    {
        $lesson->delete();

        return redirect()
            ->route('elearning.lessons.index', $course)
            ->with('success', 'Lesson deleted.');
    }
}
