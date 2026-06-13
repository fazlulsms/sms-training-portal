<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BlogPost extends Model
{
    protected $fillable = [
        'title', 'slug', 'blog_category_id', 'course_id', 'training_schedule_id',
        'article_type', 'ai_generated', 'ai_generated_at',
        'featured_image', 'excerpt', 'content',
        'seo_title', 'seo_description', 'og_title', 'og_description',
        'focus_keywords', 'tags', 'hashtags',
        'social_linkedin', 'social_facebook', 'social_twitter', 'social_instagram',
        'author', 'status', 'published_at', 'view_count',
        'approved_by', 'approved_at', 'change_log',
    ];

    protected $casts = [
        'published_at'    => 'datetime',
        'ai_generated_at' => 'datetime',
        'approved_at'     => 'datetime',
        'ai_generated'    => 'boolean',
        'tags'            => 'array',
        'change_log'      => 'array',
    ];

    // Status values
    public const STATUS_DRAFT        = 'draft';
    public const STATUS_UNDER_REVIEW = 'under_review';
    public const STATUS_APPROVED     = 'approved';
    public const STATUS_PUBLISHED    = 'published';
    public const STATUS_ARCHIVED     = 'archived';

    // Article types
    public const TYPE_BLOG         = 'blog_post';
    public const TYPE_TRAINING_NEWS = 'training_news';
    public const TYPE_SUCCESS_STORY = 'success_story';
    public const TYPE_ANNOUNCEMENT  = 'course_announcement';

    // ── Relationships ────────────────────────────────────────
    public function category()
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function trainingSchedule()
    {
        return $this->belongsTo(TrainingSchedule::class, 'training_schedule_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // ── Scopes ───────────────────────────────────────────────
    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED)
                     ->where('published_at', '<=', now());
    }

    public function scopeTrainingNews($query)
    {
        return $query->whereIn('article_type', [self::TYPE_TRAINING_NEWS, self::TYPE_SUCCESS_STORY, self::TYPE_ANNOUNCEMENT]);
    }

    // ── Helpers ──────────────────────────────────────────────
    public function getReadingTimeAttribute(): int
    {
        $words = str_word_count(strip_tags($this->content ?? ''));
        return max(1, (int) ceil($words / 200));
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_DRAFT        => 'Draft',
            self::STATUS_UNDER_REVIEW => 'Under Review',
            self::STATUS_APPROVED     => 'Approved',
            self::STATUS_PUBLISHED    => 'Published',
            self::STATUS_ARCHIVED     => 'Archived',
            default                   => ucfirst($this->status),
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            self::STATUS_DRAFT        => 'badge-secondary',
            self::STATUS_UNDER_REVIEW => 'badge-warning',
            self::STATUS_APPROVED     => 'badge-info',
            self::STATUS_PUBLISHED    => 'badge-success',
            self::STATUS_ARCHIVED     => 'badge-dark',
            default                   => 'badge-secondary',
        };
    }

    public function getArticleTypeLabelAttribute(): string
    {
        return match($this->article_type) {
            self::TYPE_TRAINING_NEWS  => 'Training News',
            self::TYPE_SUCCESS_STORY  => 'Success Story',
            self::TYPE_ANNOUNCEMENT   => 'Course Announcement',
            default                   => 'Blog Post',
        };
    }

    public function appendChangeLog(string $action, ?int $userId = null): void
    {
        $log   = $this->change_log ?? [];
        $log[] = [
            'action'    => $action,
            'user_id'   => $userId ?? auth()->id(),
            'timestamp' => now()->toIso8601String(),
        ];
        $this->change_log = $log;
    }

    public static function generateSlug(string $title): string
    {
        $slug  = Str::slug($title);
        $count = static::where('slug', 'like', "$slug%")->count();
        return $count ? "$slug-$count" : $slug;
    }
}
