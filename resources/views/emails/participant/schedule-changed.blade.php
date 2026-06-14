@extends('emails.layouts.master')
@section('subject-strip') 📅 Schedule Updated @endsection
@section('subject-theme', 'purple')

@section('content')
<p class="em-greeting" style="font-size:16px;font-weight:600;color:#111827;margin:0 0 16px;">Dear <strong>{{ $enrollment->full_name ?? $enrollment->participant_name }}</strong>,</p>

<p style="margin:0 0 14px;line-height:1.7;color:#374151;">
  Please note that the schedule for <strong>{{ $courseName }}</strong> has been updated. Below are the latest details:
</p>

<div class="em-info-card" style="background:#f0f6ff;border-left:4px solid #2563eb;border-radius:8px;padding:18px 22px;margin:20px 0;">
  <table style="width:100%;border-collapse:collapse;">
    <tr class="em-row" style="border-bottom:1px solid #dbeafe;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Course</td>
      <td class="em-val" style="padding:7px 0;color:#111827;font-size:13px;font-weight:700;vertical-align:top;">{{ $courseName }}</td>
    </tr>
    <tr class="em-row" style="border-bottom:1px solid #dbeafe;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Batch Code</td>
      <td class="em-val" style="padding:7px 0;color:#111827;font-size:13px;font-weight:700;vertical-align:top;">{{ $schedule?->batch_code ?? '—' }}</td>
    </tr>
    <tr class="em-row" style="border-bottom:1px solid #dbeafe;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">New Start Date</td>
      <td class="em-val" style="padding:7px 0;color:#111827;font-size:13px;font-weight:700;vertical-align:top;"><strong>{{ $schedule?->start_date?->format('d M Y') ?? '—' }}</strong></td>
    </tr>
    <tr class="em-row" style="border-bottom:1px solid #dbeafe;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">End Date</td>
      <td class="em-val" style="padding:7px 0;color:#111827;font-size:13px;font-weight:700;vertical-align:top;">{{ $schedule?->end_date?->format('d M Y') ?? '—' }}</td>
    </tr>
    <tr class="em-row" style="border-bottom:1px solid #dbeafe;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Venue</td>
      <td class="em-val" style="padding:7px 0;color:#111827;font-size:13px;font-weight:700;vertical-align:top;">{{ $schedule?->venue ?? '—' }}</td>
    </tr>
    <tr class="em-row" style="border-bottom:none;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Mode</td>
      <td class="em-val" style="padding:7px 0;color:#111827;font-size:13px;font-weight:700;vertical-align:top;">{{ $enrollment->selected_mode ?? '—' }}</td>
    </tr>
  </table>
</div>

@if(!empty($changes))
<div class="em-alert em-alert-blue" style="background:#eff6ff;border:1px solid #bfdbfe;color:#1d4ed8;border-radius:8px;padding:14px 18px;margin:16px 0;font-size:13px;line-height:1.6;">
  <strong>What changed:</strong>
  <ul style="margin:6px 0 0;padding-left:18px;">
    @foreach($changes as $field => $change)
    <li style="margin-bottom:4px;">{{ $field }}: <s style="color:#6b7280;">{{ $change['from'] }}</s> &rarr; <strong>{{ $change['to'] }}</strong></li>
    @endforeach
  </ul>
</div>
@endif

<p style="margin:0 0 14px;line-height:1.7;color:#374151;">
  Please update your calendar accordingly. If you have any conflicts, contact us as soon as possible at <a href="mailto:training@smscert.com" style="color:#2563eb;">training@smscert.com</a>.
</p>
@endsection
