<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Enroll — {{ $schedule->course?->name ?? $schedule->training_title }}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing:border-box; }
body { margin:0; font-family:'Inter',sans-serif; background:#f8fafc; color:#111827; font-size:15px; }

/* Top bar */
.enroll-topbar { background:#0f172a; padding:14px 24px; display:flex; align-items:center; justify-content:space-between; }
.enroll-logo { color:#fff; font-size:18px; font-weight:900; text-decoration:none; }
.enroll-logo span { color:#3b82f6; }
.enroll-topbar-link { color:rgba(255,255,255,.6); font-size:13px; text-decoration:none; }
.enroll-topbar-link:hover { color:#fff; }

/* Hero strip */
.enroll-hero {
    background:linear-gradient(135deg,#1e3a8a,#2563eb);
    color:#fff; padding:28px 24px;
}
.enroll-hero-inner { max-width:760px; margin:0 auto; }
.enroll-hero-label { font-size:11.5px; font-weight:800; text-transform:uppercase; letter-spacing:.8px; opacity:.6; margin-bottom:8px; }
.enroll-hero-title { font-size:22px; font-weight:900; margin:0 0 12px; line-height:1.3; }
.enroll-hero-chips { display:flex; flex-wrap:wrap; gap:10px; }
.enroll-chip {
    background:rgba(255,255,255,.15); padding:5px 13px; border-radius:20px;
    font-size:12.5px; font-weight:600; display:inline-flex; align-items:center; gap:5px;
}

/* Layout */
.enroll-body { max-width:760px; margin:0 auto; padding:32px 16px 60px; }

/* Progress steps */
.enroll-steps { display:flex; align-items:center; margin-bottom:32px; }
.step-item { display:flex; align-items:center; gap:8px; font-size:13px; font-weight:700; }
.step-circle {
    width:28px; height:28px; border-radius:50%; display:flex; align-items:center;
    justify-content:center; font-size:12px; font-weight:900; flex-shrink:0;
    background:#e5e7eb; color:#9ca3af;
}
.step-item.active .step-circle  { background:#1e3a8a; color:#fff; }
.step-item.done .step-circle    { background:#16a34a; color:#fff; }
.step-item.active .step-label   { color:#111827; }
.step-label { color:#9ca3af; }
.step-line { flex:1; height:2px; background:#e5e7eb; margin:0 8px; }
.step-line.done { background:#16a34a; }

/* Card */
.enroll-card { background:#fff; border:1px solid #e9ecf0; border-radius:16px; padding:28px; margin-bottom:20px; }
.enroll-card-title { font-size:16px; font-weight:800; color:#111827; margin:0 0 20px; display:flex; align-items:center; gap:8px; }
.enroll-card-title-num { width:24px; height:24px; border-radius:50%; background:#1e3a8a; color:#fff; font-size:11px; font-weight:900; display:flex; align-items:center; justify-content:center; flex-shrink:0; }

/* Form fields */
.form-row { display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:0; }
@media(max-width:540px){ .form-row{grid-template-columns:1fr;} }
.form-group { margin-bottom:18px; }
.form-group:last-child { margin-bottom:0; }
.form-label { font-size:13px; font-weight:700; color:#374151; margin-bottom:6px; display:block; }
.form-input {
    width:100%; padding:11px 14px; border:1.5px solid #e5e7eb; border-radius:10px;
    font-size:14.5px; font-family:'Inter',sans-serif; color:#111827; background:#fff; transition:border-color .14s;
}
.form-input:focus { outline:none; border-color:#1e3a8a; box-shadow:0 0 0 3px rgba(30,58,138,.1); }
.form-input.is-invalid { border-color:#ef4444; }
.form-error { color:#ef4444; font-size:12px; margin-top:4px; }
.form-hint  { color:#9ca3af; font-size:12px; margin-top:4px; }

/* Radio mode cards */
.mode-cards { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
@media(max-width:400px){ .mode-cards{grid-template-columns:1fr;} }
.mode-card-label { display:block; position:relative; cursor:pointer; }
.mode-card-label input[type=radio] { position:absolute; opacity:0; width:0; height:0; }
.mode-card {
    border:2px solid #e5e7eb; border-radius:12px; padding:16px;
    transition:all .14s; text-align:center;
}
.mode-card-label input:checked + .mode-card {
    border-color:#1e3a8a; background:#f0f4ff;
}
.mode-card-icon { font-size:28px; margin-bottom:6px; }
.mode-card-name { font-size:14px; font-weight:800; color:#111827; }
.mode-card-desc { font-size:12px; color:#6b7280; margin-top:2px; }

/* Fee summary */
.fee-summary { background:linear-gradient(135deg,#1e3a8a,#2563eb); border-radius:14px; padding:22px; color:#fff; }
.fee-row { display:flex; justify-content:space-between; font-size:14px; padding:7px 0; border-bottom:1px solid rgba(255,255,255,.15); }
.fee-row:last-child { border-bottom:none; font-size:18px; font-weight:900; padding-top:14px; }
.fee-label { opacity:.8; }
.fee-value { font-weight:700; }

/* Submit button */
.enroll-submit {
    width:100%; background:linear-gradient(135deg,#1e3a8a,#2563eb); color:#fff;
    border:none; padding:16px; border-radius:12px; font-size:16px; font-weight:900;
    cursor:pointer; font-family:'Inter',sans-serif; margin-top:4px;
    display:flex; align-items:center; justify-content:center; gap:8px;
}
.enroll-submit:hover { opacity:.92; }

/* Terms */
.enroll-terms { font-size:12.5px; color:#9ca3af; text-align:center; margin-top:12px; line-height:1.6; }
.enroll-terms a { color:#6b7280; }

/* Policy note */
.policy-box { background:#fffbeb; border:1px solid #fcd34d; border-radius:10px; padding:14px 16px; font-size:13px; color:#92400e; line-height:1.6; margin-top:16px; }
</style>
</head>
<body>

{{-- Top bar --}}
<div class="enroll-topbar">
    <a href="{{ url('/') }}" class="enroll-logo">SMS <span>Training</span></a>
    <a href="{{ route('public.course.detail', $schedule->course->slug ?? $schedule->course_id) }}" class="enroll-topbar-link">
        ← Back to Course
    </a>
</div>

{{-- Hero --}}
<div class="enroll-hero">
    <div class="enroll-hero-inner">
        <div class="enroll-hero-label">Enrollment Form</div>
        <div class="enroll-hero-title">{{ $schedule->course?->name ?? $schedule->training_title }}</div>
        <div class="enroll-hero-chips">
            <span class="enroll-chip">📅 {{ \Carbon\Carbon::parse($schedule->start_date)->format('d M') }} – {{ \Carbon\Carbon::parse($schedule->end_date)->format('d M Y') }}</span>
            <span class="enroll-chip">{{ $schedule->training_mode }}</span>
            @if($schedule->training_mode !== 'Online' && $schedule->venue)
            <span class="enroll-chip">📍 {{ $schedule->venue }}</span>
            @elseif($schedule->training_mode === 'Online')
            <span class="enroll-chip">📍 Online (Zoom)</span>
            @endif
            @if($schedule->trainer)
            <span class="enroll-chip">👤 {{ $schedule->trainer->name }}</span>
            @endif
            @php
                $seatsLeft = $schedule->seats_left;
            @endphp
            @if(!is_null($seatsLeft))
            <span class="enroll-chip" style="{{ $seatsLeft <= 5 ? 'background:rgba(239,68,68,.2);' : '' }}">
                {{ $seatsLeft <= 0 ? '🔴 Full' : '🟢 ' . $seatsLeft . ' seats left' }}
            </span>
            @endif
        </div>
    </div>
</div>

{{-- Body --}}
<div class="enroll-body">

    {{-- Steps --}}
    <div class="enroll-steps">
        <div class="step-item active">
            <div class="step-circle">1</div>
            <span class="step-label">Your Details</span>
        </div>
        <div class="step-line"></div>
        <div class="step-item">
            <div class="step-circle">2</div>
            <span class="step-label">Review</span>
        </div>
        <div class="step-line"></div>
        <div class="step-item">
            <div class="step-circle">3</div>
            <span class="step-label">Payment</span>
        </div>
        <div class="step-line"></div>
        <div class="step-item">
            <div class="step-circle">4</div>
            <span class="step-label">Confirmed</span>
        </div>
    </div>

    {{-- Error banner --}}
    @if($errors->any())
    <div style="background:#fee2e2;border:1px solid #fca5a5;border-radius:12px;padding:14px 18px;margin-bottom:20px;color:#991b1b;font-size:14px;font-weight:600;">
        ⚠ Please fix the errors below before submitting.
    </div>
    @endif

    <form method="POST" action="{{ route('public.enroll.store', $schedule->id) }}">
        @csrf

        {{-- Section 1: Personal Info --}}
        <div class="enroll-card">
            <div class="enroll-card-title">
                <div class="enroll-card-title-num">1</div>
                Personal Information
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Full Name *</label>
                    <input type="text" name="full_name" class="form-input {{ $errors->has('full_name') ? 'is-invalid' : '' }}"
                           value="{{ old('full_name') }}" placeholder="As per NID / Passport" required>
                    @error('full_name')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Email Address *</label>
                    <input type="email" name="email" class="form-input {{ $errors->has('email') ? 'is-invalid' : '' }}"
                           value="{{ old('email') }}" placeholder="you@example.com" required>
                    @error('email')<p class="form-error">{{ $message }}</p>@enderror
                    <p class="form-hint">Your login credentials will be sent here.</p>
                </div>
                <div class="form-group">
                    <label class="form-label">Mobile Number *</label>
                    <input type="text" name="phone" class="form-input {{ $errors->has('phone') ? 'is-invalid' : '' }}"
                           value="{{ old('phone') }}" placeholder="+880 1X-XXXXXXXXX" required>
                    @error('phone')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Gender</label>
                    <select name="gender" class="form-input">
                        <option value="">Select gender</option>
                        @foreach(['Male','Female','Other','Prefer not to say'] as $g)
                        <option value="{{ $g }}" {{ old('gender') === $g ? 'selected' : '' }}>{{ $g }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">NID / Passport No.</label>
                    <input type="text" name="nid_passport" class="form-input" value="{{ old('nid_passport') }}">
                </div>
            </div>
        </div>

        {{-- Section 2: Professional Details --}}
        <div class="enroll-card">
            <div class="enroll-card-title">
                <div class="enroll-card-title-num">2</div>
                Professional Details
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Job Title / Designation</label>
                    <input type="text" name="designation" class="form-input" value="{{ old('designation') }}" placeholder="e.g. Safety Officer">
                </div>
                <div class="form-group">
                    <label class="form-label">Organization / Company</label>
                    <input type="text" name="company" class="form-input" value="{{ old('company') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Industry / Sector</label>
                    <input type="text" name="industry" class="form-input" value="{{ old('industry') }}" placeholder="e.g. Construction, Oil & Gas">
                </div>
                <div class="form-group">
                    <label class="form-label">Years of Experience</label>
                    <select name="experience_years" class="form-input">
                        <option value="">Select</option>
                        @foreach(['Less than 1 year','1-3 years','3-5 years','5-10 years','10+ years'] as $e)
                        <option value="{{ $e }}" {{ old('experience_years') === $e ? 'selected' : '' }}>{{ $e }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Section 3: Contact / Address --}}
        <div class="enroll-card">
            <div class="enroll-card-title">
                <div class="enroll-card-title-num">3</div>
                Contact &amp; Address
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Country *</label>
                    <select name="country" class="form-input" id="countrySelect" required>
                        <option value="">Select country</option>
                        @php
                        $countries = ['Bangladesh','India','Pakistan','Nepal','Sri Lanka','Myanmar','UAE','Saudi Arabia','Qatar','Kuwait','UK','USA','Canada','Australia','Other'];
                        @endphp
                        @foreach($countries as $c)
                        <option value="{{ $c }}" {{ old('country','Bangladesh') === $c ? 'selected' : '' }}>{{ $c }}</option>
                        @endforeach
                    </select>
                    @error('country')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">City / District</label>
                    <input type="text" name="city" class="form-input" value="{{ old('city') }}">
                </div>
                <div class="form-group" style="grid-column:1/-1;">
                    <label class="form-label">Full Address</label>
                    <input type="text" name="full_address" class="form-input" value="{{ old('full_address') }}" placeholder="Street, area, postal code">
                </div>
                <div class="form-group">
                    <label class="form-label">Emergency Contact Name</label>
                    <input type="text" name="emergency_contact_name" class="form-input" value="{{ old('emergency_contact_name') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Emergency Contact Phone</label>
                    <input type="text" name="emergency_contact_phone" class="form-input" value="{{ old('emergency_contact_phone') }}">
                </div>
            </div>
        </div>

        {{-- Section 4: Participation Mode --}}
        @if(in_array($schedule->training_mode, ['Hybrid','Physical','Online']))
        <div class="enroll-card">
            <div class="enroll-card-title">
                <div class="enroll-card-title-num">4</div>
                Participation Mode
            </div>
            <div class="mode-cards">
                @if(in_array($schedule->training_mode, ['Physical','Hybrid']))
                <label class="mode-card-label">
                    <input type="radio" name="selected_mode" value="Physical"
                           {{ old('selected_mode', $schedule->training_mode !== 'Online' ? 'Physical' : '') === 'Physical' ? 'checked' : '' }}>
                    <div class="mode-card">
                        <div class="mode-card-icon">🏢</div>
                        <div class="mode-card-name">Physical</div>
                        <div class="mode-card-desc">{{ $schedule->venue ?? 'At our venue' }}</div>
                        @php $physFee = $schedule->discount_fee ?? $schedule->physical_fee; @endphp
                        @if($physFee)<div style="font-size:13px;font-weight:800;color:#1e3a8a;margin-top:6px;">{{ $schedule->currency ?? 'BDT' }} {{ number_format($physFee) }}</div>@endif
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
                        @php $onlineFee = $schedule->discount_fee ?? $schedule->online_fee; @endphp
                        @if($onlineFee)<div style="font-size:13px;font-weight:800;color:#1e3a8a;margin-top:6px;">{{ $schedule->currency ?? 'BDT' }} {{ number_format($onlineFee) }}</div>@endif
                    </div>
                </label>
                @endif
            </div>
            @error('selected_mode')<p class="form-error" style="margin-top:8px;">{{ $message }}</p>@enderror
        </div>
        @else
        <input type="hidden" name="selected_mode" value="{{ $schedule->training_mode }}">
        @endif

        {{-- Section 5: Dietary / Special --}}
        <div class="enroll-card">
            <div class="enroll-card-title">
                <div class="enroll-card-title-num">5</div>
                Additional Information
            </div>
            <div class="form-group">
                <label class="form-label">Dietary Requirements / Special Needs</label>
                <textarea name="special_requirements" class="form-input" rows="3" placeholder="e.g. vegetarian, wheelchair access…">{{ old('special_requirements') }}</textarea>
            </div>
            <div class="form-group">
                <label class="form-label">How did you hear about us?</label>
                <select name="referral_source" class="form-input">
                    <option value="">Select</option>
                    @foreach(['Google Search','LinkedIn','Facebook','Colleague / Friend','Previous Attendee','Email Newsletter','Website','Other'] as $src)
                    <option value="{{ $src }}" {{ old('referral_source') === $src ? 'selected' : '' }}>{{ $src }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Any questions for the trainer?</label>
                <textarea name="pre_questions" class="form-input" rows="2" placeholder="Optional…">{{ old('pre_questions') }}</textarea>
            </div>
        </div>

        {{-- Fee Summary --}}
        <div class="fee-summary">
            <div class="fee-row">
                <span class="fee-label">Course</span>
                <span class="fee-value" style="font-size:13px;max-width:220px;text-align:right;">{{ $schedule->course?->name ?? $schedule->training_title }}</span>
            </div>
            <div class="fee-row">
                <span class="fee-label">Batch</span>
                <span class="fee-value">{{ $schedule->batch_code }}</span>
            </div>
            <div class="fee-row">
                <span class="fee-label">Dates</span>
                <span class="fee-value">{{ \Carbon\Carbon::parse($schedule->start_date)->format('d M') }} – {{ \Carbon\Carbon::parse($schedule->end_date)->format('d M Y') }}</span>
            </div>
            @php
                $enrollFee = $schedule->discount_fee ?? ($schedule->training_mode === 'Online' ? $schedule->online_fee : ($schedule->physical_fee ?? $schedule->online_fee));
            @endphp
            @if($enrollFee)
            <div class="fee-row">
                <span class="fee-label">Enrollment Fee</span>
                <span class="fee-value">{{ $schedule->currency ?? 'BDT' }} {{ number_format($enrollFee) }}</span>
            </div>
            @endif
            <div class="fee-row">
                <span class="fee-label">Total Due</span>
                <span class="fee-value">{{ $schedule->currency ?? 'BDT' }} {{ $enrollFee ? number_format($enrollFee) : 'TBA' }}</span>
            </div>
        </div>

        <div class="policy-box" style="margin-top:16px;">
            📋 <strong>Payment notice:</strong> Course fee is payable upon confirmation. You will receive payment instructions via email after enrollment. Payment can be made via bank transfer, bKash, or online payment gateway.
        </div>

        <input type="hidden" name="payment_method" value="manual">

        <button type="submit" class="enroll-submit" style="margin-top:20px;">
            ✅ Complete Enrollment
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
        </button>
        <div class="enroll-terms">
            By submitting this form you agree to our
            <a href="#">Terms & Conditions</a> and <a href="#">Privacy Policy</a>.
            Your data will only be used for training administration.
        </div>
    </form>
</div>

</body>
</html>
