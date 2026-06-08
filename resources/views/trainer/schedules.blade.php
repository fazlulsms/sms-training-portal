@extends('layouts.app')
@section('page-title', 'My Schedules')
@section('content')

<x-page-header title="My Training Schedules" desc="All training sessions assigned to you.">
</x-page-header>

<x-flash-message />

<div class="dt-wrap">
    <div class="dt-scroll">
        <table class="dt">
            <thead>
                <tr>
                    <th>Course</th>
                    <th>Batch</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Mode</th>
                    <th>Venue / Link</th>
                    <th class="c">Enrolled</th>
                    <th class="c">Status</th>
                    <th class="c">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($schedules as $schedule)
                @php
                    $s = strtolower($schedule->status ?? '');
                    $stBadge = match($s) {
                        'open'      => 'badge-success',
                        'closed'    => 'badge-danger',
                        'completed' => 'badge-secondary',
                        default     => 'badge-warning',
                    };
                @endphp
                <tr>
                    <td class="td-main">{{ $schedule->course->name ?? '—' }}</td>
                    <td>{{ $schedule->batch_code ?? '—' }}</td>
                    <td class="nowrap text-muted">{{ $schedule->start_date ? \Carbon\Carbon::parse($schedule->start_date)->format('d M Y') : '—' }}</td>
                    <td class="nowrap text-muted">{{ $schedule->end_date   ? \Carbon\Carbon::parse($schedule->end_date)->format('d M Y')   : '—' }}</td>
                    <td>{{ $schedule->training_mode ?? '—' }}</td>
                    <td style="max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" class="text-muted text-small">
                        {{ $schedule->venue ?? $schedule->zoom_link ?? '—' }}
                    </td>
                    <td class="c">
                        <span style="background:#eff6ff;color:#1e3a8a;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:700;">{{ $schedule->enrollments->count() }}</span>
                    </td>
                    <td class="c"><span class="badge {{ $stBadge }}">{{ ucfirst($schedule->status ?? '—') }}</span></td>
                    <td class="c">
                        <a href="{{ route('trainer.schedule.participants', $schedule->id) }}" class="btn btn-edit btn-xs">Participants →</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9">
                        <div class="empty-state">
                            <div class="empty-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            </div>
                            <p class="empty-title">No schedules assigned yet</p>
                            <p class="empty-desc">Contact the admin to assign you to a training schedule.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
