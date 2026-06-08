@extends('layouts.app')
@section('page-title', 'Lesson Builder — ' . $lesson->title)

@section('content')

<style>
/* ── Builder shell ── */
.builder-grid { display:grid; grid-template-columns:360px 1fr; gap:20px; align-items:start; }
@media(max-width:1100px){ .builder-grid { grid-template-columns:1fr; } }

/* ── Block card ── */
.block-card {
    background:#fff; border:1px solid #e5e7eb; border-radius:12px;
    margin-bottom:10px; overflow:hidden;
    box-shadow:0 1px 4px rgba(15,23,42,.05);
    transition:border-color .15s;
}
.block-card:hover { border-color:#cbd5e1; }
.block-card-header {
    display:flex; align-items:center; gap:10px; padding:11px 15px;
    border-bottom:1px solid #f3f4f6; background:#f9fafb;
}
.block-type-badge {
    display:inline-flex; align-items:center; gap:5px;
    padding:3px 9px; border-radius:20px; font-size:10.5px; font-weight:800;
    text-transform:uppercase; letter-spacing:.4px; flex-shrink:0; color:white;
}
.block-title  { font-size:13px; font-weight:700; color:#111827; flex:1; min-width:0;
                white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.block-order  { font-size:11.5px; color:#9ca3af; font-weight:700; flex-shrink:0; }
.block-actions { display:flex; align-items:center; gap:3px; flex-shrink:0; }
.bb { width:28px; height:28px; border-radius:7px; border:none; cursor:pointer;
      display:inline-flex; align-items:center; justify-content:center;
      font-family:inherit; transition:background .12s; text-decoration:none; }
.bb-move   { background:#f3f4f6; color:#6b7280; }
.bb-edit   { background:#dbeafe; color:#1e40af; }
.bb-del    { background:#fee2e2; color:#991b1b; }
.bb:hover  { filter:brightness(.9); }
.block-preview { padding:10px 15px; font-size:12.5px; color:#6b7280; line-height:1.5; }
.block-inactive-tag { font-size:10px; background:#f3f4f6; color:#9ca3af;
                      padding:1px 6px; border-radius:4px; font-weight:700; flex-shrink:0; }

/* ── Empty ── */
.blocks-empty { text-align:center; padding:44px 20px; background:#fff;
                border:2px dashed #e5e7eb; border-radius:12px; color:#9ca3af; }

/* ── Type picker ── */
.type-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(130px,1fr)); gap:9px; margin:14px 0; }
.type-btn  { background:#f8fafc; border:2px solid #e5e7eb; border-radius:10px;
             padding:11px 8px; text-align:center; cursor:pointer; transition:all .14s;
             text-decoration:none; display:block; }
.type-btn:hover { border-color:#6366f1; background:#f0f0ff; }
.tp-emoji { font-size:20px; margin-bottom:5px; display:block; }
.tp-lbl   { font-size:11.5px; font-weight:700; color:#374151; line-height:1.3; }

/* ── Block form ── */
.bform-card  { background:#fff; border:2px solid #4f46e5; border-radius:14px; overflow:hidden; margin-bottom:16px; }
.bform-head  { background:linear-gradient(135deg,#4f46e5,#7c3aed); color:white;
               padding:13px 18px; display:flex; align-items:center; justify-content:space-between; }
.bform-head h4 { margin:0; font-size:14px; font-weight:800; }
.bform-body  { padding:20px; }

/* Form controls inside block forms */
.fi  { width:100%; border:1px solid #e5e7eb; border-radius:8px;
       padding:9px 12px; font-size:13.5px; color:#111827; font-family:inherit;
       background:#fff; box-sizing:border-box; transition:border-color .15s; }
.fi:focus { outline:none; border-color:#6366f1; box-shadow:0 0 0 3px rgba(99,102,241,.12); }
.fl  { display:block; font-size:12px; font-weight:700; color:#374151;
       margin-bottom:5px; margin-top:13px; }
.fl:first-child { margin-top:0; }
.fg2 { display:grid; grid-template-columns:1fr 1fr; gap:12px; }

/* Repeater */
.rep-row   { display:flex; gap:7px; align-items:flex-start; margin-bottom:7px; }
.rep-row .fi { flex:1; }
.btn-rmv   { background:#fee2e2; color:#991b1b; border:none; border-radius:7px;
             padding:9px 10px; cursor:pointer; font-family:inherit; font-size:11px; font-weight:700; flex-shrink:0; }
.btn-addrow{ background:#eff6ff; color:#1e40af; border:1px dashed #93c5fd; border-radius:8px;
             padding:8px 14px; cursor:pointer; font-family:inherit; font-size:12px; font-weight:700;
             width:100%; margin-top:4px; }

/* KC options */
.kc-row  { display:flex; gap:8px; align-items:center; margin-bottom:7px; }
.kc-row .fi { flex:1; }
.kc-chk  { width:17px; height:17px; accent-color:#16a34a; cursor:pointer; flex-shrink:0; }
.kc-hint { font-size:11px; color:#9ca3af; flex-shrink:0; font-weight:600; min-width:30px; }
</style>

<x-flash-message />

{{-- Breadcrumb --}}
<div style="display:flex; align-items:center; gap:7px; font-size:13px; color:#6b7280; margin-bottom:16px;">
    <a href="{{ route('elearning.courses.index') }}" style="color:#1e3a8a; font-weight:600;">Courses</a>
    <span>›</span>
    <a href="{{ route('elearning.lessons.index', $course) }}" style="color:#1e3a8a; font-weight:600;">{{ Str::limit($course->name, 40) }}</a>
    <span>›</span>
    <strong style="color:#111827;">{{ $lesson->title }}</strong>
    <span style="background:#dbeafe; color:#1e40af; padding:2px 8px; border-radius:20px; font-size:11px; font-weight:700; margin-left:4px;">
        Lesson {{ $lesson->lesson_order }}
    </span>
</div>

<div class="builder-grid">

    {{-- ═══ LEFT: Lesson Settings ═══════════════════════════════════ --}}
    <div>

        <div class="card" style="margin-bottom:14px;">
            <div class="card-header" style="background:linear-gradient(135deg,#1e3a8a,#2563eb); color:white; border-radius:12px 12px 0 0;">
                📋 Lesson Settings
            </div>
            <div class="card-body">
                <form action="{{ route('elearning.lessons.update', [$course, $lesson]) }}" method="POST">
                    @csrf @method('PUT')
                    @include('elearning.lessons.form', ['lesson' => $lesson])
                    <div style="display:flex; gap:8px; margin-top:16px;">
                        <button type="submit" class="btn btn-primary btn-sm">Save Settings</button>
                        <a href="{{ route('elearning.lessons.index', $course) }}" class="btn btn-ghost btn-sm">← All Lessons</a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Block summary --}}
        @if($blocks->isNotEmpty())
        <div class="card">
            <div class="card-header">📦 Block Summary</div>
            <div class="card-body" style="padding:12px 16px;">
                @foreach($blocks->groupBy('block_type') as $btype => $bgroup)
                    <div style="display:flex; justify-content:space-between; font-size:13px; padding:4px 0; border-bottom:1px solid #f3f4f6;">
                        <span>{{ \App\Models\LessonBlock::TYPES[$btype]['label'] ?? $btype }}</span>
                        <span class="badge badge-info">{{ $bgroup->count() }}</span>
                    </div>
                @endforeach
                <div style="display:flex; justify-content:space-between; font-size:13px; padding:6px 0; font-weight:700;">
                    <span>Total</span>
                    <span>{{ $blocks->count() }} blocks</span>
                </div>
            </div>
        </div>
        @endif

    </div>

    {{-- ═══ RIGHT: Block Builder ══════════════════════════════════════ --}}
    <div>

        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:14px; flex-wrap:wrap; gap:10px;">
            <h2 style="margin:0; font-size:16px; font-weight:800; color:#111827;">🧱 Content Blocks</h2>
            <a href="{{ route('elearning.lessons.edit', [$course, $lesson]) }}?add_type=picker"
               class="btn btn-primary btn-sm">+ Add Content Block</a>
        </div>

        {{-- Alerts --}}
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
        <div class="bform-card" style="border-color:#1e3a8a;">
            <div class="bform-head" style="background:linear-gradient(135deg,#1e3a8a,#2563eb);">
                <h4>Choose a Block Type</h4>
                <a href="{{ route('elearning.lessons.edit', [$course, $lesson]) }}" style="color:rgba(255,255,255,.7); font-size:13px;">✕ Cancel</a>
            </div>
            <div class="bform-body">
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

        {{-- ── Add Block Form ── --}}
        @if($addType && $addType !== 'picker' && array_key_exists($addType, \App\Models\LessonBlock::TYPES))
            @php $tInfo = \App\Models\LessonBlock::TYPES[$addType]; @endphp
            <div class="bform-card">
                <div class="bform-head">
                    <h4>➕ Add: {{ $tInfo['label'] }}</h4>
                    <a href="{{ route('elearning.lessons.edit', [$course, $lesson]) }}?add_type=picker"
                       style="color:rgba(255,255,255,.7); font-size:13px;">← Types</a>
                </div>
                <div class="bform-body">
                    <form action="{{ route('elearning.blocks.store', [$course, $lesson]) }}" method="POST">
                        @csrf
                        <input type="hidden" name="block_type" value="{{ $addType }}">

                        <label class="fl">Block Title <span style="color:#9ca3af;font-weight:400;">(optional header)</span></label>
                        <input type="text" name="title" class="fi" value="{{ old('title') }}"
                               placeholder="e.g. Core Principles">

                        @include('elearning.lessons.partials.block-fields', ['type' => $addType, 'block' => null])

                        <div style="display:flex; gap:8px; margin-top:18px;">
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
                    <h4>✏️ Edit: {{ $editBlock->getTypeLabel() }}</h4>
                    <a href="{{ route('elearning.lessons.edit', [$course, $lesson]) }}" style="color:rgba(255,255,255,.7); font-size:13px;">✕ Close</a>
                </div>
                <div class="bform-body">
                    <form action="{{ route('elearning.blocks.update', [$course, $lesson, $editBlock]) }}" method="POST">
                        @csrf @method('PUT')
                        <input type="hidden" name="block_type" value="{{ $editBlock->block_type }}">

                        <label class="fl">Block Title</label>
                        <input type="text" name="title" class="fi"
                               value="{{ old('title', $editBlock->title) }}">

                        @include('elearning.lessons.partials.block-fields', ['type' => $editBlock->block_type, 'block' => $editBlock])

                        <div class="fg2" style="margin-top:14px;">
                            <div>
                                <label class="fl">Status</label>
                                <select name="status" class="fi">
                                    <option value="active"   {{ $editBlock->status === 'active'   ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ $editBlock->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div style="display:flex; gap:8px; margin-top:18px;">
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
                    <span class="block-order">{{ $idx + 1 }}</span>
                    <span class="block-type-badge" style="background:{{ $block->getTypeColor() }}">
                        {{ $block->getTypeLabel() }}
                    </span>
                    @if($block->status !== 'active')
                        <span class="block-inactive-tag">INACTIVE</span>
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

                {{-- Preview --}}
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
                <div style="font-size:38px; margin-bottom:12px;">🧱</div>
                <div style="font-size:15px; font-weight:800; color:#374151; margin-bottom:8px;">No content blocks yet</div>
                <p style="font-size:13px; margin:0 0 16px;">Add video, rich text, slides, quizzes and more.</p>
                <a href="{{ route('elearning.lessons.edit', [$course, $lesson]) }}?add_type=picker" class="btn btn-primary">
                    + Add Your First Block
                </a>
            </div>
            @endif
        @endforelse

        @if($blocks->isNotEmpty() && !$addType && !$editBlock)
            <div style="text-align:center; margin-top:10px;">
                <a href="{{ route('elearning.lessons.edit', [$course, $lesson]) }}?add_type=picker"
                   class="btn btn-ghost btn-sm">+ Add Another Block</a>
            </div>
        @endif

    </div>{{-- /right --}}

</div>

@endsection
