<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\ElearningEnrollment;
use App\Models\TrainingSchedule;

class DashboardController extends Controller
{
    public function index()
    {
        $currentYear = date('Y');

        // ── Instructor-Led / Manual Training Stats ──────────
        $manualCourses     = Course::where('course_type', 'manual')
            ->orWhereNull('course_type')
            ->count();

        $totalSchedules    = TrainingSchedule::count();

        $totalEnrollmentsThisYear = Enrollment::whereYear('created_at', $currentYear)->count();

        $totalConfirmed    = Enrollment::where('registration_status', 'Confirmed')
            ->whereYear('created_at', $currentYear)->count();

        $attendanceCompleted = Enrollment::whereIn('attendance_status', ['Present', 'Partial'])
            ->whereYear('created_at', $currentYear)->count();

        $totalPaid         = Enrollment::where('payment_status', 'Paid')
            ->whereYear('created_at', $currentYear)->count();

        $totalPaidAmount   = Enrollment::whereYear('created_at', $currentYear)
            ->sum('amount_received');

        $dueEnrollments    = Enrollment::where('completion_status', 'Completed')
            ->where('payment_status', '!=', 'Paid')
            ->whereYear('created_at', $currentYear)
            ->get();

        $totalDue          = $dueEnrollments->count();
        $totalDueAmount    = $dueEnrollments->sum(fn ($e) => max(($e->applied_fee ?? 0) - ($e->amount_received ?? 0), 0));

        $pendingPayments   = Enrollment::where('payment_status', 'Pending')
            ->whereYear('created_at', $currentYear)->count();

        $certificatesIssued = Enrollment::where('certificate_generated', 1)
            ->whereYear('created_at', $currentYear)->count();

        // Upcoming schedules list (next 5)
        $upcomingSchedules = TrainingSchedule::with(['course', 'trainer'])
            ->whereDate('start_date', '>=', now())
            ->orderBy('start_date')
            ->limit(5)
            ->get();

        // First upcoming (for legacy compact widget)
        $upcomingSchedule  = $upcomingSchedules->first();
        $upcomingTotalEnrolled = $upcomingSchedule
            ? Enrollment::where('training_schedule_id', $upcomingSchedule->id)->count() : 0;
        $upcomingTotalPaid     = $upcomingSchedule
            ? Enrollment::where('training_schedule_id', $upcomingSchedule->id)
                ->where('payment_status', 'Paid')->count() : 0;

        // IL status breakdown
        $ilOpen       = TrainingSchedule::where('status', 'Open')->count();
        $ilCompleted  = TrainingSchedule::where('status', 'Completed')->count();
        $ilPostponed  = TrainingSchedule::whereIn('status', ['Postponed', 'Cancelled'])->count();

        // Recent manual enrollments (last 6)
        $recentManualEnrollments = Enrollment::with(['trainingSchedule.course'])
            ->latest()->limit(6)->get();

        // ── eLearning Stats ─────────────────────────────────
        $elCourses       = Course::where('course_type', 'elearning')->count();
        $elCoursesActive = Course::where('course_type', 'elearning')->where('status', 1)->count();

        $elEnrollments   = ElearningEnrollment::whereYear('created_at', $currentYear)->count();
        $elInProgress    = ElearningEnrollment::whereYear('created_at', $currentYear)
            ->where('completion_status', '!=', 'completed')
            ->where('access_status', 'unlocked')
            ->count();
        $elCompleted     = ElearningEnrollment::whereYear('created_at', $currentYear)
            ->where('completion_status', 'completed')->count();
        $elNotStarted    = ElearningEnrollment::whereYear('created_at', $currentYear)
            ->where('completion_status', 'not_started')->count();
        $elEligibleCerts = ElearningEnrollment::whereYear('created_at', $currentYear)
            ->where('certificate_status', 'eligible')->count();
        $elIssuedCerts   = ElearningEnrollment::whereYear('created_at', $currentYear)
            ->where('certificate_status', 'issued')->count();

        // Recent eLearning enrollments (last 6)
        $recentElEnrollments = ElearningEnrollment::with('course')
            ->latest()->limit(6)->get();

        return view('dashboard', compact(
            'currentYear',
            // instructor-led
            'manualCourses',
            'totalSchedules',
            'totalEnrollmentsThisYear',
            'totalConfirmed',
            'attendanceCompleted',
            'totalPaid',
            'totalPaidAmount',
            'totalDue',
            'totalDueAmount',
            'pendingPayments',
            'certificatesIssued',
            'upcomingSchedule',
            'upcomingTotalEnrolled',
            'upcomingTotalPaid',
            'upcomingSchedules',
            'recentManualEnrollments',
            'ilOpen',
            'ilCompleted',
            'ilPostponed',
            // elearning
            'elCourses',
            'elCoursesActive',
            'elEnrollments',
            'elInProgress',
            'elCompleted',
            'elNotStarted',
            'elEligibleCerts',
            'elIssuedCerts',
            'recentElEnrollments',
        ));
    }
}
