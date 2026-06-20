<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Setup\LtfDeliveryMethodController;
use App\Http\Controllers\Setup\LtfTrainingModelController;
use App\Http\Controllers\Setup\LtfProgramPurposeController;
use App\Http\Controllers\Setup\LtfLearningFrameworkController;
use App\Http\Controllers\Setup\LtfStandardController;
use App\Http\Controllers\Setup\LtfIndustryController;
use App\Http\Controllers\Setup\LtfAudienceTypeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FeedbackTemplateController;
use App\Http\Controllers\FeedbackResponseController;
use App\Http\Controllers\FeedbackSubmissionController;
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
use App\Http\Controllers\ElearningQuizAdminController;
use App\Http\Controllers\ElearningQuizController;
use App\Http\Controllers\ElearningQuizQuestionController;
use App\Http\Controllers\ElearningQuizAttemptController;

use App\Http\Controllers\ParticipantDashboardController;
use App\Http\Controllers\LessonAudioProgressController;
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
use App\Http\Controllers\AiLessonContentController;
use App\Http\Controllers\AiTrainingNewsController;
use App\Http\Controllers\TrainingMediaController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\LessonAudioController;
use App\Http\Controllers\KnowledgeResourceController;
use App\Http\Controllers\AiQuestionBankController;
use App\Http\Controllers\PptElearningBuilderController;

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

    // Compliance & informational pages
    Route::get('/about-us',                    [PublicController::class, 'about'])          ->name('about');
    Route::get('/contact',                     [PublicController::class, 'contact'])        ->name('contact');
    Route::post('/contact',                    [PublicController::class, 'contactSubmit'])  ->name('contact.submit');
    Route::get('/terms-and-conditions',        [PublicController::class, 'terms'])          ->name('terms');
    Route::get('/privacy-policy',              [PublicController::class, 'privacy'])        ->name('privacy');
    Route::get('/refund-policy',               [PublicController::class, 'refund'])         ->name('refund');

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

    Route::prefix('knowledge-hub')->name('knowledge-hub.')->group(function () {
        Route::get('/', [KnowledgeResourceController::class, 'index'])->name('index');
        Route::get('/create', [KnowledgeResourceController::class, 'create'])->name('create');
        Route::post('/', [KnowledgeResourceController::class, 'store'])->name('store');
        Route::get('/{knowledgeResource}', [KnowledgeResourceController::class, 'show'])->name('show');
        Route::get('/{knowledgeResource}/edit', [KnowledgeResourceController::class, 'edit'])->name('edit');
        Route::put('/{knowledgeResource}', [KnowledgeResourceController::class, 'update'])->name('update');
        Route::post('/{knowledgeResource}/archive', [KnowledgeResourceController::class, 'archive'])->name('archive');
        Route::get('/{knowledgeResource}/file', [KnowledgeResourceController::class, 'viewFile'])->name('file');
        Route::get('/{knowledgeResource}/download', [KnowledgeResourceController::class, 'download'])->name('download');
    });

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

    Route::post('/my-elearning/{enrollment}/lessons/{lesson}/audio-progress', [LessonAudioProgressController::class, 'upsert'])
        ->name('participant.lesson.audio-progress');

    Route::get('/my-elearning/{enrollment}/lessons/{lesson}/audio-status', [LessonAudioProgressController::class, 'status'])
        ->name('participant.lesson.audio-status');


    Route::get('/my-elearning/{enrollment}/quizzes/{quiz}', [ParticipantQuizController::class, 'start'])
        ->name('participant.quiz.start');

    Route::post('/my-elearning/{enrollment}/quizzes/{quiz}/submit', [ParticipantQuizController::class, 'submit'])
        ->name('participant.quiz.submit');

    Route::get('/my-certificates', [ParticipantDashboardController::class, 'myCertificates'])
        ->name('participant.my-certificates');
});

