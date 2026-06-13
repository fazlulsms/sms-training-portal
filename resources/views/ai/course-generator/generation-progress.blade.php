@extends('layouts.app')
@section('page-title', 'Generating Course — ' . $course->name)

@section('content')
<style>
@keyframes spin { to { transform:rotate(360deg); } }
.gen-spinner {
    width:18px; height:18px;
    border:3px solid #e5e7eb;
    border-top-color:#1e3a8a;
    border-radius:50%;
    animation:spin .8s linear infinite;
    display:inline-block;
}
.lesson-row { display:flex; align-items:center; gap:12px; padding:10px 20px; border-top:1px solid #f3f4f6; transition:background .3s; }
.lesson-icon { width:30px; height:30px; border-radius:50%; background:#f1f5f9; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:15px; transition:all .3s; }
.stat-card { text-align:center; padding:16px 12px; background:#f8fafc; border-radius:10px; }
.stat-card .val { font-size:26px; font-weight:800; }
.stat-card .lbl { font-size:11px; color:#6b7280; margin-top:3px; }
.action-btn { display:inline-flex; align-items:center; gap:8px; padding:12px 24px; border-radius:9px; font-weight:700; font-size:14px; text-decoration:none; cursor:pointer; border:none; }
.phase-badge { display:inline-block; background:#e0e7ff; color:#3730a3; border-radius:6px; padding:2px 10px; font-size:11px; font-weight:700; letter-spacing:.3px; }
.phase-badge.active { background:#1e3a8a; color:#fff; }
.phase-badge.done   { background:#d1fae5; color:#065f46; }
</style>

<div style="max-width:880px; margin:auto; padding:24px 0;">

    {{-- ── Header ──────────────────────────────────────────────────── --}}
    <div style="background:linear-gradient(135deg,#0f1e45,#1e3a8a); border-radius:14px; padding:24px 28px; margin-bottom:20px; color:#fff;">
        <div style="font-size:11px; text-transform:uppercase; letter-spacing:1px; color:#93c5fd; margin-bottom:6px;">
            ✨ Mode B — Complete eLearning Course Generation
        </div>
        <div style="font-size:22px; font-weight:800; margin-bottom:4px;">{{ $course->name }}</div>
        <div style="font-size:13px; color:#bfdbfe;">
            {{ $lessons->count() }} lessons &middot; {{ count($modules) }} modules &middot; Level: {{ $level }}
            @if($course->duration)
                &middot; Duration: {{ $course->duration }}
            @endif
        </div>
    </div>

    {{-- ── Duration Warning ─────────────────────────────────────────── --}}
    @if($durationWarning && $totalLessonMin > 0 && $requestedMinutes > 0)
    <div style="background:#fff7ed; border:1.5px solid #fed7aa; border-radius:10px; padding:14px 18px; margin-bottom:18px; display:flex; align-items:flex-start; gap:12px;">
        <span style="font-size:20px; flex-shrink:0;">⚠</span>
        <div>
            <div style="font-weight:700; color:#c2410c; margin-bottom:3px;">Duration Notice</div>
            <div style="font-size:13px; color:#92400e;">
                The AI designed <strong>{{ round($totalLessonMin / 60, 1) }} hours</strong> of content for a
                <strong>{{ round($requestedMinutes / 60, 1) }}-hour</strong> course
                ({{ round(($totalLessonMin - $requestedMinutes) / 60, 1) }}h over target).
                Content will still be generated for all lessons. Review and trim after generation if needed.
            </div>
        </div>
    </div>
    @endif

    {{-- ── Phase Tracker ────────────────────────────────────────────── --}}
    <div style="background:#fff; border-radius:12px; padding:16px 24px; margin-bottom:18px; box-shadow:0 2px 12px rgba(0,0,0,.08); display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
        <span style="font-size:12px; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:.5px;">Generation Phase:</span>
        <span id="phase1Badge" class="phase-badge active">1 — Lesson Content</span>
        <span style="color:#d1d5db;">→</span>
        <span id="phase2Badge" class="phase-badge">2 — Module Quizzes</span>
        <span style="color:#d1d5db;">→</span>
        <span id="phase3Badge" class="phase-badge">3 — Final Assessment</span>
    </div>

    {{-- ── Overall Progress ────────────────────────────────────────── --}}
    <div id="progressCard" style="background:#fff; border-radius:12px; padding:22px 24px; margin-bottom:18px; box-shadow:0 2px 12px rgba(0,0,0,.08);">
        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:14px; flex-wrap:wrap; gap:8px;">
            <div>
                <div id="statusHeading" style="font-size:16px; font-weight:700; color:#1e3a8a;">Preparing generation…</div>
                <div id="statusSub"     style="font-size:12.5px; color:#6b7280; margin-top:3px;">Do not close this page until generation completes.</div>
            </div>
            <div style="text-align:right;">
                <div id="progressFraction" style="font-size:26px; font-weight:800; color:#1e3a8a;">0 / {{ $lessons->count() }}</div>
                <div style="font-size:11px; color:#9ca3af;">Lessons Complete</div>
            </div>
        </div>

        {{-- Progress bar --}}
        <div style="background:#e5e7eb; border-radius:99px; height:12px; overflow:hidden; margin-bottom:16px;">
            <div id="progressBar" style="background:linear-gradient(90deg,#1e3a8a,#2563eb); height:100%; width:0%; border-radius:99px; transition:width .5s ease;"></div>
        </div>

        {{-- Stats row --}}
        <div style="display:grid; grid-template-columns:repeat(5,1fr); gap:12px;">
            <div class="stat-card"><div id="statDone"    class="val" style="color:#2563eb;">0</div><div class="lbl">Lessons Done</div></div>
            <div class="stat-card"><div id="statBlocks"  class="val" style="color:#059669;">0</div><div class="lbl">Blocks Generated</div></div>
            <div class="stat-card"><div id="statQuizzes" class="val" style="color:#7c3aed;">0</div><div class="lbl">Module Quizzes</div></div>
            <div class="stat-card"><div id="statFinal"   class="val" style="color:#0891b2;">—</div><div class="lbl">Final Assessment</div></div>
            <div class="stat-card"><div id="statFailed"  class="val" style="color:#9ca3af;">0</div><div class="lbl">Failed</div></div>
        </div>
    </div>

    {{-- ── Module / Lesson List ─────────────────────────────────────── --}}
    <div style="background:#fff; border-radius:12px; box-shadow:0 2px 12px rgba(0,0,0,.08); overflow:hidden; margin-bottom:20px;">

        @foreach($modules as $mod)
        <div style="border-bottom:1px solid #f3f4f6;">

            {{-- Module header --}}
            <div style="padding:11px 20px; background:#f8fafc; display:flex; align-items:center; gap:10px; justify-content:space-between;">
                <div style="display:flex; align-items:center; gap:10px;">
                    <span style="background:#e0e7ff; color:#3730a3; border-radius:6px; padding:2px 9px; font-size:11px; font-weight:700;">
                        Module {{ $mod['index'] }}
                    </span>
                    <span style="font-size:13px; font-weight:700; color:#374151;">{{ $mod['title'] }}</span>
                </div>
                <div id="mod-quiz-status-{{ $mod['index'] }}" style="font-size:11.5px; color:#9ca3af;"></div>
            </div>

            {{-- Lessons --}}
            @foreach($mod['lessons'] as $lesson)
            <div id="row-{{ $lesson['id'] }}" class="lesson-row">
                <div id="icon-{{ $lesson['id'] }}" class="lesson-icon">⏳</div>
                <div style="flex:1; min-width:0;">
                    <div style="font-size:13.5px; font-weight:600; color:#111827; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                        {{ $lesson['title'] }}
                    </div>
                    <div id="sub-{{ $lesson['id'] }}" style="font-size:11.5px; color:#9ca3af; margin-top:2px;">
                        Pending
                        @if($lesson['duration_minutes'])
                            · {{ $lesson['duration_minutes'] }} min
                        @endif
                    </div>
                </div>
                <div id="blocks-{{ $lesson['id'] }}" style="font-size:12px; color:#9ca3af; white-space:nowrap; flex-shrink:0;"></div>
            </div>
            @endforeach

        </div>
        @endforeach

        {{-- Final Assessment row --}}
        <div style="border-top:2px solid #e0e7ff;">
            <div style="padding:11px 20px; background:#f0f4ff; display:flex; align-items:center; gap:10px; justify-content:space-between;">
                <div style="display:flex; align-items:center; gap:10px;">
                    <span style="background:#1e3a8a; color:#fff; border-radius:6px; padding:2px 9px; font-size:11px; font-weight:700;">Phase 3</span>
                    <span style="font-size:13px; font-weight:700; color:#1e3a8a;">Final Course Assessment</span>
                </div>
                <div id="final-assessment-status" style="font-size:11.5px; color:#9ca3af;">Pending</div>
            </div>
        </div>

    </div>

    {{-- ── Completion Panel ─────────────────────────────────────────── --}}
    <div id="completionPanel" style="display:none; background:#fff; border-radius:14px; box-shadow:0 4px 28px rgba(0,0,0,.12); overflow:hidden; margin-bottom:24px;">
        <div id="completionHeader" style="background:linear-gradient(135deg,#064e3b,#059669); padding:22px 28px; color:#fff;">
            <div style="font-size:12px; color:#a7f3d0; margin-bottom:5px;">✅ Generation Complete</div>
            <div style="font-size:20px; font-weight:800;">Course Ready for Review</div>
            <div style="font-size:13px; color:#d1fae5; margin-top:4px;">{{ $course->name }}</div>
        </div>
        <div style="padding:24px 28px;">
            <div id="completionStats" style="display:grid; grid-template-columns:repeat(3,1fr); gap:14px; margin-bottom:20px;"></div>
            <div id="failureAlert" style="display:none; background:#fff7ed; border:1px solid #fed7aa; border-radius:8px; padding:12px 16px; margin-bottom:20px; font-size:13px; color:#c2410c;"></div>
            <div style="display:flex; flex-wrap:wrap; gap:10px;">
                <a href="{{ route('elearning.courses.edit', $course->id) }}"
                   class="action-btn" style="background:linear-gradient(135deg,#1e3a8a,#2563eb); color:#fff;">
                    📋 Review Course
                </a>
                <a href="{{ route('lessons.index', $course->id) }}"
                   class="action-btn" style="background:#f8fafc; color:#374151; border:1.5px solid #e5e7eb;">
                    📚 View All Lessons
                </a>
                <button onclick="retryFailed()" id="retryBtn" style="display:none;"
                        class="action-btn" style="background:#fff7ed; color:#c2410c; border:1.5px solid #fed7aa;">
                    🔄 Retry Failed Lessons
                </button>
            </div>
        </div>
    </div>

</div>

<script>
// ── Server data ────────────────────────────────────────────────────
const CSRF       = '{{ csrf_token() }}';
const LEVEL      = '{{ $level }}';
const TOTAL      = {{ $lessons->count() }};
const COURSE_ID  = {{ $course->id }};
const GEN_URL    = '{{ route('ai.course-generator.generate-next', $course->id) }}';
const QUIZ_URL   = '{{ route('ai.course-generator.generate-module-quiz', $course->id) }}';
const FINAL_URL  = '{{ route('ai.course-generator.generate-final-assessment', $course->id) }}';

const MODULES = @json(collect($modules)->map(fn($m) => [
    'index'   => $m['index'],
    'title'   => $m['title'],
    'lessons' => collect($m['lessons'])->map(fn($l) => [
        'id'          => $l['id'],
        'title'       => $l['title'],
        'lesson_type' => $l['lesson_type'] ?? 'concept',
    ])->values(),
])->values());

// ── State ──────────────────────────────────────────────────────────
let doneCount    = 0;
let failCount    = 0;
let totalBlocks  = 0;
let quizCount    = 0;
let lessonSeq    = 0; // global lesson number (1-based)
const failedLessons = [];

// ── UI helpers ─────────────────────────────────────────────────────
function setLessonState(id, state, blocksCreated) {
    const icon = document.getElementById('icon-'   + id);
    const sub  = document.getElementById('sub-'    + id);
    const blk  = document.getElementById('blocks-' + id);
    const row  = document.getElementById('row-'    + id);
    if (!icon) return;

    if (state === 'generating') {
        icon.innerHTML        = '<div class="gen-spinner"></div>';
        icon.style.background = '#eff6ff';
        sub.textContent       = 'Generating…';
        sub.style.color       = '#2563eb';
        row.style.background  = '#eff6ff';
    } else if (state === 'done') {
        icon.textContent      = '✅';
        icon.style.background = '#d1fae5';
        sub.textContent       = 'Complete';
        sub.style.color       = '#059669';
        if (blk) blk.textContent = (blocksCreated || 0) + ' blocks';
        row.style.background  = '#f0fdf4';
    } else if (state === 'failed') {
        icon.textContent      = '❌';
        icon.style.background = '#fee2e2';
        sub.textContent       = 'Failed — retry manually';
        sub.style.color       = '#dc2626';
        row.style.background  = '#fef2f2';
    }
}

function setModuleQuizState(modIndex, state, questionsCreated) {
    const el = document.getElementById('mod-quiz-status-' + modIndex);
    if (!el) return;
    if (state === 'generating') {
        el.innerHTML  = '<span class="gen-spinner" style="vertical-align:middle;"></span> Generating quiz…';
        el.style.color = '#7c3aed';
    } else if (state === 'done') {
        el.textContent = '✅ ' + (questionsCreated || 0) + ' questions';
        el.style.color = '#059669';
    } else if (state === 'failed') {
        el.textContent = '⚠ Quiz skipped';
        el.style.color = '#d97706';
    }
}

function setFinalAssessmentState(state, questionsCreated) {
    const el = document.getElementById('final-assessment-status');
    if (!el) return;
    if (state === 'generating') {
        el.innerHTML   = '<span class="gen-spinner" style="vertical-align:middle;"></span> Generating final assessment…';
        el.style.color = '#0891b2';
    } else if (state === 'done') {
        el.textContent = '✅ ' + (questionsCreated || 0) + ' questions — Pass mark: 70% — 2 attempts';
        el.style.color = '#059669';
        document.getElementById('statFinal').textContent = questionsCreated || '✅';
        document.getElementById('statFinal').style.color = '#059669';
    } else if (state === 'failed') {
        el.textContent = '⚠ Final assessment skipped';
        el.style.color = '#d97706';
        document.getElementById('statFinal').textContent = '⚠';
        document.getElementById('statFinal').style.color = '#d97706';
    }
}

function setPhase(phase) {
    const badges = {
        1: document.getElementById('phase1Badge'),
        2: document.getElementById('phase2Badge'),
        3: document.getElementById('phase3Badge'),
    };
    for (const [p, el] of Object.entries(badges)) {
        if (!el) continue;
        el.className = 'phase-badge' + (parseInt(p) < phase ? ' done' : (parseInt(p) === phase ? ' active' : ''));
    }
}

function updateProgress() {
    const processed = doneCount + failCount;
    const pct       = TOTAL > 0 ? Math.round(processed / TOTAL * 100) : 0;
    document.getElementById('progressBar').style.width      = pct + '%';
    document.getElementById('progressFraction').textContent = processed + ' / ' + TOTAL;
    document.getElementById('statDone').textContent    = doneCount;
    document.getElementById('statBlocks').textContent  = totalBlocks;
    document.getElementById('statQuizzes').textContent = quizCount;
    document.getElementById('statFailed').textContent  = failCount;
    if (failCount > 0) document.getElementById('statFailed').style.color = '#dc2626';
}

// ── Phase 1: Lesson content generation ────────────────────────────
async function generateAllLessons() {
    setPhase(1);
    let globalLessonNum = 0;

    for (const mod of MODULES) {
        for (const lesson of mod.lessons) {
            globalLessonNum++;
            const heading = `Generating lesson content… (Lesson ${globalLessonNum} of ${TOTAL})`;
            document.getElementById('statusHeading').textContent = heading;
            document.getElementById('statusSub').textContent     = lesson.title;
            setLessonState(lesson.id, 'generating');

            let success = false, blocks = 0;

            try {
                const res  = await fetch(GEN_URL, {
                    method:  'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                    body:    JSON.stringify({
                        lesson_id:     lesson.id,
                        level:         LEVEL,
                        lesson_type:   lesson.lesson_type || 'concept',
                        lesson_number: globalLessonNum,
                        total_lessons: TOTAL,
                    }),
                });
                const data = res.ok ? await res.json() : {};
                success    = !!(data.success);
                blocks     = data.blocks_created || 0;
            } catch (e) { /* network error — treated as failure */ }

            if (success) {
                doneCount++;
                totalBlocks += blocks;
                setLessonState(lesson.id, 'done', blocks);
            } else {
                failCount++;
                failedLessons.push(lesson);
                setLessonState(lesson.id, 'failed');
            }
            updateProgress();
        }

        // Phase 2: Module quiz after each module
        await generateModuleQuiz(mod);
    }
}

// ── Phase 2: Module quiz ───────────────────────────────────────────
async function generateModuleQuiz(mod) {
    setPhase(2);
    const heading = `Generating Module Quiz ${mod.index} of ${MODULES.length}…`;
    document.getElementById('statusHeading').textContent = heading;
    document.getElementById('statusSub').textContent     = mod.title;
    setModuleQuizState(mod.index, 'generating');

    try {
        const res  = await fetch(QUIZ_URL, {
            method:  'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body:    JSON.stringify({
                module_title: mod.title,
                module_index: mod.index,
                lesson_ids:   mod.lessons.map(l => l.id),
                level:        LEVEL,
            }),
        });
        const data = res.ok ? await res.json() : {};

        if (data.success) {
            quizCount++;
            updateProgress();
            setModuleQuizState(mod.index, 'done', data.questions_created);
        } else {
            setModuleQuizState(mod.index, 'failed');
        }
    } catch {
        setModuleQuizState(mod.index, 'failed');
    }
}

// ── Phase 3: Final assessment ──────────────────────────────────────
async function generateFinalAssessment() {
    setPhase(3);
    document.getElementById('statusHeading').textContent = 'Generating Final Course Assessment…';
    document.getElementById('statusSub').textContent     = 'Building comprehensive exam covering all modules. This may take 30–60 seconds.';
    setFinalAssessmentState('generating');

    try {
        const res  = await fetch(FINAL_URL, {
            method:  'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body:    JSON.stringify({ level: LEVEL }),
        });
        const data = res.ok ? await res.json() : {};

        if (data.success) {
            setFinalAssessmentState('done', data.questions_created);
        } else {
            setFinalAssessmentState('failed');
        }
    } catch {
        setFinalAssessmentState('failed');
    }
}

// ── Completion panel ───────────────────────────────────────────────
function showCompletion() {
    document.getElementById('progressBar').style.cssText += '; width:100%; background:linear-gradient(90deg,#059669,#10b981);';
    document.getElementById('statusHeading').textContent  = '✅ Generation complete';
    document.getElementById('statusSub').textContent      = 'All lessons, quizzes, and final assessment processed. Review your course below.';

    const finalQs = document.getElementById('statFinal').textContent;
    const stats = [
        { val: {{ count($modules) }}, lbl: 'Modules',          color: '#1e3a8a' },
        { val: doneCount,              lbl: 'Lessons Done',     color: '#059669' },
        { val: totalBlocks,            lbl: 'Content Blocks',   color: '#d97706' },
        { val: quizCount,              lbl: 'Module Quizzes',   color: '#7c3aed' },
        { val: finalQs,                lbl: 'Final Exam Qs',    color: '#0891b2' },
        { val: failCount,              lbl: 'Failed',           color: failCount > 0 ? '#dc2626' : '#9ca3af' },
    ];

    document.getElementById('completionStats').innerHTML = stats.map(s =>
        `<div class="stat-card">
            <div class="val" style="color:${s.color};">${s.val}</div>
            <div class="lbl">${s.lbl}</div>
         </div>`
    ).join('');

    if (failCount > 0) {
        const fa = document.getElementById('failureAlert');
        fa.style.display = 'block';
        fa.innerHTML = `<strong>${failCount} lesson(s) failed to generate.</strong> Open the course editor and use the AI Content Generator on each failed lesson individually.`;
        document.getElementById('retryBtn').style.display = 'inline-flex';
    }

    document.getElementById('completionPanel').style.display = 'block';
    setTimeout(() => document.getElementById('completionPanel').scrollIntoView({ behavior:'smooth', block:'start' }), 200);
}

// ── Retry failed lessons ───────────────────────────────────────────
async function retryFailed() {
    if (failedLessons.length === 0) return;
    document.getElementById('retryBtn').style.display = 'none';
    document.getElementById('completionPanel').style.display = 'none';

    const toRetry = [...failedLessons];
    failedLessons.length = 0;
    failCount = 0;
    let retryNum = 0;

    for (const lesson of toRetry) {
        retryNum++;
        document.getElementById('statusHeading').textContent = `Retrying failed lessons… (${retryNum} of ${toRetry.length})`;
        document.getElementById('statusSub').textContent     = lesson.title;
        setLessonState(lesson.id, 'generating');

        let success = false, blocks = 0;
        try {
            const res  = await fetch(GEN_URL, {
                method:  'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                body:    JSON.stringify({
                    lesson_id:     lesson.id,
                    level:         LEVEL,
                    lesson_type:   lesson.lesson_type || 'concept',
                    lesson_number: retryNum,
                    total_lessons: toRetry.length,
                }),
            });
            const data = res.ok ? await res.json() : {};
            success    = !!(data.success);
            blocks     = data.blocks_created || 0;
        } catch {}

        if (success) {
            doneCount++;
            totalBlocks += blocks;
            setLessonState(lesson.id, 'done', blocks);
        } else {
            failCount++;
            failedLessons.push(lesson);
            setLessonState(lesson.id, 'failed');
        }
        updateProgress();
    }

    showCompletion();
}

// ── Boot ───────────────────────────────────────────────────────────
(async function () {
    await generateAllLessons();
    await generateFinalAssessment();
    showCompletion();
})();
</script>
@endsection
