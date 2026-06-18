@extends('layouts.public')

@php
    $certIssued = $enrollment->certificate_status === 'issued';
    $courseName = $enrollment->course->name ?? 'eLearning Course';
    $certNumber = $enrollment->certificate_number ?? '—';
    $learnerName = $enrollment->participant_name ?? ($enrollment->user->name ?? '—');
    $completionDate = $enrollment->completion_date
        ? \Carbon\Carbon::parse($enrollment->completion_date)->format('d M Y')
        : ($enrollment->updated_at ? $enrollment->updated_at->format('d M Y') : '—');
    $company     = $enrollment->company     ?? null;
    $designation = $enrollment->designation ?? null;
    $country     = $enrollment->country     ?? null;
    $verifyUrl   = request()->fullUrl();
@endphp

@section('page-title', 'eLearning Certificate — ' . $learnerName . ' — SMS Training Academy')
@section('seo-title', 'Verified eLearning Certificate — SMS Training Academy')
@section('seo-desc', 'Certificate ' . $certNumber . ' has been verified in the SMS Training Academy official registry.')

@section('content')
<style>
*, *::before, *::after { box-sizing: border-box; }

/* ── Page shell ── */
.ecv { background: #f4f6fb; min-height: 100vh; padding: 32px 16px 60px; }

/* ── Centered card ── */
.ecv-card {
    max-width: 680px; margin: 0 auto;
    background: #fff; border-radius: 20px;
    box-shadow: 0 8px 40px rgba(15,23,42,.12);
    overflow: hidden;
}

/* ── Green verified banner ── */
.ecv-banner {
    background: linear-gradient(135deg, #0F7A43 0%, #18A05E 100%);
    padding: 28px 30px 24px;
    position: relative; overflow: hidden;
}
.ecv-banner::after {
    content: '';
    position: absolute; right: -30px; top: -30px;
    width: 200px; height: 200px;
    background: url("data:image/svg+xml,%3Csvg viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Ccircle cx='50' cy='50' r='46' fill='none' stroke='rgba(255,255,255,.07)' stroke-width='4'/%3E%3Cpolyline points='28,50 44,66 72,34' fill='none' stroke='rgba(255,255,255,.07)' stroke-width='6' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E") center/contain no-repeat;
    pointer-events: none;
}
.ecv-banner-head {
    display: flex; align-items: center; gap: 14px; margin-bottom: 16px;
}
.ecv-seal {
    width: 48px; height: 48px; border-radius: 50%; flex-shrink: 0;
    border: 2px solid rgba(255,255,255,.6);
    display: flex; align-items: center; justify-content: center;
    background: rgba(255,255,255,.1);
}
.ecv-banner-text .ecv-verified-pill {
    display: inline-flex; align-items: center; gap: 5px;
    background: rgba(255,255,255,.18); border: 1px solid rgba(255,255,255,.3);
    border-radius: 20px; padding: 3px 10px; font-size: 11px; font-weight: 800;
    color: #fff; text-transform: uppercase; letter-spacing: .6px; margin-bottom: 4px;
}
.ecv-banner-text h1 {
    font-size: 20px; font-weight: 900; color: #fff; margin: 0 0 2px; line-height: 1.2;
}
.ecv-banner-text p { font-size: 12.5px; color: rgba(255,255,255,.65); margin: 0; }

/* Certificate type badge */
.ecv-type-badge {
    display: inline-flex; align-items: center; gap: 6px;
    background: rgba(255,255,255,.12); border: 1px solid rgba(255,255,255,.2);
    border-radius: 8px; padding: 6px 12px;
    font-size: 12px; font-weight: 700; color: rgba(255,255,255,.85);
    margin-top: 2px;
}

/* ── NOT issued warning ── */
.ecv-warn {
    background: #fffbeb; border-bottom: 3px solid #f59e0b;
    padding: 16px 22px; display: flex; align-items: flex-start; gap: 12px;
}
.ecv-warn-icon { flex-shrink: 0; margin-top: 2px; }
.ecv-warn-text { font-size: 13px; color: #92400e; font-weight: 600; line-height: 1.5; }

/* ── Data grid ── */
.ecv-grid {
    display: grid; grid-template-columns: 1fr 1fr;
    border-top: 1px solid #f0f2f5;
}
@media(max-width: 480px) { .ecv-grid { grid-template-columns: 1fr; } }

.ecv-cell {
    padding: 16px 22px;
    border-right: 1px solid #f0f2f5;
    border-bottom: 1px solid #f0f2f5;
}
.ecv-cell:nth-child(2n) { border-right: none; }
@media(max-width: 480px) {
    .ecv-cell { border-right: none; }
}
.ecv-cell.full { grid-column: 1/-1; border-right: none; }

.ecv-cell-label {
    font-size: 10px; font-weight: 800; text-transform: uppercase;
    letter-spacing: .65px; color: #9ca3af;
    display: flex; align-items: center; gap: 5px; margin-bottom: 4px;
}
.ecv-cell-val { font-size: 14px; font-weight: 800; color: #111827; line-height: 1.3; }
.ecv-cell-val.mono { font-family: 'SFMono-Regular', Consolas, monospace; font-size: 13px; color: #163C8A; letter-spacing: .3px; }
.ecv-cell-val.lg { font-size: 16px; }

/* ── Footer strip ── */
.ecv-footer {
    background: linear-gradient(135deg, #f0fdf4, #dcfce7);
    border-top: 1px solid #bbf7d0;
    padding: 14px 22px;
    display: flex; align-items: center; justify-content: space-between; gap: 10px;
    flex-wrap: wrap;
}
.ecv-footer-l { display: flex; align-items: center; gap: 8px; }
.ecv-footer-text { font-size: 12px; color: #166534; font-weight: 700; line-height: 1.4; }
.ecv-footer-text small { font-weight: 500; opacity: .75; display: block; }

/* ── Action strip ── */
.ecv-actions {
    padding: 14px 22px; background: #f8fafc;
    border-top: 1px solid #f0f2f5;
    display: flex; gap: 8px; flex-wrap: wrap;
}
.ecv-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 9px 16px; border-radius: 9px; font-size: 13px; font-weight: 700;
    cursor: pointer; font-family: inherit; text-decoration: none;
    border: 1.5px solid transparent; transition: all .14s; white-space: nowrap;
}
.ecv-btn.primary { background: #042C53; color: #fff; border-color: #042C53; }
.ecv-btn.primary:hover { background: #163C8A; border-color: #163C8A; }
.ecv-btn.ghost  { background: #fff; color: #374151; border-color: #e5e7eb; }
.ecv-btn.ghost:hover { border-color: #042C53; color: #042C53; }

/* ── Trust badges below card ── */
.ecv-badges {
    max-width: 680px; margin: 16px auto 0;
    display: flex; gap: 8px; flex-wrap: wrap; justify-content: center;
}
.ecv-badge {
    display: inline-flex; align-items: center; gap: 6px;
    background: #fff; border: 1px solid #e9ecf0; border-radius: 20px;
    padding: 6px 13px; font-size: 11.5px; font-weight: 700; color: #374151;
    box-shadow: 0 1px 3px rgba(0,0,0,.04);
}
.ecv-badge svg { color: #16a34a; }

/* ── Verify another link ── */
.ecv-again {
    text-align: center; margin-top: 20px;
    font-size: 12.5px; color: #9ca3af;
}
.ecv-again a { color: #042C53; font-weight: 700; text-decoration: none; }
.ecv-again a:hover { text-decoration: underline; }
</style>

<div class="ecv">

    <div class="ecv-card">

        {{-- ── Green verified banner ── --}}
        <div class="ecv-banner">
            <div class="ecv-banner-head">
                <div class="ecv-seal">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" stroke="rgba(255,255,255,.9)" stroke-width="1.8"/>
                        <polyline points="9 12 11 14 15 10" stroke="rgba(255,255,255,.9)" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="ecv-banner-text">
                    <div class="ecv-verified-pill">
                        <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                        Verified
                    </div>
                    <h1>Authentic eLearning Certificate</h1>
                    <p>Registered in the SMS Training Academy official registry</p>
                </div>
            </div>
            <div class="ecv-type-badge">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                eLearning Certificate of Completion
            </div>
        </div>

        {{-- ── Warning if not yet officially issued ── --}}
        @if(!$certIssued)
        <div class="ecv-warn">
            <div class="ecv-warn-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                    <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
            </div>
            <div class="ecv-warn-text">This certificate record exists but has not yet been officially issued. Please contact <a href="mailto:training@smscert.com" style="color:#92400e;">training@smscert.com</a> if you believe this is an error.</div>
        </div>
        @endif

        {{-- ── Credential details grid ── --}}
        <div class="ecv-grid">

            <div class="ecv-cell">
                <div class="ecv-cell-label">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="5" y="2" width="14" height="20" rx="2"/><line x1="9" y1="7" x2="15" y2="7"/></svg>
                    Certificate No.
                </div>
                <div class="ecv-cell-val mono">{{ $certNumber }}</div>
            </div>

            <div class="ecv-cell">
                <div class="ecv-cell-label">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    Completion Date
                </div>
                <div class="ecv-cell-val">{{ $completionDate }}</div>
            </div>

            <div class="ecv-cell full">
                <div class="ecv-cell-label">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    Participant Name
                </div>
                <div class="ecv-cell-val lg">{{ $learnerName }}</div>
            </div>

            @if($designation)
            <div class="ecv-cell">
                <div class="ecv-cell-label">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
                    Designation
                </div>
                <div class="ecv-cell-val">{{ $designation }}</div>
            </div>
            @endif

            @if($company)
            <div class="ecv-cell {{ $designation ? '' : 'full' }}">
                <div class="ecv-cell-label">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    Organisation
                </div>
                <div class="ecv-cell-val">{{ $company }}</div>
            </div>
            @endif

            @if($country)
            <div class="ecv-cell {{ (!$designation && !$company) ? 'full' : '' }}">
                <div class="ecv-cell-label">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10"/></svg>
                    Country
                </div>
                <div class="ecv-cell-val">{{ $country }}</div>
            </div>
            @endif

            <div class="ecv-cell full" style="border-bottom:none;">
                <div class="ecv-cell-label">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                    Course / Programme
                </div>
                <div class="ecv-cell-val lg">{{ $courseName }}</div>
            </div>

        </div>

        {{-- ── Action buttons ── --}}
        <div class="ecv-actions">
            <button class="ecv-btn primary" onclick="window.print()">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                Print
            </button>
            <button class="ecv-btn ghost" onclick="ecvShare()">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
                Share
            </button>
            <button class="ecv-btn ghost" onclick="ecvCopy()">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                <span id="ecv-copy-txt">Copy Link</span>
            </button>
        </div>

        {{-- ── Verified footer strip ── --}}
        <div class="ecv-footer">
            <div class="ecv-footer-l">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    <polyline points="9 12 11 14 15 10"/>
                </svg>
                <div class="ecv-footer-text">
                    Verified by SMS Training Academy
                    <small>Sustainable Management System Inc. &middot; New York, USA</small>
                </div>
            </div>
            <span style="font-size:11px;color:#9ca3af;font-weight:600;white-space:nowrap;">{{ now()->format('d M Y') }}</span>
        </div>

    </div>

    {{-- ── Trust badges ── --}}
    <div class="ecv-badges">
        <span class="ecv-badge">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            Official Registry
        </span>
        <span class="ecv-badge">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 9h.01M9 15h.01M15 9h.01M15 15h.01"/></svg>
            QR Verified
        </span>
        <span class="ecv-badge">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            Secure Record
        </span>
        <span class="ecv-badge">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
            Instant Verification
        </span>
    </div>

    <div class="ecv-again">
        <a href="{{ url('/verify') }}">Verify another certificate</a>
        &nbsp;&middot;&nbsp;
        <a href="{{ url('/') }}">SMS Training Academy</a>
    </div>

</div>

<script>
function ecvShare() {
    if (navigator.share) {
        navigator.share({
            title: 'SMS Training Academy — Verified Certificate',
            text: 'Certificate of {{ $learnerName }} — {{ $courseName }}',
            url: '{{ $verifyUrl }}'
        }).catch(function () {});
    } else {
        ecvCopy();
    }
}
function ecvCopy() {
    navigator.clipboard.writeText('{{ $verifyUrl }}').then(function () {
        var el = document.getElementById('ecv-copy-txt');
        if (el) { el.textContent = 'Copied!'; setTimeout(function () { el.textContent = 'Copy Link'; }, 2000); }
    }).catch(function () {});
}
</script>
@endsection
