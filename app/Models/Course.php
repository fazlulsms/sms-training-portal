<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Course extends Model
{
    protected $fillable = [
        'name', 'code', 'slug', 'category', 'category_id', 'status',
        'delivery_type', 'language', 'duration', 'cpd_hours',
        'course_type', 'description', 'short_description', 'full_description',
        'learning_objectives', 'course_outline', 'who_should_attend', 'prerequisites',
        'banner_image', 'certificate_type', 'certification_info',
        'course_fee', 'public_price', 'access_days', 'passing_score',
        'certificate_template', 'lesson_count', 'certification_remarks',
        'is_public', 'is_featured', 'display_order', 'featured_order',
        'course_video_url', 'faq', 'seo_title', 'seo_description', 'seo_keywords',
        'ai_generated', 'ai_course_structure',
        'gen_status', 'gen_progress', 'gen_started_at', 'gen_completed_at',
    ];

    protected $casts = [
        'is_public'           => 'boolean',
        'is_featured'         => 'boolean',
        'ai_generated'        => 'boolean',
        'ai_course_structure' => 'array',
        'gen_progress'        => 'array',
        'gen_started_at'      => 'datetime',
        'gen_completed_at'    => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(function (Course $course) {
            if (empty($course->slug) && !empty($course->name)) {
                $base = Str::slug($course->name);
                $slug = $base;
                $i = 1;
                while (static::where('slug', $slug)->where('id', '!=', $course->id ?? 0)->exists()) {
                    $slug = "$base-$i";
                    $i++;
                }
                $course->slug = $slug;
            }
        });
    }

    public function courseCategory()
    {
        return $this->belongsTo(CourseCategory::class, 'category_id');
    }

    public function lessons()
    {
        return $this->hasMany(\App\Models\ElearningLesson::class, 'course_id', 'id');
    }

    public function elearningLessons()
    {
        return $this->hasMany(\App\Models\ElearningLesson::class, 'course_id', 'id');
    }

    public function trainingSchedules()
    {
        return $this->hasMany(\App\Models\TrainingSchedule::class, 'course_id', 'id');
    }

    public function publicSchedules()
    {
        return $this->hasMany(\App\Models\TrainingSchedule::class, 'course_id', 'id')
            ->where('is_public', true)
            ->whereIn('schedule_status', ['Upcoming', 'Running'])
            ->where(function ($q) {
                $q->whereNull('registration_deadline')
                  ->orWhere('registration_deadline', '>=', now()->toDateString());
            })
            ->where(function ($q) {
                $q->whereNull('available_seats')
                  ->orWhere('available_seats', '>', 0);
            });
    }

    public function blogPosts()
    {
        return $this->hasMany(\App\Models\BlogPost::class);
    }

    public function testimonials()
    {
        return $this->hasMany(\App\Models\Testimonial::class)
                    ->whereIn('status', ['approved', 'featured']);
    }

    public function getMinFeeAttribute(): ?float
    {
        $fees = $this->trainingSchedules()
            ->where('is_public', true)
            ->get()
            ->flatMap(fn ($s) => array_filter([$s->physical_fee, $s->online_fee]))
            ->filter()
            ->values();
        return $fees->isEmpty() ? null : $fees->min();
    }

    public function getMaxFeeAttribute(): ?float
    {
        $fees = $this->trainingSchedules()
            ->where('is_public', true)
            ->get()
            ->flatMap(fn ($s) => array_filter([$s->physical_fee, $s->online_fee]))
            ->filter()
            ->values();
        return $fees->isEmpty() ? null : $fees->max();
    }
}
