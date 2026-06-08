@extends('layouts.app')
@section('page-title', 'Training Schedules')
@section('content')

<x-page-header title="Training Schedules" desc="View and manage all scheduled training sessions.">
    <x-slot:actions>
        <a href="/admin/training-schedules/create" class="btn btn-primary btn-sm">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add Schedule
        </a>
    </x-slot:actions>
</x-page-header>

<x-flash-message />

<div class="filter-bar">
    <div class="filter-row">
        <div class="fi-search-wrap" style="flex:1;min-width:220px;">
            <span class="fi-search-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></span>
            <input class="fi fi-search" type="text" id="schSearch" placeholder="Search course, trainer, batch…" style="width:100%;">
        </div>
        <select class="fi" id="schStatus" style="min-width:140px;">
            <option value="">All Status</option>
            <option value="Open">Open</option>
            <option value="Closed">Closed</option>
            <option value="Completed">Completed</option>
            <option value="Postponed">Postponed</option>
            <option value="Cancelled">Cancelled</option>
        </select>
    </div>
</div>

<div class="dt-wrap">
    <div class="dt-scroll">
        <table class="dt" id="schTable">
            <thead>
                <tr>
                    <th>Course / Batch</th>
                    <th>Dates</th>
                    <th>Trainer</th>
                    <th class="c">Status</th>
                    <th class="c">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($schedules as $schedule)
                @php
                    $st = $schedule->status;
                    $stBadge = match($st) {
                        'Open'      => 'badge-success',
                        'Closed'    => 'badge-danger',
                        'Completed' => 'badge-info',
                        'Postponed' => 'badge-warning',
                        default     => 'badge-secondary',
                    };
                @endphp
                <tr>
                    <td>
                        <div class="td-main">{{ $schedule->course->name ?? 'N/A' }}</div>
                        @if($schedule->batch_code)
                            <div class="td-sub">Batch: {{ $schedule->batch_code }}</div>
                        @endif
                    </td>
                    <td>
                        <div class="nowrap">{{ \Carbon\Carbon::parse($schedule->start_date)->format('d M Y') }}</div>
                        <div class="td-sub">to {{ \Carbon\Carbon::parse($schedule->end_date)->format('d M Y') }}</div>
                    </td>
                    <td>{{ $schedule->trainer->name ?? '—' }}</td>
                    <td class="c"><span class="badge {{ $stBadge }}">{{ $st }}</span></td>
                    <td class="c">
                        <div class="dt-actions" style="justify-content:center;">
                            <a href="{{ route('attendance.sheet', $schedule->id) }}" class="btn btn-primary btn-xs">Attendance</a>
                            <a href="/admin/training-schedules/edit/{{ $schedule->id }}" class="btn btn-edit btn-xs">Edit</a>
                            <a href="/admin/training-schedules/delete/{{ $schedule->id }}"
                               onclick="return confirm('Delete this schedule?')"
                               class="btn btn-del btn-xs">Delete</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5">
                        <div class="empty-state">
                            <div class="empty-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            </div>
                            <p class="empty-title">No schedules found</p>
                            <p class="empty-desc">Create your first training schedule.</p>
                            <a href="/admin/training-schedules/create" class="btn btn-primary btn-sm">Add Schedule</a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
(function () {
    const search = document.getElementById('schSearch');
    const stF    = document.getElementById('schStatus');
    const rows   = () => document.querySelectorAll('#schTable tbody tr[class!=""]');

    function filter() {
        const q = search.value.toLowerCase();
        const s = stF.value.toLowerCase();
        document.querySelectorAll('#schTable tbody tr').forEach(r => {
            if (!r.querySelector('td')) return;
            const txt = r.innerText.toLowerCase();
            r.style.display = (!q || txt.includes(q)) && (!s || txt.includes(s)) ? '' : 'none';
        });
    }
    [search, stF].forEach(el => el.addEventListener('input', filter));
})();
</script>

@endsection
