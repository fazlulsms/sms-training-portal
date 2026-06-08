<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\ElearningEnrollment;
use App\Models\ElearningLesson;
use App\Models\ElearningLessonResource;
use App\Models\ElearningQuiz;
use App\Models\ElearningQuizQuestion;
use App\Models\LessonProgress;
use App\Models\QuizAttempt;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoElearningJourneySeeder extends Seeder
{
    private const COURSE_CODE = 'DEMO-EL-JOURNEY-001';

    public function run(): void
    {
        // ── 1. Course ──────────────────────────────────────────────────────
        $course = Course::updateOrCreate(
            ['code' => self::COURSE_CODE],
            [
                'name'        => 'Demo E-Learning Course - Participant Journey Test',
                'status'      => 1,
                'course_type' => 'elearning',
                'description' => 'Demo course for testing the full participant journey: enrollment → lessons → quiz → certificate.',
                'course_fee'  => 1000,
                'access_days' => 365,
            ]
        );

        // ── 2. Lessons ─────────────────────────────────────────────────────
        $lesson1 = ElearningLesson::updateOrCreate(
            ['course_id' => $course->id, 'lesson_order' => 1],
            [
                'title'            => 'Introduction to the Course',
                'lesson_content'   => '<p>Welcome to this demo course! This lesson introduces the course structure, learning objectives, and how to navigate the platform.</p><p>Watch the video above, then download the PDF guide before proceeding to Lesson 2.</p>',
                'video_url'        => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                'duration_minutes' => 10,
                'status'           => 'active',
            ]
        );

        $lesson2 = ElearningLesson::updateOrCreate(
            ['course_id' => $course->id, 'lesson_order' => 2],
            [
                'title'            => 'Main Learning Content',
                'lesson_content'   => '<p>This is the main content lesson. It covers the core concepts that will be tested in the final quiz.</p><p>Study the material carefully. You must complete this lesson before accessing the Final Review.</p>',
                'video_url'        => null,
                'duration_minutes' => 20,
                'status'           => 'active',
            ]
        );

        $lesson3 = ElearningLesson::updateOrCreate(
            ['course_id' => $course->id, 'lesson_order' => 3],
            [
                'title'            => 'Final Review',
                'lesson_content'   => '<p>You have reached the final lesson. Before this lesson can be marked complete, you must pass the <strong>Demo Final Quiz</strong> below.</p><p>The quiz has 5 questions. You need to score at least 70% to pass. You have 3 attempts.</p>',
                'video_url'        => null,
                'duration_minutes' => 5,
                'status'           => 'active',
            ]
        );

        // ── 3. Resources on Lesson 1 ───────────────────────────────────────
        ElearningLessonResource::updateOrCreate(
            ['lesson_id' => $lesson1->id, 'title' => '[DEMO] Course Guide PDF'],
            [
                'resource_type' => 'pdf',
                'file_path'     => 'demo-resources/demo-course-guide.pdf',
                'external_url'  => null,
                'status'        => 'active',
            ]
        );

        ElearningLessonResource::updateOrCreate(
            ['lesson_id' => $lesson1->id, 'title' => '[DEMO] Introduction Slides'],
            [
                'resource_type' => 'link',
                'file_path'     => null,
                'external_url'  => 'https://example.com/demo-slides',
                'status'        => 'active',
            ]
        );

        // ── 4. Quiz on Lesson 3 ────────────────────────────────────────────
        $quiz = ElearningQuiz::updateOrCreate(
            ['lesson_id' => $lesson3->id, 'title' => 'Demo Final Quiz'],
            [
                'description' => 'Tests your understanding of the demo course. Score at least 70% to pass.',
                'pass_mark'   => 70,
                'max_attempt' => 3,
                'status'      => 'active',
            ]
        );

        // ── 5. Quiz Questions ──────────────────────────────────────────────
        $questions = [
            [
                'question_text'  => 'What is the primary purpose of a Learning Management System (LMS)?',
                'option_a'       => 'To manage financial transactions',
                'option_b'       => 'To deliver, track, and manage educational content and training',
                'option_c'       => 'To handle human resources payroll',
                'option_d'       => 'To manage inventory and supply chain',
                'correct_answer' => 'B',
            ],
            [
                'question_text'  => 'What happens when a participant passes the quiz on a lesson?',
                'option_a'       => 'The course is automatically deleted',
                'option_b'       => 'The participant is redirected to payment',
                'option_c'       => 'The lesson is automatically marked as complete',
                'option_d'       => 'The quiz is permanently locked',
                'correct_answer' => 'C',
            ],
            [
                'question_text'  => 'When is a participant eligible for a certificate in this system?',
                'option_a'       => 'After enrolling in the course',
                'option_b'       => 'After payment is cleared and all lessons are completed',
                'option_c'       => 'After completing at least one lesson',
                'option_d'       => 'After taking any quiz attempt',
                'correct_answer' => 'B',
            ],
            [
                'question_text'  => 'What is the correct sequence for accessing lessons in this system?',
                'option_a'       => 'Any lesson can be accessed in any order',
                'option_b'       => 'Only odd-numbered lessons are accessible first',
                'option_c'       => 'Lessons must be completed sequentially — each lesson unlocks the next',
                'option_d'       => 'All lessons are locked until the admin unlocks them manually',
                'correct_answer' => 'C',
            ],
            [
                'question_text'  => 'What happens to course completion if the enrollment payment is NOT cleared?',
                'option_a'       => 'The course is still marked as completed',
                'option_b'       => 'The certificate is automatically issued',
                'option_c'       => 'Progress can reach 100% but completion status is NOT set and no certificate is issued',
                'option_d'       => 'The progress resets to zero',
                'correct_answer' => 'C',
            ],
        ];

        foreach ($questions as $q) {
            ElearningQuizQuestion::updateOrCreate(
                ['quiz_id' => $quiz->id, 'question_text' => $q['question_text']],
                array_merge($q, [
                    'question_type' => 'mcq',
                    'marks'         => 1,
                    'status'        => 'active',
                ])
            );
        }

        // ── 6. Demo Participants ───────────────────────────────────────────
        $participants = [
            ['demo.paid.notstarted@sms.test',  'Demo Paid Not Started',    'not_started'],
            ['demo.paid.inprogress@sms.test',  'Demo Paid In Progress',    'in_progress'],
            ['demo.paid.completed@sms.test',   'Demo Paid Completed',      'completed'],
            ['demo.unpaid.completed@sms.test', 'Demo Unpaid Completed',    'unpaid'],
            ['demo.paid.failedquiz@sms.test',  'Demo Paid Failed Quiz',    'failed_quiz'],
            ['demo.expired@sms.test',          'Demo Expired Participant', 'expired'],
        ];

        foreach ($participants as [$email, $name, $scenario]) {
            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name'      => $name,
                    'password'  => Hash::make('password'),
                    'role'      => 'participant',
                    'is_active' => true,
                ]
            );

            $this->setupEnrollment($user, $course, $quiz, $lesson1, $lesson2, $lesson3, $scenario);
        }
    }

    private function setupEnrollment(
        User $user,
        Course $course,
        ElearningQuiz $quiz,
        ElearningLesson $lesson1,
        ElearningLesson $lesson2,
        ElearningLesson $lesson3,
        string $scenario
    ): void {
        $enrollmentBase = [
            'participant_name' => $user->name,
            'email'            => $user->email,
            'amount'           => 1000,
            'currency'         => 'BDT',
            'started_at'       => now()->subDays(7),
        ];

        $enrollmentScenario = match ($scenario) {
            'not_started' => [
                'payment_status'      => 'paid',
                'access_status'       => 'unlocked',
                'progress_percentage' => 0,
                'completion_status'   => 'not_started',
                'certificate_status'  => 'not_issued',
                'expires_at'          => null,
                'completion_date'     => null,
            ],
            'in_progress' => [
                'payment_status'      => 'paid',
                'access_status'       => 'unlocked',
                'progress_percentage' => 33,
                'completion_status'   => 'in_progress',
                'certificate_status'  => 'not_issued',
                'expires_at'          => null,
                'completion_date'     => null,
            ],
            'completed' => [
                'payment_status'      => 'paid',
                'access_status'       => 'unlocked',
                'progress_percentage' => 100,
                'completion_status'   => 'completed',
                'certificate_status'  => 'eligible',
                'expires_at'          => null,
                'completion_date'     => now()->subDays(1),
            ],
            'unpaid' => [
                'payment_status'      => 'pending',
                'access_status'       => 'locked',
                'progress_percentage' => 100,
                'completion_status'   => 'in_progress',
                'certificate_status'  => 'not_issued',
                'expires_at'          => null,
                'completion_date'     => null,
            ],
            'failed_quiz' => [
                'payment_status'      => 'paid',
                'access_status'       => 'unlocked',
                'progress_percentage' => 67,
                'completion_status'   => 'in_progress',
                'certificate_status'  => 'not_issued',
                'expires_at'          => null,
                'completion_date'     => null,
            ],
            'expired' => [
                'payment_status'      => 'paid',
                'access_status'       => 'unlocked',
                'progress_percentage' => 0,
                'completion_status'   => 'not_started',
                'certificate_status'  => 'not_issued',
                'expires_at'          => Carbon::now()->subDays(30),
                'completion_date'     => null,
            ],
        };

        $enrollment = ElearningEnrollment::updateOrCreate(
            ['user_id' => $user->id, 'course_id' => $course->id],
            array_merge($enrollmentBase, $enrollmentScenario)
        );

        // Always wipe existing progress/attempts so re-running the seeder resets state
        LessonProgress::where('enrollment_id', $enrollment->id)->delete();
        QuizAttempt::where('elearning_enrollment_id', $enrollment->id)->delete();

        // ── Lesson Progress ────────────────────────────────────────────────
        if ($scenario === 'in_progress') {
            $this->mkProgress($user, $enrollment, $lesson1, 'completed', $course, 6, 5);
            $this->mkProgress($user, $enrollment, $lesson2, 'in_progress', $course, 2, null);
        } elseif (in_array($scenario, ['completed', 'unpaid'])) {
            $this->mkProgress($user, $enrollment, $lesson1, 'completed', $course, 6, 5);
            $this->mkProgress($user, $enrollment, $lesson2, 'completed', $course, 4, 3);
            $this->mkProgress($user, $enrollment, $lesson3, 'completed', $course, 2, 1);
        } elseif ($scenario === 'failed_quiz') {
            $this->mkProgress($user, $enrollment, $lesson1, 'completed', $course, 6, 5);
            $this->mkProgress($user, $enrollment, $lesson2, 'completed', $course, 4, 3);
            $this->mkProgress($user, $enrollment, $lesson3, 'in_progress', $course, 2, null);
        }
        // not_started, expired: no LessonProgress records

        // ── Quiz Attempts ──────────────────────────────────────────────────
        if (in_array($scenario, ['completed', 'unpaid'])) {
            QuizAttempt::create([
                'enrollment_id'           => null,
                'elearning_enrollment_id' => $enrollment->id,
                'quiz_id'                 => $quiz->id,
                'total_questions'         => 5,
                'correct_answers'         => 4,
                'score'                   => 80.00,
            ]);
        } elseif ($scenario === 'failed_quiz') {
            QuizAttempt::create([
                'enrollment_id'           => null,
                'elearning_enrollment_id' => $enrollment->id,
                'quiz_id'                 => $quiz->id,
                'total_questions'         => 5,
                'correct_answers'         => 2,
                'score'                   => 40.00,
            ]);
        }
    }

    private function mkProgress(
        User $user,
        ElearningEnrollment $enrollment,
        ElearningLesson $lesson,
        string $status,
        Course $course,
        int $startedDaysAgo,
        ?int $completedDaysAgo
    ): void {
        LessonProgress::create([
            'user_id'       => $user->id,
            'enrollment_id' => $enrollment->id,
            'course_id'     => $course->id,
            'lesson_id'     => $lesson->id,
            'status'        => $status,
            'started_at'    => now()->subDays($startedDaysAgo),
            'completed_at'  => $completedDaysAgo !== null ? now()->subDays($completedDaysAgo) : null,
        ]);
    }
}
