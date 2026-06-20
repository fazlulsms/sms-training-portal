@extends('layouts.app')
@section('page-title', $pptCourse->title . ' — PPT eLearning Builder')

@section('content')
<script>window._pptCsrf = '{{ csrf_token() }}';</script>
<div class="page-wrap">

{{-- ── Header ──────────────────────────────────────── --}}
<div style="display:flex;align-items:center;gap:12px;margin-bottom:20px;flex-wrap:wrap;">
    <a href="{{ route('ppt-builder.index') }}" style="display:flex;align-items:center;gap:5px;color:#6b7280;font-size:13px;text-decoration:none;">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
        PPT Builder
    </a>
    <svg width="14" height="14" fill="none" stroke="#d1d5db" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
    <span style="font-size:15px;font-weight:700;color:#111827;">{{ $pptCourse->title }}</span>
    <span class="badge badge-info" style="margin-left:4px;">{{ $pptCourse->total_slides }} slides</span>
    <div style="margin-left:auto;display:flex;gap:8px;align-items:center;">
        @if($pptCourse->course_id)
            <a href="/admin/courses/edit/{{ $pptCourse->course_id }}" class="btn btn-ghost btn-sm" target="_blank">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right:4px;"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                View Published Course
            </a>
        @else
            {{-- Dispatches event caught by the publish modal inside x-data scope --}}
            <button onclick="window.dispatchEvent(new CustomEvent('open-publish'))" class="btn btn-success btn-sm">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right:4px;"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                Publish to eLearning Course
            </button>
        @endif
    </div>
</div>

{{-- ── Flash ──────────────────────────────────────── --}}
<x-flash-message />

