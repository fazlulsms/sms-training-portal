@extends('emails.layouts.master')
@section('subject-strip') 🎉 Congratulations — Test Passed! @endsection
@section('subject-theme', 'green')

@section('content')
<p class="em-greeting" style="font-size:16px;font-weight:600;color:#111827;margin:0 0 16px;">Dear <strong>{{ $emailData['participant_name'] }}</strong>,</p>

<p style="margin:0 0 14px;line-height:1.7;color:#374151;">
  Congratulations! We are pleased to inform you that you have <strong>successfully passed</strong>
  the knowledge test for <strong>{{ $emailData['course_name'] }}</strong>.
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
    <tr class="em-row" style="border-bottom:none;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Result</td>
      <td class="em-val" style="padding:7px 0;font-size:13px;vertical-align:top;">
        <span class="em-badge-green" style="display:inline-block;background:#dcfce7;color:#15803d;padding:2px 10px;border-radius:12px;font-size:12px;font-weight:700;">✅ PASSED</span>
      </td>
    </tr>
  </table>
</div>

<div class="em-alert em-alert-green" style="background:#f0fdf4;border:1px solid #86efac;color:#15803d;border-radius:8px;padding:14px 18px;margin:16px 0;font-size:13px;line-height:1.6;">
  🏆 Your certificate of completion will be issued by the training admin shortly. You will receive another email when it is ready.
</div>

<p style="margin:0 0 14px;line-height:1.7;color:#374151;">
  Well done on your achievement! We hope this training has been valuable to you.
</p>
@endsection
