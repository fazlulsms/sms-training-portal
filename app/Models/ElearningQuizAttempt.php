<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ElearningQuizAttempt extends Model
{
    protected $fillable = [
        'enrollment_id',
        'quiz_id',
        'attempt_no',
        'score',
        'total_marks',
        'passed',
        'started_at',
        'submitted_at',
    ];

    public function enrollment()
    {
        return $this->belongsTo(ElearningEnrollment::class);
    }

    public function quiz()
    {
        return $this->belongsTo(ElearningQuiz::class);
    }

    public function answers()
    {
        return $this->hasMany(ElearningQuizAnswer::class, 'attempt_id');
    }
}