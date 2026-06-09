<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParticipantTestAttempt extends Model
{
    protected $fillable = [
        'enrollment_id', 'question_set_id', 'exam_token', 'attempt_number',
        'status', 'score', 'total_marks', 'percentage', 'pass_fail',
        'manual_review_pending', 'started_at', 'submitted_at',
        'ip_address', 'user_agent',
    ];

    protected $casts = [
        'pass_fail'             => 'boolean',
        'manual_review_pending' => 'boolean',
        'started_at'            => 'datetime',
        'submitted_at'          => 'datetime',
    ];

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function questionSet()
    {
        return $this->belongsTo(QuestionSet::class);
    }

    public function answers()
    {
        return $this->hasMany(ParticipantTestAnswer::class, 'attempt_id');
    }

    public function isSubmitted(): bool
    {
        return in_array($this->status, ['submitted','pending_review','passed','failed','attempt_limit_reached']);
    }

    public function isActive(): bool
    {
        return in_array($this->status, ['not_started','in_progress']);
    }
}
