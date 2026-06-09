@extends('layouts.app')
@section('title', isset($session) ? 'Edit Session' : 'New Training Session')
@section('content')

<style>
.form-page { max-width:820px; }
.breadcrumb { display:flex; align-items:center; gap:6px; font-size:12.5px; color:#9ca3af; margin-bottom:6px; }
.breadcrumb a { color:#6b7280; text-decoration:none; font-weight:600; }
.breadcrumb a:hover { color:#1e3a8a; }
.breadcrumb-sep { color:#d1d5db; }
.form-section { background:#fff; border:1px solid #e5e9f0; border-radius:16px; overflow:hidden; margin-bottom:16px; box-shadow:0 1px 4px rgba(15,23,42,.04); }
.form-section-header { padding:16px 24px 14px; border-bottom:1px solid #f0f2f7; display:flex; align-items:center; gap:10px; background:#fafbfd; }
.form-section-icon { width:34px; height:34px; border-radius:9px; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0; }
.form-section-title { font-size:13.5px; font-weight:800; color:#111827; }
.form-section-sub   { font-size:12px; color:#9ca3af; margin-top:1px; }
.form-section-body  { padding:22px 24px; }
.field-grid { display:grid; gap:18px; }
.col-2 { grid-template-columns:1fr 1fr; }
.col-3 { grid-template-columns:1fr 1fr 1fr; }
.col-span-2 { grid-column:1/-1; }
.field-label { display:block; font-size:12px; font-weight:700; color:#374151; text-transform:uppercase; letter-spacing:.5px; margin-bottom:7px; }
.field-label .req { color:#ef4444; margin-left:2px; }
.field-input { display:block; width:100%; padding:10px 14px; font-size:14px; font-family:inherit; color:#111827; background:#fff; border:1.5px solid #e5e9f0; border-radius:10px; outline:none; transition:border-color .15s,box-shadow .15s; line-height:1.4; }
.field-input:focus { border-color:#1e3a8a; box-shadow:0 0 0 3px rgba(30,58,138,.09); }
.field-input.is-invalid { border-color:#ef4444; }
textarea.field-input { resize:vertical; min-height:90px; }
select.field-input { cursor:pointer; }
.field-error { font-size:12px; color:#ef4444; margin-top:5px; font-weight:600; }
.form-actions { background:#fff; border:1px solid #e5e9f0; border-radius:16px; padding:18px 24px; display:flex; align-items:center; justify-content:space-between; box-shadow:0 1px 4px rgba(15,23,42,.04); }
.form-actions-right { display:flex; gap:10px; }

/* Status pill */
.status-pills { display:flex; gap:8px; flex-wrap:wrap; margin-top:4px; }
.status-pill {
    padding:6px 14px; border-radius:20px; font-size:12.5px; font-weight:700;
    cursor:pointer; border:2px solid transparent; transition:all .15s;
    user-select:none;
}
.status-pill input { display:none; }
.status-pill:has(input:checked) { border-color: currentColor; }

@media(max-width:600px){
    .col-2,.col-3 { grid-template-columns:1fr; }
    .col-span-2 { grid-column:auto; }
    .form-actions { flex-direction:column; gap:12px; align-items:stretch; }
    .form-actions-right { justify-content:flex-end; }
}
</style>

<div class="form-page">

    <div class="breadcrumb">
        <a href="{{ route('corporate.projects.index') }}">Corporate</a>
        <span class="breadcrumb-sep">/</span>
        <a href="{{ route('corporate.sessions.index') }}">Sessions</a>
        <span class="breadcrumb-sep">/</span>
        <span>{{ isset($session) ? 'Edit' : 'New' }}</span>
    </div>

    <div class="page-header" style="margin-bottom:22px;">
        <div>
            <h1 class="page-title">{{ isset($session) ? 'Edit Training Session' : 'New Training Session' }}</h1>
            <p class="page-subtitle">{{ isset($session) ? 'Update session details below' : 'Schedule a new corporate training session' }}</p>
        </div>
        <a href="{{ route('corporate.sessions.index') }}" class="btn btn-secondary">← Back</a>
    </div>

    <form method="POST" action="{{ isset($session) ? route('corporate.sessions.update', $session) : route('corporate.sessions.store') }}">
        @csrf
        @if(isset($session)) @method('PUT') @endif

        {{-- Section 1: Project & Course --}}
        <div class="form-section">
            <div class="form-section-header">
                <div class="form-section-icon" style="background:#eff6ff;">📋</div>
                <div>
                    <div class="form-section-title">Session Information</div>
                    <div class="form-section-sub">Link to a project and define what will be delivered</div>
                </div>
            </div>
            <div class="form-section-body">
                <div class="field-grid col-2">
                    <div class="col-span-2">
                        <label class="field-label">Project <span class="req">*</span></label>
                        <select name="corporate_project_id"
                                class="field-input {{ $errors->has('corporate_project_id') ? 'is-invalid' : '' }}" required>
                            <option value="">Select a project…</option>
                            @foreach($projects as $p)
                            <option value="{{ $p->id }}"
                                {{ old('corporate_project_id', $session->corporate_project_id ?? $selectedProject?->id) == $p->id ? 'selected' : '' }}>
                                {{ $p->project_name }} — {{ $p->company_name }}
                            </option>
                            @endforeach
                        </select>
                        @error('corporate_project_id')<div class="field-error">⚠ {{ $message }}</div>@enderror
                    </div>

                    <div class="col-span-2">
                        <label class="field-label">Course / Training Title <span class="req">*</span></label>
                        <input type="text" name="course_name"
                               class="field-input {{ $errors->has('course_name') ? 'is-invalid' : '' }}"
                               value="{{ old('course_name', $session->course_name ?? '') }}"
                               required placeholder="e.g. Safety Induction, Fire Emergency Drill, ISO Awareness">
                        @error('course_name')<div class="field-error">⚠ {{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="field-label">Trainer / Facilitator</label>
                        <input type="text" name="trainer_name" class="field-input"
                               value="{{ old('trainer_name', $session->trainer_name ?? '') }}"
                               placeholder="Full name">
                    </div>
                    <div>
                        <label class="field-label">Target Group / Audience</label>
                        <input type="text" name="target_group" class="field-input"
                               value="{{ old('target_group', $session->target_group ?? '') }}"
                               placeholder="e.g. Factory Workers, Supervisors, New Recruits">
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 2: Schedule --}}
        <div class="form-section">
            <div class="form-section-header">
                <div class="form-section-icon" style="background:#fefce8;">📅</div>
                <div>
                    <div class="form-section-title">Schedule & Venue</div>
                    <div class="form-section-sub">When and where the training takes place</div>
                </div>
            </div>
            <div class="form-section-body">
                <div class="field-grid col-3">
                    <div>
                        <label class="field-label">Start Date <span class="req">*</span></label>
                        <input type="date" name="training_date" class="field-input" required
                               value="{{ old('training_date', $session?->training_date?->format('Y-m-d') ?? '') }}">
                    </div>
                    <div>
                        <label class="field-label">End Date <span style="color:#9ca3af;font-weight:600;font-size:11px;">(if multi-day)</span></label>
                        <input type="date" name="training_date_end" class="field-input"
                               value="{{ old('training_date_end', $session?->training_date_end?->format('Y-m-d') ?? '') }}">
                    </div>
                    <div>
                        <label class="field-label">Duration</label>
                        <input type="text" name="duration" class="field-input"
                               value="{{ old('duration', $session->duration ?? '') }}"
                               placeholder="e.g. 1 Day, 8 Hours, 2×4hrs">
                    </div>
                    <div class="col-span-2">
                        <label class="field-label">Venue / Location</label>
                        <input type="text" name="venue" class="field-input"
                               value="{{ old('venue', $session->venue ?? '') }}"
                               placeholder="e.g. Training Room A, ABC Factory Canteen, Zoom (Online)">
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 3: Status --}}
        <div class="form-section">
            <div class="form-section-header">
                <div class="form-section-icon" style="background:#f0fdf4;">🔖</div>
                <div>
                    <div class="form-section-title">Session Status</div>
                    <div class="form-section-sub">Current state of this training session</div>
                </div>
            </div>
            <div class="form-section-body">
                @php
                    $statuses = [
                        'Planned'   => ['#f0f4ff','#1e3a8a','🗓'],
                        'Ongoing'   => ['#f0fdf4','#16a34a','▶'],
                        'Completed' => ['#dbeafe','#2563eb','✓'],
                        'Cancelled' => ['#fee2e2','#dc2626','✕'],
                    ];
                    $selStatus = old('status', $session->status ?? 'Planned');
                @endphp
                <label class="field-label">Select Status <span class="req">*</span></label>
                <div class="status-pills">
                    @foreach($statuses as $sName => [$bg, $fg, $icon])
                    <label class="status-pill" style="background:{{ $bg }};color:{{ $fg }};">
                        <input type="radio" name="status" value="{{ $sName }}"
                               {{ $selStatus === $sName ? 'checked' : '' }} required>
                        {{ $icon }} {{ $sName }}
                    </label>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Section 4: Description --}}
        <div class="form-section">
            <div class="form-section-header">
                <div class="form-section-icon" style="background:#faf5ff;">📝</div>
                <div>
                    <div class="form-section-title">Description & Objectives</div>
                    <div class="form-section-sub">Outline training goals, agenda, or special notes</div>
                </div>
            </div>
            <div class="form-section-body">
                <label class="field-label">Description</label>
                <textarea name="description" class="field-input" rows="5"
                          placeholder="Describe the training objectives, agenda, prerequisites, or any special instructions…">{{ old('description', $session->description ?? '') }}</textarea>
            </div>
        </div>

        {{-- Action bar --}}
        <div class="form-actions">
            <div style="font-size:12.5px;color:#9ca3af;"><span style="color:#ef4444;">*</span> Required fields</div>
            <div class="form-actions-right">
                <a href="{{ route('corporate.sessions.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    {{ isset($session) ? '✓ Save Changes' : '+ Create Session' }}
                </button>
            </div>
        </div>
    </form>
</div>

<script>
// Highlight selected status pill border
document.querySelectorAll('.status-pill input').forEach(radio => {
    const pill = radio.closest('.status-pill');
    if (radio.checked) pill.style.outline = '2px solid currentColor';
    radio.addEventListener('change', () => {
        document.querySelectorAll('.status-pill').forEach(p => p.style.outline = 'none');
        if (radio.checked) pill.style.outline = '2px solid currentColor';
    });
});
</script>
@endsection
