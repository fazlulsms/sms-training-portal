<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register â€” {{ $schedule->course->name ?? 'Training' }} | SMS Training Academy</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
    *, *::before, *::after { box-sizing: border-box; }

    body {
        margin: 0;
        min-height: 100vh;
        font-family: 'Inter', system-ui, sans-serif;
        color: #111827;
        background: #f0f4ff;
    }

    /* â”€â”€ Top nav bar â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
    .top-bar {
        background: linear-gradient(135deg, #042C53 0%, #042C53 60%, #378ADD 100%);
        padding: 14px 32px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 2px 12px rgba(15,36,112,.3);
    }
    .top-bar-brand {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .top-bar-logo {
        width: 40px; height: 40px;
        background: rgba(255,255,255,.15);
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 20px;
        border: 1px solid rgba(255,255,255,.2);
    }
    .top-bar-name {
        color: #fff;
        font-weight: 800;
        font-size: 16px;
        line-height: 1.2;
    }
    .top-bar-tagline {
        color: rgba(255,255,255,.6);
        font-size: 11px;
        font-weight: 500;
    }
    .top-bar-badge {
        background: rgba(255,255,255,.12);
        border: 1px solid rgba(255,255,255,.2);
        color: rgba(255,255,255,.85);
        padding: 5px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    /* â”€â”€ Hero / Course card â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
    .hero {
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
        padding: 40px 32px 50px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    .hero::before {
        content: '';
        position: absolute;
        top: -60px; right: -60px;
        width: 240px; height: 240px;
        background: rgba(255,255,255,.05);
        border-radius: 50%;
    }
    .hero::after {
        content: '';
        position: absolute;
        bottom: -40px; left: -40px;
        width: 160px; height: 160px;
        background: rgba(255,255,255,.04);
        border-radius: 50%;
    }
    .hero-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(255,255,255,.12);
        border: 1px solid rgba(255,255,255,.2);
        color: rgba(255,255,255,.85);
        padding: 5px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .6px;
        margin-bottom: 16px;
        position: relative; z-index: 1;
    }
    .hero-title {
        color: #fff;
        font-size: 28px;
        font-weight: 900;
        margin: 0 0 20px;
        line-height: 1.3;
        position: relative; z-index: 1;
        max-width: 680px;
        margin-left: auto;
        margin-right: auto;
    }
    .hero-chips {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 10px;
        position: relative; z-index: 1;
    }
    .hero-chip {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        background: rgba(255,255,255,.12);
        border: 1px solid rgba(255,255,255,.18);
        color: #fff;
        padding: 7px 14px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        backdrop-filter: blur(4px);
    }
    .hero-chip svg { opacity: .8; flex-shrink: 0; }

    /* â”€â”€ Fee highlight chips â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
    .fee-row {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 10px;
        margin-top: 16px;
        position: relative; z-index: 1;
    }
    .fee-chip {
        background: rgba(255,255,255,.92);
        color: #1e3a8a;
        padding: 8px 18px;
        border-radius: 10px;
        font-size: 13.5px;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .fee-chip .fee-label {
        font-size: 11px;
        font-weight: 600;
        color: #6b7280;
        margin-right: 2px;
    }

    /* â”€â”€ Main content wrapper â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
    .page-wrap {
        max-width: 780px;
        margin: -28px auto 48px;
        padding: 0 20px;
        position: relative;
        z-index: 10;
    }

    /* â”€â”€ Form card â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
    .form-card {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 8px 40px rgba(15,23,42,.12);
        overflow: hidden;
    }

    /* â”€â”€ Success banner â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
    .success-banner {
        background: linear-gradient(135deg, #065f46 0%, #059669 100%);
        color: #fff;
        padding: 28px 32px;
        text-align: center;
    }
    .success-icon {
        width: 60px; height: 60px;
        background: rgba(255,255,255,.2);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 28px;
        margin: 0 auto 14px;
    }
    .success-title {
        font-size: 20px;
        font-weight: 800;
        margin: 0 0 6px;
    }
    .success-sub {
        font-size: 14px;
        opacity: .85;
        margin: 0;
    }

    /* â”€â”€ Form section headers â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
    .form-section {
        padding: 28px 32px 0;
    }
    .form-section:last-of-type {
        padding-bottom: 0;
    }
    .section-label {
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .8px;
        color: #9ca3af;
        margin: 0 0 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .section-label::after {
        content: '';
        flex: 1;
        height: 1px;
        background: #f0f2f5;
    }

    /* â”€â”€ Form grid â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
    .fg { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px; }
    .fg-full { grid-column: 1 / -1; }
    @media (max-width: 600px) { .fg { grid-template-columns: 1fr; } }

    /* â”€â”€ Form fields â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
    .field { display: flex; flex-direction: column; gap: 6px; }
    .field label {
        font-size: 13px;
        font-weight: 700;
        color: #374151;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .field label .req { color: #ef4444; font-size: 14px; line-height: 1; }
    .field input,
    .field select,
    .field textarea {
        width: 100%;
        padding: 10px 14px;
        border: 1.5px solid #e5e7eb;
        border-radius: 10px;
        font-size: 14px;
        font-family: inherit;
        color: #111827;
        background: #fafbfc;
        transition: border-color .15s, box-shadow .15s;
        outline: none;
        appearance: none;
        -webkit-appearance: none;
    }
    .field input:focus,
    .field select:focus,
    .field textarea:focus {
        border-color: #2563eb;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(37,99,235,.1);
    }
    .field input::placeholder,
    .field textarea::placeholder { color: #c4c9d4; }

    /* Select arrow */
    .select-wrap { position: relative; }
    .select-wrap select { padding-right: 36px; cursor: pointer; }
    .select-wrap::after {
        content: '';
        position: absolute;
        right: 13px; top: 50%;
        transform: translateY(-50%);
        width: 0; height: 0;
        border-left: 5px solid transparent;
        border-right: 5px solid transparent;
        border-top: 6px solid #9ca3af;
        pointer-events: none;
    }

    /* Phone row */
    .phone-row { display: flex; gap: 10px; }
    .phone-code-wrap { position: relative; }
    .phone-code-wrap input {
        width: 90px;
        text-align: center;
        font-weight: 700;
        color: #1e3a8a;
        background: #f0f4ff;
        border-color: #c7d2fe;
        flex-shrink: 0;
    }
    .phone-code-wrap input:focus { border-color: #2563eb; background: #fff; }
    .phone-main input { flex: 1; }

    /* Mode cards */
    .mode-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
    @media (max-width: 400px) { .mode-grid { grid-template-columns: 1fr; } }
    .mode-card {
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        padding: 14px 16px;
        cursor: pointer;
        transition: border-color .15s, background .15s;
        display: flex;
        align-items: flex-start;
        gap: 12px;
        position: relative;
    }
    .mode-card:has(input:checked) { border-color: #2563eb; background: #eff6ff; }
    .mode-card input[type=radio] { display: none; }
    .mode-radio {
        width: 20px; height: 20px;
        border-radius: 50%;
        border: 2px solid #d1d5db;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0; margin-top: 1px;
        transition: border-color .15s, background .15s;
    }
    .mode-card:has(input:checked) .mode-radio {
        border-color: #2563eb;
        background: #2563eb;
    }
    .mode-radio::after {
        content: '';
        width: 7px; height: 7px;
        border-radius: 50%;
        background: #fff;
        opacity: 0;
        transition: opacity .15s;
    }
    .mode-card:has(input:checked) .mode-radio::after { opacity: 1; }
    .mode-name { font-weight: 800; font-size: 14px; color: #111827; }
    .mode-fee  { font-size: 12.5px; color: #6b7280; margin-top: 3px; font-weight: 600; }
    .mode-card:has(input:checked) .mode-name { color: #1e3a8a; }
    .mode-card:has(input:checked) .mode-fee  { color: #2563eb; }

    /* â”€â”€ Form footer â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
    .form-footer {
        padding: 24px 32px 32px;
        border-top: 1px solid #f0f2f5;
        margin-top: 24px;
        display: flex;
        flex-direction: column;
        gap: 14px;
    }
    .submit-btn {
        width: 100%;
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
        color: #fff;
        border: none;
        padding: 14px 28px;
        border-radius: 12px;
        font-size: 15px;
        font-weight: 800;
        font-family: inherit;
        cursor: pointer;
        transition: opacity .15s, transform .1s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        box-shadow: 0 4px 14px rgba(37,99,235,.35);
    }
    .submit-btn:hover   { opacity: .92; }
    .submit-btn:active  { transform: scale(.98); }
    .form-note {
        text-align: center;
        font-size: 12.5px;
        color: #9ca3af;
        line-height: 1.6;
    }
    .form-note a { color: #2563eb; font-weight: 600; text-decoration: none; }

    /* â”€â”€ Validation errors â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
    .err-box {
        background: #fef2f2;
        border: 1px solid #fecaca;
        border-radius: 10px;
        padding: 14px 18px;
        margin: 20px 32px 0;
        font-size: 13.5px;
        color: #991b1b;
    }
    .err-box ul { margin: 6px 0 0; padding-left: 18px; }
    .err-box li { margin-bottom: 2px; }
    .field-err { font-size: 12px; color: #dc2626; font-weight: 600; margin-top: 3px; }
    .field input.has-err, .field select.has-err, .field textarea.has-err {
        border-color: #fca5a5;
        background: #fff5f5;
    }

    /* â”€â”€ Footer strip â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
    .page-footer {
        text-align: center;
        padding: 20px;
        font-size: 12.5px;
        color: #9ca3af;
    }
    .page-footer strong { color: #6b7280; }
    </style>
</head>
<body>

{{-- â”€â”€ Top bar â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
<div class="top-bar">
    <div class="top-bar-brand">
        <div class="top-bar-logo">ðŸŽ“</div>
        <div>
            <div class="top-bar-name">SMS Training Academy</div>
            <div class="top-bar-tagline">Sustainable Management System Inc.</div>
        </div>
    </div>
    <div class="top-bar-badge">Public Registration</div>
</div>

{{-- â”€â”€ Hero â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
<div class="hero">
    <div class="hero-eyebrow">
        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
        Open Enrollment
    </div>
    <h1 class="hero-title">{{ $schedule->course->name ?? 'Training Course' }}</h1>

    <div class="hero-chips">
        @if($schedule->batch_code)
        <span class="hero-chip">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            Batch: {{ $schedule->batch_code }}
        </span>
        @endif
        @if($schedule->start_date && $schedule->end_date)
        <span class="hero-chip">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            {{ \Carbon\Carbon::parse($schedule->start_date)->format('d M Y') }}
            â€“
            {{ \Carbon\Carbon::parse($schedule->end_date)->format('d M Y') }}
        </span>
        @endif
        @if($schedule->training_mode)
        <span class="hero-chip">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
            {{ $schedule->training_mode }}
        </span>
        @endif
        @if($schedule->venue)
        <span class="hero-chip">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            {{ $schedule->venue }}
        </span>
        @endif
    </div>

    <div class="fee-row">
        @if($schedule->physical_fee > 0 && ($schedule->training_mode === 'Physical' || $schedule->training_mode === 'Hybrid'))
        <span class="fee-chip">
            <span class="fee-label">Physical</span>
            {{ $schedule->currency }} {{ number_format($schedule->physical_fee, 2) }}
        </span>
        @endif
        @if($schedule->online_fee > 0 && ($schedule->training_mode === 'Online' || $schedule->training_mode === 'Hybrid'))
        <span class="fee-chip">
            <span class="fee-label">Online</span>
            {{ $schedule->currency }} {{ number_format($schedule->online_fee, 2) }}
        </span>
        @endif
    </div>
</div>

{{-- â”€â”€ Form card â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
<div class="page-wrap">
    <div class="form-card">

        {{-- Success state --}}
        @if(session('success'))
        <div class="success-banner">
            <div class="success-icon">âœ“</div>
            <div class="success-title">Registration Submitted!</div>
            <p class="success-sub">{{ session('success') }}</p>
        </div>
        @endif

        {{-- Validation errors --}}
        @if($errors->any())
        <div class="err-box">
            <strong>Please fix the following errors:</strong>
            <ul>
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="/register-training/{{ $schedule->id }}">
            @csrf

            {{-- Personal Info --}}
            <div class="form-section">
                <div class="section-label">Personal Information</div>
                <div class="fg">
                    <div class="field">
                        <label>Full Name <span class="req">*</span></label>
                        <input type="text" name="full_name"
                               value="{{ old('full_name') }}"
                               placeholder="Enter your full name"
                               class="{{ $errors->has('full_name') ? 'has-err' : '' }}"
                               required>
                        @error('full_name')<div class="field-err">{{ $message }}</div>@enderror
                    </div>
                    <div class="field">
                        <label>Email Address <span class="req">*</span></label>
                        <input type="email" name="email"
                               value="{{ old('email') }}"
                               placeholder="you@example.com"
                               class="{{ $errors->has('email') ? 'has-err' : '' }}"
                               required>
                        @error('email')<div class="field-err">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            {{-- Professional Info --}}
            <div class="form-section" style="padding-top:20px;">
                <div class="section-label">Professional Details</div>
                <div class="fg">
                    <div class="field">
                        <label>Company / Organization</label>
                        <input type="text" name="company"
                               value="{{ old('company') }}"
                               placeholder="Your company name">
                    </div>
                    <div class="field">
                        <label>Designation / Job Title</label>
                        <input type="text" name="designation"
                               value="{{ old('designation') }}"
                               placeholder="e.g. Safety Officer">
                    </div>
                </div>
            </div>

            {{-- Contact Info --}}
            <div class="form-section" style="padding-top:20px;">
                <div class="section-label">Contact Information</div>
                <div class="fg">
                    <div class="field">
                        <label>Country</label>
                        <div class="select-wrap">
                            <select name="country" id="countrySelect">
                                <option value="">â€” Select Country â€”</option>
                                <option value="Bangladesh"           data-code="+880" {{ old('country') == 'Bangladesh'           ? 'selected' : '' }}>ðŸ‡§ðŸ‡© Bangladesh</option>
                                <option value="India"                data-code="+91"  {{ old('country') == 'India'                ? 'selected' : '' }}>ðŸ‡®ðŸ‡³ India</option>
                                <option value="United States"        data-code="+1"   {{ old('country') == 'United States'        ? 'selected' : '' }}>ðŸ‡ºðŸ‡¸ United States</option>
                                <option value="United Arab Emirates" data-code="+971" {{ old('country') == 'United Arab Emirates' ? 'selected' : '' }}>ðŸ‡¦ðŸ‡ª United Arab Emirates</option>
                                <option value="Malaysia"             data-code="+60"  {{ old('country') == 'Malaysia'             ? 'selected' : '' }}>ðŸ‡²ðŸ‡¾ Malaysia</option>
                                <option value="Indonesia"            data-code="+62"  {{ old('country') == 'Indonesia'            ? 'selected' : '' }}>ðŸ‡®ðŸ‡© Indonesia</option>
                                <option value="Vietnam"              data-code="+84"  {{ old('country') == 'Vietnam'              ? 'selected' : '' }}>ðŸ‡»ðŸ‡³ Vietnam</option>
                                <option value="Thailand"             data-code="+66"  {{ old('country') == 'Thailand'             ? 'selected' : '' }}>ðŸ‡¹ðŸ‡­ Thailand</option>
                                <option value="Sri Lanka"            data-code="+94"  {{ old('country') == 'Sri Lanka'            ? 'selected' : '' }}>ðŸ‡±ðŸ‡° Sri Lanka</option>
                                <option value="Nepal"                data-code="+977" {{ old('country') == 'Nepal'                ? 'selected' : '' }}>ðŸ‡³ðŸ‡µ Nepal</option>
                            </select>
                        </div>
                    </div>

                    <div class="field">
                        <label>Mobile Number</label>
                        <div class="phone-row">
                            <div class="phone-code-wrap">
                                <input type="text" name="country_code" id="countryCode"
                                       value="{{ old('country_code') }}"
                                       placeholder="+880">
                            </div>
                            <div class="phone-main" style="flex:1;">
                                <input type="text" name="mobile_number"
                                       value="{{ old('mobile_number') }}"
                                       placeholder="01XXXXXXXXX">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="fg">
                    <div class="field fg-full">
                        <label>Full Address</label>
                        <textarea name="full_address" rows="2"
                                  placeholder="Street, City, Postal Code">{{ old('full_address') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Participation Mode --}}
            <div class="form-section" style="padding-top:20px;">
                <div class="section-label">Participation Mode <span style="color:#ef4444; margin-left:2px;">*</span></div>
                <div class="mode-grid">
                    @if($schedule->training_mode === 'Physical' || $schedule->training_mode === 'Hybrid')
                    <label class="mode-card">
                        <input type="radio" name="selected_mode" value="Physical"
                               {{ old('selected_mode', $schedule->training_mode === 'Physical' ? 'Physical' : '') == 'Physical' ? 'checked' : '' }}
                               required>
                        <div class="mode-radio"></div>
                        <div>
                            <div class="mode-name">ðŸ¢ Physical</div>
                            <div class="mode-fee">
                                {{ $schedule->currency }} {{ number_format($schedule->physical_fee, 2) }}
                                @if($schedule->venue) Â· {{ $schedule->venue }} @endif
                            </div>
                        </div>
                    </label>
                    @endif

                    @if($schedule->training_mode === 'Online' || $schedule->training_mode === 'Hybrid')
                    <label class="mode-card">
                        <input type="radio" name="selected_mode" value="Online"
                               {{ old('selected_mode', $schedule->training_mode === 'Online' ? 'Online' : '') == 'Online' ? 'checked' : '' }}
                               required>
                        <div class="mode-radio"></div>
                        <div>
                            <div class="mode-name">ðŸ’» Online</div>
                            <div class="mode-fee">
                                {{ $schedule->currency }} {{ number_format($schedule->online_fee, 2) }}
                                Â· Zoom / Virtual
                            </div>
                        </div>
                    </label>
                    @endif
                </div>
                @error('selected_mode')<div class="field-err" style="margin-top:8px;">{{ $message }}</div>@enderror
            </div>

            {{-- Submit --}}
            <div class="form-footer">
                <button type="submit" class="submit-btn">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="22 2 15 22 11 13 2 9 22 2"/></svg>
                    Submit Registration
                </button>
                <p class="form-note">
                    By submitting this form you agree to be contacted by SMS Training Academy.<br>
                    Questions? Email <a href="mailto:info@smstraining.com.bd">info@smstraining.com.bd</a>
                </p>
            </div>

        </form>
    </div>
</div>

<div class="page-footer">
    &copy; {{ date('Y') }} <strong>SMS Training Academy</strong> â€” Sustainable Management System Inc.
</div>

<script>
// Auto-fill country code when country is selected
document.getElementById('countrySelect').addEventListener('change', function () {
    const code = this.options[this.selectedIndex].dataset.code || '';
    document.getElementById('countryCode').value = code;
});
</script>

</body>
</html>
