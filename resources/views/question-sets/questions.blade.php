@extends('layouts.app')
@section('page-title', 'Manage Questions – ' . $questionSet->title)
@section('content')

<x-page-header title="Question Builder" desc="{{ $questionSet->title }}" />

<style>
.qb-layout{display:grid;grid-template-columns:1fr 360px;gap:20px;align-items:flex-start;}
.qb-card{background:#fff;border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;box-shadow:0 1px 6px rgba(0,0,0,.05);}
.qb-card-header{padding:16px 20px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;}
.qb-card-title{font-size:14px;font-weight:800;color:#1e293b;}
.qb-card-body{padding:20px;}
.q-item{border:1px solid #e2e8f0;border-radius:10px;padding:16px;margin-bottom:12px;background:#fafafa;position:relative;}
.q-item:hover{border-color:#bfdbfe;background:#f0f9ff;}
.q-num{display:inline-flex;align-items:center;justify-content:center;width:22px;height:22px;background:#1e3a8a;color:#fff;border-radius:50%;font-size:11px;font-weight:800;margin-right:8px;flex-shrink:0;}
.q-type-badge{display:inline-flex;align-items:center;padding:2px 8px;border-radius:10px;font-size:11px;font-weight:700;background:#eff6ff;color:#1d4ed8;margin-left:6px;}
.q-text{font-size:13px;font-weight:600;color:#1e293b;margin:6px 0;}
.q-options{font-size:12px;color:#64748b;margin-top:6px;padding-left:16px;}
.q-option-item{margin-bottom:2px;}
.q-option-item.correct{color:#16a34a;font-weight:700;}
.q-actions{position:absolute;top:12px;right:12px;display:flex;gap:6px;}
.action-btn{display:inline-flex;align-items:center;gap:3px;padding:4px 8px;border-radius:6px;font-size:11px;font-weight:600;text-decoration:none;border:none;cursor:pointer;}
.action-btn-amber{background:#fffbeb;color:#92400e;}
.action-btn-red{background:#fef2f2;color:#dc2626;}
.marks-badge{display:inline-flex;align-items:center;padding:2px 8px;border-radius:10px;font-size:11px;font-weight:700;background:#f0fdf4;color:#166534;margin-left:6px;}
.req-badge{display:inline-flex;align-items:center;padding:2px 8px;border-radius:10px;font-size:11px;font-weight:700;background:#fef2f2;color:#dc2626;margin-left:4px;}
/* Form styles */
.fg{display:flex;flex-direction:column;gap:5px;margin-bottom:14px;}
.fg label{font-size:11px;font-weight:800;color:#475569;text-transform:uppercase;letter-spacing:.04em;}
.fg input,.fg select,.fg textarea{border:1px solid #cbd5e1;border-radius:7px;padding:8px 11px;font-size:13px;color:#334155;width:100%;box-sizing:border-box;}
.fg input:focus,.fg select:focus,.fg textarea:focus{border-color:#1e3a8a;outline:none;}
.options-section{border:1px solid #e2e8f0;border-radius:8px;padding:12px;background:#f8fafc;margin-bottom:14px;}
.option-row{display:flex;align-items:center;gap:8px;margin-bottom:8px;}
.option-row input[type=text]{flex:1;}
.option-row input[type=checkbox]{width:16px;height:16px;flex-shrink:0;}
.btn-add-option{background:#eff6ff;color:#1d4ed8;border:1px dashed #bfdbfe;border-radius:6px;padding:6px 12px;font-size:12px;font-weight:700;cursor:pointer;width:100%;}
.btn-submit{background:#1e3a8a;color:#fff;border:none;border-radius:8px;padding:10px 18px;font-size:13px;font-weight:700;cursor:pointer;width:100%;}
.info-panel{background:linear-gradient(135deg,#f0f9ff,#dbeafe);border:1px solid #bfdbfe;border-radius:10px;padding:16px;margin-bottom:16px;}
.info-row{display:flex;justify-content:space-between;align-items:center;font-size:13px;margin-bottom:6px;}
.info-label{color:#64748b;font-weight:600;}
.info-val{font-weight:800;color:#1e293b;}
</style>

@if(session('success'))
<div style="background:#f0fdf4;border:1px solid #86efac;color:#166534;border-radius:8px;padding:12px 18px;margin-bottom:16px;font-size:13px;">✅ {{ session('success') }}</div>
@endif

<div style="margin-bottom:14px;display:flex;gap:10px;flex-wrap:wrap;">
    <a href="/admin/question-sets" style="color:#64748b;font-size:13px;text-decoration:none;">← Back to Question Sets</a>
    <span style="color:#cbd5e1;">|</span>
    <a href="/admin/question-sets/edit/{{ $questionSet->id }}" style="color:#1d4ed8;font-size:13px;text-decoration:none;">⚙️ Edit Settings</a>
</div>

<div class="qb-layout">
    {{-- LEFT: existing questions --}}
    <div>
        <div class="qb-card">
            <div class="qb-card-header">
                <div class="qb-card-title">📋 Questions ({{ $questionSet->questions->count() }})</div>
                <div style="font-size:12px;color:#64748b;">Total: {{ $questionSet->total_marks }} marks · Pass: {{ $questionSet->effectivePassMark() }} marks</div>
            </div>
            <div class="qb-card-body">
                @if($questionSet->questions->isEmpty())
                <div style="text-align:center;padding:32px;color:#94a3b8;">
                    <div style="font-size:32px;margin-bottom:8px;">📝</div>
                    <div style="font-size:14px;font-weight:700;color:#374151;">No questions yet</div>
                    <div style="font-size:13px;margin-top:4px;">Use the form on the right to add your first question.</div>
                </div>
                @else
                @foreach($questionSet->questions as $idx => $q)
                <div class="q-item">
                    <div style="display:flex;align-items:flex-start;gap:4px;flex-wrap:wrap;padding-right:80px;">
                        <span class="q-num">{{ $idx + 1 }}</span>
                        <span class="q-type-badge">{{ $types[$q->question_type] ?? $q->question_type }}</span>
                        @if($q->marks > 0)<span class="marks-badge">{{ $q->marks }} marks</span>@endif
                        @if($q->is_required)<span class="req-badge">Required</span>@endif
                        @if($q->manual_review_required)<span style="background:#fef3c7;color:#92400e;font-size:11px;font-weight:700;padding:2px 7px;border-radius:10px;margin-left:4px;">Manual Review</span>@endif
                    </div>
                    <div class="q-text">{{ $q->question_text }}</div>
                    @if($q->options->isNotEmpty())
                    <div class="q-options">
                        @foreach($q->options as $opt)
                        <div class="q-option-item {{ $opt->is_correct ? 'correct' : '' }}">
                            {{ $opt->is_correct ? '✔' : '○' }} {{ $opt->option_text }}
                        </div>
                        @endforeach
                    </div>
                    @endif
                    <div class="q-actions">
                        <a href="/admin/question-sets/{{ $questionSet->id }}/questions/edit/{{ $q->id }}" class="action-btn action-btn-amber">✏️</a>
                        <a href="/admin/question-sets/{{ $questionSet->id }}/questions/delete/{{ $q->id }}"
                           class="action-btn action-btn-red"
                           onclick="return confirm('Delete this question?')">🗑️</a>
                    </div>
                </div>
                @endforeach
                @endif
            </div>
        </div>
    </div>

    {{-- RIGHT: add question form --}}
    <div>
        <div class="qb-card">
            <div class="qb-card-header">
                <div class="qb-card-title">+ Add Question</div>
            </div>
            <div class="qb-card-body">
                <div class="info-panel">
                    <div class="info-row"><span class="info-label">Question Set</span><span class="info-val" style="font-size:12px;max-width:160px;text-align:right;">{{ Str::limit($questionSet->title,30) }}</span></div>
                    <div class="info-row"><span class="info-label">Total Marks</span><span class="info-val">{{ $questionSet->total_marks }}</span></div>
                    <div class="info-row"><span class="info-label">Pass Mark</span><span class="info-val">{{ $questionSet->effectivePassMark() }}</span></div>
                    <div class="info-row"><span class="info-label">Questions</span><span class="info-val">{{ $questionSet->questions->count() }}</span></div>
                </div>

                <form method="POST" action="/admin/question-sets/{{ $questionSet->id }}/questions/store" id="addQuestionForm">
                    @csrf

                    <div class="fg">
                        <label>Question Type *</label>
                        <select name="question_type" id="questionType" onchange="updateTypeUI()">
                            @foreach($types as $key => $label)
                            <option value="{{ $key }}" {{ old('question_type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="fg">
                        <label>Question Text *</label>
                        <textarea name="question_text" rows="3" required placeholder="Enter your question here…">{{ old('question_text') }}</textarea>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                        <div class="fg">
                            <label>Marks</label>
                            <input type="number" name="marks" value="{{ old('marks', 5) }}" min="0">
                        </div>
                        <div class="fg">
                            <label>Sort Order</label>
                            <input type="number" name="sort_order" value="{{ old('sort_order', $questionSet->questions->count()) }}" min="0">
                        </div>
                    </div>

                    {{-- True/False correct answer --}}
                    <div class="fg" id="trueFalseSection" style="display:none;">
                        <label>Correct Answer</label>
                        <select name="correct_answer">
                            <option value="true">True</option>
                            <option value="false">False</option>
                        </select>
                    </div>

                    {{-- MCQ options --}}
                    <div id="mcqOptionsSection" style="display:none;">
                        <div class="options-section">
                            <div style="font-size:11px;font-weight:800;color:#475569;text-transform:uppercase;letter-spacing:.04em;margin-bottom:8px;">
                                Options <span style="color:#64748b;text-transform:none;font-weight:400;">(tick correct)</span>
                            </div>
                            <div id="optionsList">
                                <div class="option-row">
                                    <input type="checkbox" name="options[0][is_correct]" value="1">
                                    <input type="text" name="options[0][text]" placeholder="Option A">
                                </div>
                                <div class="option-row">
                                    <input type="checkbox" name="options[1][is_correct]" value="1">
                                    <input type="text" name="options[1][text]" placeholder="Option B">
                                </div>
                            </div>
                            <button type="button" class="btn-add-option" onclick="addOption()">+ Add Option</button>
                        </div>
                    </div>

                    {{-- Short answer correct --}}
                    <div class="fg" id="shortAnswerSection" style="display:none;">
                        <label>Correct Answer (for auto-grading)</label>
                        <input type="text" name="correct_answer" value="{{ old('correct_answer') }}" placeholder="Leave blank if manual review">
                        <label style="display:flex;align-items:center;gap:6px;margin-top:4px;text-transform:none;letter-spacing:0;">
                            <input type="checkbox" name="exact_match_required" value="1"> Require exact match
                        </label>
                    </div>

                    {{-- Date correct --}}
                    <div class="fg" id="dateSection" style="display:none;">
                        <label>Correct Date (for auto-grading)</label>
                        <input type="text" name="correct_answer" value="{{ old('correct_answer') }}" placeholder="YYYY-MM-DD or leave blank">
                    </div>

                    <div style="display:flex;gap:10px;margin-bottom:14px;">
                        <label style="display:flex;align-items:center;gap:6px;font-size:12px;font-weight:600;color:#374151;cursor:pointer;">
                            <input type="checkbox" name="is_required" value="1" checked> Required
                        </label>
                        <label style="display:flex;align-items:center;gap:6px;font-size:12px;font-weight:600;color:#374151;cursor:pointer;">
                            <input type="checkbox" name="manual_review_required" value="1"> Manual Review
                        </label>
                    </div>

                    <button type="submit" class="btn-submit">➕ Add Question</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let optionIndex = 2;

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
                     <input type="text" name="options[${optionIndex}][text]" placeholder="Option ${String.fromCharCode(65+optionIndex)}">
                     <button type="button" onclick="this.parentElement.remove()" style="background:#fef2f2;color:#dc2626;border:none;border-radius:5px;padding:4px 8px;cursor:pointer;font-size:12px;">✕</button>`;
    list.appendChild(div);
    optionIndex++;
}

// Init on load
updateTypeUI();
</script>

@endsection
