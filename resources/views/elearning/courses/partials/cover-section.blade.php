<style>
/* ── Cover Generator ───────────────────────────────────────── */
.cover-wrap        { display:grid; grid-template-columns:340px 1fr; gap:32px; align-items:start; }
@media(max-width:780px){ .cover-wrap { grid-template-columns:1fr; } }

.cover-preview-box {
    border:2px dashed #cbd5e1; border-radius:14px; overflow:hidden;
    background:#f8fafc; min-height:192px; position:relative;
    display:flex; align-items:center; justify-content:center;
}
.cover-preview-box img { width:100%; height:100%; object-fit:cover; display:block; }
.cover-empty-state {
    display:flex; flex-direction:column; align-items:center; justify-content:center;
    padding:32px 20px; text-align:center; color:#94a3b8;
}
.cover-empty-state svg { width:52px; height:52px; opacity:.4; margin-bottom:12px; }
.cover-empty-state p  { font-size:13px; line-height:1.5; margin:0; }

.cover-badge {
    position:absolute; top:10px; right:10px;
    background:rgba(16,185,129,.9); color:#fff;
    font-size:11px; font-weight:700; padding:3px 9px; border-radius:20px;
    letter-spacing:.3px;
}
.cover-badge.manual { background:rgba(99,102,241,.9); }

