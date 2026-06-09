<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\TrainingSchedule;
use App\Mail\TrainingMail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CertificateController extends Controller
{
    // ── Eligible attendance values ────────────────────────────────────────
    private const ELIGIBLE_ATTENDANCE = ['Present', 'Attended', 'Completed', 'Partial', 'Late'];

    // ── Available certificate templates ───────────────────────────────────
    public const TEMPLATES = [
        'attendance'  => 'Certificate of Attendance',
        'completion'  => 'Certificate of Completion',
        'auditor'     => 'Auditor / Lead Auditor Training Certificate',
    ];

    // ── Templates that render in portrait orientation ─────────────────────
    private const PORTRAIT_TEMPLATES = []; // auditor is landscape

    // ─────────────────────────────────────────────────────────────────────
    // INDEX — main list with all filters
    // ─────────────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $courses   = Course::orderBy('name')->get();
        $schedules = TrainingSchedule::with('course')->orderBy('start_date', 'desc')->get();

        // Build query only when at least one filter is active
        $hasFilter = $request->anyFilled(['q','course_id','schedule_id','status']);

        $enrollments      = collect();
        $selectedSchedule = null;
        $totalEligible    = 0;
        $totalGenerated   = 0;

        if ($hasFilter) {
            $q          = $request->input('q');
            $courseId   = $request->input('course_id');
            $scheduleId = $request->input('schedule_id');
            $status     = $request->input('status', 'eligible'); // default: eligible

            $selectedSchedule = $scheduleId;

            $query = Enrollment::with('trainingSchedule.course')
                ->when($scheduleId, fn($qb) => $qb->where('training_schedule_id', $scheduleId))
                ->when($courseId && !$scheduleId, fn($qb) => $qb->whereHas('trainingSchedule', fn($ts) =>
                    $ts->where('course_id', $courseId)
                ))
                ->when($q, fn($qb) => $qb->where(fn($sub) =>
                    $sub->where('full_name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhere('company', 'like', "%{$q}%")
                        ->orWhere('certificate_number', 'like', "%{$q}%")
                ));

            // Eligibility gate: attendance + completion
            $eligibleQuery = (clone $query)
                ->whereIn('attendance_status', self::ELIGIBLE_ATTENDANCE)
                ->where('completion_status', 'Completed');

            $totalEligible = $eligibleQuery->count();
            $totalGenerated = (clone $eligibleQuery)->whereNotNull('certificate_number')->count();

            $enrollments = match($status) {
                'generated'     => (clone $eligibleQuery)->whereNotNull('certificate_number')->orderBy('certificate_issue_date','desc')->get(),
                'not_generated' => (clone $eligibleQuery)->whereNull('certificate_number')->orderBy('full_name')->get(),
                default         => $eligibleQuery->orderBy('full_name')->get(), // 'eligible' = all eligible
            };
        }

        return view('certificates.index', compact(
            'courses', 'schedules', 'enrollments',
            'selectedSchedule', 'totalEligible', 'totalGenerated'
        ));
    }

    // ─────────────────────────────────────────────────────────────────────
    // FILTER (POST redirect to GET with query params)
    // ─────────────────────────────────────────────────────────────────────
    public function filter(Request $request)
    {
        return redirect()->route('certificates.index', array_filter([
            'schedule_id' => $request->schedule_id,
            'course_id'   => $request->course_id,
            'q'           => $request->q,
            'status'      => $request->status,
        ]));
    }

    // ─────────────────────────────────────────────────────────────────────
    // SHOW BY SCHEDULE (legacy URL kept working)
    // ─────────────────────────────────────────────────────────────────────
    public function showBySchedule($id)
    {
        return redirect()->route('certificates.index', ['schedule_id' => $id, 'status' => 'eligible']);
    }

    // ─────────────────────────────────────────────────────────────────────
    // INDIVIDUAL — generate form
    // ─────────────────────────────────────────────────────────────────────
    public function generateForm($id)
    {
        $enrollment = Enrollment::with('trainingSchedule.course')->findOrFail($id);
        $templates  = self::TEMPLATES;
        return view('certificates.generate', compact('enrollment', 'templates'));
    }

    // ─────────────────────────────────────────────────────────────────────
    // INDIVIDUAL — generate (save)
    // ─────────────────────────────────────────────────────────────────────
    public function generate(Request $request, $id)
    {
        $request->validate([
            'certificate_template'   => 'required|string',
            'certificate_issue_date' => 'required|date',
        ]);

        $enrollment = Enrollment::findOrFail($id);

        if ($enrollment->certificate_number) {
            return back()->with('error', 'Certificate already generated for this participant.');
        }

        $certNumber = $this->generateCertNumber();

        $enrollment->update([
            'certificate_number'      => $certNumber,
            'certificate_issue_date'  => $request->certificate_issue_date,
            'certificate_generated'   => 1,
            'certificate_template'    => $request->certificate_template,
            'completion_status'       => 'Completed',
            'certificate_generated_by'=> Auth::user()->name ?? 'Admin',
            'certificate_generated_at'=> now(),
        ]);

        // Notify via TrainingNotificationService if possible
        try {
            \App\Services\TrainingNotificationService::certificateIssued($enrollment->fresh());
        } catch (\Throwable $e) {
            Log::warning('Certificate notification failed', ['id' => $enrollment->id, 'error' => $e->getMessage()]);
        }

        return redirect()->route('certificates.index', [
            'schedule_id' => $enrollment->training_schedule_id,
            'status'      => 'eligible',
        ])->with('success', 'Certificate generated successfully.');
    }

    // ─────────────────────────────────────────────────────────────────────
    // BULK GENERATE
    // ─────────────────────────────────────────────────────────────────────
    public function bulkGenerate(Request $request)
    {
        $request->validate([
            'enrollment_ids'         => 'required|array|min:1',
            'enrollment_ids.*'       => 'integer|exists:enrollments,id',
            'certificate_template'   => 'required|string',
            'certificate_issue_date' => 'required|date',
        ]);

        $template   = $request->certificate_template;
        $issueDate  = $request->certificate_issue_date;
        $scheduleId = $request->schedule_id;
        $generated  = 0;
        $skipped    = 0;
        $emailed    = 0;
        $emailFailed= 0;

        foreach ($request->enrollment_ids as $enrollmentId) {
            $enrollment = Enrollment::with('trainingSchedule.course')->find($enrollmentId);
            if (!$enrollment) continue;

            // Skip if already has certificate
            if ($enrollment->certificate_number) {
                $skipped++;
                continue;
            }

            // Skip if not eligible
            if (!$this->isEligible($enrollment)) {
                $skipped++;
                continue;
            }

            $certNumber = $this->generateCertNumber();

            $enrollment->update([
                'certificate_number'      => $certNumber,
                'certificate_issue_date'  => $issueDate,
                'certificate_generated'   => 1,
                'certificate_template'    => $template,
                'completion_status'       => 'Completed',
                'certificate_generated_by'=> Auth::user()->name ?? 'Admin',
                'certificate_generated_at'=> now(),
            ]);

            $generated++;

            // Auto-send email with PDF attachment
            if ($enrollment->email) {
                try {
                    $this->sendCertificateEmail($enrollment->fresh());
                    $emailed++;
                } catch (\Throwable $e) {
                    Log::error('Bulk certificate email failed', [
                        'enrollment_id' => $enrollment->id,
                        'error'         => $e->getMessage(),
                    ]);
                    $emailFailed++;
                }
            }
        }

        $summary = "Certificates generated: {$generated}. Skipped (already issued or ineligible): {$skipped}. Emails sent: {$emailed}. Failed emails: {$emailFailed}.";

        $redirect = $scheduleId
            ? ['schedule_id' => $scheduleId, 'status' => 'eligible']
            : [];

        return redirect()->route('certificates.index', $redirect)
            ->with('bulk_result', $summary);
    }

    // ─────────────────────────────────────────────────────────────────────
    // EMAIL — send/resend certificate to participant
    // ─────────────────────────────────────────────────────────────────────
    public function emailCertificate(Request $request, $id)
    {
        $enrollment = Enrollment::with('trainingSchedule.course')->findOrFail($id);

        if (!$enrollment->certificate_number) {
            return back()->with('error', 'No certificate generated yet for this participant.');
        }

        if (!$enrollment->email) {
            return back()->with('error', 'No email address on record for this participant.');
        }

        try {
            $this->sendCertificateEmail($enrollment);
            return back()->with('success', "Certificate email sent to {$enrollment->email}.");
        } catch (\Throwable $e) {
            Log::error('Certificate email send failed', ['id' => $id, 'error' => $e->getMessage()]);
            return back()->with('error', 'Email sending failed: ' . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────────────────────────────
    // VIEW — certificate HTML
    // ─────────────────────────────────────────────────────────────────────
    public function view($id)
    {
        $enrollment = Enrollment::with('trainingSchedule.course')->findOrFail($id);
        $viewName   = $this->templateView($enrollment->certificate_template ?? 'attendance');
        return view($viewName, compact('enrollment'));
    }

    // ─────────────────────────────────────────────────────────────────────
    // PDF — download certificate PDF
    // ─────────────────────────────────────────────────────────────────────
    public function pdf($id)
    {
        $enrollment = Enrollment::with('trainingSchedule.course')->findOrFail($id);

        $template    = $enrollment->certificate_template ?? 'attendance';
        $viewName    = $this->templateView($template);
        $orientation = $this->templateOrientation($template);

        $pdf = Pdf::loadView($viewName, compact('enrollment'))
            ->setPaper('a4', $orientation)
            ->setOption(['isRemoteEnabled' => true, 'dpi' => 150]);

        $safeFileName = str_replace(['/', '\\'], '-', $enrollment->certificate_number ?? 'certificate');
        return $pdf->download($safeFileName . '.pdf');
    }

    // ─────────────────────────────────────────────────────────────────────
    // DELETE — revoke certificate
    // ─────────────────────────────────────────────────────────────────────
    public function delete($id)
    {
        $enrollment = Enrollment::findOrFail($id);
        $scheduleId = $enrollment->training_schedule_id;

        $enrollment->update([
            'certificate_number'      => null,
            'certificate_issue_date'  => null,
            'certificate_generated'   => 0,
            'certificate_template'    => null,
            'certificate_email_sent'  => false,
            'certificate_email_sent_at'=> null,
            'certificate_generated_by'=> null,
            'certificate_generated_at'=> null,
        ]);

        return redirect()->route('certificates.index', ['schedule_id' => $scheduleId, 'status' => 'eligible'])
            ->with('success', 'Certificate revoked successfully.');
    }

    // ─────────────────────────────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────────────────────────────

    // ── Resolve Blade view name for a template key ───────────────────────
    private function templateView(string $template): string
    {
        return match ($template) {
            'auditor'  => 'certificates.auditor',
            default    => 'certificates.attendance',
        };
    }

    // ── Resolve PDF orientation for a template key ────────────────────────
    private function templateOrientation(string $template): string
    {
        return in_array($template, self::PORTRAIT_TEMPLATES) ? 'portrait' : 'landscape';
    }

    private function isEligible(Enrollment $enrollment): bool
    {
        // Base attendance + completion check
        if (!in_array($enrollment->attendance_status, self::ELIGIBLE_ATTENDANCE)
            || $enrollment->completion_status !== 'Completed') {
            return false;
        }

        // If the schedule has an exam assigned, participant must have passed
        $enrollment->loadMissing('trainingSchedule');
        $assignment = \App\Models\TrainingQuestionAssignment::where(
            'training_schedule_id', $enrollment->training_schedule_id
        )->first();

        if ($assignment) {
            $result = $enrollment->testResult;
            if (!$result || !$result->certificate_eligible) {
                return false;
            }
        }

        return true;
    }

    private function generateCertNumber(): string
    {
        $year = date('Y');

        $last = Enrollment::whereNotNull('certificate_number')
            ->where('certificate_number', 'like', 'SMS-TC-%')
            ->orderByRaw("CAST(SUBSTRING_INDEX(certificate_number, '-', -1) AS UNSIGNED) DESC")
            ->lockForUpdate()
            ->first();

        $next = 1;
        if ($last && preg_match('/(\d+)$/', $last->certificate_number, $m)) {
            $next = (int)$m[1] + 1;
        }

        return 'SMS-TC-' . $year . '-' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    private function sendCertificateEmail(Enrollment $enrollment): void
    {
        $courseName = $enrollment->trainingSchedule?->course?->name ?? 'Training Programme';
        $certNumber = $enrollment->certificate_number;

        // Build PDF attachment
        $template    = $enrollment->certificate_template ?? 'attendance';
        $viewName    = $this->templateView($template);
        $orientation = $this->templateOrientation($template);

        $pdf        = Pdf::loadView($viewName, compact('enrollment'))
            ->setPaper('a4', $orientation)
            ->setOption(['isRemoteEnabled' => true, 'dpi' => 150]);
        $pdfData    = $pdf->output();
        $fileName   = str_replace(['/', '\\'], '-', $certNumber ?? 'certificate') . '.pdf';

        $mail = new TrainingMail(
            'Certificate Issued – ' . $courseName,
            'emails.participant.certificate-issued',
            [
                'enrollment' => $enrollment,
                'courseName' => $courseName,
                'certNumber' => $certNumber,
                'loginUrl'   => url('/verify-certificate/' . $certNumber),
            ],
            [['name' => $fileName, 'data' => $pdfData]],
        );

        Mail::to($enrollment->email)->send($mail);

        $enrollment->update([
            'certificate_email_sent'    => true,
            'certificate_email_sent_at' => now(),
        ]);
    }
}
