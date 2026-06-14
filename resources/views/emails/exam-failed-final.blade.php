@extends('emails.layouts.master')
@section('subject-strip') 📋 Knowledge Test — Attempt Limit Reached @endsection
@section('subject-theme', 'red')

@section('content')
<p class="em-greeting" style="font-size:16px;font-weight:600;color:#111827;margin:0 0 16px;">Dear <strong>{{ $emailData['participant_name'] }}</strong>,</p>

<p style="margin:0 0 14px;line-height:1.7;color:#374151;">
  Thank you for your efforts in completing the knowledge test for <strong>{{ $emailData['course_name'] }}</strong>.
  We regret to inform you that you have not achieved the minimum passing score and you have used all available attempts.
</p>

<div class="em-info-card" style="background:#f0f6ff;border-left:4px solid #2563eb;border-radius:8px;padding:18px 22px;margin:20px 0;">
  <table style="width:100%;border-collapse:collapse;">
    <tr class="em-row" style="border-bottom:1px solid #dbeafe;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Exam</td>
      <td class="em-val" style="padding:7px 0;color:#111827;font-size:13px;font-weight:700;vertical-align:top;">{{ $emailData['exam_title'] }}</td>
    </tr>
    <tr class="em-row" style="border-bottom:1px solid #dbeafe;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Best Score</td>
      <td class="em-val" style="padding:7px 0;color:#111827;font-size:13px;font-weight:700;vertical-align:top;">{{ $emailData['score'] }} / {{ $emailData['total_marks'] }} marks</td>
    </tr>
    <tr class="em-row" style="border-bottom:1px solid #dbeafe;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Best Percentage</td>
      <td class="em-val" style="padding:7px 0;color:#111827;font-size:13px;font-weight:700;vertical-align:top;">{{ $emailData['percentage'] }}%</td>
    </tr>
    <tr class="em-row" style="border-bottom:none;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Result</td>
      <td class="em-val" style="padding:7px 0;font-size:13px;vertical-align:top;">
        <span class="em-badge-red" style="display:inline-block;background:#fee2e2;color:#b91c1c;padding:2px 10px;border-radius:12px;font-size:12px;font-weight:700;">Attempt Limit Reached</span>
      </td>
    </tr>
  </table>
</div>

<div class="em-alert em-alert-red" style="background:#fef2f2;border:1px solid #fca5a5;color:#b91c1c;border-radius:8px;padding:14px 18px;margin:16px 0;font-size:13px;line-height:1.6;">
  ⚠️ Unfortunately, you have used all available attempts. Your certificate cannot be issued at this time.
</div>

<p style="margin:0 0 14px;line-height:1.7;color:#374151;">
  If you believe there is an error or would like to discuss this further, please contact us at
  <a href="mailto:training@smscert.com" style="color:#2563eb;">training@smscert.com</a>.
</p>
@endsection
