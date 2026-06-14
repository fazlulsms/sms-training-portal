<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Course;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::withCount('usages')->latest()->paginate(20);
        return view('coupons.index', compact('coupons'));
    }

    public function create()
    {
        $courses = Course::where('is_public', true)->orderBy('name')->get(['id', 'name', 'course_type']);
        return view('coupons.form', compact('courses'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        Coupon::create($data);
        return redirect()->route('admin.coupons.index')->with('success', 'Coupon created successfully.');
    }

    public function show(Coupon $coupon)
    {
        $usages = CouponUsage::where('coupon_id', $coupon->id)->latest()->paginate(25);
        return view('coupons.show', compact('coupon', 'usages'));
    }

    public function edit(Coupon $coupon)
    {
        $courses = Course::where('is_public', true)->orderBy('name')->get(['id', 'name', 'course_type']);
        return view('coupons.form', compact('coupon', 'courses'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        $data = $this->validated($request, $coupon->id);
        $coupon->update($data);
        return redirect()->route('admin.coupons.index')->with('success', 'Coupon updated.');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return redirect()->route('admin.coupons.index')->with('success', 'Coupon deleted.');
    }

    public function toggle(Coupon $coupon)
    {
        $coupon->update(['is_active' => !$coupon->is_active]);
        return back()->with('success', $coupon->is_active ? 'Coupon activated.' : 'Coupon deactivated.');
    }

    // ── Public AJAX endpoint ─────────────────────────────────────────────────
    public function validateCoupon(Request $request)
    {
        $request->validate([
            'code'      => 'required|string',
            'email'     => 'required|email',
            'course_id' => 'required|integer',
            'type'      => 'required|in:ilt,elearning',
            'amount'    => 'required|numeric|min:0',
        ]);

        $svc    = app(\App\Services\CouponService::class);
        $result = $svc->validate(
            $request->code,
            $request->email,
            (int) $request->course_id,
            $request->type,
            (float) $request->amount
        );

        if (!$result['valid']) {
            return response()->json(['valid' => false, 'message' => $result['message']], 422);
        }

        $coupon = $result['coupon'];
        return response()->json([
            'valid'            => true,
            'message'          => $result['message'],
            'is_complimentary' => $coupon->type === 'complimentary',
            'type'             => $coupon->type,
            'discount_value'   => $coupon->discount_value,
            'discount_amount'  => $result['discount_amount'],
            'final_amount'     => $result['final_amount'],
        ]);
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        $rules = [
            'code'           => 'required|string|max:50|unique:coupons,code' . ($ignoreId ? ",{$ignoreId}" : ''),
            'name'           => 'required|string|max:150',
            'description'    => 'nullable|string|max:500',
            'type'           => 'required|in:fixed,percentage,complimentary',
            'discount_value' => 'nullable|numeric|min:0|max:100000',
            'training_type'  => 'required|in:both,elearning,ilt',
            'course_ids'     => 'nullable|array',
            'course_ids.*'   => 'integer|exists:courses,id',
            'starts_at'      => 'nullable|date',
            'expires_at'     => 'nullable|date|after_or_equal:starts_at',
            'max_uses'       => 'nullable|integer|min:1',
            'per_user_limit' => 'required|integer|min:1|max:10',
            'is_active'      => 'boolean',
            'notes'          => 'nullable|string|max:1000',
        ];

        $data = $request->validate($rules);

        // percentage cap
        if ($data['type'] === 'percentage' && isset($data['discount_value'])) {
            $data['discount_value'] = min($data['discount_value'], 100);
        }
        // complimentary has no discount_value
        if ($data['type'] === 'complimentary') {
            $data['discount_value'] = null;
        }

        $data['is_active'] = $request->boolean('is_active', true);
        $data['code']      = strtoupper(trim($data['code']));

        return $data;
    }
}
