@extends('layouts.app')

@section('page-title', 'Attendance — ' . ($schedule->course->name ?? 'Schedule'))

@section('content')

<style>
.att-wrap { padding: 28px; }

.back-btn {
    display: inline-flex; align-items: center; gap: 6px;
    color: #1e3a8a; font-weight: 700; text-decoration: none; font-size: 14px; margin-bottom: 18px;
}

/* ── Schedule header card ── */
.schedule-card {
    background: linear-gradient(135deg, #1e3a8a, #1e40af);
    border-radius: 14px; padding: 20px 24px; color: white;
    margin-bottom: 20px; box-shadow: 0 4px 16px rgba(30,58,138,.2);
}
.schedule-card h2 { margin: 0 0 12px; font-size: 19px; font-weight: 800; }
.schedule-meta { display: flex; flex-wrap: wrap; gap: 24px; }
.schedule-meta-item .label { font-size: 11px; opacity: .65; text-transform: uppercase; letter-spacing: .5px; }
.schedule-meta-item .val   { font-size: 14px; font-weight: 700; margin-top: 2px; }

/* ── Alerts ── */
.alert { padding: 12px 16px; border-radius: 8px; font-weight: 600; margin-bottom: 16px; font-size: 13px; }
.alert-success { background: #dcfce7; color: #166534; }
.alert-error   { background: #fee2e2; color: #991b1b; }

/* ── Legend ── */
.legend {
    display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 14px; align-items: center;
}
.legend-item {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 12px; font-weight: 600; color: #374151;
}
.legend-dot {
    width: 10px; height: 10px; border-radius: 50%;
}

/* ── Attendance matrix ── */
.matrix-card {
    background: white; border: 1px solid #e5e7eb; border-radius: 14px;
    overflow: hidden; box-shadow: 0 1px 4px rgba(15,23,42,.06);
}
.matrix-scroll { overflow-x: auto; }

.att-table { width: 100%; border-collapse: collapse; min-width: 600px; }

.att-table th {
    padding: 11px 12px; font-size: 11px; font-weight: 700; color: #6b7280;
    text-transform: uppercase; letter-spacing: .5px;
    background: #f9fafb; border-bottom: 1px solid #e5e7eb;
    white-space: nowrap; text-align: left;
}
.att-table th.day-col { text-align: center; min-width: 130px; }
.att-table th.pct-col { text-align: center; min-width: 90px; }

.att-table td {
    padding: 10px 12px; border-bottom: 1px solid #f3f4f6;
    vertical-align: middle; font-size: 13px;
}
.att-table tbody tr:last-child td { border-bottom: none; }
.att-table tbody tr:hover td { background: #f9fafb; }

.participant-name { font-weight: 700; color: #111827; }
.participant-sub  { font-size: 11px; color: #9ca3af; margin-top: 1px; }

/* Status select colour coding */
.status-select {
    width: 110px; padding: 5px 6px; border-radius: 7px; font-size: 12px; font-weight: 700;
    border: 1px solid #d1d5db; cursor: pointer; font-family: inherit;
    appearance: none; text-align: center;
}
.status-select.s-present  { background: #dcfce7; color: #166534; border-color: #86efac; }
.status-select.s-absent   { background: #fee2e2; color: #991b1b; border-color: #fca5a5; }
.status-select.s-late     { background: #fef3c7; color: #92400e; border-color: #fde68a; }
.status-select.s-excused  { background: #dbeafe; color: #1e40af; border-color: #93c5fd; }
.status-select.s-pending  { background: #f3f4f6; color: #6b7280; border-color: #d1d5db; }

.pct-badge {
    display: inline-block; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 800;
    text-align: center; min-width: 52px;
}
.pct-high   { background: #dcfce7; color: #166534; }
.pct-mid    { background: #fef3c7; color: #92400e; }
.pct-low    { background: #fee2e2; color: #991b1b; }
.pct-zero   { background: #f3f4f6; color: #6b7280; }

/* ── Footer action bar ── */
.action-bar {
    display: flex; align-items: center; justify-content: space-between;
    padding: 14px 20px; border-top: 1px solid #e5e7eb; background: #f9fafb;
    flex-wrap: wrap; gap: 10px;
}
.action-bar-note { font-size: 13px; color: #6b7280; }

.btn-save {
    background: #1e3a8a; color: white; padding: 10px 22px;
    border: none; border-radius: 9px; font-weight: 800; font-size: 14px;
    cursor: pointer; font-family: inherit;
    display: inline-flex; align-items: center; gap: 7px;
}
.btn-save:hover { background: #1e2d6e; }
</style>

<div class="att-wrap">

    {{-- Back link --}}
    @if(Auth::check() && Auth::user()->isTrainer())
        <a href="{{ route('trainer.schedule.participants', $schedule->id) }}" class="back-btn">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
            Back to Participants
        </a>
    @else
        <a href="/admin/training-schedules" class="back-btn">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
            Back to Schedules
        </a>
    @endif

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    {{-- Schedule info --}}
    <div class="schedule-card">
        <h2>{{ $schedule->course->name ?? 'Training' }} — Attendance Sheet</h2>
        <div class="schedule-meta">
            <div class="schedule-meta-item">
                <div class="label">Batch</div>
                <div class="val">{{ $schedule->batch_code ?? '—' }}</div>
            </div>
            <div class="schedule-meta-item">
                <div class="label">Dates</div>
                <div class="val">
                    {{ \Carbon\Carbon::parse($schedule->start_date)->format('d M Y') }}
                    @if($schedule->start_date !== $schedule->end_date)
                        → {{ \Carbon\Carbon::parse($schedule->end_date)->format('d M Y') }}
                    @endif
                </div>
            </div>
            <div class="schedule-meta-item">
                <div class="label">Sessions</div>
                <div class="val">{{ $sessionDates->count() }} day(s)</div>
            </div>
            <div class="schedule-meta-item">
                <div class="label">Enrolled</div>
                <div class="val">{{ $schedule->enrollments->count() }} participants</div>
            </div>
            <div class="schedule-meta-item">
                <div class="label">Trainer</div>
                <div class="val">{{ $schedule->trainer->name ?? '—' }}</div>
            </div>
        </div>
    </div>

    {{-- Legend --}}
    <div class="legend">
        <span style="font-size:12px; font-weight:700; color:#374151;">Status:</span>
        <span class="legend-item"><span class="legend-dot" style="background:#16a34a;"></span>Present</span>
        <span class="legend-item"><span class="legend-dot" style="background:#dc2626;"></span>Absent</span>
        <span class="legend-item"><span class="legend-dot" style="background:#f59e0b;"></span>Late</span>
        <span class="legend-item"><span class="legend-dot" style="background:#3b82f6;"></span>Excused</span>
        <span class="legend-item"><span class="legend-dot" style="background:#9ca3af;"></span>Pending</span>
        <span style="margin-left:16px; font-size:12px; color:#6b7280;">≥80% = Present summary · <80% = Partial · 0% = Absent</span>
    </div>

    {{-- Attendance Matrix --}}
    <form method="POST" action="{{ Auth::check() && Auth::user()->isTrainer()
        ? route('trainer.schedule.attendance.save', $schedule->id)
        : route('attendance.save', $schedule->id) }}">
        @csrf

        <div class="matrix-card">
            <div class="matrix-scroll">
                <table class="att-table">
                    <thead>
                        <tr>
                            <th style="min-width:200px;">Participant</th>
                            @foreach($sessionDates as $index => $date)
                                <th class="day-col">
                                    Day {{ $index + 1 }}<br>
                                    <span style="font-weight:600; color:#9ca3af; font-size:10px;">
                                        {{ $date->format('d M') }} ({{ $date->format('D') }})
                                    </span>
                                </th>
                            @endforeach
                            <th class="pct-col">Attendance %</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($schedule->enrollments as $enrollment)
                            @php
                                $enrollmentRecords = $records->get($enrollment->id, collect());
                                $totalSessions = $sessionDates->count();
                                $presentCount  = $enrollmentRecords->filter(fn($r) => in_array($r->status, ['Present','Late']))->count();
                                $pct = $totalSessions > 0 ? round(($presentCount / $totalSessions) * 100) : 0;
                                $pctClass = $pct === 0 ? 'pct-zero' : ($pct >= 80 ? 'pct-high' : ($pct >= 40 ? 'pct-mid' : 'pct-low'));
                            @endphp
                            <tr>
                                <td>
                                    <div class="participant-name">{{ $enrollment->full_name ?? '—' }}</div>
                                    <div class="participant-sub">{{ $enrollment->company ?? $enrollment->email ?? '' }}</div>
                                </td>

                                @foreach($sessionDates as $date)
                                    @php
                                        $dateStr = $date->toDateString();
                                        $record  = $enrollmentRecords->get($dateStr);
                                        $status  = $record?->status ?? 'Pending';
                                        $cssClass = 's-' . strtolower($status);
                                    @endphp
                                    <td style="text-align:center;">
                                        <select
                                            name="attendance[{{ $enrollment->id }}][{{ $dateStr }}][status]"
                                            class="status-select {{ $cssClass }}"
                                            onchange="this.className='status-select s-'+this.value.toLowerCase()">
                                            @foreach(['Pending','Present','Absent','Late','Excused'] as $opt)
                                                <option value="{{ $opt }}" {{ $status === $opt ? 'selected' : '' }}>
                                                    {{ $opt }}
                                                </option>
                                            @endforeach
                                        </select>
                                        {{-- Hidden fields to preserve check-in/out and remarks --}}
                                        <input type="hidden" name="attendance[{{ $enrollment->id }}][{{ $dateStr }}][check_in]"  value="{{ $record?->check_in_time ?? '' }}">
                                        <input type="hidden" name="attendance[{{ $enrollment->id }}][{{ $dateStr }}][check_out]" value="{{ $record?->check_out_time ?? '' }}">
                                        <input type="hidden" name="attendance[{{ $enrollment->id }}][{{ $dateStr }}][remarks]"   value="{{ $record?->remarks ?? '' }}">
                                    </td>
                                @endforeach

                                <td style="text-align:center;">
                                    <span class="pct-badge {{ $pctClass }}">{{ $pct }}%</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $sessionDates->count() + 2 }}"
                                    style="padding:32px; text-align:center; color:#9ca3af; font-size:14px;">
                                    No participants enrolled in this schedule.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="action-bar">
                <span class="action-bar-note">
                    Changes are applied to attendance summary on Save. Minimum 80% required for certificate eligibility.
                </span>
                <button type="submit" class="btn-save">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    Save Attendance
                </button>
            </div>
        </div>

    </form>

</div>

<script>
// Update select colour class on change (visible on same page without save)
document.querySelectorAll('.status-select').forEach(function(sel) {
    sel.addEventListener('change', function() {
        this.className = 'status-select s-' + this.value.toLowerCase();
    });
});
</script>

@endsection
