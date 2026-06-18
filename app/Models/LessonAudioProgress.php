<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonAudioProgress extends Model
{
    protected $table = 'lesson_audio_progress';

    protected $fillable = [
        'enrollment_id',
        'lesson_id',
        'audio_id',
        'user_id',
        'high_water_mark',
        'seconds_listened',
        'duration_seconds',
        'completion_percentage',
        'is_completed',
        'completed_at',
        'last_listened_at',
    ];

    protected $casts = [
        'high_water_mark'       => 'float',
        'seconds_listened'      => 'float',
        'completion_percentage' => 'float',
        'is_completed'          => 'boolean',
        'completed_at'          => 'datetime',
        'last_listened_at'      => 'datetime',
    ];

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(ElearningEnrollment::class);
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(ElearningLesson::class);
    }

    public function audio(): BelongsTo
    {
        return $this->belongsTo(LessonAudio::class, 'audio_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
