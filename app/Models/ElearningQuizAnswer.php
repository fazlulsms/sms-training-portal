<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ElearningQuizAnswer extends Model
{
    protected $fillable = [
        'attempt_id',
        'question_id',
        'selected_answer',
        'is_correct',
        'marks_obtained',
    ];

    public function attempt()
    {
        return $this->belongsTo(ElearningQuizAttempt::class);
    }

    public function question()
    {
        return $this->belongsTo(ElearningQuizQuestion::class);
    }
}