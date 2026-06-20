@extends('layouts.app')
@section('page-title', $pptCourse->title . ' — PPT eLearning Builder')

@section('content')
<script>window._pptCsrf = '{{ csrf_token() }}';</script>

@php
    $audioCount = $pptCourse->slides()->where('audio_status','ready')->count();
    $aiCount    = $pptCourse->slides()->whereNotNull('ai_explanation')->count();
    $checkCount = $pptCourse->slides()->whereNotNull('knowledge_check')->count();
    $total      = $pptCourse->total_slides ?: 1;
@endphp

<style>
/* ── Layout ─────────────────────────────────────── */
.ppt-wrap      { display:flex; flex-direction:column; height:calc(100vh - 160px); overflow:hidden; }
.ppt-header    { flex-shrink:0; padding:0 0 14px; }
.ppt-body      { flex:1; display:grid; grid-template-columns:300px 1fr; gap:0; overflow:hidden; border:1px solid #e2e8f0; border-radius:14px; background:#fff; box-shadow:0 1px 4px rgba(0,0,0,.06); }

/* ── Left sidebar ───────────────────────────────── */
.ppt-sidebar   { display:flex; flex-direction:column; border-right:1px solid #e9ecf0; overflow:hidden; background:#fafbfc; }
.sb-section    { border-bottom:1px solid #e9ecf0; }
.sb-section-head { display:flex; align-items:center; justify-content:space-between; padding:11px 14px; }
.sb-section-title { font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:.7px; color:#94a3b8; }
.sb-add-btn    { font-size:11px; font-weight:700; color:#1e3a8a; background:none; border:none; cursor:pointer; padding:3px 8px; border-radius:5px; transition:background .12s; }
.sb-add-btn:hover { background:#eff6ff; }
.mod-list      { padding:6px 8px 8px; }
.mod-pill      { display:flex; align-items:center; gap:6px; padding:6px 10px; border-radius:7px; cursor:pointer; border:none; background:none; width:100%; text-align:left; transition:background .12s; position:relative; }
.mod-pill:hover { background:#f1f5f9; }
.mod-pill.active { background:#eff6ff; }
.mod-pill-label { flex:1; min-width:0; font-size:12px; font-weight:600; color:#374151; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.mod-pill.active .mod-pill-label { color:#1e3a8a; }
.mod-pill-count { font-size:10px; font-weight:700; background:#e5e7eb; color:#6b7280; border-radius:10px; padding:1px 7px; flex-shrink:0; }
.mod-pill.active .mod-pill-count { background:#dbeafe; color:#1e40af; }
.mod-actions   { display:none; gap:2px; }
.mod-pill:hover .mod-actions { display:flex; }
.mod-action-btn { background:none; border:none; cursor:pointer; color:#94a3b8; padding:3px; border-radius:4px; transition:color .1s; }
.mod-action-btn:hover { color:#374151; }

/* ── Slide list ─────────────────────────────────── */
.slide-list    { flex:1; overflow-y:auto; padding:6px 8px 8px; }
.slide-item    { display:flex; align-items:center; gap:9px; padding:8px 9px; border-radius:8px; cursor:pointer; border:1px solid transparent; transition:all .12s; margin-bottom:2px; }
.slide-item:hover { background:#f8fafc; border-color:#e2e8f0; }
.slide-item.active { background:#eff6ff; border-color:#bfdbfe; }
.slide-num     { width:22px; text-align:center; font-size:10px; font-weight:700; color:#94a3b8; flex-shrink:0; }
.slide-thumb   { width:52px; height:36px; border-radius:5px; overflow:hidden; flex-shrink:0; background:#e5e7eb; display:flex; align-items:center; justify-content:center; border:1px solid #e5e7eb; }
.slide-thumb img { width:100%; height:100%; object-fit:cover; }
.slide-info    { flex:1; min-width:0; }
.slide-title-t { font-size:12px; font-weight:600; color:#374151; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; line-height:1.3; }
.slide-badges  { display:flex; gap:3px; margin-top:3px; flex-wrap:wrap; }
.sbadge        { font-size:9px; font-weight:700; border-radius:4px; padding:1px 5px; }
.sbadge-audio  { background:#d1fae5; color:#065f46; }
.sbadge-ai     { background:#dbeafe; color:#1e40af; }
.sbadge-check  { background:#ede9fe; color:#5b21b6; }
.sbadge-gone   { background:#fee2e2; color:#991b1b; }

/* ── Right panel ────────────────────────────────── */
.ppt-right     { display:flex; flex-direction:column; overflow:hidden; }
.slide-toolbar { display:flex; align-items:center; gap:10px; padding:12px 20px; border-bottom:1px solid #e9ecf0; flex-shrink:0; background:#fff; flex-wrap:wrap; }
.slide-id      { font-size:12px; color:#94a3b8; font-weight:700; background:#f1f5f9; border-radius:5px; padding:3px 8px; flex-shrink:0; }
.slide-name    { font-size:14px; font-weight:700; color:#111827; flex:1; min-width:0; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.mod-select    { font-size:12px; border:1px solid #d1d5db; border-radius:7px; padding:5px 10px; background:#fafafa; color:#374151; cursor:pointer; outline:none; transition:border .12s; }
.mod-select:focus { border-color:#1e3a8a; }
.remove-btn    { font-size:12px; font-weight:600; padding:5px 12px; border-radius:7px; border:1px solid; cursor:pointer; transition:all .12s; flex-shrink:0; }
.remove-btn.active { background:#fee2e2; color:#991b1b; border-color:#fca5a5; }
.remove-btn.active:hover { background:#fecaca; }
.remove-btn.inactive { background:#f1f5f9; color:#374151; border-color:#e2e8f0; }
.remove-btn.inactive:hover { background:#e2e8f0; }

/* ── Tab bar ────────────────────────────────────── */
.tab-bar       { display:flex; gap:0; border-bottom:2px solid #e9ecf0; flex-shrink:0; padding:0 20px; background:#fff; }
.tab-btn       { display:flex; align-items:center; gap:6px; padding:11px 16px; background:none; border:none; border-bottom:2px solid transparent; margin-bottom:-2px; cursor:pointer; font-size:13px; font-weight:600; color:#6b7280; transition:color .15s,border-color .15s; white-space:nowrap; }
.tab-btn:hover { color:#374151; }
.tab-btn.active { color:#1e3a8a; border-bottom-color:#1e3a8a; }
.tab-btn .tab-dot { width:6px; height:6px; border-radius:50%; background:#22c55e; }

/* ── Tab content ────────────────────────────────── */
.tab-content   { flex:1; overflow-y:auto; padding:20px; }
.field-group   { margin-bottom:16px; }
.field-label   { display:block; font-size:12px; font-weight:700; color:#374151; margin-bottom:5px; letter-spacing:.1px; }
.field-label small { font-weight:400; color:#94a3b8; }
.field-input   { width:100%; border:1px solid #d1d5db; border-radius:7px; padding:8px 11px; font-size:13px; font-family:inherit; color:#111827; background:#fff; transition:border .12s; outline:none; resize:vertical; }
.field-input:focus { border-color:#1e3a8a; box-shadow:0 0 0 3px rgba(30,58,138,.07); }
.char-count    { font-size:11px; color:#94a3b8; margin-top:3px; }

/* ── Slide preview ──────────────────────────────── */
.slide-preview-img { width:100%; border-radius:8px; border:1px solid #e5e7eb; object-fit:contain; max-height:200px; background:#f9fafb; display:block; }

/* ── Info banners ───────────────────────────────── */
.info-banner   { border-radius:8px; padding:10px 14px; font-size:12px; line-height:1.6; margin-bottom:16px; }
.info-blue     { background:#eff6ff; border:1px solid #bfdbfe; color:#1e40af; }
.info-amber    { background:#fff7ed; border:1px solid #fed7aa; color:#92400e; }
.info-green    { background:#f0fdf4; border:1px solid #bbf7d0; color:#065f46; }
.info-purple   { background:#f5f3ff; border:1px solid #ddd6fe; color:#4c1d95; }

/* ── Section divider ────────────────────────────── */
.sect-div      { border:none; border-top:1px solid #f1f5f9; margin:18px 0; }

/* ── Action row ─────────────────────────────────── */
.action-row    { display:flex; align-items:center; gap:10px; flex-wrap:wrap; }

/* ── Audio player card ──────────────────────────── */
.audio-card    { background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; padding:14px 16px; }
.audio-card-head { display:flex; align-items:center; gap:8px; font-size:13px; font-weight:700; color:#065f46; margin-bottom:10px; }

/* ── Knowledge check card ───────────────────────── */
.kc-card       { background:#f5f3ff; border:1px solid #ddd6fe; border-radius:10px; padding:14px 16px; }
.kc-option     { display:flex; align-items:flex-start; gap:8px; padding:6px 10px; border-radius:6px; margin-bottom:4px; font-size:12px; }
.kc-option.correct { background:#d1fae5; color:#065f46; font-weight:700; }
.kc-letter     { width:18px; height:18px; border-radius:4px; background:#e5e7eb; display:flex; align-items:center; justify-content:center; font-size:10px; font-weight:800; flex-shrink:0; }
.kc-option.correct .kc-letter { background:#059669; color:#fff; }

/* ── Progress stats ─────────────────────────────── */
.stats-row     { display:flex; gap:6px; }
.stat-pill     { display:flex; align-items:center; gap:5px; padding:3px 9px; border-radius:6px; font-size:11px; font-weight:700; }

/* ── Empty state ────────────────────────────────── */
.empty-panel   { display:flex; flex-direction:column; align-items:center; justify-content:center; height:100%; color:#9ca3af; text-align:center; padding:40px; }

/* ── Btn tweaks ─────────────────────────────────── */
.btn-purple  { background:#7c3aed; color:#fff; border-color:#7c3aed; }
.btn-purple:hover { background:#6d28d9; }
.btn-success { background:#16a34a; color:#fff; border-color:#16a34a; }
.btn-success:hover { background:#15803d; }

/* ── Voice selector ─────────────────────────────── */
.voice-grid    { display:grid; grid-template-columns:repeat(3,1fr); gap:6px; }
.voice-opt     { border:2px solid #e5e7eb; border-radius:7px; padding:8px; cursor:pointer; text-align:center; transition:all .12s; }
.voice-opt:hover { border-color:#94a3b8; }
.voice-opt.selected { border-color:#1e3a8a; background:#eff6ff; }
.voice-opt-name { font-size:12px; font-weight:700; color:#374151; }
.voice-opt-desc { font-size:10px; color:#6b7280; margin-top:1px; }

/* ── Scrollbar ──────────────────────────────────── */
.slide-list::-webkit-scrollbar, .tab-content::-webkit-scrollbar { width:4px; }
.slide-list::-webkit-scrollbar-thumb, .tab-content::-webkit-scrollbar-thumb { background:#d1d5db; border-radius:4px; }

/* ── Modal ──────────────────────────────────────── */
.modal-overlay { position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:9999; display:flex; align-items:center; justify-content:center; }
.modal-box     { background:#fff; border-radius:14px; padding:26px; box-shadow:0 20px 60px rgba(0,0,0,.2); }
.modal-head    { display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; }
.modal-title   { font-size:16px; font-weight:700; color:#111827; }
.modal-close   { background:none; border:none; cursor:pointer; color:#9ca3af; padding:4px; border-radius:6px; }
.modal-close:hover { background:#f1f5f9; color:#374151; }
</style>

<div class="ppt-wrap">

{{-- ── Header ─────────────────────────────────────── --}}
<div class="ppt-header">
    <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
        {{-- Breadcrumb --}}
        <a href="{{ route('ppt-builder.index') }}" style="display:flex;align-items:center;gap:4px;color:#6b7280;font-size:12px;text-decoration:none;font-weight:600;transition:color .12s;" onmouseover="this.style.color='#374151'" onmouseout="this.style.color='#6b7280'">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
            PPT Builder
        </a>
        <svg width="12" height="12" fill="none" stroke="#d1d5db" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
        <span style="font-size:14px;font-weight:700;color:#111827;">{{ Str::limit($pptCourse->title, 55) }}</span>

        {{-- Stats pills --}}
        <div class="stats-row" style="margin-left:6px;">
            <span class="stat-pill" style="background:#f1f5f9;color:#374151;">
                <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/></svg>
                {{ $pptCourse->total_slides }} slides
            </span>
            @if($audioCount > 0)
            <span class="stat-pill" style="background:#d1fae5;color:#065f46;">
                <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><path d="M15.54 8.46a5 5 0 0 1 0 7.07"/></svg>
                {{ $audioCount }} audio
            </span>
            @endif
            @if($aiCount > 0)
            <span class="stat-pill" style="background:#dbeafe;color:#1e40af;">
                <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
                {{ $aiCount }} AI
            </span>
            @endif
            @if($checkCount > 0)
            <span class="stat-pill" style="background:#ede9fe;color:#5b21b6;">
                <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                {{ $checkCount }} checks
            </span>
            @endif
        </div>

        {{-- Right actions --}}
        <div style="margin-left:auto;display:flex;gap:8px;align-items:center;">
            @if($pptCourse->course_id)
                <a href="/admin/courses/edit/{{ $pptCourse->course_id }}" class="btn btn-ghost btn-sm" target="_blank" style="display:flex;align-items:center;gap:5px;">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                    View Course
                </a>
            @else
                <button onclick="window.dispatchEvent(new CustomEvent('open-publish'))" class="btn btn-success btn-sm" style="display:flex;align-items:center;gap:5px;">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    Publish to Course
                </button>
            @endif
        </div>
    </div>
</div>

{{-- ── Main editor ─────────────────────────────────── --}}
<div class="ppt-body" x-data="pptEditor()" x-init="init()">

    {{-- ══ LEFT SIDEBAR ══════════════════════════════════════ --}}
    <div class="ppt-sidebar">

        {{-- Modules section --}}
        <div class="sb-section">
            <div class="sb-section-head">
                <span class="sb-section-title">Modules</span>
                <button @click="showAddModule=true" class="sb-add-btn">+ Add Module</button>
            </div>
            <div class="mod-list">
                {{-- All slides --}}
                <button @click="filterModule=null"
                        :class="filterModule===null ? 'mod-pill active' : 'mod-pill'">
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
                    <span class="mod-pill-label">All Slides</span>
                    <span class="mod-pill-count">{{ $pptCourse->slides->count() }}</span>
                </button>
                {{-- Per module --}}
                <template x-for="mod in modules" :key="mod.id">
                    <div style="position:relative;">
                        <button @click="filterModule=mod.id"
                                :class="filterModule===mod.id ? 'mod-pill active' : 'mod-pill'">
                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                            <span class="mod-pill-label" x-text="mod.title"></span>
                            <span class="mod-pill-count" x-text="slidesForModule(mod.id).length"></span>
                            <span class="mod-actions" @click.stop>
                                <button class="mod-action-btn" @click="editModule(mod)" title="Edit">
                                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                </button>
                                <button class="mod-action-btn" @click="deleteModule(mod)" title="Delete" style="color:#f87171;">
                                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/></svg>
                                </button>
                            </span>
                        </button>
                    </div>
                </template>
                <div x-show="modules.length===0" style="font-size:11px;color:#9ca3af;padding:6px 10px 4px;text-align:center;">No modules yet — add one to organise slides</div>
            </div>
        </div>

        {{-- Slide list header --}}
        <div class="sb-section-head" style="padding:10px 14px 8px;">
            <span class="sb-section-title">Slides</span>
            <label style="display:flex;align-items:center;gap:5px;font-size:11px;color:#6b7280;cursor:pointer;font-weight:600;">
                <input type="checkbox" x-model="showRemoved" style="margin:0;accent-color:#1e3a8a;">
                Show removed
            </label>
        </div>

        {{-- Slide list --}}
        <div class="slide-list">
            <template x-for="slide in visibleSlides" :key="slide.id">
                <div @click="selectSlide(slide)"
                     :class="selectedSlide && selectedSlide.id===slide.id ? 'slide-item active' : 'slide-item'"
                     :style="slide.is_removed ? 'opacity:.4;' : ''">
                    <span class="slide-num" x-text="slide.slide_number"></span>
                    <div class="slide-thumb">
                        <template x-if="slide.image_url">
                            <img :src="slide.image_url" alt="">
                        </template>
                        <template x-if="!slide.image_url">
                            <svg width="18" height="18" fill="none" stroke="#cbd5e1" stroke-width="1.5" viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="7" y1="8" x2="17" y2="8"/><line x1="7" y1="12" x2="13" y2="12"/></svg>
                        </template>
                    </div>
                    <div class="slide-info">
                        <div class="slide-title-t" x-text="slide.title || 'Slide '+slide.slide_number"></div>
                        <div class="slide-badges">
                            <span x-show="slide.audio_status==='ready'" class="sbadge sbadge-audio">AUDIO</span>
                            <span x-show="slide.ai_narration_script && slide.audio_status!=='ready'" class="sbadge sbadge-ai">AI</span>
                            <span x-show="slide.knowledge_check" class="sbadge sbadge-check">QUIZ</span>
                            <span x-show="slide.is_removed" class="sbadge sbadge-gone">REMOVED</span>
                        </div>
                    </div>
                </div>
            </template>
            <div x-show="visibleSlides.length===0" style="font-size:12px;color:#9ca3af;padding:24px;text-align:center;">No slides to show</div>
        </div>
    </div>

    {{-- ══ RIGHT PANEL ═══════════════════════════════════════ --}}
    <div class="ppt-right">

        {{-- Empty state --}}
        <div x-show="!selectedSlide" class="empty-panel">
            <svg width="56" height="56" fill="none" stroke="#d1d5db" stroke-width="1.2" viewBox="0 0 24 24" style="margin-bottom:14px;"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/><line x1="7" y1="8" x2="17" y2="8"/><line x1="7" y1="12" x2="13" y2="12"/></svg>
            <p style="font-size:14px;font-weight:700;color:#6b7280;margin:0 0 4px;">Select a slide to edit</p>
            <p style="font-size:12px;color:#9ca3af;margin:0;">Click any slide from the list on the left</p>
        </div>

        {{-- Slide editor --}}
        <template x-if="selectedSlide">
            <div style="display:flex;flex-direction:column;height:100%;overflow:hidden;">

                {{-- Toolbar --}}
                <div class="slide-toolbar">
                    <span class="slide-id" x-text="'Slide ' + selectedSlide.slide_number"></span>
                    <span class="slide-name" x-text="selectedSlide.title || 'Untitled Slide'"></span>
                    {{-- Module assign --}}
                    <select @change="assignToModule($event.target.value)"
                            x-model="selectedSlide.ppt_module_id"
                            class="mod-select">
                        <option :value="null">— No module —</option>
                        <template x-for="mod in modules" :key="mod.id">
                            <option :value="mod.id" x-text="mod.title"></option>
                        </template>
                    </select>
                    {{-- Remove / Restore --}}
                    <button @click="toggleRemove()"
                            :class="selectedSlide.is_removed ? 'remove-btn active' : 'remove-btn inactive'">
                        <template x-if="selectedSlide.is_removed">
                            <span style="display:flex;align-items:center;gap:5px;">
                                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-4.95"/></svg>
                                Restore
                            </span>
                        </template>
                        <template x-if="!selectedSlide.is_removed">
                            <span style="display:flex;align-items:center;gap:5px;">
                                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                Remove
                            </span>
                        </template>
                    </button>
                </div>

                {{-- Tab bar --}}
                <div class="tab-bar">
                    {{-- Content tab --}}
                    <button @click="activeTab='Content'" :class="activeTab==='Content' ? 'tab-btn active' : 'tab-btn'">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                        Content
                    </button>
                    {{-- AI tab --}}
                    <button @click="activeTab='AI'" :class="activeTab==='AI' ? 'tab-btn active' : 'tab-btn'">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>
                        AI Explanation
                        <span x-show="selectedSlide.ai_narration_script" class="tab-dot"></span>
                    </button>
                    {{-- Audio tab --}}
                    <button @click="activeTab='Audio'" :class="activeTab==='Audio' ? 'tab-btn active' : 'tab-btn'">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><path d="M15.54 8.46a5 5 0 0 1 0 7.07"/></svg>
                        Audio
                        <span x-show="selectedSlide.audio_status==='ready'" class="tab-dot"></span>
                    </button>
                    {{-- Knowledge Check tab --}}
                    <button @click="activeTab='Knowledge Check'" :class="activeTab==='Knowledge Check' ? 'tab-btn active' : 'tab-btn'">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                        Knowledge Check
                        <span x-show="selectedSlide.knowledge_check" class="tab-dot" style="background:#7c3aed;"></span>
                    </button>
                </div>

                {{-- Tab content --}}
                <div class="tab-content">

                    {{-- ══ Content Tab ══ --}}
                    <div x-show="activeTab==='Content'">
                        {{-- Slide image preview --}}
                        <template x-if="selectedSlide.image_url">
                            <div style="margin-bottom:18px;border-radius:10px;overflow:hidden;border:1px solid #e5e7eb;background:#f9fafb;display:flex;align-items:center;justify-content:center;max-height:210px;">
                                <img :src="selectedSlide.image_url" class="slide-preview-img" style="max-height:210px;">
                            </div>
                        </template>

                        <div class="field-group">
                            <label class="field-label">Slide Title</label>
                            <input type="text" x-model="editForm.title" class="field-input" placeholder="Enter slide title…">
                        </div>

                        <div class="field-group">
                            <label class="field-label">Slide Content</label>
                            <textarea x-model="editForm.content_text" rows="5" class="field-input" placeholder="Slide body text…"></textarea>
                        </div>

                        <div class="field-group">
                            <label class="field-label">Speaker Notes <small>(extracted from PowerPoint)</small></label>
                            <textarea x-model="editForm.speaker_notes" rows="4" class="field-input" placeholder="Speaker notes from the original presentation…"></textarea>
                        </div>

                        <div class="field-group">
                            <label class="field-label">
                                Discussion Points
                                <small> — Tell AI what to emphasise for this slide (max 3,000 chars)</small>
                            </label>
                            <textarea x-model="editForm.discussion_points" rows="5" class="field-input" maxlength="3000"
                                      placeholder="e.g. Focus on auditor responsibilities&#10;Give a factory floor example&#10;Explain what evidence is required"></textarea>
                            <div class="char-count" x-text="(editForm.discussion_points||'').length + ' / 3,000 characters'"></div>
                        </div>

                        <div class="field-group">
                            <label class="field-label">Trainer Notes <small>(private — not shown to learners)</small></label>
                            <textarea x-model="editForm.trainer_notes" rows="3" class="field-input" placeholder="Private notes for trainers…"></textarea>
                        </div>

                        <div class="action-row">
                            <button @click="saveContent()" class="btn btn-primary" :disabled="saving">
                                <span x-text="saving ? 'Saving…' : 'Save Changes'"></span>
                            </button>
                            <span x-show="saveMsg" x-text="saveMsg" style="font-size:12px;color:#16a34a;font-weight:600;"></span>
                        </div>
                    </div>

                    {{-- ══ AI Tab ══ --}}
                    <div x-show="activeTab==='AI'">
                        <div class="info-banner info-blue">
                            <strong>Priority:</strong> Speaker Notes → Discussion Points → Slide Content.<br>
                            Fill in <em>Discussion Points</em> on the Content tab first to get the best AI output.
                        </div>

                        <div class="action-row" style="margin-bottom:18px;">
                            <button @click="generateAiExplain()" class="btn btn-primary" :disabled="aiLoading">
                                <span x-show="!aiLoading" style="display:flex;align-items:center;gap:6px;">
                                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                                    <span x-text="selectedSlide.ai_narration_script ? 'Regenerate AI Explanation' : 'Generate AI Explanation'"></span>
                                </span>
                                <span x-show="aiLoading">Generating…</span>
                            </button>
                            <span x-show="selectedSlide.ai_generated_at" style="font-size:11px;color:#94a3b8;" x-text="selectedSlide.ai_generated_at ? 'Last: ' + selectedSlide.ai_generated_at : ''"></span>
                        </div>

                        <div x-show="aiError" x-text="aiError" class="info-banner" style="background:#fef2f2;border:1px solid #fecaca;color:#991b1b;margin-bottom:16px;"></div>

                        <div x-show="selectedSlide.ai_narration_script">
                            <div class="field-group">
                                <label class="field-label">
                                    AI Narration Script
                                    <small> — spoken by TTS voice</small>
                                </label>
                                <textarea x-model="editForm.ai_narration_script" rows="7" class="field-input"></textarea>
                                <div class="char-count" x-text="'~' + Math.round((editForm.ai_narration_script||'').split(/\s+/).filter(Boolean).length / 2.5) + ' sec audio estimate'"></div>
                            </div>

                            <div class="field-group">
                                <label class="field-label">AI Slide Explanation <small> — shown to learners</small></label>
                                <textarea x-model="editForm.ai_explanation" rows="6" class="field-input"></textarea>
                            </div>

                            <div x-show="(editForm.ai_key_points||[]).length > 0" class="field-group">
                                <label class="field-label">Key Learning Points</label>
                                <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:12px 14px;display:flex;flex-direction:column;gap:6px;">
                                    <template x-for="(point, i) in (editForm.ai_key_points||[])" :key="i">
                                        <div style="display:flex;align-items:flex-start;gap:8px;">
                                            <svg width="14" height="14" fill="none" stroke="#1e3a8a" stroke-width="2.5" viewBox="0 0 24 24" style="margin-top:2px;flex-shrink:0;"><polyline points="20 6 9 17 4 12"/></svg>
                                            <span x-text="point" style="font-size:12px;color:#374151;line-height:1.5;"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <div class="field-group">
                                <label class="field-label">AI Trainer Notes</label>
                                <textarea x-model="editForm.ai_trainer_notes" rows="3" class="field-input"></textarea>
                            </div>

                            <div class="action-row">
                                <button @click="saveAiContent()" class="btn btn-primary" :disabled="saving">
                                    <span x-text="saving ? 'Saving…' : 'Save AI Content'"></span>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- ══ Audio Tab ══ --}}
                    <div x-show="activeTab==='Audio'">

                        <div x-show="!selectedSlide.ai_narration_script" class="info-banner info-amber">
                            <strong>No narration script yet.</strong> Go to the <strong>AI Explanation</strong> tab and generate a script first — it becomes the audio transcript.
                        </div>

                        <div x-show="selectedSlide.ai_narration_script">

                            {{-- Current audio --}}
                            <div x-show="selectedSlide.audio_status==='ready'" class="audio-card" style="margin-bottom:18px;">
                                <div class="audio-card-head">
                                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><path d="M15.54 8.46a5 5 0 0 1 0 7.07"/></svg>
                                    Audio Ready
                                    <span x-show="selectedSlide.audio_duration" style="font-size:11px;color:#065f46;font-weight:400;" x-text="selectedSlide.audio_duration ? '· ' + selectedSlide.audio_duration + 's' : ''"></span>
                                </div>
                                <audio :src="selectedSlide.audio_url" controls style="width:100%;border-radius:6px;"></audio>
                                <div style="margin-top:10px;">
                                    <button @click="deleteAudio()" class="btn btn-del btn-sm">Remove Audio</button>
                                </div>
                            </div>

                            {{-- Voice selector --}}
                            <div class="field-group">
                                <label class="field-label">Voice <small> — choose your AI narrator</small></label>
                                <div class="voice-grid">
                                    <template x-for="v in [{id:'nova',name:'Nova',desc:'Female · Warm'},{id:'alloy',name:'Alloy',desc:'Neutral'},{id:'echo',name:'Echo',desc:'Male'},{id:'fable',name:'Fable',desc:'Male · Narrative'},{id:'onyx',name:'Onyx',desc:'Male · Deep'},{id:'shimmer',name:'Shimmer',desc:'Female · Clear'}]" :key="v.id">
                                        <div @click="audioVoice=v.id" :class="audioVoice===v.id ? 'voice-opt selected' : 'voice-opt'">
                                            <div class="voice-opt-name" x-text="v.name"></div>
                                            <div class="voice-opt-desc" x-text="v.desc"></div>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <div class="action-row" style="margin-bottom:18px;">
                                <button @click="generateAudio()" class="btn btn-primary" :disabled="audioLoading" style="display:flex;align-items:center;gap:6px;">
                                    <svg x-show="!audioLoading" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><path d="M19 12c0 .34-.03.67-.08 1M15.54 8.46a5 5 0 0 1 1.46 3.54"/></svg>
                                    <span x-text="audioLoading ? 'Generating audio…' : (selectedSlide.audio_status==='ready' ? 'Regenerate Audio' : 'Generate Audio')"></span>
                                </button>
                            </div>

                            <hr class="sect-div">

                            <div class="field-group">
                                <label class="field-label">Upload Trainer-Recorded Audio <small>(MP3, WAV, OGG, M4A)</small></label>
                                <div class="action-row">
                                    <input type="file" x-ref="audioUpload" accept=".mp3,.wav,.ogg,.m4a" style="font-size:12px;border:1px solid #d1d5db;border-radius:7px;padding:6px 10px;background:#fafafa;">
                                    <button @click="uploadAudio()" class="btn btn-ghost" :disabled="audioLoading">Upload</button>
                                </div>
                            </div>

                            <div x-show="audioError" x-text="audioError" class="info-banner" style="background:#fef2f2;border:1px solid #fecaca;color:#991b1b;margin-top:12px;"></div>
                        </div>
                    </div>

                    {{-- ══ Knowledge Check Tab ══ --}}
                    <div x-show="activeTab==='Knowledge Check'">

                        {{-- Current check --}}
                        <div x-show="selectedSlide.knowledge_check" class="kc-card" style="margin-bottom:18px;">
                            <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px;">
                                <svg width="14" height="14" fill="none" stroke="#5b21b6" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                                <span style="font-size:13px;font-weight:700;color:#4c1d95;">Knowledge Check</span>
                                <span x-text="selectedSlide.knowledge_check?.type?.replace('_',' ').replace(/\b\w/g,c=>c.toUpperCase())||''"
                                      style="font-size:10px;background:#ede9fe;color:#5b21b6;border-radius:5px;padding:2px 7px;font-weight:700;"></span>
                            </div>
                            <div x-text="selectedSlide.knowledge_check?.question" style="font-size:13px;font-weight:700;color:#1e1b4b;margin-bottom:10px;line-height:1.4;"></div>
                            <template x-for="(opt, i) in (selectedSlide.knowledge_check?.options || [])" :key="i">
                                <div :class="selectedSlide.knowledge_check?.correct === String.fromCharCode(65+i) ? 'kc-option correct' : 'kc-option'">
                                    <span class="kc-letter" x-text="String.fromCharCode(65+i)"></span>
                                    <span x-text="opt"></span>
                                </div>
                            </template>
                            <div x-show="selectedSlide.knowledge_check?.explanation"
                                 style="margin-top:10px;font-size:12px;color:#6b7280;background:#f5f3ff;border-radius:6px;padding:8px 10px;font-style:italic;line-height:1.5;"
                                 x-text="'💡 ' + (selectedSlide.knowledge_check?.explanation||'')"></div>
                            <div style="margin-top:12px;">
                                <button @click="clearCheck()" class="btn btn-del btn-sm">Remove Knowledge Check</button>
                            </div>
                        </div>

                        <div class="field-group">
                            <label class="field-label">Generate AI Knowledge Check <small> — from slide content only</small></label>
                            <div class="action-row">
                                <select x-model="checkType" class="field-input" style="width:auto;">
                                    <option value="multiple_choice">Multiple Choice</option>
                                    <option value="true_false">True / False</option>
                                    <option value="reflection">Reflection Question</option>
                                </select>
                                <button @click="generateCheck()" class="btn btn-purple" :disabled="checkLoading">
                                    <span x-text="checkLoading ? 'Generating…' : (selectedSlide.knowledge_check ? 'Regenerate' : 'Generate Check')"></span>
                                </button>
                            </div>
                        </div>

                        <div x-show="checkError" x-text="checkError" class="info-banner" style="background:#fef2f2;border:1px solid #fecaca;color:#991b1b;margin-top:8px;"></div>
                    </div>

                </div>{{-- /tab-content --}}
            </div>
        </template>

    </div>{{-- /ppt-right --}}

    {{-- ══ Add/Edit Module Modal ══════════════════════════════ --}}
    <div x-show="showAddModule || editingModule"
         class="modal-overlay"
         @keydown.escape.window="showAddModule=false;editingModule=null">
        <div @click.stop class="modal-box" style="width:440px;max-width:95vw;">
            <div class="modal-head">
                <span class="modal-title" x-text="editingModule ? 'Edit Module' : 'New Module'"></span>
                <button @click="showAddModule=false;editingModule=null" class="modal-close">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
            <div class="field-group">
                <label class="field-label">Module Title</label>
                <input type="text" x-model="moduleForm.title" class="field-input" placeholder="e.g. Module 1: Introduction" @keydown.enter="saveModule()">
            </div>
            <div class="field-group">
                <label class="field-label">Description <small>(optional)</small></label>
                <textarea x-model="moduleForm.description" rows="2" class="field-input"></textarea>
            </div>
            <div class="action-row">
                <button @click="saveModule()" class="btn btn-primary" :disabled="moduleLoading" x-text="moduleLoading ? 'Saving…' : 'Save Module'"></button>
                <button @click="showAddModule=false;editingModule=null" class="btn btn-ghost">Cancel</button>
            </div>
        </div>
    </div>

    {{-- ══ Publish Modal ══════════════════════════════════════ --}}
    <div x-show="showPublish"
         @open-publish.window="showPublish=true"
         class="modal-overlay"
         @keydown.escape.window="showPublish=false">
        <div @click.stop class="modal-box" style="width:540px;max-width:95vw;max-height:92vh;overflow-y:auto;">
            <div class="modal-head">
                <div>
                    <div class="modal-title">Publish to eLearning Course</div>
                    <div style="font-size:12px;color:#6b7280;margin-top:2px;">This creates a new draft eLearning course from your slides</div>
                </div>
                <button @click="showPublish=false" class="modal-close">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>

            {{-- What will be created summary --}}
            <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:8px;margin-bottom:20px;">
                <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:10px;text-align:center;">
                    <div style="font-size:20px;font-weight:800;color:#111827;">{{ $pptCourse->slides->count() }}</div>
                    <div style="font-size:10px;color:#6b7280;font-weight:600;text-transform:uppercase;">Lessons</div>
                </div>
                @php $mc = $pptCourse->modules()->count(); @endphp
                <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:10px;text-align:center;">
                    <div style="font-size:20px;font-weight:800;color:#111827;">{{ $mc }}</div>
                    <div style="font-size:10px;color:#6b7280;font-weight:600;text-transform:uppercase;">Modules</div>
                </div>
                <div style="background:#d1fae5;border:1px solid #a7f3d0;border-radius:8px;padding:10px;text-align:center;">
                    <div style="font-size:20px;font-weight:800;color:#065f46;">{{ $audioCount }}</div>
                    <div style="font-size:10px;color:#065f46;font-weight:600;text-transform:uppercase;">Audio</div>
                </div>
                <div style="background:#ede9fe;border:1px solid #ddd6fe;border-radius:8px;padding:10px;text-align:center;">
                    <div style="font-size:20px;font-weight:800;color:#5b21b6;">{{ $checkCount }}</div>
                    <div style="font-size:10px;color:#5b21b6;font-weight:600;text-transform:uppercase;">Quizzes</div>
                </div>
            </div>

            <div x-show="publishError" x-text="publishError" class="info-banner" style="background:#fef2f2;border:1px solid #fecaca;color:#991b1b;margin-bottom:16px;"></div>

            <div style="display:flex;flex-direction:column;gap:14px;">
                <div class="field-group" style="margin:0;">
                    <label class="field-label">Course Title <span style="color:#ef4444;">*</span></label>
                    <input type="text" x-model="publishForm.title" class="field-input" :placeholder="`{{ addslashes($pptCourse->title) }}`">
                </div>
                <div class="field-group" style="margin:0;">
                    <label class="field-label">Description</label>
                    <textarea x-model="publishForm.description" rows="2" class="field-input" placeholder="Brief course description…"></textarea>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div class="field-group" style="margin:0;">
                        <label class="field-label">Language <span style="color:#ef4444;">*</span></label>
                        <select x-model="publishForm.language" class="field-input" style="height:auto;">
                            <option value="English">English</option>
                            <option value="Bangla">Bangla</option>
                            <option value="Arabic">Arabic</option>
                            <option value="French">French</option>
                            <option value="Spanish">Spanish</option>
                        </select>
                    </div>
                    <div class="field-group" style="margin:0;">
                        <label class="field-label">Category</label>
                        <input type="text" x-model="publishForm.category" class="field-input" placeholder="e.g. Safety, HR…">
                    </div>
                </div>
                <div class="field-group" style="margin:0;">
                    <label class="field-label">Target Audience</label>
                    <input type="text" x-model="publishForm.target_audience" class="field-input" placeholder="e.g. All staff, Supervisors…">
                </div>
            </div>

            <div style="margin-top:4px;padding:10px 0 0;">
                <div style="font-size:11px;color:#9ca3af;">Course will be created as <strong>Draft</strong> — you can review lessons and publish from the Course Editor.</div>
            </div>

            <div class="action-row" style="margin-top:20px;">
                <button @click="publishCourse()" class="btn btn-success" :disabled="publishLoading" style="display:flex;align-items:center;gap:6px;">
                    <svg x-show="!publishLoading" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    <span x-text="publishLoading ? 'Creating course…' : 'Create eLearning Course'"></span>
                </button>
                <button @click="showPublish=false" class="btn btn-ghost">Cancel</button>
            </div>
        </div>
    </div>{{-- /publish modal --}}

</div>{{-- /ppt-body / x-data --}}
</div>{{-- /ppt-wrap --}}

<script>
function pptEditor() {
    return {
        slides: @json($slidesData),
        modules: @json($modulesData),
        selectedSlide: null,
        activeTab: 'Content',
        filterModule: null,
        showRemoved: false,
        showAddModule: false,
        editingModule: null,
        saving: false,
        saveMsg: '',
        aiLoading: false,
        aiError: '',
        audioLoading: false,
        audioError: '',
        checkLoading: false,
        checkError: '',
        audioVoice: 'nova',
        checkType: 'multiple_choice',
        moduleLoading: false,
        editForm: {},
        moduleForm: { title: '', description: '' },
        showPublish: false,
        publishLoading: false,
        publishError: '',
        publishForm: {
            title: '{{ addslashes($pptCourse->title) }}',
            description: '{{ addslashes($pptCourse->description ?? '') }}',
            language: 'English',
            category: '',
            target_audience: '',
        },

        init() {
            if (this.slides.length > 0) this.selectSlide(this.slides[0]);
        },

        get visibleSlides() {
            return this.slides.filter(s => {
                if (!this.showRemoved && s.is_removed) return false;
                if (this.filterModule !== null && parseInt(s.ppt_module_id) !== parseInt(this.filterModule)) return false;
                return true;
            });
        },

        slidesForModule(moduleId) {
            return this.slides.filter(s => parseInt(s.ppt_module_id) === parseInt(moduleId) && !s.is_removed);
        },

        selectSlide(slide) {
            this.selectedSlide = slide;
            this.editForm = {
                title: slide.title,
                content_text: slide.content_text,
                speaker_notes: slide.speaker_notes,
                discussion_points: slide.discussion_points,
                ai_narration_script: slide.ai_narration_script,
                ai_explanation: slide.ai_explanation,
                ai_trainer_notes: slide.ai_trainer_notes,
                ai_key_points: slide.ai_key_points || [],
                trainer_notes: slide.trainer_notes,
            };
            this.aiError = '';
            this.audioError = '';
            this.checkError = '';
            this.saveMsg = '';
        },

        async saveContent() {
            this.saving = true;
            this.saveMsg = '';
            const resp = await this.put(`{{ route('ppt-builder.slides.update', [$pptCourse, ':id']) }}`.replace(':id', this.selectedSlide.id), {
                title: this.editForm.title,
                content_text: this.editForm.content_text,
                speaker_notes: this.editForm.speaker_notes,
                discussion_points: this.editForm.discussion_points,
                trainer_notes: this.editForm.trainer_notes,
            });
            this.saving = false;
            if (resp.success) {
                this.updateSlideInList(resp.slide);
                this.saveMsg = 'Saved!';
                setTimeout(() => this.saveMsg = '', 2500);
            }
        },

        async saveAiContent() {
            this.saving = true;
            const resp = await this.put(`{{ route('ppt-builder.slides.update', [$pptCourse, ':id']) }}`.replace(':id', this.selectedSlide.id), {
                ai_narration_script: this.editForm.ai_narration_script,
                ai_explanation: this.editForm.ai_explanation,
                ai_trainer_notes: this.editForm.ai_trainer_notes,
            });
            this.saving = false;
            if (resp.success) this.updateSlideInList(resp.slide);
        },

        async generateAiExplain() {
            this.aiLoading = true;
            this.aiError = '';
            await this.put(`{{ route('ppt-builder.slides.update', [$pptCourse, ':id']) }}`.replace(':id', this.selectedSlide.id), {
                discussion_points: this.editForm.discussion_points,
                speaker_notes: this.editForm.speaker_notes,
            });
            const resp = await this.post(`{{ route('ppt-builder.slides.ai-explain', [$pptCourse, ':id']) }}`.replace(':id', this.selectedSlide.id), {});
            this.aiLoading = false;
            if (resp.success) {
                this.updateSlideInList(resp.slide);
                this.editForm.ai_narration_script = resp.slide.ai_narration_script;
                this.editForm.ai_explanation = resp.slide.ai_explanation;
                this.editForm.ai_trainer_notes = resp.slide.ai_trainer_notes;
                this.editForm.ai_key_points = resp.slide.ai_key_points;
            } else {
                this.aiError = resp.error || 'AI generation failed.';
            }
        },

        async generateAudio() {
            this.audioLoading = true;
            this.audioError = '';
            const resp = await this.post(`{{ route('ppt-builder.slides.audio.generate', [$pptCourse, ':id']) }}`.replace(':id', this.selectedSlide.id), { voice: this.audioVoice });
            this.audioLoading = false;
            if (resp.success) {
                this.selectedSlide.audio_url = resp.audio_url;
                this.selectedSlide.audio_status = 'ready';
                this.selectedSlide.audio_duration = resp.duration;
                this.updateSlideInList(this.selectedSlide);
            } else {
                this.audioError = resp.error || 'Audio generation failed.';
            }
        },

        async uploadAudio() {
            const fileInput = this.$refs.audioUpload;
            if (!fileInput.files.length) { this.audioError = 'Select an audio file first.'; return; }
            this.audioLoading = true;
            this.audioError = '';
            const fd = new FormData();
            fd.append('audio', fileInput.files[0]);
            fd.append('_token', window._pptCsrf || '');
            try {
                const r = await fetch(`{{ route('ppt-builder.slides.audio.upload', [$pptCourse, ':id']) }}`.replace(':id', this.selectedSlide.id), { method: 'POST', body: fd });
                const data = await r.json();
                this.audioLoading = false;
                if (data.success) {
                    this.selectedSlide.audio_url = data.audio_url;
                    this.selectedSlide.audio_status = 'ready';
                    this.updateSlideInList(this.selectedSlide);
                } else {
                    this.audioError = data.error || 'Upload failed.';
                }
            } catch(e) {
                this.audioLoading = false;
                this.audioError = 'Upload failed: ' + e.message;
            }
        },

        async deleteAudio() {
            if (!confirm('Remove the audio for this slide?')) return;
            const resp = await this.delete(`{{ route('ppt-builder.slides.audio.delete', [$pptCourse, ':id']) }}`.replace(':id', this.selectedSlide.id));
            if (resp.success) {
                this.selectedSlide.audio_url = null;
                this.selectedSlide.audio_status = 'none';
                this.selectedSlide.audio_duration = null;
                this.updateSlideInList(this.selectedSlide);
            }
        },

        async generateCheck() {
            this.checkLoading = true;
            this.checkError = '';
            const resp = await this.post(`{{ route('ppt-builder.slides.ai-check', [$pptCourse, ':id']) }}`.replace(':id', this.selectedSlide.id), { type: this.checkType });
            this.checkLoading = false;
            if (resp.success) {
                this.selectedSlide.knowledge_check = resp.knowledge_check;
                this.updateSlideInList(this.selectedSlide);
            } else {
                this.checkError = resp.error || 'Failed to generate knowledge check.';
            }
        },

        async clearCheck() {
            const resp = await this.put(`{{ route('ppt-builder.slides.update', [$pptCourse, ':id']) }}`.replace(':id', this.selectedSlide.id), { knowledge_check: null });
            if (resp.success) {
                this.selectedSlide.knowledge_check = null;
                this.updateSlideInList(this.selectedSlide);
            }
        },

        async toggleRemove() {
            const resp = await this.post(`{{ route('ppt-builder.slides.remove', [$pptCourse, ':id']) }}`.replace(':id', this.selectedSlide.id), {});
            if (resp.success !== undefined) {
                this.selectedSlide.is_removed = resp.is_removed;
                this.updateSlideInList(this.selectedSlide);
            }
        },

        async assignToModule(moduleId) {
            const mid = moduleId && moduleId !== 'null' ? parseInt(moduleId) : null;
            const resp = await this.post(`{{ route('ppt-builder.slides.assign', [$pptCourse, ':id']) }}`.replace(':id', this.selectedSlide.id), {
                ppt_module_id: mid,
            });
            if (resp.success) {
                this.selectedSlide.ppt_module_id = mid;
                this.updateSlideInList(this.selectedSlide);
            }
        },

        editModule(mod) {
            this.editingModule = mod;
            this.moduleForm = { title: mod.title, description: mod.description || '' };
        },

        async saveModule() {
            if (!this.moduleForm.title.trim()) return;
            this.moduleLoading = true;
            let resp;
            if (this.editingModule) {
                resp = await this.put(`{{ route('ppt-builder.modules.update', [$pptCourse, ':mid']) }}`.replace(':mid', this.editingModule.id), this.moduleForm);
                if (resp.success) {
                    const idx = this.modules.findIndex(m => m.id === this.editingModule.id);
                    if (idx > -1) this.modules[idx] = resp.module;
                }
            } else {
                resp = await this.post(`{{ route('ppt-builder.modules.store', $pptCourse) }}`, this.moduleForm);
                if (resp.success) this.modules.push(resp.module);
            }
            this.moduleLoading = false;
            this.showAddModule = false;
            this.editingModule = null;
            this.moduleForm = { title: '', description: '' };
        },

        async deleteModule(mod) {
            if (!confirm(`Delete module "${mod.title}"? Slides will become unassigned.`)) return;
            const resp = await this.delete(`{{ route('ppt-builder.modules.destroy', [$pptCourse, ':mid']) }}`.replace(':mid', mod.id));
            if (resp.success) {
                this.modules = this.modules.filter(m => m.id !== mod.id);
                this.slides.forEach(s => { if (s.ppt_module_id === mod.id) s.ppt_module_id = null; });
                if (this.filterModule === mod.id) this.filterModule = null;
            }
        },

        updateSlideInList(updatedSlide) {
            const idx = this.slides.findIndex(s => s.id === updatedSlide.id);
            if (idx > -1) this.slides[idx] = { ...this.slides[idx], ...updatedSlide };
            if (this.selectedSlide && this.selectedSlide.id === updatedSlide.id) {
                this.selectedSlide = { ...this.selectedSlide, ...updatedSlide };
            }
        },

        async publishCourse() {
            if (!this.publishForm.title.trim()) {
                this.publishError = 'Course title is required.';
                return;
            }
            this.publishLoading = true;
            this.publishError = '';
            const resp = await this.post('{{ route('ppt-builder.publish', $pptCourse) }}', this.publishForm);
            this.publishLoading = false;
            if (resp.success) {
                window.location.href = resp.course_url;
            } else if (resp.course_url) {
                this.publishError = resp.error;
                this.showPublish = false;
                window.open(resp.course_url, '_blank');
            } else {
                this.publishError = resp.error || 'Publish failed. Please try again.';
            }
        },

        csrfToken() { return window._pptCsrf || ''; },

        async post(url, data) {
            try {
                const r = await fetch(url, { method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':this.csrfToken(),'Accept':'application/json'}, body:JSON.stringify(data) });
                return await r.json();
            } catch(e) { return { error: e.message }; }
        },

        async put(url, data) {
            try {
                const r = await fetch(url, { method:'PUT', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':this.csrfToken(),'Accept':'application/json'}, body:JSON.stringify(data) });
                return await r.json();
            } catch(e) { return { error: e.message }; }
        },

        async delete(url) {
            try {
                const r = await fetch(url, { method:'DELETE', headers:{'X-CSRF-TOKEN':this.csrfToken(),'Accept':'application/json'} });
                return await r.json();
            } catch(e) { return { error: e.message }; }
        },
    };
}
</script>

@endsection
