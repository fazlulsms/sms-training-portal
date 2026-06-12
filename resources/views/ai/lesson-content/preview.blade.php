@extends('layouts.app')
@section('page-title', 'AI Lesson Content — Preview')

@section('content')
<div style="max-width:860px; margin:0 auto; padding:30px 20px;">

    {{-- Header --}}
    <div style="display:flex; align-items:center; gap:14px; margin-bottom:28px;">
        <div style="background:linear-gradient(135deg,#6366f1,#8b5cf6); color:#fff; border-radius:12px; padding:10px 14px; font-size:20px; line-height:1;">✨</div>
        <div>
            <h1 style="font-size:20px; font-weight:800; margin:0 0 2px; color:var(--text);">AI Lesson Content Preview</h1>
            <p style="font-size:13px; color:var(--muted); margin:0;">{{ $course->name }} — {{ $lesson->title }}</p>
        </div>
        <div style="margin-left:auto; font-size:12px; color:var(--muted); text-align:right;">
            Level: <strong>{{ $draft['learning_level'] }}</strong><br>
            {{ count($draft['ai']['blocks'] ?? []) }} blocks generated
        </div>
    </div>

    <form method="POST" action="{{ route('elearning.ai-lesson-content.save', [$course, $lesson]) }}">
        @csrf

        {{-- Clear existing toggle --}}
        <div style="background:#fef9c3; border:1px solid #fde047; border-radius:10px; padding:14px 18px; margin-bottom:24px; display:flex; gap:12px; align-items:center;">
            <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-size:13px; font-weight:600; color:#854d0e;">
                <input type="checkbox" name="clear_existing" value="1" style="width:16px; height:16px; accent-color:#d97706;">
                Replace existing blocks — delete all current blocks before adding AI content
            </label>
        </div>

        {{-- Block list --}}
        @foreach(($draft['ai']['blocks'] ?? []) as $bi => $block)
        @php $type = $block['type'] ?? 'rich_text'; @endphp

        <div style="background:var(--card); border:1px solid var(--border); border-radius:12px; margin-bottom:16px; overflow:hidden;">

            {{-- Block header --}}
            @php
                $colors = [
                    'rich_text'         => '#1e3a8a',
                    'fun_fact'          => '#d97706',
                    'reflection'        => '#7c3aed',
                    'click_reveal'      => '#0369a1',
                    'myth_fact'         => '#b91c1c',
                    'workplace_example' => '#065f46',
                    'scenario'          => '#6d28d9',
                    'knowledge_check'   => '#b45309',
                    'case_study'        => '#3730a3',
                ];
                $icons = [
                    'rich_text'         => '📄',
                    'fun_fact'          => '💡',
                    'reflection'        => '🤔',
                    'click_reveal'      => '👁',
                    'myth_fact'         => '⚡',
                    'workplace_example' => '🏭',
                    'scenario'          => '🎯',
                    'knowledge_check'   => '❓',
                    'case_study'        => '📋',
                ];
                $labels = [
                    'rich_text'         => 'Rich Text',
                    'fun_fact'          => 'Fun Fact',
                    'reflection'        => 'Reflection',
                    'click_reveal'      => 'Click to Reveal',
                    'myth_fact'         => 'Myth vs Fact',
                    'workplace_example' => 'Workplace Example',
                    'scenario'          => 'Scenario Exercise',
                    'knowledge_check'   => 'Knowledge Check',
                    'case_study'        => 'Case Study',
                ];
                $bcolor = $colors[$type] ?? '#374151';
                $bicon  = $icons[$type]  ?? '📦';
                $blabel = $labels[$type] ?? ucfirst(str_replace('_',' ',$type));
            @endphp
            <div style="background:{{ $bcolor }}; color:#fff; padding:10px 16px; display:flex; align-items:center; gap:10px;">
                <span style="font-size:16px;">{{ $bicon }}</span>
                <span style="font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.5px;">{{ $blabel }}</span>
                <span style="font-size:13px; margin-left:4px; opacity:.85;">{{ $block['title'] ?? '' }}</span>
                <span style="margin-left:auto; font-size:11px; opacity:.6;">Block {{ $bi + 1 }}</span>
            </div>

            <div style="padding:18px 20px; display:flex; flex-direction:column; gap:12px;">

                {{-- Title field (always) --}}
                <div>
                    <label style="font-size:11px; font-weight:700; text-transform:uppercase; color:var(--muted); display:block; margin-bottom:4px;">Block Title</label>
                    <input type="text" name="blocks[{{ $bi }}][title]" value="{{ $block['title'] ?? '' }}"
                           style="width:100%; border:1px solid var(--border); border-radius:8px; padding:8px 12px; font-size:13px; background:var(--input-bg); color:var(--text);">
                </div>

                @switch($type)

                {{-- ── Rich Text ──────────────────────────── --}}
                @case('rich_text')
                <div>
                    <label style="font-size:11px; font-weight:700; text-transform:uppercase; color:var(--muted); display:block; margin-bottom:4px;">Content (HTML)</label>
                    <textarea name="blocks[{{ $bi }}][content]" rows="6"
                              style="width:100%; border:1px solid var(--border); border-radius:8px; padding:10px 12px; font-size:13px; font-family:monospace; background:var(--input-bg); color:var(--text); resize:vertical;">{{ $block['content'] ?? '' }}</textarea>
                </div>
                @break

                {{-- ── Fun Fact ────────────────────────────── --}}
                @case('fun_fact')
                <div style="display:grid; grid-template-columns:60px 1fr; gap:10px;">
                    <div>
                        <label style="font-size:11px; font-weight:700; text-transform:uppercase; color:var(--muted); display:block; margin-bottom:4px;">Icon</label>
                        <input type="text" name="blocks[{{ $bi }}][icon]" value="{{ $block['icon'] ?? '💡' }}"
                               style="width:100%; border:1px solid var(--border); border-radius:8px; padding:8px 10px; font-size:20px; text-align:center; background:var(--input-bg); color:var(--text);">
                    </div>
                    <div>
                        <label style="font-size:11px; font-weight:700; text-transform:uppercase; color:var(--muted); display:block; margin-bottom:4px;">Fact Title</label>
                        <input type="text" name="blocks[{{ $bi }}][ff_title]" value="{{ $block['ff_title'] ?? '' }}"
                               style="width:100%; border:1px solid var(--border); border-radius:8px; padding:8px 12px; font-size:13px; background:var(--input-bg); color:var(--text);">
                    </div>
                </div>
                <div>
                    <label style="font-size:11px; font-weight:700; text-transform:uppercase; color:var(--muted); display:block; margin-bottom:4px;">Fact Content</label>
                    <textarea name="blocks[{{ $bi }}][ff_content]" rows="3"
                              style="width:100%; border:1px solid var(--border); border-radius:8px; padding:10px 12px; font-size:13px; background:var(--input-bg); color:var(--text); resize:vertical;">{{ $block['ff_content'] ?? '' }}</textarea>
                </div>
                @break

                {{-- ── Myth vs Fact ────────────────────────── --}}
                @case('myth_fact')
                <div>
                    <label style="font-size:11px; font-weight:700; text-transform:uppercase; color:#b91c1c; display:block; margin-bottom:4px;">❌ Myth</label>
                    <textarea name="blocks[{{ $bi }}][myth]" rows="3"
                              style="width:100%; border:1px solid #fca5a5; border-radius:8px; padding:10px 12px; font-size:13px; background:#fef2f2; color:#7f1d1d; resize:vertical;">{{ $block['myth'] ?? '' }}</textarea>
                </div>
                <div>
                    <label style="font-size:11px; font-weight:700; text-transform:uppercase; color:#15803d; display:block; margin-bottom:4px;">✅ Fact</label>
                    <textarea name="blocks[{{ $bi }}][fact]" rows="3"
                              style="width:100%; border:1px solid #86efac; border-radius:8px; padding:10px 12px; font-size:13px; background:#f0fdf4; color:#14532d; resize:vertical;">{{ $block['fact'] ?? '' }}</textarea>
                </div>
                @break

                {{-- ── Click to Reveal ─────────────────────── --}}
                @case('click_reveal')
                <div>
                    <label style="font-size:11px; font-weight:700; text-transform:uppercase; color:var(--muted); display:block; margin-bottom:4px;">Question</label>
                    <input type="text" name="blocks[{{ $bi }}][question]" value="{{ $block['question'] ?? '' }}"
                           style="width:100%; border:1px solid var(--border); border-radius:8px; padding:8px 12px; font-size:13px; background:var(--input-bg); color:var(--text);">
                </div>
                <div>
                    <label style="font-size:11px; font-weight:700; text-transform:uppercase; color:var(--muted); display:block; margin-bottom:4px;">Answer</label>
                    <input type="text" name="blocks[{{ $bi }}][answer]" value="{{ $block['answer'] ?? '' }}"
                           style="width:100%; border:1px solid var(--border); border-radius:8px; padding:8px 12px; font-size:13px; background:var(--input-bg); color:var(--text);">
                </div>
                <div>
                    <label style="font-size:11px; font-weight:700; text-transform:uppercase; color:var(--muted); display:block; margin-bottom:4px;">Explanation (optional)</label>
                    <input type="text" name="blocks[{{ $bi }}][explanation]" value="{{ $block['explanation'] ?? '' }}"
                           style="width:100%; border:1px solid var(--border); border-radius:8px; padding:8px 12px; font-size:13px; background:var(--input-bg); color:var(--text);">
                </div>
                @break

                {{-- ── Reflection ──────────────────────────── --}}
                @case('reflection')
                <div>
                    <label style="font-size:11px; font-weight:700; text-transform:uppercase; color:var(--muted); display:block; margin-bottom:4px;">Reflection Prompt</label>
                    <input type="text" name="blocks[{{ $bi }}][prompt]" value="{{ $block['prompt'] ?? '' }}"
                           style="width:100%; border:1px solid var(--border); border-radius:8px; padding:8px 12px; font-size:13px; background:var(--input-bg); color:var(--text);">
                </div>
                @if(!empty($block['questions']))
                <div>
                    <label style="font-size:11px; font-weight:700; text-transform:uppercase; color:var(--muted); display:block; margin-bottom:4px;">Guiding Questions (read-only)</label>
                    <ul style="margin:0; padding-left:18px; color:var(--muted); font-size:13px; line-height:1.7;">
                        @foreach($block['questions'] as $rq)<li>{{ $rq }}</li>@endforeach
                    </ul>
                </div>
                @endif
                @break

                {{-- ── Workplace Example ───────────────────── --}}
                @case('workplace_example')
                <div>
                    <label style="font-size:11px; font-weight:700; text-transform:uppercase; color:var(--muted); display:block; margin-bottom:6px;">Examples (read-only)</label>
                    <div style="display:flex; flex-direction:column; gap:8px;">
                        @foreach($block['examples'] ?? [] as $ex)
                        <div style="display:flex; gap:10px; align-items:flex-start; background:#f0fdf4; border:1px solid #bbf7d0; border-radius:8px; padding:10px 14px;">
                            <span style="flex-shrink:0; background:#10b981; color:#fff; border-radius:6px; padding:3px 8px; font-size:11px; font-weight:700;">{{ $ex['context'] ?? '' }}</span>
                            <span style="font-size:13px; color:#065f46;">{{ $ex['description'] ?? '' }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @break

                {{-- ── Scenario ────────────────────────────── --}}
                @case('scenario')
                <div>
                    <label style="font-size:11px; font-weight:700; text-transform:uppercase; color:var(--muted); display:block; margin-bottom:4px;">Scenario Text</label>
                    <textarea name="blocks[{{ $bi }}][text]" rows="4"
                              style="width:100%; border:1px solid var(--border); border-radius:8px; padding:10px 12px; font-size:13px; background:var(--input-bg); color:var(--text); resize:vertical;">{{ $block['text'] ?? '' }}</textarea>
                </div>
                @if(!empty($block['options']))
                <div>
                    <label style="font-size:11px; font-weight:700; text-transform:uppercase; color:var(--muted); display:block; margin-bottom:6px;">Options (read-only)</label>
                    @foreach($block['options'] as $opt)
                    <div style="display:flex; gap:8px; align-items:flex-start; padding:8px 12px; border-radius:8px; margin-bottom:6px; background:{{ $opt['correct'] ? '#f0fdf4' : '#f9fafb' }}; border:1px solid {{ $opt['correct'] ? '#86efac' : 'var(--border)' }};">
                        <span style="font-size:13px; font-weight:700; color:{{ $opt['correct'] ? '#15803d' : '#6b7280' }};">{{ $opt['correct'] ? '✅' : '○' }}</span>
                        <div>
                            <div style="font-size:13px; color:var(--text);">{{ $opt['text'] }}</div>
                            <div style="font-size:12px; color:var(--muted); margin-top:2px;">{{ $opt['explanation'] ?? '' }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
                @break

                {{-- ── Knowledge Check ─────────────────────── --}}
                @case('knowledge_check')
                <div>
                    <label style="font-size:11px; font-weight:700; text-transform:uppercase; color:var(--muted); display:block; margin-bottom:4px;">
                        Question
                        <span style="font-weight:400; text-transform:none; margin-left:6px; background:#e0e7ff; color:#3730a3; padding:2px 8px; border-radius:4px; font-size:10px;">{{ strtoupper($block['kc_type'] ?? 'single') }}</span>
                    </label>
                    <input type="text" name="blocks[{{ $bi }}][question]" value="{{ $block['question'] ?? '' }}"
                           style="width:100%; border:1px solid var(--border); border-radius:8px; padding:8px 12px; font-size:13px; background:var(--input-bg); color:var(--text);">
                </div>
                @if(!empty($block['options']))
                <div>
                    <label style="font-size:11px; font-weight:700; text-transform:uppercase; color:var(--muted); display:block; margin-bottom:6px;">Options (read-only)</label>
                    @foreach($block['options'] as $opt)
                    <div style="padding:6px 12px; border-radius:6px; margin-bottom:4px; font-size:13px; background:{{ $opt['correct'] ? '#f0fdf4' : '#f9fafb' }}; border:1px solid {{ $opt['correct'] ? '#86efac' : 'var(--border)' }}; color:{{ $opt['correct'] ? '#15803d' : 'var(--text)' }}; font-weight:{{ $opt['correct'] ? '700' : '400' }};">
                        {{ $opt['correct'] ? '✅' : '○' }} {{ $opt['text'] }}
                    </div>
                    @endforeach
                </div>
                @endif
                <div>
                    <label style="font-size:11px; font-weight:700; text-transform:uppercase; color:var(--muted); display:block; margin-bottom:4px;">Explanation</label>
                    <input type="text" name="blocks[{{ $bi }}][explanation]" value="{{ $block['explanation'] ?? '' }}"
                           style="width:100%; border:1px solid var(--border); border-radius:8px; padding:8px 12px; font-size:13px; background:var(--input-bg); color:var(--text);">
                </div>
                @break

                {{-- ── Case Study ──────────────────────────── --}}
                @case('case_study')
                <div>
                    <label style="font-size:11px; font-weight:700; text-transform:uppercase; color:var(--muted); display:block; margin-bottom:4px;">Case Description</label>
                    <textarea name="blocks[{{ $bi }}][case_description]" rows="4"
                              style="width:100%; border:1px solid var(--border); border-radius:8px; padding:10px 12px; font-size:13px; background:var(--input-bg); color:var(--text); resize:vertical;">{{ $block['case_description'] ?? '' }}</textarea>
                </div>
                @if(!empty($block['questions']))
                <div>
                    <label style="font-size:11px; font-weight:700; text-transform:uppercase; color:var(--muted); display:block; margin-bottom:4px;">Discussion Questions (read-only)</label>
                    <ol style="margin:0; padding-left:18px; color:var(--muted); font-size:13px; line-height:1.7;">
                        @foreach($block['questions'] as $csq)<li>{{ $csq }}</li>@endforeach
                    </ol>
                </div>
                @endif
                <div>
                    <label style="font-size:11px; font-weight:700; text-transform:uppercase; color:var(--muted); display:block; margin-bottom:4px;">Expected Response</label>
                    <textarea name="blocks[{{ $bi }}][expected_response]" rows="3"
                              style="width:100%; border:1px solid var(--border); border-radius:8px; padding:10px 12px; font-size:13px; background:var(--input-bg); color:var(--text); resize:vertical;">{{ $block['expected_response'] ?? '' }}</textarea>
                </div>
                @break

                @endswitch

            </div>
        </div>
        @endforeach

        {{-- Footer actions --}}
        <div style="position:sticky; bottom:0; background:var(--card); border-top:1px solid var(--border); border-radius:12px 12px 0 0; padding:16px 20px; display:flex; gap:10px; align-items:center; margin-top:8px;">
            <button type="submit"
                    style="background:linear-gradient(135deg,#6366f1,#8b5cf6); color:#fff; border:none; border-radius:9px; padding:12px 28px; font-size:14px; font-weight:700; cursor:pointer; flex-shrink:0;">
                ✅ Save {{ count($draft['ai']['blocks'] ?? []) }} Content Blocks
            </button>
            <a href="#" onclick="event.preventDefault(); document.getElementById('regen-form').submit();"
               style="background:#e0e7ff; color:#3730a3; border:none; border-radius:9px; padding:12px 22px; font-size:13px; font-weight:700; cursor:pointer; text-decoration:none; display:inline-block;">
                ♻️ Regenerate
            </a>
            <form id="regen-form" method="POST" action="{{ route('elearning.ai-lesson-content.generate', [$course, $lesson]) }}" style="display:none;">
                @csrf
                <input type="hidden" name="learning_level" value="{{ $draft['learning_level'] }}">
            </form>
            <form method="POST" action="{{ route('elearning.ai-lesson-content.cancel', [$course, $lesson]) }}" style="margin-left:auto;">
                @csrf
                <button type="submit" style="background:transparent; border:1px solid var(--border); color:var(--muted); border-radius:9px; padding:12px 18px; font-size:13px; cursor:pointer;">
                    Discard
                </button>
            </form>
        </div>

    </form>
</div>
@endsection
