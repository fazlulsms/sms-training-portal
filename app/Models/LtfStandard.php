<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class LtfStandard extends Model
{
    protected $table = 'ltf_standards';

    protected $fillable = [
        'domain', 'name', 'full_name', 'slug', 'version',
        'description', 'display_order', 'status',
    ];

    // Domain labels are stable — no separate table needed.
    public const DOMAINS = [
        'iso'               => 'ISO Standards',
        'social_compliance' => 'Social Compliance',
        'sustainability'    => 'Sustainability',
        'supply_chain'      => 'Supply Chain Security',
        'labor_rights'      => 'Labor & Human Rights',
        'grievance'         => 'Grievance & Worker Voice',
        'hse'               => 'Health & Safety',
        'quality_ops'       => 'Quality & Operations',
        'professional_dev'  => 'Professional Development',
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

    public function getDomainLabelAttribute(): string
    {
        return self::DOMAINS[$this->domain] ?? ucfirst($this->domain);
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->version
            ? "{$this->name}:{$this->version}"
            : $this->name;
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_ltf_standards');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public static function groupedForSelect(): array
    {
        return static::active()
            ->orderBy('domain')
            ->orderBy('display_order')
            ->orderBy('name')
            ->get()
            ->groupBy('domain')
            ->map(fn ($items, $domain) => [
                'label'   => self::DOMAINS[$domain] ?? ucfirst($domain),
                'options' => $items->mapWithKeys(fn ($s) => [$s->id => $s->name . ($s->version ? " ({$s->version})" : '')]),
            ])
            ->values()
            ->all();
    }
}
