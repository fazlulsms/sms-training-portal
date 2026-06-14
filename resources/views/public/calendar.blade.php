@extends('layouts.public')

@section('page-title', 'Training Calendar')
@section('seo-title', 'Training Calendar — SMS Training Academy')
@section('seo-desc', 'Browse upcoming public training sessions by month. Register online or contact us for corporate group bookings.')
@section('seo-keys', 'training schedule, upcoming training, SMS Training Academy calendar, training archive')

@section('content')
<style>
/* ── Hero ── */
.cal-hero {
    background: linear-gradient(135deg, #060d2e 0%, #0f2470 45%, #1e3a8a 100%);
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
.cal-tab:hover  { color: #1e3a8a; }
.cal-tab.active { color: #1e3a8a; border-bottom-color: #1e3a8a; }
.cal-tab-badge  {
    background: #f1f5f9; color: #6b7280;
    font-size: 11px; font-weight: 800; padding: 2px 8px; border-radius: 20px;
}
.cal-tab.active .cal-tab-badge { background: #1e3a8a; color: #fff; }

/* ── Filter bar ── */
.cal-filter-bar { background: #f8fafc; border-bottom: 1px solid #e9ecf0; padding: 11px 0; }
.cal-filter-row { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
.cal-fi {
    padding: 7px 11px; border: 1.5px solid #e5e7eb; border-radius: 8px;
    font-size: 13px; font-family: inherit; color: #374151; background: #fff;
    transition: border-color .12s; min-width: 110px;
}
.cal-fi:focus { outline: none; border-color: #1e3a8a; }
.cal-fi-wide  { min-width: 190px; }
.cal-fi-btn {
    padding: 7px 15px; background: #1e3a8a; color: #fff; border: none;
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

/* ── Body wrapper ── */
.cal-body { padding: 22px 0 64px; }

.cal-result-bar {
    font-size: 13px; color: #6b7280; font-weight: 600; margin-bottom: 18px;
}
.cal-result-bar strong { color: #111827; }

/* ── Month heading ── */
.tl-month-head {
    display: flex; align-items: center; gap: 11px;
    margin: 28px 0 10px;
}
.tl-month-head:first-child { margin-top: 2px; }
.tl-month-label {
    font-size: 11px; font-weight: 900; color: #374151;
    text-transform: uppercase; letter-spacing: 1px; white-space: nowrap;
}
.tl-month-count {
    font-size: 11px; color: #9ca3af; font-weight: 600; white-space: nowrap;
}
.tl-month-rule { flex: 1; height: 1px; background: #e2e8f0; }

/* ── Year heading (archive) ── */
.tl-year-head {
    display: flex; align-items: center; gap: 12px;
    margin: 36px 0 6px;
}
.tl-year-head:first-child { margin-top: 2px; }
.tl-year-label {
    font-size: 17px; font-weight: 900; color: #111827; white-space: nowrap;
}
.tl-year-count { font-size: 12px; color: #9ca3af; font-weight: 600; }
.tl-year-rule  { flex: 1; height: 2px; background: #e2e8f0; }

/* ── Archive month heading (nested, lighter) ── */
.tl-month-head--arc .tl-month-label { color: #64748b; }
.tl-month-head--arc .tl-month-rule  { background: #f1f5f9; }

/* ── Timeline item ── */
.tl-item {
    display: flex; align-items: stretch;
    background: #fff; border: 1px solid #e9ecf0; border-radius: 10px;
    margin-bottom: 8px; min-height: 110px;
    transition: border-color .15s, box-shadow .15s;
}
.tl-item:hover {
    border-color: #bfdbfe;
    box-shadow: 0 2px 14px rgba(30,58,138,.07);
}

/* Left: Date block */
.tl-date {
    width: 76px; flex-shrink: 0;
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    border-right: 1px solid #eef0f3; border-radius: 10px 0 0 10px;
    padding: 14px 8px; text-align: center; background: #fafbff;
}
.tl-date-day { font-size: 30px; font-weight: 900; color: #0f2470; line-height: 1; }
.tl-date-mon {
    font-size: 11px; font-weight: 800; color: #1e3a8a;
    text-transform: uppercase; letter-spacing: .5px; margin-top: 2px;
}
.tl-date-range {
    margin-top: 6px; padding-top: 6px; border-top: 1px solid #e9ecf0;
    font-size: 10px; font-weight: 600; color: #9ca3af; line-height: 1.5;
    white-space: nowrap;
}

/* Middle: course + meta */
.tl-mid {
    flex: 1; padding: 14px 18px; min-width: 0;
    display: flex; flex-direction: column; justify-content: space-between;
}
.tl-top { flex: 1; }
.tl-course-name {
    font-size: 14.5px; font-weight: 800; color: #111827;
    text-decoration: none; line-height: 1.35;
    display: -webkit-box; -webkit-line-clamp: 2;
    -webkit-box-orient: vertical; overflow: hidden;
    margin-bottom: 9px; display: block;
}
.tl-course-name:hover { color: #1e3a8a; }
.tl-meta {
    display: flex; flex-wrap: wrap; gap: 5px 14px; align-items: center;
}
.tl-meta-item {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: 12.5px; color: #6b7280;
}
.tl-meta-item svg { opacity: .65; flex-shrink: 0; }
.tl-meta-dur { font-size: 12px; color: #9ca3af; }
.tl-bottom { margin-top: 9px; }
.tl-detail-link {
    font-size: 11.5px; color: #9ca3af; text-decoration: none;
    display: inline-flex; align-items: center; gap: 3px;
    transition: color .12s;
}
.tl-detail-link:hover { color: #1e3a8a; }

/* Mode badges */
.tl-badge {
    display: inline-flex; align-items: center;
    padding: 2px 9px; border-radius: 20px; font-size: 11px; font-weight: 700;
    white-space: nowrap; line-height: 1.6;
}
.tl-f2f    { background: #f0fdf4; color: #15803d; }
.tl-online { background: #eff6ff; color: #1d4ed8; }
.tl-hybrid { background: #fff7ed; color: #c2410c; }

/* Right: fee + action */
.tl-right {
    width: 158px; flex-shrink: 0;
    padding: 14px 16px;
    display: flex; flex-direction: column;
    align-items: flex-end; justify-content: space-between;
    border-left: 1px solid #eef0f3; border-radius: 0 10px 10px 0;
}
.tl-fee { text-align: right; }
.tl-fee-amount {
    font-size: 14.5px; font-weight: 900; color: #1e3a8a; line-height: 1.2;
}
.tl-fee-sub { font-size: 10.5px; color: #9ca3af; margin-top: 1px; }
.tl-fee-dual { text-align: right; }
.tl-fee-dual-row {
    display: flex; align-items: baseline; justify-content: flex-end; gap: 5px;
    font-size: 11.5px; line-height: 1.55;
}
.tl-fee-dual-tag { font-size: 10px; color: #9ca3af; }
.tl-fee-dual-amt { font-weight: 800; color: #1e3a8a; }
.tl-fee-contact { font-size: 12px; color: #9ca3af; }

.tl-enroll-btn {
    display: inline-flex; align-items: center; justify-content: center;
    padding: 7px 14px; background: #1e3a8a; color: #fff; border-radius: 7px;
    font-size: 12.5px; font-weight: 700; text-decoration: none;
    white-space: nowrap; transition: background .12s; margin-top: 10px;
}
.tl-enroll-btn:hover { background: #1d4ed8; }
.tl-closed {
    font-size: 11.5px; color: #d1d5db; font-weight: 700; margin-top: 10px;
    text-align: right;
}

/* Archive: completed badge + view btn */
.tl-completed {
    display: inline-flex; align-items: center; gap: 4px;
    background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0;
    padding: 3px 9px; border-radius: 20px; font-size: 11px; font-weight: 700;
}
.tl-view-btn {
    display: inline-flex; align-items: center; gap: 4px; margin-top: 8px;
    padding: 6px 12px; border: 1.5px solid #d1d5db; color: #6b7280;
    border-radius: 7px; font-size: 12px; font-weight: 700;
    text-decoration: none; white-space: nowrap; transition: all .12s;
}
.tl-view-btn:hover { border-color: #1e3a8a; color: #1e3a8a; background: #f5f8ff; }

/* Archive item: muted */
.tl-item-arc .tl-date    { background: #f8fafc; }
.tl-item-arc .tl-date-day { color: #64748b; }
.tl-item-arc .tl-date-mon { color: #94a3b8; }
.tl-item-arc .tl-course-name { color: #374151; }
.tl-item-arc:hover { border-color: #e2e8f0; box-shadow: none; }

/* ── Year pills (archive filter) ── */
.arc-pills { display: flex; gap: 7px; flex-wrap: wrap; margin-bottom: 20px; }
.arc-pill {
    padding: 5px 14px; border-radius: 20px; font-size: 12.5px; font-weight: 700;
    text-decoration: none; border: 1.5px solid #e9ecf0; color: #6b7280; background: #fff;
    transition: all .12s;
}
.arc-pill:hover  { border-color: #1e3a8a; color: #1e3a8a; }
.arc-pill.active { background: #1e3a8a; border-color: #1e3a8a; color: #fff; }

/* ── Empty state ── */
.cal-empty {
    padding: 60px 24px; text-align: center;
    background: #f8fafc; border-radius: 14px; border: 1.5px dashed #e2e8f0;
    margin-top: 8px;
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
    padding: 10px 22px; background: #1e3a8a; color: #fff;
    border-radius: 9px; font-weight: 700; font-size: 14px;
    text-decoration: none; transition: opacity .12s;
}
.cal-empty-cta:hover { opacity: .9; }
.cal-empty-note { font-size: 13px; color: #9ca3af; margin-top: 10px; display: block; }
.cal-empty-note a { color: #1e3a8a; font-weight: 700; text-decoration: none; }

/* ── Pagination ── */
.cal-pagination { margin-top: 24px; }

/* ─── Mobile ≤ 639px ─── */
@media(max-width: 639px) {
    .tl-item { flex-direction: column; min-height: unset; }
    .tl-date {
        width: 100%; flex-direction: row; gap: 10px; text-align: left;
        justify-content: flex-start; border-right: none;
        border-bottom: 1px solid #eef0f3;
        border-radius: 10px 10px 0 0; padding: 10px 14px;
    }
    .tl-date-day  { font-size: 22px; }
    .tl-date-range { margin-top: 0; padding-top: 0; border-top: none; }
    .tl-mid { padding: 12px 14px; }
    .tl-right {
        width: 100%; border-left: none; border-top: 1px solid #eef0f3;
        border-radius: 0 0 10px 10px; flex-direction: row;
        align-items: center; padding: 10px 14px;
    }
    .tl-enroll-btn { margin-top: 0; }
    .tl-closed     { margin-top: 0; }
    .tl-view-btn   { margin-top: 0; }
    .tl-month-head { margin: 22px 0 8px; }
    .tl-year-head  { margin: 28px 0 4px; }
}
</style>

{{-- ── Hero ── --}}
<div class="cal-hero">
<div class="pub-container">
<div class="cal-hero-inner">
    <h1>Training Calendar</h1>
    <p>Public open-enrolment training sessions — browse by month and register online.</p>
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

{{-- ══════════════════════════ UPCOMING TAB ══════════════════════════ --}}
@if($tab === 'upcoming')

@if($upcoming->isEmpty())
<div class="cal-empty">
    <div class="cal-empty-icon">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#1e3a8a" stroke-width="1.8"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
    </div>
    <h3>No upcoming public training scheduled</h3>
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

<div class="tl-month-head">
    <span class="tl-month-label">{{ $monthLabel }}</span>
    <span class="tl-month-count">{{ $sessions->count() }} {{ Str::plural('session', $sessions->count()) }}</span>
    <div class="tl-month-rule"></div>
</div>

@foreach($sessions as $s)
@php
    $currency    = $s->currency ?? 'BDT';
    $onlineFee   = (float)($s->online_fee   ?? 0);
    $physicalFee = (float)($s->physical_fee ?? 0);
    $singleFee   = $onlineFee ?: $physicalFee;
    $dualFee     = $onlineFee && $physicalFee && $onlineFee !== $physicalFee;
    $startDt     = \Carbon\Carbon::parse($s->start_date);
    $endDt       = $s->end_date ? \Carbon\Carbon::parse($s->end_date) : $startDt;
    $days        = $startDt->diffInDays($endDt) + 1;
    $daysLabel   = $days . ' ' . Str::plural('Day', $days);
    $modeRaw     = strtolower($s->training_mode ?? 'physical');
    $modeLabel   = match($modeRaw) { 'online'=>'Online','hybrid'=>'Hybrid', default=>'Physical' };
    $modeCls     = match($modeRaw) { 'online'=>'tl-online','hybrid'=>'tl-hybrid', default=>'tl-f2f' };
    $venue       = $modeRaw === 'online' ? 'Online (Zoom)' : ($s->venue ?? '—');
    $courseSlug  = $s->course->slug ?? $s->course_id;
    $sameMonth   = $startDt->format('M Y') === $endDt->format('M Y');
    if ($sameMonth) {
        $dateRange = '– ' . $endDt->format('d M');
    } else {
        $dateRange = '– ' . $endDt->format('d M');
    }
    $multiDay = $days > 1;
@endphp
<div class="tl-item">

    {{-- DATE --}}
    <div class="tl-date">
        <div class="tl-date-day">{{ $startDt->format('d') }}</div>
        <div class="tl-date-mon">{{ $startDt->format('M') }}</div>
        @if($multiDay)
        <div class="tl-date-range">{{ $dateRange }}<br>{{ $endDt->format('Y') }}</div>
        @else
        <div class="tl-date-range">{{ $startDt->format('Y') }}</div>
        @endif
    </div>

    {{-- MIDDLE --}}
    <div class="tl-mid">
        <div class="tl-top">
            <a href="{{ route('public.course.detail', $courseSlug) }}" class="tl-course-name">
                {{ $s->course?->name ?? $s->training_title }}
            </a>
            <div class="tl-meta">
                <span class="tl-badge {{ $modeCls }}">{{ $modeLabel }}</span>
                @if($s->trainer)
                <span class="tl-meta-item">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    {{ $s->trainer->name }}
                </span>
                @endif
                <span class="tl-meta-item">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    {{ $venue }}
                </span>
                <span class="tl-meta-dur">
                    {{ $daysLabel }}
                    @if($s->time_start && $s->time_end)
                    &nbsp;·&nbsp;{{ \Carbon\Carbon::parse($s->time_start)->format('g A') }}–{{ \Carbon\Carbon::parse($s->time_end)->format('g A') }}
                    @endif
                </span>
            </div>
        </div>
        <div class="tl-bottom">
            <a href="{{ route('public.course.detail', $courseSlug) }}" class="tl-detail-link">
                Course details
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="7" y1="17" x2="17" y2="7"/><polyline points="7 7 17 7 17 17"/></svg>
            </a>
        </div>
    </div>

    {{-- RIGHT --}}
    <div class="tl-right">
        <div class="tl-fee">
            @if($dualFee)
            <div class="tl-fee-dual">
                <div class="tl-fee-dual-row">
                    <span class="tl-fee-dual-tag">Online</span>
                    <span class="tl-fee-dual-amt">{{ $currency }} {{ number_format($onlineFee) }}</span>
                </div>
                <div class="tl-fee-dual-row">
                    <span class="tl-fee-dual-tag">Physical</span>
                    <span class="tl-fee-dual-amt">{{ $currency }} {{ number_format($physicalFee) }}</span>
                </div>
            </div>
            @elseif($singleFee)
            <div class="tl-fee-amount">{{ $currency }} {{ number_format($singleFee) }}</div>
            <div class="tl-fee-sub">per person</div>
            @else
            <div class="tl-fee-contact">Contact us</div>
            @endif
        </div>
        <div>
            @if($s->is_open)
            <a href="{{ route('public.enroll', $s->id) }}" class="tl-enroll-btn">Enroll Now</a>
            @else
            <div class="tl-closed">Closed</div>
            @endif
        </div>
    </div>

</div>{{-- .tl-item --}}
@endforeach

@endforeach

@if($upcoming->hasPages())
<div class="cal-pagination">{{ $upcoming->links() }}</div>
@endif

@endif {{-- end upcoming not empty --}}


{{-- ══════════════════════════ ARCHIVE TAB ══════════════════════════ --}}
@else

@if($past->isEmpty())
<div class="cal-empty">
    <div class="cal-empty-icon">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#1e3a8a" stroke-width="1.8"><polyline points="21 8 21 21 3 21 3 8"/><rect x="1" y="3" width="22" height="5"/><line x1="10" y1="12" x2="14" y2="12"/></svg>
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
        $yr     = \Carbon\Carbon::parse($s->start_date)->year;
        $moKey  = \Carbon\Carbon::parse($s->start_date)->format('Y-m');
        $moLbl  = \Carbon\Carbon::parse($s->start_date)->format('F Y');
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
<div class="tl-year-head">
    <span class="tl-year-label">{{ $year }}</span>
    <span class="tl-year-count">{{ $yearTotal }} {{ Str::plural('session', $yearTotal) }}</span>
    <div class="tl-year-rule"></div>
</div>

@foreach($months as $moKey => $monthData)

<div class="tl-month-head tl-month-head--arc">
    <span class="tl-month-label">{{ $monthData['label'] }}</span>
    <span class="tl-month-count">{{ count($monthData['items']) }} {{ Str::plural('session', count($monthData['items'])) }}</span>
    <div class="tl-month-rule"></div>
</div>

@foreach($monthData['items'] as $s)
@php
    $startDt    = \Carbon\Carbon::parse($s->start_date);
    $endDt      = $s->end_date ? \Carbon\Carbon::parse($s->end_date) : $startDt;
    $days       = $startDt->diffInDays($endDt) + 1;
    $daysLabel  = $days . ' ' . Str::plural('Day', $days);
    $modeRaw    = strtolower($s->training_mode ?? 'physical');
    $modeLabel  = match($modeRaw) { 'online'=>'Online','hybrid'=>'Hybrid', default=>'Physical' };
    $modeCls    = match($modeRaw) { 'online'=>'tl-online','hybrid'=>'tl-hybrid', default=>'tl-f2f' };
    $venue      = $modeRaw === 'online' ? 'Online (Zoom)' : ($s->venue ?? '—');
    $courseSlug = $s->course->slug ?? $s->course_id;
    $multiDay   = $days > 1;
    $dateRange  = '– ' . $endDt->format('d M');
@endphp
<div class="tl-item tl-item-arc">

    {{-- DATE --}}
    <div class="tl-date">
        <div class="tl-date-day">{{ $startDt->format('d') }}</div>
        <div class="tl-date-mon">{{ $startDt->format('M') }}</div>
        @if($multiDay)
        <div class="tl-date-range">{{ $dateRange }}<br>{{ $endDt->format('Y') }}</div>
        @else
        <div class="tl-date-range">{{ $startDt->format('Y') }}</div>
        @endif
    </div>

    {{-- MIDDLE --}}
    <div class="tl-mid">
        <div class="tl-top">
            <a href="{{ route('public.course.detail', $courseSlug) }}" class="tl-course-name">
                {{ $s->course?->name ?? $s->training_title }}
            </a>
            <div class="tl-meta">
                <span class="tl-badge {{ $modeCls }}" style="opacity:.8;">{{ $modeLabel }}</span>
                @if($s->trainer)
                <span class="tl-meta-item">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    {{ $s->trainer->name }}
                </span>
                @endif
                <span class="tl-meta-item">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    {{ $venue }}
                </span>
                <span class="tl-meta-dur">{{ $daysLabel }}</span>
            </div>
        </div>
        <div class="tl-bottom">
            <a href="{{ route('public.course.detail', $courseSlug) }}" class="tl-detail-link">
                View course
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="7" y1="17" x2="17" y2="7"/><polyline points="7 7 17 7 17 17"/></svg>
            </a>
        </div>
    </div>

    {{-- RIGHT --}}
    <div class="tl-right">
        <span class="tl-completed">
            <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
            Completed
        </span>
        <a href="{{ route('public.course.detail', $courseSlug) }}" class="tl-view-btn">
            View Details
            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="7" y1="17" x2="17" y2="7"/><polyline points="7 7 17 7 17 17"/></svg>
        </a>
    </div>

</div>{{-- .tl-item.tl-item-arc --}}
@endforeach

@endforeach {{-- months --}}
@endforeach {{-- years --}}

@endif {{-- end archive not empty --}}

@endif {{-- end tab --}}

</div>{{-- .cal-body --}}
</div>{{-- .pub-container --}}
@endsection
