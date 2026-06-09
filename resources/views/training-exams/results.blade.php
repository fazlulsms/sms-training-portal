@extends('layouts.app')
@section('page-title', 'Exam Results – ' . $schedule->course?->name)
@section('content')

<x-page-header title="Exam Results" desc="{{ $schedule->course?->name }} · {{ $schedule->batch_code }}" />

<style>
.er-card{background:#fff;border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;box-shadow:0 1px 6px rgba(0,0,0,.05);margin-bottom:20px;}
.er-header{padding:16px 20px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;}
.er-title{font-size:14px;font-weight:800;color:#1e293b;}
.badge{display:inline-flex;align-items:center;padding:3px 9px;border-radius:20px;font-size:11px;font-weight:700;}
.badge-green{background:#dcfce7;color:#166534;}
.badge-red{background:#fee2e2;color:#991b1b;}
.badge-orange{background:#fff7ed;color:#c2410c;}
.badge-blue{background:#eff6ff;color:#1d4ed8;}
.badge-gray{background:#f1f5f9;color:#64748b;}
.badge-amber{background:#fffbeb;color:#92400e;}
.er-table{width:100%;border-collapse:collapse;}
.er-table th{padding:10px 14px;font-size:11px;font-weight:800;color:#64748b;text-transform:uppercase;letter-spacing:.05em;border-bottom:2px solid #f1f5f9;text-align:left;background:#fafafa;}
.er-table td{padding:12px 14px;font-size:13px;color:#374151;border-bottom:1px solid #f8fafc;vertical-align:middle;}
.er-table tr:hover td{background:#f8faff;}
.action-btn{display:inline-flex;align-items:center;gap:3px;padding:5px 9px;border-radius:6px;font-size:11px;font-weight:600;text-decoration:none;border:none;cursor:pointer;font-family:inherit;}
.action-btn-blue{background:#eff6ff;color:#1d4ed8;}
.action-btn-amber{background:#fffbeb;color:#92400e;}
.action-btn-green{background:#f0fdf4;color:#166534;}
.action-btn-gray{background:#f1f5f9;color:#64748b;}
.stat-bar{display:flex;gap:16px;flex-wrap:wrap;}
.stat-item{background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:12px 20px;text-align:center;min-width:100px;}
.stat-num{font-size:22px;font-weight:900;color:#1e293b;}
.stat-label{font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;letter-spacing:.04em;margin-top:2px;}
.assign-panel{background:linear-gradient(135deg,#f0f9ff,#dbeafe);border:1px solid #bfdbfe;border-radius:12px;padding:20px;margin-bottom:20px;}
.assign-title{font-size:14px;font-weight:800;color:#1e3a8a;margin-bottom:12px;}
.assign-row{display:flex;align-items:center;gap:14px;flex-wrap:wrap;}
.assign-select{border:1px solid #bfdbfe;border-radius:8px;padding:8px 12px;font-size:13px;color:#1e3a8a;background:#fff;}
.btn-assign{background:#1e3a8a;color:#fff;border:none;border-radius:8px;padding:9px 18px;font-size:13px;font-weight:700;cursor:pointer;}
.no-exam-banner{background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:16px 20px;display:flex;align-items:center;gap:12px;font-size:13px;color:#92400e;}
</style>

@if(session('success'))
<div style="background:#f0fdf4;border:1px solid #86efac;color:#166534;border-radius:8px;padding:12px 18px;margin-bottom:16px;font-size:13px;">✅ {{ session('success') }}</div>
@endif
@if(session('error'))
<div style="background:#fef2f2;border:1px solid #fecaca;color:#b91c1c;border-radius:8px;padding:12px 18px;margin-bottom:16px;font-size:13px;">⚠️ {{ session('error') }}</div>
@endif

{{-- Assignment panel --}}
@if($assignment)
<div class="assign-panel">
    <div class="assign-title">📋 Assigned Question Set</div>
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
        <div>
            <div style="font-size:15px;font-weight:700;color:#1e293b;">{{ $assignment->questionSet->title }}</div>
            <div style="font-size:12px;color:#475569;margin-top:4px;">
                Total: {{ $assignment->questionSet->total_marks }} marks ·
                Pass: {{ $assignment->questionSet->effectivePassMark() }} marks ·
                Attempts: {{ $assignment->effectiveAttempts() }}
                @if($assignment->questionSet->time_limit_minutes)
                · {{ $assignment->questionSet->time_limit_minutes }} min limit
                @endif
            </div>
        </div>
        <div style="display:flex;gap:8px;">
            <a href="/admin/question-sets/{{ $assignment->questionSet->id }}/questions"
               style="background:#fff;border:1px solid #bfdbfe;color:#1d4ed8;border-radius:8px;padding:7px 14px;font-size:12px;font-weight:700;text-decoration:none;">⚙️ Edit Questions</a>
            <form method="POST" action="/admin/training-schedules/{{ $schedule->id }}/assign-exam" style="display:inline;">
                @csrf
                <input type="hidden" name="require_exam" value="0">
                <button type="submit" onclick="return confirm('Remove exam requirement from this schedule?')"
                        style="background:#fef2f2;border:1px solid #fecaca;color:#dc2626;border-radius:8px;padding:7px 14px;font-size:12px;font-weight:700;cursor:pointer;">
                    ✕ Remove
                </button>
            </form>
        </div>
    </div>
</div>
@else
<div class="no-exam-banner">
    <div style="font-size:24px;">📋</div>
    <div>
        <div style="font-weight:800;margin-bottom:4px;">No Exam Assigned</div>
        <div>Assign a question set below to require a knowledge test for this training schedule.</div>
    </div>
</div>

@php $allQS = \App\Models\QuestionSet::where('status','Active')->orderBy('title')->get(); @endphp
@if($allQS->isNotEmpty())
<div class="assign-panel" style="margin-bottom:20px;">
    <div class="assign-title">➕ Assign Knowledge Test</div>
    <form method="POST" action="/admin/training-schedules/{{ $schedule->id }}/assign-exam">
        @csrf
        <div class="assign-row">
            <select name="question_set_id" class="assign-select" required>
                <option value="">Select Question Set…</option>
                @foreach($allQS as $qs)
                <option value="{{ $qs->id }}">{{ $qs->title }} ({{ $qs->total_marks }} marks)</option>
                @endforeach
            </select>
            <input type="number" name="allowed_attempts" class="assign-select" placeholder="Attempts (blank = use default)" style="width:220px;">
            <label style="display:flex;align-items:center;gap:6px;font-size:13px;font-weight:600;cursor:pointer;color:#1e3a8a;">
                <input type="checkbox" name="exam_active_after_attendance" value="1" checked> Auto-send after attendance
            </label>
            <button type="submit" class="btn-assign">📋 Assign Exam</button>
        </div>
    </form>
</div>
@endif
@endif

{{-- Stats --}}
@php
    $total     = $enrollments->count();
    $notStarted= $enrollments->filter(fn($e) => !$e->testResult || $e->testResult->overall_status === 'not_started')->count();
    $passed    = $enrollments->filter(fn($e) => $e->testResult?->overall_status === 'passed')->count();
    $failed    = $enrollments->filter(fn($e) => in_array($e->testResult?->overall_status, ['failed','attempt_limit_reached']))->count();
    $pending   = $enrollments->filter(fn($e) => $e->testResult?->overall_status === 'pending_review')->count();
@endphp
<div class="stat-bar" style="margin-bottom:20px;">
    <div class="stat-item"><div class="stat-num">{{ $total }}</div><div class="stat-label">Total</div></div>
    <div class="stat-item"><div class="stat-num" style="color:#64748b;">{{ $notStarted }}</div><div class="stat-label">Not Started</div></div>
    <div class="stat-item"><div class="stat-num" style="color:#16a34a;">{{ $passed }}</div><div class="stat-label">Passed</div></div>
    <div class="stat-item"><div class="stat-num" style="color:#dc2626;">{{ $failed }}</div><div class="stat-label">Failed</div></div>
    @if($pending > 0)
    <div class="stat-item"><div class="stat-num" style="color:#d97706;">{{ $pending }}</div><div class="stat-label">Pending Review</div></div>
    @endif
</div>

{{-- Participant results table --}}
<div class="er-card">
    <div class="er-header">
        <div class="er-title">👥 Participant Exam Status</div>
        <a href="/admin/training-schedules" style="font-size:13px;color:#64748b;text-decoration:none;">← All Schedules</a>
    </div>

    @if($enrollments->isEmpty())
    <div style="text-align:center;padding:40px;color:#94a3b8;">No enrollments found for this schedule.</div>
    @else
    <table class="er-table">
        <thead>
            <tr>
                <th>Participant</th>
                <th>Attendance</th>
                <th>Test Status</th>
                <th>Attempts</th>
                <th>Best Score</th>
                <th>Cert Eligible</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        @foreach($enrollments as $enrollment)
        @php
            $result     = $enrollment->testResult;
            $statusLabel= $result ? $result->statusLabel() : 'Not Started';
            $statusColor= $result ? $result->statusColor() : 'gray';
            $attempts   = $enrollment->testAttempts;
            $lastAttempt= $attempts->sortByDesc('attempt_number')->first();
            $maxAttempts= $assignment ? $assignment->effectiveAttempts() : 1;
            $certOk     = $result?->certificate_eligible ?? false;
        @endphp
        <tr>
            <td>
                <div style="font-weight:700;color:#1e293b;">{{ $enrollment->full_name }}</div>
                <div style="font-size:11px;color:#64748b;">{{ $enrollment->email }}</div>
            </td>
            <td>
                @php $att = $enrollment->attendance_status; @endphp
                <span class="badge {{ in_array($att,['Present','Partial','Late','Attended']) ? 'badge-green' : ($att==='Absent'?'badge-red':'badge-gray') }}">
                    {{ $att ?? 'Pending' }}
                </span>
            </td>
            <td>
                @php
                    $badgeClass = match($statusColor) {
                        'green'  => 'badge-green',
                        'red'    => 'badge-red',
                        'orange' => 'badge-amber',
                        'blue'   => 'badge-blue',
                        default  => 'badge-gray',
                    };
                @endphp
                <span class="badge {{ $badgeClass }}">{{ $statusLabel }}</span>
                @if($enrollment->exam_email_sent)
                <div style="font-size:11px;color:#94a3b8;margin-top:3px;">📧 Email sent</div>
                @endif
            </td>
            <td>
                {{ $result?->attempts_used ?? 0 }} / {{ $maxAttempts }}
            </td>
            <td>
                @if($result?->best_score !== null)
                    {{ $result->best_score }}/{{ $assignment?->questionSet?->total_marks ?? '—' }}
                    <div style="font-size:11px;color:#64748b;">{{ number_format($result->best_percentage,1) }}%</div>
                @else
                    <span style="color:#94a3b8;">—</span>
                @endif
            </td>
            <td>
                @if($certOk)
                    <span class="badge badge-green">✅ Eligible</span>
                @elseif($result && in_array($result->overall_status, ['failed','attempt_limit_reached']))
                    <span class="badge badge-red">❌ Not Eligible</span>
                @elseif(!$assignment)
                    <span class="badge badge-blue">No Exam Required</span>
                @else
                    <span class="badge badge-gray">Pending</span>
                @endif
            </td>
            <td style="white-space:nowrap;">
                @if($lastAttempt && $lastAttempt->isSubmitted())
                    <a href="/admin/training-exams/answers/{{ $lastAttempt->id }}" class="action-btn action-btn-blue">👁 Answers</a>
                    @if($lastAttempt->manual_review_pending || $lastAttempt->status === 'pending_review')
                    <a href="/admin/training-exams/answers/{{ $lastAttempt->id }}" class="action-btn action-btn-amber">⭐ Grade</a>
                    @endif
                @endif
                @if($assignment)
                    <form method="POST" action="/admin/training-exams/send-reminder/{{ $enrollment->id }}" style="display:inline;">
                        @csrf
                        <button type="submit" class="action-btn action-btn-gray">📧 Remind</button>
                    </form>
                    @if($result && in_array($result->overall_status, ['failed','attempt_limit_reached']))
                    <form method="POST" action="/admin/training-exams/reset-attempt/{{ $enrollment->id }}" style="display:inline;">
                        @csrf
                        <button type="submit" class="action-btn action-btn-green"
                                onclick="return confirm('Create a new attempt for {{ $enrollment->full_name }}?')">🔄 Reset</button>
                    </form>
                    @endif
                @endif
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
    @endif
</div>

@endsection
