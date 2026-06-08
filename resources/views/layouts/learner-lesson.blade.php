<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('page-title', 'Lesson') — SMS Training</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
    *, *::before, *::after { box-sizing: border-box; }
    html, body {
        margin: 0; padding: 0;
        width: 100%; height: 100%;
        font-family: 'Inter', system-ui, sans-serif;
        color: #111827;
        overflow: hidden;   /* lesson shell handles its own scroll */
    }

    /* ── Impersonation Banner ─────────────────────────────── */
    .imp-banner {
        position: fixed; top: 0; left: 0; right: 0;
        z-index: 9999;
        background: #7c3aed;
        color: #fff;
        padding: 8px 20px;
        display: flex; align-items: center; justify-content: space-between;
        font-size: 13px; font-weight: 600;
        box-shadow: 0 2px 8px rgba(124,58,237,.4);
    }
    .imp-banner-btn {
        background: rgba(255,255,255,.2);
        border: 1px solid rgba(255,255,255,.35);
        color: #fff; padding: 4px 14px; border-radius: 6px;
        font-size: 12px; font-weight: 700; cursor: pointer;
        font-family: inherit;
    }

    /* ── Outer Shell ──────────────────────────────────────── */
    .ll-shell {
        display: flex;
        height: 100vh;
        overflow: hidden;
        background: #fff;
    }
    /* Push content down when impersonation banner is showing */
    body.has-imp-banner .ll-shell { height: calc(100vh - 36px); margin-top: 36px; }

    /* ── Lesson Navigation Drawer ─────────────────────────── */
    .ll-nav {
        width: 320px;
        min-width: 320px;
        background: #1c1d1f;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        transition: width .28s cubic-bezier(.4,0,.2,1),
                    min-width .28s cubic-bezier(.4,0,.2,1);
        position: relative;
        z-index: 50;
        flex-shrink: 0;
    }
    .ll-nav.nav-collapsed {
        width: 0;
        min-width: 0;
    }

    /* Drawer header */
    .ll-nav-header {
        padding: 18px 18px 14px;
        border-bottom: 1px solid rgba(255,255,255,.07);
        flex-shrink: 0;
    }
    .ll-nav-course {
        font-size: 13px; font-weight: 800; color: #fff;
        line-height: 1.35; margin-bottom: 12px;
        white-space: normal; word-break: break-word;
    }
    .ll-nav-org {
        font-size: 11px; color: rgba(255,255,255,.4);
        font-weight: 600; margin-bottom: 10px;
        text-transform: uppercase; letter-spacing: .5px;
    }
    .ll-nav-prog-track {
        background: rgba(255,255,255,.12);
        border-radius: 20px; height: 4px; overflow: hidden;
        margin-bottom: 6px;
    }
    .ll-nav-prog-fill  { height: 4px; background: #34d399; border-radius: 20px; transition: width .5s; }
    .ll-nav-prog-label {
        display: flex; justify-content: space-between;
        font-size: 11px; color: rgba(255,255,255,.45); font-weight: 600;
    }

    /* Lesson list */
    .ll-nav-list {
        flex: 1;
        overflow-y: auto;
        padding: 6px 0 20px;
    }
    .ll-nav-list::-webkit-scrollbar { width: 3px; }
    .ll-nav-list::-webkit-scrollbar-thumb { background: rgba(255,255,255,.1); border-radius: 10px; }

    .ll-nav-section-label {
        padding: 14px 18px 6px;
        font-size: 10px; font-weight: 800;
        color: rgba(255,255,255,.35);
        text-transform: uppercase; letter-spacing: .8px;
    }

    .ll-lesson-item {
        display: flex; align-items: flex-start; gap: 12px;
        padding: 11px 18px;
        cursor: pointer;
        transition: background .12s;
        text-decoration: none;
        border-left: 3px solid transparent;
        position: relative;
    }
    .ll-lesson-item:hover  { background: rgba(255,255,255,.05); }
    .ll-lesson-item.active {
        background: rgba(99,130,255,.15);
        border-left-color: #6366f1;
    }
    .ll-lesson-item.locked { opacity: .4; cursor: not-allowed; pointer-events: none; }

    .ll-item-icon {
        width: 28px; height: 28px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0; font-size: 11px; font-weight: 800; margin-top: 1px;
    }
    .li-done    { background: #16a34a; color: #fff; }
    .li-active  { background: #6366f1; color: #fff; }
    .li-ready   { background: rgba(255,255,255,.10); color: rgba(255,255,255,.7); }
    .li-locked  { background: rgba(255,255,255,.05); color: rgba(255,255,255,.25); }

    .ll-item-body { flex: 1; min-width: 0; }
    .ll-item-title {
        font-size: 13px; font-weight: 600; color: rgba(255,255,255,.85);
        line-height: 1.35;
    }
    .ll-lesson-item.active .ll-item-title { color: #fff; font-weight: 700; }
    .ll-item-meta {
        font-size: 11px; color: rgba(255,255,255,.38); margin-top: 3px;
        display: flex; align-items: center; gap: 4px;
    }

    /* ── Right Column (topbar + scrollable content) ────────── */
    .ll-body {
        flex: 1;
        min-width: 0;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        background: #f8f9fc;
    }

    /* Topbar */
    .ll-topbar {
        height: 56px;
        background: #fff;
        border-bottom: 1px solid #e5e7eb;
        display: flex; align-items: center; justify-content: space-between;
        padding: 0 20px 0 0;
        gap: 12px;
        flex-shrink: 0;
        box-shadow: 0 1px 4px rgba(15,23,42,.04);
        position: relative; z-index: 40;
    }
    .ll-topbar-left {
        display: flex; align-items: center; gap: 0; flex: 1; min-width: 0;
    }
    .ll-toggle-btn {
        width: 56px; height: 56px; flex-shrink: 0;
        background: none; border: none; cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        color: #374151;
        transition: background .12s;
    }
    .ll-toggle-btn:hover { background: #f3f4f6; }
    .ll-back-btn {
        display: inline-flex; align-items: center; gap: 5px;
        color: #6b7280; font-size: 13px; font-weight: 600;
        text-decoration: none; padding: 6px 10px; border-radius: 6px;
        transition: color .12s, background .12s; white-space: nowrap;
        flex-shrink: 0;
    }
    .ll-back-btn:hover { color: #1e3a8a; background: #f0f4ff; }
    .ll-topbar-title {
        font-size: 14px; font-weight: 700; color: #111827;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        padding: 0 12px;
    }
    .ll-topbar-right {
        display: flex; align-items: center; gap: 10px; flex-shrink: 0;
    }
    .ll-status-pill {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 4px 11px; border-radius: 20px;
        font-size: 12px; font-weight: 700; white-space: nowrap;
    }
    .sp-done     { background: #dcfce7; color: #15803d; }
    .sp-progress { background: #dbeafe; color: #1d4ed8; }
    .sp-pending  { background: #f3f4f6; color: #6b7280; }
    .ll-lesson-counter {
        font-size: 12px; font-weight: 700; color: #9ca3af;
        white-space: nowrap;
    }

    /* Scrollable main area */
    .ll-main {
        flex: 1;
        overflow-y: auto;
        scroll-behavior: smooth;
    }
    .ll-main::-webkit-scrollbar { width: 6px; }
    .ll-main::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 10px; }

    /* Mobile overlay for nav */
    .ll-nav-overlay {
        display: none;
        position: fixed; inset: 0; z-index: 45;
        background: rgba(0,0,0,.55);
    }
    .ll-nav-overlay.show { display: block; }

    /* ── Mobile breakpoints ────────────────────────────────── */
    @media (max-width: 860px) {
        .ll-nav {
            position: fixed; left: 0; top: 0; bottom: 0;
            z-index: 48;
            transform: translateX(-100%);
            transition: transform .28s cubic-bezier(.4,0,.2,1);
            width: 300px !important;
            min-width: 300px !important;
        }
        .ll-nav.nav-open { transform: translateX(0); }
        .ll-nav.nav-collapsed { transform: translateX(-100%); }
        .ll-topbar-title { display: none; }
    }

    @media (max-width: 500px) {
        .ll-topbar { padding: 0 12px 0 0; }
        .ll-lesson-counter { display: none; }
    }
    </style>
</head>
<body class="{{ session('impersonating_admin_id') ? 'has-imp-banner' : '' }}">

{{-- Impersonation Banner --}}
@if(session('impersonating_admin_id'))
<div class="imp-banner">
    <div style="display:flex; align-items:center; gap:8px;">
        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
        <span>Admin View — viewing as <strong>{{ Auth::user()->name ?? '' }}</strong></span>
    </div>
    <form method="POST" action="{{ route('impersonation.stop') }}" style="margin:0;">
        @csrf
        <button type="submit" class="imp-banner-btn">← Return to Admin</button>
    </form>
</div>
@endif

@yield('content')

</body>
</html>
