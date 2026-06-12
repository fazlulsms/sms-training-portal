<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class FeedbackResponse extends Model
{
    protected $fillable = [
        'assignment_id', 'token', 'user_id', 'enrollment_id',
        'elearning_enrollment_id', 'trainer_id', 'respondent_name',
        'respondent_email', 'is_complete', 'submitted_at', 'is_demo',
        'testimonial_consent', 'testimonial_approved', 'testimonial_text',
        'overall_rating',
    ];

    protected $casts = [
        'is_complete'          => 'boolean',
        'is_demo'              => 'boolean',
        'testimonial_consent'  => 'boolean',
        'testimonial_approved' => 'boolean',
        'submitted_at'         => 'datetime',
        'overall_rating'       => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (empty($model->token)) {
                $model->token = Str::random(48);
            }
        });
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(FeedbackAssignment::class, 'assignment_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(FeedbackAnswer::class, 'response_id');
    }

    public function getSubmitUrlAttribute(): string
    {
        return route('feedback.show', $this->token);
    }

    public function scopeCompleted($query)
    {
        return $query->where('is_complete', true);
    }

    public function scopePending($query)
    {
        return $query->where('is_complete', false);
    }
}
