@extends('layouts.app')
@section('page-title', 'My Learning Dashboard')
@section('content')

@php
    $user         = Auth::user();
    $totalEl      = $elearningEnrollments->count();
    $inProgressEl = $elearningEnrollments->where('completion_status', '!=', 'completed')
                        ->where('access_status', 'unlocked')->count();
    $completedEl  = $elearningEnrollments->where('completion_status', 'completed')->count();
    $certIssued   = $elearningEnrollments->where('certificate_status', 'issued')->count();
    $certEligible = $elearningEnrollments->where('certificate_status', 'eligible')->count();
    $totalManual  = $manualEnrollments->count();
    $totalAll     = $totalEl + $totalManual;

    // Active courses for "Continue Learning" — unlocked, not completed, has next lesson
    $activeCourses = $elearningEnrollments
        ->where('completion_status', '!=', 'completed')
        ->where('access_status', 'unlocked')
        ->filter(fn($e) => isset($nextLessonMap[$e->id]));
@endphp

<x-flash-message />

<style>
/* ── Welcome Card ── */
.welcome-card {
    background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 60%, #1d4ed8 100%);
    border-radius: 16px;
    padding: 26px 28px;
    color: white;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 20px;
    box-shadow: 0 8px 24px rgba(30,58,138,.30);
    position: relative;
    overflow: hidden;
}
.welcome-card::before {
    content: '';
    position: absolute;
    top: -40px; right: -40px;
    width: 200px; height: 200px;
    background: rgba(255,255,255,.05);
    border-radius: 50%;
}
.welcome-card::after {
    content: '';
    position: absolute;
    bottom: -60px; right: 80px;
    width: 160px; height: 160px;
    background: rgba(255,255,255,.04);
    border-radius: 50%;
}
.welcome-text h2 { margin: 0 0 4px; font-size: 20px; font-weight: 800; }
.welcome-text p  { margin: 0; font-size: 13px; opacity: .8; }
.welcome-stats {
    display: flex; gap: 24px; flex-wrap: wrap;
}
.ws-item { text-align: center; }
.ws-num  { font-size: 24px; font-weight: 800; line-height: 1; }
.ws-lbl  { font-size: 11px; opacity: .75; font-weight: 600; text-transform: uppercase; letter-spacing: .6px; margin-top: 2px; }

/* ── Section title ── */
.lms-section-title {
    font-size: 15px; font-weight: 800; color: #111827;
    margin: 0 0 14px; display: flex; align-items: center; gap: 8px;
}

/* ── Summary stat cards ── */
.summary-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 14px;
    margin-bottom: 28px;
}
@media (max-width: 900px) { .summary-grid { grid-template-columns: repeat(2,1fr); } }
@media (max-width: 480px) { .summary-grid { grid-template-columns: 1fr 1fr; } }

