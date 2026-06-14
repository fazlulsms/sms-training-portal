@extends('layouts.public')

@section('page-title', 'Verify Certificate — SMS Training Academy')
@section('seo-title', 'Verify Training Certificate — SMS Training Academy')
@section('seo-desc', 'Instantly verify the authenticity of an SMS Training Academy certificate by entering the certificate number and recipient name.')

@section('content')
<style>
/* ══════════════════════════════════════════════════════════
   VERIFY CERTIFICATE — Full-width premium page
══════════════════════════════════════════════════════════ */

/* ── Hero ── */
.vc-hero {
    background: linear-gradient(145deg, #060d2e 0%, #0a1854 35%, #1e3a8a 70%, #1d4ed8 100%);
    position: relative; overflow: hidden;
    padding: 88px 24px 180px;
    color: #fff; text-align: center;
}
.vc-hero::before {
    content: '';
    position: absolute; inset: 0;
    background:
        radial-gradient(ellipse 60% 50% at 15% 60%, rgba(99,102,241,.18) 0%, transparent 70%),
        radial-gradient(ellipse 50% 40% at 85% 20%, rgba(37,99,235,.22) 0%, transparent 60%);
    pointer-events: none;
}
.vc-hero::after {
    content: '';
    position: absolute; inset: 0;
    background-image: radial-gradient(rgba(255,255,255,.055) 1px, transparent 1px);
    background-size: 30px 30px;
    pointer-events: none;
}
.vc-hero-inner { position: relative; z-index: 1; max-width: 680px; margin: 0 auto; }

.vc-eyebrow {
    display: inline-flex; align-items: center; gap: 8px;
    background: rgba(255,255,255,.09); border: 1px solid rgba(255,255,255,.18);
    padding: 7px 18px; border-radius: 30px;
    font-size: 11.5px; font-weight: 800; text-transform: uppercase; letter-spacing: .9px;
    margin-bottom: 24px; color: rgba(255,255,255,.85);
}

.vc-shield-wrap {
    width: 80px; height: 80px; border-radius: 22px;
    background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.2);
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 24px;
    box-shadow: 0 8px 32px rgba(0,0,0,.2);
}

.vc-hero h1 {
    font-size: 50px; font-weight: 900; margin: 0 0 14px; line-height: 1.1;
    background: linear-gradient(135deg, #fff 30%, rgba(255,255,255,.75) 100%);
    -webkit-background-clip: text; -webkit-text-fill-color: transparent;
}
@media(max-width: 640px) { .vc-hero h1 { font-size: 32px; } }

.vc-hero p {
    font-size: 16.5px; opacity: .72; margin: 0 0 36px; line-height: 1.75;
}

/* Glass form card */
.vc-form-card {
    background: rgba(255,255,255,.08);
    border: 1px solid rgba(255,255,255,.2);
    border-radius: 20px; padding: 28px 28px 22px;
    backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px);
    box-shadow: 0 32px 80px rgba(0,0,0,.35), inset 0 1px 0 rgba(255,255,255,.15);
    max-width: 620px; margin: 0 auto;
}
.vc-form-label {
    display: block; font-size: 11px; font-weight: 800;
    text-transform: uppercase; letter-spacing: .8px;
    color: rgba(255,255,255,.5); margin-bottom: 8px;
}
.vc-form-row { display: flex; gap: 10px; margin-bottom: 10px; }
.vc-form-row:last-of-type { margin-bottom: 0; }
@media(max-width: 560px) { .vc-form-row { flex-direction: column; } }

.vc-input {
    flex: 1; padding: 13px 16px;
    border-radius: 11px; border: 1.5px solid rgba(255,255,255,.22);
    background: rgba(255,255,255,.09); color: #fff;
    font-size: 14.5px; font-family: inherit; outline: none;
    transition: border-color .15s, background .15s;
    min-width: 0;
}
.vc-input::placeholder { color: rgba(255,255,255,.38); }
.vc-input:focus {
    border-color: rgba(255,255,255,.6);
    background: rgba(255,255,255,.16);
    box-shadow: 0 0 0 3px rgba(255,255,255,.06);
}
.vc-input.mono { font-family: 'SFMono-Regular', Consolas, monospace; letter-spacing: .5px; }

