<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonProgress extends Model
{
    protected $fillable = [
        'user_id',
        'enrollment_id',
        'course_id',
        'lesson_id',
        'status',
        'started_at',
        'completed_at',
        'time_spent_seconds',
    ];

    protected $casts = [
        'started_at'   => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function enrollment()
    {
        return $this->belongsTo(ElearningEnrollment::class);
    }

    public function lesson()
    {
        return $this->belongsTo(ElearningLesson::class);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }
}
