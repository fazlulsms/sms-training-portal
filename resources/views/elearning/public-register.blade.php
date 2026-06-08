<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — {{ $course->name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body {
            margin: 0; padding: 0;
            font-family: 'Inter', Arial, sans-serif;
            background: #f0f2f8;
            color: #111827;
            min-height: 100vh;
        }

        /* ── Top bar ── */
        .topbar {
            background: linear-gradient(135deg, #1e3a8a, #1e40af);
            padding: 14px 24px;
            display: flex; align-items: center; gap: 12px;
        }
        .topbar-logo {
            width: 40px; height: 40px; background: white;
            border-radius: 8px; padding: 5px; object-fit: contain;
        }
        .topbar-name { color: white; font-size: 17px; font-weight: 800; }
        .topbar-sub  { color: #93c5fd; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; }

        /* ── Page wrapper ── */
        .page { max-width: 760px; margin: 36px auto; padding: 0 16px 48px; }

        /* ── Course hero ── */
        .course-hero {
            background: linear-gradient(135deg, #0f766e, #0d9488);
            border-radius: 14px; padding: 28px 30px; color: white;
            margin-bottom: 24px; box-shadow: 0 4px 20px rgba(15,118,110,.25);
        }
        .hero-label  { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; opacity: .75; margin-bottom: 8px; }
        .hero-title  { font-size: 22px; font-weight: 800; margin: 0 0 14px; line-height: 1.3; }
        .hero-meta   { display: flex; flex-wrap: wrap; gap: 18px; }
        .hero-meta-item .label { font-size: 11px; opacity: .7; margin-bottom: 2px; }
        .hero-meta-item .val   { font-size: 14px; font-weight: 700; }

        /* ── Form card ── */
        .form-card {
            background: white; border: 1px solid #e5e7eb; border-radius: 14px;
            overflow: hidden; box-shadow: 0 2px 8px rgba(15,23,42,.07);
        }
        .form-card-header {
            padding: 18px 28px; border-bottom: 1px solid #f3f4f6;
            font-size: 16px; font-weight: 800; color: #111827;
        }
        .form-card-body { padding: 28px; }

        /* ── Form fields ── */
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .form-group { margin-bottom: 0; }
        .form-group.full { grid-column: 1 / -1; }
        .form-label {
            display: block; font-size: 13px; font-weight: 700;
            color: #374151; margin-bottom: 6px;
        }
        .form-label .req { color: #dc2626; margin-left: 2px; }
        .form-input {
            width: 100%; padding: 10px 13px;
            border: 1px solid #d1d5db; border-radius: 8px;
            font-size: 14px; font-family: inherit; color: #111827;
            transition: border-color .15s, box-shadow .15s;
        }
        .form-input:focus {
            outline: none; border-color: #0f766e;
            box-shadow: 0 0 0 3px rgba(15,118,110,.12);
        }
        .error-msg { color: #dc2626; font-size: 12px; margin-top: 4px; }

        /* ── Alerts ── */
        .alert {
            padding: 14px 18px; border-radius: 10px; font-weight: 600;
            margin-bottom: 20px; font-size: 14px;
        }
        .alert-success { background: #dcfce7; color: #166534; border: 1px solid #a7f3d0; }
        .alert-error   { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }

        /* ── Submit button ── */
        .btn-submit {
            width: 100%; padding: 13px;
            background: linear-gradient(135deg, #0f766e, #0d9488);
            color: white; border: none; border-radius: 10px;
            font-size: 15px; font-weight: 800; cursor: pointer;
            font-family: inherit; margin-top: 20px;
            transition: opacity .2s;
        }
        .btn-submit:hover { opacity: .9; }

        /* ── Note ── */
        .note {
            text-align: center; font-size: 12px; color: #9ca3af;
            margin-top: 16px; line-height: 1.6;
        }

        @media(max-width: 600px) {
            .form-grid { grid-template-columns: 1fr; }
            .course-hero { padding: 20px; }
            .form-card-body { padding: 20px; }
        }
    </style>
</head>
<body>

<div class="topbar">
    <img src="{{ asset('sms-logo.png') }}" alt="SMS" class="topbar-logo">
    <div>
        <div class="topbar-name">SMS Panel</div>
        <div class="topbar-sub">Training Management</div>
    </div>
</div>

<div class="page">

    {{-- Course hero --}}
    <div class="course-hero">
        <div class="hero-label">eLearning Course Registration</div>
        <h1 class="hero-title">{{ $course->name }}</h1>
        <div class="hero-meta">
            @if($course->code)
            <div class="hero-meta-item">
                <div class="label">Course Code</div>
                <div class="val">{{ $course->code }}</div>
            </div>
            @endif
            @if($course->duration)
            <div class="hero-meta-item">
                <div class="label">Duration</div>
                <div class="val">{{ $course->duration }}</div>
            </div>
            @endif
            @if($course->course_fee)
            <div class="hero-meta-item">
                <div class="label">Fee</div>
                <div class="val">BDT {{ number_format($course->course_fee, 2) }}</div>
            </div>
            @else
            <div class="hero-meta-item">
                <div class="label">Fee</div>
                <div class="val">Contact us</div>
            </div>
            @endif
            <div class="hero-meta-item">
                <div class="label">Mode</div>
                <div class="val">Self-Paced eLearning</div>
            </div>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success">
            ✅ {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">
            {{ session('error') }}
        </div>
    @endif

    {{-- Registration Form --}}
    @if(!session('success'))
    <div class="form-card">
        <div class="form-card-header">Complete Your Registration</div>
        <div class="form-card-body">

            <form method="POST" action="{{ route('elearning.public.register.store', $course->id) }}">
                @csrf

                <div class="form-grid">

                    <div class="form-group full">
                        <label class="form-label">Full Name <span class="req">*</span></label>
                        <input type="text" name="participant_name"
                               value="{{ old('participant_name') }}"
                               class="form-input" required
                               placeholder="Enter your full name">
                        @error('participant_name')
                            <div class="error-msg">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email Address <span class="req">*</span></label>
                        <input type="email" name="email"
                               value="{{ old('email') }}"
                               class="form-input" required
                               placeholder="your@email.com">
                        @error('email')
                            <div class="error-msg">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Phone / Mobile</label>
                        <input type="text" name="phone"
                               value="{{ old('phone') }}"
                               class="form-input"
                               placeholder="+880 1700 000000">
                        @error('phone')
                            <div class="error-msg">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Company / Organisation</label>
                        <input type="text" name="company"
                               value="{{ old('company') }}"
                               class="form-input"
                               placeholder="Your company name">
                        @error('company')
                            <div class="error-msg">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Designation / Job Title</label>
                        <input type="text" name="designation"
                               value="{{ old('designation') }}"
                               class="form-input"
                               placeholder="e.g. Quality Manager">
                        @error('designation')
                            <div class="error-msg">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group full">
                        <label class="form-label">Country</label>
                        <input type="text" name="country"
                               value="{{ old('country', 'Bangladesh') }}"
                               class="form-input"
                               placeholder="Your country">
                        @error('country')
                            <div class="error-msg">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

                <button type="submit" class="btn-submit">
                    Submit Registration →
                </button>

            </form>

            <p class="note">
                After submitting, our team will review your registration and send<br>
                payment and access details to your email address.
            </p>

        </div>
    </div>
    @endif

</div>

</body>
</html>
