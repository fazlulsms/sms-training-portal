<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonAudio extends Model
{
    protected $table = 'lesson_audio';

    protected $fillable = [
        'lesson_id',
        'block_id',
        'audio_type',
        'voice',
        'language',
        'file_path',
        'duration_seconds',
        'status',
        'error_message',
        'generated_at',
    ];

    protected $casts = [
        'generated_at'     => 'datetime',
        'duration_seconds' => 'integer',
    ];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(ElearningLesson::class, 'lesson_id');
    }

    public function block(): BelongsTo
    {
        return $this->belongsTo(LessonBlock::class, 'block_id');
    }

    public function isReady(): bool
    {
        return $this->status === 'ready' && $this->file_path !== null;
    }

    public function publicUrl(): ?string
    {
        if (!$this->file_path) return null;
        return asset('storage/' . $this->file_path);
    }
}
