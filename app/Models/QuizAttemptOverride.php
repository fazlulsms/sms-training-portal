<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizAttemptOverride extends Model
{
    protected $fillable = [
        'enrollment_id',
        'quiz_id',
        'extra_attempts',
        'admin_user_id',
        'reason',
    ];

    protected $casts = [
        'extra_attempts' => 'integer',
    ];

    public function enrollment()
    {
        return $this->belongsTo(ElearningEnrollment::class, 'enrollment_id');
    }

    public function quiz()
    {
        return $this->belongsTo(ElearningQuiz::class, 'quiz_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }
}
