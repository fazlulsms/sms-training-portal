@extends('layouts.app')
@section('page-title', 'Lesson Builder — ' . $lesson->title)

@section('content')

<style>
/* ═══════════════════════════════════════════════════
   LESSON BUILDER — Premium UI
═══════════════════════════════════════════════════ */

/* ── Builder two-column layout ───────────────── */
.builder-grid {
    display: grid;
    grid-template-columns: 360px 1fr;
    gap: 20px;
    align-items: start;
}
@media (max-width:1100px) { .builder-grid { grid-template-columns: 1fr; } }

/* ── Breadcrumb bar ──────────────────────────── */
.lb-crumb {
    display: flex; align-items: center; gap: 7px;
    background: var(--surface); border: 1px solid var(--border);
    border-radius: var(--r); padding: 9px 15px;
    font-size: 13px; color: var(--text-muted);
    box-shadow: var(--shadow-sm);
    margin-bottom: 18px; flex-wrap: wrap;
}
.lb-crumb a { color: var(--sms-primary); font-weight: 600; text-decoration: none; }
.lb-crumb a:hover { text-decoration: underline; }
.lb-crumb .sep { color: var(--border); font-size: 18px; line-height: 1; }
.lb-crumb .cur { font-weight: 700; color: var(--text); }
.lb-order-pill {
    background: var(--clr-info-bg); color: var(--clr-info-txt);
    padding: 2px 9px; border-radius: 20px; font-size: 11px; font-weight: 700;
    margin-left: 2px; flex-shrink: 0;
}

/* ── Left panel card header ──────────────────── */
.lp-head {
    background: linear-gradient(135deg, var(--sms-primary) 0%, #2563eb 100%);
    padding: 16px 20px; border-radius: var(--r-xl) var(--r-xl) 0 0;
    display: flex; align-items: center; gap: 12px;
}
.lp-head-icon {
    width: 38px; height: 38px; border-radius: 9px; flex-shrink: 0;
    background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.2);
    display: flex; align-items: center; justify-content: center;
}
.lp-head-title { font-size: 14px; font-weight: 800; color: white; line-height: 1.2; }
.lp-head-sub   { font-size: 11.5px; color: rgba(255,255,255,0.65); margin-top: 2px; }

/* ── Block summary ───────────────────────────── */
.bsumm-row {
    display: flex; justify-content: space-between; align-items: center;
    font-size: 13px; padding: 7px 0; border-bottom: 1px solid var(--border-light);
    color: var(--text-2);
}
.bsumm-row:last-of-type { border-bottom: none; }
.bsumm-total {
    display: flex; justify-content: space-between; align-items: center;
    font-size: 13px; font-weight: 800; color: var(--text);
    padding: 8px 0 0; border-top: 2px solid var(--border); margin-top: 4px;
}

/* ── Right panel top bar ─────────────────────── */
.rp-topbar {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 14px; flex-wrap: wrap; gap: 10px;
}
.rp-title {
    font-size: 15px; font-weight: 800; color: var(--text);
    display: flex; align-items: center; gap: 8px; margin: 0;
}
.rp-count {
    background: var(--border-light); color: var(--text-muted);
    padding: 2px 9px; border-radius: 20px; font-size: 11.5px; font-weight: 700;
}

