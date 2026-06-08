<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    protected $fillable = [
        'enrollment_id',
        'elearning_enrollment_id',
        'quiz_id',
        'total_questions',
        'correct_answers',
        'score',
    ];
}