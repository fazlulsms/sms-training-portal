<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\TrainerController;
use App\Http\Controllers\TrainingScheduleController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReportsController;

use App\Http\Controllers\ElearningCourseController;
use App\Http\Controllers\ElearningLessonController;
use App\Http\Controllers\LessonBlockController;
use App\Http\Controllers\ElearningEnrollmentController;
use App\Http\Controllers\ElearningLessonResourceController;
use App\Http\Controllers\ElearningQuizController;
use App\Http\Controllers\ElearningQuizQuestionController;
use App\Http\Controllers\ElearningQuizAttemptController;

use App\Http\Controllers\ParticipantDashboardController;
use App\Http\Controllers\NotificationSettingsController;
use App\Http\Controllers\ParticipantQuizController;
use App\Http\Controllers\ElearningCertificateController;
use App\Http\Controllers\TrainerPortalController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\DemoController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\PublicEnrollmentController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\TestimonialController;
use App\Http\Controllers\QuestionSetController;
use App\Http\Controllers\TrainingExamController;
use App\Http\Controllers\ParticipantExamController;
use App\Http\Controllers\CourseCategoryController;
use App\Http\Controllers\CorporateInquiryController;
use App\Http\Controllers\AiController;
use App\Http\Controllers\AiPromptTemplateController;
use App\Http\Controllers\AiCourseGeneratorController;
use App\Http\Controllers\AiTrainerProfileController;

/*
|--------------------------------------------------------------------------
| Public Website Routes (no auth required)
|--------------------------------------------------------------------------
*/
Route::name('public.')->group(function () {
    Route::get('/',                             [PublicController::class, 'home'])              ->name('home');
    Route::get('/courses',                      [PublicController::class, 'courses'])           ->name('courses');
    Route::get('/courses/{slug}',               [PublicController::class, 'courseDetail'])      ->name('course.detail');
    Route::get('/training-calendar',            [PublicController::class, 'calendar'])          ->name('calendar');
    Route::get('/blog',                         [PublicController::class, 'blog'])              ->name('blog');
    Route::get('/blog/{slug}',                  [PublicController::class, 'blogDetail'])        ->name('blog.detail');
    Route::get('/reviews',                      [PublicController::class, 'testimonials'])      ->name('testimonials');
    Route::post('/reviews/submit',              [PublicController::class, 'testimonialSubmit']) ->name('testimonials.submit');
    Route::get('/verify',                       [PublicController::class, 'verifyCertificate']) ->name('verify-certificate');
    // Trainer directory
    Route::get('/trainers',                     [PublicController::class, 'trainers'])          ->name('trainers');
    Route::get('/trainers/{id}',                [PublicController::class, 'trainerProfile'])    ->name('trainer.profile');
    // Corporate training
    Route::get('/corporate-training',           [CorporateInquiryController::class, 'publicPage'])  ->name('corporate');
    Route::post('/corporate-training',          [CorporateInquiryController::class, 'publicStore']) ->name('corporate.submit');

    // Public enrollment flow
    Route::get('/enroll/{scheduleId}',          [PublicEnrollmentController::class, 'show'])    ->name('enroll');
    Route::post('/enroll/{scheduleId}',         [PublicEnrollmentController::class, 'store'])   ->name('enroll.store');
    Route::get('/enroll/{enrollmentId}/success',[PublicEnrollmentController::class, 'success']) ->name('enroll.success');
    Route::get('/enroll/{enrollmentId}/payment',[PublicEnrollmentController::class, 'payment']) ->name('enroll.payment');
});

Route::get('/dashboard-redirect', function () {
    if (auth()->check()) {
        $user = auth()->user();
        return match($user->role) {
            'trainer'     => redirect()->route('trainer.dashboard'),
            'participant' => redirect()->route('participant.my-courses'),
            default       => redirect()->route('dashboard'),
        };
    }
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'admin'])
    ->name('dashboard');

// Stop impersonating — auth only (the "current user" is the learner during impersonation)
Route::post('/stop-impersonating', [\App\Http\Controllers\ElearningEnrollmentController::class, 'stopImpersonating'])
    ->middleware('auth')
    ->name('impersonation.stop');

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    /*
    |--------------------------------------------------------------------------
    | Participant My Courses
    |--------------------------------------------------------------------------
    */
    Route::get('/my-courses', [ParticipantDashboardController::class, 'myCourses'])
        ->name('participant.my-courses');

    // Certificate download — accessible to both participants (own cert) and admins
    Route::get('/elearning/enrollments/{enrollment}/certificate', [ElearningCertificateController::class, 'generate'])
        ->name('elearning.certificate.generate');

    Route::get('/my-courses/{enrollment}', [ParticipantDashboardController::class, 'courseDetails'])
        ->name('participant.course-details');

    Route::get('/my-elearning/{enrollment}', [ParticipantDashboardController::class, 'elearningDetails'])
        ->name('participant.elearning-details');

    Route::get('/my-elearning/{enrollment}/lessons/{lesson}', [ParticipantDashboardController::class, 'showLesson'])
        ->name('participant.lesson.show');

    Route::post('/my-elearning/{enrollment}/lessons/{lesson}/complete', [ParticipantDashboardController::class, 'markLessonComplete'])
        ->name('participant.lesson.complete');

    Route::get('/my-elearning/{enrollment}/quizzes/{quiz}', [ParticipantQuizController::class, 'start'])
        ->name('participant.quiz.start');

    Route::post('/my-elearning/{enrollment}/quizzes/{quiz}/submit', [ParticipantQuizController::class, 'submit'])
        ->name('participant.quiz.submit');

    Route::get('/my-certificates', [ParticipantDashboardController::class, 'myCertificates'])
        ->name('participant.my-certificates');
});


