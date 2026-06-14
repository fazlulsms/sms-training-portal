@extends('layouts.public')

@section('page-title', 'Verify Certificate')
@section('seo-title', 'Verify Training Certificate — SMS Training Academy')
@section('seo-desc', 'Instantly verify the authenticity of an SMS Training Academy certificate by entering the certificate number and recipient name.')

@section('content')
<style>
.verify-hero { background:linear-gradient(135deg,#0f172a,#1e3a8a); padding:70px 0; color:#fff; text-align:center; }
.verify-hero h1 { font-size:36px; font-weight:900; margin:0 0 10px; }
.verify-hero p  { font-size:16px; opacity:.75; margin:0 0 36px; }
.verify-input-row { display:flex; gap:10px; max-width:520px; margin:0 auto; }
.verify-input {
    flex:1; padding:14px 18px; border-radius:12px; border:1.5px solid rgba(255,255,255,.3);
    background:rgba(255,255,255,.12); color:#fff; font-size:15px; font-family:inherit; outline:none;
    letter-spacing:.5px;
}
.verify-input::placeholder { color:rgba(255,255,255,.45); }
.verify-input:focus { border-color:rgba(255,255,255,.7); background:rgba(255,255,255,.2); }
.verify-btn {
    padding:14px 24px; background:#fff; color:#1e3a8a; border:none; border-radius:12px;
    font-weight:800; font-size:15px; cursor:pointer; font-family:inherit; white-space:nowrap;
}
.verify-hint { font-size:12.5px; opacity:.5; margin-top:10px; }

.verify-body { padding:48px 0 60px; }
.verify-main { max-width:640px; margin:0 auto; }

/* Result card */
.result-card { border-radius:16px; padding:28px; margin-bottom:24px; }
.result-card.valid   { background:#f0fdf4; border:2px solid #86efac; }
.result-card.invalid { background:#fff1f2; border:2px solid #fca5a5; }

.result-status { display:flex; align-items:center; gap:12px; margin-bottom:18px; }
.result-icon { font-size:32px; }
.result-title { font-size:20px; font-weight:900; }
.result-title.valid   { color:#16a34a; }
.result-title.invalid { color:#dc2626; }
.result-sub { font-size:13.5px; color:#6b7280; margin-top:2px; }

.cert-detail-grid { display:grid; grid-template-columns:1fr 1fr; gap:0; border:1px solid #e9ecf0; border-radius:12px; overflow:hidden; background:#fff; }
.cert-cell { padding:13px 16px; border-bottom:1px solid #f0f2f5; }
.cert-cell:nth-last-child(-n+2) { border-bottom:none; }
.cert-label { font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:.5px; color:#9ca3af; margin-bottom:3px; }
.cert-value { font-size:14.5px; font-weight:700; color:#111827; }

/* Info boxes */
.info-box { background:#fff; border:1px solid #e9ecf0; border-radius:14px; padding:28px; margin-bottom:20px; }
.info-box-title { font-size:15px; font-weight:800; color:#111827; margin:0 0 12px; }
.info-box-text { font-size:14px; color:#6b7280; line-height:1.7; margin:0; }
.info-steps { list-style:none; padding:0; margin:0; counter-reset:steps; }
.info-steps li { counter-increment:steps; display:flex; gap:12px; align-items:flex-start; padding:8px 0; font-size:14px; color:#374151; }
.info-steps li::before { content:counter(steps); width:22px; height:22px; background:#1e3a8a; color:#fff; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:900; flex-shrink:0; margin-top:1px; }
</style>

<div class="verify-hero">
    <div class="pub-container">
        <div style="font-size:52px;margin-bottom:14px;">🏆</div>
        <h1>Certificate Verification</h1>
        <p>Verify certificates issued by SMS Training Academy — enter your name and certificate number</p>
        <form method="GET" action="{{ route('public.verify-certificate') }}" style="max-width:540px;margin:0 auto;">
            <div class="verify-input-row" style="margin-bottom:10px;">
                <input type="text" name="name" class="verify-input"
                       value="{{ request('name') }}"
                       placeholder="Your full name (as on certificate)"
                       autocomplete="off">
            </div>
            <div class="verify-input-row">
                <input type="text" name="cert" class="verify-input"
                       value="{{ request('cert') }}"
                       placeholder="Certificate No. e.g. SMS-TC-2026-0001"
                       autocomplete="off" spellcheck="false">
                <button type="submit" class="verify-btn">🔍 Verify</button>
            </div>
            <p class="verify-hint">Both fields are required. Name must match the certificate (at least 50% similarity).</p>
        </form>
    </div>
</div>

<div class="pub-container">
<div class="verify-body">
<div class="verify-main">

    @if(request('cert') && request('name'))

    @if($result && $result['found'])
    {{-- ✅ VERIFIED --}}
    <div class="result-card valid">
        <div class="result-status">
            <div class="result-icon">✅</div>
            <div>
                <div class="result-title valid">Certificate Verified</div>
                <div class="result-sub">This certificate is authentic and issued by SMS Training Academy</div>
            </div>
        </div>
        <div class="cert-detail-grid">
            <div class="cert-cell">
                <div class="cert-label">Certificate No.</div>
                <div class="cert-value" style="font-family:monospace;">{{ $result['cert_number'] }}</div>
            </div>
            <div class="cert-cell">
                <div class="cert-label">Issued To</div>
                <div class="cert-value">{{ $result['name'] }}</div>
            </div>
            <div class="cert-cell">
                <div class="cert-label">Course / Programme</div>
                <div class="cert-value">{{ $result['course'] }}</div>
            </div>
            <div class="cert-cell">
                <div class="cert-label">Issue Date</div>
                <div class="cert-value">{{ $result['issue_date'] ? \Carbon\Carbon::parse($result['issue_date'])->format('d M Y') : '—' }}</div>
            </div>
            @if($result['company'] && $result['company'] !== '—')
            <div class="cert-cell">
                <div class="cert-label">Company / Organisation</div>
                <div class="cert-value">{{ $result['company'] }}</div>
            </div>
            @endif
            @if($result['batch'] && $result['batch'] !== '—')
            <div class="cert-cell">
                <div class="cert-label">Batch</div>
                <div class="cert-value">{{ $result['batch'] }}</div>
            </div>
            @endif
            <div class="cert-cell" style="grid-column:1/-1;">
                <div class="cert-label">Certificate Type</div>
                <div class="cert-value">{{ $result['type'] }} Training Certificate</div>
            </div>
        </div>
        <div style="margin-top:14px;padding:10px 14px;background:#dcfce7;border-radius:8px;font-size:13px;color:#166534;font-weight:600;">
            🔒 Verified by SMS Training Academy · Sustainable Management System Inc.
        </div>
    </div>

    @elseif($result && !$result['found'] && ($result['name_mismatch'] ?? false))
    {{-- ⚠️ Cert exists but name doesn't match --}}
    <div class="result-card" style="background:#fffbeb;border:2px solid #fbbf24;">
        <div class="result-status">
            <div class="result-icon">⚠️</div>
            <div>
                <div class="result-title" style="color:#b45309;">Name Does Not Match</div>
                <div class="result-sub">A certificate with this number exists, but the name you entered does not match our records.</div>
            </div>
        </div>
        <p style="font-size:14px;color:#6b7280;margin:0;line-height:1.7;">
            Please check the name exactly as printed on the certificate and try again.
            If you believe this is an error, contact us at
            <a href="mailto:training@smscert.com" style="color:#1e3a8a;font-weight:700;">training@smscert.com</a>.
        </p>
    </div>

    @else
    {{-- ❌ Not found --}}
    <div class="result-card invalid">
        <div class="result-status">
            <div class="result-icon">❌</div>
            <div>
                <div class="result-title invalid">Certificate Not Found</div>
                <div class="result-sub">No certificate matching "<strong>{{ request('cert') }}</strong>" was found in our records.</div>
            </div>
        </div>
        <p style="font-size:14px;color:#6b7280;margin:0;line-height:1.7;">
            Please check the certificate number and try again. If you believe this is an error, contact us at
            <a href="mailto:training@smscert.com" style="color:#1e3a8a;font-weight:700;">training@smscert.com</a>.
        </p>
    </div>
    @endif

    @elseif(request('cert') && !request('name'))
    <div class="result-card" style="background:#eff6ff;border:2px solid #bfdbfe;">
        <div class="result-status">
            <div class="result-icon">ℹ️</div>
            <div>
                <div class="result-title" style="color:#1e40af;">Name Required</div>
                <div class="result-sub">Please enter your full name (as printed on the certificate) along with the certificate number.</div>
            </div>
        </div>
    </div>
    @endif

    {{-- Info panels --}}
    <div class="info-box">
        <h3 class="info-box-title">🔍 How to Verify</h3>
        <ol class="info-steps">
            <li>Enter your <strong>full name</strong> exactly as it appears on your certificate.</li>
            <li>Locate the <strong>certificate number</strong> printed on your certificate document and enter it in the second field.</li>
            <li>Click "Verify" — your name must match at least 50% to confirm authenticity.</li>
        </ol>
    </div>

    <div class="info-box">
        <h3 class="info-box-title">📋 About Our Certificates</h3>
        <p class="info-box-text">
            SMS Training Academy issues internationally recognised certificates upon successful completion of training programs. Each certificate carries a unique verification code registered in our secure database. Certificates include:
        </p>
        <ul style="margin:12px 0 0;padding-left:18px;font-size:14px;color:#374151;line-height:1.8;">
            <li>Participant full name</li>
            <li>Course or program name</li>
            <li>Completion date and batch information</li>
            <li>Unique certificate verification number</li>
            <li>Authorised signatures</li>
        </ul>
    </div>

    <div style="background:linear-gradient(135deg,#f0f4ff,#dbeafe);border:1px solid #bfdbfe;border-radius:14px;padding:22px;text-align:center;">
        <div style="font-size:24px;margin-bottom:8px;">💬</div>
        <div style="font-size:16px;font-weight:800;color:#111827;margin-bottom:6px;">Need Help?</div>
        <p style="font-size:14px;color:#6b7280;margin:0 0 14px;line-height:1.6;">Can't verify a certificate or have a question about our programs?</p>
        <a href="mailto:training@smscert.com" class="pub-enroll-btn" style="display:inline-block;">📧 Contact Us</a>
    </div>

</div>
</div>
</div>
@endsection
