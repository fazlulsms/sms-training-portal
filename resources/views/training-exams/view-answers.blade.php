@extends('layouts.app')
@section('page-title', 'View Answers – ' . $attempt->enrollment->full_name)
@section('content')

<x-page-header title="Exam Answers" desc="{{ $attempt->enrollment->full_name }} · Attempt #{{ $attempt->attempt_number }}" />

<style>
.va-card{background:#fff;border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;box-shadow:0 1px 6px rgba(0,0,0,.05);margin-bottom:20px;}
.va-header{padding:16px 20px;border-bottom:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;}
.va-title{font-size:14px;font-weight:800;color:#1e293b;}
.q-block{padding:20px;border-bottom:1px solid #f8fafc;}
.q-block:last-child{border-bottom:none;}
.q-num{display:inline-flex;align-items:center;justify-content:center;width:22px;height:22px;background:#1e3a8a;color:#fff;border-radius:50%;font-size:11px;font-weight:800;margin-right:8px;}
.q-text{font-size:14px;font-weight:600;color:#1e293b;margin:8px 0;}
.answer-box{background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:12px 14px;margin-top:8px;font-size:13px;color:#374151;}
.answer-box.correct{background:#f0fdf4;border-color:#86efac;}
.answer-box.incorrect{background:#fef2f2;border-color:#fca5a5;}
.marks-input{border:1px solid #cbd5e1;border-radius:6px;padding:6px 10px;font-size:13px;width:80px;text-align:center;}
.notes-input{border:1px solid #cbd5e1;border-radius:6px;padding:6px 10px;font-size:13px;width:100%;box-sizing:border-box;margin-top:6px;}
.badge{display:inline-flex;align-items:center;padding:3px 9px;border-radius:20px;font-size:11px;font-weight:700;}
.badge-green{background:#dcfce7;color:#166534;}
.badge-red{background:#fee2e2;color:#991b1b;}
.badge-amber{background:#fffbeb;color:#92400e;}
.badge-gray{background:#f1f5f9;color:#64748b;}
</style>

@php $scheduleId = $attempt->enrollment->training_schedule_id; @endphp
<a href="/admin/training-exams/{{ $scheduleId }}/results" style="display:inline-flex;align-items:center;gap:5px;color:#64748b;font-size:13px;text-decoration:none;margin-bottom:14px;">← Back to Results</a>

{{-- Summary card --}}
<div class="va-card">
    <div class="va-header">
        <div class="va-title">📊 Attempt Summary</div>
        <span class="badge {{ in_array($attempt->status,['passed']) ? 'badge-green' : ($attempt->status==='failed'?'badge-red':($attempt->status==='pending_review'?'badge-amber':'badge-gray')) }}">
            {{ ucfirst(str_replace('_',' ',$attempt->status)) }}
        </span>
    </div>
    <div style="padding:16px 20px;display:flex;gap:20px;flex-wrap:wrap;font-size:13px;">
        <div><strong>Participant:</strong> {{ $attempt->enrollment->full_name }}</div>
        <div><strong>Attempt:</strong> #{{ $attempt->attempt_number }}</div>
        <div><strong>Submitted:</strong> {{ $attempt->submitted_at?->format('d M Y H:i') ?? '—' }}</div>
        @if($attempt->score !== null)
        <div><strong>Score:</strong> {{ $attempt->score }}/{{ $attempt->total_marks }} ({{ number_format($attempt->percentage,1) }}%)</div>
        @endif
        @if($attempt->ip_address)
        <div><strong>IP:</strong> {{ $attempt->ip_address }}</div>
        @endif
    </div>
</div>

{{-- Grade form --}}
<form method="POST" action="/admin/training-exams/grade/{{ $attempt->id }}">
@csrf

<div class="va-card">
    <div class="va-header">
        <div class="va-title">📝 Questions & Answers</div>
        @if($attempt->manual_review_pending || $attempt->status === 'pending_review')
        <span style="background:#fffbeb;color:#92400e;font-size:12px;font-weight:700;padding:5px 12px;border-radius:8px;">⭐ Manual Review Required</span>
        @endif
    </div>

    @foreach($attempt->questionSet->questions as $idx => $question)
    @php
        $answer = $attempt->answers->firstWhere('question_id', $question->id);
        $autoGraded = $answer && !$answer->manual_graded && $answer->is_correct !== null;
    @endphp
    <div class="q-block">
        <div style="display:flex;align-items:flex-start;gap:6px;flex-wrap:wrap;">
            <span class="q-num">{{ $idx + 1 }}</span>
            <span style="background:#eff6ff;color:#1d4ed8;font-size:11px;font-weight:700;padding:2px 7px;border-radius:10px;">{{ \App\Models\Question::TYPES[$question->question_type] ?? $question->question_type }}</span>
            @if($question->marks > 0)<span style="background:#f0fdf4;color:#166534;font-size:11px;font-weight:700;padding:2px 7px;border-radius:10px;">{{ $question->marks }} marks</span>@endif
        </div>
        <div class="q-text">{{ $question->question_text }}</div>

        {{-- Show options for MCQ/T-F --}}
        @if(in_array($question->question_type, ['mcq_single','mcq_multiple','true_false']) && $question->options->isNotEmpty())
        <div style="font-size:12px;color:#64748b;margin-bottom:6px;">
            @foreach($question->options as $opt)
            @php
                $selectedIds = array_map('intval', (array)($answer?->answer_options ?? []));
                $isSelected  = in_array((int)$opt->id, $selectedIds);
            @endphp
            <div style="padding:4px 0;{{ $isSelected ? 'font-weight:700;color:#1e293b;' : '' }}">
                {{ $isSelected ? '☑' : '☐' }} {{ $opt->option_text }}
                @if($opt->is_correct)<span style="color:#16a34a;margin-left:6px;">✔ Correct</span>@endif
            </div>
            @endforeach
        </div>
        @endif

        {{-- Answer display --}}
        @if($answer)
        @php
            $boxClass = $autoGraded ? ($answer->is_correct ? 'correct' : 'incorrect') : '';
        @endphp
        <div class="answer-box {{ $boxClass }}">
            @if($question->question_type === 'file_upload' && $answer->file_path)
                📎 <a href="{{ Storage::url($answer->file_path) }}" target="_blank">View uploaded file</a>
            @elseif($question->question_type === 'declaration')
                {{ $answer->answer_text === 'Yes' ? '✅ Agreed / Declared' : '— Not answered' }}
            @elseif(in_array($question->question_type, ['mcq_single','mcq_multiple','true_false']))
                @php
                    $selectedIds = array_map('intval', (array)($answer->answer_options ?? []));
                    $selectedLabels = $question->options->whereIn('id', $selectedIds)->pluck('option_text')->join(', ');
                @endphp
                {{ $selectedLabels ?: '— Not answered' }}
            @else
                {{ $answer->answer_text ?: '— Not answered' }}
            @endif
            @if($autoGraded)
                <span style="float:right;font-size:11px;font-weight:700;color:{{ $answer->is_correct ? '#166534' : '#dc2626' }};">
                    {{ $answer->is_correct ? '✔ Correct' : '✗ Incorrect' }} · {{ $answer->marks_awarded ?? 0 }}/{{ $question->marks }}
                </span>
            @endif
        </div>
        @else
        <div class="answer-box"><em style="color:#94a3b8;">No answer provided</em></div>
        @endif

        {{-- Manual grading fields for non-auto-gradable or flagged --}}
        @if($answer && (!$question->isAutoGradable() || $answer->manual_graded || $question->manual_review_required))
        <div style="margin-top:10px;display:flex;align-items:flex-start;gap:12px;flex-wrap:wrap;">
            <div>
                <div style="font-size:11px;font-weight:700;color:#64748b;margin-bottom:4px;">MARKS AWARDED</div>
                <input type="number" name="grades[{{ $answer->id }}][marks]"
                       value="{{ $answer->marks_awarded ?? '' }}"
                       min="0" max="{{ $question->marks }}"
                       class="marks-input"
                       placeholder="0">
                <span style="font-size:11px;color:#64748b;margin-left:4px;">/ {{ $question->marks }}</span>
            </div>
            <div style="flex:1;min-width:200px;">
                <div style="font-size:11px;font-weight:700;color:#64748b;margin-bottom:4px;">REVIEWER NOTES</div>
                <input type="text" name="grades[{{ $answer->id }}][notes]"
                       value="{{ $answer->reviewer_notes ?? '' }}"
                       class="notes-input"
                       placeholder="Optional notes…">
            </div>
        </div>
        @endif
    </div>
    @endforeach

    <div style="padding:16px 20px;border-top:1px solid #f1f5f9;display:flex;justify-content:flex-end;gap:10px;">
        <a href="/admin/training-exams/{{ $scheduleId }}/results"
           style="background:#f1f5f9;color:#475569;border:1px solid #e2e8f0;border-radius:8px;padding:10px 20px;font-size:13px;font-weight:600;text-decoration:none;">
            Cancel
        </a>
        @if($attempt->manual_review_pending || $attempt->status === 'pending_review' || $attempt->answers->where('manual_graded', false)->isNotEmpty())
        <button type="submit" style="background:#1e3a8a;color:#fff;border:none;border-radius:8px;padding:10px 24px;font-size:13px;font-weight:700;cursor:pointer;">
            💾 Save Grading & Update Result
        </button>
        @endif
    </div>
</div>

</form>

@endsection
