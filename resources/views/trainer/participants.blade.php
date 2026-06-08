@extends('layouts.app')

@section('page-title', 'Participants — ' . ($schedule->course->name ?? 'Schedule'))

@section('content')

<style>
.t-wrap { padding: 28px; }

.back-btn {
    display:inline-flex; align-items:center; gap:6px;
    color:#1e3a8a; font-weight:700; text-decoration:none; font-size:14px; margin-bottom:18px;
}

.schedule-info-card {
    background: linear-gradient(135deg, #1e3a8a, #1e40af);
    border-radius:14px; padding:22px 24px; color:white; margin-bottom:22px;
    box-shadow:0 4px 16px rgba(30,58,138,.2);
}
.schedule-info-card h2 { margin:0 0 14px; font-size:20px; font-weight:800; }
.schedule-meta { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; }
.meta-item .meta-label { font-size:11px; opacity:.65; text-transform:uppercase; letter-spacing:.5px; margin-bottom:3px; }
.meta-item .meta-value { font-size:14px; font-weight:700; }

.alert { padding:12px 16px; border-radius:8px; font-weight:600; margin-bottom:16px; font-size:13px; }
.alert-success { background:#dcfce7; color:#166534; }
.alert-error   { background:#fee2e2; color:#991b1b; }

.t-card { background:#fff; border:1px solid #e5e7eb; border-radius:14px; box-shadow:0 1px 4px rgba(15,23,42,.06); overflow:hidden; }
.t-card-header {
    padding:14px 20px; border-bottom:1px solid #f3f4f6;
    font-size:14px; font-weight:800; color:#111827;
    display:flex; align-items:center; justify-content:space-between;
}

.p-table { width:100%; border-collapse:collapse; }
.p-table th {
    padding:11px 14px; text-align:left; font-size:11px; font-weight:700;
    color:#6b7280; text-transform:uppercase; letter-spacing:.5px;
    background:#f9fafb; border-bottom:1px solid #e5e7eb; white-space:nowrap;
}
.p-table td { padding:12px 14px; border-bottom:1px solid #f3f4f6; font-size:13px; color:#374151; vertical-align:middle; }
.p-table tbody tr:last-child td { border-bottom:none; }
.p-table tbody tr:hover td { background:#f9fafb; }

.participant-name { font-weight:700; color:#111827; }
.participant-sub  { font-size:12px; color:#9ca3af; margin-top:1px; }

.badge { display:inline-block; padding:3px 9px; border-radius:20px; font-size:11px; font-weight:700; }
.badge-success  { background:#dcfce7; color:#166534; }
.badge-danger   { background:#fee2e2; color:#991b1b; }
.badge-warning  { background:#fef3c7; color:#92400e; }
.badge-info     { background:#dbeafe; color:#1e40af; }
.badge-secondary{ background:#f3f4f6; color:#6b7280; }

/* ── Inline update form ── */
.inline-form { display:inline-flex; align-items:center; gap:5px; }
.select-sm {
    padding:4px 8px; border:1px solid #d1d5db; border-radius:7px;
    font-size:12px; font-weight:600; background:#fff; color:#374151;
    cursor:pointer; font-family:inherit;
}
.btn-save-sm {
    padding:4px 10px; background:#1e3a8a; color:white; border:none;
    border-radius:7px; font-size:12px; font-weight:700; cursor:pointer; font-family:inherit;
}
.btn-save-sm:hover { background:#1e2d6e; }

.empty-state { padding:36px; text-align:center; color:#9ca3af; font-size:14px; }

@media(max-width:900px) {
    .schedule-meta { grid-template-columns:repeat(2,1fr); }
}
</style>

<div class="t-wrap">

    <a href="{{ route('trainer.schedules') }}" class="back-btn">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
        Back to My Schedules
    </a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    {{-- Schedule info --}}
    <div class="schedule-info-card">
        <h2>{{ $schedule->course->name ?? 'Training' }}</h2>
        <div class="schedule-meta">
            <div class="meta-item">
                <div class="meta-label">Batch Code</div>
                <div class="meta-value">{{ $schedule->batch_code ?? '—' }}</div>
            </div>
            <div class="meta-item">
                <div class="meta-label">Dates</div>
                <div class="meta-value">
                    {{ $schedule->start_date ? \Carbon\Carbon::parse($schedule->start_date)->format('d M Y') : '—' }}
                    @if($schedule->end_date) → {{ \Carbon\Carbon::parse($schedule->end_date)->format('d M Y') }} @endif
                </div>
            </div>
            <div class="meta-item">
                <div class="meta-label">Mode</div>
                <div class="meta-value">{{ $schedule->training_mode ?? '—' }}</div>
            </div>
            <div class="meta-item">
                <div class="meta-label">Total Enrolled</div>
                <div class="meta-value">{{ $schedule->enrollments->count() }} participants</div>
            </div>
        </div>
    </div>

    {{-- Participants table --}}
    <div class="t-card">
        <div class="t-card-header" style="justify-content:space-between; flex-wrap:wrap; gap:10px;">
            <span>Enrolled Participants ({{ $schedule->enrollments->count() }})</span>
            <a href="{{ route('trainer.schedule.attendance', $schedule->id) }}"
               style="display:inline-flex; align-items:center; gap:5px; background:#1e3a8a; color:white; padding:7px 14px; border-radius:8px; text-decoration:none; font-weight:700; font-size:13px;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                Attendance Sheet
            </a>

            {{-- Quick stats --}}
            @php
                $enrs = $schedule->enrollments;
                $present   = $enrs->where('attendance_status', 'Present')->count();
                $completed = $enrs->where('completion_status', 'Completed')->count();
            @endphp
            <span style="font-size:12px; font-weight:600; color:#6b7280;">
                Present: <strong>{{ $present }}</strong> &nbsp;·&nbsp; Completed: <strong>{{ $completed }}</strong>
            </span>
        </div>

        <div style="overflow-x:auto;">
            <table class="p-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Participant</th>
                        <th>Company</th>
                        <th>Mode</th>
                        <th>Payment</th>
                        <th>Attendance</th>
                        <th>Completion</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($schedule->enrollments as $enrollment)
                        <tr>
                            <td>{{ $loop->iteration }}</td>

                            <td>
                                <div class="participant-name">{{ $enrollment->full_name ?? '—' }}</div>
                                <div class="participant-sub">{{ $enrollment->email ?? '' }}</div>
                                @if($enrollment->phone)
                                    <div class="participant-sub">{{ $enrollment->phone }}</div>
                                @endif
                            </td>

                            <td>{{ $enrollment->company ?? '—' }}</td>

                            <td>
                                <span class="badge badge-info">{{ $enrollment->selected_mode ?? '—' }}</span>
                            </td>

                            <td>
                                @php $ps = strtolower($enrollment->payment_status ?? ''); @endphp
                                @if($ps === 'paid')    <span class="badge badge-success">Paid</span>
                                @elseif($ps === 'waived') <span class="badge badge-info">Waived</span>
                                @else                  <span class="badge badge-warning">{{ ucfirst($enrollment->payment_status ?? 'Pending') }}</span>
                                @endif
                            </td>

                            {{-- Attendance inline update --}}
                            <td>
                                <form method="POST"
                                      action="{{ route('trainer.attendance.update', $enrollment->id) }}"
                                      class="inline-form">
                                    @csrf
                                    <select name="attendance_status" class="select-sm"
                                            onchange="this.form.submit()" title="Update attendance">
                                        @foreach(['Pending','Present','Absent','Partial','Late'] as $opt)
                                            <option value="{{ $opt }}"
                                                {{ ($enrollment->attendance_status ?? 'Pending') === $opt ? 'selected' : '' }}>
                                                {{ $opt }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
                            </td>

                            {{-- Completion inline update --}}
                            <td>
                                <form method="POST"
                                      action="{{ route('trainer.completion.update', $enrollment->id) }}"
                                      class="inline-form">
                                    @csrf
                                    <select name="completion_status" class="select-sm"
                                            onchange="this.form.submit()" title="Update completion">
                                        @foreach(['Pending','Completed','Not Completed'] as $opt)
                                            <option value="{{ $opt }}"
                                                {{ ($enrollment->completion_status ?? 'Pending') === $opt ? 'selected' : '' }}>
                                                {{ $opt }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="empty-state">No participants enrolled in this schedule yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection
