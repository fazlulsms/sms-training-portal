<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class LtfAudienceType extends Model
{
    protected $table = 'ltf_audience_types';

    protected $fillable = [
        'name', 'slug', 'description', 'display_order', 'status',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $model) {
            if (empty($model->slug)) {
                $model->slug = self::uniqueSlug(Str::slug($model->name));
            }
        });
    }

    private static function uniqueSlug(string $base): string
    {
        $slug = $base;
        $i    = 1;
        while (static::where('slug', $slug)->exists()) {
            $slug = "$base-$i";
            $i++;
        }
        return $slug;
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_ltf_audiences');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public static function forSelect(): \Illuminate\Support\Collection
    {
        return static::active()
            ->orderBy('display_order')
            ->orderBy('name')
            ->pluck('name', 'id');
    }
}
