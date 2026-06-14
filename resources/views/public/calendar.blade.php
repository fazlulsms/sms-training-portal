@extends('layouts.public')

@section('page-title', 'Training Calendar')
@section('seo-title', 'Training Calendar — SMS Training Academy')
@section('seo-desc', 'Browse upcoming public training batches and register before the deadline. View past training archives by year, mode, and course.')
@section('seo-keys', 'training schedule, upcoming training, SMS Training Academy calendar, training archive, past training')

@section('content')
<style>
/* ── Calendar page ── */
.cal-hero {
    background: linear-gradient(135deg, #060d2e 0%, #0f2470 45%, #1e3a8a 100%);
    padding: 52px 0 60px; color: #fff; position: relative; overflow: hidden;
}
.cal-hero::after {
    content: ''; position: absolute; inset: 0;
    background-image: radial-gradient(rgba(255,255,255,.04) 1px, transparent 1px);
    background-size: 26px 26px; pointer-events: none;
}
.cal-hero-inner { position: relative; z-index: 1; }
.cal-hero-eyebrow {
    display: inline-flex; align-items: center; gap: 7px;
    background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.18);
    padding: 4px 13px; border-radius: 20px;
    font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: .8px;
    color: rgba(255,255,255,.8); margin-bottom: 14px;
}
.cal-hero h1 { font-size: 36px; font-weight: 900; margin: 0 0 10px; line-height: 1.2; }
.cal-hero p  { font-size: 15.5px; opacity: .7; margin: 0; max-width: 540px; line-height: 1.7; }
@media(max-width: 640px) { .cal-hero h1 { font-size: 26px; } }

