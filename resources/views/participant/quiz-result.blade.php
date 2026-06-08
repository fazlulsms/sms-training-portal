@extends('layouts.app')
@section('page-title', 'Quiz Result')
@section('content')

<style>
.result-page { max-width: 700px; margin: 0 auto; }

.back-link {
    display: inline-flex; align-items: center; gap: 6px;
    color: #6b7280; font-weight: 600; text-decoration: none;
    margin-bottom: 18px; font-size: 13.5px;
    transition: color .15s;
}
.back-link:hover { color: #1e3a8a; }

/* ── Result hero ── */
.result-hero {
    border-radius: 16px; padding: 36px 32px;
    text-align: center; margin-bottom: 20px;
    box-shadow: 0 8px 24px rgba(0,0,0,.10);
}
.result-hero.passed { background: linear-gradient(135deg, #15803d, #4ade80); color: white; }
.result-hero.failed { background: linear-gradient(135deg, #b91c1c, #f87171); color: white; }

.result-icon-wrap { margin-bottom: 12px; }
.result-title    { font-size: 24px; font-weight: 800; margin: 0 0 6px; }
.result-subtitle { font-size: 14px; opacity: .85; margin: 0 0 20px; }

.score-ring {
    width: 110px; height: 110px;
    border-radius: 50%;
    background: rgba(255,255,255,.18);
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    margin: 0 auto;
    border: 3px solid rgba(255,255,255,.35);
}
.score-num { font-size: 32px; font-weight: 800; line-height: 1; }
.score-lbl { font-size: 11px; font-weight: 700; opacity: .8; text-transform: uppercase; letter-spacing: .5px; margin-top: 3px; }

/* ── Stat boxes ── */
.stats-grid {
    display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px;
    margin-bottom: 20px;
}
@media (max-width: 480px) { .stats-grid { grid-template-columns: 1fr; } }

.stat-box {
    background: #fff; border: 1px solid #e5e7eb; border-radius: 14px;
    padding: 18px; text-align: center;
    box-shadow: 0 1px 4px rgba(15,23,42,.05);
}
.sb-val  { font-size: 28px; font-weight: 800; color: #111827; line-height: 1; }
.sb-lbl  { font-size: 12px; color: #6b7280; font-weight: 600; margin-top: 4px; }

/* ── Pass mark bar ── */
.pass-bar-card {
    background: #fff; border: 1px solid #e5e7eb; border-radius: 14px;
    padding: 18px 20px; margin-bottom: 20px;
    box-shadow: 0 1px 4px rgba(15,23,42,.05);
}
.pb-header { font-size: 13px; font-weight: 700; color: #374151; margin-bottom: 10px; display: flex; justify-content: space-between; }
.pb-track  { height: 10px; background: #f1f5f9; border-radius: 10px; overflow: hidden; position: relative; }
.pb-fill   { height: 100%; border-radius: 10px; transition: width .5s; }
.pb-fill.pass { background: #16a34a; }
.pb-fill.fail { background: #dc2626; }
.pb-marker { position: absolute; top: -4px; bottom: -4px; width: 3px; background: #f59e0b; border-radius: 2px; }
.pb-labels { display: flex; justify-content: space-between; font-size: 11.5px; color: #9ca3af; font-weight: 600; margin-top: 6px; }

/* ── History card ── */
.history-card {
    background: #fff; border: 1px solid #e5e7eb; border-radius: 14px;
    overflow: hidden; box-shadow: 0 1px 4px rgba(15,23,42,.05);
    margin-bottom: 20px;
}
.hc-head { padding: 14px 20px; border-bottom: 1px solid #f1f5f9; font-size: 14px; font-weight: 800; color: #111827; }
.history-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.history-table th {
    padding: 10px 16px; text-align: left; font-size: 10.5px; font-weight: 700;
    color: #6b7280; text-transform: uppercase; letter-spacing: .5px;
    background: #f9fafb; border-bottom: 1px solid #e5e7eb;
}
.history-table td { padding: 11px 16px; border-bottom: 1px solid #f8fafc; color: #374151; }
.history-table tr:last-child td { border-bottom: none; }
.history-table tr:hover td { background: #f9fafb; }
.history-table tr.current-row td { background: #fffbeb; }

.badge { display: inline-block; padding: 3px 9px; border-radius: 20px; font-size: 11px; font-weight: 700; }
.badge-success { background: #dcfce7; color: #166534; }
.badge-danger  { background: #fee2e2; color: #991b1b; }

/* ── Actions ── */
.action-row { display: flex; gap: 10px; flex-wrap: wrap; }
.btn {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 11px 20px; border-radius: 10px; font-weight: 700; font-size: 14px;
    text-decoration: none; border: none; cursor: pointer; font-family: inherit;
    transition: background .15s;
}
.btn-primary { background: #1e3a8a; color: white; }
.btn-primary:hover { background: #1d4ed8; }
.btn-teal    { background: #0f766e; color: white; }
.btn-teal:hover { background: #0d9488; }
.btn-light   { background: #f3f4f6; color: #374151; border: 1px solid #e5e7eb; }
.btn-light:hover { background: #e5e7eb; }
</style>

<div class="result-page">

    <a href="{{ route('participant.lesson.show', [$enrollment->id, $quiz->lesson_id]) }}" class="back-link">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
        Back to Lesson
    </a>

    {{-- Hero ── --}}
    @php $passed = $score >= $quiz->pass_mark; @endphp
    <div class="result-hero {{ $passed ? 'passed' : 'failed' }}">
        <div class="result-icon-wrap">
            @if($passed)
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.8)" stroke-width="1.5"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg>
            @else
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.8)" stroke-width="1.5"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
            @endif
        </div>
        <p class="result-title">{{ $passed ? 'Congratulations! You Passed.' : 'Not Quite There Yet.' }}</p>
        <p class="result-subtitle">{{ $quiz->title }}</p>
        <div class="score-ring">
            <div class="score-num">{{ $score }}%</div>
            <div class="score-lbl">Score</div>
        </div>
    </div>

    {{-- Stats ── --}}
    <div class="stats-grid">
        <div class="stat-box">
            <div class="sb-val">{{ $totalQuestions }}</div>
            <div class="sb-lbl">Total Questions</div>
        </div>
        <div class="stat-box">
            <div class="sb-val" style="color:#16a34a;">{{ $correctAnswers }}</div>
            <div class="sb-lbl">Correct</div>
        </div>
        <div class="stat-box">
            <div class="sb-val" style="color:#dc2626;">{{ $totalQuestions - $correctAnswers }}</div>
            <div class="sb-lbl">Incorrect</div>
        </div>
    </div>

    {{-- Pass mark bar ── --}}
    <div class="pass-bar-card">
        @php $pmPct = min($quiz->pass_mark, 100); @endphp
        <div class="pb-header">
            <span>Score vs. Pass Mark</span>
            <span>Pass: {{ $quiz->pass_mark }}%</span>
        </div>
        <div class="pb-track">
            <div class="pb-fill {{ $passed ? 'pass' : 'fail' }}" style="width:{{ min($score,100) }}%;"></div>
            <div class="pb-marker" style="left:{{ $pmPct }}%;"></div>
        </div>
        <div class="pb-labels">
            <span>0%</span>
            <span style="color:#f59e0b;font-weight:700;">Required: {{ $quiz->pass_mark }}%</span>
            <span>100%</span>
        </div>
    </div>

    {{-- Attempt history ── --}}
    @if($attempts->count() > 0)
    <div class="history-card">
        <div class="hc-head">Attempt History</div>
        <table class="history-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Score</th>
                    <th>Correct / Total</th>
                    <th>Required</th>
                    <th>Result</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attempts as $h)
                <tr class="{{ $h->id === $attempt->id ? 'current-row' : '' }}">
                    <td>{{ $loop->iteration }}</td>
                    <td><strong>{{ $h->score }}%</strong></td>
                    <td>{{ $h->correct_answers }} / {{ $h->total_questions }}</td>
                    <td>{{ $quiz->pass_mark }}%</td>
                    <td>
                        @if($h->score >= $quiz->pass_mark)
                            <span class="badge badge-success">Passed</span>
                        @else
                            <span class="badge badge-danger">Failed</span>
                        @endif
                    </td>
                    <td style="color:#6b7280;font-size:12px;">{{ $h->created_at->format('d M Y, g:i A') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Actions ── --}}
    <div class="action-row">
        @if($passed)
            <a href="{{ route('participant.lesson.show', [$enrollment->id, $quiz->lesson_id]) }}" class="btn btn-teal">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                Continue to Lesson
            </a>
            <a href="{{ route('participant.elearning-details', $enrollment->id) }}" class="btn btn-light">
                Course Overview
            </a>
        @else
            @php
                $attemptsUsed = $attempts->count();
                $canRetake    = $quiz->max_attempt <= 0 || $attemptsUsed < $quiz->max_attempt;
            @endphp
            @if($canRetake)
                <a href="{{ route('participant.quiz.start', [$enrollment->id, $quiz->id]) }}" class="btn btn-primary">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-.08-8.17"/></svg>
                    Retake Quiz
                </a>
            @else
                <span class="btn btn-light" style="cursor:not-allowed;opacity:.6;">No Attempts Remaining</span>
            @endif
            <a href="{{ route('participant.lesson.show', [$enrollment->id, $quiz->lesson_id]) }}" class="btn btn-light">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                Review Lesson
            </a>
        @endif
    </div>

</div>

@endsection
