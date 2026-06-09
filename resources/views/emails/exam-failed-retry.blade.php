@php
    $headerColor = '#92400e';
    $accentColor = '#d97706';
    $headerIcon  = '📋';
    $headerTitle = 'Test Result – Please Try Again';
    $subject     = 'Knowledge Test Result – ' . ($emailData['exam_title'] ?? 'Knowledge Test');
@endphp
@include('emails.partials.participant-header')

<p>Dear <strong>{{ $emailData['participant_name'] }}</strong>,</p>

<p>
    Thank you for completing the knowledge test for <strong>{{ $emailData['course_name'] }}</strong>.
    Unfortunately, you did not achieve the minimum passing score in this attempt.
</p>

<div class="info-card">
    <table>
        <tr><td>Exam</td><td>{{ $emailData['exam_title'] }}</td></tr>
        <tr><td>Your Score</td><td>{{ $emailData['score'] }} / {{ $emailData['total_marks'] }} marks</td></tr>
        <tr><td>Percentage</td><td>{{ $emailData['percentage'] }}%</td></tr>
        <tr><td>Result</td><td style="color:#dc2626;font-weight:800;">❌ Not Passed</td></tr>
        <tr><td>Remaining Attempts</td><td><strong>{{ $emailData['remaining_attempts'] }}</strong></td></tr>
    </table>
</div>

<div class="alert-box alert-yellow">
    💡 You have <strong>{{ $emailData['remaining_attempts'] }} attempt(s)</strong> remaining.
    Please review the training materials and try again using the link below.
</div>

<div style="text-align:center;margin:28px 0;">
    <a href="{{ $emailData['retry_url'] }}" class="btn" style="background:#d97706;">🔄 Try Again</a>
</div>

<p>If you have any questions, please contact us at <a href="mailto:training@smscert.com" style="color:#1e3a8a;">training@smscert.com</a>.</p>

<p>Best regards,<br><strong>SMS Training Services</strong><br>Sustainable Management System Bangladesh</p>

@include('emails.partials.participant-footer')