.vc-submit-btn {
    padding: 13px 26px; flex-shrink: 0;
    background: linear-gradient(135deg, #fff 0%, #dbeafe 100%);
    color: #0f2470; border: none; border-radius: 11px;
    font-weight: 900; font-size: 14.5px; cursor: pointer; font-family: inherit;
    display: flex; align-items: center; gap: 8px; white-space: nowrap;
    box-shadow: 0 4px 20px rgba(0,0,0,.25);
    transition: transform .15s, box-shadow .15s;
}
.vc-submit-btn:hover { transform: translateY(-1px); box-shadow: 0 8px 28px rgba(0,0,0,.35); }
.vc-submit-btn:active { transform: translateY(0); }

.vc-form-divider { border: none; border-top: 1px solid rgba(255,255,255,.1); margin: 18px 0 14px; }

.vc-form-hints { display: flex; align-items: center; justify-content: center; gap: 20px; flex-wrap: wrap; }
.vc-form-hint-item { display: flex; align-items: center; gap: 6px; font-size: 12px; opacity: .45; }

/* Trust strip */
.vc-trust { display: flex; align-items: center; justify-content: center; gap: 32px; margin-top: 28px; flex-wrap: wrap; position: relative; z-index: 1; }
.vc-trust-item { display: flex; align-items: center; gap: 7px; font-size: 12.5px; color: rgba(255,255,255,.55); font-weight: 600; }

/* ── Content wrapper — pulled up over hero ── */
.vc-body {
    margin-top: -100px;
    position: relative; z-index: 2;
    padding: 0 24px 80px;
}
.vc-body-inner { max-width: 960px; margin: 0 auto; }

/* ── Result: VERIFIED ── */
.vc-verified-wrap {
    border-radius: 20px; overflow: hidden;
    box-shadow: 0 20px 60px rgba(5,46,22,.25);
    margin-bottom: 36px;
}
.vc-verified-top {
    background: linear-gradient(135deg, #052e16 0%, #14532d 60%, #166534 100%);
    padding: 28px 32px;
    display: flex; align-items: center; gap: 18px; flex-wrap: wrap;
}
.vc-verified-icon {
    width: 56px; height: 56px; border-radius: 15px; flex-shrink: 0;
    background: rgba(255,255,255,.12); border: 1px solid rgba(255,255,255,.2);
    display: flex; align-items: center; justify-content: center;
}
.vc-verified-text { flex: 1; min-width: 0; }
.vc-verified-title { font-size: 22px; font-weight: 900; color: #fff; margin: 0 0 3px; }
.vc-verified-sub { font-size: 13.5px; color: rgba(255,255,255,.65); margin: 0; }
.vc-verified-seal {
    background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.2);
    border-radius: 30px; padding: 8px 18px;
    font-size: 11.5px; font-weight: 900; color: #86efac;
    text-transform: uppercase; letter-spacing: .8px;
    display: flex; align-items: center; gap: 7px; white-space: nowrap; flex-shrink: 0;
}
.vc-seal-dot { width: 7px; height: 7px; border-radius: 50%; background: #4ade80; animation: pulse-dot 1.8s ease-in-out infinite; }
@keyframes pulse-dot {
    0%, 100% { opacity: 1; transform: scale(1); }
    50%       { opacity: .5; transform: scale(.75); }
}

.vc-cert-body { background: #fff; }
.vc-cert-grid {
    display: grid; grid-template-columns: repeat(3, 1fr);
}
@media(max-width: 680px) { .vc-cert-grid { grid-template-columns: 1fr 1fr; } }
@media(max-width: 420px) { .vc-cert-grid { grid-template-columns: 1fr; } }

.vc-cert-cell {
    padding: 22px 26px;
    border-right: 1px solid #f0f2f5;
    border-bottom: 1px solid #f0f2f5;
}
.vc-cert-cell:nth-child(3n) { border-right: none; }
@media(max-width: 680px) {
    .vc-cert-cell:nth-child(2n) { border-right: none; }
    .vc-cert-cell:nth-child(3n) { border-right: 1px solid #f0f2f5; }
}
.vc-cert-cell.full { grid-column: 1/-1; border-right: none; border-bottom: none; }
.vc-cert-cell-label {
    font-size: 10.5px; font-weight: 800; text-transform: uppercase;
    letter-spacing: .7px; color: #9ca3af; margin-bottom: 6px;
    display: flex; align-items: center; gap: 6px;
}
.vc-cert-cell-label svg { opacity: .6; }
.vc-cert-cell-value { font-size: 15px; font-weight: 800; color: #111827; line-height: 1.35; }
.vc-cert-cell-value.mono {
    font-family: 'SFMono-Regular', Consolas, monospace;
    font-size: 14px; color: #1e3a8a; letter-spacing: .4px;
}

.vc-cert-footer {
    background: linear-gradient(135deg, #f0fdf4, #dcfce7);
    border-top: 1px solid #bbf7d0;
    padding: 16px 26px;
    display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;
}
.vc-cert-footer-left { display: flex; align-items: center; gap: 10px; }
.vc-cert-footer-left svg { color: #16a34a; flex-shrink: 0; }
.vc-cert-footer-text { font-size: 13.5px; color: #166534; font-weight: 700; }
.vc-cert-footer-text span { font-weight: 500; opacity: .8; }
.vc-cert-footer-print {
    font-size: 12.5px; color: #16a34a; font-weight: 700;
    display: flex; align-items: center; gap: 6px;
    text-decoration: none; opacity: .7;
    cursor: pointer; background: none; border: none; font-family: inherit; padding: 0;
}
.vc-cert-footer-print:hover { opacity: 1; }

/* ── Alert states ── */
.vc-alert {
    background: #fff; border-radius: 20px;
    border: 1.5px solid #e9ecf0;
    padding: 28px 32px;
    display: flex; align-items: flex-start; gap: 20px;
    box-shadow: 0 8px 40px rgba(0,0,0,.06);
    margin-bottom: 36px;
}
.vc-alert.warning { border-color: #fbbf24; background: #fffbeb; }
.vc-alert.danger  { border-color: #fca5a5; background: #fff1f2; }
.vc-alert.info    { border-color: #bfdbfe; background: #eff6ff; }
.vc-alert-icon {
    width: 52px; height: 52px; border-radius: 14px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
}
.vc-alert.warning .vc-alert-icon { background: #fef3c7; }
.vc-alert.danger  .vc-alert-icon { background: #fee2e2; }
.vc-alert.info    .vc-alert-icon { background: #dbeafe; }
.vc-alert-title { font-size: 20px; font-weight: 900; margin: 0 0 8px; }
.vc-alert.warning .vc-alert-title { color: #92400e; }
.vc-alert.danger  .vc-alert-title { color: #991b1b; }
.vc-alert.info    .vc-alert-title { color: #1e40af; }
.vc-alert-text { font-size: 14.5px; color: #6b7280; line-height: 1.75; margin: 0; }
.vc-alert-text a { color: #1e3a8a; font-weight: 700; text-decoration: none; }
.vc-alert-text a:hover { text-decoration: underline; }

/* ── Section header ── */
.vc-section-hd {
    font-size: 11.5px; font-weight: 800; text-transform: uppercase;
    letter-spacing: .8px; color: #9ca3af;
    display: flex; align-items: center; gap: 12px;
    margin-bottom: 20px;
}
.vc-section-hd::after { content: ''; flex: 1; height: 1px; background: #f0f2f5; }

/* ── How it works steps ── */
.vc-steps { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-bottom: 36px; }
@media(max-width: 640px) { .vc-steps { grid-template-columns: 1fr; gap: 10px; } }

.vc-step {
    background: #fff; border: 1px solid #e9ecf0; border-radius: 16px;
    padding: 22px; position: relative; overflow: hidden;
    transition: box-shadow .15s, border-color .15s;
}
.vc-step:hover { box-shadow: 0 8px 28px rgba(30,58,138,.09); border-color: #bfdbfe; }
.vc-step-num-bg {
    font-size: 64px; font-weight: 900; color: #f0f4ff;
    position: absolute; right: 10px; bottom: -8px; line-height: 1;
    pointer-events: none; user-select: none;
}
.vc-step-icon {
    width: 44px; height: 44px; border-radius: 13px; margin-bottom: 16px;
    background: linear-gradient(135deg, #eff6ff, #dbeafe);
    display: flex; align-items: center; justify-content: center;
}
.vc-step h4 { font-size: 14.5px; font-weight: 800; color: #111827; margin: 0 0 7px; }
.vc-step p  { font-size: 13.5px; color: #6b7280; margin: 0; line-height: 1.65; }

/* ── Two-col info ── */
.vc-info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 28px; }
@media(max-width: 640px) { .vc-info-grid { grid-template-columns: 1fr; } }

.vc-info-card { background: #fff; border: 1px solid #e9ecf0; border-radius: 16px; padding: 24px; }
.vc-info-card-hd { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; }
.vc-info-card-icon {
    width: 42px; height: 42px; border-radius: 12px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    background: linear-gradient(135deg, #f0f4ff, #dbeafe);
}
.vc-info-title { font-size: 15px; font-weight: 800; color: #111827; }

.vc-dot-list { list-style: none; padding: 0; margin: 0; }
.vc-dot-list li {
    display: flex; align-items: flex-start; gap: 10px;
    font-size: 13.5px; color: #374151; padding: 8px 0;
    border-bottom: 1px solid #f5f5f7; line-height: 1.55;
}
.vc-dot-list li:last-child { border-bottom: none; padding-bottom: 0; }
.vc-dot { width: 6px; height: 6px; border-radius: 50%; background: #2563eb; flex-shrink: 0; margin-top: 6px; }

.vc-num-list { list-style: none; padding: 0; margin: 0; counter-reset: steps; }
.vc-num-list li {
    counter-increment: steps;
    display: flex; align-items: flex-start; gap: 12px;
    font-size: 13.5px; color: #374151; padding: 8px 0;
    border-bottom: 1px solid #f5f5f7; line-height: 1.55;
}
.vc-num-list li:last-child { border-bottom: none; padding-bottom: 0; }
.vc-num-list li::before {
    content: counter(steps);
    width: 22px; height: 22px; border-radius: 50%;
    background: linear-gradient(135deg, #1e3a8a, #2563eb); color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-size: 11px; font-weight: 900; flex-shrink: 0; margin-top: 1px;
}

/* ── CTA Banner ── */
.vc-cta {
    background: linear-gradient(135deg, #0a1854, #1e3a8a, #2563eb);
    border-radius: 20px; padding: 36px 40px;
    display: flex; align-items: center; gap: 32px; flex-wrap: wrap;
    position: relative; overflow: hidden;
}
.vc-cta::before {
    content: ''; position: absolute; inset: 0;
    background-image: radial-gradient(rgba(255,255,255,.05) 1px, transparent 1px);
    background-size: 22px 22px;
}
.vc-cta::after {
    content: ''; position: absolute;
    right: -60px; top: -60px;
    width: 220px; height: 220px; border-radius: 50%;
    background: rgba(255,255,255,.04);
    pointer-events: none;
}
.vc-cta-body { position: relative; z-index: 1; flex: 1; min-width: 200px; }
.vc-cta h3 { font-size: 22px; font-weight: 900; color: #fff; margin: 0 0 7px; }
.vc-cta p  { font-size: 14.5px; color: rgba(255,255,255,.7); margin: 0; line-height: 1.65; }
.vc-cta-actions { position: relative; z-index: 1; display: flex; gap: 10px; flex-wrap: wrap; }
.vc-cta-primary {
    background: #fff; color: #0f2470;
    padding: 13px 22px; border-radius: 11px;
    font-weight: 900; font-size: 14px; text-decoration: none;
    display: flex; align-items: center; gap: 7px;
    transition: opacity .14s;
}
.vc-cta-primary:hover { opacity: .92; }
.vc-cta-secondary {
    background: rgba(255,255,255,.1); color: #fff;
    border: 1px solid rgba(255,255,255,.22);
    padding: 13px 22px; border-radius: 11px;
    font-weight: 700; font-size: 14px; text-decoration: none;
    display: flex; align-items: center; gap: 7px;
    transition: background .14s;
}
.vc-cta-secondary:hover { background: rgba(255,255,255,.18); }
</style>

{{-- ══════════════ HERO ══════════════ --}}
<div class="vc-hero">
    <div class="vc-hero-inner">

        <div class="vc-eyebrow">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            Instant Certificate Verification
        </div>

        <div class="vc-shield-wrap">
            <svg width="38" height="38" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.9)" stroke-width="1.6">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                <polyline points="9 12 11 14 15 10" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>

        <h1>Certificate Verification</h1>
        <p>Verify the authenticity of any SMS Training Academy certificate<br>instantly and securely — enter the details below to get started.</p>

        {{-- Glass form card --}}
        <div class="vc-form-card">
            <form method="GET" action="{{ route('public.verify-certificate') }}">
                <label class="vc-form-label">Full name (as printed on certificate)</label>
                <div class="vc-form-row" style="margin-bottom:12px;">
                    <input type="text" name="name" class="vc-input"
                           value="{{ request('name') }}"
                           placeholder="e.g. Md. Fazlul Haque"
                           autocomplete="off" required>
                </div>
                <label class="vc-form-label">Certificate number</label>
                <div class="vc-form-row">
                    <input type="text" name="cert" class="vc-input mono"
                           value="{{ request('cert') }}"
                           placeholder="e.g. SMS-TC-2026-0001"
                           autocomplete="off" spellcheck="false" required>
                    <button type="submit" class="vc-submit-btn">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        Verify Now
                    </button>
                </div>
            </form>
            <hr class="vc-form-divider">
            <div class="vc-form-hints">
                <span class="vc-form-hint-item">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    Secure & private
                </span>
                <span class="vc-form-hint-item">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    Instant results
                </span>
                <span class="vc-form-hint-item">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                    50% name match required
                </span>
            </div>
        </div>

    </div>

    {{-- Trust strip --}}
    <div class="vc-trust">
        <div class="vc-trust-item">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg>
            Internationally Recognised
        </div>
        <div class="vc-trust-item">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 9h.01M9 15h.01M15 9h.01M15 15h.01"/></svg>
            QR-Code Verified
        </div>
        <div class="vc-trust-item">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            Tamper-proof Database
        </div>
        <div class="vc-trust-item">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
            CPD Accredited
        </div>
    </div>
</div>

{{-- ══════════════ BODY (pulled over hero) ══════════════ --}}
<div class="vc-body">
<div class="vc-body-inner">

    {{-- ── RESULT STATES ── --}}
    @if(request('cert') && request('name'))

        @if($result && $result['found'])
        {{-- ✅ VERIFIED --}}
        <div class="vc-verified-wrap">
            <div class="vc-verified-top">
                <div class="vc-verified-icon">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#4ade80" stroke-width="2">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        <polyline points="9 12 11 14 15 10" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="vc-verified-text">
                    <div class="vc-verified-title">Certificate Verified</div>
                    <div class="vc-verified-sub">This certificate is authentic and registered in our secure database</div>
                </div>
                <div class="vc-verified-seal">
                    <div class="vc-seal-dot"></div>
                    Authentic
                </div>
            </div>

            <div class="vc-cert-body">
                <div class="vc-cert-grid">

                    <div class="vc-cert-cell">
                        <div class="vc-cert-cell-label">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="5" y="2" width="14" height="20" rx="2"/><line x1="9" y1="7" x2="15" y2="7"/><line x1="9" y1="11" x2="15" y2="11"/><line x1="9" y1="15" x2="13" y2="15"/></svg>
                            Certificate No.
                        </div>
                        <div class="vc-cert-cell-value mono">{{ $result['cert_number'] }}</div>
                    </div>

                    <div class="vc-cert-cell">
                        <div class="vc-cert-cell-label">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            Issued To
                        </div>
                        <div class="vc-cert-cell-value">{{ $result['name'] }}</div>
                    </div>

                    <div class="vc-cert-cell">
                        <div class="vc-cert-cell-label">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            Issue Date
                        </div>
                        <div class="vc-cert-cell-value">
                            {{ $result['issue_date'] ? \Carbon\Carbon::parse($result['issue_date'])->format('d M Y') : '—' }}
                        </div>
                    </div>

                    <div class="vc-cert-cell" style="grid-column: 1 / -1; border-right: none;">
                        <div class="vc-cert-cell-label">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                            Course / Programme
                        </div>
                        <div class="vc-cert-cell-value" style="font-size:17px;">{{ $result['course'] }}</div>
                    </div>

                    @if(!empty($result['company']) && $result['company'] !== '—')
                    <div class="vc-cert-cell">
                        <div class="vc-cert-cell-label">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
                            Company / Organisation
                        </div>
                        <div class="vc-cert-cell-value">{{ $result['company'] }}</div>
                    </div>
                    @endif

                    @if(!empty($result['batch']) && $result['batch'] !== '—')
                    <div class="vc-cert-cell">
                        <div class="vc-cert-cell-label">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            Batch / Cohort
                        </div>
                        <div class="vc-cert-cell-value">{{ $result['batch'] }}</div>
                    </div>
                    @endif

                    <div class="vc-cert-cell @if(empty($result['batch']) || $result['batch'] === '—') @endif">
                        <div class="vc-cert-cell-label">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg>
                            Certificate Type
                        </div>
                        <div class="vc-cert-cell-value">{{ $result['type'] }} Training Certificate</div>
                    </div>

                </div>

                <div class="vc-cert-footer">
                    <div class="vc-cert-footer-left">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            <polyline points="9 12 11 14 15 10" stroke-width="2.5"/>
                        </svg>
                        <span class="vc-cert-footer-text">
                            Verified by SMS Training Academy
                            <span> · Sustainable Management System Inc. · New York, USA</span>
                        </span>
                    </div>
                    <button class="vc-cert-footer-print" onclick="window.print()">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                        Print / Save
                    </button>
                </div>
            </div>
        </div>

        @elseif($result && !$result['found'] && ($result['name_mismatch'] ?? false))
        {{-- ⚠️ Name mismatch --}}
        <div class="vc-alert warning">
            <div class="vc-alert-icon">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                    <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
            </div>
            <div>
                <div class="vc-alert-title">Name Does Not Match</div>
                <p class="vc-alert-text">
                    A certificate with number <strong>{{ request('cert') }}</strong> exists in our records,
                    but the name you entered does not match what's on the certificate.
                    Please check the spelling exactly as it appears on your certificate and try again.<br><br>
                    Still having trouble? Contact us at
                    <a href="mailto:training@smscert.com">training@smscert.com</a>.
                </p>
            </div>
        </div>

        @else
        {{-- ❌ Not found --}}
        <div class="vc-alert danger">
            <div class="vc-alert-icon">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
                </svg>
            </div>
            <div>
                <div class="vc-alert-title">Certificate Not Found</div>
                <p class="vc-alert-text">
                    No certificate matching <strong>"{{ request('cert') }}"</strong> was found in our database.
                    Please double-check the certificate number and ensure it is entered exactly as printed
                    (e.g. <em>SMS-TC-2026-0001</em>).<br><br>
                    If you believe this is an error, contact us at
                    <a href="mailto:training@smscert.com">training@smscert.com</a>
                    and we'll look into it.
                </p>
            </div>
        </div>
        @endif

    @elseif(request('cert') && !request('name'))
    {{-- ℹ️ Name missing --}}
    <div class="vc-alert info">
        <div class="vc-alert-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
        </div>
        <div>
            <div class="vc-alert-title">Full Name Required</div>
            <p class="vc-alert-text">
                Please enter your full name exactly as it appears on your certificate,
                along with the certificate number, and click Verify.
            </p>
        </div>
    </div>
    @endif

    {{-- ══ HOW IT WORKS ══ --}}
    <div class="vc-section-hd">How verification works</div>
    <div class="vc-steps">
        <div class="vc-step">
            <div class="vc-step-num-bg">1</div>
            <div class="vc-step-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#1e3a8a" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                </svg>
            </div>
            <h4>Enter Your Name</h4>
            <p>Type your full name exactly as printed on your certificate — spelling and spacing matter.</p>
        </div>
        <div class="vc-step">
            <div class="vc-step-num-bg">2</div>
            <div class="vc-step-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#1e3a8a" stroke-width="2">
                    <rect x="5" y="2" width="14" height="20" rx="2"/><line x1="9" y1="7" x2="15" y2="7"/><line x1="9" y1="11" x2="15" y2="11"/>
                </svg>
            </div>
            <h4>Enter Certificate No.</h4>
            <p>Find the unique certificate number on your document — usually formatted as <em>SMS-TC-YYYY-XXXX</em>.</p>
        </div>
        <div class="vc-step">
            <div class="vc-step-num-bg">3</div>
            <div class="vc-step-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#1e3a8a" stroke-width="2">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    <polyline points="9 12 11 14 15 10"/>
                </svg>
            </div>
            <h4>Instant Verification</h4>
            <p>Our system instantly checks our secure certificate registry and returns the full authenticated record.</p>
        </div>
    </div>

    {{-- ══ INFO GRID ══ --}}
    <div class="vc-section-hd">Certificate information</div>
    <div class="vc-info-grid">
        <div class="vc-info-card">
            <div class="vc-info-card-hd">
                <div class="vc-info-card-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#1e3a8a" stroke-width="1.8">
                        <circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/>
                    </svg>
                </div>
                <div class="vc-info-title">What Each Certificate Contains</div>
            </div>
            <ul class="vc-dot-list">
                <li><div class="vc-dot"></div>Participant's full name and designation</li>
                <li><div class="vc-dot"></div>Course or programme name and type</li>
                <li><div class="vc-dot"></div>Completion date and batch information</li>
                <li><div class="vc-dot"></div>Unique QR-linked certificate number</li>
                <li><div class="vc-dot"></div>Authorised signatures of Directors &amp; Trainers</li>
                <li><div class="vc-dot"></div>CPD credit hours (where applicable)</li>
            </ul>
        </div>
        <div class="vc-info-card">
            <div class="vc-info-card-hd">
                <div class="vc-info-card-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#1e3a8a" stroke-width="1.8">
                        <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                </div>
                <div class="vc-info-title">Why Verify a Certificate?</div>
            </div>
            <ul class="vc-dot-list">
                <li><div class="vc-dot"></div>Employers can instantly confirm staff qualifications</li>
                <li><div class="vc-dot"></div>Clients verify trainer credentials before engagement</li>
                <li><div class="vc-dot"></div>Regulatory bodies confirm compliance training</li>
                <li><div class="vc-dot"></div>Prevent fraudulent or altered certificates</li>
                <li><div class="vc-dot"></div>Share a verifiable proof with LinkedIn or CVs</li>
                <li><div class="vc-dot"></div>Auditors can confirm third-party training records</li>
            </ul>
        </div>
    </div>

    {{-- ══ CTA ══ --}}
    <div class="vc-cta">
        <div class="vc-cta-body">
            <h3>Can't Verify Your Certificate?</h3>
            <p>Our team is here to help. If you're experiencing issues or need a duplicate certificate, contact our training office directly.</p>
        </div>
        <div class="vc-cta-actions">
            <a href="mailto:training@smscert.com" class="vc-cta-primary">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                Email Support
            </a>
            <a href="{{ route('public.courses') }}" class="vc-cta-secondary">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                Browse Courses
            </a>
        </div>
    </div>

</div>
</div>

@endsection
