@extends('emails.layouts.master')
@section('subject-strip') 📋 Test Result — Please Try Again @endsection
@section('subject-theme', 'amber')

@section('content')
<p class="em-greeting" style="font-size:16px;font-weight:600;color:#111827;margin:0 0 16px;">Dear <strong>{{ $emailData['participant_name'] }}</strong>,</p>

<p style="margin:0 0 14px;line-height:1.7;color:#374151;">
  Thank you for completing the knowledge test for <strong>{{ $emailData['course_name'] }}</strong>.
  Unfortunately, you did not achieve the minimum passing score in this attempt.
</p>

<div class="em-info-card" style="background:#f0f6ff;border-left:4px solid #2563eb;border-radius:8px;padding:18px 22px;margin:20px 0;">
  <table style="width:100%;border-collapse:collapse;">
    <tr class="em-row" style="border-bottom:1px solid #dbeafe;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Exam</td>
      <td class="em-val" style="padding:7px 0;color:#111827;font-size:13px;font-weight:700;vertical-align:top;">{{ $emailData['exam_title'] }}</td>
    </tr>
    <tr class="em-row" style="border-bottom:1px solid #dbeafe;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Your Score</td>
      <td class="em-val" style="padding:7px 0;color:#111827;font-size:13px;font-weight:700;vertical-align:top;">{{ $emailData['score'] }} / {{ $emailData['total_marks'] }} marks</td>
    </tr>
    <tr class="em-row" style="border-bottom:1px solid #dbeafe;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Percentage</td>
      <td class="em-val" style="padding:7px 0;color:#111827;font-size:13px;font-weight:700;vertical-align:top;">{{ $emailData['percentage'] }}%</td>
    </tr>
    <tr class="em-row" style="border-bottom:1px solid #dbeafe;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Result</td>
      <td class="em-val" style="padding:7px 0;font-size:13px;vertical-align:top;">
        <span class="em-badge-red" style="display:inline-block;background:#fee2e2;color:#b91c1c;padding:2px 10px;border-radius:12px;font-size:12px;font-weight:700;">❌ Not Passed</span>
      </td>
    </tr>
    <tr class="em-row" style="border-bottom:none;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Remaining Attempts</td>
      <td class="em-val" style="padding:7px 0;color:#b45309;font-size:13px;font-weight:700;vertical-align:top;"><strong>{{ $emailData['remaining_attempts'] }}</strong></td>
    </tr>
  </table>
</div>

<div class="em-alert em-alert-yellow" style="background:#fffbeb;border:1px solid #fbbf24;color:#92400e;border-radius:8px;padding:14px 18px;margin:16px 0;font-size:13px;line-height:1.6;">
  💡 You have <strong>{{ $emailData['remaining_attempts'] }} attempt(s)</strong> remaining.
  Please review the training materials and try again using the link below.
</div>

<div class="em-btn-wrap" style="text-align:center;margin:24px 0;">
  <a href="{{ $emailData['retry_url'] }}" class="em-btn em-btn-amber" style="display:inline-block;background:#b45309;color:#ffffff !important;text-decoration:none;padding:13px 32px;border-radius:8px;font-size:14px;font-weight:700;">🔄 Try Again</a>
</div>

<p style="margin:0 0 14px;line-height:1.7;color:#374151;">
  If you have any questions, please contact us at <a href="mailto:training@smscert.com" style="color:#2563eb;">training@smscert.com</a>.
</p>
@endsection
