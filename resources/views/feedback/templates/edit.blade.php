@extends('layouts.app')
@section('page-title', 'Edit: ' . $template->name)
@section('content')
<style>
.fg { margin-bottom:16px; }
.fg label { display:block; font-weight:600; font-size:13px; color:#374151; margin-bottom:5px; }
.fg input, .fg select, .fg textarea { width:100%; padding:9px 12px; border:1.5px solid #d1d5db; border-radius:7px; font-size:14px; font-family:inherit; outline:none; box-sizing:border-box; }
.fg input:focus, .fg select:focus { border-color:#1e3a8a; }
.q-card { background:#f8fafc; border:1.5px solid #e5e7eb; border-radius:10px; padding:16px; margin-bottom:12px; }
.q-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; }
</style>
<div style="max-width:860px; margin:auto;">
    <div style="margin-bottom:16px;">
        <a href="{{ route('feedback.templates.show', $template) }}" style="color:#6b7280; font-size:13px; text-decoration:none;">← Back to {{ $template->name }}</a>
    </div>
    <div style="background:#fff; border-radius:14px; padding:28px; box-shadow:0 2px 12px rgba(0,0,0,.07);">
        <h2 style="font-size:20px; font-weight:800; color:#111827; margin:0 0 22px;">Edit Template</h2>

        @if($errors->any())
        <div style="background:#fee2e2; border-radius:8px; padding:12px 16px; margin-bottom:18px;">
            @foreach($errors->all() as $e)<div style="font-size:13px; color:#b91c1c;">• {{ $e }}</div>@endforeach
        </div>
        @endif

        <form method="POST" action="{{ route('feedback.templates.update', $template) }}">
            @csrf @method('PUT')

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                <div class="fg" style="grid-column:1/-1;">
                    <label>Template Name *</label>
                    <input type="text" name="name" value="{{ old('name', $template->name) }}" required>
                </div>
                <div class="fg">
                    <label>Training Type *</label>
                    <select name="type">
                        @foreach($types as $val => $label)
                        <option value="{{ $val }}" {{ old('type', $template->type) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="fg">
                    <label>Description</label>
                    <input type="text" name="description" value="{{ old('description', $template->description) }}">
                </div>
            </div>

            <div style="display:flex; gap:24px; flex-wrap:wrap; padding:14px 16px; background:#f8fafc; border-radius:8px; margin-bottom:22px;">
                <label style="display:flex; align-items:center; gap:8px; font-size:13px; font-weight:600; color:#374151; cursor:pointer;">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $template->is_active) ? 'checked' : '' }}> Active
                </label>
                <label style="display:flex; align-items:center; gap:8px; font-size:13px; font-weight:600; color:#374151; cursor:pointer;">
                    <input type="checkbox" name="require_for_certificate" value="1" {{ old('require_for_certificate', $template->require_for_certificate) ? 'checked' : '' }}> Required for Certificate
                </label>
                <label style="display:flex; align-items:center; gap:8px; font-size:13px; font-weight:600; color:#374151; cursor:pointer;">
                    <input type="checkbox" name="allow_multiple" value="1" {{ old('allow_multiple', $template->allow_multiple) ? 'checked' : '' }}> Allow Multiple Submissions
                </label>
            </div>

            <div style="margin-bottom:14px; display:flex; justify-content:space-between; align-items:center;">
                <div style="font-size:15px; font-weight:700; color:#111827;">Questions</div>
                <button type="button" onclick="addQuestion()" style="background:#e0e7ff; color:#3730a3; border:none; border-radius:7px; padding:7px 16px; font-size:13px; font-weight:700; cursor:pointer;">+ Add Question</button>
            </div>

            <div id="questionsContainer">
                @foreach($template->questions as $i => $q)
                <div class="q-card" id="qcard-{{ $i }}">
                    <div class="q-header">
                        <span style="font-size:12px; font-weight:700; color:#6b7280;">Question {{ $i + 1 }}</span>
                        <button type="button" onclick="removeQuestion({{ $i }})" style="background:#fee2e2; color:#b91c1c; border:none; border-radius:5px; padding:3px 10px; font-size:12px; cursor:pointer;">Remove</button>
                    </div>
                    <div style="display:grid; grid-template-columns:2fr 1fr 1fr; gap:10px; margin-bottom:10px;">
                        <input type="text" name="questions[{{ $i }}][question_text]" value="{{ $q->question_text }}" required style="padding:8px 10px; border:1.5px solid #d1d5db; border-radius:6px; font-size:13px;">
                        <select name="questions[{{ $i }}][question_type]" style="padding:8px 10px; border:1.5px solid #d1d5db; border-radius:6px; font-size:13px;">
                            @foreach($qTypes as $val => $label)
                            <option value="{{ $val }}" {{ $q->question_type === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        <select name="questions[{{ $i }}][category]" style="padding:8px 10px; border:1.5px solid #d1d5db; border-radius:6px; font-size:13px;">
                            @foreach($categories as $val => $label)
                            <option value="{{ $val }}" {{ $q->category === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <label style="display:flex; align-items:center; gap:6px; font-size:12px; color:#374151; cursor:pointer;">
                        <input type="hidden" name="questions[{{ $i }}][is_required]" value="0">
                        <input type="checkbox" name="questions[{{ $i }}][is_required]" value="1" {{ $q->is_required ? 'checked' : '' }}> Required
                    </label>
                </div>
                @endforeach
            </div>

            <div style="margin-top:24px; display:flex; gap:10px;">
                <button type="submit" style="background:linear-gradient(135deg,#1e3a8a,#2563eb); color:#fff; padding:12px 28px; border:none; border-radius:9px; font-weight:800; font-size:14px; cursor:pointer;">Save Changes</button>
                <a href="{{ route('feedback.templates.show', $template) }}" style="background:#f1f5f9; color:#374151; padding:12px 20px; border-radius:9px; font-size:14px; font-weight:600; text-decoration:none;">Cancel</a>
            </div>
        </form>
    </div>
</div>
<script>
let qCount = {{ $template->questions->count() }};
const qTypes = @json($qTypes);
const categories = @json($categories);
function addQuestion() {
    const i = qCount++;
    const card = document.createElement('div');
    card.className = 'q-card'; card.id = 'qcard-' + i;
    const typeOptions = Object.entries(qTypes).map(([v,l]) => `<option value="${v}">${l}</option>`).join('');
    const catOptions  = Object.entries(categories).map(([v,l]) => `<option value="${v}">${l}</option>`).join('');
    card.innerHTML = `<div class="q-header"><span style="font-size:12px;font-weight:700;color:#6b7280;">Question ${i+1}</span><button type="button" onclick="removeQuestion(${i})" style="background:#fee2e2;color:#b91c1c;border:none;border-radius:5px;padding:3px 10px;font-size:12px;cursor:pointer;">Remove</button></div>
    <div style="display:grid;grid-template-columns:2fr 1fr 1fr;gap:10px;margin-bottom:10px;">
        <input type="text" name="questions[${i}][question_text]" placeholder="Question text" required style="padding:8px 10px;border:1.5px solid #d1d5db;border-radius:6px;font-size:13px;">
        <select name="questions[${i}][question_type]" style="padding:8px 10px;border:1.5px solid #d1d5db;border-radius:6px;font-size:13px;">${typeOptions}</select>
        <select name="questions[${i}][category]" style="padding:8px 10px;border:1.5px solid #d1d5db;border-radius:6px;font-size:13px;">${catOptions}</select>
    </div>
    <label style="display:flex;align-items:center;gap:6px;font-size:12px;color:#374151;cursor:pointer;"><input type="hidden" name="questions[${i}][is_required]" value="0"><input type="checkbox" name="questions[${i}][is_required]" value="1" checked> Required</label>`;
    document.getElementById('questionsContainer').appendChild(card);
}
function removeQuestion(i) { const el = document.getElementById('qcard-'+i); if(el) el.remove(); }
</script>
@endsection
