<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PptCourse extends Model
{
    protected $table = 'ppt_courses';

    protected $fillable = [
        'title',
        'description',
        'status',
        'original_filename',
        'file_path',
        'file_size',
        'total_slides',
        'processing_error',
        'course_id',
        'created_by',
        'completion_mode',
        'settings',
    ];

    protected function casts(): array
    {
        return [
            'settings' => 'array',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function modules(): HasMany
    {
        return $this->hasMany(PptModule::class)->orderBy('module_order');
    }

    public function slides(): HasMany
    {
        return $this->hasMany(PptSlide::class)->where('is_removed', false)->orderBy('slide_order');
    }

    public function allSlides(): HasMany
    {
        return $this->hasMany(PptSlide::class)->orderBy('slide_number');
    }

    public function getFileSizeHumanAttribute(): string
    {
        $bytes = (int) $this->file_size;
        if ($bytes < 1024) return $bytes . ' B';
        if ($bytes < 1048576) return round($bytes / 1024, 1) . ' KB';
        return round($bytes / 1048576, 1) . ' MB';
    }

    public function getCompletionPercent(): float
    {
        $total = $this->slides()->count();
        if ($total === 0) return 0;
        $withAudio = $this->slides()->where('audio_status', 'ready')->count();
        return round(($withAudio / $total) * 100, 1);
    }

    public function isReady(): bool
    {
        return in_array($this->status, ['ready', 'published']);
    }
}
