<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Invoice $invoice)
    {
        //
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Proforma Invoice - ' . $this->invoice->invoice_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.invoice',
        );
    }

   public function attachments(): array
{
    $pdf = Pdf::loadView('invoices.pdf', [
        'invoice' => $this->invoice
    ])->setPaper('a4', 'portrait');

    $safeFileName = str_replace(['/', '\\'], '-', $this->invoice->invoice_number);

    return [
        Attachment::fromData(
            fn () => $pdf->output(),
            $safeFileName . '.pdf'
        )->withMime('application/pdf'),
    ];
}
}