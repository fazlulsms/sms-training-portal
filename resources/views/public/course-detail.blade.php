@extends('layouts.public')

@section('page-title', $course->name)
@section('seo-title', $course->name . ' — SMS Training Academy')
@section('seo-desc', $course->short_description ?? Str::limit(strip_tags($course->description ?? ''), 160))

@section('content')
<style>
.cd-hero {
    background: linear-gradient(135deg,#0f172a 0%,#042C53 100%);
    padding:48px 0; color:#fff;
}
.cd-hero-inner { display:grid; grid-template-columns:1fr 380px; gap:40px; align-items:start; }
@media(max-width:900px){ .cd-hero-inner{grid-template-columns:1fr;} .cd-enroll-card{order:-1;} }

.cd-breadcrumb { font-size:13px; opacity:.6; margin-bottom:16px; display:flex; align-items:center; gap:6px; flex-wrap:wrap; }
.cd-breadcrumb a { color:inherit; text-decoration:none; }
.cd-title { font-size:32px; font-weight:900; margin:0 0 14px; line-height:1.2; }
@media(max-width:768px){ .cd-title{font-size:24px;} }
.cd-subtitle { font-size:16px; opacity:.8; line-height:1.7; margin:0 0 24px; }
.cd-meta-row { display:flex; flex-wrap:wrap; gap:14px 22px; margin-bottom:18px; }
.cd-meta-item { display:inline-flex; align-items:center; gap:7px; font-size:13.5px; opacity:.85; font-weight:600; }
.cd-badges { display:flex; gap:8px; flex-wrap:wrap; }

/* Sticky enroll card */
.cd-enroll-card {
    background:#fff; border-radius:16px; box-shadow:0 12px 40px rgba(0,0,0,.25);
    overflow:hidden; position:sticky; top:80px;
}
.cd-enroll-card-banner { height:180px; overflow:hidden; background:#042C53; display:flex; align-items:center; justify-content:center; font-size:56px; }
.cd-enroll-card-banner img { width:100%; height:100%; object-fit:cover; }
.cd-enroll-card-body { padding:22px; }
.cd-enroll-price { font-size:26px; font-weight:900; color:#042C53; margin-bottom:4px; }
.cd-enroll-price small { font-size:13px; color:#6b7280; font-weight:500; }
.cd-enroll-divider { border:none; border-top:1px solid #f0f2f5; margin:16px 0; }
.cd-enroll-btn {
    display:block; background:linear-gradient(135deg,#042C53,#378ADD); color:#fff;
    padding:14px; border-radius:12px; text-align:center; font-weight:800; font-size:15px;
    text-decoration:none; margin-bottom:10px; transition:opacity .14s;
}
.cd-enroll-btn:hover { opacity:.9; }
.cd-enroll-feature { font-size:13px; color:#374151; display:flex; align-items:center; gap:8px; margin-bottom:8px; }
.cd-enroll-feature svg { flex-shrink:0; color:#16a34a; }

/* ── Premium Tab Bar ─────────────────────────────────────────── */
.cd-tabs-wrap {
    background: #fff;
    border: 1px solid #e9ecf0;
    border-radius: 14px;
    padding: 6px;
    margin-bottom: 28px;
    display: flex;
    gap: 2px;
    overflow-x: auto;
    scrollbar-width: none;
    box-shadow: 0 1px 4px rgba(15,23,42,.05);
    position: relative;
}
@media(max-width:900px){
    .cd-tabs-wrap::after {
        content: '';
        position: absolute;
        right: 0; top: 0; bottom: 0;
        width: 24px;
        background: linear-gradient(to right, transparent, #fff);
        pointer-events: none;
        border-radius: 0 14px 14px 0;
    }
}
.cd-tabs-wrap::-webkit-scrollbar { display: none; }

.cd-tab {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 10px 18px; font-size: 13px; font-weight: 700; color: #6b7280;
    cursor: pointer; white-space: nowrap; border: none; border-radius: 10px;
    background: transparent; font-family: inherit;
    transition: all .15s; flex-shrink: 0;
}
.cd-tab svg { opacity: .6; }
.cd-tab:hover { color: #042C53; background: #f0f4ff; }
.cd-tab:hover svg { opacity: 1; }
.cd-tab.active {
    color: #042C53;
    background: linear-gradient(135deg, #eff6ff, #dbeafe);
    box-shadow: 0 1px 4px rgba(30,58,138,.12);
    font-weight: 800;
}
.cd-tab.active svg { opacity: 1; }

.cd-tab-panel { display:none; }
.cd-tab-panel.active { display:block; }

/* ── Panel heading ───────────────────────────────────────────── */
.cd-panel-heading {
    font-size: 20px; font-weight: 900; color: #111827;
    margin: 0 0 22px; display: flex; align-items: center; gap: 10px;
}
.cd-panel-heading::before {
    content: '';
    display: block; width: 4px; height: 22px;
    background: linear-gradient(135deg, #042C53, #378ADD);
    border-radius: 2px; flex-shrink: 0;
}

/* ── Prose content ───────────────────────────────────────────── */
.cd-prose { font-size: 15px; line-height: 1.85; color: #374151; }
.cd-prose h2, .cd-prose h3 { color: #111827; }
.cd-prose ul, .cd-prose ol { padding-left: 1.4em; }
.cd-prose li { margin-bottom: .5em; }

/* ── Checklist items (objectives / who should attend) ────────── */
.checklist { list-style: none; padding: 0; margin: 0; }
.checklist li {
    display: flex; align-items: flex-start; gap: 12px;
    padding: 11px 14px; border-radius: 10px; margin-bottom: 6px;
    background: #f8fafc; border: 1px solid #f0f2f5;
    font-size: 14.5px; color: #374151; line-height: 1.5;
}
.checklist li:hover { background: #f0f6ff; border-color: #bfdbfe; }
.checklist-icon { color: #16a34a; flex-shrink: 0; margin-top: 1px; }

/* ── Course Outline — Module/Lesson accordion ────────────────── */
.outline-module {
    background: #fff;
    border: 1px solid #e9ecf0;
    border-radius: 12px;
    margin-bottom: 10px;
    overflow: hidden;
    transition: box-shadow .15s;
}
.outline-module:hover { box-shadow: 0 4px 16px rgba(15,23,42,.08); }

.outline-mod-header {
    display: flex; align-items: center; gap: 14px;
    padding: 15px 20px;
    background: linear-gradient(135deg, #0f172a 0%, #042C53 100%);
    cursor: pointer; user-select: none;
}
.outline-mod-num {
    width: 30px; height: 30px; border-radius: 8px;
    background: rgba(255,255,255,.15); border: 1px solid rgba(255,255,255,.25);
    display: flex; align-items: center; justify-content: center;
    font-size: 12px; font-weight: 900; color: #fff; flex-shrink: 0;
}
.outline-mod-title {
    font-size: 14.5px; font-weight: 800; color: #fff; flex: 1; line-height: 1.35;
}
.outline-mod-toggle {
    color: rgba(255,255,255,.6); flex-shrink: 0;
    transition: transform .2s;
}
.outline-module.open .outline-mod-toggle { transform: rotate(180deg); }

.outline-lessons {
    display: none; padding: 8px 12px 12px;
}
.outline-module.open .outline-lessons { display: block; }

.outline-lesson {
    display: flex; align-items: flex-start; gap: 10px;
    padding: 9px 10px; border-radius: 8px; margin-bottom: 3px;
    font-size: 14px; color: #374151; line-height: 1.45;
    transition: background .12s;
}
.outline-lesson:hover { background: #f0f6ff; }
.outline-lesson-badge {
    font-size: 10.5px; font-weight: 800; color: #378ADD;
    background: #eff6ff; padding: 2px 7px; border-radius: 5px;
    flex-shrink: 0; margin-top: 2px; white-space: nowrap; letter-spacing: .2px;
}
.outline-plain-line {
    display: flex; align-items: flex-start; gap: 10px;
    padding: 8px 10px; font-size: 14px; color: #374151; line-height: 1.45;
}
.outline-dot {
    width: 6px; height: 6px; border-radius: 50%;
    background: #378ADD; flex-shrink: 0; margin-top: 6px;
}

/* Schedule table */
.schedule-table { width:100%; border-collapse:collapse; font-size:14px; }
.schedule-table th { background:#f8fafc; padding:12px 14px; text-align:left; font-size:11.5px; font-weight:800; text-transform:uppercase; letter-spacing:.5px; color:#6b7280; border-bottom:2px solid #e9ecf0; }
.schedule-table td { padding:14px; border-bottom:1px solid #f0f2f5; color:#374151; vertical-align:middle; }
.schedule-table tr:hover td { background:#f8fafc; }

/* Trainer card */
.trainer-card { display:flex; gap:20px; align-items:flex-start; background:#f8fafc; border-radius:14px; padding:22px; }
.trainer-avatar { width:72px; height:72px; border-radius:50%; object-fit:cover; background:#dbeafe; display:flex; align-items:center; justify-content:center; font-size:28px; font-weight:900; color:#042C53; flex-shrink:0; }
.trainer-avatar img { width:72px; height:72px; border-radius:50%; object-fit:cover; }
.trainer-name { font-size:18px; font-weight:800; color:#111827; margin:0 0 4px; }
.trainer-title { font-size:13.5px; color:#6b7280; margin:0 0 10px; }
.trainer-bio { font-size:14px; color:#374151; line-height:1.7; margin:0; }

/* Right sidebar */
.cd-layout { display:grid; grid-template-columns:1fr 340px; gap:40px; padding:40px 0 60px; }
@media(max-width:900px){ .cd-layout{grid-template-columns:1fr;} }
</style>

{{-- Course Hero --}}
<div class="cd-hero">
    <div class="pub-container">
        <div class="cd-hero-inner">
            <div>
                <div class="cd-breadcrumb">
                    <a href="{{ route('public.home') }}">Home</a>
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                    <a href="{{ route('public.courses') }}">Courses</a>
                    @if($course->category)
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                    <a href="{{ route('public.courses') }}?category={{ urlencode($course->category) }}">{{ $course->category }}</a>
                    @endif
                </div>

                <h1 class="cd-title">{{ $course->name }}</h1>
                <p class="cd-subtitle">{{ $course->short_description ?? Str::limit(strip_tags($course->description), 200) }}</p>

                <div class="cd-meta-row">
                    @if($course->duration)
                    <span class="cd-meta-item"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> {{ $course->duration }}</span>
                    @endif
                    <span class="cd-meta-item">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        {{ $course->certificate_type ?? 'Certificate of Completion' }}
                    </span>
                    @if($course->language)
                    <span class="cd-meta-item">🌐 {{ $course->language }}</span>
                    @endif
                    @if($course->cpd_hours)
                    <span class="cd-meta-item">⭐ {{ $course->cpd_hours }} CPD Hours</span>
                    @endif
                </div>

                <div class="cd-badges">
                    <span class="delivery-badge {{ match($course->delivery_type ?? '') { 'eLearning'=>'db-elearning', 'Hybrid'=>'db-hybrid', default=>'db-instructor' } }}">
                        {{ $course->delivery_type ?? 'Instructor-Led' }}
                    </span>
                    @if($course->category)<span class="tag-badge">{{ $course->category }}</span>@endif
                </div>
            </div>

            {{-- Enroll card (desktop right column) --}}
            <div class="cd-enroll-card">
                <div class="cd-enroll-card-banner">
                    @if($course->banner_image)
                        <img src="{{ asset('storage/' . $course->banner_image) }}" alt="{{ $course->name }}">
                    @else 🎓 @endif
                </div>
                <div class="cd-enroll-card-body">
                    @if($course->delivery_type === 'eLearning')
                        {{-- eLearning price --}}
                        @php $elPrice = $course->public_price ?? $course->course_fee; @endphp
                        @if($elPrice)
                        <div class="cd-enroll-price">
                            BDT {{ number_format($elPrice) }}
                            <small>one-time</small>
                        </div>
                        @endif
                        <a href="{{ route('elearning.public.register', $course->id) }}" class="cd-enroll-btn">🎓 Enroll Online</a>
                        <hr class="cd-enroll-divider">
                        <div class="cd-enroll-feature"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> Self-paced learning</div>
                        @if($course->access_days)
                        <div class="cd-enroll-feature"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> {{ $course->access_days }}-day access</div>
                        @endif
                        <div class="cd-enroll-feature"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> Certificate of completion</div>
                        @if($course->cpd_hours)
                        <div class="cd-enroll-feature"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> {{ $course->cpd_hours }} CPD Hours</div>
                        @endif
                    @else
                        {{-- ILT price --}}
                        @if($course->min_fee)
                        <div class="cd-enroll-price">
                            BDT {{ number_format($course->min_fee) }}
                            @if($course->min_fee != $course->max_fee) – {{ number_format($course->max_fee) }} @endif
                            <small>per participant</small>
                        </div>
                        @endif
                        @if($course->publicSchedules->count())
                        <a href="#schedules" class="cd-enroll-btn">📅 View Open Schedules</a>
                        @else
                        <a href="mailto:training@smscert.com" class="cd-enroll-btn">📧 Contact to Enroll</a>
                        @endif
                        <hr class="cd-enroll-divider">
                        <div class="cd-enroll-feature"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> Professional certificate</div>
                        <div class="cd-enroll-feature"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> Expert instructors</div>
                        <div class="cd-enroll-feature"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> Flexible delivery modes</div>
                        @if($course->cpd_hours)
                        <div class="cd-enroll-feature"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> {{ $course->cpd_hours }} CPD Hours</div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Course Body --}}
<div class="pub-container">
<div class="cd-layout">

    {{-- Main Content --}}
    <div>
        {{-- Premium Tab Bar --}}
        <div class="cd-tabs-wrap">
            <button class="cd-tab active" onclick="showTab('overview', this)">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                Overview
            </button>
            @if($course->learning_objectives)
            <button class="cd-tab" onclick="showTab('objectives', this)">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg>
                Learning Objectives
            </button>
            @endif
            @if($course->course_outline)
            <button class="cd-tab" onclick="showTab('outline', this)">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                Course Outline
            </button>
            @endif
            @if($course->who_should_attend || $course->prerequisites)
            <button class="cd-tab" onclick="showTab('audience', this)">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                Who Should Attend
            </button>
            @endif
            @if($course->publicSchedules->count())
            <button class="cd-tab" onclick="showTab('schedules', this)" id="tab-schedules">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                Schedule &amp; Fees
            </button>
            @endif
            @if($course->testimonials->count())
            <button class="cd-tab" onclick="showTab('reviews', this)">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                Reviews <span style="background:#042C53;color:#fff;font-size:10px;padding:1px 6px;border-radius:10px;margin-left:2px;">{{ $course->testimonials->count() }}</span>
            </button>
            @endif
        </div>

        {{-- Overview tab --}}
        <div class="cd-tab-panel active" id="tab-panel-overview">
            @if($course->full_description ?? $course->description)
            <div class="cd-prose">{!! nl2br(e($course->full_description ?? $course->description)) !!}</div>
            @endif
        </div>

        {{-- Objectives tab --}}
        @if($course->learning_objectives)
        <div class="cd-tab-panel" id="tab-panel-objectives">
            <div class="cd-panel-heading">What You Will Learn</div>
            <ul class="checklist">
                @foreach(array_filter(array_map('trim', explode("\n", $course->learning_objectives))) as $obj)
                @php $obj = ltrim($obj, '•-* '); @endphp
                @if($obj)
                <li>
                    <svg class="checklist-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    {{ $obj }}
                </li>
                @endif
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Outline tab — parsed module/lesson accordion --}}
        @if($course->course_outline)
        <div class="cd-tab-panel" id="tab-panel-outline">
            <div class="cd-panel-heading">Course Outline</div>
            @php
                $outlineLines = array_filter(array_map('trim', explode("\n", $course->course_outline)));
                $modules = [];
                $currentMod = null;
                $modIndex = 0;

                foreach ($outlineLines as $line) {
                    if (empty($line)) continue;
                    $isModule = preg_match('/^(module|unit|section|part|topic)\s*\d*/i', $line)
                        || (
                            !preg_match('/^[•\-\*\d\.]/u', $line)
                            && !preg_match('/^lesson\s/i', $line)
                            && mb_strlen($line) > 4
                            && mb_strlen($line) < 120
                            && empty($modules) === false
                                ? !str_contains(strtolower($line), 'lesson')
                                : true
                        );

                    // Simpler heuristic: lines starting without bullet/number → module header
                    $firstChar = mb_substr($line, 0, 1);
                    $startsWithBullet = in_array($firstChar, ['•', '-', '*']) || preg_match('/^\d+[\.\)]\s/', $line);
                    $isLessonKeyword = preg_match('/^(lesson|topic|unit|activity)\s+\d/i', $line);
                    $isModuleKeyword = preg_match('/^(module|section|part|chapter|week)\s*\d*/i', $line);

                    if ($isModuleKeyword || (!$startsWithBullet && !$isLessonKeyword)) {
                        $modIndex++;
                        $modules[] = ['num' => $modIndex, 'title' => $line, 'lessons' => []];
                        $currentMod = count($modules) - 1;
                    } else {
                        $cleanLine = ltrim($line, '•-* ');
                        if ($currentMod === null) {
                            $modules[] = ['num' => ++$modIndex, 'title' => null, 'lessons' => [$cleanLine]];
                            $currentMod = 0;
                        } else {
                            $modules[$currentMod]['lessons'][] = $cleanLine;
                        }
                    }
                }
            @endphp

            @if(count($modules))
                @foreach($modules as $mi => $mod)
                <div class="outline-module open" id="mod-{{ $mi }}">
                    @if($mod['title'])
                    <div class="outline-mod-header" onclick="toggleModule('mod-{{ $mi }}')">
                        <div class="outline-mod-num">{{ $mod['num'] }}</div>
                        <div class="outline-mod-title">{{ $mod['title'] }}</div>
                        @if(count($mod['lessons']))
                        <svg class="outline-mod-toggle" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                        @endif
                    </div>
                    @endif
                    @if(count($mod['lessons']))
                    <div class="outline-lessons">
                        @foreach($mod['lessons'] as $lesson)
                        @php
                            // Try to extract a lesson number badge (e.g. "1.1" from "Lesson 1.1: ...")
                            $badge = null;
                            if (preg_match('/^(lesson\s+)?(\d+[\.\d]*):?\s*/i', $lesson, $m)) {
                                $badge = trim($m[0], ': ');
                                $lesson = preg_replace('/^(lesson\s+)?\d+[\.\d]*:?\s*/i', '', $lesson);
                            }
                        @endphp
                        <div class="outline-lesson">
                            @if($badge)
                                <span class="outline-lesson-badge">{{ $badge }}</span>
                            @else
                                <div class="outline-dot"></div>
                            @endif
                            {{ trim($lesson) }}
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
                @endforeach
            @else
                {{-- Fallback: render raw text --}}
                <div class="cd-prose">{!! nl2br(e($course->course_outline)) !!}</div>
            @endif
        </div>
        @endif

        {{-- Audience tab --}}
        @if($course->who_should_attend || $course->prerequisites)
        <div class="cd-tab-panel" id="tab-panel-audience">
            @if($course->who_should_attend)
            <div class="cd-panel-heading">Who Should Attend</div>
            <ul class="checklist" style="margin-bottom:28px;">
                @foreach(array_filter(array_map('trim', explode("\n", $course->who_should_attend))) as $line)
                @php $line = ltrim($line, '•-* '); @endphp
                @if($line)
                <li>
                    <svg class="checklist-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    {{ $line }}
                </li>
                @endif
                @endforeach
            </ul>
            @endif
            @if($course->prerequisites)
            <div class="cd-panel-heading" style="margin-top:8px;">Prerequisites</div>
            <div class="cd-prose">{!! nl2br(e($course->prerequisites)) !!}</div>
            @endif
        </div>
        @endif

        {{-- Schedules tab --}}
        @if($course->publicSchedules->count())
        <div class="cd-tab-panel" id="tab-panel-schedules">
            <div class="cd-panel-heading" id="schedules">Available Schedules</div>
            <div style="overflow-x:auto;">
                <table class="schedule-table">
                    <thead>
                        <tr>
                            <th>Batch</th>
                            <th>Dates</th>
                            <th>Mode</th>
                            <th>Venue</th>
                            <th>Trainer</th>
                            <th>Fee</th>
                            <th>Seats</th>
                            <th>Deadline</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($course->publicSchedules as $s)
                        @php
                            $fee = $s->discount_fee ?? ($s->training_mode === 'Online' ? $s->online_fee : $s->physical_fee);
                            $origFee = $s->training_mode === 'Online' ? $s->online_fee : $s->physical_fee;
                            $seatsLeft = $s->seats_left;
                        @endphp
                        <tr>
                            <td style="font-weight:700;">{{ $s->batch_code }}</td>
                            <td>
                                {{ \Carbon\Carbon::parse($s->start_date)->format('d M') }}
                                – {{ \Carbon\Carbon::parse($s->end_date)->format('d M Y') }}
                                @if($s->time_start)
                                <div style="font-size:12px;color:#6b7280;margin-top:2px;">
                                    {{ \Carbon\Carbon::parse($s->time_start)->format('h:i A') }}
                                    – {{ \Carbon\Carbon::parse($s->time_end)->format('h:i A') }}
                                </div>
                                @endif
                            </td>
                            <td>
                                <span class="sc-mode-badge {{ match(strtolower($s->training_mode ?? '')) { 'online'=>'scm-online', 'hybrid'=>'scm-hybrid', default=>'scm-physical' } }}">
                                    {{ $s->training_mode }}
                                </span>
                            </td>
                            <td style="font-size:13px;">{{ $s->training_mode === 'Online' ? 'Zoom' : ($s->venue ?? 'TBA') }}</td>
                            <td style="font-size:13px;">{{ $s->trainer?->name ?? 'TBA' }}</td>
                            <td>
                                <strong>{{ $s->currency }} {{ number_format($fee) }}</strong>
                                @if($s->discount_fee && $origFee && $s->discount_fee < $origFee)
                                <div style="font-size:12px;color:#9ca3af;text-decoration:line-through;">{{ number_format($origFee) }}</div>
                                @endif
                            </td>
                            <td>
                                @if(!is_null($seatsLeft))
                                <span style="font-size:13px;font-weight:700;color:{{ $seatsLeft <= 5 ? '#ef4444' : '#16a34a' }};">
                                    {{ $seatsLeft <= 0 ? 'Full' : $seatsLeft . ' left' }}
                                </span>
                                @else
                                <span style="color:#9ca3af;font-size:13px;">Open</span>
                                @endif
                            </td>
                            <td>
                                @if($s->registration_deadline)
                                <div style="font-size:12px;color:#6b7280;">{{ \Carbon\Carbon::parse($s->registration_deadline)->format('d M Y') }}</div>
                                @endif
                            </td>
                            <td>
                                @if($s->is_open)
                                <a href="{{ route('public.enroll', $s->id) }}" class="pub-enroll-btn" style="padding:7px 14px;font-size:12.5px;">
                                    Enroll Now
                                </a>
                                @else
                                <span style="font-size:12px;color:#9ca3af;font-weight:600;">Closed</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- Reviews tab --}}
        @if($course->testimonials->count())
        <div class="cd-tab-panel" id="tab-panel-reviews">
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:18px;">
                @foreach($course->testimonials as $t)
                <div class="testi-card" style="background:#f8fafc;">
                    <div class="testi-stars">{{ str_repeat('★', $t->rating) }}{{ str_repeat('☆', 5 - $t->rating) }}</div>
                    <p class="testi-text">"{{ Str::limit($t->feedback, 200) }}"</p>
                    <div class="testi-author">
                        <div class="testi-avatar">{{ strtoupper(substr($t->name,0,1)) }}</div>
                        <div>
                            <div class="testi-name">{{ $t->name }}</div>
                            <div class="testi-role">{{ $t->designation }}{{ $t->company ? ' · '.$t->company : '' }}</div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- Right sidebar --}}
    <aside>
        {{-- LTF Taxonomy --}}
        @if($course->ltfStandards->count() || $course->ltfIndustries->count() || $course->ltf_competency_level)
        <div style="background:var(--surface);border:0.5px solid var(--border);border-radius:var(--r-lg);padding:16px;margin-bottom:20px;">
            <h4 style="font-size:11px;font-weight:500;letter-spacing:.06em;text-transform:uppercase;color:var(--text-muted);margin:0 0 14px;">This course covers</h4>

            @if($course->ltfStandards->count())
            <div style="margin-bottom:10px;">
                <div style="font-size:11px;color:var(--text-muted);margin-bottom:6px;">Standards</div>
                <div>
                    @foreach($course->ltfStandards as $standard)
                    <span style="display:inline-flex;font-size:11px;font-weight:500;padding:3px 9px;border-radius:20px;background:#E6F1FB;color:#0C447C;border:0.5px solid #85B7EB;margin-right:6px;margin-bottom:6px;"@if($standard->full_name) title="{{ $standard->full_name }}"@endif>{{ $standard->name }}</span>
                    @endforeach
                </div>
            </div>
            @endif

            @if($course->ltfIndustries->count())
            <div style="margin-bottom:10px;">
                <div style="font-size:11px;color:var(--text-muted);margin-bottom:6px;">Industries</div>
                <div>
                    @foreach($course->ltfIndustries as $industry)
                    <span style="display:inline-flex;font-size:11px;font-weight:500;padding:3px 9px;border-radius:20px;background:#FAEEDA;color:#633806;border:0.5px solid #EF9F27;margin-right:6px;margin-bottom:6px;">{{ $industry->name }}</span>
                    @endforeach
                </div>
            </div>
            @endif

            @if($course->ltf_competency_level)
            <div>
                <div style="font-size:11px;color:var(--text-muted);margin-bottom:6px;">Level</div>
                <span style="display:inline-flex;font-size:11px;font-weight:500;padding:3px 9px;border-radius:20px;background:#EEEDFE;color:#3C3489;border:0.5px solid #AFA9EC;">{{ ucfirst($course->ltf_competency_level) }}</span>
            </div>
            @endif
        </div>
        @endif

        {{-- Quick facts --}}
        <div style="background:#fff;border:1px solid #e9ecf0;border-radius:14px;padding:22px;margin-bottom:20px;">
            <h4 style="font-size:13px;font-weight:800;text-transform:uppercase;letter-spacing:.6px;color:#6b7280;margin:0 0 16px;">Course Facts</h4>
            @if($course->duration)
            <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid #f0f2f5;font-size:14px;">
                <span style="color:#6b7280;">Duration</span> <strong>{{ $course->duration }}</strong>
            </div>
            @endif
            @if($course->language)
            <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid #f0f2f5;font-size:14px;">
                <span style="color:#6b7280;">Language</span> <strong>{{ $course->language }}</strong>
            </div>
            @endif
            @if($course->certificate_type)
            <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid #f0f2f5;font-size:14px;">
                <span style="color:#6b7280;">Certificate</span> <strong>{{ $course->certificate_type }}</strong>
            </div>
            @endif
            @if($course->cpd_hours)
            <div style="display:flex;justify-content:space-between;padding:10px 0;font-size:14px;">
                <span style="color:#6b7280;">CPD Hours</span> <strong>{{ $course->cpd_hours }}</strong>
            </div>
            @endif
        </div>

        {{-- Related courses --}}
        @if($relatedCourses->count())
        <div style="background:#fff;border:1px solid #e9ecf0;border-radius:14px;padding:22px;">
            <h4 style="font-size:13px;font-weight:800;text-transform:uppercase;letter-spacing:.6px;color:#6b7280;margin:0 0 16px;">Related Courses</h4>
            @foreach($relatedCourses as $rc)
            <a href="{{ route('public.course.detail', $rc->slug ?? $rc->id) }}"
               style="display:flex;align-items:flex-start;gap:12px;padding:10px 0;border-bottom:1px solid #f0f2f5;text-decoration:none;color:inherit;">
                <div style="width:48px;height:48px;border-radius:8px;background:#f0f4ff;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;">🎓</div>
                <div>
                    <div style="font-size:13.5px;font-weight:700;color:#111827;line-height:1.3;">{{ Str::limit($rc->name, 50) }}</div>
                    @if($rc->category)<div style="font-size:12px;color:#6b7280;margin-top:3px;">{{ $rc->category }}</div>@endif
                </div>
            </a>
            @endforeach
        </div>
        @endif

        {{-- Related blog posts --}}
        @if($course->blogPosts->count())
        <div style="background:#fff;border:1px solid #e9ecf0;border-radius:14px;padding:22px;margin-top:20px;">
            <h4 style="font-size:13px;font-weight:800;text-transform:uppercase;letter-spacing:.6px;color:#6b7280;margin:0 0 16px;">Related Articles</h4>
            @foreach($course->blogPosts as $bp)
            <a href="{{ route('public.blog.detail', $bp->slug) }}"
               style="display:block;font-size:14px;font-weight:700;color:#042C53;text-decoration:none;padding:8px 0;border-bottom:1px solid #f0f2f5;line-height:1.4;">
                {{ $bp->title }}
            </a>
            @endforeach
        </div>
        @endif
    </aside>

</div>
</div>

<script>
function showTab(name, btn) {
    // Hide all panels
    document.querySelectorAll('.cd-tab-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.cd-tab').forEach(b => b.classList.remove('active'));
    // Show selected
    const panel = document.getElementById('tab-panel-' + name);
    if (panel) panel.classList.add('active');
    if (btn) btn.classList.add('active');
    // Scroll to section if schedules
    if (name === 'schedules') {
        const el = document.getElementById('schedules');
        if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

// If URL has #schedules anchor, open that tab
window.addEventListener('load', function() {
    if (window.location.hash === '#schedules') {
        const btn = document.getElementById('tab-schedules');
        if (btn) btn.click();
    }
});

function toggleModule(id) {
    const mod = document.getElementById(id);
    if (mod) mod.classList.toggle('open');
}
</script>

@endsection
