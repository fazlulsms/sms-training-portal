{{--
    Shared lesson fields (used by create form + lesson info section in builder).
    Expects: $course, $lesson (or null), $lessonTypes (optional — controller passes it on edit)
--}}

@php
    $lt      = $lesson ?? null;
    $ltypes  = $lessonTypes ?? \App\Models\ElearningLesson::lessonTypes();
    $crules  = \App\Models\ElearningLesson::completionRules();
@endphp

<div class="lf-grid">

    {{-- ── Row 1: Lesson Title ──────────────────────────── --}}
    <div class="lf-full">
        <label class="form-label">Lesson Title <span class="req">*</span></label>
        <input type="text" name="title"
               value="{{ old('title', $lt->title ?? '') }}"
               class="form-input" required placeholder="e.g. Introduction to the Course">
        @error('title')<div class="form-error">{{ $message }}</div>@enderror
    </div>

    {{-- ── Row 2: Short Description ─────────────────────── --}}
    <div class="lf-full">
        <label class="form-label">Short Description
            <span class="hint">— shown in the lesson list</span>
        </label>
        <textarea name="short_description" class="form-input" rows="2"
                  placeholder="Brief one-line description of this lesson">{{ old('short_description', $lt->short_description ?? '') }}</textarea>
        @error('short_description')<div class="form-error">{{ $message }}</div>@enderror
    </div>

    {{-- ── Row 3: Learning Objectives ──────────────────── --}}
    <div class="lf-full">
        <label class="form-label">Learning Objectives
            <span class="hint">— what participants will be able to do after this lesson</span>
        </label>
        <textarea name="learning_objectives" class="form-input" rows="3"
                  placeholder="• Understand the core principles&#10;• Apply the framework in real scenarios&#10;• Evaluate outcomes">{{ old('learning_objectives', $lt->learning_objectives ?? '') }}</textarea>
        @error('learning_objectives')<div class="form-error">{{ $message }}</div>@enderror
    </div>

    {{-- ── Row 4: Lesson Order + Estimated Learning Time ── --}}
    <div class="lf-half">
        <label class="form-label">Lesson Order <span class="req">*</span></label>
        <input type="number" name="lesson_order" min="1"
               value="{{ old('lesson_order', $lt->lesson_order ?? 1) }}"
               class="form-input" required>
        @error('lesson_order')<div class="form-error">{{ $message }}</div>@enderror
    </div>
    <div class="lf-half">
        <label class="form-label">Estimated Learning Time
            <span class="hint">— minutes</span>
        </label>
        <input type="number" name="duration_minutes" min="1"
               value="{{ old('duration_minutes', $lt->duration_minutes ?? '') }}"
               class="form-input" placeholder="e.g. 15">
        @error('duration_minutes')<div class="form-error">{{ $message }}</div>@enderror
    </div>

    {{-- ── Row 5: Lesson Type ───────────────────────────── --}}
    <div class="lf-full">
        <label class="form-label">Lesson Type</label>
        <select name="lesson_type" class="form-input">
            @foreach($ltypes as $value => $label)
                <option value="{{ $value }}"
                    {{ old('lesson_type', $lt->lesson_type ?? 'mixed') === $value ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        @error('lesson_type')<div class="form-error">{{ $message }}</div>@enderror
    </div>

    {{-- ── Row 6: Completion Method + Required Passing Score --}}
    <div class="lf-half">
        <label class="form-label">Completion Method</label>
        <select name="completion_rule" class="form-input">
            @foreach($crules as $value => $label)
                <option value="{{ $value }}"
                    {{ old('completion_rule', $lt->completion_rule ?? 'manual') === $value ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        @error('completion_rule')<div class="form-error">{{ $message }}</div>@enderror
    </div>
    <div class="lf-half">
        <label class="form-label">Required Passing Score
            <span class="hint">— % (only applies when Completion Method = Pass Quiz)</span>
        </label>
        <input type="number" name="required_passing_score" min="1" max="100"
               value="{{ old('required_passing_score', $lt->required_passing_score ?? '') }}"
               class="form-input" placeholder="e.g. 80">
        @error('required_passing_score')<div class="form-error">{{ $message }}</div>@enderror
    </div>

    {{-- ── Row 7: Certificate Rule + Status ───────────────── --}}
    <div class="lf-half">
        <label class="form-label">Certificate Rule</label>
        <select name="certificate_eligible" class="form-input">
            <option value="1" {{ old('certificate_eligible', ($lt->certificate_eligible ?? true) ? '1' : '0') == '1' ? 'selected' : '' }}>
                Required — Must complete for certificate
            </option>
            <option value="0" {{ old('certificate_eligible', ($lt->certificate_eligible ?? true) ? '1' : '0') == '0' ? 'selected' : '' }}>
                Optional — Bonus / supplementary lesson
            </option>
        </select>
        @error('certificate_eligible')<div class="form-error">{{ $message }}</div>@enderror
    </div>
    <div class="lf-half">
        <label class="form-label">Status <span class="req">*</span></label>
        <select name="status" class="form-input">
            <option value="active"   {{ old('status', $lt->status ?? 'active') === 'active'   ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ old('status', $lt->status ?? 'active') === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
        @error('status')<div class="form-error">{{ $message }}</div>@enderror
    </div>

    {{-- ── Row 8: Legacy Video URL ──────────────────────── --}}
    <div class="lf-full">
        <label class="form-label lf-legacy-label">
            Legacy Video URL
            <span class="lf-legacy-badge">Old lessons only — use a Video Block instead</span>
        </label>
        <input type="text" name="legacy_video_url"
               value="{{ old('legacy_video_url', $lt->video_url ?? '') }}"
               class="form-input" placeholder="YouTube, Vimeo, or direct .mp4 URL">
        @error('legacy_video_url')<div class="form-error">{{ $message }}</div>@enderror
    </div>

    {{-- ── Row 9: Legacy Lesson Notes ───────────────────── --}}
    <div class="lf-full">
        <label class="form-label lf-legacy-label">
            Legacy Lesson Notes
            <span class="lf-legacy-badge">Old lessons only — use a Rich Text Block instead</span>
        </label>
        <textarea name="legacy_lesson_notes" class="form-input" rows="3"
                  placeholder="Only shown when no content blocks exist on this lesson">{{ old('legacy_lesson_notes', $lt->lesson_content ?? '') }}</textarea>
        @error('legacy_lesson_notes')<div class="form-error">{{ $message }}</div>@enderror
    </div>

</div>{{-- /.lf-grid --}}

<style>
.lf-grid  { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
.lf-full  { grid-column:1 / -1; }
.lf-half  { grid-column:span 1; }

.form-label {
    display:block; font-size:13px; font-weight:700; color:#374151; margin-bottom:5px;
}
.form-label .hint {
    font-size:11.5px; color:#9ca3af; font-weight:400;
}
.form-label .req { color:#dc2626; }

.form-input {
    width:100%; border:1px solid #e5e7eb; border-radius:8px;
    padding:9px 12px; font-size:13.5px; color:#111827; font-family:inherit;
    background:#fff; transition:border-color .15s;
    box-sizing:border-box;
}
.form-input:focus {
    outline:none; border-color:#6366f1; box-shadow:0 0 0 3px rgba(99,102,241,.12);
}
.form-error { font-size:12px; color:#dc2626; margin-top:4px; font-weight:600; }

.lf-legacy-label { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
.lf-legacy-badge {
    font-size:11px; background:#fef3c7; color:#92400e;
    padding:2px 7px; border-radius:4px; font-weight:600; white-space:nowrap;
}

@media (max-width: 600px) {
    .lf-grid  { grid-template-columns:1fr; }
    .lf-half  { grid-column:1 / -1; }
}
</style>
