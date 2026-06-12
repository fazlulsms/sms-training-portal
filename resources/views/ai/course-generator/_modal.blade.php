{{--
  AI Course Generator Modal
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
<div id="aiCourseModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.55);
     z-index:9999; align-items:center; justify-content:center; padding:20px;">
    <div style="background:#fff; border-radius:16px; width:100%; max-width:680px; max-height:90vh;
                overflow-y:auto; box-shadow:0 24px 80px rgba(0,0,0,.3);">

        {{-- Header --}}
        <div style="padding:20px 24px 16px; border-bottom:1px solid #f0f2f5; display:flex; justify-content:space-between; align-items:center;
                    background:linear-gradient(135deg,#0f1e45,#1e3a8a); border-radius:16px 16px 0 0;">
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

        {{-- Form --}}
        <div style="padding:22px 24px;" id="aiModalForm">
            <div id="aiFormError" style="display:none; background:#fee2e2; border-radius:8px; padding:10px 14px; margin-bottom:16px; font-size:13px; color:#b91c1c;"></div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">

                {{-- Course Name --}}
                <div style="grid-column:1/-1;">
                    <label style="font-size:12.5px; font-weight:700; color:#374151; display:block; margin-bottom:5px;">
                        Course Name <span style="color:#ef4444;">*</span>
                    </label>
                    <input type="text" id="ai_course_name" placeholder="e.g. ISO 14001:2015 Internal Auditor Training"
                           style="width:100%; padding:9px 12px; border:1.5px solid #d1d5db; border-radius:7px; font-size:14px; box-sizing:border-box;"
                           onfocus="this.style.borderColor='#1e3a8a'" onblur="this.style.borderColor='#d1d5db'">
                </div>

                {{-- Duration --}}
                <div>
                    <label style="font-size:12.5px; font-weight:700; color:#374151; display:block; margin-bottom:5px;">
                        Duration <span style="color:#ef4444;">*</span>
                    </label>
                    <input type="text" id="ai_duration" placeholder="e.g. 16 Hours / 2 Days"
                           style="width:100%; padding:9px 12px; border:1.5px solid #d1d5db; border-radius:7px; font-size:14px; box-sizing:border-box;"
                           onfocus="this.style.borderColor='#1e3a8a'" onblur="this.style.borderColor='#d1d5db'">
                </div>

                {{-- Language --}}
                <div>
                    <label style="font-size:12.5px; font-weight:700; color:#374151; display:block; margin-bottom:5px;">
                        Language <span style="color:#ef4444;">*</span>
                    </label>
                    <select id="ai_language" style="width:100%; padding:9px 12px; border:1.5px solid #d1d5db; border-radius:7px; font-size:14px; box-sizing:border-box;">
                        <option value="English">English</option>
                        <option value="Bangla">Bangla</option>
                        <option value="Arabic">Arabic</option>
                        <option value="Chinese">Chinese (Mandarin)</option>
                        <option value="French">French</option>
                    </select>
                </div>

                {{-- Target Audience --}}
                <div style="grid-column:1/-1;">
                    <label style="font-size:12.5px; font-weight:700; color:#374151; display:block; margin-bottom:5px;">
                        Target Audience <span style="color:#ef4444;">*</span>
                    </label>
                    <input type="text" id="ai_target_audience" placeholder="e.g. Internal Auditors, EMS Coordinators, Compliance Managers"
                           style="width:100%; padding:9px 12px; border:1.5px solid #d1d5db; border-radius:7px; font-size:14px; box-sizing:border-box;"
                           onfocus="this.style.borderColor='#1e3a8a'" onblur="this.style.borderColor='#d1d5db'">
                </div>

                {{-- Industry --}}
                <div>
                    <label style="font-size:12.5px; font-weight:700; color:#374151; display:block; margin-bottom:5px;">
                        Industry <span style="color:#ef4444;">*</span>
                    </label>
                    <select id="ai_industry" style="width:100%; padding:9px 12px; border:1.5px solid #d1d5db; border-radius:7px; font-size:14px; box-sizing:border-box;">
                        <option value="General">General / Cross-Industry</option>
                        <option value="Manufacturing">Manufacturing</option>
                        <option value="Garments & Apparel">Garments & Apparel</option>
                        <option value="Food & Beverage">Food & Beverage</option>
                        <option value="Construction">Construction</option>
                        <option value="Healthcare">Healthcare</option>
                        <option value="Chemical">Chemical</option>
                        <option value="Logistics & Supply Chain">Logistics & Supply Chain</option>
                        <option value="Energy">Energy</option>
                        <option value="Financial Services">Financial Services</option>
                    </select>
                </div>

                {{-- Learning Level --}}
                <div>
                    <label style="font-size:12.5px; font-weight:700; color:#374151; display:block; margin-bottom:5px;">
                        Learning Level <span style="color:#ef4444;">*</span>
                    </label>
                    <select id="ai_learning_level" style="width:100%; padding:9px 12px; border:1.5px solid #d1d5db; border-radius:7px; font-size:14px; box-sizing:border-box;">
                        <option value="Beginner">Beginner</option>
                        <option value="Intermediate" selected>Intermediate</option>
                        <option value="Advanced">Advanced</option>
                        <option value="Expert">Expert / Lead Auditor</option>
                    </select>
                </div>

                {{-- Standard / Framework (optional) --}}
                <div style="grid-column:1/-1;">
                    <label style="font-size:12.5px; font-weight:700; color:#374151; display:block; margin-bottom:5px;">
                        Standard / Framework <span style="font-size:11.5px; font-weight:400; color:#9ca3af;">(optional)</span>
                    </label>
                    <input type="text" id="ai_standard" placeholder="e.g. ISO 14001:2015, SLCP, Higg FEM, GRI Standards"
                           style="width:100%; padding:9px 12px; border:1.5px solid #d1d5db; border-radius:7px; font-size:14px; box-sizing:border-box;"
                           onfocus="this.style.borderColor='#1e3a8a'" onblur="this.style.borderColor='#d1d5db'">
                </div>

                {{-- Additional Instructions (optional) --}}
                <div style="grid-column:1/-1;">
                    <label style="font-size:12.5px; font-weight:700; color:#374151; display:block; margin-bottom:5px;">
                        Additional Instructions <span style="font-size:11.5px; font-weight:400; color:#9ca3af;">(optional)</span>
                    </label>
                    <textarea id="ai_instructions" rows="3"
                              placeholder="e.g. Focus on practical audit techniques. Include real garment factory scenarios."
                              style="width:100%; padding:9px 12px; border:1.5px solid #d1d5db; border-radius:7px; font-size:13.5px; resize:vertical; box-sizing:border-box; line-height:1.5;"
                              onfocus="this.style.borderColor='#1e3a8a'" onblur="this.style.borderColor='#d1d5db'"></textarea>
                </div>
            </div>

            {{-- Generation Mode (eLearning only) --}}
            @if(($aiCourseType ?? 'ilt') === 'elearning')
            <div id="genModeSection" style="margin-top:18px; border:1.5px solid #c7d2fe; border-radius:10px; overflow:hidden;">
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
            <div style="margin-top:20px; display:flex; gap:10px; align-items:center;">
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
                SMS Training AI is generating a 90–95% complete course structure.<br>
                This usually takes 20–45 seconds. Please wait…
            </div>
        </div>

    </div>
</div>

<style>
@keyframes aiSpin { to { transform: rotate(360deg); } }
</style>

<script>
const AI_GENERATE_URL = '{{ route('ai.course-generator.generate') }}';
const AI_COURSE_TYPE  = '{{ $aiCourseType ?? 'ilt' }}';
const AI_CSRF         = '{{ csrf_token() }}';

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

async function submitAiGenerate() {
    const courseName    = document.getElementById('ai_course_name').value.trim();
    const duration      = document.getElementById('ai_duration').value.trim();
    const language      = document.getElementById('ai_language').value;
    const targetAud     = document.getElementById('ai_target_audience').value.trim();
    const industry      = document.getElementById('ai_industry').value;
    const level         = document.getElementById('ai_learning_level').value;
    const standard      = document.getElementById('ai_standard').value.trim();
    const instructions  = document.getElementById('ai_instructions').value.trim();
    const modeBEl       = document.getElementById('modeB');
    const generationMode = (modeBEl && modeBEl.checked) ? 'complete' : 'structure';

    if (!courseName)    { showAiError('Course Name is required.'); return; }
    if (!duration)      { showAiError('Duration is required.'); return; }
    if (!targetAud)     { showAiError('Target Audience is required.'); return; }

    // Show loading with mode-appropriate message
    document.getElementById('aiModalForm').style.display    = 'none';
    document.getElementById('aiLoadingState').style.display = 'block';
    if (generationMode === 'complete') {
        document.getElementById('aiLoadingTitle').textContent    = 'Generating complete eLearning course…';
        document.getElementById('aiLoadingSubtitle').innerHTML   = 'AI is building course structure AND generating full lesson content.<br>This can take 1–3 minutes for a full course. Please wait…';
    }

    try {
        const res  = await fetch(AI_GENERATE_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': AI_CSRF, 'Accept': 'application/json' },
            body: JSON.stringify({
                course_name:      courseName,
                duration:         duration,
                language:         language,
                target_audience:  targetAud,
                industry:         industry,
                learning_level:   level,
                standard:         standard,
                instructions:     instructions,
                course_type:      AI_COURSE_TYPE,
                generation_mode:  generationMode,
            }),
        });

        if (res.status === 419) {
            showAiForm();
            showAiError('Page session expired. Please refresh the page and try again.');
            return;
        }
        if (!res.ok && res.status !== 200) {
            showAiForm();
            showAiError('Server error (' + res.status + '). Please refresh the page and try again.');
            return;
        }

        const data = await res.json();

        if (data.success) {
            window.location.href = data.redirect_url;
        } else {
            showAiForm();
            showAiError(data.error || data.message || 'Generation failed. Please refresh and try again.');
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
