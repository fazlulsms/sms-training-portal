<?php

namespace App\Http\Controllers;

use App\Mail\RegistrationConfirmed;
use App\Models\Enrollment;
use App\Models\TrainingSchedule;
use App\Services\AutoInvoiceService;
use App\Services\PaymentConfirmationService;
use App\Services\TrainingNotificationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EnrollmentController extends Controller
{
    public function index(Request $request)
    {
        $q                 = $request->input('search', $request->input('q'));
        $paymentStatus     = $request->input('payment_status');
        $attendanceStatus  = $request->input('attendance_status');
        $completionStatus  = $request->input('completion_status');
        $courseId          = $request->input('course_id');

        $enrollments = Enrollment::with('trainingSchedule.course')
            ->when($q, fn($query) => $query->where(fn($sub) =>
                $sub->where('full_name', 'like', "%$q%")
                    ->orWhere('email', 'like', "%$q%")
                    ->orWhere('company', 'like', "%$q%")
                    ->orWhereHas('trainingSchedule', fn($ts) =>
                        $ts->where('batch_code', 'like', "%$q%")
                           ->orWhereHas('course', fn($c) => $c->where('name', 'like', "%$q%"))
                    )
            ))
            ->when($paymentStatus, fn($query) => $query->where('payment_status', $paymentStatus))
            ->when($attendanceStatus, fn($query) => $query->where('attendance_status', $attendanceStatus))
            ->when($completionStatus, fn($query) => $query->where('completion_status', $completionStatus))
            ->when($courseId, fn($query) => $query->whereHas('trainingSchedule', fn($ts) => $ts->where('course_id', $courseId)))
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('enrollments.index', compact('enrollments'));
    }

    public function exportCsv(Request $request)
    {
        $q                = $request->input('search', $request->input('q'));
        $paymentStatus    = $request->input('payment_status');
        $attendanceStatus = $request->input('attendance_status');
        $completionStatus = $request->input('completion_status');

        $enrollments = Enrollment::with('trainingSchedule.course')
            ->when($q, fn($query) => $query->where(fn($sub) =>
                $sub->where('full_name', 'like', "%$q%")
                    ->orWhere('email', 'like', "%$q%")
                    ->orWhere('company', 'like', "%$q%")
                    ->orWhereHas('trainingSchedule', fn($ts) =>
                        $ts->where('batch_code', 'like', "%$q%")
                           ->orWhereHas('course', fn($c) => $c->where('name', 'like', "%$q%"))
                    )
            ))
            ->when($paymentStatus, fn($query) => $query->where('payment_status', $paymentStatus))
            ->when($attendanceStatus, fn($query) => $query->where('attendance_status', $attendanceStatus))
            ->when($completionStatus, fn($query) => $query->where('completion_status', $completionStatus))
            ->orderBy('created_at', 'desc')
            ->get();

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="enrollments_' . now()->format('Ymd') . '.csv"',
        ];

        return response()->stream(function () use ($enrollments) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Name', 'Email', 'Company', 'Course', 'Batch', 'Mode', 'Payment', 'Attendance', 'Completion', 'Enrolled On']);
            foreach ($enrollments as $e) {
                fputcsv($handle, [
                    $e->id,
                    $e->full_name,
                    $e->email,
                    $e->company,
                    $e->trainingSchedule->course->name ?? '',
                    $e->trainingSchedule->batch_code ?? '',
                    $e->selected_mode,
                    $e->payment_status,
                    $e->attendance_status,
                    $e->completion_status,
                    $e->created_at->format('d M Y'),
                ]);
            }
            fclose($handle);
        }, 200, $headers);
    }

    public function create()
    {
        $schedules = TrainingSchedule::with('course')
            ->orderByRaw("FIELD(status, 'Open', 'Closed', 'Postponed', 'Completed', 'Cancelled')")
            ->orderBy('start_date', 'asc')
            ->get();

        return view('enrollments.create', compact('schedules'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'training_schedule_id' => 'required|exists:training_schedules,id',
            'full_name' => 'required|string|max:255',
        ]);

        $enrollment = Enrollment::create([
            'training_schedule_id' => $request->training_schedule_id,
            'full_name' => $request->full_name,
            'email' => $request->email,
            'company' => $request->company,
            'designation' => $request->designation,
            'country' => $request->country,
            'country_code' => $request->country_code,
            'mobile_number' => $request->mobile_number,
            'full_address' => $request->full_address,
            'selected_mode' => $request->selected_mode,
            'applied_fee' => $request->applied_fee,
            'payment_status' => $request->payment_status ?? 'Pending',
            'amount_received' => $request->amount_received ?? 0,
            'payment_method' => $request->payment_method,
            'attendance_status' => $request->attendance_status ?? 'Pending',
            'completion_status' => $request->completion_status ?? 'Pending',
            'registration_status' => 'Confirmed',
            'remarks' => $request->remarks,
        ]);

        // Auto-invoice + registration email
        try {
            $enrollment->load('trainingSchedule.course');
            $invoice = AutoInvoiceService::forIltEnrollment($enrollment);
            TrainingNotificationService::iltRegistrationCompleted($enrollment, $invoice);
        } catch (\Throwable $e) {
            Log::error('AutoInvoice/RegistrationConfirmed failed (ILT admin)', [
                'enrollment_id' => $enrollment->id, 'error' => $e->getMessage(),
            ]);
        }

        // Admin alert
        TrainingNotificationService::adminNewRegistration($enrollment);

        return redirect('/enrollments')->with('success', 'Enrollment added successfully');
    }

    public function edit($id)
    {
        $enrollment = Enrollment::findOrFail($id);

        $schedules = TrainingSchedule::with('course')
            ->orderByRaw("FIELD(status, 'Open', 'Closed', 'Postponed', 'Completed', 'Cancelled')")
            ->orderBy('start_date', 'asc')
            ->get();

        return view('enrollments.edit', compact('enrollment', 'schedules'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'training_schedule_id' => 'required|exists:training_schedules,id',
            'full_name' => 'required|string|max:255',
        ]);

        $enrollment = Enrollment::findOrFail($id);

        // Capture old statuses before update
        $wasAlreadyPaid     = PaymentConfirmationService::isPaidStatus($enrollment->payment_status);
        $oldAttendance      = $enrollment->attendance_status;
        $oldCompletion      = $enrollment->completion_status;
        $oldCertStatus      = $enrollment->certificate_number;

        $enrollment->update([
            'training_schedule_id' => $request->training_schedule_id,
            'full_name'            => $request->full_name,
            'email'                => $request->email,
            'company'              => $request->company,
            'designation'          => $request->designation,
            'country'              => $request->country,
            'country_code'         => $request->country_code,
            'mobile_number'        => $request->mobile_number,
            'full_address'         => $request->full_address,
            'selected_mode'        => $request->selected_mode,
            'applied_fee'          => $request->applied_fee,
            // Payment fields managed via Invoice → 💳 Pay only
            // Attendance/Completion managed via Trainer Portal only
            'remarks'              => $request->remarks,
        ]);

        $fresh   = $enrollment->fresh();
        // Payment is managed via Invoice area — check fresh status (could have been synced there)
        $nowPaid = PaymentConfirmationService::isPaidStatus($fresh->payment_status);

        // Payment confirmed (only if synced externally between last save and now — safety net)
        if ($nowPaid && !$wasAlreadyPaid) {
            try {
                PaymentConfirmationService::handleIltEnrollment($fresh);
            } catch (\Throwable $e) {
                Log::error('PaymentConfirmation failed (ILT update)', [
                    'enrollment_id' => $enrollment->id, 'error' => $e->getMessage(),
                ]);
            }
        }

        // Attendance marked
        $newAttendance = $request->attendance_status ?? '';
        if (in_array($newAttendance, ['Attended', 'Completed']) && !in_array($oldAttendance, ['Attended', 'Completed'])) {
            TrainingNotificationService::attendanceCompleted($fresh);
        }

        // Course completed
        $newCompletion = $request->completion_status ?? '';
        if ($newCompletion === 'Completed' && $oldCompletion !== 'Completed') {
            TrainingNotificationService::courseCompleted($fresh);
        }

        // Certificate issued (certificate_number just assigned)
        $newCertNumber = $fresh->certificate_number;
        if ($newCertNumber && !$oldCertStatus) {
            TrainingNotificationService::certificateIssued($fresh);
        }

        return redirect('/enrollments')->with('success', 'Enrollment updated successfully');
    }

    public function delete($id)
    {
        Enrollment::findOrFail($id)->delete();

        return redirect('/enrollments')->with('success', 'Enrollment deleted successfully');
    }

    public function certificate($id)
    {
        $enrollment = Enrollment::with('trainingSchedule.course')->findOrFail($id);

        return view('enrollments.certificate', compact('enrollment'));
    }

    public function certificatePdf($id)
    {
        $enrollment = Enrollment::with('trainingSchedule.course')->findOrFail($id);

        $pdf = Pdf::loadView('enrollments.certificate_pdf', compact('enrollment'))
            ->setPaper('a4', 'landscape');

        return $pdf->download(($enrollment->certificate_number ?? 'certificate') . '.pdf');
    }

    public function verifyForm()
    {
        return view('verify_certificate');
    }

    public function verifyCertificate(Request $request)
    {
        $enrollment = Enrollment::where('certificate_number', $request->certificate_number)
            ->where('full_name', 'LIKE', '%' . $request->full_name . '%')
            ->first();

        return view('verify_result', compact('enrollment'));
    }

    public function verifyByNumber($certificate_number)
    {
        $enrollment = Enrollment::where('certificate_number', $certificate_number)->first();

        return view('verify_result', compact('enrollment'));
    }

    public function publicCreate($schedule_id)
    {
        $schedule = TrainingSchedule::with('course')->findOrFail($schedule_id);

        return view('enrollments.public-create', compact('schedule'));
    }

    public function publicStore(Request $request, $schedule_id)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'selected_mode' => 'required|string|max:50',
        ]);

        $schedule = TrainingSchedule::with('course')->findOrFail($schedule_id);

        $appliedFee = $request->selected_mode == 'Physical'
            ? $schedule->physical_fee
            : $schedule->online_fee;

        $enrollment = Enrollment::create([
            'training_schedule_id' => $schedule->id,
            'full_name' => $request->full_name,
            'email' => $request->email,
            'company' => $request->company,
            'designation' => $request->designation,
            'country' => $request->country,
            'country_code' => $request->country_code,
            'mobile_number' => $request->mobile_number,
            'full_address' => $request->full_address,
            'selected_mode' => $request->selected_mode,
            'applied_fee' => $appliedFee,
            'payment_status' => 'Pending',
            'amount_received' => 0,
            'attendance_status' => 'Pending',
            'completion_status' => 'Pending',
            'registration_status' => 'Pending',
            'remarks' => 'Registered through public form',
        ]);

        // Auto-invoice + registration email + admin alert
        try {
            $enrollment->setRelation('trainingSchedule', $schedule);
            $invoice = AutoInvoiceService::forIltEnrollment($enrollment, $schedule);
            TrainingNotificationService::iltRegistrationCompleted($enrollment, $invoice);
        } catch (\Throwable $e) {
            Log::error('AutoInvoice/RegistrationConfirmed failed (ILT public)', [
                'enrollment_id' => $enrollment->id, 'error' => $e->getMessage(),
            ]);
        }
        TrainingNotificationService::adminNewRegistration($enrollment);

        return redirect('/register-training/' . $schedule->id)
            ->with('success', 'Registration submitted successfully. Our team will contact you soon.');
    }
}