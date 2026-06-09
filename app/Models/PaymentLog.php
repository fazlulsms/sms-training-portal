<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentLog extends Model
{
    protected $fillable = [
        'invoice_id',
        'enrollment_id',
        'elearning_enrollment_id',
        'amount',
        'payment_method',
        'transaction_id',
        'payment_status',
        'payment_date',
        'received_by',
        'gateway_response',
        'remarks',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount'       => 'decimal:2',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function elearningEnrollment()
    {
        return $this->belongsTo(ElearningEnrollment::class);
    }
}
