@extends('layouts.learner-lesson')
@section('page-title', $lesson->title)
@section('content')

@php
    $previewMode = $previewMode ?? false;
    $lpMap       = $previewMode ? collect() : $enrollment->lessonProgress->keyBy('lesson_id');
    $courseName  = $previewMode ? ($previewCourse->name ?? $lesson->course->name) : $enrollment->course->name;
    $completedN  = $lpMap->where('status', 'completed')->count();
    $totalN      = $lessons->count();
    $pct         = $totalN > 0 ? round(($completedN / $totalN) * 100) : 0;

    // Audio completion — defaults for preview mode / non-audio lessons
    $requiresAudioCompletion = $requiresAudioCompletion ?? false;
    $audioProgressMap        = $audioProgressMap ?? collect();
    $audioRecords            = $audioRecords ?? collect();

    $lessonBlocks    = $lesson->blocks;
    $hasBlocks       = $lessonBlocks->isNotEmpty();
    $blockCount      = $hasBlocks ? $lessonBlocks->count() : 0;
    $isCompleted     = $lessonProgress?->isCompleted();
    $isLocked        = $previewMode ? false : $enrollment->access_status !== 'unlocked';

    $hasQuizzes    = $lesson->quizzes->isNotEmpty();
    $hasResources  = $lesson->resources->isNotEmpty();
    // Show activities panel when there is a ready recap audio, or quizzes/resources
    $hasRecap      = isset($lessonRecapAudio) && $lessonRecapAudio && $lessonRecapAudio->isReady();
    $hasActivities = $hasQuizzes || $hasResources || $hasRecap || ($previewMode ?? false);
    $lastPanel     = $blockCount + ($hasActivities ? 1 : 0);

    $blockTypeIcons = [
        'rich_text'          => '📝', 'video'    => '🎬', 'audio'    => '🎧',
        'image'              => '🖼️', 'gallery'  => '🎨', 'pdf'      => '📄',
        'download'           => '📥', 'slides'   => '🖥️', 'accordion'=> '📂',
        'knowledge_check'    => '❓', 'scenario' => '🎭', 'matching' => '🔗',
        'fun_fact'           => '💡', 'reflection'       => '🤔',
        'click_reveal'       => '👁', 'myth_fact'        => '⚡',
        'workplace_example'  => '🏭', 'case_study'       => '📋',
    ];

    $ytBlocks = [];
    if (!$previewMode) {
        foreach ($lessonBlocks as $bi => $block) {
            if ($block->block_type === 'video' &&
                preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&\s]+)/', $block->content ?? '', $vm)) {
                $ytBlocks[$block->id] = ['ytId' => $vm[1], 'step' => $bi + 1];
            }
        }
    }

    $stepTypes = array_merge(
        ['overview'],
        $lessonBlocks->map(fn($b) => $b->block_type)->values()->all(),
        $hasActivities ? ['activities'] : []
    );
@endphp

