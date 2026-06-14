<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\CouponUsage;

class CouponService
{
    /**
     * Validate a coupon code for a given context.
     *
     * @param  string  $code
     * @param  string  $email        Registrant's email (for per-user limit check)
     * @param  int     $courseId     The Course model ID
     * @param  string  $type         'ilt' or 'elearning'
     * @param  float   $amount       Original fee amount
     * @return array{valid:bool, message:string, coupon?:Coupon, discount_amount?:float, final_amount?:float}
     */
    public function validate(string $code, string $email, int $courseId, string $type, float $amount): array
    {
        $coupon = Coupon::where('code', strtoupper(trim($code)))->first();

        if (!$coupon) {
            return ['valid' => false, 'message' => 'Coupon code not found.'];
        }
        if (!$coupon->is_active) {
            return ['valid' => false, 'message' => 'This coupon is currently inactive.'];
        }
        if ($coupon->starts_at && $coupon->starts_at->isFuture()) {
            return ['valid' => false, 'message' => 'This coupon is not yet active.'];
        }
        if ($coupon->expires_at && $coupon->expires_at->isPast()) {
            return ['valid' => false, 'message' => 'This coupon has expired.'];
        }
        if ($coupon->max_uses !== null && $coupon->used_count >= $coupon->max_uses) {
            return ['valid' => false, 'message' => 'This coupon has reached its maximum usage limit.'];
        }

        // Per-user limit
        $userUsage = CouponUsage::where('coupon_id', $coupon->id)
            ->where('email', strtolower($email))
            ->count();
        if ($userUsage >= $coupon->per_user_limit) {
            return ['valid' => false, 'message' => 'You have already used this coupon the maximum number of times.'];
        }

        // Training type check
        if ($coupon->training_type !== 'both' && $coupon->training_type !== $type) {
            $label = $type === 'elearning' ? 'eLearning' : 'Instructor-Led';
            return ['valid' => false, 'message' => "This coupon is not valid for {$label} courses."];
        }

        // Course restriction
        if (!empty($coupon->course_ids) && !in_array($courseId, $coupon->course_ids)) {
            return ['valid' => false, 'message' => 'This coupon is not applicable to this course.'];
        }

        $discountAmount = $this->calcDiscount($coupon, $amount);
        $finalAmount    = max(0, $amount - $discountAmount);

        return [
            'valid'           => true,
            'coupon'          => $coupon,
            'discount_amount' => $discountAmount,
            'final_amount'    => $finalAmount,
            'message'         => $this->successMessage($coupon, $discountAmount),
        ];
    }

    public function calcDiscount(Coupon $coupon, float $amount): float
    {
        return match($coupon->type) {
            'complimentary' => $amount,
            'fixed'         => min((float) $coupon->discount_value, $amount),
            'percentage'    => round($amount * ($coupon->discount_value / 100), 2),
            default         => 0,
        };
    }

    public function recordUsage(
        Coupon $coupon,
        string $email,
        string $enrollmentType,
        int    $enrollmentId,
        float  $originalAmount,
        float  $finalAmount,
        string $courseName = ''
    ): void {
        CouponUsage::create([
            'coupon_id'       => $coupon->id,
            'email'           => strtolower($email),
            'enrollment_type' => $enrollmentType,
            'enrollment_id'   => $enrollmentId,
            'course_name'     => $courseName,
            'original_amount' => $originalAmount,
            'discount_amount' => $originalAmount - $finalAmount,
            'final_amount'    => $finalAmount,
            'used_at'         => now(),
        ]);

        $coupon->increment('used_count');
    }

    private function successMessage(Coupon $coupon, float $discountAmount): string
    {
        return match($coupon->type) {
            'complimentary' => "🎉 Complimentary access granted! You will be enrolled free of charge.",
            'fixed'         => "✅ Coupon applied: BDT " . number_format($discountAmount) . " discount.",
            'percentage'    => "✅ Coupon applied: {$coupon->discount_value}% discount (BDT " . number_format($discountAmount) . " off).",
            default         => "✅ Coupon applied.",
        };
    }
}
