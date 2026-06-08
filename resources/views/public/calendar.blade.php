@extends('layouts.public')

@section('page-title', 'Training Calendar')
@section('seo-title', 'Upcoming Training Schedule — SMS Training Services')
@section('seo-desc', 'Browse upcoming training batches and register for professional certification programs.')

@section('content')
<style>
.cal-hero { background:linear-gradient(135deg,#1e3a8a,#1d4ed8); padding:48px 0; color:#fff; }
.cal-hero h1 { font-size:34px; font-weight:900; margin:0 0 8px; }
.cal-hero p  { opacity:.8; margin:0; }

.cal-filters {
    background:#fff; border-bottom:1px solid #e9ecf0; padding:18px 0;
    position:sticky; top:64px; z-index:10;
    box-shadow:0 2px 8px rgba(0,0,0,.05);
}
.cal-filter-row { display:flex; gap:14px; align-items:center; flex-wrap:wrap; }
.cal-filter-select {
    padding:9px 14px; border:1.5px solid #e5e7eb; border-radius:10px; font-size:14px;
    font-family:inherit; color:#374151; background:#fff; cursor:pointer;
}
.cal-filter-select:focus { outline:none; border-color:#1e3a8a; }
.cal-filter-btn {
    padding:9px 18px; background:#1e3a8a; color:#fff; border:none; border-radius:10px;
    font-weight:700; font-size:14px; cursor:pointer; font-family:inherit;
}
.cal-filter-clear { font-size:13px; color:#6b7280; text-decoration:none; padding:9px 0; }
.cal-filter-clear:hover { color:#1e3a8a; }

.cal-body { padding:40px 0 60px; }
.cal-grid { display:flex; flex-direction:column; gap:16px; }

.sc-row {
    background:#fff; border:1px solid #e9ecf0; border-radius:14px;
    display:grid; grid-template-columns:80px 1fr auto; gap:0;
    overflow:hidden; transition:box-shadow .15s;
}
.sc-row:hover { box-shadow:0 4px 20px rgba(0,0,0,.08); }

.sc-date-col {
    background:linear-gradient(180deg,#1e3a8a,#1d4ed8);
    color:#fff; display:flex; flex-direction:column; align-items:center;
    justify-content:center; padding:18px 10px; text-align:center;
}
.sc-date-day  { font-size:30px; font-weight:900; line-height:1; }
.sc-date-mon  { font-size:12px; font-weight:700; text-transform:uppercase; opacity:.8; }

.sc-info-col { padding:18px 22px; }
.sc-row-title { font-size:16px; font-weight:800; color:#111827; margin:0 0 8px; text-decoration:none; display:block; }
.sc-row-title:hover { color:#1e3a8a; }
.sc-row-meta { display:flex; flex-wrap:wrap; gap:10px 18px; font-size:13px; color:#6b7280; }
.sc-row-meta-item { display:inline-flex; align-items:center; gap:5px; }

.sc-action-col {
    padding:18px 22px; display:flex; flex-direction:column;
    align-items:flex-end; justify-content:center; gap:10px; min-width:160px;
}
.sc-fee { font-size:18px; font-weight:900; color:#1e3a8a; }
.sc-fee small { font-size:11px; font-weight:600; color:#9ca3af; }

@media(max-width:700px){
    .sc-row { grid-template-columns:60px 1fr; }
    .sc-action-col { grid-column:1/-1; flex-direction:row; justify-content:space-between; padding:12px 16px; border-top:1px solid #f0f2f5; }
    .sc-info-col { padding:14px 16px; }
}
</style>

<div class="cal-hero">
    <div class="pub-container">
        <h1>📅 Training Calendar</h1>
        <p>Browse all upcoming and running training batches. Register before seats fill up.</p>
    </div>
</div>

{{-- Filters bar --}}
<div class="cal-filters">
    <div class="pub-container">
        <form method="GET" action="{{ route('public.calendar') }}">
            <div class="cal-filter-row">
                <input type="month" name="month" class="cal-filter-select"
                       value="{{ request('month') }}" title="Filter by month">

                <select name="mode" class="cal-filter-select">
                    <option value="">All Modes</option>
                    @foreach(['Physical','Online','Hybrid'] as $m)
                    <option value="{{ $m }}" {{ request('mode') === $m ? 'selected' : '' }}>{{ $m }}</option>
                    @endforeach
                </select>

                <select name="course" class="cal-filter-select">
                    <option value="">All Courses</option>
                    @foreach($courses as $c)
                    <option value="{{ $c->id }}" {{ request('course') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>

                <button type="submit" class="cal-filter-btn">🔍 Filter</button>

                @if(request()->hasAny(['month','mode','course']))
                <a href="{{ route('public.calendar') }}" class="cal-filter-clear">✕ Clear</a>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="pub-container">
<div class="cal-body">

    @if($schedules->isEmpty())
    <div style="text-align:center;padding:80px 20px;">
        <div style="font-size:56px;margin-bottom:16px;">📅</div>
        <h3 style="font-size:22px;font-weight:800;color:#111827;margin:0 0 8px;">No schedules found</h3>
        <p style="color:#6b7280;">Try removing filters or <a href="{{ route('public.calendar') }}" style="color:#1e3a8a;font-weight:700;">view all schedules</a>.</p>
    </div>
    @else
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:10px;">
        <p style="color:#6b7280;font-size:14px;margin:0;font-weight:600;">
            <strong style="color:#111827;">{{ $schedules->total() }}</strong> schedule{{ $schedules->total() !== 1 ? 's' : '' }} available
        </p>
    </div>
    <div class="cal-grid">
        @foreach($schedules as $s)
        @php
            $fee = $s->discount_fee ?? ($s->training_mode === 'Online' ? $s->online_fee : ($s->physical_fee ?? $s->online_fee));
            $seatsLeft = $s->seats_left;
        @endphp
        <div class="sc-row">
            <div class="sc-date-col">
                <div class="sc-date-day">{{ \Carbon\Carbon::parse($s->start_date)->format('d') }}</div>
                <div class="sc-date-mon">{{ \Carbon\Carbon::parse($s->start_date)->format('M Y') }}</div>
            </div>
            <div class="sc-info-col">
                <a href="{{ route('public.course.detail', $s->course->slug ?? $s->course_id) }}" class="sc-row-title">
                    {{ $s->course?->name ?? $s->training_title }}
                </a>
                <div class="sc-row-meta">
                    <span class="sc-row-meta-item">
                        📅 {{ \Carbon\Carbon::parse($s->start_date)->format('d M') }} – {{ \Carbon\Carbon::parse($s->end_date)->format('d M Y') }}
                    </span>
                    @if($s->time_start)
                    <span class="sc-row-meta-item">
                        🕒 {{ \Carbon\Carbon::parse($s->time_start)->format('h:i A') }} – {{ \Carbon\Carbon::parse($s->time_end)->format('h:i A') }}
                    </span>
                    @endif
                    <span class="sc-row-meta-item">
                        <span class="sc-mode-badge {{ match(strtolower($s->training_mode ?? '')) { 'online'=>'scm-online','hybrid'=>'scm-hybrid',default=>'scm-physical' } }}">
                            {{ $s->training_mode }}
                        </span>
                    </span>
                    @if($s->training_mode !== 'Online' && $s->venue)
                    <span class="sc-row-meta-item">📍 {{ $s->venue }}</span>
                    @elseif($s->training_mode === 'Online')
                    <span class="sc-row-meta-item">📍 Online (Zoom)</span>
                    @endif
                    @if($s->trainer)
                    <span class="sc-row-meta-item">👤 {{ $s->trainer->name }}</span>
                    @endif
                    @if(!is_null($seatsLeft))
                    <span class="sc-row-meta-item" style="color:{{ $seatsLeft <= 5 ? '#ef4444' : '#16a34a' }};font-weight:700;">
                        {{ $seatsLeft <= 0 ? '🔴 Full' : '🟢 ' . $seatsLeft . ' seats left' }}
                    </span>
                    @endif
                    @if($s->registration_deadline)
                    <span class="sc-row-meta-item">⏰ Deadline: {{ \Carbon\Carbon::parse($s->registration_deadline)->format('d M Y') }}</span>
                    @endif
                </div>
            </div>
            <div class="sc-action-col">
                @if($fee)
                <div class="sc-fee">{{ $s->currency ?? 'BDT' }} {{ number_format($fee) }}<br><small>per person</small></div>
                @endif
                @if($s->is_open)
                <a href="{{ route('public.enroll', $s->id) }}" class="pub-enroll-btn">Enroll Now</a>
                @else
                <span style="font-size:12.5px;font-weight:700;color:#9ca3af;">Registration Closed</span>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    <div style="margin-top:32px;">{{ $schedules->links() }}</div>
    @endif
</div>
</div>
@endsection
