<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ElearningLessonResource extends Model
{
    protected $fillable = [
        'lesson_id',
        'title',
        'resource_type',
        'file_path',
        'external_url',
        'status',
    ];

    public function lesson()
    {
        return $this->belongsTo(ElearningLesson::class);
    }
}