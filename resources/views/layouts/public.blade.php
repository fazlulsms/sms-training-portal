<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('seo-title', config('app.name', 'SMS Training')) | @yield('page-title', 'Professional Training & Certification')</title>
    <meta name="description" content="@yield('seo-desc', 'SMS Training Services — Professional capacity building and certification programs in Bangladesh.')">
    <meta name="keywords"    content="@yield('seo-keys', 'training, certification, capacity building, SMS Training, Bangladesh')">
    <meta property="og:title"       content="@yield('og-title', config('app.name'))">
    <meta property="og:description" content="@yield('seo-desc', '')">
    <meta property="og:image"       content="@yield('og-image', asset('images/og-default.jpg'))">
    <meta property="og:type"        content="website">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
    /* ══ PUBLIC LAYOUT GLOBAL STYLES ══════════════════════════ */
    *, *::before, *::after { box-sizing: border-box; }
    html { scroll-behavior: smooth; }
    body {
        margin: 0; padding: 0;
        font-family: 'Inter', system-ui, sans-serif;
        color: #1f2937;
        background: #fff;
        line-height: 1.6;
        -webkit-font-smoothing: antialiased;
    }

    /* ── Top bar ─────────────────────────────────────────────── */
    .pub-topbar {
        background: #0f2470;
        color: rgba(255,255,255,.75);
        font-size: 12.5px;
        padding: 7px 0;
    }
    .pub-topbar-inner {
        max-width: 1240px; margin: 0 auto; padding: 0 24px;
        display: flex; align-items: center; justify-content: space-between; gap: 16px;
        flex-wrap: wrap;
    }
    .pub-topbar a { color: rgba(255,255,255,.75); text-decoration: none; }
    .pub-topbar a:hover { color: #fff; }
    .pub-topbar-left, .pub-topbar-right { display: flex; align-items: center; gap: 18px; }

    /* ── Main nav ─────────────────────────────────────────────── */
    .pub-nav {
        background: #fff;
        border-bottom: 1px solid #e9ecf0;
        position: sticky; top: 0; z-index: 100;
        box-shadow: 0 2px 12px rgba(15,23,42,.07);
    }
    .pub-nav-inner {
        max-width: 1240px; margin: 0 auto; padding: 0 24px;
        display: flex; align-items: center; justify-content: space-between; gap: 20px;
        height: 68px;
    }

    /* Logo */
    .pub-logo {
        display: flex; align-items: center; gap: 12px;
        text-decoration: none; flex-shrink: 0;
    }
    .pub-logo-icon {
        width: 44px; height: 44px;
        background: linear-gradient(135deg, #0f2470 0%, #1e3a8a 100%);
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        color: #fff; font-size: 20px; font-weight: 900;
        box-shadow: 0 3px 10px rgba(15,36,112,.3);
    }
    .pub-logo-text strong {
        display: block; font-size: 15px; font-weight: 900;
        color: #0f2470; line-height: 1.2;
    }
    .pub-logo-text span {
        display: block; font-size: 11px; font-weight: 500;
        color: #6b7280; margin-top: 1px;
    }

    /* Desktop menu */
    .pub-menu {
        display: flex; align-items: center; gap: 2px; flex: 1; justify-content: center;
    }
    .pub-menu a {
        padding: 8px 12px; border-radius: 8px;
        font-size: 13.5px; font-weight: 600; color: #374151;
        text-decoration: none; white-space: nowrap;
        transition: color .14s, background .14s;
    }
    .pub-menu a:hover, .pub-menu a.active { color: #1e3a8a; background: #eff6ff; }

    /* Nav actions */
    .pub-nav-actions { display: flex; align-items: center; gap: 10px; flex-shrink: 0; }
    .pub-btn {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 8px 18px; border-radius: 9px;
        font-weight: 700; font-size: 13px; font-family: inherit;
        text-decoration: none; cursor: pointer; transition: all .15s; white-space: nowrap;
    }
    .pub-btn-outline {
        border: 1.5px solid #1e3a8a; color: #1e3a8a; background: transparent;
    }
    .pub-btn-outline:hover { background: #eff6ff; }
    .pub-btn-solid {
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
        color: #fff; border: none;
        box-shadow: 0 3px 10px rgba(37,99,235,.3);
    }
    .pub-btn-solid:hover { opacity: .9; transform: translateY(-1px); }

    /* Hamburger */
    .pub-hamburger {
        display: none; flex-direction: column; gap: 5px;
        background: none; border: none; cursor: pointer; padding: 4px;
    }
    .pub-hamburger span {
        display: block; width: 24px; height: 2.5px;
        background: #374151; border-radius: 2px; transition: all .2s;
    }

    /* Mobile nav */
    .pub-mobile-nav {
        display: none; background: #fff;
        border-top: 1px solid #e9ecf0;
        padding: 16px 24px 20px;
        flex-direction: column; gap: 4px;
    }
    .pub-mobile-nav.open { display: flex; }
    .pub-mobile-nav a {
        padding: 10px 14px; border-radius: 8px;
        font-size: 14.5px; font-weight: 600; color: #374151;
        text-decoration: none; display: block;
    }
    .pub-mobile-nav a:hover { background: #f3f4f6; color: #1e3a8a; }
    .pub-mobile-nav .mob-divider { border: none; border-top: 1px solid #f0f2f5; margin: 8px 0; }

    /* ── Main content area ────────────────────────────────── */
    main.pub-main { min-height: 60vh; }

    /* ── Flash messages ───────────────────────────────────── */
    .pub-flash {
        max-width: 1240px; margin: 16px auto; padding: 0 24px;
    }
    .pub-alert {
        padding: 13px 18px; border-radius: 10px; font-weight: 600; font-size: 14px;
        display: flex; align-items: flex-start; gap: 10px;
        margin-bottom: 12px;
    }
    .pub-alert-success { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
    .pub-alert-error   { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
    .pub-alert-info    { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }

    /* ── Footer ───────────────────────────────────────────── */
    .pub-footer {
        background: #0f172a;
        color: rgba(255,255,255,.75);
        padding: 60px 0 0;
        margin-top: 80px;
    }
    .pub-footer-inner {
        max-width: 1240px; margin: 0 auto; padding: 0 24px;
    }
    .pub-footer-grid {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr 1fr;
        gap: 40px;
        padding-bottom: 50px;
        border-bottom: 1px solid rgba(255,255,255,.08);
    }
    @media (max-width: 900px)  { .pub-footer-grid { grid-template-columns: 1fr 1fr; } }
    @media (max-width: 560px)  { .pub-footer-grid { grid-template-columns: 1fr; } }

    .footer-brand .footer-logo {
        display: flex; align-items: center; gap: 10px; margin-bottom: 16px;
    }
    .footer-brand .footer-logo-icon {
        width: 40px; height: 40px;
        background: linear-gradient(135deg, #1e3a8a, #2563eb);
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        color: #fff; font-size: 18px; font-weight: 900;
    }
    .footer-brand .footer-logo-name {
        font-size: 15px; font-weight: 800; color: #fff; line-height: 1.2;
    }
    .footer-brand p {
        font-size: 13.5px; line-height: 1.7; margin: 0 0 20px;
    }
    .footer-social { display: flex; gap: 10px; flex-wrap: wrap; }
    .footer-social a {
        width: 34px; height: 34px; border-radius: 8px;
        background: rgba(255,255,255,.08);
        display: flex; align-items: center; justify-content: center;
        color: rgba(255,255,255,.7); text-decoration: none; font-size: 14px;
        transition: background .14s;
    }
    .footer-social a:hover { background: rgba(255,255,255,.18); color: #fff; }

    .footer-col h4 {
        font-size: 13px; font-weight: 800; color: #fff;
        text-transform: uppercase; letter-spacing: .6px;
        margin: 0 0 16px;
    }
    .footer-col ul { list-style: none; margin: 0; padding: 0; }
    .footer-col li { margin-bottom: 8px; }
    .footer-col a {
        color: rgba(255,255,255,.65); font-size: 13.5px;
        text-decoration: none; transition: color .14s;
    }
    .footer-col a:hover { color: #fff; }

    .footer-contact-item {
        display: flex; align-items: flex-start; gap: 8px;
        font-size: 13.5px; margin-bottom: 10px; color: rgba(255,255,255,.65);
    }
    .footer-contact-item svg { flex-shrink: 0; margin-top: 2px; }

    .pub-footer-bottom {
        padding: 18px 0;
        text-align: center;
        font-size: 13px; color: rgba(255,255,255,.4);
    }
    .pub-footer-bottom a { color: rgba(255,255,255,.55); text-decoration: none; }

    /* ── Shared card helpers ──────────────────────────────── */
    .pub-container { max-width: 1240px; margin: 0 auto; padding: 0 24px; }
    .pub-section { padding: 64px 0; }
    .pub-section-sm { padding: 40px 0; }
    .section-heading {
        font-size: 30px; font-weight: 900; color: #0f172a; margin: 0 0 6px; line-height: 1.25;
    }
    .section-subheading {
        font-size: 16px; color: #6b7280; margin: 0 0 40px; line-height: 1.6;
    }
    .section-eyebrow {
        font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;
        color: #2563eb; margin-bottom: 10px;
    }

    /* ── Course card ──────────────────────────────────────── */
    .course-card {
        background: #fff; border: 1px solid #e9ecf0; border-radius: 16px;
        overflow: hidden; display: flex; flex-direction: column;
        transition: box-shadow .2s, transform .2s;
        box-shadow: 0 2px 8px rgba(15,23,42,.06);
    }
    .course-card:hover {
        box-shadow: 0 8px 28px rgba(15,23,42,.12);
        transform: translateY(-3px);
    }
    .course-card-img {
        width: 100%; height: 180px; object-fit: cover; background: #f0f4ff;
        display: flex; align-items: center; justify-content: center; color: #9ca3af;
    }
    .course-card-img img { width: 100%; height: 100%; object-fit: cover; }
    .course-card-body { padding: 20px; flex: 1; display: flex; flex-direction: column; gap: 8px; }
    .course-card-category {
        font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px;
        color: #2563eb; background: #eff6ff; padding: 3px 9px; border-radius: 20px;
        display: inline-block;
    }
    .course-card-title {
        font-size: 15.5px; font-weight: 800; color: #111827;
        line-height: 1.35; margin: 0;
        text-decoration: none; display: block;
    }
    .course-card-title:hover { color: #1e3a8a; }
    .course-card-desc {
        font-size: 13.5px; color: #6b7280; line-height: 1.6;
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
        margin: 0;
    }
    .course-card-meta {
        display: flex; align-items: center; gap: 12px;
        flex-wrap: wrap; margin-top: 4px;
    }
    .course-card-meta-item {
        display: inline-flex; align-items: center; gap: 5px;
        font-size: 12.5px; color: #6b7280; font-weight: 500;
    }
    .course-card-footer {
        padding: 14px 20px;
        border-top: 1px solid #f0f2f5;
        display: flex; align-items: center; justify-content: space-between; gap: 10px;
    }
    .course-price { font-size: 16px; font-weight: 900; color: #1e3a8a; }
    .course-price small { font-size: 11px; color: #9ca3af; font-weight: 500; display: block; }
    .pub-enroll-btn {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 8px 16px; border-radius: 9px;
        background: #1e3a8a; color: #fff;
        font-size: 13px; font-weight: 700; text-decoration: none;
        transition: background .14s, transform .1s;
    }
    .pub-enroll-btn:hover { background: #1d4ed8; transform: translateY(-1px); }

    /* ── Delivery type badge ──────────────────────────────── */
    .delivery-badge {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 3px 9px; border-radius: 20px;
        font-size: 11px; font-weight: 700;
    }
    .db-elearning    { background: #fdf4ff; color: #7c3aed; }
    .db-instructor   { background: #f0fdf4; color: #15803d; }
    .db-hybrid       { background: #fff7ed; color: #c2410c; }

    /* ── Tag badge ────────────────────────────────────────── */
    .tag-badge {
        display: inline-block; padding: 3px 9px; border-radius: 20px;
        font-size: 11px; font-weight: 700; background: #f3f4f6; color: #374151;
    }

    /* ── Responsive breakpoints ───────────────────────────── */
    @media (max-width: 768px) {
        .pub-menu, .pub-nav-actions { display: none; }
        .pub-hamburger { display: flex; }
        .pub-topbar { display: none; }
        .pub-section { padding: 48px 0; }
        .section-heading { font-size: 24px; }
    }
    @media (max-width: 480px) {
        .pub-container { padding: 0 16px; }
        .pub-section { padding: 36px 0; }
    }
    </style>

    @stack('head')
</head>
<body>

{{-- ── Top bar ──────────────────────────────────────────────── --}}
<div class="pub-topbar">
    <div class="pub-topbar-inner">
        <div class="pub-topbar-left">
            <span>📧 <a href="mailto:training@smscert.com">training@smscert.com</a></span>
            <span>📞 <a href="tel:+8801XXXXXXXXX">+880 1XX-XXXXXXX</a></span>
        </div>
        <div class="pub-topbar-right">
            <a href="{{ route('public.verify-certificate') }}">🎓 Verify Certificate</a>
            @auth
                <a href="{{ route('participant.my-courses') }}">My Dashboard</a>
            @else
                <a href="{{ route('login') }}">Participant Login</a>
            @endauth
        </div>
    </div>
</div>

{{-- ── Main Navigation ──────────────────────────────────────── --}}
<nav class="pub-nav">
    <div class="pub-nav-inner">

        <a href="{{ route('public.home') }}" class="pub-logo">
            <div class="pub-logo-icon">S</div>
            <div class="pub-logo-text">
                <strong>SMS Training</strong>
                <span>Sustainable Management System</span>
            </div>
        </a>

        <div class="pub-menu">
            <a href="{{ route('public.home') }}"              class="{{ request()->routeIs('public.home') ? 'active' : '' }}">Home</a>
            <a href="{{ route('public.courses') }}"           class="{{ request()->routeIs('public.courses*') ? 'active' : '' }}">Courses</a>
            <a href="{{ route('public.courses') }}?type=eLearning"     class="{{ request()->query('type') === 'eLearning' ? 'active' : '' }}">eLearning</a>
            <a href="{{ route('public.courses') }}?type=Instructor-Led" class="{{ request()->query('type') === 'Instructor-Led' ? 'active' : '' }}">Instructor-Led</a>
            <a href="{{ route('public.calendar') }}"          class="{{ request()->routeIs('public.calendar') ? 'active' : '' }}">Calendar</a>
            <a href="{{ route('public.blog') }}"              class="{{ request()->routeIs('public.blog*') ? 'active' : '' }}">Blog</a>
            <a href="{{ route('public.testimonials') }}"      class="{{ request()->routeIs('public.testimonials') ? 'active' : '' }}">Reviews</a>
        </div>

        <div class="pub-nav-actions">
            @auth
            <a href="{{ route('participant.my-courses') }}" class="pub-btn pub-btn-outline">My Dashboard</a>
            @else
            <a href="{{ route('login') }}" class="pub-btn pub-btn-outline">Login</a>
            @endauth
            <a href="{{ route('public.courses') }}" class="pub-btn pub-btn-solid">Browse Courses</a>
        </div>

        <button class="pub-hamburger" onclick="toggleMobileNav()" aria-label="Menu">
            <span></span><span></span><span></span>
        </button>
    </div>

    {{-- Mobile nav --}}
    <div class="pub-mobile-nav" id="mobileNav">
        <a href="{{ route('public.home') }}">🏠 Home</a>
        <a href="{{ route('public.courses') }}">📚 All Courses</a>
        <a href="{{ route('public.courses') }}?type=eLearning">💻 eLearning</a>
        <a href="{{ route('public.courses') }}?type=Instructor-Led">👨‍🏫 Instructor-Led</a>
        <a href="{{ route('public.calendar') }}">📅 Training Calendar</a>
        <a href="{{ route('public.blog') }}">✍️ Blog</a>
        <a href="{{ route('public.testimonials') }}">⭐ Reviews</a>
        <hr class="mob-divider">
        <a href="{{ route('public.verify-certificate') }}">🎓 Verify Certificate</a>
        @auth
        <a href="{{ route('participant.my-courses') }}">📊 My Dashboard</a>
        @else
        <a href="{{ route('login') }}">🔐 Login</a>
        @endauth
    </div>
</nav>

{{-- ── Flash Messages ────────────────────────────────────────── --}}
@if(session('success') || session('error') || session('info'))
<div class="pub-flash">
    @if(session('success'))
    <div class="pub-alert pub-alert-success">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" style="flex-shrink:0;margin-top:1px;"><polyline points="20 6 9 17 4 12"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="pub-alert pub-alert-error">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:1px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        {{ session('error') }}
    </div>
    @endif
    @if(session('info'))
    <div class="pub-alert pub-alert-info">ℹ️ {{ session('info') }}</div>
    @endif
</div>
@endif

{{-- ── Page Content ─────────────────────────────────────────── --}}
<main class="pub-main">
    @yield('content')
</main>

{{-- ── Footer ───────────────────────────────────────────────── --}}
<footer class="pub-footer">
    <div class="pub-footer-inner">
        <div class="pub-footer-grid">

            {{-- Brand col --}}
            <div class="footer-brand">
                <div class="footer-logo">
                    <div class="footer-logo-icon">S</div>
                    <div class="footer-logo-name">SMS Training Services<br><span style="font-weight:500;font-size:11px;color:rgba(255,255,255,.5);">Sustainable Management System Bangladesh</span></div>
                </div>
                <p>Professional capacity building, compliance training, and certification programs for individuals and organizations across Bangladesh and beyond.</p>
                <div class="footer-social">
                    <a href="#" title="Facebook">f</a>
                    <a href="#" title="LinkedIn">in</a>
                    <a href="#" title="YouTube">▶</a>
                    <a href="#" title="Twitter">𝕏</a>
                </div>
            </div>

            {{-- Quick Links --}}
            <div class="footer-col">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="{{ route('public.courses') }}">All Courses</a></li>
                    <li><a href="{{ route('public.courses') }}?type=eLearning">eLearning</a></li>
                    <li><a href="{{ route('public.courses') }}?type=Instructor-Led">Instructor-Led</a></li>
                    <li><a href="{{ route('public.calendar') }}">Training Calendar</a></li>
                    <li><a href="{{ route('public.blog') }}">Blog</a></li>
                    <li><a href="{{ route('public.testimonials') }}">Testimonials</a></li>
                </ul>
            </div>

            {{-- Resources --}}
            <div class="footer-col">
                <h4>Resources</h4>
                <ul>
                    <li><a href="{{ route('public.verify-certificate') }}">Verify Certificate</a></li>
                    <li><a href="{{ route('login') }}">Participant Login</a></li>
                    <li><a href="#">Corporate Training</a></li>
                    <li><a href="#">FAQs</a></li>
                    <li><a href="#">Refund Policy</a></li>
                    <li><a href="#">Privacy Policy</a></li>
                </ul>
            </div>

            {{-- Contact --}}
            <div class="footer-col">
                <h4>Contact</h4>
                <div class="footer-contact-item">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,12 2,6"/></svg>
                    <a href="mailto:training@smscert.com" style="color:inherit;">training@smscert.com</a>
                </div>
                <div class="footer-contact-item">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13.6 19.79 19.79 0 0 1 1.61 5.17 2 2 0 0 1 3.59 3h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 10.6a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    +880 1XX-XXXXXXX
                </div>
                <div class="footer-contact-item">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    Dhaka, Bangladesh
                </div>
            </div>
        </div>

        <div class="pub-footer-bottom">
            &copy; {{ date('Y') }} SMS Training Services. All rights reserved.
            &nbsp;·&nbsp; <a href="#">Privacy Policy</a>
            &nbsp;·&nbsp; <a href="#">Terms of Use</a>
        </div>
    </div>
</footer>

<script>
function toggleMobileNav() {
    document.getElementById('mobileNav').classList.toggle('open');
}
// Close on outside click
document.addEventListener('click', function(e) {
    const nav = document.getElementById('mobileNav');
    const btn = document.querySelector('.pub-hamburger');
    if (!nav.contains(e.target) && !btn.contains(e.target)) {
        nav.classList.remove('open');
    }
});
</script>

@stack('scripts')
</body>
</html>
