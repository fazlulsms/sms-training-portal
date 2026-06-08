<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    protected $fillable = [
        'training_schedule_id',
        'selected_mode',
        'applied_fee',
        'full_name',
        'email',
        'phone',
        'company',
        'designation',
        'country',
        'country_code',
        'mobile_number',
        'full_address',
        'payment_status',
        'amount_received',
        'payment_method',
        'registration_status',
        'attendance_status',
        'completion_status',
        'certificate_number',
        'certificate_issue_date',
        'certificate_generated',
        'remarks',
    ];

    public function trainingSchedule()
    {
        return $this->belongsTo(\App\Models\TrainingSchedule::class, 'training_schedule_id');
    }

    public function schedule()
    {
        return $this->belongsTo(\App\Models\TrainingSchedule::class, 'training_schedule_id');
    }
}