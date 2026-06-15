<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\ElearningLesson;
use App\Models\LessonAudio;
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
        ]);

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

        $audioRecords = LessonAudio::where('lesson_id', $lesson->id)->get();

        return view('elearning.lessons.edit', compact(
            'course', 'lesson', 'blocks', 'types', 'rules', 'lessonTypes', 'addType', 'editBlock', 'audioRecords'
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
        ]);

        return redirect()
            ->route('elearning.lessons.edit', [$course, $lesson])
            ->with('success', '✅ Lesson settings saved.');
    }

    public function preview(Course $course, ElearningLesson $lesson)
    {
        $lesson->load([
            'course',
            'resources',
            'quizzes.questions',
            'blocks' => fn ($q) => $q->where('status', 'active')->orderBy('sort_order'),
        ]);

        foreach ($lesson->quizzes as $quiz) {
            $quiz->setRelation('attempts', collect());
        }

        $lessons = ElearningLesson::where('course_id', $course->id)
            ->where('status', 'active')
            ->orderBy('lesson_order')
            ->get();

        $currentIndex   = $lessons->search(fn ($l) => $l->id === $lesson->id);
        $previousLesson = ($currentIndex !== false && $currentIndex > 0) ? $lessons[$currentIndex - 1] : null;
        $nextLesson     = ($currentIndex !== false && $currentIndex < $lessons->count() - 1) ? $lessons[$currentIndex + 1] : null;

        return view('participant.lesson-show', [
            'enrollment'     => null,
            'lesson'         => $lesson,
            'lessonProgress' => null,
            'quizzesPassed'  => false,
            'previousLesson' => $previousLesson,
            'nextLesson'     => $nextLesson,
            'lessons'        => $lessons,
            'currentIndex'   => $currentIndex,
            'previewMode'    => true,
            'previewCourse'  => $course,
        ]);
    }

    public function destroy(Course $course, ElearningLesson $lesson)
    {
        $lesson->delete();

        return redirect()
            ->route('elearning.lessons.index', $course)
            ->with('success', 'Lesson deleted.');
    }
}
