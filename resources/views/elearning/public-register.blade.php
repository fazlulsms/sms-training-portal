@extends('layouts.public')

@section('page-title', 'Register — ' . $course->name)
@section('seo-title', 'Enroll in ' . $course->name . ' — SMS Training Services')
@section('seo-desc', 'Register for ' . $course->name . '. Self-paced eLearning with certificate of completion.')

@push('head')
@include('partials.registration-styles')
@endpush

@section('content')

{{-- Hero --}}
<div class="reg-hero">
    <div class="pub-container reg-hero-inner">
        <div class="reg-breadcrumb">
            <a href="{{ route('public.home') }}">Home</a><span>/</span>
            <a href="{{ route('public.courses') }}">Courses</a><span>/</span>
            <a href="{{ route('public.course.detail', $course->slug ?? $course->id) }}">{{ Str::limit($course->name, 40) }}</a><span>/</span>
            <span>Register</span>
        </div>
        <div class="reg-hero-type">💻 eLearning Registration</div>
        <h1>{{ $course->name }}</h1>
        <div class="reg-hero-badges">
            <span class="reg-hero-badge">💻 Self-Paced eLearning</span>
            @if($course->duration)<span class="reg-hero-badge">⏱ {{ $course->duration }}</span>@endif
            @if($course->language)<span class="reg-hero-badge">🌐 {{ $course->language }}</span>@endif
            @if($course->cpd_hours)<span class="reg-hero-badge">⭐ {{ $course->cpd_hours }} CPD Hours</span>@endif
            @if($course->certificate_type)<span class="reg-hero-badge">🎓 {{ $course->certificate_type }}</span>@endif
        </div>
    </div>
</div>

<div class="pub-container">
<div class="reg-body">

{{-- ── LEFT: Form ── --}}
<div class="reg-main">

    @if(session('success'))
    <div class="reg-success-card">
        <div class="reg-success-icon">
            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
        </div>
        <h2>Registration Submitted!</h2>
        <p>Thank you for registering for <strong>{{ $course->name }}</strong>.<br>Our team will send payment instructions and course access to your email within 24 hours.</p>
        <div class="reg-next-steps">
            <div class="reg-next-title">What happens next?</div>
            @foreach(['Confirmation email sent within 24 hours','Our team sends payment instructions to your email','Course access granted once payment is verified','Start learning at your own pace — anytime, anywhere'] as $step)
            <div class="reg-next-item">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                {{ $step }}
            </div>
            @endforeach
        </div>
        <div class="reg-success-actions">
            <a href="{{ route('public.courses') }}" class="pub-enroll-btn">Browse More Courses</a>
            <a href="{{ route('public.home') }}" class="btn-ghost-link">← Back to Home</a>
        </div>
    </div>

    @else

    @if(session('error'))
    <div class="reg-alert-error">⚠️ {{ session('error') }}</div>
    @endif
    @if($errors->any())
    <div class="reg-alert-error">⚠ Please fix the highlighted errors below before submitting.</div>
    @endif

    <form method="POST" action="{{ route('elearning.public.register.store', $course->id) }}">
        @csrf

        {{-- 1. Personal Information --}}
        <div class="reg-card">
            <div class="reg-card-title"><div class="reg-card-num">1</div> Personal Information</div>
            <div class="form-grid-2">
                <div class="fg full">
                    <label class="fl">Full Name <span class="req">*</span></label>
                    <input type="text" name="participant_name" value="{{ old('participant_name') }}"
                           class="fi {{ $errors->has('participant_name') ? 'is-err' : '' }}"
                           required placeholder="As per NID / Passport">
                    @error('participant_name')<div class="fe">{{ $message }}</div>@enderror
                </div>
                <div class="fg">
                    <label class="fl">Email Address <span class="req">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="fi {{ $errors->has('email') ? 'is-err' : '' }}"
                           required placeholder="you@example.com">
                    @error('email')<div class="fe">{{ $message }}</div>@enderror
                    <div class="fh">Login credentials will be sent here.</div>
                </div>
                <div class="fg">
                    <label class="fl">Mobile Number</label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                           class="fi" placeholder="+880 1X-XXXXXXXXX">
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
                    <input type="text" name="designation" value="{{ old('designation') }}" class="fi" placeholder="e.g. Quality Manager">
                </div>
                <div class="fg">
                    <label class="fl">Organization / Company</label>
                    <input type="text" name="company" value="{{ old('company') }}" class="fi">
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
                    <select name="country" class="fi {{ $errors->has('country') ? 'is-err' : '' }}">
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
                    <input type="text" name="full_address" value="{{ old('full_address') }}" class="fi" placeholder="Street, area, postal code">
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

        {{-- 4. Additional Information --}}
        <div class="reg-card">
            <div class="reg-card-title"><div class="reg-card-num">4</div> Additional Information</div>
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
                <label class="fl">Any questions before you start?</label>
                <textarea name="pre_questions" class="fi" rows="2"
                          placeholder="Optional — we'll answer before granting access…">{{ old('pre_questions') }}</textarea>
            </div>
        </div>

        {{-- Payment Info --}}
        @include('partials.registration-payment-info', ['type' => 'elearning'])

        {{-- Policies + Agreement --}}
        @include('partials.registration-agreement')

        <button type="submit" class="btn-reg-submit">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            Submit Registration
        </button>
    </form>

    <a href="{{ route('public.course.detail', $course->slug ?? $course->id) }}" class="reg-back-link">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
        Back to Course Details
    </a>

    @endif
</div>

{{-- ── RIGHT: Sidebar ── --}}
<aside class="reg-sidebar">
    @php $fee = $course->public_price ?: $course->course_fee; @endphp

    <div class="sidebar-card">
        <div class="sidebar-img">
            @if($course->banner_image)
            <img src="{{ asset('storage/'.$course->banner_image) }}" alt="{{ $course->name }}">
            @else <span>🎓</span> @endif
        </div>
        <div class="sidebar-body">
            <div class="sidebar-price-row">
                @if($fee)
                <div class="sidebar-price">BDT {{ number_format($fee) }}</div>
                <div class="sidebar-price-label">one-time</div>
                @else
                <div class="sidebar-price" style="font-size:18px;color:#6b7280;">Contact for fee</div>
                @endif
            </div>
            <div class="feature-row">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                Self-paced — learn at your own pace
            </div>
            @if($course->access_days)
            <div class="feature-row">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                {{ $course->access_days }}-day course access
            </div>
            @endif
            <div class="feature-row">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                Certificate of completion
            </div>
            @if($course->cpd_hours)
            <div class="feature-row">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                {{ $course->cpd_hours }} CPD Hours awarded
            </div>
            @endif
            <div class="feature-row">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                Access on any device
            </div>
            <div class="feature-row">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                Expert-authored content
            </div>
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
@endsection
