<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PptSlide extends Model
{
    protected $table = 'ppt_slides';

    protected $fillable = [
        'ppt_course_id',
        'ppt_module_id',
        'lesson_id',
        'slide_number',
        'slide_order',
        'title',
        'content_text',
        'speaker_notes',
        'image_path',
        'discussion_points',
        'ai_explanation',
        'ai_narration_script',
        'ai_key_points',
        'ai_trainer_notes',
        'ai_generated_at',
        'audio_path',
        'audio_duration',
        'audio_status',
        'audio_generated_at',
        'knowledge_check',
        'trainer_notes',
        'is_removed',
    ];

    protected function casts(): array
    {
        return [
            'ai_key_points'      => 'array',
            'knowledge_check'    => 'array',
            'ai_generated_at'    => 'datetime',
            'audio_generated_at' => 'datetime',
            'is_removed'         => 'boolean',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(PptCourse::class, 'ppt_course_id');
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(PptModule::class, 'ppt_module_id');
    }

    public function imageUrl(): ?string
    {
        if (!$this->image_path) return null;
        return asset('storage/' . $this->image_path);
    }

    public function audioUrl(): ?string
    {
        if (!$this->audio_path || $this->audio_status !== 'ready') return null;
        return asset('storage/' . $this->audio_path);
    }

    public function isAiReady(): bool
    {
        return !empty($this->ai_narration_script);
    }

    public function isAudioReady(): bool
    {
        return $this->audio_status === 'ready' && !empty($this->audio_path);
    }

    public function getStatusBadge(): string
    {
        if ($this->is_removed) return 'removed';
        if ($this->isAudioReady()) return 'audio_ready';
        if ($this->isAiReady()) return 'ai_ready';
        if (!empty($this->discussion_points)) return 'has_notes';
        return 'empty';
    }
}
