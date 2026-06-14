@extends('layouts.public')

@section('page-title', 'Enroll — ' . ($schedule->course?->name ?? $schedule->training_title))
@section('seo-title', 'Register for ' . ($schedule->course?->name ?? $schedule->training_title) . ' — SMS Training Services')
@section('seo-desc', 'Secure your seat for ' . ($schedule->course?->name ?? $schedule->training_title) . '. Complete the enrollment form to register.')

@push('head')
@include('partials.registration-styles')
@endpush

@section('content')

@php
    $courseName  = $schedule->course?->name ?? $schedule->training_title;
    $currency    = $schedule->currency ?? 'BDT';
    $physicalFee = (int) ($schedule->discount_fee ?? $schedule->physical_fee ?? 0);
    $onlineFee   = (int) ($schedule->discount_fee ?? $schedule->online_fee   ?? 0);
    $seatsLeft   = $schedule->seats_left;
@endphp

{{-- Hero --}}
<div class="reg-hero">
    <div class="pub-container reg-hero-inner">
        <div class="reg-breadcrumb">
            <a href="{{ route('public.home') }}">Home</a><span>/</span>
            <a href="{{ route('public.courses') }}">Courses</a><span>/</span>
            @if($schedule->course)
            <a href="{{ route('public.course.detail', $schedule->course->slug ?? $schedule->course_id) }}">{{ Str::limit($courseName, 35) }}</a><span>/</span>
            @endif
            <span>Enroll</span>
        </div>
        <div class="reg-hero-type">🏢 Instructor-Led Training Registration</div>
        <h1>{{ $courseName }}</h1>
        <div class="reg-hero-badges">
            <span class="reg-hero-badge">📅 {{ \Carbon\Carbon::parse($schedule->start_date)->format('d M') }} – {{ \Carbon\Carbon::parse($schedule->end_date)->format('d M Y') }}</span>
            <span class="reg-hero-badge">{{ $schedule->training_mode }}</span>
            @if($schedule->training_mode !== 'Online' && $schedule->venue)
            <span class="reg-hero-badge">📍 {{ $schedule->venue }}</span>
            @elseif($schedule->training_mode === 'Online')
            <span class="reg-hero-badge">📍 Online (Zoom)</span>
            @endif
            @if($schedule->trainer)
            <span class="reg-hero-badge">👤 {{ $schedule->trainer->name }}</span>
            @endif
            @if($schedule->batch_code)
            <span class="reg-hero-badge">🗂 {{ $schedule->batch_code }}</span>
            @endif
            @if(!is_null($seatsLeft))
            <span class="reg-hero-badge" style="{{ $seatsLeft <= 5 ? 'background:rgba(239,68,68,.25);' : '' }}">
                {{ $seatsLeft <= 0 ? '🔴 Full' : ($seatsLeft <= 5 ? '🟠 ' . $seatsLeft . ' seats left' : '🟢 ' . $seatsLeft . ' seats left') }}
            </span>
            @endif
        </div>
    </div>
</div>

<div class="pub-container">
<div class="reg-body">

