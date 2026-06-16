{{--
  AI Course Generator Modal — LTF v2.0 Full Taxonomy UI
  Include in any course create view.
  Requires: $aiCourseType = 'ilt' | 'elearning'
--}}

{{-- ── Trigger Button ─────────────────────────────────────── --}}
<button type="button" onclick="openAiModal()"
        style="display:inline-flex; align-items:center; gap:7px; padding:10px 20px;
               background:linear-gradient(135deg,#1e3a8a,#2563eb); color:#fff;
               border:none; border-radius:9px; font-size:14px; font-weight:700;
               cursor:pointer; box-shadow:0 4px 14px rgba(30,58,138,.35);">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/>
    </svg>
    ✨ Generate with AI
</button>

{{-- ── Modal Overlay ───────────────────────────────────────── --}}
<div id="aiCourseModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.6);
     z-index:9999; align-items:flex-start; justify-content:center; padding:20px; overflow-y:auto;">
    <div style="background:#fff; border-radius:16px; width:100%; max-width:780px;
                box-shadow:0 24px 80px rgba(0,0,0,.3); margin:auto;">

        {{-- Header --}}
        <div style="padding:20px 24px 16px; border-bottom:1px solid #f0f2f5; display:flex; justify-content:space-between; align-items:center;
                    background:linear-gradient(135deg,#0f1e45,#1e3a8a); border-radius:16px 16px 0 0; position:sticky; top:0; z-index:10;">
            <div>
                <div style="font-size:17px; font-weight:800; color:#fff;">✨ AI Course Generator</div>
                <div style="font-size:12.5px; color:#93c5fd; margin-top:2px;">
                    Powered by OpenAI — SMS Training Academy
                </div>
            </div>
            <button onclick="closeAiModal()" type="button"
                    style="background:rgba(255,255,255,.15); border:none; border-radius:8px; width:32px; height:32px;
                           font-size:18px; color:#fff; cursor:pointer; line-height:1;">×</button>
        </div>

        {{-- PHP: Load all taxonomy data --}}
        @php
            use App\Models\LtfLearningFramework;
            use App\Models\LtfDeliveryMethod;
            use App\Models\LtfTrainingModel;
            use App\Models\LtfProgramPurpose;
            use App\Models\LtfStandard;
            use App\Models\LtfIndustry;
            use App\Models\LtfAudienceType;

            $aiFrameworks    = LtfLearningFramework::active()->orderBy('display_order')->get(['id','name','ai_block_hint']);
            $aiDeliveries    = LtfDeliveryMethod::active()->orderBy('display_order')->get(['id','name']);
            $aiTrainingModels= LtfTrainingModel::active()->orderBy('display_order')->get(['id','name']);
            $aiPurposes      = LtfProgramPurpose::active()->orderBy('display_order')->get(['id','name','suggested_framework_id']);
            $aiStandards     = LtfStandard::active()->orderBy('name')->get(['id','name']);
            $aiIndustries    = LtfIndustry::active()->orderBy('display_order')->get(['id','name']);
            $aiAudiences     = LtfAudienceType::active()->orderBy('display_order')->get(['id','name']);

            // Build purpose→framework map for JS
            $purposeToFramework = $aiPurposes->pluck('suggested_framework_id','id')->filter()->toArray();

            // Build framework lookup for JS (includes block sequence and assessment style)
            $allBlockLabels  = \App\Support\LtfBlockStrategy::blockLabels();
            $allStrategyData = \App\Support\LtfBlockStrategy::all();
            $frameworkData   = $aiFrameworks->keyBy('id')->map(function($f) use ($allBlockLabels, $allStrategyData) {
                $hint     = $f->ai_block_hint;
                $strategy = $allStrategyData[$hint] ?? null;
                $sequence = $strategy ? array_map(fn($t) => $allBlockLabels[$t] ?? $t, $strategy['sequence']) : [];
                return [
                    'name'            => $f->name,
                    'hint'            => $hint,
                    'sequence'        => $sequence,
                    'assessmentStyle' => $strategy['assessment_style'] ?? '',
                    'structureHints'  => $strategy['structure_hints']  ?? '',
                    'lessonDepth'     => $strategy['lesson_depth']     ?? 'medium',
                    'rationale'       => $strategy['rationale']        ?? '',
                ];
            })->toArray();
        @endphp

        {{-- Form --}}
        <div style="padding:22px 24px;" id="aiModalForm">
            <div id="aiFormError" style="display:none; background:#fee2e2; border-radius:8px; padding:10px 14px; margin-bottom:16px; font-size:13px; color:#b91c1c;"></div>

            {{-- ═══ Section 1: Course Information ══════════════════════════ --}}
            <div style="margin-bottom:20px;">
                <div style="font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:.8px; color:#6b7280;
                            border-bottom:1px solid #e5e7eb; padding-bottom:6px; margin-bottom:14px;">
                    1 · Course Information
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">

                    <div style="grid-column:1/-1;">
                        <label class="ai-label">Course Name <span style="color:#ef4444;">*</span></label>
                        <input type="text" id="ai_course_name" placeholder="e.g. ISO 14001:2015 Internal Auditor Training" maxlength="250"
                               class="ai-input" onfocus="this.style.borderColor='#1e3a8a'" onblur="this.style.borderColor='#d1d5db'">
                    </div>

                    <div>
                        <label class="ai-label">Duration <span style="color:#ef4444;">*</span></label>
                        <input type="text" id="ai_duration" placeholder="e.g. 16 Hours / 2 Days"
                               class="ai-input" onfocus="this.style.borderColor='#1e3a8a'" onblur="this.style.borderColor='#d1d5db'">
                    </div>

                    <div>
                        <label class="ai-label">Language <span style="color:#ef4444;">*</span></label>
                        <select id="ai_language" class="ai-input">
                            <option value="English">English</option>
                            <option value="Bangla">Bangla</option>
                            <option value="Arabic">Arabic</option>
                            <option value="Chinese">Chinese (Mandarin)</option>
                            <option value="French">French</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- ═══ Section 2: Learning Taxonomy ═══════════════════════════ --}}
            <div style="margin-bottom:20px;">
                <div style="font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:.8px; color:#6b7280;
                            border-bottom:1px solid #e5e7eb; padding-bottom:6px; margin-bottom:14px; display:flex; align-items:center; gap:8px;">
                    2 · Learning Taxonomy
                    <span style="font-size:10px; background:#e0e7ff; color:#3730a3; padding:1px 6px; border-radius:4px; font-weight:700; letter-spacing:.2px; text-transform:none;">LTF v2.0</span>
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">

                    {{-- Program Purpose (drives framework auto-suggest) --}}
                    <div>
                        <label class="ai-label">
                            Program Purpose
                            <span class="ltf-badge">LTF</span>
                            <span class="opt-label">(recommended)</span>
                        </label>
                        <select id="ai_ltf_purpose" class="ai-input" onchange="handlePurposeChange(); updateContextPreview();"
                                onfocus="this.style.borderColor='#4f46e5'" onblur="this.style.borderColor='#d1d5db'">
                            <option value="">— Select training purpose —</option>
                            @foreach($aiPurposes as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                        <div style="font-size:11px; color:#6b7280; margin-top:3px;">Drives the Learning Framework selection below.</div>
                    </div>

                    {{-- Delivery Method --}}
                    <div>
                        <label class="ai-label">
                            Delivery Method
                            <span class="ltf-badge">LTF</span>
                            <span class="opt-label">(optional)</span>
                        </label>
                        <select id="ai_ltf_delivery" class="ai-input" onchange="updateContextPreview()"
                                onfocus="this.style.borderColor='#4f46e5'" onblur="this.style.borderColor='#d1d5db'">
                            <option value="">— Select delivery method —</option>
                            @foreach($aiDeliveries as $d)
                            <option value="{{ $d->id }}">{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Learning Framework (auto-suggested from Purpose) --}}
                    <div>
                        <label class="ai-label">
                            Learning Framework
                            <span class="ltf-badge">LTF</span>
                            <span id="aiFrameworkAutoLabel" style="display:none; font-size:10px; background:#dcfce7; color:#166534; padding:1px 5px; border-radius:4px; font-weight:700; margin-left:4px;">auto-suggested</span>
                        </label>
                        <select id="ai_ltf_framework" class="ai-input" onchange="updateContextPreview()"
                                onfocus="this.style.borderColor='#4f46e5'" onblur="this.style.borderColor='#d1d5db'">
                            <option value="">— Auto-detect from content —</option>
                            @foreach($aiFrameworks as $fw)
                            <option value="{{ $fw->id }}">{{ $fw->name }}</option>
                            @endforeach
                        </select>
                        <div style="font-size:11px; color:#6b7280; margin-top:3px;">Shapes lesson structure, block types, and assessment style.</div>
                    </div>

                    {{-- Competency Level --}}
                    <div>
                        <label class="ai-label">
                            Competency Level
                            <span class="ltf-badge">LTF</span>
                            <span class="opt-label">(optional)</span>
                        </label>
                        <select id="ai_ltf_competency" class="ai-input" onchange="updateContextPreview()"
                                onfocus="this.style.borderColor='#4f46e5'" onblur="this.style.borderColor='#d1d5db'">
                            <option value="">— Match learning level —</option>
                            <option value="beginner">Beginner — awareness &amp; recall</option>
                            <option value="intermediate">Intermediate — understanding &amp; application</option>
                            <option value="advanced">Advanced — analysis &amp; evaluation</option>
                            <option value="expert">Expert — synthesis &amp; leadership</option>
                        </select>
                        <div style="font-size:11px; color:#6b7280; margin-top:3px;">Controls word count, depth, and question difficulty.</div>
                    </div>

                    {{-- Training Model (full-width, less prominent) --}}
                    <div style="grid-column:1/-1;">
                        <label class="ai-label" style="color:#6b7280;">
                            Training Model
                            <span class="ltf-badge" style="background:#f3f4f6; color:#6b7280;">LTF</span>
                            <span class="opt-label">(optional)</span>
                        </label>
                        <select id="ai_ltf_training_model" class="ai-input" style="font-size:13px;" onchange="updateContextPreview()"
                                onfocus="this.style.borderColor='#4f46e5'" onblur="this.style.borderColor='#d1d5db'">
                            <option value="">— Public / Corporate / Internal / etc. —</option>
                            @foreach($aiTrainingModels as $tm)
                            <option value="{{ $tm->id }}">{{ $tm->name }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>
            </div>

            {{-- ═══ Section 3: Domain Context ══════════════════════════════ --}}
            <div style="margin-bottom:20px;">
                <div style="font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:.8px; color:#6b7280;
                            border-bottom:1px solid #e5e7eb; padding-bottom:6px; margin-bottom:14px; display:flex; align-items:center; gap:8px;">
                    3 · Domain Context
                    <span style="font-size:10px; background:#e0e7ff; color:#3730a3; padding:1px 6px; border-radius:4px; font-weight:700; letter-spacing:.2px; text-transform:none;">LTF v2.0</span>
                </div>

                {{-- Standards multi-select --}}
                <div style="margin-bottom:14px;">
                    <label class="ai-label">
                        Standards &amp; Frameworks
                        <span class="ltf-badge">LTF</span>
                        <span class="opt-label">(optional — select all that apply)</span>
                    </label>
                    <div class="chip-container" id="chips_standards">
                        @foreach($aiStandards as $s)
                        <button type="button" class="chip" data-id="{{ $s->id }}" data-type="standards" data-name="{{ $s->name }}"
                                onclick="toggleChip(this)">{{ $s->name }}</button>
                        @endforeach
                    </div>
                </div>

                {{-- Industries multi-select --}}
                <div style="margin-bottom:14px;">
                    <label class="ai-label">
                        Industries
                        <span class="ltf-badge">LTF</span>
                        <span class="opt-label">(optional — select all that apply)</span>
                    </label>
                    <div class="chip-container" id="chips_industries">
                        @foreach($aiIndustries as $ind)
                        <button type="button" class="chip" data-id="{{ $ind->id }}" data-type="industries" data-name="{{ $ind->name }}"
                                onclick="toggleChip(this)">{{ $ind->name }}</button>
                        @endforeach
                    </div>
                </div>

                {{-- Audiences multi-select --}}
                <div style="margin-bottom:14px;">
                    <label class="ai-label">
                        Target Audiences
                        <span class="ltf-badge">LTF</span>
                        <span class="opt-label">(optional — select all that apply)</span>
                    </label>
                    <div class="chip-container" id="chips_audiences">
                        @foreach($aiAudiences as $aud)
                        <button type="button" class="chip" data-id="{{ $aud->id }}" data-type="audiences" data-name="{{ $aud->name }}"
                                onclick="toggleChip(this)">{{ $aud->name }}</button>
                        @endforeach
                    </div>
                </div>

                {{-- Target Audience free text --}}
                <div>
                    <label class="ai-label" style="color:#6b7280;">
                        Who Should Attend <span class="opt-label">(optional description)</span>
                    </label>
                    <input type="text" id="ai_target_audience" maxlength="490"
                           placeholder="e.g. EMS Coordinators, Internal Auditors, Factory Managers"
                           class="ai-input" style="font-size:13px;"
                           onfocus="this.style.borderColor='#1e3a8a'" onblur="this.style.borderColor='#d1d5db'">
                </div>
            </div>

            {{-- ═══ AI Context Preview ═══════════════════════════════════════ --}}
            <div id="aiContextPreview" style="display:none; margin-bottom:20px; border:1.5px solid #c7d2fe; border-radius:10px; overflow:hidden;">
                <div style="background:#e0e7ff; padding:7px 14px; font-size:11px; font-weight:800; text-transform:uppercase;
                            letter-spacing:.5px; color:#3730a3; display:flex; justify-content:space-between; align-items:center;">
                    <span>🔍 AI Generation Context Preview</span>
                    <span style="font-size:10px; font-weight:400; text-transform:none; color:#6d28d9;">Updates as you select taxonomy</span>
                </div>
                <div id="aiContextPreviewBody" style="padding:12px 14px; display:grid; grid-template-columns:140px 1fr; gap:4px 10px; font-size:12px; align-items:start;"></div>
            </div>

            {{-- ═══ Additional Instructions ══════════════════════════════════ --}}
            <div style="margin-bottom:20px;">
                <div style="font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:.8px; color:#6b7280;
                            border-bottom:1px solid #e5e7eb; padding-bottom:6px; margin-bottom:14px;">
                    4 · Additional Instructions <span style="font-weight:400; text-transform:none; font-size:11px; color:#9ca3af;">(optional)</span>
                </div>
                <textarea id="ai_instructions" rows="2" maxlength="900"
                          placeholder="e.g. Focus on practical audit techniques. Include real garment factory scenarios from Bangladesh."
                          style="width:100%; padding:9px 12px; border:1.5px solid #d1d5db; border-radius:7px; font-size:13.5px;
                                 resize:vertical; box-sizing:border-box; line-height:1.5;"
                          onfocus="this.style.borderColor='#1e3a8a'" onblur="this.style.borderColor='#d1d5db'"></textarea>
            </div>

            {{-- Generation Mode (eLearning only) --}}
            @if(($aiCourseType ?? 'ilt') === 'elearning')
            <div id="genModeSection" style="margin-bottom:20px; border:1.5px solid #c7d2fe; border-radius:10px; overflow:hidden;">
                <div style="background:#e0e7ff; padding:8px 14px; font-size:12px; font-weight:800; text-transform:uppercase; color:#3730a3; letter-spacing:.5px;">
                    ✨ Generation Mode
                </div>
                <div style="padding:14px; display:flex; flex-direction:column; gap:10px;">
                    <label style="display:flex; gap:12px; align-items:flex-start; cursor:pointer; padding:10px 12px; border-radius:8px; border:1.5px solid #c7d2fe; background:#f5f3ff;">
                        <input type="radio" name="ai_gen_mode" value="structure" id="modeA" checked
                               style="margin-top:3px; accent-color:#6366f1;" onchange="updateModeUI()">
                        <div>
                            <div style="font-size:13px; font-weight:700; color:#3730a3;">Mode A — Structure Only</div>
                            <div style="font-size:12px; color:#6d28d9; margin-top:2px;">Generate course outline with lesson shells. You manually add content blocks to each lesson.</div>
                        </div>
                    </label>
                    <label style="display:flex; gap:12px; align-items:flex-start; cursor:pointer; padding:10px 12px; border-radius:8px; border:1.5px solid #a3e635; background:#f7fee7;">
                        <input type="radio" name="ai_gen_mode" value="complete" id="modeB"
                               style="margin-top:3px; accent-color:#4d7c0f;" onchange="updateModeUI()">
                        <div>
                            <div style="font-size:13px; font-weight:700; color:#365314;">Mode B — Complete eLearning</div>
                            <div style="font-size:12px; color:#4d7c0f; margin-top:2px;">Auto-generates full lesson content (10–14 varied blocks per lesson). Takes 1–3 minutes. No manual work needed.</div>
                        </div>
                    </label>
                </div>
            </div>
            @endif

            {{-- Generate button --}}
            <div style="display:flex; gap:10px; align-items:center;">
                <button type="button" id="aiGenerateBtn" onclick="submitAiGenerate()"
                        style="background:linear-gradient(135deg,#1e3a8a,#2563eb); color:#fff; padding:12px 28px;
                               border:none; border-radius:9px; font-weight:800; font-size:14px; cursor:pointer;
                               display:flex; align-items:center; gap:8px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/>
                    </svg>
                    Generate Course
                </button>
                <button type="button" onclick="closeAiModal()"
                        style="background:#f1f5f9; color:#374151; padding:12px 20px; border:none; border-radius:9px; font-size:14px; font-weight:600; cursor:pointer;">
                    Cancel
                </button>
            </div>
        </div>

        {{-- Loading overlay --}}
        <div id="aiLoadingState" style="display:none; padding:50px 24px; text-align:center;">
            <div style="width:44px; height:44px; border:4px solid #e9ecf0; border-top-color:#1e3a8a; border-radius:50%;
                        animation:aiSpin 1s linear infinite; margin:0 auto 16px;"></div>
            <div id="aiLoadingTitle" style="font-size:16px; font-weight:700; color:#1e3a8a; margin-bottom:6px;">Generating your course…</div>
            <div id="aiLoadingSubtitle" style="font-size:13.5px; color:#6b7280; line-height:1.6;">
                SMS Training AI is generating a complete, taxonomy-aligned course structure.<br>
                This usually takes 20–45 seconds. Please wait…
            </div>
        </div>

    </div>
</div>

<style>
@keyframes aiSpin { to { transform: rotate(360deg); } }

.ai-label {
    font-size: 12.5px;
    font-weight: 700;
    color: #374151;
    display: block;
    margin-bottom: 5px;
}
.ai-input {
    width: 100%;
    padding: 9px 12px;
    border: 1.5px solid #d1d5db;
    border-radius: 7px;
    font-size: 14px;
    box-sizing: border-box;
    background: #fff;
    transition: border-color .15s;
}
.ltf-badge {
    font-size: 10px;
    background: #e0e7ff;
    color: #3730a3;
    padding: 1px 5px;
    border-radius: 4px;
    font-weight: 700;
    margin-left: 4px;
    letter-spacing: .2px;
}
.opt-label {
    font-size: 11.5px;
    font-weight: 400;
    color: #9ca3af;
}
.chip-container {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    padding: 10px;
    border: 1.5px solid #d1d5db;
    border-radius: 8px;
    background: #f9fafb;
    max-height: 130px;
    overflow-y: auto;
}
.chip {
    display: inline-flex;
    align-items: center;
    padding: 4px 10px;
    border-radius: 20px;
    border: 1.5px solid #d1d5db;
    background: #fff;
    font-size: 12px;
    font-weight: 500;
    color: #374151;
    cursor: pointer;
    transition: all .15s;
    white-space: nowrap;
}
.chip:hover { border-color: #6366f1; color: #4f46e5; }
.chip.active {
    border-color: #4f46e5;
    background: #e0e7ff;
    color: #3730a3;
    font-weight: 700;
}
.preview-row {
    display: contents;
}
.preview-label {
    font-size: 11.5px;
    color: #6b7280;
    font-weight: 600;
    padding: 3px 0;
}
.preview-value {
    font-size: 12px;
    color: #111827;
    font-weight: 500;
    padding: 3px 0;
}
</style>

<script>
const AI_GENERATE_URL = '{{ route('ai.course-generator.generate') }}';
const AI_COURSE_TYPE  = '{{ $aiCourseType ?? 'ilt' }}';
const AI_CSRF         = '{{ csrf_token() }}';

// Purpose → Framework suggestion map (from DB)
const purposeToFramework = @json($purposeToFramework);

// Framework data (id → {name, hint})
const frameworkData = @json($frameworkData);

// Competency labels
const competencyLabels = {
    beginner:     'Beginner — awareness & recall',
    intermediate: 'Intermediate — understanding & application',
    advanced:     'Advanced — analysis & evaluation',
    expert:       'Expert — synthesis & leadership',
};

// Selected multi-select state
const selectedIds = { standards: [], industries: [], audiences: [] };

// ── Chip toggle ───────────────────────────────────────────────────────
function toggleChip(el) {
    const id   = parseInt(el.dataset.id);
    const type = el.dataset.type;
    const idx  = selectedIds[type].indexOf(id);

    if (idx === -1) {
        selectedIds[type].push(id);
        el.classList.add('active');
    } else {
        selectedIds[type].splice(idx, 1);
        el.classList.remove('active');
    }
    updateContextPreview();
}

// ── Purpose → Framework auto-suggest ─────────────────────────────────
function handlePurposeChange() {
    const purposeId  = parseInt(document.getElementById('ai_ltf_purpose').value) || null;
    const fwSelect   = document.getElementById('ai_ltf_framework');
    const autoLabel  = document.getElementById('aiFrameworkAutoLabel');

    if (purposeId && purposeToFramework[purposeId]) {
        const suggestedId = purposeToFramework[purposeId];
        fwSelect.value    = suggestedId;
        autoLabel.style.display = 'inline-block';
    } else {
        autoLabel.style.display = 'none';
    }
}

// ── Context Preview ───────────────────────────────────────────────────
function updateContextPreview() {
    const purposeEl   = document.getElementById('ai_ltf_purpose');
    const frameworkEl = document.getElementById('ai_ltf_framework');
    const deliveryEl  = document.getElementById('ai_ltf_delivery');
    const modelEl     = document.getElementById('ai_ltf_training_model');
    const compEl      = document.getElementById('ai_ltf_competency');

    const purposeText   = purposeEl.options[purposeEl.selectedIndex]?.text || '';
    const frameworkId   = parseInt(frameworkEl.value) || null;
    const deliveryText  = deliveryEl.options[deliveryEl.selectedIndex]?.text || '';
    const modelText     = modelEl.options[modelEl.selectedIndex]?.text || '';
    const compValue     = compEl.value;

    const fw = frameworkId ? frameworkData[frameworkId] : null;

    // Collect chip names
    const stdNames  = [...document.querySelectorAll('#chips_standards .chip.active')].map(c => c.dataset.name);
    const indNames  = [...document.querySelectorAll('#chips_industries .chip.active')].map(c => c.dataset.name);
    const audNames  = [...document.querySelectorAll('#chips_audiences .chip.active')].map(c => c.dataset.name);

    const hasAny = purposeEl.value || frameworkId || deliveryEl.value || modelEl.value || compValue
                   || stdNames.length || indNames.length || audNames.length;

    const preview = document.getElementById('aiContextPreview');
    const body    = document.getElementById('aiContextPreviewBody');

    if (!hasAny) {
        preview.style.display = 'none';
        return;
    }

    preview.style.display = 'block';

    let html = '';

    // ── Row builder ──────────────────────────────────────────────────
    const row = (label, value, full) =>
        full
            ? `<div class="preview-label" style="grid-column:1">${label}</div><div class="preview-value" style="grid-column:2">${value}</div>`
            : `<div class="preview-label">${label}</div><div class="preview-value">${value}</div>`;

    const divider = () => `<div style="grid-column:1/-1; border-top:1px solid #e0e7ff; margin:4px 0;"></div>`;

    // ── Section 1: Course taxonomy ───────────────────────────────────
    if (purposeEl.value) {
        html += row('Program Purpose', escHtml(purposeText.replace(/^— /, '')));
    }
    if (fw) {
        html += row('Learning Framework', `<strong>${escHtml(fw.name)}</strong>`);
    }
    if (deliveryEl.value) {
        html += row('Delivery Method', escHtml(deliveryText.replace(/^— /, '')));
    }
    if (modelEl.value) {
        html += row('Training Model', escHtml(modelText.replace(/^— /, '')));
    }
    if (compValue) {
        const compLabels = {beginner:'Beginner',intermediate:'Intermediate',advanced:'Advanced',expert:'Expert'};
        html += row('Competency Level', `<span style="background:#fef3c7;color:#92400e;padding:1px 6px;border-radius:4px;font-weight:700;">${compLabels[compValue]||compValue}</span>`);
    }

    // ── Section 2: Domain context ────────────────────────────────────
    if (stdNames.length || indNames.length || audNames.length) {
        html += divider();
        if (stdNames.length)  html += row('Standards',  escHtml(stdNames.join(', ')));
        if (indNames.length)  html += row('Industries', escHtml(indNames.join(', ')));
        if (audNames.length)  html += row('Audiences',  escHtml(audNames.join(', ')));
    }

    // ── Section 3: Generation strategy (the key value) ───────────────
    if (fw) {
        html += divider();
        html += `<div style="grid-column:1/-1; font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:.5px; color:#3730a3; margin-top:2px;">Generation Strategy</div>`;

        if (fw.sequence && fw.sequence.length) {
            const blockChips = fw.sequence.map(b =>
                `<span style="display:inline-block;padding:2px 7px;border-radius:10px;background:#e0e7ff;color:#3730a3;font-size:11px;font-weight:600;margin:2px 2px 0 0;">${escHtml(b)}</span>`
            ).join('');
            html += `<div class="preview-label">Block Sequence</div><div class="preview-value" style="line-height:1.8;">${blockChips}</div>`;
        }

        if (fw.lessonDepth) {
            const depthColors = {short:'#fef3c7;color:#92400e',concise:'#fef3c7;color:#92400e',medium:'#dbeafe;color:#1e40af',deep:'#ede9fe;color:#5b21b6'};
            const dc = depthColors[fw.lessonDepth] || 'dbeafe;color:#1e40af';
            html += row('Lesson Depth', `<span style="background:${dc};padding:1px 6px;border-radius:4px;font-weight:700;font-size:11px;">${fw.lessonDepth}</span>`);
        }

        if (fw.assessmentStyle) {
            html += `<div class="preview-label">Assessment Style</div><div class="preview-value" style="font-size:11px;color:#374151;line-height:1.5;">${escHtml(fw.assessmentStyle)}</div>`;
        }

        if (fw.structureHints) {
            html += `<div class="preview-label">Structure</div><div class="preview-value" style="font-size:11px;color:#374151;line-height:1.5;">${escHtml(fw.structureHints)}</div>`;
        }
    }

    body.innerHTML = html;
}

function escHtml(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// ── Modal open/close ─────────────────────────────────────────────────
function openAiModal() {
    document.getElementById('aiCourseModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function closeAiModal() {
    document.getElementById('aiCourseModal').style.display = 'none';
    document.body.style.overflow = '';
    showAiForm();
}

function showAiForm() {
    document.getElementById('aiModalForm').style.display    = 'block';
    document.getElementById('aiLoadingState').style.display = 'none';
    document.getElementById('aiFormError').style.display    = 'none';
}

function updateModeUI() {
    const modeB = document.getElementById('modeB');
    if (!modeB) return;
    const btn = document.getElementById('aiGenerateBtn');
    if (modeB.checked) {
        btn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg> Generate Complete eLearning';
    } else {
        btn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg> Generate Course';
    }
}

// ── Submit ───────────────────────────────────────────────────────────
async function submitAiGenerate() {
    const courseName    = document.getElementById('ai_course_name').value.trim();
    const duration      = document.getElementById('ai_duration').value.trim();
    const language      = document.getElementById('ai_language').value;
    const targetAud     = document.getElementById('ai_target_audience').value.trim();
    const instructions  = document.getElementById('ai_instructions').value.trim();
    const ltfPurpose    = document.getElementById('ai_ltf_purpose').value;
    const ltfDelivery   = document.getElementById('ai_ltf_delivery').value;
    const ltfModel      = document.getElementById('ai_ltf_training_model').value;
    const ltfFramework  = document.getElementById('ai_ltf_framework').value;
    const ltfCompetency = document.getElementById('ai_ltf_competency').value;
    const modeBEl       = document.getElementById('modeB');
    const generationMode = (modeBEl && modeBEl.checked) ? 'complete' : 'structure';

    if (!courseName) { showAiError('Course Name is required.'); return; }
    if (!duration)   { showAiError('Duration is required.'); return; }

    // Show loading
    document.getElementById('aiModalForm').style.display    = 'none';
    document.getElementById('aiLoadingState').style.display = 'block';
    if (generationMode === 'complete') {
        document.getElementById('aiLoadingTitle').textContent   = 'Generating complete eLearning course…';
        document.getElementById('aiLoadingSubtitle').innerHTML  = 'AI is building a taxonomy-aligned course structure AND full lesson content.<br>This can take 1–3 minutes. Please wait…';
    }

    try {
        const payload = {
            course_name:      courseName,
            duration:         duration,
            language:         language,
            target_audience:  targetAud,
            instructions:     instructions,
            course_type:      AI_COURSE_TYPE,
            generation_mode:  generationMode,
        };

        // LTF taxonomy — only include if selected
        if (ltfPurpose)   payload.ltf_program_purpose_id    = parseInt(ltfPurpose);
        if (ltfDelivery)  payload.ltf_delivery_method_id    = parseInt(ltfDelivery);
        if (ltfModel)     payload.ltf_training_model_id     = parseInt(ltfModel);
        if (ltfFramework) payload.ltf_learning_framework_id = parseInt(ltfFramework);
        if (ltfCompetency)payload.ltf_competency_level      = ltfCompetency;

        // Multi-select arrays
        if (selectedIds.standards.length)  payload.ltf_standard_ids  = selectedIds.standards;
        if (selectedIds.industries.length) payload.ltf_industry_ids  = selectedIds.industries;
        if (selectedIds.audiences.length)  payload.ltf_audience_ids  = selectedIds.audiences;

        const res = await fetch(AI_GENERATE_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': AI_CSRF, 'Accept': 'application/json' },
            body: JSON.stringify(payload),
        });

        let data = null;
        try { data = await res.json(); } catch {}

        if (!res.ok) {
            showAiForm();
            if (res.status === 419) {
                showAiError('Your session expired. Please refresh the page and try again.');
            } else if (res.status === 422 && data) {
                const msgs = data.errors
                    ? Object.values(data.errors).flat().join(' ')
                    : (data.message || 'Validation failed. Please check your input.');
                showAiError('⚠ ' + msgs);
            } else {
                showAiError(data?.error || data?.message || 'Server error (' + res.status + '). Please refresh and try again.');
            }
            return;
        }

        if (data && data.success) {
            window.location.href = data.redirect_url;
        } else {
            showAiForm();
            showAiError(data?.error || data?.message || 'Generation failed. Please refresh and try again.');
        }
    } catch (e) {
        showAiForm();
        showAiError('Network error: ' + e.message + '. Please refresh the page.');
    }
}

function showAiError(msg) {
    const el = document.getElementById('aiFormError');
    el.textContent = msg;
    el.style.display = 'block';
}

// Close on backdrop click
document.getElementById('aiCourseModal').addEventListener('click', function(e) {
    if (e.target === this) closeAiModal();
});
</script>
