@extends('layouts.app')
@section('page-title', 'Edit Question Set')
@section('content')

<x-page-header title="Edit Question Set" desc="Update the test settings." />

<style>
.qs-form-card{background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:32px;max-width:800px;margin:0 auto;box-shadow:0 1px 8px rgba(0,0,0,.06);}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:18px;}
.fg{display:flex;flex-direction:column;gap:5px;}
.fg.full{grid-column:1/-1;}
.fg label{font-size:12px;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.04em;}
.fg input,.fg select,.fg textarea{border:1px solid #cbd5e1;border-radius:8px;padding:9px 13px;font-size:13px;color:#334155;}
.section-title{font-size:12px;font-weight:800;color:#1e3a8a;text-transform:uppercase;letter-spacing:.06em;margin:24px 0 14px;padding-bottom:6px;border-bottom:2px solid #e2e8f0;}
.toggle-row{display:flex;align-items:center;justify-content:space-between;padding:12px 16px;background:#f8fafc;border-radius:8px;margin-bottom:10px;}
.toggle-label{font-size:13px;font-weight:600;color:#374151;}
.toggle-sub{font-size:12px;color:#64748b;margin-top:2px;}
.actions{margin-top:28px;display:flex;gap:12px;justify-content:space-between;border-top:1px solid #e2e8f0;padding-top:20px;flex-wrap:wrap;}
.btn-primary{background:#1e3a8a;color:#fff;border:none;border-radius:8px;padding:11px 26px;font-size:13px;font-weight:700;cursor:pointer;}
.btn-cancel{background:#f1f5f9;color:#475569;border:1px solid #e2e8f0;border-radius:8px;padding:11px 20px;font-size:13px;font-weight:600;text-decoration:none;}
.btn-questions{background:#fffbeb;color:#92400e;border:1px solid #fde68a;border-radius:8px;padding:11px 20px;font-size:13px;font-weight:700;text-decoration:none;}
</style>

<div class="qs-form-card">
    @if($errors->any())
    <div style="background:#fef2f2;border:1px solid #fecaca;color:#b91c1c;border-radius:8px;padding:14px;margin-bottom:20px;font-size:13px;">
        @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
    </div>
    @endif

    <form method="POST" action="/admin/question-sets/update/{{ $questionSet->id }}">
        @csrf

        <div class="section-title">Basic Information</div>
        <div class="form-grid">
            <div class="fg full">
                <label>Question Set Title *</label>
                <input type="text" name="title" value="{{ old('title', $questionSet->title) }}" required>
            </div>
            <div class="fg full">
                <label>Description / Instructions</label>
                <textarea name="description" rows="3">{{ old('description', $questionSet->description) }}</textarea>
            </div>
            <div class="fg">
                <label>Related Course (Optional)</label>
                <select name="course_id">
                    <option value="">— Not linked —</option>
                    @foreach($courses as $course)
                    <option value="{{ $course->id }}" {{ old('course_id', $questionSet->course_id) == $course->id ? 'selected' : '' }}>{{ $course->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="fg">
                <label>Status</label>
                <select name="status">
                    <option value="Active" {{ old('status', $questionSet->status)=='Active' ? 'selected':'' }}>Active</option>
                    <option value="Inactive" {{ old('status', $questionSet->status)=='Inactive' ? 'selected':'' }}>Inactive</option>
                </select>
            </div>
        </div>

        <div class="section-title">Scoring & Pass Rules</div>
        <div class="form-grid">
            <div class="fg">
                <label>Total Marks *</label>
                <input type="number" name="total_marks" value="{{ old('total_marks', $questionSet->total_marks) }}" min="1" required>
            </div>
            <div class="fg">
                <label>Pass Mark (absolute)</label>
                <input type="number" name="pass_mark" value="{{ old('pass_mark', $questionSet->pass_mark) }}" min="1">
            </div>
            <div class="fg">
                <label>Pass Percentage (%)</label>
                <input type="number" name="pass_percentage" value="{{ old('pass_percentage', $questionSet->pass_percentage) }}" min="1" max="100">
            </div>
            <div class="fg">
                <label>Allowed Attempts *</label>
                <input type="number" name="allowed_attempts" value="{{ old('allowed_attempts', $questionSet->allowed_attempts) }}" min="1" max="10" required>
            </div>
            <div class="fg">
                <label>Time Limit (minutes)</label>
                <input type="number" name="time_limit_minutes" value="{{ old('time_limit_minutes', $questionSet->time_limit_minutes) }}" min="1" max="600" placeholder="No limit">
            </div>
        </div>

        <div class="section-title">Options</div>

        <div class="toggle-row">
            <div>
                <div class="toggle-label">Show result to participant</div>
                <div class="toggle-sub">Participants can see their score after submitting.</div>
            </div>
            <input type="hidden" name="show_result_to_participant" value="0">
            <input type="checkbox" name="show_result_to_participant" value="1"
                   {{ old('show_result_to_participant', $questionSet->show_result_to_participant) ? 'checked' : '' }}
                   style="width:18px;height:18px;cursor:pointer;">
        </div>

        <div class="toggle-row">
            <div>
                <div class="toggle-label">Allow certificate after passing</div>
                <div class="toggle-sub">Certificate eligibility granted when participant passes.</div>
            </div>
            <input type="hidden" name="allow_certificate_after_pass" value="0">
            <input type="checkbox" name="allow_certificate_after_pass" value="1"
                   {{ old('allow_certificate_after_pass', $questionSet->allow_certificate_after_pass) ? 'checked' : '' }}
                   style="width:18px;height:18px;cursor:pointer;">
        </div>

        <div class="actions">
            <a href="/admin/question-sets/{{ $questionSet->id }}/questions" class="btn-questions">✏️ Manage Questions</a>
            <div style="display:flex;gap:10px;">
                <a href="/admin/question-sets" class="btn-cancel">Cancel</a>
                <button type="submit" class="btn-primary">💾 Save Changes</button>
            </div>
        </div>
    </form>
</div>

@endsection
