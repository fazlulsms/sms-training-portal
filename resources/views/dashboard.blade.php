@extends('layouts.app')
@section('page-title', 'Dashboard')
@section('content')

<x-flash-message />

<style>
/* ── Dashboard-specific styles ───────────────────────────── */
.dash-header { margin-bottom: 22px; }
.dash-title { font-size: 22px; font-weight: 800; color: #111827; margin: 0 0 2px; }
.dash-sub   { font-size: 13px; color: #6b7280; font-weight: 500; }

.quick-actions {
    display: flex; flex-wrap: wrap; gap: 10px;
    margin-bottom: 26px;
}

.section-heading {
    font-size: 13px; font-weight: 800; text-transform: uppercase;
    letter-spacing: .8px; margin: 0 0 12px;
    display: flex; align-items: center; gap: 8px;
}
.sh-blue   { color: #2563eb; }
.sh-amber  { color: #d97706; }
.sh-slate  { color: #475569; }
.sh-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
.sh-dot-blue  { background: #3b82f6; }
.sh-dot-amber { background: #f59e0b; }
.sh-dot-slate { background: #94a3b8; }

.flow-section { margin-bottom: 28px; }

.stat-grid-6 {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 12px;
}
@media (max-width: 1300px) { .stat-grid-6 { grid-template-columns: repeat(3, 1fr); } }
@media (max-width: 768px)  { .stat-grid-6 { grid-template-columns: repeat(2, 1fr); } }

.stat-card-el {
    background: #fff; border: 1px solid #dbeafe; border-radius: 12px;
    padding: 14px 16px; display: flex; align-items: flex-start; gap: 12px;
    box-shadow: 0 1px 6px rgba(59,130,246,.07);
}
.stat-card-il {
    background: #fff; border: 1px solid #fde68a; border-radius: 12px;
    padding: 14px 16px; display: flex; align-items: flex-start; gap: 12px;
    box-shadow: 0 1px 6px rgba(245,158,11,.07);
}
.stat-card-fin {
    background: #fff; border: 1px solid #e2e8f0; border-radius: 12px;
    padding: 14px 16px; display: flex; align-items: flex-start; gap: 12px;
    box-shadow: 0 1px 6px rgba(15,23,42,.05);
}
.stat-icon-wrap {
    width: 40px; height: 40px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.si-blue  { background: #eff6ff; color: #2563eb; }
.si-indigo{ background: #eef2ff; color: #4f46e5; }
.si-cyan  { background: #ecfeff; color: #0891b2; }
.si-teal  { background: #f0fdf4; color: #059669; }
.si-purple{ background: #faf5ff; color: #7c3aed; }
.si-sky   { background: #f0f9ff; color: #0284c7; }
.si-amber { background: #fffbeb; color: #d97706; }
.si-orange{ background: #fff7ed; color: #ea580c; }
.si-lime  { background: #f7fee7; color: #65a30d; }
.si-rose  { background: #fff1f2; color: #e11d48; }
.si-green { background: #f0fdf4; color: #16a34a; }

.sc-val  { font-size: 22px; font-weight: 800; color: #111827; line-height: 1; }
.sc-label{ font-size: 11.5px; color: #6b7280; font-weight: 600; margin-top: 3px; }

.dash-table-wrap { background:#fff; border:1px solid #e5e7eb; border-radius:14px; overflow:hidden; box-shadow:0 1px 6px rgba(15,23,42,.05); }
.dash-table { width:100%; border-collapse:collapse; font-size:13px; }
.dash-table th { padding:9px 14px; background:#f8fafc; color:#6b7280; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.6px; text-align:left; border-bottom:1px solid #f1f5f9; }
.dash-table td { padding:10px 14px; border-bottom:1px solid #f8fafc; color:#374151; vertical-align:middle; }
.dash-table tr:last-child td { border-bottom:none; }
.dash-table tr:hover td { background:#fafcff; }

.ov-bar-wrap { margin-bottom: 12px; }
.ov-bar-label { display:flex; justify-content:space-between; margin-bottom:5px; font-size:12.5px; font-weight:600; }
.ov-bar { height:8px; background:#f1f5f9; border-radius:6px; overflow:hidden; }
.ov-fill { height:100%; border-radius:6px; }
.fill-blue   { background: #3b82f6; }
.fill-amber  { background: #f59e0b; }
.fill-slate  { background: #94a3b8; }
.fill-green  { background: #22c55e; }

.summ-row { display:flex; gap:10px; flex-wrap:wrap; margin-top:12px; }
.summ-pill { display:flex; align-items:center; gap:7px; padding:10px 16px; border-radius:9px; font-size:12.5px; font-weight:700; flex:1; min-width:0; }
.sp-open { background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0; }
.sp-done { background:#eff6ff; color:#1e40af; border:1px solid #bfdbfe; }
.sp-post { background:#fff7ed; color:#c2410c; border:1px solid #fed7aa; }
.summ-num { font-size:20px; font-weight:800; }

.two-col { display:grid; grid-template-columns:1fr 1fr; gap:18px; margin-bottom:28px; }
@media (max-width: 900px) { .two-col { grid-template-columns:1fr; } }
</style>

{{-- ══ HEADER ═══════════════════════════════════════════ --}}
<div class="dash-header">
    <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px;">
        <div>
            <h1 class="dash-title">Dashboard</h1>
            <p class="dash-sub">Training overview &mdash; {{ $currentYear }}</p>
        </div>
        <div class="quick-actions">
            <a href="{{ route('elearning.courses.index') }}" class="btn btn-primary btn-sm">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-right:5px"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Add E-Learning Course
            </a>
            <a href="/admin/training-schedules/create" class="btn btn-sm" style="background:#fffbeb;color:#d97706;border:1.5px solid #fde68a;font-weight:700;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-right:5px"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Add IL Schedule
            </a>
            <a href="/admin/enrollments/create" class="btn btn-ghost btn-sm">+ Enrollment</a>
            <a href="/admin/invoices/create" class="btn btn-ghost btn-sm">+ Invoice</a>
        </div>
    </div>
</div>

{{-- ══ E-LEARNING STATS ══════════════════════════════════ --}}
<div class="flow-section">
    <div class="section-heading sh-blue">
        <span class="sh-dot sh-dot-blue"></span>
        E-Learning &nbsp;/&nbsp; Self-Paced
        <span style="font-size:11px;font-weight:600;color:#93c5fd;text-transform:none;letter-spacing:0;">{{ $currentYear }}</span>
    </div>
    <div class="stat-grid-6">
        <div class="stat-card-el">
            <div class="stat-icon-wrap si-blue"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg></div>
            <div><div class="sc-val">{{ $elCourses }}</div><div class="sc-label">Total EL Courses</div></div>
        </div>
        <div class="stat-card-el">
            <div class="stat-icon-wrap si-indigo"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
            <div><div class="sc-val">{{ $elCoursesActive }}</div><div class="sc-label">Published Courses</div></div>
        </div>
        <div class="stat-card-el">
            <div class="stat-icon-wrap si-cyan"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
            <div><div class="sc-val">{{ $elEnrollments }}</div><div class="sc-label">Enrollments</div></div>
        </div>
        <div class="stat-card-el">
            <div class="stat-icon-wrap si-sky"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg></div>
            <div><div class="sc-val">{{ $elInProgress }}</div><div class="sc-label">In Progress</div></div>
        </div>
        <div class="stat-card-el">
            <div class="stat-icon-wrap si-teal"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></div>
            <div><div class="sc-val">{{ $elCompleted }}</div><div class="sc-label">Completed</div></div>
        </div>
        <div class="stat-card-el">
            <div class="stat-icon-wrap si-purple"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg></div>
            <div><div class="sc-val">{{ $elEligibleCerts }}</div><div class="sc-label">Eligible Certs</div></div>
        </div>
    </div>
</div>

{{-- ══ INSTRUCTOR-LED STATS ══════════════════════════════ --}}
<div class="flow-section">
    <div class="section-heading sh-amber">
        <span class="sh-dot sh-dot-amber"></span>
        Instructor-Led &nbsp;/&nbsp; Manual Training
        <span style="font-size:11px;font-weight:600;color:#fbbf24;text-transform:none;letter-spacing:0;">{{ $currentYear }}</span>
    </div>
    <div class="stat-grid-6">
        <div class="stat-card-il">
            <div class="stat-icon-wrap si-amber"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg></div>
            <div><div class="sc-val">{{ $manualCourses }}</div><div class="sc-label">Manual Courses</div></div>
        </div>
        <div class="stat-card-il">
            <div class="stat-icon-wrap si-orange"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
            <div><div class="sc-val">{{ $totalSchedules }}</div><div class="sc-label">Schedules</div></div>
        </div>
        <div class="stat-card-il">
            <div class="stat-icon-wrap si-lime"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg></div>
            <div><div class="sc-val">{{ $totalConfirmed }}</div><div class="sc-label">Confirmed</div></div>
        </div>
        <div class="stat-card-il">
            <div class="stat-icon-wrap si-amber"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg></div>
            <div><div class="sc-val">{{ $attendanceCompleted }}</div><div class="sc-label">Attendance Done</div></div>
        </div>
        <div class="stat-card-il">
            <div class="stat-icon-wrap si-orange"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></div>
            <div><div class="sc-val">{{ $certificatesIssued }}</div><div class="sc-label">Certs Issued</div></div>
        </div>
        <div class="stat-card-il">
            <div class="stat-icon-wrap si-rose"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg></div>
            <div><div class="sc-val">{{ $pendingPayments }}</div><div class="sc-label">Pending Payments</div></div>
        </div>
    </div>
</div>

{{-- ══ FINANCIAL OVERVIEW ════════════════════════════════ --}}
<div class="flow-section">
    <div class="section-heading sh-slate">
        <span class="sh-dot sh-dot-slate"></span>
        Financial Overview
    </div>
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;">
        <div class="stat-card-fin">
            <div class="stat-icon-wrap si-green"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></div>
            <div><div class="sc-val" style="color:#16a34a;">৳{{ number_format($totalPaidAmount, 0) }}</div><div class="sc-label">Total Paid</div></div>
        </div>
        <div class="stat-card-fin">
            <div class="stat-icon-wrap si-rose"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></div>
            <div><div class="sc-val" style="color:#e11d48;">৳{{ number_format($totalDueAmount, 0) }}</div><div class="sc-label">Total Due</div></div>
        </div>
        <div class="stat-card-fin">
            <div class="stat-icon-wrap si-amber"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
            <div><div class="sc-val" style="color:#d97706;">{{ $pendingPayments }}</div><div class="sc-label">Pending Enrollments</div></div>
        </div>
    </div>
</div>

{{-- ══ RECENT ENROLLMENTS + UPCOMING TRAININGS ══════════ --}}
<div class="two-col">

    <div>
        <div class="section-heading sh-blue" style="margin-bottom:10px;">
            <span class="sh-dot sh-dot-blue"></span>Recent E-Learning Enrollments
        </div>
        <div class="dash-table-wrap">
            <div style="overflow-x:auto;">
                <table class="dash-table">
                    <thead>
                        <tr><th>Participant</th><th>Course</th><th>Progress</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        @forelse($recentElEnrollments as $ee)
                        @php
                            $prog = $ee->progress_percentage ?? 0;
                            $comp = $ee->completion_status ?? 'not_started';
                            $compBadge = match($comp) { 'completed' => 'badge-success', 'in_progress' => 'badge-info', default => 'badge-secondary' };
                            $compLabel = match($comp) { 'completed' => 'Completed', 'in_progress' => 'In Progress', default => 'Not Started' };
                        @endphp
                        <tr>
                            <td style="font-weight:600;">{{ $ee->participant_name ?? optional($ee->user)->name ?? '—' }}</td>
                            <td style="max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:#6b7280;font-size:12px;">{{ optional($ee->course)->name ?? '—' }}</td>
                            <td style="min-width:80px;">
                                <div style="display:flex;align-items:center;gap:6px;">
                                    <div class="ov-bar" style="flex:1;"><div class="ov-fill fill-blue" style="width:{{ min($prog,100) }}%;"></div></div>
                                    <span style="font-size:11px;font-weight:700;color:#6b7280;">{{ $prog }}%</span>
                                </div>
                            </td>
                            <td><span class="badge {{ $compBadge }}">{{ $compLabel }}</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="4" style="text-align:center;padding:22px;color:#9ca3af;font-size:13px;">No eLearning enrollments yet</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div style="padding:10px 14px;border-top:1px solid #f1f5f9;">
                <a href="{{ route('elearning.enrollments.index') }}" class="btn btn-ghost btn-xs">View all →</a>
            </div>
        </div>
    </div>

    <div>
        <div class="section-heading sh-amber" style="margin-bottom:10px;">
            <span class="sh-dot sh-dot-amber"></span>Upcoming Instructor-Led Trainings
        </div>
        <div class="dash-table-wrap">
            <div style="overflow-x:auto;">
                <table class="dash-table">
                    <thead>
                        <tr><th>Date</th><th>Course</th><th>Trainer</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        @forelse($upcomingSchedules as $sched)
                        @php
                            $s = strtolower($sched->status ?? '');
                            $stBadge = match($s) { 'open' => 'badge-success', 'closed' => 'badge-danger', 'completed' => 'badge-secondary', default => 'badge-warning' };
                        @endphp
                        <tr>
                            <td class="nowrap" style="color:#6b7280;font-size:12px;">{{ $sched->start_date ? \Carbon\Carbon::parse($sched->start_date)->format('d M Y') : '—' }}</td>
                            <td style="font-weight:600;max-width:110px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ optional($sched->course)->name ?? '—' }}</td>
                            <td style="color:#6b7280;font-size:12px;">{{ optional($sched->trainer)->name ?? '—' }}</td>
                            <td><span class="badge {{ $stBadge }}">{{ ucfirst($sched->status ?? '—') }}</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="4" style="text-align:center;padding:22px;color:#9ca3af;font-size:13px;">No upcoming schedules</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div style="padding:10px 14px;border-top:1px solid #f1f5f9;">
                <a href="/admin/training-schedules" class="btn btn-ghost btn-xs">View all →</a>
            </div>
        </div>
    </div>

</div>

{{-- ══ PROGRESS OVERVIEWS ════════════════════════════════ --}}
<div class="two-col">

    <div class="dash-table-wrap" style="padding:18px;">
        <div class="section-heading sh-blue" style="margin-bottom:16px;">
            <span class="sh-dot sh-dot-blue"></span>E-Learning Progress Overview
        </div>
        @php
            $elTotal = $elCompleted + $elInProgress + $elNotStarted;
            $pctComp = $elTotal > 0 ? round($elCompleted / $elTotal * 100) : 0;
            $pctProg = $elTotal > 0 ? round($elInProgress / $elTotal * 100) : 0;
            $pctNot  = $elTotal > 0 ? round($elNotStarted / $elTotal * 100) : 0;
        @endphp
        <div class="ov-bar-wrap">
            <div class="ov-bar-label"><span style="color:#16a34a;">Completed</span><span style="font-weight:700;">{{ $elCompleted }} <span style="color:#9ca3af;font-weight:500;">({{ $pctComp }}%)</span></span></div>
            <div class="ov-bar"><div class="ov-fill fill-green" style="width:{{ $pctComp }}%;"></div></div>
        </div>
        <div class="ov-bar-wrap">
            <div class="ov-bar-label"><span style="color:#2563eb;">In Progress</span><span style="font-weight:700;">{{ $elInProgress }} <span style="color:#9ca3af;font-weight:500;">({{ $pctProg }}%)</span></span></div>
            <div class="ov-bar"><div class="ov-fill fill-blue" style="width:{{ $pctProg }}%;"></div></div>
        </div>
        <div class="ov-bar-wrap">
            <div class="ov-bar-label"><span style="color:#9ca3af;">Not Started</span><span style="font-weight:700;">{{ $elNotStarted }} <span style="color:#9ca3af;font-weight:500;">({{ $pctNot }}%)</span></span></div>
            <div class="ov-bar"><div class="ov-fill fill-slate" style="width:{{ $pctNot }}%;"></div></div>
        </div>
        <div style="margin-top:14px;padding-top:12px;border-top:1px solid #f1f5f9;font-size:12px;color:#6b7280;">
            <strong style="color:#1e40af;">{{ $elIssuedCerts }}</strong> certificates issued &nbsp;·&nbsp;
            <strong style="color:#7c3aed;">{{ $elEligibleCerts }}</strong> eligible
        </div>
    </div>

    <div class="dash-table-wrap" style="padding:18px;">
        <div class="section-heading sh-amber" style="margin-bottom:16px;">
            <span class="sh-dot sh-dot-amber"></span>Instructor-Led Summary
        </div>
        <div class="summ-row">
            <div class="summ-pill sp-open"><div><div class="summ-num">{{ $ilOpen }}</div><div style="font-size:11px;font-weight:600;opacity:.8;">Open</div></div></div>
            <div class="summ-pill sp-done"><div><div class="summ-num">{{ $ilCompleted }}</div><div style="font-size:11px;font-weight:600;opacity:.8;">Completed</div></div></div>
            <div class="summ-pill sp-post"><div><div class="summ-num">{{ $ilPostponed }}</div><div style="font-size:11px;font-weight:600;opacity:.8;">Postponed</div></div></div>
        </div>
        @php
            $ilTotal = $ilOpen + $ilCompleted + $ilPostponed;
            $pctIlDone = $ilTotal > 0 ? round($ilCompleted / $ilTotal * 100) : 0;
        @endphp
        <div style="margin-top:16px;padding-top:12px;border-top:1px solid #f1f5f9;">
            <div class="ov-bar-wrap" style="margin-bottom:6px;">
                <div class="ov-bar-label">
                    <span style="color:#6b7280;font-size:12px;">Completion Rate</span>
                    <span style="font-weight:700;font-size:12px;">{{ $pctIlDone }}%</span>
                </div>
                <div class="ov-bar"><div class="ov-fill fill-amber" style="width:{{ $pctIlDone }}%;"></div></div>
            </div>
        </div>
        <div style="margin-top:12px;font-size:12px;color:#6b7280;">
            <strong style="color:#d97706;">{{ $totalConfirmed }}</strong> confirmed enrollments &nbsp;·&nbsp;
            <strong style="color:#16a34a;">{{ $totalPaid }}</strong> fully paid
        </div>
    </div>

</div>

@endsection
