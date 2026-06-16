@extends('layouts.app')
@section('page-title', 'Preview — ' . $quiz->title)
@section('content')

<x-page-header title="Quiz Preview" desc="{{ $course->name }} → {{ $lesson->title }}">
    <x-slot:actions>
        <a href="{{ route('elearning.quizzes.index', [$course, $lesson]) }}" class="btn btn-ghost btn-sm">← Quizzes</a>
        <a href="{{ route('elearning.quizzes.edit', [$course, $lesson, $quiz]) }}" class="btn btn-edit btn-sm">Edit Quiz</a>
        <a href="{{ route('elearning.quiz-questions.index', [$course, $lesson, $quiz]) }}" class="btn btn-primary btn-sm">Manage Questions</a>
    </x-slot:actions>
</x-page-header>

<x-flash-message />

<style>
.preview-notice {
    background:#fef9c3; border:1.5px solid #fde68a; border-radius:10px;
    padding:12px 18px; margin-bottom:22px; font-size:13px; color:#92400e;
    display:flex; align-items:center; gap:10px;
}
.qcard {
    background:#fff; border:1.5px solid #e5e7eb; border-radius:12px;
    padding:22px 24px; margin-bottom:16px;
}
.qcard-header {
    display:flex; align-items:flex-start; gap:14px; margin-bottom:16px;
}
.qnum {
    background:#1e3a8a; color:#fff; border-radius:50%;
    width:30px; height:30px; min-width:30px;
    display:flex; align-items:center; justify-content:center;
    font-size:13px; font-weight:800; margin-top:2px;
}
.qtext { font-size:14.5px; font-weight:600; color:#1e293b; line-height:1.5; flex:1; }
.qmeta { display:flex; gap:8px; flex-wrap:wrap; margin-top:6px; }
.qbadge {
    font-size:10.5px; font-weight:700; padding:2px 8px; border-radius:20px;
    text-transform:uppercase; letter-spacing:.3px;
}
.qbadge.mcq      { background:#dbeafe; color:#1d4ed8; }
.qbadge.truefalse{ background:#ede9fe; color:#6d28d9; }
.qbadge.scenario { background:#fef3c7; color:#92400e; }
.qbadge.easy     { background:#dcfce7; color:#166534; }
.qbadge.medium   { background:#fef9c3; color:#92400e; }
.qbadge.hard     { background:#fee2e2; color:#991b1b; }
.option-row {
    display:flex; align-items:flex-start; gap:10px;
    padding:8px 12px; border-radius:8px; margin-bottom:6px; font-size:13.5px;
    border:1.5px solid #e5e7eb;
}
.option-row.correct {
    background:#f0fdf4; border-color:#86efac; color:#166534; font-weight:600;
}
.option-row .opt-letter {
    font-weight:800; min-width:20px; color:#64748b;
}
.option-row.correct .opt-letter { color:#15803d; }
.explanation-box {
    background:#eff6ff; border-left:4px solid #3b82f6; border-radius:0 6px 6px 0;
    padding:10px 14px; font-size:13px; color:#1e40af; margin-top:10px;
}
.quiz-meta-bar {
    background:#f8fafc; border:1.5px solid #e2e8f0; border-radius:12px;
    padding:16px 22px; margin-bottom:22px;
    display:grid; grid-template-columns:repeat(auto-fit,minmax(140px,1fr)); gap:12px;
}
.meta-item { }
.meta-label { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#64748b; margin-bottom:3px; }
.meta-value { font-size:14.5px; font-weight:700; color:#1e293b; }
</style>

{{-- Admin-only preview notice --}}
<div class="preview-notice">
    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    <span><strong>Admin Preview Only.</strong> Correct answers are shown. This page does not create any learner attempt records.</span>
</div>

{{-- Assessment type label (Phase 5) --}}
@php
    $isModuleCheck    = str_contains($lesson->title, 'Knowledge Check');
    $isFinalAssessment= str_contains($lesson->title, 'Final Course Assessment');
@endphp
@if($isModuleCheck)
<div style="background:#ede9fe; border-radius:8px; padding:10px 16px; margin-bottom:18px; font-size:13px; color:#4c1d95; font-weight:600;">
    📋 Module Knowledge Check — Scored assessment. Learner must pass (≥{{ $quiz->pass_mark }}%) to complete this module.
</div>
@elseif($isFinalAssessment)
<div style="background:#fef3c7; border-radius:8px; padding:10px 16px; margin-bottom:18px; font-size:13px; color:#78350f; font-weight:600;">
    🏆 Final Course Assessment — Scored final assessment. Required for course completion and certificate eligibility.
</div>
@else
<div style="background:#f0fdf4; border-radius:8px; padding:10px 16px; margin-bottom:18px; font-size:13px; color:#14532d; font-weight:600;">
    📝 Lesson Quiz — Scored assessment attached to this lesson.
</div>
@endif

{{-- Quiz metadata bar --}}
<div class="quiz-meta-bar">
    <div class="meta-item">
        <div class="meta-label">Quiz Title</div>
        <div class="meta-value">{{ $quiz->title }}</div>
    </div>
    <div class="meta-item">
        <div class="meta-label">Questions</div>
        <div class="meta-value">{{ $quiz->questions->count() }}</div>
    </div>
    <div class="meta-item">
        <div class="meta-label">Pass Mark</div>
        <div class="meta-value">{{ $quiz->pass_mark }}%</div>
    </div>
    <div class="meta-item">
        <div class="meta-label">Max Attempts</div>
        <div class="meta-value">{{ $quiz->max_attempt ?: 'Unlimited' }}</div>
    </div>
    <div class="meta-item">
        <div class="meta-label">Status</div>
        <div class="meta-value">{{ ucfirst($quiz->status) }}</div>
    </div>
</div>

{{-- Questions --}}
@forelse($quiz->questions->where('status','active') as $i => $q)
@php
    $qtLabel = match($q->question_type) {
        'truefalse', 'true_false' => 'True / False',
        'scenario'                => 'Scenario MCQ',
        default                   => 'MCQ',
    };
    $diffLabel = ucfirst($q->difficulty ?? 'medium');
    $isTF = in_array($q->question_type, ['truefalse', 'true_false']);
@endphp
<div class="qcard">
    <div class="qcard-header">
        <div class="qnum">{{ $i + 1 }}</div>
        <div>
            <div class="qtext">{{ $q->question_text }}</div>
            <div class="qmeta">
                <span class="qbadge {{ strtolower(str_replace(' ','',str_replace('/','', $qtLabel))) }}">{{ $qtLabel }}</span>
                <span class="qbadge {{ strtolower($q->difficulty ?? 'medium') }}">{{ $diffLabel }}</span>
                @if($q->module_index)
                    <span class="qbadge" style="background:#f1f5f9;color:#475569;">Module {{ $q->module_index }}</span>
                @endif
                <span class="qbadge" style="background:#f1f5f9;color:#475569;">{{ $q->marks }} mark{{ $q->marks !== 1 ? 's' : '' }}</span>
            </div>
        </div>
    </div>

    {{-- Options --}}
    @foreach(['a','b','c','d'] as $opt)
        @php $text = $q->{'option_'.$opt}; @endphp
        @if($text)
        <div class="option-row {{ strtolower($q->correct_answer) === $opt ? 'correct' : '' }}">
            <span class="opt-letter">{{ strtoupper($opt) }}.</span>
            <span>{{ $text }}</span>
            @if(strtolower($q->correct_answer) === $opt)
                <span style="margin-left:auto; font-size:12px; font-weight:700; color:#15803d;">✓ Correct</span>
            @endif
        </div>
        @endif
    @endforeach

    @if($q->explanation)
    <div class="explanation-box">
        <strong>Explanation:</strong> {{ $q->explanation }}
    </div>
    @endif
</div>
@empty
<div style="background:#fff; border:1.5px solid #e5e7eb; border-radius:12px; padding:40px; text-align:center; color:#6b7280;">
    <div style="font-size:32px; margin-bottom:10px;">❓</div>
    <p class="font-semibold">No active questions yet.</p>
    <a href="{{ route('elearning.quiz-questions.create', [$course, $lesson, $quiz]) }}" class="btn btn-primary btn-sm" style="margin-top:12px; display:inline-block;">Add Questions</a>
</div>
@endforelse

@endsection
