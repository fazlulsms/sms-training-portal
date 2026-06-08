<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id',
        'enrollment_id',
        'participant_name',
        'description',
        'quantity',
        'unit_price',
        'line_total',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }
}