{{-- ── Main Editor (Alpine.js) ─────────────────────── --}}
<div x-data="pptEditor()" x-init="init()">
<div style="display:grid;grid-template-columns:280px 1fr;gap:20px;min-height:600px;">

    {{-- ────────────────────────────────────────
         LEFT PANEL — Slide List & Module Manager
    ──────────────────────────────────────── --}}
    <div style="display:flex;flex-direction:column;gap:12px;">

        {{-- Module manager --}}
        <div class="card">
            <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;padding:10px 14px;">
                <span style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#374151;">Modules</span>
                <button @click="showAddModule=true" style="font-size:11px;color:#1e3a8a;background:none;border:none;cursor:pointer;font-weight:600;">+ Add</button>
            </div>
            <div class="card-body" style="padding:6px 8px;">
                <button @click="activeModule=null;filterModule=null"
                    :style="filterModule===null ? 'background:#eff6ff;color:#1e3a8a;' : ''"
                    style="width:100%;text-align:left;padding:7px 10px;border:none;border-radius:6px;font-size:12px;cursor:pointer;font-weight:500;">
                    All Slides ({{ $pptCourse->slides->count() }})
                </button>
                <template x-for="mod in modules" :key="mod.id">
                    <div style="position:relative;">
                        <button @click="filterModule=mod.id"
                            :style="filterModule===mod.id ? 'background:#eff6ff;color:#1e3a8a;' : ''"
                            style="width:100%;text-align:left;padding:7px 10px 7px 20px;border:none;border-radius:6px;font-size:12px;cursor:pointer;display:flex;align-items:center;justify-content:space-between;">
                            <span x-text="mod.title" style="flex:1;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"></span>
                            <span x-text="slidesForModule(mod.id).length" style="background:#e5e7eb;border-radius:10px;padding:1px 7px;font-size:11px;font-weight:700;flex-shrink:0;margin-left:4px;"></span>
                        </button>
                        <button @click="editModule(mod)" style="position:absolute;right:28px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#94a3b8;padding:2px;">
                            <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        </button>
                        <button @click="deleteModule(mod)" style="position:absolute;right:6px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#ef4444;padding:2px;">
                            <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </button>
                    </div>
                </template>
                <div x-show="modules.length===0" style="font-size:12px;color:#9ca3af;padding:8px 10px;text-align:center;">No modules yet</div>
            </div>
        </div>

        {{-- Slide list --}}
        <div class="card" style="flex:1;overflow:hidden;display:flex;flex-direction:column;">
            <div class="card-header" style="padding:10px 14px;display:flex;align-items:center;justify-content:space-between;">
                <span style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#374151;">Slides</span>
                <label style="display:flex;align-items:center;gap:5px;font-size:11px;color:#6b7280;cursor:pointer;">
                    <input type="checkbox" x-model="showRemoved" style="margin:0;">
                    Show removed
                </label>
            </div>
            <div style="flex:1;overflow-y:auto;padding:6px 8px;">
                <template x-for="slide in visibleSlides" :key="slide.id">
                    <div @click="selectSlide(slide)"
                         :class="selectedSlide && selectedSlide.id===slide.id ? 'slide-item active' : 'slide-item'"
                         :style="slide.is_removed ? 'opacity:.45;' : ''"
                         style="display:flex;align-items:center;gap:8px;padding:8px 10px;border-radius:7px;cursor:pointer;margin-bottom:2px;border:1px solid transparent;transition:background .12s;">

                        {{-- Slide thumbnail --}}
                        <div style="width:44px;height:30px;border-radius:4px;overflow:hidden;flex-shrink:0;background:#e5e7eb;display:flex;align-items:center;justify-content:center;">
                            <template x-if="slide.image_url">
                                <img :src="slide.image_url" style="width:100%;height:100%;object-fit:cover;">
                            </template>
                            <template x-if="!slide.image_url">
                                <span x-text="slide.slide_number" style="font-size:11px;font-weight:700;color:#9ca3af;"></span>
                            </template>
                        </div>

                        <div style="flex:1;min-width:0;">
                            <div x-text="slide.title || 'Slide '+slide.slide_number" style="font-size:12px;font-weight:600;color:#374151;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"></div>
                            <div style="display:flex;gap:4px;margin-top:2px;">
                                <span x-show="slide.audio_status==='ready'" style="font-size:9px;background:#d1fae5;color:#065f46;border-radius:4px;padding:1px 5px;font-weight:700;">AUDIO</span>
                                <span x-show="slide.ai_narration_script && slide.audio_status!=='ready'" style="font-size:9px;background:#dbeafe;color:#1e40af;border-radius:4px;padding:1px 5px;font-weight:700;">AI</span>
                                <span x-show="slide.knowledge_check" style="font-size:9px;background:#ede9fe;color:#5b21b6;border-radius:4px;padding:1px 5px;font-weight:700;">CHECK</span>
                                <span x-show="slide.is_removed" style="font-size:9px;background:#fee2e2;color:#991b1b;border-radius:4px;padding:1px 5px;font-weight:700;">REMOVED</span>
                            </div>
                        </div>
                    </div>
                </template>
                <div x-show="visibleSlides.length===0" style="font-size:12px;color:#9ca3af;padding:16px;text-align:center;">No slides to show</div>
            </div>
        </div>
    </div>

    {{-- ────────────────────────────────────────
         RIGHT PANEL — Slide Editor
    ──────────────────────────────────────── --}}
    <div>

        {{-- No slide selected --}}
        <div x-show="!selectedSlide" style="display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;min-height:400px;color:#9ca3af;">
            <svg width="64" height="64" fill="none" stroke="#d1d5db" stroke-width="1.2" viewBox="0 0 24 24" style="margin-bottom:16px;"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/><line x1="7" y1="8" x2="17" y2="8"/><line x1="7" y1="12" x2="13" y2="12"/></svg>
            <p style="font-size:14px;font-weight:600;color:#6b7280;">Select a slide to edit</p>
            <p style="font-size:12px;color:#9ca3af;">Click any slide from the list on the left</p>
        </div>

        {{-- Slide editor --}}
        <div x-show="selectedSlide" class="card" style="display:flex;flex-direction:column;">

            {{-- Slide header --}}
            <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap;">
                <div>
                    <span style="font-size:12px;color:#9ca3af;font-weight:600;">Slide </span>
                    <span x-text="selectedSlide?.slide_number" style="font-size:12px;color:#9ca3af;font-weight:600;"></span>
                    <span style="font-size:15px;font-weight:700;color:#111827;margin-left:6px;" x-text="selectedSlide?.title || ''"></span>
                </div>
                <div style="display:flex;gap:8px;align-items:center;">
                    {{-- Assign to module --}}
                    <select @change="assignToModule($event.target.value)"
                            x-model="selectedSlide.ppt_module_id"
                            style="font-size:12px;border:1px solid #d1d5db;border-radius:6px;padding:5px 10px;background:white;color:#374151;">
                        <option :value="null">— No module —</option>
                        <template x-for="mod in modules" :key="mod.id">
                            <option :value="mod.id" x-text="mod.title"></option>
                        </template>
                    </select>
                    {{-- Remove/restore --}}
                    <button @click="toggleRemove()"
                            :class="selectedSlide?.is_removed ? 'btn btn-primary btn-sm' : 'btn btn-del btn-sm'">
                        <span x-text="selectedSlide?.is_removed ? 'Restore' : 'Remove'"></span>
                    </button>
                </div>
            </div>

            <div class="card-body">

                {{-- Tab bar --}}
                <div style="display:flex;gap:0;border-bottom:2px solid #e5e7eb;margin-bottom:20px;">
                    <template x-for="tab in ['Content','AI','Audio','Knowledge Check']" :key="tab">
                        <button @click="activeTab=tab"
                                :style="activeTab===tab ? 'border-bottom:2px solid #1e3a8a;color:#1e3a8a;margin-bottom:-2px;' : 'color:#6b7280;'"
                                style="padding:8px 16px;background:none;border:none;cursor:pointer;font-size:13px;font-weight:600;transition:color .15s;"
                                x-text="tab"></button>
                    </template>
                </div>

                {{-- ══ TAB: Content ══ --}}
                <div x-show="activeTab==='Content'">
                    {{-- Slide preview (if image) --}}
                    <template x-if="selectedSlide?.image_url">
                        <div style="margin-bottom:18px;border-radius:8px;overflow:hidden;border:1px solid #e5e7eb;max-height:220px;background:#f9fafb;display:flex;align-items:center;justify-content:center;">
                            <img :src="selectedSlide.image_url" style="max-width:100%;max-height:220px;object-fit:contain;">
                        </div>
                    </template>

                    <div class="form-grid" style="display:grid;gap:16px;">
                        <div>
                            <label class="form-label">Slide Title</label>
                            <input type="text" x-model="editForm.title" class="filter-input" style="width:100%;font-size:13px;">
                        </div>
                        <div>
                            <label class="form-label">Slide Content</label>
                            <textarea x-model="editForm.content_text" rows="6" class="filter-input" style="width:100%;resize:vertical;font-size:13px;font-family:inherit;"></textarea>
                        </div>
                        <div>
                            <label class="form-label">Speaker Notes <span style="font-size:11px;color:#9ca3af;">(from PowerPoint)</span></label>
                            <textarea x-model="editForm.speaker_notes" rows="4" class="filter-input" style="width:100%;resize:vertical;font-size:13px;font-family:inherit;"></textarea>
                        </div>
                        <div>
                            <label class="form-label">
                                Discussion Points
                                <span style="font-size:11px;color:#9ca3af;font-weight:400;"> — Guide AI emphasis (max 3,000 chars)</span>
                            </label>
                            <textarea x-model="editForm.discussion_points" rows="5"
                                      class="filter-input" maxlength="3000"
                                      placeholder="e.g. Explain auditor implications&#10;Give a factory floor example&#10;Discuss common mistakes&#10;Explain audit evidence requirements"
                                      style="width:100%;resize:vertical;font-size:13px;font-family:inherit;"></textarea>
                            <div style="font-size:11px;color:#9ca3af;margin-top:3px;" x-text="(editForm.discussion_points||'').length + ' / 3000 characters'"></div>
                        </div>
                        <div>
                            <label class="form-label">Trainer Notes</label>
                            <textarea x-model="editForm.trainer_notes" rows="3" class="filter-input" style="width:100%;resize:vertical;font-size:13px;font-family:inherit;" placeholder="Private notes for the trainer — not shown to learners."></textarea>
                        </div>
                        <div>
                            <button @click="saveContent()" class="btn btn-primary" :disabled="saving">
                                <span x-text="saving ? 'Saving…' : 'Save Changes'"></span>
                            </button>
                            <span x-show="saveMsg" x-text="saveMsg" style="font-size:12px;color:#22c55e;margin-left:10px;"></span>
                        </div>
                    </div>
                </div>

                {{-- ══ TAB: AI ══ --}}
                <div x-show="activeTab==='AI'">
                    <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;padding:12px 14px;margin-bottom:18px;font-size:12px;color:#374151;line-height:1.6;">
                        <strong style="color:#1e3a8a;">Priority order:</strong> Speaker Notes → Discussion Points → Slide Content.<br>
                        Add discussion points on the <em>Content</em> tab first, then generate the AI explanation.
                    </div>

                    <div style="display:flex;gap:10px;margin-bottom:18px;align-items:center;flex-wrap:wrap;">
                        <button @click="generateAiExplain()" class="btn btn-primary" :disabled="aiLoading">
                            <span x-text="aiLoading ? 'Generating…' : (selectedSlide?.ai_narration_script ? 'Regenerate AI Explanation' : 'Generate AI Explanation')"></span>
                        </button>
                        <span x-show="selectedSlide?.ai_generated_at" style="font-size:11px;color:#9ca3af;" x-text="'Last generated: ' + selectedSlide?.ai_generated_at"></span>
                    </div>

                    <span x-show="aiError" x-text="aiError" style="display:block;color:#ef4444;font-size:12px;margin-bottom:12px;background:#fef2f2;border:1px solid #fecaca;border-radius:6px;padding:8px 12px;"></span>

                    <div x-show="selectedSlide?.ai_narration_script" style="display:grid;gap:16px;">
                        <div>
                            <label class="form-label">AI Narration Script <span style="font-size:11px;color:#9ca3af;">(spoken by AI voice)</span></label>
                            <textarea x-model="editForm.ai_narration_script" rows="7"
                                      class="filter-input" style="width:100%;resize:vertical;font-size:13px;font-family:inherit;"
                                      placeholder="AI narration script will appear here…"></textarea>
                            <div style="font-size:11px;color:#9ca3af;margin-top:3px;" x-text="'~' + Math.round((editForm.ai_narration_script||'').split(' ').length/2.5) + ' sec audio estimate'"></div>
                        </div>
                        <div>
                            <label class="form-label">AI Slide Explanation</label>
                            <textarea x-model="editForm.ai_explanation" rows="7"
                                      class="filter-input" style="width:100%;resize:vertical;font-size:13px;font-family:inherit;"
                                      placeholder="Full slide explanation for learners…"></textarea>
                        </div>
                        <div>
                            <label class="form-label">Key Learning Points</label>
                            <div x-show="selectedSlide?.ai_key_points?.length">
                                <template x-for="(point, i) in (editForm.ai_key_points||[])" :key="i">
                                    <div style="display:flex;gap:8px;align-items:flex-start;margin-bottom:6px;">
                                        <svg width="14" height="14" fill="none" stroke="#1e3a8a" stroke-width="2.5" viewBox="0 0 24 24" style="margin-top:3px;flex-shrink:0;"><polyline points="20 6 9 17 4 12"/></svg>
                                        <span x-text="point" style="font-size:13px;color:#374151;"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <div>
                            <label class="form-label">Trainer Notes <span style="font-size:11px;color:#9ca3af;">(AI suggestion)</span></label>
                            <textarea x-model="editForm.ai_trainer_notes" rows="3"
                                      class="filter-input" style="width:100%;resize:vertical;font-size:13px;font-family:inherit;"></textarea>
                        </div>
                        <div>
                            <button @click="saveAiContent()" class="btn btn-primary" :disabled="saving">
                                <span x-text="saving ? 'Saving…' : 'Save AI Content'"></span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- ══ TAB: Audio ══ --}}
                <div x-show="activeTab==='Audio'">
                    <div x-show="!selectedSlide?.ai_narration_script" style="background:#fff7ed;border:1px solid #fed7aa;border-radius:8px;padding:14px;margin-bottom:18px;font-size:13px;color:#92400e;">
                        Generate an AI narration script on the <strong>AI tab</strong> before creating audio.
                    </div>

                    <div x-show="selectedSlide?.ai_narration_script" style="display:grid;gap:18px;">

                        {{-- Current audio --}}
                        <div x-show="selectedSlide?.audio_status==='ready'" style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:14px;">
                            <div style="font-size:13px;font-weight:600;color:#065f46;margin-bottom:8px;">Audio Ready</div>
                            <audio :src="selectedSlide?.audio_url" controls style="width:100%;"></audio>
                            <div style="font-size:11px;color:#6b7280;margin-top:6px;" x-text="selectedSlide?.audio_duration ? 'Estimated duration: ~' + selectedSlide.audio_duration + ' seconds' : ''"></div>
                        </div>

                        {{-- Generate AI audio --}}
                        <div>
                            <label class="form-label">Generate AI Voice Narration</label>
                            <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
                                <select x-model="audioVoice" style="font-size:13px;border:1px solid #d1d5db;border-radius:6px;padding:7px 12px;background:white;">
                                    <option value="nova">Nova (female, warm)</option>
                                    <option value="alloy">Alloy (neutral)</option>
                                    <option value="echo">Echo (male)</option>
                                    <option value="fable">Fable (male, narrative)</option>
                                    <option value="onyx">Onyx (male, deep)</option>
                                    <option value="shimmer">Shimmer (female, clear)</option>
                                </select>
                                <button @click="generateAudio()" class="btn btn-primary" :disabled="audioLoading">
                                    <span x-text="audioLoading ? 'Generating audio…' : (selectedSlide?.audio_status==='ready' ? 'Regenerate Audio' : 'Generate Audio')"></span>
                                </button>
                            </div>
                        </div>

                        {{-- Upload manual audio --}}
                        <div style="border-top:1px solid #f1f5f9;padding-top:16px;">
                            <label class="form-label">Upload Trainer-Recorded Audio</label>
                            <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
                                <input type="file" x-ref="audioUpload" accept=".mp3,.wav,.ogg,.m4a" style="font-size:12px;">
                                <button @click="uploadAudio()" class="btn btn-ghost" :disabled="audioLoading">Upload</button>
                            </div>
                        </div>

                        {{-- Delete audio --}}
                        <div x-show="selectedSlide?.audio_status==='ready'">
                            <button @click="deleteAudio()" class="btn btn-del btn-sm" style="font-size:12px;">Remove Audio</button>
                        </div>

                        <span x-show="audioError" x-text="audioError" style="display:block;color:#ef4444;font-size:12px;background:#fef2f2;border:1px solid #fecaca;border-radius:6px;padding:8px 12px;"></span>
                    </div>
                </div>

                {{-- ══ TAB: Knowledge Check ══ --}}
                <div x-show="activeTab==='Knowledge Check'">
                    <div style="display:grid;gap:16px;">

                        {{-- Current knowledge check --}}
                        <div x-show="selectedSlide?.knowledge_check" style="background:#f5f3ff;border:1px solid #ddd6fe;border-radius:8px;padding:14px;">
                            <div style="font-size:13px;font-weight:600;color:#4c1d95;margin-bottom:8px;">Current Knowledge Check</div>
                            <div x-text="selectedSlide?.knowledge_check?.question" style="font-size:13px;font-weight:600;color:#374151;margin-bottom:8px;"></div>
                            <template x-for="(opt, i) in (selectedSlide?.knowledge_check?.options || [])" :key="i">
                                <div :style="selectedSlide?.knowledge_check?.correct && opt.startsWith(selectedSlide.knowledge_check.correct) || selectedSlide?.knowledge_check?.correct === String.fromCharCode(65+i) ? 'color:#065f46;font-weight:600;' : 'color:#374151;'"
                                     style="font-size:12px;padding:3px 0;display:flex;gap:6px;align-items:center;">
                                    <span x-text="String.fromCharCode(65+i) + '.'"></span>
                                    <span x-text="opt"></span>
                                </div>
                            </template>
                            <div x-show="selectedSlide?.knowledge_check?.explanation"
                                 style="font-size:12px;color:#6b7280;margin-top:8px;font-style:italic;"
                                 x-text="'Explanation: ' + (selectedSlide?.knowledge_check?.explanation || '')"></div>
                        </div>

                        <div>
                            <label class="form-label">Generate AI Knowledge Check</label>
                            <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
                                <select x-model="checkType" style="font-size:13px;border:1px solid #d1d5db;border-radius:6px;padding:7px 12px;background:white;">
                                    <option value="multiple_choice">Multiple Choice</option>
                                    <option value="true_false">True / False</option>
                                    <option value="reflection">Reflection Question</option>
                                </select>
                                <button @click="generateCheck()" class="btn btn-purple" :disabled="checkLoading">
                                    <span x-text="checkLoading ? 'Generating…' : (selectedSlide?.knowledge_check ? 'Regenerate' : 'Generate Knowledge Check')"></span>
                                </button>
                            </div>
                        </div>

                        <span x-show="checkError" x-text="checkError" style="display:block;color:#ef4444;font-size:12px;background:#fef2f2;border:1px solid #fecaca;border-radius:6px;padding:8px 12px;"></span>

                        <div x-show="selectedSlide?.knowledge_check" style="border-top:1px solid #f1f5f9;padding-top:12px;">
                            <button @click="clearCheck()" class="btn btn-del btn-sm">Remove Knowledge Check</button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>{{-- /editor grid --}}

