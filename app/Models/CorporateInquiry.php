<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CorporateInquiry extends Model
{
    protected $fillable = [
        'company_name', 'contact_person', 'email', 'phone', 'country',
        'training_requirement', 'participants_count', 'preferred_date',
        'preferred_mode', 'message', 'status', 'admin_notes',
    ];

    protected $casts = [
        'preferred_date' => 'date',
    ];
}
