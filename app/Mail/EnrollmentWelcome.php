<?php

namespace App\Mail;

use App\Models\ElearningEnrollment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EnrollmentWelcome extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  ElearningEnrollment  $enrollment
     * @param  string               $plainPassword  The unhashed temp password (only used at send time)
     */
    public function __construct(
        public ElearningEnrollment $enrollment,
        public string $plainPassword,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Training Enrollment Confirmation – SMS Training Panel',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.enrollment-welcome',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
