@extends('layouts.app')
@section('title', 'Corporate Projects')
@section('content')

<style>
/* ── Stat cards ─────────────────────────────── */
.corp-stats { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; margin-bottom:24px; }
@media(max-width:700px){ .corp-stats{ grid-template-columns:repeat(2,1fr); } }

.corp-stat {
    background:#fff; border:1px solid #e5e9f0; border-radius:16px;
    padding:20px 20px 16px; position:relative; overflow:hidden;
    box-shadow:0 1px 4px rgba(15,23,42,.04);
    transition:box-shadow .15s;
}
.corp-stat:hover { box-shadow:0 4px 18px rgba(15,23,42,.08); }
.corp-stat-accent {
    position:absolute; top:0; left:0; right:0; height:3px; border-radius:16px 16px 0 0;
}
.corp-stat-icon {
    width:38px; height:38px; border-radius:10px;
    display:flex; align-items:center; justify-content:center;
    font-size:17px; margin-bottom:12px;
}
.corp-stat-num  { font-size:30px; font-weight:900; line-height:1; margin-bottom:4px; }
.corp-stat-label{ font-size:11.5px; color:#6b7280; font-weight:700; text-transform:uppercase; letter-spacing:.5px; }

/* ── Filter bar ─────────────────────────────── */
.filter-bar {
    background:#fff; border:1px solid #e5e9f0; border-radius:14px;
    padding:14px 18px; margin-bottom:18px;
    display:flex; gap:10px; align-items:center; flex-wrap:wrap;
    box-shadow:0 1px 3px rgba(15,23,42,.03);
}
.filter-input {
    padding:8px 12px 8px 36px; border:1.5px solid #e5e9f0; border-radius:9px;
    font-size:13.5px; font-family:inherit; color:#111827; outline:none;
    transition:border-color .15s, box-shadow .15s; width:240px; background:#fafbfd;
}
.filter-input:focus { border-color:#1e3a8a; box-shadow:0 0 0 3px rgba(30,58,138,.08); background:#fff; }
.filter-select {
    padding:8px 12px; border:1.5px solid #e5e9f0; border-radius:9px;
    font-size:13.5px; font-family:inherit; color:#374151; outline:none;
    background:#fafbfd; cursor:pointer;
    transition:border-color .15s;
}
.filter-select:focus { border-color:#1e3a8a; }
.filter-search-wrap { position:relative; }
.filter-search-icon { position:absolute; left:11px; top:50%; transform:translateY(-50%); color:#9ca3af; pointer-events:none; }
.filter-clear { font-size:12.5px; color:#9ca3af; text-decoration:none; font-weight:600; padding:4px 8px; border-radius:7px; transition:color .15s; }
.filter-clear:hover { color:#ef4444; background:#fee2e2; }
.filter-count { margin-left:auto; font-size:12.5px; color:#9ca3af; font-weight:600; white-space:nowrap; }

/* ── Project cards ──────────────────────────── */
.project-list { display:flex; flex-direction:column; gap:10px; }

.project-card {
    background:#fff; border:1px solid #e5e9f0; border-radius:16px;
    padding:0; overflow:hidden;
    display:grid; grid-template-columns:auto 1fr auto;
    align-items:stretch;
    box-shadow:0 1px 4px rgba(15,23,42,.04);
    transition:box-shadow .15s, border-color .15s;
    text-decoration:none; color:inherit;
}
.project-card:hover { box-shadow:0 5px 22px rgba(15,23,42,.09); border-color:#c8d5f0; }

/* left color stripe */
.project-stripe {
    width:5px; flex-shrink:0; border-radius:0;
}

/* main body */
.project-body { padding:18px 20px; min-width:0; }
.project-top { display:flex; align-items:flex-start; gap:14px; margin-bottom:10px; }
.project-icon {
    width:44px; height:44px; border-radius:11px;
    background:#f0f4ff; display:flex; align-items:center; justify-content:center;
    font-size:20px; flex-shrink:0;
}
.project-titles { min-width:0; }
.project-name {
    font-size:15.5px; font-weight:800; color:#111827;
    white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
    margin-bottom:2px;
}
.project-company { font-size:13px; color:#6b7280; font-weight:600; }

.project-chips { display:flex; gap:8px; flex-wrap:wrap; align-items:center; }
.chip {
    display:inline-flex; align-items:center; gap:5px;
    background:#f4f6fb; border:1px solid #e5e9f0;
    padding:3px 10px; border-radius:20px;
    font-size:12px; font-weight:600; color:#4b5563;
    white-space:nowrap;
}
.chip-icon { font-size:11px; }

/* status badge */
.status-badge {
    padding:4px 12px; border-radius:20px;
    font-size:11.5px; font-weight:800; white-space:nowrap;
    letter-spacing:.2px;
}

/* right actions panel */
.project-actions-panel {
    padding:16px 18px; display:flex; flex-direction:column;
    justify-content:space-between; align-items:flex-end;
    gap:10px; border-left:1px solid #f0f2f7; min-width:130px;
}
.actions-row { display:flex; gap:6px; }

/* ── Empty state ────────────────────────────── */
.empty-state {
    background:#fff; border:1px solid #e5e9f0; border-radius:16px;
    padding:64px 32px; text-align:center;
    box-shadow:0 1px 4px rgba(15,23,42,.04);
}
.empty-icon { font-size:52px; margin-bottom:16px; }
.empty-title { font-size:18px; font-weight:800; color:#111827; margin-bottom:8px; }
.empty-text  { font-size:14px; color:#6b7280; margin-bottom:24px; }

@media(max-width:640px){
    .project-card { grid-template-columns:auto 1fr; }
    .project-actions-panel { display:none; }
}
</style>

{{-- Page header --}}
<div class="page-header">
    <div>
        <h1 class="page-title">Corporate Training Projects</h1>
        <p class="page-subtitle">Manage factory and corporate training engagements</p>
    </div>
    <a href="{{ route('corporate.projects.create') }}" class="btn btn-primary">
        <span style="font-size:16px;margin-right:4px;">+</span> New Project
    </a>
</div>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

{{-- Stat cards --}}
<div class="corp-stats">
    <div class="corp-stat">
        <div class="corp-stat-accent" style="background:#1e3a8a;"></div>
        <div class="corp-stat-icon" style="background:#eff6ff;">🏢</div>
        <div class="corp-stat-num" style="color:#1e3a8a;">{{ $stats['total_projects'] }}</div>
        <div class="corp-stat-label">Total Projects</div>
    </div>
    <div class="corp-stat">
        <div class="corp-stat-accent" style="background:#16a34a;"></div>
        <div class="corp-stat-icon" style="background:#f0fdf4;">✅</div>
        <div class="corp-stat-num" style="color:#16a34a;">{{ $stats['active_projects'] }}</div>
        <div class="corp-stat-label">Active Now</div>
    </div>
    <div class="corp-stat">
        <div class="corp-stat-accent" style="background:#d97706;"></div>
        <div class="corp-stat-icon" style="background:#fffbeb;">📅</div>
        <div class="corp-stat-num" style="color:#d97706;">{{ $stats['total_sessions'] }}</div>
        <div class="corp-stat-label">Training Sessions</div>
    </div>
    <div class="corp-stat">
        <div class="corp-stat-accent" style="background:#7c3aed;"></div>
        <div class="corp-stat-icon" style="background:#f5f3ff;">🏆</div>
        <div class="corp-stat-num" style="color:#7c3aed;">{{ $stats['total_certificates'] }}</div>
        <div class="corp-stat-label">Certificates Issued</div>
    </div>
</div>

{{-- Filter bar --}}
<div class="filter-bar">
    <form method="GET" action="{{ route('corporate.projects.index') }}"
          style="display:contents;">
        <div class="filter-search-wrap">
            <span class="filter-search-icon">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </span>
            <input type="text" name="q" value="{{ request('q') }}"
                   placeholder="Search project, company, contact, email…" class="filter-input" style="width:270px;">
        </div>

        <select name="status" class="filter-select">
            <option value="">All Statuses</option>
            @foreach(['Active','Completed','On Hold','Cancelled'] as $s)
            <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ $s }}</option>
            @endforeach
        </select>

        <input type="date" name="date_from" value="{{ request('date_from') }}" title="Created from"
               class="filter-select" style="cursor:pointer;" placeholder="From">
        <input type="date" name="date_to" value="{{ request('date_to') }}" title="Created to"
               class="filter-select" style="cursor:pointer;" placeholder="To">

        <button type="submit" class="btn btn-primary" style="padding:8px 18px;font-size:13.5px;">Filter</button>

        @if(request()->hasAny(['q','status','date_from','date_to']))
        <a href="{{ route('corporate.projects.index') }}" class="filter-clear">✕ Clear</a>
        @endif
    </form>

    <div class="filter-count">{{ $projects->total() }} project{{ $projects->total() !== 1 ? 's' : '' }}</div>
</div>

{{-- Project list --}}
@php $statusColors = \App\Models\CorporateProject::statusColors(); @endphp

@forelse($projects as $project)
@php
    $sc = $statusColors[$project->status] ?? '#6b7280';
    $statusBg = ['Active'=>'#dcfce7','Completed'=>'#dbeafe','On Hold'=>'#fff7ed','Cancelled'=>'#fee2e2'][$project->status] ?? '#f3f4f6';
@endphp

<div class="project-list" style="margin-bottom:0;">
<div class="project-card">

    {{-- Colour stripe --}}
    <div class="project-stripe" style="background:{{ $sc }};"></div>

    {{-- Main body --}}
    <div class="project-body">
        <div class="project-top">
            <div class="project-icon">🏢</div>
            <div class="project-titles" style="flex:1;min-width:0;">
                <div class="project-name">
                    <a href="{{ route('corporate.projects.show', $project) }}"
                       style="text-decoration:none;color:inherit;">{{ $project->project_name }}</a>
                </div>
                <div class="project-company">{{ $project->company_name }}</div>
            </div>
            <span class="status-badge" style="background:{{ $statusBg }};color:{{ $sc }};">
                {{ $project->status }}
            </span>
        </div>

        <div class="project-chips">
            @if($project->contact_person)
            <span class="chip">
                <span class="chip-icon">👤</span>{{ $project->contact_person }}
                @if($project->contact_designation) · <em style="font-style:normal;color:#9ca3af;">{{ $project->contact_designation }}</em> @endif
            </span>
            @endif
            @if($project->phone)
            <span class="chip"><span class="chip-icon">📞</span>{{ $project->phone }}</span>
            @endif
            <span class="chip" style="background:#f0f4ff;border-color:#c7d7f9;color:#1e3a8a;">
                <span class="chip-icon">📅</span>{{ $project->sessions_count ?? 0 }} session{{ ($project->sessions_count ?? 0) !== 1 ? 's' : '' }}
            </span>
            <span class="chip" style="background:#f0fdf4;border-color:#bbf7d0;color:#16a34a;">
                <span class="chip-icon">👥</span>{{ $project->participants_count ?? 0 }} participants
            </span>
            <span class="chip" style="background:#f5f3ff;border-color:#ddd6fe;color:#7c3aed;">
                <span class="chip-icon">🏆</span>{{ $project->certificates_count ?? 0 }} certificates
            </span>
        </div>
    </div>

    {{-- Actions panel --}}
    <div class="project-actions-panel">
        <div class="actions-row">
            <a href="{{ route('corporate.projects.show', $project) }}" class="btn btn-sm btn-secondary">View</a>
            <a href="{{ route('corporate.projects.edit', $project) }}" class="btn btn-sm btn-secondary">Edit</a>
        </div>
        <form method="POST" action="{{ route('corporate.projects.destroy', $project) }}"
              onsubmit="return confirm('Delete «{{ $project->project_name }}» and ALL its sessions, participants, and certificates?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-sm btn-danger" style="font-size:12px;">🗑 Delete</button>
        </form>
    </div>

</div>
</div>
@empty

<div class="empty-state">
    <div class="empty-icon">🏢</div>
    <div class="empty-title">No projects found</div>
    <div class="empty-text">
        @if(request()->hasAny(['q','status']))
            No projects match your current filters. <a href="{{ route('corporate.projects.index') }}" style="color:#1e3a8a;font-weight:700;">Clear filters</a>
        @else
            Get started by creating your first corporate training project.
        @endif
    </div>
    @if(!request()->hasAny(['q','status']))
    <a href="{{ route('corporate.projects.create') }}" class="btn btn-primary">+ Create First Project</a>
    @endif
</div>

@endforelse

@if($projects->hasPages())
<div style="margin-top:20px;">{{ $projects->links() }}</div>
@endif

@endsection
