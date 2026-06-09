@extends('layouts.app')
@section('title', isset($participant) ? 'Edit Participant' : 'Add Participant')
@section('content')

<style>
.form-page { max-width:680px; }
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
.col-span-2 { grid-column:1/-1; }
.field-label { display:block; font-size:12px; font-weight:700; color:#374151; text-transform:uppercase; letter-spacing:.5px; margin-bottom:7px; }
.field-label .req { color:#ef4444; margin-left:2px; }
.field-input { display:block; width:100%; padding:10px 14px; font-size:14px; font-family:inherit; color:#111827; background:#fff; border:1.5px solid #e5e9f0; border-radius:10px; outline:none; transition:border-color .15s,box-shadow .15s; }
.field-input:focus { border-color:#1e3a8a; box-shadow:0 0 0 3px rgba(30,58,138,.09); }
.field-input.is-invalid { border-color:#ef4444; }
.field-error { font-size:12px; color:#ef4444; margin-top:5px; font-weight:600; }
.field-hint { font-size:12px; color:#9ca3af; margin-top:5px; }
.icon-input-wrap { position:relative; }
.icon-input-wrap .field-icon { position:absolute; left:13px; top:50%; transform:translateY(-50%); color:#9ca3af; font-size:15px; pointer-events:none; }
.icon-input-wrap .field-input { padding-left:34px; }
.form-actions { background:#fff; border:1px solid #e5e9f0; border-radius:16px; padding:18px 24px; display:flex; align-items:center; justify-content:space-between; box-shadow:0 1px 4px rgba(15,23,42,.04); }
.form-actions-right { display:flex; gap:10px; }
@media(max-width:560px){ .col-2{grid-template-columns:1fr;} .col-span-2{grid-column:auto;} }
</style>

<div class="form-page">

    <div class="breadcrumb">
        <a href="{{ route('corporate.sessions.show', $session) }}">{{ $session->course_name }}</a>
        <span class="breadcrumb-sep">/</span>
        <a href="{{ route('corporate.sessions.participants.index', $session) }}">Participants</a>
        <span class="breadcrumb-sep">/</span>
        <span>{{ isset($participant) ? 'Edit' : 'Add' }}</span>
    </div>

    <div class="page-header" style="margin-bottom:22px;">
        <div>
            <h1 class="page-title">{{ isset($participant) ? 'Edit Participant' : 'Add Participant' }}</h1>
            <p class="page-subtitle">{{ $session->course_name }} · {{ $session->project->company_name }}</p>
        </div>
        <a href="{{ route('corporate.sessions.participants.index', $session) }}" class="btn btn-secondary">← Back</a>
    </div>

    <form method="POST" action="{{ isset($participant) ? route('corporate.sessions.participants.update', [$session, $participant]) : route('corporate.sessions.participants.store', $session) }}">
        @csrf
        @if(isset($participant)) @method('PUT') @endif

        {{-- Identity --}}
        <div class="form-section">
            <div class="form-section-header">
                <div class="form-section-icon" style="background:#eff6ff;">👤</div>
                <div>
                    <div class="form-section-title">Participant Identity</div>
                    <div class="form-section-sub">Name and employee identification</div>
                </div>
            </div>
            <div class="form-section-body">
                <div class="field-grid col-2">
                    <div class="col-span-2">
                        <label class="field-label">Full Name <span class="req">*</span></label>
                        <input type="text" name="participant_name"
                               class="field-input {{ $errors->has('participant_name') ? 'is-invalid' : '' }}"
                               value="{{ old('participant_name', $participant->participant_name ?? '') }}"
                               required placeholder="As per IC / official records">
                        @error('participant_name')<div class="field-error">⚠ {{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="field-label">Employee / Staff ID</label>
                        <input type="text" name="employee_id" class="field-input"
                               value="{{ old('employee_id', $participant->employee_id ?? '') }}"
                               placeholder="EMP-001">
                        <div class="field-hint">Used for CSV export and certificate records</div>
                    </div>
                    <div>
                        <label class="field-label">Position / Designation</label>
                        <input type="text" name="position" class="field-input"
                               value="{{ old('position', $participant->position ?? '') }}"
                               placeholder="e.g. Supervisor, Operator">
                    </div>
                    <div class="col-span-2">
                        <label class="field-label">Department / Unit</label>
                        <input type="text" name="department" class="field-input"
                               value="{{ old('department', $participant->department ?? '') }}"
                               placeholder="e.g. Production, Safety & Health, HR">
                    </div>
                </div>
            </div>
        </div>

        {{-- Contact --}}
        <div class="form-section">
            <div class="form-section-header">
                <div class="form-section-icon" style="background:#f0fdf4;">📱</div>
                <div>
                    <div class="form-section-title">Contact Information</div>
                    <div class="form-section-sub">Optional — used for certificate delivery if needed</div>
                </div>
            </div>
            <div class="form-section-body">
                <div class="field-grid col-2">
                    <div>
                        <label class="field-label">Email Address</label>
                        <div class="icon-input-wrap">
                            <span class="field-icon">✉</span>
                            <input type="email" name="email" class="field-input"
                                   value="{{ old('email', $participant->email ?? '') }}"
                                   placeholder="participant@company.com">
                        </div>
                    </div>
                    <div>
                        <label class="field-label">Phone / Mobile</label>
                        <div class="icon-input-wrap">
                            <span class="field-icon">📞</span>
                            <input type="text" name="contact_number" class="field-input"
                                   value="{{ old('contact_number', $participant->contact_number ?? '') }}"
                                   placeholder="+60 12-345 6789">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <div style="font-size:12.5px;color:#9ca3af;"><span style="color:#ef4444;">*</span> Required fields</div>
            <div class="form-actions-right">
                <a href="{{ route('corporate.sessions.participants.index', $session) }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    {{ isset($participant) ? '✓ Save Changes' : '+ Add Participant' }}
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
