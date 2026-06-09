<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParticipantTestResult extends Model
{
    protected $fillable = [
        'enrollment_id', 'question_set_id', 'overall_status',
        'attempts_used', 'best_score', 'best_percentage',
        'certificate_eligible', 'passed_at',
    ];

    protected $casts = [
        'certificate_eligible' => 'boolean',
        'passed_at'            => 'datetime',
    ];

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function questionSet()
    {
        return $this->belongsTo(QuestionSet::class);
    }

    public function isPassed(): bool
    {
        return $this->overall_status === 'passed';
    }

    public function statusLabel(): string
    {
        return match($this->overall_status) {
            'not_started'          => 'Not Started',
            'in_progress'          => 'In Progress',
            'passed'               => 'Passed',
            'failed'               => 'Failed',
            'attempt_limit_reached'=> 'Attempt Limit Reached',
            'pending_review'       => 'Pending Review',
            default                => ucfirst($this->overall_status),
        };
    }

    public function statusColor(): string
    {
        return match($this->overall_status) {
            'passed'               => 'green',
            'failed'               => 'red',
            'attempt_limit_reached'=> 'red',
            'pending_review'       => 'orange',
            'in_progress'          => 'blue',
            default                => 'gray',
        };
    }
}
