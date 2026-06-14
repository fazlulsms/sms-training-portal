@extends('layouts.public')

@section('page-title', 'Register — ' . $course->name)
@section('seo-title', 'Enroll in ' . $course->name . ' — SMS Training Services')
@section('seo-desc', 'Register for ' . $course->name . '. Self-paced eLearning with certificate of completion.')

@section('content')
<style>
.reg-hero {
    background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 60%, #0f766e 100%);
    padding: 48px 0 56px; color: #fff; position: relative; overflow: hidden;
}
.reg-hero::before {
    content: ''; position: absolute; inset: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}
.reg-hero-inner { position: relative; }
.reg-breadcrumb { font-size: 13px; color: rgba(255,255,255,.6); display: flex; align-items: center; gap: 6px; margin-bottom: 20px; flex-wrap: wrap; }
.reg-breadcrumb a { color: rgba(255,255,255,.7); text-decoration: none; transition: color .15s; }
.reg-breadcrumb a:hover { color: #fff; }
.reg-breadcrumb span { opacity: .4; }
.reg-hero h1 { font-size: 30px; font-weight: 900; margin: 0 0 10px; line-height: 1.25; }
@media(max-width:768px){ .reg-hero h1 { font-size: 22px; } }
.reg-hero-badges { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 16px; }
.reg-hero-badge {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 5px 13px; border-radius: 20px;
    background: rgba(255,255,255,.12); color: rgba(255,255,255,.9);
    font-size: 12px; font-weight: 600; border: 1px solid rgba(255,255,255,.15);
}

.reg-body { display: grid; grid-template-columns: 1fr 380px; gap: 32px; padding: 40px 0 60px; align-items: start; }
@media(max-width: 900px) { .reg-body { grid-template-columns: 1fr; } .reg-sidebar { order: -1; } }

/* Form */
.reg-form-card {
    background: #fff; border: 1px solid #e5e7eb; border-radius: 16px;
    overflow: hidden; box-shadow: 0 4px 24px rgba(15,23,42,.08);
}
.reg-form-header {
    padding: 20px 28px; border-bottom: 1px solid #f0f2f5;
    display: flex; align-items: center; gap: 10px;
}
.reg-form-header-icon {
    width: 36px; height: 36px; border-radius: 10px;
    background: linear-gradient(135deg, #0f766e, #0d9488);
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.reg-form-header h2 { font-size: 16px; font-weight: 800; color: #111827; margin: 0; }
.reg-form-header p  { font-size: 12px; color: #9ca3af; margin: 2px 0 0; }
.reg-form-body { padding: 28px; }

.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.form-group { display: flex; flex-direction: column; gap: 5px; }
.form-group.full { grid-column: 1 / -1; }
@media(max-width: 600px) { .form-grid { grid-template-columns: 1fr; } }
.form-label { font-size: 12px; font-weight: 700; color: #374151; text-transform: uppercase; letter-spacing: .4px; }
.form-label .req { color: #dc2626; margin-left: 2px; }
.form-input {
    padding: 10px 13px; border: 1.5px solid #e5e7eb; border-radius: 9px;
    font-size: 14px; font-family: inherit; color: #111827; background: #fff;
    transition: border-color .15s, box-shadow .15s;
}
.form-input:focus { outline: none; border-color: #0f766e; box-shadow: 0 0 0 3px rgba(15,118,110,.1); }
.error-msg { color: #dc2626; font-size: 11.5px; }

.btn-submit {
    width: 100%; padding: 14px; margin-top: 22px;
    background: linear-gradient(135deg, #1e3a8a, #0f766e);
    color: #fff; border: none; border-radius: 11px;
    font-size: 15px; font-weight: 800; cursor: pointer;
    font-family: inherit; letter-spacing: .2px;
    transition: opacity .2s, transform .1s;
    display: flex; align-items: center; justify-content: center; gap: 8px;
}
.btn-submit:hover { opacity: .92; transform: translateY(-1px); }
.btn-submit:active { transform: translateY(0); }

.reg-note {
    display: flex; align-items: flex-start; gap: 8px;
    background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px;
    padding: 12px 14px; margin-top: 16px; font-size: 12.5px; color: #166534; line-height: 1.5;
}

/* Sidebar */
.reg-sidebar {}
.sidebar-course-card {
    background: #fff; border: 1px solid #e5e7eb; border-radius: 16px;
    overflow: hidden; box-shadow: 0 4px 20px rgba(15,23,42,.07); margin-bottom: 18px;
}
.sidebar-course-img { height: 170px; overflow: hidden; background: linear-gradient(135deg,#1e3a8a,#0f766e); display: flex; align-items: center; justify-content: center; font-size: 52px; position: relative; }
.sidebar-course-img img { width: 100%; height: 100%; object-fit: cover; }
.sidebar-course-body { padding: 20px; }
.sidebar-price {
    display: flex; align-items: baseline; gap: 6px;
    margin-bottom: 16px; padding-bottom: 16px;
    border-bottom: 1px solid #f0f2f5;
}
.sidebar-price-amount { font-size: 28px; font-weight: 900; color: #1e3a8a; }
.sidebar-price-label  { font-size: 12px; color: #9ca3af; }
.feature-row { display: flex; align-items: center; gap: 10px; padding: 7px 0; border-bottom: 1px solid #f9fafb; font-size: 13.5px; color: #374151; }
.feature-row:last-child { border-bottom: none; }
.feature-row svg { flex-shrink: 0; color: #16a34a; }

.trust-card {
    background: linear-gradient(135deg, #1e3a8a, #1d4ed8);
    border-radius: 14px; padding: 20px; color: #fff;
}
.trust-card h4 { font-size: 15px; font-weight: 800; margin: 0 0 6px; }
.trust-card p  { font-size: 13px; opacity: .85; margin: 0 0 14px; line-height: 1.5; }
.trust-bullets { display: flex; flex-direction: column; gap: 7px; }
.trust-bullet { display: flex; align-items: center; gap: 8px; font-size: 13px; opacity: .9; }

/* Success state */
.success-card {
    background: #fff; border: 1px solid #e5e7eb; border-radius: 16px;
    padding: 48px 36px; text-align: center;
    box-shadow: 0 4px 24px rgba(15,23,42,.08);
}
.success-icon {
    width: 72px; height: 72px; background: #dcfce7; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 20px;
}
</style>

{{-- Hero --}}
<div class="reg-hero">
    <div class="pub-container reg-hero-inner">
        <div class="reg-breadcrumb">
            <a href="{{ route('public.home') }}">Home</a>
            <span>/</span>
            <a href="{{ route('public.courses') }}">Courses</a>
            <span>/</span>
            <a href="{{ route('public.course.detail', $course->slug ?? $course->id) }}">{{ Str::limit($course->name, 40) }}</a>
            <span>/</span>
            <span style="color:rgba(255,255,255,.9);">Register</span>
        </div>
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

    {{-- LEFT: Form --}}
    <div>

        @if(session('success'))
        <div class="success-card">
            <div class="success-icon">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <h3 style="font-size:22px;font-weight:900;color:#111827;margin:0 0 10px;">Registration Submitted!</h3>
            <p style="font-size:15px;color:#6b7280;line-height:1.6;margin:0 0 24px;">
                Thank you for registering for <strong>{{ $course->name }}</strong>.<br>
                Our team will review your registration and send payment &amp; access details to your email shortly.
            </p>
            <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:16px;text-align:left;margin-bottom:24px;">
                <div style="font-size:13px;font-weight:700;color:#166534;margin-bottom:8px;">What happens next?</div>
                @foreach(['You will receive a confirmation email within 24 hours','Our team will send payment instructions to your email','After payment confirmation, you get immediate course access','Start learning at your own pace anytime, anywhere'] as $step)
                <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:#166534;padding:4px 0;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    {{ $step }}
                </div>
                @endforeach
            </div>
            <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
                <a href="{{ route('public.courses') }}" class="pub-enroll-btn" style="background:#1e3a8a;">Browse More Courses</a>
                <a href="{{ route('public.home') }}" style="display:inline-flex;align-items:center;gap:6px;padding:11px 22px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:14px;font-weight:700;color:#374151;text-decoration:none;">← Back to Home</a>
            </div>
        </div>

        @else

        @if(session('error'))
        <div style="background:#fee2e2;border:1px solid #fca5a5;border-radius:10px;padding:14px 18px;color:#991b1b;font-size:14px;font-weight:600;margin-bottom:20px;">
            ⚠️ {{ session('error') }}
        </div>
        @endif

        <div class="reg-form-card">
            <div class="reg-form-header">
                <div class="reg-form-header-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </div>
                <div>
                    <h2>Complete Your Registration</h2>
                    <p>Fill in your details below to enrol</p>
                </div>
            </div>
            <div class="reg-form-body">
                <form method="POST" action="{{ route('elearning.public.register.store', $course->id) }}">
                    @csrf
                    <div class="form-grid">

                        <div class="form-group full">
                            <label class="form-label">Full Name <span class="req">*</span></label>
                            <input type="text" name="participant_name" value="{{ old('participant_name') }}"
                                   class="form-input" required placeholder="e.g. Rahman Al-Amin">
                            @error('participant_name')<div class="error-msg">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">Email Address <span class="req">*</span></label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                   class="form-input" required placeholder="your@email.com">
                            @error('email')<div class="error-msg">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">Phone / Mobile</label>
                            <input type="text" name="phone" value="{{ old('phone') }}"
                                   class="form-input" placeholder="+880 1700 000000">
                            @error('phone')<div class="error-msg">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">Company / Organisation</label>
                            <input type="text" name="company" value="{{ old('company') }}"
                                   class="form-input" placeholder="Your company name">
                            @error('company')<div class="error-msg">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">Designation / Job Title</label>
                            <input type="text" name="designation" value="{{ old('designation') }}"
                                   class="form-input" placeholder="e.g. Quality Manager">
                            @error('designation')<div class="error-msg">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group full">
                            <label class="form-label">Country</label>
                            <input type="text" name="country" value="{{ old('country', 'Bangladesh') }}"
                                   class="form-input">
                            @error('country')<div class="error-msg">{{ $message }}</div>@enderror
                        </div>

                    </div>

                    <button type="submit" class="btn-submit">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                        Submit Registration
                    </button>
                </form>

                <div class="reg-note">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:1px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    After submitting, our team will review your registration and send payment instructions and course access details to your email address within 24 hours.
                </div>
            </div>
        </div>

        <a href="{{ route('public.course.detail', $course->slug ?? $course->id) }}"
           style="display:inline-flex;align-items:center;gap:6px;margin-top:16px;font-size:13px;font-weight:600;color:#6b7280;text-decoration:none;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
            Back to Course Details
        </a>

        @endif
    </div>

    {{-- RIGHT: Sidebar --}}
    <aside class="reg-sidebar">

        {{-- Course card --}}
        <div class="sidebar-course-card">
            <div class="sidebar-course-img">
                @if($course->banner_image)
                <img src="{{ asset('storage/'.$course->banner_image) }}" alt="{{ $course->name }}">
                @else 🎓 @endif
            </div>
            <div class="sidebar-course-body">
                @php $fee = $course->public_price ?: $course->course_fee; @endphp
                <div class="sidebar-price">
                    @if($fee)
                    <div class="sidebar-price-amount">BDT {{ number_format($fee) }}</div>
                    <div class="sidebar-price-label">one-time</div>
                    @else
                    <div class="sidebar-price-amount" style="font-size:18px;color:#6b7280;">Contact for fee</div>
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

        {{-- Trust card --}}
        <div class="trust-card">
            <h4>Why SMS Training?</h4>
            <p>Bangladesh's leading professional training & certification provider, trusted by 500+ organisations.</p>
            <div class="trust-bullets">
                <div class="trust-bullet">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    Internationally recognised certificates
                </div>
                <div class="trust-bullet">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    ISO standards specialists
                </div>
                <div class="trust-bullet">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    Dedicated learner support
                </div>
                <div class="trust-bullet">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    Flexible payment options
                </div>
            </div>
        </div>

        {{-- Need help --}}
        <div style="background:#fff;border:1px solid #e5e7eb;border-radius:14px;padding:18px;margin-top:18px;text-align:center;">
            <div style="font-size:13px;font-weight:700;color:#111827;margin-bottom:4px;">Need help enrolling?</div>
            <div style="font-size:12.5px;color:#6b7280;margin-bottom:12px;">Our team is ready to assist you</div>
            <a href="mailto:training@smscert.com"
               style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;background:#f0f4ff;color:#1e3a8a;border-radius:8px;font-size:13px;font-weight:700;text-decoration:none;">
                📧 training@smscert.com
            </a>
        </div>

    </aside>

</div>
</div>
@endsection
