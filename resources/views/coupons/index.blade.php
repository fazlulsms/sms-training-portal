@extends('layouts.app')
@section('page-title', 'Coupons & Promotions')
@section('content')

<x-page-header title="Coupons & Promotions" desc="Create and manage discount and complimentary coupons.">
    <x-slot:actions>
        <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary btn-sm">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            New Coupon
        </a>
    </x-slot:actions>
</x-page-header>

<x-flash-message />

{{-- Summary stats --}}
@php
    $total        = \App\Models\Coupon::count();
    $active       = \App\Models\Coupon::where('is_active', true)->count();
    $totalUses    = \App\Models\CouponUsage::count();
    $totalDiscount= \App\Models\CouponUsage::sum('discount_amount');
@endphp
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:16px;margin-bottom:24px;">
    @foreach([
        ['Total Coupons',      $total,                       '#1e3a8a', '#dbeafe'],
        ['Active Coupons',     $active,                      '#166534', '#dcfce7'],
        ['Total Uses',         $totalUses,                   '#7c3aed', '#f3e8ff'],
        ['Discount Given',     'BDT '.number_format($totalDiscount), '#b45309','#fef3c7'],
    ] as [$label, $value, $color, $bg])
    <div style="background:{{ $bg }};border-radius:12px;padding:16px 18px;">
        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:{{ $color }};opacity:.8;margin-bottom:4px;">{{ $label }}</div>
        <div style="font-size:22px;font-weight:900;color:{{ $color }};">{{ $value }}</div>
    </div>
    @endforeach
</div>

<div class="dt-wrap">
    <div class="dt-scroll">
        <table class="dt">
            <thead>
                <tr>
                    <th>Code / Name</th>
                    <th>Type</th>
                    <th class="c">Training</th>
                    <th class="c">Validity</th>
                    <th class="c">Uses</th>
                    <th class="c">Status</th>
                    <th class="c">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($coupons as $coupon)
                @php
                    $typeColors = [
                        'fixed'         => ['#1e3a8a','#dbeafe'],
                        'percentage'    => ['#7c3aed','#f3e8ff'],
                        'complimentary' => ['#166534','#dcfce7'],
                    ];
                    [$tc, $tb] = $typeColors[$coupon->type] ?? ['#6b7280','#f3f4f6'];
                @endphp
                <tr>
                    <td>
                        <div class="td-main" style="font-family:monospace;font-size:15px;font-weight:800;letter-spacing:.5px;">{{ $coupon->code }}</div>
                        <div class="td-sub">{{ $coupon->name }}</div>
                    </td>
                    <td>
                        <span class="badge" style="background:{{ $tb }};color:{{ $tc }};">{{ $coupon->getTypeLabel() }}</span>
                        <div class="td-sub" style="margin-top:3px;">{{ $coupon->getDiscountDisplay() }}</div>
                    </td>
                    <td class="c">
                        @php $tt = ['both'=>'Both','elearning'=>'eLearning','ilt'=>'ILT']; @endphp
                        <span class="badge badge-secondary">{{ $tt[$coupon->training_type] ?? $coupon->training_type }}</span>
                    </td>
                    <td class="c" style="font-size:12px;">
                        @if($coupon->starts_at || $coupon->expires_at)
                        <div>{{ $coupon->starts_at?->format('d M Y') ?? '∞' }}</div>
                        <div style="color:#9ca3af;">→ {{ $coupon->expires_at?->format('d M Y') ?? '∞' }}</div>
                        @else
                        <span style="color:#9ca3af;">No limit</span>
                        @endif
                    </td>
                    <td class="c">
                        <div style="font-size:15px;font-weight:800;color:#111827;">{{ $coupon->used_count }}</div>
                        @if($coupon->max_uses)
                        <div class="td-sub">/ {{ $coupon->max_uses }} max</div>
                        @endif
                    </td>
                    <td class="c">
                        @if($coupon->is_active && $coupon->isValid())
                        <span class="badge badge-success">Active</span>
                        @elseif($coupon->is_active && $coupon->expires_at?->isPast())
                        <span class="badge badge-warning">Expired</span>
                        @else
                        <span class="badge badge-danger">Inactive</span>
                        @endif
                    </td>
                    <td class="c">
                        <div class="dt-actions" style="justify-content:center;">
                            <a href="{{ route('admin.coupons.show', $coupon) }}" class="btn btn-xs btn-secondary">Usage</a>
                            <a href="{{ route('admin.coupons.edit', $coupon) }}" class="btn btn-edit btn-xs">Edit</a>
                            <form method="POST" action="{{ route('admin.coupons.toggle', $coupon) }}" style="display:inline;">
                                @csrf
                                <button class="btn btn-xs {{ $coupon->is_active ? 'btn-warning' : 'btn-success' }}"
                                        style="font-size:11px;"
                                        onclick="return confirm('{{ $coupon->is_active ? 'Deactivate' : 'Activate' }} this coupon?')">
                                    {{ $coupon->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.coupons.destroy', $coupon) }}" style="display:inline;">
                                @csrf @method('DELETE')
                                <button class="btn btn-del btn-xs" onclick="return confirm('Delete this coupon?')">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7">
                    <div class="empty-state">
                        <div class="empty-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg></div>
                        <p class="empty-title">No coupons yet</p>
                        <p class="empty-desc">Create your first coupon to offer discounts or complimentary access.</p>
                        <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary btn-sm">Create Coupon</a>
                    </div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($coupons->hasPages())
    <div style="padding:14px 16px;border-top:1px solid #f0f2f5;">{{ $coupons->links() }}</div>
    @endif
</div>
@endsection
