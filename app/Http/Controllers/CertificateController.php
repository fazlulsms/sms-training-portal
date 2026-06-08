<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Enrollment;
use App\Models\TrainingSchedule;
use Barryvdh\DomPDF\Facade\Pdf;

class CertificateController extends Controller
{
    public function index()
    {
        $schedules = TrainingSchedule::with('course')
            ->orderBy('start_date', 'desc')
            ->get();

        $enrollments = collect();

        return view('certificates.index', compact('schedules', 'enrollments'));
    }

    public function filter(Request $request)
    {
        return redirect('/certificates/schedule/' . $request->training_schedule_id);
    }

    public function showBySchedule($id)
    {
        $schedules = TrainingSchedule::with('course')
            ->orderBy('start_date', 'desc')
            ->get();

        $selectedSchedule = $id;

        $enrollments = Enrollment::with('trainingSchedule.course')
            ->where('training_schedule_id', $id)
            ->where('completion_status', 'Completed')
            ->get();

        return view('certificates.index', compact('schedules', 'enrollments', 'selectedSchedule'));
    }

    public function generateForm($id)
    {
        $enrollment = Enrollment::with('trainingSchedule.course')->findOrFail($id);

        return view('certificates.generate', compact('enrollment'));
    }

    public function generate(Request $request, $id)
    {
        $enrollment = Enrollment::findOrFail($id);

        $year = date('Y');

        $last = Enrollment::whereNotNull('certificate_number')
            ->orderBy('id', 'desc')
            ->first();

        if ($last && preg_match('/(\d+)$/', $last->certificate_number, $matches)) {
            $nextNumber = intval($matches[1]) + 1;
        } else {
            $nextNumber = 1;
        }

        $certificateNo = 'SMS-TC-' . $year . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        $enrollment->update([
            'certificate_number' => $certificateNo,
            'certificate_issue_date' => $request->certificate_issue_date,
            'certificate_generated' => 1,
            'completion_status' => 'Completed',
        ]);

        return redirect('/certificates/schedule/' . $enrollment->training_schedule_id)
            ->with('success', 'Certificate generated successfully');
    }

    public function view($id)
    {
        $enrollment = Enrollment::with('trainingSchedule.course')->findOrFail($id);

        return view('certificates.attendance', compact('enrollment'));
    }

   public function pdf($id)
{
    $enrollment = Enrollment::with('trainingSchedule.course')->findOrFail($id);

    $pdf = Pdf::loadView('certificates.attendance', compact('enrollment'))
        ->setPaper('a4', 'portrait')
        ->setOption(['isRemoteEnabled' => true]);

    $safeFileName = str_replace(['/', '\\'], '-', $enrollment->certificate_number ?? 'certificate');

    return $pdf->download($safeFileName . '.pdf');
}

    public function delete($id)
    {
        $enrollment = Enrollment::findOrFail($id);

        $scheduleId = $enrollment->training_schedule_id;

        $enrollment->update([
            'certificate_number' => null,
            'certificate_issue_date' => null,
            'certificate_generated' => 0,
        ]);

        return redirect('/certificates/schedule/' . $scheduleId)
            ->with('success', 'Certificate deleted successfully');
    }
}