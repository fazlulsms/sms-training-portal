<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizReviewGate extends Model
{
    protected $fillable = [
        'enrollment_id',
        'quiz_id',
        'required_lesson_ids',
        'reviewed_lesson_ids',
        'extra_attempts_granted',
        'status',
        'triggered_at',
        'unlocked_at',
    ];

    protected $casts = [
        'required_lesson_ids'    => 'array',
        'reviewed_lesson_ids'    => 'array',
        'extra_attempts_granted' => 'integer',
        'triggered_at'           => 'datetime',
        'unlocked_at'            => 'datetime',
    ];

    public function enrollment()
    {
        return $this->belongsTo(ElearningEnrollment::class, 'enrollment_id');
    }

    public function quiz()
    {
        return $this->belongsTo(ElearningQuiz::class, 'quiz_id');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Record a lesson review visit. Returns true if the gate completed (all reviewed).
     */
    public function recordLessonVisit(int $lessonId): bool
    {
        $reviewed = $this->reviewed_lesson_ids ?? [];

        if (!in_array($lessonId, $reviewed)) {
            $reviewed[] = $lessonId;
            $this->reviewed_lesson_ids = $reviewed;
            $this->save();
        }

        $allReviewed = empty(array_diff($this->required_lesson_ids ?? [], $this->reviewed_lesson_ids ?? []));

        if ($allReviewed && $this->status === 'pending') {
            $this->update([
                'status'      => 'completed',
                'unlocked_at' => now(),
            ]);
            return true;
        }

        return false;
    }

    public function reviewedCount(): int
    {
        return count($this->reviewed_lesson_ids ?? []);
    }

    public function requiredCount(): int
    {
        return count($this->required_lesson_ids ?? []);
    }
}
