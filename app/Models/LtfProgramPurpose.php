<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class LtfProgramPurpose extends Model
{
    protected $table = 'ltf_program_purposes';

    protected $fillable = [
        'name', 'slug', 'description',
        'suggested_framework_id', 'display_order', 'status',
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

    public function suggestedFramework(): BelongsTo
    {
        return $this->belongsTo(LtfLearningFramework::class, 'suggested_framework_id');
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'ltf_program_purpose_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public static function forSelect(): \Illuminate\Support\Collection
    {
        return static::active()->orderBy('display_order')->orderBy('name')->pluck('name', 'id');
    }

    /**
     * Returns a map of {purpose_id => suggested_framework_id} for JS auto-suggest.
     *
     * @return array<int, int|null>
     */
    public static function suggestionMap(): array
    {
        return static::active()
            ->orderBy('display_order')
            ->pluck('suggested_framework_id', 'id')
            ->toArray();
    }
}
