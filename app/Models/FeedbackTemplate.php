<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedbackTemplate extends Model
{
    protected $fillable = [
        'name', 'type', 'description', 'is_default', 'is_active',
        'allow_multiple', 'require_for_certificate', 'created_by',
    ];

    protected $casts = [
        'is_default'              => 'boolean',
        'is_active'               => 'boolean',
        'allow_multiple'          => 'boolean',
        'require_for_certificate' => 'boolean',
    ];

    public static array $TYPES = [
        'ilt'       => 'Instructor-Led Training',
        'elearning' => 'eLearning Course',
        'webinar'   => 'Webinar',
        'workshop'  => 'Workshop',
        'trainer'   => 'Trainer Evaluation',
    ];

    public function questions(): HasMany
    {
        return $this->hasMany(FeedbackQuestion::class, 'template_id')->orderBy('sort_order');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(FeedbackAssignment::class, 'template_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getTypeLabelAttribute(): string
    {
        return self::$TYPES[$this->type] ?? ucfirst($this->type);
    }
}
