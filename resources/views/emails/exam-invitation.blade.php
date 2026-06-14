@extends('emails.layouts.master')
@section('subject-strip') 📋 Knowledge Test Invitation @endsection
@section('subject-theme', 'blue')

@section('content')
<p class="em-greeting" style="font-size:16px;font-weight:600;color:#111827;margin:0 0 16px;">Dear <strong>{{ $emailData['participant_name'] }}</strong>,</p>

<p style="margin:0 0 14px;line-height:1.7;color:#374151;">
  Thank you for attending and participating in <strong>{{ $emailData['course_name'] }}</strong>.
  We appreciate your commitment to professional development.
</p>

<p style="margin:0 0 14px;line-height:1.7;color:#374151;">
  @if(!empty($emailData['is_reminder']))
    <strong>This is a reminder</strong> — you have not yet completed your knowledge test.
  @else
    As part of the training programme, you are required to complete a <strong>knowledge test</strong>.
  @endif
  Please complete the exam at your earliest convenience using the link below.
</p>

<div class="em-info-card" style="background:#f0f6ff;border-left:4px solid #2563eb;border-radius:8px;padding:18px 22px;margin:20px 0;">
  <table style="width:100%;border-collapse:collapse;">
    <tr class="em-row" style="border-bottom:1px solid #dbeafe;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Exam Title</td>
      <td class="em-val" style="padding:7px 0;color:#111827;font-size:13px;font-weight:700;vertical-align:top;">{{ $emailData['exam_title'] }}</td>
    </tr>
    <tr class="em-row" style="border-bottom:1px solid #dbeafe;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Pass Mark</td>
      <td class="em-val" style="padding:7px 0;color:#111827;font-size:13px;font-weight:700;vertical-align:top;">{{ $emailData['pass_mark_text'] }}</td>
    </tr>
    <tr class="em-row" style="border-bottom:1px solid #dbeafe;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Allowed Attempts</td>
      <td class="em-val" style="padding:7px 0;color:#111827;font-size:13px;font-weight:700;vertical-align:top;">{{ $emailData['allowed_attempts'] }}</td>
    </tr>
    <tr class="em-row" style="border-bottom:none;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Time Limit</td>
      <td class="em-val" style="padding:7px 0;color:#111827;font-size:13px;font-weight:700;vertical-align:top;">{{ $emailData['time_limit'] }}</td>
    </tr>
  </table>
</div>

<div class="em-btn-wrap" style="text-align:center;margin:24px 0;">
  <a href="{{ $emailData['exam_url'] }}" class="em-btn" style="display:inline-block;background:#2563eb;color:#ffffff !important;text-decoration:none;padding:13px 32px;border-radius:8px;font-size:14px;font-weight:700;">📝 Start Knowledge Test</a>
</div>

@if(!empty($emailData['cert_note']))
<div class="em-alert em-alert-blue" style="background:#eff6ff;border:1px solid #bfdbfe;color:#1d4ed8;border-radius:8px;padding:14px 18px;margin:16px 0;font-size:13px;line-height:1.6;">
  🏆 {{ $emailData['cert_note'] }}
</div>
@endif

<div class="em-alert em-alert-yellow" style="background:#fffbeb;border:1px solid #fbbf24;color:#92400e;border-radius:8px;padding:14px 18px;margin:16px 0;font-size:13px;line-height:1.6;">
  ⚠️ <strong>Important:</strong> This exam link is personal and unique to you. Please do not share it with others.
</div>

<p style="margin:0 0 14px;line-height:1.7;color:#374151;">
  If you have any questions, please contact us at <a href="mailto:training@smscert.com" style="color:#2563eb;">training@smscert.com</a>.
</p>
@endsection
