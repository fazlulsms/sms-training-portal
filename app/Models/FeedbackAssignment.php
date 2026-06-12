<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class FeedbackAssignment extends Model
{
    protected $fillable = [
        'template_id', 'assignable_type', 'assignable_id',
        'is_required', 'require_for_certificate',
        'due_days_after_completion', 'is_active',
    ];

    protected $casts = [
        'is_required'             => 'boolean',
        'require_for_certificate' => 'boolean',
        'is_active'               => 'boolean',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(FeedbackTemplate::class, 'template_id');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(FeedbackResponse::class, 'assignment_id');
    }

    public function completedResponses(): HasMany
    {
        return $this->hasMany(FeedbackResponse::class, 'assignment_id')->where('is_complete', true);
    }
}
