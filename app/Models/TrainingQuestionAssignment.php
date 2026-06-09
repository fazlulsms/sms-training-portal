<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingQuestionAssignment extends Model
{
    protected $fillable = [
        'training_schedule_id', 'question_set_id',
        'allowed_attempts', 'exam_active_after_attendance',
    ];

    protected $casts = [
        'exam_active_after_attendance' => 'boolean',
    ];

    public function trainingSchedule()
    {
        return $this->belongsTo(TrainingSchedule::class);
    }

    public function questionSet()
    {
        return $this->belongsTo(QuestionSet::class);
    }

    public function effectiveAttempts(): int
    {
        return $this->allowed_attempts ?? $this->questionSet->allowed_attempts ?? 1;
    }
}
