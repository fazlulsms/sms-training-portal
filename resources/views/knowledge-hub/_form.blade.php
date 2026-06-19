@php
    $editing = isset($resource);
    $value = fn (string $field, $default = '') => old($field, $editing ? $resource->{$field} : $default);
@endphp

@if($errors->any())
    <div class="alert alert-error">
        <div>
            <strong>Please correct the highlighted information.</strong>
            <ul style="margin:6px 0 0 18px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<div class="card">
    <div class="card-header">
        <h3>Resource Information</h3>
    </div>
    <div class="card-body">
        <div class="kh-form-grid">
            <div class="kh-field kh-span-2">
                <label for="title">Title <span>*</span></label>
                <input id="title" name="title" type="text" value="{{ $value('title') }}" required maxlength="255">
            </div>

            <div class="kh-field">
                <label for="resource_type">Resource Type <span>*</span></label>
                <select id="resource_type" name="resource_type" required>
                    <option value="">Select type</option>
                    @foreach(\App\Models\KnowledgeResource::RESOURCE_TYPES as $type)
                        <option value="{{ $type }}" @selected($value('resource_type') === $type)>{{ $type }}</option>
                    @endforeach
                </select>
            </div>

            <div class="kh-field">
                <label for="category">Category <span>*</span></label>
                <select id="category" name="category" required>
                    <option value="">Select category</option>
                    @foreach(\App\Models\KnowledgeResource::CATEGORIES as $category)
                        <option value="{{ $category }}" @selected($value('category') === $category)>{{ $category }}</option>
                    @endforeach
                </select>
            </div>

            <div class="kh-field">
                <label for="subcategory">Subcategory</label>
                <input id="subcategory" name="subcategory" type="text" value="{{ $value('subcategory') }}" maxlength="120">
            </div>

            <div class="kh-field">
                <label for="standard_framework">Standard / Framework <span>*</span></label>
                <input id="standard_framework" name="standard_framework" type="text"
                       value="{{ $value('standard_framework') }}" required maxlength="150"
                       placeholder="e.g. ISO 9001:2015">
            </div>

            <div class="kh-field">
                <label for="version">Version</label>
                <input id="version" name="version" type="text" value="{{ $value('version') }}"
                       maxlength="50" placeholder="e.g. 2024 or Rev. 2">
            </div>

            <div class="kh-field">
                <label for="clause_number">Clause Number</label>
                <input id="clause_number" name="clause_number" type="text" value="{{ $value('clause_number') }}" maxlength="80" placeholder="e.g. 4.1–4.4">
            </div>

            <div class="kh-field">
                <label for="difficulty_level">Difficulty Level <span>*</span></label>
                <select id="difficulty_level" name="difficulty_level" required>
                    @foreach(['beginner','intermediate','advanced','expert'] as $level)
                        <option value="{{ $level }}" @selected($value('difficulty_level', 'intermediate') === $level)>{{ ucfirst($level) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="kh-field">
                <label for="status">Status <span>*</span></label>
                <select id="status" name="status" required>
                    @foreach(\App\Models\KnowledgeResource::STATUSES as $status)
                        <option value="{{ $status }}" @selected($value('status', 'draft') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
                <small>Only Approved resources are eligible for future AI use.</small>
            </div>

            <div class="kh-field">
                <label for="file">Uploaded File {{ $editing ? '' : '*' }}</label>
                <input id="file" name="file" type="file" {{ $editing ? '' : 'required' }}
                       accept=".pdf,.docx,.doc,.pptx,.xlsx,.txt,.jpg,.jpeg,.png,.mp4">
                <small>
                    PDF, DOCX, DOC, PPTX, XLSX, TXT, JPG, PNG or MP4. Maximum 100 MB.
                    @if($editing)
                        Leave blank to keep {{ $resource->original_file_name }}.
                    @endif
                </small>
            </div>

            <div class="kh-field kh-span-2">
                <label for="notes">Notes / Remarks</label>
                <textarea id="notes" name="notes" rows="6" maxlength="10000"
                          placeholder="Add context, intended use, source details, or review notes.">{{ $value('notes') }}</textarea>
            </div>

            <div class="kh-field kh-span-2">
                <label for="learning_objectives">Learning Objectives</label>
                <textarea id="learning_objectives" name="learning_objectives" rows="4">{{ $value('learning_objectives') }}</textarea>
            </div>

            <div class="kh-field kh-span-2">
                <label for="source_references">Source References</label>
                <textarea id="source_references" name="source_references" rows="3">{{ $value('source_references') }}</textarea>
            </div>

            <div class="kh-field kh-span-2">
                <label for="extracted_text">Machine-Readable Source Text</label>
                <textarea id="extracted_text" name="extracted_text" rows="10" placeholder="Automatically extracted from supported documents. For images or video, enter an approved transcript or source text here.">{{ $value('extracted_text') }}</textarea>
                <small>V2 generation uses this text only. Review it before approving the resource.</small>
            </div>
        </div>
    </div>
</div>

<div style="display:flex;justify-content:flex-end;gap:10px;margin-top:16px;">
    <a href="{{ $editing ? route('knowledge-hub.show', $resource) : route('knowledge-hub.index') }}" class="btn btn-ghost">Cancel</a>
    <button type="submit" class="btn btn-primary">{{ $editing ? 'Save Changes' : 'Create Resource' }}</button>
</div>

<style>
    .kh-form-grid { display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px; }
    .kh-span-2 { grid-column:span 2; }
    .kh-field { display:flex;flex-direction:column;gap:6px; }
    .kh-field label { font-size:12px;font-weight:700;color:#374151; }
    .kh-field label span { color:#dc2626; }
    .kh-field input,.kh-field select,.kh-field textarea {
        width:100%;border:1px solid #d1d5db;border-radius:9px;padding:10px 12px;
        font:inherit;font-size:13.5px;background:#fff;color:#111827;
    }
    .kh-field input:focus,.kh-field select:focus,.kh-field textarea:focus {
        outline:none;border-color:#042c53;box-shadow:0 0 0 3px rgba(4,44,83,.1);
    }
    .kh-field small { color:#6b7280;font-size:11.5px;line-height:1.45; }
    @media(max-width:720px) {
        .kh-form-grid { grid-template-columns:1fr; }
        .kh-span-2 { grid-column:span 1; }
    }
</style>
