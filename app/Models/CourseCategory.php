<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CourseCategory extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'icon', 'image',
        'display_order', 'is_public', 'status',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (CourseCategory $cat) {
            if (empty($cat->slug) && !empty($cat->name)) {
                $base = Str::slug($cat->name);
                $slug = $base;
                $i = 1;
                while (static::where('slug', $slug)->where('id', '!=', $cat->id ?? 0)->exists()) {
                    $slug = "$base-$i";
                    $i++;
                }
                $cat->slug = $slug;
            }
        });
    }

    public function courses()
    {
        return $this->hasMany(Course::class, 'category_id');
    }

    public function publicCourses()
    {
        return $this->hasMany(Course::class, 'category_id')->where('is_public', true);
    }
}
