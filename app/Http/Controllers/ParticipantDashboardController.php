<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\ElearningEnrollment;
use App\Models\ElearningLesson;
use App\Models\LessonAudio;
use App\Models\LessonAudioProgress;
use App\Models\LessonProgress;
use App\Models\QuizAttemptOverride;
use App\Models\QuizReviewGate;
use App\Services\LessonProgressService;
use Illuminate\Support\Facades\Auth;

class ParticipantDashboardController extends Controller
{
    public function __construct(private LessonProgressService $progressService) {}

    // ── My Courses ────────────────────────────────────────────────────────────

    public function myCourses()
    {
        $user = Auth::user();

        // Auto-link user_id on any existing email-matched enrollments that lack it
        ElearningEnrollment::where('email', $user->email)
            ->whereNull('user_id')
            ->update(['user_id' => $user->id]);

        $manualEnrollments = Enrollment::with(['schedule.course', 'schedule.trainer'])
            ->where('email', $user->email)
            ->latest()
            ->get();

        $elearningEnrollments = ElearningEnrollment::with([
            'course.lessons' => fn ($q) => $q->orderBy('lesson_order'),
            'lessonProgress',
        ])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        // Map enrollment_id => first incomplete lesson (for "Continue" button)
        $nextLessonMap = [];
        foreach ($elearningEnrollments as $enrollment) {
            $lpMap = $enrollment->lessonProgress->keyBy('lesson_id');
            foreach ($enrollment->course->lessons as $lesson) {
                $lp = $lpMap->get($lesson->id);
                if (!$lp || $lp->status !== 'completed') {
                    $nextLessonMap[$enrollment->id] = $lesson;
                    break;
                }
            }
        }

        return view('participant.my-courses', compact(
            'manualEnrollments',
            'elearningEnrollments',
            'nextLessonMap'
        ));
    }

    // ── Manual training course details ────────────────────────────────────────

    public function courseDetails(Enrollment $enrollment)
    {
        abort_unless($enrollment->email === Auth::user()->email, 403);

        $enrollment->load(['schedule.course', 'schedule.trainer']);

        return view('participant.course-details', compact('enrollment'));
    }

    // ── eLearning course overview ─────────────────────────────────────────────

    public function elearningDetails(ElearningEnrollment $enrollment)
    {
        abort_unless($enrollment->user_id === Auth::id(), 403);

        $enrollment->load([
            'course.lessons' => fn ($q) => $q->orderBy('lesson_order'),
            'lessonProgress',
        ]);

        $lessonProgressMap = $enrollment->lessonProgress->keyBy('lesson_id');

        return view('participant.elearning-details', compact('enrollment', 'lessonProgressMap'));
    }

    // ── Lesson player ─────────────────────────────────────────────────────────

