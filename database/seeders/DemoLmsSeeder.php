<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\ElearningEnrollment;
use App\Models\ElearningLesson;
use App\Models\ElearningQuiz;
use App\Models\ElearningQuizQuestion;
use App\Models\LessonProgress;
use App\Models\QuizAttempt;
use App\Models\Setting;
use App\Models\Trainer;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoLmsSeeder extends Seeder
{
    // ── Identifier prefix so demo data is never confused with real data
    const TAG = '[DEMO]';

    public function run(): void
    {
        $this->command->info('🚀 Setting up LMS Demo Environment...');

        $this->seedUsers();
        $this->seedTrainer();
        $this->seedCourse();
        $this->seedSettings();

        $this->command->info('✅ Demo environment ready.');
        $this->printSummary();
    }

    // ────────────────────────────────────────────────────
    // USERS
    // ────────────────────────────────────────────────────

    private function seedUsers(): void
    {
        $users = [
            [
                'name'        => '[DEMO] Super Admin',
                'email'       => 'demo.superadmin@sms.test',
                'role'        => 'super_admin',
                'company'     => 'SMS Demo',
                'designation' => 'Super Administrator',
            ],
            [
                'name'        => '[DEMO] Admin',
                'email'       => 'demo.admin@sms.test',
                'role'        => 'admin',
                'company'     => 'SMS Demo',
                'designation' => 'Administrator',
            ],
            [
                'name'        => '[DEMO] Trainer',
                'email'       => 'demo.trainer@sms.test',
                'role'        => 'trainer',
                'company'     => 'SMS Demo',
                'designation' => 'Lead Trainer',
            ],
            [
                'name'        => '[DEMO] Paid Participant',
                'email'       => 'demo.participant.paid@sms.test',
                'role'        => 'participant',
                'company'     => 'Demo Corp',
                'designation' => 'Quality Manager',
            ],
            [
                'name'        => '[DEMO] Unpaid Participant',
                'email'       => 'demo.participant.unpaid@sms.test',
                'role'        => 'participant',
                'company'     => 'Demo Corp',
                'designation' => 'Auditor',
            ],
            [
                'name'        => '[DEMO] Failed Quiz Participant',
                'email'       => 'demo.participant.failed@sms.test',
                'role'        => 'participant',
                'company'     => 'Demo Corp',
                'designation' => 'HSE Officer',
            ],
        ];

        foreach ($users as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                array_merge($data, [
                    'password'  => Hash::make('password'),
                    'is_active' => true,
                ])
            );
            $this->command->line("  👤 User: {$data['name']} ({$data['email']})");
        }
    }

    // ────────────────────────────────────────────────────
    // TRAINER RECORD
    // ────────────────────────────────────────────────────

    private function seedTrainer(): void
    {
        $trainerUser = User::where('email', 'demo.trainer@sms.test')->first();

        $trainer = Trainer::updateOrCreate(
            ['email' => 'demo.trainer@sms.test'],
            [
                'user_id'      => $trainerUser?->id,
                'name'         => '[DEMO] Trainer',
                'designation'  => 'Lead Trainer',
                'organization' => 'SMS Demo',
                'phone'        => '01700000000',
                'status'       => true,
            ]
        );

        $this->command->line("  🎓 Trainer record: {$trainer->name}");
    }

    // ────────────────────────────────────────────────────
    // COURSE → LESSONS → QUIZ → QUESTIONS → ENROLLMENTS
    // ────────────────────────────────────────────────────

    private function seedCourse(): void
    {
        // ── Course ──────────────────────────────────────
        $course = Course::updateOrCreate(
            ['code' => 'DEMO-LMS-001'],
            [
                'name'        => '[DEMO] E-Learning Course - Full LMS Test',
                'description' => 'This is a demo course created for LMS testing purposes. Do not enroll real participants.',
                'course_type' => 'elearning',
                'status'      => 1,           // published/active
                'course_fee'  => 0,
            ]
        );

        $this->command->line("  📚 Course: {$course->name} (ID: {$course->id})");

        // ── Lessons ─────────────────────────────────────
        $lessons = [
            [
                'lesson_order'   => 1,
                'title'          => '[DEMO] Lesson 1 - Course Introduction',
                'lesson_content' => "Welcome to the LMS Demo Course.\n\nThis lesson introduces the course structure and objectives.\n\nYou can mark this lesson complete using the button below. No quiz is required for this lesson.",
                'status'         => 'active',
                'duration_minutes' => 10,
            ],
            [
                'lesson_order'   => 2,
                'title'          => '[DEMO] Lesson 2 - Main Learning Content',
                'lesson_content' => "This is the main content lesson.\n\nTopics covered:\n• Topic A: Understanding the fundamentals\n• Topic B: Key principles and practices\n• Topic C: Application in real scenarios\n\nReview this content carefully before proceeding to the final quiz.",
                'status'         => 'active',
                'duration_minutes' => 20,
            ],
            [
                'lesson_order'   => 3,
                'title'          => '[DEMO] Lesson 3 - Final Review & Quiz',
                'lesson_content' => "This is the final lesson. A quiz is required to complete this lesson.\n\nPass mark: 70%\nQuestions: 5 MCQ\nYou need at least 4 correct answers to pass.",
                'status'         => 'active',
                'duration_minutes' => 15,
            ],
        ];

        $lessonRecords = [];
        foreach ($lessons as $lessonData) {
            // Match on course_id + lesson_order to avoid duplicates
            $lesson = ElearningLesson::updateOrCreate(
                ['course_id' => $course->id, 'lesson_order' => $lessonData['lesson_order']],
                $lessonData + ['course_id' => $course->id]
            );
            $lessonRecords[] = $lesson;
            $this->command->line("  📄 Lesson {$lessonData['lesson_order']}: {$lesson->title} (ID: {$lesson->id})");
        }

        $finalLesson = $lessonRecords[2]; // Lesson 3

        // ── Quiz on Lesson 3 ─────────────────────────────
        $quiz = ElearningQuiz::updateOrCreate(
            ['lesson_id' => $finalLesson->id],
            [
                'title'       => '[DEMO] Final Quiz - LMS Test',
                'description' => 'Final assessment for the LMS demo course. Pass mark is 70% (4 out of 5 correct).',
                'pass_mark'   => 70,
                'max_attempt' => 5,
                'status'      => 'active',
            ]
        );

        $this->command->line("  📝 Quiz: {$quiz->title} (ID: {$quiz->id}, pass_mark: {$quiz->pass_mark}%)");

        // ── 5 MCQ Questions ──────────────────────────────
        $questions = [
            [
                'question_text'  => '[DEMO] Q1: Which of the following is the correct definition of a management system?',
                'question_type'  => 'mcq',
                'option_a'       => 'A set of interrelated elements to establish policies and objectives',
                'option_b'       => 'A software application for tracking employees',
                'option_c'       => 'A financial reporting framework',
                'option_d'       => 'A legal compliance checklist',
                'correct_answer' => 'A',
                'marks'          => 1,
            ],
            [
                'question_text'  => '[DEMO] Q2: What does "continual improvement" mean in the context of an LMS?',
                'question_type'  => 'mcq',
                'option_a'       => 'Making changes once a year',
                'option_b'       => 'Ongoing effort to improve processes, products or services',
                'option_c'       => 'Replacing the system every 5 years',
                'option_d'       => 'Hiring more staff',
                'correct_answer' => 'B',
                'marks'          => 1,
            ],
            [
                'question_text'  => '[DEMO] Q3: What is the primary purpose of a gap analysis?',
                'question_type'  => 'mcq',
                'option_a'       => 'To assess financial performance',
                'option_b'       => 'To compare current state against desired state',
                'option_c'       => 'To measure employee satisfaction',
                'option_d'       => 'To review marketing strategies',
                'correct_answer' => 'B',
                'marks'          => 1,
            ],
            [
                'question_text'  => '[DEMO] Q4: Which audit type is conducted by the organization on itself?',
                'question_type'  => 'mcq',
                'option_a'       => 'Third-party audit',
                'option_b'       => 'Regulatory audit',
                'option_c'       => 'Internal audit',
                'option_d'       => 'Certification audit',
                'correct_answer' => 'C',
                'marks'          => 1,
            ],
            [
                'question_text'  => '[DEMO] Q5: Which document defines the scope and objectives of a management system?',
                'question_type'  => 'mcq',
                'option_a'       => 'Work instruction',
                'option_b'       => 'Policy statement',
                'option_c'       => 'Audit checklist',
                'option_d'       => 'Corrective action report',
                'correct_answer' => 'B',
                'marks'          => 1,
            ],
        ];

        foreach ($questions as $i => $qData) {
            ElearningQuizQuestion::updateOrCreate(
                ['quiz_id' => $quiz->id, 'question_text' => $qData['question_text']],
                $qData + ['quiz_id' => $quiz->id, 'status' => 'active']
            );
            $this->command->line("  ❓ Q".($i+1).": {$qData['question_text']} [correct: {$qData['correct_answer']}]");
        }

        // ── Enrollments ──────────────────────────────────
        $this->seedEnrollments($course);
    }

    private function seedEnrollments(Course $course): void
    {
        $enrollments = [
            [
                'email'          => 'demo.participant.paid@sms.test',
                'payment_status' => 'paid',
                'access_status'  => 'unlocked',
                'label'          => 'Paid Participant',
            ],
            [
                'email'          => 'demo.participant.unpaid@sms.test',
                'payment_status' => 'pending',
                'access_status'  => 'locked',
                'label'          => 'Unpaid Participant',
            ],
            [
                'email'          => 'demo.participant.failed@sms.test',
                'payment_status' => 'paid',
                'access_status'  => 'unlocked',
                'label'          => 'Failed Quiz Participant',
            ],
        ];

        foreach ($enrollments as $data) {
            $user = User::where('email', $data['email'])->first();

            $enrollment = ElearningEnrollment::updateOrCreate(
                ['course_id' => $course->id, 'email' => $data['email']],
                [
                    'user_id'              => $user?->id,
                    'participant_name'     => $user?->name ?? $data['label'],
                    'phone'               => '01700000001',
                    'company'             => 'Demo Corp',
                    'amount'              => 0,
                    'currency'            => 'BDT',
                    'payment_method'      => 'demo',
                    'payment_status'      => $data['payment_status'],
                    'access_status'       => $data['access_status'],
                    'completion_status'   => 'not_started',
                    'progress_percentage' => 0,
                    'certificate_status'  => 'not_issued',
                    'started_at'          => $data['access_status'] === 'unlocked' ? now() : null,
                ]
            );

            // Clear any existing demo progress/attempts so state is fresh
            LessonProgress::where('enrollment_id', $enrollment->id)->delete();
            QuizAttempt::where('elearning_enrollment_id', $enrollment->id)->delete();

            // Reset enrollment stats to clean state
            $enrollment->update([
                'progress_percentage' => 0,
                'completion_status'   => 'not_started',
                'certificate_status'  => 'not_issued',
                'completion_date'     => null,
            ]);

            $this->command->line("  📋 Enrollment [{$data['label']}]: payment={$data['payment_status']}, access={$data['access_status']} (Enrollment ID: {$enrollment->id})");
        }
    }

    // ────────────────────────────────────────────────────
    // SETTINGS
    // ────────────────────────────────────────────────────

    private function seedSettings(): void
    {
        $settings = [
            // General
            ['elearning.default_pass_mark',           '70',  'elearning', 'Default Pass Mark (%)'],
            ['elearning.progress_calculation',         'required_lessons_only', 'elearning', 'Progress Calculation Method'],
            ['elearning.completion_requires_quiz',     '1',   'elearning', 'Completion Requires Quiz Pass'],
            ['elearning.completion_requires_payment',  '1',   'elearning', 'Completion Requires Payment Cleared'],
            ['elearning.min_attendance_pct',           '80',  'elearning', 'Min Attendance for Certificate (%)'],
            // Certificate
            ['elearning.auto_eligible',                '1',   'elearning', 'Auto-Set Certificate Eligible'],
            ['elearning.admin_approval_required',      '1',   'elearning', 'Admin Approval Required to Issue Certificate'],
            // Participant
            ['elearning.allow_self_registration',      '0',   'elearning', 'Allow Self-Registration'],
            ['elearning.auto_create_account',          '1',   'elearning', 'Auto-Create Participant Account'],
            ['elearning.require_email_verification',   '0',   'elearning', 'Require Email Verification'],
            ['elearning.auto_link_enrollment',         '1',   'elearning', 'Auto-Link Enrollment by Email'],
        ];

        foreach ($settings as [$key, $value, $group, $label]) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'group' => $group, 'label' => $label]
            );
        }

        $this->command->line("  ⚙️  Settings seeded.");
    }

    // ────────────────────────────────────────────────────
    // SUMMARY
    // ────────────────────────────────────────────────────

    private function printSummary(): void
    {
        $this->command->newLine();
        $this->command->info('═══════════════════════════════════════════════════');
        $this->command->info('  DEMO LMS ENVIRONMENT - CREDENTIALS');
        $this->command->info('═══════════════════════════════════════════════════');

        $this->command->table(
            ['Role', 'Name', 'Email', 'Password'],
            [
                ['Super Admin',  '[DEMO] Super Admin',            'demo.superadmin@sms.test',        'password'],
                ['Admin',        '[DEMO] Admin',                  'demo.admin@sms.test',             'password'],
                ['Trainer',      '[DEMO] Trainer',                'demo.trainer@sms.test',           'password'],
                ['Participant',  '[DEMO] Paid Participant',        'demo.participant.paid@sms.test',   'password'],
                ['Participant',  '[DEMO] Unpaid Participant',      'demo.participant.unpaid@sms.test', 'password'],
                ['Participant',  '[DEMO] Failed Quiz Participant', 'demo.participant.failed@sms.test', 'password'],
            ]
        );

        $course = Course::where('code', 'DEMO-LMS-001')->first();
        $this->command->info("  Demo Course ID: " . ($course?->id ?? 'N/A'));
        $this->command->info('═══════════════════════════════════════════════════');
        $this->command->newLine();
        $this->command->info('  Open: http://127.0.0.1:8000/admin/elearning/demo-check');
        $this->command->newLine();
    }
}
