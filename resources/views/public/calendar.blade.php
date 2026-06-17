@extends('layouts.public')

@section('page-title', 'Training Calendar')
@section('seo-title', 'Training Calendar — SMS Training Academy')
@section('seo-desc', 'Browse upcoming public training sessions and register online. View our complete schedule of open-enrolment training programmes.')
@section('seo-keys', 'training schedule, upcoming training, SMS Training Academy calendar, training archive')

@section('content')
<style>
/* ── Hero ── */
.cal-hero {
    background: linear-gradient(135deg, #060d2e 0%, #042C53 45%, #042C53 100%);
    padding: 40px 0 48px; color: #fff; position: relative; overflow: hidden;
}
.cal-hero::after {
    content: ''; position: absolute; inset: 0;
    background-image: radial-gradient(rgba(255,255,255,.04) 1px, transparent 1px);
    background-size: 26px 26px; pointer-events: none;
}
.cal-hero-inner { position: relative; z-index: 1; }
.cal-hero h1 { font-size: 30px; font-weight: 900; margin: 0 0 6px; }
.cal-hero p  { font-size: 14.5px; opacity: .65; margin: 0; }
@media(max-width:640px){ .cal-hero h1 { font-size: 22px; } }

/* ── Tabs ── */
.cal-tabs-bar {
    background: #fff; border-bottom: 2px solid #f1f5f9;
    position: sticky; top: 68px; z-index: 20;
}
.cal-tabs-inner { display: flex; }
.cal-tab {
    padding: 13px 20px; font-size: 13.5px; font-weight: 700; color: #6b7280;
    text-decoration: none; display: inline-flex; align-items: center; gap: 7px;
    border-bottom: 2.5px solid transparent; margin-bottom: -2px;
    transition: color .12s, border-color .12s; white-space: nowrap;
}
.cal-tab:hover  { color: #042C53; }
.cal-tab.active { color: #042C53; border-bottom-color: #042C53; }
.cal-tab-badge  {
    background: #f1f5f9; color: #6b7280;
    font-size: 11px; font-weight: 800; padding: 2px 8px; border-radius: 20px;
}
.cal-tab.active .cal-tab-badge { background: #042C53; color: #fff; }

/* ── Filter bar ── */
.cal-filter-bar { background: #f8fafc; border-bottom: 1px solid #e9ecf0; padding: 11px 0; }
.cal-filter-row { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
.cal-fi {
    padding: 7px 11px; border: 1.5px solid #e5e7eb; border-radius: 8px;
    font-size: 13px; font-family: inherit; color: #374151; background: #fff;
    transition: border-color .12s; min-width: 110px;
}
.cal-fi:focus { outline: none; border-color: #042C53; }
.cal-fi-wide  { min-width: 190px; }
.cal-fi-btn {
    padding: 7px 15px; background: #042C53; color: #fff; border: none;
    border-radius: 8px; font-weight: 700; font-size: 13px;
    cursor: pointer; font-family: inherit; transition: opacity .12s;
    display: inline-flex; align-items: center; gap: 5px;
}
.cal-fi-btn:hover { opacity: .88; }
.cal-fi-reset {
    font-size: 13px; color: #9ca3af; text-decoration: none;
    font-weight: 600; padding: 7px 2px; transition: color .12s;
    display: inline-flex; align-items: center; gap: 4px;
}
.cal-fi-reset:hover { color: #374151; }

/* ── Body ── */
.cal-body { padding: 26px 0 64px; }
.cal-result-bar {
    font-size: 13px; color: #6b7280; font-weight: 600; margin-bottom: 6px;
}
.cal-result-bar strong { color: #111827; }

/* ── Month heading ── */
.cal-month-head {
    display: flex; align-items: center; gap: 12px;
    margin: 30px 0 12px;
}
.cal-month-head:first-child { margin-top: 4px; }
.cal-month-label {
    font-size: 11px; font-weight: 900; color: #374151;
    text-transform: uppercase; letter-spacing: 1px; white-space: nowrap;
}
.cal-month-count { font-size: 11px; color: #9ca3af; font-weight: 600; white-space: nowrap; }
.cal-month-rule  { flex: 1; height: 1px; background: #e2e8f0; }

/* ── Year heading (archive) ── */
.cal-year-head {
    display: flex; align-items: center; gap: 12px;
    margin: 38px 0 6px;
}
.cal-year-head:first-child { margin-top: 4px; }
.cal-year-label { font-size: 18px; font-weight: 900; color: #111827; white-space: nowrap; }
.cal-year-count { font-size: 12px; color: #9ca3af; font-weight: 600; }
.cal-year-rule  { flex: 1; height: 2px; background: #e2e8f0; }

/* Archive month heading — nested, lighter */
.cal-month-head--arc .cal-month-label { color: #64748b; letter-spacing: .7px; }
.cal-month-head--arc { margin-top: 20px; }

/* ── Schedule card grid — 2 per row ── */
.schedules-grid {
    display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px;
}
@media(max-width: 768px) { .schedules-grid { grid-template-columns: 1fr; } }

/* ── Schedule card (identical to homepage) ── */
.schedule-card {
    background: #fff; border: 1px solid #e9ecf0; border-radius: 14px;
    padding: 20px; display: flex; gap: 18px;
    transition: box-shadow .2s; box-shadow: 0 2px 8px rgba(15,23,42,.05);
}
.schedule-card:hover { box-shadow: 0 6px 20px rgba(15,23,42,.1); }
.schedule-date-block {
    background: #042C53; color: #fff;
    border-radius: 10px; padding: 10px 14px; text-align: center;
    flex-shrink: 0; min-width: 56px;
    display: flex; flex-direction: column; align-items: center; justify-content: center;
}
.sc-day  { font-size: 22px; font-weight: 900; line-height: 1; }
.sc-mon  { font-size: 11px; font-weight: 700; opacity: .8; text-transform: uppercase; margin-top: 2px; }
.sc-body { flex: 1; min-width: 0; }
.sc-title {
    font-size: 14.5px; font-weight: 800; color: #111827; margin: 0 0 6px;
    line-height: 1.35; text-decoration: none; display: block;
}
.sc-title:hover { color: #042C53; }
.sc-meta {
    font-size: 12.5px; color: #6b7280;
    display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 12px;
}
.sc-meta-item { display: inline-flex; align-items: center; gap: 4px; }
.sc-meta-item svg { opacity: .7; flex-shrink: 0; }
.sc-mode-badge {
    padding: 2px 8px; border-radius: 20px; font-size: 11px; font-weight: 700;
}
.scm-physical { background: #f0fdf4; color: #15803d; }
.scm-online   { background: #eff6ff; color: #1a5f9e; }
.scm-hybrid   { background: #fff7ed; color: #c2410c; }
.sc-fee { font-size: 15px; font-weight: 900; color: #042C53; }

/* Archive card variant */
.schedule-card--arc { opacity: .88; }
.schedule-card--arc .schedule-date-block { background: #64748b; }
.schedule-card--arc .sc-title { color: #374151; }
.schedule-card--arc:hover { opacity: 1; box-shadow: 0 4px 14px rgba(15,23,42,.07); }

.arc-completed {
    display: inline-flex; align-items: center; gap: 4px;
    background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0;
    padding: 2px 9px; border-radius: 20px; font-size: 11px; font-weight: 700;
}
.arc-view-btn {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 6px 12px; border: 1.5px solid #d1d5db; color: #6b7280;
    border-radius: 7px; font-size: 12px; font-weight: 700;
    text-decoration: none; white-space: nowrap; transition: all .12s;
}
.arc-view-btn:hover { border-color: #042C53; color: #042C53; background: #f5f8ff; }

/* ── Year filter pills ── */
.arc-pills { display: flex; gap: 7px; flex-wrap: wrap; margin-bottom: 20px; }
.arc-pill {
    padding: 5px 14px; border-radius: 20px; font-size: 12.5px; font-weight: 700;
    text-decoration: none; border: 1.5px solid #e9ecf0; color: #6b7280; background: #fff;
    transition: all .12s;
}
.arc-pill:hover  { border-color: #042C53; color: #042C53; }
.arc-pill.active { background: #042C53; border-color: #042C53; color: #fff; }

/* ── Empty state ── */
.cal-empty {
    padding: 60px 24px; text-align: center;
    background: #f8fafc; border-radius: 14px; border: 1.5px dashed #e2e8f0; margin-top: 8px;
}
.cal-empty-icon {
    width: 50px; height: 50px; border-radius: 14px;
    background: linear-gradient(135deg, #eff6ff, #dbeafe);
    display: flex; align-items: center; justify-content: center; margin: 0 auto 14px;
}
.cal-empty h3 { font-size: 17px; font-weight: 800; color: #111827; margin: 0 0 8px; }
.cal-empty p  { font-size: 14px; color: #6b7280; margin: 0 0 20px; line-height: 1.7; }
.cal-empty-cta {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 10px 22px; background: #042C53; color: #fff;
    border-radius: 9px; font-weight: 700; font-size: 14px;
    text-decoration: none; transition: opacity .12s;
}
.cal-empty-cta:hover { opacity: .9; }
.cal-empty-note { font-size: 13px; color: #9ca3af; margin-top: 10px; display: block; }
.cal-empty-note a { color: #042C53; font-weight: 700; text-decoration: none; }

/* ── Pagination ── */
.cal-pagination { margin-top: 24px; }
</style>

{{-- ── Hero ── --}}
<div class="cal-hero">
<div class="pub-container">
<div class="cal-hero-inner">
    <h1>Training Calendar</h1>
    <p>Public open-enrolment sessions — browse by month and register online.</p>
</div>
</div>
</div>

{{-- ── Tabs ── --}}
<div class="cal-tabs-bar">
<div class="pub-container">
    <div class="cal-tabs-inner">
        <a href="{{ route('public.calendar', array_merge(request()->except(['tab','page']), ['tab'=>'upcoming'])) }}"
           class="cal-tab {{ $tab === 'upcoming' ? 'active' : '' }}">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            Upcoming
            <span class="cal-tab-badge">{{ $upcoming->total() }}</span>
        </a>
        <a href="{{ route('public.calendar', array_merge(request()->except(['tab','page','month']), ['tab'=>'archive'])) }}"
           class="cal-tab {{ $tab === 'archive' ? 'active' : '' }}">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="21 8 21 21 3 21 3 8"/><rect x="1" y="3" width="22" height="5"/><line x1="10" y1="12" x2="14" y2="12"/></svg>
            Past / Archive
            <span class="cal-tab-badge">{{ $past->count() }}</span>
        </a>
    </div>
</div>
</div>

{{-- ── Filters ── --}}
<div class="cal-filter-bar">
<div class="pub-container">
    <form method="GET" action="{{ route('public.calendar') }}">
        <input type="hidden" name="tab" value="{{ $tab }}">
        <div class="cal-filter-row">
            @if($tab === 'upcoming')
                <input type="month" name="month" class="cal-fi" value="{{ request('month') }}" title="Filter by month">
            @else
                <select name="year" class="cal-fi">
                    <option value="">All Years</option>
                    @foreach($archiveYears as $yr)
                    <option value="{{ $yr }}" {{ request('year') == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                    @endforeach
                </select>
            @endif
            <select name="mode" class="cal-fi">
                <option value="">All Modes</option>
                @foreach(['Physical','Online','Hybrid'] as $m)
                <option value="{{ $m }}" {{ request('mode') === $m ? 'selected' : '' }}>{{ $m }}</option>
                @endforeach
            </select>
            <select name="course" class="cal-fi cal-fi-wide">
                <option value="">All Courses</option>
                @foreach($courses as $c)
                <option value="{{ $c->id }}" {{ request('course') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="cal-fi-btn">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                Search
            </button>
            @if(request()->hasAny(['month','mode','course','year']))
            <a href="{{ route('public.calendar', ['tab'=>$tab]) }}" class="cal-fi-reset">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                Reset
            </a>
            @endif
        </div>
    </form>
</div>
</div>

{{-- ── Body ── --}}
<div class="pub-container">
<div class="cal-body">

{{-- ═══════════════════ UPCOMING TAB ═══════════════════ --}}
@if($tab === 'upcoming')

@if($upcoming->isEmpty())
<div class="cal-empty">
    <div class="cal-empty-icon">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#042C53" stroke-width="1.8"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
    </div>
    <h3>No upcoming training sessions scheduled</h3>
    <p>Please check the archive or contact us for corporate training enquiries.@if(request()->hasAny(['month','mode','course'])) Try clearing your filters.@endif</p>
    <a href="{{ route('public.contact') }}" class="cal-empty-cta">Request a Training</a>
    <span class="cal-empty-note">View past sessions in the <a href="{{ route('public.calendar', ['tab'=>'archive']) }}">Archive</a>.</span>
</div>
@else

@php
    $upcomingByMonth = $upcoming->getCollection()->groupBy(function($s) {
        return \Carbon\Carbon::parse($s->start_date)->format('F Y');
    });
@endphp

<p class="cal-result-bar">
    <strong>{{ $upcoming->total() }}</strong> upcoming {{ Str::plural('session', $upcoming->total()) }}
    @if(request()->hasAny(['month','mode','course'])) <span style="color:#9ca3af;font-weight:400;">— filtered</span>@endif
</p>

@foreach($upcomingByMonth as $monthLabel => $sessions)

<div class="cal-month-head">
    <span class="cal-month-label">{{ $monthLabel }}</span>
    <span class="cal-month-count">{{ $sessions->count() }} {{ Str::plural('session', $sessions->count()) }}</span>
    <div class="cal-month-rule"></div>
</div>

<div class="schedules-grid">
    @foreach($sessions as $s)
    @include('public.partials.schedule-card', ['schedule' => $s])
    @endforeach
</div>

@endforeach

@if($upcoming->hasPages())
<div class="cal-pagination">{{ $upcoming->links() }}</div>
@endif

@endif {{-- end upcoming not empty --}}


{{-- ═══════════════════ ARCHIVE TAB ═══════════════════ --}}
@else

@if($past->isEmpty())
<div class="cal-empty">
    <div class="cal-empty-icon">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#042C53" stroke-width="1.8"><polyline points="21 8 21 21 3 21 3 8"/><rect x="1" y="3" width="22" height="5"/><line x1="10" y1="12" x2="14" y2="12"/></svg>
    </div>
    <h3>No archive records found</h3>
    <p>No past training sessions match your filters.@if(request()->hasAny(['year','mode','course'])) Try clearing the filters.@endif</p>
    <a href="{{ route('public.calendar', ['tab'=>'archive']) }}" class="cal-empty-cta">View Full Archive</a>
</div>
@else

{{-- Year filter pills --}}
@if($archiveYears->count() > 1)
<div class="arc-pills">
    <a href="{{ route('public.calendar', array_merge(request()->except(['year','page']), ['tab'=>'archive'])) }}"
       class="arc-pill {{ !request('year') ? 'active' : '' }}">All Years</a>
    @foreach($archiveYears as $yr)
    <a href="{{ route('public.calendar', array_merge(request()->except(['year','page']), ['tab'=>'archive','year'=>$yr])) }}"
       class="arc-pill {{ request('year') == $yr ? 'active' : '' }}">{{ $yr }}</a>
    @endforeach
</div>
@endif

@php
    $pastNested = [];
    foreach ($past as $s) {
        $yr    = \Carbon\Carbon::parse($s->start_date)->year;
        $moKey = \Carbon\Carbon::parse($s->start_date)->format('Y-m');
        $moLbl = \Carbon\Carbon::parse($s->start_date)->format('F Y');
        if (!isset($pastNested[$yr][$moKey])) {
            $pastNested[$yr][$moKey] = ['label' => $moLbl, 'items' => []];
        }
        $pastNested[$yr][$moKey]['items'][] = $s;
    }
    krsort($pastNested);
    foreach ($pastNested as &$months) { krsort($months); }
    unset($months);
@endphp

<p class="cal-result-bar">
    <strong>{{ $past->count() }}</strong> past {{ Str::plural('session', $past->count()) }}
    @if(request('year')) <span style="color:#9ca3af;font-weight:400;">in {{ request('year') }}</span>@endif
    @if(request()->hasAny(['mode','course'])) <span style="color:#9ca3af;font-weight:400;">— filtered</span>@endif
</p>

@foreach($pastNested as $year => $months)

@php $yearTotal = array_sum(array_map(fn($m) => count($m['items']), $months)); @endphp
<div class="cal-year-head">
    <span class="cal-year-label">{{ $year }}</span>
    <span class="cal-year-count">{{ $yearTotal }} {{ Str::plural('session', $yearTotal) }}</span>
    <div class="cal-year-rule"></div>
</div>

@foreach($months as $moKey => $monthData)

<div class="cal-month-head cal-month-head--arc">
    <span class="cal-month-label">{{ $monthData['label'] }}</span>
    <span class="cal-month-count">{{ count($monthData['items']) }} {{ Str::plural('session', count($monthData['items'])) }}</span>
    <div class="cal-month-rule"></div>
</div>

<div class="schedules-grid">
@foreach($monthData['items'] as $s)
@php
    $modeRaw  = strtolower($s->training_mode ?? 'physical');
    $modeCls  = match($modeRaw) { 'online'=>'scm-online','hybrid'=>'scm-hybrid', default=>'scm-physical' };
    $venue    = $modeRaw === 'online' ? 'Online (Zoom)' : ($s->venue ?? 'TBA');
    $arcSlug  = $s->course->slug ?? $s->course_id;
    $arcStart = \Carbon\Carbon::parse($s->start_date);
    $arcEnd   = $s->end_date ? \Carbon\Carbon::parse($s->end_date) : $arcStart;
@endphp
<div class="schedule-card schedule-card--arc">
    <div class="schedule-date-block">
        <div class="sc-day">{{ $arcStart->format('d') }}</div>
        <div class="sc-mon">{{ $arcStart->format('M') }}</div>
    </div>
    <div class="sc-body">
        <a href="{{ route('public.course.detail', $arcSlug) }}" class="sc-title">
            {{ Str::limit($s->course?->name ?? $s->training_title, 55) }}
        </a>
        <div class="sc-meta">
            <span class="sc-meta-item">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                {{ $arcStart->format('d M') }} – {{ $arcEnd->format('d M Y') }}
            </span>
            <span class="sc-meta-item">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                {{ $venue }}
            </span>
            @if($s->trainer)
            <span class="sc-meta-item">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                {{ $s->trainer->name }}
            </span>
            @endif
        </div>
        <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap;">
            <div style="display:flex;align-items:center;gap:8px;">
                <span class="sc-mode-badge {{ $modeCls }}">{{ $s->training_mode }}</span>
                <span class="arc-completed">
                    <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                    Completed
                </span>
            </div>
            <a href="{{ route('public.course.detail', $arcSlug) }}" class="arc-view-btn">
                View Details
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="7" y1="17" x2="17" y2="7"/><polyline points="7 7 17 7 17 17"/></svg>
            </a>
        </div>
    </div>
</div>
@endforeach
</div>

@endforeach {{-- months --}}
@endforeach {{-- years --}}

@endif {{-- end archive not empty --}}
@endif {{-- end tab --}}

</div>{{-- .cal-body --}}
</div>{{-- .pub-container --}}
@endsection
