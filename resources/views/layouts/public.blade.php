<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('seo-title', 'SMS Training Academy') | @yield('page-title', 'Professional Training & Certification')</title>
    <meta name="description" content="@yield('seo-desc', 'SMS Training Academy — Professional capacity building, compliance training, and certification programs for individuals and organisations worldwide.')">
    <meta name="keywords"    content="@yield('seo-keys', 'training, certification, capacity building, SMS Training Academy, compliance, ESG, eLearning')">
    <meta property="og:title"       content="@yield('og-title', 'SMS Training Academy')">
    <meta property="og:description" content="@yield('seo-desc', '')">
    <meta property="og:image"       content="@yield('og-image', asset('images/og-default.jpg'))">
    <meta property="og:type"        content="website">
    <link rel="icon" type="image/x-icon"  href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <meta name="theme-color" content="#042C53">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
    /* ══ PUBLIC LAYOUT — SMS Training Academy Design System ══════ */
    *, *::before, *::after { box-sizing: border-box; }
    html { scroll-behavior: smooth; overflow-x: hidden; }
    body {
        margin: 0; padding: 0;
        font-family: 'Inter', system-ui, sans-serif;
        color: #1f2937;
        background: #fff;
        line-height: 1.6;
        -webkit-font-smoothing: antialiased;
        overflow-x: hidden;
    }

    /* ── Design tokens ───────────────────────────────────────────── */
    :root {
        --navy:    #042C53;
        --brand:   #042C53;
        --blue:    #378ADD;
        --radius:  12px;
        --radius-lg: 16px;
        --max-w:   1240px;
        --gap-x:   24px;
    }

    /* ── Main nav ─────────────────────────────────────────────────── */
    .pub-nav {
        background: #fff;
        border-bottom: 1px solid #e9ecf0;
        position: sticky; top: 0; z-index: 100;
        box-shadow: 0 2px 12px rgba(15,23,42,.07);
    }
    .pub-nav-inner {
        max-width: var(--max-w); margin: 0 auto; padding: 0 var(--gap-x);
        display: flex; align-items: center; justify-content: space-between; gap: 20px;
        height: 68px;
    }

    /* Logo */
    .pub-logo {
        display: flex; align-items: center; gap: 12px;
        text-decoration: none; flex-shrink: 0;
    }
    .pub-logo img { flex-shrink: 0; }
    .pub-logo-text strong {
        display: block; font-size: 14.5px; font-weight: 900;
        color: #042C53; line-height: 1.2;
    }
    .pub-logo-text span {
        font-size: 10.5px; font-weight: 500;
        color: #6b7280; margin-top: 1px;
    }
    .pub-logo-sub-full  { display: block; }
    .pub-logo-sub-short { display: none; }

    /* Desktop menu */
    .pub-menu {
        display: flex; align-items: center; gap: 2px; flex: 1; justify-content: center;
    }
    .pub-menu a {
        padding: 7px 11px; border-radius: 8px;
        font-size: 13px; font-weight: 600; color: #374151;
        text-decoration: none; white-space: nowrap;
        transition: color .14s, background .14s;
    }
    .pub-menu a:hover, .pub-menu a.active { color: #042C53; background: #eff6ff; }

    /* Nav actions */
    .pub-nav-actions { display: flex; align-items: center; gap: 8px; flex-shrink: 0; }
    .pub-btn {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 8px 16px; border-radius: var(--radius);
        font-weight: 700; font-size: 13px; font-family: inherit;
        text-decoration: none; cursor: pointer; transition: all .15s; white-space: nowrap;
    }
    .pub-btn-outline {
        border: 1.5px solid #042C53; color: #042C53; background: transparent;
    }
    .pub-btn-outline:hover { background: #eff6ff; }
    .pub-btn-solid {
        background: linear-gradient(135deg, #042C53 0%, #378ADD 100%);
        color: #fff; border: none;
        box-shadow: 0 3px 10px rgba(55,138,221,.28);
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
        padding: 16px var(--gap-x) 20px;
        flex-direction: column; gap: 2px;
    }
    .pub-mobile-nav.open { display: flex; }
    .pub-mobile-nav a {
        padding: 10px 14px; border-radius: 9px;
        font-size: 14.5px; font-weight: 600; color: #374151;
        text-decoration: none; display: block;
    }
    .pub-mobile-nav a:hover { background: #f3f4f6; color: #042C53; }
    .pub-mobile-nav .mob-divider { border: none; border-top: 1px solid #f0f2f5; margin: 8px 0; }
    .pub-mobile-nav .mob-cta {
        background: linear-gradient(135deg,#042C53,#378ADD);
        color: #fff !important; border-radius: 10px; margin-top: 4px;
    }

    /* ── Main content area ──────────────────────────────────────── */
    main.pub-main { min-height: 60vh; }

    /* ── Flash messages ─────────────────────────────────────────── */
    .pub-flash {
        max-width: var(--max-w); margin: 16px auto; padding: 0 var(--gap-x);
    }
    .pub-alert {
        padding: 13px 18px; border-radius: 10px; font-weight: 600; font-size: 14px;
        display: flex; align-items: flex-start; gap: 10px;
        margin-bottom: 12px;
    }
    .pub-alert-success { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
    .pub-alert-error   { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
    .pub-alert-info    { background: #eff6ff; color: #1a5f9e; border: 1px solid #bfdbfe; }

    /* ── Footer ─────────────────────────────────────────────────── */
    .pub-footer {
        background: #0f172a;
        color: rgba(255,255,255,.72);
        padding: 64px 0 0;
        margin-top: 80px;
    }
    .pub-footer-inner {
        max-width: var(--max-w); margin: 0 auto; padding: 0 var(--gap-x);
    }
    .pub-footer-grid {
        display: grid;
        grid-template-columns: 2.2fr 1fr 1fr 1.3fr;
        gap: 48px;
        padding-bottom: 52px;
        border-bottom: 1px solid rgba(255,255,255,.08);
    }
    @media (max-width: 980px)  { .pub-footer-grid { grid-template-columns: 1fr 1fr; gap: 36px; } }
    @media (max-width: 560px)  { .pub-footer-grid { grid-template-columns: 1fr; gap: 28px; } }

    .footer-brand .footer-logo {
        display: flex; align-items: center; gap: 14px; margin-bottom: 16px;
    }
    .footer-brand .footer-logo img { flex-shrink: 0; }
    .footer-brand .footer-logo-text strong {
        display: block; font-size: 14.5px; font-weight: 900; color: #fff; line-height: 1.2;
    }
    .footer-brand .footer-logo-text span {
        display: block; font-size: 10.5px; color: rgba(255,255,255,.45); font-weight: 500; margin-top: 2px;
    }
    .footer-brand p {
        font-size: 13.5px; line-height: 1.75; margin: 0 0 20px;
        color: rgba(255,255,255,.58);
    }
    .footer-social { display: flex; gap: 8px; flex-wrap: wrap; }
    .footer-social a {
        width: 34px; height: 34px; border-radius: 8px;
        background: rgba(255,255,255,.07);
        display: flex; align-items: center; justify-content: center;
        color: rgba(255,255,255,.6); text-decoration: none;
        transition: background .14s, color .14s;
    }
    .footer-social a:hover { background: rgba(255,255,255,.16); color: #fff; }

    .footer-col h4 {
        font-size: 11.5px; font-weight: 800; color: #fff;
        text-transform: uppercase; letter-spacing: .7px;
        margin: 0 0 18px;
    }
    .footer-col ul { list-style: none; margin: 0; padding: 0; }
    .footer-col li { margin-bottom: 9px; }
    .footer-col a {
        color: rgba(255,255,255,.58); font-size: 13.5px;
        text-decoration: none; transition: color .14s;
    }
    .footer-col a:hover { color: #fff; }

    .footer-contact-item {
        display: flex; align-items: flex-start; gap: 9px;
        font-size: 13.5px; margin-bottom: 11px; color: rgba(255,255,255,.58);
        line-height: 1.5;
    }
    .footer-contact-item svg { flex-shrink: 0; margin-top: 2px; opacity: .65; }
    .footer-contact-item a { color: rgba(255,255,255,.7); text-decoration: none; }
    .footer-contact-item a:hover { color: #fff; }

    .pub-footer-bottom {
        padding: 20px 0;
        display: flex; align-items: center; justify-content: space-between;
        flex-wrap: wrap; gap: 10px;
        font-size: 12.5px; color: rgba(255,255,255,.35);
    }
    .pub-footer-bottom a { color: rgba(255,255,255,.45); text-decoration: none; transition: color .14s; }
    .pub-footer-bottom a:hover { color: rgba(255,255,255,.75); }
    .pub-footer-bottom-links { display: flex; gap: 18px; }

    /* ── Shared layout helpers ───────────────────────────────────── */
    .pub-container { max-width: var(--max-w); margin: 0 auto; padding: 0 var(--gap-x); }
    .pub-section    { padding: 64px 0; }
    .pub-section-sm { padding: 40px 0; }

    /* ── Course grid — 3 col → 2 col → 1 col ────────────────────── */
    .courses-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 22px;
    }
    @media (max-width: 1024px) { .courses-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 580px)  { .courses-grid { grid-template-columns: 1fr; } }

    /* Section header row */
    .section-header { margin-bottom: 32px; }
    .section-header-row {
        display: flex; align-items: flex-end; justify-content: space-between;
        flex-wrap: wrap; gap: 12px;
    }
    .view-all-link {
        font-size: 13.5px; font-weight: 700; color: #042C53; text-decoration: none;
        display: inline-flex; align-items: center; gap: 5px;
    }
    .view-all-link:hover { color: #378ADD; }

    .section-eyebrow {
        font-size: 11.5px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;
        color: #378ADD; margin-bottom: 10px;
    }
    .section-heading {
        font-size: 30px; font-weight: 900; color: #0f172a; margin: 0 0 6px; line-height: 1.25;
    }
    .section-subheading {
        font-size: 16px; color: #6b7280; margin: 0 0 40px; line-height: 1.6;
    }

    /* ── Course card ─────────────────────────────────────────────── */
    .course-card {
        background: #fff; border: 1px solid #e9ecf0; border-radius: var(--radius-lg);
        overflow: hidden; display: flex; flex-direction: column;
        transition: box-shadow .2s, transform .2s;
        box-shadow: 0 2px 8px rgba(15,23,42,.06);
    }
    .course-card:hover {
        box-shadow: 0 10px 32px rgba(15,23,42,.12);
        transform: translateY(-3px);
    }
    .course-card-img {
        width: 100%; height: 176px; overflow: hidden;
        display: flex; align-items: center; justify-content: center;
        background: #f0f4ff; flex-shrink: 0;
    }
    .course-card-img img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .course-card-body { padding: 18px 20px; flex: 1; display: flex; flex-direction: column; gap: 8px; }
    .course-card-category {
        font-size: 10.5px; font-weight: 800; text-transform: uppercase; letter-spacing: .5px;
        color: #378ADD; background: #eff6ff; padding: 3px 9px; border-radius: 20px;
        display: inline-block;
    }
    .course-card-title {
        font-size: 15px; font-weight: 800; color: #111827;
        line-height: 1.35; margin: 0;
        text-decoration: none; display: block;
    }
    .course-card-title:hover { color: #042C53; }
    .course-card-desc {
        font-size: 13px; color: #6b7280; line-height: 1.6;
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
        margin: 0;
    }
    .course-card-meta {
        display: flex; align-items: center; gap: 12px;
        flex-wrap: wrap; margin-top: 2px;
    }
    .course-card-meta-item {
        display: inline-flex; align-items: center; gap: 5px;
        font-size: 12px; color: #6b7280; font-weight: 500;
    }
    .course-card-footer {
        padding: 13px 20px;
        border-top: 1px solid #f0f2f5;
        display: flex; align-items: center; justify-content: space-between; gap: 10px;
    }
    .course-price { font-size: 15.5px; font-weight: 900; color: #042C53; }
    .course-price small { font-size: 11px; color: #9ca3af; font-weight: 500; display: block; line-height: 1.3; }

    /* ── Primary / secondary action buttons ─────────────────────── */
    .pub-btn-primary {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 9px 18px; border-radius: var(--radius);
        background: linear-gradient(135deg, #042C53, #378ADD); color: #fff;
        font-size: 13px; font-weight: 700; text-decoration: none;
        border: none; cursor: pointer; font-family: inherit;
        box-shadow: 0 3px 10px rgba(55,138,221,.25);
        transition: opacity .14s, transform .14s;
    }
    .pub-btn-primary:hover { opacity: .9; transform: translateY(-1px); }
    .pub-enroll-btn { /* alias */ }
    .pub-enroll-btn,
    a.pub-enroll-btn {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 8px 16px; border-radius: var(--radius);
        background: #042C53; color: #fff;
        font-size: 13px; font-weight: 700; text-decoration: none;
        transition: background .14s, transform .1s;
    }
    .pub-enroll-btn:hover { background: #0a4278; transform: translateY(-1px); }

    /* ── Delivery type badge ─────────────────────────────────────── */
    .delivery-badge {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 3px 9px; border-radius: 20px;
        font-size: 11px; font-weight: 700;
    }
    .db-elearning  { background: #fdf4ff; color: #7c3aed; }
    .db-instructor { background: #f0fdf4; color: #15803d; }
    .db-hybrid     { background: #fff7ed; color: #c2410c; }

    .tag-badge {
        display: inline-block; padding: 3px 9px; border-radius: 20px;
        font-size: 11px; font-weight: 700; background: #f3f4f6; color: #374151;
    }

    /* ── Schedule mode badges ────────────────────────────────────── */
    .sc-mode-badge { padding: 2px 8px; border-radius: 20px; font-size: 11px; font-weight: 700; }
    .scm-physical { background: #f0fdf4; color: #15803d; }
    .scm-online   { background: #eff6ff; color: #1a5f9e; }
    .scm-hybrid   { background: #fff7ed; color: #c2410c; }

    /* ── Responsive ─────────────────────────────────────────────── */
    @media (max-width: 768px) {
        .pub-menu, .pub-nav-actions { display: none; }
        .pub-hamburger { display: flex; flex-shrink: 0; margin-left: auto; }
        .pub-section { padding: 48px 0; }
        .section-heading { font-size: 24px; }
        .pub-footer { margin-top: 56px; }
        .pub-logo { min-width: 0; flex-shrink: 1; }
        .pub-logo-sub-full  { display: none; }
        .pub-logo-sub-short { display: block; }
    }
    @media (max-width: 480px) {
        .pub-container { padding: 0 16px; }
        .pub-section { padding: 36px 0; }
    }

    /* ── Override Tailwind forms-plugin focus defaults (brand colours) ── */
    input:focus, input:focus-visible,
    select:focus, select:focus-visible,
    textarea:focus, textarea:focus-visible {
        --tw-ring-color: rgba(55,138,221,.3) !important;
        border-color: #378ADD !important;
        box-shadow: 0 0 0 3px rgba(55,138,221,.18) !important;
        outline: none !important;
    }
    </style>

    @stack('head')
</head>
<body>

{{-- ── Main Navigation ──────────────────────────────────────────── --}}
<nav class="pub-nav">
    <div class="pub-nav-inner">

        <a href="{{ route('public.home') }}" class="pub-logo">
            <img src="{{ asset('sms-logo.png') }}" alt="SMS Training Academy" style="height:42px;width:auto;display:block;">
            <div class="pub-logo-text">
                <strong>SMS Training Academy</strong>
                <span class="pub-logo-sub-full">Powered by Sustainable Management System Inc.</span>
                <span class="pub-logo-sub-short">Powered by SMS Inc.</span>
            </div>
        </a>

        <div class="pub-menu">
            <a href="{{ route('public.home') }}"               class="{{ request()->routeIs('public.home') ? 'active' : '' }}">Home</a>
            <a href="{{ route('public.courses') }}"            class="{{ request()->routeIs('public.courses') && !request('type') ? 'active' : '' }}">Courses</a>
            <a href="{{ route('public.courses') }}?type=eLearning"      class="{{ request()->routeIs('public.courses') && request('type') === 'eLearning' ? 'active' : '' }}">eLearning</a>
            <a href="{{ route('public.courses') }}?type=Instructor-Led" class="{{ request()->routeIs('public.courses') && request('type') === 'Instructor-Led' ? 'active' : '' }}">Instructor-Led</a>
            <a href="{{ route('public.calendar') }}"           class="{{ request()->routeIs('public.calendar') ? 'active' : '' }}">Calendar</a>
            <a href="{{ route('public.testimonials') }}"       class="{{ request()->routeIs('public.testimonials') ? 'active' : '' }}">Reviews</a>
            <a href="{{ route('public.blog') }}"               class="{{ request()->routeIs('public.blog*') ? 'active' : '' }}">Blog</a>
            <a href="{{ route('public.verify-certificate') }}" class="{{ request()->routeIs('public.verify-certificate') ? 'active' : '' }}">Verify</a>
            <a href="{{ route('public.about') }}"              class="{{ request()->routeIs('public.about') ? 'active' : '' }}">About</a>
            <a href="{{ route('public.contact') }}"            class="{{ request()->routeIs('public.contact*') ? 'active' : '' }}">Contact</a>
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
        <a href="{{ route('public.home') }}">Home</a>
        <a href="{{ route('public.courses') }}">All Courses</a>
        <a href="{{ route('public.courses') }}?type=eLearning">eLearning</a>
        <a href="{{ route('public.courses') }}?type=Instructor-Led">Instructor-Led</a>
        <a href="{{ route('public.calendar') }}">Training Calendar</a>
        <a href="{{ route('public.testimonials') }}">Reviews</a>
        <a href="{{ route('public.blog') }}">Blog</a>
        <a href="{{ route('public.verify-certificate') }}">Verify Certificate</a>
        <a href="{{ route('public.about') }}">About Us</a>
        <a href="{{ route('public.contact') }}">Contact Us</a>
        <hr class="mob-divider">
        @auth
        <a href="{{ route('participant.my-courses') }}" class="mob-cta">My Dashboard</a>
        @else
        <a href="{{ route('login') }}" class="mob-cta">Login / My Account</a>
        @endauth
    </div>
</nav>

{{-- ── Flash Messages ───────────────────────────────────────────── --}}
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
    <div class="pub-alert pub-alert-info">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:1px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        {{ session('info') }}
    </div>
    @endif
</div>
@endif

{{-- ── Page Content ─────────────────────────────────────────────── --}}
<main class="pub-main">
    @yield('content')
</main>

{{-- ── Footer ───────────────────────────────────────────────────── --}}
<footer class="pub-footer">
    <div class="pub-footer-inner">
        <div class="pub-footer-grid">

            {{-- Brand col --}}
            <div class="footer-brand">
                <div class="footer-logo">
                    <img src="{{ asset('sms-logo.png') }}" alt="SMS Training Academy" style="height:46px;width:auto;display:block;filter:brightness(0) invert(1);opacity:.9;">
                    <div class="footer-logo-text">
                        <strong>SMS Training Academy</strong>
                        <span>Powered by Sustainable Management System Inc.</span>
                    </div>
                </div>
                <p>Professional capacity building, compliance training, and internationally recognised certification programs for individuals and organisations worldwide.</p>
                <div class="footer-social">
                    <a href="https://www.linkedin.com/company/sustainable-management-system-sms" target="_blank" rel="noopener" title="LinkedIn">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6zM2 9h4v12H2z"/><circle cx="4" cy="4" r="2"/></svg>
                    </a>
                    <a href="https://www.facebook.com/smstrainingservices" target="_blank" rel="noopener" title="Facebook">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                    </a>
                    <a href="https://www.youtube.com/@sms-training" target="_blank" rel="noopener" title="YouTube">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46a2.78 2.78 0 0 0-1.95 1.96A29 29 0 0 0 1 12a29 29 0 0 0 .46 5.58A2.78 2.78 0 0 0 3.41 19.6C5.12 20 12 20 12 20s6.88 0 8.59-.46a2.78 2.78 0 0 0 1.95-1.95A29 29 0 0 0 23 12a29 29 0 0 0-.46-5.58z"/><polygon points="9.75 15.02 15.5 12 9.75 8.98 9.75 15.02" fill="#0f172a"/></svg>
                    </a>
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
                    <li><a href="{{ route('public.testimonials') }}">Reviews</a></li>
                    <li><a href="{{ route('public.blog') }}">Blog</a></li>
                </ul>
            </div>

            {{-- Resources --}}
            <div class="footer-col">
                <h4>Resources</h4>
                <ul>
                    <li><a href="{{ route('public.about') }}">About Us</a></li>
                    <li><a href="{{ route('public.contact') }}">Contact Us</a></li>
                    @auth
                    <li><a href="{{ route('participant.my-courses') }}">My Dashboard</a></li>
                    @else
                    <li><a href="{{ route('login') }}">Participant Login</a></li>
                    @endauth
                </ul>
            </div>

            {{-- Contact --}}
            <div class="footer-col">
                <h4>Contact Us</h4>
                <div class="footer-contact-item">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,12 2,6"/></svg>
                    <div>
                        <a href="mailto:training@smscert.com">training@smscert.com</a><br>
                        <a href="mailto:info@smscert.com">info@smscert.com</a>
                    </div>
                </div>
                <div class="footer-contact-item">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    <div>
                        277 Cherry Street, Suite-12N<br>
                        New York, NY, USA
                    </div>
                </div>
            </div>
        </div>

        <div class="pub-footer-bottom">
            <span>&copy; {{ date('Y') }} SMS Training Academy. All rights reserved. Powered by Sustainable Management System Inc.</span>
            <div class="pub-footer-bottom-links">
                <a href="{{ route('public.privacy') }}">Privacy Policy</a>
                <a href="{{ route('public.terms') }}">Terms of Use</a>
                <a href="{{ route('public.refund') }}">Refund Policy</a>
                <a href="{{ route('public.verify-certificate') }}">Verify Certificate</a>
            </div>
        </div>
    </div>
</footer>

<script>
function toggleMobileNav() {
    document.getElementById('mobileNav').classList.toggle('open');
}
document.addEventListener('click', function(e) {
    const nav = document.getElementById('mobileNav');
    const btn = document.querySelector('.pub-hamburger');
    if (nav && btn && !nav.contains(e.target) && !btn.contains(e.target)) {
        nav.classList.remove('open');
    }
});
</script>

@stack('scripts')
</body>
</html>
