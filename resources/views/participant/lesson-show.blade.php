@extends('layouts.learner-lesson')

@section('page-title', $lesson->title)

@section('content')

@php
    $lpMap       = $enrollment->lessonProgress->keyBy('lesson_id');
    $completedN  = $lpMap->where('status', 'completed')->count();
    $totalN      = $lessons->count();
    $pct         = $totalN > 0 ? round(($completedN / $totalN) * 100) : 0;

    $lessonBlocks    = $lesson->blocks;
    $hasBlocks       = $lessonBlocks->isNotEmpty();
    $hasLegacy       = !$hasBlocks && (!empty($lesson->lesson_content) || !empty($lesson->video_url));

    $blockCount      = $hasBlocks ? $lessonBlocks->count() : 0;
    $isCompleted     = $lessonProgress?->isCompleted();
    $isLocked        = $enrollment->access_status !== 'unlocked';
@endphp

<style>
/* ══ LESSON SHELL ══════════════════════════════════════════ */
.ls-shell { display: flex; height: 100%; overflow: hidden; }

/* ══ CONTENT AREA ══════════════════════════════════════════ */
.ls-main {
    flex: 1; min-width: 0;
    display: flex; flex-direction: column;
    overflow: hidden; background: #f4f5f7;
}

/* Scrollable body */
.ls-scroll {
    flex: 1;
    overflow-y: auto;
    overflow-x: hidden;
    scroll-behavior: smooth;
}
.ls-scroll::-webkit-scrollbar { width: 6px; }
.ls-scroll::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 10px; }

/* Content wrapper — generous padding, max-width for comfortable reading */
.ls-content-wrap {
    max-width: 860px;
    margin: 0 auto;
    padding: 32px 36px 120px;
    width: 100%;
}

/* ══ LESSON OVERVIEW CARD ══════════════════════════════════ */
.lesson-overview {
    background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
    border-radius: 16px;
    padding: 28px 32px;
    color: #fff;
    margin-bottom: 32px;
    box-shadow: 0 8px 24px rgba(30,58,138,.22);
    position: relative; overflow: hidden;
}
.lesson-overview::before {
    content: ''; position: absolute;
    top: -40px; right: -40px;
    width: 180px; height: 180px;
    background: rgba(255,255,255,.05); border-radius: 50%;
}
.lo-breadcrumb {
    font-size: 12px; font-weight: 600; opacity: .7;
    margin-bottom: 10px; position: relative; z-index: 1;
    display: flex; align-items: center; gap: 6px;
}
.lo-title {
    font-size: 22px; font-weight: 900;
    margin: 0 0 20px; line-height: 1.3; position: relative; z-index: 1;
}
.lo-meta {
    display: flex; flex-wrap: wrap; gap: 10px 20px;
    position: relative; z-index: 1;
}
.lo-meta-item {
    display: inline-flex; align-items: center; gap: 6px;
    background: rgba(255,255,255,.12);
    padding: 5px 12px; border-radius: 20px;
    font-size: 12.5px; font-weight: 600;
}

