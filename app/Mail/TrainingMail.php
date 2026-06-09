<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TrainingMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        private string $emailSubject,
        private string $emailView,
        private array  $emailData        = [],
        private array  $pdfAttachments   = [],   // [['name'=>'file.pdf','data'=>'<binary>']]
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->emailSubject);
    }

    public function content(): Content
    {
        return new Content(view: $this->emailView, with: $this->emailData);
    }

    public function attachments(): array
    {
        return array_map(
            fn($a) => Attachment::fromData(fn() => $a['data'], $a['name'])->withMime('application/pdf'),
            $this->pdfAttachments
        );
    }
}
