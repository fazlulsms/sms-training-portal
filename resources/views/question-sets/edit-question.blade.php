@extends('layouts.app')
@section('page-title', 'Edit Question')
@section('content')

<x-page-header title="Edit Question" desc="{{ $questionSet->title }}" />

<style>
.eq-card{background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:28px;max-width:700px;margin:0 auto;box-shadow:0 1px 8px rgba(0,0,0,.06);}
.fg{display:flex;flex-direction:column;gap:5px;margin-bottom:16px;}
.fg label{font-size:11px;font-weight:800;color:#475569;text-transform:uppercase;letter-spacing:.04em;}
.fg input,.fg select,.fg textarea{border:1px solid #cbd5e1;border-radius:7px;padding:9px 12px;font-size:13px;color:#334155;}
.options-section{border:1px solid #e2e8f0;border-radius:8px;padding:12px;background:#f8fafc;margin-bottom:16px;}
.option-row{display:flex;align-items:center;gap:8px;margin-bottom:8px;}
.option-row input[type=text]{flex:1;}
.btn-add-option{background:#eff6ff;color:#1d4ed8;border:1px dashed #bfdbfe;border-radius:6px;padding:6px 12px;font-size:12px;font-weight:700;cursor:pointer;width:100%;}
.actions{display:flex;gap:12px;justify-content:flex-end;border-top:1px solid #e2e8f0;padding-top:20px;margin-top:20px;}
.btn-primary{background:#1e3a8a;color:#fff;border:none;border-radius:8px;padding:10px 24px;font-size:13px;font-weight:700;cursor:pointer;}
.btn-cancel{background:#f1f5f9;color:#475569;border:1px solid #e2e8f0;border-radius:8px;padding:10px 18px;font-size:13px;font-weight:600;text-decoration:none;}
</style>

<a href="/admin/question-sets/{{ $questionSet->id }}/questions" style="display:inline-flex;align-items:center;gap:5px;color:#64748b;font-size:13px;text-decoration:none;margin-bottom:14px;">← Back to Questions</a>

<div class="eq-card">
    @if($errors->any())
    <div style="background:#fef2f2;border:1px solid #fecaca;color:#b91c1c;border-radius:8px;padding:14px;margin-bottom:20px;font-size:13px;">
        @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
    </div>
    @endif

    <form method="POST" action="/admin/question-sets/{{ $questionSet->id }}/questions/update/{{ $question->id }}">
        @csrf

        <div class="fg">
            <label>Question Type *</label>
            <select name="question_type" id="questionType" onchange="updateTypeUI()">
                @foreach($types as $key => $label)
                <option value="{{ $key }}" {{ old('question_type', $question->question_type) == $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="fg">
            <label>Question Text *</label>
            <textarea name="question_text" rows="4" required>{{ old('question_text', $question->question_text) }}</textarea>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            <div class="fg">
                <label>Marks</label>
                <input type="number" name="marks" value="{{ old('marks', $question->marks) }}" min="0">
            </div>
            <div class="fg">
                <label>Sort Order</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $question->sort_order) }}" min="0">
            </div>
        </div>

        {{-- True/False correct answer --}}
        <div class="fg" id="trueFalseSection" style="display:none;">
            <label>Correct Answer</label>
            <select name="correct_answer" id="tfCorrect">
                <option value="true"  {{ old('correct_answer', ($question->options->where('is_correct',true)->first()?->option_text == 'True' ? 'true' : 'false')) == 'true' ? 'selected':'' }}>True</option>
                <option value="false" {{ old('correct_answer', ($question->options->where('is_correct',true)->first()?->option_text == 'False' ? 'false' : 'true')) == 'false' ? 'selected':'' }}>False</option>
            </select>
        </div>

        {{-- MCQ options --}}
        <div id="mcqOptionsSection" style="display:none;">
            <div class="options-section">
                <div style="font-size:11px;font-weight:800;color:#475569;text-transform:uppercase;letter-spacing:.04em;margin-bottom:8px;">Options (tick correct)</div>
                <div id="optionsList">
                    @foreach($question->options as $idx => $opt)
                    <div class="option-row">
                        <input type="checkbox" name="options[{{ $idx }}][is_correct]" value="1" {{ $opt->is_correct ? 'checked' : '' }}>
                        <input type="text" name="options[{{ $idx }}][text]" value="{{ $opt->option_text }}" placeholder="Option">
                        <button type="button" onclick="this.parentElement.remove()" style="background:#fef2f2;color:#dc2626;border:none;border-radius:5px;padding:4px 8px;cursor:pointer;font-size:12px;">✕</button>
                    </div>
                    @endforeach
                </div>
                <button type="button" class="btn-add-option" onclick="addOption()">+ Add Option</button>
            </div>
        </div>

        {{-- Short answer correct --}}
        <div class="fg" id="shortAnswerSection" style="display:none;">
            <label>Correct Answer (for auto-grading)</label>
            <input type="text" name="correct_answer" value="{{ old('correct_answer', $question->correct_answer) }}" placeholder="Leave blank if manual review">
            <label style="display:flex;align-items:center;gap:6px;margin-top:4px;text-transform:none;letter-spacing:0;font-size:12px;font-weight:600;color:#374151;cursor:pointer;">
                <input type="checkbox" name="exact_match_required" value="1" {{ $question->exact_match_required ? 'checked':'' }}> Require exact match
            </label>
        </div>

        {{-- Date correct --}}
        <div class="fg" id="dateSection" style="display:none;">
            <label>Correct Date</label>
            <input type="text" name="correct_answer" value="{{ old('correct_answer', $question->correct_answer) }}" placeholder="YYYY-MM-DD">
        </div>

        <div style="display:flex;gap:16px;margin-bottom:14px;">
            <label style="display:flex;align-items:center;gap:6px;font-size:13px;font-weight:600;color:#374151;cursor:pointer;">
                <input type="checkbox" name="is_required" value="1" {{ old('is_required', $question->is_required) ? 'checked':'' }}>
                Required
            </label>
            <label style="display:flex;align-items:center;gap:6px;font-size:13px;font-weight:600;color:#374151;cursor:pointer;">
                <input type="checkbox" name="manual_review_required" value="1" {{ old('manual_review_required', $question->manual_review_required) ? 'checked':'' }}>
                Manual Review Required
            </label>
        </div>

        <div class="actions">
            <a href="/admin/question-sets/{{ $questionSet->id }}/questions" class="btn-cancel">Cancel</a>
            <button type="submit" class="btn-primary">💾 Save Question</button>
        </div>
    </form>
</div>

<script>
let optionIndex = {{ $question->options->count() }};

function updateTypeUI() {
    const type = document.getElementById('questionType').value;
    document.getElementById('trueFalseSection').style.display  = type === 'true_false'   ? 'block' : 'none';
    document.getElementById('mcqOptionsSection').style.display = (type === 'mcq_single' || type === 'mcq_multiple') ? 'block' : 'none';
    document.getElementById('shortAnswerSection').style.display= type === 'short_answer' ? 'block' : 'none';
    document.getElementById('dateSection').style.display       = type === 'date'          ? 'block' : 'none';
}

function addOption() {
    const list = document.getElementById('optionsList');
    const div  = document.createElement('div');
    div.className = 'option-row';
    div.innerHTML = `<input type="checkbox" name="options[${optionIndex}][is_correct]" value="1">
                     <input type="text" name="options[${optionIndex}][text]" placeholder="Option">
                     <button type="button" onclick="this.parentElement.remove()" style="background:#fef2f2;color:#dc2626;border:none;border-radius:5px;padding:4px 8px;cursor:pointer;font-size:12px;">✕</button>`;
    list.appendChild(div);
    optionIndex++;
}

updateTypeUI();
</script>

@endsection
