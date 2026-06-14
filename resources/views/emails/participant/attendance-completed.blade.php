@extends('emails.layouts.master')
@section('subject-strip') ✅ Attendance Confirmed @endsection
@section('subject-theme', 'green')

@section('content')
<p class="em-greeting" style="font-size:16px;font-weight:600;color:#111827;margin:0 0 16px;">Dear <strong>{{ $enrollment->full_name ?? $enrollment->participant_name }}</strong>,</p>

<p style="margin:0 0 14px;line-height:1.7;color:#374151;">
  Your attendance for <strong>{{ $courseName }}</strong> has been recorded as <strong>Attended</strong>. Thank you for participating!
</p>

<div class="em-info-card" style="background:#f0f6ff;border-left:4px solid #2563eb;border-radius:8px;padding:18px 22px;margin:20px 0;">
  <table style="width:100%;border-collapse:collapse;">
    <tr class="em-row" style="border-bottom:1px solid #dbeafe;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Course / Programme</td>
      <td class="em-val" style="padding:7px 0;color:#111827;font-size:13px;font-weight:700;vertical-align:top;">{{ $courseName }}</td>
    </tr>
    <tr class="em-row" style="border-bottom:1px solid #dbeafe;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Attendance Status</td>
      <td class="em-val" style="padding:7px 0;font-size:13px;vertical-align:top;">
        <span class="em-badge-green" style="display:inline-block;background:#dcfce7;color:#15803d;padding:2px 10px;border-radius:12px;font-size:12px;font-weight:700;">{{ $enrollment->attendance_status ?? 'Attended' }}</span>
      </td>
    </tr>
    <tr class="em-row" style="border-bottom:none;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Date Recorded</td>
      <td class="em-val" style="padding:7px 0;color:#111827;font-size:13px;font-weight:700;vertical-align:top;">{{ now()->format('d M Y') }}</td>
    </tr>
  </table>
</div>

<div class="em-alert em-alert-green" style="background:#f0fdf4;border:1px solid #86efac;color:#15803d;border-radius:8px;padding:14px 18px;margin:16px 0;font-size:13px;line-height:1.6;">
  🏆 Upon successful completion of all requirements, your certificate will be issued and you will be notified via email.
</div>

<p style="margin:0 0 14px;line-height:1.7;color:#374151;">
  If you have any questions, please contact us at <a href="mailto:training@smscert.com" style="color:#2563eb;">training@smscert.com</a>.
</p>
@endsection
