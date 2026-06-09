<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class NotificationSetting extends Model
{
    protected $fillable = ['key', 'group', 'label', 'description', 'enabled'];

    protected $casts = ['enabled' => 'boolean'];

    /** Check if a notification type is enabled (with 5-min cache). */
    public static function isEnabled(string $key): bool
    {
        return Cache::remember("notif_setting_{$key}", 300, function () use ($key) {
            $row = static::where('key', $key)->first();
            return $row ? (bool)$row->enabled : true;   // default ON if not seeded
        });
    }

    /** Bust cache when a setting is changed. */
    protected static function booted(): void
    {
        static::saved(fn($m) => Cache::forget("notif_setting_{$m->key}"));
    }
}
