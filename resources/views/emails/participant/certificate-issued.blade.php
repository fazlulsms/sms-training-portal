@extends('emails.layouts.master')
@section('subject-strip') 🏆 Certificate Issued @endsection
@section('subject-theme', 'amber')

@section('content')
<p class="em-greeting" style="font-size:16px;font-weight:600;color:#111827;margin:0 0 16px;">Dear <strong>{{ $enrollment->full_name ?? $enrollment->participant_name }}</strong>,</p>

<p style="margin:0 0 14px;line-height:1.7;color:#374151;">
  We are pleased to inform you that your <strong>Certificate of Completion</strong> has been officially issued for the following programme:
</p>

{{-- Certificate Hero --}}
<div class="em-highlight" style="background:#fffbeb;border:2px solid #fbbf24;border-radius:10px;text-align:center;padding:24px;margin:20px 0;">
  <div class="em-highlight-eyebrow" style="font-size:11px;color:#6b7280;text-transform:uppercase;letter-spacing:0.8px;margin-bottom:8px;">Certificate of Completion</div>
  <div class="em-highlight-main" style="font-size:20px;font-weight:900;color:#b45309;margin:4px 0;">{{ $courseName }}</div>
  @if($certNumber)
  <div class="em-highlight-sub" style="font-size:13px;color:#6b7280;margin-top:8px;">
    Certificate No: <strong style="color:#111827;font-family:'Courier New',monospace;font-size:14px;">{{ $certNumber }}</strong>
  </div>
  @endif
</div>

<div class="em-info-card" style="background:#f0f6ff;border-left:4px solid #2563eb;border-radius:8px;padding:18px 22px;margin:20px 0;">
  <table style="width:100%;border-collapse:collapse;">
    <tr class="em-row" style="border-bottom:1px solid #dbeafe;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Issue Date</td>
      <td class="em-val" style="padding:7px 0;color:#111827;font-size:13px;font-weight:700;vertical-align:top;">{{ $enrollment->certificate_issue_date ? \Carbon\Carbon::parse($enrollment->certificate_issue_date)->format('d M Y') : now()->format('d M Y') }}</td>
    </tr>
    @if($certNumber)
    <tr class="em-row" style="border-bottom:1px solid #dbeafe;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Certificate No.</td>
      <td class="em-val" style="padding:7px 0;color:#111827;font-size:13px;font-weight:700;vertical-align:top;font-family:'Courier New',monospace;">{{ $certNumber }}</td>
    </tr>
    @endif
    <tr class="em-row" style="border-bottom:none;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Verify Online</td>
      <td class="em-val" style="padding:7px 0;font-size:13px;vertical-align:top;"><a href="{{ url('/verify') }}" style="color:#2563eb;">{{ url('/verify') }}</a></td>
    </tr>
  </table>
</div>

<div class="em-btn-wrap" style="text-align:center;margin:24px 0;">
  <a href="{{ $loginUrl }}" class="em-btn em-btn-amber" style="display:inline-block;background:#b45309;color:#ffffff !important;text-decoration:none;padding:13px 32px;border-radius:8px;font-size:14px;font-weight:700;">📥 Download Certificate</a>
</div>

<div class="em-alert em-alert-green" style="background:#f0fdf4;border:1px solid #86efac;color:#15803d;border-radius:8px;padding:14px 18px;margin:16px 0;font-size:13px;line-height:1.6;">
  ✅ Your certificate can be verified online at any time using your certificate number at <a href="{{ url('/verify') }}" style="color:#15803d;font-weight:700;">{{ url('/verify') }}</a>.
</div>

<p style="margin:0 0 14px;line-height:1.7;color:#374151;">
  Congratulations on your achievement! We hope this training has been valuable to you.
</p>
@endsection
