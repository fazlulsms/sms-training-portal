@extends('layouts.public')

@section('page-title', 'Training Calendar')
@section('seo-title', 'Training Calendar — SMS Training Academy')
@section('seo-desc', 'Browse upcoming public training sessions and register before the deadline. View the complete past training archive by year, course, and mode.')
@section('seo-keys', 'training schedule, upcoming training, SMS Training Academy calendar, training archive, past training')

@section('content')
<style>
/* ── Hero ── */
.cal-hero {
    background: linear-gradient(135deg, #060d2e 0%, #0f2470 45%, #1e3a8a 100%);
    padding: 44px 0 52px; color: #fff; position: relative; overflow: hidden;
}
.cal-hero::after {
    content: ''; position: absolute; inset: 0;
    background-image: radial-gradient(rgba(255,255,255,.04) 1px, transparent 1px);
    background-size: 26px 26px; pointer-events: none;
}
.cal-hero-inner { position: relative; z-index: 1; }
.cal-hero h1  { font-size: 32px; font-weight: 900; margin: 0 0 8px; }
.cal-hero p   { font-size: 15px; opacity: .68; margin: 0; line-height: 1.7; }
@media(max-width:640px){ .cal-hero h1 { font-size: 24px; } }

/* ── Tabs ── */
.cal-tabs-bar {
    background: #fff; border-bottom: 2px solid #f1f5f9;
    position: sticky; top: 68px; z-index: 20;
}
.cal-tabs-inner { display: flex; }
.cal-tab {
    padding: 14px 22px; font-size: 13.5px; font-weight: 700; color: #6b7280;
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
.cal-filter-bar { background: #f8fafc; border-bottom: 1px solid #e9ecf0; padding: 12px 0; }
.cal-filter-row { display: flex; gap: 9px; align-items: center; flex-wrap: wrap; }
.cal-fi {
    padding: 8px 12px; border: 1.5px solid #e5e7eb; border-radius: 8px;
    font-size: 13px; font-family: inherit; color: #374151; background: #fff;
    transition: border-color .12s; min-width: 120px;
}
.cal-fi:focus { outline: none; border-color: #1e3a8a; }
.cal-fi-wide { min-width: 200px; }
.cal-fi-btn {
    padding: 8px 16px; background: #1e3a8a; color: #fff; border: none;
    border-radius: 8px; font-weight: 700; font-size: 13px;
    cursor: pointer; font-family: inherit; transition: opacity .12s;
    display: inline-flex; align-items: center; gap: 5px;
}
.cal-fi-btn:hover { opacity: .88; }
.cal-fi-reset {
    font-size: 13px; color: #9ca3af; text-decoration: none;
    font-weight: 600; padding: 8px 2px; transition: color .12s;
    display: inline-flex; align-items: center; gap: 4px;
}
.cal-fi-reset:hover { color: #374151; }

/* ── Body ── */
.cal-body { padding: 28px 0 64px; }
.cal-result-bar {
    font-size: 13px; color: #6b7280; font-weight: 600; margin-bottom: 14px;
}
.cal-result-bar strong { color: #111827; }

/* ── Table wrapper ── */
.cal-tbl-wrap {
    border-radius: 14px; border: 1px solid #e2e8f0;
    overflow-x: auto;
    box-shadow: 0 2px 12px rgba(15,36,112,.05);
}
.cal-tbl {
    width: 100%; min-width: 960px;
    border-collapse: collapse; font-size: 13.5px;
    table-layout: fixed;
}

/* ── Table header ── */
.cal-tbl thead th {
    background: #0f2470; color: #fff;
    font-size: 10.5px; font-weight: 800;
    text-transform: uppercase; letter-spacing: .6px;
    padding: 11px 13px; text-align: left;
    white-space: nowrap; border-right: 1px solid rgba(255,255,255,.1);
}
.cal-tbl thead th:last-child { border-right: none; }

/* Column widths — fixed layout */
.cal-tbl .col-date     { width: 9%; }
.cal-tbl .col-course   { width: 23%; }
.cal-tbl .col-dur      { width: 10%; }
.cal-tbl .col-mode     { width: 7%; }
.cal-tbl .col-fee      { width: 14%; }
.cal-tbl .col-trainer  { width: 10%; }
.cal-tbl .col-venue    { width: 11%; }
.cal-tbl .col-deadline { width: 9%; }
.cal-tbl .col-action   { width: 7%; }

/* Archive widths */
.cal-tbl .acol-date    { width: 10%; }
.cal-tbl .acol-course  { width: 28%; }
.cal-tbl .acol-dur     { width: 9%; }
.cal-tbl .acol-mode    { width: 7%; }
.cal-tbl .acol-trainer { width: 12%; }
.cal-tbl .acol-venue   { width: 13%; }
.cal-tbl .acol-status  { width: 9%; }
.cal-tbl .acol-action  { width: 12%; }

/* ── Table body rows ── */
.cal-tbl tbody tr { transition: background .1s; }
.cal-tbl tbody tr:nth-child(odd)  { background: #fff; }
.cal-tbl tbody tr:nth-child(even) { background: #f8fafc; }
.cal-tbl tbody tr:hover           { background: #eff6ff; }
.cal-tbl tbody tr.year-row        { background: #f1f5f9 !important; }
.cal-tbl tbody tr.year-row:hover  { background: #e8edf3 !important; }

.cal-tbl tbody td {
    padding: 12px 13px; border-bottom: 1px solid #eef0f3;
    vertical-align: middle; color: #374151; line-height: 1.5;
    word-break: break-word;
}
.cal-tbl tbody tr:last-child td  { border-bottom: none; }
.cal-tbl tbody tr.year-row td    { border-bottom: 1px solid #e2e8f0; }

/* ── Cell types ── */
.td-date {
    font-size: 13px; font-weight: 700; color: #111827; white-space: nowrap;
}
.td-date-sub { font-size: 11.5px; color: #9ca3af; font-weight: 500; margin-top: 1px; }

.td-course-link {
    font-weight: 700; color: #111827; text-decoration: none;
    font-size: 13.5px; display: block; line-height: 1.4;
}
.td-course-link:hover { color: #1e3a8a; }

.td-dur { font-size: 13px; color: #374151; }
.td-dur-time { font-size: 12px; color: #9ca3af; margin-top: 2px; }

/* Mode badges */
.mode-badge {
    display: inline-flex; align-items: center;
    padding: 3px 9px; border-radius: 20px; font-size: 11.5px; font-weight: 700;
    white-space: nowrap;
}
.mode-f2f    { background: #f0fdf4; color: #15803d; }
.mode-online { background: #eff6ff; color: #1d4ed8; }
.mode-hybrid { background: #fff7ed; color: #c2410c; }

/* Fee */
.td-fee { font-size: 12.5px; }
.fee-row { display: flex; align-items: baseline; gap: 4px; line-height: 1.6; }
.fee-label { font-size: 11px; font-weight: 700; color: #9ca3af; width: 46px; flex-shrink: 0; }
.fee-amt   { font-weight: 800; color: #1e3a8a; }
.fee-single { font-size: 14px; font-weight: 900; color: #1e3a8a; }
.fee-cur    { font-size: 11px; color: #9ca3af; }

/* Deadline */
.td-deadline { font-size: 13px; white-space: nowrap; }
.deadline-warn { color: #f97316; font-weight: 700; }
.deadline-ok   { color: #374151; }
.deadline-badge {
    display: inline-block; font-size: 10px; font-weight: 800;
    background: #fef2f2; color: #dc2626; padding: 1px 6px; border-radius: 9px;
    margin-top: 2px;
}

/* Action */
.enroll-btn {
    display: inline-flex; align-items: center; justify-content: center;
    padding: 7px 13px; background: #1e3a8a; color: #fff; border-radius: 7px;
    font-size: 12.5px; font-weight: 700; text-decoration: none;
    white-space: nowrap; transition: background .12s;
}
.enroll-btn:hover  { background: #1d4ed8; }
.enroll-closed { font-size: 12px; color: #d1d5db; font-weight: 700; }

.view-btn {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 6px 12px; border: 1.5px solid #1e3a8a; color: #1e3a8a;
    border-radius: 7px; font-size: 12.5px; font-weight: 700; text-decoration: none;
    white-space: nowrap; transition: background .12s;
}
.view-btn:hover { background: #eff6ff; }

/* Status badges */
.status-completed {
    display: inline-flex; align-items: center; gap: 4px;
    background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0;
    padding: 3px 10px; border-radius: 20px; font-size: 11.5px; font-weight: 700;
}

/* ── Year group row (archive) ── */
.year-row td {
    padding: 10px 13px;
}
.year-row-inner {
    display: flex; align-items: center; gap: 12px;
}
.year-label {
    font-size: 14px; font-weight: 900; color: #374151;
}
.year-count {
    font-size: 11.5px; color: #9ca3af; font-weight: 600;
}

/* ── Archive year pills ── */
.arc-pills { display: flex; gap: 7px; flex-wrap: wrap; margin-bottom: 18px; }
.arc-pill {
    padding: 5px 14px; border-radius: 20px; font-size: 12.5px; font-weight: 700;
    text-decoration: none; border: 1.5px solid #e9ecf0; color: #6b7280; background: #fff;
    transition: all .12s;
}
.arc-pill:hover  { border-color: #1e3a8a; color: #1e3a8a; }
.arc-pill.active { background: #1e3a8a; border-color: #1e3a8a; color: #fff; }

/* ── Empty state ── */
.cal-empty {
    padding: 64px 24px; text-align: center;
    background: #f8fafc; border-radius: 14px; border: 1.5px dashed #e2e8f0;
}
.cal-empty-icon {
    width: 52px; height: 52px; border-radius: 14px;
    background: linear-gradient(135deg, #eff6ff, #dbeafe);
    display: flex; align-items: center; justify-content: center; margin: 0 auto 14px;
}
.cal-empty h3 { font-size: 17px; font-weight: 800; color: #111827; margin: 0 0 8px; }
.cal-empty p  { font-size: 14px; color: #6b7280; margin: 0 0 20px; line-height: 1.7; }
.cal-empty-cta {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 10px 22px; background: #1e3a8a; color: #fff;
    border-radius: 9px; font-weight: 700; font-size: 14px; text-decoration: none;
    transition: opacity .12s;
}
.cal-empty-cta:hover { opacity: .9; }
.cal-empty-note { font-size: 13px; color: #9ca3af; margin-top: 10px; display: block; }
.cal-empty-note a { color: #1e3a8a; font-weight: 700; text-decoration: none; }

/* ── Pagination tweak ── */
.cal-pagination { margin-top: 20px; }

/* ════════════════════════════════
   MOBILE — stacked cards (< 768px)
   ════════════════════════════════ */
@media(max-width: 767px) {
    /* Hide table, show card list */
    .cal-tbl-wrap { display: none; }
    .cal-mob-list { display: block; }
}
@media(min-width: 768px) {
    .cal-mob-list { display: none; }
}

/* Mobile card */
.mob-card {
    background: #fff; border: 1px solid #e9ecf0; border-radius: 12px;
    margin-bottom: 10px; overflow: hidden;
}
.mob-card-head {
    background: linear-gradient(90deg, #0f2470, #1e3a8a);
    padding: 10px 14px; display: flex; align-items: center; justify-content: space-between;
}
.mob-card-date { font-size: 13px; font-weight: 800; color: #fff; }
.mob-card-mode { /* mode badge small */ }
.mob-card-body { padding: 12px 14px; display: flex; flex-direction: column; gap: 8px; }
.mob-card-title {
    font-size: 14px; font-weight: 800; color: #111827; text-decoration: none; line-height: 1.4;
}
.mob-card-title:hover { color: #1e3a8a; }
.mob-card-row {
    display: flex; align-items: flex-start; gap: 6px; font-size: 12.5px; color: #6b7280;
}
.mob-card-row-label { font-size: 10.5px; font-weight: 800; color: #9ca3af; text-transform: uppercase; letter-spacing: .4px; width: 58px; flex-shrink: 0; padding-top: 1px; }
.mob-card-row-val   { color: #374151; font-weight: 500; flex: 1; }
.mob-card-foot {
    padding: 10px 14px; border-top: 1px solid #f1f5f9;
    display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px;
}
.mob-card-fee { font-size: 12px; color: #374151; line-height: 1.7; }
.mob-card-fee b { color: #1e3a8a; }

/* Archive mobile year header */
.mob-year-head {
    font-size: 14px; font-weight: 900; color: #374151;
    padding: 12px 0 8px; border-bottom: 1.5px solid #e9ecf0; margin-bottom: 10px; margin-top: 20px;
    display: flex; align-items: center; gap: 10px;
}
.mob-year-head:first-child { margin-top: 0; }
.mob-year-cnt { font-size: 11.5px; color: #9ca3af; font-weight: 600; }

/* Archive card is same structure but no Enroll Now */
.mob-card-arc .mob-card-head { background: #64748b; }
</style>

{{-- ── Hero ── --}}
<div class="cal-hero">
<div class="pub-container">
<div class="cal-hero-inner">
    <h1>Training Calendar</h1>
    <p>Browse upcoming public training sessions and register before the deadline.</p>
</div>
</div>
</div>

{{-- ── Tabs ── --}}
<div class="cal-tabs-bar">
<div class="pub-container">
    <div class="cal-tabs-inner">
        <a href="{{ route('public.calendar', array_merge(request()->except(['tab','page']), ['tab'=>'upcoming'])) }}"
           class="cal-tab {{ $tab === 'upcoming' ? 'active' : '' }}">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            Upcoming Trainings
            <span class="cal-tab-badge">{{ $upcoming->total() }}</span>
        </a>
        <a href="{{ route('public.calendar', array_merge(request()->except(['tab','page','month']), ['tab'=>'archive'])) }}"
           class="cal-tab {{ $tab === 'archive' ? 'active' : '' }}">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="21 8 21 21 3 21 3 8"/><rect x="1" y="3" width="22" height="5"/><line x1="10" y1="12" x2="14" y2="12"/></svg>
            Archive / Past Trainings
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
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                Search
            </button>

            @if(request()->hasAny(['month','mode','course','year']))
            <a href="{{ route('public.calendar', ['tab'=>$tab]) }}" class="cal-fi-reset">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
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

{{-- ══════════════ UPCOMING TAB ══════════════ --}}
@if($tab === 'upcoming')

@if($upcoming->isEmpty())
<div class="cal-empty">
    <div class="cal-empty-icon">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#1e3a8a" stroke-width="1.8"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
    </div>
    <h3>No upcoming public training is currently scheduled</h3>
    <p>Please check the archive or contact us for corporate training enquiries.@if(request()->hasAny(['month','mode','course'])) Try clearing your filters.@endif</p>
    <a href="{{ route('public.contact') }}" class="cal-empty-cta">Request Training Schedule</a>
    <span class="cal-empty-note">
        View past sessions in the <a href="{{ route('public.calendar', ['tab'=>'archive']) }}">Archive</a>.
    </span>
</div>
@else

<p class="cal-result-bar">
    <strong>{{ $upcoming->total() }}</strong> upcoming {{ Str::plural('session', $upcoming->total()) }}
    @if(request()->hasAny(['month','mode','course'])) <span style="color:#9ca3af;font-weight:400;">— filtered</span>@endif
</p>

{{-- ── Desktop table ── --}}
<div class="cal-tbl-wrap">
<table class="cal-tbl">
<colgroup>
    <col class="col-date">
    <col class="col-course">
    <col class="col-dur">
    <col class="col-mode">
    <col class="col-fee">
    <col class="col-trainer">
    <col class="col-venue">
    <col class="col-deadline">
    <col class="col-action">
</colgroup>
<thead>
<tr>
    <th>Date</th>
    <th>Course / Topic</th>
    <th>Duration &amp; Time</th>
    <th>Mode</th>
    <th>Fee</th>
    <th>Trainer</th>
    <th>Venue</th>
    <th>Deadline</th>
    <th>Action</th>
</tr>
</thead>
<tbody>
@foreach($upcoming as $s)
@php
    $currency    = $s->currency ?? 'BDT';
    $onlineFee   = (float)($s->online_fee   ?? 0);
    $physicalFee = (float)($s->physical_fee ?? 0);
    $startDt     = \Carbon\Carbon::parse($s->start_date);
    $endDt       = $s->end_date ? \Carbon\Carbon::parse($s->end_date) : $startDt;
    $days        = $startDt->diffInDays($endDt) + 1;
    $daysLabel   = $days . ' ' . Str::plural('Day', $days);
    $modeRaw     = strtolower($s->training_mode ?? 'physical');
    $modeLabel   = match($modeRaw) { 'online'=>'Online','hybrid'=>'Hybrid', default=>'F2F' };
    $modeCls     = match($modeRaw) { 'online'=>'mode-online','hybrid'=>'mode-hybrid', default=>'mode-f2f' };
    $venue       = $modeRaw === 'online' ? 'Online (Zoom)' : ($s->venue ?? '—');
    $daysLeft    = $s->registration_deadline
                   ? now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($s->registration_deadline), false)
                   : null;
    if ($startDt->format('M Y') === $endDt->format('M Y')) {
        $dateRange = $startDt->format('d') . '–' . $endDt->format('d M Y');
    } else {
        $dateRange = $startDt->format('d M') . ' – ' . $endDt->format('d M Y');
    }
@endphp
<tr>
    {{-- DATE --}}
    <td class="td-date" data-label="Date">
        {{ $dateRange }}
        @if($startDt->year === now()->year)
        @else
        <div class="td-date-sub">{{ $startDt->format('Y') }}</div>
        @endif
    </td>

    {{-- COURSE --}}
    <td data-label="Course">
        <a href="{{ route('public.course.detail', $s->course->slug ?? $s->course_id) }}" class="td-course-link">
            {{ $s->course?->name ?? $s->training_title }}
        </a>
        @if($s->batch_code)
        <span style="font-size:11px;color:#9ca3af;">Batch: {{ $s->batch_code }}</span>
        @endif
    </td>

    {{-- DURATION & TIME --}}
    <td data-label="Duration &amp; Time">
        <div class="td-dur">{{ $daysLabel }}</div>
        @if($s->time_start && $s->time_end)
        <div class="td-dur-time">{{ \Carbon\Carbon::parse($s->time_start)->format('g:i A') }} – {{ \Carbon\Carbon::parse($s->time_end)->format('g:i A') }}</div>
        @endif
    </td>

    {{-- MODE --}}
    <td data-label="Mode">
        <span class="mode-badge {{ $modeCls }}">{{ $modeLabel }}</span>
    </td>

    {{-- FEE --}}
    <td data-label="Fee">
        @if($onlineFee && $physicalFee && $onlineFee !== $physicalFee)
            <div class="td-fee">
                <div class="fee-row"><span class="fee-label">Online</span><span class="fee-amt">{{ $currency }} {{ number_format($onlineFee) }}</span></div>
                <div class="fee-row"><span class="fee-label">Physical</span><span class="fee-amt">{{ $currency }} {{ number_format($physicalFee) }}</span></div>
            </div>
        @elseif($onlineFee || $physicalFee)
            @php $singleFee = $onlineFee ?: $physicalFee; @endphp
            <span class="fee-single">{{ $currency }} {{ number_format($singleFee) }}</span>
            <div class="fee-cur">per person</div>
        @else
            <span style="color:#9ca3af;font-size:12px;">Contact us</span>
        @endif
    </td>

    {{-- TRAINER --}}
    <td data-label="Trainer" style="font-size:13px;color:#374151;">
        {{ $s->trainer?->name ?? '—' }}
    </td>

    {{-- VENUE --}}
    <td data-label="Venue" style="font-size:13px;color:#374151;">
        {{ $venue }}
    </td>

    {{-- DEADLINE --}}
    <td data-label="Deadline">
        @if($s->registration_deadline)
            <div class="{{ ($daysLeft !== null && $daysLeft <= 7) ? 'deadline-warn' : 'deadline-ok' }} td-deadline">
                {{ \Carbon\Carbon::parse($s->registration_deadline)->format('d M Y') }}
            </div>
            @if($daysLeft !== null && $daysLeft >= 0 && $daysLeft <= 7)
            <div class="deadline-badge">{{ $daysLeft }}d left</div>
            @endif
        @else
            <span style="color:#9ca3af;font-size:12px;">Open</span>
        @endif
    </td>

    {{-- ACTION --}}
    <td data-label="Action">
        @if($s->is_open)
        <a href="{{ route('public.enroll', $s->id) }}" class="enroll-btn">Enroll Now</a>
        @else
        <span class="enroll-closed">Closed</span>
        @endif
    </td>
</tr>
@endforeach
</tbody>
</table>
</div>

{{-- ── Mobile cards (upcoming) ── --}}
<div class="cal-mob-list">
@foreach($upcoming as $s)
@php
    $currency    = $s->currency ?? 'BDT';
    $onlineFee   = (float)($s->online_fee   ?? 0);
    $physicalFee = (float)($s->physical_fee ?? 0);
    $startDt     = \Carbon\Carbon::parse($s->start_date);
    $endDt       = $s->end_date ? \Carbon\Carbon::parse($s->end_date) : $startDt;
    $modeRaw     = strtolower($s->training_mode ?? 'physical');
    $modeLabel   = match($modeRaw) { 'online'=>'Online','hybrid'=>'Hybrid', default=>'F2F' };
    $modeCls     = match($modeRaw) { 'online'=>'mode-online','hybrid'=>'mode-hybrid', default=>'mode-f2f' };
    $venue       = $modeRaw === 'online' ? 'Online (Zoom)' : ($s->venue ?? '—');
    if ($startDt->format('M Y') === $endDt->format('M Y')) {
        $dateRange = $startDt->format('d') . '–' . $endDt->format('d M Y');
    } else {
        $dateRange = $startDt->format('d M') . ' – ' . $endDt->format('d M Y');
    }
@endphp
<div class="mob-card">
    <div class="mob-card-head">
        <span class="mob-card-date">{{ $dateRange }}</span>
        <span class="mode-badge {{ $modeCls }}" style="font-size:11px;">{{ $modeLabel }}</span>
    </div>
    <div class="mob-card-body">
        <a href="{{ route('public.course.detail', $s->course->slug ?? $s->course_id) }}" class="mob-card-title">
            {{ $s->course?->name ?? $s->training_title }}
        </a>
        @if($s->time_start)
        <div class="mob-card-row">
            <span class="mob-card-row-label">Time</span>
            <span class="mob-card-row-val">{{ \Carbon\Carbon::parse($s->time_start)->format('g:i A') }} – {{ \Carbon\Carbon::parse($s->time_end)->format('g:i A') }}</span>
        </div>
        @endif
        @if($s->trainer)
        <div class="mob-card-row">
            <span class="mob-card-row-label">Trainer</span>
            <span class="mob-card-row-val">{{ $s->trainer->name }}</span>
        </div>
        @endif
        <div class="mob-card-row">
            <span class="mob-card-row-label">Venue</span>
            <span class="mob-card-row-val">{{ $venue }}</span>
        </div>
        @if($s->registration_deadline)
        <div class="mob-card-row">
            <span class="mob-card-row-label">Deadline</span>
            <span class="mob-card-row-val" style="color:#f97316;font-weight:700;">{{ \Carbon\Carbon::parse($s->registration_deadline)->format('d M Y') }}</span>
        </div>
        @endif
    </div>
    <div class="mob-card-foot">
        <div class="mob-card-fee">
            @if($onlineFee && $physicalFee && $onlineFee !== $physicalFee)
                <div>Online: <b>{{ $currency }} {{ number_format($onlineFee) }}</b></div>
                <div>Physical: <b>{{ $currency }} {{ number_format($physicalFee) }}</b></div>
            @elseif($onlineFee || $physicalFee)
                <b>{{ $currency }} {{ number_format($onlineFee ?: $physicalFee) }}</b> / person
            @endif
        </div>
        @if($s->is_open)
        <a href="{{ route('public.enroll', $s->id) }}" class="enroll-btn">Enroll Now</a>
        @else
        <span class="enroll-closed">Closed</span>
        @endif
    </div>
</div>
@endforeach
</div>

{{-- Pagination --}}
@if($upcoming->hasPages())
<div class="cal-pagination">{{ $upcoming->links() }}</div>
@endif

@endif {{-- end upcoming not empty --}}


{{-- ══════════════ ARCHIVE TAB ══════════════ --}}
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

{{-- Year pills --}}
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

<p class="cal-result-bar">
    <strong>{{ $past->count() }}</strong> past {{ Str::plural('session', $past->count()) }}
    @if(request('year')) <span style="color:#9ca3af;font-weight:400;">in {{ request('year') }}</span>@endif
</p>

{{-- ── Desktop archive table ── --}}
<div class="cal-tbl-wrap">
<table class="cal-tbl">
<colgroup>
    <col class="acol-date">
    <col class="acol-course">
    <col class="acol-dur">
    <col class="acol-mode">
    <col class="acol-trainer">
    <col class="acol-venue">
    <col class="acol-status">
    <col class="acol-action">
</colgroup>
<thead>
<tr>
    <th>Date</th>
    <th>Course / Topic</th>
    <th>Duration</th>
    <th>Mode</th>
    <th>Trainer</th>
    <th>Venue</th>
    <th>Status</th>
    <th>Action</th>
</tr>
</thead>
<tbody>
@foreach($pastByYear as $year => $sessions)
<tr class="year-row">
    <td colspan="8">
        <div class="year-row-inner">
            <span class="year-label">{{ $year }}</span>
            <span class="year-count">{{ $sessions->count() }} {{ Str::plural('session', $sessions->count()) }}</span>
        </div>
    </td>
</tr>
@foreach($sessions as $s)
@php
    $startDt   = \Carbon\Carbon::parse($s->start_date);
    $endDt     = $s->end_date ? \Carbon\Carbon::parse($s->end_date) : $startDt;
    $days      = $startDt->diffInDays($endDt) + 1;
    $daysLabel = $days . ' ' . Str::plural('Day', $days);
    $modeRaw   = strtolower($s->training_mode ?? 'physical');
    $modeLabel = match($modeRaw) { 'online'=>'Online','hybrid'=>'Hybrid', default=>'F2F' };
    $modeCls   = match($modeRaw) { 'online'=>'mode-online','hybrid'=>'mode-hybrid', default=>'mode-f2f' };
    $venue     = $modeRaw === 'online' ? 'Online (Zoom)' : ($s->venue ?? '—');
    if ($startDt->format('M Y') === $endDt->format('M Y')) {
        $dateRange = $startDt->format('d') . '–' . $endDt->format('d M Y');
    } else {
        $dateRange = $startDt->format('d M') . ' – ' . $endDt->format('d M Y');
    }
@endphp
<tr>
    <td class="td-date" data-label="Date" style="color:#6b7280;">{{ $dateRange }}</td>
    <td data-label="Course">
        <a href="{{ route('public.course.detail', $s->course->slug ?? $s->course_id) }}" class="td-course-link" style="color:#374151;">
            {{ $s->course?->name ?? $s->training_title }}
        </a>
    </td>
    <td data-label="Duration">
        <div class="td-dur" style="color:#6b7280;">{{ $daysLabel }}</div>
        @if($s->time_start && $s->time_end)
        <div class="td-dur-time">{{ \Carbon\Carbon::parse($s->time_start)->format('g:i A') }} – {{ \Carbon\Carbon::parse($s->time_end)->format('g:i A') }}</div>
        @endif
    </td>
    <td data-label="Mode"><span class="mode-badge {{ $modeCls }}" style="opacity:.8;">{{ $modeLabel }}</span></td>
    <td data-label="Trainer" style="font-size:13px;color:#6b7280;">{{ $s->trainer?->name ?? '—' }}</td>
    <td data-label="Venue"   style="font-size:13px;color:#6b7280;">{{ $venue }}</td>
    <td data-label="Status">
        <span class="status-completed">
            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
            Completed
        </span>
    </td>
    <td data-label="Action">
        <a href="{{ route('public.course.detail', $s->course->slug ?? $s->course_id) }}" class="view-btn">
            View Summary
        </a>
    </td>
</tr>
@endforeach
@endforeach
</tbody>
</table>
</div>

{{-- ── Mobile cards (archive) ── --}}
<div class="cal-mob-list">
@foreach($pastByYear as $year => $sessions)
<div class="mob-year-head">
    <span>{{ $year }}</span>
    <span class="mob-year-cnt">{{ $sessions->count() }} {{ Str::plural('session', $sessions->count()) }}</span>
</div>
@foreach($sessions as $s)
@php
    $startDt   = \Carbon\Carbon::parse($s->start_date);
    $endDt     = $s->end_date ? \Carbon\Carbon::parse($s->end_date) : $startDt;
    $modeRaw   = strtolower($s->training_mode ?? 'physical');
    $modeLabel = match($modeRaw) { 'online'=>'Online','hybrid'=>'Hybrid', default=>'F2F' };
    $modeCls   = match($modeRaw) { 'online'=>'mode-online','hybrid'=>'mode-hybrid', default=>'mode-f2f' };
    $venue     = $modeRaw === 'online' ? 'Online (Zoom)' : ($s->venue ?? '—');
    if ($startDt->format('M Y') === $endDt->format('M Y')) {
        $dateRange = $startDt->format('d') . '–' . $endDt->format('d M Y');
    } else {
        $dateRange = $startDt->format('d M') . ' – ' . $endDt->format('d M Y');
    }
@endphp
<div class="mob-card mob-card-arc">
    <div class="mob-card-head">
        <span class="mob-card-date">{{ $dateRange }}</span>
        <span class="mode-badge {{ $modeCls }}" style="font-size:11px;">{{ $modeLabel }}</span>
    </div>
    <div class="mob-card-body">
        <a href="{{ route('public.course.detail', $s->course->slug ?? $s->course_id) }}" class="mob-card-title" style="color:#374151;">
            {{ $s->course?->name ?? $s->training_title }}
        </a>
        @if($s->trainer)
        <div class="mob-card-row">
            <span class="mob-card-row-label">Trainer</span>
            <span class="mob-card-row-val">{{ $s->trainer->name }}</span>
        </div>
        @endif
        <div class="mob-card-row">
            <span class="mob-card-row-label">Venue</span>
            <span class="mob-card-row-val">{{ $venue }}</span>
        </div>
    </div>
    <div class="mob-card-foot">
        <span class="status-completed">
            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
            Completed
        </span>
        <a href="{{ route('public.course.detail', $s->course->slug ?? $s->course_id) }}" class="view-btn">View Summary</a>
    </div>
</div>
@endforeach
@endforeach
</div>

@endif {{-- end archive not empty --}}

@endif {{-- end tab --}}

</div>{{-- .cal-body --}}
</div>{{-- .pub-container --}}
@endsection
