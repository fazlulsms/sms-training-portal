@extends('layouts.app')
@section('title', 'Certificate Management')
@section('content')

@php
    $eligible  = $participants->where(fn($p) => $p->attendance?->status === 'Present')->count();
    $generated = $participants->filter(fn($p) => $p->certificate !== null)->count();
    $pending   = $eligible - $generated;
    $absent    = $participants->where(fn($p) => $p->attendance?->status === 'Absent')->count();
    $pct       = $participants->count() > 0 ? round(($generated / $participants->count()) * 100) : 0;
@endphp

<style>
/* ── Breadcrumb ──── */
.breadcrumb { display:flex; align-items:center; gap:6px; font-size:12.5px; color:#9ca3af; margin-bottom:6px; }
.breadcrumb a { color:#6b7280; text-decoration:none; font-weight:600; }
.breadcrumb a:hover { color:#1e3a8a; }
.breadcrumb-sep { color:#d1d5db; }

/* ── Hero stat cards ──── */
.cert-stats { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; margin-bottom:24px; }
@media(max-width:640px){ .cert-stats { grid-template-columns:repeat(2,1fr); } }

.cert-stat {
    background:#fff; border:1px solid #e5e9f0; border-radius:16px;
    padding:20px 20px 16px; text-align:center; position:relative; overflow:hidden;
    box-shadow:0 1px 4px rgba(15,23,42,.04);
    transition:box-shadow .15s;
}
.cert-stat:hover { box-shadow:0 4px 18px rgba(15,23,42,.08); }
.cert-stat-accent { position:absolute; top:0; left:0; right:0; height:3px; border-radius:16px 16px 0 0; }
.cert-stat-icon { font-size:24px; margin-bottom:8px; }
.cert-stat-num   { font-size:30px; font-weight:900; line-height:1; margin-bottom:4px; }
.cert-stat-label { font-size:11px; color:#6b7280; font-weight:700; text-transform:uppercase; letter-spacing:.6px; }

/* ── Progress bar ──── */
.cert-progress-bar {
    background:#fff; border:1px solid #e5e9f0; border-radius:14px;
    padding:16px 22px; margin-bottom:18px;
    display:flex; align-items:center; gap:18px; flex-wrap:wrap;
    box-shadow:0 1px 3px rgba(15,23,42,.03);
}
.progress-track { flex:1; min-width:200px; background:#f0f2f7; border-radius:100px; height:10px; overflow:hidden; }
.progress-fill  { height:100%; border-radius:100px; background:linear-gradient(90deg,#7c3aed,#a78bfa); transition:width .4s ease; }

/* ── Generate banner ──── */
.generate-banner {
    background:linear-gradient(135deg,#fffbeb 0%,#fef9c3 100%);
    border:1.5px solid #fcd34d; border-radius:14px;
    padding:18px 22px; margin-bottom:20px;
    display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap;
}
.generate-banner-icon { font-size:32px; flex-shrink:0; }
.generate-banner-title { font-size:15px; font-weight:800; color:#78350f; margin-bottom:2px; }
.generate-banner-sub   { font-size:13px; color:#92400e; }

/* ── Participant rows ──── */
.cert-table { width:100%; border-collapse:collapse; }
.cert-table th {
    background:#fafbfd; padding:10px 16px;
    font-size:11px; font-weight:800; color:#6b7280;
    text-transform:uppercase; letter-spacing:.5px;
    border-bottom:1px solid #e9ecf0; text-align:left; white-space:nowrap;
}
.cert-table td { padding:13px 16px; border-bottom:1px solid #f4f5f8; vertical-align:middle; }
.cert-table tr:last-child td { border-bottom:none; }
.cert-table tr:hover td { background:#fafbfd; }

/* ── Participant name cell ──── */
.p-avatar {
    width:34px; height:34px; border-radius:50%;
    background:linear-gradient(135deg,#eff6ff,#dbeafe);
    display:flex; align-items:center; justify-content:center;
    font-size:13px; font-weight:800; color:#1e3a8a;
    flex-shrink:0;
}
.p-name  { font-size:14px; font-weight:700; color:#111827; line-height:1.2; }
.p-meta  { font-size:12px; color:#9ca3af; margin-top:2px; }

/* ── Cert number ──── */
.cert-number {
    font-family: 'Courier New', monospace;
    font-size:12.5px; font-weight:700; color:#7c3aed;
    background:#f5f3ff; border:1px solid #ddd6fe;
    padding:3px 10px; border-radius:8px; white-space:nowrap;
    display:inline-block;
}

/* ── Status badge ──── */
.att-badge { padding:3px 10px; border-radius:20px; font-size:11.5px; font-weight:800; white-space:nowrap; }

/* ── Action buttons ──── */
.btn-view-cert {
    display:inline-flex; align-items:center; gap:5px;
    background:#f5f3ff; color:#7c3aed;
    border:1.5px solid #ddd6fe; border-radius:9px;
    padding:5px 14px; font-size:12.5px; font-weight:700;
    text-decoration:none; transition:all .15s; white-space:nowrap;
}
.btn-view-cert:hover { background:#ede9fe; border-color:#c4b5fd; color:#6d28d9; }
.btn-gen-cert {
    display:inline-flex; align-items:center; gap:5px;
    background:#f0fdf4; color:#16a34a;
    border:1.5px solid #bbf7d0; border-radius:9px;
    padding:5px 14px; font-size:12.5px; font-weight:700;
    cursor:pointer; transition:all .15s; white-space:nowrap;
}
.btn-gen-cert:hover { background:#dcfce7; border-color:#86efac; }

/* ── Empty state ──── */
.empty-row td { text-align:center; padding:48px 24px; color:#9ca3af; }

@media(max-width:768px){
    .cert-table th:nth-child(2), .cert-table td:nth-child(2) { display:none; }
    .cert-table th:nth-child(5), .cert-table td:nth-child(5) { display:none; }
}
</style>

{{-- Breadcrumb --}}
<div class="breadcrumb">
    <a href="{{ route('corporate.projects.index') }}">Corporate</a>
    <span class="breadcrumb-sep">/</span>
    <a href="{{ route('corporate.projects.show', $session->project) }}">{{ $session->project->project_name }}</a>
    <span class="breadcrumb-sep">/</span>
    <a href="{{ route('corporate.sessions.show', $session) }}">{{ $session->course_name }}</a>
    <span class="breadcrumb-sep">/</span>
    <span>Certificates</span>
</div>

{{-- Page header --}}
<div class="page-header" style="margin-bottom:22px;">
    <div>
        <h1 class="page-title">Certificate Management</h1>
        <p class="page-subtitle">
            {{ $session->project->company_name }}
            <span style="color:#d1d5db;margin:0 6px;">·</span>
            {{ $session->course_name }}
            <span style="color:#d1d5db;margin:0 6px;">·</span>
            📅 {{ $session->training_date->format('d M Y') }}
        </p>
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap;">
        <a href="{{ route('corporate.sessions.show', $session) }}" class="btn btn-secondary">← Back</a>
        @if($generated > 0)
        <a href="{{ route('corporate.sessions.certificates.zip', $session) }}"
           class="btn btn-secondary" style="background:#f0fdf4;border-color:#bbf7d0;color:#16a34a;font-weight:700;">
            ⬇ Download ZIP ({{ $generated }})
        </a>
        @endif
    </div>
</div>

@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

{{-- Stat cards --}}
<div class="cert-stats">
    <div class="cert-stat">
        <div class="cert-stat-accent" style="background:#6b7280;"></div>
        <div class="cert-stat-icon">👥</div>
        <div class="cert-stat-num" style="color:#374151;">{{ $participants->count() }}</div>
        <div class="cert-stat-label">Total Participants</div>
    </div>
    <div class="cert-stat">
        <div class="cert-stat-accent" style="background:#16a34a;"></div>
        <div class="cert-stat-icon">✅</div>
        <div class="cert-stat-num" style="color:#16a34a;">{{ $eligible }}</div>
        <div class="cert-stat-label">Eligible (Present)</div>
    </div>
    <div class="cert-stat">
        <div class="cert-stat-accent" style="background:#7c3aed;"></div>
        <div class="cert-stat-icon">🏆</div>
        <div class="cert-stat-num" style="color:#7c3aed;">{{ $generated }}</div>
        <div class="cert-stat-label">Certificates Issued</div>
    </div>
    <div class="cert-stat">
        <div class="cert-stat-accent" style="background:{{ $pending > 0 ? '#d97706' : '#16a34a' }};"></div>
        <div class="cert-stat-icon">{{ $pending > 0 ? '⏳' : '🎉' }}</div>
        <div class="cert-stat-num" style="color:{{ $pending > 0 ? '#d97706' : '#16a34a' }};">{{ $pending }}</div>
        <div class="cert-stat-label">{{ $pending > 0 ? 'Pending' : 'All Done' }}</div>
    </div>
</div>

{{-- Progress bar --}}
<div class="cert-progress-bar">
    <div style="flex-shrink:0;">
        <div style="font-size:13px;font-weight:800;color:#374151;">Certificate Progress</div>
        <div style="font-size:12px;color:#9ca3af;margin-top:1px;">{{ $generated }} of {{ $eligible }} eligible participants issued</div>
    </div>
    <div class="progress-track">
        <div class="progress-fill" style="width:{{ $eligible > 0 ? round($generated/$eligible*100) : 0 }}%;"></div>
    </div>
    <div style="font-size:18px;font-weight:900;color:#7c3aed;flex-shrink:0;min-width:48px;text-align:right;">
        {{ $eligible > 0 ? round($generated/$eligible*100) : 0 }}%
    </div>
</div>

{{-- Generate banner --}}
@if($pending > 0)
<div class="generate-banner">
    <div style="display:flex;align-items:center;gap:16px;">
        <div class="generate-banner-icon">🏆</div>
        <div>
            <div class="generate-banner-title">{{ $pending }} certificate{{ $pending !== 1 ? 's' : '' }} ready to generate</div>
            <div class="generate-banner-sub">All Present-marked participants without a certificate will receive one.</div>
        </div>
    </div>
    <form method="POST" action="{{ route('corporate.sessions.certificates.bulk', $session) }}">
        @csrf
        <button type="submit" class="btn btn-primary"
                style="background:#d97706;border-color:#d97706;padding:10px 22px;font-size:14px;">
            🏆 Generate All {{ $pending }} Certificates
        </button>
    </form>
</div>
@elseif($generated > 0 && $pending === 0)
<div style="background:linear-gradient(135deg,#f0fdf4,#dcfce7);border:1.5px solid #86efac;border-radius:14px;padding:16px 22px;margin-bottom:20px;display:flex;align-items:center;gap:14px;">
    <span style="font-size:28px;">🎉</span>
    <div>
        <div style="font-size:14px;font-weight:800;color:#166534;">All certificates generated!</div>
        <div style="font-size:13px;color:#16a34a;margin-top:1px;">{{ $generated }} certificate{{ $generated !== 1 ? 's' : '' }} issued for all eligible participants.</div>
    </div>
</div>
@endif

{{-- Filter Bar --}}
<div style="background:#fff;border:1px solid #e5e9f0;border-radius:14px;padding:14px 18px;margin-bottom:18px;display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
    <form method="GET" action="{{ route('corporate.sessions.certificates.index', $session) }}" style="display:contents;">
        <div style="position:relative;">
            <span style="position:absolute;left:11px;top:50%;transform:translateY(-50%);color:#9ca3af;pointer-events:none;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </span>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search name or certificate no…"
                   style="padding:8px 12px 8px 36px;border:1.5px solid #e5e9f0;border-radius:9px;font-size:13.5px;font-family:inherit;width:240px;background:#fafbfd;outline:none;">
        </div>
        <select name="status" style="padding:8px 12px;border:1.5px solid #e5e9f0;border-radius:9px;font-size:13.5px;font-family:inherit;color:#374151;background:#fafbfd;cursor:pointer;outline:none;">
            <option value="">All Attendance</option>
            <option value="Present"    {{ request('status') === 'Present'    ? 'selected' : '' }}>Present</option>
            <option value="Absent"     {{ request('status') === 'Absent'     ? 'selected' : '' }}>Absent</option>
            <option value="Partial"    {{ request('status') === 'Partial'    ? 'selected' : '' }}>Partial</option>
        </select>
        <button type="submit" class="btn btn-primary" style="padding:8px 18px;">Filter</button>
        @if(request()->hasAny(['q','status']))
        <a href="{{ route('corporate.sessions.certificates.index', $session) }}" style="font-size:12.5px;color:#9ca3af;text-decoration:none;font-weight:600;padding:4px 8px;border-radius:7px;">✕ Clear</a>
        @endif
    </form>
    <div style="margin-left:auto;font-size:12.5px;color:#9ca3af;font-weight:600;white-space:nowrap;">{{ $participants->count() }} record(s)</div>
</div>

{{-- Participants table --}}
<div style="background:#fff;border:1px solid #e5e9f0;border-radius:16px;overflow:hidden;box-shadow:0 1px 4px rgba(15,23,42,.04);">

    {{-- Table header --}}
    <div style="padding:14px 20px 12px;border-bottom:1px solid #f0f2f7;background:#fafbfd;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
        <div>
            <span style="font-size:13px;font-weight:800;color:#111827;text-transform:uppercase;letter-spacing:.3px;">Participants</span>
            <span style="background:#f0f4ff;color:#1e3a8a;padding:2px 9px;border-radius:20px;font-size:12px;font-weight:800;margin-left:8px;">{{ $participants->count() }}</span>
        </div>
        <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
            {{-- Legend --}}
            <span style="font-size:12px;color:#9ca3af;display:flex;align-items:center;gap:4px;">
                <span style="width:8px;height:8px;border-radius:50%;background:#7c3aed;display:inline-block;"></span> Issued
            </span>
            <span style="font-size:12px;color:#9ca3af;display:flex;align-items:center;gap:4px;">
                <span style="width:8px;height:8px;border-radius:50%;background:#16a34a;display:inline-block;"></span> Eligible
            </span>
            <span style="font-size:12px;color:#9ca3af;display:flex;align-items:center;gap:4px;">
                <span style="width:8px;height:8px;border-radius:50%;background:#e5e7eb;display:inline-block;"></span> Not eligible
            </span>
        </div>
    </div>

    <div style="overflow-x:auto;">
        <table class="cert-table">
            <thead>
                <tr>
                    <th style="width:44px;padding-left:20px;">#</th>
                    <th>Participant</th>
                    <th>Position / Dept</th>
                    <th>Attendance</th>
                    <th>Certificate No.</th>
                    <th>Issued On</th>
                    <th style="text-align:right;padding-right:20px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($participants as $i => $p)
                @php
                    $att      = $p->attendance;
                    $cert     = $p->certificate;
                    $attColor = match($att?->status) { 'Present'=>'#16a34a','Absent'=>'#dc2626','Partial'=>'#d97706', default=>'#9ca3af' };
                    $attBg    = match($att?->status) { 'Present'=>'#dcfce7','Absent'=>'#fee2e2','Partial'=>'#fff7ed', default=>'#f3f4f6' };
                    $initials = collect(explode(' ', $p->participant_name))->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->implode('');
                    $rowBg    = $cert ? 'background:#faf5ff;' : '';
                @endphp
                <tr style="{{ $rowBg }}">
                    <td style="color:#9ca3af;font-size:12px;font-weight:700;padding-left:20px;">{{ $i + 1 }}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div class="p-avatar">{{ $initials }}</div>
                            <div>
                                <div class="p-name">{{ $p->participant_name }}</div>
                                @if($p->employee_id)
                                <div class="p-meta">{{ $p->employee_id }}</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($p->position || $p->department)
                        <div style="font-size:13px;color:#4b5563;">{{ $p->position }}</div>
                        @if($p->department)<div style="font-size:12px;color:#9ca3af;">{{ $p->department }}</div>@endif
                        @else
                        <span style="color:#d1d5db;font-size:13px;">—</span>
                        @endif
                    </td>
                    <td>
                        <span class="att-badge" style="background:{{ $attBg }};color:{{ $attColor }};">
                            {{ $att?->status ?? 'Not Marked' }}
                        </span>
                    </td>
                    <td>
                        @if($cert)
                        <span class="cert-number">{{ $cert->certificate_number }}</span>
                        @else
                        <span style="color:#d1d5db;font-size:13px;">—</span>
                        @endif
                    </td>
                    <td style="font-size:13px;color:#6b7280;white-space:nowrap;">
                        @if($cert)
                        {{ $cert->created_at->format('d M Y') }}
                        @else —
                        @endif
                    </td>
                    <td style="text-align:right;padding-right:16px;">
                        @if($cert)
                            <a href="{{ route('corporate.certificates.view', $cert) }}" target="_blank" class="btn-view-cert">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                View
                            </a>
                        @elseif($att?->status === 'Present')
                            <form method="POST" action="{{ route('corporate.sessions.certificates.generate', [$session, $p]) }}" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn-gen-cert">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                    Generate
                                </button>
                            </form>
                        @else
                            <span style="font-size:12px;color:#d1d5db;font-style:italic;">Not eligible</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr class="empty-row">
                    <td colspan="7">
                        <div style="font-size:32px;margin-bottom:10px;">👥</div>
                        <div style="font-size:15px;font-weight:700;color:#374151;margin-bottom:4px;">No participants yet</div>
                        <div style="font-size:13px;">
                            <a href="{{ route('corporate.sessions.participants.index', $session) }}" style="color:#1e3a8a;font-weight:700;">Add participants →</a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
