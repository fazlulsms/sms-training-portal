@extends('layouts.app')
@section('page-title', $quiz->title)
@section('content')

<style>
.quiz-page { max-width: 760px; margin: 0 auto; }

.back-link {
    display: inline-flex; align-items: center; gap: 6px;
    color: #6b7280; font-weight: 600; text-decoration: none;
    margin-bottom: 18px; font-size: 13.5px;
    transition: color .15s;
}
.back-link:hover { color: #1e3a8a; }

/* ── Quiz info header ── */
.quiz-header-card {
    background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
    border-radius: 16px; padding: 26px 28px; color: white;
    margin-bottom: 22px;
    box-shadow: 0 8px 24px rgba(30,58,138,.25);
}
.qh-title { font-size: 20px; font-weight: 800; margin: 0 0 16px; }
.qh-meta { display: flex; flex-wrap: wrap; gap: 20px; }
.qh-stat { text-align: center; background: rgba(255,255,255,.12); border-radius: 10px; padding: 12px 18px; min-width: 80px; }
.qh-stat-val { font-size: 22px; font-weight: 800; line-height: 1; }
.qh-stat-lbl { font-size: 10.5px; opacity: .8; font-weight: 600; text-transform: uppercase; letter-spacing: .5px; margin-top: 3px; }
.qh-attempts { margin-top: 14px; font-size: 13px; opacity: .85; }

/* ── Question cards ── */
.q-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    margin-bottom: 16px;
    overflow: hidden;
    box-shadow: 0 1px 6px rgba(15,23,42,.05);
    transition: border-color .15s;
}
.q-card:focus-within { border-color: #93c5fd; box-shadow: 0 0 0 3px rgba(147,197,253,.2); }

.q-header {
    padding: 14px 20px;
    background: #f8fafc;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    gap: 10px;
}
.q-number {
    width: 28px; height: 28px; border-radius: 8px;
    background: #1e3a8a; color: white;
    display: flex; align-items: center; justify-content: center;
    font-size: 12px; font-weight: 800; flex-shrink: 0;
}
.q-text { font-size: 14px; font-weight: 700; color: #111827; line-height: 1.5; flex: 1; }

.q-options { padding: 16px 20px; display: flex; flex-direction: column; gap: 10px; }

.option-label {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    border: 1.5px solid #e5e7eb;
    border-radius: 10px;
    cursor: pointer;
    transition: border-color .15s, background .15s;
    font-size: 14px;
    font-weight: 600;
    color: #374151;
}
.option-label:hover { border-color: #93c5fd; background: #f0f9ff; }

.option-label input[type="radio"] { display: none; }
.option-label input[type="radio"]:checked ~ .option-circle { background: #1e3a8a; border-color: #1e3a8a; }
.option-label input[type="radio"]:checked ~ .option-circle::after { opacity: 1; }
.option-label:has(input:checked) { border-color: #1e3a8a; background: #eff6ff; color: #1e40af; }

.option-circle {
    width: 20px; height: 20px; border-radius: 50%;
    border: 2px solid #d1d5db;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    transition: border-color .15s, background .15s;
    position: relative;
}
.option-circle::after {
    content: '';
    width: 8px; height: 8px; border-radius: 50%;
    background: white;
    opacity: 0;
    transition: opacity .15s;
}

.option-key {
    width: 24px; height: 24px; border-radius: 6px;
    background: #f3f4f6; color: #6b7280;
    display: flex; align-items: center; justify-content: center;
    font-size: 11px; font-weight: 800;
    flex-shrink: 0;
    transition: background .15s;
}
.option-label:has(input:checked) .option-key { background: #dbeafe; color: #1e40af; }

/* ── Submit bar ── */
.submit-bar {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    padding: 18px 22px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 14px;
    margin-top: 4px;
    box-shadow: 0 1px 6px rgba(15,23,42,.05);
}
.submit-bar p { margin: 0; font-size: 13px; color: #6b7280; font-weight: 600; }
.submit-btn {
    background: #1e3a8a; color: white;
    border: none; padding: 12px 26px;
    font-size: 14px; font-weight: 700;
    border-radius: 10px; cursor: pointer;
    font-family: inherit;
    display: inline-flex; align-items: center; gap: 8px;
    transition: background .15s;
}
.submit-btn:hover { background: #1d4ed8; }

/* ── Progress counter ── */
.quiz-progress-bar-wrap {
    background: #fff; border: 1px solid #e5e7eb; border-radius: 12px;
    padding: 14px 18px; margin-bottom: 18px;
    box-shadow: 0 1px 4px rgba(15,23,42,.04);
}
.qp-label { font-size: 12px; font-weight: 700; color: #6b7280; margin-bottom: 8px; display: flex; justify-content: space-between; }
.qp-bar { height: 6px; background: #f1f5f9; border-radius: 10px; overflow: hidden; }
.qp-fill { height: 100%; background: #2563eb; border-radius: 10px; transition: width .3s; }
</style>

<div class="quiz-page">

    <a href="{{ route('participant.lesson.show', [$enrollment->id, $quiz->lesson_id]) }}" class="back-link">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
        Back to Lesson
    </a>

    {{-- Quiz Header ── --}}
    <div class="quiz-header-card">
        <div class="qh-title">{{ $quiz->title }}</div>
        <div class="qh-meta">
            <div class="qh-stat">
                <div class="qh-stat-val">{{ $quiz->questions->count() }}</div>
                <div class="qh-stat-lbl">Questions</div>
            </div>
            <div class="qh-stat">
                <div class="qh-stat-val">{{ $quiz->pass_mark }}%</div>
                <div class="qh-stat-lbl">Pass Mark</div>
            </div>
            <div class="qh-stat">
                @if($quiz->max_attempt > 0)
                    <div class="qh-stat-val">{{ $quiz->max_attempt - $attemptCount }}</div>
                    <div class="qh-stat-lbl">Remaining</div>
                @else
                    <div class="qh-stat-val">∞</div>
                    <div class="qh-stat-lbl">Attempts</div>
                @endif
            </div>
            <div class="qh-stat">
                <div class="qh-stat-val">{{ $attemptCount }}</div>
                <div class="qh-stat-lbl">Used</div>
            </div>
        </div>
        @if($attemptCount > 0)
        <div class="qh-attempts">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;opacity:.7;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            You have attempted this quiz {{ $attemptCount }} time{{ $attemptCount !== 1 ? 's' : '' }} before.
        </div>
        @endif
    </div>

    {{-- Progress indicator (JS-driven) ── --}}
    <div class="quiz-progress-bar-wrap">
        <div class="qp-label">
            <span>Questions answered</span>
            <span id="answered-count">0</span> / {{ $quiz->questions->count() }}
        </div>
        <div class="qp-bar">
            <div class="qp-fill" id="qp-fill" style="width:0%;"></div>
        </div>
    </div>

    {{-- Questions ── --}}
    <form id="quiz-form"
          method="POST"
          action="{{ route('participant.quiz.submit', ['enrollment' => $enrollment->id, 'quiz' => $quiz->id]) }}">
        @csrf

        @foreach($quiz->questions as $question)
        @php
            $qType = strtolower($question->question_type ?? '');
            $qText = $question->question ?? $question->question_text ?? 'Question text missing';
        @endphp
        <div class="q-card" data-question="{{ $question->id }}">
            <div class="q-header">
                <div class="q-number">{{ $loop->iteration }}</div>
                <div class="q-text">{{ $qText }}</div>
            </div>
            <div class="q-options">
                @if($qType === 'true_false')
                    @foreach(['True', 'False'] as $opt)
                    <label class="option-label">
                        <input type="radio" name="answers[{{ $question->id }}]" value="{{ $opt }}" required
                               onchange="updateProgress()">
                        <span class="option-circle"></span>
                        <span class="option-key">{{ substr($opt,0,1) }}</span>
                        {{ $opt }}
                    </label>
                    @endforeach
                @else
                    @foreach(['A','B','C','D'] as $key)
                    @php $optVal = $question->{'option_' . strtolower($key)} ?? null; @endphp
                    @if($optVal)
                    <label class="option-label">
                        <input type="radio" name="answers[{{ $question->id }}]" value="{{ $key }}" required
                               onchange="updateProgress()">
                        <span class="option-circle"></span>
                        <span class="option-key">{{ $key }}</span>
                        {{ $optVal }}
                    </label>
                    @endif
                    @endforeach
                @endif
            </div>
        </div>
        @endforeach

        {{-- Submit ── --}}
        <div class="submit-bar">
            <p>Answer all questions, then submit your quiz.</p>
            <button type="submit" class="submit-btn">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                Submit Quiz
            </button>
        </div>

    </form>
</div>

<script>
const totalQ = {{ $quiz->questions->count() }};

function updateProgress() {
    const answered = document.querySelectorAll('#quiz-form input[type="radio"]:checked');
    const uniqueAnswered = new Set([...answered].map(i => i.name)).size;
    document.getElementById('answered-count').textContent = uniqueAnswered;
    document.getElementById('qp-fill').style.width = (uniqueAnswered / totalQ * 100) + '%';
}
</script>

@endsection
