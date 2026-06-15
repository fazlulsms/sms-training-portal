<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class LtfCourseType extends Model
{
    protected $table = 'ltf_course_types';

    protected $fillable = [
        'group', 'name', 'slug', 'description', 'display_order', 'status',
    ];

    public const GROUPS = [
        'elearning'  => 'eLearning',
        'ilt'        => 'Instructor-Led Training (ILT)',
        'assessment' => 'Assessment-Based Programs',
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

    public function getGroupLabelAttribute(): string
    {
        return self::GROUPS[$this->group] ?? ucfirst($this->group);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'ltf_course_type_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public static function groupedForSelect(): array
    {
        return static::active()
            ->orderBy('group')
            ->orderBy('display_order')
            ->orderBy('name')
            ->get()
            ->groupBy('group')
            ->map(fn ($items, $group) => [
                'label'   => self::GROUPS[$group] ?? ucfirst($group),
                'options' => $items->pluck('name', 'id'),
            ])
            ->values()
            ->all();
    }
}