/* ══ ALERTS ════════════════════════════════════════════════ */
.ls-alert {
    padding: 13px 18px; border-radius: 10px;
    font-weight: 600; font-size: 14px; margin-bottom: 20px;
    display: flex; align-items: flex-start; gap: 10px;
}
.ls-alert-error   { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
.ls-alert-success { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
.ls-alert-info    { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }

/* ══ CONTENT BLOCKS ════════════════════════════════════════ */
.lb {
    background: #fff;
    border-radius: 14px;
    margin-bottom: 24px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(15,23,42,.06);
    border: 1px solid #e9ecf0;
}

/* Block header — colored left-border accent style */
.lb-head {
    padding: 14px 22px;
    border-bottom: 1px solid #f0f2f5;
    display: flex; align-items: center; gap: 10px;
    background: #fafbfc;
}
.lb-head-icon {
    width: 30px; height: 30px; border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; font-size: 15px;
}
.lh-text  { background: #eff6ff; }
.lh-video { background: #f5f3ff; }
.lh-audio { background: #ecfdf5; }
.lh-image { background: #fdf2f8; }
.lh-gall  { background: #fff7ed; }
.lh-acc   { background: #f0fdf4; }
.lh-pdf   { background: #fff1f2; }
.lh-dl    { background: #f0fdf4; }
.lh-slide { background: #eff6ff; }
.lh-kc    { background: #fffbeb; }
.lh-sc    { background: #faf5ff; }
.lh-match { background: #f0fdfa; }
.lh-legacy{ background: #f8f9fa; }

.lb-head-label {
    font-size: 12px; font-weight: 800;
    text-transform: uppercase; letter-spacing: .6px;
    color: #6b7280;
}
.lb-head-title {
    font-size: 14.5px; font-weight: 700; color: #111827;
    margin-left: 4px;
}

/* Block body */
.lb-body {
    padding: 26px 28px;
    font-size: 16px; line-height: 1.85; color: #374151;
}

/* Rich text block body */
.lb-body.rt-body h1, .lb-body.rt-body h2, .lb-body.rt-body h3 {
    color: #111827; margin-top: 1.5em; margin-bottom: .5em;
}
.lb-body.rt-body p  { margin: 0 0 1.2em; }
.lb-body.rt-body ul, .lb-body.rt-body ol { padding-left: 1.5em; margin: 0 0 1.2em; }
.lb-body.rt-body li { margin-bottom: .4em; }
.lb-body.rt-body strong { color: #111827; }
.lb-body.rt-body code {
    background: #f1f5f9; color: #be185d;
    padding: 2px 6px; border-radius: 4px; font-size: 14px;
}

/* Video */
.video-wrap {
    position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden;
    border-radius: 10px; background: #000;
}
.video-wrap iframe { position: absolute; top:0; left:0; width:100%; height:100%; border:0; }

/* ── Accordion ─────────────────────────────────────────── */
.acc-item { border-bottom: 1px solid #f0f2f5; }
.acc-item:last-child { border-bottom: none; }
.acc-header {
    width: 100%; background: none; border: none; cursor: pointer;
    display: flex; align-items: center; justify-content: space-between; gap: 12px;
    padding: 16px 28px;
    font-size: 15px; font-weight: 700; color: #111827;
    font-family: inherit; text-align: left;
    transition: background .12s;
}
.acc-header:hover { background: #fafbfc; }
.acc-chevron { transition: transform .22s; flex-shrink: 0; color: #9ca3af; }
.acc-item.open .acc-chevron { transform: rotate(180deg); }
.acc-body {
    padding: 6px 28px 22px;
    font-size: 15.5px; color: #4b5563; line-height: 1.8;
}

/* ── Knowledge Check ───────────────────────────────────── */
.kc-question { font-size: 16px; font-weight: 700; color: #111827; margin: 0 0 18px; line-height: 1.55; }
.kc-opt-label {
    display: flex; align-items: center; gap: 12px;
    padding: 13px 16px; border: 1.5px solid #e5e7eb; border-radius: 12px;
    cursor: pointer; margin-bottom: 10px;
    font-size: 15px; font-weight: 600; color: #374151;
    transition: border-color .14s, background .14s;
}
.kc-opt-label:hover { border-color: #93c5fd; background: #f0f9ff; }
.kc-opt-label input { display: none; }
.kc-opt-label:has(input:checked) { border-color: #1e3a8a; background: #eff6ff; color: #1e40af; }
.kc-opt-circle {
    width: 20px; height: 20px; border-radius: 50%; border: 2px solid #d1d5db;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    transition: border-color .14s, background .14s; position: relative;
}
.kc-opt-label:has(input:checked) .kc-opt-circle { background: #1e3a8a; border-color: #1e3a8a; }
.kc-opt-circle::after {
    content: ''; width: 7px; height: 7px; border-radius: 50%;
    background: white; opacity: 0; position: absolute; transition: opacity .14s;
}
.kc-opt-label:has(input:checked) .kc-opt-circle::after { opacity: 1; }
.kc-opt-key {
    width: 26px; height: 26px; border-radius: 7px; background: #f3f4f6; color: #6b7280;
    display: flex; align-items: center; justify-content: center;
    font-size: 12px; font-weight: 800; flex-shrink: 0;
    transition: background .14s;
}
.kc-opt-label:has(input:checked) .kc-opt-key { background: #dbeafe; color: #1e40af; }
.kc-opt-label.correct { border-color: #16a34a !important; background: #f0fdf4 !important; color: #15803d !important; }
.kc-opt-label.wrong   { border-color: #dc2626 !important; background: #fef2f2 !important; color: #991b1b !important; }
.kc-result-pass { background: #dcfce7; color: #166534; padding: 12px 16px; border-radius: 10px; font-size: 14px; font-weight: 700; margin-top: 14px; }
.kc-result-fail { background: #fee2e2; color: #991b1b; padding: 12px 16px; border-radius: 10px; font-size: 14px; font-weight: 700; margin-top: 14px; }

/* ── Slides ─────────────────────────────────────────────── */
.slide-panel { display: none; }
.slide-panel.active { display: block; }
.slide-nav {
    display: flex; align-items: center; justify-content: space-between; gap: 10px;
    margin-top: 18px; padding-top: 18px; border-top: 1px solid #f0f2f5;
}
.slide-counter { font-size: 13px; font-weight: 700; color: #6b7280; }

/* ── Scenario ───────────────────────────────────────────── */
.sc-scenario-text {
    background: #f8fafc; border-left: 4px solid #1e3a8a;
    padding: 16px 20px; border-radius: 0 10px 10px 0;
    font-size: 15.5px; line-height: 1.75; color: #374151;
    margin-bottom: 20px;
}
.sc-opt { margin-bottom: 10px; }
.sc-opt-btn {
    width: 100%; background: #f8fafc; border: 1.5px solid #e5e7eb; border-radius: 12px;
    padding: 14px 16px; cursor: pointer; font-family: inherit;
    font-size: 15px; font-weight: 600; color: #374151;
    text-align: left; display: flex; align-items: center; gap: 12px;
    transition: border-color .14s, background .14s;
}
.sc-opt-btn:hover { border-color: #93c5fd; background: #f0f9ff; }
.sc-opt-btn.selected-correct { border-color: #16a34a; background: #f0fdf4; color: #166534; }
.sc-opt-btn.selected-wrong   { border-color: #dc2626; background: #fef2f2; color: #991b1b; }
.sc-opt-letter {
    width: 28px; height: 28px; border-radius: 50%; background: #e5e7eb; color: #374151;
    display: flex; align-items: center; justify-content: center;
    font-size: 13px; font-weight: 800; flex-shrink: 0;
}
.sc-exp {
    background: #fffbeb; border: 1px solid #fde68a; border-radius: 10px;
    padding: 12px 16px; font-size: 14px; color: #92400e; line-height: 1.65;
    margin-top: 8px;
}

/* ── Matching ────────────────────────────────────────────── */
.match-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
@media(max-width:600px){ .match-grid { grid-template-columns: 1fr; } }
.match-col-header { font-size: 11px; font-weight: 800; color: #6b7280; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 8px; }
.match-term {
    background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 10px;
    padding: 12px 14px; font-size: 14.5px; font-weight: 700; color: #111827;
    margin-bottom: 10px;
}
.match-select {
    width: 100%; border: 1.5px solid #e5e7eb; border-radius: 10px;
    padding: 10px 12px; font-family: inherit; font-size: 14px; color: #374151;
    margin-bottom: 10px; background: #fff; cursor: pointer;
}
.match-select:focus { outline: none; border-color: #6366f1; }

/* ── Resources row ──────────────────────────────────────── */
.resource-row {
    display: flex; align-items: center; justify-content: space-between;
    padding: 12px 16px; border: 1px solid #e9ecf0; border-radius: 10px;
    margin-bottom: 10px; background: #fafbfc; gap: 10px;
}
.resource-row:last-child { margin-bottom: 0; }
.resource-row a { color: #1e3a8a; font-weight: 700; font-size: 14.5px; text-decoration: none; }
.resource-row a:hover { text-decoration: underline; }
.resource-type { font-size: 10.5px; color: #9ca3af; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; }

/* ── Quiz row ────────────────────────────────────────────── */
.quiz-row {
    padding: 20px; border: 1px solid #e9ecf0; border-radius: 12px;
    margin-bottom: 12px; background: #fafbfc;
}
.quiz-name     { font-weight: 800; font-size: 15.5px; color: #111827; margin-bottom: 6px; }
.quiz-meta-line{ font-size: 13px; color: #6b7280; margin-bottom: 14px; line-height: 1.5; }

/* ── Lightbox ────────────────────────────────────────────── */
#lb-overlay {
    display: none; position: fixed; inset: 0; z-index: 9999;
    background: rgba(0,0,0,.9); align-items: center; justify-content: center;
    flex-direction: column; gap: 14px;
}
#lb-overlay.open { display: flex; }
#lb-overlay img { max-width: 90vw; max-height: 82vh; border-radius: 10px; object-fit: contain; }
#lb-caption { color: rgba(255,255,255,.75); font-size: 13px; }
#lb-close {
    position: absolute; top: 18px; right: 22px;
    background: rgba(255,255,255,.1); border: none; color: white;
    width: 36px; height: 36px; border-radius: 50%; font-size: 20px; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
}

/* ── Bottom navigation bar ───────────────────────────────── */
.ls-bottom-bar {
    position: sticky;
    bottom: 0;
    background: #fff;
    border-top: 1px solid #e5e7eb;
    padding: 14px 28px;
    display: flex; align-items: center; justify-content: space-between; gap: 14px;
    flex-wrap: wrap;
    flex-shrink: 0;
    z-index: 20;
    box-shadow: 0 -4px 16px rgba(15,23,42,.06);
}
.ls-bottom-bar.completed-bar { background: #f0fdf4; border-top-color: #bbf7d0; }

.lbb-hint {
    font-size: 13.5px; font-weight: 600; color: #6b7280;
    display: flex; align-items: center; gap: 8px;
}
.completed-bar .lbb-hint { color: #166534; }

.lbb-progress {
    font-size: 11.5px; color: #9ca3af; font-weight: 600;
    display: flex; align-items: center; gap: 6px; white-space: nowrap;
}

.lbb-actions { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }

/* Buttons */
.lbb-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 10px 18px; border-radius: 10px;
    font-weight: 700; font-size: 13.5px; font-family: inherit;
    text-decoration: none; border: none; cursor: pointer; white-space: nowrap;
    transition: background .15s, transform .1s;
}
.lbb-btn:active { transform: scale(.97); }
.btn-complete  { background: #16a34a; color: #fff; }
.btn-complete:hover { background: #15803d; }
.btn-next      { background: #1e3a8a; color: #fff; }
.btn-next:hover{ background: #1d4ed8; }
.btn-prev      { background: #f3f4f6; color: #374151; border: 1px solid #e5e7eb; }
.btn-prev:hover{ background: #e9ecf0; }
.btn-disabled  { background: #e5e7eb; color: #9ca3af; cursor: not-allowed; pointer-events: none; }
.btn-overview  { background: #0f766e; color: #fff; }
.btn-overview:hover { background: #0d9488; }

/* ══ RESPONSIVE ════════════════════════════════════════════ */
@media (max-width: 860px) {
    .ls-content-wrap { padding: 20px 20px 100px; }
    .lo-title { font-size: 19px; }
    .lb-body { padding: 20px 20px; font-size: 15.5px; }
    .acc-header { padding: 14px 20px; font-size: 14.5px; }
    .acc-body   { padding: 6px 20px 18px; }
    .ls-bottom-bar { padding: 12px 16px; }
}

@media (max-width: 500px) {
    .ls-content-wrap { padding: 16px 14px 90px; }
    .lo-title { font-size: 17px; }
    .lo-meta  { gap: 8px 12px; }
    .lo-meta-item { font-size: 12px; padding: 4px 10px; }
    .lb-body { padding: 16px 16px; font-size: 15px; }
    .lbb-btn  { padding: 9px 14px; font-size: 13px; }
    .lesson-overview { padding: 22px 20px; }
}
</style>

<div class="ls-shell">

    {{-- ══ LESSON NAVIGATION DRAWER ══════════════════════════ --}}
    <aside class="ll-nav" id="llNav">
        <div class="ll-nav-header">
            <div class="ll-nav-org">SMS Training Services</div>
            <div class="ll-nav-course">{{ $enrollment->course->name }}</div>
            <div class="ll-nav-prog-track">
                <div class="ll-nav-prog-fill" style="width:{{ $pct }}%"></div>
            </div>
            <div class="ll-nav-prog-label">
                <span>{{ $completedN }} / {{ $totalN }} lessons done</span>
                <span>{{ $pct }}%</span>
            </div>
        </div>

        <nav class="ll-nav-list">
            <div class="ll-nav-section-label">Course Lessons</div>

            @foreach($lessons as $idx => $sLesson)
                @php
                    $slp          = $lpMap->get($sLesson->id);
                    $slDone       = $slp && $slp->status === 'completed';
                    $slCurrent    = $sLesson->id === $lesson->id;
                    $prevDone     = $idx === 0 || ($lpMap->get($lessons[$idx - 1]->id)?->status === 'completed');
                    $slAccessible = $slDone || $slCurrent || $prevDone;
                    $stateClass   = $slCurrent ? 'active' : ($slAccessible ? '' : 'locked');
                    $iconClass    = $slCurrent ? 'li-active' : ($slDone ? 'li-done' : ($slAccessible ? 'li-ready' : 'li-locked'));
                @endphp

                @if($slAccessible && !$slCurrent)
                    <a href="{{ route('participant.lesson.show', [$enrollment->id, $sLesson->id]) }}"
                       class="ll-lesson-item {{ $stateClass }}"
                       onclick="document.getElementById('llNav').classList.remove('nav-open')">
                @elseif($slCurrent)
                    <span class="ll-lesson-item {{ $stateClass }}" style="cursor:default;">
                @else
                    <span class="ll-lesson-item locked">
                @endif

                    <div class="ll-item-icon {{ $iconClass }}">
                        @if($slDone)
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5"><polyline points="20 6 9 17 4 12"/></svg>
                        @elseif(!$slAccessible)
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        @elseif($slCurrent)
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                        @else
                            {{ $idx + 1 }}
                        @endif
                    </div>

                    <div class="ll-item-body">
                        <div class="ll-item-title">{{ $sLesson->title }}</div>
                        <div class="ll-item-meta">
                            @if($sLesson->duration_minutes)
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                {{ $sLesson->duration_minutes }}m
                                ·
                            @endif
                            @if($slDone) ✓ Completed
                            @elseif($slCurrent) ▶ In Progress
                            @elseif(!$slAccessible) 🔒 Locked
                            @else Not started
                            @endif
                        </div>
                    </div>

                @if($slCurrent || !$slAccessible)
                    </span>
                @else
                    </a>
                @endif
            @endforeach
        </nav>
    </aside>

    {{-- Overlay for mobile --}}
    <div class="ll-nav-overlay" id="llNavOverlay" onclick="toggleNav()"></div>

    {{-- ══ MAIN PANEL ════════════════════════════════════════ --}}
    <div class="ls-main">

        {{-- Top bar --}}
        <div class="ll-topbar">
            <div class="ll-topbar-left">
                <button class="ll-toggle-btn" onclick="toggleNav()" title="Toggle lesson list" aria-label="Toggle lesson navigation">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
                    </svg>
                </button>

                <a href="{{ route('participant.elearning-details', $enrollment->id) }}" class="ll-back-btn">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                    My Course
                </a>

                <span class="ll-topbar-title">{{ $lesson->title }}</span>
            </div>

            <div class="ll-topbar-right">
                @if($isCompleted)
                    <span class="ll-status-pill sp-done">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                        Completed
                    </span>
                @elseif($lessonProgress)
                    <span class="ll-status-pill sp-progress">In Progress</span>
                @else
                    <span class="ll-status-pill sp-pending">Not Started</span>
                @endif

                <span class="ll-lesson-counter">{{ $currentIndex + 1 }} / {{ $lessons->count() }}</span>
            </div>
        </div>

        {{-- Scrollable lesson content --}}
        <div class="ls-scroll">
            <div class="ls-content-wrap">

                {{-- Flash messages --}}
                @if(session('error'))
                    <div class="ls-alert ls-alert-error">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:2px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        {{ session('error') }}
                    </div>
                @endif
                @if(session('success'))
                    <div class="ls-alert ls-alert-success">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:2px;"><polyline points="20 6 9 17 4 12"/></svg>
                        {{ session('success') }}
                    </div>
                @endif

                {{-- ── Lesson Overview Card ─────────────────────────── --}}
                <div class="lesson-overview">
                    <div class="lo-breadcrumb">
                        {{ $enrollment->course->name }}
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                        Lesson {{ $currentIndex + 1 }} of {{ $totalN }}
                    </div>
                    <h1 class="lo-title">{{ $lesson->title }}</h1>
                    <div class="lo-meta">
                        @if($lesson->duration_minutes)
                        <span class="lo-meta-item">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            {{ $lesson->duration_minutes }} minutes
                        </span>
                        @endif
                        @if($blockCount > 0)
                        <span class="lo-meta-item">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                            {{ $blockCount }} {{ Str::plural('section', $blockCount) }}
                        </span>
                        @endif
                        @if($lesson->completion_rule === 'pass_quiz')
                        <span class="lo-meta-item">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><circle cx="12" cy="12" r="10"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                            Pass quiz to complete
                            @if($lesson->required_passing_score) ({{ $lesson->required_passing_score }}%) @endif
                        </span>
                        @else
                        <span class="lo-meta-item">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                            Mark complete when done
                        </span>
                        @endif
                        @if($lesson->certificate_eligible ?? true)
                        <span class="lo-meta-item">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg>
                            Required for certificate
                        </span>
                        @endif
                        @if($isCompleted)
                        <span class="lo-meta-item" style="background:rgba(52,211,153,.2); color:#34d399;">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                            Completed
                        </span>
                        @endif
                    </div>
                </div>

                {{-- ══════════════════════════════════════════════════════
                     MULTI-BLOCK CONTENT (new system)
                ══════════════════════════════════════════════════════ --}}
                @if($hasBlocks)
                @foreach($lessonBlocks as $block)
                @switch($block->block_type)

                    {{-- ── Rich Text ─────────────────────────────────── --}}
                    @case('rich_text')
                    <div class="lb">
                        @if($block->title)
                        <div class="lb-head">
                            <div class="lb-head-icon lh-text">📝</div>
                            <span class="lb-head-label">Reading</span>
                            <span class="lb-head-title">{{ $block->title }}</span>
                        </div>
                        @endif
                        <div class="lb-body rt-body">{!! $block->content !!}</div>
                    </div>
                    @break

                    {{-- ── Video ─────────────────────────────────────── --}}
                    @case('video')
                    @php
                        $vUrl = $block->content ?? '';
                        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&\s]+)/', $vUrl, $vm)) {
                            $vUrl = 'https://www.youtube.com/embed/' . $vm[1] . '?rel=0';
                        } elseif (preg_match('/vimeo\.com\/(\d+)/', $vUrl, $vm)) {
                            $vUrl = 'https://player.vimeo.com/video/' . $vm[1];
                        }
                        $vIsEmbed = str_contains($vUrl, '/embed/') || str_contains($vUrl, 'player.vimeo');
                    @endphp
                    <div class="lb">
                        <div class="lb-head">
                            <div class="lb-head-icon lh-video">🎬</div>
                            <span class="lb-head-label">Video</span>
                            <span class="lb-head-title">{{ $block->title ?: 'Video Lesson' }}</span>
                        </div>
                        <div class="lb-body" style="padding: 20px 22px;">
                            @if($vIsEmbed)
                                <div class="video-wrap"><iframe src="{{ $vUrl }}" allowfullscreen loading="lazy"></iframe></div>
                            @else
                                <a href="{{ $block->content }}" target="_blank"
                                   style="display:inline-flex;align-items:center;gap:8px;background:#7c3aed;color:#fff;padding:12px 20px;border-radius:10px;font-weight:700;text-decoration:none;font-size:15px;">
                                    ▶ Open Video
                                </a>
                            @endif
                        </div>
                    </div>
                    @break

                    {{-- ── Audio ─────────────────────────────────────── --}}
                    @case('audio')
                    <div class="lb">
                        <div class="lb-head">
                            <div class="lb-head-icon lh-audio">🎧</div>
                            <span class="lb-head-label">Audio</span>
                            <span class="lb-head-title">{{ $block->title ?: 'Audio Lesson' }}</span>
                        </div>
                        <div class="lb-body">
                            <audio controls style="width:100%; border-radius:10px; outline:none;">
                                <source src="{{ $block->content }}" type="audio/mpeg">
                                <a href="{{ $block->content }}" target="_blank">Download audio</a>
                            </audio>
                        </div>
                    </div>
                    @break

                    {{-- ── Image ─────────────────────────────────────── --}}
                    @case('image')
                    <div class="lb">
                        @if($block->title)
                        <div class="lb-head">
                            <div class="lb-head-icon lh-image">🖼️</div>
                            <span class="lb-head-label">Image</span>
                            <span class="lb-head-title">{{ $block->title }}</span>
                        </div>
                        @endif
                        <div class="lb-body" style="text-align:center; padding: 24px;">
                            <img src="{{ $block->content }}"
                                 alt="{{ $block->settings_json['caption'] ?? $block->title ?? '' }}"
                                 style="max-width:100%; border-radius:10px; cursor:zoom-in; box-shadow:0 2px 12px rgba(15,23,42,.10);"
                                 onclick="openLightbox(this.src, this.alt)">
                            @if(!empty($block->settings_json['caption']))
                                <p style="font-size:13.5px; color:#6b7280; margin-top:10px; font-style:italic;">{{ $block->settings_json['caption'] }}</p>
                            @endif
                        </div>
                    </div>
                    @break

                    {{-- ── Image Gallery ─────────────────────────────── --}}
                    @case('gallery')
                    @php $gItems = $block->getDecodedContent(); @endphp
                    <div class="lb">
                        <div class="lb-head">
                            <div class="lb-head-icon lh-gall">🎨</div>
                            <span class="lb-head-label">Gallery</span>
                            <span class="lb-head-title">{{ $block->title ?: 'Image Gallery' }}</span>
                        </div>
                        <div class="lb-body">
                            <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(200px,1fr)); gap:12px;">
                                @foreach($gItems as $gi)
                                <div style="text-align:center;">
                                    <img src="{{ $gi['url'] }}" alt="{{ $gi['caption'] ?? '' }}"
                                         style="width:100%; border-radius:10px; cursor:zoom-in; object-fit:cover; height:150px; box-shadow:0 2px 8px rgba(15,23,42,.08);"
                                         onclick="openLightbox(this.src, '{{ addslashes($gi['caption'] ?? '') }}')">
                                    @if(!empty($gi['caption']))
                                        <p style="font-size:12.5px; color:#6b7280; margin:6px 0 0; font-style:italic;">{{ $gi['caption'] }}</p>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @break

                    {{-- ── Accordion ─────────────────────────────────── --}}
                    @case('accordion')
                    @php $acItems = $block->getDecodedContent(); @endphp
                    <div class="lb">
                        @if($block->title)
                        <div class="lb-head">
                            <div class="lb-head-icon lh-acc">📂</div>
                            <span class="lb-head-label">Topics</span>
                            <span class="lb-head-title">{{ $block->title }}</span>
                        </div>
                        @endif
                        <div style="padding: 0;">
                            @foreach($acItems as $ai => $item)
                            <div class="acc-item" id="acc-{{ $block->id }}-{{ $ai }}">
                                <button type="button" class="acc-header"
                                        onclick="toggleAcc('acc-{{ $block->id }}-{{ $ai }}')">
                                    <span>{{ $item['title'] ?? '' }}</span>
                                    <svg class="acc-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                                </button>
                                <div class="acc-body" style="display:none;">
                                    {!! nl2br(e($item['body'] ?? '')) !!}
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @break

                    {{-- ── PDF ───────────────────────────────────────── --}}
                    @case('pdf')
                    <div class="lb">
                        <div class="lb-head">
                            <div class="lb-head-icon lh-pdf">📄</div>
                            <span class="lb-head-label">Document</span>
                            <span class="lb-head-title">{{ $block->title ?: 'PDF Document' }}</span>
                        </div>
                        <div class="lb-body">
                            <iframe src="{{ $block->content }}"
                                    style="width:100%; height:520px; border:none; border-radius:10px; background:#f9fafb;">
                            </iframe>
                            @if($block->settings_json['allow_download'] ?? true)
                            <div style="margin-top:12px;">
                                <a href="{{ $block->content }}" target="_blank"
                                   style="display:inline-flex;align-items:center;gap:7px;background:#0f766e;color:#fff;padding:10px 18px;border-radius:9px;font-weight:700;text-decoration:none;font-size:14px;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                    Download PDF
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                    @break

                    {{-- ── Download Resources ────────────────────────── --}}
                    @case('download')
                    @php $dlItems = $block->getDecodedContent(); @endphp
                    <div class="lb">
                        <div class="lb-head">
                            <div class="lb-head-icon lh-dl">📥</div>
                            <span class="lb-head-label">Resources</span>
                            <span class="lb-head-title">{{ $block->title ?: 'Downloads' }} ({{ count($dlItems) }})</span>
                        </div>
                        <div class="lb-body">
                            @foreach($dlItems as $dl)
                            <div class="resource-row">
                                <a href="{{ $dl['url'] ?? '#' }}" target="_blank">{{ $dl['title'] ?? 'Download' }}</a>
                                <span class="resource-type">{{ strtoupper($dl['type'] ?? 'file') }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @break

                    {{-- ── Slides ────────────────────────────────────── --}}
                    @case('slides')
                    @php
                        $slideItems = $block->getDecodedContent();
                        $slideId    = 'slides-' . $block->id;
                    @endphp
                    <div class="lb">
                        <div class="lb-head">
                            <div class="lb-head-icon lh-slide">🖥️</div>
                            <span class="lb-head-label">Slides</span>
                            <span class="lb-head-title">{{ $block->title ?: 'Presentation' }}</span>
                        </div>
                        <div class="lb-body">
                            <div id="{{ $slideId }}">
                                @foreach($slideItems as $si => $slide)
                                <div class="slide-panel {{ $si === 0 ? 'active' : '' }}" data-slide="{{ $si }}">
                                    @if(!empty($slide['image_url']))
                                        <img src="{{ $slide['image_url'] }}" alt="{{ $slide['title'] ?? '' }}"
                                             style="width:100%; max-height:340px; object-fit:contain; border-radius:10px; margin-bottom:16px;">
                                    @endif
                                    @if(!empty($slide['title']))
                                        <h3 style="font-size:20px; font-weight:800; color:#111827; margin:0 0 10px;">{{ $slide['title'] }}</h3>
                                    @endif
                                    @if(!empty($slide['text']))
                                        <div style="font-size:15.5px; color:#374151; line-height:1.8;">{!! $slide['text'] !!}</div>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                            <div class="slide-nav">
                                <button type="button"
                                        style="display:inline-flex;align-items:center;gap:6px;background:#f3f4f6;color:#374151;border:1px solid #e5e7eb;padding:9px 16px;border-radius:9px;font-weight:700;font-size:13.5px;cursor:pointer;font-family:inherit;"
                                        onclick="slidePrev('{{ $slideId }}')">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg> Prev
                                </button>
                                <span class="slide-counter" id="{{ $slideId }}-counter">1 / {{ count($slideItems) }}</span>
                                <button type="button"
                                        style="display:inline-flex;align-items:center;gap:6px;background:#1e3a8a;color:#fff;border:none;padding:9px 16px;border-radius:9px;font-weight:700;font-size:13.5px;cursor:pointer;font-family:inherit;"
                                        onclick="slideNext('{{ $slideId }}', {{ count($slideItems) }})">
                                    Next <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    @break

                    {{-- ── Knowledge Check ───────────────────────────── --}}
                    @case('knowledge_check')
                    @php $kc = $block->getDecodedContent(); @endphp
                    <div class="lb">
                        <div class="lb-head">
                            <div class="lb-head-icon lh-kc">❓</div>
                            <span class="lb-head-label">Knowledge Check</span>
                            <span class="lb-head-title">{{ $block->title ?: 'Quick Quiz' }}</span>
                        </div>
                        <div class="lb-body">
                            <div id="kc-{{ $block->id }}" data-type="{{ $kc['type'] ?? 'single' }}">
                                <p class="kc-question">{{ $kc['question'] ?? '' }}</p>
                                <div>
                                    @foreach($kc['options'] ?? [] as $oi => $opt)
                                    <label class="kc-opt-label" id="kclbl-{{ $block->id }}-{{ $oi }}">
                                        @if(($kc['type'] ?? 'single') === 'multiple')
                                            <input type="checkbox" name="kc_ans_{{ $block->id }}[]" value="{{ $oi }}"
                                                   onchange="kcCheck('kc-{{ $block->id }}')">
                                        @else
                                            <input type="radio" name="kc_ans_{{ $block->id }}" value="{{ $oi }}"
                                                   onchange="kcCheck('kc-{{ $block->id }}')">
                                        @endif
                                        <span class="kc-opt-circle"></span>
                                        <span class="kc-opt-key">{{ chr(65 + $oi) }}</span>
                                        {{ $opt['text'] ?? '' }}
                                    </label>
                                    @endforeach
                                </div>
                                <div class="kc-result" id="kcres-{{ $block->id }}" style="display:none;"></div>
                                <button type="button"
                                        style="display:inline-flex;align-items:center;gap:7px;background:#d97706;color:#fff;border:none;padding:10px 20px;border-radius:9px;font-weight:700;font-size:14px;cursor:pointer;font-family:inherit;margin-top:14px;"
                                        onclick="kcSubmit('kc-{{ $block->id }}',
                                            {{ json_encode(array_map(fn($o,$i)=>['idx'=>$i,'correct'=>$o['correct']??false], $kc['options']??[], array_keys($kc['options']??[]))) }},
                                            '{{ addslashes($kc['explanation'] ?? '') }}')">
                                    Check Answer
                                </button>
                            </div>
                        </div>
                    </div>
                    @break

                    {{-- ── Scenario Exercise ─────────────────────────── --}}
                    @case('scenario')
                    @php $sc = $block->getDecodedContent(); @endphp
                    <div class="lb">
                        <div class="lb-head">
                            <div class="lb-head-icon lh-sc">🎭</div>
                            <span class="lb-head-label">Scenario</span>
                            <span class="lb-head-title">{{ $block->title ?: 'Scenario Exercise' }}</span>
                        </div>
                        <div class="lb-body">
                            <div class="sc-scenario-text">{{ $sc['text'] ?? '' }}</div>
                            <p style="font-size:14px; font-weight:700; color:#374151; margin-bottom:14px;">How would you respond?</p>
                            @foreach($sc['options'] ?? [] as $soi => $sopt)
                            <div class="sc-opt" id="scopt-{{ $block->id }}-{{ $soi }}">
                                <button type="button" class="sc-opt-btn"
                                        onclick="scSelect('{{ $block->id }}', {{ $soi }}, {{ json_encode($sc['options'] ?? []) }})">
                                    <span class="sc-opt-letter">{{ chr(65 + $soi) }}</span>
                                    {{ $sopt['text'] ?? '' }}
                                </button>
                                <div class="sc-exp" id="scexp-{{ $block->id }}-{{ $soi }}" style="display:none;"></div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @break

                    {{-- ── Matching Activity ─────────────────────────── --}}
                    @case('matching')
                    @php
                        $matchData  = $block->getDecodedContent();
                        $pairs      = $matchData['pairs'] ?? [];
                        $matchId    = 'match-' . $block->id;
                        $rightItems = collect($pairs)->pluck('right')->shuffle()->values()->toArray();
                    @endphp
                    <div class="lb">
                        <div class="lb-head">
                            <div class="lb-head-icon lh-match">🔗</div>
                            <span class="lb-head-label">Matching</span>
                            <span class="lb-head-title">{{ $block->title ?: 'Matching Activity' }}</span>
                        </div>
                        <div class="lb-body">
                            <p style="font-size:14px; color:#6b7280; margin-bottom:18px;">Match each term with the correct definition.</p>
                            <div class="match-grid" id="{{ $matchId }}">
                                <div>
                                    <div class="match-col-header">Terms</div>
                                    @foreach($pairs as $pair)
                                    <div class="match-term">{{ $pair['left'] ?? '' }}</div>
                                    @endforeach
                                </div>
                                <div>
                                    <div class="match-col-header">Select Definitions</div>
                                    @foreach($pairs as $pi => $pair)
                                    <select class="match-select" id="{{ $matchId }}-sel-{{ $pi }}"
                                            data-correct="{{ $pair['right'] ?? '' }}"
                                            onchange="checkMatch('{{ $matchId }}', {{ count($pairs) }})">
                                        <option value="">— choose —</option>
                                        @foreach($rightItems as $ri)
                                        <option value="{{ $ri }}">{{ $ri }}</option>
                                        @endforeach
                                    </select>
                                    @endforeach
                                </div>
                            </div>
                            <div id="{{ $matchId }}-result" style="display:none; margin-top:14px; padding:12px 16px; border-radius:10px; font-size:14px; font-weight:700;"></div>
                        </div>
                    </div>
                    @break

                @endswitch
                @endforeach
                @endif

                {{-- ══ LEGACY CONTENT (backward compatible) ══════════════ --}}
                @if($hasLegacy)
                    @if(!empty($lesson->lesson_content))
                    <div class="lb">
                        <div class="lb-head">
                            <div class="lb-head-icon lh-legacy">📋</div>
                            <span class="lb-head-label">Lesson Content</span>
                        </div>
                        <div class="lb-body rt-body">{!! nl2br(e($lesson->lesson_content)) !!}</div>
                    </div>
                    @endif
                    @if(!empty($lesson->video_url))
                    @php
                        $lVUrl = $lesson->video_url;
                        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&\s]+)/', $lVUrl, $m))
                            $lVUrl = 'https://www.youtube.com/embed/' . $m[1] . '?rel=0';
                        $lVIsEmbed = str_contains($lVUrl, '/embed/') || str_contains($lVUrl, 'player.vimeo');
                    @endphp
                    <div class="lb">
                        <div class="lb-head">
                            <div class="lb-head-icon lh-video">🎬</div>
                            <span class="lb-head-label">Video</span>
                        </div>
                        <div class="lb-body" style="padding: 20px 22px;">
                            @if($lVIsEmbed)
                                <div class="video-wrap"><iframe src="{{ $lVUrl }}" allowfullscreen></iframe></div>
                            @else
                                <a href="{{ $lesson->video_url }}" target="_blank"
                                   style="display:inline-flex;align-items:center;gap:8px;background:#7c3aed;color:#fff;padding:12px 20px;border-radius:10px;font-weight:700;text-decoration:none;font-size:15px;">
                                    ▶ Open Video
                                </a>
                            @endif
                        </div>
                    </div>
                    @endif
                @endif

                {{-- Legacy Resources --}}
                @if($lesson->resources->isNotEmpty())
                <div class="lb">
                    <div class="lb-head">
                        <div class="lb-head-icon lh-dl">📎</div>
                        <span class="lb-head-label">Resources</span>
                        <span class="lb-head-title">{{ $lesson->resources->count() }} attached file{{ $lesson->resources->count() !== 1 ? 's' : '' }}</span>
                    </div>
                    <div class="lb-body">
                        @foreach($lesson->resources as $resource)
                        <div class="resource-row">
                            <a href="{{ $resource->external_url ?? asset('storage/' . $resource->file_path) }}" target="_blank">
                                {{ $resource->title ?? 'Download' }}
                            </a>
                            <span class="resource-type">{{ $resource->resource_type ?? 'file' }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Quizzes --}}
                @if($lesson->quizzes->isNotEmpty())
                <div class="lb">
                    <div class="lb-head">
                        <div class="lb-head-icon lh-kc">📝</div>
                        <span class="lb-head-label">Assessment</span>
                        <span class="lb-head-title">Lesson Quiz</span>
                    </div>
                    <div class="lb-body">
                        @foreach($lesson->quizzes as $quiz)
                        @php
                            $bestAttempt  = $quiz->attempts->sortByDesc('score')->first();
                            $quizPassed   = $bestAttempt && $bestAttempt->score >= $quiz->pass_mark;
                            $attemptsUsed = $quiz->attempts->count();
                            $attemptsLeft = $quiz->max_attempt - $attemptsUsed;
                        @endphp
                        <div class="quiz-row">
                            <div class="quiz-name">{{ $quiz->title }}</div>
                            <div class="quiz-meta-line">
                                Passing score: <strong>{{ $quiz->pass_mark }}%</strong>
                                &nbsp;·&nbsp; Max attempts: {{ $quiz->max_attempt }}
                                @if($bestAttempt) &nbsp;·&nbsp; Your best: <strong style="color:{{ $bestAttempt->score >= $quiz->pass_mark ? '#16a34a' : '#dc2626' }};">{{ $bestAttempt->score }}%</strong> @endif
                                @if(!$quizPassed && $attemptsLeft > 0) &nbsp;·&nbsp; {{ $attemptsLeft }} attempt{{ $attemptsLeft !== 1 ? 's' : '' }} left @endif
                            </div>
                            @if($quizPassed)
                                <span style="display:inline-flex;align-items:center;gap:7px;background:#dcfce7;color:#166534;padding:9px 16px;border-radius:9px;font-weight:700;font-size:14px;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                    Passed — {{ $bestAttempt->score }}%
                                </span>
                            @elseif($attemptsLeft <= 0)
                                <span style="background:#fee2e2;color:#991b1b;padding:9px 16px;border-radius:9px;font-weight:700;font-size:14px;display:inline-block;">No attempts remaining</span>
                            @else
                                <a href="{{ route('participant.quiz.start', ['enrollment' => $enrollment->id, 'quiz' => $quiz->id]) }}"
                                   style="display:inline-flex;align-items:center;gap:7px;background:#d97706;color:#fff;padding:10px 20px;border-radius:9px;font-weight:700;font-size:14px;text-decoration:none;">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                                    {{ $bestAttempt ? 'Retake Quiz' : 'Start Quiz' }}
                                </a>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Empty state --}}
                @if(!$hasBlocks && !$hasLegacy && $lesson->resources->isEmpty() && $lesson->quizzes->isEmpty())
                <div style="text-align:center; padding:60px 20px; background:#fff; border-radius:16px; border: 1px solid #e9ecf0;">
                    <div style="font-size:48px; margin-bottom:14px;">📄</div>
                    <div style="font-size:16px; font-weight:700; color:#6b7280;">No content has been added to this lesson yet.</div>
                    <div style="font-size:14px; color:#9ca3af; margin-top:6px;">Please check back later or contact your administrator.</div>
                </div>
                @endif

            </div>{{-- /.ls-content-wrap --}}

            {{-- ══ STICKY BOTTOM BAR ════════════════════════════════ --}}
            <div class="ls-bottom-bar {{ $isCompleted ? 'completed-bar' : '' }}">

                <div class="lbb-hint">
                    @if($isCompleted)
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                        Lesson completed!
                    @elseif($lesson->quizzes->isNotEmpty() && !$quizzesPassed)
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;"><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><circle cx="12" cy="12" r="10"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                        Pass the quiz to complete this lesson
                    @else
                        Mark this lesson as complete when you're ready.
                    @endif
                    <span class="lbb-progress">
                        {{ $completedN }}/{{ $totalN }} lessons &nbsp;·&nbsp; {{ $pct }}%
                    </span>
                </div>

                <div class="lbb-actions">
                    @if($previousLesson)
                        <a href="{{ route('participant.lesson.show', [$enrollment->id, $previousLesson->id]) }}"
                           class="lbb-btn btn-prev">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                            Previous
                        </a>
                    @endif

                    @if(!$isCompleted)
                        @if($quizzesPassed || $lesson->quizzes->isEmpty())
                            <form method="POST" action="{{ route('participant.lesson.complete', [$enrollment->id, $lesson->id]) }}" style="margin:0;">
                                @csrf
                                <button type="submit" class="lbb-btn btn-complete">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                    Mark Complete
                                </button>
                            </form>
                        @else
                            <span class="lbb-btn btn-disabled">Complete Quiz First</span>
                        @endif
                    @endif

                    @if($isCompleted && $nextLesson)
                        <a href="{{ route('participant.lesson.show', [$enrollment->id, $nextLesson->id]) }}"
                           class="lbb-btn btn-next">
                            Next Lesson
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                        </a>
                    @elseif($isCompleted && !$nextLesson)
                        <a href="{{ route('participant.elearning-details', $enrollment->id) }}"
                           class="lbb-btn btn-overview">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                            Course Overview
                        </a>
                    @elseif(!$isCompleted && $nextLesson)
                        <span class="lbb-btn btn-disabled">
                            Next — Complete first
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        </span>
                    @endif
                </div>

            </div>{{-- /.ls-bottom-bar --}}

        </div>{{-- /.ls-scroll --}}

    </div>{{-- /.ls-main --}}

</div>{{-- /.ls-shell --}}

{{-- Lightbox --}}
<div id="lb-overlay" onclick="if(event.target===this)closeLightbox()">
    <button id="lb-close" onclick="closeLightbox()">✕</button>
    <img id="lb-img" src="" alt="">
    <div id="lb-caption"></div>
</div>

<script>
// ── Nav toggle ───────────────────────────────────────────
const llNav     = document.getElementById('llNav');
const llOverlay = document.getElementById('llNavOverlay');
const isMobile  = () => window.innerWidth <= 860;

function toggleNav() {
    if (isMobile()) {
        llNav.classList.toggle('nav-open');
        llOverlay.classList.toggle('show');
    } else {
        llNav.classList.toggle('nav-collapsed');
    }
}

// Close overlay on resize to desktop
window.addEventListener('resize', () => {
    if (!isMobile()) {
        llNav.classList.remove('nav-open');
        llOverlay.classList.remove('show');
    }
});

// ── Lightbox ──────────────────────────────────────────────
function openLightbox(src, caption) {
    document.getElementById('lb-img').src = src;
    document.getElementById('lb-caption').textContent = caption || '';
    document.getElementById('lb-overlay').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeLightbox() {
    document.getElementById('lb-overlay').classList.remove('open');
    document.body.style.overflow = '';
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeLightbox(); });

// ── Accordion ─────────────────────────────────────────────
function toggleAcc(id) {
    const item = document.getElementById(id);
    const body = item.querySelector('.acc-body');
    const open = item.classList.toggle('open');
    body.style.display = open ? 'block' : 'none';
}

// ── Slides ────────────────────────────────────────────────
function slidePrev(id) {
    const panels = document.getElementById(id).querySelectorAll('.slide-panel');
    let cur = [...panels].findIndex(p => p.classList.contains('active'));
    if (cur > 0) {
        panels[cur].classList.remove('active');
        panels[cur - 1].classList.add('active');
        updateSlideCounter(id, cur - 1, panels.length);
    }
}
function slideNext(id, total) {
    const panels = document.getElementById(id).querySelectorAll('.slide-panel');
    let cur = [...panels].findIndex(p => p.classList.contains('active'));
    if (cur < panels.length - 1) {
        panels[cur].classList.remove('active');
        panels[cur + 1].classList.add('active');
        updateSlideCounter(id, cur + 1, panels.length);
    }
}
function updateSlideCounter(id, cur, total) {
    const el = document.getElementById(id + '-counter');
    if (el) el.textContent = (cur + 1) + ' / ' + total;
}

// ── Knowledge Check ───────────────────────────────────────
function kcCheck() {}
function kcSubmit(id, correctMap, explanation) {
    const wrap   = document.getElementById(id);
    const type   = wrap.dataset.type;
    let userAnswers = [];

    if (type === 'multiple') {
        wrap.querySelectorAll('input[type=checkbox]:checked').forEach(cb => userAnswers.push(parseInt(cb.value)));
    } else {
        const radio = wrap.querySelector('input[type=radio]:checked');
        if (radio) userAnswers.push(parseInt(radio.value));
    }

    if (!userAnswers.length) { alert('Please select an answer first.'); return; }

    const correctIndices = correctMap.filter(o => o.correct).map(o => o.idx);
    const isCorrect = correctIndices.length === userAnswers.length &&
                      correctIndices.every(i => userAnswers.includes(i));

    wrap.querySelectorAll('.kc-opt-label').forEach((lbl, i) => {
        lbl.querySelector('input').disabled = true;
        if (correctIndices.includes(i)) lbl.classList.add('correct');
        else if (userAnswers.includes(i)) lbl.classList.add('wrong');
    });

    const resEl = wrap.querySelector('.kc-result');
    resEl.style.display = 'block';
    resEl.className = 'kc-result ' + (isCorrect ? 'kc-result-pass' : 'kc-result-fail');
    resEl.innerHTML = (isCorrect ? '✅ Correct!' : '❌ Incorrect.')
                    + (explanation ? ' <span style="font-weight:400;">' + explanation + '</span>' : '');
    wrap.querySelector('button[onclick^="kcSubmit"]').style.display = 'none';
}

// ── Scenario ──────────────────────────────────────────────
function scSelect(blockId, idx, options) {
    document.querySelectorAll(`[id^="scopt-${blockId}-"] .sc-opt-btn`).forEach(btn => { btn.disabled = true; });
    options.forEach((opt, i) => {
        const btn = document.querySelector(`#scopt-${blockId}-${i} .sc-opt-btn`);
        const exp = document.getElementById(`scexp-${blockId}-${i}`);
        if (btn && i === idx) btn.classList.add(opt.correct ? 'selected-correct' : 'selected-wrong');
        if (exp && opt.explanation) { exp.style.display = 'block'; exp.textContent = opt.explanation; }
    });
}

// ── Matching ──────────────────────────────────────────────
function checkMatch(id, total) {
    let correct = 0;
    for (let i = 0; i < total; i++) {
        const sel = document.getElementById(id + '-sel-' + i);
        if (!sel) continue;
        if (sel.value === sel.dataset.correct) {
            sel.style.borderColor = '#16a34a'; sel.style.background = '#f0fdf4'; correct++;
        } else if (sel.value) {
            sel.style.borderColor = '#e5e7eb'; sel.style.background = '';
        }
    }
    const res = document.getElementById(id + '-result');
    if (res && correct === total) {
        res.style.display = 'block'; res.style.color = '#166534';
        res.style.background = '#dcfce7'; res.style.padding = '12px 16px';
        res.style.borderRadius = '10px';
        res.textContent = '✅ All ' + total + ' pairs matched correctly!';
    }
}
</script>

@endsection
