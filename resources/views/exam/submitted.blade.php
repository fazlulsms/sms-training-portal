<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Exam Already Submitted – SMS Training Services</title>
<style>
body{font-family:'Segoe UI',Arial,sans-serif;background:#f0f4f8;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0;padding:20px;}
.card{background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:40px;max-width:500px;width:100%;text-align:center;box-shadow:0 4px 20px rgba(0,0,0,.08);}
.icon{font-size:52px;margin-bottom:16px;}
h1{font-size:20px;font-weight:800;color:#1e293b;margin:0 0 10px;}
p{font-size:14px;color:#64748b;line-height:1.6;margin:0;}
.status{display:inline-flex;align-items:center;padding:6px 16px;border-radius:20px;font-size:13px;font-weight:700;margin:16px 0;}
.status.passed{background:#dcfce7;color:#166534;}
.status.failed{background:#fee2e2;color:#991b1b;}
.status.pending{background:#fffbeb;color:#92400e;}
</style>
</head>
<body>
<div class="card">
    <div class="icon">
        @if($attempt->status === 'passed') 🎉
        @elseif(in_array($attempt->status, ['submitted','pending_review'])) ⏳
        @else 📋
        @endif
    </div>
    <h1>Exam Already Submitted</h1>

    @php
        $isP = $attempt->status === 'passed';
        $isN = in_array($attempt->status, ['submitted','pending_review']);
    @endphp

    <div class="status {{ $isP ? 'passed' : ($isN ? 'pending' : 'failed') }}">
        {{ ucfirst(str_replace('_',' ', $attempt->status)) }}
    </div>

    <p>
        This exam link has already been submitted.
        @if($attempt->submitted_at)
        <br>Submitted on {{ $attempt->submitted_at->format('d M Y, h:i A') }}.
        @endif
    </p>
    <p style="margin-top:12px;">Please check your email for your result. You may close this page.</p>

    <div style="margin-top:24px;padding-top:20px;border-top:1px solid #f1f5f9;font-size:12px;color:#94a3b8;">
        SMS Training Services · Sustainable Management System Bangladesh
    </div>
</div>
</body>
</html>
