<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('page-title', 'My Learning') — SMS Training Academy</title>
    <link rel="icon" type="image/x-icon"  href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <meta name="theme-color" content="#042C53">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')

    <style>
    *, *::before, *::after { box-sizing: border-box; }
    html, body { margin: 0; padding: 0; font-family: 'Inter', system-ui, sans-serif; background: #f8fafc; color: #111827; min-height: 100vh; }

    /* ── Layout shell ── */
    .p-shell { display: flex; min-height: 100vh; }

    /* ── Sidebar ── */
    .p-sidebar {
        width: 240px;
        flex-shrink: 0;
        background: linear-gradient(180deg, #0f1e45 0%, #1e3a8a 100%);
        display: flex;
        flex-direction: column;
        position: fixed;
        top: 0; bottom: 0; left: 0;
        z-index: 200;
        overflow-y: auto;
        overflow-x: hidden;
        scrollbar-width: thin;
        scrollbar-color: rgba(255,255,255,.1) transparent;
        transition: transform .25s ease;
    }
    .p-sidebar::-webkit-scrollbar { width: 4px; }
    .p-sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,.10); border-radius: 20px; }

    /* ── Brand ── */
    .p-brand {
        display: flex;
        align-items: center;
        gap: 11px;
        padding: 20px 16px 18px;
        border-bottom: 1px solid rgba(255,255,255,.08);
        flex-shrink: 0;
    }
    .p-brand-logo {
        width: 40px; height: 40px;
        object-fit: contain;
        background: white;
        padding: 5px;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0,0,0,.25);
        flex-shrink: 0;
    }
    .p-brand-name { font-size: 14px; font-weight: 800; color: #fff; line-height: 1.1; }
    .p-brand-sub  { font-size: 9px; font-weight: 700; color: #60a5fa; text-transform: uppercase; letter-spacing: 1px; margin-top: 2px; }

    /* ── Sidebar nav ── */
    .p-nav { flex: 1; padding: 12px 10px; }
    .p-nav-label {
        font-size: 9.5px; font-weight: 800; color: rgba(255,255,255,.4);
        text-transform: uppercase; letter-spacing: 1.2px;
        padding: 12px 8px 6px;
    }
    .p-nav-item {
        display: flex; align-items: center; gap: 10px;
        padding: 9px 10px; border-radius: 9px;
        color: rgba(255,255,255,.7); text-decoration: none;
        font-size: 13.5px; font-weight: 600;
        transition: background .15s, color .15s;
        margin-bottom: 2px;
    }
    .p-nav-item:hover { background: rgba(255,255,255,.1); color: #fff; }
    .p-nav-item.active { background: rgba(255,255,255,.15); color: #fff; }
    .p-nav-icon { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; background: rgba(255,255,255,.08); }
    .p-nav-item.active .p-nav-icon { background: rgba(99,179,237,.25); }

    /* ── Sidebar footer / user ── */
    .p-sidebar-footer {
        padding: 12px 14px;
        border-top: 1px solid rgba(255,255,255,.08);
        flex-shrink: 0;
    }
    .p-user-row { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; }
    .p-avatar {
        width: 34px; height: 34px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        background: #3b82f6; color: #fff;
        font-size: 13px; font-weight: 800; flex-shrink: 0;
        overflow: hidden;
    }
    .p-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .p-user-name  { font-size: 13px; font-weight: 700; color: #fff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .p-user-email { font-size: 11px; color: rgba(255,255,255,.5); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .p-logout {
        display: flex; align-items: center; gap: 8px;
        color: #fca5a5; text-decoration: none;
        padding: 7px 10px; border-radius: 8px;
        font-size: 12.5px; font-weight: 600;
        background: none; border: none; cursor: pointer;
        width: 100%; text-align: left; font-family: inherit;
        transition: background .15s;
    }
    .p-logout:hover { background: rgba(239,68,68,.10); }

    /* ── Main content area ── */
    .p-main {
        margin-left: 240px;
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }

    /* ── Topbar ── */
    .p-topbar {
        height: 56px;
        background: #fff;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 24px;
        position: sticky; top: 0; z-index: 100;
        flex-shrink: 0;
    }
    .p-topbar-left { display: flex; align-items: center; gap: 12px; }
    .p-topbar-title { font-size: 15px; font-weight: 800; color: #111827; }
    .p-topbar-right { display: flex; align-items: center; gap: 10px; }
    .p-topbar-avatar {
        width: 32px; height: 32px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        background: #1e3a8a; color: #fff;
        font-size: 12px; font-weight: 800;
        overflow: hidden; cursor: pointer;
    }
    .p-topbar-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .p-topbar-name { font-size: 13px; font-weight: 700; color: #374151; }

    /* ── Content ── */
    .p-content { flex: 1; padding: 24px; }

    /* ── Mobile toggle ── */
    #p-sidebar-toggle { display: none; }
    .p-hamburger {
        display: none;
        flex-direction: column;
        gap: 5px; cursor: pointer;
        padding: 6px;
        background: none; border: none;
    }
    .p-hamburger span { display: block; width: 20px; height: 2px; background: #374151; border-radius: 2px; }
    .p-overlay {
        display: none;
        position: fixed; inset: 0;
        background: rgba(0,0,0,.5);
        z-index: 150;
    }

    @media (max-width: 768px) {
        .p-sidebar { transform: translateX(-100%); }
        #p-sidebar-toggle:checked ~ .p-overlay { display: block; }
        #p-sidebar-toggle:checked ~ .p-shell .p-sidebar { transform: translateX(0); }
        .p-main { margin-left: 0; }
        .p-hamburger { display: flex; }
        .p-content { padding: 16px; }
    }

    /* ── Impersonation banner ── */
    .p-imp-banner {
        background: #7c3aed; color: #fff;
        padding: 10px 20px;
        display: flex; align-items: center; justify-content: space-between;
        font-size: 13px; font-weight: 700;
    }
    </style>
    @stack('styles')
</head>
<body>

<input type="checkbox" id="p-sidebar-toggle">
<label for="p-sidebar-toggle" class="p-overlay"></label>

<div class="p-shell">

    {{-- ════ Sidebar ════ --}}
    <aside class="p-sidebar">

        {{-- Brand --}}
        <div class="p-brand">
            <img src="{{ asset('sms-logo.png') }}" alt="SMS Training Academy" class="p-brand-logo">
            <div>
                <div class="p-brand-name">SMS Training<br>Academy</div>
                <div class="p-brand-sub">Learner Portal</div>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="p-nav">
            <div class="p-nav-label">My Learning</div>

            <a href="{{ route('participant.my-courses') }}"
               class="p-nav-item {{ request()->is('my-courses*') || request()->is('my-elearning*') ? 'active' : '' }}">
                <span class="p-nav-icon">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                </span>
                My Dashboard
            </a>

            <a href="{{ route('participant.my-certificates') }}"
               class="p-nav-item {{ request()->is('my-certificates*') ? 'active' : '' }}">
                <span class="p-nav-icon">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg>
                </span>
                My Certificates
            </a>

            <div class="p-nav-label" style="margin-top:4px;">Account</div>

            <a href="{{ route('profile.edit') }}"
               class="p-nav-item {{ request()->is('profile*') ? 'active' : '' }}">
                <span class="p-nav-icon">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </span>
                My Profile
            </a>

            <a href="{{ route('public.home') }}" target="_blank"
               class="p-nav-item">
                <span class="p-nav-icon">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                </span>
                Training Website
            </a>
        </nav>

        {{-- Sidebar footer / user ── --}}
        @auth
        <div class="p-sidebar-footer">
            <div class="p-user-row">
                <div class="p-avatar">
                    @if(Auth::user()->photo_path)
                        <img src="{{ Auth::user()->photoUrl() }}" alt="{{ Auth::user()->name }}">
                    @else
                        {{ Auth::user()->initials() }}
                    @endif
                </div>
                <div style="min-width:0;">
                    <div class="p-user-name">{{ Auth::user()->name }}</div>
                    <div class="p-user-email">{{ Auth::user()->email }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="p-logout">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    Sign Out
                </button>
            </form>
        </div>
        @endauth

    </aside>

    {{-- ════ Main area ════ --}}
    <div class="p-main">

        {{-- Topbar --}}
        <div class="p-topbar">
            <div class="p-topbar-left">
                <label for="p-sidebar-toggle" class="p-hamburger" aria-label="Menu">
                    <span></span><span></span><span></span>
                </label>
                <div class="p-topbar-title">@yield('page-title', 'My Learning')</div>
            </div>
            @auth
            <div class="p-topbar-right">
                <a href="{{ route('profile.edit') }}" style="display:flex;align-items:center;gap:8px;text-decoration:none;">
                    <div class="p-topbar-avatar">
                        @if(Auth::user()->photo_path)
                            <img src="{{ Auth::user()->photoUrl() }}" alt="{{ Auth::user()->name }}">
                        @else
                            {{ Auth::user()->initials() }}
                        @endif
                    </div>
                    <span class="p-topbar-name" style="display:none;">{{ Auth::user()->name }}</span>
                </a>
            </div>
            @endauth
        </div>

        {{-- Impersonation banner --}}
        @if(session('impersonating_admin_id'))
        <div class="p-imp-banner">
            <span>👁 Admin View Mode — viewing as <strong>{{ Auth::user()->name }}</strong></span>
            <form method="POST" action="{{ route('impersonation.stop') }}" style="margin:0;">
                @csrf
                <button type="submit" style="background:rgba(255,255,255,.2);border:1px solid rgba(255,255,255,.4);color:#fff;padding:5px 14px;border-radius:6px;font-size:12px;font-weight:700;cursor:pointer;">
                    ← Return to Admin
                </button>
            </form>
        </div>
        @endif

        {{-- Page content --}}
        <main class="p-content">
            @yield('content')
        </main>

    </div><!-- /.p-main -->

</div><!-- /.p-shell -->

@stack('scripts')
</body>
</html>
