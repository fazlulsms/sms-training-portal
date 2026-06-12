<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeedbackQuestion extends Model
{
    protected $fillable = [
        'template_id', 'question_text', 'question_type',
        'category', 'options', 'is_required', 'sort_order',
    ];

    protected $casts = [
        'options'     => 'array',
        'is_required' => 'boolean',
    ];

    public static array $TYPES = [
        'rating_5' => '1–5 Star Rating',
        'yes_no'   => 'Yes / No',
        'text'     => 'Open Text',
        'select'   => 'Dropdown Select',
    ];

    public static array $CATEGORIES = [
        'overall'   => 'Overall',
        'content'   => 'Content Quality',
        'trainer'   => 'Trainer Evaluation',
        'platform'  => 'Platform Experience',
        'elearning' => 'eLearning Specific',
        'open'      => 'Open Feedback',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(FeedbackTemplate::class, 'template_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(FeedbackAnswer::class, 'question_id');
    }
}
