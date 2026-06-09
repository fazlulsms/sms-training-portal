<?php

namespace App\Services;

use App\Models\Invoice;

class AutoInvoiceService
{
    /**
     * Generate a sequential invoice number: SMS/BD/YY###
     */
    public static function generateInvoiceNumber(): string
    {
        $last = Invoice::orderBy('id', 'desc')->first();
        $next = $last ? $last->id + 1 : 1;

        return 'SMS/BD/' . date('y') . str_pad($next, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Create an auto-invoice for an ILT enrollment (Enrollment model).
     *
     * @param  \App\Models\Enrollment  $enrollment
     * @param  \App\Models\TrainingSchedule|null  $schedule
     * @return \App\Models\Invoice
     */
    public static function forIltEnrollment($enrollment, $schedule = null): Invoice
    {
        $schedule = $schedule ?? $enrollment->trainingSchedule;
        $schedule?->loadMissing('course');

        $courseName   = $schedule?->course?->name ?? 'Training Program';
        $batchCode    = $schedule?->batch_code    ?? '';
        $startDate    = $schedule?->start_date    ? \Carbon\Carbon::parse($schedule->start_date)->format('d M Y') : '';
        $venue        = $schedule?->venue         ?? $enrollment->selected_mode ?? '';
        $trainingDate = $schedule?->start_date    ? \Carbon\Carbon::parse($schedule->start_date)->toDateString() : now()->toDateString();
        $mode         = $enrollment->selected_mode ?? 'Physical';

        $fee         = (float) ($enrollment->applied_fee ?? 0);
        $vatPercent  = 0;
        $vatAmount   = 0;
        $discount    = 0;
        $total       = $fee;

        return Invoice::create([
            'invoice_number'        => self::generateInvoiceNumber(),
            'invoice_type'          => 'auto',
            'enrollment_id'         => $enrollment->id,
            'client_name'           => $enrollment->full_name,
            'client_email'          => $enrollment->email,
            'client_phone'          => $enrollment->mobile_number ?? null,
            'client_address'        => $enrollment->full_address  ?? null,
            'client_country'        => $enrollment->country       ?? null,
            'client_company'        => $enrollment->company       ?? null,
            'service_type'          => 'ILT Training',
            'training_name'         => $courseName . ($batchCode ? " ({$batchCode})" : ''),
            'training_date'         => $trainingDate,
            'training_duration'     => null,
            'training_method_venue' => $mode . ($venue ? " — {$venue}" : ''),
            'number_of_participants'=> 1,
            'fee_per_person'        => $fee,
            'charge_for'            => $fee,
            'invoice_date'          => now()->toDateString(),
            'due_date'              => now()->addDays(7)->toDateString(),
            'currency'              => 'BDT',
            'subtotal'              => $fee,
            'vat_percent'           => $vatPercent,
            'vat_amount'            => $vatAmount,
            'discount_amount'       => $discount,
            'grand_total'           => $total,
            'total_amount'          => $total,
            'amount_in_words'       => self::amountInWords($total),
            'payment_status'        => self::mapPaymentStatus($enrollment->payment_status),
            'amount_paid'           => $enrollment->amount_received ?? 0,
            'payment_method'        => $enrollment->payment_method  ?? null,
            'notes'                 => 'Auto-generated on registration',
            'status'                => 'Issued',
        ]);
    }

    /**
     * Create an auto-invoice for an eLearning enrollment (ElearningEnrollment model).
     *
     * @param  \App\Models\ElearningEnrollment  $enrollment
     * @return \App\Models\Invoice
     */
    public static function forElearningEnrollment($enrollment): Invoice
    {
        $enrollment->loadMissing('course');

        $courseName = $enrollment->course?->name ?? 'eLearning Course';
        $fee        = (float) ($enrollment->amount ?? 0);
        $total      = $fee;

        return Invoice::create([
            'invoice_number'              => self::generateInvoiceNumber(),
            'invoice_type'                => 'auto',
            'elearning_enrollment_id'     => $enrollment->id,
            'client_name'                 => $enrollment->participant_name,
            'client_email'          => $enrollment->email,
            'client_phone'          => $enrollment->phone       ?? null,
            'client_address'        => null,
            'client_country'        => $enrollment->country     ?? null,
            'client_company'        => $enrollment->company     ?? null,
            'service_type'          => 'eLearning',
            'training_name'         => $courseName,
            'training_date'         => now()->toDateString(),
            'training_duration'     => null,
            'training_method_venue' => 'Online / Self-Paced',
            'number_of_participants'=> 1,
            'fee_per_person'        => $fee,
            'charge_for'            => $fee,
            'invoice_date'          => now()->toDateString(),
            'due_date'              => now()->addDays(7)->toDateString(),
            'currency'              => $enrollment->currency ?? 'BDT',
            'subtotal'              => $fee,
            'vat_percent'           => 0,
            'vat_amount'            => 0,
            'discount_amount'       => 0,
            'grand_total'           => $total,
            'total_amount'          => $total,
            'amount_in_words'       => self::amountInWords($total, $enrollment->currency ?? 'BDT'),
            'payment_status'        => self::mapPaymentStatus($enrollment->payment_status),
            'amount_paid'           => 0,
            'payment_method'        => $enrollment->payment_method ?? null,
            'notes'                 => 'Auto-generated on registration',
            'status'                => 'Issued',
        ]);
    }

    /**
     * Map enrollment payment_status to invoice ENUM: Unpaid | Partial | Paid | Cancelled
     */
    public static function mapPaymentStatus(?string $status): string
    {
        return match(strtolower($status ?? '')) {
            'paid', 'manual_approved', 'completed' => 'Paid',
            'partial'                               => 'Partial',
            'cancelled', 'refunded'                => 'Cancelled',
            default                                 => 'Unpaid',
        };
    }

    /**
     * Basic number-to-words for invoice amounts.
     */
    public static function amountInWords(float $amount, string $currency = 'BDT'): string
    {
        $number  = (int) round($amount);
        $convert = null;
        $convert = function (int $num) use (&$convert): string {
            if ($num === 0) return '';
            $ones  = ['','One','Two','Three','Four','Five','Six','Seven','Eight','Nine',
                      'Ten','Eleven','Twelve','Thirteen','Fourteen','Fifteen',
                      'Sixteen','Seventeen','Eighteen','Nineteen'];
            $tens  = ['','','Twenty','Thirty','Forty','Fifty','Sixty','Seventy','Eighty','Ninety'];
            if ($num < 20) return $ones[$num];
            if ($num < 100) return $tens[(int)($num/10)] . ($num%10 ? ' '.$ones[$num%10] : '');
            if ($num < 1000) return $ones[(int)($num/100)] . ' Hundred' . ($num%100 ? ' '.$convert($num%100) : '');
            if ($num < 100000) return $convert((int)($num/1000)) . ' Thousand' . ($num%1000 ? ' '.$convert($num%1000) : '');
            if ($num < 10000000) return $convert((int)($num/100000)) . ' Lakh' . ($num%100000 ? ' '.$convert($num%100000) : '');
            return $convert((int)($num/10000000)) . ' Crore' . ($num%10000000 ? ' '.$convert($num%10000000) : '');
        };

        $words = trim(preg_replace('/\s+/', ' ', $convert($number)));
        return $words ? $words . ' ' . $currency . ' Only' : 'Zero ' . $currency . ' Only';
    }
}
