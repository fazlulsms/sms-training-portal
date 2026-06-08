@extends('layouts.public')

@section('page-title', 'Verify Certificate')
@section('seo-title', 'Verify Training Certificate — SMS Training Services')
@section('seo-desc', 'Verify the authenticity of an SMS Training Services certificate by entering the certificate number.')

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
        <p>Enter the certificate number to verify its authenticity</p>
        <form method="GET" action="{{ route('public.verify-certificate') }}">
            <div class="verify-input-row">
                <input type="text" name="cert" class="verify-input"
                       value="{{ request('cert') }}"
                       placeholder="e.g. SMS-2025-CERT-000123"
                       autocomplete="off" spellcheck="false">
                <button type="submit" class="verify-btn">🔍 Verify</button>
            </div>
            <p class="verify-hint">Certificate numbers are printed on the certificate document</p>
        </form>
    </div>
</div>

<div class="pub-container">
<div class="verify-body">
<div class="verify-main">

    @if(request('cert'))
    @php
        $certCode  = trim(request('cert'));
        // Try to find a participant certificate record
        $certificate = null;
        if (class_exists(\App\Models\Certificate::class)) {
            $certificate = \App\Models\Certificate::where('certificate_number', $certCode)->first();
        }
        // Fallback: search enrollment records for matching code
        if (!$certificate) {
            $enrollment = \App\Models\ElearningEnrollment::where('certificate_number', $certCode)->first()
                       ?? \App\Models\Enrollment::where('certificate_number', $certCode)->first();
        }
        $found = $certificate || (!empty($enrollment) && !empty($enrollment->certificate_number));
    @endphp

    @if($found)
    <div class="result-card valid">
        <div class="result-status">
            <div class="result-icon">✅</div>
            <div>
                <div class="result-title valid">Certificate Verified</div>
                <div class="result-sub">This certificate is authentic and issued by SMS Training Services</div>
            </div>
        </div>
        @php $record = $certificate ?? $enrollment; @endphp
        <div class="cert-detail-grid">
            <div class="cert-cell">
                <div class="cert-label">Certificate No.</div>
                <div class="cert-value">{{ $certCode }}</div>
            </div>
            <div class="cert-cell">
                <div class="cert-label">Issued To</div>
                <div class="cert-value">{{ $record->participant_name ?? $record->user?->name ?? '—' }}</div>
            </div>
            <div class="cert-cell">
                <div class="cert-label">Course</div>
                <div class="cert-value">{{ $record->course?->name ?? $record->trainingSchedule?->course?->name ?? '—' }}</div>
            </div>
            <div class="cert-cell">
                <div class="cert-label">Issue Date</div>
                <div class="cert-value">{{ $record->certificate_issued_at ? \Carbon\Carbon::parse($record->certificate_issued_at)->format('d M Y') : ($record->updated_at?->format('d M Y') ?? '—') }}</div>
            </div>
        </div>
    </div>
    @else
    <div class="result-card invalid">
        <div class="result-status">
            <div class="result-icon">❌</div>
            <div>
                <div class="result-title invalid">Certificate Not Found</div>
                <div class="result-sub">No certificate matching "<strong>{{ $certCode }}</strong>" was found in our records.</div>
            </div>
        </div>
        <p style="font-size:14px;color:#6b7280;margin:0;line-height:1.7;">
            Please check the certificate number and try again. If you believe this is an error, contact us at
            <a href="mailto:training@smscert.com" style="color:#1e3a8a;font-weight:700;">training@smscert.com</a>.
        </p>
    </div>
    @endif
    @endif

    {{-- Info panels --}}
    <div class="info-box">
        <h3 class="info-box-title">🔍 How to Verify</h3>
        <ol class="info-steps">
            <li>Locate the certificate number printed on your certificate document (usually at the bottom or back).</li>
            <li>Enter the full certificate number in the search box above.</li>
            <li>Click "Verify" to check its authenticity in our database.</li>
        </ol>
    </div>

    <div class="info-box">
        <h3 class="info-box-title">📋 About Our Certificates</h3>
        <p class="info-box-text">
            SMS Training Services issues certificates upon successful completion of training programs. Each certificate carries a unique verification code registered in our secure database. Certificates include:
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
