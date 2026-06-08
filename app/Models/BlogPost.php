<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BlogPost extends Model
{
    protected $fillable = [
        'title', 'slug', 'blog_category_id', 'course_id',
        'featured_image', 'excerpt', 'content',
        'seo_title', 'seo_description', 'author',
        'status', 'published_at', 'view_count',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    // ── Relationships ────────────────────────────────────────
    public function category()
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // ── Scopes ───────────────────────────────────────────────
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                     ->where('published_at', '<=', now());
    }

    // ── Helpers ──────────────────────────────────────────────
    public function getReadingTimeAttribute(): int
    {
        $words = str_word_count(strip_tags($this->content));
        return max(1, (int) ceil($words / 200));
    }

    public static function generateSlug(string $title): string
    {
        $slug = Str::slug($title);
        $count = static::where('slug', 'like', "$slug%")->count();
        return $count ? "$slug-$count" : $slug;
    }
}
