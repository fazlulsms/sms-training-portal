@extends('layouts.app')
@section('page-title', 'Coupon: ' . $coupon->code)
@section('content')

<x-page-header title="Coupon: {{ $coupon->code }}" desc="{{ $coupon->name }}">
    <x-slot:actions>
        <a href="{{ route('admin.coupons.edit', $coupon) }}" class="btn btn-edit btn-sm">Edit</a>
        <form method="POST" action="{{ route('admin.coupons.toggle', $coupon) }}" style="display:inline;">
            @csrf
            <button class="btn btn-sm {{ $coupon->is_active ? 'btn-warning' : 'btn-success' }}">
                {{ $coupon->is_active ? 'Deactivate' : 'Activate' }}
            </button>
        </form>
        <a href="{{ route('admin.coupons.index') }}" class="btn btn-ghost btn-sm">← All Coupons</a>
    </x-slot:actions>
</x-page-header>

<x-flash-message />

{{-- Summary --}}
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:16px;margin-bottom:24px;">
    @php
        $totalUses     = $coupon->usages->count();
        $totalDiscount = $coupon->usages->sum('discount_amount');
        $totalRevenue  = $coupon->usages->sum('original_amount');
        $compCount     = $coupon->type === 'complimentary' ? $totalUses : 0;
    @endphp
    @foreach([
        ['Total Uses',           $coupon->used_count,                '#1e3a8a','#dbeafe'],
        ['Discount Given',       'BDT '.number_format($totalDiscount),'#7c3aed','#f3e8ff'],
        ['Original Revenue',     'BDT '.number_format($totalRevenue), '#b45309','#fef3c7'],
        ['Status',               $coupon->is_active && $coupon->isValid() ? 'Active' : 'Inactive', $coupon->is_active ? '#166534' : '#991b1b', $coupon->is_active ? '#dcfce7' : '#fee2e2'],
    ] as [$label, $value, $color, $bg])
    <div style="background:{{ $bg }};border-radius:12px;padding:16px 18px;">
        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:{{ $color }};opacity:.8;margin-bottom:4px;">{{ $label }}</div>
        <div style="font-size:20px;font-weight:900;color:{{ $color }};">{{ $value }}</div>
    </div>
    @endforeach
</div>

{{-- Coupon details --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px;">
    <div class="card">
        <div class="card-header">Coupon Configuration</div>
        <div class="card-body">
            <table style="width:100%;font-size:13.5px;border-collapse:collapse;">
                @foreach([
                    ['Code',          '<code style="font-size:15px;font-weight:800;letter-spacing:.5px;">'.$coupon->code.'</code>'],
                    ['Name',          e($coupon->name)],
                    ['Type',          $coupon->getTypeLabel()],
                    ['Value',         $coupon->getDiscountDisplay()],
                    ['Training Type', ucfirst($coupon->training_type)],
                    ['Courses',       empty($coupon->course_ids) ? 'All public courses' : count($coupon->course_ids).' specific courses'],
                    ['Starts',        $coupon->starts_at?->format('d M Y') ?? '—'],
                    ['Expires',       $coupon->expires_at?->format('d M Y') ?? 'Never'],
                    ['Max Uses',      $coupon->max_uses ?? 'Unlimited'],
                    ['Per User Limit',$coupon->per_user_limit],
                    ['Used Count',    $coupon->used_count],
                ] as [$label, $val])
                <tr style="border-bottom:1px solid #f0f2f5;">
                    <td style="padding:8px 0;color:#6b7280;font-weight:600;width:140px;">{{ $label }}</td>
                    <td style="padding:8px 0;font-weight:700;">{!! $val !!}</td>
                </tr>
                @endforeach
            </table>
            @if($coupon->notes)
            <div style="margin-top:12px;padding:10px 12px;background:#f9fafb;border-radius:8px;font-size:13px;color:#374151;">
                <strong>Notes:</strong> {{ $coupon->notes }}
            </div>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-header">Revenue Impact</div>
        <div class="card-body">
            <div style="text-align:center;padding:20px 0;">
                <div style="font-size:13px;color:#9ca3af;margin-bottom:4px;">Total Discount Given</div>
                <div style="font-size:32px;font-weight:900;color:#7c3aed;">BDT {{ number_format($totalDiscount) }}</div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:12px;">
                <div style="background:#f0f4ff;border-radius:10px;padding:14px;text-align:center;">
                    <div style="font-size:11px;font-weight:700;color:#6b7280;margin-bottom:4px;">ORIGINAL FEES</div>
                    <div style="font-size:17px;font-weight:800;color:#1e3a8a;">BDT {{ number_format($totalRevenue) }}</div>
                </div>
                <div style="background:#f0fdf4;border-radius:10px;padding:14px;text-align:center;">
                    <div style="font-size:11px;font-weight:700;color:#6b7280;margin-bottom:4px;">COLLECTED</div>
                    <div style="font-size:17px;font-weight:800;color:#166534;">BDT {{ number_format($totalRevenue - $totalDiscount) }}</div>
                </div>
            </div>
            @if($coupon->type === 'complimentary')
            <div style="margin-top:14px;padding:12px;background:#fef3c7;border-radius:9px;font-size:13px;color:#92400e;text-align:center;">
                🎁 {{ $totalUses }} complimentary enrollment{{ $totalUses !== 1 ? 's' : '' }} granted
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Usage History --}}
<div class="dt-wrap">
    <div style="padding:14px 16px;border-bottom:1px solid #f0f2f5;font-size:14px;font-weight:800;color:#111827;">
        Usage History
    </div>
    <div class="dt-scroll">
        <table class="dt">
            <thead>
                <tr>
                    <th>Email</th>
                    <th>Course</th>
                    <th class="c">Type</th>
                    <th class="r">Original</th>
                    <th class="r">Discount</th>
                    <th class="r">Final Paid</th>
                    <th class="c">Used At</th>
                </tr>
            </thead>
            <tbody>
                @forelse($usages as $u)
                <tr>
                    <td><div class="td-main">{{ $u->email }}</div></td>
                    <td><div class="td-sub">{{ $u->course_name }}</div></td>
                    <td class="c"><span class="badge badge-secondary">{{ strtoupper($u->enrollment_type) }}</span></td>
                    <td class="r" style="font-size:13px;">BDT {{ number_format($u->original_amount) }}</td>
                    <td class="r" style="font-size:13px;color:#7c3aed;font-weight:700;">- BDT {{ number_format($u->discount_amount) }}</td>
                    <td class="r" style="font-size:13px;font-weight:800;color:#166534;">BDT {{ number_format($u->final_amount) }}</td>
                    <td class="c" style="font-size:12px;color:#6b7280;">{{ $u->used_at->format('d M Y H:i') }}</td>
                </tr>
                @empty
                <tr><td colspan="7">
                    <div class="empty-state">
                        <p class="empty-title">No uses yet</p>
                        <p class="empty-desc">No participant has used this coupon yet.</p>
                    </div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($usages->hasPages())
    <div style="padding:14px 16px;border-top:1px solid #f0f2f5;">{{ $usages->links() }}</div>
    @endif
</div>
@endsection
