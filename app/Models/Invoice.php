<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'invoice_type',
        'enrollment_id',
        'elearning_enrollment_id',
        'payment_confirmed_email_sent',

        'client_name',
        'client_email',
        'client_phone',
        'client_address',
        'client_country',
        'client_company',
        'contact_person',
        'client_reference_number',

        'service_type',
        'training_name',
        'training_date',
        'training_duration',
        'training_method_venue',

        'number_of_participants',
        'fee_per_person',
        'charge_for',

        'invoice_date',
        'due_date',
        'currency',

        'subtotal',
        'vat_percent',
        'vat_amount',
        'discount_amount',
	'grand_total',
        'total_amount',
        'amount_in_words',

        'payment_status',
        'amount_paid',
        'payment_method',

        'notes',
        'terms',
        'prepared_by',
        'status',
    ];

    protected $casts = [
        'payment_confirmed_email_sent' => 'boolean',
        'number_of_participants' => 'integer',
        'fee_per_person' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'vat_percent' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function paymentLogs()
    {
        return $this->hasMany(PaymentLog::class);
    }

    public function isPaid(): bool
    {
        return strtolower($this->payment_status ?? '') === 'paid';
    }
}