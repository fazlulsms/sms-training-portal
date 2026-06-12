@extends('layouts.app')
@section('page-title', 'Create Prompt Template')
@section('content')

<x-page-header title="Create Prompt Template" desc="Define a new AI prompt template for the SMS Training Academy.">
    <x-slot:actions>
        <a href="{{ route('ai.prompt-templates.index') }}" class="btn btn-secondary btn-sm">← Back to Templates</a>
    </x-slot:actions>
</x-page-header>

<form method="POST" action="{{ route('ai.prompt-templates.store') }}">
    @csrf

    <div style="display:grid; grid-template-columns:2fr 1fr; gap:20px; align-items:start;">

        {{-- ── Left column: prompt content ──────────────────── --}}
        <div style="display:flex; flex-direction:column; gap:18px;">

            {{-- Basic Info --}}
            <div style="background:#fff; border:1px solid #e9ecf0; border-radius:12px; padding:22px;">
                <h3 style="font-size:14px; font-weight:800; color:#111827; margin:0 0 16px;">Basic Information</h3>

                <div style="margin-bottom:14px;">
                    <label style="font-size:12.5px; font-weight:700; color:#374151; display:block; margin-bottom:5px;">Template Name <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="template_name" value="{{ old('template_name') }}" required
                           placeholder="e.g. Course Generator"
                           style="width:100%; padding:9px 12px; border:1.5px solid {{ $errors->has('template_name') ? '#ef4444' : '#d1d5db' }}; border-radius:7px; font-size:13.5px; box-sizing:border-box;">
                    @error('template_name')<p style="font-size:12px; color:#ef4444; margin:4px 0 0;">{{ $message }}</p>@enderror
                </div>

                <div style="margin-bottom:14px;">
                    <label style="font-size:12.5px; font-weight:700; color:#374151; display:block; margin-bottom:5px;">Template Code <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="template_code" value="{{ old('template_code') }}" required
                           placeholder="e.g. course_generator_v2 (unique, alphanumeric + underscore)"
                           style="width:100%; padding:9px 12px; border:1.5px solid {{ $errors->has('template_code') ? '#ef4444' : '#d1d5db' }}; border-radius:7px; font-size:13.5px; font-family:monospace; box-sizing:border-box;">
                    <p style="font-size:11.5px; color:#9ca3af; margin:4px 0 0;">Must be unique. Use lowercase letters, numbers, and underscores only.</p>
                    @error('template_code')<p style="font-size:12px; color:#ef4444; margin:4px 0 0;">{{ $message }}</p>@enderror
                </div>

                <div style="margin-bottom:14px;">
                    <label style="font-size:12.5px; font-weight:700; color:#374151; display:block; margin-bottom:5px;">Category <span style="color:#ef4444;">*</span></label>
                    <select name="category" required style="width:100%; padding:9px 12px; border:1.5px solid #d1d5db; border-radius:7px; font-size:13.5px; box-sizing:border-box;">
                        <option value="">Select a category…</option>
                        @foreach($categories as $group => $items)
                            <optgroup label="{{ $group }}">
                                @foreach($items as $key => $label)
                                    <option value="{{ $group }}" {{ old('category') === $group ? 'selected' : '' }}>{{ $group }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                        @foreach(array_keys($categories) as $group)
                        @endforeach
                    </select>
                    @error('category')<p style="font-size:12px; color:#ef4444; margin:4px 0 0;">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label style="font-size:12.5px; font-weight:700; color:#374151; display:block; margin-bottom:5px;">Description</label>
                    <textarea name="description" rows="2" placeholder="Brief description of what this template generates…"
                              style="width:100%; padding:9px 12px; border:1.5px solid #d1d5db; border-radius:7px; font-size:13.5px; resize:vertical; box-sizing:border-box;">{{ old('description') }}</textarea>
                </div>
            </div>

            {{-- System Prompt --}}
            <div style="background:#fff; border:1px solid #e9ecf0; border-radius:12px; padding:22px;">
                <h3 style="font-size:14px; font-weight:800; color:#111827; margin:0 0 6px;">System Prompt <span style="color:#ef4444;">*</span></h3>
                <p style="font-size:12px; color:#6b7280; margin:0 0 12px;">
                    Role and behavioural instructions for the AI. The global <strong>SMS Master System Prompt</strong> is automatically prepended — you do not need to repeat it here.
                </p>
                <textarea name="system_prompt" required rows="8"
                          placeholder="You are an expert curriculum designer specialising in…"
                          style="width:100%; padding:10px 12px; border:1.5px solid {{ $errors->has('system_prompt') ? '#ef4444' : '#d1d5db' }}; border-radius:7px; font-size:13px; font-family:monospace; resize:vertical; box-sizing:border-box; line-height:1.6;">{{ old('system_prompt') }}</textarea>
                @error('system_prompt')<p style="font-size:12px; color:#ef4444; margin:4px 0 0;">{{ $message }}</p>@enderror
            </div>

            {{-- User Prompt Template --}}
            <div style="background:#fff; border:1px solid #e9ecf0; border-radius:12px; padding:22px;">
                <h3 style="font-size:14px; font-weight:800; color:#111827; margin:0 0 6px;">User Prompt Template <span style="color:#ef4444;">*</span></h3>
                <p style="font-size:12px; color:#6b7280; margin:0 0 12px;">
                    The prompt sent as the user message. Use <code style="background:#f1f5f9; padding:1px 5px; border-radius:3px;">{input}</code> as the variable placeholder — it will be replaced with the user's test input or feature-specific data.
                </p>
                <textarea name="user_prompt_template" required rows="12"
                          placeholder="Create a complete professional training course for:&#10;&#10;{input}&#10;&#10;Please provide:&#10;1. COURSE OVERVIEW&#10;…"
                          style="width:100%; padding:10px 12px; border:1.5px solid {{ $errors->has('user_prompt_template') ? '#ef4444' : '#d1d5db' }}; border-radius:7px; font-size:13px; font-family:monospace; resize:vertical; box-sizing:border-box; line-height:1.6;">{{ old('user_prompt_template') }}</textarea>
                @error('user_prompt_template')<p style="font-size:12px; color:#ef4444; margin:4px 0 0;">{{ $message }}</p>@enderror
            </div>

            {{-- Output Format --}}
            <div style="background:#fff; border:1px solid #e9ecf0; border-radius:12px; padding:22px;">
                <h3 style="font-size:14px; font-weight:800; color:#111827; margin:0 0 6px;">Output Format Instructions</h3>
                <p style="font-size:12px; color:#6b7280; margin:0 0 12px;">Optional. Appended after the user prompt — use to enforce format, tone, or length constraints.</p>
                <textarea name="output_format_instructions" rows="4"
                          placeholder="Use clear headings and numbered lists. Be specific and practical. All content must be suitable for professional paid training delivery."
                          style="width:100%; padding:10px 12px; border:1.5px solid #d1d5db; border-radius:7px; font-size:13px; font-family:monospace; resize:vertical; box-sizing:border-box; line-height:1.6;">{{ old('output_format_instructions') }}</textarea>
            </div>

        </div>

        {{-- ── Right column: settings & submit ──────────────── --}}
        <div style="display:flex; flex-direction:column; gap:18px;">

            {{-- Model Settings --}}
            <div style="background:#fff; border:1px solid #e9ecf0; border-radius:12px; padding:22px;">
                <h3 style="font-size:14px; font-weight:800; color:#111827; margin:0 0 16px;">Model Settings</h3>
                <p style="font-size:12px; color:#6b7280; margin:0 0 14px;">Leave blank to use the global AI model setting.</p>

                <div style="margin-bottom:14px;">
                    <label style="font-size:12.5px; font-weight:700; color:#374151; display:block; margin-bottom:5px;">Model Override</label>
                    <select name="model_override" style="width:100%; padding:9px 12px; border:1.5px solid #d1d5db; border-radius:7px; font-size:13px; box-sizing:border-box;">
                        <option value="">Default ({{ config('ai.model', 'gpt-4o-mini') }})</option>
                        <option value="gpt-4o-mini"   {{ old('model_override') === 'gpt-4o-mini'   ? 'selected' : '' }}>gpt-4o-mini (fast, cheap)</option>
                        <option value="gpt-4o"        {{ old('model_override') === 'gpt-4o'        ? 'selected' : '' }}>gpt-4o (most capable)</option>
                        <option value="gpt-4-turbo"   {{ old('model_override') === 'gpt-4-turbo'   ? 'selected' : '' }}>gpt-4-turbo</option>
                        <option value="gpt-3.5-turbo" {{ old('model_override') === 'gpt-3.5-turbo' ? 'selected' : '' }}>gpt-3.5-turbo (legacy)</option>
                    </select>
                </div>

                <div style="margin-bottom:14px;">
                    <label style="font-size:12.5px; font-weight:700; color:#374151; display:block; margin-bottom:5px;">Temperature</label>
                    <input type="number" name="temperature" value="{{ old('temperature', '0.70') }}" step="0.05" min="0" max="2"
                           style="width:100%; padding:9px 12px; border:1.5px solid #d1d5db; border-radius:7px; font-size:13px; box-sizing:border-box;">
                    <p style="font-size:11.5px; color:#9ca3af; margin:4px 0 0;">0 = deterministic, 1 = creative, 2 = very random. Default: 0.70</p>
                </div>

                <div>
                    <label style="font-size:12.5px; font-weight:700; color:#374151; display:block; margin-bottom:5px;">Max Tokens</label>
                    <input type="number" name="max_tokens" value="{{ old('max_tokens', '2000') }}" min="100" max="16000"
                           style="width:100%; padding:9px 12px; border:1.5px solid #d1d5db; border-radius:7px; font-size:13px; box-sizing:border-box;">
                    <p style="font-size:11.5px; color:#9ca3af; margin:4px 0 0;">Maximum response length in tokens. Default: 2000</p>
                </div>
            </div>

            {{-- Status --}}
            <div style="background:#fff; border:1px solid #e9ecf0; border-radius:12px; padding:22px;">
                <h3 style="font-size:14px; font-weight:800; color:#111827; margin:0 0 14px;">Status</h3>
                <label style="display:flex; align-items:center; gap:10px; cursor:pointer;">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}
                           style="width:16px; height:16px; cursor:pointer;">
                    <div>
                        <div style="font-size:13.5px; font-weight:700; color:#111827;">Active</div>
                        <div style="font-size:12px; color:#6b7280;">Inactive templates cannot be tested or used in production.</div>
                    </div>
                </label>
            </div>

            {{-- Master Prompt preview --}}
            <div style="background:#fffbeb; border:1px solid #fde68a; border-radius:12px; padding:18px;">
                <h3 style="font-size:13px; font-weight:800; color:#92400e; margin:0 0 8px;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle; margin-right:4px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    SMS Master Prompt Active
                </h3>
                <p style="font-size:12px; color:#92400e; margin:0;">The global SMS Master System Prompt is automatically prepended to your system prompt at runtime. You do not need to add it manually.</p>
            </div>

            {{-- Submit --}}
            <div style="background:#fff; border:1px solid #e9ecf0; border-radius:12px; padding:18px; display:flex; flex-direction:column; gap:10px;">
                <button type="submit" style="background:#1e3a8a; color:#fff; padding:11px; border:none; border-radius:8px; font-weight:800; font-size:14px; cursor:pointer; width:100%;">
                    Create Template
                </button>
                <a href="{{ route('ai.prompt-templates.index') }}" style="text-align:center; padding:10px; background:#f1f5f9; color:#374151; border-radius:8px; font-size:13.5px; font-weight:600; text-decoration:none; display:block;">
                    Cancel
                </a>
            </div>

        </div>
    </div>
</form>

<script>
// Auto-generate template_code from template_name
document.querySelector('[name=template_name]').addEventListener('input', function() {
    const codeField = document.querySelector('[name=template_code]');
    if (codeField.dataset.userEdited) return;
    codeField.value = this.value
        .toLowerCase()
        .replace(/[^a-z0-9\s]/g, '')
        .replace(/\s+/g, '_')
        .replace(/_+/g, '_')
        .slice(0, 80);
});
document.querySelector('[name=template_code]').addEventListener('input', function() {
    this.dataset.userEdited = 'true';
});
</script>

@endsection