/* ── Content block card ──────────────────────── */
.block-card {
    background: var(--surface); border: 1px solid var(--border);
    border-radius: var(--r-lg); margin-bottom: 8px; overflow: hidden;
    box-shadow: var(--shadow-sm);
    transition: border-color .15s, box-shadow .15s, transform .12s;
}
.block-card:hover {
    border-color: #c7d2fe; box-shadow: var(--shadow-md); transform: translateY(-1px);
}
.block-card-header {
    display: flex; align-items: center; gap: 9px;
    padding: 11px 14px; background: #fafbfc;
    border-bottom: 1px solid var(--border-light);
}
.block-num {
    width: 22px; height: 22px; border-radius: 50%; flex-shrink: 0;
    background: var(--border-light); color: var(--text-muted);
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 10.5px; font-weight: 800;
}
.block-type-badge {
    display: inline-flex; align-items: center; padding: 2px 9px;
    border-radius: 20px; font-size: 10px; font-weight: 800;
    text-transform: uppercase; letter-spacing: 0.5px; flex-shrink: 0; color: white;
}
.block-title {
    font-size: 13px; font-weight: 700; color: var(--text); flex: 1;
    min-width: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.block-inactive-pill {
    font-size: 10px; background: var(--clr-warning-bg); color: var(--clr-warning-txt);
    padding: 2px 7px; border-radius: 5px; font-weight: 700; flex-shrink: 0;
}
.block-actions { display: flex; align-items: center; gap: 3px; flex-shrink: 0; }
.bb {
    width: 28px; height: 28px; border-radius: 7px; border: none; cursor: pointer;
    display: inline-flex; align-items: center; justify-content: center;
    font-family: inherit; font-size: 13px;
    transition: background .12s, transform .1s; text-decoration: none;
}
.bb:hover { transform: scale(1.1); }
.bb-move { background: var(--border-light); color: var(--text-muted); }
.bb-move:hover { background: var(--border); }
.bb-edit { background: var(--clr-info-bg); color: var(--clr-info-txt); }
.bb-edit:hover { background: #bfdbfe; }
.bb-del  { background: var(--clr-danger-bg); color: var(--clr-danger-txt); }
.bb-del:hover  { background: #fecaca; }

.block-preview {
    padding: 10px 14px 12px; font-size: 12.5px;
    color: var(--text-muted); line-height: 1.55;
}

/* ── Empty state ─────────────────────────────── */
.blocks-empty {
    text-align: center; padding: 52px 28px;
    background: var(--surface); border: 2px dashed var(--border);
    border-radius: var(--r-xl); color: var(--text-muted);
}
.blocks-empty-icon {
    width: 56px; height: 56px; margin: 0 auto 16px;
    background: var(--border-light); border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
}
.blocks-empty h4 { font-size: 15px; font-weight: 800; color: var(--text-2); margin: 0 0 8px; }
.blocks-empty p  { font-size: 13px; margin: 0 0 20px; }

/* ── Type picker ─────────────────────────────── */
.type-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(115px, 1fr));
    gap: 8px; margin: 12px 0 2px;
}
.type-btn {
    background: var(--surface); border: 1.5px solid var(--border);
    border-radius: var(--r-lg); padding: 13px 8px; text-align: center;
    cursor: pointer; text-decoration: none; display: block;
    transition: border-color .14s, background .14s, transform .12s, box-shadow .14s;
}
.type-btn:hover {
    border-color: #818cf8; background: #f5f3ff;
    transform: translateY(-2px); box-shadow: 0 4px 14px rgba(99,102,241,.13);
}
.tp-emoji { font-size: 22px; margin-bottom: 7px; display: block; }
.tp-lbl   { font-size: 11.5px; font-weight: 700; color: var(--text-2); line-height: 1.3; }

/* ── Block form panel (add / edit / picker) ── */
.bform-card {
    background: var(--surface); border: 2px solid #6366f1;
    border-radius: var(--r-xl); overflow: hidden;
    margin-bottom: 16px; box-shadow: var(--shadow-md);
}
.bform-card.bform-picker { border-color: var(--sms-primary); }

.bform-head {
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    color: white; padding: 14px 20px;
    display: flex; align-items: center; justify-content: space-between;
}
.bform-card.bform-picker .bform-head {
    background: linear-gradient(135deg, var(--sms-primary) 0%, #2563eb 100%);
}
.bform-head h4 { margin: 0; font-size: 14px; font-weight: 800; }
.bform-head a  { color: rgba(255,255,255,0.75); font-size: 13px; text-decoration: none; }
.bform-head a:hover { color: white; }
.bform-body { padding: 22px; }

/* Form controls inside block forms */
.fi {
    width: 100%; border: 1px solid var(--border); border-radius: var(--r);
    padding: 9px 12px; font-size: 13.5px; color: var(--text); font-family: inherit;
    background: #fff; box-sizing: border-box; transition: border-color .15s, box-shadow .15s;
}
.fi:focus { outline: none; border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,.12); }
textarea.fi { resize: vertical; }

.fl {
    display: block; font-size: 12px; font-weight: 700;
    color: var(--text-2); margin-bottom: 5px; margin-top: 14px;
}
.fl:first-child { margin-top: 0; }
.fg2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }

