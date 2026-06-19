@extends('layouts.app')

@section('page-title', 'Certificate Reports')

@section('content')

<div style="padding:20px 24px;">

    <h2 style="font-size:26px; font-weight:800; margin:0;">Certificate Report</h2>
    <p style="color:#6b7280; margin:5px 0 20px;">Track issued and pending certificates by course and batch.</p>

    <div style="background:white; border:1px solid #e5e7eb; padding:16px; margin-bottom:18px;">
        <form method="GET" action="/admin/reports/certificates" style="display:grid; grid-template-columns:repeat(4, 1fr); gap:12px;">
            <input type="date" name="from_date" value="{{ request('from_date') }}">
            <input type="date" name="to_date" value="{{ request('to_date') }}">

            <select name="course_id">
                <option value="">All Courses</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                        {{ $course->name }}
                    </option>
                @endforeach
            </select>

            <select name="training_schedule_id">
                <option value="">All Batches</option>
                @foreach($schedules as $schedule)
                    <option value="{{ $schedule->id }}" {{ request('training_schedule_id') == $schedule->id ? 'selected' : '' }}>
                        {{ $schedule->batch_code }} | {{ $schedule->course->name ?? 'N/A' }}
                    </option>
                @endforeach
            </select>

            <select name="certificate_status">
                <option value="">Certificate Status</option>
                <option value="Issued" {{ request('certificate_status') == 'Issued' ? 'selected' : '' }}>Issued</option>
                <option value="Pending" {{ request('certificate_status') == 'Pending' ? 'selected' : '' }}>Pending</option>
            </select>

            <button type="submit" style="background:#173a8a; color:white; border:none; font-weight:700;">Filter</button>

            <a href="/admin/reports/certificates"
               style="background:#6b7280; color:white; text-align:center; padding:10px; text-decoration:none; font-weight:700;">
                Reset
            </a>
        </form>
    </div>

    <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:14px; margin-bottom:18px;">
        <div class="summary-card">
            <div>Total Completed</div>
            <strong>{{ $totalCompleted }}</strong>
        </div>

        <div class="summary-card">
            <div>Certificates Issued</div>
            <strong>{{ $totalIssued }}</strong>
        </div>

        <div class="summary-card">
            <div>Certificates Pending</div>
            <strong>{{ $totalPending }}</strong>
        </div>
    </div>

    <div style="background:white; border:1px solid #e5e7eb; padding:12px;">
        <table style="width:100%; border-collapse:collapse; font-size:13px;">
            <thead>
                <tr style="background:#f8fafc;">
                    <th style="padding:10px; text-align:left;">Participant</th>
                    <th style="padding:10px; text-align:left;">Company</th>
                    <th style="padding:10px; text-align:left;">Course</th>
                    <th style="padding:10px; text-align:left;">Batch</th>
                    <th style="padding:10px; text-align:left;">Certificate No.</th>
                    <th style="padding:10px; text-align:left;">Issue Date</th>
                    <th style="padding:10px; text-align:center;">Status</th>
                    <th style="padding:10px; text-align:right;">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($certificates as $item)
                    <tr style="border-bottom:1px solid #e5e7eb;">
                        <td style="padding:10px;">
                            <strong>{{ $item->full_name }}</strong><br>
                            <span style="color:#6b7280;">{{ $item->email }}</span>
                        </td>

                        <td style="padding:10px;">{{ $item->company ?? 'N/A' }}</td>

                        <td style="padding:10px;">{{ $item->trainingSchedule->course->name ?? 'N/A' }}</td>

                        <td style="padding:10px;">{{ $item->trainingSchedule->batch_code ?? 'N/A' }}</td>

                        <td style="padding:10px;">{{ $item->certificate_number ?? 'Not Generated' }}</td>

                        <td style="padding:10px;">{{ $item->certificate_issue_date ?? 'N/A' }}</td>

                        <td style="padding:10px; text-align:center;">
                            @if($item->certificate_generated)
                                <span class="badge success">Issued</span>
                            @else
                                <span class="badge warning">Pending</span>
                            @endif
                        </td>

                        <td style="padding:10px; text-align:right; white-space:nowrap;">
                            @if($item->certificate_generated)
                                <a href="/admin/certificates/view/{{ $item->id }}" target="_blank" class="btn-view">View</a>
                                <a href="/admin/certificates/pdf/{{ $item->id }}" class="btn-pdf">PDF</a>
                            @else
                                <a href="/admin/certificates/generate/{{ $item->id }}" class="btn-generate">Generate</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="padding:25px; text-align:center; color:#6b7280;">
                            No certificate records found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top:14px;">
            {{ $certificates->links() }}
        </div>
    </div>

</div>

<style>
    input, select {
        height:42px;
        padding:8px;
        border:1px solid #d1d5db;
        box-sizing:border-box;
    }

    .summary-card {
        background:white;
        border:1px solid #e5e7eb;
        border-left:5px solid #173a8a;
        padding:16px;
        color:#6b7280;
        font-weight:700;
    }

    .summary-card strong {
        display:block;
        margin-top:8px;
        font-size:26px;
        color:#111827;
    }

    .badge {
        padding:5px 10px;
        font-size:12px;
        font-weight:700;
        border-radius:4px;
    }

    .success {
        background:#dcfce7;
        color:#166534;
    }

    .warning {
        background:#fef3c7;
        color:#92400e;
    }

    .btn-view {
        background:#eef2ff;
        color:#173a8a;
        padding:6px 10px;
        text-decoration:none;
        font-weight:700;
        border-radius:4px;
    }

    .btn-pdf {
        background:#ecfdf5;
        color:#166534;
        padding:6px 10px;
        text-decoration:none;
        font-weight:700;
        border-radius:4px;
    }

    .btn-generate {
        background:#173a8a;
        color:white;
        padding:6px 10px;
        text-decoration:none;
        font-weight:700;
        border-radius:4px;
    }
</style>

@endsection