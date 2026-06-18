<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Exam Result — SMS Training Academy</title>
<style>
*,*::before,*::after{box-sizing:border-box;}
body{font-family:'Segoe UI',Arial,sans-serif;background:#f0f4f8;color:#1e293b;margin:0;padding:0;}
.top-bar{background:linear-gradient(135deg,#0f1e45,#1e3a8a);color:#fff;padding:18px 28px;}
.top-bar h1{font-size:17px;font-weight:800;margin:0;}
.top-bar p{font-size:12px;opacity:.7;margin:3px 0 0;}
.container{max-width:640px;margin:36px auto;padding:0 16px 48px;}
.result-card{background:#fff;border:2px solid;border-radius:16px;padding:32px;text-align:center;margin-bottom:24px;}
.result-card.passed{border-color:#86efac;background:#f0fdf4;}
.result-card.failed{border-color:#fca5a5;background:#fef2f2;}
.result-card.pending{border-color:#fde68a;background:#fffbeb;}
.result-icon{font-size:56px;margin-bottom:12px;}
.result-title{font-size:26px;font-weight:900;margin:0 0 6px;}
.result-title.passed{color:#15803d;}
.result-title.failed{color:#dc2626;}
.result-title.pending{color:#b45309;}
.result-sub{font-size:14px;color:#6b7280;margin:0 0 20px;}
.score-row{display:flex;justify-content:center;gap:24px;flex-wrap:wrap;margin:20px 0;}
.score-box{background:rgba(255,255,255,.7);border-radius:12px;padding:14px 20px;min-width:110px;}
.score-num{font-size:26px;font-weight:900;color:#1e293b;}
.score-label{font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.04em;margin-top:4px;}
.info-card{background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:20px;margin-bottom:16px;}
.info-card table{width:100%;border-collapse:collapse;}
.info-card td{padding:8px 4px;font-size:13px;border-bottom:1px solid #f1f5f9;}
.info-card td:first-child{color:#64748b;font-weight:600;width:44%;}
.info-card td:last-child{font-weight:700;color:#1e293b;}
.info-card tr:last-child td{border-bottom:none;}
</style>
</head>
<body>

<div class="top-bar">
    <h1><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-4px;margin-right:6px"><rect x="9" y="2" width="6" height="4" rx="1"/><path d="M9 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2h-3"/></svg>Exam Result</h1>
    <p>{{ $attempt->enrollment->trainingSchedule?->course?->name ?? 'Training Programme' }}</p>
</div>

<div class="container">

@php
    $status  = $attempt->status;
    $isPassed = $status === 'passed';
    $isPending= in_array($status, ['pending_review', 'submitted']);
    $isFailed = in_array($status, ['failed', 'attempt_limit_reached']);
    $showResult = $attempt->questionSet->show_result_to_participant;
@endphp

<div class="result-card {{ $isPassed ? 'passed' : ($isPending ? 'pending' : 'failed') }}">
    <div class="result-icon">
        @if($isPassed) &#10003;
        @elseif($isPending) &#8230;
        @else &#10007;
        @endif
    </div>
    <div class="result-title {{ $isPassed ? 'passed' : ($isPending ? 'pending' : 'failed') }}">
        @if($isPassed) Congratulations! You Passed!
        @elseif($isPending) Submitted — Pending Review
        @else Not Passed
        @endif
    </div>
    <div class="result-sub">
        @if($isPassed) You have successfully passed the knowledge test.
        @elseif($isPending) Your exam has been submitted. Results will be available after manual review.
        @else You did not achieve the minimum passing score.
        @endif
    </div>

    @if($showResult && $attempt->score !== null)
    <div class="score-row">
        <div class="score-box">
            <div class="score-num">{{ $attempt->score }}/{{ $attempt->total_marks }}</div>
            <div class="score-label">Score</div>
        </div>
        <div class="score-box">
            <div class="score-num">{{ number_format($attempt->percentage, 1) }}%</div>
            <div class="score-label">Percentage</div>
        </div>
        <div class="score-box">
            <div class="score-num" style="color:{{ $isPassed ? '#15803d' : '#dc2626' }};">
                {{ $isPassed ? '&#10003;' : '&#10007;' }}
            </div>
            <div class="score-label">{{ $isPassed ? 'Passed' : 'Not Passed' }}</div>
        </div>
    </div>
    @endif
</div>

<div class="info-card">
    <table>
        <tr><td>Participant</td><td>{{ $attempt->enrollment->full_name }}</td></tr>
        <tr><td>Course</td><td>{{ $attempt->enrollment->trainingSchedule?->course?->name ?? '—' }}</td></tr>
        <tr><td>Exam</td><td>{{ $attempt->questionSet->title }}</td></tr>
        <tr><td>Attempt #</td><td>{{ $attempt->attempt_number }}</td></tr>
        <tr><td>Submitted At</td><td>{{ $attempt->submitted_at?->format('d M Y, h:i A') ?? '—' }}</td></tr>
        @if($isPassed && $attempt->questionSet->allow_certificate_after_pass)
        <tr><td>Certificate</td><td style="color:#15803d;">&#127942; Will be issued by admin</td></tr>
        @endif
    </table>
</div>

@if($isFailed)
<div style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:16px;font-size:13px;color:#92400e;">
    âš ï¸ Please check your email for instructions. If you have remaining attempts, you will receive a new exam link.
</div>
@endif

<div style="text-align:center;margin-top:24px;">
    <p style="font-size:13px;color:#64748b;">You may close this page. Results and any further instructions will be sent to your email.</p>
    <p style="font-size:12px;color:#94a3b8;margin-top:8px;">SMS Training Academy · Sustainable Management System Inc.</p>
</div>

</div>
</body>
</html>
