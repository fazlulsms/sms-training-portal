<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMS Training Panel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        *, *::before, *::after { box-sizing: border-box; }
        html, body { margin:0; padding:0; width:100%; height:100%; background:#f0f2f8; font-family:'Inter',Arial,sans-serif; color:#111827; }

        /* ══ SIDEBAR ══════════════════════════════════════ */
        .sidebar {
            width: 268px; height: 100vh;
            background: #0f1e45;
            color: white;
            padding: 0 0 20px;
            position: fixed; left: 0; top: 0;
            overflow-y: auto;
            box-shadow: 4px 0 24px rgba(10,16,40,0.22);
            z-index: 1002;
            display: flex; flex-direction: column;
        }
        .sidebar::-webkit-scrollbar { width: 4px; }
        .sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.10); border-radius:20px; }

        /* brand */
        .sb-brand {
            display: flex; align-items: center; gap: 11px;
            padding: 18px 16px 16px;
            border-bottom: 1px solid rgba(255,255,255,0.07);
            margin-bottom: 6px; flex-shrink: 0;
        }
        .sb-logo { width:42px; height:42px; object-fit:contain; background:white; padding:6px; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.25); flex-shrink:0; }
        .sb-brand-name  { font-size:16px; font-weight:800; color:#fff; line-height:1.1; }
        .sb-brand-sub   { font-size:9.5px; font-weight:700; color:#60a5fa; text-transform:uppercase; letter-spacing:1.2px; margin-top:2px; }

        /* nav core */
        .sb-nav { flex: 1; padding: 4px 10px 8px; }

        /* standalone item */
        .sb-item {
            display: flex; align-items: center; gap: 9px;
            color: #94a3b8; text-decoration: none;
            padding: 9px 12px; margin-bottom: 1px;
            font-size: 13px; font-weight: 600;
            border-radius: 9px;
            transition: background .15s, color .15s;
            position: relative;
        }
        .sb-item:hover { background: rgba(255,255,255,0.07); color: #e2e8f0; }
        .sb-item.active {
            background: rgba(96,165,250,0.15);
            color: #fff;
        }
        .sb-item.active::before {
            content: ''; position: absolute;
            left: 0; top: 20%; bottom: 20%;
            width: 3px; border-radius: 0 3px 3px 0;
            background: #60a5fa;
        }
        .sb-icon { width:18px; min-width:18px; display:flex; align-items:center; justify-content:center; opacity:.8; }
        .sb-item.active .sb-icon { opacity: 1; }

        /* group header (collapsible trigger) */
        .sb-group { margin-top: 6px; }

        .sb-group-header {
            display: flex; align-items: center; justify-content: space-between;
            padding: 9px 12px 8px;
            cursor: pointer; user-select: none; border-radius: 9px;
            transition: background .15s;
        }
        .sb-group-header:hover { background: rgba(255,255,255,0.05); }

        .sb-group-label {
            display: flex; align-items: center; gap: 8px;
            font-size: 11px; font-weight: 800;
            text-transform: uppercase; letter-spacing: 1px;
        }
        /* eLearning group — blue accent */
        .sg-el .sb-group-label { color: #60a5fa; }
        .sg-el .sb-group-pill  { background: rgba(96,165,250,0.15); color: #93c5fd; }
        /* Instructor-Led group — amber accent */
        .sg-il .sb-group-label { color: #fbbf24; }
        .sg-il .sb-group-pill  { background: rgba(251,191,36,0.12); color: #fcd34d; }
        /* Reports group — emerald accent */
        .sg-reports .sb-group-label { color: #34d399; }
        /* Corporate group — indigo accent */
        .sg-corporate .sb-group-label { color: #818cf8; }
        /* Admin group */
        .sg-admin .sb-group-label { color: #94a3b8; }

        .sb-group-pill { font-size:9px; font-weight:800; padding:2px 7px; border-radius:10px; }

        .sb-chevron { width:14px; height:14px; transition: transform .2s; opacity:.6; }
        .sb-chevron.open { transform: rotate(90deg); }

        /* group items wrapper */
        .sb-group-items {
            padding: 2px 0 4px;
            overflow: hidden;
        }

        /* sub-item (inside group) */
        .sb-sub {
            display: flex; align-items: center; gap: 9px;
            color: #94a3b8; text-decoration: none;
            padding: 8px 12px 8px 32px;
            margin-bottom: 1px;
            font-size: 12.5px; font-weight: 600;
            border-radius: 8px;
            transition: background .15s, color .15s;
            position: relative;
        }
        .sb-sub:hover { background: rgba(255,255,255,0.06); color: #e2e8f0; }
        /* eLearning active sub */
        .sg-el .sb-sub.active { background: rgba(96,165,250,0.12); color: #93c5fd; }
        .sg-el .sb-sub.active::before { content:''; position:absolute; left:0; top:22%; bottom:22%; width:3px; border-radius:0 3px 3px 0; background:#60a5fa; }
        /* IL active sub */
        .sg-il .sb-sub.active { background: rgba(251,191,36,0.10); color: #fcd34d; }
        .sg-il .sb-sub.active::before { content:''; position:absolute; left:0; top:22%; bottom:22%; width:3px; border-radius:0 3px 3px 0; background:#fbbf24; }
        /* Admin active sub */
        .sg-admin .sb-sub.active { background: rgba(148,163,184,0.10); color: #e2e8f0; }
        .sg-admin .sb-sub.active::before { content:''; position:absolute; left:0; top:22%; bottom:22%; width:3px; border-radius:0 3px 3px 0; background:#94a3b8; }

        /* divider */
        .sb-divider { height:1px; background:rgba(255,255,255,0.06); margin:8px 12px; }

        /* footer */
        .sb-footer { border-top:1px solid rgba(255,255,255,0.07); padding:12px 10px 8px; flex-shrink:0; }
        .sb-user { display:flex; align-items:center; gap:10px; padding:8px 10px; border-radius:10px; margin-bottom:4px; }
        .sb-avatar { width:32px; height:32px; background:rgba(255,255,255,0.12); border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:800; color:#fff; flex-shrink:0; }
        .sb-user-name { font-size:12.5px; font-weight:700; color:#fff; }
        .sb-user-email { font-size:11px; color:#64748b; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:148px; }
        .sb-logout { display:flex; align-items:center; gap:9px; color:#fca5a5; text-decoration:none; padding:8px 10px; border-radius:9px; font-size:13px; font-weight:600; transition:background .15s; width:100%; background:none; border:none; cursor:pointer; text-align:left; font-family:inherit; }
        .sb-logout:hover { background:rgba(239,68,68,0.10); color:#fca5a5; }

        /* ══ TOPBAR ════════════════════════════════════════ */
        .topbar { position:fixed; top:0; left:268px; right:0; height:58px; background:#fff; border-bottom:1px solid #e5e7eb; display:flex; align-items:center; justify-content:space-between; padding:0 26px; z-index:900; box-shadow:0 1px 6px rgba(15,23,42,.05); }
        .topbar-title { font-size:16px; font-weight:700; color:#111827; }
        .topbar-right { display:flex; align-items:center; gap:14px; }
        .topbar-user { display:flex; align-items:center; gap:8px; padding:5px 12px; border-radius:8px; background:#f3f4f6; font-size:13px; font-weight:600; color:#374151; }
        .topbar-avatar { width:28px; height:28px; background:#1e3a8a; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:700; color:#fff; }

        /* ══ LAYOUT ════════════════════════════════════════ */
        .layout { display:flex; width:100vw; height:100vh; overflow:hidden; }
        .content { margin-left:268px; width:calc(100vw - 268px); height:100vh; padding:80px 28px 28px; overflow-y:auto; overflow-x:hidden; }

        /* ══ MOBILE ════════════════════════════════════════ */
        .mobile-topbar { display:none; }
        #sidebar-toggle { display:none; }
        .overlay { display:none; }

        @media (max-width: 992px) {
            .topbar { display:none; }
            .mobile-topbar {
                display:flex; align-items:center; justify-content:space-between;
                position:fixed; top:0; left:0; right:0; height:58px;
                background:#0f1e45; color:white;
                padding:0 16px; z-index:1003;
                box-shadow:0 4px 12px rgba(0,0,0,0.20);
            }
            .mobile-brand { display:flex; align-items:center; gap:10px; font-size:16px; font-weight:800; }
            .mobile-brand img { width:30px; height:30px; object-fit:contain; background:white; padding:4px; border-radius:7px; }
            .toggle-btn { background:rgba(255,255,255,0.12); color:white; border:none; border-radius:8px; width:38px; height:38px; cursor:pointer; display:flex; align-items:center; justify-content:center; }
            .sidebar { transform:translateX(-100%); transition:transform .25s ease; }
            #sidebar-toggle:checked ~ .layout .sidebar { transform:translateX(0); }
            .overlay { position:fixed; inset:0; background:rgba(0,0,0,0.45); z-index:1001; }
            #sidebar-toggle:checked ~ .overlay { display:block; }
            .content { margin-left:0; width:100vw; padding:72px 16px 20px; }
        }
    </style>
</head>
<body>

<input type="checkbox" id="sidebar-toggle">

<div class="mobile-topbar">
    <div class="mobile-brand">
        <img src="{{ asset('sms-logo.png') }}" alt="SMS Logo">
        <span>SMS Panel</span>
    </div>
    <label for="sidebar-toggle" class="toggle-btn">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
    </label>
</div>

<label for="sidebar-toggle" class="overlay"></label>

<div class="layout">
<aside class="sidebar">

    <div class="sb-brand">
        <img src="{{ asset('sms-logo.png') }}" alt="SMS Logo" class="sb-logo">
        <div>
            <div class="sb-brand-name">SMS Panel</div>
            <div class="sb-brand-sub">Training Management</div>
        </div>
    </div>

    <nav class="sb-nav">

    {{-- ════════════════════════════════════════
         TRAINER NAVIGATION
    ════════════════════════════════════════ --}}
    @if(Auth::check() && Auth::user()->isTrainer())

        <a href="{{ route('trainer.dashboard') }}"
           class="sb-item {{ request()->is('trainer/dashboard') ? 'active' : '' }}">
            <span class="sb-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/></svg></span>
            Dashboard
        </a>

        <div class="sb-divider"></div>
        <div class="sb-group sg-il" x-data="{ open: {{ request()->is('trainer/schedules*') || request()->is('trainer/enrollments*') ? 'true' : 'false' }} }">
            <div class="sb-group-header" @click="open = !open">
                <div class="sb-group-label">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    Instructor-Led
                </div>
                <svg class="sb-chevron" :class="{ open }" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
            </div>
            <div class="sb-group-items" x-show="open" x-transition>
                <a href="{{ route('trainer.schedules') }}"
                   class="sb-sub {{ request()->is('trainer/schedules*') ? 'active' : '' }}">
                    <span class="sb-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></span>
                    My Schedules
                </a>
            </div>
        </div>

        <div class="sb-divider"></div>

        <a href="{{ route('participant.my-courses') }}"
           class="sb-item {{ request()->is('my-courses*') || request()->is('my-elearning*') ? 'active' : '' }}">
            <span class="sb-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg></span>
            My Courses
        </a>

        <a href="{{ route('profile.edit') }}"
           class="sb-item {{ request()->is('profile*') ? 'active' : '' }}">
            <span class="sb-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span>
            My Profile
        </a>

    {{-- ════════════════════════════════════════
         PARTICIPANT NAVIGATION
    ════════════════════════════════════════ --}}
    @elseif(Auth::check() && Auth::user()->isParticipant())

        <a href="{{ route('participant.my-courses') }}"
           class="sb-item {{ request()->is('my-courses*') || request()->is('my-elearning*') ? 'active' : '' }}">
            <span class="sb-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5-10-5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg></span>
            My Learning
        </a>

        <a href="{{ route('participant.my-certificates') }}"
           class="sb-item {{ request()->is('my-certificates*') ? 'active' : '' }}">
            <span class="sb-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg></span>
            My Certificates
        </a>

        <a href="{{ route('profile.edit') }}"
           class="sb-item {{ request()->is('profile*') ? 'active' : '' }}">
            <span class="sb-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span>
            My Profile
        </a>

    {{-- ════════════════════════════════════════
         ADMIN NAVIGATION
    ════════════════════════════════════════ --}}
    @else

        {{-- A. Dashboard --}}
        <a href="/dashboard"
           class="sb-item {{ request()->is('dashboard') ? 'active' : '' }}">
            <span class="sb-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/></svg></span>
            Dashboard
        </a>

        <div class="sb-divider"></div>

        {{-- B. E-Learning / Self-Paced ─ Blue ─────────── --}}
        @php
            $elActive = request()->is('elearning*');
        @endphp
        <div class="sb-group sg-el"
             x-data="{ open: {{ $elActive ? 'true' : 'false' }} }">

            <div class="sb-group-header" @click="open = !open">
                <div class="sb-group-label">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 10v6M2 10l10-5 10 5-10 5-10-5z"/></svg>
                    E-Learning
                    <span class="sb-group-pill">Self-Paced</span>
                </div>
                <svg class="sb-chevron" :class="{ open }" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
            </div>

            <div class="sb-group-items" x-show="open" x-transition>

                <a href="{{ route('elearning.courses.index') }}"
                   class="sb-sub {{ request()->is('elearning/courses*') ? 'active' : '' }}">
                    <span class="sb-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg></span>
                    Courses
                </a>

                {{-- Lessons, Quizzes, Resources are nested under a course — link to courses index --}}
                <a href="{{ route('elearning.courses.index') }}"
                   class="sb-sub {{ request()->is('elearning/*/lessons*') ? 'active' : '' }}">
                    <span class="sb-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2"/></svg></span>
                    Lessons
                </a>

                <a href="{{ route('elearning.courses.index') }}"
                   class="sb-sub {{ request()->is('elearning/*/resources*') ? 'active' : '' }}">
                    <span class="sb-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg></span>
                    Resources
                </a>

                <a href="{{ route('elearning.courses.index') }}"
                   class="sb-sub {{ request()->is('elearning/*/quizzes*') ? 'active' : '' }}">
                    <span class="sb-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg></span>
                    Quizzes
                </a>

                <a href="{{ route('elearning.courses.index') }}"
                   class="sb-sub {{ request()->is('elearning/*/questions*') ? 'active' : '' }}">
                    <span class="sb-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/></svg></span>
                    Question Bank
                </a>

                <a href="{{ route('elearning.enrollments.index') }}"
                   class="sb-sub {{ request()->is('elearning/enrollments*') ? 'active' : '' }}">
                    <span class="sb-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></span>
                    Enrollments
                </a>

                {{-- Certificates: filter enrollments by certificate status --}}
                <a href="{{ route('elearning.enrollments.index') }}?certificate=eligible"
                   class="sb-sub">
                    <span class="sb-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg></span>
                    Certificates
                </a>

                {{-- Reports: TODO — route does not exist yet --}}
                {{-- <a href="/admin/reports/elearning" class="sb-sub">Reports</a> --}}

            </div>
        </div>

        <div class="sb-divider"></div>

        {{-- C. Instructor-Led / Manual ─ Amber ──────────── --}}
        @php
            $ilActive = request()->is('admin/courses*') || request()->is('admin/trainers*')
                     || request()->is('admin/training-schedules*') || request()->is('enrollments*')
                     || request()->is('admin/invoices*') || request()->is('admin/certificates*')
                     || request()->is('admin/attendance*');
        @endphp
        <div class="sb-group sg-il"
             x-data="{ open: {{ $ilActive ? 'true' : 'false' }} }">

            <div class="sb-group-header" @click="open = !open">
                <div class="sb-group-label">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    Instructor-Led
                    <span class="sb-group-pill">Manual</span>
                </div>
                <svg class="sb-chevron" :class="{ open }" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
            </div>

            <div class="sb-group-items" x-show="open" x-transition>

                <a href="/admin/courses"
                   class="sb-sub {{ request()->is('admin/courses*') ? 'active' : '' }}">
                    <span class="sb-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg></span>
                    Courses
                </a>

                <a href="/admin/trainers"
                   class="sb-sub {{ request()->is('admin/trainers*') ? 'active' : '' }}">
                    <span class="sb-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span>
                    Trainers
                </a>

                <a href="/admin/training-schedules"
                   class="sb-sub {{ request()->is('admin/training-schedules*') ? 'active' : '' }}">
                    <span class="sb-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></span>
                    Training Schedule
                </a>

                <a href="/admin/enrollments"
                   class="sb-sub {{ (request()->is('admin/enrollments') || request()->is('enrollments?*') || request()->is('admin/enrollments/*')) && !request()->is('elearning*') ? 'active' : '' }}">
                    <span class="sb-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg></span>
                    Enrollments
                </a>

                <a href="/admin/attendance/{{ optional(\App\Models\TrainingSchedule::whereDate('start_date','>=',now())->orderBy('start_date')->first())->id ?? 0 }}"
                   class="sb-sub {{ request()->is('admin/attendance*') ? 'active' : '' }}">
                    <span class="sb-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg></span>
                    Attendance
                </a>

                <a href="/admin/invoices"
                   class="sb-sub {{ request()->is('admin/invoices*') ? 'active' : '' }}">
                    <span class="sb-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg></span>
                    Invoices
                </a>

                <a href="/admin/certificates"
                   class="sb-sub {{ request()->is('admin/certificates*') ? 'active' : '' }}">
                    <span class="sb-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg></span>
                    Certificates
                </a>

                {{-- Reports: TODO — routes not yet implemented --}}
                {{-- <a href="/admin/reports/training" class="sb-sub">Reports</a> --}}

            </div>
        </div>

        <div class="sb-divider"></div>

        {{-- C2. Corporate Training ───────────────────────── --}}
        @php
            $corpActive = request()->is('corporate*');
        @endphp
        <div class="sb-group sg-corporate"
             x-data="{ open: {{ $corpActive ? 'true' : 'false' }} }">

            <div class="sb-group-header" @click="open = !open">
                <div class="sb-group-label">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/><line x1="12" y1="12" x2="12" y2="16"/><line x1="10" y1="14" x2="14" y2="14"/></svg>
                    Corporate Training
                </div>
                <svg class="sb-chevron" :class="{ open }" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
            </div>

            <div class="sb-group-items" x-show="open" x-transition>

                <a href="{{ route('corporate.projects.index') }}"
                   class="sb-sub {{ request()->is('corporate/projects*') ? 'active' : '' }}">
                    <span class="sb-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></span>
                    Projects
                </a>

                <a href="{{ route('corporate.sessions.index') }}"
                   class="sb-sub {{ request()->is('corporate/sessions*') ? 'active' : '' }}">
                    <span class="sb-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></span>
                    Training Sessions
                </a>

            </div>
        </div>

        <div class="sb-divider"></div>

        {{-- C3. Reports & Analytics ─────────────────────── --}}
        @php $rptActive = request()->is('reports*'); @endphp
        <div class="sb-group sg-reports"
             x-data="{ open: {{ $rptActive ? 'true' : 'false' }} }">

            <div class="sb-group-header" @click="open = !open">
                <div class="sb-group-label">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                    Reports & Analytics
                </div>
                <svg class="sb-chevron" :class="{ open }" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
            </div>

            <div class="sb-group-items" x-show="open" x-transition>
                <a href="{{ route('reports.index') }}"
                   class="sb-sub {{ request()->is('reports') ? 'active' : '' }}">
                    <span class="sb-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg></span>
                    Overview
                </a>
                <a href="{{ route('reports.elearning') }}"
                   class="sb-sub {{ request()->is('reports/elearning*') ? 'active' : '' }}">
                    <span class="sb-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg></span>
                    eLearning
                </a>
                <a href="{{ route('reports.ilt') }}"
                   class="sb-sub {{ request()->is('reports/ilt*') ? 'active' : '' }}">
                    <span class="sb-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></span>
                    Instructor-Led
                </a>
                <a href="{{ route('reports.financial') }}"
                   class="sb-sub {{ request()->is('reports/financial*') ? 'active' : '' }}">
                    <span class="sb-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></span>
                    Financial
                </a>
                <a href="{{ route('reports.geographic') }}"
                   class="sb-sub {{ request()->is('reports/geographic*') ? 'active' : '' }}">
                    <span class="sb-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg></span>
                    Geographic
                </a>
                <a href="{{ route('reports.export-center') }}"
                   class="sb-sub {{ request()->is('reports/export-center*') ? 'active' : '' }}">
                    <span class="sb-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg></span>
                    Export Center
                </a>
            </div>
        </div>

        <div class="sb-divider"></div>

        {{-- D. Administration ──────────────────────────── --}}
        @php
            $adminActive = request()->is('users*') || request()->is('settings*') || request()->is('profile*');
        @endphp
        <div class="sb-group sg-admin"
             x-data="{ open: {{ $adminActive ? 'true' : 'false' }} }">

            <div class="sb-group-header" @click="open = !open">
                <div class="sb-group-label">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                    Administration
                </div>
                <svg class="sb-chevron" :class="{ open }" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
            </div>

            <div class="sb-group-items" x-show="open" x-transition>

                <a href="{{ route('users.index') }}"
                   class="sb-sub {{ request()->is('users*') ? 'active' : '' }}">
                    <span class="sb-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></span>
                    User Management
                </a>

                <a href="{{ route('settings.index') }}"
                   class="sb-sub {{ request()->is('settings') ? 'active' : '' }}">
                    <span class="sb-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg></span>
                    Settings
                </a>

                <a href="{{ route('notifications.index') }}"
                   class="sb-sub {{ request()->is('settings/notifications*') ? 'active' : '' }}">
                    <span class="sb-icon">🔔</span>
                    Email Notifications
                </a>

                <a href="{{ route('profile.edit') }}"
                   class="sb-sub {{ request()->is('profile*') ? 'active' : '' }}">
                    <span class="sb-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span>
                    My Profile
                </a>

            </div>
        </div>

    @endif

    </nav>

    @auth
    <div class="sb-footer">
        <div class="sb-user">
            <div class="sb-avatar">{{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}</div>
            <div style="min-width:0;">
                <div class="sb-user-name">{{ Auth::user()->name ?? 'User' }}</div>
                <div class="sb-user-email">{{ Auth::user()->email ?? '' }}</div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="sb-logout">
                <span class="sb-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg></span>
                Logout
            </button>
        </form>
    </div>
    @endauth

</aside>

{{-- Topbar (desktop) --}}
<div class="topbar">
    <div class="topbar-title">@yield('page-title', 'SMS Training Panel')</div>
    @auth
    <div class="topbar-right">
        <div class="topbar-user">
            <div class="topbar-avatar">{{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}</div>
            {{ Auth::user()->name ?? 'User' }}
            &nbsp;<span style="color:#9ca3af;font-size:11px;">{{ ucfirst(Auth::user()->role ?? '') }}</span>
        </div>
    </div>
    @endauth
</div>

<main class="content">

    {{-- ── Admin impersonation banner ─────────────────────────────────────── --}}
    @if(session('impersonating_admin_id'))
    <div style="position:sticky; top:0; z-index:999; background:#7c3aed; color:#fff; padding:10px 20px; display:flex; align-items:center; justify-content:space-between; border-radius:8px; margin-bottom:16px; box-shadow:0 4px 12px rgba(124,58,237,.35);">
        <div style="display:flex; align-items:center; gap:10px;">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            <span style="font-size:14px; font-weight:700;">👁 Admin View Mode</span>
            <span style="font-size:13px; opacity:.85;">You are viewing the learner portal as <strong>{{ Auth::user()->name }}</strong></span>
        </div>
        <form method="POST" action="{{ route('impersonation.stop') }}" style="margin:0;">
            @csrf
            <button type="submit" style="background:rgba(255,255,255,.2); border:1px solid rgba(255,255,255,.4); color:#fff; padding:6px 16px; border-radius:6px; font-size:13px; font-weight:700; cursor:pointer;">
                ← Return to Admin Panel
            </button>
        </form>
    </div>
    @endif

    @yield('content')
</main>

</div><!-- /.layout -->
</body>
</html>
