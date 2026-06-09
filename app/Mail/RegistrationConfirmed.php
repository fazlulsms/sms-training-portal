<?php

namespace App\Mail;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RegistrationConfirmed extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  Invoice      $invoice       The auto-generated invoice
     * @param  string       $courseName    Course / program name
     * @param  string|null  $scheduleInfo  Batch code, date, venue string (ILT only)
     * @param  string|null  $tempPassword  New account temp password (null if account existed)
     * @param  string       $type          'ILT' or 'eLearning'
     */
    public function __construct(
        public Invoice  $invoice,
        public string   $courseName,
        public ?string  $scheduleInfo  = null,
        public ?string  $tempPassword  = null,
        public string   $type          = 'ILT',
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Registration Confirmed - Invoice Attached',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.registration-confirmed',
            with: [
                'invoice'      => $this->invoice,
                'courseName'   => $this->courseName,
                'scheduleInfo' => $this->scheduleInfo,
                'tempPassword' => $this->tempPassword,
                'type'         => $this->type,
                'loginUrl'     => url('/login'),
            ],
        );
    }

    public function attachments(): array
    {
        $pdf = Pdf::loadView('invoices.auto-pdf', [
            'invoice' => $this->invoice,
        ])->setPaper('a4', 'portrait');

        $safeFileName = str_replace(['/', '\\'], '-', $this->invoice->invoice_number);

        return [
            Attachment::fromData(
                fn () => $pdf->output(),
                'Invoice-' . $safeFileName . '.pdf'
            )->withMime('application/pdf'),
        ];
    }
}
