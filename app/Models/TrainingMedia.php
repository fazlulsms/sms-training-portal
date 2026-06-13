<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingMedia extends Model
{
    protected $fillable = [
        'training_schedule_id', 'media_type', 'file_path', 'file_name',
        'file_size', 'mime_type', 'caption', 'alt_text', 'seo_description',
        'is_featured', 'sort_order', 'ai_captions_generated', 'uploaded_by',
    ];

    protected $casts = [
        'is_featured'          => 'boolean',
        'ai_captions_generated' => 'boolean',
    ];

    public static array $types = [
        'cover'    => 'Cover Photo',
        'gallery'  => 'Gallery Photo',
        'group'    => 'Group Photo',
        'trainer'  => 'Trainer Photo',
        'venue'    => 'Venue Photo',
        'activity' => 'Activity Photo',
    ];

    public function schedule()
    {
        return $this->belongsTo(TrainingSchedule::class, 'training_schedule_id');
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }

    public function getFileSizeHumanAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes < 1024) return $bytes . ' B';
        if ($bytes < 1048576) return round($bytes / 1024, 1) . ' KB';
        return round($bytes / 1048576, 1) . ' MB';
    }
}
