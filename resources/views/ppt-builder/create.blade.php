@extends('layouts.app')
@section('page-title', 'Upload Presentation — PPT eLearning Builder')

@section('content')
<div class="page-wrap" style="max-width:720px;">
    <x-page-header title="Upload Presentation" desc="Upload your PowerPoint file to extract slides and begin building your eLearning course." />

    <x-flash-message />

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('ppt-builder.store') }}" enctype="multipart/form-data" id="upload-form">
                @csrf

                <div style="margin-bottom:20px;">
                    <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Course Title <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}"
                           class="filter-input" style="width:100%;font-size:14px;padding:10px 14px;"
                           placeholder="e.g. ISO 45001 Occupational Health & Safety Awareness"
                           required>
                    @error('title')<div style="color:#ef4444;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                </div>

                <div style="margin-bottom:20px;">
                    <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Description <span style="color:#9ca3af;font-weight:400;">(optional)</span></label>
                    <textarea name="description" rows="3"
                              class="filter-input" style="width:100%;resize:vertical;font-size:14px;padding:10px 14px;"
                              placeholder="Brief description of this training course...">{{ old('description') }}</textarea>
                    @error('description')<div style="color:#ef4444;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                </div>

                <div style="margin-bottom:24px;" x-data="{ dragging: false, file: null }">
                    <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">PowerPoint File <span style="color:#ef4444;">*</span></label>
                    <div @dragover.prevent="dragging=true" @dragleave.prevent="dragging=false"
                         @drop.prevent="dragging=false; file=$event.dataTransfer.files[0]; $refs.fileInput.files=$event.dataTransfer.files"
                         :class="dragging ? 'drag-over' : ''"
                         style="border:2px dashed #d1d5db;border-radius:10px;padding:32px;text-align:center;cursor:pointer;transition:border-color .15s;background:#f9fafb;"
                         :style="dragging ? 'border-color:#1e3a8a;background:#eff6ff;' : ''">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5" style="margin:0 auto 12px;display:block;"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
                        <div style="font-size:14px;font-weight:600;color:#374151;" x-text="file ? file.name : 'Drop your PPTX file here'"></div>
                        <div style="font-size:12px;color:#6b7280;margin-top:4px;" x-show="!file">or click to browse (PPTX / PPT, max 50 MB)</div>
                        <input x-ref="fileInput" type="file" name="file" accept=".pptx,.ppt"
                               @change="file=$event.target.files[0]"
                               style="display:none" required>
                        <button type="button" @click="$refs.fileInput.click()"
                                style="margin-top:12px;padding:7px 18px;border:1px solid #d1d5db;border-radius:7px;background:white;font-size:13px;cursor:pointer;color:#374151;">
                            Browse Files
                        </button>
                    </div>
                    @error('file')<div style="color:#ef4444;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                </div>

                <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;padding:14px 16px;margin-bottom:24px;">
                    <div style="font-size:13px;font-weight:600;color:#1e3a8a;margin-bottom:8px;">What happens after upload?</div>
                    <ul style="font-size:12px;color:#374151;line-height:1.7;padding-left:18px;margin:0;">
                        <li>Slides are extracted automatically — titles, content, speaker notes, and images</li>
                        <li>You can then organise slides into modules</li>
                        <li>Add discussion points to guide AI explanations for each slide</li>
                        <li>Generate AI narration scripts and audio one slide at a time</li>
                    </ul>
                </div>

                <div style="display:flex;gap:12px;">
                    <button type="submit" class="btn btn-primary" id="submit-btn">
                        Upload & Extract Slides
                    </button>
                    <a href="{{ route('ppt-builder.index') }}" class="btn btn-ghost">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('upload-form').addEventListener('submit', function() {
    const btn = document.getElementById('submit-btn');
    btn.textContent = 'Extracting slides…';
    btn.disabled = true;
});
</script>
@endsection
