<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizAdminAction extends Model
{
    protected $fillable = [
        'action',
        'admin_user_id',
        'enrollment_id',
        'quiz_id',
        'reason',
        'previous_score',
        'new_status',
        'metadata',
    ];

    protected $casts = [
        'metadata'       => 'array',
        'previous_score' => 'decimal:2',
    ];

    // Action type constants
    const RESET_ATTEMPTS    = 'reset_attempts';
    const ADD_EXTRA_ATTEMPT = 'add_extra_attempt';
    const MARK_PASSED       = 'mark_passed';

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }

    public function enrollment()
    {
        return $this->belongsTo(ElearningEnrollment::class, 'enrollment_id');
    }

    public function quiz()
    {
        return $this->belongsTo(ElearningQuiz::class, 'quiz_id');
    }

    public static function log(
        string $action,
        int    $adminId,
        int    $enrollmentId,
        int    $quizId,
        string $reason,
        ?float $previousScore = null,
        ?string $newStatus    = null,
        array  $metadata      = []
    ): self {
        return self::create([
            'action'         => $action,
            'admin_user_id'  => $adminId,
            'enrollment_id'  => $enrollmentId,
            'quiz_id'        => $quizId,
            'reason'         => $reason,
            'previous_score' => $previousScore,
            'new_status'     => $newStatus,
            'metadata'       => $metadata ?: null,
        ]);
    }

    public function actionLabel(): string
    {
        return match ($this->action) {
            self::RESET_ATTEMPTS    => 'Reset Attempts',
            self::ADD_EXTRA_ATTEMPT => 'Extra Attempt Granted',
            self::MARK_PASSED       => 'Marked as Passed',
            default                 => ucwords(str_replace('_', ' ', $this->action)),
        };
    }
}
