@extends('layouts.app')
@section('page-title', 'Trainer Dashboard')
@section('content')

<x-flash-message />

{{-- Welcome bar --}}
<div style="background:linear-gradient(135deg,#1e3a8a 0%,#1e40af 100%);border-radius:14px;padding:22px 26px;color:white;margin-bottom:24px;display:flex;align-items:center;gap:18px;box-shadow:0 4px 16px rgba(30,58,138,.2);">
    <div style="width:52px;height:52px;border-radius:50%;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;font-size:22px;font-weight:800;flex-shrink:0;">
        {{ strtoupper(substr($trainer->name, 0, 1)) }}
    </div>
    <div>
        <p style="font-size:20px;font-weight:800;margin:0 0 3px;">Welcome, {{ $trainer->name }}</p>
        <p style="font-size:13px;opacity:.8;margin:0;">{{ $trainer->designation ?? 'Trainer' }}{{ $trainer->organization ? ' · ' . $trainer->organization : '' }}</p>
    </div>
</div>

{{-- Stats --}}
<div class="stat-grid-4">
    <div class="stat-card">
        <div class="stat-icon stat-icon-blue">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        </div>
        <div>
            <div class="stat-label">Assigned Schedules</div>
            <div class="stat-value">{{ $totalSchedules }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-green">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
        <div>
            <div class="stat-label">Total Participants</div>
            <div class="stat-value">{{ $totalParticipants }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-teal">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
        </div>
        <div>
            <div class="stat-label">Present</div>
            <div class="stat-value">{{ $attendanceSummary['present'] }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-purple">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg>
        </div>
        <div>
            <div class="stat-label">Completed</div>
            <div class="stat-value">{{ $completionSummary['completed'] }}</div>
        </div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;">

    {{-- Upcoming session --}}
    <div class="card">
        <div class="card-header">
            <h3 style="display:flex;align-items:center;gap:8px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                Upcoming Session
            </h3>
        </div>
        <div class="card-body">
            @if($upcomingSchedule)
                <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:16px 18px;">
                    <div style="font-size:17px;font-weight:800;color:#92400e;margin-bottom:12px;">{{ $upcomingSchedule->course->name ?? 'Training' }}</div>
                    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;">
                        <div><div class="stat-label">Batch</div><div style="font-size:13px;font-weight:700;">{{ $upcomingSchedule->batch_code ?? '—' }}</div></div>
                        <div><div class="stat-label">Start Date</div><div style="font-size:13px;font-weight:700;">{{ \Carbon\Carbon::parse($upcomingSchedule->start_date)->format('d M Y') }}</div></div>
                        <div><div class="stat-label">Mode</div><div style="font-size:13px;font-weight:700;">{{ $upcomingSchedule->training_mode ?? '—' }}</div></div>
                    </div>
                </div>
            @else
                <p class="text-muted" style="text-align:center;margin:20px 0;">No upcoming sessions scheduled.</p>
            @endif
        </div>
    </div>

    {{-- Attendance overview --}}
    <div class="card">
        <div class="card-header">
            <h3 style="display:flex;align-items:center;gap:8px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                Attendance Overview
            </h3>
        </div>
        <div class="card-body">
            <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:10px;">
                <div style="background:#dcfce7;color:#166534;border-radius:9px;padding:12px;text-align:center;">
                    <div style="font-size:26px;font-weight:800;">{{ $attendanceSummary['present'] }}</div>
                    <div style="font-size:11px;font-weight:700;margin-top:2px;">Present</div>
                </div>
                <div style="background:#fee2e2;color:#991b1b;border-radius:9px;padding:12px;text-align:center;">
                    <div style="font-size:26px;font-weight:800;">{{ $attendanceSummary['absent'] }}</div>
                    <div style="font-size:11px;font-weight:700;margin-top:2px;">Absent</div>
                </div>
                <div style="background:#fef3c7;color:#92400e;border-radius:9px;padding:12px;text-align:center;">
                    <div style="font-size:26px;font-weight:800;">{{ $attendanceSummary['partial'] }}</div>
                    <div style="font-size:11px;font-weight:700;margin-top:2px;">Partial</div>
                </div>
                <div style="background:#f3f4f6;color:#6b7280;border-radius:9px;padding:12px;text-align:center;">
                    <div style="font-size:26px;font-weight:800;">{{ $attendanceSummary['pending'] }}</div>
                    <div style="font-size:11px;font-weight:700;margin-top:2px;">Pending</div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Recent Schedules --}}
<div class="card">
    <div class="card-header">
        <h3>My Schedules (Recent)</h3>
        <a href="{{ route('trainer.schedules') }}" style="font-size:12px;color:var(--sms-primary);font-weight:700;text-decoration:none;">View All →</a>
    </div>
    <div class="dt-scroll">
        <table class="dt">
            <thead>
                <tr>
                    <th>Course</th>
                    <th>Batch</th>
                    <th>Dates</th>
                    <th>Mode</th>
                    <th class="c">Enrolled</th>
                    <th class="c">Status</th>
                    <th class="c">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentSchedules as $schedule)
                @php
                    $s = strtolower($schedule->status ?? '');
                    $stBadge = match($s) {
                        'open'      => 'badge-success',
                        'closed'    => 'badge-danger',
                        'completed' => 'badge-secondary',
                        default     => 'badge-warning',
                    };
                @endphp
                <tr>
                    <td class="td-main">{{ $schedule->course->name ?? '—' }}</td>
                    <td>{{ $schedule->batch_code ?? '—' }}</td>
                    <td class="nowrap text-muted text-small">
                        {{ $schedule->start_date ? \Carbon\Carbon::parse($schedule->start_date)->format('d M Y') : '—' }}
                        @if($schedule->end_date) → {{ \Carbon\Carbon::parse($schedule->end_date)->format('d M Y') }} @endif
                    </td>
                    <td>{{ $schedule->training_mode ?? '—' }}</td>
                    <td class="c">
                        <span style="background:#eff6ff;color:#1e3a8a;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:700;">{{ $schedule->enrollments->count() }}</span>
                    </td>
                    <td class="c"><span class="badge {{ $stBadge }}">{{ ucfirst($schedule->status ?? '—') }}</span></td>
                    <td class="c">
                        <a href="{{ route('trainer.schedule.participants', $schedule->id) }}" class="btn btn-edit btn-xs">
                            Participants →
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <p class="empty-title">No schedules assigned yet</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
