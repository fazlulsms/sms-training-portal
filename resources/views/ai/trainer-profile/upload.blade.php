@extends('layouts.app')
@section('page-title', 'AI Trainer Profile Generator')
@section('content')
<style>
.tpg-wrap    { max-width:780px; margin:auto; }
.tpg-card    { background:#fff; border-radius:14px; box-shadow:0 4px 20px rgba(0,0,0,.07); overflow:hidden; }
.tpg-hdr     { background:linear-gradient(135deg,#0f1e45,#1e3a8a); padding:24px 28px; }
.tpg-hdr h1  { font-size:20px; font-weight:800; color:#fff; margin:0 0 4px; }
.tpg-hdr p   { font-size:13px; color:#93c5fd; margin:0; }
.tpg-body    { padding:28px; }
.fg          { margin-bottom:20px; }
.fg label    { display:block; font-weight:700; font-size:13.5px; color:#374151; margin-bottom:7px; }
.fg input, .fg select, .fg textarea {
    width:100%; padding:10px 13px; border:1.5px solid #d1d5db; border-radius:8px;
    font-size:14px; font-family:inherit; outline:none; box-sizing:border-box;
    transition:border-color .15s;
}
.fg input:focus, .fg select:focus, .fg textarea:focus { border-color:#1e3a8a; }
.fg .hint    { font-size:12px; color:#6b7280; margin-top:5px; }
.frow        { display:grid; grid-template-columns:1fr 1fr; gap:18px; }
.drop-zone   {
    border:2px dashed #d1d5db; border-radius:10px; padding:32px 20px;
    text-align:center; cursor:pointer; transition:all .2s; background:#fafafa;
}
.drop-zone:hover, .drop-zone.drag-over { border-color:#1e3a8a; background:#eff6ff; }
.drop-zone .dz-icon { font-size:36px; margin-bottom:8px; display:block; }
.drop-zone .dz-main { font-weight:700; font-size:14px; color:#374151; }
.drop-zone .dz-sub  { font-size:12.5px; color:#6b7280; margin-top:4px; }
.file-list   { margin-top:12px; display:flex; flex-direction:column; gap:6px; }
.file-item   {
    display:flex; align-items:center; gap:10px; padding:8px 12px;
    background:#f0f9ff; border:1px solid #bae6fd; border-radius:7px; font-size:13px;
}
.file-item .fi-name { flex:1; font-weight:600; color:#0369a1; }
.file-item .fi-size { color:#6b7280; font-size:12px; }
.file-item .fi-rm   { background:none; border:none; color:#ef4444; cursor:pointer; font-size:15px; padding:0 4px; }
.sec-title   { font-size:14px; font-weight:800; color:#1e3a8a; border-bottom:2px solid #dbeafe;
               padding-bottom:8px; margin:24px 0 16px; }
.info-box    { background:#eff6ff; border:1px solid #bfdbfe; border-radius:9px; padding:14px 16px;
               font-size:13px; color:#1e40af; margin-bottom:20px; }
.info-box ul { margin:6px 0 0 18px; padding:0; }
.info-box li { margin-bottom:3px; }
.btn-gen     {
    display:inline-flex; align-items:center; gap:8px;
    background:linear-gradient(135deg,#1e3a8a,#2563eb); color:#fff;
    border:none; border-radius:9px; padding:13px 28px; font-size:15px;
    font-weight:700; cursor:pointer; box-shadow:0 4px 14px rgba(30,58,138,.35);
    transition:opacity .2s;
}
.btn-gen:hover { opacity:.9; }
.btn-gen:disabled { opacity:.55; cursor:not-allowed; }
.spinner { display:none; width:16px; height:16px; border:2.5px solid rgba(255,255,255,.4);
           border-top-color:#fff; border-radius:50%; animation:spin .7s linear infinite; }
@keyframes spin { to { transform:rotate(360deg); } }
</style>

<div class="tpg-wrap">
    <div class="tpg-card">
        <div class="tpg-hdr">
            <h1>✨ AI Trainer Profile Generator</h1>
            <p>Upload CV, resume, LinkedIn export, or portfolio — AI builds a professional trainer profile instantly.</p>
        </div>
        <div class="tpg-body">

            @if(session('error'))
            <div style="background:#fee2e2; border:1px solid #fca5a5; border-radius:8px; padding:13px 16px; margin-bottom:20px; color:#b91c1c; font-size:14px;">
                ⚠ {{ session('error') }}
            </div>
            @endif

            <div class="info-box">
                <strong>What AI will generate:</strong>
                <ul>
                    <li>Professional bio (150–250 words, third person)</li>
                    <li>Expertise areas, certifications, professional highlights</li>
                    <li>Industries served, countries covered, training specialisations</li>
                    <li>SEO title, description, and keywords</li>
                </ul>
                <div style="margin-top:8px; font-size:12px; color:#2563eb;">
                    ⚠ AI only extracts information present in the documents — it will not invent qualifications or clients.
                </div>
            </div>

            <form method="POST" action="{{ route('ai.trainer-profile.generate') }}" enctype="multipart/form-data" id="tpgForm">
                @csrf

                <div class="sec-title">1. Select or Create Trainer</div>

                <div class="frow" style="margin-bottom:20px;">
                    <div class="fg" style="margin-bottom:0;">
                        <label>Existing Trainer (optional)</label>
                        <select name="trainer_id" id="trainerSelect" onchange="toggleNewName()">
                            <option value="">— Create new trainer —</option>
                            @foreach($trainers as $t)
                            <option value="{{ $t->id }}">{{ $t->name }}{{ $t->designation ? ' — ' . $t->designation : '' }}{{ $t->ai_generated ? ' ✨' : '' }}</option>
                            @endforeach
                        </select>
                        <div class="hint">Selecting an existing trainer will update their profile.</div>
                    </div>
                    <div class="fg" style="margin-bottom:0;" id="newNameWrap">
                        <label>New Trainer Name <span style="color:red">*</span></label>
                        <input type="text" name="trainer_name" id="trainerName" placeholder="e.g. Mr. John Smith">
                        <div class="hint">Required when creating a new trainer.</div>
                    </div>
                </div>

                <div class="sec-title">2. Upload Documents</div>

                <div class="fg">
                    <div class="drop-zone" id="dropZone" onclick="document.getElementById('fileInput').click()">
                        <span class="dz-icon">📄</span>
                        <div class="dz-main">Click to browse or drag &amp; drop files here</div>
                        <div class="dz-sub">PDF, DOCX, DOC, TXT &nbsp;·&nbsp; Max 10 MB per file &nbsp;·&nbsp; Up to 5 files</div>
                    </div>
                    <input type="file" id="fileInput" name="documents[]" multiple
                           accept=".pdf,.docx,.doc,.txt" style="display:none" onchange="handleFiles(this.files)">
                    <div class="file-list" id="fileList"></div>
                </div>

                <div class="sec-title">3. Additional Context <span style="font-weight:400; color:#6b7280;">(optional)</span></div>

                <div class="fg">
                    <label>Notes for AI</label>
                    <textarea name="extra_notes" rows="3" placeholder="e.g. Focus on ISO auditing experience. This trainer specialises in the automotive sector."
                              style="resize:vertical;"></textarea>
                    <div class="hint">Any context that helps AI produce a better profile. Max 2000 characters.</div>
                </div>

                <div style="display:flex; align-items:center; gap:14px; padding-top:8px; border-top:1px solid #f0f2f5; margin-top:8px;">
                    <button type="submit" class="btn-gen" id="genBtn" disabled>
                        <span class="spinner" id="genSpinner"></span>
                        <svg id="genIcon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/>
                        </svg>
                        Generate Profile with AI
                    </button>
                    <span id="genStatus" style="font-size:13px; color:#6b7280;"></span>
                    <a href="/admin/trainers" style="margin-left:auto; font-size:13.5px; color:#6b7280; text-decoration:none; font-weight:600;">← Back to Trainers</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// ── File management ──────────────────────────────────────────────
let selectedFiles = new DataTransfer();

function handleFiles(fileList) {
    Array.from(fileList).forEach(f => {
        if (selectedFiles.files.length >= 5) return;
        // Avoid duplicates
        let exists = Array.from(selectedFiles.files).some(ex => ex.name === f.name && ex.size === f.size);
        if (! exists) selectedFiles.items.add(f);
    });
    syncInput();
    renderList();
}

function removeFile(idx) {
    let dt = new DataTransfer();
    Array.from(selectedFiles.files).forEach((f, i) => { if (i !== idx) dt.items.add(f); });
    selectedFiles = dt;
    syncInput();
    renderList();
}

function syncInput() {
    let inp = document.getElementById('fileInput');
    inp.files = selectedFiles.files;
}

function renderList() {
    let ul = document.getElementById('fileList');
    ul.innerHTML = '';
    Array.from(selectedFiles.files).forEach((f, i) => {
        let size = f.size > 1048576 ? (f.size/1048576).toFixed(1)+' MB' : Math.round(f.size/1024)+' KB';
        ul.innerHTML += `<div class="file-item">
            <span style="font-size:18px;">${fileIcon(f.name)}</span>
            <span class="fi-name">${f.name}</span>
            <span class="fi-size">${size}</span>
            <button class="fi-rm" type="button" onclick="removeFile(${i})" title="Remove">×</button>
        </div>`;
    });
    updateBtn();
}

function fileIcon(name) {
    let ext = name.split('.').pop().toLowerCase();
    return {pdf:'📕', docx:'📘', doc:'📗', txt:'📄'}[ext] || '📎';
}

function updateBtn() {
    let btn    = document.getElementById('genBtn');
    let status = document.getElementById('genStatus');
    let count  = selectedFiles.files.length;
    btn.disabled = count === 0;
    status.textContent = count > 0 ? count + ' file' + (count > 1 ? 's' : '') + ' selected' : '';
}

// ── Drag & drop ──────────────────────────────────────────────────
let dz = document.getElementById('dropZone');
dz.addEventListener('dragover',  e => { e.preventDefault(); dz.classList.add('drag-over'); });
dz.addEventListener('dragleave', () => dz.classList.remove('drag-over'));
dz.addEventListener('drop', e => {
    e.preventDefault();
    dz.classList.remove('drag-over');
    handleFiles(e.dataTransfer.files);
});

// ── Trainer name toggle ──────────────────────────────────────────
function toggleNewName() {
    let sel  = document.getElementById('trainerSelect').value;
    let wrap = document.getElementById('newNameWrap');
    let inp  = document.getElementById('trainerName');
    if (sel) {
        wrap.style.opacity = '.45';
        inp.required = false;
    } else {
        wrap.style.opacity = '1';
        inp.required = true;
    }
}
toggleNewName();

// ── Submission spinner ───────────────────────────────────────────
document.getElementById('tpgForm').addEventListener('submit', function() {
    let btn = document.getElementById('genBtn');
    btn.disabled = true;
    document.getElementById('genSpinner').style.display = 'inline-block';
    document.getElementById('genIcon').style.display    = 'none';
    document.getElementById('genStatus').textContent    = 'Extracting text & generating profile… this may take 20–40 seconds.';
});
</script>
@endsection
