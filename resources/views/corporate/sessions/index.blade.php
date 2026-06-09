@extends('layouts.app')
@section('title', 'Training Sessions')
@section('content')

<style>
/* ── Filter bar ─────────────────────────────── */
.filter-bar { background:#fff; border:1px solid #e5e9f0; border-radius:14px; padding:14px 18px; margin-bottom:18px; display:flex; gap:10px; align-items:center; flex-wrap:wrap; box-shadow:0 1px 3px rgba(15,23,42,.03); }
.filter-input { padding:8px 12px 8px 36px; border:1.5px solid #e5e9f0; border-radius:9px; font-size:13.5px; font-family:inherit; color:#111827; outline:none; transition:border-color .15s,box-shadow .15s; width:220px; background:#fafbfd; }
.filter-input:focus { border-color:#1e3a8a; box-shadow:0 0 0 3px rgba(30,58,138,.08); background:#fff; }
.filter-select { padding:8px 12px; border:1.5px solid #e5e9f0; border-radius:9px; font-size:13.5px; font-family:inherit; color:#374151; outline:none; background:#fafbfd; cursor:pointer; transition:border-color .15s; }
.filter-select:focus { border-color:#1e3a8a; }
.filter-search-wrap { position:relative; }
.filter-search-icon { position:absolute; left:11px; top:50%; transform:translateY(-50%); color:#9ca3af; pointer-events:none; }
.filter-clear { font-size:12.5px; color:#9ca3af; text-decoration:none; font-weight:600; padding:4px 8px; border-radius:7px; transition:color .15s; }
.filter-clear:hover { color:#ef4444; background:#fee2e2; }
.filter-count { margin-left:auto; font-size:12.5px; color:#9ca3af; font-weight:600; white-space:nowrap; }

/* ── Session cards ──────────────────────────── */
.session-card {
    background:#fff; border:1px solid #e5e9f0; border-radius:16px;
    margin-bottom:10px; overflow:hidden;
    display:grid; grid-template-columns:5px 1fr auto;
    box-shadow:0 1px 4px rgba(15,23,42,.04);
    transition:box-shadow .15s, border-color .15s;
}
.session-card:hover { box-shadow:0 5px 22px rgba(15,23,42,.09); border-color:#c8d5f0; }
.session-stripe { flex-shrink:0; }
.session-body { padding:16px 20px; }
.session-top { display:flex; align-items:flex-start; justify-content:space-between; gap:12px; margin-bottom:10px; flex-wrap:wrap; }
.session-name { font-size:15px; font-weight:800; color:#111827; margin-bottom:3px; text-decoration:none; }
.session-name:hover { color:#1e3a8a; }
.session-project { font-size:12.5px; color:#6b7280; font-weight:600; }
.session-chips { display:flex; gap:7px; flex-wrap:wrap; }
.chip { display:inline-flex; align-items:center; gap:5px; background:#f4f6fb; border:1px solid #e5e9f0; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:600; color:#4b5563; white-space:nowrap; }
.status-badge { padding:4px 12px; border-radius:20px; font-size:11.5px; font-weight:800; white-space:nowrap; flex-shrink:0; }
.session-actions { padding:16px 18px; display:flex; flex-direction:column; justify-content:space-between; align-items:flex-end; gap:8px; border-left:1px solid #f0f2f7; min-width:120px; }
.actions-row { display:flex; gap:6px; }

.empty-state { background:#fff; border:1px solid #e5e9f0; border-radius:16px; padding:64px 32px; text-align:center; box-shadow:0 1px 4px rgba(15,23,42,.04); }
</style>

<div class="page-header">
    <div>
        <h1 class="page-title">Training Sessions</h1>
        <p class="page-subtitle">All corporate training sessions across projects</p>
    </div>
    <a href="{{ route('corporate.sessions.create') }}" class="btn btn-primary">
        <span style="font-size:16px;margin-right:4px;">+</span> New Session
    </a>
</div>

@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

{{-- Filter bar --}}
<div class="filter-bar">
    <form method="GET" action="{{ route('corporate.sessions.index') }}" style="display:contents;">
        <div class="filter-search-wrap">
            <span class="filter-search-icon">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </span>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search course, trainer, venue…" class="filter-input">
        </div>

        <select name="project_id" class="filter-select">
            <option value="">All Projects</option>
            @foreach($projects as $p)
            <option value="{{ $p->id }}" {{ request('project_id') == $p->id ? 'selected' : '' }}>{{ $p->project_name }}</option>
            @endforeach
        </select>

        <select name="status" class="filter-select">
            <option value="">All Statuses</option>
            @foreach(['Planned','Ongoing','Completed','Cancelled'] as $s)
            <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ $s }}</option>
            @endforeach
        </select>

        <input type="date" name="date_from" value="{{ request('date_from') }}" title="Training date from"
               class="filter-select" style="cursor:pointer;">
        <input type="date" name="date_to" value="{{ request('date_to') }}" title="Training date to"
               class="filter-select" style="cursor:pointer;">

        <button type="submit" class="btn btn-primary" style="padding:8px 18px;font-size:13.5px;">Filter</button>

        @if(request()->hasAny(['q','project_id','status','date_from','date_to']))
        <a href="{{ route('corporate.sessions.index') }}" class="filter-clear">✕ Clear</a>
        @endif
    </form>
    <div class="filter-count">{{ $sessions->total() }} session{{ $sessions->total() !== 1 ? 's' : '' }}</div>
</div>

{{-- Session cards --}}
@php
$statusColors = \App\Models\CorporateSession::statusColors();
$statusBgs = ['Planned'=>'#f0f4ff','Ongoing'=>'#f0fdf4','Completed'=>'#dbeafe','Cancelled'=>'#fee2e2'];
@endphp

@forelse($sessions as $session)
@php
    $sc  = $statusColors[$session->status] ?? '#6b7280';
    $sbg = $statusBgs[$session->status] ?? '#f3f4f6';
@endphp
<div class="session-card">
    <div class="session-stripe" style="background:{{ $sc }};"></div>
    <div class="session-body">
        <div class="session-top">
            <div style="min-width:0;">
                <a href="{{ route('corporate.sessions.show', $session) }}" class="session-name">{{ $session->course_name }}</a>
                <div class="session-project">
                    <a href="{{ route('corporate.projects.show', $session->project) }}"
                       style="color:#1e3a8a;font-weight:700;text-decoration:none;">{{ $session->project->company_name }}</a>
                    <span style="color:#d1d5db;margin:0 4px;">·</span>
                    <span style="color:#9ca3af;">{{ $session->project->project_name }}</span>
                </div>
            </div>
            <span class="status-badge" style="background:{{ $sbg }};color:{{ $sc }};">{{ $session->status }}</span>
        </div>

        <div class="session-chips">
            <span class="chip" style="background:#f0f4ff;border-color:#c7d7f9;color:#1e3a8a;">
                📅 {{ $session->training_date->format('d M Y') }}
                @if($session->training_date_end && $session->training_date_end != $session->training_date)
                – {{ $session->training_date_end->format('d M Y') }}
                @endif
            </span>
            @if($session->trainer_name)
            <span class="chip">👤 {{ $session->trainer_name }}</span>
            @endif
            @if($session->venue)
            <span class="chip">📍 {{ $session->venue }}</span>
            @endif
            @if($session->duration)
            <span class="chip">⏱ {{ $session->duration }}</span>
            @endif
            <span class="chip" style="background:#f0fdf4;border-color:#bbf7d0;color:#16a34a;">
                👥 {{ $session->participants_count }} participant{{ $session->participants_count !== 1 ? 's' : '' }}
            </span>
            @if($session->target_group)
            <span class="chip" style="background:#fefce8;border-color:#fef08a;color:#854d0e;">{{ $session->target_group }}</span>
            @endif
        </div>
    </div>

    <div class="session-actions">
        <div class="actions-row">
            <a href="{{ route('corporate.sessions.show', $session) }}" class="btn btn-sm btn-secondary">View</a>
            <a href="{{ route('corporate.sessions.edit', $session) }}" class="btn btn-sm btn-secondary">Edit</a>
        </div>
        <form method="POST" action="{{ route('corporate.sessions.destroy', $session) }}"
              onsubmit="return confirm('Delete this session and all its data?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-sm btn-danger" style="font-size:12px;">🗑 Delete</button>
        </form>
    </div>
</div>
@empty

<div class="empty-state">
    <div style="font-size:48px;margin-bottom:14px;">📅</div>
    <div style="font-size:18px;font-weight:800;color:#111827;margin-bottom:8px;">No sessions found</div>
    <div style="font-size:14px;color:#6b7280;margin-bottom:22px;">
        @if(request()->hasAny(['q','project_id','status']))
            No sessions match your filters. <a href="{{ route('corporate.sessions.index') }}" style="color:#1e3a8a;font-weight:700;">Clear filters</a>
        @else
            Create your first training session to get started.
        @endif
    </div>
    @if(!request()->hasAny(['q','project_id','status']))
    <a href="{{ route('corporate.sessions.create') }}" class="btn btn-primary">+ New Session</a>
    @endif
</div>

@endforelse

@if($sessions->hasPages())
<div style="margin-top:20px;">{{ $sessions->links() }}</div>
@endif
@endsection
