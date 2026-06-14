@extends('emails.layouts.master')
@section('subject-strip') 🎓 Course Access Activated @endsection
@section('subject-theme', 'green')

@section('content')
<p class="em-greeting" style="font-size:16px;font-weight:600;color:#111827;margin:0 0 16px;">Dear <strong>{{ $enrollment->participant_name }}</strong>,</p>

<p style="margin:0 0 14px;line-height:1.7;color:#374151;">
  Great news! Your access to <strong>{{ $courseName }}</strong> has been <strong>activated</strong>. You can now log in and start learning!
</p>

<div class="em-info-card" style="background:#f0f6ff;border-left:4px solid #2563eb;border-radius:8px;padding:18px 22px;margin:20px 0;">
  <table style="width:100%;border-collapse:collapse;">
    <tr class="em-row" style="border-bottom:1px solid #dbeafe;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Course</td>
      <td class="em-val" style="padding:7px 0;color:#111827;font-size:13px;font-weight:700;vertical-align:top;">{{ $courseName }}</td>
    </tr>
    <tr class="em-row" style="border-bottom:{{ $tempPassword ? '1px solid #dbeafe' : 'none' }};">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Access Valid Until</td>
      <td class="em-val" style="padding:7px 0;color:#111827;font-size:13px;font-weight:700;vertical-align:top;">{{ $accessDays }}</td>
    </tr>
    @if($tempPassword)
    <tr class="em-row" style="border-bottom:1px solid #dbeafe;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Username</td>
      <td class="em-val" style="padding:7px 0;color:#111827;font-size:13px;font-weight:700;vertical-align:top;">{{ $enrollment->email }}</td>
    </tr>
    <tr class="em-row" style="border-bottom:none;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Temporary Password</td>
      <td class="em-val" style="padding:7px 0;font-size:13px;vertical-align:top;"><code style="background:#f3f4f6;padding:3px 8px;border-radius:4px;font-size:13px;font-family:'Courier New',monospace;color:#1e3a8a;font-weight:700;">{{ $tempPassword }}</code></td>
    </tr>
    @endif
  </table>
</div>

@if($tempPassword)
<div class="em-alert em-alert-yellow" style="background:#fffbeb;border:1px solid #fbbf24;color:#92400e;border-radius:8px;padding:14px 18px;margin:16px 0;font-size:13px;line-height:1.6;">
  ⚠️ Please log in and <strong>change your password immediately</strong> for security. Go to <em>My Profile → Change Password</em>.
</div>
@endif

<div class="em-btn-wrap" style="text-align:center;margin:24px 0;">
  <a href="{{ $loginUrl }}" class="em-btn em-btn-green" style="display:inline-block;background:#15803d;color:#ffffff !important;text-decoration:none;padding:13px 32px;border-radius:8px;font-size:14px;font-weight:700;">🚀 Start Learning Now</a>
</div>

<p style="margin:0 0 14px;line-height:1.7;color:#374151;">
  If you need any assistance, please contact us at <a href="mailto:training@smscert.com" style="color:#2563eb;">training@smscert.com</a>.
</p>
@endsection