{{-- ══ Add/Edit Module Modal — must be inside x-data scope ══ --}}
<div x-show="showAddModule || editingModule"
     style="position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:9999;display:flex;align-items:center;justify-content:center;"
     @keydown.escape.window="showAddModule=false;editingModule=null">
    <div @click.stop style="background:white;border-radius:12px;width:440px;max-width:95vw;padding:24px;box-shadow:0 20px 60px rgba(0,0,0,.2);">
        <div style="font-size:16px;font-weight:700;color:#111827;margin-bottom:16px;" x-text="editingModule ? 'Edit Module' : 'Add Module'"></div>
        <div style="margin-bottom:12px;">
            <label class="form-label">Module Title</label>
            <input type="text" x-model="moduleForm.title" class="filter-input" style="width:100%;" placeholder="e.g. Module 1: Introduction to ISO 45001">
        </div>
        <div style="margin-bottom:20px;">
            <label class="form-label">Description <span style="font-weight:400;color:#9ca3af;">(optional)</span></label>
            <textarea x-model="moduleForm.description" rows="2" class="filter-input" style="width:100%;resize:vertical;"></textarea>
        </div>
        <div style="display:flex;gap:10px;">
            <button @click="saveModule()" class="btn btn-primary" :disabled="moduleLoading" x-text="moduleLoading ? 'Saving…' : 'Save Module'"></button>
            <button @click="showAddModule=false;editingModule=null" class="btn btn-ghost">Cancel</button>
        </div>
    </div>
