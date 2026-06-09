<?php

namespace Database\Seeders;

use App\Models\NotificationSetting;
use Illuminate\Database\Seeder;

class NotificationSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // ── Participant ──────────────────────────────────────────────
            ['group' => 'participant', 'key' => 'participant.registration_completed',
             'label' => 'Registration Confirmed',
             'description' => 'Sent when a participant successfully registers for a course (with invoice PDF attached).'],

            ['group' => 'participant', 'key' => 'participant.payment_pending',
             'label' => 'Payment Pending',
             'description' => 'Sent after registration to remind the participant that payment is awaited.'],

            ['group' => 'participant', 'key' => 'participant.payment_confirmed',
             'label' => 'Payment Confirmed',
             'description' => 'Sent when payment is marked Paid (with paid invoice + receipt PDFs attached).'],

            ['group' => 'participant', 'key' => 'participant.enrollment_approved',
             'label' => 'Enrollment Approved',
             'description' => 'Sent when admin manually approves an enrollment.'],

            ['group' => 'participant', 'key' => 'participant.enrollment_rejected',
             'label' => 'Enrollment Rejected',
             'description' => 'Sent when an enrollment is rejected, with optional reason.'],

            ['group' => 'participant', 'key' => 'participant.course_access_activated',
             'label' => 'Course Access Activated',
             'description' => 'Sent when eLearning course access is unlocked for the participant.'],

            ['group' => 'participant', 'key' => 'participant.schedule_changed',
             'label' => 'Training Schedule Changed',
             'description' => 'Sent when the date/venue of an ILT session is updated.'],

            ['group' => 'participant', 'key' => 'participant.training_reminder',
             'label' => 'Training Reminder',
             'description' => 'Sent X days before a scheduled ILT session as a reminder.'],

            ['group' => 'participant', 'key' => 'participant.attendance_completed',
             'label' => 'Attendance Marked',
             'description' => 'Sent when attendance_status is changed to Attended/Completed.'],

            ['group' => 'participant', 'key' => 'participant.course_completed',
             'label' => 'Course Completed',
             'description' => 'Sent when completion_status is marked Completed for ILT or eLearning.'],

            ['group' => 'participant', 'key' => 'participant.certificate_issued',
             'label' => 'Certificate Issued',
             'description' => 'Sent when a certificate is issued, with certificate number.'],

            ['group' => 'participant', 'key' => 'participant.certificate_ready',
             'label' => 'Certificate Ready for Download',
             'description' => 'Sent when the digital certificate is ready to download from the portal.'],

            ['group' => 'participant', 'key' => 'participant.certificate_revoked',
             'label' => 'Certificate Revoked',
             'description' => 'Sent if a previously issued certificate is revoked/cancelled.'],

            // ── Admin ────────────────────────────────────────────────────
            ['group' => 'admin', 'key' => 'admin.new_registration',
             'label' => 'New Registration Alert',
             'description' => 'Admin receives an alert whenever a new participant registers.'],

            ['group' => 'admin', 'key' => 'admin.payment_attempt',
             'label' => 'Payment Attempt',
             'description' => 'Admin is notified when a participant submits a payment (gateway or manual).'],

            ['group' => 'admin', 'key' => 'admin.payment_failed',
             'label' => 'Payment Failed',
             'description' => 'Admin is notified when a payment gateway transaction fails.'],

            ['group' => 'admin', 'key' => 'admin.payment_evidence_uploaded',
             'label' => 'Payment Evidence Uploaded',
             'description' => 'Admin is notified when a participant uploads proof of payment.'],

            ['group' => 'admin', 'key' => 'admin.feedback_submitted',
             'label' => 'Feedback / Testimonial Submitted',
             'description' => 'Admin is notified when a participant submits a review or feedback.'],

            ['group' => 'admin', 'key' => 'admin.certificate_correction_request',
             'label' => 'Certificate Correction Request',
             'description' => 'Admin is notified when a participant requests a correction to their certificate.'],

            ['group' => 'admin', 'key' => 'admin.support_contact',
             'label' => 'Support Contact',
             'description' => 'Admin is notified when a participant sends a support message from the dashboard.'],
        ];

        foreach ($settings as $s) {
            NotificationSetting::firstOrCreate(['key' => $s['key']], $s);
        }
    }
}
