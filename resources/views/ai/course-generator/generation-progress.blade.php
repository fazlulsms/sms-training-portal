@extends('layouts.app')
@section('page-title', 'Generating Course — ' . $course->name)

@section('content')
<style>
.ai-badge-icon {
    width: 44px; height: 44px; border-radius: 12px;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.phase-badge { font-size: 0.78rem; transition: background 0.3s, color 0.3s; }
.phase-badge.active  { background: #0d6efd !important; color: #fff !important; }
.phase-badge.done    { background: #198754 !important; color: #fff !important; }
.stat-tile {
    background: #fff; border: 1px solid #e9ecef; border-radius: 12px;
    padding: 16px; text-align: center;
    box-shadow: 0 1px 4px rgba(0,0,0,.04);
}
.stat-icon {
    width: 40px; height: 40px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 8px;
}
.stat-val { font-size: 1.6rem; font-weight: 700; line-height: 1; }
.stat-lbl { font-size: 0.72rem; color: #6c757d; margin-top: 4px; }
</style>

<div class="container-fluid py-4" id="gen-page">

    {{-- Header --}}
    <div class="d-flex align-items-center gap-3 mb-4">
        <div class="ai-badge-icon">
            <i class="fas fa-robot fa-lg text-white"></i>
        </div>
        <div>
            <h4 class="mb-0 fw-bold">AI Course Generator</h4>
            <p class="text-muted mb-0 small">{{ $course->name }}</p>
        </div>
        <span class="badge bg-primary ms-auto px-3 py-2">Mode B — Full Course</span>
    </div>

    {{-- Status Card --}}
    <div class="card shadow-sm mb-4" id="status-card">
        <div class="card-body p-4">

            {{-- Phase Tracker --}}
            <div class="d-flex flex-wrap align-items-center gap-2 mb-3" id="phase-tracker">
                <span class="badge rounded-pill px-3 py-2 phase-badge" id="phase-1" style="background:#e9ecef;color:#495057;">
                    <i class="fas fa-book-open me-1"></i> Phase 1: Lesson Content
                </span>
                <i class="fas fa-chevron-right text-muted small"></i>
                <span class="badge rounded-pill px-3 py-2 phase-badge" id="phase-2" style="background:#e9ecef;color:#495057;">
                    <i class="fas fa-question-circle me-1"></i> Phase 2: Module Quizzes
                </span>
                <i class="fas fa-chevron-right text-muted small"></i>
                <span class="badge rounded-pill px-3 py-2 phase-badge" id="phase-3" style="background:#e9ecef;color:#495057;">
                    <i class="fas fa-graduation-cap me-1"></i> Phase 3: Final Assessment
                </span>
            </div>

            {{-- Status Heading --}}
            <h5 class="fw-semibold mb-1" id="status-heading">
                <span class="spinner-border spinner-border-sm text-primary me-2" id="status-spinner"></span>
                <span id="status-text">Queued — waiting for queue worker…</span>
            </h5>
            <p class="text-muted small mb-3" id="status-sub">
                You can close this tab. Generation continues in the background.
            </p>

            {{-- Progress Bar --}}
            <div class="progress mb-1" style="height:12px;border-radius:8px;">
                <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                     id="progress-bar" role="progressbar" style="width:0%"></div>
            </div>
            <div class="d-flex justify-content-between small text-muted mb-4">
                <span id="lessons-counter">Lessons: 0 / 0</span>
                <span id="progress-pct">0%</span>
            </div>

            {{-- Stats Grid --}}
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="stat-tile">
                        <div class="stat-icon bg-primary-subtle"><i class="fas fa-book text-primary"></i></div>
                        <div class="stat-val" id="stat-lessons">0</div>
                        <div class="stat-lbl">Lessons Done</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-tile">
                        <div class="stat-icon bg-success-subtle"><i class="fas fa-layer-group text-success"></i></div>
                        <div class="stat-val" id="stat-blocks">0</div>
                        <div class="stat-lbl">Content Blocks</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-tile">
                        <div class="stat-icon bg-warning-subtle"><i class="fas fa-question-circle text-warning"></i></div>
                        <div class="stat-val" id="stat-quizzes">0</div>
                        <div class="stat-lbl">Module Quizzes</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-tile">
                        <div class="stat-icon bg-info-subtle"><i class="fas fa-graduation-cap text-info"></i></div>
                        <div class="stat-val" id="stat-final">–</div>
                        <div class="stat-lbl">Final Questions</div>
                    </div>
                </div>
            </div>

            {{-- Completed Panel --}}
            <div id="completed-panel" class="d-none">
                <div class="alert alert-success d-flex align-items-center gap-3">
                    <i class="fas fa-check-circle fa-2x text-success flex-shrink-0"></i>
                    <div>
                        <strong>Course Generated Successfully!</strong><br>
                        <span id="completed-summary" class="small text-muted"></span>
                    </div>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('elearning.courses.edit', $course->id) }}" class="btn btn-success">
                        <i class="fas fa-edit me-1"></i> Review &amp; Publish Course
                    </a>
                    <a href="{{ url('/admin/ai/course-generator') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-plus me-1"></i> Generate Another Course
                    </a>
                </div>
            </div>

            {{-- Failed Panel --}}
            <div id="failed-panel" class="d-none">
                <div class="alert alert-danger d-flex align-items-center gap-3">
                    <i class="fas fa-exclamation-triangle fa-2x text-danger flex-shrink-0"></i>
                    <div>
                        <strong>Generation Failed</strong><br>
                        <span id="failed-error" class="small text-muted"></span>
                    </div>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('elearning.courses.edit', $course->id) }}" class="btn btn-outline-danger">
                        <i class="fas fa-wrench me-1"></i> Open Course Editor
                    </a>
                </div>
            </div>

        </div>
    </div>

    {{-- Info Box --}}
    <div class="card border-0 bg-light">
        <div class="card-body small text-muted">
            <i class="fas fa-info-circle text-primary me-2"></i>
            <strong>Background Generation:</strong>
            AI is building all lesson content, module quizzes, and a final assessment automatically.
            You can safely close this tab — check back in a few minutes. If generation seems stuck,
            ensure the queue worker is running:
            <code>php artisan queue:work --timeout=10800 --tries=1</code>
        </div>
    </div>

