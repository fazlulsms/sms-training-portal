<?php

namespace App\Http\Controllers\Corporate;

use App\Http\Controllers\Controller;
use App\Models\CorporateSession;
use App\Models\CorporateParticipant;
use App\Models\CorporateCertificate;
use App\Models\CorporateAttendance;
use Illuminate\Http\Request;
use ZipArchive;

class CertificateController extends Controller
{
    /** List / workflow entry for a session */
    public function index(Request $request, CorporateSession $session)
    {
        $session->load('project');

        $q      = $request->input('q');
        $status = $request->input('status'); // attendance status filter

        $participants = $session->participants()
            ->with([
                'attendance' => fn($q) => $q->where('corporate_session_id', $session->id),
                'certificate',
            ])
            ->when($q, fn($query) => $query->where(fn($sub) =>
                $sub->where('participant_name', 'like', "%$q%")
                    ->orWhereHas('certificate', fn($c) => $c->where('certificate_number', 'like', "%$q%"))
            ))
            ->orderBy('participant_name')
            ->get();

        // Apply attendance status filter on the collection (since attendance is eager-loaded)
        if ($status) {
            $participants = $participants->filter(fn($p) => $p->attendance?->status === $status)->values();
        }

        $eligible     = $participants->filter(fn($p) => $p->attendance?->status === 'Present');
        $alreadyDone  = $participants->filter(fn($p) => $p->certificate !== null);

        return view('corporate.certificates.index', compact('session', 'participants', 'eligible', 'alreadyDone'));
    }

    /** Generate certificates for ALL eligible (Present) participants in a session */
    public function generateBulk(Request $request, CorporateSession $session)
    {
        $presentIds = CorporateAttendance::where('corporate_session_id', $session->id)
            ->where('status', 'Present')
            ->pluck('corporate_participant_id');

        $generated = 0;
        foreach ($presentIds as $pid) {
            // Skip if already has a certificate
            if (CorporateCertificate::where('corporate_participant_id', $pid)
                                     ->where('corporate_session_id', $session->id)
                                     ->exists()) {
                continue;
            }

            CorporateCertificate::create([
                'corporate_project_id'     => $session->corporate_project_id,
                'corporate_session_id'      => $session->id,
                'corporate_participant_id'  => $pid,
                'certificate_number'        => CorporateCertificate::generateNumber(),
                'issue_date'                => now()->toDateString(),
            ]);
            $generated++;
        }

        $session->update(['certificates_generated' => true]);

        return redirect()->route('corporate.sessions.certificates.index', $session)
                         ->with('success', "$generated certificate(s) generated.");
    }

    /** Generate / regenerate certificate for a single participant */
    public function generateSingle(Request $request, CorporateSession $session, CorporateParticipant $participant)
    {
        $attendance = CorporateAttendance::where('corporate_session_id', $session->id)
            ->where('corporate_participant_id', $participant->id)
            ->first();

        if (!$attendance || $attendance->status !== 'Present') {
            return redirect()->route('corporate.sessions.certificates.index', $session)
                             ->with('error', 'Participant must be marked Present before generating a certificate.');
        }

        $cert = CorporateCertificate::firstOrCreate(
            ['corporate_session_id' => $session->id, 'corporate_participant_id' => $participant->id],
            [
                'corporate_project_id'    => $session->corporate_project_id,
                'certificate_number'       => CorporateCertificate::generateNumber(),
                'issue_date'               => now()->toDateString(),
            ]
        );

        return redirect()->route('corporate.sessions.certificates.index', $session)
                         ->with('success', 'Certificate ' . $cert->certificate_number . ' generated.');
    }

    /** View a certificate (HTML print page) */
    public function view(CorporateCertificate $certificate)
    {
        $certificate->load('participant', 'session.project');
        return view('corporate.certificates.view', compact('certificate'));
    }

    /** Download all certificates for a session as ZIP */
    public function downloadZip(CorporateSession $session)
    {
        $certificates = CorporateCertificate::where('corporate_session_id', $session->id)
            ->with('participant')
            ->get();

        if ($certificates->isEmpty()) {
            return back()->with('error', 'No certificates generated yet.');
        }

        $zipPath = storage_path('app/temp/corp_certs_' . $session->id . '_' . time() . '.zip');
        @mkdir(dirname($zipPath), 0755, true);

        $zip = new ZipArchive();
        $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        foreach ($certificates as $cert) {
            // Generate a simple PDF-like HTML for each (placeholder — real PDF requires DomPDF/wkhtmltopdf)
            $html = $this->buildCertificateHtml($cert);
            $tmpFile = storage_path('app/temp/cert_' . $cert->id . '.html');
            file_put_contents($tmpFile, $html);
            $zip->addFile($tmpFile, $cert->safe_filename);
        }

        $zip->close();

        return response()->download($zipPath, 'certificates_session_' . $session->id . '.zip')
                         ->deleteFileAfterSend(true);
    }

    private function buildCertificateHtml(CorporateCertificate $cert): string
    {
        $p   = $cert->participant;
        $s   = $cert->session;
        $prj = $cert->project;

        return '<!DOCTYPE html><html><head><meta charset="UTF-8">
            <style>
                body{font-family:Georgia,serif;text-align:center;padding:60px;background:#fff;}
                h1{color:#1e3a8a;font-size:36px;margin-bottom:6px;}
                .cert-number{font-size:13px;color:#6b7280;margin-bottom:40px;}
                .presented{font-size:14px;color:#6b7280;margin-bottom:10px;}
                .name{font-size:32px;font-weight:bold;color:#111827;border-bottom:2px solid #1e3a8a;display:inline-block;padding:0 40px 8px;}
                .course{font-size:18px;color:#374151;margin:30px 0 10px;}
                .company{font-size:15px;color:#6b7280;}
                .date{font-size:13px;color:#9ca3af;margin-top:40px;}
                .border{border:8px double #1e3a8a;padding:40px;}
            </style></head><body>
            <div class="border">
                <h1>Certificate of Training</h1>
                <div class="cert-number">Certificate No: ' . htmlspecialchars($cert->certificate_number) . '</div>
                <div class="presented">This is to certify that</div>
                <div class="name">' . htmlspecialchars($p->participant_name ?? '') . '</div>
                <div class="course">has successfully completed the training in<br><strong>' . htmlspecialchars($s->course_name) . '</strong></div>
                <div class="company">Organised by ' . htmlspecialchars($prj->company_name) . '</div>
                <div class="date">Date: ' . $s->training_date?->format('d M Y') . ' &nbsp;|&nbsp; Issued: ' . $cert->issue_date?->format('d M Y') . '</div>
            </div></body></html>';
    }
}
