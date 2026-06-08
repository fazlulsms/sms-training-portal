<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EnrollmentConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $enrollment;
    public $user;
    public $tempPassword;

    /**
     * Create a new message instance.
     */
    public function __construct($enrollment, $user, string $tempPassword = null)
    {
        $this->enrollment    = $enrollment;
        $this->user          = $user;
        $this->tempPassword  = $tempPassword;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Enrollment Confirmation — ' . ($this->enrollment->trainingSchedule?->course?->name ?? 'SMS Training'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.enrollment-confirmation',
            with: [
                'enrollment'    => $this->enrollment,
                'user'          => $this->user,
                'tempPassword'  => $this->tempPassword,
                'loginUrl'      => url('/login'),
                'courseName'    => $this->enrollment->trainingSchedule?->course?->name ?? 'Training Program',
                'startDate'     => $this->enrollment->trainingSchedule?->start_date,
                'batchCode'     => $this->enrollment->trainingSchedule?->batch_code,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
