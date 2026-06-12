@extends('layouts.app')
@section('page-title', 'AI Trainer Profile — Review & Save')
@section('content')
<style>
.pvw-grid  { display:grid; grid-template-columns:1fr 340px; gap:22px; max-width:1140px; margin:auto; align-items:start; }
.pvw-card  { background:#fff; border-radius:14px; box-shadow:0 4px 18px rgba(0,0,0,.07); overflow:hidden; }
.pvw-hdr   { background:linear-gradient(135deg,#0f1e45,#1e3a8a); padding:18px 24px; }
.pvw-hdr h2{ font-size:16px; font-weight:800; color:#fff; margin:0 0 2px; }
.pvw-hdr p { font-size:12px; color:#93c5fd; margin:0; }
.pvw-body  { padding:24px; }
.fg        { margin-bottom:18px; }
.fg label  { display:block; font-weight:700; font-size:13px; color:#374151; margin-bottom:6px; }
.fg input, .fg select, .fg textarea {
    width:100%; padding:9px 12px; border:1.5px solid #d1d5db; border-radius:8px;
    font-size:13.5px; font-family:inherit; outline:none; box-sizing:border-box;
    transition:border-color .15s;
}
.fg input:focus, .fg textarea:focus { border-color:#1e3a8a; }
.fg .hint  { font-size:11.5px; color:#6b7280; margin-top:4px; }
.frow      { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
.sec-title { font-size:13px; font-weight:800; color:#1e3a8a; border-bottom:2px solid #dbeafe;
             padding-bottom:6px; margin:20px 0 14px; }
/* Right panel */
.info-card { background:#fff; border-radius:12px; box-shadow:0 4px 18px rgba(0,0,0,.07); overflow:hidden; }
.info-hdr  { background:#f8fafc; border-bottom:1px solid #e5e7eb; padding:14px 18px; }
.info-hdr h3{ font-size:13.5px; font-weight:800; color:#1e3a8a; margin:0; }
.info-body { padding:18px; }
.stat-row  { display:flex; justify-content:space-between; padding:7px 0;
             border-bottom:1px solid #f3f4f6; font-size:13px; }
.stat-row:last-child { border-bottom:none; }
.stat-lbl  { color:#6b7280; }
.stat-val  { font-weight:700; color:#111827; }
.conf-note { background:#fffbeb; border:1px solid #fde68a; border-radius:8px;
             padding:12px 14px; font-size:12.5px; color:#92400e; margin-top:14px; line-height:1.5; }
.file-chip { display:inline-flex; align-items:center; gap:5px; background:#eff6ff;
             border:1px solid #bfdbfe; border-radius:6px; padding:4px 9px;
             font-size:12px; font-weight:600; color:#1d4ed8; margin:3px 2px; }
.ai-badge  { display:inline-flex; align-items:center; gap:5px; padding:3px 9px;
             background:linear-gradient(90deg,#1e3a8a,#2563eb); color:#fff;
             border-radius:20px; font-size:11px; font-weight:700; }
/* Buttons */
.btn-save  { background:#16a34a; color:#fff; border:none; border-radius:8px; padding:12px 24px;
             font-size:14px; font-weight:700; cursor:pointer; }
.btn-back  { background:#6b7280; color:#fff; border:none; border-radius:8px; padding:12px 20px;
             font-size:14px; font-weight:700; cursor:pointer; }
.btn-cancel{ background:#f1f5f9; color:#374151; border:none; border-radius:8px; padding:12px 18px;
             font-size:14px; font-weight:700; cursor:pointer; text-decoration:none; display:inline-block; }
@media(max-width:900px) { .pvw-grid { grid-template-columns:1fr; } }
</style>

@php
    $ai       = $aiOutput;
    $usage    = $aiUsage;
    $draft    = $draft;

    function aiVal($arr, $key, $default = '') {
        return $arr[$key] ?? $default;
    }
    function aiArr($arr, $key) {
        $v = $arr[$key] ?? [];
        return is_array($v) ? implode("\n", array_filter($v)) : (string)$v;
    }
@endphp

<form method="POST" action="{{ route('ai.trainer-profile.save') }}" id="pvwForm">
@csrf

<div class="pvw-grid">

{{-- ── LEFT: Editable fields ────────────────────────────────── --}}
<div>
    <div class="pvw-card">
        <div class="pvw-hdr">
            <h2>✨ Review & Edit AI-Generated Profile</h2>
            <p>Review all fields carefully before saving. You can edit any section below.</p>
        </div>
        <div class="pvw-body">

            <div style="background:#f0fdf4; border:1px solid #86efac; border-radius:8px; padding:12px 16px; margin-bottom:20px; display:flex; align-items:center; gap:10px;">
                <span style="font-size:20px;">✅</span>
                <div>
                    <div style="font-weight:700; font-size:13.5px; color:#166534;">Profile generated for: <strong>{{ $draft['trainer_name'] }}</strong></div>
                    <div style="font-size:12px; color:#16a34a; margin-top:2px;">
                        {{ count($draft['files_analyzed'] ?? []) }} document(s) analysed &nbsp;·&nbsp;
                        Generated in {{ $draft['duration'] ?? '—' }}s
                        &nbsp;·&nbsp; <span class="ai-badge">✨ AI Generated</span>
                    </div>
                </div>
            </div>

            {{-- ── Identity ──────────────────────────────────────── --}}
            <div class="sec-title">Identity</div>
            <div class="frow">
                <div class="fg">
                    <label>Full Name <span style="color:red">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $draft['trainer_name']) }}" required>
                </div>
                <div class="fg">
                    <label>Designation / Title</label>
                    <input type="text" name="designation" value="{{ old('designation', aiVal($ai, 'designation')) }}">
                </div>
            </div>
            <div class="frow">
                <div class="fg">
                    <label>Organization / Company</label>
                    <input type="text" name="organization" value="{{ old('organization', aiVal($ai, 'organization')) }}">
                </div>
                <div class="fg">
                    <label>Academic Qualification</label>
                    <input type="text" name="qualification" value="{{ old('qualification', aiVal($ai, 'qualification')) }}"
                           placeholder="e.g. MBA, MSc Engineering">
                </div>
            </div>
            <div class="frow">
                <div class="fg">
                    <label>Years of Experience</label>
                    <input type="text" name="experience" value="{{ old('experience', aiVal($ai, 'years_experience')) }}"
                           placeholder="e.g. 15+ years">
                </div>
                <div class="fg">
                    <label>Languages Spoken</label>
                    <input type="text" name="languages_spoken" value="{{ old('languages_spoken', aiArr($ai, 'languages_spoken')) }}"
                           placeholder="e.g. English, Bangla">
                </div>
            </div>

            {{-- ── Bio ─────────────────────────────────────────── --}}
            <div class="sec-title">Professional Bio</div>
            <div class="fg">
                <label>Short Bio (Public) <span style="color:red">*</span></label>
                <textarea name="short_bio" rows="7" id="bioField"
                          oninput="updateBioCount()">{{ old('short_bio', aiVal($ai, 'professional_bio')) }}</textarea>
                <div class="hint">
                    <span id="bioCount">0</span> words &nbsp;·&nbsp; Target: 150–250 words &nbsp;·&nbsp; Third person recommended
                </div>
            </div>

            {{-- ── Expertise & Certifications ──────────────────── --}}
            <div class="sec-title">Expertise & Certifications</div>
            <div class="frow">
                <div class="fg">
                    <label>Expertise Areas</label>
                    <textarea name="expertise_areas" rows="5">{{ old('expertise_areas', aiArr($ai, 'expertise_areas')) }}</textarea>
                    <div class="hint">One per line</div>
                </div>
                <div class="fg">
                    <label>Certifications</label>
                    <textarea name="certifications" rows="5">{{ old('certifications', aiArr($ai, 'certifications')) }}</textarea>
                    <div class="hint">One per line, e.g. IRCA Lead Auditor (ISO 9001)</div>
                </div>
            </div>

            {{-- ── Highlights & Industries ──────────────────────── --}}
            <div class="sec-title">Highlights & Industry Coverage</div>
            <div class="fg">
                <label>Professional Highlights</label>
                <textarea name="professional_highlights" rows="4">{{ old('professional_highlights', aiArr($ai, 'professional_highlights')) }}</textarea>
                <div class="hint">Key achievements, credentials, or notable clients — one per line</div>
            </div>
            <div class="frow">
                <div class="fg">
                    <label>Industries Served</label>
                    <textarea name="industries_served" rows="4">{{ old('industries_served', aiArr($ai, 'industries_served')) }}</textarea>
                    <div class="hint">One per line</div>
                </div>
                <div class="fg">
                    <label>Countries Covered</label>
                    <textarea name="countries_covered" rows="4">{{ old('countries_covered', aiArr($ai, 'countries_covered')) }}</textarea>
                    <div class="hint">One per line</div>
                </div>
            </div>

            {{-- ── Specialisations ─────────────────────────────── --}}
            <div class="sec-title">Training Specialisations</div>
            <div class="frow">
                <div class="fg">
                    <label>Training Topics</label>
                    <textarea name="training_specializations" rows="5">{{ old('training_specializations', aiArr($ai, 'training_specializations')) }}</textarea>
                    <div class="hint">One per line</div>
                </div>
                <div class="fg">
                    <label>Audit / Assessment Topics</label>
                    <textarea name="audit_specializations" rows="5">{{ old('audit_specializations', aiArr($ai, 'audit_specializations')) }}</textarea>
                    <div class="hint">One per line. Leave blank if not applicable.</div>
                </div>
            </div>

            {{-- ── SEO ──────────────────────────────────────────── --}}
            <div class="sec-title">SEO Meta Tags</div>
            <div class="fg">
                <label>SEO Title</label>
                <input type="text" name="seo_title" value="{{ old('seo_title', aiVal($ai, 'seo_title')) }}"
                       placeholder="60–70 characters">
            </div>
            <div class="fg">
                <label>SEO Description</label>
                <textarea name="seo_description" rows="2">{{ old('seo_description', aiVal($ai, 'seo_description')) }}</textarea>
                <div class="hint">150–160 characters recommended</div>
            </div>
            <div class="fg">
                <label>SEO Keywords</label>
                <input type="text" name="seo_keywords" value="{{ old('seo_keywords', aiVal($ai, 'seo_keywords')) }}"
                       placeholder="comma-separated keywords">
            </div>

            {{-- ── Actions ─────────────────────────────────────── --}}
            <div style="display:flex; gap:10px; padding-top:20px; border-top:1px solid #e5e7eb; margin-top:8px; flex-wrap:wrap;">
                <button type="submit" name="_action" value="save" class="btn-save">
                    ✅ Save Trainer Profile
                </button>
                <a href="{{ route('ai.trainer-profile.index') }}"
                   style="display:inline-flex; align-items:center; gap:6px; padding:12px 18px; background:#eff6ff;
                          color:#1d4ed8; border-radius:8px; text-decoration:none; font-weight:700; font-size:14px; border:1.5px solid #bfdbfe;">
                    ← Re-upload Documents
                </a>
                <form method="POST" action="{{ route('ai.trainer-profile.cancel') }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn-cancel"
                            onclick="return confirm('Discard this AI draft and go back to Trainers?')">
                        Discard & Cancel
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ── RIGHT: AI Analysis Summary ───────────────────────────── --}}
<div>
    <div class="info-card">
        <div class="info-hdr">
            <h3>AI Analysis Summary</h3>
        </div>
        <div class="info-body">

            @if(! empty($draft['files_analyzed']))
            <div style="margin-bottom:14px;">
                <div style="font-size:12px; font-weight:700; color:#6b7280; margin-bottom:6px;">DOCUMENTS ANALYSED</div>
                @foreach($draft['files_analyzed'] as $f)
                <div class="file-chip">
                    {{ $f['type'] === 'PDF' ? '📕' : ($f['type'] === 'DOCX' ? '📘' : '📄') }}
                    {{ $f['name'] }}
                    <span style="color:#6b7280;">({{ number_format($f['chars']) }} chars)</span>
                </div>
                @endforeach
            </div>
            @endif

            <div class="stat-row">
                <span class="stat-lbl">Trainer</span>
                <span class="stat-val">{{ $draft['trainer_name'] }}</span>
            </div>
            @if($trainer)
            <div class="stat-row">
                <span class="stat-lbl">Action</span>
                <span class="stat-val" style="color:#d97706;">Update existing</span>
            </div>
            @else
            <div class="stat-row">
                <span class="stat-lbl">Action</span>
                <span class="stat-val" style="color:#16a34a;">Create new</span>
            </div>
            @endif
            <div class="stat-row">
                <span class="stat-lbl">Files processed</span>
                <span class="stat-val">{{ count($draft['files_analyzed'] ?? []) }}</span>
            </div>
            <div class="stat-row">
                <span class="stat-lbl">Generation time</span>
                <span class="stat-val">{{ $draft['duration'] ?? '—' }}s</span>
            </div>
            @if(! empty($usage['total_tokens']))
            <div class="stat-row">
                <span class="stat-lbl">Tokens used</span>
                <span class="stat-val">{{ number_format($usage['total_tokens']) }}</span>
            </div>
            @endif

            @if(! empty($ai['confidence_notes']))
            <div class="conf-note">
                <strong>AI Confidence Note:</strong><br>
                {{ $ai['confidence_notes'] }}
            </div>
            @endif

        </div>
    </div>

    {{-- Quick stats from AI output ──────────────────────────── --}}
    <div class="info-card" style="margin-top:16px;">
        <div class="info-hdr">
            <h3>Generated Fields</h3>
        </div>
        <div class="info-body">
            @php
                $counts = [
                    'Expertise areas'       => count((array)($ai['expertise_areas'] ?? [])),
                    'Certifications'        => count((array)($ai['certifications'] ?? [])),
                    'Highlights'            => count((array)($ai['professional_highlights'] ?? [])),
                    'Industries'            => count((array)($ai['industries_served'] ?? [])),
                    'Training topics'       => count((array)($ai['training_specializations'] ?? [])),
                    'Audit topics'          => count((array)($ai['audit_specializations'] ?? [])),
                    'Countries'             => count((array)($ai['countries_covered'] ?? [])),
                ];
            @endphp
            @foreach($counts as $lbl => $cnt)
            <div class="stat-row">
                <span class="stat-lbl">{{ $lbl }}</span>
                <span class="stat-val">{{ $cnt > 0 ? $cnt : '—' }}</span>
            </div>
            @endforeach

            <div style="margin-top:12px; padding:10px 12px; background:#f0fdf4; border-radius:7px; font-size:12px; color:#166534; line-height:1.5;">
                All fields are fully editable above. Review carefully before saving — especially certifications and qualifications.
            </div>
        </div>
    </div>
</div>

</div>{{-- pvw-grid --}}
</form>

<script>
function updateBioCount() {
    let text  = document.getElementById('bioField').value.trim();
    let words = text ? text.split(/\s+/).filter(Boolean).length : 0;
    let el    = document.getElementById('bioCount');
    el.textContent = words;
    el.style.color = (words >= 150 && words <= 250) ? '#16a34a' : (words > 0 ? '#d97706' : '#6b7280');
}
updateBioCount();
</script>
@endsection
