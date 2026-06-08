@extends('layouts.app')

@section('content')

<style>
.manual-wrap {
    padding: 30px;
}

.manual-card {
    background: #ffffff;
    border-radius: 14px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 8px 22px rgba(0,0,0,0.08);
    margin-bottom: 22px;
    overflow: hidden;
}

.manual-header {
    background: #173a8a;
    color: #ffffff;
    padding: 22px;
}

.manual-header h2 {
    color: #ffffff;
    margin: 0;
    font-size: 24px;
}

.manual-body {
    padding: 22px;
}

.info-row {
    margin-bottom: 10px;
}

.cert-btn {
    display: inline-block;
    background: #0f766e;
    color: #ffffff !important;
    padding: 11px 16px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 700;
    margin-top: 10px;
}

.back-link {
    display: inline-block;
    margin-bottom: 18px;
    color: #173a8a;
    font-weight: 700;
    text-decoration: none;
}
</style>

<div class="manual-wrap">

    <a href="{{ route('participant.my-courses') }}" class="back-link">
        ← Back to My Courses
    </a>

    <div class="manual-card">
        <div class="manual-header">
            <h2>{{ $enrollment->schedule->course->name ?? 'Training Details' }}</h2>
        </div>

        <div class="manual-body">
            <div class="info-row"><strong>Batch:</strong> {{ $enrollment->schedule->batch_code ?? '-' }}</div>
            <div class="info-row"><strong>Trainer:</strong> {{ $enrollment->schedule->trainer->name ?? '-' }}</div>

            <div class="info-row">
                <strong>Training Date:</strong>
                {{ $enrollment->schedule->start_date ?? '-' }}
                to
                {{ $enrollment->schedule->end_date ?? '-' }}
            </div>

            <div class="info-row"><strong>Mode:</strong> {{ $enrollment->schedule->mode ?? '-' }}</div>
            <div class="info-row"><strong>Venue/Link:</strong> {{ $enrollment->schedule->venue ?? $enrollment->schedule->zoom_link ?? '-' }}</div>
            <div class="info-row"><strong>Payment Status:</strong> {{ ucfirst($enrollment->payment_status ?? 'pending') }}</div>
            <div class="info-row"><strong>Attendance Status:</strong> {{ ucfirst($enrollment->attendance_status ?? 'pending') }}</div>
            <div class="info-row"><strong>Completion Status:</strong> {{ ucfirst($enrollment->completion_status ?? 'pending') }}</div>
        </div>
    </div>

    <div class="manual-card">
        <div class="manual-body">
            <h3>Certificate</h3>

            @if(($enrollment->certificate_generated ?? 0) == 1 || !empty($enrollment->certificate_number))
                <div class="info-row"><strong>Certificate Number:</strong> {{ $enrollment->certificate_number ?? '-' }}</div>
                <div class="info-row"><strong>Issue Date:</strong> {{ $enrollment->certificate_issue_date ?? '-' }}</div>

                <a href="{{ url('/certificates/pdf/' . $enrollment->id) }}" class="cert-btn">
                    Download Certificate
                </a>
            @else
                <p>Certificate is not available yet.</p>
            @endif
        </div>
    </div>

</div>

@endsection