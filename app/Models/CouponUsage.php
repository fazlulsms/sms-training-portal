<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CouponUsage extends Model
{
    protected $fillable = [
        'coupon_id', 'email', 'enrollment_type', 'enrollment_id',
        'course_name', 'original_amount', 'discount_amount', 'final_amount', 'used_at',
    ];

    protected $casts = [
        'used_at' => 'datetime',
    ];

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }
}
