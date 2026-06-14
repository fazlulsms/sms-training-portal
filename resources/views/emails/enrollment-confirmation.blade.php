@extends('emails.layouts.master')
@section('subject-strip') 📋 Enrollment Confirmed @endsection
@section('subject-theme', 'blue')

@section('content')
<p class="em-greeting" style="font-size:16px;font-weight:600;color:#111827;margin:0 0 16px;">Dear <strong>{{ $user->name }}</strong>,</p>

<p style="margin:0 0 14px;line-height:1.7;color:#374151;">
  Your enrollment has been successfully registered. We look forward to welcoming you to the training!
</p>

{{-- Enrollment Details --}}
<div class="em-info-card" style="background:#f0f6ff;border-left:4px solid #2563eb;border-radius:8px;padding:18px 22px;margin:20px 0;">
  <table style="width:100%;border-collapse:collapse;">
    <tr class="em-row" style="border-bottom:1px solid #dbeafe;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Course</td>
      <td class="em-val" style="padding:7px 0;color:#111827;font-size:13px;font-weight:700;vertical-align:top;">{{ $courseName }}</td>
    </tr>
    <tr class="em-row" style="border-bottom:1px solid #dbeafe;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Batch</td>
      <td class="em-val" style="padding:7px 0;color:#111827;font-size:13px;font-weight:700;vertical-align:top;">{{ $batchCode ?? 'TBA' }}</td>
    </tr>
    <tr class="em-row" style="border-bottom:1px solid #dbeafe;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Start Date</td>
      <td class="em-val" style="padding:7px 0;color:#111827;font-size:13px;font-weight:700;vertical-align:top;">{{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d M Y') : 'TBA' }}</td>
    </tr>
    <tr class="em-row" style="border-bottom:none;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Reference</td>
      <td class="em-val" style="padding:7px 0;color:#111827;font-size:13px;font-weight:700;vertical-align:top;">{{ $enrollment->enrollment_code ?? 'ENR-' . str_pad($enrollment->id, 6, '0', STR_PAD_LEFT) }}</td>
    </tr>
  </table>
</div>

@if($tempPassword)
{{-- Credentials --}}
<div class="em-cred-card" style="background:#0f1e45;border-radius:10px;padding:20px 24px;margin:20px 0;">
  <p style="margin:0 0 14px;color:#bfdbfe;font-size:13px;font-weight:700;">🔑 Your Account Credentials</p>
  <span class="em-cred-label" style="color:#93c5fd;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;display:block;margin-bottom:2px;">Email Address</span>
  <span class="em-cred-val" style="color:#e2e8f0;font-size:14px;font-weight:600;display:block;margin-bottom:12px;">{{ $user->email }}</span>
  <span class="em-cred-label" style="color:#93c5fd;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;display:block;margin-bottom:6px;">Temporary Password</span>
  <span class="em-cred-pw" style="color:#fbbf24;font-family:'Courier New',Courier,monospace;font-size:16px;font-weight:700;letter-spacing:1px;display:inline-block;background:rgba(255,255,255,.08);padding:6px 14px;border-radius:6px;border:1px solid rgba(251,191,36,.3);">{{ $tempPassword }}</span>
  <p style="margin:12px 0 0;color:#64748b;font-size:11px;"><strong style="color:#93c5fd;">Please log in and change your password immediately.</strong></p>
</div>
<div class="em-btn-wrap" style="text-align:center;margin:24px 0;">
  <a href="{{ $loginUrl }}" class="em-btn" style="display:inline-block;background:#2563eb;color:#ffffff !important;text-decoration:none;padding:13px 32px;border-radius:8px;font-size:14px;font-weight:700;">🔐 Login to My Account</a>
</div>
@endif

<div class="em-alert em-alert-blue" style="background:#eff6ff;border:1px solid #bfdbfe;color:#1d4ed8;border-radius:8px;padding:14px 18px;margin:16px 0;font-size:13px;line-height:1.6;">
  💳 <strong>Payment Information:</strong> Our team will contact you with payment instructions within 24 hours.
</div>

<p style="margin:0 0 14px;line-height:1.7;color:#374151;">
  If you have any questions, please contact us at <a href="mailto:training@smscert.com" style="color:#2563eb;">training@smscert.com</a>. Thank you for choosing SMS Training Academy!
</p>
@endsection
