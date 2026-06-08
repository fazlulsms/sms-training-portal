@extends('layouts.app')
@section('page-title', 'Certificates')
@section('content')

<x-page-header title="Certificate Management" desc="Generate and manage training completion certificates.">
</x-page-header>

<x-flash-message />

<div class="filter-bar" style="margin-bottom:20px;">
    <form method="POST" action="/admin/certificates/filter">
        @csrf
        <div class="filter-row">
            <div class="filter-group" style="flex:1;min-width:280px;">
                <label>Training Schedule</label>
                <select class="fi" name="training_schedule_id" required style="width:100%;">
                    <option value="">Select a schedule…</option>
                    @foreach($schedules as $schedule)
                        <option value="{{ $schedule->id }}"
                            {{ isset($selectedSchedule) && $selectedSchedule == $schedule->id ? 'selected' : '' }}>
                            {{ $schedule->course->name ?? 'N/A' }} | {{ $schedule->batch_code }}
                            | {{ $schedule->start_date }} → {{ $schedule->end_date }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div style="display:flex;align-items:flex-end;">
                <button class="btn btn-primary btn-sm" type="submit">Filter</button>
            </div>
        </div>
    </form>
</div>

<div class="dt-wrap">
    <div class="dt-scroll">
        <table class="dt">
            <thead>
                <tr>
                    <th>Participant</th>
                    <th>Company</th>
                    <th>Course</th>
                    <th>Certificate No.</th>
                    <th>Issue Date</th>
                    <th class="c">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($enrollments as $enrollment)
                <tr>
                    <td class="td-main">{{ $enrollment->full_name }}</td>
                    <td>{{ $enrollment->company ?? '—' }}</td>
                    <td>{{ $enrollment->trainingSchedule->course->name ?? 'N/A' }}</td>
                    <td>
                        @if($enrollment->certificate_number)
                            <span class="td-mono">{{ $enrollment->certificate_number }}</span>
                        @else
                            <span class="text-muted text-small">Not generated</span>
                        @endif
                    </td>
                    <td class="text-muted text-small">{{ $enrollment->certificate_issue_date ?? '—' }}</td>
                    <td class="c">
                        <div class="dt-actions" style="justify-content:center;">
                            @if(!$enrollment->certificate_generated)
                                <a href="/admin/certificates/generate/{{ $enrollment->id }}" class="btn btn-primary btn-xs">Generate</a>
                            @else
                                <a href="/admin/certificates/view/{{ $enrollment->id }}" target="_blank" class="btn btn-view btn-xs">View</a>
                                <a href="/admin/certificates/pdf/{{ $enrollment->id }}" target="_blank" class="btn btn-approve btn-xs">PDF</a>
                                <a href="/admin/certificates/delete/{{ $enrollment->id }}"
                                   onclick="return confirm('Delete this certificate?')"
                                   class="btn btn-del btn-xs">Delete</a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <div class="empty-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg>
                            </div>
                            <p class="empty-title">No participants found</p>
                            <p class="empty-desc">Select a training schedule above to view completed participants.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