.sum-card {
    background: #fff;
    border-radius: 14px;
    padding: 18px 20px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 1px 6px rgba(15,23,42,.05);
    display: flex;
    align-items: center;
    gap: 14px;
    cursor: pointer;
    transition: box-shadow .15s, transform .15s;
    text-decoration: none;
    color: inherit;
}
.sum-card:hover { box-shadow: 0 4px 16px rgba(15,23,42,.10); transform: translateY(-2px); }
.sum-icon {
    width: 46px; height: 46px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.si-blue   { background: #eff6ff; color: #2563eb; }
.si-amber  { background: #fffbeb; color: #d97706; }
.si-green  { background: #f0fdf4; color: #16a34a; }
.si-purple { background: #faf5ff; color: #7c3aed; }
.sum-val   { font-size: 24px; font-weight: 800; color: #111827; line-height: 1; }
.sum-lbl   { font-size: 12px; color: #6b7280; font-weight: 600; margin-top: 3px; }

/* ── Continue Learning cards ── */
.continue-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 18px;
    margin-bottom: 28px;
}
.cl-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 1px 6px rgba(15,23,42,.05);
    display: flex;
    flex-direction: column;
    transition: box-shadow .15s;
}
.cl-card:hover { box-shadow: 0 4px 16px rgba(15,23,42,.10); }
.cl-banner {
    height: 6px;
    background: linear-gradient(90deg, #2563eb, #60a5fa);
}
.cl-body { padding: 18px; flex: 1; display: flex; flex-direction: column; }
.cl-title { font-size: 15px; font-weight: 800; color: #111827; margin: 0 0 12px; line-height: 1.3; }
.cl-meta  { font-size: 12px; color: #6b7280; margin-bottom: 12px; }
.cl-progress-wrap { margin-bottom: 14px; }
.cl-progress-row  { display: flex; justify-content: space-between; font-size: 12px; font-weight: 700; color: #6b7280; margin-bottom: 5px; }
.cl-bar  { height: 8px; background: #f1f5f9; border-radius: 10px; overflow: hidden; }
.cl-fill { height: 100%; background: linear-gradient(90deg,#2563eb,#3b82f6); border-radius: 10px; }
.cl-next { font-size: 12px; color: #475569; font-weight: 600; padding: 8px 12px; background: #f8fafc; border-radius: 8px; margin-bottom: 14px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.cl-next span { color: #1e3a8a; font-weight: 700; }
.cl-btn  { display: flex; align-items: center; justify-content: center; gap: 6px; background: #1e3a8a; color: #fff; text-decoration: none; font-weight: 700; font-size: 13px; padding: 10px; border-radius: 9px; margin-top: auto; transition: background .15s; }
.cl-btn:hover { background: #1d4ed8; }

/* ── Certificate status row ── */
.cert-section { margin-bottom: 28px; }
.cert-cards-row { display: flex; flex-wrap: wrap; gap: 14px; }
.cert-mini {
    background: #fff; border-radius: 12px; border: 1px solid #e5e7eb;
    padding: 14px 18px; display: flex; align-items: center; gap: 14px;
    flex: 1; min-width: 260px;
    box-shadow: 0 1px 4px rgba(15,23,42,.05);
}
.cert-icon-wrap { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.ci-issued  { background: #f0fdf4; color: #16a34a; }
.ci-eligible{ background: #eff6ff; color: #2563eb; }
.cert-name { font-size: 13.5px; font-weight: 700; color: #111827; margin-bottom: 3px; }
.cert-status-line { font-size: 11.5px; color: #6b7280; font-weight: 600; }

/* ── Course grid (all courses) ── */
.course-section { margin-bottom: 28px; }
.course-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 18px;
}
.course-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 1px 6px rgba(15,23,42,.05);
    display: flex;
    flex-direction: column;
    transition: box-shadow .15s, transform .15s;
}
.course-card:hover { box-shadow: 0 6px 20px rgba(15,23,42,.10); transform: translateY(-2px); }
.cc-banner-el { height: 5px; background: linear-gradient(90deg,#2563eb,#60a5fa); }
.cc-banner-il { height: 5px; background: linear-gradient(90deg,#d97706,#fbbf24); }
.cc-body { padding: 18px; flex: 1; display: flex; flex-direction: column; }
.cc-type-chip {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 3px 9px; border-radius: 20px; font-size: 10.5px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .5px;
    margin-bottom: 10px;
}
.chip-el { background: #eff6ff; color: #2563eb; }
.chip-il { background: #fffbeb; color: #d97706; }
.cc-title { font-size: 15px; font-weight: 800; color: #111827; margin: 0 0 12px; line-height: 1.3; }
.cc-meta-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px 14px; margin-bottom: 14px; }
.cc-meta-item .cc-meta-label { font-size: 10.5px; color: #9ca3af; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 3px; }
.cc-meta-item .cc-meta-val   { font-size: 13px; font-weight: 700; color: #374151; }
.cc-progress-wrap { margin-bottom: 14px; }
.cc-progress-row  { display: flex; justify-content: space-between; font-size: 11.5px; font-weight: 700; color: #6b7280; margin-bottom: 4px; }
.cc-bar  { height: 7px; background: #f1f5f9; border-radius: 10px; overflow: hidden; }
.cc-fill-el { height: 100%; background: linear-gradient(90deg,#2563eb,#3b82f6); border-radius: 10px; }
.cc-fill-done { background: linear-gradient(90deg,#16a34a,#4ade80); }
.cc-actions { margin-top: auto; display: flex; flex-direction: column; gap: 8px; }
.cc-btn-primary { display: flex; align-items: center; justify-content: center; gap: 6px; background: #1e3a8a; color: #fff; text-decoration: none; font-weight: 700; font-size: 13px; padding: 10px; border-radius: 9px; transition: background .15s; }
.cc-btn-primary:hover { background: #1d4ed8; }
.cc-btn-teal { background: #0f766e; color: #fff; }
.cc-btn-teal:hover { background: #0d9488; }
.cc-btn-ghost { display: flex; align-items: center; justify-content: center; background: #f8fafc; color: #374151; text-decoration: none; font-weight: 600; font-size: 12px; padding: 8px; border-radius: 8px; border: 1px solid #e5e7eb; }
.cc-btn-locked { display: flex; align-items: center; justify-content: center; background: #f3f4f6; color: #9ca3af; font-weight: 700; font-size: 13px; padding: 10px; border-radius: 9px; cursor: not-allowed; }
</style>

{{-- ══ WELCOME CARD ══════════════════════════════════════ --}}
<div class="welcome-card">
    <div class="welcome-text" style="position:relative;z-index:1;">
        <h2>Welcome back, {{ $user->name ?? 'Learner' }} 👋</h2>
        <p>Track your learning journey and continue where you left off.</p>
    </div>
    <div class="welcome-stats" style="position:relative;z-index:1;">
        <div class="ws-item">
            <div class="ws-num">{{ $totalAll }}</div>
            <div class="ws-lbl">Enrolled</div>
        </div>
        <div class="ws-item">
            <div class="ws-num">{{ $completedEl }}</div>
            <div class="ws-lbl">Completed</div>
        </div>
        <div class="ws-item">
            <div class="ws-num">{{ $certIssued }}</div>
            <div class="ws-lbl">Certificates</div>
        </div>
    </div>
</div>

{{-- ══ SUMMARY CARDS ═════════════════════════════════════ --}}
<div class="summary-grid">
    <a href="#all-courses" class="sum-card">
        <div class="sum-icon si-blue">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
        </div>
        <div>
            <div class="sum-val">{{ $totalAll }}</div>
            <div class="sum-lbl">My Courses</div>
        </div>
    </a>
    <a href="#continue-learning" class="sum-card">
        <div class="sum-icon si-amber">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
        </div>
        <div>
            <div class="sum-val">{{ $inProgressEl }}</div>
            <div class="sum-lbl">In Progress</div>
        </div>
    </a>
    <a href="#all-courses" class="sum-card">
        <div class="sum-icon si-green">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        </div>
        <div>
            <div class="sum-val">{{ $completedEl }}</div>
            <div class="sum-lbl">Completed</div>
        </div>
    </a>
    <a href="{{ route('participant.my-certificates') }}" class="sum-card">
        <div class="sum-icon si-purple">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg>
        </div>
        <div>
            <div class="sum-val">{{ $certIssued }}</div>
            <div class="sum-lbl">Certificates</div>
        </div>
    </a>
</div>

{{-- ══ CONTINUE LEARNING ═════════════════════════════════ --}}
@if($activeCourses->isNotEmpty())
<div id="continue-learning" style="margin-bottom:28px;">
    <div class="lms-section-title">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.5"><polygon points="5 3 19 12 5 21 5 3"/></svg>
        Continue Learning
    </div>
    <div class="continue-grid">
        @foreach($activeCourses->take(4) as $enrollment)
        @php
            $pct         = $enrollment->progress_percentage;
            $nextLesson  = $nextLessonMap[$enrollment->id] ?? null;
            $totalLessons= $enrollment->course->lessons->count();
            $doneLessons = $enrollment->lessonProgress->where('status','completed')->count();
        @endphp
        <div class="cl-card">
            <div class="cl-banner"></div>
            <div class="cl-body">
                <div class="cl-title">{{ $enrollment->course->name ?? 'Course' }}</div>
                <div class="cl-meta">
                    {{ $doneLessons }} / {{ $totalLessons }} lessons &nbsp;·&nbsp;
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    Self-Paced
                </div>
                <div class="cl-progress-wrap">
                    <div class="cl-progress-row"><span>Progress</span><span>{{ $pct }}%</span></div>
                    <div class="cl-bar"><div class="cl-fill" style="width:{{ $pct }}%;"></div></div>
                </div>
                @if($nextLesson)
                <div class="cl-next">
                    <span>Next:</span> {{ $nextLesson->title }}
                </div>
                @endif
                <a href="{{ route('participant.lesson.show', [$enrollment->id, $nextLesson->id]) }}" class="cl-btn">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                    Continue Learning
                </a>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- ══ CERTIFICATE STATUS ════════════════════════════════ --}}
@if($certIssued > 0 || $certEligible > 0)
<div class="cert-section">
    <div class="lms-section-title">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="2.5"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg>
        Certificate Status
    </div>
    <div class="cert-cards-row">
        @foreach($elearningEnrollments->whereIn('certificate_status', ['issued','eligible'])->take(4) as $enrollment)
        <div class="cert-mini">
            <div class="cert-icon-wrap {{ $enrollment->certificate_status === 'issued' ? 'ci-issued' : 'ci-eligible' }}">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg>
            </div>
            <div style="flex:1;min-width:0;">
                <div class="cert-name" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $enrollment->course->name ?? '—' }}</div>
                <div class="cert-status-line">
                    @if($enrollment->certificate_status === 'issued')
                        <span style="color:#16a34a;font-weight:700;">✓ Certificate Issued</span>
                        &nbsp;·&nbsp;
                        <a href="{{ route('elearning.certificate.generate', $enrollment->id) }}" target="_blank" style="color:#1e3a8a;font-weight:700;font-size:11.5px;">Download →</a>
                    @else
                        <span style="color:#2563eb;font-weight:700;">Eligible — Awaiting Issuance</span>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div style="margin-top:10px;">
        <a href="{{ route('participant.my-certificates') }}" style="font-size:13px;color:#1e3a8a;font-weight:700;text-decoration:none;">View all certificates →</a>
    </div>
</div>
@endif

{{-- ══ ALL COURSES: E-LEARNING ══════════════════════════ --}}
<div id="all-courses" class="course-section">
    <div class="lms-section-title">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.5"><path d="M22 10v6M2 10l10-5 10 5-10 5-10-5z"/></svg>
        E-Learning Courses
        <span style="background:#eff6ff;color:#2563eb;padding:2px 9px;border-radius:20px;font-size:11px;font-weight:800;">{{ $totalEl }}</span>
    </div>

    @if($elearningEnrollments->isEmpty())
        <div style="padding:32px;text-align:center;border:2px dashed #e5e7eb;border-radius:14px;background:#fafafa;color:#9ca3af;font-size:14px;margin-bottom:28px;">
            No e-learning enrollments yet. Contact admin to get enrolled.
        </div>
    @else
    <div class="course-grid" style="margin-bottom:28px;">
        @foreach($elearningEnrollments as $enrollment)
        @php
            $pct          = $enrollment->progress_percentage;
            $isDone       = $enrollment->completion_status === 'completed';
            $hasCert      = $enrollment->certificate_status === 'issued';
            $isEligible   = $enrollment->certificate_status === 'eligible';
            $isLocked     = $enrollment->access_status !== 'unlocked';
            $nextLesson   = $nextLessonMap[$enrollment->id] ?? null;
            $totalLessons = $enrollment->course->lessons->count();
            $doneLessons  = $enrollment->lessonProgress->where('status','completed')->count();
        @endphp
        <div class="course-card">
            <div class="cc-banner-el"></div>
            <div class="cc-body">
                <div class="cc-type-chip chip-el">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 10v6M2 10l10-5 10 5-10 5-10-5z"/></svg>
                    Self-Paced
                </div>
                <div class="cc-title">{{ $enrollment->course->name ?? 'E-Learning Course' }}</div>

                <div class="cc-meta-grid">
                    <div class="cc-meta-item">
                        <div class="cc-meta-label">Payment</div>
                        <div class="cc-meta-val">
                            @if(in_array($enrollment->payment_status, ['paid','manual_approved','waived','free']))
                                <span class="badge badge-success" style="font-size:11px;">Cleared</span>
                            @else
                                <span class="badge badge-warning" style="font-size:11px;">{{ ucfirst(str_replace('_',' ',$enrollment->payment_status)) }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="cc-meta-item">
                        <div class="cc-meta-label">Status</div>
                        <div class="cc-meta-val">
                            @if($isDone)
                                <span class="badge badge-success" style="font-size:11px;">Completed</span>
                            @elseif($isLocked)
                                <span class="badge badge-secondary" style="font-size:11px;">Locked</span>
                            @else
                                <span class="badge badge-info" style="font-size:11px;">In Progress</span>
                            @endif
                        </div>
                    </div>
                    <div class="cc-meta-item">
                        <div class="cc-meta-label">Lessons</div>
                        <div class="cc-meta-val">{{ $doneLessons }} / {{ $totalLessons }}</div>
                    </div>
                    <div class="cc-meta-item">
                        <div class="cc-meta-label">Certificate</div>
                        <div class="cc-meta-val">
                            @if($hasCert)
                                <span class="badge badge-success" style="font-size:11px;">Issued</span>
                            @elseif($isEligible)
                                <span class="badge badge-info" style="font-size:11px;">Eligible</span>
                            @else
                                <span class="badge badge-secondary" style="font-size:11px;">—</span>
                            @endif
                        </div>
                    </div>
                </div>

                @if(!$isLocked)
                <div class="cc-progress-wrap">
                    <div class="cc-progress-row"><span>Progress</span><span>{{ $pct }}%</span></div>
                    <div class="cc-bar"><div class="cc-fill-el {{ $isDone ? 'cc-fill-done' : '' }}" style="width:{{ $pct }}%;"></div></div>
                </div>
                @endif

                <div class="cc-actions">
                    @if($isLocked)
                        <div class="cc-btn-locked">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:5px;"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            Access Locked
                        </div>
                    @elseif($hasCert)
                        <a href="{{ route('elearning.certificate.generate', $enrollment->id) }}" target="_blank" class="cc-btn-primary cc-btn-teal">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg>
                            Download Certificate
                        </a>
                        <a href="{{ route('participant.elearning-details', $enrollment->id) }}" class="cc-btn-ghost">View Course</a>
                    @elseif($isDone)
                        <a href="{{ route('participant.elearning-details', $enrollment->id) }}" class="cc-btn-primary">Course Overview</a>
                    @elseif($nextLesson)
                        <a href="{{ route('participant.lesson.show', [$enrollment->id, $nextLesson->id]) }}" class="cc-btn-primary">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                            Continue Learning
                        </a>
                        <a href="{{ route('participant.elearning-details', $enrollment->id) }}" class="cc-btn-ghost">Course Overview</a>
                    @else
                        <a href="{{ route('participant.elearning-details', $enrollment->id) }}" class="cc-btn-primary">Start Learning</a>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

{{-- ══ ALL COURSES: INSTRUCTOR-LED ═════════════════════ --}}
@if($manualEnrollments->isNotEmpty())
<div class="course-section">
    <div class="lms-section-title">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        Instructor-Led Training
        <span style="background:#fffbeb;color:#d97706;padding:2px 9px;border-radius:20px;font-size:11px;font-weight:800;">{{ $totalManual }}</span>
    </div>
    <div class="course-grid">
        @foreach($manualEnrollments as $enrollment)
        @php
            $paid = strtolower($enrollment->payment_status ?? '') === 'paid';
            $done = strtolower($enrollment->completion_status ?? '') === 'completed';
        @endphp
        <div class="course-card">
            <div class="cc-banner-il"></div>
            <div class="cc-body">
                <div class="cc-type-chip chip-il">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                    Instructor-Led
                </div>
                <div class="cc-title">{{ $enrollment->schedule->course->name ?? 'Training Course' }}</div>

                <div class="cc-meta-grid">
                    <div class="cc-meta-item">
                        <div class="cc-meta-label">Batch</div>
                        <div class="cc-meta-val">{{ $enrollment->schedule->batch_code ?? '—' }}</div>
                    </div>
                    <div class="cc-meta-item">
                        <div class="cc-meta-label">Trainer</div>
                        <div class="cc-meta-val" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $enrollment->schedule->trainer->name ?? '—' }}</div>
                    </div>
                    <div class="cc-meta-item">
                        <div class="cc-meta-label">Start Date</div>
                        <div class="cc-meta-val">{{ $enrollment->schedule->start_date ? \Carbon\Carbon::parse($enrollment->schedule->start_date)->format('d M Y') : '—' }}</div>
                    </div>
                    <div class="cc-meta-item">
                        <div class="cc-meta-label">Payment</div>
                        <div class="cc-meta-val">
                            @if($paid) <span class="badge badge-success" style="font-size:11px;">Paid</span>
                            @else <span class="badge badge-warning" style="font-size:11px;">Pending</span>
                            @endif
                        </div>
                    </div>
                    <div class="cc-meta-item">
                        <div class="cc-meta-label">Attendance</div>
                        <div class="cc-meta-val">{{ ucfirst($enrollment->attendance_status ?? 'Pending') }}</div>
                    </div>
                    <div class="cc-meta-item">
                        <div class="cc-meta-label">Completion</div>
                        <div class="cc-meta-val">
                            @if($done) <span class="badge badge-success" style="font-size:11px;">Done</span>
                            @else <span class="badge badge-secondary" style="font-size:11px;">Pending</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="cc-actions">
                    <a href="{{ route('participant.course-details', $enrollment->id) }}" class="cc-btn-primary" style="background:#d97706;">
                        View Training Details
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

@endsection
