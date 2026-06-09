<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'question_set_id', 'question_text', 'question_type',
        'is_required', 'marks', 'correct_answer',
        'exact_match_required', 'manual_review_required', 'sort_order',
    ];

    protected $casts = [
        'is_required'           => 'boolean',
        'exact_match_required'  => 'boolean',
        'manual_review_required'=> 'boolean',
    ];

    public const TYPES = [
        'mcq_single'    => 'Multiple Choice (Single Answer)',
        'mcq_multiple'  => 'Multiple Choice (Multiple Answers)',
        'true_false'    => 'True / False',
        'short_answer'  => 'Short Answer',
        'paragraph'     => 'Paragraph Answer',
        'date'          => 'Date Field',
        'file_upload'   => 'File Upload',
        'declaration'   => 'Declaration / Checkbox',
    ];

    public function questionSet()
    {
        return $this->belongsTo(QuestionSet::class);
    }

    public function options()
    {
        return $this->hasMany(QuestionOption::class)->orderBy('sort_order');
    }

    public function isAutoGradable(): bool
    {
        if ($this->manual_review_required) return false;
        return in_array($this->question_type, ['mcq_single', 'mcq_multiple', 'true_false', 'date', 'short_answer']);
    }
}
