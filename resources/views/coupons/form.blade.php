@extends('layouts.app')
@section('page-title', isset($coupon) ? 'Edit Coupon' : 'Create Coupon')
@section('content')

<x-page-header
    title="{{ isset($coupon) ? 'Edit Coupon: ' . $coupon->code : 'Create Coupon' }}"
    desc="{{ isset($coupon) ? 'Update coupon details and restrictions.' : 'Set up a discount or complimentary access coupon.' }}">
    <x-slot:actions>
        <a href="{{ route('admin.coupons.index') }}" class="btn btn-ghost btn-sm">← Back to Coupons</a>
    </x-slot:actions>
</x-page-header>

<x-flash-message />

<form method="POST"
      action="{{ isset($coupon) ? route('admin.coupons.update', $coupon) : route('admin.coupons.store') }}"
      style="max-width:760px;">
    @csrf
    @if(isset($coupon)) @method('PUT') @endif

    {{-- Basic Info --}}
    <div class="card" style="margin-bottom:20px;">
        <div class="card-header">Coupon Details</div>
        <div class="card-body">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div class="form-group">
                    <label class="form-label">Coupon Code <span style="color:#dc2626">*</span></label>
                    <input type="text" name="code" value="{{ old('code', $coupon->code ?? '') }}"
                           class="form-control" required placeholder="e.g. SUMMER30"
                           style="text-transform:uppercase;font-family:monospace;font-weight:800;font-size:15px;letter-spacing:.5px;">
                    <small class="text-muted">Auto-uppercased. Unique identifier participants will type.</small>
                    @error('code')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Coupon Name <span style="color:#dc2626">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $coupon->name ?? '') }}"
                           class="form-control" required placeholder="e.g. Summer 30% Off">
                    @error('name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="form-group" style="margin-top:14px;">
                <label class="form-label">Description (Internal)</label>
                <textarea name="description" class="form-control" rows="2"
                          placeholder="Internal note about this coupon's purpose…">{{ old('description', $coupon->description ?? '') }}</textarea>
            </div>
        </div>
    </div>

    {{-- Coupon Type --}}
    <div class="card" style="margin-bottom:20px;">
        <div class="card-header">Discount Type &amp; Value</div>
        <div class="card-body">
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:20px;" id="typeCards">
                @php $selType = old('type', $coupon->type ?? 'percentage'); @endphp
                @foreach([
                    ['fixed',         '💰', 'Fixed Amount',  'Deduct a fixed BDT amount from the fee.'],
                    ['percentage',    '📊', 'Percentage',    'Deduct a % of the original fee.'],
                    ['complimentary', '🎁', 'Complimentary', '100% free — no invoice, instant access.'],
                ] as [$val, $ico, $lbl, $desc])
                <label style="cursor:pointer;">
                    <input type="radio" name="type" value="{{ $val }}" {{ $selType === $val ? 'checked' : '' }}
                           style="display:none;" onchange="updateTypeUI()">
                    <div class="type-card {{ $selType === $val ? 'type-card-active' : '' }}" data-val="{{ $val }}"
                         style="border:2px solid {{ $selType === $val ? '#1e3a8a' : '#e5e7eb' }};border-radius:12px;padding:16px;text-align:center;background:{{ $selType === $val ? '#f0f4ff' : '#fff' }};transition:all .15s;">
                        <div style="font-size:28px;margin-bottom:6px;">{{ $ico }}</div>
                        <div style="font-size:13px;font-weight:800;color:#111827;">{{ $lbl }}</div>
                        <div style="font-size:11.5px;color:#6b7280;margin-top:3px;">{{ $desc }}</div>
                    </div>
                </label>
                @endforeach
            </div>
            @error('type')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror

            <div id="discountValueWrap" style="{{ $selType === 'complimentary' ? 'display:none' : '' }}">
                <div class="form-group">
                    <label class="form-label" id="discountValueLabel">
                        {{ $selType === 'fixed' ? 'Discount Amount (BDT)' : 'Discount Percentage (%)' }}
                        <span style="color:#dc2626">*</span>
                    </label>
                    <input type="number" name="discount_value" id="discountValueInput"
                           value="{{ old('discount_value', $coupon->discount_value ?? '') }}"
                           class="form-control" step="0.01" min="0"
                           placeholder="{{ $selType === 'percentage' ? 'e.g. 20 (for 20%)' : 'e.g. 500' }}"
                           style="max-width:280px;">
                    @error('discount_value')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
    </div>

    {{-- Applicability --}}
    <div class="card" style="margin-bottom:20px;">
        <div class="card-header">Applicability</div>
        <div class="card-body">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                <div class="form-group">
                    <label class="form-label">Training Type</label>
                    <select name="training_type" class="form-control">
                        @foreach(['both'=>'Both (eLearning & ILT)','elearning'=>'eLearning Only','ilt'=>'ILT / Instructor-Led Only'] as $val=>$lbl)
                        <option value="{{ $val }}" {{ old('training_type', $coupon->training_type ?? 'both') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Applicable Courses</label>
                <small class="text-muted d-block" style="margin-bottom:8px;">Leave all unchecked to apply to ALL public courses.</small>
                <div style="max-height:220px;overflow-y:auto;border:1px solid #e5e7eb;border-radius:9px;padding:12px;display:grid;grid-template-columns:1fr 1fr;gap:6px;">
                    @php $selectedCourseIds = old('course_ids', $coupon->course_ids ?? []); @endphp
                    @foreach($courses as $c)
                    <label style="display:flex;align-items:center;gap:8px;font-size:13px;cursor:pointer;padding:4px;">
                        <input type="checkbox" name="course_ids[]" value="{{ $c->id }}"
                               {{ in_array($c->id, $selectedCourseIds ?? []) ? 'checked' : '' }}>
                        <span>{{ $c->name }}</span>
                        <span style="font-size:10px;color:#9ca3af;background:#f3f4f6;padding:1px 6px;border-radius:4px;">{{ $c->course_type }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Validity & Limits --}}
    <div class="card" style="margin-bottom:20px;">
        <div class="card-header">Validity &amp; Usage Limits</div>
        <div class="card-body">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div class="form-group">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="starts_at" class="form-control"
                           value="{{ old('starts_at', ($coupon->starts_at ?? null)?->format('Y-m-d') ?? '') }}">
                    <small class="text-muted">Leave blank to activate immediately.</small>
                </div>
                <div class="form-group">
                    <label class="form-label">Expiry Date</label>
                    <input type="date" name="expires_at" class="form-control"
                           value="{{ old('expires_at', ($coupon->expires_at ?? null)?->format('Y-m-d') ?? '') }}">
                    <small class="text-muted">Leave blank for no expiry.</small>
                </div>
                <div class="form-group">
                    <label class="form-label">Maximum Total Uses</label>
                    <input type="number" name="max_uses" class="form-control" min="1"
                           value="{{ old('max_uses', $coupon->max_uses ?? '') }}"
                           placeholder="Leave blank for unlimited">
                    @error('max_uses')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Per User / Email Limit</label>
                    <input type="number" name="per_user_limit" class="form-control" min="1" max="10"
                           value="{{ old('per_user_limit', $coupon->per_user_limit ?? 1) }}">
                    @error('per_user_limit')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
    </div>

    {{-- Status & Notes --}}
    <div class="card" style="margin-bottom:24px;">
        <div class="card-header">Status &amp; Internal Notes</div>
        <div class="card-body">
            <div class="form-group" style="margin-bottom:16px;">
                <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-size:14px;font-weight:600;">
                    <input type="checkbox" name="is_active" value="1"
                           {{ old('is_active', $coupon->is_active ?? true) ? 'checked' : '' }}
                           style="width:17px;height:17px;accent-color:#1e3a8a;">
                    Coupon is Active (participants can use it)
                </label>
            </div>
            <div class="form-group">
                <label class="form-label">Internal Notes</label>
                <textarea name="notes" class="form-control" rows="2"
                          placeholder="e.g. For partner organisation XYZ, valid for Q3 2026">{{ old('notes', $coupon->notes ?? '') }}</textarea>
            </div>
        </div>
    </div>

    <div style="display:flex;gap:12px;">
        <button type="submit" class="btn btn-primary">
            {{ isset($coupon) ? 'Update Coupon' : 'Create Coupon' }}
        </button>
        <a href="{{ route('admin.coupons.index') }}" class="btn btn-ghost">Cancel</a>
    </div>
</form>

<script>
function updateTypeUI() {
    var radios = document.querySelectorAll('input[name="type"]');
    var selected = '';
    radios.forEach(function(r) {
        var card = r.closest('label').querySelector('.type-card');
        if (r.checked) {
            selected = r.value;
            card.style.borderColor = '#1e3a8a';
            card.style.background  = '#f0f4ff';
        } else {
            card.style.borderColor = '#e5e7eb';
            card.style.background  = '#fff';
        }
    });
    var wrap  = document.getElementById('discountValueWrap');
    var label = document.getElementById('discountValueLabel');
    var input = document.getElementById('discountValueInput');
    if (selected === 'complimentary') {
        wrap.style.display = 'none';
        if (input) input.removeAttribute('required');
    } else {
        wrap.style.display = '';
        if (input) input.setAttribute('required', 'required');
        if (label) label.firstChild.textContent = selected === 'fixed'
            ? 'Discount Amount (BDT) '
            : 'Discount Percentage (%) ';
        if (input) input.placeholder = selected === 'fixed' ? 'e.g. 500' : 'e.g. 20 (for 20%)';
    }
}
document.querySelectorAll('input[name="type"]').forEach(function(r) {
    r.addEventListener('change', updateTypeUI);
});
</script>
@endsection
