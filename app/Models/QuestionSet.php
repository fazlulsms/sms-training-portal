<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionSet extends Model
{
    protected $fillable = [
        'title', 'description', 'course_id', 'status',
        'total_marks', 'pass_mark', 'pass_percentage',
        'allowed_attempts', 'time_limit_minutes',
        'show_result_to_participant', 'allow_certificate_after_pass',
        'created_by',
    ];

    protected $casts = [
        'show_result_to_participant' => 'boolean',
        'allow_certificate_after_pass' => 'boolean',
    ];

    public function questions()
    {
        return $this->hasMany(Question::class)->orderBy('sort_order');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function trainingAssignment()
    {
        return $this->hasOne(TrainingQuestionAssignment::class);
    }

    /**
     * Compute effective pass mark (absolute or from percentage)
     */
    public function effectivePassMark(): int
    {
        if ($this->pass_mark) {
            return $this->pass_mark;
        }
        if ($this->pass_percentage) {
            return (int) round($this->total_marks * $this->pass_percentage / 100);
        }
        return (int) round($this->total_marks * 0.5); // default 50%
    }
}
