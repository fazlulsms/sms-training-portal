@extends('layouts.app')
@section('page-title', $enrollment->course->name ?? 'Course Overview')
@section('content')

<style>
/* ── Layout ── */
.course-page { display: flex; gap: 22px; align-items: flex-start; }
.course-main { flex: 1; min-width: 0; }
.course-sidebar { width: 300px; flex-shrink: 0; }
@media (max-width: 960px) {
    .course-page { flex-direction: column; }
    .course-sidebar { width: 100%; }
}

/* ── Header card ── */
.course-header {
    background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
    border-radius: 16px;
    padding: 26px 28px;
    color: white;
    margin-bottom: 20px;
    box-shadow: 0 8px 24px rgba(30,58,138,.25);
    position: relative;
    overflow: hidden;
}
.course-header::before {
    content: '';
    position: absolute;
    top: -50px; right: -50px;
    width: 200px; height: 200px;
    background: rgba(255,255,255,.05);
    border-radius: 50%;
}
.ch-title { font-size: 21px; font-weight: 800; margin: 0 0 14px; position: relative; z-index: 1; line-height: 1.3; }
.ch-meta  { display: flex; flex-wrap: wrap; gap: 16px 24px; margin-bottom: 20px; position: relative; z-index: 1; }
.ch-meta-item .ch-label { font-size: 10.5px; opacity: .7; text-transform: uppercase; letter-spacing: .6px; margin-bottom: 2px; font-weight: 700; }
.ch-meta-item .ch-val   { font-size: 13px; font-weight: 700; }
.ch-progress { position: relative; z-index: 1; }
.ch-prog-bar { height: 10px; background: rgba(255,255,255,.25); border-radius: 20px; overflow: hidden; }
.ch-prog-fill { height: 100%; background: #fff; border-radius: 20px; transition: width .5s; }
.ch-prog-label { display: flex; justify-content: space-between; font-size: 12px; font-weight: 700; margin-top: 6px; opacity: .9; }

/* ── Certificate panel ── */
.cert-panel {
    border-radius: 12px;
    padding: 16px 20px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    flex-wrap: wrap;
}
.cert-panel.issued   { background: #f0fdf4; border: 1px solid #a7f3d0; }
.cert-panel.eligible { background: #eff6ff; border: 1px solid #bfdbfe; }
.cert-panel strong { font-size: 14px; font-weight: 800; display: block; }
.cert-panel span   { font-size: 12px; color: #6b7280; margin-top: 2px; display: block; }
.cert-dl-btn {
    background: #0f766e; color: white;
    padding: 9px 16px; border-radius: 8px;
    text-decoration: none; font-weight: 700; font-size: 13px; white-space: nowrap;
    display: inline-flex; align-items: center; gap: 6px;
}

/* ── Lessons card ── */
.lessons-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(15,23,42,.05);
}
.lessons-card-head {
    padding: 16px 20px;
    border-bottom: 1px solid #f1f5f9;
    font-size: 15px;
    font-weight: 800;
    color: #111827;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.lessons-count { font-size: 12px; color: #9ca3af; font-weight: 600; }

.lesson-row {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px 20px;
    border-bottom: 1px solid #f8fafc;
    transition: background .12s;
}
.lesson-row:last-child { border-bottom: none; }
.lesson-row:hover { background: #fafcff; }

.lesson-num {
    width: 34px; height: 34px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 13px; font-weight: 800;
    flex-shrink: 0;
}
.ln-done     { background: #dcfce7; color: #166534; }
.ln-active   { background: #dbeafe; color: #1e40af; }
.ln-pending  { background: #f3f4f6; color: #6b7280; }
.ln-locked   { background: #f9fafb; color: #d1d5db; border: 1px solid #e5e7eb; }

.lesson-info { flex: 1; min-width: 0; }
.lesson-title-text { font-weight: 700; font-size: 14px; color: #111827; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.lesson-sub-text  { font-size: 11.5px; color: #9ca3af; margin-top: 2px; }

.lesson-status-badge {
    flex-shrink: 0;
}

.lesson-action-btn {
    flex-shrink: 0;
    display: inline-flex; align-items: center; gap: 5px;
    padding: 7px 14px; border-radius: 8px;
    font-weight: 700; font-size: 12.5px;
    text-decoration: none;
    white-space: nowrap;
}
.la-open     { background: #1e3a8a; color: #fff; }
.la-review   { background: #0f766e; color: #fff; }
.la-locked   { background: #f3f4f6; color: #9ca3af; cursor: not-allowed; }

/* ── Sidebar cards ── */
.sb-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    overflow: hidden;
    margin-bottom: 16px;
    box-shadow: 0 1px 6px rgba(15,23,42,.05);
}
.sb-card-head {
    padding: 13px 16px;
    border-bottom: 1px solid #f1f5f9;
    font-size: 12.5px;
    font-weight: 800;
    color: #374151;
    text-transform: uppercase;
    letter-spacing: .6px;
}
.sb-card-body { padding: 16px; }
.sb-stat-row  { display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid #f8fafc; font-size: 13px; }
.sb-stat-row:last-child { border-bottom: none; }
.sb-stat-label { color: #6b7280; font-weight: 600; }
.sb-stat-val   { font-weight: 800; color: #111827; }

/* ── Alert ── */
.lms-alert { padding: 12px 16px; border-radius: 10px; font-weight: 600; margin-bottom: 16px; font-size: 13px; }
.lms-alert-error   { background: #fee2e2; color: #991b1b; }
.lms-alert-success { background: #dcfce7; color: #166534; }

/* ── Back link ── */
.back-link {
    display: inline-flex; align-items: center; gap: 6px;
    color: #6b7280; font-weight: 600; text-decoration: none;
    margin-bottom: 18px; font-size: 13.5px;
    transition: color .15s;
}
.back-link:hover { color: #1e3a8a; }
</style>

@if(session('error'))
    <div class="lms-alert lms-alert-error">{{ session('error') }}</div>
@endif
@if(session('success'))
    <div class="lms-alert lms-alert-success">{{ session('success') }}</div>
@endif

<a href="{{ route('participant.my-courses') }}" class="back-link">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
    My Courses
</a>

{{-- ── Course Header ── --}}
<div class="course-header">
    <div class="ch-title">{{ $enrollment->course->name ?? 'eLearning Course' }}</div>
    <div class="ch-meta">
        <div class="ch-meta-item">
            <div class="ch-label">Payment</div>
            <div class="ch-val">{{ ucfirst(str_replace('_', ' ', $enrollment->payment_status)) }}</div>
        </div>
        <div class="ch-meta-item">
            <div class="ch-label">Access</div>
            <div class="ch-val">{{ ucfirst($enrollment->access_status) }}</div>
        </div>
        <div class="ch-meta-item">
            <div class="ch-label">Status</div>
            <div class="ch-val">{{ $enrollment->completion_status === 'completed' ? 'Completed ✓' : 'In Progress' }}</div>
        </div>
        @if($enrollment->expires_at)
        <div class="ch-meta-item">
            <div class="ch-label">Access Expires</div>
            <div class="ch-val">{{ $enrollment->expires_at->format('d M Y') }}</div>
        </div>
        @endif
    </div>
    <div class="ch-progress">
        <div class="ch-prog-bar">
            <div class="ch-prog-fill" style="width:{{ $enrollment->progress_percentage }}%;"></div>
        </div>
        <div class="ch-prog-label">
            <span>{{ $lessonProgressMap->where('status','completed')->count() }} / {{ $enrollment->course->lessons->count() }} lessons completed</span>
            <span>{{ $enrollment->progress_percentage }}%</span>
        </div>
    </div>
</div>

{{-- ── Certificate Panel ── --}}
@if($enrollment->certificate_status === 'issued')
<div class="cert-panel issued">
    <div>
        <strong style="color:#065f46;">🎓 Certificate Issued</strong>
        <span>Certificate No: {{ $enrollment->certificate_number }}</span>
    </div>
    <a href="{{ route('elearning.certificate.generate', $enrollment->id) }}" target="_blank" class="cert-dl-btn">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        Download PDF
    </a>
</div>
@elseif($enrollment->certificate_status === 'eligible')
<div class="cert-panel eligible">
    <div>
        <strong style="color:#1e40af;">Certificate Eligible</strong>
        <span>You have completed all requirements. Waiting for admin to issue your certificate.</span>
    </div>
    <span style="display:inline-flex;align-items:center;gap:6px;background:#dbeafe;color:#1e40af;padding:8px 14px;border-radius:8px;font-weight:700;font-size:13px;">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        Pending Issuance
    </span>
</div>
@endif

{{-- ── Two-column layout ── --}}
<div class="course-page">

    {{-- Main: Lessons List ── --}}
    <div class="course-main">
        <div class="lessons-card">
            <div class="lessons-card-head">
                <span>Course Content</span>
                <span class="lessons-count">{{ $enrollment->course->lessons->count() }} lessons</span>
            </div>

            @php $lessons = $enrollment->course->lessons; @endphp

            @forelse($lessons as $index => $lesson)
                @php
                    $lp           = $lessonProgressMap->get($lesson->id);
                    $isCompleted  = $lp && $lp->status === 'completed';
                    $isInProgress = $lp && $lp->status === 'in_progress';
                    $prevLesson   = $index > 0 ? $lessons[$index - 1] : null;
                    $isAccessible = $index === 0
                        || ($prevLesson && $lessonProgressMap->get($prevLesson->id)?->status === 'completed');
                @endphp
                <div class="lesson-row">
                    <div class="lesson-num {{ $isCompleted ? 'ln-done' : ($isInProgress ? 'ln-active' : ($isAccessible ? 'ln-pending' : 'ln-locked')) }}">
                        @if($isCompleted)
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                        @elseif(!$isAccessible)
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        @else
                            {{ $index + 1 }}
                        @endif
                    </div>

                    <div class="lesson-info">
                        <div class="lesson-title-text">{{ $lesson->title }}</div>
                        <div class="lesson-sub-text">
                            @if($lesson->duration_minutes){{ $lesson->duration_minutes }} min &nbsp;·&nbsp;@endif
                            Lesson {{ $lesson->lesson_order }}
                            @if($lesson->video_url) &nbsp;·&nbsp; Video @endif
                            @if($isCompleted) &nbsp;·&nbsp; <span style="color:#16a34a;font-weight:700;">Done</span>
                            @elseif($isInProgress) &nbsp;·&nbsp; <span style="color:#1e40af;font-weight:700;">In Progress</span>
                            @elseif(!$isAccessible) &nbsp;·&nbsp; Locked
                            @endif
                        </div>
                    </div>

                    <div class="lesson-status-badge">
                        @if($isCompleted)
                            <span class="badge badge-success" style="font-size:11px;">Done</span>
                        @elseif($isInProgress)
                            <span class="badge badge-info" style="font-size:11px;">Active</span>
                        @elseif(!$isAccessible)
                            <span class="badge badge-secondary" style="font-size:11px;">Locked</span>
                        @else
                            <span class="badge badge-secondary" style="font-size:11px;background:#f0f9ff;color:#0284c7;">Ready</span>
                        @endif
                    </div>

                    @if($isAccessible)
                        <a href="{{ route('participant.lesson.show', [$enrollment->id, $lesson->id]) }}"
                           class="lesson-action-btn {{ $isCompleted ? 'la-review' : 'la-open' }}">
                            @if($isCompleted)
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                Review
                            @elseif($isInProgress)
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                                Continue
                            @else
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                                Start
                            @endif
                        </a>
                    @else
                        <span class="lesson-action-btn la-locked">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            Locked
                        </span>
                    @endif
                </div>
            @empty
                <div style="padding:28px;text-align:center;color:#9ca3af;font-size:14px;">No lessons have been added to this course yet.</div>
            @endforelse
        </div>
    </div>

    {{-- Sidebar ── --}}
    <div class="course-sidebar">

        {{-- Progress summary ── --}}
        <div class="sb-card">
            <div class="sb-card-head">Your Progress</div>
            <div class="sb-card-body">
                <div style="text-align:center;margin-bottom:16px;">
                    @php
                        $pct = $enrollment->progress_percentage;
                        $circ_r = 36;
                        $circ_c = round(2 * M_PI * $circ_r, 2);
                        $offset = round($circ_c - ($pct / 100) * $circ_c, 2);
                    @endphp
                    <svg width="100" height="100" viewBox="0 0 100 100">
                        <circle cx="50" cy="50" r="{{ $circ_r }}" fill="none" stroke="#f1f5f9" stroke-width="10"/>
                        <circle cx="50" cy="50" r="{{ $circ_r }}" fill="none" stroke="#2563eb" stroke-width="10"
                            stroke-dasharray="{{ $circ_c }}"
                            stroke-dashoffset="{{ $offset }}"
                            stroke-linecap="round"
                            transform="rotate(-90 50 50)"/>
                        <text x="50" y="50" text-anchor="middle" dominant-baseline="central"
                              font-size="18" font-weight="800" fill="#111827">{{ $pct }}%</text>
                    </svg>
                </div>
                <div class="sb-stat-row">
                    <span class="sb-stat-label">Lessons Done</span>
                    <span class="sb-stat-val">{{ $lessonProgressMap->where('status','completed')->count() }} / {{ $enrollment->course->lessons->count() }}</span>
                </div>
                <div class="sb-stat-row">
                    <span class="sb-stat-label">Status</span>
                    <span class="sb-stat-val">
                        @if($enrollment->completion_status === 'completed')
                            <span style="color:#16a34a;">Completed ✓</span>
                        @else
                            <span style="color:#2563eb;">In Progress</span>
                        @endif
                    </span>
                </div>
                <div class="sb-stat-row">
                    <span class="sb-stat-label">Certificate</span>
                    <span class="sb-stat-val">
                        @if($enrollment->certificate_status === 'issued')
                            <span style="color:#16a34a;">Issued ✓</span>
                        @elseif($enrollment->certificate_status === 'eligible')
                            <span style="color:#2563eb;">Eligible</span>
                        @else
                            <span style="color:#9ca3af;">Not yet</span>
                        @endif
                    </span>
                </div>
                @if($enrollment->expires_at)
                <div class="sb-stat-row">
                    <span class="sb-stat-label">Expires</span>
                    <span class="sb-stat-val" style="font-size:12px;">{{ $enrollment->expires_at->format('d M Y') }}</span>
                </div>
                @endif
            </div>
        </div>

        {{-- Next action ── --}}
        @php
            $firstIncomplete = $enrollment->course->lessons->first(function($l) use ($lessonProgressMap) {
                $lp = $lessonProgressMap->get($l->id);
                return !$lp || $lp->status !== 'completed';
            });
        @endphp
        @if($firstIncomplete && $enrollment->access_status === 'unlocked' && $enrollment->completion_status !== 'completed')
        <div class="sb-card">
            <div class="sb-card-head">Next Up</div>
            <div class="sb-card-body">
                <div style="font-size:13px;font-weight:700;color:#111827;margin-bottom:10px;line-height:1.4;">{{ $firstIncomplete->title }}</div>
                <a href="{{ route('participant.lesson.show', [$enrollment->id, $firstIncomplete->id]) }}"
                   style="display:flex;align-items:center;justify-content:center;gap:6px;background:#1e3a8a;color:#fff;text-decoration:none;font-weight:700;font-size:13px;padding:10px;border-radius:9px;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                    Continue Learning
                </a>
            </div>
        </div>
        @endif

    </div>

</div>

@endsection