/* Repeater rows */
.rep-row { display: flex; gap: 7px; align-items: flex-start; margin-bottom: 7px; }
.rep-row .fi { flex: 1; }
.btn-rmv {
    background: var(--clr-danger-bg); color: var(--clr-danger-txt);
    border: none; border-radius: 7px; padding: 9px 10px;
    cursor: pointer; font-family: inherit; font-size: 11px; font-weight: 700;
    flex-shrink: 0; transition: background .12s;
}
.btn-rmv:hover { background: #fecaca; }
.btn-addrow {
    background: #eff6ff; color: var(--clr-info-txt);
    border: 1px dashed #93c5fd; border-radius: var(--r);
    padding: 8px 14px; cursor: pointer; font-family: inherit;
    font-size: 12px; font-weight: 700; width: 100%; margin-top: 5px;
    transition: background .12s;
}
.btn-addrow:hover { background: #dbeafe; }

/* Knowledge check options */
.kc-row { display: flex; gap: 8px; align-items: center; margin-bottom: 7px; }
.kc-row .fi { flex: 1; }
.kc-chk  { width: 17px; height: 17px; accent-color: #16a34a; cursor: pointer; flex-shrink: 0; }
.kc-hint { font-size: 11px; color: var(--text-light); flex-shrink: 0; font-weight: 600; min-width: 30px; }

/* Form action footer */
.bform-footer {
    display: flex; gap: 8px; margin-top: 20px;
    padding-top: 16px; border-top: 1px solid var(--border);
}
</style>

<x-flash-message />

{{-- ── Breadcrumb ──────────────────────────────────────────────────── --}}
<div class="lb-crumb">
    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="color:var(--text-light);flex-shrink:0;"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
    <a href="{{ route('elearning.courses.index') }}">Courses</a>
    <span class="sep">/</span>
    <a href="{{ route('elearning.lessons.index', $course) }}">{{ Str::limit($course->name, 40) }}</a>
    <span class="sep">/</span>
    <span class="cur">{{ Str::limit($lesson->title, 50) }}</span>
    <span class="lb-order-pill">Lesson {{ $lesson->lesson_order }}</span>
</div>

@if($course->ai_generation_version === 2)
<div class="card" style="margin-bottom:16px;border-left:4px solid #0f766e;">
    <div class="card-body" style="padding:14px 18px;">
        <div style="display:flex;justify-content:space-between;gap:12px;align-items:flex-start;flex-wrap:wrap;">
            <div>
                <div style="font-size:12px;font-weight:800;color:#0f766e;text-transform:uppercase;letter-spacing:.5px;">Knowledge Hub Traceability</div>
                <div style="font-size:12px;color:#64748b;margin-top:3px;">This lesson is permanently grounded in these approved sources.</div>
            </div>
            <a class="btn btn-view btn-sm" href="{{ route('ai.course-generator.blueprint', $course) }}">V2 Quality Review</a>
        </div>
        <div style="display:flex;gap:6px;flex-wrap:wrap;margin-top:10px;">
            @forelse($lesson->knowledgeResources as $source)
                <a class="badge badge-teal" style="text-decoration:none;" target="_blank" href="{{ route('knowledge-hub.show', $source) }}">{{ $source->clause_number ?: $source->title }}</a>
            @empty
                <span class="badge badge-danger">Missing source — this lesson cannot pass V2 quality review</span>
            @endforelse
        </div>
    </div>
</div>
@endif

<div class="builder-grid">

    {{-- ═══ LEFT: Lesson Settings ═════════════════════════════════════ --}}
    <div>

        {{-- Settings card --}}
        <div class="card card-flush" style="border-radius:var(--r-xl); box-shadow:var(--shadow-md); margin-bottom:14px;">
            <div class="lp-head">
                <div class="lp-head-icon">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                </div>
                <div>
                    <div class="lp-head-title">Lesson Settings</div>
                    <div class="lp-head-sub">Title, type &amp; completion rules</div>
                </div>
            </div>
            <div class="card-body" style="padding:22px;">
                <form action="{{ route('elearning.lessons.update', [$course, $lesson]) }}" method="POST">
                    @csrf @method('PUT')
                    @include('elearning.lessons.form', ['lesson' => $lesson])
                    <div style="display:flex; gap:8px; margin-top:18px; padding-top:16px; border-top:1px solid var(--border);">
                        <button type="submit" class="btn btn-primary btn-sm">Save Settings</button>
                        <a href="{{ route('elearning.lessons.index', $course) }}" class="btn btn-ghost btn-sm">← All Lessons</a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Block summary card --}}
        @if($blocks->isNotEmpty())
        <div class="card" style="border-radius:var(--r-xl); box-shadow:var(--shadow-sm);">
            <div class="card-header" style="border-radius:var(--r-xl) var(--r-xl) 0 0;">
                <h3 style="display:flex; align-items:center; gap:8px; margin:0;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="8" height="8" rx="1"/><rect x="13" y="3" width="8" height="8" rx="1"/><rect x="3" y="13" width="8" height="8" rx="1"/><rect x="13" y="13" width="8" height="8" rx="1"/></svg>
                    Block Summary
                </h3>
            </div>
            <div class="card-body" style="padding:14px 18px;">
                @foreach($blocks->groupBy('block_type') as $btype => $bgroup)
                    <div class="bsumm-row">
                        <span>{{ \App\Models\LessonBlock::TYPES[$btype]['label'] ?? $btype }}</span>
                        <span class="badge badge-info">{{ $bgroup->count() }}</span>
                    </div>
                @endforeach
                <div class="bsumm-total">
                    <span>Total</span>
                    <span>{{ $blocks->count() }} block{{ $blocks->count() !== 1 ? 's' : '' }}</span>
                </div>
            </div>
        </div>
        @endif

        @include('elearning.partials.audio-admin-panel', ['audioRecords' => $audioRecords])

    </div>{{-- /left --}}

    {{-- ═══ RIGHT: Block Builder ════════════════════════════════════════ --}}
    <div>

        <div class="rp-topbar">
            <h2 class="rp-title">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="8" height="8" rx="1"/><rect x="13" y="3" width="8" height="8" rx="1"/><rect x="3" y="13" width="8" height="8" rx="1"/><rect x="13" y="13" width="8" height="8" rx="1"/></svg>
                Content Blocks
                @if($blocks->isNotEmpty())
                    <span class="rp-count">{{ $blocks->count() }}</span>
                @endif
            </h2>
            <div style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
                <a href="{{ route('elearning.lessons.preview', [$course, $lesson]) }}"
                   class="btn btn-sm" style="background:#fef3c7; color:#92400e; border:1px solid #fde68a;"
                   target="_blank" title="Preview lesson as a learner">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    Preview
                </a>
                @if(config('ai.enabled', false) && in_array(auth()->user()?->role, ['super_admin', 'admin']))
                <a href="{{ route('elearning.lessons.edit', [$course, $lesson]) }}?ai_generate=1"
                   class="btn btn-sm" style="background:#f0fdf4; color:#15803d; border:1px solid #86efac;"
                   title="Generate lesson content using AI">
                    ✨ Generate with AI
                </a>
                @endif
                <a href="{{ route('elearning.lessons.edit', [$course, $lesson]) }}?add_type=picker"
                   class="btn btn-primary btn-sm">+ Add Content Block</a>
            </div>
        </div>

        {{-- Session alerts --}}
        @if(session('success'))
            <div class="alert alert-success" style="margin-bottom:12px;">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error" style="margin-bottom:12px;">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-error" style="margin-bottom:12px;">{{ $errors->first() }}</div>
        @endif

        {{-- ── Type Picker ── --}}
        @if($addType === 'picker')
        <div class="bform-card bform-picker">
            <div class="bform-head">
                <h4>Choose a Block Type</h4>
                <a href="{{ route('elearning.lessons.edit', [$course, $lesson]) }}">✕ Cancel</a>
            </div>
            <div class="bform-body">
                <p style="font-size:12.5px; color:var(--text-muted); margin:0 0 2px;">Select the type of content to add to this lesson.</p>
                <div class="type-grid">
                    @foreach(\App\Models\LessonBlock::TYPES as $tKey => $tInfo)
                        <a href="{{ route('elearning.lessons.edit', [$course, $lesson]) }}?add_type={{ $tKey }}"
                           class="type-btn">
                            <span class="tp-emoji">@include('elearning.lessons.partials.type-icon', ['type' => $tKey])</span>
                            <div class="tp-lbl">{{ $tInfo['label'] }}</div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- ── AI Generate Form ── --}}
        @if(request('ai_generate') === '1' && config('ai.enabled', false))
        <div class="bform-card" style="border-color:#16a34a; margin-bottom:16px;">
            <div class="bform-head" style="background:linear-gradient(135deg,#14532d 0%,#16a34a 100%);">
                <h4>✨ Generate Lesson Content with AI</h4>
                <a href="{{ route('elearning.lessons.edit', [$course, $lesson]) }}">✕ Cancel</a>
            </div>
            <div class="bform-body">
                <p style="font-size:13px; color:var(--text-muted); margin:0 0 14px;">
                    AI will generate a complete set of content blocks for this lesson using the course and lesson details.
                    Review and edit everything on the next screen before saving.
                </p>
                <form action="{{ route('elearning.ai-lesson-content.generate', [$course, $lesson]) }}" method="POST">
                    @csrf
                    <div class="fg2" style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
                        <div>
                            <label class="fl">Course Level *</label>
                            <select name="learning_level" class="fi">
                                <option value="Awareness">Awareness &nbsp;(500–800 words)</option>
                                <option value="Professional" selected>Professional &nbsp;(800–1,500 words)</option>
                                <option value="Advanced">Advanced &nbsp;(1,000–2,000 words)</option>
                            </select>
                        </div>
                        <div>
                            <label class="fl">Lesson Title (auto-used)</label>
                            <input type="text" class="fi" value="{{ $lesson->title }}" disabled
                                   style="background:#f8fafc; color:var(--text-muted);">
                        </div>
                    </div>
                    <label class="fl" style="margin-top:14px;">
                        Additional Instructions
                        <span style="font-weight:400; color:var(--text-light);">(optional)</span>
                    </label>
                    <textarea name="extra_notes" class="fi" rows="2"
                              placeholder="e.g. Include Bangladesh regulatory context, focus on ISO clause 6.1, add real audit examples…"></textarea>
                    <div class="bform-footer">
                        <button type="submit" class="btn btn-primary btn-sm"
                                onclick="this.disabled=true; this.textContent='Generating…'; this.form.submit();">
                            ✨ Generate Content
                        </button>
                        <a href="{{ route('elearning.lessons.edit', [$course, $lesson]) }}"
                           class="btn btn-ghost btn-sm">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
        @endif

        {{-- ── Add Block Form ── --}}
        @if($addType && $addType !== 'picker' && array_key_exists($addType, \App\Models\LessonBlock::TYPES))
            @php $tInfo = \App\Models\LessonBlock::TYPES[$addType]; @endphp
            <div class="bform-card">
                <div class="bform-head">
                    <h4>Add Block — {{ $tInfo['label'] }}</h4>
                    <a href="{{ route('elearning.lessons.edit', [$course, $lesson]) }}?add_type=picker">← All Types</a>
                </div>
                <div class="bform-body">
                    <form action="{{ route('elearning.blocks.store', [$course, $lesson]) }}" method="POST">
                        @csrf
                        <input type="hidden" name="block_type" value="{{ $addType }}">

                        <label class="fl">Block Title
                            <span style="color:var(--text-light); font-weight:400;">(optional header)</span>
                        </label>
                        <input type="text" name="title" class="fi" value="{{ old('title') }}"
                               placeholder="e.g. Core Principles">

                        @include('elearning.lessons.partials.block-fields', ['type' => $addType, 'block' => null])

                        <div class="bform-footer">
                            <button type="submit" class="btn btn-primary">Add Block</button>
                            <a href="{{ route('elearning.lessons.edit', [$course, $lesson]) }}" class="btn btn-ghost btn-sm">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        {{-- ── Edit Block Form ── --}}
        @if($editBlock)
            <div class="bform-card">
                <div class="bform-head">
                    <h4>Edit — {{ $editBlock->getTypeLabel() }}</h4>
                    <a href="{{ route('elearning.lessons.edit', [$course, $lesson]) }}">✕ Close</a>
                </div>
                <div class="bform-body">
                    <form action="{{ route('elearning.blocks.update', [$course, $lesson, $editBlock]) }}" method="POST">
                        @csrf @method('PUT')
                        <input type="hidden" name="block_type" value="{{ $editBlock->block_type }}">

                        <label class="fl">Block Title</label>
                        <input type="text" name="title" class="fi"
                               value="{{ old('title', $editBlock->title) }}">

                        @include('elearning.lessons.partials.block-fields', ['type' => $editBlock->block_type, 'block' => $editBlock])

                        <div class="fg2" style="margin-top:16px;">
                            <div>
                                <label class="fl">Status</label>
                                <select name="status" class="fi">
                                    <option value="active"   {{ $editBlock->status === 'active'   ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ $editBlock->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <div>
                                <label class="fl">Audio</label>
                                <select name="audio_enabled" class="fi">
                                    <option value="0" {{ !$editBlock->audio_enabled ? 'selected' : '' }}>Disabled — no audio</option>
                                    <option value="1" {{ $editBlock->audio_enabled  ? 'selected' : '' }}>Enabled — generate in Audio panel</option>
                                </select>
                            </div>
                        </div>

                        <div class="bform-footer">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                            <a href="{{ route('elearning.lessons.edit', [$course, $lesson]) }}" class="btn btn-ghost btn-sm">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        {{-- ── Block List ── --}}
        @forelse($blocks as $idx => $block)
            <div class="block-card">
                <div class="block-card-header">
                    <span class="block-num">{{ $idx + 1 }}</span>
                    <span class="block-type-badge" style="background:{{ $block->getTypeColor() }}">
                        {{ $block->getTypeLabel() }}
                    </span>
                    @if($block->status !== 'active')
                        <span class="block-inactive-pill">Inactive</span>
                    @endif
                    @if($block->audio_enabled)
                        @php $bAud = ($blockAudioMap ?? collect())->get($block->id); @endphp
                        <span title="Audio enabled{{ $bAud && $bAud->isReady() ? ' — ready' : ' — not generated' }}"
                              style="font-size:11px;padding:2px 7px;border-radius:5px;font-weight:700;flex-shrink:0;
                              background:{{ $bAud && $bAud->isReady() ? '#dcfce7' : '#fef3c7' }};
                              color:{{ $bAud && $bAud->isReady() ? '#16a34a' : '#92400e' }};">
                            🔊 {{ $bAud && $bAud->isReady() ? 'Audio ready' : 'Audio pending' }}
                        </span>
                    @endif
                    <span class="block-title">{{ $block->title ?: '(no title)' }}</span>

                    <div class="block-actions">
                        @if(!$loop->first)
                            <form method="POST" action="{{ route('elearning.blocks.move-up', [$course, $lesson, $block]) }}" style="margin:0;">
                                @csrf
                                <button type="submit" class="bb bb-move" title="Move Up">↑</button>
                            </form>
                        @endif
                        @if(!$loop->last)
                            <form method="POST" action="{{ route('elearning.blocks.move-down', [$course, $lesson, $block]) }}" style="margin:0;">
                                @csrf
                                <button type="submit" class="bb bb-move" title="Move Down">↓</button>
                            </form>
                        @endif
                        <a href="{{ route('elearning.lessons.edit', [$course, $lesson]) }}?edit_block={{ $block->id }}"
                           class="bb bb-edit" title="Edit">✏</a>
                        <form method="POST" action="{{ route('elearning.blocks.destroy', [$course, $lesson, $block]) }}"
                              style="margin:0;" onsubmit="return confirm('Delete this block?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="bb bb-del" title="Delete">🗑</button>
                        </form>
                    </div>
                </div>

                <div class="block-preview">
                    @switch($block->block_type)
                        @case('rich_text')
                            {!! Str::limit(strip_tags($block->content ?? ''), 120, '…') ?: '<em style="color:#d1d5db;">Empty</em>' !!}
                            @break
                        @case('video')
                            <strong>URL:</strong> {{ Str::limit($block->content ?? '—', 80) }}
                            @break
                        @case('audio')
                            <strong>Audio:</strong> {{ Str::limit($block->content ?? '—', 80) }}
                            @break
                        @case('image')
                            <strong>Image:</strong> {{ Str::limit($block->content ?? '—', 60) }}
                            @if(!empty($block->settings_json['caption'])) · <em>{{ $block->settings_json['caption'] }}</em> @endif
                            @break
                        @case('pdf')
                            <strong>PDF:</strong> {{ Str::limit($block->content ?? '—', 80) }}
                            @break
                        @case('accordion')
                            @php $its = $block->getDecodedContent(); @endphp
                            {{ count($its) }} section{{ count($its) !== 1 ? 's' : '' }}:
                            @foreach(array_slice($its, 0, 3) as $it)
                                <strong>{{ $it['title'] ?? '' }}</strong>{{ !$loop->last ? ', ' : '' }}
                            @endforeach
                            @if(count($its) > 3) +{{ count($its)-3 }} more @endif
                            @break
                        @case('gallery')
                            {{ count($block->getDecodedContent()) }} image(s)
                            @break
                        @case('slides')
                            {{ count($block->getDecodedContent()) }} slide(s)
                            @break
                        @case('download')
                            {{ count($block->getDecodedContent()) }} file(s)
                            @break
                        @case('knowledge_check')
                            @php $kc = $block->getDecodedContent(); @endphp
                            <strong>Q:</strong> {{ Str::limit($kc['question'] ?? '—', 100) }}
                            @break
                        @case('scenario')
                            @php $sc = $block->getDecodedContent(); @endphp
                            {{ Str::limit($sc['text'] ?? '—', 120, '…') }}
                            @break
                        @case('matching')
                            @php $mp = $block->getDecodedContent(); @endphp
                            {{ count($mp['pairs'] ?? []) }} pair(s)
                            @break
                        @default
                            {{ Str::limit($block->content ?? '—', 100) }}
                    @endswitch
                </div>
            </div>
        @empty
            @if(!$addType)
            <div class="blocks-empty">
                <div class="blocks-empty-icon">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="2"><rect x="3" y="3" width="8" height="8" rx="1"/><rect x="13" y="3" width="8" height="8" rx="1"/><rect x="3" y="13" width="8" height="8" rx="1"/><rect x="13" y="13" width="8" height="8" rx="1"/></svg>
                </div>
                <h4>No content blocks yet</h4>
                <p>Add video, rich text, slides, quizzes and more<br>to build this lesson.</p>
                <a href="{{ route('elearning.lessons.edit', [$course, $lesson]) }}?add_type=picker"
                   class="btn btn-primary">+ Add Your First Block</a>
            </div>
            @endif
        @endforelse

        @if($blocks->isNotEmpty() && !$addType && !$editBlock)
            <div style="text-align:center; margin-top:14px;">
                <a href="{{ route('elearning.lessons.edit', [$course, $lesson]) }}?add_type=picker"
                   class="btn btn-ghost btn-sm">+ Add Another Block</a>
            </div>
        @endif

    </div>{{-- /right --}}

</div>{{-- /.builder-grid --}}

@endsection
