@extends('layouts.app')
@section('page-title', 'Edit eLearning Course')
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
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
        <h2 style="font-size:24px; font-weight:800; color:#111827; margin:0;">Edit eLearning Course</h2>
        <a href="{{ route('elearning.lessons.index', $course) }}"
           style="background:#0ea5e9; color:#fff; padding:9px 18px; border-radius:8px; text-decoration:none; font-weight:700; font-size:13.5px;">
            Manage Lessons →
        </a>
    </div>

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
            <a href="{{ route('elearning.lessons.index', $course) }}"
               style="background:#16a34a; color:#fff; padding:5px 12px; border-radius:6px; text-decoration:none; font-weight:600;">
                📚 Manage Lessons
            </a>
            @if($course->is_public && $course->slug)
            <a href="{{ route('public.course.detail', $course->slug) }}" target="_blank"
               style="background:#2563eb; color:#fff; padding:5px 12px; border-radius:6px; text-decoration:none; font-weight:600;">
                🌐 View Public Page
            </a>
            @endif
            <a href="{{ route('elearning.courses.index') }}"
               style="background:#6b7280; color:#fff; padding:5px 12px; border-radius:6px; text-decoration:none; font-weight:600;">
                ← Course List
            </a>
        </div>
    </div>
    @endif

    <div class="tab-nav">
        <button class="tab-btn active" id="tab-btn-basic"    onclick="showTab('basic',this)"   type="button">Basic Info</button>
        <button class="tab-btn"         id="tab-btn-content" onclick="showTab('content',this)" type="button">Public Content</button>
        <button class="tab-btn"         id="tab-btn-cover"   onclick="showTab('cover',this)"   type="button">🖼 Course Cover</button>
        <button class="tab-btn"         id="tab-btn-seo"     onclick="showTab('seo',this)"     type="button">Visibility &amp; SEO</button>
    </div>

    <form method="POST" action="{{ route('elearning.courses.update', $course) }}" enctype="multipart/form-data" id="courseEditForm">
        <input type="hidden" name="_action" id="courseAction" value="save">
        <input type="hidden" name="_tab"    id="courseTab"    value="basic">
        @csrf
        @method('PUT')

        {{-- ── TAB 1: Basic Info ──────────────────────────────── --}}
        <div id="tab-basic" class="tab-panel active">
            <div class="frow">
                <div class="fg">
                    <label>Course Name <span style="color:red">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $course->name) }}" required>
                </div>
                <div class="fg">
                    <label>Course Code</label>
                    <input type="text" name="code" value="{{ old('code', $course->code) }}" placeholder="e.g. SMS-EL-001">
                </div>
            </div>
            <div class="frow">
                <div class="fg">
                    <label>Status <span style="color:red">*</span></label>
                    <select name="status" required>
                        <option value="1" {{ old('status', $course->status)==1?'selected':'' }}>Active</option>
                        <option value="0" {{ old('status', $course->status)==0?'selected':'' }}>Inactive</option>
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
                    <input type="text" name="category" value="{{ old('category', $course->category) }}" placeholder="e.g. ISO Standards">
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
                    <input type="text" name="duration" value="{{ old('duration', $course->duration) }}" placeholder="e.g. 4 Hours / Self-Paced">
                </div>
                <div class="fg">
                    <label>CPD Hours</label>
                    <input type="number" name="cpd_hours" value="{{ old('cpd_hours', $course->cpd_hours) }}" placeholder="e.g. 4">
                </div>
            </div>
            <div class="frow3">
                <div class="fg">
                    <label>Course Fee (BDT)</label>
                    <input type="number" step="0.01" name="course_fee" value="{{ old('course_fee', $course->course_fee) }}">
                </div>
                <div class="fg">
                    <label>Public Price (BDT)</label>
                    <input type="number" step="0.01" name="public_price" value="{{ old('public_price', $course->public_price) }}">
                </div>
                <div class="fg">
                    <label>Access Days</label>
                    <input type="number" name="access_days" value="{{ old('access_days', $course->access_days ?? 30) }}" placeholder="30">
                </div>
            </div>
            <div class="frow">
                <div class="fg">
                    <label>Passing Score (%)</label>
                    <input type="number" name="passing_score" value="{{ old('passing_score', $course->passing_score ?? 70) }}" min="1" max="100">
                </div>
                <div class="fg">
                    <label>Certificate Type</label>
                    <input type="text" name="certificate_type" value="{{ old('certificate_type', $course->certificate_type) }}" placeholder="e.g. Certificate of Completion">
                </div>
            </div>

            {{-- Assessment Policy ------------------------------------------------- --}}
            <div style="background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:10px;padding:16px 20px;margin-top:4px;">
                <div style="font-size:12.5px;font-weight:800;color:#1e3a8a;margin-bottom:12px;text-transform:uppercase;letter-spacing:.4px;">Assessment Policy</div>
                <div class="frow3">
                    <div class="fg">
                        <label>Policy Type</label>
                        <select name="assessment_policy">
                            <option value="normal"  {{ old('assessment_policy', $course->assessment_policy ?? 'normal') === 'normal'  ? 'selected' : '' }}>Normal eLearning</option>
                            <option value="auditor" {{ old('assessment_policy', $course->assessment_policy ?? 'normal') === 'auditor' ? 'selected' : '' }}>Auditor / Qualification</option>
                            <option value="custom"  {{ old('assessment_policy', $course->assessment_policy ?? 'normal') === 'custom'  ? 'selected' : '' }}>Custom</option>
                        </select>
                    </div>
                    <div class="fg">
                        <label>Module Check Max Attempts</label>
                        <input type="number" name="module_check_max_attempts" min="1" max="10"
                               value="{{ old('module_check_max_attempts', $course->module_check_max_attempts ?? 3) }}">
                    </div>
                    <div class="fg">
                        <label>Final Exam Max Attempts</label>
                        <input type="number" name="final_exam_max_attempts" min="1" max="10"
                               value="{{ old('final_exam_max_attempts', $course->final_exam_max_attempts ?? 3) }}">
                    </div>
                </div>
                <div class="frow" style="margin-top:10px;gap:24px;">
                    <label style="display:flex;align-items:center;gap:8px;font-size:13px;cursor:pointer;">
                        <input type="hidden" name="require_module_review" value="0">
                        <input type="checkbox" name="require_module_review" value="1"
                               {{ old('require_module_review', $course->require_module_review ?? true) ? 'checked' : '' }}
                               style="width:16px;height:16px;">
                        Require module review before retry (unlocks 3 extra attempts after re-visiting module)
                    </label>
                    <label style="display:flex;align-items:center;gap:8px;font-size:13px;cursor:pointer;">
                        <input type="hidden" name="require_admin_approval" value="0">
                        <input type="checkbox" name="require_admin_approval" value="1"
                               {{ old('require_admin_approval', $course->require_admin_approval ?? false) ? 'checked' : '' }}
                               style="width:16px;height:16px;">
                        Require admin approval after all attempts exhausted (Final Exam)
                    </label>
                </div>
            </div>
            <div class="fg">
                <label>Banner Image</label>
                @if($course->banner_image)
                <div style="margin-bottom:8px;">
                    <img src="{{ asset('storage/'.$course->banner_image) }}" alt="Banner" style="height:80px; border-radius:6px; object-fit:cover;">
                    <span style="font-size:12px; color:#6b7280; margin-left:8px;">Current banner</span>
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
                <label>Short Description (shown on course cards)</label>
                <textarea name="short_description" rows="3" maxlength="500">{{ old('short_description', $course->short_description) }}</textarea>
            </div>
            <div class="fg">
                <label>Full Description</label>
                <textarea name="full_description" rows="7">{{ old('full_description', $course->full_description) }}</textarea>
            </div>
            <div class="fg">
                <label>Learning Objectives</label>
                <textarea name="learning_objectives" rows="5" placeholder="One objective per line">{{ old('learning_objectives', $course->learning_objectives) }}</textarea>
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
                <textarea name="certification_info" rows="3" placeholder="Details about the certificate awarded">{{ old('certification_info', $course->certification_info) }}</textarea>
            </div>
            <div class="fg">
                <label>Course Intro Video URL (YouTube)</label>
                <input type="url" name="course_video_url" value="{{ old('course_video_url', $course->course_video_url) }}" placeholder="https://www.youtube.com/watch?v=...">
            </div>
            <div class="fg">
                <label>FAQ (plain text or JSON)</label>
                <textarea name="faq" rows="5" placeholder="Q: How long do I have access?&#10;A: 30 days from enrollment date.">{{ old('faq', $course->faq) }}</textarea>
            </div>
        </div>

        {{-- ── TAB 3: Course Cover ────────────────────────────── --}}
        <div id="tab-cover" class="tab-panel">
            @include('elearning.courses.partials.cover-section')
        </div>

        {{-- ── TAB 4: Visibility & SEO ─────────────────────────── --}}
        <div id="tab-seo" class="tab-panel">
            <div class="fg">
                <label>URL Slug (auto-generated if blank)</label>
                <input type="text" name="slug" value="{{ old('slug', $course->slug) }}" placeholder="e.g. iso-9001-elearning">
                <small style="color:#6b7280; font-size:12px; display:block; margin-top:4px;">Public URL: /courses/{{ $course->slug }}</small>
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
                <input type="text" name="seo_title" value="{{ old('seo_title', $course->seo_title) }}" placeholder="Defaults to course name if blank">
            </div>
            <div class="fg">
                <label>SEO Description (max 200 chars)</label>
                <textarea name="seo_description" rows="3" maxlength="200">{{ old('seo_description', $course->seo_description) }}</textarea>
            </div>
            <div class="fg">
                <label>SEO Keywords (comma-separated)</label>
                <input type="text" name="seo_keywords" value="{{ old('seo_keywords', $course->seo_keywords) }}" placeholder="ISO 9001, elearning, online course">
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
            <button type="button" onclick="submitCourse('lessons')"
                    style="background:#16a34a; color:#fff; padding:11px 22px; border:none; border-radius:8px; font-weight:700; font-size:14px; cursor:pointer;">
                Save &amp; Manage Lessons →
            </button>
            <a href="{{ route('elearning.courses.index') }}"
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
})();
</script>

{{-- Assessment Summary (Phase 6) --}}
@include('elearning.courses.partials.assessment-summary')

@endsection
