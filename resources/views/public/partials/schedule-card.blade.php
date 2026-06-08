@php
    $modeClass = match(strtolower($schedule->training_mode ?? 'physical')) {
        'online'  => 'scm-online',
        'hybrid'  => 'scm-hybrid',
        default   => 'scm-physical',
    };
    $fee = $schedule->training_mode === 'Online'
        ? ($schedule->discount_fee ?? $schedule->online_fee)
        : ($schedule->discount_fee ?? $schedule->physical_fee ?? $schedule->online_fee);
    $currency = $schedule->currency ?? 'BDT';
    $seatsLeft = $schedule->seats_left;
@endphp
<div class="schedule-card">
    <div class="schedule-date-block">
        <div class="sc-day">{{ \Carbon\Carbon::parse($schedule->start_date)->format('d') }}</div>
        <div class="sc-mon">{{ \Carbon\Carbon::parse($schedule->start_date)->format('M') }}</div>
    </div>
    <div class="sc-body">
        <div class="sc-title">{{ Str::limit($schedule->course->name ?? $schedule->training_title, 55) }}</div>
        <div class="sc-meta">
            <span class="sc-meta-item">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                {{ \Carbon\Carbon::parse($schedule->start_date)->format('d M') }} – {{ \Carbon\Carbon::parse($schedule->end_date)->format('d M Y') }}
            </span>
            @if($schedule->venue || $schedule->zoom_link)
            <span class="sc-meta-item">
                📍 {{ $schedule->training_mode === 'Online' ? 'Online (Zoom)' : ($schedule->venue ?? 'TBA') }}
            </span>
            @endif
            @if($schedule->trainer)
            <span class="sc-meta-item">👤 {{ $schedule->trainer->name }}</span>
            @endif
        </div>
        <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap;">
            <div style="display:flex;align-items:center;gap:8px;">
                <span class="sc-mode-badge {{ $modeClass }}">{{ $schedule->training_mode }}</span>
                @if(!is_null($seatsLeft))
                <span style="font-size:12px;font-weight:600;color:{{ $seatsLeft <= 5 ? '#ef4444' : '#16a34a' }};">
                    {{ $seatsLeft <= 0 ? 'Full' : $seatsLeft . ' seats left' }}
                </span>
                @endif
            </div>
            <div style="display:flex;align-items:center;gap:10px;">
                @if($fee)
                <div class="sc-fee">{{ $currency }} {{ number_format($fee) }}</div>
                @endif
                @if($schedule->is_open && ($seatsLeft === null || $seatsLeft > 0))
                <a href="{{ route('public.enroll', $schedule->id) }}" class="pub-enroll-btn" style="padding:7px 14px;font-size:12.5px;">
                    Enroll
                </a>
                @else
                <span style="font-size:12px;font-weight:700;color:#9ca3af;">Closed</span>
                @endif
            </div>
        </div>
    </div>
</div>