</div>

<script>
(function () {
    const STATUS_URL = '{{ route('ai.course-generator.generation-status', $course->id) }}';
    const POLL_MS    = 3000;
    let   timer      = null;
    let   stopped    = false;

    function updatePhase(phase) {
        var phases = { lessons: 1, module_quiz: 2, final_assessment: 3, completed: 3 };
        var active = phases[phase] || 0;
        [1, 2, 3].forEach(function(n) {
            var el = document.getElementById('phase-' + n);
            if (!el) return;
            el.classList.remove('active', 'done');
            if (n < active)  el.classList.add('done');
            if (n === active) el.classList.add('active');
        });
    }

    function setText(id, val) {
        var el = document.getElementById(id);
        if (el) el.textContent = val;
    }

    function poll() {
        if (stopped) return;

        fetch(STATUS_URL, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                var status   = data.gen_status   || 'none';
                var progress = data.gen_progress || {};

                var done    = parseInt(progress.lessons_done     || 0);
                var total   = parseInt(progress.total_lessons    || 0);
                var blocks  = parseInt(progress.blocks_generated || 0);
                var quizzes = parseInt(progress.quizzes_done     || 0);
                var finals  = parseInt(progress.final_questions  || 0);
                var pct     = total > 0 ? Math.min(100, Math.round((done / total) * 100)) : 0;
                var step    = progress.current_step || status;
                var phase   = progress.phase || '';

                setText('stat-lessons', done);
                setText('stat-blocks',  blocks);
                setText('stat-quizzes', quizzes);
                setText('stat-final',   finals > 0 ? finals : (status === 'completed' ? '✓' : '–'));
                setText('lessons-counter', 'Lessons: ' + done + ' / ' + total);
                setText('progress-pct', pct + '%');

                var bar = document.getElementById('progress-bar');
                if (bar) bar.style.width = pct + '%';

                updatePhase(phase);

                if (status === 'running') {
                    setText('status-text', step);
                    scheduleNext();

                } else if (status === 'pending' || status === 'none') {
                    setText('status-text', 'Queued — waiting for queue worker…');
                    setText('status-sub', 'Make sure the queue worker is running: php artisan queue:work --timeout=10800 --tries=1');
                    scheduleNext();

                } else if (status === 'completed') {
                    stopped = true;
                    clearTimeout(timer);

                    setText('status-text', 'Generation Complete!');
                    var spinner = document.getElementById('status-spinner');
                    if (spinner) spinner.classList.add('d-none');

                    if (bar) {
                        bar.style.width = '100%';
                        bar.classList.remove('progress-bar-animated', 'progress-bar-striped');
                        bar.classList.add('bg-success');
                    }

                    setText('stat-final', finals > 0 ? finals : '✓');
                    updatePhase('completed');
                    [1, 2, 3].forEach(function(n) {
                        var el = document.getElementById('phase-' + n);
                        if (el) { el.classList.remove('active'); el.classList.add('done'); }
                    });

                    setText('completed-summary', step);
                    document.getElementById('completed-panel').classList.remove('d-none');
                    var sub = document.getElementById('status-sub');
                    if (sub) sub.classList.add('d-none');
                    setText('lessons-counter', 'Lessons: ' + done + ' / ' + total);
                    setText('progress-pct', '100%');

                } else if (status === 'failed') {
                    stopped = true;
                    clearTimeout(timer);

                    setText('status-text', 'Generation Failed');
                    var spinner2 = document.getElementById('status-spinner');
                    if (spinner2) spinner2.classList.add('d-none');

                    if (bar) {
                        bar.classList.remove('progress-bar-animated', 'progress-bar-striped');
                        bar.classList.add('bg-danger');
                    }

                    setText('failed-error', progress.error || step || 'Unknown error. Check server logs.');
                    document.getElementById('failed-panel').classList.remove('d-none');
                    var sub2 = document.getElementById('status-sub');
                    if (sub2) sub2.classList.add('d-none');
                }
            })
            .catch(function(err) {
                console.error('Poll error:', err);
                scheduleNext();
            });
    }

    function scheduleNext() {
        if (!stopped) timer = setTimeout(poll, POLL_MS);
    }

    poll();
})();
</script>
@endsection