/*
|--------------------------------------------------------------------------
| Trainer Portal Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'trainer'])->prefix('trainer')->name('trainer.')->group(function () {

    Route::get('/dashboard', [TrainerPortalController::class, 'dashboard'])
        ->name('dashboard');

    Route::get('/schedules', [TrainerPortalController::class, 'schedules'])
        ->name('schedules');

    Route::get('/schedules/{schedule}', [TrainerPortalController::class, 'participants'])
        ->name('schedule.participants');

    Route::post('/enrollments/{enrollment}/attendance', [TrainerPortalController::class, 'updateAttendance'])
        ->name('attendance.update');

    Route::post('/enrollments/{enrollment}/completion', [TrainerPortalController::class, 'updateCompletion'])
        ->name('completion.update');

    Route::get('/schedules/{schedule}/attendance', [TrainerPortalController::class, 'attendanceSheet'])
        ->name('schedule.attendance');
    Route::post('/schedules/{schedule}/attendance/save', [TrainerPortalController::class, 'attendanceSave'])
        ->name('schedule.attendance.save');
});

require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| Public Routes (no auth required)
|--------------------------------------------------------------------------
*/
// Manual training public registration
Route::get('/register-training/{schedule_id}', [EnrollmentController::class, 'publicCreate']);
Route::post('/register-training/{schedule_id}', [EnrollmentController::class, 'publicStore']);

// eLearning public registration
Route::get('/elearning-register/{course}', [ElearningEnrollmentController::class, 'publicRegister'])
    ->name('elearning.public.register');
Route::post('/elearning-register/{course}', [ElearningEnrollmentController::class, 'publicRegisterStore'])
    ->name('elearning.public.register.store');
// ── Participant Exam (public — secure token-based, no auth) ──────────────
Route::get('/exam/{token}',        [ParticipantExamController::class, 'show'])   ->name('exam.show');
Route::post('/exam/{token}/submit',[ParticipantExamController::class, 'submit']) ->name('exam.submit');
Route::get('/exam/{token}/result', [ParticipantExamController::class, 'result']) ->name('exam.result');

// Manual training certificate verification (public)
Route::get('/verify-certificate', [EnrollmentController::class, 'verifyForm']);
Route::post('/verify-certificate', [EnrollmentController::class, 'verifyCertificate']);
Route::get('/verify-certificate/{certificate_number}', [EnrollmentController::class, 'verifyByNumber']);

// eLearning certificate verification (public — QR codes on PDF certificates point here)
Route::get('/elearning/verify-certificate', function (\Illuminate\Http\Request $request) {
    $enrollment = \App\Models\ElearningEnrollment::where('certificate_number', $request->query('cert'))
        ->firstOrFail();
    return view('elearning.certificates.verify', compact('enrollment'));
})->name('elearning.certificate.verify.public');

