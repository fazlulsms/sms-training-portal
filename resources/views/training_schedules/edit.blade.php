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

    {{-- ── Knowledge Test Assignment ────────────────────────────── --}}
    @php
        $examAssignment = $schedule->questionAssignment()->with('questionSet')->first();
        $activeQS = \App\Models\QuestionSet::where('status','Active')->orderBy('title')->get();
    @endphp
    <div style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:24px;margin-top:20px;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px;">
            <div>
                <div style="font-size:14px;font-weight:800;color:#1e293b;">📋 Knowledge Test (Optional)</div>
                <div style="font-size:12px;color:#64748b;margin-top:3px;">Require participants to pass an exam before certificate can be issued.</div>
            </div>
            <a href="/admin/training-exams/{{ $schedule->id }}/results"
               style="background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;border-radius:8px;padding:7px 14px;font-size:12px;font-weight:700;text-decoration:none;">
                📊 View Exam Results
            </a>
        </div>

        @if($examAssignment)
        <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:14px 18px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:14px;">
            <div>
                <div style="font-size:13px;font-weight:800;color:#166534;">✅ Exam Assigned</div>
                <div style="font-size:13px;color:#374151;margin-top:4px;">{{ $examAssignment->questionSet->title }}</div>
                <div style="font-size:12px;color:#64748b;margin-top:2px;">
                    {{ $examAssignment->questionSet->total_marks }} marks ·
                    Pass: {{ $examAssignment->questionSet->effectivePassMark() }} ·
                    Attempts: {{ $examAssignment->effectiveAttempts() }}
                </div>
            </div>
            <form method="POST" action="/admin/training-schedules/{{ $schedule->id }}/assign-exam">
                @csrf
                <input type="hidden" name="require_exam" value="0">
                <button type="submit" onclick="return confirm('Remove exam from this schedule?')"
                        style="background:#fef2f2;border:1px solid #fecaca;color:#dc2626;border-radius:8px;padding:7px 14px;font-size:12px;font-weight:700;cursor:pointer;">
                    ✕ Remove Exam
                </button>
            </form>
        </div>
        @else
        <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:8px;padding:12px 16px;font-size:13px;color:#92400e;margin-bottom:14px;">
            ℹ️ No exam assigned. Certificates can be issued based on attendance/completion only.
        </div>
        @endif

        @if($activeQS->isNotEmpty())
        <form method="POST" action="/admin/training-schedules/{{ $schedule->id }}/assign-exam">
            @csrf
            <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;">
                <div style="flex:1;min-width:200px;">
                    <label style="font-size:11px;font-weight:800;color:#475569;text-transform:uppercase;letter-spacing:.04em;display:block;margin-bottom:5px;">{{ $examAssignment ? 'Change' : 'Assign' }} Question Set</label>
                    <select name="question_set_id" required class="form-control" style="margin-top:0;">
                        <option value="">Select Question Set…</option>
                        @foreach($activeQS as $qs)
                        <option value="{{ $qs->id }}" {{ $examAssignment?->question_set_id == $qs->id ? 'selected' : '' }}>
                            {{ $qs->title }} ({{ $qs->total_marks }} marks)
                        </option>
                        @endforeach
                    </select>
                </div>
                <div style="width:160px;">
                    <label style="font-size:11px;font-weight:800;color:#475569;text-transform:uppercase;letter-spacing:.04em;display:block;margin-bottom:5px;">Override Attempts</label>
                    <input type="number" name="allowed_attempts" class="form-control" style="margin-top:0;"
                           value="{{ $examAssignment?->allowed_attempts }}" placeholder="(use default)" min="1" max="10">
                </div>
                <div>
                    <label style="display:flex;align-items:center;gap:6px;font-size:12px;font-weight:600;cursor:pointer;color:#374151;margin-bottom:8px;">
                        <input type="checkbox" name="exam_active_after_attendance" value="1"
                               {{ $examAssignment?->exam_active_after_attendance !== false ? 'checked' : '' }}>
                        Auto-send after attendance
                    </label>
                    <button type="submit"
                            style="background:#1e3a8a;color:#fff;border:none;border-radius:8px;padding:10px 20px;font-size:13px;font-weight:700;cursor:pointer;">
                        📋 {{ $examAssignment ? 'Update' : 'Assign' }} Exam
                    </button>
                </div>
            </div>
        </form>
        @else
        <div style="font-size:13px;color:#94a3b8;">No active question sets available. <a href="/admin/question-sets/create" style="color:#1d4ed8;">Create one</a> first.</div>
        @endif
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