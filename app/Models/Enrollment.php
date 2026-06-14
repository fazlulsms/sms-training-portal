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
        'gender',
        'company',
        'designation',
        'industry',
        'experience_years',
        'country',
        'country_code',
        'city',
        'mobile_number',
        'full_address',
        'emergency_contact_name',
        'emergency_contact_phone',
        'special_requirements',
        'referral_source',
        'pre_questions',
        'payment_status',
        'amount_received',
        'payment_method',
        'registration_status',
        'attendance_status',
        'completion_status',
        'certificate_number',
        'certificate_issue_date',
        'certificate_generated',
        'certificate_template',
        'certificate_email_sent',
        'certificate_email_sent_at',
        'certificate_generated_by',
        'certificate_generated_at',
        'exam_email_sent', 'exam_email_sent_at',
        'remarks',
    ];

    protected $casts = [
        'certificate_email_sent'    => 'boolean',
        'certificate_email_sent_at' => 'datetime',
        'certificate_generated_at'  => 'datetime',
        'exam_email_sent'           => 'boolean',
        'exam_email_sent_at'        => 'datetime',
    ];

    public function trainingSchedule()
    {
        return $this->belongsTo(\App\Models\TrainingSchedule::class, 'training_schedule_id');
    }

    public function schedule()
    {
        return $this->belongsTo(\App\Models\TrainingSchedule::class, 'training_schedule_id');
    }

    public function testAttempts()
    {
        return $this->hasMany(\App\Models\ParticipantTestAttempt::class);
    }

    public function testResult()
    {
        return $this->hasOne(\App\Models\ParticipantTestResult::class);
    }
}