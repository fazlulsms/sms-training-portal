<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ElearningQuizQuestion extends Model
{
    protected $fillable = [
        'quiz_id',
        'question_text',
        'question_type',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'correct_answer',
        'marks',
        'status',
    ];

    public function quiz()
    {
        return $this->belongsTo(ElearningQuiz::class);
    }
}