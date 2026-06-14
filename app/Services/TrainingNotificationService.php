<?php

namespace App\Services;

use App\Mail\TrainingMail;
use App\Models\EmailLog;
use App\Models\ElearningEnrollment;
use App\Models\Enrollment;
use App\Models\Invoice;
use App\Models\NotificationSetting;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class TrainingNotificationService
{
    // ── Core: template-based send ─────────────────────────────────────

    public static function send(
        string $type,
        string $recipient,
        string $subject,
        string $view,
        array  $data        = [],
        string $modelType   = null,
        int    $modelId     = null,
        array  $attachments = []
    ): bool {
        if (!NotificationSetting::isEnabled($type)) return false;

        $log = EmailLog::create([
            'recipient'          => $recipient,
            'subject'            => $subject,
            'notification_type'  => $type,
            'related_model_type' => $modelType,
            'related_model_id'   => $modelId,
            'status'             => 'sent',
            'sent_at'            => now(),
        ]);

        try {
            Mail::to($recipient)->queue(new TrainingMail($subject, $view, $data, $attachments));
            return true;
        } catch (\Throwable $e) {
            $log->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
            Log::error('TrainingNotification failed', [
                'type' => $type, 'recipient' => $recipient, 'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /** Dispatch a pre-built Mailable (with PDF attachments) and still log it. */
    public static function dispatch(
        string   $type,
        Mailable $mailable,
        string   $recipient,
        string   $subject,
        string   $modelType = null,
        int      $modelId   = null
    ): bool {
        if (!NotificationSetting::isEnabled($type)) return false;

        $log = EmailLog::create([
            'recipient'          => $recipient,
            'subject'            => $subject,
            'notification_type'  => $type,
            'related_model_type' => $modelType,
            'related_model_id'   => $modelId,
            'status'             => 'sent',
            'sent_at'            => now(),
        ]);

        try {
            Mail::to($recipient)->queue($mailable);
            return true;
        } catch (\Throwable $e) {
            $log->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
            Log::error('TrainingNotification failed', [
                'type' => $type, 'recipient' => $recipient, 'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    public static function adminEmail(): string
    {
        return env('ADMIN_NOTIFICATION_EMAIL', config('mail.from.address', 'admin@smscert.com'));
    }

    // ══════════════════════════════════════════════════════════════════
    // PARTICIPANT NOTIFICATIONS
    // ══════════════════════════════════════════════════════════════════

    public static function iltRegistrationCompleted(
        Enrollment $enrollment,
        Invoice    $invoice      = null,
        string     $tempPassword = null
    ): void {
        if (!$enrollment->email) return;
        try {
            $enrollment->loadMissing('trainingSchedule.course');
            $schedule = $enrollment->trainingSchedule;
            $scheduleInfo = collect([
                $schedule?->batch_code,
                $schedule?->start_date?->format('d M Y'),
                $schedule?->venue,
            ])->filter()->implode(' · ');

            static::dispatch(
                type:      'participant.registration_completed',
                mailable:  new \App\Mail\RegistrationConfirmed(
                    invoice:      $invoice,
                    courseName:   $schedule?->course?->name ?? 'Training Program',
                    scheduleInfo: $scheduleInfo ?: null,
                    tempPassword: $tempPassword,
                    type:         'ILT',
                ),
                recipient: $enrollment->email,
                subject:   'Registration Confirmed — ' . ($schedule?->course?->name ?? 'Training Program'),
                modelType: 'Enrollment',
                modelId:   $enrollment->id,
            );
        } catch (\Throwable $e) {
            Log::error('TNS::iltRegistrationCompleted', ['id' => $enrollment->id, 'error' => $e->getMessage()]);
        }
    }

    public static function elearningRegistrationCompleted(
        ElearningEnrollment $enrollment,
        Invoice             $invoice      = null,
        string              $tempPassword = null
    ): void {
        if (!$enrollment->email) return;
        try {
            $enrollment->loadMissing('course');
            static::dispatch(
                type:      'participant.registration_completed',
                mailable:  new \App\Mail\RegistrationConfirmed(
                    invoice:      $invoice,
                    courseName:   $enrollment->course?->name ?? 'eLearning Course',
                    scheduleInfo: null,
                    tempPassword: $tempPassword,
                    type:         'eLearning',
                ),
                recipient: $enrollment->email,
                subject:   'Registration Confirmed — ' . ($enrollment->course?->name ?? 'eLearning Course'),
                modelType: 'ElearningEnrollment',
                modelId:   $enrollment->id,
            );
        } catch (\Throwable $e) {
            Log::error('TNS::elearningRegistrationCompleted', ['id' => $enrollment->id, 'error' => $e->getMessage()]);
        }
    }

    public static function paymentPending(
        $enrollment,
        Invoice $invoice   = null,
        string  $modelType = 'Enrollment'
    ): void {
        $email = $enrollment->email ?? null;
        if (!$email) return;
        try {
            $courseName = static::resolveCourseName($enrollment);
            $amount     = $invoice?->grand_total ?? $invoice?->total_amount
                        ?? $enrollment->applied_fee ?? $enrollment->amount ?? 0;
            $currency   = $invoice?->currency ?? 'BDT';

            static::send(
                type:      'participant.payment_pending',
                recipient: $email,
                subject:   'Payment Pending — ' . $courseName,
                view:      'emails.participant.payment-pending',
                data:      compact('enrollment', 'invoice', 'courseName', 'amount', 'currency'),
                modelType: $modelType,
                modelId:   $enrollment->id,
            );
        } catch (\Throwable $e) {
            Log::error('TNS::paymentPending', ['error' => $e->getMessage()]);
        }
    }

    public static function enrollmentApproved($enrollment, string $modelType = 'Enrollment'): void
    {
        $email = $enrollment->email ?? null;
        if (!$email) return;
        try {
            $courseName = static::resolveCourseName($enrollment);
            static::send(
                type:      'participant.enrollment_approved',
                recipient: $email,
                subject:   'Enrollment Approved — ' . $courseName,
                view:      'emails.participant.enrollment-approved',
                data:      compact('enrollment', 'courseName'),
                modelType: $modelType,
                modelId:   $enrollment->id,
            );
        } catch (\Throwable $e) {
            Log::error('TNS::enrollmentApproved', ['error' => $e->getMessage()]);
        }
    }

    public static function enrollmentRejected($enrollment, string $reason = '', string $modelType = 'Enrollment'): void
    {
        $email = $enrollment->email ?? null;
        if (!$email) return;
        try {
            $courseName = static::resolveCourseName($enrollment);
            static::send(
                type:      'participant.enrollment_rejected',
                recipient: $email,
                subject:   'Enrollment Update — ' . $courseName,
                view:      'emails.participant.enrollment-rejected',
                data:      compact('enrollment', 'courseName', 'reason'),
                modelType: $modelType,
                modelId:   $enrollment->id,
            );
        } catch (\Throwable $e) {
            Log::error('TNS::enrollmentRejected', ['error' => $e->getMessage()]);
        }
    }

    public static function courseAccessActivated(ElearningEnrollment $enrollment, string $tempPassword = null): void
    {
        if (!$enrollment->email) return;
        try {
            $enrollment->loadMissing('course');
            $courseName = $enrollment->course?->name ?? 'eLearning Course';
            $loginUrl   = url('/login');
            $accessDays = $enrollment->expires_at
                ? \Carbon\Carbon::parse($enrollment->expires_at)->format('d M Y')
                : 'Lifetime Access';

            static::send(
                type:      'participant.course_access_activated',
                recipient: $enrollment->email,
                subject:   'Course Access Activated — ' . $courseName,
                view:      'emails.participant.course-access-activated',
                data:      compact('enrollment', 'courseName', 'loginUrl', 'accessDays', 'tempPassword'),
                modelType: 'ElearningEnrollment',
                modelId:   $enrollment->id,
            );
        } catch (\Throwable $e) {
            Log::error('TNS::courseAccessActivated', ['error' => $e->getMessage()]);
        }
    }

    public static function scheduleChanged(Enrollment $enrollment, array $changes = []): void
    {
        if (!$enrollment->email) return;
        try {
            $enrollment->loadMissing('trainingSchedule.course');
            $courseName = $enrollment->trainingSchedule?->course?->name ?? 'Training Program';
            $schedule   = $enrollment->trainingSchedule;

            static::send(
                type:      'participant.schedule_changed',
                recipient: $enrollment->email,
                subject:   'Training Schedule Updated — ' . $courseName,
                view:      'emails.participant.schedule-changed',
                data:      compact('enrollment', 'courseName', 'schedule', 'changes'),
                modelType: 'Enrollment',
                modelId:   $enrollment->id,
            );
        } catch (\Throwable $e) {
            Log::error('TNS::scheduleChanged', ['error' => $e->getMessage()]);
        }
    }

    public static function trainingReminder(Enrollment $enrollment, int $daysAhead = 1): void
    {
        if (!$enrollment->email) return;
        try {
            $enrollment->loadMissing('trainingSchedule.course');
            $courseName = $enrollment->trainingSchedule?->course?->name ?? 'Training Program';
            $schedule   = $enrollment->trainingSchedule;

            static::send(
                type:      'participant.training_reminder',
                recipient: $enrollment->email,
                subject:   "Reminder: Training in {$daysAhead} Day(s) — " . $courseName,
                view:      'emails.participant.training-reminder',
                data:      compact('enrollment', 'courseName', 'schedule', 'daysAhead'),
                modelType: 'Enrollment',
                modelId:   $enrollment->id,
            );
        } catch (\Throwable $e) {
            Log::error('TNS::trainingReminder', ['error' => $e->getMessage()]);
        }
    }

    public static function attendanceCompleted($enrollment, string $modelType = 'Enrollment'): void
    {
        $email = $enrollment->email ?? null;
        if (!$email) return;
        try {
            $courseName = static::resolveCourseName($enrollment);
            static::send(
                type:      'participant.attendance_completed',
                recipient: $email,
                subject:   'Attendance Confirmed — ' . $courseName,
                view:      'emails.participant.attendance-completed',
                data:      compact('enrollment', 'courseName'),
                modelType: $modelType,
                modelId:   $enrollment->id,
            );
        } catch (\Throwable $e) {
            Log::error('TNS::attendanceCompleted', ['error' => $e->getMessage()]);
        }
    }

    public static function courseCompleted($enrollment, string $modelType = 'Enrollment'): void
    {
        $email = $enrollment->email ?? null;
        if (!$email) return;
        try {
            $courseName = static::resolveCourseName($enrollment);
            static::send(
                type:      'participant.course_completed',
                recipient: $email,
                subject:   'Congratulations! You Completed — ' . $courseName,
                view:      'emails.participant.course-completed',
                data:      compact('enrollment', 'courseName'),
                modelType: $modelType,
                modelId:   $enrollment->id,
            );
        } catch (\Throwable $e) {
            Log::error('TNS::courseCompleted', ['error' => $e->getMessage()]);
        }
    }

    public static function certificateIssued($enrollment, string $modelType = 'Enrollment'): void
    {
        $email = $enrollment->email ?? null;
        if (!$email) return;
        try {
            $courseName = static::resolveCourseName($enrollment);
            $certNumber = $enrollment->certificate_number ?? null;
            $loginUrl   = url('/login');

            static::send(
                type:      'participant.certificate_issued',
                recipient: $email,
                subject:   'Your Certificate is Ready — ' . $courseName,
                view:      'emails.participant.certificate-issued',
                data:      compact('enrollment', 'courseName', 'certNumber', 'loginUrl'),
                modelType: $modelType,
                modelId:   $enrollment->id,
            );
        } catch (\Throwable $e) {
            Log::error('TNS::certificateIssued', ['error' => $e->getMessage()]);
        }
    }

    public static function certificateRevoked($enrollment, string $reason = '', string $modelType = 'Enrollment'): void
    {
        $email = $enrollment->email ?? null;
        if (!$email) return;
        try {
            $courseName = static::resolveCourseName($enrollment);
            static::send(
                type:      'participant.certificate_revoked',
                recipient: $email,
                subject:   'Certificate Revoked — ' . $courseName,
                view:      'emails.participant.certificate-revoked',
                data:      compact('enrollment', 'courseName', 'reason'),
                modelType: $modelType,
                modelId:   $enrollment->id,
            );
        } catch (\Throwable $e) {
            Log::error('TNS::certificateRevoked', ['error' => $e->getMessage()]);
        }
    }

    // ══════════════════════════════════════════════════════════════════
    // ADMIN NOTIFICATIONS
    // ══════════════════════════════════════════════════════════════════

    public static function adminNewRegistration($enrollment, string $modelType = 'Enrollment'): void
    {
        try {
            $courseName = static::resolveCourseName($enrollment);
            $name  = $enrollment->full_name ?? $enrollment->participant_name ?? 'Participant';
            $email = $enrollment->email ?? '—';

            static::send(
                type:      'admin.new_registration',
                recipient: static::adminEmail(),
                subject:   "New Registration: {$name} — {$courseName}",
                view:      'emails.admin.new-registration',
                data:      compact('enrollment', 'courseName', 'name', 'email'),
                modelType: $modelType,
                modelId:   $enrollment->id,
            );
        } catch (\Throwable $e) {
            Log::error('TNS::adminNewRegistration', ['error' => $e->getMessage()]);
        }
    }

    public static function adminPaymentAttempt(
        $enrollment,
        string $status    = 'completed',
        float  $amount    = 0,
        string $modelType = 'Enrollment'
    ): void {
        try {
            $courseName = static::resolveCourseName($enrollment);
            $name       = $enrollment->full_name ?? $enrollment->participant_name ?? 'Participant';

            static::send(
                type:      $status === 'failed' ? 'admin.payment_failed' : 'admin.payment_attempt',
                recipient: static::adminEmail(),
                subject:   "Payment {$status}: {$name} — {$courseName}",
                view:      'emails.admin.payment-attempt',
                data:      compact('enrollment', 'courseName', 'name', 'status', 'amount'),
                modelType: $modelType,
                modelId:   $enrollment->id,
            );
        } catch (\Throwable $e) {
            Log::error('TNS::adminPaymentAttempt', ['error' => $e->getMessage()]);
        }
    }

    public static function adminGenericAlert(
        string $type,
        string $subject,
        array  $data      = [],
        string $modelType = null,
        int    $modelId   = null
    ): void {
        try {
            static::send(
                type:      $type,
                recipient: static::adminEmail(),
                subject:   $subject,
                view:      'emails.admin.generic-alert',
                data:      $data,
                modelType: $modelType,
                modelId:   $modelId,
            );
        } catch (\Throwable $e) {
            Log::error('TNS::adminGenericAlert', ['type' => $type, 'error' => $e->getMessage()]);
        }
    }

    // ── Internal helper ───────────────────────────────────────────────

    private static function resolveCourseName($enrollment): string
    {
        if ($enrollment instanceof Enrollment) {
            $enrollment->loadMissing('trainingSchedule.course');
            return $enrollment->trainingSchedule?->course?->name ?? 'Training Program';
        }
        if ($enrollment instanceof ElearningEnrollment) {
            $enrollment->loadMissing('course');
            return $enrollment->course?->name ?? 'eLearning Course';
        }
        return 'Training Program';
    }
}
