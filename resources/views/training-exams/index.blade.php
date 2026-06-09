@extends('layouts.app')
@section('page-title', 'Exam Results')
@section('content')

<style>
*,*::before,*::after{box-sizing:border-box;}
.page-header{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:24px;}
.page-title{font-size:22px;font-weight:900;color:#1e293b;}
.page-sub{font-size:13px;color:#64748b;margin-top:2px;}

/* Stats */
.stat-grid{display:flex;gap:14px;flex-wrap:wrap;margin-bottom:22px;}
.stat-card{background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:14px 22px;min-width:110px;text-align:center;flex:1;}
.stat-num{font-size:26px;font-weight:900;line-height:1;}
.stat-lbl{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#64748b;margin-top:4px;}
.s-total{color:#1e293b;}
.s-pending{color:#d97706;}
.s-passed{color:#16a34a;}
.s-failed{color:#dc2626;}
.s-progress{color:#2563eb;}

/* Filters */
.filter-bar{background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:16px 20px;margin-bottom:18px;display:flex;align-items:flex-end;gap:12px;flex-wrap:wrap;}
.filter-group{display:flex;flex-direction:column;gap:5px;min-width:180px;}
.filter-label{font-size:11px;font-weight:800;color:#64748b;text-transform:uppercase;letter-spacing:.05em;}
.filter-input{border:1px solid #d1d5db;border-radius:8px;padding:8px 12px;font-size:13px;color:#1e293b;background:#fff;}
.filter-input:focus{outline:none;border-color:#1e3a8a;}
.btn-filter{background:#1e3a8a;color:#fff;border:none;border-radius:8px;padding:9px 18px;font-size:13px;font-weight:700;cursor:pointer;white-space:nowrap;}
.btn-clear{background:#f1f5f9;color:#64748b;border:1px solid #e2e8f0;border-radius:8px;padding:9px 14px;font-size:13px;font-weight:600;text-decoration:none;white-space:nowrap;}

/* Table */
.card{background:#fff;border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,.04);}
.tbl{width:100%;border-collapse:collapse;}
.tbl th{padding:10px 14px;font-size:11px;font-weight:800;color:#64748b;text-transform:uppercase;letter-spacing:.05em;border-bottom:2px solid #f1f5f9;background:#fafafa;text-align:left;white-space:nowrap;}
.tbl td{padding:11px 14px;font-size:13px;color:#374151;border-bottom:1px solid #f8fafc;vertical-align:middle;}
.tbl tr:last-child td{border-bottom:none;}
.tbl tr:hover td{background:#f8faff;}
.name{font-weight:700;color:#1e293b;}
.sub{font-size:11px;color:#94a3b8;margin-top:2px;}
.badge{display:inline-flex;align-items:center;padding:3px 9px;border-radius:20px;font-size:11px;font-weight:700;}
.bg-passed{background:#dcfce7;color:#166534;}
.bg-failed{background:#fee2e2;color:#991b1b;}
.bg-pending{background:#fffbeb;color:#92400e;}
.bg-progress{background:#dbeafe;color:#1d4ed8;}
.bg-gray{background:#f1f5f9;color:#64748b;}
.bg-limit{background:#fce7f3;color:#9d174d;}
.score-bar-wrap{display:flex;align-items:center;gap:8px;}
.score-bar{height:6px;background:#e2e8f0;border-radius:3px;flex:1;min-width:60px;overflow:hidden;}
.score-bar-fill{height:100%;border-radius:3px;transition:width .3s;}
.action-btn{display:inline-flex;align-items:center;gap:3px;padding:4px 9px;border-radius:6px;font-size:11px;font-weight:600;text-decoration:none;border:none;cursor:pointer;font-family:inherit;white-space:nowrap;}
.ab-blue{background:#eff6ff;color:#1d4ed8;}
.ab-amber{background:#fffbeb;color:#b45309;}
.ab-gray{background:#f1f5f9;color:#64748b;}
.ab-green{background:#f0fdf4;color:#166534;}
.empty-state{text-align:center;padding:60px 20px;color:#94a3b8;}
.empty-state .ei{font-size:40px;margin-bottom:12px;}
.empty-state p{font-size:14px;}
.pagination-wrap{padding:14px 20px;border-top:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;}
.pag-info{font-size:12px;color:#64748b;}
</style>

<div class="page-header">
    <div>
        <div class="page-title">📋 Exam Results</div>
        <div class="page-sub">All participant test attempts across ILT training schedules</div>
    </div>
    <a href="/admin/question-sets" style="background:#f0fdf4;border:1px solid #86efac;color:#166534;border-radius:8px;padding:8px 16px;font-size:12px;font-weight:700;text-decoration:none;">
        ⚙️ Manage Question Sets
    </a>
</div>

@if(session('success'))
<div style="background:#f0fdf4;border:1px solid #86efac;color:#166534;border-radius:8px;padding:11px 16px;margin-bottom:14px;font-size:13px;">✅ {{ session('success') }}</div>
@endif

{{-- Stats bar --}}
<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-num s-total">{{ $stats['total'] }}</div>
        <div class="stat-lbl">Total Attempts</div>
    </div>
    <div class="stat-card">
        <div class="stat-num s-pending">{{ $stats['pending_review'] }}</div>
        <div class="stat-lbl">Pending Review</div>
    </div>
    <div class="stat-card">
        <div class="stat-num s-passed">{{ $stats['passed'] }}</div>
        <div class="stat-lbl">Passed</div>
    </div>
    <div class="stat-card">
        <div class="stat-num s-failed">{{ $stats['failed'] }}</div>
        <div class="stat-lbl">Failed</div>
    </div>
    <div class="stat-card">
        <div class="stat-num s-progress">{{ $stats['in_progress'] }}</div>
        <div class="stat-lbl">In Progress</div>
    </div>
</div>

{{-- Filter bar --}}
<form method="GET" action="/admin/training-exams">
<div class="filter-bar">
    <div class="filter-group" style="flex:2;min-width:220px;">
        <div class="filter-label">Search Participant</div>
        <input type="text" name="search" class="filter-input" placeholder="Name, email or company…" value="{{ $search }}">
    </div>
    <div class="filter-group">
        <div class="filter-label">Status</div>
        <select name="status" class="filter-input">
            <option value="">All Statuses</option>
            <option value="pending_review"      {{ $status==='pending_review' ? 'selected' : '' }}>⏳ Pending Review</option>
            <option value="passed"              {{ $status==='passed' ? 'selected' : '' }}>✅ Passed</option>
            <option value="failed"              {{ $status==='failed' ? 'selected' : '' }}>❌ Failed</option>
            <option value="attempt_limit_reached" {{ $status==='attempt_limit_reached' ? 'selected' : '' }}>🚫 Limit Reached</option>
            <option value="in_progress"         {{ $status==='in_progress' ? 'selected' : '' }}>🔄 In Progress</option>
            <option value="submitted"           {{ $status==='submitted' ? 'selected' : '' }}>📤 Submitted</option>
        </select>
    </div>
    <div class="filter-group">
        <div class="filter-label">Course</div>
        <select name="course_id" class="filter-input">
            <option value="">All Courses</option>
            @foreach($courses as $c)
            <option value="{{ $c->id }}" {{ $course == $c->id ? 'selected' : '' }}>{{ Str::limit($c->name, 45) }}</option>
            @endforeach
        </select>
    </div>
    <button type="submit" class="btn-filter">🔍 Filter</button>
    @if($search || $status || $course)
    <a href="/admin/training-exams" class="btn-clear">✕ Clear</a>
    @endif
</div>
</form>

{{-- Results table --}}
<div class="card">
    @if($attempts->isEmpty())
    <div class="empty-state">
        <div class="ei">📋</div>
        <p>No exam attempts found{{ ($search || $status || $course) ? ' matching your filters' : '' }}.</p>
        @if($search || $status || $course)
        <a href="/admin/training-exams" style="color:#1d4ed8;font-size:13px;">Clear filters</a>
        @endif
    </div>
    @else
    <table class="tbl">
        <thead>
            <tr>
                <th>#</th>
                <th>Participant</th>
                <th>Course / Schedule</th>
                <th>Exam</th>
                <th>Attempt</th>
                <th>Score</th>
                <th>Status</th>
                <th>Submitted</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        @foreach($attempts as $attempt)
        @php
            $enrollment = $attempt->enrollment;
            $schedule   = $enrollment?->trainingSchedule;
            $course     = $schedule?->course;
            $qs         = $attempt->questionSet;
            $pct        = $attempt->percentage ?? 0;
            $barColor   = $attempt->status === 'passed' ? '#16a34a'
                        : ($attempt->status === 'failed' ? '#dc2626'
                        : ($attempt->status === 'pending_review' ? '#d97706' : '#2563eb'));
            $statusBadge = match($attempt->status) {
                'passed'               => ['✅ Passed',          'bg-passed'],
                'failed'               => ['❌ Failed',           'bg-failed'],
                'pending_review'       => ['⏳ Pending Review',   'bg-pending'],
                'attempt_limit_reached'=> ['🚫 Limit Reached',   'bg-limit'],
                'in_progress'          => ['🔄 In Progress',      'bg-progress'],
                'submitted'            => ['📤 Submitted',        'bg-gray'],
                default                => [ucfirst($attempt->status), 'bg-gray'],
            };
        @endphp
        <tr>
            <td style="color:#94a3b8;font-size:12px;">{{ $attempt->id }}</td>
            <td>
                <div class="name">{{ $enrollment?->full_name ?? '—' }}</div>
                <div class="sub">{{ $enrollment?->email ?? '' }}</div>
                @if($enrollment?->company)
                <div class="sub">{{ $enrollment->company }}</div>
                @endif
            </td>
            <td>
                <div style="font-weight:600;color:#1e293b;font-size:13px;">{{ Str::limit($course?->name ?? '—', 40) }}</div>
                <div class="sub">{{ $schedule?->batch_code ?? '' }}
                    @if($schedule?->start_date) · {{ \Carbon\Carbon::parse($schedule->start_date)->format('d M Y') }}@endif
                </div>
            </td>
            <td>
                <div style="font-size:12px;color:#475569;font-weight:600;">{{ Str::limit($qs?->title ?? '—', 35) }}</div>
            </td>
            <td style="text-align:center;">
                <span style="background:#f1f5f9;color:#1e293b;font-size:12px;font-weight:800;padding:3px 10px;border-radius:20px;">#{{ $attempt->attempt_number }}</span>
            </td>
            <td>
                @if($attempt->score !== null && $qs)
                <div style="font-weight:800;font-size:13px;color:#1e293b;">{{ $attempt->score }}/{{ $qs->total_marks }}</div>
                <div class="score-bar-wrap" style="margin-top:4px;">
                    <div class="score-bar">
                        <div class="score-bar-fill" style="width:{{ min($pct,100) }}%;background:{{ $barColor }};"></div>
                    </div>
                    <span style="font-size:11px;font-weight:700;color:{{ $barColor }};">{{ number_format($pct,1) }}%</span>
                </div>
                @else
                <span style="color:#94a3b8;">—</span>
                @endif
            </td>
            <td>
                <span class="badge {{ $statusBadge[1] }}">{{ $statusBadge[0] }}</span>
                @if($attempt->manual_review_pending)
                <div style="font-size:11px;color:#d97706;margin-top:3px;">⚠️ Needs grading</div>
                @endif
            </td>
            <td style="white-space:nowrap;">
                @if($attempt->submitted_at)
                <div style="font-size:12px;color:#374151;font-weight:600;">{{ $attempt->submitted_at->format('d M Y') }}</div>
                <div class="sub">{{ $attempt->submitted_at->format('h:i A') }}</div>
                @else
                <span style="color:#94a3b8;font-size:12px;">—</span>
                @endif
            </td>
            <td style="white-space:nowrap;">
                <a href="/admin/training-exams/answers/{{ $attempt->id }}" class="action-btn ab-blue">👁 Answers</a>
                @if($attempt->manual_review_pending || $attempt->status === 'pending_review')
                <a href="/admin/training-exams/answers/{{ $attempt->id }}" class="action-btn ab-amber">⭐ Grade</a>
                @endif
                @if($schedule)
                <a href="/admin/training-exams/{{ $schedule->id }}/results" class="action-btn ab-gray">📊 Schedule</a>
                @endif
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>

    <div class="pagination-wrap">
        <div class="pag-info">
            Showing {{ $attempts->firstItem() }}–{{ $attempts->lastItem() }} of {{ $attempts->total() }} attempts
        </div>
        <div>
            {{ $attempts->links('pagination::simple-tailwind') }}
        </div>
    </div>
    @endif
</div>

@endsection
