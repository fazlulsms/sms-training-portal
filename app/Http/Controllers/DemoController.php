<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\ElearningEnrollment;
use App\Models\ElearningLesson;
use App\Models\ElearningQuiz;
use App\Models\LessonProgress;
use App\Models\QuizAttempt;
use App\Models\Setting;
use App\Models\Trainer;
use App\Models\User;
use App\Services\LessonProgressService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DemoController extends Controller
{
    // ── DEMO-LMS-001 identifiers ──────────────────────────
    private const DEMO_EMAILS = [
        'superadmin' => 'demo.superadmin@sms.test',
        'admin'      => 'demo.admin@sms.test',
        'trainer'    => 'demo.trainer@sms.test',
        'paid'       => 'demo.participant.paid@sms.test',
        'unpaid'     => 'demo.participant.unpaid@sms.test',
        'failed'     => 'demo.participant.failed@sms.test',
    ];

    private const DEMO_COURSE_CODE = 'DEMO-LMS-001';

    // ── DEMO-EL-JOURNEY-001 identifiers ───────────────────
    private const DEMO_JOURNEY_COURSE_CODE = 'DEMO-EL-JOURNEY-001';

    private const DEMO_JOURNEY_EMAILS = [
        'demo.paid.notstarted@sms.test',
        'demo.paid.inprogress@sms.test',
        'demo.paid.completed@sms.test',
        'demo.unpaid.completed@sms.test',
        'demo.paid.failedquiz@sms.test',
        'demo.expired@sms.test',
    ];

    // ── Check Page ────────────────────────────────────────

    public function check()
    {
        // ── DEMO-LMS-001 data ──────────────────────────────
        $allDemoEmails = array_merge(array_values(self::DEMO_EMAILS), self::DEMO_JOURNEY_EMAILS);
        $users = User::whereIn('email', $allDemoEmails)->get()->keyBy('email');

        $course   = Course::where('code', self::DEMO_COURSE_CODE)->first();
        $trainer  = Trainer::where('email', self::DEMO_EMAILS['trainer'])->first();
        $lessons  = $course ? ElearningLesson::where('course_id', $course->id)->orderBy('lesson_order')->get() : collect();
        $quiz     = $course && $lessons->count() >= 3
            ? ElearningQuiz::where('lesson_id', $lessons->last()->id)->first()
            : null;

        $enrollments = $course
            ? ElearningEnrollment::with('user')
                ->where('course_id', $course->id)
                ->whereIn('email', [self::DEMO_EMAILS['paid'], self::DEMO_EMAILS['unpaid'], self::DEMO_EMAILS['failed']])
                ->get()
                ->keyBy('email')
            : collect();

        $enrollmentStatus = [];
        foreach ($enrollments as $email => $enr) {
            $completedLessons = LessonProgress::where('enrollment_id', $enr->id)->where('status', 'completed')->count();
            $quizAttempts     = QuizAttempt::where('elearning_enrollment_id', $enr->id)->get();
            $bestQuizScore    = $quiz ? $quizAttempts->where('quiz_id', $quiz->id)->max('score') : null;

            $enrollmentStatus[$email] = [
                'enrollment'        => $enr,
                'completed_lessons' => $completedLessons,
                'total_lessons'     => $lessons->count(),
                'quiz_attempts'     => $quizAttempts->count(),
                'best_quiz_score'   => $bestQuizScore,
                'quiz_passed'       => $quiz && $bestQuizScore >= $quiz->pass_mark,
            ];
        }

        $settings    = Setting::where('group', 'elearning')->get()->keyBy('key');
        $credentials = [
            ['Super Admin',             'demo.superadmin@sms.test',          'password', 'super_admin', '/dashboard'],
            ['Admin',                   'demo.admin@sms.test',               'password', 'admin',       '/dashboard'],
            ['Trainer',                 'demo.trainer@sms.test',             'password', 'trainer',     '/trainer/dashboard'],
            ['Paid Participant',        'demo.participant.paid@sms.test',    'password', 'participant', '/my-courses'],
            ['Unpaid Participant',      'demo.participant.unpaid@sms.test',  'password', 'participant', '/my-courses'],
            ['Failed Quiz Participant', 'demo.participant.failed@sms.test',  'password', 'participant', '/my-courses'],
        ];

        // ── DEMO-EL-JOURNEY-001 data ───────────────────────
        $journeyCourse  = Course::where('code', self::DEMO_JOURNEY_COURSE_CODE)->first();
        $journeyLessons = $journeyCourse
            ? ElearningLesson::where('course_id', $journeyCourse->id)->orderBy('lesson_order')->get()
            : collect();
        $journeyQuiz    = $journeyCourse && $journeyLessons->count() >= 3
            ? ElearningQuiz::where('lesson_id', $journeyLessons->last()->id)->first()
            : null;

        $journeyEnrollments = $journeyCourse
            ? ElearningEnrollment::with('user')
                ->where('course_id', $journeyCourse->id)
                ->whereIn('email', self::DEMO_JOURNEY_EMAILS)
                ->get()
                ->keyBy('email')
            : collect();

        $journeyEnrollmentStatus = [];
        foreach ($journeyEnrollments as $email => $enr) {
            $completedLessons = LessonProgress::where('enrollment_id', $enr->id)->where('status', 'completed')->count();
            $inProgressLessons= LessonProgress::where('enrollment_id', $enr->id)->where('status', 'in_progress')->count();
            $quizAttempts     = QuizAttempt::where('elearning_enrollment_id', $enr->id)->get();
            $bestQuizScore    = $journeyQuiz ? $quizAttempts->where('quiz_id', $journeyQuiz->id)->max('score') : null;

            $journeyEnrollmentStatus[$email] = [
                'enrollment'          => $enr,
                'completed_lessons'   => $completedLessons,
                'in_progress_lessons' => $inProgressLessons,
                'total_lessons'       => $journeyLessons->count(),
                'quiz_attempts'       => $quizAttempts->count(),
                'best_quiz_score'     => $bestQuizScore,
                'quiz_passed'         => $journeyQuiz && $bestQuizScore >= $journeyQuiz->pass_mark,
            ];
        }

        $journeyCredentials = [
            ['Paid — Not Started',  'demo.paid.notstarted@sms.test',  'Tests: empty dashboard, locked lesson 2 & 3'],
            ['Paid — In Progress',  'demo.paid.inprogress@sms.test',  'Tests: continue learning card, partial progress'],
            ['Paid — Completed',    'demo.paid.completed@sms.test',   'Tests: certificate eligible, 100% progress'],
            ['Unpaid — Completed',  'demo.unpaid.completed@sms.test', 'Tests: payment gate blocks certificate even at 100%'],
            ['Paid — Failed Quiz',  'demo.paid.failedquiz@sms.test',  'Tests: quiz fail → lesson 3 not completed'],
            ['Expired Access',      'demo.expired@sms.test',          'Tests: 403 on lesson access after expiry'],
        ];

        return view('demo.check', compact(
            'users', 'course', 'trainer', 'lessons', 'quiz',
            'enrollments', 'enrollmentStatus', 'settings', 'credentials',
            'journeyCourse', 'journeyLessons', 'journeyQuiz',
            'journeyEnrollments', 'journeyEnrollmentStatus', 'journeyCredentials'
        ));
    }

    // ── Action: Reset DEMO-LMS-001 progress ──────────────

    public function resetDemo()
    {
        abort_if(app()->isProduction(), 403, 'Demo actions are disabled in production.');

        $enrollmentIds = $this->getDemoEnrollmentIds();
        if (empty($enrollmentIds)) {
            return back()->with('demo_error', 'No DEMO-LMS-001 enrollments found. Run: php artisan db:seed --class=DemoLmsSeeder');
        }

        LessonProgress::whereIn('enrollment_id', $enrollmentIds)->delete();
        QuizAttempt::whereIn('elearning_enrollment_id', $enrollmentIds)->delete();
        ElearningEnrollment::whereIn('id', $enrollmentIds)->update([
            'progress_percentage' => 0,
            'completion_status'   => 'not_started',
            'certificate_status'  => 'not_issued',
            'completion_date'     => null,
        ]);

        return back()->with('demo_success', '✅ DEMO-LMS-001 progress reset. All lesson progress and quiz attempts cleared.');
    }

    // ── Action: Reset DEMO-EL-JOURNEY-001 to seeder state ─

    public function resetDemoJourney()
    {
        abort_if(app()->isProduction(), 403, 'Demo actions are disabled in production.');

        $enrollmentIds = $this->getDemoJourneyEnrollmentIds();
        if (empty($enrollmentIds)) {
            return back()->with('demo_error', 'No Journey enrollments found. Run: php artisan db:seed --class=DemoElearningJourneySeeder');
        }

        LessonProgress::whereIn('enrollment_id', $enrollmentIds)->delete();
        QuizAttempt::whereIn('elearning_enrollment_id', $enrollmentIds)->delete();
        ElearningEnrollment::whereIn('id', $enrollmentIds)->update([
            'progress_percentage' => 0,
            'completion_status'   => 'not_started',
            'certificate_status'  => 'not_issued',
            'completion_date'     => null,
        ]);

        return back()->with('demo_success', '✅ All 6 Journey participant enrollments reset to zero. Re-run the seeder to restore initial states.');
    }

    // ── Action: Mark all lessons complete for one enrollment ──

    public function markComplete(LessonProgressService $svc, int $enrollmentId)
    {
        abort_if(app()->isProduction(), 403, 'Demo actions are disabled in production.');

        $enrollment = $this->getDemoEnrollment($enrollmentId);
        if (!$enrollment) return back()->with('demo_error', 'Invalid demo enrollment.');

        $lessons = ElearningLesson::where('course_id', $enrollment->course_id)->orderBy('lesson_order')->get();

        foreach ($lessons as $lesson) {
            $svc->markCompleted($enrollment->user_id ?? 1, $enrollment, $lesson);
        }

        return back()->with('demo_success', "✅ All {$lessons->count()} lessons marked complete for {$enrollment->participant_name}.");
    }

    // ── Action: Create a PASSING quiz attempt ─────────────

    public function passQuiz(LessonProgressService $svc, int $enrollmentId)
    {
        abort_if(app()->isProduction(), 403, 'Demo actions are disabled in production.');

        $enrollment = $this->getDemoEnrollment($enrollmentId);
        if (!$enrollment) return back()->with('demo_error', 'Invalid demo enrollment.');

        $quiz = $this->getDemoFinalQuiz($enrollment->course_id);
        if (!$quiz) return back()->with('demo_error', 'Demo final quiz not found.');

        QuizAttempt::create([
            'enrollment_id'           => null,
            'elearning_enrollment_id' => $enrollment->id,
            'quiz_id'                 => $quiz->id,
            'total_questions'         => 5,
            'correct_answers'         => 4,
            'score'                   => 80.00,
        ]);

        $finalLesson = ElearningLesson::where('course_id', $enrollment->course_id)
            ->orderBy('lesson_order', 'desc')
            ->first();

        if ($finalLesson) {
            $svc->markCompleted($enrollment->user_id ?? 1, $enrollment, $finalLesson);
        }

        return back()->with('demo_success', "✅ Passing attempt created (80%, pass_mark: {$quiz->pass_mark}%). Final lesson marked complete.");
    }

    // ── Action: Create a FAILING quiz attempt ─────────────

    public function failQuiz(int $enrollmentId)
    {
        abort_if(app()->isProduction(), 403, 'Demo actions are disabled in production.');

        $enrollment = $this->getDemoEnrollment($enrollmentId);
        if (!$enrollment) return back()->with('demo_error', 'Invalid demo enrollment.');

        $quiz = $this->getDemoFinalQuiz($enrollment->course_id);
        if (!$quiz) return back()->with('demo_error', 'Demo final quiz not found.');

        QuizAttempt::create([
            'enrollment_id'           => null,
            'elearning_enrollment_id' => $enrollment->id,
            'quiz_id'                 => $quiz->id,
            'total_questions'         => 5,
            'correct_answers'         => 2,
            'score'                   => 40.00,
        ]);

        return back()->with('demo_success', "✅ Failing attempt created (40%, pass_mark: {$quiz->pass_mark}%). Lesson 3 remains incomplete.");
    }

    // ── Action: Recalculate progress for one enrollment ───

    public function recalculate(LessonProgressService $svc, int $enrollmentId)
    {
        abort_if(app()->isProduction(), 403, 'Demo actions are disabled in production.');

        $enrollment = $this->getDemoEnrollment($enrollmentId);
        if (!$enrollment) return back()->with('demo_error', 'Invalid demo enrollment.');

        $svc->recalculateProgress($enrollment);
        $enrollment->refresh();

        return back()->with('demo_success', "✅ Recalculated for {$enrollment->participant_name}: progress={$enrollment->progress_percentage}%, completion={$enrollment->completion_status}, certificate={$enrollment->certificate_status}.");
    }

    // ── Helpers ───────────────────────────────────────────

    private function getDemoEnrollmentIds(): array
    {
        $course = Course::where('code', self::DEMO_COURSE_CODE)->first();
        if (!$course) return [];

        return ElearningEnrollment::where('course_id', $course->id)
            ->whereIn('email', [self::DEMO_EMAILS['paid'], self::DEMO_EMAILS['unpaid'], self::DEMO_EMAILS['failed']])
            ->pluck('id')
            ->toArray();
    }

    private function getDemoJourneyEnrollmentIds(): array
    {
        $course = Course::where('code', self::DEMO_JOURNEY_COURSE_CODE)->first();
        if (!$course) return [];

        return ElearningEnrollment::where('course_id', $course->id)
            ->whereIn('email', self::DEMO_JOURNEY_EMAILS)
            ->pluck('id')
            ->toArray();
    }

    private function getDemoEnrollment(int $id): ?ElearningEnrollment
    {
        $ids = array_merge($this->getDemoEnrollmentIds(), $this->getDemoJourneyEnrollmentIds());
        if (!in_array($id, $ids)) return null;

        return ElearningEnrollment::find($id);
    }

    private function getDemoFinalQuiz(int $courseId): ?ElearningQuiz
    {
        $finalLesson = ElearningLesson::where('course_id', $courseId)
            ->orderBy('lesson_order', 'desc')
            ->first();

        return $finalLesson
            ? ElearningQuiz::where('lesson_id', $finalLesson->id)->first()
            : null;
    }
}
