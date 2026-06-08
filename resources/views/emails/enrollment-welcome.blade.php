<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Training Enrollment Confirmation</title>
    <style>
        body       { margin:0; padding:0; background:#f4f6f9; font-family: 'Segoe UI', Arial, sans-serif; color:#1f2937; }
        .wrapper   { max-width:620px; margin:32px auto; background:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 4px 20px rgba(0,0,0,.08); }
        .header    { background:linear-gradient(135deg,#1e3a8a 0%,#2563eb 100%); padding:32px 40px; color:#ffffff; }
        .header h1 { margin:0 0 4px; font-size:22px; font-weight:800; letter-spacing:-0.3px; }
        .header p  { margin:0; font-size:13px; opacity:.8; }
        .body      { padding:32px 40px; }
        .greeting  { font-size:16px; font-weight:600; margin-bottom:12px; }
        .intro     { font-size:14px; line-height:1.65; color:#4b5563; margin-bottom:24px; }
        .card      { background:#f8faff; border:1px solid #dbeafe; border-radius:10px; padding:20px 24px; margin-bottom:20px; }
        .card h3   { margin:0 0 14px; font-size:13px; font-weight:800; text-transform:uppercase; letter-spacing:.6px; color:#1e3a8a; }
        .row       { display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid #e5e7eb; font-size:14px; }
        .row:last-child { border-bottom:none; }
        .row .label{ color:#6b7280; font-weight:500; width:45%; }
        .row .value{ color:#111827; font-weight:600; width:52%; text-align:right; word-break:break-all; }
        .cred-card { background:#fffbeb; border:1px solid #fde68a; border-radius:10px; padding:20px 24px; margin-bottom:20px; }
        .cred-card h3 { margin:0 0 14px; font-size:13px; font-weight:800; text-transform:uppercase; letter-spacing:.6px; color:#92400e; }
        .cred-card .row .value { color:#92400e; }
        .password-box { background:#fff; border:2px solid #fbbf24; border-radius:8px; padding:10px 16px; font-family:monospace; font-size:16px; font-weight:700; color:#92400e; letter-spacing:1px; margin-top:4px; display:inline-block; }
        .btn-wrap  { text-align:center; margin:28px 0 12px; }
        .btn       { background:linear-gradient(135deg,#1e3a8a,#2563eb); color:#ffffff !important; text-decoration:none; padding:13px 32px; border-radius:8px; font-size:15px; font-weight:700; display:inline-block; }
        .note      { font-size:12.5px; color:#6b7280; background:#f9fafb; border-radius:8px; padding:14px 18px; margin-bottom:24px; line-height:1.6; }
        .footer    { background:#f1f5f9; padding:20px 40px; text-align:center; font-size:12px; color:#9ca3af; }
        .footer strong { color:#6b7280; }
    </style>
</head>
<body>
<div class="wrapper">

    {{-- Header --}}
    <div class="header">
        <h1>Training Enrollment Confirmation</h1>
        <p>Sustainable Management System Bangladesh</p>
    </div>

    <div class="body">

        <div class="greeting">Dear {{ $enrollment->participant_name }},</div>

        <div class="intro">
            Greetings from <strong>Sustainable Management System Bangladesh</strong>.<br>
            You have been successfully enrolled in the following training course. Your learner account has been created and your login credentials are included below.
        </div>

        {{-- Enrollment Details --}}
        <div class="card">
            <h3>Enrollment Details</h3>
            <div class="row">
                <span class="label">Course</span>
                <span class="value">{{ $enrollment->course->name ?? 'N/A' }}</span>
            </div>
            <div class="row">
                <span class="label">Enrollment ID</span>
                <span class="value">{{ $enrollment->enrollment_number ?? '#' . $enrollment->id }}</span>
            </div>
            @if($enrollment->payment_status === 'paid' || $enrollment->payment_status === 'manual_approved')
            <div class="row">
                <span class="label">Access Status</span>
                <span class="value" style="color:#059669;">✓ Unlocked</span>
            </div>
            @else
            <div class="row">
                <span class="label">Access Status</span>
                <span class="value" style="color:#d97706;">Pending Payment</span>
            </div>
            @endif
            @if($enrollment->expires_at)
            <div class="row">
                <span class="label">Access Expires</span>
                <span class="value">{{ \Carbon\Carbon::parse($enrollment->expires_at)->format('d M Y') }}</span>
            </div>
            @endif
        </div>

        {{-- Login Credentials --}}
        <div class="cred-card">
            <h3>🔑 Your Login Credentials</h3>
            <div class="row">
                <span class="label">Portal URL</span>
                <span class="value">
                    <a href="{{ config('app.url') }}" style="color:#1e3a8a;">{{ config('app.url') }}</a>
                </span>
            </div>
            <div class="row">
                <span class="label">Email Address</span>
                <span class="value">{{ $enrollment->email }}</span>
            </div>
            <div class="row" style="align-items:center;">
                <span class="label">Temporary Password</span>
                <span class="value">
                    <span class="password-box">{{ $plainPassword }}</span>
                </span>
            </div>
        </div>

        <div class="note">
            ⚠️ <strong>Important:</strong> This is a temporary password. Please log in and change it immediately after your first login. Go to <em>My Profile → Change Password</em>.
        </div>

        {{-- CTA Button --}}
        <div class="btn-wrap">
            <a href="{{ config('app.url') }}/login" class="btn">
                🎓 &nbsp;Access My Learning Portal
            </a>
        </div>

        <p style="font-size:13px; color:#6b7280; margin-top:24px;">
            If you require any assistance, please contact the SMS Training Team by replying to this email.
        </p>

        <p style="font-size:14px; color:#374151;">
            Thank you.<br>
            <strong>Sustainable Management System Bangladesh</strong>
        </p>

    </div>

    <div class="footer">
        <strong>Sustainable Management System Bangladesh</strong><br>
        This is an automated message. Please do not reply directly to this email.
    </div>

</div>
</body>
</html>
