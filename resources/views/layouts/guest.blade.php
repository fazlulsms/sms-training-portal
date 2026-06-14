<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'SMS Training Academy') }} — Login</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
    *, *::before, *::after { box-sizing: border-box; }
    body {
        margin: 0; padding: 0;
        font-family: 'Inter', system-ui, sans-serif;
        background: #f1f5f9;
        min-height: 100vh;
        -webkit-font-smoothing: antialiased;
    }

    /* ── Full-page split layout ── */
    .gl-shell {
        min-height: 100vh;
        display: grid;
        grid-template-columns: 1fr 1fr;
    }
    @media (max-width: 860px) {
        .gl-shell { grid-template-columns: 1fr; }
        .gl-brand  { display: none; }
    }

    /* ── Left brand panel ── */
    .gl-brand {
        background: linear-gradient(145deg, #0a1a5c 0%, #1e3a8a 50%, #1d4ed8 100%);
        position: relative; overflow: hidden;
        display: flex; flex-direction: column;
        justify-content: space-between;
        padding: 48px;
        color: #fff;
    }
    .gl-brand::before {
        content: '';
        position: absolute; inset: 0;
        background: url("data:image/svg+xml,%3Csvg width='80' height='80' viewBox='0 0 80 80' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.025'%3E%3Cpath d='M50 50c0-5.523 4.477-10 10-10s10 4.477 10 10-4.477 10-10 10c0 5.523-4.477 10-10 10s-10-4.477-10-10 4.477-10 10-10zM10 10c0-5.523 4.477-10 10-10s10 4.477 10 10-4.477 10-10 10c0 5.523-4.477 10-10 10S0 25.523 0 20s4.477-10 10-10z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }

    .gl-brand-logo {
        display: flex; align-items: center; gap: 12px;
        text-decoration: none; position: relative; z-index: 1;
    }
    .gl-brand-logo-icon {
        width: 44px; height: 44px; border-radius: 12px;
        background: rgba(255,255,255,.12);
        border: 1px solid rgba(255,255,255,.2);
        display: flex; align-items: center; justify-content: center;
    }
    .gl-brand-logo-text strong {
        display: block; font-size: 15px; font-weight: 900; color: #fff; line-height: 1.2;
    }
    .gl-brand-logo-text span {
        display: block; font-size: 10.5px; color: rgba(255,255,255,.5); margin-top: 1px;
    }

    .gl-brand-center { position: relative; z-index: 1; }
    .gl-brand-badge {
        display: inline-flex; align-items: center; gap: 8px;
        background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.2);
        padding: 6px 14px; border-radius: 20px;
        font-size: 11.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .7px;
        margin-bottom: 22px;
    }
    .gl-brand-headline {
        font-size: 36px; font-weight: 900; line-height: 1.2;
        margin: 0 0 18px;
        background: linear-gradient(135deg, #fff 0%, rgba(255,255,255,.75) 100%);
        -webkit-background-clip: text; -webkit-text-fill-color: transparent;
    }
    .gl-brand-sub {
        font-size: 15.5px; opacity: .75; line-height: 1.7; margin: 0 0 36px;
    }
    .gl-trust-items { display: flex; flex-direction: column; gap: 14px; }
    .gl-trust-item {
        display: flex; align-items: center; gap: 12px;
        background: rgba(255,255,255,.07); border: 1px solid rgba(255,255,255,.1);
        border-radius: 12px; padding: 14px 16px;
    }
    .gl-trust-icon {
        width: 36px; height: 36px; border-radius: 9px;
        background: rgba(255,255,255,.1);
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .gl-trust-title  { font-size: 13.5px; font-weight: 800; line-height: 1.2; }
    .gl-trust-detail { font-size: 12px; opacity: .6; margin-top: 2px; }

    .gl-brand-footer {
        position: relative; z-index: 1;
        font-size: 12px; opacity: .45;
    }

    /* ── Right form panel ── */
    .gl-form-panel {
        display: flex; flex-direction: column;
        justify-content: center; align-items: center;
        padding: 48px 40px;
        background: #fff;
        min-height: 100vh;
    }
    @media (max-width: 480px) { .gl-form-panel { padding: 32px 20px; } }

    .gl-form-wrap { width: 100%; max-width: 400px; }

    /* Mobile logo — only visible on small screens */
    .gl-mobile-logo {
        display: none;
        text-align: center; margin-bottom: 32px;
    }
    @media (max-width: 860px) { .gl-mobile-logo { display: block; } }
    .gl-mobile-logo-icon {
        width: 52px; height: 52px; border-radius: 14px;
        background: linear-gradient(135deg,#0f2470,#1e3a8a);
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 12px;
        box-shadow: 0 6px 20px rgba(15,36,112,.25);
    }
    .gl-mobile-logo strong { display: block; font-size: 16px; font-weight: 900; color: #0f2470; }
    .gl-mobile-logo span   { display: block; font-size: 11px; color: #6b7280; margin-top: 3px; }

    .gl-form-heading {
        font-size: 24px; font-weight: 900; color: #111827; margin: 0 0 6px;
    }
    .gl-form-sub {
        font-size: 14.5px; color: #6b7280; margin: 0 0 32px; line-height: 1.6;
    }

    /* Form element overrides — work alongside Tailwind/Breeze defaults */
    .gl-form-wrap .block { display: block; }
    .gl-form-wrap label  { font-size: 13.5px; font-weight: 700; color: #374151; display: block; margin-bottom: 6px; }
    .gl-form-wrap input[type="email"],
    .gl-form-wrap input[type="password"],
    .gl-form-wrap input[type="text"] {
        width: 100%; padding: 11px 14px;
        border: 1.5px solid #e5e7eb; border-radius: 10px;
        font-size: 14.5px; font-family: inherit; color: #111827;
        background: #fff; outline: none;
        transition: border-color .14s, box-shadow .14s;
    }
    .gl-form-wrap input:focus {
        border-color: #1e3a8a;
        box-shadow: 0 0 0 3px rgba(30,58,138,.1);
    }
    .gl-form-wrap .mt-4 { margin-top: 18px; }
    .gl-form-wrap .mt-1 { margin-top: 5px; }

    .gl-submit-btn {
        display: block; width: 100%;
        background: linear-gradient(135deg,#1e3a8a,#2563eb); color: #fff;
        border: none; padding: 13px;
        border-radius: 11px; font-size: 15px; font-weight: 800;
        cursor: pointer; font-family: inherit;
        box-shadow: 0 4px 14px rgba(37,99,235,.3);
        transition: opacity .14s, transform .14s;
    }
    .gl-submit-btn:hover { opacity: .92; transform: translateY(-1px); }

    .gl-divider { border: none; border-top: 1px solid #f0f2f5; margin: 28px 0; }

    .gl-back-link {
        display: block; text-align: center;
        font-size: 13.5px; color: #6b7280; text-decoration: none;
        transition: color .13s;
    }
    .gl-back-link:hover { color: #1e3a8a; }
    .gl-back-link svg { vertical-align: -3px; margin-right: 5px; }

    .gl-form-footer { margin-top: 36px; text-align: center; font-size: 12px; color: #9ca3af; }
    </style>
</head>
<body>

<div class="gl-shell">

    {{-- ── Left: Brand panel ── --}}
    <div class="gl-brand">
        <a href="{{ route('public.home') }}" class="gl-brand-logo">
            <div class="gl-brand-logo-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M12 2L3 7v5c0 5.25 3.75 10.15 9 11.25C17.25 22.15 21 17.25 21 12V7L12 2z" fill="rgba(255,255,255,.15)" stroke="rgba(255,255,255,.5)" stroke-width="1.5"/>
                    <path d="M8 12l3 3 5-5" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="gl-brand-logo-text">
                <strong>SMS Training Academy</strong>
                <span>Powered by Sustainable Management System Inc.</span>
            </div>
        </a>

        <div class="gl-brand-center">
            <div class="gl-brand-badge">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg>
                Learner Portal
            </div>
            <h1 class="gl-brand-headline">
                Your Professional<br>Learning Hub
            </h1>
            <p class="gl-brand-sub">
                Access your enrolled courses, track progress, download certificates, and grow your career — all in one place.
            </p>
            <div class="gl-trust-items">
                <div class="gl-trust-item">
                    <div class="gl-trust-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.85)" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                    </div>
                    <div>
                        <div class="gl-trust-title">eLearning &amp; ILT Courses</div>
                        <div class="gl-trust-detail">Self-paced online and instructor-led formats</div>
                    </div>
                </div>
                <div class="gl-trust-item">
                    <div class="gl-trust-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.85)" stroke-width="2"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg>
                    </div>
                    <div>
                        <div class="gl-trust-title">Internationally Recognised Certificates</div>
                        <div class="gl-trust-detail">CPD accredited · QR-verified · Instant download</div>
                    </div>
                </div>
                <div class="gl-trust-item">
                    <div class="gl-trust-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.85)" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                    </div>
                    <div>
                        <div class="gl-trust-title">Track Your Progress</div>
                        <div class="gl-trust-detail">Lesson-by-lesson completion tracking</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="gl-brand-footer">
            &copy; {{ date('Y') }} SMS Training Academy. All rights reserved.
        </div>
    </div>

    {{-- ── Right: Form panel ── --}}
    <div class="gl-form-panel">
        <div class="gl-form-wrap">

            {{-- Mobile logo --}}
            <div class="gl-mobile-logo">
                <a href="{{ route('public.home') }}" style="text-decoration:none;">
                    <div class="gl-mobile-logo-icon">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none">
                            <path d="M12 2L3 7v5c0 5.25 3.75 10.15 9 11.25C17.25 22.15 21 17.25 21 12V7L12 2z" fill="rgba(255,255,255,.15)" stroke="rgba(255,255,255,.6)" stroke-width="1.5"/>
                            <path d="M8 12l3 3 5-5" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <strong>SMS Training Academy</strong>
                    <span>Powered by Sustainable Management System Inc.</span>
                </a>
            </div>

            <h2 class="gl-form-heading">Welcome back</h2>
            <p class="gl-form-sub">Sign in to access your courses and certificates.</p>

            {{ $slot }}

            <button class="gl-submit-btn" form="loginForm" style="display:none;" id="glLoginBtn">
                Sign In to Academy
            </button>

            <hr class="gl-divider">

            <a href="{{ route('public.home') }}" class="gl-back-link">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                Back to SMS Training Academy
            </a>

            <div class="gl-form-footer">
                &copy; {{ date('Y') }} SMS Training Academy · Sustainable Management System Inc.<br>
                277 Cherry Street, Suite-12N, New York, NY, USA
            </div>
        </div>
    </div>

</div>

<script>
// Wire the custom submit button to the Breeze form
document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form');
    const btn  = document.querySelector('form [type="submit"]');
    // Style the Breeze submit button to match our design
    if (btn) {
        btn.style.cssText = 'display:block;width:100%;background:linear-gradient(135deg,#1e3a8a,#2563eb);color:#fff;border:none;padding:13px;border-radius:11px;font-size:15px;font-weight:800;cursor:pointer;font-family:inherit;box-shadow:0 4px 14px rgba(37,99,235,.3);transition:opacity .14s,transform .14s;margin-top:8px;';
    }
});
</script>
</body>
</html>
