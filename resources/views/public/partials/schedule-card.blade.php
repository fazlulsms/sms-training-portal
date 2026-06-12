@php
    $modeClass = match(strtolower($schedule->training_mode ?? 'physical')) {
        'online'  => 'scm-online',
        'hybrid'  => 'scm-hybrid',
        default   => 'scm-physical',
    };
    $currency     = $schedule->currency ?? 'BDT';
    $physicalFee  = (float) ($schedule->physical_fee ?? 0);
    $onlineFee    = (float) ($schedule->online_fee   ?? 0);
    $seatsLeft    = $schedule->seats_left;
    $mode         = $schedule->training_mode ?? 'Physical';

    // Build fee display: show both when Hybrid or when both fees are set and differ
    $showBoth = ($mode === 'Hybrid' || ($physicalFee && $onlineFee && $physicalFee !== $onlineFee))
                && $physicalFee && $onlineFee;
    $singleFee = $mode === 'Online' ? $onlineFee : ($physicalFee ?: $onlineFee);
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
                @if($showBoth)
                <div style="text-align:right;line-height:1.35;">
                    <div style="font-size:11px;font-weight:600;color:#6b7280;margin-bottom:1px;">
                        <span style="color:#3b82f6;">Online</span> {{ $currency }} {{ number_format($onlineFee) }}
                        &nbsp;·&nbsp;
                        <span style="color:#059669;">Physical</span> {{ $currency }} {{ number_format($physicalFee) }}
                    </div>
                </div>
                @elseif($singleFee)
                <div class="sc-fee">{{ $currency }} {{ number_format($singleFee) }}</div>
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
