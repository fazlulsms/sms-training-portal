@extends('layouts.app')
@section('title', isset($project) ? 'Edit Project' : 'New Corporate Project')
@section('content')

<style>
/* ── Form page layout ───────────────────────── */
.form-page { max-width: 820px; }

/* ── Breadcrumb ─────────────────────────────── */
.breadcrumb { display:flex; align-items:center; gap:6px; font-size:12.5px; color:#9ca3af; margin-bottom:6px; }
.breadcrumb a { color:#6b7280; text-decoration:none; font-weight:600; }
.breadcrumb a:hover { color:#1e3a8a; }
.breadcrumb-sep { color:#d1d5db; }

/* ── Section card ───────────────────────────── */
.form-section {
    background: #fff;
    border: 1px solid #e5e9f0;
    border-radius: 16px;
    overflow: hidden;
    margin-bottom: 16px;
    box-shadow: 0 1px 4px rgba(15,23,42,.04);
}
.form-section-header {
    padding: 16px 24px 14px;
    border-bottom: 1px solid #f0f2f7;
    display: flex;
    align-items: center;
    gap: 10px;
    background: #fafbfd;
}
.form-section-icon {
    width: 34px; height: 34px;
    border-radius: 9px;
    display: flex; align-items: center; justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
}
.form-section-title { font-size: 13.5px; font-weight: 800; color: #111827; letter-spacing: .1px; }
.form-section-sub   { font-size: 12px; color: #9ca3af; margin-top: 1px; }
.form-section-body  { padding: 22px 24px; }

/* ── Field grid ─────────────────────────────── */
.field-grid { display: grid; gap: 18px; }
.col-2 { grid-template-columns: 1fr 1fr; }
.col-1 { grid-template-columns: 1fr; }
.col-span-2 { grid-column: 1 / -1; }

/* ── Form controls ──────────────────────────── */
.field-label {
    display: block;
    font-size: 12px;
    font-weight: 700;
    color: #374151;
    text-transform: uppercase;
    letter-spacing: .5px;
    margin-bottom: 7px;
}
.field-label .req { color: #ef4444; margin-left: 2px; }

.field-input {
    display: block; width: 100%;
    padding: 10px 14px;
    font-size: 14px;
    font-family: inherit;
    color: #111827;
    background: #fff;
    border: 1.5px solid #e5e9f0;
    border-radius: 10px;
    outline: none;
    transition: border-color .15s, box-shadow .15s;
    line-height: 1.4;
}
.field-input:focus {
    border-color: #1e3a8a;
    box-shadow: 0 0 0 3px rgba(30,58,138,.09);
}
.field-input.is-invalid { border-color: #ef4444; }
.field-input.is-invalid:focus { box-shadow: 0 0 0 3px rgba(239,68,68,.10); }
textarea.field-input { resize: vertical; min-height: 80px; }
select.field-input { cursor: pointer; }

.field-error { font-size: 12px; color: #ef4444; margin-top: 5px; font-weight: 600; display: flex; align-items: center; gap: 4px; }

/* status select with color indicator */
.status-opt-Active    { color: #16a34a; }
.status-opt-Completed { color: #2563eb; }
.status-opt-On\ Hold  { color: #d97706; }
.status-opt-Cancelled { color: #dc2626; }

/* ── Action bar ─────────────────────────────── */
.form-actions {
    background: #fff;
    border: 1px solid #e5e9f0;
    border-radius: 16px;
    padding: 18px 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 1px 4px rgba(15,23,42,.04);
}
.form-actions-right { display: flex; gap: 10px; }

/* ── Status pill preview ────────────────────── */
#status-preview {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 4px 12px; border-radius: 20px;
    font-size: 12px; font-weight: 700;
    transition: background .2s, color .2s;
}

@media(max-width:600px) {
    .col-2 { grid-template-columns: 1fr; }
    .col-span-2 { grid-column: auto; }
    .form-actions { flex-direction: column; gap: 12px; align-items: stretch; }
    .form-actions-right { justify-content: flex-end; }
}
</style>

<div class="form-page">

    {{-- Breadcrumb + header --}}
    <div class="breadcrumb">
        <a href="{{ route('corporate.projects.index') }}">Corporate</a>
        <span class="breadcrumb-sep">/</span>
        <a href="{{ route('corporate.projects.index') }}">Projects</a>
        <span class="breadcrumb-sep">/</span>
        <span>{{ isset($project) ? 'Edit' : 'New' }}</span>
    </div>

    <div class="page-header" style="margin-bottom:22px;">
        <div>
            <h1 class="page-title">{{ isset($project) ? 'Edit Project' : 'New Corporate Project' }}</h1>
            <p class="page-subtitle">{{ isset($project) ? 'Update project details and contact information' : 'Create a new factory or corporate training project' }}</p>
        </div>
        <a href="{{ route('corporate.projects.index') }}" class="btn btn-secondary">← Back to Projects</a>
    </div>

    <form method="POST" action="{{ isset($project) ? route('corporate.projects.update', $project) : route('corporate.projects.store') }}">
        @csrf
        @if(isset($project)) @method('PUT') @endif

        {{-- Section 1: Core Info --}}
        <div class="form-section">
            <div class="form-section-header">
                <div class="form-section-icon" style="background:#eff6ff;">🏢</div>
                <div>
                    <div class="form-section-title">Project & Company Information</div>
                    <div class="form-section-sub">Basic identification details for this engagement</div>
                </div>
            </div>
            <div class="form-section-body">
                <div class="field-grid col-1" style="margin-bottom:18px;">
                    <div>
                        <label class="field-label">Project Name <span class="req">*</span></label>
                        <input type="text" name="project_name"
                               class="field-input {{ $errors->has('project_name') ? 'is-invalid' : '' }}"
                               value="{{ old('project_name', $project->project_name ?? '') }}"
                               required placeholder="e.g. Safety Induction Training 2025">
                        @error('project_name')
                        <div class="field-error">⚠ {{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="field-grid col-2">
                    <div>
                        <label class="field-label">Company / Organisation <span class="req">*</span></label>
                        <input type="text" name="company_name"
                               class="field-input {{ $errors->has('company_name') ? 'is-invalid' : '' }}"
                               value="{{ old('company_name', $project->company_name ?? '') }}"
                               required placeholder="e.g. ABC Manufacturing Ltd">
                        @error('company_name')
                        <div class="field-error">⚠ {{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label class="field-label">Project Status <span class="req">*</span></label>
                        @php
                            $statusColors = ['Active'=>['#dcfce7','#16a34a'],'Completed'=>['#dbeafe','#2563eb'],'On Hold'=>['#fff7ed','#d97706'],'Cancelled'=>['#fee2e2','#dc2626']];
                            $selectedStatus = old('status', $project->status ?? 'Active');
                            [$sBg, $sFg] = $statusColors[$selectedStatus] ?? ['#f3f4f6','#6b7280'];
                        @endphp
                        <div style="position:relative;">
                            <select name="status" id="statusSelect" class="field-input" onchange="updateStatus(this)">
                                @foreach(['Active','Completed','On Hold','Cancelled'] as $s)
                                <option value="{{ $s }}" {{ $selectedStatus === $s ? 'selected' : '' }}>{{ $s }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div style="margin-top:8px;">
                            <span id="status-preview" style="background:{{ $sBg }};color:{{ $sFg }};">
                                <span id="status-dot" style="width:7px;height:7px;border-radius:50%;background:{{ $sFg }};display:inline-block;"></span>
                                {{ $selectedStatus }}
                            </span>
                        </div>
                    </div>
                    <div class="col-span-2">
                        <label class="field-label">Office / Site Address</label>
                        <textarea name="address" class="field-input" rows="2"
                                  placeholder="Street address, city, postcode…">{{ old('address', $project->address ?? '') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 2: Contact --}}
        <div class="form-section">
            <div class="form-section-header">
                <div class="form-section-icon" style="background:#f0fdf4;">👤</div>
                <div>
                    <div class="form-section-title">Contact Details</div>
                    <div class="form-section-sub">Primary point of contact for this project</div>
                </div>
            </div>
            <div class="form-section-body">
                <div class="field-grid col-2">
                    <div>
                        <label class="field-label">Contact Person</label>
                        <input type="text" name="contact_person" class="field-input"
                               value="{{ old('contact_person', $project->contact_person ?? '') }}"
                               placeholder="Full name">
                    </div>
                    <div>
                        <label class="field-label">Designation / Title</label>
                        <input type="text" name="contact_designation" class="field-input"
                               value="{{ old('contact_designation', $project->contact_designation ?? '') }}"
                               placeholder="e.g. HR Manager, Training Coordinator">
                    </div>
                    <div>
                        <label class="field-label">Email Address</label>
                        <div style="position:relative;">
                            <span style="position:absolute;left:13px;top:50%;transform:translateY(-50%);color:#9ca3af;font-size:15px;">✉</span>
                            <input type="email" name="email" class="field-input" style="padding-left:34px;"
                                   value="{{ old('email', $project->email ?? '') }}"
                                   placeholder="contact@company.com">
                        </div>
                    </div>
                    <div>
                        <label class="field-label">Phone Number</label>
                        <div style="position:relative;">
                            <span style="position:absolute;left:13px;top:50%;transform:translateY(-50%);color:#9ca3af;font-size:15px;">📞</span>
                            <input type="text" name="phone" class="field-input" style="padding-left:34px;"
                                   value="{{ old('phone', $project->phone ?? '') }}"
                                   placeholder="+60 12-345 6789">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 3: Remarks --}}
        <div class="form-section">
            <div class="form-section-header">
                <div class="form-section-icon" style="background:#fefce8;">📝</div>
                <div>
                    <div class="form-section-title">Notes & Remarks</div>
                    <div class="form-section-sub">Internal notes, scope details, special requirements</div>
                </div>
            </div>
            <div class="form-section-body">
                <label class="field-label">Remarks</label>
                <textarea name="remarks" class="field-input" rows="4"
                          placeholder="Add any internal notes, background, scope of training, or special requirements…">{{ old('remarks', $project->remarks ?? '') }}</textarea>
            </div>
        </div>

        {{-- Action bar --}}
        <div class="form-actions">
            <div style="font-size:12.5px;color:#9ca3af;">
                <span style="color:#ef4444;">*</span> Required fields
            </div>
            <div class="form-actions-right">
                <a href="{{ route('corporate.projects.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    {{ isset($project) ? '✓ Save Changes' : '+ Create Project' }}
                </button>
            </div>
        </div>

    </form>
</div>

<script>
const statusMap = {
    'Active':    ['#dcfce7','#16a34a'],
    'Completed': ['#dbeafe','#2563eb'],
    'On Hold':   ['#fff7ed','#d97706'],
    'Cancelled': ['#fee2e2','#dc2626'],
};
function updateStatus(sel) {
    const [bg, fg] = statusMap[sel.value] || ['#f3f4f6','#6b7280'];
    const preview = document.getElementById('status-preview');
    const dot     = document.getElementById('status-dot');
    preview.style.background = bg;
    preview.style.color = fg;
    dot.style.background = fg;
    preview.lastChild.textContent = sel.value;
}
</script>
@endsection
