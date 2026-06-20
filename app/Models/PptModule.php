<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PptModule extends Model
{
    protected $table = 'ppt_modules';

    protected $fillable = [
        'ppt_course_id',
        'title',
        'description',
        'module_order',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(PptCourse::class, 'ppt_course_id');
    }

    public function slides(): HasMany
    {
        return $this->hasMany(PptSlide::class)->where('is_removed', false)->orderBy('slide_order');
    }
}
