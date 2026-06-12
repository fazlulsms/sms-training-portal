<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedbackAnswer extends Model
{
    protected $fillable = [
        'response_id', 'question_id',
        'answer_rating', 'answer_bool', 'answer_text',
    ];

    protected $casts = [
        'answer_bool' => 'boolean',
    ];

    public function response(): BelongsTo
    {
        return $this->belongsTo(FeedbackResponse::class, 'response_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(FeedbackQuestion::class, 'question_id');
    }

    public function getDisplayValueAttribute(): string
    {
        return match ($this->question?->question_type) {
            'rating_5' => $this->answer_rating ? str_repeat('★', $this->answer_rating) . str_repeat('☆', 5 - $this->answer_rating) : '—',
            'yes_no'   => $this->answer_bool === null ? '—' : ($this->answer_bool ? 'Yes' : 'No'),
            default    => $this->answer_text ?? '—',
        };
    }
}
