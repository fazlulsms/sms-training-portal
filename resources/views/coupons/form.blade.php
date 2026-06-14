@extends('layouts.app')
@section('page-title', isset($coupon) ? 'Edit Coupon' : 'Create Coupon')
@section('content')
<style>
.fg { margin-bottom:18px; }
.fg label { display:block; font-weight:600; font-size:13.5px; color:#374151; margin-bottom:6px; }
.fg input,.fg select,.fg textarea {
    width:100%; padding:10px 13px; border:1.5px solid #d1d5db; border-radius:8px;
    font-size:14px; font-family:inherit; outline:none; box-sizing:border-box;
}
.fg input:focus,.fg select:focus,.fg textarea:focus { border-color:#1e3a8a; }
.fg small { display:block; margin-top:4px; font-size:12px; color:#6b7280; }
.fg .err { color:#dc2626; font-size:12px; margin-top:4px; }
.frow  { display:grid; grid-template-columns:1fr 1fr; gap:18px; }
.frow3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:18px; }
.ccard { background:#fff; border:1.5px solid #e5e7eb; border-radius:14px; padding:24px; margin-bottom:20px; box-shadow:0 2px 8px rgba(0,0,0,.05); }
.ccard-title { font-size:15px; font-weight:800; color:#1e3a8a; margin-bottom:20px; padding-bottom:10px; border-bottom:1px solid #e5e7eb; }
.type-card { border:2px solid #e5e7eb; border-radius:12px; padding:18px 12px; text-align:center; cursor:pointer; transition:all .15s; background:#fff; }
.type-card:hover { border-color:#93c5fd; background:#f8faff; }
.type-card.sel { border-color:#1e3a8a; background:#f0f4ff; }
.type-card .ico { font-size:28px; margin-bottom:6px; }
.type-card .lbl { font-size:13px; font-weight:800; color:#111827; }
.type-card .sub { font-size:11.5px; color:#6b7280; margin-top:3px; }
</style>

<x-page-header
    title="{{ isset($coupon) ? 'Edit Coupon: ' . $coupon->code : 'Create Coupon' }}"
    desc="{{ isset($coupon) ? 'Update coupon details and restrictions.' : 'Set up a discount or complimentary access coupon.' }}">
    <x-slot:actions>
        <a href="{{ route('admin.coupons.index') }}" class="btn btn-ghost btn-sm">← All Coupons</a>
    </x-slot:actions>
</x-page-header>

<x-flash-message />

@if($errors->any())
<div style="background:#fee2e2;border:1px solid #fca5a5;border-radius:10px;padding:14px 18px;margin-bottom:20px;max-width:760px;">
    <ul style="margin:0;padding-left:18px;color:#b91c1c;font-size:13.5px;">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
</div>
@endif

<form method="POST"
      action="{{ isset($coupon) ? route('admin.coupons.update', $coupon) : route('admin.coupons.store') }}"
      style="max-width:760px;">
    @csrf
    @if(isset($coupon)) @method('PUT') @endif

    {{-- ── 1. Coupon Details ── --}}
    <div class="ccard">
        <div class="ccard-title">Coupon Details</div>
        <div class="frow">
            <div class="fg">
                <label>Coupon Code <span style="color:#dc2626">*</span></label>
                <input type="text" name="code" value="{{ old('code', $coupon->code ?? '') }}"
                       required placeholder="e.g. SUMMER30"
                       style="text-transform:uppercase;font-family:monospace;font-weight:800;font-size:15px;letter-spacing:.5px;">
                <small>Auto-uppercased. Unique code participants will type.</small>
                @error('code')<div class="err">{{ $message }}</div>@enderror
            </div>
            <div class="fg">
                <label>Coupon Name <span style="color:#dc2626">*</span></label>
                <input type="text" name="name" value="{{ old('name', $coupon->name ?? '') }}"
                       required placeholder="e.g. Summer 30% Off">
                @error('name')<div class="err">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="fg">
            <label>Description <span style="color:#9ca3af;font-weight:400;">(internal note)</span></label>
            <textarea name="description" rows="2"
                      placeholder="Internal note about this coupon's purpose…">{{ old('description', $coupon->description ?? '') }}</textarea>
        </div>
    </div>

    {{-- ── 2. Discount Type & Value ── --}}
    <div class="ccard">
        <div class="ccard-title">Discount Type &amp; Value</div>
        @php $selType = old('type', $coupon->type ?? 'percentage'); @endphp
        <div class="frow3" style="margin-bottom:20px;">
            @foreach([
                ['fixed',         '💰', 'Fixed Amount',  'Deduct a fixed BDT amount from the fee.'],
                ['percentage',    '📊', 'Percentage',    'Deduct a % of the original fee.'],
                ['complimentary', '🎁', 'Complimentary', '100% free — no invoice, instant access.'],
            ] as [$val, $ico, $lbl, $desc])
            <label style="cursor:pointer;">
                <input type="radio" name="type" value="{{ $val }}" {{ $selType === $val ? 'checked' : '' }}
                       style="display:none;" onchange="updateTypeUI()">
                <div class="type-card {{ $selType === $val ? 'sel' : '' }}" data-val="{{ $val }}">
                    <div class="ico">{{ $ico }}</div>
                    <div class="lbl">{{ $lbl }}</div>
                    <div class="sub">{{ $desc }}</div>
                </div>
            </label>
            @endforeach
        </div>
        @error('type')<div class="err" style="margin-bottom:10px;">{{ $message }}</div>@enderror

        <div id="discountValueWrap" style="{{ $selType === 'complimentary' ? 'display:none' : '' }}">
            <div class="fg" style="max-width:340px;margin-bottom:0;">
                <label id="discountValueLabel">
                    {{ $selType === 'fixed' ? 'Discount Amount (BDT)' : 'Discount Percentage (%)' }}
                    <span style="color:#dc2626">*</span>
                </label>
                <input type="number" name="discount_value" id="discountValueInput"
                       value="{{ old('discount_value', $coupon->discount_value ?? '') }}"
                       step="0.01" min="0"
                       placeholder="{{ $selType === 'percentage' ? 'e.g. 20 (for 20%)' : 'e.g. 500' }}">
                @error('discount_value')<div class="err">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>

    {{-- ── 3. Applicability ── --}}
    <div class="ccard">
        <div class="ccard-title">Applicability</div>
        <div class="fg" style="max-width:340px;">
            <label>Training Type</label>
            <select name="training_type">
                @foreach(['both'=>'Both (eLearning & ILT)','elearning'=>'eLearning Only','ilt'=>'ILT / Instructor-Led Only'] as $val=>$lbl)
                <option value="{{ $val }}" {{ old('training_type', $coupon->training_type ?? 'both') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                @endforeach
            </select>
        </div>
        <div class="fg">
            <label>Applicable Courses</label>
            <small style="margin-bottom:10px;display:block;">Leave all unchecked to apply to ALL public courses.</small>
            @php $selectedCourseIds = old('course_ids', $coupon->course_ids ?? []); @endphp

            {{-- Search + controls --}}
            <div style="display:flex;gap:8px;align-items:center;margin-bottom:8px;flex-wrap:wrap;">
                <input type="text" id="courseSearch" placeholder="🔍 Search courses…"
                       oninput="filterCourses()"
                       style="flex:1;min-width:200px;padding:8px 12px;border:1.5px solid #d1d5db;border-radius:8px;font-size:13px;font-family:inherit;outline:none;">
                <button type="button" onclick="selectAllCourses(true)"
                        style="padding:7px 14px;font-size:12px;font-weight:700;border:1.5px solid #d1d5db;border-radius:7px;background:#f9fafb;cursor:pointer;font-family:inherit;white-space:nowrap;">
                    ✓ Select All
                </button>
                <button type="button" onclick="selectAllCourses(false)"
                        style="padding:7px 14px;font-size:12px;font-weight:700;border:1.5px solid #d1d5db;border-radius:7px;background:#f9fafb;cursor:pointer;font-family:inherit;white-space:nowrap;">
                    ✕ Clear All
                </button>
            </div>
            <div id="courseSelCount" style="font-size:12px;color:#6b7280;margin-bottom:6px;"></div>

            {{-- Scrollable checklist --}}
            <div id="courseList" style="max-height:260px;overflow-y:auto;border:1.5px solid #e5e7eb;border-radius:9px;padding:6px;">
                @foreach($courses as $c)
                <label class="course-item"
                       data-name="{{ strtolower($c->name) }}"
                       style="display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:7px;cursor:pointer;transition:background .1s;font-weight:400;">
                    <input type="checkbox" name="course_ids[]" value="{{ $c->id }}"
                           onchange="updateSelCount()"
                           {{ in_array($c->id, $selectedCourseIds ?? []) ? 'checked' : '' }}
                           style="width:15px;height:15px;accent-color:#1e3a8a;flex-shrink:0;">
                    <span style="flex:1;font-size:13.5px;color:#111827;">{{ $c->name }}</span>
                    <span style="font-size:10.5px;color:#6b7280;background:#f3f4f6;padding:2px 8px;border-radius:4px;white-space:nowrap;">
                        {{ $c->course_type === 'elearning' ? 'eLearning' : 'ILT' }}
                    </span>
                </label>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── 4. Validity & Limits ── --}}
    <div class="ccard">
        <div class="ccard-title">Validity &amp; Usage Limits</div>
        <div class="frow">
            <div class="fg">
                <label>Start Date</label>
                <input type="date" name="starts_at"
                       value="{{ old('starts_at', ($coupon->starts_at ?? null)?->format('Y-m-d') ?? '') }}">
                <small>Leave blank to activate immediately.</small>
            </div>
            <div class="fg">
                <label>Expiry Date</label>
                <input type="date" name="expires_at"
                       value="{{ old('expires_at', ($coupon->expires_at ?? null)?->format('Y-m-d') ?? '') }}">
                <small>Leave blank for no expiry.</small>
            </div>
            <div class="fg">
                <label>Maximum Total Uses</label>
                <input type="number" name="max_uses" min="1"
                       value="{{ old('max_uses', $coupon->max_uses ?? '') }}"
                       placeholder="Leave blank for unlimited">
                @error('max_uses')<div class="err">{{ $message }}</div>@enderror
            </div>
            <div class="fg">
                <label>Per User / Email Limit</label>
                <input type="number" name="per_user_limit" min="1" max="10"
                       value="{{ old('per_user_limit', $coupon->per_user_limit ?? 1) }}">
                @error('per_user_limit')<div class="err">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>

    {{-- ── 5. Status & Notes ── --}}
    <div class="ccard">
        <div class="ccard-title">Status &amp; Internal Notes</div>
        <div class="fg">
            <label style="display:flex;align-items:center;gap:10px;cursor:pointer;">
                <input type="checkbox" name="is_active" value="1"
                       {{ old('is_active', $coupon->is_active ?? true) ? 'checked' : '' }}
                       style="width:17px;height:17px;accent-color:#1e3a8a;">
                <span>Coupon is Active (participants can use it)</span>
            </label>
        </div>
        <div class="fg" style="margin-bottom:0;">
            <label>Internal Notes</label>
            <textarea name="notes" rows="2"
                      placeholder="e.g. For partner organisation XYZ, valid for Q3 2026">{{ old('notes', $coupon->notes ?? '') }}</textarea>
        </div>
    </div>

    <div style="display:flex;gap:12px;margin-bottom:40px;">
        <button type="submit"
                style="padding:12px 28px;background:#1e3a8a;color:#fff;border:none;border-radius:9px;font-size:14px;font-weight:700;cursor:pointer;font-family:inherit;">
            {{ isset($coupon) ? 'Update Coupon' : 'Create Coupon' }}
        </button>
        <a href="{{ route('admin.coupons.index') }}"
           style="padding:12px 24px;background:#f3f4f6;color:#374151;border-radius:9px;font-size:14px;font-weight:600;text-decoration:none;">
            Cancel
        </a>
    </div>
</form>

<script>
function updateTypeUI() {
    var radios   = document.querySelectorAll('input[name="type"]');
    var selected = '';
    radios.forEach(function(r) {
        var card = r.closest('label').querySelector('.type-card');
        if (r.checked) {
            selected = r.value;
            card.classList.add('sel');
        } else {
            card.classList.remove('sel');
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
        if (label) label.childNodes[0].textContent = selected === 'fixed'
            ? 'Discount Amount (BDT) ' : 'Discount Percentage (%) ';
        if (input) input.placeholder = selected === 'fixed' ? 'e.g. 500' : 'e.g. 20 (for 20%)';
    }
}
document.querySelectorAll('input[name="type"]').forEach(function(r) {
    r.addEventListener('change', updateTypeUI);
});

// Course search & select
function filterCourses() {
    var q = document.getElementById('courseSearch').value.toLowerCase().trim();
    document.querySelectorAll('.course-item').forEach(function(el) {
        el.style.display = (!q || el.dataset.name.includes(q)) ? '' : 'none';
    });
}
function selectAllCourses(state) {
    document.querySelectorAll('.course-item').forEach(function(el) {
        if (el.style.display !== 'none') {
            var cb = el.querySelector('input[type="checkbox"]');
            if (cb) cb.checked = state;
        }
    });
    updateSelCount();
}
function updateSelCount() {
    var total   = document.querySelectorAll('.course-item input[type="checkbox"]').length;
    var checked = document.querySelectorAll('.course-item input[type="checkbox"]:checked').length;
    var el = document.getElementById('courseSelCount');
    if (el) el.textContent = checked > 0
        ? checked + ' of ' + total + ' course(s) selected — coupon will only apply to these.'
        : 'No courses selected — coupon will apply to all public courses.';
}
document.addEventListener('DOMContentLoaded', function() {
    updateSelCount();
    document.querySelectorAll('.course-item').forEach(function(el) {
        el.addEventListener('mouseenter', function() { el.style.background = '#f8faff'; });
        el.addEventListener('mouseleave', function() { el.style.background = ''; });
    });
});
</script>
@endsection