{{-- ── LEFT: Form ── --}}
<div class="reg-main">

    @if(session('error'))
    <div class="reg-alert-error">⚠️ {{ session('error') }}</div>
    @endif
    @if($errors->any())
    <div class="reg-alert-error">⚠ Please fix the highlighted errors below before submitting.</div>
    @endif

    <form method="POST" action="{{ route('public.enroll.store', $schedule->id) }}">
        @csrf

        {{-- 1. Personal Information --}}
        <div class="reg-card">
            <div class="reg-card-title"><div class="reg-card-num">1</div> Personal Information</div>
            <div class="form-grid-2">
                <div class="fg full">
                    <label class="fl">Full Name <span class="req">*</span></label>
                    <input type="text" name="full_name" value="{{ old('full_name', $user?->name) }}"
                           class="fi {{ $errors->has('full_name') ? 'is-err' : '' }}"
                           required placeholder="As per NID / Passport">
                    @error('full_name')<div class="fe">{{ $message }}</div>@enderror
                </div>
                <div class="fg">
                    <label class="fl">Email Address <span class="req">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $user?->email) }}"
                           class="fi {{ $errors->has('email') ? 'is-err' : '' }}"
                           required placeholder="you@example.com">
                    @error('email')<div class="fe">{{ $message }}</div>@enderror
                    <div class="fh">Your login credentials will be sent here.</div>
                </div>
                <div class="fg">
                    <label class="fl">Mobile Number <span class="req">*</span></label>
                    <input type="text" name="phone" value="{{ old('phone', $user?->phone) }}"
                           class="fi {{ $errors->has('phone') ? 'is-err' : '' }}"
                           required placeholder="+880 1X-XXXXXXXXX">
                    @error('phone')<div class="fe">{{ $message }}</div>@enderror
                </div>
                <div class="fg" style="grid-column:1/-1;">
                    <label class="fl">Gender</label>
                    <select name="gender" class="fi" style="max-width:220px;">
                        <option value="">Select gender</option>
                        @foreach(['Male','Female','Other','Prefer not to say'] as $g)
                        <option value="{{ $g }}" {{ old('gender') === $g ? 'selected' : '' }}>{{ $g }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- 2. Professional Details --}}
        <div class="reg-card">
            <div class="reg-card-title"><div class="reg-card-num">2</div> Professional Details</div>
            <div class="form-grid-2">
                <div class="fg">
                    <label class="fl">Job Title / Designation</label>
                    <input type="text" name="designation" value="{{ old('designation', $user?->designation) }}"
                           class="fi" placeholder="e.g. Safety Officer">
                </div>
                <div class="fg">
                    <label class="fl">Organization / Company</label>
                    <input type="text" name="company" value="{{ old('company', $user?->company) }}" class="fi">
                </div>
                <div class="fg">
                    <label class="fl">Industry / Sector</label>
                    <input type="text" name="industry" value="{{ old('industry') }}" class="fi" placeholder="e.g. Construction, Oil & Gas">
                </div>
                <div class="fg">
                    <label class="fl">Years of Experience</label>
                    <select name="experience_years" class="fi">
                        <option value="">Select</option>
                        @foreach(['Less than 1 year','1–3 years','3–5 years','5–10 years','10+ years'] as $e)
                        <option value="{{ $e }}" {{ old('experience_years') === $e ? 'selected' : '' }}>{{ $e }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- 3. Contact & Address --}}
        <div class="reg-card">
            <div class="reg-card-title"><div class="reg-card-num">3</div> Contact &amp; Address</div>
            <div class="form-grid-2">
                <div class="fg">
                    <label class="fl">Country <span class="req">*</span></label>
                    <select name="country" class="fi {{ $errors->has('country') ? 'is-err' : '' }}" required>
                        <option value="">Select country</option>
                        @php $countries = ['Bangladesh','India','Pakistan','Nepal','Sri Lanka','Myanmar','UAE','Saudi Arabia','Qatar','Kuwait','UK','USA','Canada','Australia','Other']; @endphp
                        @foreach($countries as $c)
                        <option value="{{ $c }}" {{ old('country','Bangladesh') === $c ? 'selected' : '' }}>{{ $c }}</option>
                        @endforeach
                    </select>
                    @error('country')<div class="fe">{{ $message }}</div>@enderror
                </div>
                <div class="fg">
                    <label class="fl">City / District</label>
                    <input type="text" name="city" value="{{ old('city') }}" class="fi">
                </div>
                <div class="fg full">
                    <label class="fl">Full Address</label>
                    <input type="text" name="full_address" value="{{ old('full_address') }}"
                           class="fi" placeholder="Street, area, postal code">
                </div>
                <div class="fg">
                    <label class="fl">Emergency Contact Name</label>
                    <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}" class="fi">
                </div>
                <div class="fg">
                    <label class="fl">Emergency Contact Phone</label>
                    <input type="text" name="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}" class="fi">
                </div>
            </div>
        </div>

        {{-- 4. Participation Mode --}}
        @if(in_array($schedule->training_mode, ['Hybrid','Physical','Online']))
        <div class="reg-card">
            <div class="reg-card-title"><div class="reg-card-num">4</div> Participation Mode <span class="req" style="font-size:12px;margin-left:4px;">*</span></div>
            <div class="mode-cards">
                @if(in_array($schedule->training_mode, ['Physical','Hybrid']))
                <label class="mode-card-label">
                    <input type="radio" name="selected_mode" value="Physical"
                           {{ old('selected_mode', $schedule->training_mode !== 'Online' ? 'Physical' : '') === 'Physical' ? 'checked' : '' }}>
                    <div class="mode-card">
                        <div class="mode-card-icon">🏢</div>
                        <div class="mode-card-name">Physical</div>
                        <div class="mode-card-desc">{{ $schedule->venue ?? 'At our venue' }}</div>
                        @if($physicalFee)<div class="mode-card-fee">{{ $currency }} {{ number_format($physicalFee) }}</div>@endif
                    </div>
                </label>
                @endif
                @if(in_array($schedule->training_mode, ['Online','Hybrid']))
                <label class="mode-card-label">
                    <input type="radio" name="selected_mode" value="Online"
                           {{ old('selected_mode', $schedule->training_mode === 'Online' ? 'Online' : '') === 'Online' ? 'checked' : '' }}>
                    <div class="mode-card">
                        <div class="mode-card-icon">💻</div>
                        <div class="mode-card-name">Online</div>
                        <div class="mode-card-desc">Live via Zoom</div>
                        @if($onlineFee)<div class="mode-card-fee">{{ $currency }} {{ number_format($onlineFee) }}</div>@endif
                    </div>
                </label>
                @endif
            </div>
            @error('selected_mode')<div class="fe" style="margin-top:8px;">{{ $message }}</div>@enderror
        </div>
        @else
        <input type="hidden" name="selected_mode" value="{{ $schedule->training_mode }}">
        @endif

        {{-- 5. Additional Information --}}
        <div class="reg-card">
            <div class="reg-card-title"><div class="reg-card-num">5</div> Additional Information</div>
            <div class="fg" style="margin-bottom:16px;">
                <label class="fl">Dietary Requirements / Special Needs</label>
                <textarea name="special_requirements" class="fi" rows="2"
                          placeholder="e.g. vegetarian, wheelchair access…">{{ old('special_requirements') }}</textarea>
            </div>
            <div class="fg" style="margin-bottom:16px;">
                <label class="fl">How did you hear about us?</label>
                <select name="referral_source" class="fi">
                    <option value="">Select</option>
                    @foreach(['Google Search','LinkedIn','Facebook','Colleague / Friend','Previous Attendee','Email Newsletter','Website','Other'] as $src)
                    <option value="{{ $src }}" {{ old('referral_source') === $src ? 'selected' : '' }}>{{ $src }}</option>
                    @endforeach
                </select>
            </div>
            <div class="fg">
                <label class="fl">Any questions for the trainer?</label>
                <textarea name="pre_questions" class="fi" rows="2" placeholder="Optional…">{{ old('pre_questions') }}</textarea>
            </div>
        </div>

        {{-- Fee Summary --}}
        <div class="fee-summary"
             data-physical="{{ $physicalFee }}"
             data-online="{{ $onlineFee }}"
             data-currency="{{ $currency }}">
            <div class="fee-row">
                <span class="fee-label">Course</span>
                <span class="fee-value" style="font-size:13px;max-width:200px;text-align:right;line-height:1.4;">{{ $courseName }}</span>
            </div>
            @if($schedule->batch_code)
            <div class="fee-row">
                <span class="fee-label">Batch</span>
                <span class="fee-value">{{ $schedule->batch_code }}</span>
            </div>
            @endif
            <div class="fee-row">
                <span class="fee-label">Dates</span>
                <span class="fee-value">{{ \Carbon\Carbon::parse($schedule->start_date)->format('d M') }} – {{ \Carbon\Carbon::parse($schedule->end_date)->format('d M Y') }}</span>
            </div>
            <div class="fee-row">
                <span class="fee-label">Mode (<span id="feeModeLbl">—</span>)</span>
                <span class="fee-value" id="feeLine">{{ $currency }} —</span>
            </div>
            <div class="fee-row">
                <span class="fee-label">Total Due</span>
                <span class="fee-value" id="feeTotal">{{ $currency }} —</span>
            </div>
        </div>

        {{-- Payment Method (hidden manual) --}}
        <input type="hidden" name="payment_method" value="manual">

        {{-- Payment Info --}}
        @include('partials.registration-payment-info', ['type' => 'ilt'])

        {{-- Policies + Agreement --}}
        @include('partials.registration-agreement')

        <button type="submit" class="btn-reg-submit">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            Complete Enrollment
        </button>
    </form>

    @if($schedule->course)
    <a href="{{ route('public.course.detail', $schedule->course->slug ?? $schedule->course_id) }}" class="reg-back-link">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
        Back to Course Details
    </a>
    @endif