</div>{{-- /module modal --}}

{{-- ══ Publish Modal ══ --}}
<div x-show="showPublish"
     @open-publish.window="showPublish=true"
     style="position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;display:flex;align-items:center;justify-content:center;"
     @keydown.escape.window="showPublish=false">
    <div @click.stop style="background:white;border-radius:14px;width:520px;max-width:95vw;max-height:92vh;overflow-y:auto;padding:28px;box-shadow:0 20px 60px rgba(0,0,0,.25);">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
            <div style="font-size:17px;font-weight:700;color:#111827;">Publish to eLearning Course</div>
            <button @click="showPublish=false" style="background:none;border:none;cursor:pointer;color:#9ca3af;padding:4px;">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>

        <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;padding:12px 14px;margin-bottom:20px;font-size:12px;color:#1e40af;line-height:1.6;">
            This will create a new <strong>eLearning Course</strong> from your {{ $pptCourse->total_slides }} slides — each slide becomes a lesson with its content, audio, and knowledge check.
        </div>

        <div x-show="publishError" x-text="publishError"
             style="display:none;background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:10px 14px;font-size:13px;color:#991b1b;margin-bottom:16px;"></div>

        <div style="display:flex;flex-direction:column;gap:14px;">
            <div>
                <label class="form-label">Course Title <span style="color:#ef4444;">*</span></label>
                <input type="text" x-model="publishForm.title" class="filter-input" style="width:100%;"
                       :placeholder="`{{ $pptCourse->title }}`">
            </div>
            <div>
                <label class="form-label">Description</label>
                <textarea x-model="publishForm.description" rows="3" class="filter-input" style="width:100%;resize:vertical;"
                          placeholder="Brief course description..."></textarea>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div>
                    <label class="form-label">Language <span style="color:#ef4444;">*</span></label>
                    <select x-model="publishForm.language" class="filter-select" style="width:100%;">
                        <option value="English">English</option>
                        <option value="Bangla">Bangla</option>
                        <option value="Arabic">Arabic</option>
                        <option value="French">French</option>
                        <option value="Spanish">Spanish</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Category</label>
                    <input type="text" x-model="publishForm.category" class="filter-input" style="width:100%;" placeholder="e.g. Safety">
                </div>
            </div>
            <div>
                <label class="form-label">Target Audience</label>
                <input type="text" x-model="publishForm.target_audience" class="filter-input" style="width:100%;"
                       placeholder="e.g. All staff, Supervisors...">
            </div>
        </div>

        {{-- Summary --}}
        <div style="margin-top:20px;border:1px solid #e5e7eb;border-radius:8px;padding:12px 16px;">
            <div style="font-size:12px;font-weight:700;color:#374151;margin-bottom:8px;text-transform:uppercase;letter-spacing:.5px;">What will be created</div>
            <div style="font-size:12px;color:#6b7280;display:flex;flex-direction:column;gap:4px;">
                <div>
                    <strong style="color:#374151;">{{ $pptCourse->slides->count() }}</strong> lessons (one per slide)
                </div>
                @php
                    $audioCount = $pptCourse->slides()->where('audio_status','ready')->count();
                    $aiCount    = $pptCourse->slides()->whereNotNull('ai_explanation')->count();
                    $checkCount = $pptCourse->slides()->whereNotNull('knowledge_check')->count();
                    $moduleCount = $pptCourse->modules()->count();
                @endphp
                @if($moduleCount > 0)
                <div><strong style="color:#374151;">{{ $moduleCount }}</strong> modules</div>
                @endif
                @if($audioCount > 0)
                <div><strong style="color:#22c55e;">{{ $audioCount }}</strong> lessons with audio ready</div>
                @endif
                @if($aiCount > 0)
                <div><strong style="color:#3b82f6;">{{ $aiCount }}</strong> lessons with AI explanation</div>
                @endif
                @if($checkCount > 0)
                <div><strong style="color:#7c3aed;">{{ $checkCount }}</strong> lessons with knowledge check</div>
                @endif
                <div style="margin-top:4px;color:#9ca3af;">Course will be created as <strong>Draft</strong> — you can review and publish from the Course Editor.</div>
            </div>
        </div>

        <div style="display:flex;gap:10px;margin-top:22px;">
            <button @click="publishCourse()" class="btn btn-success" :disabled="publishLoading">
                <span x-text="publishLoading ? 'Publishing…' : 'Create eLearning Course'"></span>
            </button>
            <button @click="showPublish=false" class="btn btn-ghost">Cancel</button>
        </div>
    </div>
