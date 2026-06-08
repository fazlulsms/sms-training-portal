@extends('layouts.app')

@section('content')

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<div style="max-width:1100px; margin:auto;">

    <div style="background:white; padding:25px; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.08);">

        <h2 style="font-size:28px; font-weight:700; margin-bottom:20px; color:#111827;">
            Edit Training Schedule
        </h2>

        <form method="POST" action="/admin/training-schedules/update/{{ $schedule->id }}">
            @csrf

            <label>Course</label>
            <select id="courseSelect" name="course_id" style="width:100%;" required>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}"
                        {{ $schedule->course_id == $course->id ? 'selected' : '' }}>
                        {{ $course->name }}
                    </option>
                @endforeach
            </select>

            <label style="display:block; margin-top:15px;">Batch Code</label>
            <input type="text"
                   name="batch_code"
                   value="{{ $schedule->batch_code }}"
                   class="form-control">

            <label style="display:block; margin-top:15px;">Trainer</label>
            <select name="trainer_id" class="form-control" required>
                @foreach($trainers as $trainer)
                    <option value="{{ $trainer->id }}"
                        {{ $schedule->trainer_id == $trainer->id ? 'selected' : '' }}>
                        {{ $trainer->name }}
                    </option>
                @endforeach
            </select>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-top:15px;">
                <div>
                    <label>Start Date</label>
                    <input type="date"
                           name="start_date"
                           value="{{ $schedule->start_date }}"
                           class="form-control"
                           required>
                </div>

                <div>
                    <label>End Date</label>
                    <input type="date"
                           name="end_date"
                           value="{{ $schedule->end_date }}"
                           class="form-control"
                           required>
                </div>
            </div>

            <label style="display:block; margin-top:15px;">Duration</label>
            <input type="text"
                   name="duration"
                   value="{{ $schedule->duration }}"
                   class="form-control">

            <label style="display:block; margin-top:15px;">Training Mode</label>
            <select name="training_mode" class="form-control">
                <option value="Physical" {{ $schedule->training_mode == 'Physical' ? 'selected' : '' }}>Physical Only</option>
                <option value="Online" {{ $schedule->training_mode == 'Online' ? 'selected' : '' }}>Online Only</option>
                <option value="Hybrid" {{ $schedule->training_mode == 'Hybrid' ? 'selected' : '' }}>Hybrid</option>
            </select>

            <label style="display:block; margin-top:15px;">Currency</label>
            <select name="currency" class="form-control">
                <option value="BDT" {{ $schedule->currency == 'BDT' ? 'selected' : '' }}>BDT</option>
                <option value="USD" {{ $schedule->currency == 'USD' ? 'selected' : '' }}>USD</option>
                <option value="VND" {{ $schedule->currency == 'VND' ? 'selected' : '' }}>VND</option>
                <option value="AED" {{ $schedule->currency == 'AED' ? 'selected' : '' }}>AED</option>
            </select>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-top:15px;">
                <div>
                    <label>Physical Fee</label>
                    <input type="number"
                           name="physical_fee"
                           value="{{ $schedule->physical_fee }}"
                           class="form-control">
                </div>

                <div>
                    <label>Online Fee</label>
                    <input type="number"
                           name="online_fee"
                           value="{{ $schedule->online_fee }}"
                           class="form-control">
                </div>
            </div>

            <label style="display:block; margin-top:15px;">Venue</label>
            <input type="text"
                   name="venue"
                   value="{{ $schedule->venue }}"
                   class="form-control">

            <label style="display:block; margin-top:15px;">Zoom Link</label>
            <input type="text"
                   name="zoom_link"
                   value="{{ $schedule->zoom_link }}"
                   class="form-control">

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-top:15px;">
                <div>
                    <label>Maximum Participants</label>
                    <input type="number"
                           name="max_participants"
                           value="{{ $schedule->max_participants }}"
                           class="form-control">
                </div>

                <div>
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="Open" {{ $schedule->status == 'Open' ? 'selected' : '' }}>Open</option>
                        <option value="Closed" {{ $schedule->status == 'Closed' ? 'selected' : '' }}>Closed</option>
                        <option value="Completed" {{ $schedule->status == 'Completed' ? 'selected' : '' }}>Completed</option>
                        <option value="Postponed" {{ $schedule->status == 'Postponed' ? 'selected' : '' }}>Postponed</option>
                        <option value="Cancelled" {{ $schedule->status == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
            </div>

            <div style="margin-top:25px;">
                <button type="submit"
                        style="background:#16a34a; color:white; padding:12px 22px; border:none; border-radius:8px; font-weight:600;">
                    Update Training Schedule
                </button>

                <a href="/admin/training-schedules"
                   style="background:#6b7280; color:white; padding:12px 22px; border-radius:8px; text-decoration:none; margin-left:8px;">
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