.cover-actions    { display:flex; flex-wrap:wrap; gap:8px; margin-top:12px; }
.cov-btn {
    display:inline-flex; align-items:center; gap:6px;
    padding:8px 15px; border-radius:8px; font-size:13px; font-weight:600;
    cursor:pointer; border:none; font-family:inherit; transition:all .15s;
}
.cov-btn.primary   { background:#1e3a8a; color:#fff; }
.cov-btn.primary:hover   { background:#1e40af; }
.cov-btn.secondary { background:#f1f5f9; color:#374151; border:1.5px solid #d1d5db; }
.cov-btn.secondary:hover { background:#e2e8f0; }
.cov-btn.danger    { background:#fee2e2; color:#b91c1c; border:1.5px solid #fca5a5; }
.cov-btn.danger:hover    { background:#fecaca; }
.cov-btn:disabled  { opacity:.5; cursor:not-allowed; }

.cover-controls   { }
.ctrl-label { font-size:12.5px; font-weight:700; color:#374151; margin-bottom:6px; display:block; text-transform:uppercase; letter-spacing:.3px; }

.style-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:8px; margin-bottom:18px; }
.style-card {
    border:2px solid #e5e7eb; border-radius:10px; padding:12px 10px; text-align:center;
    cursor:pointer; transition:all .15s; background:#fff;
}
.style-card:hover { border-color:#93c5fd; }
.style-card.selected { border-color:#1e3a8a; background:#eff6ff; }
.style-card .sc-icon { font-size:22px; margin-bottom:5px; }
.style-card .sc-name { font-size:12px; font-weight:700; color:#374151; }
.style-card .sc-desc { font-size:11px; color:#6b7280; margin-top:2px; }

.complexity-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:8px; margin-bottom:18px; }

.prompt-box {
    width:100%; font-size:12.5px; font-family:inherit;
    border:1.5px solid #d1d5db; border-radius:8px;
    padding:10px 12px; resize:vertical; line-height:1.55;
    box-sizing:border-box; color:#374151; outline:none;
    min-height:130px;
}
.prompt-box:focus { border-color:#1e3a8a; }

.prompt-actions { display:flex; gap:8px; margin-top:8px; flex-wrap:wrap; }

.gen-status-bar {
    display:none; align-items:center; gap:10px;
    background:#eff6ff; border:1px solid #bfdbfe;
    border-radius:8px; padding:10px 14px; margin-top:12px;
    font-size:13px; color:#1e40af;
}
.gen-spinner {
    width:18px; height:18px; border:3px solid #bfdbfe;
    border-top-color:#1e3a8a; border-radius:50%;
    animation:spin .7s linear infinite; flex-shrink:0;
}
@keyframes spin { to { transform:rotate(360deg); } }

.gen-error-bar {
    display:none; align-items:center; gap:10px;
    background:#fee2e2; border:1px solid #fca5a5;
    border-radius:8px; padding:10px 14px; margin-top:12px;
    font-size:13px; color:#b91c1c;
}

.cost-note { font-size:11.5px; color:#6b7280; margin-top:10px; }
.cost-note strong { color:#374151; }

.thumb-grid { display:flex; gap:12px; margin-top:16px; flex-wrap:wrap; }
.thumb-item { }
.thumb-item img { border-radius:8px; border:1px solid #e5e7eb; }
.thumb-item span { display:block; font-size:11px; color:#6b7280; margin-top:4px; text-align:center; }
</style>

{{-- ── Cover Generator Tab ───────────────────────────────────── --}}
<div class="cover-wrap" id="coverWrap">

    {{-- Left: Preview --}}
    <div>
        <div class="cover-preview-box" id="coverPreviewBox" style="aspect-ratio:16/9;">
            @if($course->cover_image)
                <img src="{{ asset('storage/'.$course->cover_image) }}" alt="Course Cover" id="coverImg">
                @if($course->cover_generated_by_ai)
                    <span class="cover-badge">AI Generated</span>
                @else
                    <span class="cover-badge manual">Custom Upload</span>
                @endif
            @else
                <div class="cover-empty-state" id="coverEmpty">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <rect x="3" y="3" width="18" height="18" rx="2"/>
                        <circle cx="8.5" cy="8.5" r="1.5"/>
                        <path d="M21 15l-5-5L5 21"/>
                    </svg>
                    <p>No cover image yet.<br>Generate one with AI or upload your own.</p>
                </div>
            @endif
        </div>

        <div class="cover-actions">
            <button type="button" class="cov-btn secondary" onclick="document.getElementById('coverUploadInput').click()">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                Upload Custom
            </button>
            @if($course->cover_image)
            <a href="{{ asset('storage/'.$course->cover_image) }}" download class="cov-btn secondary">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Download
            </a>
            <button type="button" class="cov-btn danger" id="deleteCoverBtn" onclick="deleteCover()">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/></svg>
                Remove
            </button>
            @endif
        </div>

        {{-- Thumbnail sizes --}}
        @if($course->cover_image)
        <div class="thumb-grid" style="margin-top:16px;">
            <div class="thumb-item">
                <img src="{{ asset('storage/'.($course->cover_thumbnail ?? $course->cover_image)) }}" alt="Thumbnail" style="width:120px; height:68px; object-fit:cover;">
                <span>Card thumbnail (600×338)</span>
            </div>
            <div class="thumb-item">
                <img src="{{ asset('storage/'.$course->cover_image) }}" alt="Cover" style="width:190px; height:107px; object-fit:cover;">
                <span>Full cover (1200×675)</span>
            </div>
        </div>
        @endif

        <input type="file" id="coverUploadInput" accept="image/jpeg,image/png,image/webp" style="display:none" onchange="uploadCover(this)">
    </div>

    {{-- Right: Controls --}}
    <div class="cover-controls">

        {{-- Style Selector --}}
        <span class="ctrl-label">Illustration Style</span>
        <div class="style-grid" id="styleGrid">
            <div class="style-card selected" data-val="modern" onclick="selectStyle(this)">
                <div class="sc-icon">✨</div>
                <div class="sc-name">Modern</div>
                <div class="sc-desc">Clean flat vector</div>
            </div>
            <div class="style-card" data-val="corporate" onclick="selectStyle(this)">
                <div class="sc-icon">🏢</div>
                <div class="sc-name">Corporate</div>
                <div class="sc-desc">Formal business</div>
            </div>
            <div class="style-card" data-val="premium" onclick="selectStyle(this)">
                <div class="sc-icon">💎</div>
                <div class="sc-name">Premium</div>
                <div class="sc-desc">High-end quality</div>
            </div>
        </div>

        {{-- Complexity Selector --}}
        <span class="ctrl-label">Composition</span>
        <div class="complexity-grid" id="complexityGrid">
            <div class="style-card" data-val="simple" onclick="selectComplexity(this)">
                <div class="sc-icon">◻️</div>
                <div class="sc-name">Simple</div>
                <div class="sc-desc">Minimal, 1 element</div>
            </div>
            <div class="style-card selected" data-val="standard" onclick="selectComplexity(this)">
                <div class="sc-icon">⬜</div>
                <div class="sc-name">Standard</div>
                <div class="sc-desc">1–2 people</div>
            </div>
            <div class="style-card" data-val="premium" onclick="selectComplexity(this)">
                <div class="sc-icon">🖼️</div>
                <div class="sc-name">Premium</div>
                <div class="sc-desc">Rich layered</div>
            </div>
        </div>

        {{-- Prompt Textarea --}}
        <span class="ctrl-label" style="margin-top:4px;">AI Prompt</span>
        <textarea class="prompt-box" id="coverPrompt" placeholder="Click 'Build Prompt' to auto-generate, or type your own…">{{ $course->cover_prompt ?? '' }}</textarea>
        <div class="prompt-actions">
            <button type="button" class="cov-btn secondary" onclick="buildPrompt()">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                Build Prompt
            </button>
            <button type="button" class="cov-btn secondary" onclick="document.getElementById('coverPrompt').value=''">Clear</button>
        </div>

        {{-- Generate button --}}
        <div style="margin-top:18px; border-top:1px solid #e5e7eb; padding-top:16px;">
            <button type="button" class="cov-btn primary" id="generateBtn" onclick="generateCover()" style="width:100%; justify-content:center; padding:12px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
                Generate Cover with AI
            </button>
            <p class="cost-note">Estimated cost: <strong>$0.08</strong> per generation (DALL-E 3 standard)</p>
        </div>

        {{-- Status bars --}}
        <div class="gen-status-bar" id="genStatusBar">
            <div class="gen-spinner"></div>
            <span id="genStatusMsg">Generating your course cover…</span>
        </div>
        <div class="gen-error-bar" id="genErrorBar">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12" y2="16"/></svg>
            <span id="genErrorMsg">Generation failed.</span>
        </div>

        @if($course->cover_generated_by_ai && $course->cover_prompt)
        <details style="margin-top:16px;">
            <summary style="font-size:12px; color:#6b7280; cursor:pointer;">Show used AI prompt</summary>
            <pre style="font-size:11px; color:#374151; white-space:pre-wrap; background:#f8fafc; border-radius:6px; padding:10px; margin-top:6px; line-height:1.5;">{{ $course->cover_prompt }}</pre>
        </details>
        @endif
    </div>
</div>

<script>
(function(){
    const GENERATE_URL = "{{ route('elearning.courses.cover.generate', $course) }}";
    const STATUS_URL   = "{{ route('elearning.courses.cover.status',   $course) }}";
    const UPLOAD_URL   = "{{ route('elearning.courses.cover.upload',   $course) }}";
    const DELETE_URL   = "{{ route('elearning.courses.cover.delete',   $course) }}";
    const PROMPT_URL   = "{{ route('elearning.courses.cover.preview-prompt', $course) }}";
    const CSRF         = document.querySelector('meta[name=csrf-token]')?.content ?? "{{ csrf_token() }}";

    let pollTimer = null;

    window.selectStyle = function(el) {
        document.querySelectorAll('#styleGrid .style-card').forEach(c => c.classList.remove('selected'));
        el.classList.add('selected');
    };
    window.selectComplexity = function(el) {
        document.querySelectorAll('#complexityGrid .style-card').forEach(c => c.classList.remove('selected'));
        el.classList.add('selected');
    };

    function getStyle()      { return document.querySelector('#styleGrid .style-card.selected')?.dataset.val ?? 'modern'; }
    function getComplexity() { return document.querySelector('#complexityGrid .style-card.selected')?.dataset.val ?? 'standard'; }

    window.buildPrompt = function() {
        fetch(PROMPT_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify({ style: getStyle(), complexity: getComplexity() }),
        })
        .then(r => r.json())
        .then(d => { document.getElementById('coverPrompt').value = d.prompt ?? ''; })
        .catch(() => alert('Could not build prompt.'));
    };

    window.generateCover = function() {
        const prompt = document.getElementById('coverPrompt').value.trim();
        setGenerating(true);

        fetch(GENERATE_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify({ style: getStyle(), complexity: getComplexity(), prompt: prompt || null }),
        })
        .then(r => r.json())
        .then(d => {
            if (d.status === 'queued') {
                document.getElementById('genStatusMsg').textContent = 'Queued — starting generation…';
                startPolling();
            } else {
                setGenerating(false);
                showError(d.message ?? 'Unexpected response');
            }
        })
        .catch(() => { setGenerating(false); showError('Network error — please try again.'); });
    };

    function startPolling() {
        clearInterval(pollTimer);
        let dots = 0;
        pollTimer = setInterval(function() {
            dots = (dots + 1) % 4;
            document.getElementById('genStatusMsg').textContent = 'Generating your course cover' + '.'.repeat(dots + 1);

            fetch(STATUS_URL, { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(d => {
                if (d.status === 'done') {
                    clearInterval(pollTimer);
                    setGenerating(false);
                    updatePreview(d.cover_url, true);
                    if (d.revised_prompt) document.getElementById('coverPrompt').value = d.revised_prompt;
                } else if (d.status === 'error') {
                    clearInterval(pollTimer);
                    setGenerating(false);
                    showError(d.message ?? 'Generation failed');
                }
            });
        }, 3000);
    }

    function setGenerating(on) {
        document.getElementById('generateBtn').disabled = on;
        document.getElementById('genStatusBar').style.display = on ? 'flex' : 'none';
        document.getElementById('genErrorBar').style.display  = 'none';
    }

    function showError(msg) {
        document.getElementById('genErrorMsg').textContent = msg;
        document.getElementById('genErrorBar').style.display = 'flex';
    }

    function updatePreview(url, isAi) {
        const box   = document.getElementById('coverPreviewBox');
        const empty = document.getElementById('coverEmpty');
        let img = document.getElementById('coverImg');
        if (!img) {
            img = document.createElement('img');
            img.id = 'coverImg';
            box.appendChild(img);
        }
        img.src = url + '?t=' + Date.now();
        if (empty) empty.style.display = 'none';

        // Update/add badge
        let badge = box.querySelector('.cover-badge');
        if (!badge) { badge = document.createElement('span'); badge.className = 'cover-badge'; box.appendChild(badge); }
        badge.className = 'cover-badge' + (isAi ? '' : ' manual');
        badge.textContent = isAi ? 'AI Generated' : 'Custom Upload';

        // Show delete button
        const delBtn = document.getElementById('deleteCoverBtn');
        if (!delBtn) {
            const actionsDiv = box.closest('.cover-wrap').querySelector('.cover-actions');
            const btn = document.createElement('button');
            btn.type = 'button'; btn.id = 'deleteCoverBtn';
            btn.className = 'cov-btn danger'; btn.onclick = window.deleteCover;
            btn.innerHTML = 'Remove';
            actionsDiv.appendChild(btn);
        }
    }

    window.uploadCover = function(input) {
        if (!input.files[0]) return;
        const form = new FormData();
        form.append('image', input.files[0]);
        form.append('_token', CSRF);

        setGenerating(true);
        document.getElementById('genStatusMsg').textContent = 'Uploading and processing image…';

        fetch(UPLOAD_URL, { method: 'POST', body: form, headers: { 'Accept': 'application/json' } })
        .then(r => r.json())
        .then(d => {
            setGenerating(false);
            if (d.success) {
                updatePreview(d.cover_url, false);
            } else {
                showError(d.message ?? 'Upload failed');
            }
        })
        .catch(() => { setGenerating(false); showError('Upload failed — please try again.'); });
    };

    window.deleteCover = function() {
        if (!confirm('Remove course cover? This cannot be undone.')) return;

        fetch(DELETE_URL, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        })
        .then(r => r.json())
        .then(d => {
            if (d.success) {
                const img = document.getElementById('coverImg');
                if (img) img.remove();
                const badge = document.querySelector('.cover-badge');
                if (badge) badge.remove();
                const delBtn = document.getElementById('deleteCoverBtn');
                if (delBtn) delBtn.remove();
                const empty = document.getElementById('coverEmpty');
                if (empty) empty.style.display = '';
            }
        });
    };
})();
</script>
