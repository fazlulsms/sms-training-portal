@extends('emails.layouts.master')
@section('subject-strip') 🎓 Welcome to Your Learning Portal @endsection
@section('subject-theme', 'blue')

@section('content')
<p class="em-greeting" style="font-size:16px;font-weight:600;color:#111827;margin:0 0 16px;">Dear <strong>{{ $enrollment->participant_name }}</strong>,</p>

<p style="margin:0 0 14px;line-height:1.7;color:#374151;">
  Greetings from <strong>SMS Training Academy</strong>.<br>
  You have been successfully enrolled in the following training course. Your learner account has been created and your login credentials are included below.
</p>

{{-- Enrollment Details --}}
<div class="em-info-card" style="background:#f0f6ff;border-left:4px solid #2563eb;border-radius:8px;padding:18px 22px;margin:20px 0;">
  <table style="width:100%;border-collapse:collapse;">
    <tr class="em-row" style="border-bottom:1px solid #dbeafe;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Course</td>
      <td class="em-val" style="padding:7px 0;color:#111827;font-size:13px;font-weight:700;vertical-align:top;">{{ $enrollment->course->name ?? 'N/A' }}</td>
    </tr>
    <tr class="em-row" style="border-bottom:1px solid #dbeafe;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Enrollment ID</td>
      <td class="em-val" style="padding:7px 0;color:#111827;font-size:13px;font-weight:700;vertical-align:top;">{{ $enrollment->enrollment_number ?? '#' . $enrollment->id }}</td>
    </tr>
    <tr class="em-row" style="border-bottom:{{ $enrollment->expires_at ? '1px solid #dbeafe' : 'none' }};">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Access Status</td>
      <td class="em-val" style="padding:7px 0;font-size:13px;vertical-align:top;">
        @if(in_array($enrollment->payment_status ?? '', ['paid', 'manual_approved']))
          <span class="em-badge-green" style="display:inline-block;background:#dcfce7;color:#15803d;padding:2px 10px;border-radius:12px;font-size:12px;font-weight:700;">✓ Unlocked</span>
        @else
          <span class="em-badge-amber" style="display:inline-block;background:#fef3c7;color:#b45309;padding:2px 10px;border-radius:12px;font-size:12px;font-weight:700;">Pending Payment</span>
        @endif
      </td>
    </tr>
    @if($enrollment->expires_at)
    <tr class="em-row" style="border-bottom:none;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Access Expires</td>
      <td class="em-val" style="padding:7px 0;color:#111827;font-size:13px;font-weight:700;vertical-align:top;">{{ \Carbon\Carbon::parse($enrollment->expires_at)->format('d M Y') }}</td>
    </tr>
    @endif
  </table>
</div>

{{-- Login Credentials --}}
<div class="em-cred-card" style="background:#0f1e45;border-radius:10px;padding:20px 24px;margin:20px 0;">
  <p style="margin:0 0 14px;color:#bfdbfe;font-size:13px;font-weight:700;">🔑 Your Login Credentials</p>
  <span class="em-cred-label" style="color:#93c5fd;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;display:block;margin-bottom:2px;">Portal URL</span>
  <span class="em-cred-val" style="color:#e2e8f0;font-size:14px;font-weight:600;display:block;margin-bottom:12px;"><a href="{{ config('app.url') }}" style="color:#bfdbfe;">{{ config('app.url') }}</a></span>
  <span class="em-cred-label" style="color:#93c5fd;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;display:block;margin-bottom:2px;">Email Address</span>
  <span class="em-cred-val" style="color:#e2e8f0;font-size:14px;font-weight:600;display:block;margin-bottom:12px;">{{ $enrollment->email }}</span>
  <span class="em-cred-label" style="color:#93c5fd;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;display:block;margin-bottom:6px;">Temporary Password</span>
  <span class="em-cred-pw" style="color:#fbbf24;font-family:'Courier New',Courier,monospace;font-size:16px;font-weight:700;letter-spacing:1px;display:inline-block;background:rgba(255,255,255,.08);padding:6px 14px;border-radius:6px;border:1px solid rgba(251,191,36,.3);">{{ $plainPassword }}</span>
</div>

<div class="em-alert em-alert-yellow" style="background:#fffbeb;border:1px solid #fbbf24;color:#92400e;border-radius:8px;padding:14px 18px;margin:16px 0;font-size:13px;line-height:1.6;">
  ⚠️ <strong>Important:</strong> This is a temporary password. Please log in and change it immediately after your first login. Go to <em>My Profile → Change Password</em>.
</div>

<div class="em-btn-wrap" style="text-align:center;margin:24px 0;">
  <a href="{{ config('app.url') }}/login" class="em-btn em-btn-green" style="display:inline-block;background:#15803d;color:#ffffff !important;text-decoration:none;padding:13px 32px;border-radius:8px;font-size:14px;font-weight:700;">🎓 Access My Learning Portal</a>
</div>

<p style="margin:0 0 14px;line-height:1.7;color:#374151;">
  If you require any assistance, please contact the SMS Training Academy team by replying to this email or writing to <a href="mailto:training@smscert.com" style="color:#2563eb;">training@smscert.com</a>.
</p>
@endsection
