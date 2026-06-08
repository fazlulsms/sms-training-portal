<?php

namespace App\Http\Controllers;

use App\Models\ElearningEnrollment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ElearningCertificateController extends Controller
{
    public function generate(ElearningEnrollment $enrollment)
    {
        $user = Auth::user();

        // Participants can only download their own certificate
        if ($user->isParticipant()) {
            abort_unless($enrollment->user_id === $user->id, 403, 'You are not authorised to download this certificate.');

            // Certificate must be issued before participant can download
            abort_unless(
                $enrollment->certificate_status === 'issued',
                403,
                'Your certificate has not been issued yet. Please contact admin.'
            );
        }

        $enrollment->load('course');

        if (!$enrollment->certificate_number) {
            $enrollment->certificate_number = 'EL-' . date('Y') . '-' . str_pad($enrollment->id, 5, '0', STR_PAD_LEFT);
        }

        if (!$enrollment->completion_date) {
            $enrollment->completion_date = now();
        }

        $enrollment->save();

        $verificationUrl = url('/elearning/verify-certificate?cert=' . urlencode($enrollment->certificate_number));

        $qrCode = base64_encode(
            QrCode::format('png')
                ->size(220)
                ->margin(1)
                ->generate($verificationUrl)
        );

        $pdf = Pdf::loadView('elearning.certificates.pdf', compact('enrollment', 'qrCode'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream($enrollment->certificate_number . '.pdf');
    }
}
