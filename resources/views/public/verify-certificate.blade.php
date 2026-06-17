@extends('layouts.public')

@section('page-title', 'Verify Certificate — SMS Training Academy')
@section('seo-title', 'Verify Training Certificate — SMS Training Academy')
@section('seo-desc', 'Instantly verify the authenticity of any SMS Training Academy certificate. Enter the participant name and certificate number to confirm credential validity.')

@section('content')
<style>
/* ══════════════════════════════════════════════════════════════════
   SMS TRAINING ACADEMY — Premium Certificate Verification Portal
   UI/UX redesign — logic, routes & DB unchanged
══════════════════════════════════════════════════════════════════ */
*, *::before, *::after { box-sizing: border-box; }

/* ── Page shell ── */
.vcp { background: #fff; font-family: inherit; overflow-x: hidden; }

/* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
   1. HERO SPLIT  (35 / 65)
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
/* Centered hero wrapper */
.vcp-hero-wrap {
    background: #f8fafc;
    padding: 48px 24px;
}
.vcp-hero-container {
    max-width: var(--max-w);
    margin: 0 auto;
    border-radius: 24px;
    overflow: hidden;
    box-shadow: 0 8px 40px rgba(15,23,42,.1);
}
.vcp-split {
    display: grid;
    grid-template-columns: 380px 1fr;
    min-height: 580px;
}
@media(max-width: 960px) { .vcp-split { grid-template-columns: 1fr; } }

/* ─── LEFT: Form panel ─── */
.vcp-form {
    background: linear-gradient(180deg, #0F2B6B 0%, #163C8A 100%);
    padding: 40px 40px 36px;
    display: flex; flex-direction: column; gap: 0;
    position: relative; overflow: hidden;
}
/* Dot-grid overlay */
.vcp-form::before {
    content: '';
    position: absolute; inset: 0;
    background-image: radial-gradient(rgba(255,255,255,.05) 1px, transparent 1px);
    background-size: 24px 24px;
    pointer-events: none;
}
/* Subtle SMS watermark */
.vcp-form::after {
    content: 'SMS';
    position: absolute; bottom: -16px; right: -10px;
    font-size: 180px; font-weight: 900; line-height: 1;
    color: rgba(255,255,255,.03); letter-spacing: -8px;
    pointer-events: none; user-select: none; z-index: 0;
}
.vcp-form > * { position: relative; z-index: 1; }

/* Shield icon tile */
.vcp-shield {
    width: 52px; height: 52px; border-radius: 14px; margin-bottom: 20px;
    background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.18);
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 4px 16px rgba(0,0,0,.2);
}
.vcp-form-title { font-size: 22px; font-weight: 900; color: #fff; margin: 0 0 8px; line-height: 1.2; }
.vcp-form-sub   { font-size: 13px; color: rgba(255,255,255,.6); margin: 0 0 28px; line-height: 1.7; }

/* Fields */
.vcp-field       { margin-bottom: 12px; }
.vcp-field-label {
    display: block; font-size: 10.5px; font-weight: 800;
    text-transform: uppercase; letter-spacing: .75px;
    color: rgba(255,255,255,.42); margin-bottom: 6px;
}
.vcp-input {
    width: 100%; padding: 12px 14px;
    border-radius: 10px; border: 1.5px solid rgba(255,255,255,.18);
    background: rgba(255,255,255,.08); color: #fff;
    font-size: 14px; font-family: inherit; outline: none;
    transition: border-color .14s, background .14s;
}
.vcp-input::placeholder { color: rgba(255,255,255,.3); }
.vcp-input:focus {
    border-color: rgba(255,255,255,.55);
    background: rgba(255,255,255,.14);
    box-shadow: 0 0 0 3px rgba(255,255,255,.06);
}
.vcp-input.mono { font-family: 'SFMono-Regular', Consolas, monospace; font-size: 13.5px; letter-spacing: .4px; }

.vcp-btn {
    width: 100%; padding: 13px;
    background: linear-gradient(135deg, #ffffff, #dbeafe);
    color: #042C53; border: none; border-radius: 10px;
    font-size: 15px; font-weight: 900; font-family: inherit; cursor: pointer;
    display: flex; align-items: center; justify-content: center; gap: 9px;
    margin-top: 6px;
    box-shadow: 0 6px 20px rgba(0,0,0,.25);
    transition: transform .14s, box-shadow .14s;
}
.vcp-btn:hover  { transform: translateY(-1px); box-shadow: 0 10px 28px rgba(0,0,0,.35); }
.vcp-btn:active { transform: none; }

/* Compact timeline */
.vcp-divider { border: none; border-top: 1px solid rgba(255,255,255,.1); margin: 24px 0 20px; }
.vcp-tl-label { font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: .8px; color: rgba(255,255,255,.3); margin-bottom: 14px; }
.vcp-tl {
    display: flex; flex-direction: column; gap: 0;
}
.vcp-tl-row  { display: flex; align-items: center; gap: 12px; }
.vcp-tl-num  {
    width: 24px; height: 24px; border-radius: 50%; flex-shrink: 0;
    background: rgba(255,255,255,.12); border: 1px solid rgba(255,255,255,.2);
    display: flex; align-items: center; justify-content: center;
    font-size: 11px; font-weight: 900; color: #fff;
}
.vcp-tl-text  { font-size: 12.5px; color: rgba(255,255,255,.58); font-weight: 600; }
.vcp-tl-line  { width: 1px; height: 14px; background: rgba(255,255,255,.12); margin-left: 11.5px; }

/* Form trust badges */
.vcp-form-badges { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 20px; }
.vcp-fbadge {
    display: inline-flex; align-items: center; gap: 5px;
    background: rgba(255,255,255,.07); border: 1px solid rgba(255,255,255,.13);
    border-radius: 20px; padding: 5px 11px;
    font-size: 11px; font-weight: 700; color: rgba(255,255,255,.5);
}

/* ─── RIGHT: Result panel ─── */
.vcp-result {
    background: #fff;
    background-image: linear-gradient(180deg, #fff 0%, #fafbfd 100%);
    display: flex; align-items: center; justify-content: center;
    padding: 40px 44px;
    border-left: 1px solid rgba(0,0,0,.06);
    border-bottom: 1px solid rgba(0,0,0,.04);
}
@media(max-width: 960px) { .vcp-result { padding: 36px 24px; border-left: none; border-top: 1px solid rgba(0,0,0,.06); } }

.vcp-result-inner { width: 100%; max-width: 680px; }

/* ── Idle / empty state ── */
.vcp-idle { text-align: center; padding: 20px 0; }
.vcp-idle-icon-wrap {
    width: 88px; height: 88px; border-radius: 26px; margin: 0 auto 22px;
    background: linear-gradient(135deg, #f0f4ff, #dbeafe);
    border: 1px solid #bfdbfe;
    display: flex; align-items: center; justify-content: center;
}
.vcp-idle h3 { font-size: 22px; font-weight: 900; color: #111827; margin: 0 0 8px; }
.vcp-idle p  { font-size: 14px; color: #9ca3af; margin: 0 0 28px; line-height: 1.7; max-width: 380px; margin-left: auto; margin-right: auto; }

.vcp-idle-preview {
    border: 1.5px dashed #e2e8f0; border-radius: 14px; padding: 20px;
    background: #f8fafc; text-align: left; margin-bottom: 20px;
}
.vcp-idle-preview-hd { font-size: 10.5px; font-weight: 800; text-transform: uppercase; letter-spacing: .7px; color: #d1d5db; margin-bottom: 14px; display: flex; align-items: center; gap: 8px; }
.vcp-idle-preview-hd::before { content: ''; display: block; width: 100%; height: 1px; background: #e9ecf0; }
.vcp-idle-preview-hd::after  { content: ''; display: block; width: 100%; height: 1px; background: #e9ecf0; }
.vcp-idle-fields { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
.vcp-idle-field-sk {
    background: #e9ecf0; border-radius: 6px; height: 14px; width: 60%;
    margin-bottom: 6px;
}
.vcp-idle-field-sk.wide { width: 90%; }
.vcp-idle-field-sk.val  { height: 20px; background: #d1d5db; border-radius: 7px; }

.vcp-idle-formats {
    display: flex; gap: 8px; justify-content: center; flex-wrap: wrap;
}
.vcp-idle-format {
    background: #f0f4ff; border: 1px solid #bfdbfe;
    border-radius: 8px; padding: 6px 14px;
    font-size: 12px; font-weight: 700; color: #042C53;
    font-family: 'SFMono-Regular', Consolas, monospace; letter-spacing: .3px;
}

/* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
   2. VERIFIED CREDENTIAL CARD
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
.vcp-credential {
    border-radius: 18px; overflow: hidden;
    box-shadow: 0 20px 64px rgba(15,122,67,.18), 0 4px 16px rgba(0,0,0,.08);
    width: 100%;
}

/* Green top banner */
.vcp-cred-banner {
    background: linear-gradient(135deg, #0F7A43 0%, #18A05E 100%);
    padding: 26px 30px;
    display: flex; align-items: center; justify-content: space-between;
    position: relative; overflow: hidden;
}
/* Large checkmark watermark in banner */
.vcp-cred-banner::after {
    content: '';
    position: absolute; right: -30px; top: -30px;
    width: 160px; height: 160px;
    background: url("data:image/svg+xml,%3Csvg viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Ccircle cx='50' cy='50' r='46' fill='none' stroke='rgba(255,255,255,.07)' stroke-width='4'/%3E%3Cpolyline points='28,50 44,66 72,34' fill='none' stroke='rgba(255,255,255,.07)' stroke-width='6' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E") center/contain no-repeat;
    pointer-events: none;
}
.vcp-banner-left { display: flex; align-items: center; gap: 16px; }
.vcp-banner-seal {
    width: 52px; height: 52px; border-radius: 50%; flex-shrink: 0;
    border: 2px solid rgba(255,255,255,.7);
    background: transparent;
    display: flex; align-items: center; justify-content: center;
}
.vcp-cred-banner-text { flex: 1; min-width: 0; }
.vcp-cred-title { font-size: 19px; font-weight: 900; color: #fff; margin: 0 0 2px; }
.vcp-cred-sub   { font-size: 12.5px; color: rgba(255,255,255,.65); margin: 0; }
.vcp-cred-seal {
    flex-shrink: 0; text-align: center; position: relative; z-index: 1;
}
.vcp-seal-ring {
    width: 64px; height: 64px; border-radius: 50%;
    border: 2px solid rgba(255,255,255,.7);
    display: flex; align-items: center; justify-content: center;
    background: transparent;
}
.vcp-seal-text { font-size: 8px; font-weight: 900; color: rgba(255,255,255,.7); text-transform: uppercase; letter-spacing: .8px; margin-top: 5px; }

/* Certificate detail grid — 3 columns */
.vcp-cred-body { background: #fff; }
.vcp-cred-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
}
@media(max-width: 680px) { .vcp-cred-grid { grid-template-columns: 1fr 1fr; } }
@media(max-width: 400px) { .vcp-cred-grid { grid-template-columns: 1fr; } }

.vcp-cred-cell {
    padding: 18px 22px;
    border-right: 1px solid #f0f2f5;
    border-bottom: 1px solid #f0f2f5;
}
.vcp-cred-cell:nth-child(3n)   { border-right: none; }
@media(max-width: 680px) {
    .vcp-cred-cell:nth-child(2n)  { border-right: none; }
    .vcp-cred-cell:nth-child(3n)  { border-right: 1px solid #f0f2f5; }
    .vcp-cred-cell:nth-child(3n+1){ border-right: none; }
}
.vcp-cred-cell.span2 { grid-column: span 2; }
.vcp-cred-cell.span3 { grid-column: 1/-1; border-right: none; }
.vcp-cred-cell-no-border { border-bottom: none !important; }

.vcp-cell-label {
    font-size: 10px; font-weight: 800; text-transform: uppercase;
    letter-spacing: .65px; color: #9ca3af;
    display: flex; align-items: center; gap: 5px; margin-bottom: 5px;
}
.vcp-cell-label svg { opacity: .55; }
.vcp-cell-val { font-size: 14px; font-weight: 800; color: #111827; line-height: 1.3; }
.vcp-cell-val.mono { font-family: 'SFMono-Regular', Consolas, monospace; font-size: 13px; color: #163C8A; letter-spacing: .3px; }
.vcp-cell-val.lg   { font-size: 16px; }

/* Action buttons */
.vcp-cred-actions {
    display: flex; align-items: center; gap: 8px; flex-wrap: wrap;
    padding: 16px 22px;
    background: #f8fafc; border-top: 1px solid #f0f2f5;
}
.vcp-action-btn {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 9px 16px; border-radius: 9px; font-size: 13px; font-weight: 700;
    cursor: pointer; font-family: inherit; text-decoration: none;
    border: 1.5px solid transparent; transition: all .14s; white-space: nowrap;
}
.vcp-action-btn.primary {
    background: #042C53; color: #fff; border-color: #042C53;
}
.vcp-action-btn.primary:hover { background: #163C8A; border-color: #163C8A; }
.vcp-action-btn.ghost  {
    background: #fff; color: #374151; border-color: #e5e7eb;
}
.vcp-action-btn.ghost:hover { border-color: #042C53; color: #042C53; }

/* Verified footer strip */
.vcp-cred-footer {
    background: linear-gradient(135deg, #f0fdf4, #dcfce7);
    border-top: 1px solid #bbf7d0;
    padding: 12px 22px;
    display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
    justify-content: space-between;
}
.vcp-cred-footer-l { display: flex; align-items: center; gap: 8px; }
.vcp-cred-footer-l svg { color: #16a34a; flex-shrink: 0; }
.vcp-cred-footer-text { font-size: 12px; color: #166534; font-weight: 700; }
.vcp-cred-footer-text small { font-weight: 500; opacity: .75; }

/* Trust badges beside/below verified card */
.vcp-trust-badges {
    display: flex; gap: 8px; flex-wrap: wrap; margin-top: 16px;
}
.vcp-tbadge {
    display: inline-flex; align-items: center; gap: 6px;
    background: #fff; border: 1px solid #e9ecf0; border-radius: 20px;
    padding: 6px 13px; font-size: 11.5px; font-weight: 700; color: #374151;
    box-shadow: 0 1px 3px rgba(0,0,0,.04);
}
.vcp-tbadge svg { color: #16a34a; }

/* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
   3. ALERT STATES
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
.vcp-alert {
    background: #fff; border-radius: 16px;
    border: 1.5px solid #e9ecf0;
    padding: 24px 28px;
    display: flex; align-items: flex-start; gap: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,.05);
    width: 100%;
}
.vcp-alert.warning { border-color: #fbbf24; background: #fffbeb; }
.vcp-alert.danger  { border-color: #fca5a5; background: #fff1f2; }
.vcp-alert.info    { border-color: #bfdbfe; background: #eff6ff; }
.vcp-alert-icon {
    width: 46px; height: 46px; border-radius: 13px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
}
.vcp-alert.warning .vcp-alert-icon { background: #fef3c7; }
.vcp-alert.danger  .vcp-alert-icon { background: #fee2e2; }
.vcp-alert.info    .vcp-alert-icon { background: #dbeafe; }
.vcp-alert-title { font-size: 17px; font-weight: 900; margin: 0 0 7px; }
.vcp-alert.warning .vcp-alert-title { color: #92400e; }
.vcp-alert.danger  .vcp-alert-title { color: #991b1b; }
.vcp-alert.info    .vcp-alert-title { color: #1e40af; }
.vcp-alert-body { font-size: 13.5px; color: #6b7280; line-height: 1.75; margin: 0; }
.vcp-alert-body a { color: #042C53; font-weight: 700; text-decoration: none; }
.vcp-alert-body a:hover { text-decoration: underline; }

/* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
   4. STATISTICS BAR
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
.vcp-stats {
    background: #fff;
    border-top: 1px solid #f0f2f5;
    border-bottom: 1px solid #f0f2f5;
    padding: 36px 24px;
}
.vcp-stats-grid {
    display: grid; grid-template-columns: repeat(4, 1fr);
    max-width: 900px; margin: 0 auto; gap: 0;
}
@media(max-width: 640px) { .vcp-stats-grid { grid-template-columns: 1fr 1fr; gap: 0; } }

.vcp-stat {
    text-align: center; padding: 16px 20px;
    border-right: 1px solid #f0f2f5;
}
.vcp-stat:last-child { border-right: none; }
@media(max-width: 640px) {
    .vcp-stat:nth-child(2n)  { border-right: none; }
    .vcp-stat:nth-child(1),
    .vcp-stat:nth-child(2)   { border-bottom: 1px solid #f0f2f5; }
}
.vcp-stat-val {
    font-size: 34px; font-weight: 900; color: #042C53; line-height: 1;
    margin-bottom: 4px; letter-spacing: -1px;
}
.vcp-stat-label { font-size: 12.5px; color: #9ca3af; font-weight: 600; }

/* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
   5. WHY TRUST SMS CREDENTIALS
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
.vcp-why {
    background: #f8fafc;
    padding: 52px 24px;
    position: relative; overflow: hidden;
}
/* Section watermark */
.vcp-why::before {
    content: 'REGISTRY';
    position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
    font-size: 120px; font-weight: 900; color: rgba(15,36,112,.025);
    white-space: nowrap; pointer-events: none; user-select: none;
    letter-spacing: -2px;
}
.vcp-why-inner { max-width: 980px; margin: 0 auto; position: relative; z-index: 1; }
.vcp-section-hd { text-align: center; margin-bottom: 36px; }
.vcp-section-eyebrow {
    display: inline-block; font-size: 11px; font-weight: 800;
    text-transform: uppercase; letter-spacing: 1px; color: #163C8A;
    background: #eff6ff; border: 1px solid #bfdbfe;
    padding: 4px 14px; border-radius: 20px; margin-bottom: 10px;
}
.vcp-section-title { font-size: 24px; font-weight: 900; color: #111827; margin: 0 0 8px; }
.vcp-section-sub   { font-size: 14px; color: #6b7280; margin: 0; }

.vcp-why-grid {
    display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px;
}
@media(max-width: 640px) { .vcp-why-grid { grid-template-columns: 1fr 1fr; gap: 10px; } }
@media(max-width: 380px) { .vcp-why-grid { grid-template-columns: 1fr; } }

.vcp-why-card {
    background: #fff; border: 1px solid #e9ecf0; border-radius: 14px;
    padding: 20px 18px; text-align: center;
    transition: box-shadow .15s, border-color .15s, transform .15s;
}
.vcp-why-card:hover {
    box-shadow: 0 6px 24px rgba(15,36,112,.09);
    border-color: #bfdbfe; transform: translateY(-2px);
}
.vcp-why-icon {
    width: 46px; height: 46px; border-radius: 13px;
    background: linear-gradient(135deg, #f0f4ff, #dbeafe);
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 13px;
}
.vcp-why-card h4 { font-size: 13.5px; font-weight: 800; color: #111827; margin: 0 0 5px; }
.vcp-why-card p  { font-size: 12px; color: #9ca3af; margin: 0; line-height: 1.6; }

/* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
   6. RECOGNITION BAR
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
.vcp-recognition {
    background: #fff;
    border-top: 1px solid #f0f2f5;
    padding: 32px 24px;
}
.vcp-recog-inner { max-width: 900px; margin: 0 auto; }
.vcp-recog-label {
    text-align: center; font-size: 10.5px; font-weight: 800;
    text-transform: uppercase; letter-spacing: 1px; color: #d1d5db;
    margin-bottom: 20px;
}
.vcp-recog-items {
    display: flex; align-items: center; justify-content: center;
    gap: 10px; flex-wrap: wrap;
}
.vcp-recog-item {
    display: flex; align-items: center; gap: 8px;
    background: #f8fafc; border: 1px solid #e9ecf0; border-radius: 8px;
    padding: 8px 16px; font-size: 12.5px; font-weight: 700; color: #6b7280;
    transition: border-color .13s, color .13s;
}
.vcp-recog-item:hover { border-color: #bfdbfe; color: #042C53; }
.vcp-recog-item svg { color: #9ca3af; flex-shrink: 0; }
</style>

<div class="vcp">

{{-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
     HERO SPLIT — 380px Form / remaining Result
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ --}}
<div class="vcp-hero-wrap">
<div class="vcp-hero-container">
<div class="vcp-split">

    {{-- ── LEFT: Form Panel ── --}}
    <div class="vcp-form">

        <div class="vcp-shield">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.88)" stroke-width="1.8">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                <polyline points="9 12 11 14 15 10" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>

        <div class="vcp-form-title">Certificate Verification</div>
        <div class="vcp-form-sub">Verify the authenticity of training certificates issued through SMS Training Academy.</div>

        <form method="GET" action="{{ route('public.verify-certificate') }}">
            <div class="vcp-field">
                <label class="vcp-field-label">Participant Name</label>
                <input type="text" name="name" class="vcp-input"
                       value="{{ request('name') }}"
                       placeholder="Full name as on certificate"
                       autocomplete="off" required>
            </div>
            <div class="vcp-field">
                <label class="vcp-field-label">Certificate Number</label>
                <input type="text" name="cert" class="vcp-input mono"
                       value="{{ request('cert') }}"
                       placeholder="e.g. SMS-TC-2026-0001"
                       autocomplete="off" spellcheck="false" required>
            </div>
            <button type="submit" class="vcp-btn">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                Verify Certificate
            </button>
        </form>

        <hr class="vcp-divider">

        <div class="vcp-tl-label">How it works</div>
        <div class="vcp-tl">
            <div class="vcp-tl-row">
                <div class="vcp-tl-num">1</div>
                <div class="vcp-tl-text">Enter participant name exactly as printed</div>
            </div>
            <div class="vcp-tl-line"></div>
            <div class="vcp-tl-row">
                <div class="vcp-tl-num">2</div>
                <div class="vcp-tl-text">Enter the certificate number from document</div>
            </div>
            <div class="vcp-tl-line"></div>
            <div class="vcp-tl-row">
                <div class="vcp-tl-num">3</div>
                <div class="vcp-tl-text">View the authenticated credential record</div>
            </div>
        </div>

        <div class="vcp-form-badges">
            <span class="vcp-fbadge">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                Secure
            </span>
            <span class="vcp-fbadge">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                Instant
            </span>
            <span class="vcp-fbadge">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                Global
            </span>
            <span class="vcp-fbadge">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                Official
            </span>
        </div>

    </div>

    {{-- ── RIGHT: Result Panel ── --}}
    <div class="vcp-result">
    <div class="vcp-result-inner">

        @if(request('cert') && request('name'))

            @if($result && $result['found'])
            {{-- ✅ VERIFIED CREDENTIAL CARD --}}
            <div class="vcp-credential">

                <div class="vcp-cred-banner">
                    <div class="vcp-banner-left">
                        <div class="vcp-banner-seal">
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="9" r="6" stroke="rgba(255,255,255,.9)" stroke-width="1.8"/>
                                <path d="M9 15l-2 7 5-3 5 3-2-7" stroke="rgba(255,255,255,.9)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <div class="vcp-cred-banner-text">
                            <div class="vcp-cred-title">Authentic Credential Record</div>
                            <div class="vcp-cred-sub">Registered in Official SMS Training Academy Registry</div>
                        </div>
                    </div>
                    <div class="vcp-cred-seal">
                        <div class="vcp-seal-ring">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" stroke="rgba(255,255,255,.85)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                <polyline points="9 12 11 14 15 10" stroke="rgba(255,255,255,.85)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <div class="vcp-seal-text">Official</div>
                    </div>
                </div>

                <div class="vcp-cred-body">
                    <div class="vcp-cred-grid">

                        <div class="vcp-cred-cell">
                            <div class="vcp-cell-label">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="5" y="2" width="14" height="20" rx="2"/><line x1="9" y1="7" x2="15" y2="7"/></svg>
                                Certificate No.
                            </div>
                            <div class="vcp-cell-val mono">{{ $result['cert_number'] }}</div>
                        </div>

                        <div class="vcp-cred-cell">
                            <div class="vcp-cell-label">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                Issue Date
                            </div>
                            <div class="vcp-cell-val">
                                {{ $result['issue_date'] ? \Carbon\Carbon::parse($result['issue_date'])->format('d M Y') : '—' }}
                            </div>
                        </div>

                        <div class="vcp-cred-cell">
                            <div class="vcp-cell-label">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg>
                                Certificate Type
                            </div>
                            <div class="vcp-cell-val">{{ $result['type'] }} Certificate</div>
                        </div>

                        <div class="vcp-cred-cell span2" style="border-right:none;">
                            <div class="vcp-cell-label">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                Participant Name
                            </div>
                            <div class="vcp-cell-val lg">{{ $result['name'] }}</div>
                        </div>

                        @if(!empty($result['batch']) && $result['batch'] !== '—')
                        <div class="vcp-cred-cell">
                            <div class="vcp-cell-label">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/></svg>
                                Batch / Cohort
                            </div>
                            <div class="vcp-cell-val">{{ $result['batch'] }}</div>
                        </div>
                        @endif

                        <div class="vcp-cred-cell span3" style="border-right:none;">
                            <div class="vcp-cell-label">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                                Course / Programme
                            </div>
                            <div class="vcp-cell-val lg">{{ $result['course'] }}</div>
                        </div>

                        @if(!empty($result['company']) && $result['company'] !== '—')
                        <div class="vcp-cred-cell span3 vcp-cred-cell-no-border" style="border-right:none;">
                            <div class="vcp-cell-label">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
                                Organisation
                            </div>
                            <div class="vcp-cell-val">{{ $result['company'] }}</div>
                        </div>
                        @endif

                    </div>

                    {{-- Credential Actions --}}
                    <div class="vcp-cred-actions">
                        <button class="vcp-action-btn primary" onclick="window.print()">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                            Print
                        </button>
                        <button class="vcp-action-btn ghost" onclick="shareCredential()">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
                            Share
                        </button>
                        <button class="vcp-action-btn ghost" onclick="copyVerifyLink()">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                            <span id="vcp-copy-txt">Copy Link</span>
                        </button>
                    </div>

                    <div class="vcp-cred-footer">
                        <div class="vcp-cred-footer-l">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                <polyline points="9 12 11 14 15 10"/>
                            </svg>
                            <span class="vcp-cred-footer-text">
                                Verified by SMS Training Academy
                                <small> · Sustainable Management System Inc. · New York, USA</small>
                            </span>
                        </div>
                        <span style="font-size:11px;color:#9ca3af;font-weight:600;">{{ now()->format('d M Y, h:i A') }}</span>
                    </div>
                </div>
            </div>

            {{-- Trust badges below card --}}
            <div class="vcp-trust-badges">
                <span class="vcp-tbadge">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    Official Registry
                </span>
                <span class="vcp-tbadge">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 9h.01M9 15h.01M15 9h.01M15 15h.01"/></svg>
                    QR Verified
                </span>
                <span class="vcp-tbadge">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    Secure Record
                </span>
                <span class="vcp-tbadge">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                    Tamper Resistant
                </span>
            </div>

            @elseif($result && !$result['found'] && ($result['name_mismatch'] ?? false))
            <div class="vcp-alert warning">
                <div class="vcp-alert-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2">
                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                        <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                    </svg>
                </div>
                <div>
                    <div class="vcp-alert-title">Name Does Not Match</div>
                    <p class="vcp-alert-body">
                        Certificate <strong>{{ request('cert') }}</strong> exists in our registry, but the name entered does not match our records. Please check the exact spelling as printed on your certificate.<br><br>
                        Need assistance? <a href="mailto:training@smscert.com">training@smscert.com</a>
                    </p>
                </div>
            </div>

            @else
            <div class="vcp-alert danger">
                <div class="vcp-alert-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
                    </svg>
                </div>
                <div>
                    <div class="vcp-alert-title">Certificate Not Found</div>
                    <p class="vcp-alert-body">
                        No certificate matching <strong>"{{ request('cert') }}"</strong> was found in our registry. Verify the certificate number is entered exactly as printed (e.g. <em>SMS-TC-2026-0001</em>).<br><br>
                        Contact <a href="mailto:training@smscert.com">training@smscert.com</a> if you believe this is an error.
                    </p>
                </div>
            </div>
            @endif

        @elseif(request('cert') && !request('name'))
        <div class="vcp-alert info">
            <div class="vcp-alert-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#378ADD" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
            </div>
            <div>
                <div class="vcp-alert-title">Participant Name Required</div>
                <p class="vcp-alert-body">Please enter the participant's full name as printed on the certificate, then click Verify Certificate.</p>
            </div>
        </div>

        @else
        {{-- ── Idle state ── --}}
        <div class="vcp-idle">
            <div class="vcp-idle-icon-wrap">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#042C53" stroke-width="1.5">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    <polyline points="9 12 11 14 15 10" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <h3>Credential Verification Portal</h3>
            <p>Enter the participant name and certificate number on the left — the authenticated credential record will appear here instantly.</p>

            <div class="vcp-idle-preview">
                <div class="vcp-idle-preview-hd">Result Preview</div>
                <div class="vcp-idle-fields">
                    <div>
                        <div class="vcp-idle-field-sk"></div>
                        <div class="vcp-idle-field-sk val wide"></div>
                    </div>
                    <div>
                        <div class="vcp-idle-field-sk"></div>
                        <div class="vcp-idle-field-sk val"></div>
                    </div>
                    <div>
                        <div class="vcp-idle-field-sk"></div>
                        <div class="vcp-idle-field-sk val wide"></div>
                    </div>
                    <div>
                        <div class="vcp-idle-field-sk"></div>
                        <div class="vcp-idle-field-sk val"></div>
                    </div>
                </div>
            </div>

            <div class="vcp-idle-formats">
                <span class="vcp-idle-format">SMS-TC-2026-XXXX</span>
                <span class="vcp-idle-format">SMS-EL-2026-XXXX</span>
                <span class="vcp-idle-format">SMS-CO-2026-XXXX</span>
            </div>
        </div>
        @endif

    </div>
    </div>

</div>{{-- /vcp-split --}}
</div>{{-- /vcp-hero-container --}}
</div>{{-- /vcp-hero-wrap --}}

{{-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
     STATISTICS BAR
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ --}}
<div class="vcp-stats">
    <div class="vcp-stats-grid">
        <div class="vcp-stat">
            <div class="vcp-stat-val" data-target="25000" data-suffix="+">25,000+</div>
            <div class="vcp-stat-label">Certificates Issued</div>
        </div>
        <div class="vcp-stat">
            <div class="vcp-stat-val" data-target="120" data-suffix="+">120+</div>
            <div class="vcp-stat-label">Courses Delivered</div>
        </div>
        <div class="vcp-stat">
            <div class="vcp-stat-val" data-target="35" data-suffix="+">35+</div>
            <div class="vcp-stat-label">Countries Served</div>
        </div>
        <div class="vcp-stat">
            <div class="vcp-stat-val" data-target="98" data-suffix="%">98%</div>
            <div class="vcp-stat-label">Verification Success Rate</div>
        </div>
    </div>
</div>

{{-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
     WHY TRUST SMS CREDENTIALS
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ --}}
<div class="vcp-why">
<div class="vcp-why-inner">
    <div class="vcp-section-hd">
        <div class="vcp-section-eyebrow">Credential Security</div>
        <div class="vcp-section-title">Why Trust SMS Credentials?</div>
        <div class="vcp-section-sub">Every certificate is backed by enterprise-grade security and a permanent public registry.</div>
    </div>
    <div class="vcp-why-grid">

        <div class="vcp-why-card">
            <div class="vcp-why-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#042C53" stroke-width="1.8">
                    <rect x="3" y="3" width="18" height="18" rx="2"/>
                    <path d="M9 9h.01M9 15h.01M15 9h.01M15 15h.01"/>
                </svg>
            </div>
            <h4>QR Linked</h4>
            <p>Every certificate carries a unique QR code linked directly to this verification portal.</p>
        </div>

        <div class="vcp-why-card">
            <div class="vcp-why-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#042C53" stroke-width="1.8">
                    <ellipse cx="12" cy="5" rx="9" ry="3"/>
                    <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/>
                    <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/>
                </svg>
            </div>
            <h4>Permanent Registry</h4>
            <p>Credentials are stored in an immutable registry — never deleted, always accessible.</p>
        </div>

        <div class="vcp-why-card">
            <div class="vcp-why-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#042C53" stroke-width="1.8">
                    <rect x="5" y="2" width="14" height="20" rx="2"/>
                    <line x1="9" y1="7" x2="15" y2="7"/>
                    <line x1="9" y1="11" x2="15" y2="11"/>
                    <line x1="9" y1="15" x2="13" y2="15"/>
                </svg>
            </div>
            <h4>Unique Certificate ID</h4>
            <p>Each certificate carries a unique sequential ID that cannot be duplicated or forged.</p>
        </div>

        <div class="vcp-why-card">
            <div class="vcp-why-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#042C53" stroke-width="1.8">
                    <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
                </svg>
            </div>
            <h4>Instant Verification</h4>
            <p>Results return in milliseconds — no wait time, no login required, no account needed.</p>
        </div>

        <div class="vcp-why-card">
            <div class="vcp-why-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#042C53" stroke-width="1.8">
                    <rect x="3" y="11" width="18" height="11" rx="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
            </div>
            <h4>Tamper Resistant</h4>
            <p>Any modification to a certificate immediately invalidates its verification signature.</p>
        </div>

        <div class="vcp-why-card">
            <div class="vcp-why-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#042C53" stroke-width="1.8">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="2" y1="12" x2="22" y2="12"/>
                    <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                </svg>
            </div>
            <h4>Global Access</h4>
            <p>Employers and auditors worldwide can verify credentials 24/7 from any device.</p>
        </div>

    </div>
</div>
</div>

{{-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
     RECOGNITION BAR
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ --}}
<div class="vcp-recognition">
<div class="vcp-recog-inner">
    <div class="vcp-recog-label">Training programmes aligned with</div>
    <div class="vcp-recog-items">

        <div class="vcp-recog-item">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg>
            APSCA
        </div>
        <div class="vcp-recog-item">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            SLCP
        </div>
        <div class="vcp-recog-item">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            Higg FEM
        </div>
        <div class="vcp-recog-item">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="5" y="2" width="14" height="20" rx="2"/><line x1="9" y1="7" x2="15" y2="7"/></svg>
            ISO 9001 · 14001 · 45001
        </div>
        <div class="vcp-recog-item">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
            NEBOSH
        </div>
        <div class="vcp-recog-item">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10"/></svg>
            Professional Development
        </div>

    </div>
</div>
</div>

</div>{{-- /vcp --}}

<script>
/* ── Share credential ── */
function shareCredential() {
    if (navigator.share) {
        navigator.share({
            title: 'SMS Training Academy — Verified Certificate',
            text: 'Verify this training certificate issued by SMS Training Academy',
            url: window.location.href
        }).catch(() => {});
    } else {
        copyVerifyLink();
    }
}

/* ── Copy verification URL ── */
function copyVerifyLink() {
    navigator.clipboard.writeText(window.location.href).then(function () {
        var el = document.getElementById('vcp-copy-txt');
        if (el) { el.textContent = 'Copied!'; setTimeout(function(){ el.textContent = 'Copy Link'; }, 2000); }
    }).catch(function () {
        var el = document.getElementById('vcp-copy-txt');
        if (el) el.textContent = 'Copy Link';
    });
}

/* ── Counter animation ── */
(function () {
    var counters = document.querySelectorAll('.vcp-stat-val[data-target]');
    if (!counters.length) return;
    var obs = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (!entry.isIntersecting) return;
            var el      = entry.target;
            var target  = parseInt(el.dataset.target, 10);
            var suffix  = el.dataset.suffix || '';
            var current = 0;
            var step    = Math.max(1, Math.ceil(target / 60));
            var timer   = setInterval(function () {
                current = Math.min(current + step, target);
                el.textContent = current.toLocaleString() + suffix;
                if (current >= target) clearInterval(timer);
            }, 16);
            obs.unobserve(el);
        });
    }, { threshold: 0.5 });
    counters.forEach(function (c) { obs.observe(c); });
}());
</script>

@endsection
