@php
    $headerColor = '#1e3a8a';
    $accentColor = '#1e3a8a';
    $headerIcon  = '📋';
    $headerTitle = 'Knowledge Test';
    $subject     = $emailData['exam_title'] ?? 'Knowledge Test';
@endphp
@include('emails.partials.participant-header')

<p>Dear <strong>{{ $emailData['participant_name'] }}</strong>,</p>

<p>
    Thank you for attending and participating in <strong>{{ $emailData['course_name'] }}</strong>.
    We appreciate your commitment to professional development.
</p>

<p>
    @if(!empty($emailData['is_reminder']))
        <strong>This is a reminder</strong> — you have not yet completed your knowledge test.
    @else
        As part of the training programme, you are required to complete a <strong>knowledge test</strong>.
    @endif
    Please complete the exam at your earliest convenience using the link below.
</p>

<div class="info-card">
    <table>
        <tr><td>Exam Title</td><td>{{ $emailData['exam_title'] }}</td></tr>
        <tr><td>Pass Mark</td><td>{{ $emailData['pass_mark_text'] }}</td></tr>
        <tr><td>Allowed Attempts</td><td>{{ $emailData['allowed_attempts'] }}</td></tr>
        <tr><td>Time Limit</td><td>{{ $emailData['time_limit'] }}</td></tr>
    </table>
</div>

<div style="text-align:center;margin:28px 0;">
    <a href="{{ $emailData['exam_url'] }}" class="btn">📝 Start Knowledge Test</a>
</div>

@if(!empty($emailData['cert_note']))
<div class="alert-box alert-blue">
    🏆 {{ $emailData['cert_note'] }}
</div>
@endif

<div class="alert-box alert-yellow">
    ⚠️ <strong>Important:</strong> This exam link is personal and unique to you. Please do not share it with others.
</div>

<p>If you have any questions, please contact us at <a href="mailto:training@smscert.com" style="color:#1e3a8a;">training@smscert.com</a>.</p>

<p>Best regards,<br><strong>SMS Training Services</strong><br>Sustainable Management System Bangladesh</p>

@include('emails.partials.participant-footer')
