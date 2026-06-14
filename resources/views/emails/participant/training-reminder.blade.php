@extends('emails.layouts.master')
@section('subject-strip') ⏰ Training Reminder @endsection
@section('subject-theme', 'blue')

@section('content')
<p class="em-greeting" style="font-size:16px;font-weight:600;color:#111827;margin:0 0 16px;">Dear <strong>{{ $enrollment->full_name ?? $enrollment->participant_name }}</strong>,</p>

<p style="margin:0 0 14px;line-height:1.7;color:#374151;">
  This is a friendly reminder that your training session begins in <strong>{{ $daysAhead }} day(s)</strong>. Please make sure you are prepared and ready.
</p>

<div class="em-info-card" style="background:#f0f6ff;border-left:4px solid #2563eb;border-radius:8px;padding:18px 22px;margin:20px 0;">
  <table style="width:100%;border-collapse:collapse;">
    <tr class="em-row" style="border-bottom:1px solid #dbeafe;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Course / Programme</td>
      <td class="em-val" style="padding:7px 0;color:#111827;font-size:13px;font-weight:700;vertical-align:top;">{{ $courseName }}</td>
    </tr>
    <tr class="em-row" style="border-bottom:1px solid #dbeafe;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Batch</td>
      <td class="em-val" style="padding:7px 0;color:#111827;font-size:13px;font-weight:700;vertical-align:top;">{{ $schedule?->batch_code ?? '—' }}</td>
    </tr>
    <tr class="em-row" style="border-bottom:1px solid #dbeafe;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Date</td>
      <td class="em-val" style="padding:7px 0;color:#111827;font-size:13px;font-weight:700;vertical-align:top;"><strong>{{ $schedule?->start_date?->format('d M Y') ?? '—' }}</strong></td>
    </tr>
    <tr class="em-row" style="border-bottom:1px solid #dbeafe;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Time</td>
      <td class="em-val" style="padding:7px 0;color:#111827;font-size:13px;font-weight:700;vertical-align:top;">{{ $schedule?->start_time ?? '9:00 AM' }}</td>
    </tr>
    <tr class="em-row" style="border-bottom:none;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Venue / Mode</td>
      <td class="em-val" style="padding:7px 0;color:#111827;font-size:13px;font-weight:700;vertical-align:top;">{{ $schedule?->venue ?? $enrollment->selected_mode ?? '—' }}</td>
    </tr>
  </table>
</div>

<div class="em-alert em-alert-blue" style="background:#eff6ff;border:1px solid #bfdbfe;color:#1d4ed8;border-radius:8px;padding:14px 18px;margin:16px 0;font-size:13px;line-height:1.6;">
  📋 <strong>What to bring:</strong> Valid photo ID, pen/notebook, and any materials provided during registration.
</div>

<p style="margin:0 0 14px;line-height:1.7;color:#374151;">
  If you have any questions or concerns, please contact us at <a href="mailto:training@smscert.com" style="color:#2563eb;">training@smscert.com</a> well in advance of the training date.
</p>
@endsection
