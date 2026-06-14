<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code', 'name', 'description', 'type', 'discount_value',
        'training_type', 'course_ids',
        'starts_at', 'expires_at',
        'max_uses', 'per_user_limit', 'used_count',
        'is_active', 'notes',
    ];

    protected $casts = [
        'course_ids' => 'array',
        'starts_at'  => 'date',
        'expires_at' => 'date',
        'is_active'  => 'boolean',
    ];

    public function usages()
    {
        return $this->hasMany(CouponUsage::class);
    }

    public function getTypeLabel(): string
    {
        return match($this->type) {
            'fixed'          => 'Fixed Amount',
            'percentage'     => 'Percentage',
            'complimentary'  => 'Complimentary (100% Free)',
            default          => ucfirst($this->type),
        };
    }

    public function getDiscountDisplay(): string
    {
        return match($this->type) {
            'fixed'         => 'BDT ' . number_format($this->discount_value),
            'percentage'    => $this->discount_value . '%',
            'complimentary' => '100% Free',
            default         => '—',
        };
    }

    public function isValid(): bool
    {
        if (!$this->is_active) return false;
        if ($this->starts_at && $this->starts_at->isFuture()) return false;
        if ($this->expires_at && $this->expires_at->isPast()) return false;
        if ($this->max_uses !== null && $this->used_count >= $this->max_uses) return false;
        return true;
    }

    public function totalDiscountGiven(): float
    {
        return (float) $this->usages()->sum('discount_amount');
    }

    public function totalRevenueImpact(): float
    {
        return (float) $this->usages()->sum('original_amount');
    }
}
