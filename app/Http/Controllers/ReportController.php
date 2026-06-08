<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TrainingSchedule;
use App\Models\Enrollment;
use App\Models\Course;

class ReportController extends Controller
{
    public function training(Request $request)
    {
        return view('reports.training');
    }

    public function participants(Request $request)
{
    $courses = Course::orderBy('name')->get();

    $schedules = TrainingSchedule::with('course')
        ->orderBy('start_date', 'desc')
        ->get();

    $query = Enrollment::with('trainingSchedule.course');

    if ($request->filled('from_date')) {
        $query->whereDate('created_at', '>=', $request->from_date);
    }

    if ($request->filled('to_date')) {
        $query->whereDate('created_at', '<=', $request->to_date);
    }

    if ($request->filled('course_id')) {
        $query->whereHas('trainingSchedule', function ($q) use ($request) {
            $q->where('course_id', $request->course_id);
        });
    }

    if ($request->filled('training_schedule_id')) {
        $query->where('training_schedule_id', $request->training_schedule_id);
    }

    if ($request->filled('payment_status')) {
        $query->where('payment_status', $request->payment_status);
    }

    if ($request->filled('completion_status')) {
        $query->where('completion_status', $request->completion_status);
    }

    $participants = $query->orderBy('id', 'desc')->paginate(20)->withQueryString();

    $totalRegistered = $query->count();
    $totalCompleted = (clone $query)->where('completion_status', 'Completed')->count();
    $totalPaid = (clone $query)->where('payment_status', 'Paid')->count();
    $totalCertificates = (clone $query)->where('certificate_generated', 1)->count();

    return view('reports.participants', compact(
        'courses',
        'schedules',
        'participants',
        'totalRegistered',
        'totalCompleted',
        'totalPaid',
        'totalCertificates'
    ));
}

   public function certificates(Request $request)
{
    $courses = Course::orderBy('name')->get();

    $schedules = TrainingSchedule::with('course')
        ->orderBy('start_date', 'desc')
        ->get();

    $query = Enrollment::with('trainingSchedule.course')
        ->where('completion_status', 'Completed');

    if ($request->filled('from_date')) {
        $query->whereDate('certificate_issue_date', '>=', $request->from_date);
    }

    if ($request->filled('to_date')) {
        $query->whereDate('certificate_issue_date', '<=', $request->to_date);
    }

    if ($request->filled('course_id')) {
        $query->whereHas('trainingSchedule', function ($q) use ($request) {
            $q->where('course_id', $request->course_id);
        });
    }

    if ($request->filled('training_schedule_id')) {
        $query->where('training_schedule_id', $request->training_schedule_id);
    }

    if ($request->filled('certificate_status')) {
        if ($request->certificate_status == 'Issued') {
            $query->where('certificate_generated', 1);
        }

        if ($request->certificate_status == 'Pending') {
            $query->where(function ($q) {
                $q->whereNull('certificate_generated')
                  ->orWhere('certificate_generated', 0);
            });
        }
    }

    $baseQuery = clone $query;

    $certificates = $query->orderBy('id', 'desc')->paginate(20)->withQueryString();

    $totalCompleted = (clone $baseQuery)->count();
    $totalIssued = (clone $baseQuery)->where('certificate_generated', 1)->count();
    $totalPending = (clone $baseQuery)->where(function ($q) {
        $q->whereNull('certificate_generated')
          ->orWhere('certificate_generated', 0);
    })->count();

    return view('reports.certificates', compact(
        'courses',
        'schedules',
        'certificates',
        'totalCompleted',
        'totalIssued',
        'totalPending'
    ));
}

   public function payments(Request $request)
{
    $courses = Course::orderBy('name')->get();

    $schedules = TrainingSchedule::with('course')
        ->orderBy('start_date', 'desc')
        ->get();

    $query = Enrollment::with('trainingSchedule.course');

    if ($request->filled('from_date')) {
        $query->whereDate('created_at', '>=', $request->from_date);
    }

    if ($request->filled('to_date')) {
        $query->whereDate('created_at', '<=', $request->to_date);
    }

    if ($request->filled('course_id')) {
        $query->whereHas('trainingSchedule', function ($q) use ($request) {
            $q->where('course_id', $request->course_id);
        });
    }

    if ($request->filled('training_schedule_id')) {
        $query->where('training_schedule_id', $request->training_schedule_id);
    }

    if ($request->filled('payment_status')) {
        $query->where('payment_status', $request->payment_status);
    }

    if ($request->filled('payment_method')) {
        $query->where('payment_method', $request->payment_method);
    }

    $baseQuery = clone $query;

    $payments = $query->orderBy('id', 'desc')->paginate(20)->withQueryString();

    $totalPayable = (clone $baseQuery)->sum('applied_fee');
    $totalCollected = (clone $baseQuery)->sum('amount_received');

    $totalDueAmount = max($totalPayable - $totalCollected, 0);

    $totalPaidCount = (clone $baseQuery)->where('payment_status', 'Paid')->count();

    $cashAmount = (clone $baseQuery)->where('payment_method', 'Cash')->sum('amount_received');
    $bankAmount = (clone $baseQuery)->where('payment_method', 'Bank Transfer')->sum('amount_received');
    $mobileAmount = (clone $baseQuery)->where('payment_method', 'Mobile Banking')->sum('amount_received');

    return view('reports.payments', compact(
        'courses',
        'schedules',
        'payments',
        'totalPayable',
        'totalCollected',
        'totalDueAmount',
        'totalPaidCount',
        'cashAmount',
        'bankAmount',
        'mobileAmount'
    ));
}
}