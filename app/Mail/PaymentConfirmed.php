<?php

namespace App\Mail;

use App\Models\Invoice;
use App\Models\PaymentLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentConfirmed extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Invoice    $invoice,
        public PaymentLog $log,
        public string     $courseName,
        public string     $type = 'ILT',   // 'ILT' or 'eLearning'
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Payment Received - Thank You',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.payment-confirmed',
            with: [
                'invoice'    => $this->invoice,
                'log'        => $this->log,
                'courseName' => $this->courseName,
                'type'       => $this->type,
                'loginUrl'   => url('/login'),
            ],
        );
    }

    public function attachments(): array
    {
        $safe = str_replace(['/', '\\'], '-', $this->invoice->invoice_number);

        // 1 — Paid invoice PDF (with PAID watermark)
        $invoicePdf = Pdf::loadView('invoices.auto-pdf', [
            'invoice' => $this->invoice,
            'paid'    => true,
        ])->setPaper('a4', 'portrait');

        // 2 — Money receipt PDF
        $receiptPdf = Pdf::loadView('invoices.receipt-pdf', [
            'invoice' => $this->invoice,
            'log'     => $this->log,
        ])->setPaper('a4', 'portrait');

        return [
            Attachment::fromData(fn () => $invoicePdf->output(), 'Invoice-' . $safe . '-PAID.pdf')
                ->withMime('application/pdf'),
            Attachment::fromData(fn () => $receiptPdf->output(), 'Receipt-' . $safe . '.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
