@php
    $headerColor = '#7f1d1d';
    $accentColor = '#dc2626';
    $headerIcon  = '📋';
    $headerTitle = 'Knowledge Test Result';
    $subject     = 'Knowledge Test Result – ' . ($emailData['exam_title'] ?? 'Knowledge Test');
@endphp
@include('emails.partials.participant-header')

<p>Dear <strong>{{ $emailData['participant_name'] }}</strong>,</p>

<p>
    Thank you for your efforts in completing the knowledge test for <strong>{{ $emailData['course_name'] }}</strong>.
    We regret to inform you that you have not achieved the minimum passing score and you have used all available attempts.
</p>

<div class="info-card">
    <table>
        <tr><td>Exam</td><td>{{ $emailData['exam_title'] }}</td></tr>
        <tr><td>Best Score</td><td>{{ $emailData['score'] }} / {{ $emailData['total_marks'] }} marks</td></tr>
        <tr><td>Best Percentage</td><td>{{ $emailData['percentage'] }}%</td></tr>
        <tr><td>Result</td><td style="color:#dc2626;font-weight:800;">Attempt Limit Reached</td></tr>
    </table>
</div>

<div class="alert-box alert-red">
    ⚠️ Unfortunately, you have used all available attempts. Your certificate cannot be issued at this time.
</div>

<p>
    If you believe there is an error or would like to discuss this further, please contact us at
    <a href="mailto:training@smscert.com" style="color:#1e3a8a;">training@smscert.com</a>.
</p>

<p>Best regards,<br><strong>SMS Training Services</strong><br>Sustainable Management System Bangladesh</p>

@include('emails.partials.participant-footer')
