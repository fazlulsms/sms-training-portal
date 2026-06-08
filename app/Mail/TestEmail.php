<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TestEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct()
    {
        //
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'SMS Training Management System Test Email',
        );
    }

    public function content(): Content
    {
        return new Content(
            htmlString: '
                <p>Dear Sir,</p>
                <p>Greetings from Sustainable Management System Bangladesh.</p>
                <p>This is a successful SMTP email test from the SMS Training Management System.</p>
                <p>Thank you.</p>
            ',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}