    public function showLesson(ElearningEnrollment $enrollment, ElearningLesson $lesson)
    {
        abort_unless($enrollment->user_id === Auth::id(), 403);
        abort_unless($lesson->course_id === $enrollment->course_id, 403);

        // Payment / access gate
        abort_unless(
            $enrollment->isAccessible(),
            403,
            'Course access is locked. Please complete payment to access lessons.'
        );

        // Expiry gate
        abort_if(
            $enrollment->expires_at && $enrollment->expires_at->isPast(),
            403,
            'Your enrollment has expired. Please contact the administrator.'
        );

        // Enforce sequential access: previous lesson must be completed first
        $lessons = ElearningLesson::where('course_id', $enrollment->course_id)
            ->orderBy('lesson_order')
            ->get();

        $currentIndex = $lessons->search(fn ($l) => $l->id === $lesson->id);

        if ($currentIndex > 0) {
            $previousLesson = $lessons[$currentIndex - 1];

            $previousCompleted = LessonProgress::where('enrollment_id', $enrollment->id)
                ->where('lesson_id', $previousLesson->id)
                ->where('status', 'completed')
                ->exists();

            if (!$previousCompleted) {
                return redirect()
                    ->route('participant.elearning-details', $enrollment->id)
                    ->with('error', 'Please complete the previous lesson first.');
            }
        }

        // Mark in_progress (safe — never downgrades a completed lesson)
        $this->progressService->markInProgress(Auth::id(), $enrollment, $lesson);

        // Review gate tracking: if this lesson visit satisfies a pending gate, unlock extra attempts
        $pendingGates = QuizReviewGate::where('enrollment_id', $enrollment->id)
            ->where('status', 'pending')
            ->get();

        foreach ($pendingGates as $gate) {
            if (!in_array($lesson->id, $gate->required_lesson_ids ?? [])) {
                continue;
            }

            $gateCompleted = $gate->recordLessonVisit($lesson->id);

            if ($gateCompleted) {
                // Grant extra attempts by incrementing the override counter
                $override = QuizAttemptOverride::firstOrNew([
                    'enrollment_id' => $enrollment->id,
                    'quiz_id'       => $gate->quiz_id,
                ]);
                $override->extra_attempts  = ($override->extra_attempts ?? 0) + $gate->extra_attempts_granted;
                $override->admin_user_id   = $override->admin_user_id ?? Auth::id(); // system grant
                $override->reason          = $override->reason ?? 'Auto-granted after module review';
                $override->save();
            }
        }

        // Reload enrollment with fresh lesson progress for sidebar display
        $enrollment->load([
            'course',
            'lessonProgress',
        ]);

        $lesson->load(['resources', 'quizzes.questions', 'blocks' => fn ($q) => $q->where('status', 'active')->orderBy('sort_order')]);

        // Load only this enrollment's quiz attempts — avoid pulling all enrollments' data
        foreach ($lesson->quizzes as $quiz) {
            $quiz->setRelation(
                'attempts',
                $quiz->attemptsByEnrollment($enrollment->id)->latest()->get()
            );
        }

        $lessonProgress = LessonProgress::where('enrollment_id', $enrollment->id)
            ->where('lesson_id', $lesson->id)
            ->first();

        $quizzesPassed = $this->progressService->lessonQuizzesPassed($enrollment, $lesson);

        $previousLesson = $currentIndex > 0 ? $lessons[$currentIndex - 1] : null;
        $nextLesson     = $currentIndex < $lessons->count() - 1 ? $lessons[$currentIndex + 1] : null;

        $audioRecords     = LessonAudio::where('lesson_id', $lesson->id)->where('status', 'ready')->get();
        $blockAudioMap    = $audioRecords->where('audio_type', 'ai_coach')->keyBy('block_id');
        $lessonRecapAudio = $audioRecords->firstWhere('audio_type', 'lesson_recap');

        // Audio completion tracking data for the frontend
        $requiresAudioCompletion = !$previewMode && $lesson->needsAudioCompletion();
        $audioProgressMap = $requiresAudioCompletion
            ? LessonAudioProgress::where('enrollment_id', $enrollment->id)
                ->whereIn('audio_id', $audioRecords->pluck('id'))
                ->get()
                ->keyBy('audio_id')
            : collect();

        return view('participant.lesson-show', compact(
            'enrollment',
            'lesson',
            'lessonProgress',
            'quizzesPassed',
            'previousLesson',
            'nextLesson',
            'lessons',
            'currentIndex',
            'blockAudioMap',
            'lessonRecapAudio',
            'requiresAudioCompletion',
            'audioProgressMap',
            'audioRecords'
        ));
    }

    // ── My Certificates ──────────────────────────────────────────────────────

    public function myCertificates()
    {
        $user = Auth::user();

        $elCertificates = ElearningEnrollment::with('course')
            ->where('user_id', $user->id)
            ->whereIn('certificate_status', ['issued', 'eligible'])
            ->latest()
            ->get();

        return view('participant.my-certificates', compact('elCertificates'));
    }

    // ── Mark lesson complete ──────────────────────────────────────────────────

    public function markLessonComplete(ElearningEnrollment $enrollment, ElearningLesson $lesson)
    {
        abort_unless($enrollment->user_id === Auth::id(), 403);
        abort_unless($lesson->course_id === $enrollment->course_id, 403);

        // Payment / access gate
        abort_unless(
            $enrollment->isAccessible(),
            403,
            'Course access is locked. Please complete payment to access lessons.'
        );

        // Expiry gate
        abort_if(
            $enrollment->expires_at && $enrollment->expires_at->isPast(),
            403,
            'Your enrollment has expired. Please contact the administrator.'
        );

        if (!$this->progressService->audioCompletionPassed($enrollment, $lesson)) {
            return back()->with('error', 'Please complete the lesson audio before marking this lesson as completed.');
        }

        if (!$this->progressService->lessonQuizzesPassed($enrollment, $lesson)) {
            return back()->with('error', 'Please pass the quiz before marking this lesson as complete.');
        }

        $this->progressService->markCompleted(Auth::id(), $enrollment, $lesson);

        // Redirect to next lesson instead of staying on the same page
        $lessons      = ElearningLesson::where('course_id', $enrollment->course_id)
            ->orderBy('lesson_order')
            ->get();
        $currentIndex = $lessons->search(fn ($l) => $l->id === $lesson->id);
        $nextLesson   = ($currentIndex !== false && $currentIndex < $lessons->count() - 1)
            ? $lessons[$currentIndex + 1]
            : null;

        if ($nextLesson) {
            return redirect()
                ->route('participant.lesson.show', [$enrollment->id, $nextLesson->id])
                ->with('success', '✓ Lesson completed! Continuing to the next lesson…');
        }

        // Last lesson — go to course overview
        return redirect()
            ->route('participant.elearning-details', $enrollment->id)
            ->with('success', '🎉 Congratulations! You have completed all lessons in this course.');
    }
}