</div>{{-- /publish modal --}}

</div>{{-- /x-data scope --}}

</div>{{-- /page-wrap --}}

<style>
.form-label { display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:5px; }
.slide-item:hover { background:#f8fafc; border-color:#e2e8f0; }
.slide-item.active { background:#eff6ff; border-color:#bfdbfe; }
.btn-purple { background:#7c3aed; color:#fff; border-color:#7c3aed; }
.btn-purple:hover { background:#6d28d9; }
.btn-success { background:#16a34a; color:#fff; border-color:#16a34a; }
.btn-success:hover { background:#15803d; }
</style>

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
            // Save current form first
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
            if (idx > -1) {
                this.slides[idx] = { ...this.slides[idx], ...updatedSlide };
            }
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
                // Redirect to the newly created course editor
                window.location.href = resp.course_url;
            } else if (resp.course_url) {
                // Already published — offer link
                this.publishError = resp.error + ' ';
                this.showPublish = false;
                window.open(resp.course_url, '_blank');
            } else {
                this.publishError = resp.error || 'Publish failed. Please try again.';
            }
        },

        // ── HTTP helpers ──────────────────────
        csrfToken() {
            return window._pptCsrf || '';
        },

        async post(url, data) {
            try {
                const r = await fetch(url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken(), 'Accept': 'application/json' },
                    body: JSON.stringify(data),
                });
                return await r.json();
            } catch(e) { return { error: e.message }; }
        },

        async put(url, data) {
            try {
                const r = await fetch(url, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken(), 'Accept': 'application/json' },
                    body: JSON.stringify(data),
                });
                return await r.json();
            } catch(e) { return { error: e.message }; }
        },

        async delete(url) {
            try {
                const r = await fetch(url, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': this.csrfToken(), 'Accept': 'application/json' },
                });
                return await r.json();
            } catch(e) { return { error: e.message }; }
        },
    };
}
</script>

@endsection