/*
|--------------------------------------------------------------------------
| Admin Routes (auth + admin middleware)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {

    // Courses
    Route::get('/courses', [CourseController::class, 'index']);
    Route::get('/courses/export', [CourseController::class, 'exportCsv'])->name('admin.courses.export');
    Route::get('/courses/create', [CourseController::class, 'create'])->name('admin.courses.create');
    Route::post('/courses/store', [CourseController::class, 'store']);
    Route::get('/courses/edit/{id}', [CourseController::class, 'edit']);
    Route::post('/courses/update/{id}', [CourseController::class, 'update']);
    Route::get('/courses/delete/{id}', [CourseController::class, 'delete']);

    // Course Categories
    Route::get('/course-categories', [CourseCategoryController::class, 'index']);
    Route::get('/course-categories/create', [CourseCategoryController::class, 'create']);
    Route::post('/course-categories/store', [CourseCategoryController::class, 'store']);
    Route::get('/course-categories/edit/{id}', [CourseCategoryController::class, 'edit']);
    Route::post('/course-categories/update/{id}', [CourseCategoryController::class, 'update']);
    Route::get('/course-categories/delete/{id}', [CourseCategoryController::class, 'destroy']);

    // Corporate Inquiries (admin view)
    Route::get('/corporate-inquiries', [CorporateInquiryController::class, 'index']);
    Route::get('/corporate-inquiries/{id}', [CorporateInquiryController::class, 'show']);
    Route::post('/corporate-inquiries/{id}/update', [CorporateInquiryController::class, 'update']);

    // Trainers
    Route::get('/trainers', [TrainerController::class, 'index']);
    Route::get('/trainers/create', [TrainerController::class, 'create']);
    Route::post('/trainers/store', [TrainerController::class, 'store']);
    Route::get('/trainers/edit/{id}', [TrainerController::class, 'edit']);
    Route::post('/trainers/update/{id}', [TrainerController::class, 'update']);
    Route::get('/trainers/delete/{id}', [TrainerController::class, 'delete']);

    // Training Schedules
    Route::get('/training-schedules', [TrainingScheduleController::class, 'index']);
    Route::get('/training-schedules/export', [TrainingScheduleController::class, 'exportCsv'])->name('admin.training-schedules.export');
    Route::get('/training-schedules/create', [TrainingScheduleController::class, 'create']);
    Route::post('/training-schedules/store', [TrainingScheduleController::class, 'store']);
    Route::get('/training-schedules/edit/{id}', [TrainingScheduleController::class, 'edit']);
    Route::post('/training-schedules/update/{id}', [TrainingScheduleController::class, 'update']);
    Route::get('/training-schedules/delete/{id}', [TrainingScheduleController::class, 'delete']);

    // Enrollments
    Route::get('/enrollments', [EnrollmentController::class, 'index']);
    Route::get('/enrollments/export', [EnrollmentController::class, 'exportCsv'])->name('admin.enrollments.export');
    Route::get('/enrollments/create', [EnrollmentController::class, 'create']);
    Route::post('/enrollments/store', [EnrollmentController::class, 'store']);
    Route::get('/enrollments/edit/{id}', [EnrollmentController::class, 'edit']);
    Route::post('/enrollments/update/{id}', [EnrollmentController::class, 'update']);
    Route::get('/enrollments/delete/{id}', [EnrollmentController::class, 'delete']);

    // Certificates (admin management)
    Route::get('/enrollments/generate-certificate/{id}', [EnrollmentController::class, 'generateCertificate']);
    Route::get('/enrollments/certificate/{id}', [EnrollmentController::class, 'certificate']);
    Route::get('/enrollments/certificate-pdf/{id}', [EnrollmentController::class, 'certificatePdf']);
    Route::get('/certificates', [CertificateController::class, 'index'])->name('certificates.index');
    Route::post('/certificates/filter', [CertificateController::class, 'filter']);
    Route::get('/certificates/generate/{id}', [CertificateController::class, 'generateForm']);
    Route::post('/certificates/generate/{id}', [CertificateController::class, 'generate']);
    Route::get('/certificates/schedule/{id}', [CertificateController::class, 'showBySchedule']);
    Route::post('/certificates/bulk-generate', [CertificateController::class, 'bulkGenerate'])->name('certificates.bulk');
    Route::post('/certificates/email/{id}', [CertificateController::class, 'emailCertificate'])->name('certificates.email');
    Route::get('/certificates/delete/{id}', [CertificateController::class, 'delete']);
    Route::get('/certificates/view/{id}', [CertificateController::class, 'view']);
    Route::get('/certificates/preview/{id}', [CertificateController::class, 'preview'])->name('certificates.preview');
    Route::get('/certificates/pdf/{id}', [CertificateController::class, 'pdf']);

    // Invoices
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
    Route::post('/invoices/store', [InvoiceController::class, 'store'])->name('invoices.store');
    Route::get('/invoices/view/{id}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('/invoices/pdf/{id}', [InvoiceController::class, 'pdf'])->name('invoices.pdf');
    Route::get('/invoices/edit/{id}', [InvoiceController::class, 'edit'])->name('invoices.edit');
    Route::post('/invoices/update/{id}', [InvoiceController::class, 'update'])->name('invoices.update');
    Route::get('/invoices/email/{id}', [InvoiceController::class, 'email'])->name('invoices.email');
    Route::get('/invoices/delete/{id}', [InvoiceController::class, 'delete'])->name('invoices.delete');
    Route::get('/invoices/{id}/send-email', [InvoiceController::class, 'sendEmail'])->name('invoices.sendEmail');
    Route::get('/invoices/enrollment/{id}/details', [InvoiceController::class, 'getEnrollmentDetails'])->name('invoices.enrollment.details');
    Route::get('/invoices/enrollment/{id}', [InvoiceController::class, 'getEnrollmentDetails']);
    // Dedicated payment update
    Route::get('/invoices/payment/{id}',                       [InvoiceController::class, 'paymentForm'])           ->name('invoices.payment.form');
    Route::post('/invoices/payment/{id}',                      [InvoiceController::class, 'paymentUpdate'])         ->name('invoices.payment.update');
    Route::get('/invoices/payment/for-enrollment/{id}',        [InvoiceController::class, 'paymentByEnrollment'])   ->name('invoices.payment.byEnrollment');
    Route::get('/invoices/payment/for-elearning/{id}',         [InvoiceController::class, 'paymentByElearning'])    ->name('invoices.payment.byElearning');

    // Legacy reports
    Route::get('/reports/training',      [ReportController::class, 'training']);
    Route::get('/reports/participants',  [ReportController::class, 'participants']);
    Route::get('/reports/certificates',  [ReportController::class, 'certificates']);
    Route::get('/reports/payments',      [ReportController::class, 'payments']);

    // ── Reports & Analytics Module ─────────────────────────────────────────────
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/',            [ReportsController::class, 'index'])       ->name('index');
        Route::get('/elearning',   [ReportsController::class, 'elearning'])   ->name('elearning');
        Route::get('/ilt',         [ReportsController::class, 'ilt'])         ->name('ilt');
        Route::get('/financial',   [ReportsController::class, 'financial'])   ->name('financial');
        Route::get('/geographic',  [ReportsController::class, 'geographic'])  ->name('geographic');
        Route::get('/export-center',[ReportsController::class,'exportCenter'])->name('export-center');

        // eLearning exports
        Route::get('/elearning/export/pdf',   [ReportsController::class, 'exportElearningPdf'])  ->name('elearning.pdf');
        Route::get('/elearning/export/csv',   [ReportsController::class, 'exportElearningCsv'])  ->name('elearning.csv');
        Route::get('/elearning/export/excel', [ReportsController::class, 'exportElearningExcel'])->name('elearning.excel');

        // ILT exports
        Route::get('/ilt/export/pdf',   [ReportsController::class, 'exportIltPdf'])  ->name('ilt.pdf');
        Route::get('/ilt/export/csv',   [ReportsController::class, 'exportIltCsv'])  ->name('ilt.csv');
        Route::get('/ilt/export/excel', [ReportsController::class, 'exportIltExcel'])->name('ilt.excel');

        // Financial exports
        Route::get('/financial/export/pdf',   [ReportsController::class, 'exportFinancialPdf'])  ->name('financial.pdf');
        Route::get('/financial/export/csv',   [ReportsController::class, 'exportFinancialCsv'])  ->name('financial.csv');
        Route::get('/financial/export/excel', [ReportsController::class, 'exportFinancialExcel'])->name('financial.excel');

        // Geographic exports
        Route::get('/geographic/export/pdf',  [ReportsController::class, 'exportGeographicPdf']) ->name('geographic.pdf');
        Route::get('/geographic/export/csv',  [ReportsController::class, 'exportGeographicCsv']) ->name('geographic.csv');
    });

    // ── Question Sets ─────────────────────────────────────────────────────
    Route::get('/question-sets', [QuestionSetController::class, 'index'])->name('question-sets.index');
    Route::get('/question-sets/create', [QuestionSetController::class, 'create'])->name('question-sets.create');
    Route::post('/question-sets/store', [QuestionSetController::class, 'store'])->name('question-sets.store');
    Route::get('/question-sets/edit/{id}', [QuestionSetController::class, 'edit'])->name('question-sets.edit');
    Route::post('/question-sets/update/{id}', [QuestionSetController::class, 'update'])->name('question-sets.update');
    Route::get('/question-sets/delete/{id}', [QuestionSetController::class, 'delete'])->name('question-sets.delete');
    Route::get('/question-sets/{id}/questions', [QuestionSetController::class, 'questions'])->name('question-sets.questions');
    Route::post('/question-sets/{id}/questions/store', [QuestionSetController::class, 'storeQuestion'])->name('question-sets.questions.store');
    Route::get('/question-sets/{setId}/questions/edit/{qId}', [QuestionSetController::class, 'editQuestion'])->name('question-sets.questions.edit');
    Route::post('/question-sets/{setId}/questions/update/{qId}', [QuestionSetController::class, 'updateQuestion'])->name('question-sets.questions.update');
    Route::get('/question-sets/{setId}/questions/delete/{qId}', [QuestionSetController::class, 'deleteQuestion'])->name('question-sets.questions.delete');

    // ── Training Exams ────────────────────────────────────────────────────
    Route::get('/training-exams',                        [TrainingExamController::class, 'index'])->name('training-exams.index');
    Route::post('/training-schedules/{id}/assign-exam',  [TrainingExamController::class, 'assignExam'])->name('training-exams.assign');
    Route::get('/training-exams/{scheduleId}/results',   [TrainingExamController::class, 'scheduleResults'])->name('training-exams.results');
    Route::get('/training-exams/answers/{attemptId}', [TrainingExamController::class, 'viewAnswers'])->name('training-exams.answers');
    Route::post('/training-exams/grade/{attemptId}', [TrainingExamController::class, 'grade'])->name('training-exams.grade');
    Route::post('/training-exams/reset-attempt/{enrollmentId}', [TrainingExamController::class, 'resetAttempt'])->name('training-exams.reset');
    Route::post('/training-exams/send-reminder/{enrollmentId}', [TrainingExamController::class, 'sendReminder'])->name('training-exams.reminder');

    // Attendance (admin)
    Route::get('/attendance/{schedule}', [AttendanceController::class, 'sheet'])->name('attendance.sheet');
    Route::post('/attendance/{schedule}/save', [AttendanceController::class, 'save'])->name('attendance.save');

    // User Management
    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserManagementController::class, 'create'])->name('users.create');
    Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
    Route::post('/users/{user}/reset-password', [UserManagementController::class, 'resetPassword'])->name('users.reset-password');
    Route::post('/users/{user}/toggle-active', [UserManagementController::class, 'toggleActive'])->name('users.toggle-active');

    // Settings
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');

    // Email Notification Settings
    Route::get('/settings/notifications',             [NotificationSettingsController::class, 'index'])     ->name('notifications.index');
    Route::post('/settings/notifications/{setting}/toggle', [NotificationSettingsController::class, 'toggle'])    ->name('notifications.toggle');
    Route::post('/settings/notifications/toggle-all', [NotificationSettingsController::class, 'toggleAll']) ->name('notifications.toggle-all');

    // Demo / Test Environment (admin only)
    Route::prefix('admin/elearning')->name('demo.')->group(function () {
        Route::get('/demo-check',                          [DemoController::class, 'check'])          ->name('check');
        Route::post('/demo/reset',                         [DemoController::class, 'resetDemo'])      ->name('reset');
        Route::post('/demo/mark-complete/{enrollmentId}',  [DemoController::class, 'markComplete'])   ->name('mark-complete');
        Route::post('/demo/pass-quiz/{enrollmentId}',      [DemoController::class, 'passQuiz'])       ->name('pass-quiz');
        Route::post('/demo/fail-quiz/{enrollmentId}',      [DemoController::class, 'failQuiz'])       ->name('fail-quiz');
        Route::post('/demo/recalculate/{enrollmentId}',    [DemoController::class, 'recalculate'])    ->name('recalculate');
        Route::post('/demo/reset-journey',                 [DemoController::class, 'resetDemoJourney'])->name('reset-journey');
    });

    // ── AI Administration (super_admin only) ──────────────────
    Route::get('/ai/settings', [AiController::class, 'settings'])->name('ai.settings');
    Route::get('/ai/test',     [AiController::class, 'test'])    ->name('ai.test');
    Route::post('/ai/test',    [AiController::class, 'runTest']) ->name('ai.test.run');

    // ── AI Prompt Templates ────────────────────────────────────
    Route::get( '/ai/prompt-templates',                                    [AiPromptTemplateController::class, 'index'])    ->name('ai.prompt-templates.index');
    Route::get( '/ai/prompt-templates/create',                             [AiPromptTemplateController::class, 'create'])   ->name('ai.prompt-templates.create');
    Route::post('/ai/prompt-templates',                                    [AiPromptTemplateController::class, 'store'])    ->name('ai.prompt-templates.store');
    Route::get( '/ai/prompt-templates/{promptTemplate}',                   [AiPromptTemplateController::class, 'show'])     ->name('ai.prompt-templates.show');
    Route::get( '/ai/prompt-templates/{promptTemplate}/edit',              [AiPromptTemplateController::class, 'edit'])     ->name('ai.prompt-templates.edit');
    Route::put( '/ai/prompt-templates/{promptTemplate}',                   [AiPromptTemplateController::class, 'update'])   ->name('ai.prompt-templates.update');
    Route::delete('/ai/prompt-templates/{promptTemplate}',                 [AiPromptTemplateController::class, 'destroy'])  ->name('ai.prompt-templates.destroy');
    Route::post('/ai/prompt-templates/{promptTemplate}/clone',             [AiPromptTemplateController::class, 'clone'])    ->name('ai.prompt-templates.clone');
    Route::post('/ai/prompt-templates/{promptTemplate}/toggle',            [AiPromptTemplateController::class, 'toggle'])   ->name('ai.prompt-templates.toggle');
    Route::get( '/ai/prompt-templates/{promptTemplate}/versions',          [AiPromptTemplateController::class, 'versions']) ->name('ai.prompt-templates.versions');
    Route::post('/ai/prompt-templates/{promptTemplate}/rollback/{version}',[AiPromptTemplateController::class, 'rollback']) ->name('ai.prompt-templates.rollback');
    Route::post('/ai/prompt-templates/{promptTemplate}/test',              [AiPromptTemplateController::class, 'test'])     ->name('ai.prompt-templates.test');

    // ── AI Course Generator (super_admin only) ─────────────────
    Route::post('/ai/course-generator/generate', [AiCourseGeneratorController::class, 'generate'])->name('ai.course-generator.generate');
    Route::get( '/ai/course-generator/preview',  [AiCourseGeneratorController::class, 'preview']) ->name('ai.course-generator.preview');
    Route::post('/ai/course-generator/save',     [AiCourseGeneratorController::class, 'save'])    ->name('ai.course-generator.save');
    Route::post('/ai/course-generator/cancel',   [AiCourseGeneratorController::class, 'cancel'])  ->name('ai.course-generator.cancel');

    // ── AI Trainer Profile Generator (super_admin only) ────────
    Route::get( '/ai/trainer-profile',           [AiTrainerProfileController::class, 'index'])    ->name('ai.trainer-profile.index');
    Route::post('/ai/trainer-profile/generate',  [AiTrainerProfileController::class, 'generate']) ->name('ai.trainer-profile.generate');
    Route::get( '/ai/trainer-profile/preview',   [AiTrainerProfileController::class, 'preview'])  ->name('ai.trainer-profile.preview');
    Route::post('/ai/trainer-profile/save',      [AiTrainerProfileController::class, 'save'])     ->name('ai.trainer-profile.save');
    Route::post('/ai/trainer-profile/cancel',    [AiTrainerProfileController::class, 'cancel'])   ->name('ai.trainer-profile.cancel');

}); // end admin middleware group

/*
|--------------------------------------------------------------------------
| Elearning Admin Routes (auth + admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('elearning')->name('elearning.')->group(function () {

    Route::resource('courses', ElearningCourseController::class);

    Route::get('courses/{course}/lessons', [ElearningLessonController::class, 'index'])->name('lessons.index');
    Route::get('courses/{course}/lessons/create', [ElearningLessonController::class, 'create'])->name('lessons.create');
    Route::post('courses/{course}/lessons', [ElearningLessonController::class, 'store'])->name('lessons.store');
    Route::get('courses/{course}/lessons/{lesson}/edit', [ElearningLessonController::class, 'edit'])->name('lessons.edit');
    Route::get('courses/{course}/lessons/{lesson}/preview', [ElearningLessonController::class, 'preview'])->name('lessons.preview');
    Route::put('courses/{course}/lessons/{lesson}', [ElearningLessonController::class, 'update'])->name('lessons.update');
    Route::delete('courses/{course}/lessons/{lesson}', [ElearningLessonController::class, 'destroy'])->name('lessons.destroy');

    // Lesson Block Builder routes
    Route::post('courses/{course}/lessons/{lesson}/blocks',                     [LessonBlockController::class, 'store'])   ->name('blocks.store');
    Route::put('courses/{course}/lessons/{lesson}/blocks/{block}',              [LessonBlockController::class, 'update'])  ->name('blocks.update');
    Route::delete('courses/{course}/lessons/{lesson}/blocks/{block}',           [LessonBlockController::class, 'destroy']) ->name('blocks.destroy');
    Route::post('courses/{course}/lessons/{lesson}/blocks/{block}/move-up',     [LessonBlockController::class, 'moveUp'])  ->name('blocks.move-up');
    Route::post('courses/{course}/lessons/{lesson}/blocks/{block}/move-down',   [LessonBlockController::class, 'moveDown'])->name('blocks.move-down');

    Route::get('enrollments', [ElearningEnrollmentController::class, 'index'])->name('enrollments.index');
    Route::get('enrollments/create', [ElearningEnrollmentController::class, 'create'])->name('enrollments.create');
    Route::post('enrollments', [ElearningEnrollmentController::class, 'store'])->name('enrollments.store');
    Route::get('enrollments/{enrollment}', [ElearningEnrollmentController::class, 'show'])->name('enrollments.show');
    Route::get('enrollments/{enrollment}/edit', [ElearningEnrollmentController::class, 'edit'])->name('enrollments.edit');
    Route::put('enrollments/{enrollment}', [ElearningEnrollmentController::class, 'update'])->name('enrollments.update');
    Route::delete('enrollments/{enrollment}', [ElearningEnrollmentController::class, 'destroy'])->name('enrollments.destroy');
    Route::post('enrollments/{enrollment}/approve-payment', [ElearningEnrollmentController::class, 'approvePayment'])->name('enrollments.approvePayment');

    Route::get('courses/{course}/lessons/{lesson}/resources', [ElearningLessonResourceController::class, 'index'])->name('resources.index');
    Route::get('courses/{course}/lessons/{lesson}/resources/create', [ElearningLessonResourceController::class, 'create'])->name('resources.create');
    Route::post('courses/{course}/lessons/{lesson}/resources', [ElearningLessonResourceController::class, 'store'])->name('resources.store');
    Route::delete('courses/{course}/lessons/{lesson}/resources/{resource}', [ElearningLessonResourceController::class, 'destroy'])->name('resources.destroy');

    Route::get('courses/{course}/lessons/{lesson}/quizzes', [ElearningQuizController::class, 'index'])->name('quizzes.index');
    Route::get('courses/{course}/lessons/{lesson}/quizzes/create', [ElearningQuizController::class, 'create'])->name('quizzes.create');
    Route::post('courses/{course}/lessons/{lesson}/quizzes', [ElearningQuizController::class, 'store'])->name('quizzes.store');
    Route::get('courses/{course}/lessons/{lesson}/quizzes/{quiz}/edit', [ElearningQuizController::class, 'edit'])->name('quizzes.edit');
    Route::put('courses/{course}/lessons/{lesson}/quizzes/{quiz}', [ElearningQuizController::class, 'update'])->name('quizzes.update');
    Route::delete('courses/{course}/lessons/{lesson}/quizzes/{quiz}', [ElearningQuizController::class, 'destroy'])->name('quizzes.destroy');

    Route::get('courses/{course}/lessons/{lesson}/quizzes/{quiz}/questions', [ElearningQuizQuestionController::class, 'index'])->name('quiz-questions.index');
    Route::get('courses/{course}/lessons/{lesson}/quizzes/{quiz}/questions/create', [ElearningQuizQuestionController::class, 'create'])->name('quiz-questions.create');
    Route::post('courses/{course}/lessons/{lesson}/quizzes/{quiz}/questions', [ElearningQuizQuestionController::class, 'store'])->name('quiz-questions.store');
    Route::get('courses/{course}/lessons/{lesson}/quizzes/{quiz}/questions/{question}/edit', [ElearningQuizQuestionController::class, 'edit'])->name('quiz-questions.edit');
    Route::put('courses/{course}/lessons/{lesson}/quizzes/{quiz}/questions/{question}', [ElearningQuizQuestionController::class, 'update'])->name('quiz-questions.update');
    Route::delete('courses/{course}/lessons/{lesson}/quizzes/{quiz}/questions/{question}', [ElearningQuizQuestionController::class, 'destroy'])->name('quiz-questions.destroy');

    Route::get('enrollments/{enrollment}/quizzes/{quiz}/start', [ElearningQuizAttemptController::class, 'start'])->name('quiz-attempts.start');
    Route::post('enrollments/{enrollment}/quizzes/{quiz}/attempts/{attempt}/submit', [ElearningQuizAttemptController::class, 'submit'])->name('quiz-attempts.submit');

    Route::post('enrollments/{enrollment}/issue-certificate', [ElearningEnrollmentController::class, 'issueCertificate'])
        ->name('enrollments.issueCertificate');

    // Learner account actions
    Route::post('enrollments/{enrollment}/send-welcome-email', [ElearningEnrollmentController::class, 'sendWelcomeEmail'])
        ->name('enrollments.sendWelcomeEmail');
    Route::post('enrollments/{enrollment}/reset-learner-password', [ElearningEnrollmentController::class, 'resetLearnerPassword'])
        ->name('enrollments.resetLearnerPassword');
    Route::get('enrollments/{enrollment}/login-as-learner', [ElearningEnrollmentController::class, 'loginAsLearner'])
        ->name('enrollments.loginAsLearner');

    Route::get('/certificate-verify', function (\Illuminate\Http\Request $request) {
        $enrollment = \App\Models\ElearningEnrollment::where('certificate_number', $request->cert)->firstOrFail();
        return view('elearning.certificates.verify', compact('enrollment'));
    });
});

/*
|--------------------------------------------------------------------------
| Admin — Blog & Testimonials & Public Visibility
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    // Blog posts
    Route::get('blog',                   [BlogController::class, 'index'])          ->name('blog.index');
    Route::get('blog/create',            [BlogController::class, 'create'])         ->name('blog.create');
    Route::post('blog',                  [BlogController::class, 'store'])          ->name('blog.store');
    Route::get('blog/{post}/edit',       [BlogController::class, 'edit'])           ->name('blog.edit');
    Route::put('blog/{post}',            [BlogController::class, 'update'])         ->name('blog.update');
    Route::delete('blog/{post}',         [BlogController::class, 'destroy'])        ->name('blog.destroy');

    // Blog categories
    Route::get('blog-categories',               [BlogController::class, 'categories'])      ->name('blog.categories');
    Route::post('blog-categories',              [BlogController::class, 'storeCategory'])   ->name('blog.categories.store');
    Route::put('blog-categories/{category}',    [BlogController::class, 'updateCategory'])  ->name('blog.categories.update');
    Route::delete('blog-categories/{category}', [BlogController::class, 'destroyCategory']) ->name('blog.categories.destroy');

    // Testimonials
    Route::get('testimonials',                            [TestimonialController::class, 'index'])  ->name('testimonials.index');
    Route::patch('testimonials/{testimonial}/approve',    [TestimonialController::class, 'approve'])->name('testimonials.approve');
    Route::patch('testimonials/{testimonial}/reject',     [TestimonialController::class, 'reject']) ->name('testimonials.reject');
    Route::patch('testimonials/{testimonial}/feature',    [TestimonialController::class, 'feature'])->name('testimonials.feature');
    Route::delete('testimonials/{testimonial}',           [TestimonialController::class, 'destroy'])->name('testimonials.destroy');
});

/*
|--------------------------------------------------------------------------
| Corporate Training Module
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Corporate\ProjectController      as CorpProjectController;
use App\Http\Controllers\Corporate\SessionController      as CorpSessionController;
use App\Http\Controllers\Corporate\ParticipantController  as CorpParticipantController;
use App\Http\Controllers\Corporate\AttendanceController   as CorpAttendanceController;
use App\Http\Controllers\Corporate\CertificateController  as CorpCertificateController;
use App\Http\Controllers\Corporate\EvidenceController     as CorpEvidenceController;
use App\Http\Controllers\Corporate\EvaluationController   as CorpEvaluationController;
use App\Http\Controllers\Corporate\ReportController       as CorpReportController;

Route::middleware(['auth', 'admin'])
     ->prefix('corporate')
     ->name('corporate.')
     ->group(function () {

    // ── Projects ────────────────────────────────────────────────
    Route::get('projects',                    [CorpProjectController::class, 'index'])   ->name('projects.index');
    Route::get('projects/create',             [CorpProjectController::class, 'create'])  ->name('projects.create');
    Route::post('projects',                   [CorpProjectController::class, 'store'])   ->name('projects.store');
    Route::get('projects/{project}',          [CorpProjectController::class, 'show'])    ->name('projects.show');
    Route::get('projects/{project}/edit',     [CorpProjectController::class, 'edit'])    ->name('projects.edit');
    Route::put('projects/{project}',          [CorpProjectController::class, 'update'])  ->name('projects.update');
    Route::delete('projects/{project}',       [CorpProjectController::class, 'destroy']) ->name('projects.destroy');

    // ── Sessions ────────────────────────────────────────────────
    Route::get('sessions',                    [CorpSessionController::class, 'index'])   ->name('sessions.index');
    Route::get('sessions/create',             [CorpSessionController::class, 'create'])  ->name('sessions.create');
    Route::post('sessions',                   [CorpSessionController::class, 'store'])   ->name('sessions.store');
    Route::get('sessions/{session}',          [CorpSessionController::class, 'show'])    ->name('sessions.show');
    Route::get('sessions/{session}/edit',     [CorpSessionController::class, 'edit'])    ->name('sessions.edit');
    Route::put('sessions/{session}',          [CorpSessionController::class, 'update'])  ->name('sessions.update');
    Route::delete('sessions/{session}',       [CorpSessionController::class, 'destroy']) ->name('sessions.destroy');

    // ── Participants (nested under sessions) ─────────────────────
    Route::get('sessions/{session}/participants',              [CorpParticipantController::class, 'index'])       ->name('sessions.participants.index');
    Route::get('sessions/{session}/participants/create',       [CorpParticipantController::class, 'create'])      ->name('sessions.participants.create');
    Route::post('sessions/{session}/participants',             [CorpParticipantController::class, 'store'])       ->name('sessions.participants.store');
    Route::get('sessions/{session}/participants/{participant}/edit',   [CorpParticipantController::class, 'edit'])    ->name('sessions.participants.edit');
    Route::put('sessions/{session}/participants/{participant}',        [CorpParticipantController::class, 'update'])  ->name('sessions.participants.update');
    Route::delete('sessions/{session}/participants/{participant}',     [CorpParticipantController::class, 'destroy']) ->name('sessions.participants.destroy');
    Route::post('sessions/{session}/participants/bulk-delete',         [CorpParticipantController::class, 'bulkDestroy'])  ->name('sessions.participants.bulk-destroy');
    Route::post('sessions/{session}/participants/import',              [CorpParticipantController::class, 'importCsv'])    ->name('sessions.participants.import');
    Route::get('sessions/{session}/participants/export',               [CorpParticipantController::class, 'exportCsv'])    ->name('sessions.participants.export');
    Route::get('participants/csv-template',                            [CorpParticipantController::class, 'csvTemplate'])  ->name('participants.csv-template');

    // ── Attendance ───────────────────────────────────────────────
    Route::get('sessions/{session}/attendance',             [CorpAttendanceController::class, 'sheet'])     ->name('sessions.attendance');
    Route::post('sessions/{session}/attendance',            [CorpAttendanceController::class, 'save'])      ->name('sessions.attendance.save');
    Route::post('sessions/{session}/attendance/bulk',       [CorpAttendanceController::class, 'bulkMark'])  ->name('sessions.attendance.bulk');
    Route::get('sessions/{session}/attendance/export',      [CorpAttendanceController::class, 'exportCsv']) ->name('sessions.attendance.export');

    // ── Certificates ─────────────────────────────────────────────
    Route::get('sessions/{session}/certificates',              [CorpCertificateController::class, 'index'])          ->name('sessions.certificates.index');
    Route::post('sessions/{session}/certificates/bulk',        [CorpCertificateController::class, 'generateBulk'])   ->name('sessions.certificates.bulk');
    Route::post('sessions/{session}/certificates/{participant}/generate', [CorpCertificateController::class, 'generateSingle']) ->name('sessions.certificates.generate');
    Route::get('sessions/{session}/certificates/zip',          [CorpCertificateController::class, 'downloadZip'])    ->name('sessions.certificates.zip');
    Route::get('certificates/{certificate}/view',              [CorpCertificateController::class, 'view'])            ->name('certificates.view');

    // ── Evidence ─────────────────────────────────────────────────
    Route::get('sessions/{session}/evidence',              [CorpEvidenceController::class, 'index'])   ->name('sessions.evidence.index');
    Route::post('sessions/{session}/evidence',             [CorpEvidenceController::class, 'store'])   ->name('sessions.evidence.store');
    Route::delete('sessions/{session}/evidence/{evidence}',[CorpEvidenceController::class, 'destroy']) ->name('sessions.evidence.destroy');

    // ── Evaluation ───────────────────────────────────────────────
    Route::get('sessions/{session}/evaluation',             [CorpEvaluationController::class, 'index'])   ->name('sessions.evaluation.index');
    Route::post('sessions/{session}/evaluation',            [CorpEvaluationController::class, 'store'])   ->name('sessions.evaluation.store');
    Route::delete('sessions/{session}/evaluation/{evaluation}', [CorpEvaluationController::class, 'destroy']) ->name('sessions.evaluation.destroy');

    // ── Reports ──────────────────────────────────────────────────
    Route::get('projects/{project}/report', [CorpReportController::class, 'projectReport']) ->name('projects.report');
});