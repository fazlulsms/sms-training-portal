@extends('layouts.app')

@section('content')

<div style="max-width:700px; margin:auto;">

    <div style="background:white; padding:25px; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.08);">

        <h2 style="font-size:28px; font-weight:700; margin-bottom:20px;">
            Generate Certificate
        </h2>

        <p><strong>Name:</strong> {{ $enrollment->full_name }}</p>
        <p><strong>Course:</strong> {{ $enrollment->trainingSchedule->course->name ?? 'N/A' }}</p>
        <p><strong>Batch:</strong> {{ $enrollment->trainingSchedule->batch_code ?? 'N/A' }}</p>
        <p><strong>Date:</strong> {{ $enrollment->trainingSchedule->start_date ?? 'N/A' }} to {{ $enrollment->trainingSchedule->end_date ?? 'N/A' }}</p>

        <form method="POST" action="/admin/certificates/generate/{{ $enrollment->id }}" style="margin-top:20px;">
            @csrf

            <label style="font-weight:600;">Certificate Template</label>
            <select name="certificate_template" style="width:100%; padding:12px; margin-top:8px; border:1px solid #d1d5db; border-radius:8px;">
                <option value="completion">Certificate of Completion</option>
                <option value="attendance">Certificate of Attendance</option>
            </select>

            <label style="display:block; font-weight:600; margin-top:15px;">Certificate Issue Date</label>
            <input type="date" name="certificate_issue_date" required
                   style="width:100%; padding:12px; margin-top:8px; border:1px solid #d1d5db; border-radius:8px;">

            <div style="margin-top:25px;">
                <button type="submit"
                        style="background:#16a34a; color:white; padding:12px 22px; border:none; border-radius:8px; font-weight:600;">
                    Generate Certificate
                </button>

                <a href="/admin/certificates"
                   style="background:#6b7280; color:white; padding:12px 22px; border-radius:8px; text-decoration:none; margin-left:8px;">
                    Back
                </a>
            </div>
        </form>

    </div>

</div>

@endsection