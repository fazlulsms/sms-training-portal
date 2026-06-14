@extends('layouts.public')

@section('page-title', 'Verify Certificate — SMS Training Academy')
@section('seo-title', 'Verify Training Certificate — SMS Training Academy')
@section('seo-desc', 'Instantly verify the authenticity of an SMS Training Academy certificate by entering the certificate number and recipient name.')

@section('content')
<style>
/* ══════════════════════════════════════════════════════════
   VERIFY CERTIFICATE — Two-column split layout
══════════════════════════════════════════════════════════ */

/* Full-width page wrapper */
.vc-page { min-height: 100vh; background: #f1f5f9; }

/* ── Top split section ── */
.vc-split {
    display: grid;
    grid-template-columns: 420px 1fr;
    min-height: 520px;
}
@media(max-width: 900px) {
    .vc-split { grid-template-columns: 1fr; }
}

/* ── LEFT: Form panel ── */
.vc-left {
    background: linear-gradient(160deg, #060d2e 0%, #0f2470 45%, #1e3a8a 100%);
    position: relative; overflow: hidden;
    padding: 44px 36px 44px;
    display: flex; flex-direction: column; gap: 0;
}
.vc-left::before {
    content: '';
    position: absolute; inset: 0;
    background-image: radial-gradient(rgba(255,255,255,.045) 1px, transparent 1px);
    background-size: 26px 26px;
    pointer-events: none;
}
.vc-left::after {
    content: '';
    position: absolute;
    bottom: -80px; right: -80px;
    width: 260px; height: 260px; border-radius: 50%;
    background: rgba(37,99,235,.18);
    pointer-events: none;
}

/* Brand mark */
.vc-brand {
    display: flex; align-items: center; gap: 11px;
    text-decoration: none; margin-bottom: 36px; position: relative; z-index: 1;
}
.vc-brand img { height: 36px; width: auto; display: block; filter: brightness(0) invert(1); opacity: .9; }
.vc-brand-text strong { display: block; font-size: 13.5px; font-weight: 900; color: #fff; line-height: 1.2; }
.vc-brand-text span   { display: block; font-size: 10px; color: rgba(255,255,255,.45); margin-top: 1px; }

/* Shield icon */
.vc-form-icon {
    width: 56px; height: 56px; border-radius: 16px;
    background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.18);
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 18px; position: relative; z-index: 1;
}
.vc-form-heading { font-size: 24px; font-weight: 900; color: #fff; margin: 0 0 6px; position: relative; z-index: 1; }
.vc-form-sub     { font-size: 13.5px; color: rgba(255,255,255,.6); margin: 0 0 28px; line-height: 1.65; position: relative; z-index: 1; }

/* Form fields */
.vc-form { position: relative; z-index: 1; }
.vc-field { margin-bottom: 12px; }
.vc-field-label {
    display: block; font-size: 10.5px; font-weight: 800;
    text-transform: uppercase; letter-spacing: .75px;
    color: rgba(255,255,255,.45); margin-bottom: 7px;
}
.vc-input {
    width: 100%; padding: 12px 15px;
    border-radius: 10px; border: 1.5px solid rgba(255,255,255,.2);
    background: rgba(255,255,255,.08); color: #fff;
    font-size: 14px; font-family: inherit; outline: none;
    transition: border-color .14s, background .14s;
}
.vc-input::placeholder { color: rgba(255,255,255,.32); }
.vc-input:focus {
    border-color: rgba(255,255,255,.55);
    background: rgba(255,255,255,.14);
    box-shadow: 0 0 0 3px rgba(255,255,255,.05);
}
.vc-input.mono { font-family: 'SFMono-Regular', Consolas, monospace; letter-spacing: .4px; font-size: 13.5px; }

.vc-verify-btn {
    width: 100%; padding: 13px;
    background: linear-gradient(135deg, #fff 0%, #dbeafe 100%);
    color: #0f2470; border: none; border-radius: 10px;
    font-weight: 900; font-size: 15px; cursor: pointer; font-family: inherit;
    display: flex; align-items: center; justify-content: center; gap: 9px;
    margin-top: 6px;
    box-shadow: 0 4px 20px rgba(0,0,0,.25);
    transition: transform .14s, box-shadow .14s;
}
.vc-verify-btn:hover { transform: translateY(-1px); box-shadow: 0 8px 28px rgba(0,0,0,.35); }
.vc-verify-btn:active { transform: none; }

/* Trust list */
.vc-trust-list { list-style: none; padding: 0; margin: 24px 0 0; position: relative; z-index: 1; border-top: 1px solid rgba(255,255,255,.1); padding-top: 20px; }
.vc-trust-list li {
    display: flex; align-items: center; gap: 9px;
    font-size: 12.5px; color: rgba(255,255,255,.52); font-weight: 600;
    padding: 5px 0;
}
.vc-trust-list li svg { flex-shrink: 0; color: rgba(255,255,255,.4); }

/* ── RIGHT: Result panel ── */
.vc-right {
    background: #f8fafc;
    display: flex; flex-direction: column;
    justify-content: center;
    padding: 44px 40px;
    position: relative;
}
@media(max-width: 900px) { .vc-right { padding: 32px 24px; } }

/* Empty / idle state */
.vc-idle {
    text-align: center; padding: 24px 20px;
}
.vc-idle-icon {
    width: 80px; height: 80px; border-radius: 24px;
    background: linear-gradient(135deg, #eff6ff, #dbeafe);
    border: 1px solid #bfdbfe;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 20px;
}
.vc-idle h3 { font-size: 20px; font-weight: 900; color: #111827; margin: 0 0 8px; }
.vc-idle p  { font-size: 14px; color: #9ca3af; margin: 0 0 24px; line-height: 1.65; }
.vc-idle-examples { text-align: left; background: #fff; border: 1px solid #e9ecf0; border-radius: 12px; padding: 16px 18px; }
.vc-idle-examples-title { font-size: 10.5px; font-weight: 800; text-transform: uppercase; letter-spacing: .6px; color: #9ca3af; margin-bottom: 10px; }
.vc-idle-example { font-size: 13px; color: #374151; padding: 5px 0; border-bottom: 1px solid #f5f5f7; display: flex; align-items: center; gap: 8px; }
.vc-idle-example:last-child { border-bottom: none; padding-bottom: 0; }
.vc-idle-example svg { color: #9ca3af; flex-shrink: 0; }

/* ── VERIFIED card ── */
.vc-verified {
    border-radius: 16px; overflow: hidden;
    box-shadow: 0 12px 40px rgba(5,46,22,.18);
}
.vc-verified-top {
    background: linear-gradient(135deg, #052e16, #166534);
    padding: 22px 24px;
    display: flex; align-items: center; gap: 14px;
}
.vc-verified-icon {
    width: 48px; height: 48px; border-radius: 13px; flex-shrink: 0;
    background: rgba(255,255,255,.12); border: 1px solid rgba(255,255,255,.2);
    display: flex; align-items: center; justify-content: center;
}
.vc-verified-title { font-size: 18px; font-weight: 900; color: #fff; margin: 0 0 3px; }
.vc-verified-sub   { font-size: 12.5px; color: rgba(255,255,255,.6); margin: 0; }
.vc-verified-pill {
    margin-left: auto; flex-shrink: 0;
    background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.2);
    border-radius: 20px; padding: 6px 14px;
    font-size: 11px; font-weight: 900; color: #86efac;
    text-transform: uppercase; letter-spacing: .7px;
    display: flex; align-items: center; gap: 6px;
}
.vc-pulse { width: 7px; height: 7px; border-radius: 50%; background: #4ade80; animation: vc-pulse 1.8s ease-in-out infinite; }
@keyframes vc-pulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.45;transform:scale(.7)} }

/* Certificate details */
.vc-cert-body { background: #fff; }
.vc-cert-grid { display: grid; grid-template-columns: 1fr 1fr; }
.vc-cert-cell { padding: 16px 20px; border-right: 1px solid #f0f2f5; border-bottom: 1px solid #f0f2f5; }
.vc-cert-cell:nth-child(2n) { border-right: none; }
.vc-cert-cell.full { grid-column: 1/-1; border-right: none; }
.vc-cert-cell-label {
    font-size: 10px; font-weight: 800; text-transform: uppercase;
    letter-spacing: .65px; color: #9ca3af; margin-bottom: 5px;
    display: flex; align-items: center; gap: 5px;
}
.vc-cert-cell-label svg { opacity: .6; }
.vc-cert-cell-value { font-size: 14px; font-weight: 800; color: #111827; line-height: 1.3; }
.vc-cert-cell-value.mono { font-family: 'SFMono-Regular', Consolas, monospace; font-size: 13px; color: #1e3a8a; letter-spacing: .3px; }

.vc-cert-footer {
    background: linear-gradient(135deg, #f0fdf4, #dcfce7);
    border-top: 1px solid #bbf7d0;
    padding: 13px 20px;
    display: flex; align-items: center; justify-content: space-between; gap: 10px; flex-wrap: wrap;
}
.vc-cert-footer-left { display: flex; align-items: center; gap: 8px; }
.vc-cert-footer-left svg { color: #16a34a; flex-shrink: 0; }
.vc-cert-footer-text { font-size: 12.5px; color: #166534; font-weight: 700; }
.vc-cert-footer-text small { font-weight: 500; opacity: .75; }
.vc-print-btn {
    font-size: 12px; color: #16a34a; font-weight: 700;
    display: flex; align-items: center; gap: 5px;
    cursor: pointer; background: none; border: none; font-family: inherit; padding: 0; opacity: .7;
}
.vc-print-btn:hover { opacity: 1; }

/* ── Alert cards ── */
.vc-alert {
    background: #fff; border-radius: 16px;
    border: 1.5px solid #e9ecf0;
    padding: 24px;
    display: flex; align-items: flex-start; gap: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,.05);
}
.vc-alert.warning { border-color: #fbbf24; background: #fffbeb; }
.vc-alert.danger  { border-color: #fca5a5; background: #fff1f2; }
.vc-alert.info    { border-color: #bfdbfe; background: #eff6ff; }
.vc-alert-icon {
    width: 44px; height: 44px; border-radius: 12px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
}
.vc-alert.warning .vc-alert-icon { background: #fef3c7; }
.vc-alert.danger  .vc-alert-icon { background: #fee2e2; }
.vc-alert.info    .vc-alert-icon { background: #dbeafe; }
.vc-alert-title { font-size: 17px; font-weight: 900; margin: 0 0 7px; }
.vc-alert.warning .vc-alert-title { color: #92400e; }
.vc-alert.danger  .vc-alert-title { color: #991b1b; }
.vc-alert.info    .vc-alert-title { color: #1e40af; }
.vc-alert-text { font-size: 13.5px; color: #6b7280; line-height: 1.75; margin: 0; }
.vc-alert-text a { color: #1e3a8a; font-weight: 700; text-decoration: none; }

/* ══════════════════════════════════════════════════════════
   BELOW-FOLD sections
══════════════════════════════════════════════════════════ */
.vc-below { max-width: 1100px; margin: 0 auto; padding: 48px 24px 72px; }

.vc-section-hd {
    font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: .8px;
    color: #9ca3af; display: flex; align-items: center; gap: 12px; margin-bottom: 20px;
}
.vc-section-hd::after { content: ''; flex: 1; height: 1px; background: #e9ecf0; }

/* Steps grid */
.vc-steps { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-bottom: 40px; }
@media(max-width: 640px) { .vc-steps { grid-template-columns: 1fr; } }

.vc-step {
    background: #fff; border: 1px solid #e9ecf0; border-radius: 16px;
    padding: 22px 22px 18px; position: relative; overflow: hidden;
    transition: box-shadow .15s, border-color .15s;
}
.vc-step:hover { box-shadow: 0 6px 24px rgba(30,58,138,.08); border-color: #bfdbfe; }
.vc-step-n {
    font-size: 60px; font-weight: 900; color: #f0f4ff; line-height: 1;
    position: absolute; right: 12px; bottom: -6px; pointer-events: none; user-select: none;
}
.vc-step-icon {
    width: 42px; height: 42px; border-radius: 12px; margin-bottom: 14px;
    background: linear-gradient(135deg, #eff6ff, #dbeafe);
    display: flex; align-items: center; justify-content: center;
}
.vc-step h4 { font-size: 14px; font-weight: 800; color: #111827; margin: 0 0 7px; }
.vc-step p  { font-size: 13px; color: #6b7280; margin: 0; line-height: 1.65; }

/* Info 2-col grid */
.vc-info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 32px; }
@media(max-width: 640px) { .vc-info-grid { grid-template-columns: 1fr; } }

.vc-info-card { background: #fff; border: 1px solid #e9ecf0; border-radius: 16px; padding: 22px 24px; }
.vc-info-hd { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; }
.vc-info-hd-icon {
    width: 40px; height: 40px; border-radius: 11px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    background: linear-gradient(135deg, #f0f4ff, #dbeafe);
}
.vc-info-hd-title { font-size: 14.5px; font-weight: 800; color: #111827; }
.vc-dot-list { list-style: none; padding: 0; margin: 0; }
.vc-dot-list li {
    display: flex; align-items: flex-start; gap: 9px;
    font-size: 13px; color: #374151; padding: 7px 0;
    border-bottom: 1px solid #f5f5f7; line-height: 1.55;
}
.vc-dot-list li:last-child { border-bottom: none; padding-bottom: 0; }
.vc-dot { width: 6px; height: 6px; border-radius: 50%; background: #2563eb; flex-shrink: 0; margin-top: 5px; }

/* CTA */
.vc-cta {
    background: linear-gradient(135deg, #0a1854, #1e3a8a, #2563eb);
    border-radius: 18px; padding: 32px 36px;
    display: flex; align-items: center; gap: 28px; flex-wrap: wrap;
    position: relative; overflow: hidden;
}
.vc-cta::before {
    content: ''; position: absolute; inset: 0;
    background-image: radial-gradient(rgba(255,255,255,.045) 1px, transparent 1px);
    background-size: 20px 20px;
}
.vc-cta-body { position: relative; z-index: 1; flex: 1; min-width: 180px; }
.vc-cta h3 { font-size: 20px; font-weight: 900; color: #fff; margin: 0 0 6px; }
.vc-cta p  { font-size: 14px; color: rgba(255,255,255,.65); margin: 0; line-height: 1.65; }
.vc-cta-btns { position: relative; z-index: 1; display: flex; gap: 10px; flex-wrap: wrap; }
.vc-cta-primary {
    background: #fff; color: #0f2470; padding: 12px 20px; border-radius: 10px;
    font-weight: 900; font-size: 13.5px; text-decoration: none;
    display: flex; align-items: center; gap: 7px; transition: opacity .13s;
}
.vc-cta-primary:hover { opacity: .92; }
.vc-cta-ghost {
    background: rgba(255,255,255,.1); color: #fff;
    border: 1px solid rgba(255,255,255,.2);
    padding: 12px 20px; border-radius: 10px;
    font-weight: 700; font-size: 13.5px; text-decoration: none;
    display: flex; align-items: center; gap: 7px; transition: background .13s;
}
.vc-cta-ghost:hover { background: rgba(255,255,255,.18); }
</style>

<div class="vc-page">

{{-- ══════════════ TWO-COLUMN SPLIT ══════════════ --}}
<div class="vc-split">

    {{-- ── LEFT: Form ── --}}
    <div class="vc-left">

        <a href="{{ route('public.home') }}" class="vc-brand">
            <img src="{{ asset('sms-logo.png') }}" alt="SMS Training Academy">
            <div class="vc-brand-text">
                <strong>SMS Training Academy</strong>
                <span>Powered by Sustainable Management System Inc.</span>
            </div>
        </a>

        <div class="vc-form-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.9)" stroke-width="1.8">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                <polyline points="9 12 11 14 15 10" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>

        <div class="vc-form-heading">Certificate Verification</div>
        <div class="vc-form-sub">Enter the details exactly as they appear on your certificate to verify its authenticity.</div>

        <form method="GET" action="{{ route('public.verify-certificate') }}" class="vc-form">
            <div class="vc-field">
                <label class="vc-field-label">Full name (as on certificate)</label>
                <input type="text" name="name" class="vc-input"
                       value="{{ request('name') }}"
                       placeholder="e.g. Md. Fazlul Haque"
                       autocomplete="off" required>
            </div>
            <div class="vc-field">
                <label class="vc-field-label">Certificate number</label>
                <input type="text" name="cert" class="vc-input mono"
                       value="{{ request('cert') }}"
                       placeholder="e.g. SMS-TC-2026-0001"
                       autocomplete="off" spellcheck="false" required>
            </div>
            <button type="submit" class="vc-verify-btn">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                Verify Certificate
            </button>
        </form>

        <ul class="vc-trust-list">
            <li>
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                Secure &amp; tamper-proof certificate registry
            </li>
            <li>
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                Instant results — no account required
            </li>
            <li>
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg>
                Internationally recognised CPD-accredited certificates
            </li>
            <li>
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                Name matched with at least 50% similarity threshold
            </li>
        </ul>

    </div>

    {{-- ── RIGHT: Result ── --}}
    <div class="vc-right">

        @if(request('cert') && request('name'))

            @if($result && $result['found'])
            {{-- ✅ VERIFIED --}}
            <div class="vc-verified">
                <div class="vc-verified-top">
                    <div class="vc-verified-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#4ade80" stroke-width="2">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            <polyline points="9 12 11 14 15 10" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div>
                        <div class="vc-verified-title">Certificate Verified</div>
                        <div class="vc-verified-sub">Authentic · Registered in our secure database</div>
                    </div>
                    <div class="vc-verified-pill">
                        <div class="vc-pulse"></div>
                        Authentic
                    </div>
                </div>

                <div class="vc-cert-body">
                    <div class="vc-cert-grid">

                        <div class="vc-cert-cell">
                            <div class="vc-cert-cell-label">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="5" y="2" width="14" height="20" rx="2"/><line x1="9" y1="7" x2="15" y2="7"/><line x1="9" y1="11" x2="15" y2="11"/></svg>
                                Certificate No.
                            </div>
                            <div class="vc-cert-cell-value mono">{{ $result['cert_number'] }}</div>
                        </div>

                        <div class="vc-cert-cell">
                            <div class="vc-cert-cell-label">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                Issue Date
                            </div>
                            <div class="vc-cert-cell-value">
                                {{ $result['issue_date'] ? \Carbon\Carbon::parse($result['issue_date'])->format('d M Y') : '—' }}
                            </div>
                        </div>

                        <div class="vc-cert-cell full" style="border-bottom:1px solid #f0f2f5;">
                            <div class="vc-cert-cell-label">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                Issued To
                            </div>
                            <div class="vc-cert-cell-value" style="font-size:16px;">{{ $result['name'] }}</div>
                        </div>

                        <div class="vc-cert-cell full" style="border-bottom:1px solid #f0f2f5;">
                            <div class="vc-cert-cell-label">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                                Course / Programme
                            </div>
                            <div class="vc-cert-cell-value" style="font-size:15px;">{{ $result['course'] }}</div>
                        </div>

                        @if(!empty($result['company']) && $result['company'] !== '—')
                        <div class="vc-cert-cell">
                            <div class="vc-cert-cell-label">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
                                Company / Organisation
                            </div>
                            <div class="vc-cert-cell-value">{{ $result['company'] }}</div>
                        </div>
                        @endif

                        @if(!empty($result['batch']) && $result['batch'] !== '—')
                        <div class="vc-cert-cell">
                            <div class="vc-cert-cell-label">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/></svg>
                                Batch / Cohort
                            </div>
                            <div class="vc-cert-cell-value">{{ $result['batch'] }}</div>
                        </div>
                        @endif

                        <div class="vc-cert-cell {{ (empty($result['company']) || $result['company'] === '—') && (empty($result['batch']) || $result['batch'] === '—') ? 'full' : '' }}" style="border-bottom:none;">
                            <div class="vc-cert-cell-label">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg>
                                Certificate Type
                            </div>
                            <div class="vc-cert-cell-value">{{ $result['type'] }} Training Certificate</div>
                        </div>

                    </div>

                    <div class="vc-cert-footer">
                        <div class="vc-cert-footer-left">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                <polyline points="9 12 11 14 15 10"/>
                            </svg>
                            <span class="vc-cert-footer-text">
                                Verified by SMS Training Academy
                                <small> · Sustainable Management System Inc.</small>
                            </span>
                        </div>
                        <button class="vc-print-btn" onclick="window.print()">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                            Print / Save
                        </button>
                    </div>
                </div>
            </div>

            @elseif($result && !$result['found'] && ($result['name_mismatch'] ?? false))
            {{-- ⚠️ Name mismatch --}}
            <div class="vc-alert warning">
                <div class="vc-alert-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2">
                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                        <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                    </svg>
                </div>
                <div>
                    <div class="vc-alert-title">Name Does Not Match</div>
                    <p class="vc-alert-text">
                        Certificate <strong>{{ request('cert') }}</strong> exists in our records, but the name you entered doesn't match.
                        Please check the spelling exactly as printed and try again.<br><br>
                        Contact <a href="mailto:training@smscert.com">training@smscert.com</a> if you need help.
                    </p>
                </div>
            </div>

            @else
            {{-- ❌ Not found --}}
            <div class="vc-alert danger">
                <div class="vc-alert-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
                    </svg>
                </div>
                <div>
                    <div class="vc-alert-title">Certificate Not Found</div>
                    <p class="vc-alert-text">
                        No certificate matching <strong>"{{ request('cert') }}"</strong> was found in our database.
                        Double-check the certificate number (e.g. <em>SMS-TC-2026-0001</em>) and try again.<br><br>
                        Contact <a href="mailto:training@smscert.com">training@smscert.com</a> if you believe this is an error.
                    </p>
                </div>
            </div>
            @endif

        @elseif(request('cert') && !request('name'))
        {{-- ℹ️ Missing name --}}
        <div class="vc-alert info">
            <div class="vc-alert-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
            </div>
            <div>
                <div class="vc-alert-title">Full Name Required</div>
                <p class="vc-alert-text">Please enter your full name as printed on the certificate, then click Verify.</p>
            </div>
        </div>

        @else
        {{-- Idle / no search yet --}}
        <div class="vc-idle">
            <div class="vc-idle-icon">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="1.5">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    <polyline points="9 12 11 14 15 10" stroke-width="2"/>
                </svg>
            </div>
            <h3>Ready to Verify</h3>
            <p>Enter a name and certificate number on the left to instantly check its authenticity in our database.</p>
            <div class="vc-idle-examples">
                <div class="vc-idle-examples-title">Certificate number format</div>
                <div class="vc-idle-example">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="5" y="2" width="14" height="20" rx="2"/></svg>
                    <span style="font-family:monospace;font-size:13px;color:#1e3a8a;font-weight:700;">SMS-TC-2026-0001</span>
                    <span style="font-size:12px;color:#9ca3af;">Training Certificate</span>
                </div>
                <div class="vc-idle-example">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="5" y="2" width="14" height="20" rx="2"/></svg>
                    <span style="font-family:monospace;font-size:13px;color:#1e3a8a;font-weight:700;">SMS-EL-2026-0001</span>
                    <span style="font-size:12px;color:#9ca3af;">eLearning Certificate</span>
                </div>
                <div class="vc-idle-example">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="5" y="2" width="14" height="20" rx="2"/></svg>
                    <span style="font-family:monospace;font-size:13px;color:#1e3a8a;font-weight:700;">SMS-CO-2026-0001</span>
                    <span style="font-size:12px;color:#9ca3af;">Corporate Certificate</span>
                </div>
            </div>
        </div>
        @endif

    </div>
</div>

{{-- ══════════════ BELOW-FOLD ══════════════ --}}
<div class="vc-below">

    <div class="vc-section-hd">How verification works</div>
    <div class="vc-steps">
        <div class="vc-step">
            <div class="vc-step-n">1</div>
            <div class="vc-step-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#1e3a8a" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                </svg>
            </div>
            <h4>Enter Your Name</h4>
            <p>Type your full name exactly as printed on your certificate — spelling and spacing matter.</p>
        </div>
        <div class="vc-step">
            <div class="vc-step-n">2</div>
            <div class="vc-step-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#1e3a8a" stroke-width="2">
                    <rect x="5" y="2" width="14" height="20" rx="2"/><line x1="9" y1="7" x2="15" y2="7"/><line x1="9" y1="11" x2="15" y2="11"/>
                </svg>
            </div>
            <h4>Enter Certificate No.</h4>
            <p>Find the unique number on your document — formatted as <em>SMS-TC-YYYY-XXXX</em>.</p>
        </div>
        <div class="vc-step">
            <div class="vc-step-n">3</div>
            <div class="vc-step-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#1e3a8a" stroke-width="2">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    <polyline points="9 12 11 14 15 10"/>
                </svg>
            </div>
            <h4>Get Instant Result</h4>
            <p>Our secure registry returns the full authenticated certificate record immediately.</p>
        </div>
    </div>

    <div class="vc-section-hd">Certificate information</div>
    <div class="vc-info-grid">
        <div class="vc-info-card">
            <div class="vc-info-hd">
                <div class="vc-info-hd-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#1e3a8a" stroke-width="1.8">
                        <circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/>
                    </svg>
                </div>
                <div class="vc-info-hd-title">What Each Certificate Contains</div>
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
            <div class="vc-info-hd">
                <div class="vc-info-hd-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#1e3a8a" stroke-width="1.8">
                        <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                </div>
                <div class="vc-info-hd-title">Why Verify a Certificate?</div>
            </div>
            <ul class="vc-dot-list">
                <li><div class="vc-dot"></div>Employers confirm staff qualifications instantly</li>
                <li><div class="vc-dot"></div>Clients verify trainer credentials before engagement</li>
                <li><div class="vc-dot"></div>Regulatory bodies confirm compliance training records</li>
                <li><div class="vc-dot"></div>Prevent fraudulent or altered certificates</li>
                <li><div class="vc-dot"></div>Share a verifiable proof on LinkedIn or CVs</li>
                <li><div class="vc-dot"></div>Auditors confirm third-party training records</li>
            </ul>
        </div>
    </div>

    <div class="vc-cta">
        <div class="vc-cta-body">
            <h3>Can't Verify Your Certificate?</h3>
            <p>Our team is here to help. If you're experiencing issues or need a duplicate certificate, contact our training office directly.</p>
        </div>
        <div class="vc-cta-btns">
            <a href="mailto:training@smscert.com" class="vc-cta-primary">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                Email Support
            </a>
            <a href="{{ route('public.courses') }}" class="vc-cta-ghost">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                Browse Courses
            </a>
        </div>
    </div>

</div>
</div>

@endsection
