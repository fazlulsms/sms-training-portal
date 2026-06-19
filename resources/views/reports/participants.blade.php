@extends('layouts.app')

@section('page-title', 'Participant Reports')

@section('content')

<div style="padding:20px 24px;">

    <h2 style="font-size:26px; font-weight:800; margin:0;">Participant Report</h2>
    <p style="color:#6b7280; margin:5px 0 20px;">Filter participants by date, course, batch, payment, and completion status.</p>

    <div style="background:white; border:1px solid #e5e7eb; padding:16px; margin-bottom:18px;">
        <form method="GET" action="/admin/reports/participants" style="display:grid; grid-template-columns:repeat(4, 1fr); gap:12px;">
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

            <select name="payment_status">
                <option value="">Payment Status</option>
                <option value="Paid" {{ request('payment_status') == 'Paid' ? 'selected' : '' }}>Paid</option>
                <option value="Pending" {{ request('payment_status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                <option value="Unpaid" {{ request('payment_status') == 'Unpaid' ? 'selected' : '' }}>Unpaid</option>
                <option value="Partial" {{ request('payment_status') == 'Partial' ? 'selected' : '' }}>Partial</option>
            </select>

            <select name="completion_status">
                <option value="">Completion Status</option>
                <option value="Completed" {{ request('completion_status') == 'Completed' ? 'selected' : '' }}>Completed</option>
                <option value="Pending" {{ request('completion_status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                <option value="Not Completed" {{ request('completion_status') == 'Not Completed' ? 'selected' : '' }}>Not Completed</option>
            </select>

            <button type="submit" style="background:#173a8a; color:white; border:none; font-weight:700;">Filter</button>
            <a href="/admin/reports/participants" style="background:#6b7280; color:white; text-align:center; padding:10px; text-decoration:none; font-weight:700;">Reset</a>
        </form>
    </div>

    <div style="display:grid; grid-template-columns:repeat(4, 1fr); gap:14px; margin-bottom:18px;">
        <div class="summary-card">
            <div>Total Registered</div>
            <strong>{{ $totalRegistered }}</strong>
        </div>

        <div class="summary-card">
            <div>Total Completed</div>
            <strong>{{ $totalCompleted }}</strong>
        </div>

        <div class="summary-card">
            <div>Total Paid</div>
            <strong>{{ $totalPaid }}</strong>
        </div>

        <div class="summary-card">
            <div>Certificates Issued</div>
            <strong>{{ $totalCertificates }}</strong>
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
                    <th style="padding:10px; text-align:left;">Mode</th>
                    <th style="padding:10px; text-align:left;">Payment</th>
                    <th style="padding:10px; text-align:left;">Completion</th>
                    <th style="padding:10px; text-align:right;">Amount</th>
                </tr>
            </thead>

            <tbody>
                @forelse($participants as $participant)
                    <tr style="border-bottom:1px solid #e5e7eb;">
                        <td style="padding:10px;">
                            <strong>{{ $participant->full_name }}</strong><br>
                            <span style="color:#6b7280;">{{ $participant->email }}</span>
                        </td>
                        <td style="padding:10px;">{{ $participant->company ?? 'N/A' }}</td>
                        <td style="padding:10px;">{{ $participant->trainingSchedule->course->name ?? 'N/A' }}</td>
                        <td style="padding:10px;">{{ $participant->trainingSchedule->batch_code ?? 'N/A' }}</td>
                        <td style="padding:10px;">{{ $participant->selected_mode }}</td>
                        <td style="padding:10px;">{{ $participant->payment_status }}</td>
                        <td style="padding:10px;">{{ $participant->completion_status }}</td>
                        <td style="padding:10px; text-align:right;">
                            {{ number_format($participant->amount_received ?? 0, 2) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="padding:25px; text-align:center; color:#6b7280;">
                            No participant records found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top:14px;">
            {{ $participants->links() }}
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
</style>

@endsection