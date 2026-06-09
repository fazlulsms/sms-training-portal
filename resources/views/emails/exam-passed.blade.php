@php
    $headerColor = '#14532d';
    $accentColor = '#16a34a';
    $headerIcon  = '🎉';
    $headerTitle = 'Congratulations!';
    $subject     = 'Passed – ' . ($emailData['exam_title'] ?? 'Knowledge Test');
@endphp
@include('emails.partials.participant-header')

<p>Dear <strong>{{ $emailData['participant_name'] }}</strong>,</p>

<p>
    Congratulations! We are pleased to inform you that you have <strong>successfully passed</strong>
    the knowledge test for <strong>{{ $emailData['course_name'] }}</strong>.
</p>

<div class="info-card">
    <table>
        <tr><td>Exam</td><td>{{ $emailData['exam_title'] }}</td></tr>
        <tr><td>Your Score</td><td>{{ $emailData['score'] }} / {{ $emailData['total_marks'] }} marks</td></tr>
        <tr><td>Percentage</td><td>{{ $emailData['percentage'] }}%</td></tr>
        <tr><td>Result</td><td style="color:#16a34a;font-weight:800;">✅ PASSED</td></tr>
    </table>
</div>

<div class="alert-box alert-green">
    🏆 Your certificate of completion will be issued by the training admin shortly.
</div>

<p>Well done on your achievement! We hope this training has been valuable to you.</p>

<p>Best regards,<br><strong>SMS Training Services</strong><br>Sustainable Management System Bangladesh</p>

@include('emails.partials.participant-footer')
