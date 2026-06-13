@extends('layouts.app')
@section('page-title', 'Training Photos — ' . ($schedule->course->name ?? $schedule->training_title))

@section('content')
<style>
.media-section { background:#fff;border:1px solid #e9ecef;border-radius:14px;padding:20px;margin-bottom:20px; }
.media-section-title { font-size:13px;font-weight:800;color:#374151;margin:0 0 14px;text-transform:uppercase;letter-spacing:.5px;display:flex;align-items:center;justify-content:space-between; }
.drop-zone { border:2px dashed #d1d5db;border-radius:12px;padding:36px;text-align:center;cursor:pointer;transition:all .2s;background:#fafafa; }
.drop-zone:hover, .drop-zone.dragover { border-color:#6366f1;background:#f5f3ff; }
.drop-zone input { display:none; }
.photo-grid { display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:12px; }
.photo-card { border:1px solid #e9ecef;border-radius:10px;overflow:hidden;background:#fff;position:relative;transition:box-shadow .15s; }
.photo-card:hover { box-shadow:0 4px 16px rgba(0,0,0,.12); }
.photo-card img { width:100%;height:130px;object-fit:cover;display:block; }
.photo-card-body { padding:10px; }
.photo-card-actions { display:flex;gap:4px;margin-top:8px;flex-wrap:wrap; }
.featured-badge { position:absolute;top:6px;left:6px;background:#f59e0b;color:#fff;font-size:10px;font-weight:800;padding:2px 6px;border-radius:4px; }
.type-chip { display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:20px;background:#f0f4ff;color:#1e3a8a;font-size:11px;font-weight:700;cursor:pointer;border:1.5px solid #dbeafe;transition:all .15s; }
.type-chip:hover, .type-chip.selected { background:#1e3a8a;color:#fff;border-color:#1e3a8a; }
.upload-progress { display:none;background:#f0f4ff;border-radius:8px;padding:12px;margin-top:12px; }
.upload-progress .bar { height:6px;background:#e9ecef;border-radius:4px;overflow:hidden;margin-top:6px; }
.upload-progress .bar-fill { height:100%;background:#6366f1;border-radius:4px;transition:width .3s;width:0%; }
</style>

{{-- Header --}}
<div class="d-flex align-items-center gap-3 mb-4">
    <div style="width:44px;height:44px;border-radius:12px;background:linear-gradient(135deg,#1e3a8a,#6366f1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
    </div>
    <div>
        <h4 class="mb-0 fw-bold">Training Photos</h4>
        <p class="text-muted mb-0 small">{{ $schedule->course->name ?? $schedule->training_title }} · {{ \Carbon\Carbon::parse($schedule->end_date)->format('M Y') }}</p>
    </div>
    <div class="ms-auto d-flex gap-2">
        @if($schedule->newsArticles->count())
        <a href="{{ route('training-news.edit', $schedule->newsArticles->first()->id) }}" class="btn btn-primary btn-sm">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
            Edit Article
        </a>
        @else
        <a href="{{ route('training-news.create', $schedule->id) }}" class="btn btn-primary btn-sm">Generate News</a>
        @endif
        <button class="btn btn-outline-secondary btn-sm" id="genCaptionsBtn" onclick="generateCaptions()">
            🤖 AI Captions
        </button>
        <a href="{{ route('training-news.index') }}" class="btn btn-ghost btn-sm">← Back</a>
    </div>
</div>

<x-flash-message />

{{-- Upload Section --}}
<div class="media-section">
    <div class="media-section-title">Upload Photos</div>

    {{-- Type selector --}}
    <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:16px;" id="typeSelector">
        @foreach(App\Models\TrainingMedia::$types as $val => $label)
        <span class="type-chip {{ $val==='gallery'?'selected':'' }}" onclick="selectType('{{ $val }}',this)">{{ $label }}</span>
        @endforeach
    </div>
    <input type="hidden" id="selectedType" value="gallery">

    <div class="drop-zone" id="dropZone" onclick="document.getElementById('fileInput').click()">
        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="1.5" style="margin:0 auto 10px;display:block;"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
        <p style="margin:0;font-size:14px;font-weight:700;color:#374151;">Drag & drop photos here</p>
        <p style="margin:4px 0 0;font-size:12px;color:#9ca3af;">or click to browse · JPG, PNG, WebP · Max 8MB each · Up to 20 at once</p>
        <input type="file" id="fileInput" multiple accept="image/jpg,image/jpeg,image/png,image/webp" onchange="handleFiles(this.files)">
    </div>

    <div class="upload-progress" id="uploadProgress">
        <div style="font-size:13px;font-weight:600;" id="uploadStatus">Uploading…</div>
        <div class="bar"><div class="bar-fill" id="uploadBar"></div></div>
    </div>
</div>

{{-- Media Gallery by Type --}}
@php $typeLabels = App\Models\TrainingMedia::$types; @endphp

@if($media->isEmpty())
<div style="text-align:center;padding:48px;background:#fff;border:1px solid #e9ecef;border-radius:14px;">
    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#d1d5db" stroke-width="1.5" style="margin:0 auto 12px;display:block;"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
    <p style="font-size:14px;font-weight:700;color:#6b7280;margin:0;">No photos uploaded yet</p>
    <p style="font-size:13px;color:#9ca3af;margin:4px 0 0;">Upload photos above to get started</p>
</div>
@else

@foreach($typeLabels as $type => $typeLabel)
@if(isset($media[$type]) && $media[$type]->count())
<div class="media-section">
    <div class="media-section-title">
        {{ $typeLabel }} ({{ $media[$type]->count() }})
    </div>
    <div class="photo-grid" id="grid-{{ $type }}">
        @foreach($media[$type] as $item)
        <div class="photo-card" id="card-{{ $item->id }}">
            @if($item->is_featured)<span class="featured-badge">★ Featured</span>@endif
            <img src="{{ $item->url }}" alt="{{ $item->alt_text ?? $typeLabel }}" loading="lazy">
            <div class="photo-card-body">
                <div style="font-size:11px;color:#6b7280;margin-bottom:6px;">{{ $item->file_size_human }}</div>
                <input type="text"
                    placeholder="Caption…"
                    value="{{ $item->caption }}"
                    style="width:100%;font-size:11px;padding:5px 8px;border:1px solid #e5e7eb;border-radius:6px;box-sizing:border-box;margin-bottom:4px;"
                    onblur="updateCaption({{ $item->id }}, this.value)"
                />
                <input type="text"
                    placeholder="Alt text…"
                    value="{{ $item->alt_text }}"
                    style="width:100%;font-size:11px;padding:5px 8px;border:1px solid #e5e7eb;border-radius:6px;box-sizing:border-box;"
                    onblur="updateAlt({{ $item->id }}, this.value)"
                />
                <div class="photo-card-actions">
                    <button onclick="setFeatured({{ $item->id }}, '{{ $type }}')" class="btn btn-xs" style="background:#fef3c7;color:#92400e;font-size:10px;">
                        {{ $item->is_featured ? '★ Featured' : '☆ Feature' }}
                    </button>
                    <button onclick="deleteMedia({{ $item->id }})" class="btn btn-del btn-xs" style="font-size:10px;">Delete</button>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif
@endforeach

@endif

<script>
var UPLOAD_URL          = "{{ route('training-media.store', $schedule->id) }}";
var GEN_CAPTIONS_URL    = "{{ route('training-media.generate-captions', $schedule->id) }}";
var UPDATE_BASE         = "{{ url('/admin/training-media/item') }}";
var CSRF                = "{{ csrf_token() }}";

function selectType(val, el) {
    document.querySelectorAll('.type-chip').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
    document.getElementById('selectedType').value = val;
}

// Drag & Drop
var dz = document.getElementById('dropZone');
dz.addEventListener('dragover', e => { e.preventDefault(); dz.classList.add('dragover'); });
dz.addEventListener('dragleave', () => dz.classList.remove('dragover'));
dz.addEventListener('drop', e => { e.preventDefault(); dz.classList.remove('dragover'); handleFiles(e.dataTransfer.files); });

function handleFiles(files) {
    if (!files.length) return;
    var type      = document.getElementById('selectedType').value;
    var formData  = new FormData();
    var valid     = [];
    for (var i = 0; i < files.length; i++) {
        if (files[i].size > 8 * 1024 * 1024) { alert(files[i].name + ' exceeds 8MB limit.'); continue; }
        formData.append('files[]', files[i]);
        valid.push(files[i].name);
    }
    if (!valid.length) return;
    formData.append('media_type', type);
    formData.append('_token', CSRF);

    var prog = document.getElementById('uploadProgress');
    var bar  = document.getElementById('uploadBar');
    var stat = document.getElementById('uploadStatus');
    prog.style.display = '';
    stat.textContent   = 'Uploading ' + valid.length + ' photo(s)…';
    bar.style.width    = '0%';

    var xhr = new XMLHttpRequest();
    xhr.open('POST', UPLOAD_URL);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.upload.onprogress = function(e) {
        if (e.lengthComputable) bar.style.width = Math.round(e.loaded / e.total * 90) + '%';
    };
    xhr.onload = function() {
        bar.style.width = '100%';
        stat.textContent = 'Upload complete! Refreshing…';
        setTimeout(() => location.reload(), 800);
    };
    xhr.onerror = function() {
        stat.textContent = 'Upload failed. Please try again.';
        bar.style.background = '#ef4444';
    };
    xhr.send(formData);
}

function updateCaption(id, caption) {
    fetch(UPDATE_BASE + '/' + id, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ caption })
    });
}

function updateAlt(id, alt_text) {
    fetch(UPDATE_BASE + '/' + id, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ alt_text })
    });
}

function setFeatured(id, type) {
    fetch(UPDATE_BASE + '/' + id + '/featured', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
    }).then(() => location.reload());
}

function deleteMedia(id) {
    if (!confirm('Delete this photo?')) return;
    fetch(UPDATE_BASE + '/' + id, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
    }).then(() => {
        var card = document.getElementById('card-' + id);
        if (card) card.remove();
    });
}

function generateCaptions() {
    var btn = document.getElementById('genCaptionsBtn');
    btn.disabled = true;
    btn.textContent = '⏳ Generating…';
    fetch(GEN_CAPTIONS_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
    })
    .then(r => r.json())
    .then(res => {
        btn.disabled    = false;
        btn.textContent = '🤖 AI Captions';
        if (res.success) {
            alert('AI generated captions for ' + res.updated + ' photo(s). Refreshing…');
            location.reload();
        } else {
            alert(res.error || 'No uncaptioned photos found.');
        }
    })
    .catch(() => { btn.disabled = false; btn.textContent = '🤖 AI Captions'; });
}
</script>
@endsection