/* ── Tabs ── */
.cal-tabs-bar {
    background: #fff; border-bottom: 2px solid #f1f5f9;
    position: sticky; top: 68px; z-index: 20;
}
.cal-tabs-inner { display: flex; align-items: center; gap: 0; }
.cal-tab {
    padding: 16px 24px; font-size: 14px; font-weight: 700; color: #6b7280;
    text-decoration: none; display: flex; align-items: center; gap: 8px;
    border-bottom: 2.5px solid transparent; margin-bottom: -2px;
    transition: color .13s, border-color .13s;
    white-space: nowrap;
}
.cal-tab:hover { color: #1e3a8a; }
.cal-tab.active { color: #1e3a8a; border-bottom-color: #1e3a8a; }
.cal-tab-badge {
    background: #eff6ff; color: #1e3a8a; font-size: 11px; font-weight: 800;
    padding: 2px 8px; border-radius: 20px; min-width: 24px; text-align: center;
}
.cal-tab.active .cal-tab-badge { background: #1e3a8a; color: #fff; }

/* ── Filter bar ── */
.cal-filter-bar {
    background: #f8fafc; border-bottom: 1px solid #e9ecf0; padding: 14px 0;
}
.cal-filter-row { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
.cal-filter-input {
    padding: 9px 13px; border: 1.5px solid #e5e7eb; border-radius: 9px;
    font-size: 13.5px; font-family: inherit; color: #374151; background: #fff;
    min-width: 130px; transition: border-color .13s;
}
.cal-filter-input:focus { outline: none; border-color: #1e3a8a; }
.cal-filter-btn {
    padding: 9px 18px; background: #1e3a8a; color: #fff; border: none;
    border-radius: 9px; font-weight: 700; font-size: 13.5px;
    cursor: pointer; font-family: inherit; transition: opacity .13s;
}
.cal-filter-btn:hover { opacity: .88; }
.cal-filter-reset {
    font-size: 13px; color: #9ca3af; text-decoration: none; padding: 9px 4px;
    font-weight: 600; transition: color .13s;
}
.cal-filter-reset:hover { color: #374151; }

/* ── Page body ── */
.cal-body { padding: 36px 0 64px; }

/* ── Count bar ── */
.cal-count-bar {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 20px; flex-wrap: wrap; gap: 10px;
}
.cal-count-text { font-size: 13.5px; color: #6b7280; font-weight: 600; }
.cal-count-text strong { color: #111827; }

/* ── Upcoming schedule card ── */
.sc-card {
    background: #fff; border: 1px solid #e9ecf0; border-radius: 14px;
    display: grid; grid-template-columns: 76px 1fr auto;
    overflow: hidden; transition: box-shadow .15s, border-color .15s;
    margin-bottom: 12px;
}
.sc-card:hover { box-shadow: 0 6px 24px rgba(15,36,112,.09); border-color: #c7d2fe; }

.sc-date {
    background: linear-gradient(180deg, #0f2470, #1e3a8a);
    color: #fff; display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    padding: 16px 8px; text-align: center; flex-shrink: 0;
}
.sc-date-day { font-size: 28px; font-weight: 900; line-height: 1; }
.sc-date-mon { font-size: 11px; font-weight: 700; text-transform: uppercase; opacity: .75; margin-top: 3px; }

.sc-info { padding: 16px 20px; min-width: 0; }
.sc-title {
    font-size: 15.5px; font-weight: 800; color: #111827; margin: 0 0 9px;
    text-decoration: none; display: block; line-height: 1.35;
}
.sc-title:hover { color: #1e3a8a; }
.sc-meta { display: flex; flex-wrap: wrap; gap: 6px 16px; }
.sc-meta-item {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 13px; color: #6b7280; font-weight: 500;
}
.sc-meta-item svg { flex-shrink: 0; opacity: .65; }
.sc-mode {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 2px 9px; border-radius: 20px; font-size: 11.5px; font-weight: 700;
}
.scm-physical { background: #f0fdf4; color: #15803d; }
.scm-online   { background: #eff6ff; color: #1d4ed8; }
.scm-hybrid   { background: #fff7ed; color: #c2410c; }
.sc-seats-ok  { color: #16a34a; font-weight: 700; }
.sc-seats-low { color: #ef4444; font-weight: 700; }

.sc-action {
    padding: 16px 20px; display: flex; flex-direction: column;
    align-items: flex-end; justify-content: center; gap: 10px;
    min-width: 160px; border-left: 1px solid #f1f5f9;
}
.sc-fee-from { font-size: 11px; font-weight: 700; color: #9ca3af; text-transform: uppercase; letter-spacing: .4px; margin: 0 0 2px; }
.sc-fee-val  { font-size: 20px; font-weight: 900; color: #1e3a8a; line-height: 1; margin: 0 0 4px; }
.sc-fee-curr { font-size: 12px; font-weight: 600; color: #9ca3af; }
.sc-deadline { font-size: 12px; color: #f97316; font-weight: 700; display: flex; align-items: center; gap: 4px; }
.sc-enroll-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 9px 16px; background: #1e3a8a; color: #fff;
    border-radius: 9px; font-size: 13px; font-weight: 700;
    text-decoration: none; transition: background .13s; white-space: nowrap;
}
.sc-enroll-btn:hover { background: #1d4ed8; }
.sc-closed-txt { font-size: 12px; font-weight: 700; color: #d1d5db; text-align: right; }

@media(max-width: 800px) {
    .sc-card { grid-template-columns: 64px 1fr; }
    .sc-action {
        grid-column: 1 / -1; flex-direction: row; align-items: center;
        justify-content: space-between; border-left: none;
        border-top: 1px solid #f1f5f9; padding: 12px 16px;
    }
    .sc-action-left { display: flex; flex-direction: column; gap: 2px; }
}
@media(max-width: 500px) {
    .sc-info { padding: 14px 14px; }
    .sc-title { font-size: 14px; }
    .sc-meta-item { font-size: 12px; }
}

/* ── Archive section ── */
.arc-year-header {
    display: flex; align-items: center; gap: 14px;
    margin: 36px 0 16px;
}
.arc-year-header:first-child { margin-top: 0; }
.arc-year-num {
    font-size: 22px; font-weight: 900; color: #111827;
}
.arc-year-line { flex: 1; height: 1.5px; background: #e9ecf0; }
.arc-year-count { font-size: 12px; font-weight: 700; color: #9ca3af; }

.arc-card {
    background: #fff; border: 1px solid #e9ecf0; border-radius: 13px;
    display: grid; grid-template-columns: 64px 1fr auto;
    overflow: hidden; margin-bottom: 10px;
    opacity: .92;
}
.arc-date {
    background: #64748b;
    color: #fff; display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    padding: 14px 8px; text-align: center;
}
.arc-date-day { font-size: 22px; font-weight: 900; line-height: 1; }
.arc-date-mon { font-size: 10px; font-weight: 700; text-transform: uppercase; opacity: .75; margin-top: 2px; }
.arc-info { padding: 14px 18px; }
.arc-title {
    font-size: 14.5px; font-weight: 800; color: #374151; margin: 0 0 7px;
    text-decoration: none; display: block; line-height: 1.35;
}
.arc-title:hover { color: #1e3a8a; }
.arc-meta { display: flex; flex-wrap: wrap; gap: 5px 14px; }
.arc-meta-item { display: inline-flex; align-items: center; gap: 4px; font-size: 12.5px; color: #9ca3af; }
.arc-action {
    padding: 14px 16px; display: flex; flex-direction: column;
    align-items: flex-end; justify-content: center; gap: 8px;
    border-left: 1px solid #f1f5f9; min-width: 130px;
}
.arc-status-badge {
    display: inline-flex; align-items: center; gap: 5px;
    background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0;
    padding: 4px 10px; border-radius: 20px; font-size: 11.5px; font-weight: 700;
}
.arc-view-btn {
    font-size: 13px; font-weight: 700; color: #1e3a8a;
    text-decoration: none; border: 1.5px solid #1e3a8a;
    padding: 7px 14px; border-radius: 8px;
    transition: background .13s; white-space: nowrap;
}
.arc-view-btn:hover { background: #eff6ff; }

@media(max-width: 700px) {
    .arc-card { grid-template-columns: 52px 1fr; }
    .arc-action { grid-column: 1 / -1; flex-direction: row; border-left: none; border-top: 1px solid #f1f5f9; padding: 10px 14px; }
}

/* ── Empty state ── */
.cal-empty {
    text-align: center; padding: 72px 20px; background: #f8fafc;
    border: 1.5px dashed #e2e8f0; border-radius: 16px;
}
.cal-empty-icon {
    width: 56px; height: 56px; border-radius: 16px;
    background: linear-gradient(135deg, #eff6ff, #dbeafe);
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 16px;
}
.cal-empty h3 { font-size: 18px; font-weight: 800; color: #111827; margin: 0 0 8px; }
.cal-empty p  { font-size: 14px; color: #6b7280; margin: 0 0 20px; line-height: 1.7; }
.cal-empty-btn {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 11px 22px; background: #1e3a8a; color: #fff;
    border-radius: 10px; font-weight: 700; font-size: 14px; text-decoration: none;
    transition: opacity .13s;
}
.cal-empty-btn:hover { opacity: .9; }
.cal-empty-secondary {
    font-size: 13px; color: #6b7280; margin-top: 12px; display: block;
}
.cal-empty-secondary a { color: #1e3a8a; font-weight: 700; text-decoration: none; }

/* ── Archive year filter pills ── */
.arc-year-pills { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 24px; }
.arc-year-pill {
    padding: 6px 16px; border-radius: 20px; font-size: 13px; font-weight: 700;
    text-decoration: none; border: 1.5px solid #e9ecf0; color: #6b7280;
    background: #fff; transition: all .13s;
}
.arc-year-pill:hover   { border-color: #1e3a8a; color: #1e3a8a; }
.arc-year-pill.active  { background: #1e3a8a; border-color: #1e3a8a; color: #fff; }
</style>

{{-- Hero --}}
<div class="cal-hero">
<div class="pub-container">
<div class="cal-hero-inner">
    <div class="cal-hero-eyebrow">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        Training Calendar
    </div>
    <h1>Upcoming Training Schedule</h1>
    <p>Browse public training batches and register before the deadline. Past sessions are available in the archive.</p>
</div>
</div>
</div>

{{-- Tabs --}}
<div class="cal-tabs-bar">
<div class="pub-container">
    <div class="cal-tabs-inner">
        <a href="{{ route('public.calendar', array_merge(request()->except(['tab','page']), ['tab'=>'upcoming'])) }}"
           class="cal-tab {{ $tab === 'upcoming' ? 'active' : '' }}">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/></svg>
            Upcoming Trainings
            <span class="cal-tab-badge">{{ $upcoming->total() }}</span>
        </a>
        <a href="{{ route('public.calendar', array_merge(request()->except(['tab','page','month']), ['tab'=>'archive'])) }}"
           class="cal-tab {{ $tab === 'archive' ? 'active' : '' }}">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="21 8 21 21 3 21 3 8"/><rect x="1" y="3" width="22" height="5"/><line x1="10" y1="12" x2="14" y2="12"/></svg>
            Past Training Archive
            <span class="cal-tab-badge">{{ $past->count() }}</span>
        </a>
    </div>
</div>
</div>

{{-- Filter bar --}}
<div class="cal-filter-bar">
<div class="pub-container">
    <form method="GET" action="{{ route('public.calendar') }}">
        <input type="hidden" name="tab" value="{{ $tab }}">
        <div class="cal-filter-row">

            @if($tab === 'upcoming')
            <input type="month" name="month" class="cal-filter-input"
                   value="{{ request('month') }}" placeholder="Month">
            @endif

            @if($tab === 'archive')
            <select name="year" class="cal-filter-input">
                <option value="">All Years</option>
                @foreach($archiveYears as $yr)
                <option value="{{ $yr }}" {{ request('year') == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                @endforeach
            </select>
            @endif

            <select name="mode" class="cal-filter-input">
                <option value="">All Modes</option>
                @foreach(['Physical','Online','Hybrid'] as $m)
                <option value="{{ $m }}" {{ request('mode') === $m ? 'selected' : '' }}>{{ $m }}</option>
                @endforeach
            </select>

            <select name="course" class="cal-filter-input" style="max-width:280px;">
                <option value="">All Courses</option>
                @foreach($courses as $c)
                <option value="{{ $c->id }}" {{ request('course') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>

            <button type="submit" class="cal-filter-btn">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="vertical-align:-2px;margin-right:4px;"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                Search
            </button>

            @if(request()->hasAny(['month','mode','course','year']))
            <a href="{{ route('public.calendar', ['tab' => $tab]) }}" class="cal-filter-reset">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="vertical-align:-1px;margin-right:3px;"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                Reset
            </a>
            @endif
        </div>
    </form>
</div>
</div>

<div class="pub-container">
<div class="cal-body">

{{-- ══ UPCOMING TAB ══════════════════════════════════════════════════════ --}}
@if($tab === 'upcoming')

    @if($upcoming->isEmpty())
    <div class="cal-empty">
        <div class="cal-empty-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#1e3a8a" stroke-width="1.8"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        </div>
        <h3>No upcoming training scheduled</h3>
        <p>No public training is currently scheduled.@if(request()->hasAny(['month','mode','course'])) Try removing your filters.@endif</p>
        @if(request()->hasAny(['month','mode','course']))
        <a href="{{ route('public.calendar', ['tab'=>'upcoming']) }}" class="cal-empty-btn">View All Upcoming</a>
        @else
        <a href="{{ route('public.contact') }}" class="cal-empty-btn">Request a Training Schedule</a>
        @endif
        <span class="cal-empty-secondary">
            Past training sessions are available in the
            <a href="{{ route('public.calendar', ['tab'=>'archive']) }}">Archive</a>.
        </span>
    </div>
    @else

    <div class="cal-count-bar">
        <p class="cal-count-text">
            <strong>{{ $upcoming->total() }}</strong> upcoming {{ Str::plural('session', $upcoming->total()) }}
            @if(request()->hasAny(['month','mode','course']))<span style="color:#9ca3af;font-weight:400;"> — filtered</span>@endif
        </p>
    </div>

    <div>
    @foreach($upcoming as $s)
    @php
        $calCurrency = $s->currency ?? 'BDT';
        $fees        = array_filter([(float)($s->online_fee ?? 0), (float)($s->physical_fee ?? 0)]);
        $minFee      = $fees ? min($fees) : 0;
        $modeKey     = match(strtolower($s->training_mode ?? '')) {
            'online'  => 'scm-online',
            'hybrid'  => 'scm-hybrid',
            default   => 'scm-physical',
        };
        $seatsLeft   = $s->seats_left ?? null;
        $daysLeft    = $s->registration_deadline
                       ? now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($s->registration_deadline), false)
                       : null;
    @endphp
    <div class="sc-card">
        {{-- Date block --}}
        <div class="sc-date">
            <div class="sc-date-day">{{ \Carbon\Carbon::parse($s->start_date)->format('d') }}</div>
            <div class="sc-date-mon">{{ \Carbon\Carbon::parse($s->start_date)->format('M Y') }}</div>
        </div>

        {{-- Info --}}
        <div class="sc-info">
            <a href="{{ route('public.course.detail', $s->course->slug ?? $s->course_id) }}" class="sc-title">
                {{ $s->course?->name ?? $s->training_title }}
            </a>
            <div class="sc-meta">
                {{-- Date range --}}
                <span class="sc-meta-item">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    {{ \Carbon\Carbon::parse($s->start_date)->format('d M') }} – {{ \Carbon\Carbon::parse($s->end_date)->format('d M Y') }}
                </span>
                {{-- Time --}}
                @if($s->time_start)
                <span class="sc-meta-item">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    {{ \Carbon\Carbon::parse($s->time_start)->format('g:i A') }} – {{ \Carbon\Carbon::parse($s->time_end)->format('g:i A') }}
                </span>
                @endif
                {{-- Mode --}}
                <span class="sc-meta-item">
                    <span class="sc-mode {{ $modeKey }}">{{ $s->training_mode }}</span>
                </span>
                {{-- Venue --}}
                @if($s->training_mode !== 'Online' && $s->venue)
                <span class="sc-meta-item">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    {{ $s->venue }}
                </span>
                @elseif($s->training_mode === 'Online')
                <span class="sc-meta-item">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                    Online (Zoom)
                </span>
                @endif
                {{-- Trainer --}}
                @if($s->trainer)
                <span class="sc-meta-item">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    {{ $s->trainer->name }}
                </span>
                @endif
                {{-- Seats --}}
                @if(!is_null($seatsLeft))
                <span class="sc-meta-item {{ $seatsLeft <= 5 ? 'sc-seats-low' : 'sc-seats-ok' }}">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    {{ $seatsLeft <= 0 ? 'Full' : $seatsLeft . ' seats left' }}
                </span>
                @endif
            </div>
        </div>

        {{-- Action --}}
        <div class="sc-action">
            @if($minFee > 0)
            <div>
                <p class="sc-fee-from">Starting from</p>
                <p class="sc-fee-val">{{ number_format($minFee) }}</p>
                <p class="sc-fee-curr">{{ $calCurrency }} / per person</p>
            </div>
            @endif
            @if($s->registration_deadline)
            <div class="sc-deadline">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                Deadline: {{ \Carbon\Carbon::parse($s->registration_deadline)->format('d M Y') }}
                @if($daysLeft !== null && $daysLeft >= 0 && $daysLeft <= 7)
                <span style="background:#fef2f2;color:#dc2626;font-size:10px;padding:1px 6px;border-radius:10px;margin-left:4px;">{{ $daysLeft }}d left</span>
                @endif
            </div>
            @endif
            @if($s->is_open)
            <a href="{{ route('public.enroll', $s->id) }}" class="sc-enroll-btn">
                Enroll Now
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
            @else
            <span class="sc-closed-txt">Registration Closed</span>
            @endif
        </div>
    </div>
    @endforeach
    </div>

    {{-- Pagination --}}
    @if($upcoming->hasPages())
    <div style="margin-top:28px;">{{ $upcoming->links() }}</div>
    @endif

    @endif {{-- end if upcoming not empty --}}


{{-- ══ ARCHIVE TAB ═══════════════════════════════════════════════════════ --}}
@else

    @if($past->isEmpty())
    <div class="cal-empty">
        <div class="cal-empty-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#1e3a8a" stroke-width="1.8"><polyline points="21 8 21 21 3 21 3 8"/><rect x="1" y="3" width="22" height="5"/><line x1="10" y1="12" x2="14" y2="12"/></svg>
        </div>
        <h3>No archive records found</h3>
        <p>No past training records match your filters. Try clearing your filters.</p>
        <a href="{{ route('public.calendar', ['tab'=>'archive']) }}" class="cal-empty-btn">View Full Archive</a>
    </div>
    @else

    {{-- Year quick-filter pills --}}
    @if($archiveYears->count() > 1)
    <div class="arc-year-pills">
        <a href="{{ route('public.calendar', array_merge(request()->except(['year','page']), ['tab'=>'archive'])) }}"
           class="arc-year-pill {{ !request('year') ? 'active' : '' }}">All Years</a>
        @foreach($archiveYears as $yr)
        <a href="{{ route('public.calendar', array_merge(request()->except(['year','page']), ['tab'=>'archive','year'=>$yr])) }}"
           class="arc-year-pill {{ request('year') == $yr ? 'active' : '' }}">{{ $yr }}</a>
        @endforeach
    </div>
    @endif

    <div class="cal-count-bar">
        <p class="cal-count-text">
            <strong>{{ $past->count() }}</strong> past {{ Str::plural('session', $past->count()) }}
            @if(request('year'))<span style="color:#9ca3af;font-weight:400;"> in {{ request('year') }}</span>@endif
        </p>
    </div>

    @foreach($pastByYear as $year => $sessions)
    <div class="arc-year-header">
        <span class="arc-year-num">{{ $year }}</span>
        <div class="arc-year-line"></div>
        <span class="arc-year-count">{{ $sessions->count() }} {{ Str::plural('session', $sessions->count()) }}</span>
    </div>
    @foreach($sessions as $s)
    @php
        $modeKey = match(strtolower($s->training_mode ?? '')) {
            'online'  => 'scm-online',
            'hybrid'  => 'scm-hybrid',
            default   => 'scm-physical',
        };
    @endphp
    <div class="arc-card">
        <div class="arc-date">
            <div class="arc-date-day">{{ \Carbon\Carbon::parse($s->start_date)->format('d') }}</div>
            <div class="arc-date-mon">{{ \Carbon\Carbon::parse($s->start_date)->format('M Y') }}</div>
        </div>
        <div class="arc-info">
            <a href="{{ route('public.course.detail', $s->course->slug ?? $s->course_id) }}" class="arc-title">
                {{ $s->course?->name ?? $s->training_title }}
            </a>
            <div class="arc-meta">
                <span class="arc-meta-item">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    {{ \Carbon\Carbon::parse($s->start_date)->format('d M') }} – {{ \Carbon\Carbon::parse($s->end_date)->format('d M Y') }}
                </span>
                <span class="arc-meta-item">
                    <span class="sc-mode {{ $modeKey }}" style="font-size:11px;">{{ $s->training_mode }}</span>
                </span>
                @if($s->training_mode !== 'Online' && $s->venue)
                <span class="arc-meta-item">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    {{ $s->venue }}
                </span>
                @elseif($s->training_mode === 'Online')
                <span class="arc-meta-item">Online (Zoom)</span>
                @endif
                @if($s->trainer)
                <span class="arc-meta-item">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    {{ $s->trainer->name }}
                </span>
                @endif
                @if($s->batch_code)
                <span class="arc-meta-item">Batch: {{ $s->batch_code }}</span>
                @endif
            </div>
        </div>
        <div class="arc-action">
            <span class="arc-status-badge">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                Completed
            </span>
            <a href="{{ route('public.course.detail', $s->course->slug ?? $s->course_id) }}" class="arc-view-btn">
                View Course
            </a>
        </div>
    </div>
    @endforeach
    @endforeach

    @endif {{-- end if past not empty --}}

@endif {{-- end tab switch --}}

</div>{{-- .cal-body --}}
</div>{{-- .pub-container --}}
@endsection
