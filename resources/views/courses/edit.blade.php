@extends('layouts.app')
@section('content')
<style>
.tab-nav { display:flex; gap:0; border-bottom:2px solid #e5e7eb; margin-bottom:28px; }
.tab-btn {
    padding:11px 24px; cursor:pointer; background:none; border:none;
    font-size:14px; font-weight:600; color:#6b7280;
    border-bottom:3px solid transparent; margin-bottom:-2px; transition:all .15s;
}
.tab-btn.active { color:#1e3a8a; border-bottom-color:#1e3a8a; }
.tab-panel { display:none; } .tab-panel.active { display:block; }
.fg { margin-bottom:18px; }
.fg label { display:block; font-weight:600; font-size:13.5px; color:#374151; margin-bottom:6px; }
.fg input,.fg select,.fg textarea {
    width:100%; padding:10px 13px; border:1.5px solid #d1d5db; border-radius:8px;
    font-size:14px; font-family:inherit; outline:none; box-sizing:border-box;
}
.fg input:focus,.fg select:focus,.fg textarea:focus { border-color:#1e3a8a; }
.frow { display:grid; grid-template-columns:1fr 1fr; gap:18px; }
.toggle-row { display:flex; align-items:center; gap:12px; padding:13px 0; border-bottom:1px solid #f3f4f6; }
.toggle-row .tl { font-weight:600; font-size:14px; color:#374151; flex:1; }
.toggle-switch { position:relative; display:inline-block; width:44px; height:24px; flex-shrink:0; }
.toggle-switch input { opacity:0; width:0; height:0; }
.slider { position:absolute; inset:0; background:#d1d5db; border-radius:24px; cursor:pointer; transition:.25s; }
.slider:before { content:''; position:absolute; left:3px; bottom:3px; width:18px; height:18px; background:#fff; border-radius:50%; transition:.25s; }
.toggle-switch input:checked + .slider { background:#1e3a8a; }
.toggle-switch input:checked + .slider:before { transform:translateX(20px); }
.sec-title { font-size:15px; font-weight:700; color:#1e3a8a; margin:24px 0 14px; padding-bottom:8px; border-bottom:1px solid #e5e7eb; }
</style>

<div style="max-width:1000px; margin:auto;">
<div style="background:#fff; padding:28px; border-radius:14px; box-shadow:0 4px 16px rgba(0,0,0,.07);">
    <h2 style="font-size:24px; font-weight:800; color:#111827; margin-bottom:24px;">Edit Course: {{ $course->name }}</h2>

    @if($errors->any())
    <div style="background:#fee2e2; border:1px solid #fca5a5; border-radius:8px; padding:14px; margin-bottom:20px;">
        <ul style="margin:0; padding-left:18px; color:#b91c1c; font-size:13.5px;">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    @if(session('success'))
    <div style="background:#dcfce7; border:1px solid #86efac; border-radius:8px; padding:12px 16px; margin-bottom:20px;">
        <div style="color:#166534; font-weight:700; margin-bottom:8px;">✓ {{ session('success') }}</div>
        <div style="display:flex; gap:10px; flex-wrap:wrap; font-size:13px;">
            @if($course->course_type === 'elearning')
            <a href="{{ route('elearning.lessons.index', $course->id) }}"
               style="background:#16a34a; color:#fff; padding:5px 12px; border-radius:6px; text-decoration:none; font-weight:600;">
                📚 Manage Lessons
            </a>
            @endif
            @if($course->is_public && $course->slug)
            <a href="{{ route('public.course.detail', $course->slug) }}" target="_blank"
               style="background:#2563eb; color:#fff; padding:5px 12px; border-radius:6px; text-decoration:none; font-weight:600;">
                🌐 View Public Page
            </a>
            @endif
            <a href="/admin/courses"
               style="background:#6b7280; color:#fff; padding:5px 12px; border-radius:6px; text-decoration:none; font-weight:600;">
                ← Course List
            </a>
        </div>
    </div>
    @endif

    <div class="tab-nav">
        <button class="tab-btn active" id="tab-btn-basic"           onclick="showTab('basic',this)"           type="button">Basic Info</button>
        <button class="tab-btn"         id="tab-btn-content"        onclick="showTab('content',this)"         type="button">Public Content</button>
        <button class="tab-btn"         id="tab-btn-classification" onclick="showTab('classification',this)"  type="button">Classification</button>
        <button class="tab-btn"         id="tab-btn-seo"            onclick="showTab('seo',this)"             type="button">Visibility &amp; SEO</button>
    </div>

    <form method="POST" action="/admin/courses/update/{{ $course->id }}" enctype="multipart/form-data" id="courseEditForm">
        @csrf
        <input type="hidden" name="_action" id="courseAction" value="save">
        <input type="hidden" name="_tab"    id="courseTab"    value="basic">

        {{-- ── TAB 1: Basic Info ──────────────────────────────── --}}
        <div id="tab-basic" class="tab-panel active">
            <div class="frow">
                <div class="fg">
                    <label>Course Name <span style="color:red">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $course->name) }}" required>
                </div>
                <div class="fg">
                    <label>Course Code</label>
                    <input type="text" name="code" value="{{ old('code', $course->code) }}">
                </div>
            </div>
            <div class="frow">
                <div class="fg">
                    <label>Training Type <span style="color:red">*</span></label>
                    <select name="course_type" required>
                        <option value="manual" {{ old('course_type',$course->course_type)=='manual'?'selected':'' }}>Manual / Instructor-Led</option>
                        <option value="elearning" {{ old('course_type',$course->course_type)=='elearning'?'selected':'' }}>Self-Paced eLearning</option>
                    </select>
                </div>
                <div class="fg">
                    <label>Status</label>
                    <select name="status">
                        <option value="1" {{ old('status',$course->status)==1?'selected':'' }}>Active</option>
                        <option value="0" {{ old('status',$course->status)==0?'selected':'' }}>Inactive</option>
                    </select>
                </div>
            </div>
            <div class="frow">
                <div class="fg">
                    <label>Delivery Type</label>
                    <select name="delivery_type">
                        @foreach(['Instructor-Led','eLearning','Hybrid','Online Live'] as $dt)
                        <option value="{{ $dt }}" {{ old('delivery_type',$course->delivery_type)==$dt?'selected':'' }}>{{ $dt }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="fg">
                    <label>Language</label>
                    <select name="language">
                        @foreach(['English','Bangla','English & Bangla'] as $lang)
                        <option value="{{ $lang }}" {{ old('language',$course->language)==$lang?'selected':'' }}>{{ $lang }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="frow">
                <div class="fg">
                    <label>Category (text)</label>
                    <input type="text" name="category" value="{{ old('category', $course->category) }}">
                </div>
                <div class="fg">
                    <label>Category (structured)</label>
                    <select name="category_id">
                        <option value="">— Select Category —</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id',$course->category_id)==$cat->id?'selected':'' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="frow">
                <div class="fg">
                    <label>Duration</label>
                    <input type="text" name="duration" value="{{ old('duration', $course->duration) }}" placeholder="e.g. 40 Hours / 5 Days">
                </div>
                <div class="fg">
                    <label>CPD Hours</label>
                    <input type="number" name="cpd_hours" value="{{ old('cpd_hours', $course->cpd_hours) }}">
                </div>
            </div>
            <div class="frow">
                <div class="fg">
                    <label>Public Price (BDT)</label>
                    <input type="number" step="0.01" name="public_price" value="{{ old('public_price', $course->public_price) }}">
                </div>
                <div class="fg">
                    <label>Certificate Type</label>
                    <input type="text" name="certificate_type" value="{{ old('certificate_type', $course->certificate_type) }}">
                </div>
            </div>
            <div class="fg">
                <label>Banner Image (leave blank to keep existing)</label>
                @if($course->banner_image)
                <div style="margin-bottom:8px;">
                    <img src="{{ asset('storage/' . $course->banner_image) }}" style="height:80px; border-radius:6px; object-fit:cover;">
                </div>
                @endif
                <input type="file" name="banner_image" accept="image/*" style="padding:6px;">
            </div>
            <div class="fg">
                <label>Certification Remarks (internal)</label>
                <textarea name="certification_remarks" rows="3">{{ old('certification_remarks', $course->certification_remarks) }}</textarea>
            </div>
        </div>

        {{-- ── TAB 2: Public Content ───────────────────────────── --}}
        <div id="tab-content" class="tab-panel">
            <div class="fg">
                <label>Short Description</label>
                <textarea name="short_description" rows="3" maxlength="500">{{ old('short_description', $course->short_description) }}</textarea>
            </div>
            <div class="fg">
                <label>Full Description</label>
                <textarea name="full_description" rows="7">{{ old('full_description', $course->full_description) }}</textarea>
            </div>
            <div class="fg">
                <label>Learning Objectives</label>
                <textarea name="learning_objectives" rows="5">{{ old('learning_objectives', $course->learning_objectives) }}</textarea>
            </div>
            <div class="fg">
                <label>Course Outline</label>
                <textarea name="course_outline" rows="6">{{ old('course_outline', $course->course_outline) }}</textarea>
            </div>
            <div class="fg">
                <label>Who Should Attend</label>
                <textarea name="who_should_attend" rows="4">{{ old('who_should_attend', $course->who_should_attend) }}</textarea>
            </div>
            <div class="fg">
                <label>Prerequisites</label>
                <textarea name="prerequisites" rows="3">{{ old('prerequisites', $course->prerequisites) }}</textarea>
            </div>
            <div class="fg">
                <label>Certification Info (public)</label>
                <textarea name="certification_info" rows="3">{{ old('certification_info', $course->certification_info) }}</textarea>
            </div>
            <div class="fg">
                <label>Course Intro Video URL (YouTube)</label>
                <input type="url" name="course_video_url" value="{{ old('course_video_url', $course->course_video_url) }}">
            </div>
            <div class="fg">
                <label>FAQ (plain text or JSON)</label>
                <textarea name="faq" rows="6">{{ old('faq', $course->faq) }}</textarea>
            </div>
        </div>

        {{-- ── TAB 3: Classification ──────────────────────────── --}}
        <div id="tab-classification" class="tab-panel">
            @php
                $selStdIds  = old('ltf_standard_ids', $selectedStandardIds ?? []);
                $selIndIds  = old('ltf_industry_ids',  $selectedIndustryIds  ?? []);
                $selAudIds  = old('ltf_audience_ids',  $selectedAudienceIds  ?? []);
                $selFwId    = old('ltf_learning_framework_id', $course->ltf_learning_framework_id);
                $selCompLvl = old('ltf_competency_level',      $course->ltf_competency_level ?? '');
            @endphp

            <p style="font-size:13px; color:#6b7280; margin:-4px 0 20px; padding:10px 14px; background:#f0f9ff; border-radius:8px; border-left:3px solid #0ea5e9;">
                LTF (Learning Taxonomy Framework) — classify this course for smart search, AI generation, reporting, and accreditation mapping.
            </p>

            <div class="frow" style="margin-bottom:20px;">
                <div class="fg">
                    <label>Dim 1 — Delivery Method</label>
                    <select name="ltf_delivery_method_id">
                        <option value="">— Not set —</option>
                        @foreach($ltfDeliveryMethods as $id => $name)
                        <option value="{{ $id }}" {{ old('ltf_delivery_method_id', $course->ltf_delivery_method_id) == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="fg">
                    <label>Dim 2 — Training Model</label>
                    <select name="ltf_training_model_id">
                        <option value="">— Not set —</option>
                        @foreach($ltfTrainingModels as $id => $name)
                        <option value="{{ $id }}" {{ old('ltf_training_model_id', $course->ltf_training_model_id) == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="frow" style="margin-bottom:20px;">
                <div class="fg">
                    <label>Dim 3 — Program Purpose</label>
                    <select name="ltf_program_purpose_id" id="purposeSelect" onchange="handlePurposeChange(this)">
                        <option value="">— Not set —</option>
                        @foreach($ltfProgramPurposes as $id => $name)
                        <option value="{{ $id }}" {{ old('ltf_program_purpose_id', $course->ltf_program_purpose_id) == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="fg">
                    <label>Dim 4 — Learning Framework</label>
                    <select name="ltf_learning_framework_id" id="frameworkSelect">
                        <option value="">— Not classified —</option>
                        @foreach($ltfFrameworks as $id => $name)
                        <option value="{{ $id }}" {{ $selFwId == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                    <small style="color:#6b7280; font-size:12px; display:block; margin-top:4px;">Auto-suggested from Program Purpose — you may override.</small>
                </div>
            </div>

            {{-- Dim 8: Competency Level --}}
            <div style="margin-bottom:20px;">
                <label style="display:block; font-weight:600; font-size:13.5px; color:#374151; margin-bottom:10px;">Dim 8 — Competency Level</label>
                <div style="display:flex; gap:8px; flex-wrap:wrap;">
                    @foreach(['beginner' => 'Beginner', 'intermediate' => 'Intermediate', 'advanced' => 'Advanced', 'expert' => 'Expert'] as $cval => $clbl)
                    @php $csel = $selCompLvl === $cval; @endphp
                    <button type="button" onclick="selectCompetency('{{ $cval }}')" data-value="{{ $cval }}"
                        style="padding:6px 18px; border-radius:20px; border:1.5px solid {{ $csel ? '#1e3a8a' : '#e5e7eb' }}; background:{{ $csel ? '#1e3a8a' : '#fff' }}; color:{{ $csel ? '#fff' : '#374151' }}; font-size:13px; font-weight:600; cursor:pointer;"
                        class="comp-pill">{{ $clbl }}</button>
                    @endforeach
                    @php $cNone = !$selCompLvl; @endphp
                    <button type="button" onclick="selectCompetency('')" data-value=""
                        style="padding:6px 18px; border-radius:20px; border:1.5px solid {{ $cNone ? '#1e3a8a' : '#e5e7eb' }}; background:{{ $cNone ? '#1e3a8a' : '#fff' }}; color:{{ $cNone ? '#fff' : '#9ca3af' }}; font-size:13px; cursor:pointer;"
                        class="comp-pill">Not set</button>
                </div>
                <input type="hidden" name="ltf_competency_level" id="competencyInput" value="{{ $selCompLvl }}">
            </div>

            <script>
            var purposeSuggestions = @json($purposeSuggestions ?? []);
            function handlePurposeChange(sel) {
                var fw = document.getElementById('frameworkSelect');
                if (!fw.value && purposeSuggestions[sel.value]) {
                    fw.value = purposeSuggestions[sel.value];
                }
            }
            function selectCompetency(val) {
                document.getElementById('competencyInput').value = val;
                document.querySelectorAll('.comp-pill').forEach(function(btn) {
                    var active = btn.dataset.value === val;
                    btn.style.background   = active ? '#1e3a8a' : '#fff';
                    btn.style.borderColor  = active ? '#1e3a8a' : '#e5e7eb';
                    btn.style.color        = active ? '#fff' : (btn.dataset.value ? '#374151' : '#9ca3af');
                });
            }
            </script>

            {{-- Layer 3: Standards (grouped checkboxes) --}}
            <div style="margin-bottom:20px;">
                <label style="display:block; font-weight:600; font-size:13.5px; color:#374151; margin-bottom:10px;">Dim 5 — Standards &amp; Frameworks <span style="font-weight:400; color:#9ca3af;">(select all that apply)</span></label>
                @foreach($ltfStandards as $domainGroup)
                <div style="margin-bottom:12px;">
                    <div style="font-size:11px; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:.6px; margin-bottom:6px;">{{ $domainGroup['label'] }}</div>
                    <div style="display:flex; flex-wrap:wrap; gap:8px;">
                        @foreach($domainGroup['options'] as $id => $name)
                        @php $checked = in_array($id, $selStdIds); @endphp
                        <label style="display:flex; align-items:center; gap:6px; padding:5px 12px; border:1.5px solid {{ $checked ? '#1e3a8a' : '#e5e7eb' }}; border-radius:6px; cursor:pointer; font-size:13px; font-weight:500; background:{{ $checked ? '#eff6ff' : '#fff' }};">
                            <input type="checkbox" name="ltf_standard_ids[]" value="{{ $id }}" {{ $checked ? 'checked' : '' }} style="width:auto; accent-color:#1e3a8a;">
                            {{ $name }}
                        </label>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Layer 4: Industries --}}
            <div style="margin-bottom:20px;">
                <label style="display:block; font-weight:600; font-size:13.5px; color:#374151; margin-bottom:10px;">Dim 6 — Industries <span style="font-weight:400; color:#9ca3af;">(select all that apply)</span></label>
                <div style="display:flex; flex-wrap:wrap; gap:8px;">
                    @foreach($ltfIndustries as $id => $name)
                    @php $checked = in_array($id, $selIndIds); @endphp
                    <label style="display:flex; align-items:center; gap:6px; padding:5px 12px; border:1.5px solid {{ $checked ? '#1e3a8a' : '#e5e7eb' }}; border-radius:6px; cursor:pointer; font-size:13px; font-weight:500; background:{{ $checked ? '#eff6ff' : '#fff' }};">
                        <input type="checkbox" name="ltf_industry_ids[]" value="{{ $id }}" {{ $checked ? 'checked' : '' }} style="width:auto; accent-color:#1e3a8a;">
                        {{ $name }}
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Layer 5: Audience Types --}}
            <div style="margin-bottom:20px;">
                <label style="display:block; font-weight:600; font-size:13.5px; color:#374151; margin-bottom:10px;">Dim 7 — Audience Types <span style="font-weight:400; color:#9ca3af;">(select all that apply)</span></label>
                <div style="display:flex; flex-wrap:wrap; gap:8px;">
                    @foreach($ltfAudiences as $id => $name)
                    @php $checked = in_array($id, $selAudIds); @endphp
                    <label style="display:flex; align-items:center; gap:6px; padding:5px 12px; border:1.5px solid {{ $checked ? '#1e3a8a' : '#e5e7eb' }}; border-radius:6px; cursor:pointer; font-size:13px; font-weight:500; background:{{ $checked ? '#eff6ff' : '#fff' }};">
                        <input type="checkbox" name="ltf_audience_ids[]" value="{{ $id }}" {{ $checked ? 'checked' : '' }} style="width:auto; accent-color:#1e3a8a;">
                        {{ $name }}
                    </label>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ── TAB 4: Visibility & SEO ─────────────────────────── --}}
        <div id="tab-seo" class="tab-panel">
            <div class="fg">
                <label>URL Slug</label>
                <input type="text" name="slug" value="{{ old('slug', $course->slug) }}">
                <small style="color:#6b7280; font-size:12px; display:block; margin-top:4px;">Public URL: /courses/{slug}</small>
            </div>
            <div style="background:#f8fafc; border-radius:10px; padding:16px 20px; margin-bottom:20px;">
                <div class="toggle-row">
                    <span class="tl">Show on Public Website</span>
                    <label class="toggle-switch">
                        <input type="checkbox" name="is_public" value="1" {{ old('is_public', $course->is_public) ? 'checked':'' }}>
                        <span class="slider"></span>
                    </label>
                </div>
                <div class="toggle-row" style="border:none;">
                    <span class="tl">Featured Course (show on homepage)</span>
                    <label class="toggle-switch">
                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $course->is_featured) ? 'checked':'' }}>
                        <span class="slider"></span>
                    </label>
                </div>
            </div>
            <div class="frow">
                <div class="fg">
                    <label>Display Order</label>
                    <input type="number" name="display_order" value="{{ old('display_order', $course->display_order ?? 0) }}" min="0">
                </div>
                <div class="fg">
                    <label>Featured Order</label>
                    <input type="number" name="featured_order" value="{{ old('featured_order', $course->featured_order ?? 0) }}" min="0">
                </div>
            </div>
            <p class="sec-title">SEO Meta Tags</p>
            <div class="fg">
                <label>SEO Title</label>
                <input type="text" name="seo_title" value="{{ old('seo_title', $course->seo_title) }}">
            </div>
            <div class="fg">
                <label>SEO Description</label>
                <textarea name="seo_description" rows="3" maxlength="200">{{ old('seo_description', $course->seo_description) }}</textarea>
            </div>
            <div class="fg">
                <label>SEO Keywords</label>
                <input type="text" name="seo_keywords" value="{{ old('seo_keywords', $course->seo_keywords) }}">
            </div>
        </div>

        <div style="display:flex; gap:10px; margin-top:28px; padding-top:20px; border-top:1px solid #e5e7eb; flex-wrap:wrap; align-items:center;">
            <button type="button" onclick="submitCourse('save')"
                    style="background:#1e3a8a; color:#fff; padding:11px 26px; border:none; border-radius:8px; font-weight:700; font-size:14px; cursor:pointer;">
                💾 Save Changes
            </button>
            <button type="button" onclick="submitCourse('back')"
                    style="background:#6b7280; color:#fff; padding:11px 22px; border:none; border-radius:8px; font-weight:700; font-size:14px; cursor:pointer;">
                Save &amp; Back to List
            </button>
            @if($course->course_type === 'elearning')
            <button type="button" onclick="submitCourse('lessons')"
                    style="background:#16a34a; color:#fff; padding:11px 22px; border:none; border-radius:8px; font-weight:700; font-size:14px; cursor:pointer;">
                Save &amp; Manage Lessons →
            </button>
            @endif
            <a href="/admin/courses"
               style="background:#f1f5f9; color:#374151; padding:11px 18px; border-radius:8px; text-decoration:none; font-weight:600; font-size:14px; margin-left:auto;">
                Cancel
            </a>
        </div>
    </form>
</div>
</div>

<script>
function showTab(name, btn) {
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + name).classList.add('active');
    btn.classList.add('active');
    document.getElementById('courseTab').value = name;
}

function submitCourse(action) {
    document.getElementById('courseAction').value = action;
    document.getElementById('courseEditForm').submit();
}

// Restore active tab from URL ?tab= parameter
(function () {
    const tab = new URLSearchParams(window.location.search).get('tab');
    if (tab && document.getElementById('tab-' + tab)) {
        const btn = document.getElementById('tab-btn-' + tab);
        if (btn) showTab(tab, btn);
    }
    // Live highlight for classification checkboxes
    document.querySelectorAll('input[type=checkbox]').forEach(cb => {
        cb.addEventListener('change', function() {
            const lbl = this.closest('label');
            if (!lbl) return;
            lbl.style.borderColor = this.checked ? '#1e3a8a' : '#e5e7eb';
            lbl.style.background  = this.checked ? '#eff6ff' : '#fff';
        });
    });
})();
</script>
@endsection
