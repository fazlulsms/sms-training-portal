<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParticipantTestAnswer extends Model
{
    protected $fillable = [
        'attempt_id', 'question_id', 'answer_text',
        'answer_options', 'file_path', 'marks_awarded',
        'is_correct', 'manual_graded', 'reviewer_notes',
    ];

    protected $casts = [
        'answer_options' => 'array',
        'is_correct'     => 'boolean',
        'manual_graded'  => 'boolean',
    ];

    public function attempt()
    {
        return $this->belongsTo(ParticipantTestAttempt::class, 'attempt_id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
