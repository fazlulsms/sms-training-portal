<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trainer extends Model
{
    protected $fillable = [
        'user_id', 'name', 'designation', 'organization',
        'email', 'phone', 'qualification',
        'photo', 'short_bio', 'expertise_areas', 'certifications',
        'experience', 'is_public', 'display_order', 'status',
        'professional_highlights', 'industries_served', 'countries_covered',
        'languages_spoken', 'training_specializations', 'audit_specializations',
        'seo_title', 'seo_description', 'seo_keywords',
        'ai_generated', 'ai_profile_data',
    ];

    protected $casts = [
        'is_public'      => 'boolean',
        'status'         => 'boolean',
        'ai_generated'   => 'boolean',
        'ai_profile_data'=> 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function schedules()
    {
        return $this->hasMany(TrainingSchedule::class, 'trainer_id');
    }

    public function publicSchedules()
    {
        return $this->hasMany(TrainingSchedule::class, 'trainer_id')
            ->where('is_public', true)
            ->whereIn('schedule_status', ['Upcoming', 'Running'])
            ->orderBy('start_date');
    }
}