</div>

{{-- ── RIGHT: Sidebar ── --}}
<aside class="reg-sidebar">

    {{-- Schedule info card --}}
    <div class="sidebar-card">
        <div class="sidebar-img">
            @if($schedule->course?->banner_image)
            <img src="{{ asset('storage/'.$schedule->course->banner_image) }}" alt="{{ $courseName }}">
            @else <span>🏢</span> @endif
        </div>
        <div class="sidebar-body">
            <div class="sidebar-price-row">
                @if($physicalFee || $onlineFee)
                <div>
                    <div class="sidebar-price" id="sbFee">
                        {{ $currency }} {{ number_format($physicalFee ?: $onlineFee) }}
                    </div>
                    <div class="sidebar-price-label">per participant</div>
                </div>
                @else
                <div class="sidebar-price" style="font-size:18px;color:#6b7280;">Contact for fee</div>
                @endif
            </div>

            <div class="feature-row">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                📅 {{ \Carbon\Carbon::parse($schedule->start_date)->format('d M Y') }}
            </div>
            <div class="feature-row">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                {{ $schedule->training_mode }} training
            </div>
            @if($schedule->trainer)
            <div class="feature-row">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                Trainer: {{ $schedule->trainer->name }}
            </div>
            @endif
            @if($schedule->duration_days)
            <div class="feature-row">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                {{ $schedule->duration_days }} day{{ $schedule->duration_days > 1 ? 's' : '' }}
            </div>
            @endif
            <div class="feature-row">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                Certificate of completion
            </div>
            @if(!is_null($seatsLeft) && $seatsLeft > 0)
            <div class="feature-row" style="color:#16a34a;font-weight:700;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                {{ $seatsLeft }} seat{{ $seatsLeft > 1 ? 's' : '' }} remaining
            </div>
            @endif
        </div>
    </div>

    @include('partials.registration-sidebar-trust')

    <div class="sidebar-help">
        <div class="sidebar-help-title">Need help enrolling?</div>
        <div class="sidebar-help-sub">Our team is ready to assist you</div>
        <a href="mailto:training@smscert.com" class="sidebar-help-btn">📧 training@smscert.com</a>
    </div>
</aside>

</div>
</div>

@push('scripts')
<script>
(function () {
    var summary  = document.querySelector('.fee-summary');
    if (!summary) return;
    var physical = parseInt(summary.dataset.physical, 10) || 0;
    var online   = parseInt(summary.dataset.online,   10) || 0;
    var currency = summary.dataset.currency;

    function fmt(n) { return currency + ' ' + n.toLocaleString(); }

    function update(mode) {
        var fee = (mode === 'Online') ? online : physical;
        var lbl = document.getElementById('feeModeLbl');
        var line = document.getElementById('feeLine');
        var total = document.getElementById('feeTotal');
        var sbFee = document.getElementById('sbFee');
        if (lbl)   lbl.textContent   = mode || '—';
        if (line)  line.textContent  = fee ? fmt(fee) : currency + ' TBA';
        if (total) total.textContent = fee ? fmt(fee) : currency + ' TBA';
        if (sbFee && fee) sbFee.textContent = fmt(fee);
    }

    document.querySelectorAll('input[name="selected_mode"]').forEach(function (r) {
        r.addEventListener('change', function () { update(this.value); });
    });
    var checked = document.querySelector('input[name="selected_mode"]:checked');
    update(checked ? checked.value : '');
})();
</script>
@endpush

@endsection
