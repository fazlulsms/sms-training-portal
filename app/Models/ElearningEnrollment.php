<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ElearningEnrollment extends Model
{
    protected $fillable = [
        'user_id',
        'course_id',
        'participant_name',
        'email',
        'phone',
        'gender',
        'company',
        'designation',
        'industry',
        'experience_years',
        'country',
        'city',
        'full_address',
        'emergency_contact_name',
        'emergency_contact_phone',
        'special_requirements',
        'referral_source',
        'pre_questions',
        'coupon_code',
        'original_amount_before_discount',
        'coupon_discount',
        'amount',
        'currency',
        'payment_method',
        'payment_status',
        'transaction_id',
        'gateway_name',
        'gateway_response',
        'access_status',
        'started_at',
        'expires_at',
        'completion_status',
        'certificate_status',
        'progress_percentage',
        'certificate_number',
        'certificate_issue_date',
        'completion_date',
    ];

    protected $casts = [
        'started_at'      => 'datetime',
        'expires_at'      => 'datetime',
        'completion_date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function lessonProgress()
    {
        return $this->hasMany(LessonProgress::class, 'enrollment_id');
    }

    public function isPaymentCleared(): bool
    {
        return in_array($this->payment_status, ['paid', 'manual_approved', 'waived', 'free']);
    }

    public function isAccessible(): bool
    {
        return $this->access_status === 'unlocked';
    }
}
