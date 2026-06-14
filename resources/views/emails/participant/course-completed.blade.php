@extends('emails.layouts.master')
@section('subject-strip') 🎉 Course Completed! @endsection
@section('subject-theme', 'green')

@section('content')
<p class="em-greeting" style="font-size:16px;font-weight:600;color:#111827;margin:0 0 16px;">Dear <strong>{{ $enrollment->full_name ?? $enrollment->participant_name }}</strong>,</p>

<p style="margin:0 0 14px;line-height:1.7;color:#374151;">
  Congratulations! You have successfully <strong>completed</strong> the following programme:
</p>

{{-- Course Hero --}}
<div class="em-highlight" style="background:#f0fdf4;border:2px solid #86efac;border-radius:10px;text-align:center;padding:24px;margin:20px 0;">
  <div class="em-highlight-eyebrow" style="font-size:11px;color:#6b7280;text-transform:uppercase;letter-spacing:0.8px;margin-bottom:8px;">Programme Completed</div>
  <div class="em-highlight-main" style="font-size:22px;font-weight:900;color:#15803d;margin:4px 0;">{{ $courseName }}</div>
</div>

<div class="em-info-card" style="background:#f0f6ff;border-left:4px solid #2563eb;border-radius:8px;padding:18px 22px;margin:20px 0;">
  <table style="width:100%;border-collapse:collapse;">
    <tr class="em-row" style="border-bottom:1px solid #dbeafe;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Completion Date</td>
      <td class="em-val" style="padding:7px 0;color:#111827;font-size:13px;font-weight:700;vertical-align:top;">{{ now()->format('d M Y') }}</td>
    </tr>
    <tr class="em-row" style="border-bottom:{{ isset($enrollment->certificate_number) && $enrollment->certificate_number ? '1px solid #dbeafe' : 'none' }};">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Completion Status</td>
      <td class="em-val" style="padding:7px 0;font-size:13px;vertical-align:top;">
        <span class="em-badge-green" style="display:inline-block;background:#dcfce7;color:#15803d;padding:2px 10px;border-radius:12px;font-size:12px;font-weight:700;">Completed ✓</span>
      </td>
    </tr>
    @if(isset($enrollment->certificate_number) && $enrollment->certificate_number)
    <tr class="em-row" style="border-bottom:none;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Certificate No.</td>
      <td class="em-val" style="padding:7px 0;color:#111827;font-size:13px;font-weight:700;vertical-align:top;font-family:'Courier New',monospace;">{{ $enrollment->certificate_number }}</td>
    </tr>
    @endif
  </table>
</div>

<div class="em-alert em-alert-green" style="background:#f0fdf4;border:1px solid #86efac;color:#15803d;border-radius:8px;padding:14px 18px;margin:16px 0;font-size:13px;line-height:1.6;">
  🏆 Your certificate will be processed and issued shortly. You will receive another email when it is ready for download.
</div>

<p style="margin:0 0 14px;line-height:1.7;color:#374151;">
  Thank you for completing this programme. We hope the training has been a valuable experience. If you have any questions, contact us at <a href="mailto:training@smscert.com" style="color:#2563eb;">training@smscert.com</a>.
</p>
@endsection
