@extends('layouts.app')
@section('page-title', 'AI Course Preview')
@section('content')

<style>
.preview-section { background:#fff; border:1px solid #e9ecf0; border-radius:12px; padding:22px; margin-bottom:18px; }
.preview-section h3 { font-size:14px; font-weight:800; color:#111827; margin:0 0 14px; display:flex; align-items:center; gap:8px; }
.preview-label { font-size:11.5px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#6b7280; margin-bottom:5px; display:block; }
.preview-ta {
    width:100%; padding:10px 12px; border:1.5px solid #e9ecf0; border-radius:7px;
    font-size:13.5px; font-family:inherit; resize:vertical; box-sizing:border-box;
    line-height:1.6; background:#fafbfc; transition:border-color .15s;
}
.preview-ta:focus { border-color:#1e3a8a; outline:none; background:#fff; }
.preview-inp {
    width:100%; padding:9px 12px; border:1.5px solid #e9ecf0; border-radius:7px;
    font-size:13.5px; font-family:inherit; box-sizing:border-box; background:#fafbfc; transition:border-color .15s;
}
.preview-inp:focus { border-color:#1e3a8a; outline:none; background:#fff; }
.module-card { background:#f8fafc; border:1px solid #f0f2f5; border-radius:9px; padding:14px; margin-bottom:10px; }
.lesson-pill { display:inline-flex; align-items:center; gap:5px; background:#fff; border:1px solid #e9ecf0;
               padding:5px 11px; border-radius:20px; font-size:12.5px; color:#374151; margin:3px; }
</style>

{{-- ── Page Header ─────────────────────────────────────────── --}}
<div style="background:linear-gradient(135deg,#0f1e45,#1e3a8a); border-radius:12px; padding:18px 24px; margin-bottom:22px; display:flex; justify-content:space-between; align-items:center;">
    <div>
        <div style="font-size:18px; font-weight:800; color:#fff;">✨ AI Course Preview</div>
        <div style="font-size:13px; color:#93c5fd; margin-top:3px;">
            Review and edit the generated content before saving as a draft course.
        </div>
    </div>
    <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
        <span style="background:rgba(255,255,255,.15); color:#fff; padding:5px 12px; border-radius:20px; font-size:12px; font-weight:700;">
            ✨ AI Generated Draft
        </span>
        <span style="background:rgba(255,255,255,.1); color:#93c5fd; padding:5px 12px; border-radius:20px; font-size:12px;">
            {{ strtoupper($courseType) }} · {{ $formData['language'] }}
        </span>
        @if(!empty($aiUsage['model']))
        <span style="background:rgba(255,255,255,.1); color:#93c5fd; padding:5px 12px; border-radius:20px; font-size:12px;">
            {{ $aiUsage['model'] }} · ${{ number_format($aiUsage['estimated_cost'] ?? 0, 4) }}
        </span>
        @endif
    </div>
</div>

@if(session('error'))
<div style="background:#fee2e2; border:1px solid #fca5a5; border-radius:8px; padding:12px 16px; margin-bottom:16px; font-size:13.5px; color:#b91c1c;">
    {{ session('error') }}
</div>
@endif

<form method="POST" action="{{ route('ai.course-generator.save') }}" id="previewForm">
@csrf

{{-- ── Course Info Banner ──────────────────────────────────── --}}
<div style="background:#eff6ff; border:1px solid #bfdbfe; border-radius:10px; padding:14px 20px; margin-bottom:18px; display:grid; grid-template-columns:repeat(auto-fit,minmax(150px,1fr)); gap:12px;">
    <div><span style="font-size:11px; color:#6b7280; display:block; text-transform:uppercase; letter-spacing:.5px;">Course</span><strong style="font-size:14px; color:#1e3a8a;">{{ $formData['course_name'] }}</strong></div>
    <div><span style="font-size:11px; color:#6b7280; display:block; text-transform:uppercase; letter-spacing:.5px;">Duration</span><strong style="font-size:14px; color:#1e3a8a;">{{ $formData['duration'] }}</strong></div>
    <div><span style="font-size:11px; color:#6b7280; display:block; text-transform:uppercase; letter-spacing:.5px;">Level</span><strong style="font-size:14px; color:#1e3a8a;">{{ $formData['learning_level'] }}</strong></div>
    <div><span style="font-size:11px; color:#6b7280; display:block; text-transform:uppercase; letter-spacing:.5px;">Industry</span><strong style="font-size:14px; color:#1e3a8a;">{{ $formData['industry'] }}</strong></div>
    @if(!empty($formData['standard']))
    <div><span style="font-size:11px; color:#6b7280; display:block; text-transform:uppercase; letter-spacing:.5px;">Standard</span><strong style="font-size:14px; color:#1e3a8a;">{{ $formData['standard'] }}</strong></div>
    @endif
</div>

<div style="display:grid; grid-template-columns:2fr 1fr; gap:20px; align-items:start;">

{{-- ── Left Column ─────────────────────────────────────────── --}}
<div>

{{-- Course Description --}}
<div class="preview-section">
    <h3>
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#1e3a8a" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        Course Description
    </h3>
    <span class="preview-label">Edit as needed</span>
    <textarea name="course_description" class="preview-ta" rows="5">{{ $aiOutput['course_description'] ?? '' }}</textarea>
</div>

{{-- Learning Objectives --}}
<div class="preview-section">
    <h3>
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
        Learning Objectives
    </h3>
    <span class="preview-label">One objective per line</span>
    <textarea name="learning_objectives" class="preview-ta" rows="7">{{ is_array($aiOutput['learning_objectives'] ?? '') ? implode("\n", $aiOutput['learning_objectives']) : ($aiOutput['learning_objectives'] ?? '') }}</textarea>
</div>

{{-- Target Audience & Prerequisites --}}
<div class="preview-section">
    <h3>
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        Target Audience & Prerequisites
    </h3>
    <div style="margin-bottom:14px;">
        <span class="preview-label">Target Audience</span>
        <input type="text" name="target_audience" class="preview-inp" value="{{ $aiOutput['target_audience'] ?? '' }}">
    </div>
    <div>
        <span class="preview-label">Prerequisites (one per line)</span>
        <textarea name="prerequisites" class="preview-ta" rows="3">{{ is_array($aiOutput['prerequisites'] ?? '') ? implode("\n", $aiOutput['prerequisites']) : ($aiOutput['prerequisites'] ?? '') }}</textarea>
    </div>
</div>

{{-- Modules & Lessons (read-only display) --}}
<div class="preview-section">
    <h3>
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
        Course Structure
        <span style="font-size:11.5px; font-weight:400; color:#9ca3af; margin-left:4px;">(edit individual lessons after saving)</span>
    </h3>

    @php
        $modules = $aiOutput['modules'] ?? [];
        $totalLessons = collect($modules)->sum(fn($m) => count($m['lessons'] ?? []));
    @endphp

    <div style="display:flex; gap:12px; margin-bottom:14px; flex-wrap:wrap;">
        <span style="background:#eff6ff; color:#1e3a8a; padding:4px 12px; border-radius:20px; font-size:12.5px; font-weight:700;">
            {{ count($modules) }} Modules
        </span>
        <span style="background:#f0fdf4; color:#16a34a; padding:4px 12px; border-radius:20px; font-size:12.5px; font-weight:700;">
            {{ $totalLessons }} Lessons
        </span>
        @if($courseType === 'elearning')
        <span style="background:#fef3c7; color:#d97706; padding:4px 12px; border-radius:20px; font-size:12.5px; font-weight:700;">
            {{ $totalLessons }} eLearning lesson records will be created
        </span>
        @endif
    </div>

    @foreach($modules as $moduleIndex => $module)
    <div class="module-card">
        <div style="font-weight:800; color:#1e3a8a; font-size:13.5px; margin-bottom:8px; display:flex; justify-content:space-between;">
            <span>{{ $module['title'] ?? 'Module ' . ($moduleIndex + 1) }}</span>
            @if(!empty($module['duration']))
            <span style="font-size:12px; color:#6b7280; font-weight:400;">{{ $module['duration'] }}</span>
            @endif
        </div>
        <div>
            @foreach($module['lessons'] ?? [] as $lesson)
            <span class="lesson-pill">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                {{ $lesson['title'] ?? 'Lesson' }}
            </span>
            @endforeach
        </div>
    </div>
    @endforeach
</div>

{{-- Assessment & Certificate --}}
<div class="preview-section">
    <h3>
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#ea580c" stroke-width="2"><circle cx="12" cy="8" r="7"/><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/></svg>
        Assessment & Certificate
    </h3>
    <div style="margin-bottom:14px;">
        <span class="preview-label">Assessment Plan</span>
        <textarea name="assessment_plan" class="preview-ta" rows="4">{{ $aiOutput['assessment_plan'] ?? '' }}</textarea>
    </div>
    <div>
        <span class="preview-label">Certificate Criteria</span>
        <textarea name="certificate_criteria" class="preview-ta" rows="3">{{ $aiOutput['certificate_criteria'] ?? '' }}</textarea>
    </div>
</div>

{{-- Public Summary & SEO --}}
<div class="preview-section">
    <h3>
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#0891b2" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        Public Summary & SEO
    </h3>
    <div style="margin-bottom:14px;">
        <span class="preview-label">Public Website Summary</span>
        <textarea name="public_summary" class="preview-ta" rows="3">{{ $aiOutput['public_summary'] ?? '' }}</textarea>
    </div>
    <div style="margin-bottom:14px;">
        <span class="preview-label">SEO Title <span style="color:#9ca3af; font-weight:400;">(max 60 chars)</span></span>
        <input type="text" name="seo_title" class="preview-inp" maxlength="70" value="{{ $aiOutput['seo_title'] ?? '' }}"
               oninput="document.getElementById('seoTitleCount').textContent=this.value.length">
        <span style="font-size:11.5px; color:#9ca3af;" id="seoTitleCount">{{ strlen($aiOutput['seo_title'] ?? '') }}</span> / 60
    </div>
    <div>
        <span class="preview-label">SEO Meta Description <span style="color:#9ca3af; font-weight:400;">(max 160 chars)</span></span>
        <textarea name="seo_meta_description" class="preview-ta" rows="2" maxlength="170"
                  oninput="document.getElementById('seoDescCount').textContent=this.value.length">{{ $aiOutput['seo_meta_description'] ?? '' }}</textarea>
        <span style="font-size:11.5px; color:#9ca3af;" id="seoDescCount">{{ strlen($aiOutput['seo_meta_description'] ?? '') }}</span> / 160
    </div>
</div>

</div>{{-- end left column --}}

{{-- ── Right Column: Actions ──────────────────────────────── --}}
<div>
    <div style="position:sticky; top:20px; display:flex; flex-direction:column; gap:12px;">

        {{-- AI Badge --}}
        <div style="background:linear-gradient(135deg,#0f1e45,#1e3a8a); border-radius:12px; padding:16px 18px; color:#fff; text-align:center;">
            <div style="font-size:22px; margin-bottom:4px;">✨</div>
            <div style="font-size:14px; font-weight:800;">AI Generated Draft</div>
            <div style="font-size:12px; color:#93c5fd; margin-top:2px;">{{ count($aiOutput['modules'] ?? []) }} modules · {{ collect($aiOutput['modules'] ?? [])->sum(fn($m) => count($m['lessons'] ?? [])) }} lessons</div>
        </div>

        {{-- Token usage card --}}
        @if(!empty($aiUsage['total_tokens']))
        <div style="background:#f0f4ff; border:1px solid #c7d2fe; border-radius:10px; padding:14px; font-size:12.5px;">
            <div style="font-weight:700; color:#1e3a8a; margin-bottom:8px;">AI Usage</div>
            <div style="display:flex; justify-content:space-between; margin-bottom:4px;"><span style="color:#6b7280;">Tokens used</span><strong>{{ number_format($aiUsage['total_tokens']) }}</strong></div>
            <div style="display:flex; justify-content:space-between; margin-bottom:4px;"><span style="color:#6b7280;">Est. cost</span><strong>${{ number_format($aiUsage['estimated_cost'] ?? 0, 5) }}</strong></div>
            @if(!empty($aiUsage['response_time_ms']))
            <div style="display:flex; justify-content:space-between;"><span style="color:#6b7280;">Response time</span><strong>{{ $aiUsage['response_time_ms'] }}ms</strong></div>
            @endif
        </div>
        @endif

        {{-- Actions --}}
        <div style="background:#fff; border:1px solid #e9ecf0; border-radius:12px; padding:16px; display:flex; flex-direction:column; gap:10px;">
            <div style="font-size:13px; font-weight:700; color:#374151; margin-bottom:4px;">What would you like to do?</div>

            {{-- Approve & Save --}}
            <button type="submit" name="action" value="approve"
                    style="display:flex; align-items:center; justify-content:center; gap:8px; padding:12px;
                           background:#16a34a; color:#fff; border:none; border-radius:9px; font-size:14px; font-weight:800; cursor:pointer; width:100%;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                Approve & Save Draft
            </button>
            <p style="font-size:11.5px; color:#6b7280; margin:-4px 0 4px; text-align:center;">Saves course + creates lessons as drafts</p>

            {{-- Regenerate --}}
            <a href="{{ $courseType === 'elearning' ? route('elearning.courses.create') : url('/admin/courses/create') }}?regenerate=1"
               style="display:flex; align-items:center; justify-content:center; gap:8px; padding:11px;
                      background:#fff7ed; color:#ea580c; border:1px solid #fed7aa; border-radius:9px;
                      font-size:13.5px; font-weight:700; text-decoration:none; text-align:center;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.1"/></svg>
                Regenerate
            </a>

            {{-- Cancel --}}
            <form method="POST" action="{{ route('ai.course-generator.cancel') }}">
                @csrf
                <input type="hidden" name="course_type" value="{{ $courseType }}">
                <button type="submit"
                        style="width:100%; padding:11px; background:#f1f5f9; color:#374151; border:none; border-radius:9px;
                               font-size:13.5px; font-weight:600; cursor:pointer;">
                    Cancel
                </button>
            </form>
        </div>

        {{-- Editing tip --}}
        <div style="background:#fffbeb; border:1px solid #fde68a; border-radius:9px; padding:13px 14px;">
            <div style="font-size:12px; font-weight:700; color:#92400e; margin-bottom:4px;">Editing Tips</div>
            <ul style="margin:0; padding-left:16px; font-size:12px; color:#78350f; line-height:1.7;">
                <li>All text fields above are editable</li>
                <li>Modules & lessons are saved as-is</li>
                <li>Edit individual lessons after saving</li>
                <li>Course is saved as <strong>Draft</strong> — publish manually when ready</li>
            </ul>
        </div>
    </div>
</div>

</div>{{-- end grid --}}
</form>

@endsection
