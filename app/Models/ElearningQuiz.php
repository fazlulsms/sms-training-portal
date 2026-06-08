<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ElearningQuiz extends Model
{
    protected $fillable = [
        'lesson_id',
        'title',
        'description',
        'pass_mark',
        'max_attempt',
        'status',
    ];

    public function lesson()
    {
        return $this->belongsTo(ElearningLesson::class, 'lesson_id', 'id');
    }

    public function questions()
    {
        return $this->hasMany(ElearningQuizQuestion::class, 'quiz_id', 'id');
    }

    public function attempts()
    {
        return $this->hasMany(\App\Models\QuizAttempt::class, 'quiz_id', 'id');
    }

    public function attemptsByEnrollment(int $enrollmentId)
    {
        return $this->hasMany(\App\Models\QuizAttempt::class, 'quiz_id', 'id')
            ->where('elearning_enrollment_id', $enrollmentId);
    }
}