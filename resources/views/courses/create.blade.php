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
.fg input:focus,.fg select:focus,.fg textarea:focus { border-color:#1e3a8a; outline:none; }
.frow { display:grid; grid-template-columns:1fr 1fr; gap:18px; }
.frow3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:18px; }
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
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; flex-wrap:wrap; gap:10px;">
        <h2 style="font-size:24px; font-weight:800; color:#111827; margin:0;">Add Course</h2>
        @if(auth()->user()?->isSuperAdmin())
            @php $aiCourseType = 'ilt'; @endphp
            @include('ai.course-generator._modal')
        @endif
    </div>

    @if($errors->any())
    <div style="background:#fee2e2; border:1px solid #fca5a5; border-radius:8px; padding:14px; margin-bottom:20px;">
        <ul style="margin:0; padding-left:18px; color:#b91c1c; font-size:13.5px;">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    <div class="tab-nav">
        <button class="tab-btn active" onclick="showTab('basic',this)" type="button">Basic Info</button>
        <button class="tab-btn" onclick="showTab('content',this)" type="button">Public Content</button>
        <button class="tab-btn" onclick="showTab('classification',this)" type="button">Classification</button>
        <button class="tab-btn" onclick="showTab('seo',this)" type="button">Visibility &amp; SEO</button>
    </div>

    <form method="POST" action="/admin/courses/store" enctype="multipart/form-data">
        @csrf

        {{-- ── TAB 1: Basic Info ──────────────────────────────── --}}
        <div id="tab-basic" class="tab-panel active">
            <div class="frow">
                <div class="fg">
                    <label>Course Name <span style="color:red">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required>
                </div>
                <div class="fg">
                    <label>Course Code</label>
                    <input type="text" name="code" value="{{ old('code') }}" placeholder="e.g. SMS-ISO9001">
                </div>
            </div>
            <div class="frow">
                <div class="fg">
                    <label>Training Type <span style="color:red">*</span></label>
                    <select name="course_type" required>
                        <option value="manual" {{ old('course_type','manual')=='manual'?'selected':'' }}>Manual / Instructor-Led</option>
                        <option value="elearning" {{ old('course_type')=='elearning'?'selected':'' }}>Self-Paced eLearning</option>
                    </select>
                </div>
                <div class="fg">
                    <label>Status</label>
                    <select name="status">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="frow">
                <div class="fg">
                    <label>Delivery Type</label>
                    <select name="delivery_type">
                        <option value="Instructor-Led">Instructor-Led</option>
                        <option value="eLearning">eLearning</option>
                        <option value="Hybrid">Hybrid</option>
                        <option value="Online Live">Online Live</option>
                    </select>
                </div>
                <div class="fg">
                    <label>Language</label>
                    <select name="language">
                        <option value="English">English</option>
                        <option value="Bangla">Bangla</option>
                        <option value="English & Bangla">English &amp; Bangla</option>
                    </select>
                </div>
            </div>
            <div class="frow">
                <div class="fg">
                    <label>Category (text)</label>
                    <input type="text" name="category" value="{{ old('category') }}" placeholder="e.g. ISO Standards">
                </div>
                <div class="fg">
                    <label>Category (structured)</label>
                    <select name="category_id">
                        <option value="">— Select Category —</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id')==$cat->id?'selected':'' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="frow">
                <div class="fg">
                    <label>Duration</label>
                    <input type="text" name="duration" value="{{ old('duration') }}" placeholder="e.g. 40 Hours / 5 Days">
                </div>
                <div class="fg">
                    <label>CPD Hours</label>
                    <input type="number" name="cpd_hours" value="{{ old('cpd_hours') }}" placeholder="e.g. 20">
                </div>
            </div>
            <div class="frow">
                <div class="fg">
                    <label>Public Price (BDT)</label>
                    <input type="number" step="0.01" name="public_price" value="{{ old('public_price') }}">
                </div>
                <div class="fg">
                    <label>Certificate Type</label>
                    <input type="text" name="certificate_type" value="{{ old('certificate_type') }}" placeholder="e.g. Certificate of Completion">
                </div>
            </div>
            <div class="fg">
                <label>Banner Image</label>
                <input type="file" name="banner_image" accept="image/*" style="padding:6px;">
            </div>
            <div class="fg">
                <label>Certification Remarks (internal)</label>
                <textarea name="certification_remarks" rows="3">{{ old('certification_remarks') }}</textarea>
            </div>
        </div>

        {{-- ── TAB 2: Public Content ───────────────────────────── --}}
        <div id="tab-content" class="tab-panel">
            <div class="fg">
                <label>Short Description (shown on course cards)</label>
                <textarea name="short_description" rows="3" maxlength="500">{{ old('short_description') }}</textarea>
            </div>
            <div class="fg">
                <label>Full Description</label>
                <textarea name="full_description" rows="7">{{ old('full_description') }}</textarea>
            </div>
            <div class="fg">
                <label>Learning Objectives</label>
                <textarea name="learning_objectives" rows="5" placeholder="One objective per line">{{ old('learning_objectives') }}</textarea>
            </div>
            <div class="fg">
                <label>Course Outline</label>
                <textarea name="course_outline" rows="6">{{ old('course_outline') }}</textarea>
            </div>
            <div class="fg">
                <label>Who Should Attend</label>
                <textarea name="who_should_attend" rows="4">{{ old('who_should_attend') }}</textarea>
            </div>
            <div class="fg">
                <label>Prerequisites</label>
                <textarea name="prerequisites" rows="3">{{ old('prerequisites') }}</textarea>
            </div>
            <div class="fg">
                <label>Certification Info (public)</label>
                <textarea name="certification_info" rows="3" placeholder="Details about the certificate awarded">{{ old('certification_info') }}</textarea>
            </div>
            <div class="fg">
                <label>Course Intro Video URL (YouTube)</label>
                <input type="url" name="course_video_url" value="{{ old('course_video_url') }}" placeholder="https://www.youtube.com/watch?v=...">
            </div>
            <div class="fg">
                <label>FAQ (plain text or JSON)</label>
                <textarea name="faq" rows="5" placeholder='Q: What is the duration?&#10;A: 5 days / 40 hours'>{{ old('faq') }}</textarea>
            </div>
        </div>

        {{-- ── TAB 3: Classification ──────────────────────────── --}}
        <div id="tab-classification" class="tab-panel">
            <p style="font-size:13px; color:#6b7280; margin:-4px 0 20px; padding:10px 14px; background:#f0f9ff; border-radius:8px; border-left:3px solid #0ea5e9;">
                LTF (Learning Taxonomy Framework) — classify this course for smart search, AI generation, reporting, and accreditation mapping.
            </p>

            <div class="frow" style="margin-bottom:20px;">
                <div class="fg">
                    <label>Layer 1 — Course Type</label>
                    <select name="ltf_course_type_id">
                        <option value="">— Not classified —</option>
                        @foreach($ltfCourseTypes as $grp)
                        <optgroup label="{{ $grp['label'] }}">
                            @foreach($grp['options'] as $id => $name)
                            <option value="{{ $id }}" {{ old('ltf_course_type_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </optgroup>
                        @endforeach
                    </select>
                </div>
                <div class="fg">
                    <label>Layer 2 — Learning Framework</label>
                    <select name="ltf_learning_framework_id">
                        <option value="">— Not classified —</option>
                        @foreach($ltfFrameworks as $id => $name)
                        <option value="{{ $id }}" {{ old('ltf_learning_framework_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Layer 3: Standards (grouped checkboxes) --}}
            <div style="margin-bottom:20px;">
                <label style="display:block; font-weight:600; font-size:13.5px; color:#374151; margin-bottom:10px;">Layer 3 — Standards &amp; Frameworks <span style="font-weight:400; color:#9ca3af;">(select all that apply)</span></label>
                @foreach($ltfStandards as $domainGroup)
                <div style="margin-bottom:12px;">
                    <div style="font-size:11px; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:.6px; margin-bottom:6px;">{{ $domainGroup['label'] }}</div>
                    <div style="display:flex; flex-wrap:wrap; gap:8px;">
                        @foreach($domainGroup['options'] as $id => $name)
                        <label style="display:flex; align-items:center; gap:6px; padding:5px 12px; border:1.5px solid {{ in_array($id, old('ltf_standard_ids', [])) ? '#1e3a8a' : '#e5e7eb' }}; border-radius:6px; cursor:pointer; font-size:13px; font-weight:500; background:{{ in_array($id, old('ltf_standard_ids', [])) ? '#eff6ff' : '#fff' }};">
                            <input type="checkbox" name="ltf_standard_ids[]" value="{{ $id }}" {{ in_array($id, old('ltf_standard_ids', [])) ? 'checked' : '' }} style="width:auto; accent-color:#1e3a8a;">
                            {{ $name }}
                        </label>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Layer 4: Industries --}}
            <div style="margin-bottom:20px;">
                <label style="display:block; font-weight:600; font-size:13.5px; color:#374151; margin-bottom:10px;">Layer 4 — Industries <span style="font-weight:400; color:#9ca3af;">(select all that apply)</span></label>
                <div style="display:flex; flex-wrap:wrap; gap:8px;">
                    @foreach($ltfIndustries as $id => $name)
                    <label style="display:flex; align-items:center; gap:6px; padding:5px 12px; border:1.5px solid {{ in_array($id, old('ltf_industry_ids', [])) ? '#1e3a8a' : '#e5e7eb' }}; border-radius:6px; cursor:pointer; font-size:13px; font-weight:500; background:{{ in_array($id, old('ltf_industry_ids', [])) ? '#eff6ff' : '#fff' }};">
                        <input type="checkbox" name="ltf_industry_ids[]" value="{{ $id }}" {{ in_array($id, old('ltf_industry_ids', [])) ? 'checked' : '' }} style="width:auto; accent-color:#1e3a8a;">
                        {{ $name }}
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Layer 5: Audience Types --}}
            <div style="margin-bottom:20px;">
                <label style="display:block; font-weight:600; font-size:13.5px; color:#374151; margin-bottom:10px;">Layer 5 — Audience Types <span style="font-weight:400; color:#9ca3af;">(select all that apply)</span></label>
                <div style="display:flex; flex-wrap:wrap; gap:8px;">
                    @foreach($ltfAudiences as $id => $name)
                    <label style="display:flex; align-items:center; gap:6px; padding:5px 12px; border:1.5px solid {{ in_array($id, old('ltf_audience_ids', [])) ? '#1e3a8a' : '#e5e7eb' }}; border-radius:6px; cursor:pointer; font-size:13px; font-weight:500; background:{{ in_array($id, old('ltf_audience_ids', [])) ? '#eff6ff' : '#fff' }};">
                        <input type="checkbox" name="ltf_audience_ids[]" value="{{ $id }}" {{ in_array($id, old('ltf_audience_ids', [])) ? 'checked' : '' }} style="width:auto; accent-color:#1e3a8a;">
                        {{ $name }}
                    </label>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ── TAB 4: Visibility & SEO ─────────────────────────── --}}
        <div id="tab-seo" class="tab-panel">
            <div class="fg">
                <label>URL Slug (auto-generated if blank)</label>
                <input type="text" name="slug" value="{{ old('slug') }}" placeholder="e.g. iso-9001-lead-auditor">
                <small style="color:#6b7280; font-size:12px; display:block; margin-top:4px;">Public URL: /courses/{slug}</small>
            </div>
            <div style="background:#f8fafc; border-radius:10px; padding:16px 20px; margin-bottom:20px;">
                <div class="toggle-row">
                    <span class="tl">Show on Public Website</span>
                    <label class="toggle-switch">
                        <input type="checkbox" name="is_public" value="1" {{ old('is_public') ? 'checked':'' }}>
                        <span class="slider"></span>
                    </label>
                </div>
                <div class="toggle-row" style="border:none;">
                    <span class="tl">Featured Course (show on homepage)</span>
                    <label class="toggle-switch">
                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked':'' }}>
                        <span class="slider"></span>
                    </label>
                </div>
            </div>
            <div class="frow">
                <div class="fg">
                    <label>Display Order</label>
                    <input type="number" name="display_order" value="{{ old('display_order', 0) }}" min="0">
                </div>
                <div class="fg">
                    <label>Featured Order</label>
                    <input type="number" name="featured_order" value="{{ old('featured_order', 0) }}" min="0">
                </div>
            </div>
            <p class="sec-title">SEO Meta Tags</p>
            <div class="fg">
                <label>SEO Title</label>
                <input type="text" name="seo_title" value="{{ old('seo_title') }}" placeholder="Defaults to course name if blank">
            </div>
            <div class="fg">
                <label>SEO Description (max 200 chars)</label>
                <textarea name="seo_description" rows="3" maxlength="200">{{ old('seo_description') }}</textarea>
            </div>
            <div class="fg">
                <label>SEO Keywords (comma-separated)</label>
                <input type="text" name="seo_keywords" value="{{ old('seo_keywords') }}" placeholder="ISO 9001, lead auditor, quality management">
            </div>
        </div>

        <div style="display:flex; gap:12px; margin-top:28px; padding-top:20px; border-top:1px solid #e5e7eb;">
            <button type="submit" style="background:#1e3a8a; color:#fff; padding:12px 28px; border:none; border-radius:8px; font-weight:700; font-size:15px; cursor:pointer;">
                Save Course
            </button>
            <a href="/admin/courses" style="background:#6b7280; color:#fff; padding:12px 20px; border-radius:8px; text-decoration:none; font-weight:600; font-size:15px;">
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
}
document.querySelectorAll('input[type=checkbox]').forEach(cb => {
    cb.addEventListener('change', function() {
        const lbl = this.closest('label');
        if (!lbl) return;
        lbl.style.borderColor = this.checked ? '#1e3a8a' : '#e5e7eb';
        lbl.style.background  = this.checked ? '#eff6ff' : '#fff';
    });
});
</script>
@endsection
