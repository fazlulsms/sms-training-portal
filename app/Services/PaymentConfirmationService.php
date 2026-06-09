<?php

namespace App\Services;

use App\Mail\PaymentConfirmed;
use App\Models\ElearningEnrollment;
use App\Models\Enrollment;
use App\Models\Invoice;
use App\Models\PaymentLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PaymentConfirmationService
{
    // ── Public entry points ───────────────────────────────────────────────

    /**
     * Trigger when an ILT Enrollment payment_status is set to Paid/paid.
     * Finds the linked auto-invoice, updates it, logs, and sends email.
     */
    public static function handleIltEnrollment(Enrollment $enrollment, array $meta = []): void
    {
        $invoice = self::findInvoiceForIlt($enrollment);

        if (!$invoice) {
            Log::warning('PaymentConfirmation: no auto-invoice found for ILT enrollment', [
                'enrollment_id' => $enrollment->id,
            ]);
            return;
        }

        $enrollment->loadMissing('trainingSchedule.course');
        $courseName = $enrollment->trainingSchedule?->course?->name ?? 'Training Programme';

        self::process($invoice, $courseName, 'ILT', $meta + [
            'enrollment_id' => $enrollment->id,
            'amount'        => $enrollment->amount_received ?? $enrollment->applied_fee ?? 0,
            'payment_method'=> $enrollment->payment_method,
        ]);
    }

    /**
     * Trigger when an eLearning Enrollment payment is confirmed.
     */
    public static function handleElearningEnrollment(ElearningEnrollment $enrollment, array $meta = []): void
    {
        $invoice = self::findInvoiceForElearning($enrollment);

        if (!$invoice) {
            Log::warning('PaymentConfirmation: no auto-invoice found for eLearning enrollment', [
                'elearning_enrollment_id' => $enrollment->id,
            ]);
            return;
        }

        $enrollment->loadMissing('course');
        $courseName = $enrollment->course?->name ?? 'eLearning Course';

        self::process($invoice, $courseName, 'eLearning', $meta + [
            'elearning_enrollment_id' => $enrollment->id,
            'amount'                  => $enrollment->amount ?? 0,
            'payment_method'          => $enrollment->payment_method,
            'transaction_id'          => $enrollment->transaction_id ?? null,
            'gateway_response'        => $enrollment->gateway_response ?? null,
        ]);
    }

    /**
     * Trigger when an Invoice payment_status is directly updated to Paid.
     * Works for both auto and manual invoices.
     */
    public static function handleInvoice(Invoice $invoice, array $meta = []): void
    {
        $courseName = $invoice->training_name ?? 'Training Programme';
        $type       = $invoice->invoice_type === 'auto' ? 'ILT' : 'ILT';

        self::process($invoice, $courseName, $type, $meta + [
            'amount'         => $invoice->amount_paid ?? $invoice->grand_total ?? $invoice->total_amount,
            'payment_method' => $invoice->payment_method,
        ]);
    }

    // ── Core processing ──────────────────────────────────────────────────

    private static function process(Invoice $invoice, string $courseName, string $type, array $meta): void
    {
        // ── Guard: duplicate prevention ──────────────────────────────────
        if ($invoice->payment_confirmed_email_sent) {
            Log::info('PaymentConfirmation: email already sent, skipping', [
                'invoice_id' => $invoice->id,
            ]);
            return;
        }

        // ── Update invoice to Paid ────────────────────────────────────────
        $amount = (float) ($meta['amount'] ?? $invoice->amount_paid ?? $invoice->grand_total ?? $invoice->total_amount ?? 0);

        $invoice->update([
            'payment_status' => 'Paid',
            'amount_paid'    => $amount,
        ]);

        // ── Create payment log ────────────────────────────────────────────
        $log = PaymentLog::create([
            'invoice_id'               => $invoice->id,
            'enrollment_id'            => $meta['enrollment_id']            ?? $invoice->enrollment_id            ?? null,
            'elearning_enrollment_id'  => $meta['elearning_enrollment_id']  ?? $invoice->elearning_enrollment_id  ?? null,
            'amount'                   => $amount,
            'payment_method'           => $meta['payment_method']   ?? $invoice->payment_method ?? null,
            'transaction_id'           => $meta['transaction_id']   ?? null,
            'payment_status'           => 'Paid',
            'payment_date'             => $meta['payment_date']     ?? now()->toDateString(),
            'received_by'              => $meta['received_by']      ?? null,
            'gateway_response'         => isset($meta['gateway_response'])
                ? (is_array($meta['gateway_response'])
                    ? json_encode($meta['gateway_response'])
                    : $meta['gateway_response'])
                : null,
            'remarks'                  => $meta['remarks']          ?? null,
        ]);

        // ── Send email ────────────────────────────────────────────────────
        if ($invoice->client_email) {
            try {
                Mail::to($invoice->client_email)
                    ->send(new PaymentConfirmed($invoice, $log, $courseName, $type));

                // Mark email as sent to prevent duplicates
                $invoice->update(['payment_confirmed_email_sent' => true]);

                Log::info('PaymentConfirmation: email sent', [
                    'invoice_id' => $invoice->id,
                    'email'      => $invoice->client_email,
                ]);
            } catch (\Throwable $e) {
                Log::error('PaymentConfirmation: email failed', [
                    'invoice_id' => $invoice->id,
                    'error'      => $e->getMessage(),
                ]);
            }
        }
    }

    // ── Enrollment sync ──────────────────────────────────────────────────

    /**
     * Sync the linked enrollment's payment_status / amount_received
     * whenever an invoice's payment status changes.
     * Called from InvoiceController whenever payment_status is saved.
     */
    public static function syncLinkedEnrollment(Invoice $invoice, string $newStatus, float $amountPaid): void
    {
        // ── ILT enrollment ────────────────────────────────────────────────
        if ($invoice->enrollment_id) {
            $enrollment = Enrollment::find($invoice->enrollment_id);
            if ($enrollment) {
                $iltStatus = match(strtolower($newStatus)) {
                    'paid'      => 'manual_approved',
                    'partial'   => 'Partial',
                    'unpaid'    => 'Pending',
                    default     => null,   // Cancelled → don't touch enrollment
                };

                if ($iltStatus !== null) {
                    $enrollment->update([
                        'payment_status'  => $iltStatus,
                        'amount_received' => $amountPaid,
                    ]);

                    Log::info('PaymentSync: ILT enrollment synced from invoice', [
                        'invoice_id'    => $invoice->id,
                        'enrollment_id' => $enrollment->id,
                        'new_status'    => $iltStatus,
                        'amount'        => $amountPaid,
                    ]);
                }
            }
        }

        // ── eLearning enrollment ──────────────────────────────────────────
        if ($invoice->elearning_enrollment_id) {
            $eEnroll = ElearningEnrollment::with('course', 'user')
                ->find($invoice->elearning_enrollment_id);
            if ($eEnroll) {
                $wasLocked = $eEnroll->access_status !== 'unlocked';

                if (strtolower($newStatus) === 'paid') {
                    $course    = $eEnroll->course;
                    $expiresAt = $course?->access_days ? now()->addDays($course->access_days) : null;

                    $eEnroll->update([
                        'payment_status' => 'manual_approved',
                        'access_status'  => 'unlocked',
                        'started_at'     => $eEnroll->started_at ?? now(),
                        'expires_at'     => $eEnroll->expires_at ?? $expiresAt,
                    ]);

                    // ── SEQUENCE STEP 2b: Welcome email with login credentials ──
                    // Only sent the first time access is unlocked
                    if ($wasLocked) {
                        try {
                            $fresh         = $eEnroll->fresh()->load('course', 'user');
                            $plainPassword = null;

                            if ($fresh->user) {
                                $plainPassword = 'SMS@' . date('Y') . '#'
                                    . str_pad(random_int(1000, 9999), 4, '0', STR_PAD_LEFT);
                                $fresh->user->update([
                                    'password' => \Illuminate\Support\Facades\Hash::make($plainPassword),
                                ]);
                            }

                            TrainingNotificationService::courseAccessActivated($fresh, $plainPassword);
                        } catch (\Throwable $e) {
                            Log::error('PaymentSync: welcome email failed for eLearning', [
                                'elearning_enrollment_id' => $eEnroll->id,
                                'error'                   => $e->getMessage(),
                            ]);
                        }
                    }

                } elseif (strtolower($newStatus) === 'partial') {
                    $eEnroll->update(['payment_status' => 'pending']);
                } elseif (strtolower($newStatus) === 'unpaid') {
                    $eEnroll->update(['payment_status' => 'pending']);
                }

                Log::info('PaymentSync: eLearning enrollment synced from invoice', [
                    'invoice_id'              => $invoice->id,
                    'elearning_enrollment_id' => $eEnroll->id,
                    'invoice_status'          => $newStatus,
                ]);
            }
        }
    }

    // ── Invoice lookup helpers ───────────────────────────────────────────

    private static function findInvoiceForIlt(Enrollment $enrollment): ?Invoice
    {
        // Primary: direct enrollment_id reference (new enrollments)
        $inv = Invoice::where('invoice_type', 'auto')
            ->where('enrollment_id', $enrollment->id)
            ->latest()
            ->first();

        if ($inv) return $inv;

        // Fallback: match by email + training name (older enrollments before the ref column existed)
        $enrollment->loadMissing('trainingSchedule.course');
        $courseName = $enrollment->trainingSchedule?->course?->name;

        return Invoice::where('invoice_type', 'auto')
            ->where('client_email', $enrollment->email)
            ->when($courseName, fn($q) => $q->where('training_name', 'like', "%{$courseName}%"))
            ->latest()
            ->first();
    }

    private static function findInvoiceForElearning(ElearningEnrollment $enrollment): ?Invoice
    {
        // Primary: direct elearning_enrollment_id reference
        $inv = Invoice::where('invoice_type', 'auto')
            ->where('elearning_enrollment_id', $enrollment->id)
            ->latest()
            ->first();

        if ($inv) return $inv;

        // Fallback: email match
        return Invoice::where('invoice_type', 'auto')
            ->where('client_email', $enrollment->email)
            ->where('service_type', 'eLearning')
            ->latest()
            ->first();
    }

    // ── Utility: check if a status string means "paid" ──────────────────

    public static function isPaidStatus(?string $status): bool
    {
        return in_array(strtolower($status ?? ''), ['paid', 'manual_approved']);
    }
}