<style>
/* ══ SHELL ══════════════════════════════════════════════════ */
.lp-wrap  { display:flex; flex-direction:column; height:100%; overflow:hidden; }
.ls-shell { display:flex; flex:1; min-height:0; overflow:hidden; }
.ls-main  { flex:1; min-width:0; display:flex; flex-direction:column; overflow:hidden; background:#f4f5f7; }

/* ── Step bar ─────────────────────────────────────────────── */
.lf-stepbar {
    flex-shrink:0; background:#fff; border-bottom:1px solid #e5e7eb;
    display:flex; align-items:stretch; height:52px; position:relative; z-index:30;
}
.lf-sb-info {
    display:flex; align-items:center; gap:9px; padding:0 18px; flex-shrink:0;
    border-right:1px solid #f0f2f5; min-width:170px;
}
.lf-sb-icon { font-size:18px; flex-shrink:0; line-height:1; }
.lf-sb-name { font-size:12.5px; font-weight:800; color:#111827; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:140px; }
.lf-sb-sub  { font-size:10.5px; color:#9ca3af; font-weight:600; margin-top:1px; }
.lf-sb-track {
    flex:1; display:flex; align-items:center; padding:0 14px;
    overflow-x:auto; min-width:0;
}
.lf-sb-track::-webkit-scrollbar { height:0; }
.lf-dot {
    flex-shrink:0; width:26px; height:26px; border-radius:50%;
    border:2px solid #e5e7eb; background:#fff;
    display:flex; align-items:center; justify-content:center;
    font-size:10px; font-weight:800; color:#9ca3af;
    cursor:pointer; transition:all .15s; font-family:inherit; padding:0;
}
.lf-dot:hover { border-color:#6366f1; color:#6366f1; transform:scale(1.1); }
.lf-dot.done   { background:#dbeafe; border-color:#93c5fd; color:#1e40af; }
.lf-dot.active { background:#1e3a8a; border-color:#1e3a8a; color:#fff; }
.lf-dot.future { opacity:.28; cursor:not-allowed; }
.lf-dot-line   { flex-shrink:0; height:2px; width:16px; background:#e5e7eb; transition:background .2s; }
.lf-dot-line.done { background:#93c5fd; }
.lf-sb-nav {
    display:flex; align-items:center; gap:2px; padding:0 10px;
    flex-shrink:0; border-left:1px solid #f0f2f5;
}
.lf-sb-btn {
    width:32px; height:32px; border-radius:8px; border:none;
    background:none; cursor:pointer; color:#6b7280;
    display:flex; align-items:center; justify-content:center;
    transition:background .12s,color .12s; font-family:inherit;
}
.lf-sb-btn:hover:not(:disabled) { background:#f3f4f6; color:#111827; }
.lf-sb-btn:disabled { opacity:.3; cursor:not-allowed; }
.lf-progress-line {
    position:absolute; bottom:0; left:0; height:3px;
    background:linear-gradient(90deg,#1e3a8a,#6366f1);
    border-radius:0 2px 0 0; transition:width .4s ease;
}

/* ── Viewport & panels ────────────────────────────────────── */
.lf-viewport { flex:1; overflow:hidden; position:relative; }
.lf-panel {
    position:absolute; inset:0; overflow-y:auto; overflow-x:hidden; display:none;
}
.lf-panel::-webkit-scrollbar { width:5px; }
.lf-panel::-webkit-scrollbar-thumb { background:#d1d5db; border-radius:10px; }
.lf-panel.lf-active { display:block; }
.lf-inner { max-width:860px; margin:0 auto; padding:28px 32px; width:100%; }

/* ── Footer ───────────────────────────────────────────────── */
.lf-footer {
    flex-shrink:0; background:#fff; border-top:1px solid #e5e7eb;
    padding:11px 20px; display:flex; align-items:center;
    justify-content:space-between; gap:10px; flex-wrap:wrap;
    box-shadow:0 -2px 10px rgba(15,23,42,.05);
}
.lf-foot-l { display:flex; align-items:center; gap:8px; }
.lf-foot-c { font-size:11.5px; font-weight:700; color:#9ca3af; white-space:nowrap; }
.lf-foot-r { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
.lfb {
    display:inline-flex; align-items:center; gap:6px;
    padding:9px 16px; border-radius:9px;
    font-weight:700; font-size:13.5px; font-family:inherit;
    text-decoration:none; border:none; cursor:pointer;
    transition:background .15s,transform .1s; white-space:nowrap;
}
.lfb:active { transform:scale(.97); }
.lfb-prev  { background:#f3f4f6; color:#374151; border:1px solid #e5e7eb; }
.lfb-prev:hover { background:#e9ecf0; }
.lfb-next  { background:#1e3a8a; color:#fff; }
.lfb-next:hover { background:#1d4ed8; }
.lfb-ok    { background:#16a34a; color:#fff; }
.lfb-ok:hover { background:#15803d; }
.lfb-done  { background:#f0fdf4; color:#166534; border:1px solid #bbf7d0; cursor:default; }
.lfb-blue  { background:#f0f4ff; color:#1e40af; border:1px solid #c7d2fe; }
.lfb-blue:hover { background:#e0e7ff; }
.lfb-teal  { background:#0f766e; color:#fff; }
.lfb-teal:hover { background:#0d9488; }
.lfb-dis    { background:#e5e7eb; color:#9ca3af; cursor:not-allowed; pointer-events:none; }
.lfb-locked { background:#fef2f2; color:#dc2626; border:1px solid #fecaca; }
.lfb-locked:hover { background:#fee2e2; }
.lfb-amber { background:#fef3c7; color:#92400e; border:1px solid #fde68a; }
.lfb-sm    { font-size:12px; padding:7px 12px; opacity:.75; }

/* ══ OVERVIEW CARD ══════════════════════════════════════════ */
.lesson-overview {
    background:linear-gradient(135deg,#1e3a8a 0%,#2563eb 100%);
    border-radius:16px; padding:28px 32px; color:#fff;
    margin-bottom:20px; box-shadow:0 8px 24px rgba(30,58,138,.22);
    position:relative; overflow:hidden;
}
.lesson-overview::before {
    content:''; position:absolute; top:-40px; right:-40px;
    width:180px; height:180px; background:rgba(255,255,255,.05); border-radius:50%;
}
.lo-breadcrumb { font-size:12px; font-weight:600; opacity:.7; margin-bottom:10px; position:relative; z-index:1; display:flex; align-items:center; gap:6px; }
.lo-title { font-size:22px; font-weight:900; margin:0 0 20px; line-height:1.3; position:relative; z-index:1; }
.lo-meta  { display:flex; flex-wrap:wrap; gap:10px 20px; position:relative; z-index:1; }
.lo-meta-item { display:inline-flex; align-items:center; gap:6px; background:rgba(255,255,255,.12); padding:5px 12px; border-radius:20px; font-size:12.5px; font-weight:600; }
.lo-obj  { background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:20px 24px; margin-top:16px; }
.lo-obj-head { font-size:12px; font-weight:800; color:#374151; text-transform:uppercase; letter-spacing:.5px; margin-bottom:10px; display:flex; align-items:center; gap:7px; }
.lo-obj-body { font-size:14.5px; color:#4b5563; line-height:1.8; white-space:pre-line; }
.lo-desc { background:#f8fafc; border:1px solid #e9ecf0; border-radius:10px; padding:16px 20px; margin-top:12px; font-size:14.5px; color:#374151; line-height:1.7; }

/* ══ BLOCKS ════════════════════════════════════════════════ */
.lb { background:#fff; border-radius:14px; overflow:hidden; box-shadow:0 2px 8px rgba(15,23,42,.06); border:1px solid #e9ecf0; }
.lb + .lb { margin-top:16px; }
.lb-head { padding:14px 22px; border-bottom:1px solid #f0f2f5; display:flex; align-items:center; gap:10px; background:#fafbfc; }
.lb-head-icon { width:30px; height:30px; border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:15px; }
.lh-text  { background:#eff6ff; } .lh-video { background:#f5f3ff; } .lh-audio { background:#ecfdf5; }
.lh-image { background:#fdf2f8; } .lh-gall  { background:#fff7ed; } .lh-acc   { background:#f0fdf4; }
.lh-pdf   { background:#fff1f2; } .lh-dl    { background:#f0fdf4; } .lh-slide { background:#eff6ff; }
.lh-kc    { background:#fffbeb; } .lh-sc    { background:#faf5ff; } .lh-match { background:#f0fdfa; }
.lb-head-label { font-size:12px; font-weight:800; text-transform:uppercase; letter-spacing:.6px; color:#6b7280; }
.lb-head-title { font-size:14.5px; font-weight:700; color:#111827; margin-left:4px; }
.lb-body { padding:26px 28px; font-size:16px; line-height:1.85; color:#374151; }
.lb-body.rt-body h1,.lb-body.rt-body h2,.lb-body.rt-body h3 { color:#111827; margin-top:1.5em; margin-bottom:.5em; }
.lb-body.rt-body p  { margin:0 0 1.2em; }
.lb-body.rt-body ul,.lb-body.rt-body ol { padding-left:1.5em; margin:0 0 1.2em; }
.lb-body.rt-body li { margin-bottom:.4em; }
.lb-body.rt-body strong { color:#111827; }
.lb-body.rt-body code  { background:#f1f5f9; color:#be185d; padding:2px 6px; border-radius:4px; font-size:14px; }
.video-wrap { position:relative; padding-bottom:56.25%; height:0; overflow:hidden; border-radius:10px; background:#000; }
.video-wrap iframe { position:absolute; top:0; left:0; width:100%; height:100%; border:0; }
/* ── YouTube strict controls ──────────────────────────────── */
.yt-resume-overlay {
    position:absolute; inset:0; z-index:10; border-radius:10px;
    background:rgba(0,0,0,.76); display:flex; align-items:center; justify-content:center;
}
.yt-resume-box {
    background:#fff; border-radius:14px; padding:28px 32px; text-align:center;
    max-width:288px; width:90%; box-shadow:0 16px 48px rgba(0,0,0,.38);
}
.yt-resume-icon  { font-size:38px; margin-bottom:8px; }
.yt-resume-title { font-size:17px; font-weight:800; color:#111827; margin:0 0 4px; }
.yt-resume-sub   { font-size:13px; color:#6b7280; margin:0 0 20px; }
.yt-resume-btns  { display:flex; gap:10px; justify-content:center; flex-wrap:wrap; }
.yt-btn-resume  { background:#1e3a8a; color:#fff; border:none; padding:10px 20px; border-radius:9px; font-size:14px; font-weight:700; cursor:pointer; font-family:inherit; transition:background .12s; }
.yt-btn-resume:hover { background:#1d4ed8; }
.yt-btn-restart { background:#f3f4f6; color:#374151; border:1px solid #e5e7eb; padding:10px 14px; border-radius:9px; font-size:14px; font-weight:700; cursor:pointer; font-family:inherit; }
.yt-lock-msg { display:flex; align-items:center; gap:7px; margin-top:12px; padding:11px 16px; background:#fef2f2; border:1px solid #fecaca; border-radius:9px; font-size:13px; font-weight:700; color:#dc2626; }
/* Hides YT title bar (top) and logo (bottom-right) */
.yt-shield-top {
    position:absolute; top:0; left:0; right:130px; height:62px;
    background:linear-gradient(to bottom, rgba(0,0,0,.88) 55%, rgba(0,0,0,0) 100%);
    pointer-events:all; z-index:4; cursor:default; border-radius:10px 0 0 0;
}
.yt-shield-logo {
    position:absolute; bottom:0; left:0; right:0; height:72px;
    background:#0a0a0a; pointer-events:all; z-index:4; cursor:default;
    border-radius:0 0 10px 10px;
}
/* Panel completion lock message */
.panel-lock-msg { display:flex; align-items:center; gap:7px; margin-top:12px; padding:11px 16px; background:#fef3c7; border:1px solid #fde68a; border-radius:9px; font-size:13px; font-weight:700; color:#92400e; }
.acc-item { border-bottom:1px solid #f0f2f5; }
.acc-item:last-child { border-bottom:none; }
.acc-header { width:100%; background:none; border:none; cursor:pointer; display:flex; align-items:center; justify-content:space-between; gap:12px; padding:16px 28px; font-size:15px; font-weight:700; color:#111827; font-family:inherit; text-align:left; transition:background .12s; }
.acc-header:hover { background:#fafbfc; }
.acc-chevron { transition:transform .22s; flex-shrink:0; color:#9ca3af; }
.acc-item.open .acc-chevron { transform:rotate(180deg); }
.acc-body { padding:6px 28px 22px; font-size:15.5px; color:#4b5563; line-height:1.8; }
.kc-question { font-size:16px; font-weight:700; color:#111827; margin:0 0 18px; line-height:1.55; }
.kc-opt-label { display:flex; align-items:center; gap:12px; padding:13px 16px; border:1.5px solid #e5e7eb; border-radius:12px; cursor:pointer; margin-bottom:10px; font-size:15px; font-weight:600; color:#374151; transition:border-color .14s,background .14s; }
.kc-opt-label:hover { border-color:#93c5fd; background:#f0f9ff; }
.kc-opt-label input { display:none; }
.kc-opt-label:has(input:checked) { border-color:#1e3a8a; background:#eff6ff; color:#1e40af; }
.kc-opt-circle { width:20px; height:20px; border-radius:50%; border:2px solid #d1d5db; display:flex; align-items:center; justify-content:center; flex-shrink:0; transition:border-color .14s,background .14s; position:relative; }
.kc-opt-label:has(input:checked) .kc-opt-circle { background:#1e3a8a; border-color:#1e3a8a; }
.kc-opt-circle::after { content:''; width:7px; height:7px; border-radius:50%; background:white; opacity:0; position:absolute; transition:opacity .14s; }
.kc-opt-label:has(input:checked) .kc-opt-circle::after { opacity:1; }
.kc-opt-key { width:26px; height:26px; border-radius:7px; background:#f3f4f6; color:#6b7280; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:800; flex-shrink:0; }
.kc-opt-label:has(input:checked) .kc-opt-key { background:#dbeafe; color:#1e40af; }
.kc-opt-label.correct { border-color:#16a34a!important; background:#f0fdf4!important; color:#15803d!important; }
.kc-opt-label.wrong   { border-color:#dc2626!important; background:#fef2f2!important; color:#991b1b!important; }
.kc-result { padding:12px 16px; border-radius:10px; font-size:14px; font-weight:600; margin-top:14px; line-height:1.5; }
.kc-result-pass { background:#dcfce7; color:#166534; }
.kc-result-fail { background:#fee2e2; color:#991b1b; }
.slide-panel { display:none; }
.slide-panel.active { display:block; }
.slide-nav { display:flex; align-items:center; justify-content:space-between; gap:10px; margin-top:18px; padding-top:18px; border-top:1px solid #f0f2f5; }
.slide-counter { font-size:13px; font-weight:700; color:#6b7280; }
.sc-scenario-text { background:#f8fafc; border-left:4px solid #1e3a8a; padding:16px 20px; border-radius:0 10px 10px 0; font-size:15.5px; line-height:1.75; color:#374151; margin-bottom:20px; }
.sc-opt { margin-bottom:10px; }
.sc-opt-btn { width:100%; background:#f8fafc; border:1.5px solid #e5e7eb; border-radius:12px; padding:14px 16px; cursor:pointer; font-family:inherit; font-size:15px; font-weight:600; color:#374151; text-align:left; display:flex; align-items:center; gap:12px; transition:border-color .14s,background .14s; }
.sc-opt-btn:hover { border-color:#93c5fd; background:#f0f9ff; }
.sc-opt-btn.selected-correct { border-color:#16a34a; background:#f0fdf4; color:#166534; }
.sc-opt-btn.selected-wrong   { border-color:#dc2626; background:#fef2f2; color:#991b1b; }
.sc-opt-letter { width:28px; height:28px; border-radius:50%; background:#e5e7eb; color:#374151; display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:800; flex-shrink:0; }
.sc-exp { background:#fffbeb; border:1px solid #fde68a; border-radius:10px; padding:12px 16px; font-size:14px; color:#92400e; line-height:1.65; margin-top:8px; }
.match-grid { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
@media(max-width:600px){ .match-grid { grid-template-columns:1fr; } }
.match-col-header { font-size:11px; font-weight:800; color:#6b7280; text-transform:uppercase; letter-spacing:.5px; margin-bottom:8px; }
.match-term   { background:#f8fafc; border:1px solid #e5e7eb; border-radius:10px; padding:12px 14px; font-size:14.5px; font-weight:700; color:#111827; margin-bottom:10px; }
.match-select { width:100%; border:1.5px solid #e5e7eb; border-radius:10px; padding:10px 12px; font-family:inherit; font-size:14px; color:#374151; margin-bottom:10px; background:#fff; cursor:pointer; }
.match-select:focus { outline:none; border-color:#6366f1; }
.resource-row { display:flex; align-items:center; justify-content:space-between; padding:12px 16px; border:1px solid #e9ecf0; border-radius:10px; margin-bottom:10px; background:#fafbfc; gap:10px; }
.resource-row:last-child { margin-bottom:0; }
.resource-row a { color:#1e3a8a; font-weight:700; font-size:14.5px; text-decoration:none; }
.resource-row a:hover { text-decoration:underline; }
.resource-type { font-size:10.5px; color:#9ca3af; font-weight:700; text-transform:uppercase; }
.quiz-row { padding:20px; border:1px solid #e9ecf0; border-radius:12px; margin-bottom:12px; background:#fafbfc; }
.quiz-row:last-child { margin-bottom:0; }
.quiz-name      { font-weight:800; font-size:15.5px; color:#111827; margin-bottom:6px; }
.quiz-meta-line { font-size:13px; color:#6b7280; margin-bottom:14px; line-height:1.5; }
.ls-alert { padding:13px 18px; border-radius:10px; font-weight:600; font-size:14px; margin-bottom:16px; display:flex; align-items:flex-start; gap:10px; }
.ls-alert-error   { background:#fef2f2; color:#991b1b; border:1px solid #fecaca; }
.ls-alert-success { background:#f0fdf4; color:#166534; border:1px solid #bbf7d0; }
.lf-empty { text-align:center; padding:60px 20px; background:#fff; border-radius:14px; border:1px solid #e9ecf0; }

/* Lightbox */
#lb-overlay { display:none; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,.9); align-items:center; justify-content:center; flex-direction:column; gap:14px; }
#lb-overlay.open { display:flex; }
#lb-overlay img { max-width:90vw; max-height:82vh; border-radius:10px; object-fit:contain; }
#lb-caption { color:rgba(255,255,255,.75); font-size:13px; }
#lb-close { position:absolute; top:18px; right:22px; background:rgba(255,255,255,.1); border:none; color:white; width:36px; height:36px; border-radius:50%; font-size:20px; cursor:pointer; display:flex; align-items:center; justify-content:center; font-family:inherit; }

@media(max-width:860px) {
    .lf-inner { padding:20px 18px; }
    .lo-title { font-size:19px; }
    .lb-body  { padding:20px; font-size:15.5px; }
    .lf-sb-info { min-width:0; }
    .lf-sb-name { max-width:110px; }
}
@media(max-width:500px) {
    .lf-inner  { padding:14px 12px; }
    .lo-title  { font-size:17px; }
    .lb-body   { padding:16px; font-size:15px; }
    .lfb       { padding:8px 12px; font-size:12.5px; }
    .lf-sb-track { display:none; }
    .lf-sb-info  { border-right:none; }
    .lf-footer   { padding:10px 12px; }
}

/* ══ ENHANCED CONTENT RENDERING ═══════════════════════ */

/* ── Callout Boxes ─────────────────────────────────────── */
.callout {
    display:flex; gap:13px; align-items:flex-start;
    padding:14px 18px; border-radius:10px; margin:16px 0;
    line-height:1.65; font-size:15px;
}
.callout-icon { font-size:19px; flex-shrink:0; line-height:1.45; margin-top:1px; }
.callout-content { flex:1; }
.callout-content strong, .callout-content b { font-weight:800; }
.callout-tip     { background:#fffbeb; border-left:4px solid #f59e0b; color:#78350f; }
.callout-warning { background:#fef2f2; border-left:4px solid #ef4444; color:#7f1d1d; }
.callout-success { background:#f0fdf4; border-left:4px solid #16a34a; color:#14532d; }
.callout-remember{ background:#fff7ed; border-left:4px solid #f97316; color:#7c2d12; }
.callout-example { background:#f5f3ff; border-left:4px solid #7c3aed; color:#4c1d95; }
.callout-info    { background:#eff6ff; border-left:4px solid #2563eb; color:#1e3a8a; }
.callout-note    { background:#f8fafc; border-left:4px solid #64748b; color:#374151; }

/* ── Lesson Tables ─────────────────────────────────────── */
.table-wrap { overflow-x:auto; margin:20px 0; border-radius:12px; box-shadow:0 2px 8px rgba(15,23,42,.07); border:1px solid #e5e7eb; }
.lesson-table { width:100%; border-collapse:collapse; min-width:380px; }
.lesson-table thead th {
    background:linear-gradient(135deg,#1e3a8a,#2563eb); color:#fff;
    padding:12px 16px; font-size:13.5px; font-weight:700;
    text-align:left; white-space:nowrap; letter-spacing:.2px;
}
.lesson-table thead th:first-child { border-radius:12px 0 0 0; }
.lesson-table thead th:last-child  { border-radius:0 12px 0 0; }
.lesson-table tbody td { padding:11px 16px; font-size:14.5px; color:#374151; border-bottom:1px solid #f0f2f5; vertical-align:top; line-height:1.6; }
.lesson-table tbody tr:nth-child(even) td { background:#f8fafc; }
.lesson-table tbody tr:last-child td { border-bottom:none; }
.lesson-table tbody tr:hover td { background:#f0f9ff; transition:background .12s; }

/* ── Process / Step Timeline ───────────────────────────── */
.process-flow { display:flex; flex-direction:column; margin:20px 0; }
.process-step { display:flex; gap:0; }
.ps-left  { display:flex; flex-direction:column; align-items:center; width:46px; flex-shrink:0; }
.ps-num   { width:34px; height:34px; border-radius:50%; background:linear-gradient(135deg,#1e3a8a,#3b82f6); color:#fff; display:flex; align-items:center; justify-content:center; font-size:14px; font-weight:800; flex-shrink:0; box-shadow:0 3px 8px rgba(30,58,138,.3); }
.ps-line  { flex:1; width:2px; background:linear-gradient(to bottom,#93c5fd,#dbeafe); margin:4px 0; min-height:14px; }
.process-step:last-child .ps-line { display:none; }
.ps-body  { flex:1; padding:0 0 22px 14px; }
.ps-title { font-size:15px; font-weight:800; color:#1e3a8a; margin-bottom:5px; line-height:1.3; }
.ps-desc  { font-size:14.5px; color:#4b5563; line-height:1.65; }

/* ── Definition Cards ──────────────────────────────────── */
.def-card { border:1px solid #c7d2fe; border-radius:10px; overflow:hidden; margin:14px 0; }
.def-term { background:linear-gradient(135deg,#1e3a8a,#2563eb); color:#fff; padding:10px 16px; font-size:13.5px; font-weight:800; letter-spacing:.2px; }
.def-body { background:#f5f8ff; padding:14px 16px; font-size:14.5px; color:#374151; line-height:1.65; }

/* ── Section Divider ───────────────────────────────────── */
.section-divider { display:flex; align-items:center; gap:14px; margin:22px 0; }
.section-divider-line { flex:1; height:1px; background:linear-gradient(90deg,#e5e7eb,transparent); }
.section-divider-label { font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:.8px; color:#9ca3af; white-space:nowrap; }

/* ── Enhanced Rich Text Typography ────────────────────── */
.lb-body.rt-body { font-size:15.5px; line-height:1.85; }
.lb-body.rt-body h2 { font-size:18px; font-weight:800; color:#111827; margin:1.6em 0 .6em; padding-bottom:.4em; border-bottom:2px solid #e9ecf0; }
.lb-body.rt-body h3 { font-size:15.5px; font-weight:800; color:#1e3a8a; margin:1.3em 0 .5em; display:flex; align-items:center; gap:6px; }
.lb-body.rt-body h3::before { content:''; display:inline-block; width:4px; height:16px; background:#1e3a8a; border-radius:2px; flex-shrink:0; }
.lb-body.rt-body p  { margin:0 0 1.15em; }
.lb-body.rt-body ul { padding-left:0; margin:0 0 1.15em; list-style:none; }
.lb-body.rt-body ul li { padding-left:1.4em; position:relative; margin-bottom:.55em; line-height:1.7; }
.lb-body.rt-body ul li::before { content:'▸'; position:absolute; left:0; color:#2563eb; font-weight:900; font-size:12px; top:.18em; }
.lb-body.rt-body ol { padding-left:1.6em; margin:0 0 1.15em; }
.lb-body.rt-body ol li { margin-bottom:.55em; line-height:1.7; padding-left:.3em; }
.lb-body.rt-body ol li::marker { color:#1e3a8a; font-weight:800; }
.lb-body.rt-body strong, .lb-body.rt-body b { color:#111827; font-weight:800; }
.lb-body.rt-body em { color:#4b5563; }
.lb-body.rt-body hr { border:none; border-top:2px solid #f0f2f5; margin:22px 0; }
.lb-body.rt-body code { background:#f1f5f9; color:#be185d; padding:2px 7px; border-radius:5px; font-size:13.5px; font-family:monospace; }
.lb-body.rt-body blockquote { border-left:4px solid #3b82f6; background:#f0f9ff; padding:14px 18px; border-radius:0 10px 10px 0; margin:18px 0; color:#1e3a8a; font-style:italic; font-size:15px; }

/* ── Improved block cards ──────────────────────────────── */
.lb { border-radius:16px; overflow:hidden; box-shadow:0 3px 12px rgba(15,23,42,.08); border:1px solid #e9ecf0; background:#fff; }
.lb + .lb { margin-top:18px; }
.lb-head { padding:14px 22px; border-bottom:1px solid #f0f2f5; display:flex; align-items:center; gap:10px; background:#fafbfc; }

/* ── Mobile optimisations ──────────────────────────────── */
@media(max-width:640px) {
    .myth-grid { grid-template-columns:1fr !important; }
    .callout { padding:12px 14px; font-size:14.5px; }
    .lb-body.rt-body { font-size:15px; line-height:1.8; }
    .lesson-table tbody td, .lesson-table thead th { padding:9px 12px; font-size:13px; }
    .process-flow .ps-body { padding-bottom:16px; }
    .lf-inner { padding:14px; }
}
</style>

<div class="lp-wrap">
{{-- Preview Banner --}}
@if($previewMode)
<div style="background:linear-gradient(90deg,#d97706,#b45309);color:white;padding:9px 18px;font-size:13px;font-weight:700;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;flex-shrink:0;">
    <span style="display:flex;align-items:center;gap:8px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
        Admin Preview — this is how the lesson looks to learners. Interactions are disabled.
    </span>
    <a href="{{ route('elearning.lessons.edit', [$previewCourse, $lesson]) }}"
       style="color:white;text-decoration:none;background:rgba(255,255,255,.2);padding:4px 14px;border-radius:20px;font-size:12px;white-space:nowrap;">
        ← Back to Builder
    </a>
</div>
@endif

<div class="ls-shell">

    {{-- ══ SIDEBAR ════════════════════════════════════════════ --}}
    <aside class="ll-nav" id="llNav">
        <div class="ll-nav-header">
            <div class="ll-nav-org">SMS Training Academy</div>
            <div class="ll-nav-course">{{ $courseName }}</div>
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
                    $slAccessible = $previewMode || $slDone || $slCurrent || $prevDone;
                    $stateClass   = $slCurrent ? 'active' : ($slAccessible ? '' : 'locked');
                    $iconClass    = $slCurrent ? 'li-active' : ($slDone ? 'li-done' : ($slAccessible ? 'li-ready' : 'li-locked'));
                @endphp
                @if($slAccessible && !$slCurrent)
                    <a href="{{ $previewMode ? route('elearning.lessons.preview', [$previewCourse, $sLesson]) : route('participant.lesson.show', [$enrollment->id, $sLesson->id]) }}"
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
                                {{ $sLesson->duration_minutes }}m ·
                            @endif
                            @if($slDone) ✓ Completed
                            @elseif($slCurrent) ▶ In Progress
                            @elseif(!$slAccessible) 🔒 Locked
                            @else Not started
                            @endif
                        </div>
                    </div>
                @if($slCurrent || !$slAccessible) </span> @else </a> @endif
            @endforeach
        </nav>
    </aside>

    <div class="ll-nav-overlay" id="llNavOverlay" onclick="toggleNav()"></div>

    {{-- ══ MAIN PANEL ═════════════════════════════════════════ --}}
    <div class="ls-main">

        {{-- Topbar --}}
        <div class="ll-topbar">
            <div class="ll-topbar-left">
                <button class="ll-toggle-btn" onclick="toggleNav()" type="button">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                </button>
                @if($previewMode)
                    <a href="{{ route('elearning.lessons.edit', [$previewCourse, $lesson]) }}" class="ll-back-btn">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                        Builder
                    </a>
                @else
                    <a href="{{ route('participant.elearning-details', $enrollment->id) }}" class="ll-back-btn">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                        My Course
                    </a>
                @endif
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

        {{-- Step indicator bar --}}
        <div class="lf-stepbar">
            <div class="lf-sb-info">
                <div class="lf-sb-icon" id="lfIcon">📋</div>
                <div>
                    <div class="lf-sb-name" id="lfName">Overview</div>
                    <div class="lf-sb-sub">Step <span id="lfNum">0</span> of {{ $lastPanel }}</div>
                </div>
            </div>

            <div class="lf-sb-track" id="lfTrack">
                <button class="lf-dot active" onclick="goToStep(0)" title="Overview" type="button">📋</button>
                @foreach($lessonBlocks as $bi => $block)
                <div class="lf-dot-line" id="lfLine{{ $bi }}"></div>
                <button class="lf-dot" onclick="goToStep({{ $bi + 1 }})"
                        title="{{ e($block->title ?: $block->getTypeLabel()) }}" type="button">{{ $bi + 1 }}</button>
                @endforeach
                @if($hasActivities)
                <div class="lf-dot-line" id="lfLine{{ $blockCount }}"></div>
                <button class="lf-dot" onclick="goToStep({{ $blockCount + 1 }})" title="Activities" type="button">★</button>
                @endif
            </div>

            <div class="lf-sb-nav">
                <button id="btnPrevTop" class="lf-sb-btn" onclick="prevStep()" type="button" disabled>
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                </button>
                <button id="btnNextTop" class="lf-sb-btn" onclick="nextStep()" type="button">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                </button>
            </div>

            <div id="lfProgress" class="lf-progress-line" style="width:0%"></div>
        </div>

        {{-- ── Viewport ──────────────────────────────────────── --}}
        <div class="lf-viewport">

            {{-- Panel 0: Lesson overview --}}
            <div class="lf-panel lf-active" data-step="0">
                <div class="lf-inner">

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

                    <div class="lesson-overview">
                        <div class="lo-breadcrumb">
                            {{ $courseName }}
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
                            @if($lesson->completion_rule === 'pass_quiz' && !$isCompleted)
                            <span class="lo-meta-item">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><circle cx="12" cy="12" r="10"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                                Pass quiz to complete{{ $lesson->required_passing_score ? " ({$lesson->required_passing_score}%)" : '' }}
                            </span>
                            @elseif(!$isCompleted)
                            <span class="lo-meta-item">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                                Mark complete when done
                            </span>
                            @endif
                            @if($isCompleted)
                            <span class="lo-meta-item" style="background:rgba(52,211,153,.2);color:#34d399;">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                Completed
                            </span>
                            @endif
                        </div>
                    </div>

                    @if($lesson->learning_objectives)
                    <div class="lo-obj">
                        <div class="lo-obj-head">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                            Learning Objectives
                        </div>
                        <div class="lo-obj-body">{{ $lesson->learning_objectives }}</div>
                    </div>
                    @endif

                    @if($lesson->short_description)
                    <div class="lo-desc">{{ $lesson->short_description }}</div>
                    @endif

                    @if(!$hasBlocks && !$hasActivities)
                    <div class="lf-empty" style="margin-top:20px;">
                        <div style="font-size:48px;margin-bottom:14px;">📄</div>
                        <div style="font-size:16px;font-weight:700;color:#6b7280;">No content has been added to this lesson yet.</div>
                        <div style="font-size:14px;color:#9ca3af;margin-top:6px;">Please check back later or contact your administrator.</div>
                    </div>
                    @else
                    <div style="margin-top:24px;text-align:center;">
                        @if($isCompleted && !$previewMode)
                        <button onclick="nextStep()" class="lfb lfb-blue" type="button" style="padding:12px 28px;font-size:15px;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                            Review Lesson
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                        </button>
                        @else
                        <button onclick="nextStep()" class="lfb lfb-next" type="button" style="padding:12px 28px;font-size:15px;">
                            Start Lesson
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                        </button>
                        @endif
                    </div>
                    @endif

                </div>
            </div>

            {{-- Block panels (1..N) --}}
            @foreach($lessonBlocks as $bi => $block)
            <div class="lf-panel" data-step="{{ $bi + 1 }}">
                <div class="lf-inner">
                @switch($block->block_type)

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

                    @case('video')
                    @php
                        $vRaw = $block->content ?? '';
                        $ytId = null;
                        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&\s]+)/', $vRaw, $vm)) {
                            $ytId = $vm[1];
                        }
                        if ($ytId) {
                            $vUrl     = 'https://www.youtube.com/embed/' . $ytId . '?rel=0&modestbranding=1';
                            $vIsEmbed = true;
                        } elseif (preg_match('/vimeo\.com\/(\d+)/', $vRaw, $vm)) {
                            $vUrl     = 'https://player.vimeo.com/video/' . $vm[1];
                            $vIsEmbed = true;
                        } else {
                            $vUrl     = $vRaw;
                            $vIsEmbed = str_contains($vUrl, '/embed/') || str_contains($vUrl, 'player.vimeo');
                        }
                        $vIsYT = (bool)$ytId && !$previewMode;
                    @endphp
                    <div class="lb">
                        <div class="lb-head">
                            <div class="lb-head-icon lh-video">🎬</div>
                            <span class="lb-head-label">Video</span>
                            <span class="lb-head-title">{{ $block->title ?: 'Video Lesson' }}</span>
                        </div>
                        <div class="lb-body" style="padding:20px 22px;">
                            @if($vIsYT)
                                <div class="video-wrap">
                                    <div id="yt-player-{{ $block->id }}"></div>
                                    <div class="yt-resume-overlay" id="yt-resume-{{ $block->id }}" style="display:none;">
                                        <div class="yt-resume-box">
                                            <div class="yt-resume-icon">▶</div>
                                            <p class="yt-resume-title">Resume Video?</p>
                                            <p class="yt-resume-sub">You were at <strong id="yt-resume-time-{{ $block->id }}">0:00</strong></p>
                                            <div class="yt-resume-btns">
                                                <button class="yt-btn-resume" onclick="ytResume('{{ $block->id }}')">▶ Resume</button>
                                                <button class="yt-btn-restart" onclick="ytRestart('{{ $block->id }}')">↺ Start Over</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="yt-shield-top"></div>
                                    <div class="yt-shield-logo"></div>
                                </div>
                                <div class="yt-lock-msg" id="yt-lock-msg-{{ $block->id }}" style="display:none;">
                                    🔒 Watch the full video to continue to the next section.
                                </div>
                            @elseif($vIsEmbed)
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

                    @case('audio')
                    <div class="lb">
                        <div class="lb-head">
                            <div class="lb-head-icon lh-audio">🎧</div>
                            <span class="lb-head-label">Audio</span>
                            <span class="lb-head-title">{{ $block->title ?: 'Audio Lesson' }}</span>
                        </div>
                        <div class="lb-body">
                            <audio controls style="width:100%;border-radius:10px;outline:none;">
                                <source src="{{ $block->content }}" type="audio/mpeg">
                                <a href="{{ $block->content }}" target="_blank">Download audio</a>
                            </audio>
                        </div>
                    </div>
                    @break

                    @case('image')
                    <div class="lb">
                        @if($block->title)
                        <div class="lb-head">
                            <div class="lb-head-icon lh-image">🖼️</div>
                            <span class="lb-head-label">Image</span>
                            <span class="lb-head-title">{{ $block->title }}</span>
                        </div>
                        @endif
                        <div class="lb-body" style="text-align:center;padding:24px;">
                            <img src="{{ $block->content }}"
                                 alt="{{ $block->settings_json['caption'] ?? $block->title ?? '' }}"
                                 style="max-width:100%;border-radius:10px;cursor:zoom-in;box-shadow:0 2px 12px rgba(15,23,42,.10);"
                                 onclick="openLightbox(this.src, this.alt)">
                            @if(!empty($block->settings_json['caption']))
                                <p style="font-size:13.5px;color:#6b7280;margin-top:10px;font-style:italic;">{{ $block->settings_json['caption'] }}</p>
                            @endif
                        </div>
                    </div>
                    @break

                    @case('gallery')
                    @php $gItems = $block->getDecodedContent(); @endphp
                    <div class="lb">
                        <div class="lb-head">
                            <div class="lb-head-icon lh-gall">🎨</div>
                            <span class="lb-head-label">Gallery</span>
                            <span class="lb-head-title">{{ $block->title ?: 'Image Gallery' }}</span>
                        </div>
                        <div class="lb-body">
                            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:12px;">
                                @foreach($gItems as $gi)
                                <div style="text-align:center;">
                                    <img src="{{ $gi['url'] }}" alt="{{ $gi['caption'] ?? '' }}"
                                         style="width:100%;border-radius:10px;cursor:zoom-in;object-fit:cover;height:150px;box-shadow:0 2px 8px rgba(15,23,42,.08);"
                                         onclick="openLightbox(this.src, '{{ addslashes($gi['caption'] ?? '') }}')">
                                    @if(!empty($gi['caption']))
                                        <p style="font-size:12.5px;color:#6b7280;margin:6px 0 0;font-style:italic;">{{ $gi['caption'] }}</p>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @break

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
                        <div>
                            @foreach($acItems as $ai => $item)
                            <div class="acc-item" id="acc-{{ $block->id }}-{{ $ai }}">
                                <button type="button" class="acc-header" onclick="toggleAcc('acc-{{ $block->id }}-{{ $ai }}')">
                                    <span>{{ $item['title'] ?? '' }}</span>
                                    <svg class="acc-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                                </button>
                                <div class="acc-body" style="display:none;">{!! nl2br(e($item['body'] ?? '')) !!}</div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @break

                    @case('pdf')
                    <div class="lb">
                        <div class="lb-head">
                            <div class="lb-head-icon lh-pdf">📄</div>
                            <span class="lb-head-label">Document</span>
                            <span class="lb-head-title">{{ $block->title ?: 'PDF Document' }}</span>
                        </div>
                        <div class="lb-body">
                            <iframe src="{{ $block->content }}"
                                    style="width:100%;height:520px;border:none;border-radius:10px;background:#f9fafb;"></iframe>
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

                    @case('slides')
                    @php $slideItems = $block->getDecodedContent(); $slideId = 'slides-' . $block->id; @endphp
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
                                             style="width:100%;max-height:340px;object-fit:contain;border-radius:10px;margin-bottom:16px;">
                                    @endif
                                    @if(!empty($slide['title']))
                                        <h3 style="font-size:20px;font-weight:800;color:#111827;margin:0 0 10px;">{{ $slide['title'] }}</h3>
                                    @endif
                                    @if(!empty($slide['text']))
                                        <div style="font-size:15.5px;color:#374151;line-height:1.8;">{!! $slide['text'] !!}</div>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                            <div class="slide-nav">
                                <button type="button" onclick="slidePrev('{{ $slideId }}')"
                                        style="display:inline-flex;align-items:center;gap:6px;background:#f3f4f6;color:#374151;border:1px solid #e5e7eb;padding:9px 16px;border-radius:9px;font-weight:700;font-size:13.5px;cursor:pointer;font-family:inherit;">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg> Prev
                                </button>
                                <span class="slide-counter" id="{{ $slideId }}-counter">1 / {{ count($slideItems) }}</span>
                                <button type="button" onclick="slideNext('{{ $slideId }}', {{ count($slideItems) }})"
                                        style="display:inline-flex;align-items:center;gap:6px;background:#1e3a8a;color:#fff;border:none;padding:9px 16px;border-radius:9px;font-weight:700;font-size:13.5px;cursor:pointer;font-family:inherit;">
                                    Next <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    @break

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
                                            <input type="checkbox" name="kc_ans_{{ $block->id }}[]" value="{{ $oi }}" onchange="kcCheck('kc-{{ $block->id }}')">
                                        @else
                                            <input type="radio" name="kc_ans_{{ $block->id }}" value="{{ $oi }}" onchange="kcCheck('kc-{{ $block->id }}')">
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
                    <div class="panel-lock-msg" id="block-lock-{{ $bi + 1 }}" style="display:none;">
                        ❓ Submit your answer to continue to the next section.
                    </div>
                    @break

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
                            <p style="font-size:14px;font-weight:700;color:#374151;margin-bottom:14px;">How would you respond?</p>
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
                    <div class="panel-lock-msg" id="block-lock-{{ $bi + 1 }}" style="display:none;">
                        ✋ Choose a response to continue to the next section.
                    </div>
                    @break

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
                            <p style="font-size:14px;color:#6b7280;margin-bottom:18px;">Match each term with the correct definition.</p>
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
                            <div id="{{ $matchId }}-result" style="display:none;margin-top:14px;padding:12px 16px;border-radius:10px;font-size:14px;font-weight:700;"></div>
                        </div>
                    </div>
                    <div class="panel-lock-msg" id="block-lock-{{ $bi + 1 }}" style="display:none;">
                        🔗 Match all items to continue to the next section.
                    </div>
                    @break

                    {{-- ── Fun Fact ───────────────────────────── --}}
                    @case('fun_fact')
                    @php $ffD = $block->getDecodedContent(); $ffIcon = $ffD['icon'] ?? '💡'; @endphp
                    <div class="lb" style="border:none; overflow:visible; background:transparent; box-shadow:none;">
                        <div style="background:linear-gradient(135deg,#fffbeb 0%,#fef9c3 60%,#fef08a 100%); border:2px solid #fde047; border-radius:16px; overflow:hidden; box-shadow:0 4px 16px rgba(234,179,8,.18);">
                            <div style="background:linear-gradient(90deg,#f59e0b,#d97706); padding:10px 20px; display:flex; align-items:center; gap:10px;">
                                <span style="font-size:18px;">{{ $ffIcon }}</span>
                                <span style="font-size:11.5px; font-weight:800; text-transform:uppercase; letter-spacing:.8px; color:#fff;">Did You Know?</span>
                                @if($block->title && $block->title !== 'Did You Know?')
                                <span style="font-size:13px; color:rgba(255,255,255,.85); margin-left:4px;">— {{ $block->title }}</span>
                                @endif
                            </div>
                            <div style="padding:20px 24px; display:flex; gap:18px; align-items:flex-start;">
                                <div style="font-size:40px; flex-shrink:0; line-height:1;">{{ $ffIcon }}</div>
                                <div>
                                    @if(!empty($ffD['title']))<div style="font-size:15.5px; font-weight:800; color:#78350f; margin-bottom:8px; line-height:1.3;">{{ $ffD['title'] }}</div>@endif
                                    <p style="font-size:15px; color:#92400e; margin:0; line-height:1.7;">{{ $ffD['content'] ?? '' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @break

                    {{-- ── Reflection ─────────────────────────── --}}
                    @case('reflection')
                    @php $refD = $block->getDecodedContent(); @endphp
                    <div class="lb" style="border:none; overflow:visible; background:transparent; box-shadow:none;">
                        <div style="background:linear-gradient(135deg,#faf5ff,#ede9fe); border:2px solid #c4b5fd; border-radius:16px; overflow:hidden; box-shadow:0 4px 16px rgba(109,40,217,.12);">
                            <div style="background:linear-gradient(90deg,#7c3aed,#6d28d9); padding:10px 20px; display:flex; align-items:center; gap:10px;">
                                <span style="font-size:18px;">🤔</span>
                                <span style="font-size:11.5px; font-weight:800; text-transform:uppercase; letter-spacing:.8px; color:#fff;">Reflection</span>
                                @if($block->title)
                                <span style="font-size:13px; color:rgba(255,255,255,.85); margin-left:4px;">— {{ $block->title }}</span>
                                @endif
                            </div>
                            <div style="padding:20px 24px;">
                                @if(!empty($refD['prompt']))
                                <p style="font-size:15.5px; font-weight:700; color:#4c1d95; margin:0 0 16px; line-height:1.6; border-left:4px solid #7c3aed; padding-left:14px; background:rgba(124,58,237,.06); padding:12px 14px; border-radius:0 8px 8px 0;">{{ $refD['prompt'] }}</p>
                                @endif
                                @if(!empty($refD['questions']))
                                <div style="display:flex; flex-direction:column; gap:8px;">
                                    @foreach($refD['questions'] as $qi => $rq)
                                    <div style="display:flex; gap:10px; align-items:flex-start; background:#fff; border:1px solid #ddd6fe; border-radius:10px; padding:12px 16px;">
                                        <div style="flex-shrink:0; width:24px; height:24px; background:#7c3aed; color:#fff; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:800; margin-top:1px;">{{ $qi + 1 }}</div>
                                        <p style="font-size:14.5px; color:#4c1d95; margin:0; line-height:1.65;">{{ $rq }}</p>
                                    </div>
                                    @endforeach
                                </div>
                                @endif
                                <div style="margin-top:14px; padding:10px 14px; background:rgba(109,40,217,.07); border-radius:8px; font-size:12.5px; color:#6d28d9; font-style:italic; display:flex; align-items:center; gap:8px;">
                                    <span>💭</span><span>Pause here and think about this before continuing to the next section.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @break

                    {{-- ── Click to Reveal ─────────────────────── --}}
                    @case('click_reveal')
                    @php $crD = $block->getDecodedContent(); $crId = 'cr-' . $bi; @endphp
                    <div class="lb">
                        <div style="background:linear-gradient(90deg,#0369a1,#0284c7); padding:10px 22px; display:flex; align-items:center; gap:10px;">
                            <span style="font-size:16px;">👁</span>
                            <span style="font-size:11.5px; font-weight:800; text-transform:uppercase; letter-spacing:.8px; color:#fff;">Think First, Then Reveal</span>
                            @if($block->title)
                            <span style="font-size:13px; color:rgba(255,255,255,.8); margin-left:4px;">— {{ $block->title }}</span>
                            @endif
                        </div>
                        <div style="padding:22px 26px;">
                            <div style="background:#f0f9ff; border:1.5px solid #bae6fd; border-radius:12px; padding:16px 20px; margin-bottom:18px;">
                                <div style="font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:.5px; color:#0369a1; margin-bottom:8px;">❓ Question</div>
                                <p style="font-size:16px; font-weight:600; color:#0c4a6e; margin:0; line-height:1.6;">{{ $crD['question'] ?? '' }}</p>
                            </div>
                            <button onclick="toggleReveal('{{ $crId }}')"
                                    id="{{ $crId }}-btn"
                                    style="background:linear-gradient(135deg,#0ea5e9,#0284c7); color:#fff; border:none; border-radius:10px; padding:12px 24px; font-size:14px; font-weight:700; cursor:pointer; transition:all .2s; display:flex; align-items:center; gap:8px; box-shadow:0 3px 10px rgba(14,165,233,.35);">
                                <span>👁</span><span>Reveal Answer</span>
                            </button>
                            <div id="{{ $crId }}" style="display:none; margin-top:16px; border-radius:12px; overflow:hidden; border:1.5px solid #86efac; box-shadow:0 3px 12px rgba(22,163,74,.12);">
                                <div style="background:#16a34a; padding:9px 18px; display:flex; align-items:center; gap:8px;">
                                    <span style="font-size:14px;">✅</span>
                                    <span style="font-size:12px; font-weight:800; text-transform:uppercase; letter-spacing:.5px; color:#fff;">Answer</span>
                                </div>
                                <div style="background:#f0fdf4; padding:16px 20px;">
                                    <p style="font-size:15.5px; font-weight:700; color:#14532d; margin:0 0 {{ !empty($crD['explanation']) ? '10px' : '0' }}; line-height:1.6;">{{ $crD['answer'] ?? '' }}</p>
                                    @if(!empty($crD['explanation']))
                                    <p style="font-size:14px; color:#166534; margin:0; font-style:italic; line-height:1.6; padding-top:10px; border-top:1px solid #bbf7d0;">{{ $crD['explanation'] }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @break

                    {{-- ── Myth vs Fact ────────────────────────── --}}
                    @case('myth_fact')
                    @php $mfD = $block->getDecodedContent(); @endphp
                    <div class="lb">
                        <div style="background:linear-gradient(90deg,#b91c1c,#dc2626); padding:10px 22px; display:flex; align-items:center; gap:10px;">
                            <span style="font-size:16px;">⚡</span>
                            <span style="font-size:11.5px; font-weight:800; text-transform:uppercase; letter-spacing:.8px; color:#fff;">Myth vs Fact</span>
                            @if($block->title)
                            <span style="font-size:13px; color:rgba(255,255,255,.8); margin-left:4px;">— {{ $block->title }}</span>
                            @endif
                        </div>
                        <div style="padding:20px 22px;">
                            <div class="myth-grid" style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
                                <div style="background:#fff5f5; border:2px solid #fca5a5; border-radius:14px; padding:18px; position:relative; overflow:hidden;">
                                    <div style="position:absolute; top:-8px; right:-8px; font-size:48px; opacity:.07; line-height:1;">❌</div>
                                    <div style="display:inline-flex; align-items:center; gap:6px; background:#fef2f2; border:1px solid #fca5a5; border-radius:20px; padding:4px 12px; font-size:11px; font-weight:800; color:#b91c1c; text-transform:uppercase; letter-spacing:.5px; margin-bottom:12px;">
                                        <span>❌</span> Myth
                                    </div>
                                    <p style="font-size:14.5px; color:#7f1d1d; margin:0; line-height:1.65; font-style:italic;">"{{ $mfD['myth'] ?? '' }}"</p>
                                </div>
                                <div style="background:#f0fdf4; border:2px solid #86efac; border-radius:14px; padding:18px; position:relative; overflow:hidden;">
                                    <div style="position:absolute; top:-8px; right:-8px; font-size:48px; opacity:.07; line-height:1;">✅</div>
                                    <div style="display:inline-flex; align-items:center; gap:6px; background:#dcfce7; border:1px solid #86efac; border-radius:20px; padding:4px 12px; font-size:11px; font-weight:800; color:#15803d; text-transform:uppercase; letter-spacing:.5px; margin-bottom:12px;">
                                        <span>✅</span> Fact
                                    </div>
                                    <p style="font-size:14.5px; color:#14532d; margin:0; line-height:1.65; font-weight:600;">{{ $mfD['fact'] ?? '' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @break

                    {{-- ── Workplace Example ───────────────────── --}}
                    @case('workplace_example')
                    @php $weD = $block->getDecodedContent(); @endphp
                    <div class="lb">
                        <div style="background:linear-gradient(90deg,#065f46,#047857); padding:10px 22px; display:flex; align-items:center; gap:10px;">
                            <span style="font-size:16px;">🏭</span>
                            <span style="font-size:11.5px; font-weight:800; text-transform:uppercase; letter-spacing:.8px; color:#fff;">Workplace Examples</span>
                            @if($block->title)
                            <span style="font-size:13px; color:rgba(255,255,255,.8); margin-left:4px;">— {{ $block->title }}</span>
                            @endif
                        </div>
                        <div style="padding:18px 22px;">
                            <div style="display:flex; flex-direction:column; gap:10px;">
                                @foreach($weD['examples'] ?? [] as $ex)
                                <div style="display:flex; gap:14px; align-items:flex-start; background:#f0fdf4; border:1px solid #a7f3d0; border-radius:12px; padding:14px 18px;">
                                    <div style="flex-shrink:0; background:linear-gradient(135deg,#10b981,#059669); color:#fff; border-radius:8px; padding:5px 12px; font-size:11.5px; font-weight:800; white-space:nowrap; margin-top:2px; box-shadow:0 2px 6px rgba(16,185,129,.3);">
                                        {{ $ex['context'] ?? '' }}
                                    </div>
                                    <p style="font-size:14.5px; color:#065f46; margin:0; line-height:1.65;">{{ $ex['description'] ?? '' }}</p>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @break

                    {{-- ── Case Study ──────────────────────────── --}}
                    @case('case_study')
                    @php $csD = $block->getDecodedContent(); @endphp
                    <div class="lb">
                        <div style="background:linear-gradient(90deg,#3730a3,#4338ca); padding:10px 22px; display:flex; align-items:center; gap:10px;">
                            <span style="font-size:16px;">📋</span>
                            <span style="font-size:11.5px; font-weight:800; text-transform:uppercase; letter-spacing:.8px; color:#fff;">Case Study</span>
                            @if($block->title)
                            <span style="font-size:13px; color:rgba(255,255,255,.8); margin-left:4px;">— {{ $block->title }}</span>
                            @endif
                        </div>
                        <div style="padding:20px 24px;">
                            <div style="background:#f5f3ff; border-left:4px solid #6366f1; border-radius:0 12px 12px 0; padding:16px 20px; margin-bottom:18px; font-size:15px; color:#3730a3; line-height:1.7;">
                                {{ $csD['case_description'] ?? '' }}
                            </div>
                            @if(!empty($csD['questions']))
                            <div style="margin-bottom:18px;">
                                <div style="font-size:12px; font-weight:800; text-transform:uppercase; color:#4338ca; margin-bottom:10px; letter-spacing:.5px; display:flex; align-items:center; gap:6px;">
                                    <span style="width:3px; height:14px; background:#6366f1; border-radius:2px; display:inline-block;"></span> Discussion Questions
                                </div>
                                <div style="display:flex; flex-direction:column; gap:8px;">
                                    @foreach($csD['questions'] as $qi => $csq)
                                    <div style="display:flex; gap:10px; align-items:flex-start; background:#eef2ff; border:1px solid #c7d2fe; border-radius:10px; padding:12px 16px;">
                                        <div style="flex-shrink:0; width:24px; height:24px; background:#4338ca; color:#fff; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:800; margin-top:1px;">{{ $qi + 1 }}</div>
                                        <p style="font-size:14.5px; color:#3730a3; margin:0; line-height:1.6;">{{ $csq }}</p>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                            @if(!empty($csD['expected_response']))
                            <details style="border:1.5px solid #c7d2fe; border-radius:12px; overflow:hidden;">
                                <summary style="background:linear-gradient(135deg,#e0e7ff,#eef2ff); padding:12px 18px; cursor:pointer; font-size:13.5px; font-weight:700; color:#3730a3; list-style:none; display:flex; justify-content:space-between; align-items:center; user-select:none;">
                                    <span>💡 View Model Response</span> <span style="font-size:11px;">Click to expand ▼</span>
                                </summary>
                                <div style="padding:16px 20px; background:#f5f3ff; font-size:14.5px; color:#374151; line-height:1.7; border-top:1px solid #c7d2fe;">
                                    {{ $csD['expected_response'] }}
                                </div>
                            </details>
                            @endif
                        </div>
                    </div>
                    @break

                @endswitch

                    @if($block->isAudioEligible())
                    @include('participant.partials.block-audio-player', [
                        'block'       => $block,
                        'blockAudio'  => ($blockAudioMap ?? collect())[$block->id] ?? null,
                        'lesson'      => $lesson,
                        'enrollment'  => $enrollment ?? null,
                        'previewMode' => $previewMode ?? false,
                    ])
                    @endif

                </div>
            </div>
            @endforeach

            {{-- Activities panel (quizzes + resources) --}}
            @if($hasActivities)
            <div class="lf-panel" data-step="{{ $blockCount + 1 }}">
                <div class="lf-inner">

                    @include('participant.partials.lesson-recap-card', [
                        'lessonRecapAudio' => $lessonRecapAudio ?? null,
                        'lesson'           => $lesson,
                        'enrollment'       => $enrollment ?? null,
                        'previewMode'      => $previewMode ?? false,
                    ])

                    @if($hasResources)
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

                    @if($hasQuizzes)
                    <div class="lb" style="{{ $hasResources ? 'margin-top:16px;' : '' }}">
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
                                $quizOverride  = \App\Models\QuizAttemptOverride::where('enrollment_id', $enrollment->id)->where('quiz_id', $quiz->id)->first();
                                $effectiveMax  = $quiz->max_attempt + ($quizOverride?->extra_attempts ?? 0);
                                $attemptsLeft  = $effectiveMax > 0 ? max(0, $effectiveMax - $attemptsUsed) : PHP_INT_MAX;
                                $pendingGate   = \App\Models\QuizReviewGate::where('enrollment_id', $enrollment->id)
                                    ->where('quiz_id', $quiz->id)->where('status', 'pending')->latest()->first();
                            @endphp
                            <div class="quiz-row">
                                <div class="quiz-name">{{ $quiz->title }}</div>
                                <div class="quiz-meta-line">
                                    Passing score: <strong>{{ $quiz->pass_mark }}%</strong>
                                    &nbsp;·&nbsp; Max attempts: {{ $effectiveMax ?: '∞' }}
                                    @if($bestAttempt) &nbsp;·&nbsp; Your best: <strong style="color:{{ $bestAttempt->score >= $quiz->pass_mark ? '#16a34a' : '#dc2626' }};">{{ $bestAttempt->score }}%</strong> @endif
                                    @if(!$quizPassed && $attemptsLeft > 0 && $attemptsLeft !== PHP_INT_MAX) &nbsp;·&nbsp; {{ $attemptsLeft }} attempt{{ $attemptsLeft !== 1 ? 's' : '' }} left @endif
                                </div>
                                @if($quizPassed)
                                    <span style="display:inline-flex;align-items:center;gap:7px;background:#dcfce7;color:#166534;padding:9px 16px;border-radius:9px;font-weight:700;font-size:14px;">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                        Passed — {{ $bestAttempt->score }}%
                                    </span>
                                @elseif($pendingGate)
                                    @php
                                        $reviewed = $pendingGate->reviewedCount();
                                        $required = $pendingGate->requiredCount();
                                        // Find first module lesson to link to
                                        $firstModLesson = \App\Models\ElearningLesson::whereIn('id', $pendingGate->required_lesson_ids ?? [])
                                            ->orderBy('lesson_order')->first();
                                    @endphp
                                    <div style="background:#fffbeb;border:1.5px solid #fde68a;border-radius:10px;padding:12px 16px;max-width:420px;">
                                        <div style="font-weight:700;color:#92400e;font-size:13px;margin-bottom:6px;">
                                            📖 Module Review Required
                                        </div>
                                        <div style="font-size:12.5px;color:#78350f;margin-bottom:10px;">
                                            You have used all {{ $attemptsUsed }} attempts without passing.
                                            Review this module again to unlock {{ $pendingGate->extra_attempts_granted }} more attempts.
                                            @if($required > 0)
                                                <br><span style="color:#92400e;">Progress: {{ $reviewed }} / {{ $required }} lessons reviewed</span>
                                            @endif
                                        </div>
                                        @if($firstModLesson)
                                        <a href="{{ route('participant.lesson.show', [$enrollment->id, $firstModLesson->id]) }}"
                                           style="display:inline-flex;align-items:center;gap:6px;background:#d97706;color:#fff;padding:8px 14px;border-radius:7px;font-weight:700;font-size:12.5px;text-decoration:none;">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.5"/></svg>
                                            Review Module Now
                                        </a>
                                        @endif
                                    </div>
                                @elseif($attemptsLeft <= 0)
                                    <span style="background:#fee2e2;color:#991b1b;padding:9px 16px;border-radius:9px;font-weight:700;font-size:14px;display:inline-block;">No attempts remaining — contact your administrator</span>
                                @elseif($previewMode)
                                    <span style="display:inline-flex;align-items:center;gap:7px;background:#fef3c7;color:#92400e;padding:10px 20px;border-radius:9px;font-weight:700;font-size:14px;cursor:default;">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                        Preview — Quiz disabled
                                    </span>
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

                </div>
            </div>
            @endif

        </div>{{-- /.lf-viewport --}}

        {{-- ── Footer ──────────────────────────────────────────── --}}
        <div class="lf-footer">

            <div class="lf-foot-l">
                <button id="btnPrevFoot" class="lfb lfb-prev" onclick="prevStep()" type="button" style="display:none;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                    Back
                </button>
                @if($previousLesson)
                <a href="{{ $previewMode ? route('elearning.lessons.preview', [$previewCourse, $previousLesson]) : route('participant.lesson.show', [$enrollment->id, $previousLesson->id]) }}"
                   class="lfb lfb-sm" style="background:#f3f4f6;color:#6b7280;border:1px solid #e5e7eb;" title="Go to previous lesson">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                    Prev Lesson
                </a>
                @endif
            </div>

            <div class="lf-foot-c" id="lfFootC">Overview</div>

            <div class="lf-foot-r">
                <button id="btnNextFoot" class="lfb lfb-next" onclick="nextStep()" type="button">
                    Next
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                </button>

                @if(!$isCompleted && !$previewMode)
                <form method="POST" action="{{ route('participant.lesson.complete', [$enrollment->id, $lesson->id]) }}" style="margin:0;display:none;" id="frmComplete">
                    @csrf
                    @if($quizzesPassed || $lesson->quizzes->isEmpty())
                        @if($requiresAudioCompletion)
                            {{-- Button starts disabled; JS enables it once all audio is done --}}
                            <button type="submit" class="lfb lfb-ok" id="btnMarkComplete" disabled
                                    style="opacity:.45;cursor:not-allowed;"
                                    title="Please complete the lesson audio first">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                Mark Complete
                            </button>
                        @else
                            <button type="submit" class="lfb lfb-ok" id="btnMarkComplete">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                Mark Complete
                            </button>
                        @endif
                    @else
                        <span class="lfb lfb-dis">Complete Quiz First</span>
                    @endif
                </form>
                @if($requiresAudioCompletion)
                <div id="audioGateMsg" style="font-size:11.5px;color:#d97706;font-weight:600;display:flex;align-items:center;gap:5px;margin-left:6px;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2.5"><path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/><line x1="12" y1="19" x2="12" y2="23"/><line x1="8" y1="23" x2="16" y2="23"/></svg>
                    Listen to audio first
                </div>
                @endif
                @elseif($isCompleted)
                <span class="lfb lfb-done" id="doneChip" style="display:none;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                    Completed
                </span>
                @elseif($previewMode)
                <span class="lfb lfb-amber" id="previewChip" style="display:none;cursor:default;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    Preview
                </span>
                @endif

                @if($nextLesson)
                <a href="{{ $previewMode ? route('elearning.lessons.preview', [$previewCourse, $nextLesson]) : route('participant.lesson.show', [$enrollment->id, $nextLesson->id]) }}"
                   class="lfb lfb-blue" id="btnNextLesson" style="display:none;">
                    Next Lesson
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                </a>
                @else
                <a href="{{ $previewMode ? route('elearning.lessons.index', $previewCourse) : route('participant.elearning-details', $enrollment->id) }}"
                   class="lfb lfb-teal" id="btnCourseOv" style="display:none;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                    {{ $previewMode ? 'All Lessons' : 'Course Overview' }}
                </a>
                @endif
            </div>

        </div>

    </div>{{-- /.ls-main --}}
</div>{{-- /.ls-shell --}}
</div>{{-- /.lp-wrap --}}

{{-- Lightbox --}}
<div id="lb-overlay" onclick="if(event.target===this)closeLightbox()">
    <button id="lb-close" onclick="closeLightbox()" type="button">✕</button>
    <img id="lb-img" src="" alt="">
    <div id="lb-caption"></div>
</div>

<script>
const STEP_ICONS  = {!! json_encode(array_merge(['📋'], $lessonBlocks->map(fn($b) => $blockTypeIcons[$b->block_type] ?? '📦')->values()->all(), $hasActivities ? ['📌'] : [])) !!};
const STEP_LABELS = {!! json_encode(array_merge(['Overview'], $lessonBlocks->map(fn($b) => $b->title ?: $b->getTypeLabel())->values()->all(), $hasActivities ? ['Activities'] : [])) !!};
const LAST        = {{ $lastPanel }};
const HAS_ACT     = {{ $hasActivities ? 'true' : 'false' }};
const STEP_TYPES  = {!! json_encode($stepTypes) !!};
const AUTO_DONE   = new Set(['overview','rich_text','audio','image','gallery','pdf','download','slides','accordion','activities','fun_fact','reflection','click_reveal','myth_fact','workplace_example','case_study']);

function toggleReveal(id) {
    const el = document.getElementById(id);
    const btn = document.getElementById(id + '-btn');
    if (el.style.display === 'none') {
        el.style.display = 'block';
        btn.textContent = '🙈 Hide Answer';
        btn.style.background = '#64748b';
    } else {
        el.style.display = 'none';
        btn.textContent = '👁 Reveal Answer';
        btn.style.background = '#0ea5e9';
    }
}

// Revision mode: lesson already completed — no section locks apply
const IS_COMPLETED = {{ ($isCompleted && !$previewMode) ? 'true' : 'false' }};
// Pre-mark ALL steps done for completed lessons; only overview for new lessons
const stepDone = {};
for (let i = 0; i <= LAST; i++) stepDone[i] = IS_COMPLETED || i === 0;
let cur = 0;
const panels = document.querySelectorAll('.lf-panel');
const dots   = document.querySelectorAll('#lfTrack .lf-dot');
const lines  = document.querySelectorAll('#lfTrack .lf-dot-line');

function getFrontier() {
    for (let i = 0; i <= LAST; i++) { if (!stepDone[i]) return i; }
    return LAST;
}

function goToStep(n) {
    if (n < 0 || n > LAST) return;
    if (n > getFrontier()) return; // block jumping ahead of the furthest reached step
    // Stop all audio whenever the learner changes section
    if (typeof window.lfAudioStopAll === 'function') window.lfAudioStopAll();
    // Pause any YT video on the panel we're leaving
    const leavingBid = currentYtBlockId();
    if (leavingBid && ytStates[leavingBid]?.player?.pauseVideo) {
        ytStates[leavingBid].player.pauseVideo();
    }
    panels.forEach((p, i) => p.classList.toggle('lf-active', i === n));
    dots.forEach((d, i) => {
        d.classList.remove('active','done');
        if (i < n) d.classList.add('done');
        else if (i === n) d.classList.add('active');
    });
    lines.forEach((l, i) => l.classList.toggle('done', i < n));
    cur = n;
    if (AUTO_DONE.has(STEP_TYPES[n])) stepDone[n] = true;
    renderUI();
    document.querySelector('.lf-panel.lf-active')?.scrollTo({ top: 0 });
}
function prevStep() { goToStep(cur - 1); }
function nextStep() {
    if (isCurrentPanelLocked()) {
        const bid = currentYtBlockId();
        if (bid) {
            const msg = document.getElementById('yt-lock-msg-' + bid);
            if (msg) { msg.style.display = 'flex'; setTimeout(() => { msg.style.display = 'none'; }, 3500); }
        } else {
            const msg = document.getElementById('block-lock-' + cur);
            if (msg) { msg.style.display = 'flex'; setTimeout(() => { msg.style.display = 'none'; }, 3500); }
        }
        return;
    }
    goToStep(cur + 1);
}

function renderUI() {
    const isLast = cur === LAST;

    // Step bar info
    document.getElementById('lfIcon').textContent = STEP_ICONS[cur]  || '📋';
    document.getElementById('lfName').textContent = STEP_LABELS[cur] || '';
    document.getElementById('lfNum').textContent  = cur;

    // Progress line
    document.getElementById('lfProgress').style.width = (LAST > 0 ? (cur / LAST) * 100 : 100) + '%';

    // Footer center
    document.getElementById('lfFootC').textContent =
        cur === 0 ? 'Overview'
        : (isLast && HAS_ACT) ? 'Activities'
        : `Section ${cur} of ${LAST}`;

    // Prev
    const fp = document.getElementById('btnPrevFoot');
    const tp = document.getElementById('btnPrevTop');
    if (fp) fp.style.display = cur === 0 ? 'none' : '';
    if (tp) tp.disabled = cur === 0;

    // Next section navigation button
    const fn = document.getElementById('btnNextFoot');
    const tn = document.getElementById('btnNextTop');
    const locked = isCurrentPanelLocked();
    if (fn) {
        fn.style.display = (isLast && !locked) ? 'none' : '';
        if (fn.style.display !== 'none') {
            fn.className = 'lfb ' + (locked ? 'lfb-locked' : 'lfb-next');
            const t = STEP_TYPES[cur];
            const lockLabel = t === 'video' ? '🔒 Watch First'
                : t === 'knowledge_check' ? '❓ Answer First'
                : t === 'scenario'        ? '✋ Choose First'
                : t === 'matching'        ? '🔗 Match First'
                : '🔒 Complete First';
            // Use "Finish" only on the step before last for incomplete lessons
            const nextLabel = (cur === LAST - 1 && !IS_COMPLETED) ? 'Finish' : 'Next';
            fn.innerHTML = (locked ? lockLabel : nextLabel) +
                ' <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>';
        }
    }
    if (tn) tn.disabled = locked || isLast;

    // Completion actions:
    // - Completed lessons: always show doneChip + Next Lesson (revision browsing mode)
    // - Incomplete lessons: show only on last unlocked step (original behaviour)
    const showCompletion = IS_COMPLETED || (isLast && !locked);
    ['frmComplete','doneChip','previewChip','btnNextLesson','btnCourseOv'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.style.display = showCompletion ? '' : 'none';
    });

    // Dim dots beyond the current frontier
    const frontier = getFrontier();
    dots.forEach((d, i) => d.classList.toggle('future', i > frontier));
}

// Nav toggle
const llNav = document.getElementById('llNav');
const llOvl = document.getElementById('llNavOverlay');
function toggleNav() {
    if (window.innerWidth <= 860) { llNav.classList.toggle('nav-open'); llOvl.classList.toggle('show'); }
    else llNav.classList.toggle('nav-collapsed');
}
window.addEventListener('resize', () => {
    if (window.innerWidth > 860) { llNav.classList.remove('nav-open'); llOvl.classList.remove('show'); }
});

// Lightbox
function openLightbox(src, cap) {
    document.getElementById('lb-img').src = src;
    document.getElementById('lb-caption').textContent = cap || '';
    document.getElementById('lb-overlay').classList.add('open');
}
function closeLightbox() { document.getElementById('lb-overlay').classList.remove('open'); }
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeLightbox(); });

// Accordion
function toggleAcc(id) {
    const item = document.getElementById(id);
    item.querySelector('.acc-body').style.display = item.classList.toggle('open') ? 'block' : 'none';
}

// Slides
function slidePrev(id) {
    const ps = document.getElementById(id).querySelectorAll('.slide-panel');
    let c = [...ps].findIndex(p => p.classList.contains('active'));
    if (c > 0) { ps[c].classList.remove('active'); ps[--c].classList.add('active'); }
    const el = document.getElementById(id + '-counter');
    if (el) el.textContent = (c + 1) + ' / ' + ps.length;
}
function slideNext(id, total) {
    const ps = document.getElementById(id).querySelectorAll('.slide-panel');
    let c = [...ps].findIndex(p => p.classList.contains('active'));
    if (c < ps.length - 1) { ps[c].classList.remove('active'); ps[++c].classList.add('active'); }
    const el = document.getElementById(id + '-counter');
    if (el) el.textContent = (c + 1) + ' / ' + ps.length;
}

// Knowledge check
function kcCheck() {}
function kcSubmit(id, correctMap, explanation) {
    const wrap = document.getElementById(id);
    const type = wrap.dataset.type;
    let ans = [];
    if (type === 'multiple') wrap.querySelectorAll('input[type=checkbox]:checked').forEach(cb => ans.push(parseInt(cb.value)));
    else { const r = wrap.querySelector('input[type=radio]:checked'); if (r) ans.push(parseInt(r.value)); }
    if (!ans.length) { alert('Please select an answer first.'); return; }
    const correctIdx = correctMap.filter(o => o.correct).map(o => o.idx);
    const ok = correctIdx.length === ans.length && correctIdx.every(i => ans.includes(i));
    const res = wrap.querySelector('.kc-result');
    if (ok) {
        wrap.querySelectorAll('.kc-opt-label').forEach((lbl, i) => {
            lbl.querySelector('input').disabled = true;
            if (correctIdx.includes(i)) lbl.classList.add('correct');
            else if (ans.includes(i)) lbl.classList.add('wrong');
        });
        res.style.display = 'block';
        res.className = 'kc-result kc-result-pass';
        res.innerHTML = '✅ Correct!' + (explanation ? ' <span style="font-weight:400;">' + explanation + '</span>' : '');
        wrap.querySelector('button[onclick^="kcSubmit"]').style.display = 'none';
        markStepDone(cur);
    } else {
        // Mark user selection wrong, don't lock inputs — allow retry
        wrap.querySelectorAll('.kc-opt-label').forEach((lbl, i) => {
            lbl.classList.remove('correct','wrong');
            if (ans.includes(i)) lbl.classList.add('wrong');
        });
        res.style.display = 'block';
        res.className = 'kc-result kc-result-fail';
        res.innerHTML = '❌ Incorrect — try again.';
        // Auto-reset after 1.4s for clean retry
        setTimeout(() => {
            wrap.querySelectorAll('.kc-opt-label').forEach(lbl => lbl.classList.remove('wrong'));
            wrap.querySelectorAll('input[type=radio],input[type=checkbox]').forEach(inp => inp.checked = false);
            res.style.display = 'none';
        }, 1400);
    }
}

// Scenario
function scSelect(blockId, idx, options) {
    document.querySelectorAll(`[id^="scopt-${blockId}-"] .sc-opt-btn`).forEach(btn => { btn.disabled = true; });
    options.forEach((opt, i) => {
        const btn = document.querySelector(`#scopt-${blockId}-${i} .sc-opt-btn`);
        const exp = document.getElementById(`scexp-${blockId}-${i}`);
        if (btn && i === idx) btn.classList.add(opt.correct ? 'selected-correct' : 'selected-wrong');
        if (exp && opt.explanation) { exp.style.display = 'block'; exp.textContent = opt.explanation; }
    });
    markStepDone(cur);
}

// Matching — runs on every dropdown change; only unlocks when ALL pairs correct
function checkMatch(id, total) {
    let correct = 0, filled = 0;
    for (let i = 0; i < total; i++) {
        const sel = document.getElementById(id + '-sel-' + i);
        if (!sel) continue;
        if (sel.value) filled++;
        if (sel.value === sel.dataset.correct) {
            sel.style.borderColor = '#16a34a'; sel.style.background = '#f0fdf4'; correct++;
        } else if (sel.value) {
            sel.style.borderColor = '#dc2626'; sel.style.background = '#fef2f2';
        } else {
            sel.style.borderColor = '#e5e7eb'; sel.style.background = '';
        }
    }
    const res = document.getElementById(id + '-result');
    if (!res) return;
    if (correct === total) {
        res.style.display = 'block'; res.style.color = '#166534'; res.style.background = '#dcfce7';
        res.style.borderRadius = '10px'; res.style.padding = '12px 16px';
        res.textContent = '✅ All ' + total + ' pairs matched correctly!';
        markStepDone(cur);
    } else if (filled === total) {
        res.style.display = 'block'; res.style.color = '#991b1b'; res.style.background = '#fef2f2';
        res.style.borderRadius = '10px'; res.style.padding = '12px 16px';
        res.textContent = correct + ' of ' + total + ' correct — fix the red items and try again.';
    } else {
        res.style.display = 'none';
    }
}

// ── YouTube IFrame API ────────────────────────────────────────
const YT_BLOCKS = {!! json_encode($ytBlocks) !!};
const ytStates  = {};

(function initYT() {
    if (!Object.keys(YT_BLOCKS).length) return;
    Object.keys(YT_BLOCKS).forEach(bid => {
        ytStates[bid] = {
            player: null, ready: false, pollId: null,
            hw:   parseFloat(localStorage.getItem('yt_hw_' + bid) || '0'),
            done: localStorage.getItem('yt_done_' + bid) === '1',
        };
    });
    const tag = document.createElement('script');
    tag.src = 'https://www.youtube.com/iframe_api';
    document.head.appendChild(tag);
})();

window.onYouTubeIframeAPIReady = function() {
    Object.entries(YT_BLOCKS).forEach(([bid, info]) => {
        ytStates[bid].player = new YT.Player('yt-player-' + bid, {
            videoId: info.ytId,
            playerVars: {
                rel: 0, modestbranding: 1, enablejsapi: 1,
                origin: window.location.origin,
                iv_load_policy: 3,
            },
            events: {
                onReady:       e => ytOnReady(bid),
                onStateChange: e => ytOnState(bid, e),
            },
        });
    });
};

function ytFmt(sec) {
    sec = Math.floor(sec || 0);
    return Math.floor(sec / 60) + ':' + String(sec % 60).padStart(2, '0');
}

function ytOnReady(bid) {
    const state = ytStates[bid];
    state.ready = true;
    if (!state.done && state.hw > 5) {
        const el = document.getElementById('yt-resume-time-' + bid);
        if (el) el.textContent = ytFmt(state.hw);
        const ov = document.getElementById('yt-resume-' + bid);
        if (ov) ov.style.display = 'flex';
    }
}

function ytOnState(bid, e) {
    const state = ytStates[bid];
    if (e.data === YT.PlayerState.PLAYING) {
        clearInterval(state.pollId);
        state.pollId = setInterval(() => ytPoll(bid), 500);
    } else {
        clearInterval(state.pollId);
        state.pollId = null;
    }
    // Catch forward scrub while paused/buffering (before PLAYING poll starts)
    if (e.data === YT.PlayerState.PAUSED || e.data === YT.PlayerState.BUFFERING) {
        if (state.ready && state.player.getCurrentTime) {
            const ct = state.player.getCurrentTime();
            if (ct > state.hw + 1.5) {
                state.player.seekTo(state.hw, true);
            }
        }
    }
    if (e.data === YT.PlayerState.ENDED) {
        const dur = (state.player.getDuration && state.player.getDuration()) || 1;
        if (state.hw / dur >= 0.95) {
            // Legitimately watched enough — mark complete
            state.done = true;
            localStorage.setItem('yt_done_' + bid, '1');
            renderUI();
        } else {
            // Scrubbed to end without watching — revert to highwater mark
            state.player.seekTo(state.hw, true);
            state.player.pauseVideo();
        }
    }
}

function ytPoll(bid) {
    const state = ytStates[bid];
    if (!state.player || !state.ready) return;
    if (state.player.getPlayerState() !== YT.PlayerState.PLAYING) return;
    const ct  = state.player.getCurrentTime();
    const dur = state.player.getDuration() || 1;
    // Anti-skip: revert forward jumps beyond highwater mark
    if (ct > state.hw + 1.5) {
        state.player.seekTo(state.hw, true);
        return;
    }
    if (ct > state.hw) {
        state.hw = ct;
        localStorage.setItem('yt_hw_' + bid, ct);
    }
    if (!state.done && dur > 1 && ct / dur >= 0.95) {
        state.done = true;
        localStorage.setItem('yt_done_' + bid, '1');
        clearInterval(state.pollId);
        state.pollId = null;
        renderUI();
    }
}

function ytResume(bid) {
    const state = ytStates[bid];
    const ov = document.getElementById('yt-resume-' + bid);
    if (ov) ov.style.display = 'none';
    if (state.player && state.ready) {
        state.player.seekTo(state.hw, true);
        state.player.playVideo();
    }
}

function ytRestart(bid) {
    const state = ytStates[bid];
    const ov = document.getElementById('yt-resume-' + bid);
    if (ov) ov.style.display = 'none';
    state.hw   = 0;
    state.done = false;
    localStorage.removeItem('yt_hw_'   + bid);
    localStorage.removeItem('yt_done_' + bid);
    if (state.player && state.ready) {
        state.player.seekTo(0, true);
        state.player.playVideo();
    }
    renderUI();
}

function currentYtBlockId() {
    for (const [bid, info] of Object.entries(YT_BLOCKS)) {
        if (info.step === cur) return bid;
    }
    return null;
}

function isCurrentPanelLocked() {
    if (IS_COMPLETED) return false; // revision mode — no locks on already-completed lessons
    if (stepDone[cur]) return false;
    const t = STEP_TYPES[cur];
    if (t === 'video') {
        const bid = currentYtBlockId();
        return bid ? !(ytStates[bid]?.done) : false;
    }
    return t === 'knowledge_check' || t === 'scenario' || t === 'matching';
}

function markStepDone(n) {
    stepDone[n] = true;
    renderUI();
}

renderUI();

/* ══ RICH TEXT ENHANCEMENT ENGINE ══════════════════════ */
(function enhanceRichText() {
    const calloutDefs = [
        { emoji: '💡', cls: 'callout-tip' },
        { emoji: '⚠️', cls: 'callout-warning' },
        { emoji: '⚠',  cls: 'callout-warning' },
        { emoji: '🚨', cls: 'callout-warning' },
        { emoji: '📌', cls: 'callout-remember' },
        { emoji: '✅', cls: 'callout-success' },
        { emoji: '✔️', cls: 'callout-success' },
        { emoji: '🔍', cls: 'callout-example' },
        { emoji: '🎯', cls: 'callout-info' },
        { emoji: '📋', cls: 'callout-note' },
        { emoji: '💼', cls: 'callout-note' },
        { emoji: '🔒', cls: 'callout-info' },
        { emoji: '📖', cls: 'callout-info' },
        { emoji: '🏭', cls: 'callout-note' },
    ];

    document.querySelectorAll('.rt-body').forEach(body => {

        // 1 ── Callout detection: <p> starting with a known emoji
        body.querySelectorAll('p').forEach(p => {
            const text = p.textContent.trim();
            for (const def of calloutDefs) {
                if (text.startsWith(def.emoji)) {
                    let html = p.innerHTML.trim();
                    // Strip leading emoji (may be 1–2 chars for multi-char emoji)
                    html = html.replace(def.emoji, '').replace(/^[\s:–-]+/, '').trim();
                    const div = document.createElement('div');
                    div.className = 'callout ' + def.cls;
                    div.innerHTML = '<span class="callout-icon">' + def.emoji + '</span>'
                                  + '<div class="callout-content">' + html + '</div>';
                    p.parentNode.replaceChild(div, p);
                    break;
                }
            }
        });

        // 2 ── Table enhancement
        body.querySelectorAll('table:not(.lesson-table)').forEach(table => {
            table.classList.add('lesson-table');
            const wrap = document.createElement('div');
            wrap.className = 'table-wrap';
            table.parentNode.insertBefore(wrap, table);
            wrap.appendChild(table);
        });

        // 3 ── Process flow: detect <ol> preceded by a heading that contains
        //      "step", "process", "flow", "procedure", "how to", "stages"
        body.querySelectorAll('ol').forEach(ol => {
            const prev = ol.previousElementSibling;
            const heading = prev ? prev.textContent.toLowerCase() : '';
            const isProcess = /step|process|flow|procedure|how to|stage|phase|approach/.test(heading);
            if (!isProcess) return;

            const flow = document.createElement('div');
            flow.className = 'process-flow';
            Array.from(ol.querySelectorAll('li')).forEach((li, idx) => {
                // Split into title + description at first ":" or "–" or "—"
                const raw = li.innerHTML;
                const sep = raw.search(/[:–—]/);
                let title, desc;
                if (sep > 0 && sep < 60) {
                    title = li.innerHTML.substring(0, sep).replace(/<[^>]+>/g, '').trim();
                    desc  = li.innerHTML.substring(sep + 1).trim();
                } else {
                    title = '';
                    desc  = raw;
                }
                const step = document.createElement('div');
                step.className = 'process-step';
                step.innerHTML = `<div class="ps-left"><div class="ps-num">${idx + 1}</div><div class="ps-line"></div></div>`
                               + `<div class="ps-body">${title ? '<div class="ps-title">' + title + '</div>' : ''}<div class="ps-desc">${desc}</div></div>`;
                flow.appendChild(step);
            });
            ol.parentNode.replaceChild(flow, ol);
        });

        // 4 ── Definition detection: <strong>Term:</strong> at start of <p>
        body.querySelectorAll('p').forEach(p => {
            const first = p.querySelector('strong, b');
            if (!first) return;
            const text = p.textContent.trim();
            const termText = first.textContent.trim();
            if (!termText.endsWith(':') && !termText.endsWith('—') && !termText.endsWith('–')) return;
            if (termText.length > 60) return; // too long to be a term
            // Only convert if it looks like "Term: definition text"
            const rest = text.substring(termText.length).trim();
            if (!rest || rest.length < 10) return;
            const card = document.createElement('div');
            card.className = 'def-card';
            card.innerHTML = '<div class="def-term">' + termText.replace(/:$/, '') + '</div>'
                           + '<div class="def-body">' + rest + '</div>';
            p.parentNode.replaceChild(card, p);
        });

    });
})();

// ── Block & Recap Audio Player ────────────────────────────────
(function() {
    const audioMap  = {};
    const speedMap  = {};
    const speeds    = [0.75, 1, 1.25, 1.5, 2];
    const lessonKey = 'lfAudioPos_{{ $lesson->id }}_';

    function fmt(s) {
        s = Math.floor(s || 0);
        return Math.floor(s / 60) + ':' + String(s % 60).padStart(2, '0');
    }

    function playIcon() {
        return '<svg width="16" height="16" viewBox="0 0 24 24" fill="white" stroke="none"><polygon points="5 3 19 12 5 21 5 3"/></svg>';
    }
    function pauseIcon() {
        return '<svg width="16" height="16" viewBox="0 0 24 24" fill="white" stroke="none"><rect x="6" y="4" width="4" height="16"/><rect x="14" y="4" width="4" height="16"/></svg>';
    }

    function resetPlayerUI(audioId) {
        const playBtn = document.getElementById('lfPlay_' + audioId);
        const seek    = document.getElementById('lfSeek_' + audioId);
        const curEl   = document.getElementById('lfCur_' + audioId);
        const dot     = document.getElementById('lfDot_' + audioId);
        if (playBtn) playBtn.innerHTML = playIcon();
        if (seek)    { seek.value = 0; seek.style.background = 'rgba(255,255,255,.2)'; }
        if (curEl)   curEl.textContent = '0:00';
        if (dot)     dot.classList.add('paused');
    }

    window.bapInitPlayer = function(audioId) {
        const audio = document.getElementById('lfAudio_' + audioId);
        if (!audio || audioMap[audioId]) return;
        audioMap[audioId] = audio;
        speedMap[audioId] = 1;

        const seek    = document.getElementById('lfSeek_' + audioId);
        const curEl   = document.getElementById('lfCur_' + audioId);
        const durEl   = document.getElementById('lfDur_' + audioId);
        const playBtn = document.getElementById('lfPlay_' + audioId);

        audio.addEventListener('loadedmetadata', () => {
            if (seek)  seek.max = audio.duration;
            if (durEl) durEl.textContent = fmt(audio.duration);
        });
        audio.addEventListener('timeupdate', () => {
            if (!audio.duration) return;
            const pct = (audio.currentTime / audio.duration) * 100;
            if (seek) {
                seek.value = audio.currentTime;
                seek.style.background = `linear-gradient(to right,#7c3aed ${pct}%,rgba(255,255,255,.2) 0)`;
            }
            if (curEl) curEl.textContent = fmt(audio.currentTime);
        });
        audio.addEventListener('play', () => {
            if (playBtn) playBtn.innerHTML = pauseIcon();
            const dot = document.getElementById('lfDot_' + audioId);
            if (dot) dot.classList.remove('paused');
        });
        audio.addEventListener('pause', () => {
            if (playBtn) playBtn.innerHTML = playIcon();
            const dot = document.getElementById('lfDot_' + audioId);
            if (dot) dot.classList.add('paused');
        });
        audio.addEventListener('ended', () => {
            resetPlayerUI(audioId);
            sessionStorage.removeItem(lessonKey + audioId);
        });
    };

    window.lfAudioStopAll = function() {
        document.querySelectorAll('audio[id^="lfAudio_"]').forEach(audio => {
            if (!audio.paused) audio.pause();
            audio.currentTime = 0;
            const audioId = audio.id.replace('lfAudio_', '');
            resetPlayerUI(audioId);
            sessionStorage.removeItem(lessonKey + audioId);
        });
    };

    window.lfAudioPlay = function(audioId) {
        window.bapInitPlayer(audioId);
        const audio = audioMap[audioId];
        if (!audio) return;
        if (audio.paused) {
            Object.entries(audioMap).forEach(([id, a]) => {
                if (id !== audioId && !a.paused) { a.pause(); resetPlayerUI(id); }
            });
            audio.play().catch(() => {});
        } else {
            audio.pause();
        }
    };

    window.lfAudioStop = function(audioId) {
        const audio = audioMap[audioId];
        if (!audio) return;
        audio.pause();
        audio.currentTime = 0;
        resetPlayerUI(audioId);
        sessionStorage.removeItem(lessonKey + audioId);
    };

    window.lfAudioSeek = function(audioId, el) {
        if (audioMap[audioId]) audioMap[audioId].currentTime = parseFloat(el.value);
    };

    window.lfAudioSpeed = function(audioId, btn) {
        speedMap[audioId] = ((speedMap[audioId] ?? 1) + 1) % speeds.length;
        const sp = speeds[speedMap[audioId]];
        if (audioMap[audioId]) audioMap[audioId].playbackRate = sp;
        if (btn) btn.textContent = sp + 'x';
    };

    window.buildAudioPlayerHtml = function(audioId, label, audioUrl) {
        const pi = '<svg width="16" height="16" viewBox="0 0 24 24" fill="white" stroke="none"><polygon points="5 3 19 12 5 21 5 3"/></svg>';
        const si = '<svg width="9" height="9" viewBox="0 0 24 24" fill="currentColor" stroke="none"><rect x="3" y="3" width="18" height="18" rx="2"/></svg>';
        return `<div class="bap-player-wrap">
            <div class="lf-aw-label">
                <span class="lf-aw-label-dot paused" id="lfDot_${audioId}"></span>
                ${label}
            </div>
            <div class="lf-aw-controls">
                <button class="lf-aw-play-btn" id="lfPlay_${audioId}" onclick="lfAudioPlay('${audioId}')" title="Play / Pause">${pi}</button>
                <div class="lf-aw-timeline">
                    <input type="range" class="lf-aw-seek" id="lfSeek_${audioId}" min="0" max="100" value="0" oninput="lfAudioSeek('${audioId}', this)">
                    <div class="lf-aw-time">
                        <span id="lfCur_${audioId}">0:00</span>
                        <span id="lfDur_${audioId}">0:00</span>
                    </div>
                </div>
                <button class="lf-aw-stop-btn" onclick="lfAudioStop('${audioId}')" title="Stop and reset">${si} Stop</button>
                <button class="lf-aw-speed-btn" id="lfSpeed_${audioId}" onclick="lfAudioSpeed('${audioId}', this)" title="Playback speed">1x</button>
            </div>
            <audio id="lfAudio_${audioId}" preload="none" src="${audioUrl}"></audio>
        </div>`;
    };

    // Init all server-rendered audio players on page load
    document.querySelectorAll('audio[id^="lfAudio_"]').forEach(audio => {
        window.bapInitPlayer(audio.id.replace('lfAudio_', ''));
    });
})();
</script>

@php
    /* Computed variables for the audio completion tracker script below */
    $acUrl      = (!$previewMode && isset($enrollment) && $enrollment)
                  ? route('participant.lesson.audio-progress', [$enrollment->id, $lesson->id])
                  : null;
    $acProgress = $audioProgressMap->toArray();   // keyed by audio_id integer
    $acReqIds   = $audioRecords->pluck('id')->values()->all();
    $acAllDone  = !$requiresAudioCompletion
                  || $audioRecords->isEmpty()
                  || ($audioProgressMap->where('is_completed', true)->count() >= $audioRecords->count());
@endphp

@if($requiresAudioCompletion && !$previewMode)
<script>
// ── Audio Completion Tracker ──────────────────────────────────
(function () {
    'use strict';

    /* ── Config from PHP ─────────────────────────────────── */
    const URL_PROGRESS = @json($acUrl);
    const CSRF         = '{{ csrf_token() }}';
    const THRESHOLD    = 0.90;
    const SEND_MS      = 15000;
    const INIT_PROG    = @json($acProgress);    // {audioId: {high_water_mark, seconds_listened, is_completed}}
    const REQUIRED_IDS = @json($acReqIds).map(String);
    const ALREADY_DONE = {{ $acAllDone ? 'true' : 'false' }};

    if (!URL_PROGRESS) return;

    /* ── Per-audio runtime state ─────────────────────────── */
    const state = {};       // keyed by audio DB id string
    const doneSet = new Set();

    function getOrInitState(dbId) {
        if (state[dbId]) return state[dbId];
        const init = INIT_PROG[dbId] || {};
        state[dbId] = {
            hwm:       parseFloat(init.high_water_mark  || 0),
            listened:  parseFloat(init.seconds_listened || 0),
            done:      !!init.is_completed,
            ivs:       [],      // [start, end] intervals this session
            segStart:  null,    // start of current play segment
            timer:     null,
        };
        if (state[dbId].done) doneSet.add(dbId);
        return state[dbId];
    }

    /* ── Interval-union helpers ──────────────────────────── */
    function mergeIvs(ivs) {
        if (!ivs.length) return [];
        const sorted = ivs.slice().sort((a, b) => a[0] - b[0]);
        const out = [sorted[0].slice()];
        for (let i = 1; i < sorted.length; i++) {
            const last = out[out.length - 1];
            if (sorted[i][0] <= last[1]) last[1] = Math.max(last[1], sorted[i][1]);
            else out.push(sorted[i].slice());
        }
        return out;
    }
    function ivsTotal(ivs) {
        return mergeIvs(ivs).reduce((s, [a, b]) => s + (b - a), 0);
    }

    /* ── Progress send ───────────────────────────────────── */
    function sendProgress(dbId, naturallyEnded, useBeacon) {
        const s = state[dbId];
        if (!s) return;

        // Close open segment before computing
        const audio = document.querySelector('audio[data-audio-db-id="' + dbId + '"]');
        if (s.segStart !== null && audio) {
            s.ivs.push([s.segStart, audio.currentTime]);
            s.segStart = null;
        }

        const sessionSecs = ivsTotal(s.ivs);
        const totalSecs   = Math.round((s.listened + sessionSecs) * 100) / 100;
        const hwm         = Math.round(s.hwm * 100) / 100;

        if (useBeacon) {
            const params = new URLSearchParams({
                _token:          CSRF,
                audio_id:        dbId,
                high_water_mark: hwm,
                seconds_listened:totalSecs,
                naturally_ended: naturallyEnded ? '1' : '0',
            });
            navigator.sendBeacon(URL_PROGRESS, params);
            return;
        }

        fetch(URL_PROGRESS, {
            method:  'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'Accept':       'application/json',
            },
            body: JSON.stringify({
                audio_id:        parseInt(dbId),
                high_water_mark: hwm,
                seconds_listened:totalSecs,
                naturally_ended: !!naturallyEnded,
            }),
        })
        .then(r => r.ok ? r.json() : null)
        .then(data => {
            if (!data) return;
            // Reconcile listened seconds with server
            s.listened = parseFloat(data.seconds_listened || totalSecs);
            s.ivs      = [];   // reset session intervals — server has stored them
            if (data.is_completed && !s.done) {
                s.done = true;
                doneSet.add(dbId);
                checkAllDone();
            }
        })
        .catch(() => {});
    }

    /* ── Completion gate ─────────────────────────────────── */
    function checkAllDone() {
        if (REQUIRED_IDS.every(id => doneSet.has(id))) {
            unlockMarkComplete();
        }
    }

    function unlockMarkComplete() {
        const btn = document.getElementById('btnMarkComplete');
        const msg = document.getElementById('audioGateMsg');
        if (btn) {
            btn.disabled = false;
            btn.style.opacity  = '';
            btn.style.cursor   = '';
            btn.title          = '';
        }
        if (msg) msg.style.display = 'none';
    }

    /* ── Hook a single audio element ─────────────────────── */
    function hookAudio(audio) {
        const dbId = audio.dataset.audioDbId;
        if (!dbId || !REQUIRED_IDS.includes(dbId)) return;

        const s = getOrInitState(dbId);

        // Restore playhead to high-water mark on metadata load
        audio.addEventListener('loadedmetadata', () => {
            if (s.hwm > 1 && audio.duration && s.hwm < audio.duration - 0.5) {
                audio.currentTime = s.hwm;
            }
        });

        // No-skip: snap back if user jumps past high-water mark
        audio.addEventListener('timeupdate', () => {
            if (!audio.duration || audio.currentTime === 0) return;
            if (audio.currentTime > s.hwm + 2) {
                audio.currentTime = s.hwm;
                return;
            }
            if (audio.currentTime > s.hwm) s.hwm = audio.currentTime;
        });

        // Play — open interval, start periodic send
        audio.addEventListener('play', () => {
            s.segStart = audio.currentTime;
            if (s.timer) clearInterval(s.timer);
            s.timer = setInterval(() => sendProgress(dbId, false, false), SEND_MS);
        });

        // Pause — close interval, flush
        audio.addEventListener('pause', () => {
            if (s.segStart !== null) {
                s.ivs.push([s.segStart, audio.currentTime]);
                s.segStart = null;
            }
            if (s.timer) { clearInterval(s.timer); s.timer = null; }
            sendProgress(dbId, false, false);
        });

        // Natural end — mark complete optimistically, flush
        audio.addEventListener('ended', () => {
            if (audio.duration) s.hwm = audio.duration;
            if (s.segStart !== null) {
                s.ivs.push([s.segStart, audio.duration || audio.currentTime]);
                s.segStart = null;
            }
            if (s.timer) { clearInterval(s.timer); s.timer = null; }
            sendProgress(dbId, true, false);
            // Optimistic UI
            if (!s.done) { s.done = true; doneSet.add(dbId); checkAllDone(); }
        });
    }

    /* ── Override seek to enforce no-skip ───────────────── */
    const _origSeek = window.lfAudioSeek;
    window.lfAudioSeek = function (audioId, el) {
        const audio = document.getElementById('lfAudio_' + audioId);
        if (audio && audio.dataset.audioDbId) {
            const s = state[audio.dataset.audioDbId];
            if (s && parseFloat(el.value) > s.hwm + 1) {
                el.value = s.hwm;
                if (!audio.paused) audio.currentTime = s.hwm;
                return;
            }
        }
        if (_origSeek) _origSeek(audioId, el);
    };

    /* ── Initialise ──────────────────────────────────────── */
    function initAll() {
        document.querySelectorAll('audio[data-audio-db-id]').forEach(hookAudio);
        if (ALREADY_DONE) unlockMarkComplete();
        else checkAllDone();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }

    /* ── Flush on page unload ────────────────────────────── */
    window.addEventListener('beforeunload', () => {
        Object.keys(state).forEach(dbId => {
            const s = state[dbId];
            if (s.segStart !== null || s.ivs.length) {
                sendProgress(dbId, false, true);
            }
        });
    });
})();
</script>
@endif

@endsection
