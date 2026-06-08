@extends('layouts.app')

@section('content')

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<div style="max-width:1100px; margin:auto;">

    <div style="background:white; padding:25px; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.08);">

        <h2 style="font-size:28px; font-weight:700; margin-bottom:20px; color:#111827;">
            Add Training Schedule
        </h2>

        <form method="POST" action="/admin/training-schedules/store">
            @csrf

            <label>Course</label>
            <select id="courseSelect" name="course_id" style="width:100%;" required>
                <option value="">Search and select course</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}">{{ $course->name }}</option>
                @endforeach
            </select>

            <label style="display:block; margin-top:15px;">Batch Code</label>
            <input type="text" name="batch_code" placeholder="Example: SMS-ISO9001-LA-2026-01" class="form-control">

            <label style="display:block; margin-top:15px;">Trainer</label>
            <select name="trainer_id" class="form-control" required>
                <option value="">Select Trainer</option>
                @foreach($trainers as $trainer)
                    <option value="{{ $trainer->id }}">{{ $trainer->name }}</option>
                @endforeach
            </select>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-top:15px;">
                <div>
                    <label>Start Date</label>
                    <input type="date" name="start_date" class="form-control" required>
                </div>

                <div>
                    <label>End Date</label>
                    <input type="date" name="end_date" class="form-control" required>
                </div>
            </div>

            <label style="display:block; margin-top:15px;">Duration</label>
            <input type="text" name="duration" placeholder="Example: 40 Hours / 5 Days" class="form-control">

            <label style="display:block; margin-top:15px;">Training Mode</label>
            <select name="training_mode" class="form-control">
                <option value="Physical">Physical Only</option>
                <option value="Online">Online Only</option>
                <option value="Hybrid">Hybrid</option>
            </select>

            <label style="display:block; margin-top:15px;">Currency</label>
            <select name="currency" class="form-control">
                <option value="BDT">BDT</option>
                <option value="USD">USD</option>
                <option value="VND">VND</option>
                <option value="AED">AED</option>
            </select>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-top:15px;">
                <div>
                    <label>Physical Fee</label>
                    <input type="number" name="physical_fee" class="form-control">
                </div>

                <div>
                    <label>Online Fee</label>
                    <input type="number" name="online_fee" class="form-control">
                </div>
            </div>

            <label style="display:block; margin-top:15px;">Venue</label>
            <input type="text" name="venue" class="form-control">

            <label style="display:block; margin-top:15px;">Zoom Link</label>
            <input type="text" name="zoom_link" class="form-control">

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-top:15px;">
                <div>
                    <label>Maximum Participants</label>
                    <input type="number" name="max_participants" class="form-control">
                </div>

                <div>
                    <label>Status</label>
                    <select name="status" class="form-control">
    <option value="Open">Open</option>
    <option value="Closed">Closed</option>
    <option value="Completed">Completed</option>
    <option value="Postponed">Postponed</option>
    <option value="Cancelled">Cancelled</option>
</select>

                </div>
            </div>

            <div style="margin-top:25px;">
                <button type="submit" style="background:#16a34a; color:white; padding:12px 22px; border:none; border-radius:8px; font-weight:600;">
                    Save Training Schedule
                </button>

                <a href="/admin/training-schedules" style="background:#6b7280; color:white; padding:12px 22px; border-radius:8px; text-decoration:none; margin-left:8px;">
                    Back
                </a>
            </div>

        </form>
    </div>

</div>

<style>
    .form-control {
        width: 100%;
        padding: 12px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        margin-top: 6px;
        box-sizing: border-box;
        background: white;
    }

    label {
        font-weight: 600;
        color: #374151;
    }

    .select2-container {
        margin-top: 6px;
    }

    .select2-container .select2-selection--single {
        height: 46px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 8px;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 28px;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 44px;
    }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    $('#courseSelect').select2({
        placeholder: "Search course...",
        allowClear: true
    });
});
</script>

@endsection