/*
|--------------------------------------------------------------------------
| PPT eLearning Builder Routes (auth + admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('ppt-builder')->name('ppt-builder.')->group(function () {

    Route::get('/',           [PptElearningBuilderController::class, 'index'])   ->name('index');
    Route::get('/create',     [PptElearningBuilderController::class, 'create'])  ->name('create');
    Route::post('/',          [PptElearningBuilderController::class, 'store'])   ->name('store');
    Route::get('/{pptCourse}/editor', [PptElearningBuilderController::class, 'editor'])  ->name('editor');
    Route::delete('/{pptCourse}',     [PptElearningBuilderController::class, 'destroy']) ->name('destroy');

    // Slide AJAX
    Route::get('/{pptCourse}/slides/{pptSlide}',                   [PptElearningBuilderController::class, 'getSlide'])       ->name('slides.get');
    Route::put('/{pptCourse}/slides/{pptSlide}',                   [PptElearningBuilderController::class, 'updateSlide'])    ->name('slides.update');
    Route::post('/{pptCourse}/slides/{pptSlide}/remove',           [PptElearningBuilderController::class, 'removeSlide'])    ->name('slides.remove');
    Route::post('/{pptCourse}/slides/{pptSlide}/assign',           [PptElearningBuilderController::class, 'assignSlide'])    ->name('slides.assign');
    Route::post('/{pptCourse}/slides/reorder',                     [PptElearningBuilderController::class, 'reorderSlides'])  ->name('slides.reorder');

    // AI AJAX
    Route::post('/{pptCourse}/slides/{pptSlide}/ai-explain',       [PptElearningBuilderController::class, 'aiExplain'])        ->name('slides.ai-explain');
    Route::post('/{pptCourse}/slides/{pptSlide}/ai-check',         [PptElearningBuilderController::class, 'aiKnowledgeCheck']) ->name('slides.ai-check');

    // Audio AJAX
    Route::post('/{pptCourse}/slides/{pptSlide}/audio',            [PptElearningBuilderController::class, 'generateAudio']) ->name('slides.audio.generate');
    Route::post('/{pptCourse}/slides/{pptSlide}/audio/upload',     [PptElearningBuilderController::class, 'uploadAudio'])   ->name('slides.audio.upload');
    Route::delete('/{pptCourse}/slides/{pptSlide}/audio',          [PptElearningBuilderController::class, 'deleteAudio'])   ->name('slides.audio.delete');

    // Module AJAX
    Route::post('/{pptCourse}/modules',                            [PptElearningBuilderController::class, 'storeModule'])   ->name('modules.store');
    Route::put('/{pptCourse}/modules/{pptModule}',                 [PptElearningBuilderController::class, 'updateModule'])  ->name('modules.update');
    Route::delete('/{pptCourse}/modules/{pptModule}',              [PptElearningBuilderController::class, 'destroyModule']) ->name('modules.destroy');
    Route::post('/{pptCourse}/modules/reorder',                    [PptElearningBuilderController::class, 'reorderModules'])->name('modules.reorder');

    // Publish
    Route::post('/{pptCourse}/publish',                            [PptElearningBuilderController::class, 'publish'])       ->name('publish');
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

// Coupon validation (public AJAX)
Route::post('/coupon/validate', [CouponController::class, 'validateCoupon'])->name('coupon.validate');
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
    Route::post('/ai/course-generator/generate',                   [AiCourseGeneratorController::class, 'generate'])         ->name('ai.course-generator.generate');
    Route::get( '/ai/course-generator/preview',                    [AiCourseGeneratorController::class, 'preview'])          ->name('ai.course-generator.preview');
    Route::post('/ai/course-generator/save',                       [AiCourseGeneratorController::class, 'save'])             ->name('ai.course-generator.save');
    Route::post('/ai/course-generator/cancel',                     [AiCourseGeneratorController::class, 'cancel'])           ->name('ai.course-generator.cancel');
    Route::get( '/ai/course-generator/{course}/blueprint',         [AiCourseGeneratorController::class, 'blueprint'])        ->name('ai.course-generator.blueprint');
    Route::post('/ai/course-generator/{course}/blueprint/approve', [AiCourseGeneratorController::class, 'approveBlueprint']) ->name('ai.course-generator.blueprint.approve');
    Route::get( '/ai/course-generator/{course}/quality',           [AiCourseGeneratorController::class, 'quality'])          ->name('ai.course-generator.quality');
    Route::get( '/ai/course-generator/{course}/progress',          [AiCourseGeneratorController::class, 'generationProgress'])->name('ai.course-generator.progress');
    Route::post('/ai/course-generator/{course}/generate-next',     [AiCourseGeneratorController::class, 'generateNext'])     ->name('ai.course-generator.generate-next');
    Route::post('/ai/course-generator/{course}/generate-module-quiz',      [AiCourseGeneratorController::class, 'generateModuleQuiz'])      ->name('ai.course-generator.generate-module-quiz');
    Route::post('/ai/course-generator/{course}/generate-final-assessment', [AiCourseGeneratorController::class, 'generateFinalAssessment']) ->name('ai.course-generator.generate-final-assessment');
    Route::get( '/ai/course-generator/{course}/generation-status',        [AiCourseGeneratorController::class, 'generationStatus'])          ->name('ai.course-generator.generation-status');
    Route::get('/ai/question-bank', [AiQuestionBankController::class, 'index'])->name('ai.question-bank.index');
    Route::patch('/ai/question-bank/{question}/status', [AiQuestionBankController::class, 'updateStatus'])->name('ai.question-bank.status');

    // ── Training Feedback & Evaluation ────────────────────────
    Route::prefix('feedback')->name('feedback.')->group(function () {
        // Templates
        Route::get( 'templates',                          [FeedbackTemplateController::class, 'index'])   ->name('templates.index');
        Route::get( 'templates/create',                   [FeedbackTemplateController::class, 'create'])  ->name('templates.create');
        Route::post('templates',                          [FeedbackTemplateController::class, 'store'])   ->name('templates.store');
        Route::get( 'templates/{template}',               [FeedbackTemplateController::class, 'show'])    ->name('templates.show');
        Route::get( 'templates/{template}/edit',          [FeedbackTemplateController::class, 'edit'])    ->name('templates.edit');
        Route::put( 'templates/{template}',               [FeedbackTemplateController::class, 'update'])  ->name('templates.update');
        Route::delete('templates/{template}',             [FeedbackTemplateController::class, 'destroy']) ->name('templates.destroy');
        Route::post('templates/{template}/clone',         [FeedbackTemplateController::class, 'clone'])   ->name('templates.clone');
        Route::get( 'templates/{template}/preview',       [FeedbackTemplateController::class, 'preview']) ->name('templates.preview');
        // Responses
        Route::get( 'responses',                          [FeedbackResponseController::class, 'index'])            ->name('responses.index');
        Route::get( 'responses/{response}',               [FeedbackResponseController::class, 'show'])             ->name('responses.show');
        Route::delete('responses/{response}',             [FeedbackResponseController::class, 'destroy'])          ->name('responses.destroy');
        Route::post('responses/{response}/approve-testimonial', [FeedbackResponseController::class, 'approveTestimonial'])->name('responses.approve-testimonial');
        Route::post('assign',                             [FeedbackResponseController::class, 'assign'])           ->name('assign');
    });

    // ── AI Trainer Profile Generator (super_admin only) ────────
    Route::get( '/ai/trainer-profile',           [AiTrainerProfileController::class, 'index'])    ->name('ai.trainer-profile.index');
    Route::post('/ai/trainer-profile/generate',  [AiTrainerProfileController::class, 'generate']) ->name('ai.trainer-profile.generate');
    Route::get( '/ai/trainer-profile/preview',   [AiTrainerProfileController::class, 'preview'])  ->name('ai.trainer-profile.preview');
    Route::post('/ai/trainer-profile/save',      [AiTrainerProfileController::class, 'save'])     ->name('ai.trainer-profile.save');
    Route::post('/ai/trainer-profile/cancel',    [AiTrainerProfileController::class, 'cancel'])   ->name('ai.trainer-profile.cancel');

    // ── Training News (AI Content Automation) ──────────────────────────────
    Route::get( '/training-news',                          [AiTrainingNewsController::class, 'index'])          ->name('training-news.index');
    Route::get( '/training-news/analytics',                [AiTrainingNewsController::class, 'analytics'])      ->name('training-news.analytics');
    Route::get( '/training-news/create/{schedule}',        [AiTrainingNewsController::class, 'create'])         ->name('training-news.create');
    Route::post('/training-news/generate-article/{schedule}', [AiTrainingNewsController::class, 'generateArticle'])->name('training-news.generate-article');
    Route::post('/training-news/generate-seo',             [AiTrainingNewsController::class, 'generateSeo'])    ->name('training-news.generate-seo');
    Route::post('/training-news/generate-social',          [AiTrainingNewsController::class, 'generateSocial']) ->name('training-news.generate-social');
    Route::post('/training-news/store/{schedule}',         [AiTrainingNewsController::class, 'store'])          ->name('training-news.store');
    Route::get( '/training-news/{article}/edit',           [AiTrainingNewsController::class, 'edit'])           ->name('training-news.edit');
    Route::put( '/training-news/{article}',                [AiTrainingNewsController::class, 'update'])         ->name('training-news.update');
    Route::post('/training-news/{article}/submit-review',  [AiTrainingNewsController::class, 'submitForReview'])->name('training-news.submit-review');
    Route::post('/training-news/{article}/approve',        [AiTrainingNewsController::class, 'approve'])        ->name('training-news.approve');
    Route::post('/training-news/{article}/publish',        [AiTrainingNewsController::class, 'publish'])        ->name('training-news.publish');
    Route::post('/training-news/{article}/unpublish',      [AiTrainingNewsController::class, 'unpublish'])      ->name('training-news.unpublish');
    Route::post('/training-news/{article}/archive',        [AiTrainingNewsController::class, 'archive'])        ->name('training-news.archive');
    Route::delete('/training-news/{article}',              [AiTrainingNewsController::class, 'destroy'])        ->name('training-news.destroy');

    // ── Training Media ──────────────────────────────────────────────────────
    Route::get(  '/training-media/{schedule}',                   [TrainingMediaController::class, 'index'])          ->name('training-media.index');
    Route::post( '/training-media/{schedule}/upload',            [TrainingMediaController::class, 'store'])          ->name('training-media.store');
    Route::post( '/training-media/{schedule}/reorder',           [TrainingMediaController::class, 'reorder'])        ->name('training-media.reorder');
    Route::post( '/training-media/{schedule}/generate-captions', [TrainingMediaController::class, 'generateCaptions'])->name('training-media.generate-captions');
    Route::put(  '/training-media/item/{media}',                 [TrainingMediaController::class, 'update'])         ->name('training-media.update');
    Route::post( '/training-media/item/{media}/featured',        [TrainingMediaController::class, 'setFeatured'])    ->name('training-media.featured');
    Route::delete('/training-media/item/{media}',                [TrainingMediaController::class, 'destroy'])        ->name('training-media.destroy');

    // ── Legacy redirect: old course-types URL → delivery-methods ────
    Route::get('setup/course-types{any?}', fn() => redirect()->route('setup.delivery-methods.index', [], 301))
        ->where('any', '.*');

    // ── LTF Setup (taxonomy management) ─────────────────────────────
    Route::prefix('setup')->name('setup.')->group(function () {

        Route::get('delivery-methods',                         [LtfDeliveryMethodController::class, 'index'])  ->name('delivery-methods.index');
        Route::get('delivery-methods/create',                  [LtfDeliveryMethodController::class, 'create']) ->name('delivery-methods.create');
        Route::post('delivery-methods',                        [LtfDeliveryMethodController::class, 'store'])  ->name('delivery-methods.store');
        Route::get('delivery-methods/{deliveryMethod}/edit',   [LtfDeliveryMethodController::class, 'edit'])   ->name('delivery-methods.edit');
        Route::put('delivery-methods/{deliveryMethod}',        [LtfDeliveryMethodController::class, 'update']) ->name('delivery-methods.update');
        Route::patch('delivery-methods/{deliveryMethod}/toggle',[LtfDeliveryMethodController::class, 'toggle'])->name('delivery-methods.toggle');
        Route::delete('delivery-methods/{deliveryMethod}',     [LtfDeliveryMethodController::class, 'destroy'])->name('delivery-methods.destroy');

        Route::get('training-models',                          [LtfTrainingModelController::class, 'index'])  ->name('training-models.index');
        Route::get('training-models/create',                   [LtfTrainingModelController::class, 'create']) ->name('training-models.create');
        Route::post('training-models',                         [LtfTrainingModelController::class, 'store'])  ->name('training-models.store');
        Route::get('training-models/{trainingModel}/edit',     [LtfTrainingModelController::class, 'edit'])   ->name('training-models.edit');
        Route::put('training-models/{trainingModel}',          [LtfTrainingModelController::class, 'update']) ->name('training-models.update');
        Route::patch('training-models/{trainingModel}/toggle', [LtfTrainingModelController::class, 'toggle']) ->name('training-models.toggle');
        Route::delete('training-models/{trainingModel}',       [LtfTrainingModelController::class, 'destroy'])->name('training-models.destroy');

        Route::get('program-purposes',                         [LtfProgramPurposeController::class, 'index'])  ->name('program-purposes.index');
        Route::get('program-purposes/create',                  [LtfProgramPurposeController::class, 'create']) ->name('program-purposes.create');
        Route::post('program-purposes',                        [LtfProgramPurposeController::class, 'store'])  ->name('program-purposes.store');
        Route::get('program-purposes/{programPurpose}/edit',   [LtfProgramPurposeController::class, 'edit'])   ->name('program-purposes.edit');
        Route::put('program-purposes/{programPurpose}',        [LtfProgramPurposeController::class, 'update']) ->name('program-purposes.update');
        Route::patch('program-purposes/{programPurpose}/toggle',[LtfProgramPurposeController::class, 'toggle'])->name('program-purposes.toggle');
        Route::delete('program-purposes/{programPurpose}',     [LtfProgramPurposeController::class, 'destroy'])->name('program-purposes.destroy');

        Route::get('frameworks',                          [LtfLearningFrameworkController::class, 'index'])  ->name('frameworks.index');
        Route::get('frameworks/create',                   [LtfLearningFrameworkController::class, 'create']) ->name('frameworks.create');
        Route::post('frameworks',                         [LtfLearningFrameworkController::class, 'store'])  ->name('frameworks.store');
        Route::get('frameworks/{framework}/edit',         [LtfLearningFrameworkController::class, 'edit'])   ->name('frameworks.edit');
        Route::put('frameworks/{framework}',              [LtfLearningFrameworkController::class, 'update']) ->name('frameworks.update');
        Route::patch('frameworks/{framework}/toggle',     [LtfLearningFrameworkController::class, 'toggle']) ->name('frameworks.toggle');
        Route::delete('frameworks/{framework}',           [LtfLearningFrameworkController::class, 'destroy'])->name('frameworks.destroy');

        Route::get('standards',                           [LtfStandardController::class, 'index'])  ->name('standards.index');
        Route::get('standards/create',                    [LtfStandardController::class, 'create']) ->name('standards.create');
        Route::post('standards',                          [LtfStandardController::class, 'store'])  ->name('standards.store');
        Route::get('standards/{standard}/edit',           [LtfStandardController::class, 'edit'])   ->name('standards.edit');
        Route::put('standards/{standard}',                [LtfStandardController::class, 'update']) ->name('standards.update');
        Route::patch('standards/{standard}/toggle',       [LtfStandardController::class, 'toggle']) ->name('standards.toggle');
        Route::delete('standards/{standard}',             [LtfStandardController::class, 'destroy'])->name('standards.destroy');

        Route::get('industries',                          [LtfIndustryController::class, 'index'])  ->name('industries.index');
        Route::get('industries/create',                   [LtfIndustryController::class, 'create']) ->name('industries.create');
        Route::post('industries',                         [LtfIndustryController::class, 'store'])  ->name('industries.store');
        Route::get('industries/{industry}/edit',          [LtfIndustryController::class, 'edit'])   ->name('industries.edit');
        Route::put('industries/{industry}',               [LtfIndustryController::class, 'update']) ->name('industries.update');
        Route::patch('industries/{industry}/toggle',      [LtfIndustryController::class, 'toggle']) ->name('industries.toggle');
        Route::delete('industries/{industry}',            [LtfIndustryController::class, 'destroy'])->name('industries.destroy');

        Route::get('audiences',                           [LtfAudienceTypeController::class, 'index'])  ->name('audiences.index');
        Route::get('audiences/create',                    [LtfAudienceTypeController::class, 'create']) ->name('audiences.create');
        Route::post('audiences',                          [LtfAudienceTypeController::class, 'store'])  ->name('audiences.store');
        Route::get('audiences/{audienceType}/edit',       [LtfAudienceTypeController::class, 'edit'])   ->name('audiences.edit');
        Route::put('audiences/{audienceType}',            [LtfAudienceTypeController::class, 'update']) ->name('audiences.update');
        Route::patch('audiences/{audienceType}/toggle',   [LtfAudienceTypeController::class, 'toggle']) ->name('audiences.toggle');
        Route::delete('audiences/{audienceType}',         [LtfAudienceTypeController::class, 'destroy'])->name('audiences.destroy');

    });

}); // end admin middleware group

// ── Feedback Submission (public — token-based, no auth required) ──
Route::get( '/feedback/{token}',         [FeedbackSubmissionController::class, 'show'])    ->name('feedback.show');
Route::post('/feedback/{token}',         [FeedbackSubmissionController::class, 'submit'])  ->name('feedback.submit');
Route::get( '/feedback/{token}/thankyou',[FeedbackSubmissionController::class, 'thankyou'])->name('feedback.thankyou');

/*
|--------------------------------------------------------------------------
| Elearning Admin Routes (auth + admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('elearning')->name('elearning.')->group(function () {

    Route::resource('courses', ElearningCourseController::class);

    // Course Cover Generator
    Route::post('courses/{course}/cover/generate',       [\App\Http\Controllers\CourseCoverController::class, 'generate'])      ->name('courses.cover.generate');
    Route::get('courses/{course}/cover/status',          [\App\Http\Controllers\CourseCoverController::class, 'status'])        ->name('courses.cover.status');
    Route::post('courses/{course}/cover/upload',         [\App\Http\Controllers\CourseCoverController::class, 'upload'])        ->name('courses.cover.upload');
    Route::delete('courses/{course}/cover',              [\App\Http\Controllers\CourseCoverController::class, 'delete'])        ->name('courses.cover.delete');
    Route::post('courses/{course}/cover/preview-prompt', [\App\Http\Controllers\CourseCoverController::class, 'previewPrompt']) ->name('courses.cover.preview-prompt');

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

    // AI Lesson Content Generator
    Route::post('courses/{course}/lessons/{lesson}/ai-generate', [AiLessonContentController::class, 'generate'])->name('ai-lesson-content.generate');
    Route::get( 'courses/{course}/lessons/{lesson}/ai-preview',  [AiLessonContentController::class, 'preview']) ->name('ai-lesson-content.preview');
    Route::post('courses/{course}/lessons/{lesson}/ai-save',     [AiLessonContentController::class, 'save'])    ->name('ai-lesson-content.save');
    Route::post('courses/{course}/lessons/{lesson}/ai-cancel',   [AiLessonContentController::class, 'cancel'])  ->name('ai-lesson-content.cancel');

    // AI Audio Learning Assistant — admin routes
    // Audio management — admin only, all generation happens here before publishing
    Route::post('courses/{course}/lessons/{lesson}/blocks/{block}/audio',         [LessonAudioController::class, 'generateBlock'])  ->name('audio.block.generate');
    Route::delete('courses/{course}/lessons/{lesson}/audio/block/{audio}',        [LessonAudioController::class, 'destroyBlock'])   ->name('audio.block.destroy');
    Route::post('courses/{course}/lessons/{lesson}/audio/recap',                  [LessonAudioController::class, 'generateRecap'])  ->name('audio.recap.generate');
    Route::delete('courses/{course}/lessons/{lesson}/audio/recap/{audio}',        [LessonAudioController::class, 'destroyRecap'])   ->name('audio.recap.destroy');
    Route::get('courses/{course}/lessons/{lesson}/audio/status',                  [LessonAudioController::class, 'status'])         ->name('audio.status');

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
    Route::get('courses/{course}/lessons/{lesson}/quizzes/{quiz}/preview', [ElearningQuizController::class, 'preview'])->name('quizzes.preview');

    // ── Quiz Admin Recovery (Phase 1) ─────────────────────────────────
    Route::get('enrollments/{enrollment}/quizzes/{quiz}/attempts', [ElearningQuizAdminController::class, 'attempts'])->name('quiz-admin.attempts');
    Route::post('enrollments/{enrollment}/quizzes/{quiz}/reset-attempts', [ElearningQuizAdminController::class, 'resetAttempts'])->name('quiz-admin.reset-attempts');
    Route::post('enrollments/{enrollment}/quizzes/{quiz}/add-extra-attempt', [ElearningQuizAdminController::class, 'addExtraAttempt'])->name('quiz-admin.add-extra-attempt');
    Route::post('enrollments/{enrollment}/quizzes/{quiz}/mark-passed', [ElearningQuizAdminController::class, 'markPassed'])->name('quiz-admin.mark-passed');

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

    // Coupons & Promotions
    Route::get('coupons',                  [CouponController::class, 'index'])   ->name('coupons.index');
    Route::get('coupons/create',           [CouponController::class, 'create'])  ->name('coupons.create');
    Route::post('coupons',                 [CouponController::class, 'store'])   ->name('coupons.store');
    Route::get('coupons/{coupon}',         [CouponController::class, 'show'])    ->name('coupons.show');
    Route::get('coupons/{coupon}/edit',    [CouponController::class, 'edit'])    ->name('coupons.edit');
    Route::put('coupons/{coupon}',         [CouponController::class, 'update'])  ->name('coupons.update');
    Route::delete('coupons/{coupon}',      [CouponController::class, 'destroy']) ->name('coupons.destroy');
    Route::post('coupons/{coupon}/toggle', [CouponController::class, 'toggle'])  ->name('coupons.toggle');
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
