@extends('layouts.app')
@section('page-title', 'AI Lesson Content — Preview')

@section('content')
@php
    $ai    = $draft['ai'];
    $usage = $draft['usage'] ?? [];
    $level = $draft['learning_level'] ?? '—';

    $sections = $ai['main_sections']      ?? [];
    $scenario = $ai['scenario']           ?? null;
    $kcs      = $ai['knowledge_checks']   ?? [];
@endphp

<style>
.alc-wrap   { max-width: 980px; margin: 0 auto; }
.alc-header {
    background: linear-gradient(135deg, #14532d 0%, #16a34a 100%);
    border-radius: var(--r-xl); padding: 22px 28px; margin-bottom: 22px;
    display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; flex-wrap: wrap;
}
.alc-header-left h1 { color: #fff; font-size: 19px; font-weight: 800; margin: 0 0 6px; }
.alc-header-left p  { color: rgba(255,255,255,.7); font-size: 13px; margin: 0; }
.alc-stats {
    display: flex; gap: 10px; flex-wrap: wrap;
}
.alc-stat {
    background: rgba(255,255,255,.15); border-radius: 8px; padding: 7px 13px;
    text-align: center; min-width: 70px;
}
.alc-stat-val  { font-size: 15px; font-weight: 800; color: #fff; }
.alc-stat-lbl  { font-size: 10px; color: rgba(255,255,255,.65); margin-top: 1px; }

.alc-notice {
    background: #fefce8; border: 1px solid #fde047; border-radius: var(--r);
    padding: 11px 16px; font-size: 13px; color: #713f12;
    margin-bottom: 20px; display: flex; align-items: center; gap: 10px;
}

/* Block cards */
.ai-block {
    background: var(--surface); border: 1px solid var(--border);
    border-radius: var(--r-xl); margin-bottom: 16px;
    box-shadow: var(--shadow-sm); overflow: hidden;
}
.ai-block-head {
    display: flex; align-items: center; gap: 10px;
    padding: 12px 18px; border-bottom: 1px solid var(--border-light);
    background: #f8fafc;
}
.ai-block-badge {
    font-size: 10px; font-weight: 800; text-transform: uppercase;
    padding: 3px 10px; border-radius: 20px; color: #fff; flex-shrink: 0;
}
.ai-block-title { font-size: 14px; font-weight: 700; color: var(--text); flex: 1; }
.ai-block-body  { padding: 18px 20px; }

/* Editable areas */
.ai-textarea {
    width: 100%; border: 1px solid var(--border); border-radius: var(--r);
    padding: 10px 13px; font-size: 13px; font-family: inherit;
    color: var(--text); background: #fff; box-sizing: border-box;
    resize: vertical; transition: border-color .15s;
    line-height: 1.6;
}
.ai-textarea:focus { outline: none; border-color: #16a34a; box-shadow: 0 0 0 3px rgba(22,163,74,.1); }
.ai-label {
    display: block; font-size: 11.5px; font-weight: 700;
    color: var(--text-2); margin-bottom: 5px;
}
.ai-label + .ai-label { margin-top: 13px; }
.ai-fg2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
@media (max-width: 640px) { .ai-fg2 { grid-template-columns: 1fr; } }

/* Scenario / KC option display */
.sc-opts { display: flex; flex-direction: column; gap: 8px; margin-top: 10px; }
.sc-opt  {
    display: flex; gap: 10px; align-items: flex-start;
    background: #f8fafc; border: 1px solid var(--border-light);
    border-radius: var(--r); padding: 10px 13px; font-size: 13px;
}
.sc-opt.correct { background: #f0fdf4; border-color: #86efac; }
.sc-opt.wrong   { background: #fff7ed; border-color: #fdba74; }
.sc-opt-icon    { flex-shrink: 0; font-size: 15px; margin-top: 1px; }
.sc-opt-text    { flex: 1; }
.sc-opt-expl    { font-size: 12px; color: var(--text-muted); margin-top: 4px; font-style: italic; }

/* Footer action bar */
.alc-footer {
    background: var(--surface); border: 1px solid var(--border);
    border-radius: var(--r-xl); padding: 18px 22px;
    display: flex; align-items: center; gap: 12px; flex-wrap: wrap;
    margin-top: 24px; box-shadow: var(--shadow-sm);
}
.alc-footer-left  { flex: 1; display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
.alc-footer-right { display: flex; gap: 8px; flex-wrap: wrap; }

.clear-lbl {
    font-size: 13px; font-weight: 600; color: var(--text-2);
    display: flex; align-items: center; gap: 7px; cursor: pointer; user-select: none;
}
.clear-lbl input { width: 16px; height: 16px; accent-color: #dc2626; cursor: pointer; }
</style>

<div class="alc-wrap">

    {{-- Header --}}
    <div class="alc-header">
        <div class="alc-header-left">
            <h1>✨ AI Lesson Content — Preview &amp; Edit</h1>
            <p>
                {{ $course->name }} &nbsp;›&nbsp; {{ $lesson->title }}
                &nbsp;&nbsp;|&nbsp;&nbsp;
                Level: <strong style="color:#fff;">{{ $level }}</strong>
            </p>
        </div>
        <div class="alc-stats">
            <div class="alc-stat">
                <div class="alc-stat-val">{{ count($sections) }}</div>
                <div class="alc-stat-lbl">Sections</div>
            </div>
            <div class="alc-stat">
                <div class="alc-stat-val">{{ count($kcs) }}</div>
                <div class="alc-stat-lbl">Quizzes</div>
            </div>
            @if(!empty($usage['total_tokens']))
            <div class="alc-stat">
                <div class="alc-stat-val">{{ number_format($usage['total_tokens']) }}</div>
                <div class="alc-stat-lbl">Tokens</div>
            </div>
            @endif
            @if(!empty($draft['duration_ms']))
            <div class="alc-stat">
                <div class="alc-stat-val">{{ round($draft['duration_ms'] / 1000, 1) }}s</div>
                <div class="alc-stat-lbl">Generated</div>
            </div>
            @endif
        </div>
    </div>

    <div class="alc-notice">
        <span style="font-size:16px;">📝</span>
        <span>Review and edit all content below. Changes here are applied when you click <strong>Save Content Blocks</strong>. Scenario and quiz options are fixed — edit them in the Lesson Builder after saving.</span>
    </div>

    <form action="{{ route('elearning.ai-lesson-content.save', [$course, $lesson]) }}" method="POST">
    @csrf

    {{-- ── 1. Introduction ─────────────────────────────────────────── --}}
    <div class="ai-block">
        <div class="ai-block-head">
            <span class="ai-block-badge" style="background:#1e3a8a;">Rich Text</span>
            <span class="ai-block-title">1. Introduction</span>
        </div>
        <div class="ai-block-body">
            <label class="ai-label">Content (HTML)</label>
            <textarea name="introduction" class="ai-textarea" rows="6">{{ $ai['introduction']['html'] ?? '' }}</textarea>
        </div>
    </div>

    {{-- ── 2. Main Sections ─────────────────────────────────────────── --}}
    @foreach($sections as $i => $section)
    <div class="ai-block">
        <div class="ai-block-head">
            <span class="ai-block-badge" style="background:#1e3a8a;">Rich Text</span>
            <span class="ai-block-title">{{ $i + 2 }}. Section: {{ $section['heading'] ?? 'Section ' . ($i + 1) }}</span>
        </div>
        <div class="ai-block-body">
            <label class="ai-label">Section Heading</label>
            <input type="text" name="main_sections[{{ $i }}][heading]"
                   class="ai-textarea" style="resize:none; height:auto; padding:9px 13px;"
                   value="{{ $section['heading'] ?? '' }}">
            <label class="ai-label" style="margin-top:13px;">Content (HTML)</label>
            <textarea name="main_sections[{{ $i }}][html]" class="ai-textarea" rows="7">{{ $section['html'] ?? '' }}</textarea>
        </div>
    </div>
    @endforeach

    {{-- ── 3. Practical Example ─────────────────────────────────────── --}}
    @if(!empty($ai['practical_example']['html']))
    <div class="ai-block">
        <div class="ai-block-head">
            <span class="ai-block-badge" style="background:#1e3a8a;">Rich Text</span>
            <span class="ai-block-title">{{ count($sections) + 2 }}. Practical Example</span>
        </div>
        <div class="ai-block-body">
            <label class="ai-label">Content (HTML)</label>
            <textarea name="practical_example" class="ai-textarea" rows="5">{{ $ai['practical_example']['html'] }}</textarea>
        </div>
    </div>
    @endif

    {{-- ── 4. Scenario Exercise ─────────────────────────────────────── --}}
    @if($scenario && !empty($scenario['options']))
    @php $scNum = count($sections) + 3; @endphp
    <div class="ai-block">
        <div class="ai-block-head">
            <span class="ai-block-badge" style="background:#9333ea;">Scenario</span>
            <span class="ai-block-title">{{ $scNum }}. {{ $scenario['title'] ?? 'Scenario Exercise' }}</span>
        </div>
        <div class="ai-block-body">
            <label class="ai-label">Scenario Text <small style="font-weight:400;">(editable)</small></label>
            <textarea name="scenario_text" class="ai-textarea" rows="4">{{ $scenario['text'] ?? '' }}</textarea>

            <label class="ai-label" style="margin-top:16px;">Response Options <small style="font-weight:400; color:var(--text-muted);">(fixed — edit in Lesson Builder after saving)</small></label>
            <div class="sc-opts">
                @foreach($scenario['options'] as $opt)
                <div class="sc-opt {{ $opt['correct'] ? 'correct' : 'wrong' }}">
                    <span class="sc-opt-icon">{{ $opt['correct'] ? '✅' : '❌' }}</span>
                    <div class="sc-opt-text">
                        {{ $opt['text'] }}
                        @if(!empty($opt['explanation']))
                        <div class="sc-opt-expl">{{ $opt['explanation'] }}</div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- ── 5. Knowledge Checks ──────────────────────────────────────── --}}
    @foreach($kcs as $i => $kc)
    @php $kcNum = count($sections) + 4 + $i; @endphp
    <div class="ai-block">
        <div class="ai-block-head">
            <span class="ai-block-badge" style="background:#d97706;">Knowledge Check</span>
            <span class="ai-block-title">
                {{ $kcNum }}. {{ $kc['title'] ?? 'Knowledge Check ' . ($i + 1) }}
                <span style="font-size:11px; font-weight:600; color:var(--text-muted); margin-left:6px;">
                    ({{ $kc['type'] === 'truefalse' ? 'True / False' : 'Multiple Choice' }})
                </span>
            </span>
        </div>
        <div class="ai-block-body">
            <label class="ai-label">Question <small style="font-weight:400;">(editable)</small></label>
            <textarea name="kc_question[{{ $i }}]" class="ai-textarea" rows="3">{{ $kc['question'] ?? '' }}</textarea>

            <label class="ai-label" style="margin-top:14px;">Options <small style="font-weight:400; color:var(--text-muted);">(fixed — edit in Lesson Builder after saving)</small></label>
            <div class="sc-opts">
                @foreach($kc['options'] as $opt)
                <div class="sc-opt {{ $opt['correct'] ? 'correct' : 'wrong' }}">
                    <span class="sc-opt-icon">{{ $opt['correct'] ? '✅' : '❌' }}</span>
                    <span class="sc-opt-text">{{ $opt['text'] }}</span>
                </div>
                @endforeach
            </div>

            <label class="ai-label" style="margin-top:14px;">Explanation <small style="font-weight:400;">(editable)</small></label>
            <textarea name="kc_explanation[{{ $i }}]" class="ai-textarea" rows="3">{{ $kc['explanation'] ?? '' }}</textarea>
        </div>
    </div>
    @endforeach

    {{-- ── 6. Lesson Summary ────────────────────────────────────────── --}}
    @if(!empty($ai['summary']['html']))
    @php $sumNum = count($sections) + 4 + count($kcs); @endphp
    <div class="ai-block">
        <div class="ai-block-head">
            <span class="ai-block-badge" style="background:#0f766e;">Rich Text</span>
            <span class="ai-block-title">{{ $sumNum }}. Lesson Summary</span>
        </div>
        <div class="ai-block-body">
            <label class="ai-label">Content (HTML)</label>
            <textarea name="summary" class="ai-textarea" rows="6">{{ $ai['summary']['html'] }}</textarea>
        </div>
    </div>
    @endif

    {{-- ── Footer actions ───────────────────────────────────────────── --}}
    <div class="alc-footer">
        <div class="alc-footer-left">
            <label class="clear-lbl">
                <input type="checkbox" name="clear_existing" value="1">
                Replace existing blocks (deletes current lesson content)
            </label>
        </div>
        <div class="alc-footer-right">
            <button type="submit" class="btn btn-primary">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="vertical-align:-2px;margin-right:4px;"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Save Content Blocks
            </button>
            <a href="{{ route('elearning.lessons.edit', [$course, $lesson]) }}?ai_generate=1"
               class="btn" style="background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe;">
                ↺ Regenerate
            </a>
            <a href="{{ route('elearning.ai-lesson-content.cancel', [$course, $lesson]) }}"
               class="btn btn-ghost" onclick="return confirm('Discard AI draft and return to lesson builder?')">
                Discard
            </a>
        </div>
    </div>

    </form>

</div>
@endsection
