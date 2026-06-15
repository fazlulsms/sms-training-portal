<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class LtfDeliveryMethod extends Model
{
    protected $table = 'ltf_delivery_methods';

    protected $fillable = ['name', 'slug', 'description', 'display_order', 'status'];

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

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'ltf_delivery_method_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public static function forSelect(): \Illuminate\Support\Collection
    {
        return static::active()->orderBy('display_order')->orderBy('name')->pluck('name', 'id');
    }
}
