<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiUsageLog extends Model
{
    protected $fillable = [
        'user_id',
        'feature_name',
        'model',
        'prompt_tokens',
        'completion_tokens',
        'total_tokens',
        'estimated_cost_usd',
        'request_status',
        'error_message',
    ];

    protected $casts = [
        'estimated_cost_usd' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── Scopes ────────────────────────────────────────────────

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeThisMonth($query)
    {
        return $query->whereYear('created_at', now()->year)
                     ->whereMonth('created_at', now()->month);
    }

    public function scopeSuccessful($query)
    {
        return $query->where('request_status', 'success');
    }
}
