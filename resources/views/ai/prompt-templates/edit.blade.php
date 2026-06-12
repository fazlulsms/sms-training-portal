@extends('layouts.app')
@section('page-title', 'Edit: ' . $template->template_name)
@section('content')

<x-page-header title="Edit Template" desc="{{ $template->template_name }} — v{{ $template->version_number }}">
    <x-slot:actions>
        <a href="{{ route('ai.prompt-templates.show', $template) }}" class="btn btn-secondary btn-sm">View</a>
        <a href="{{ route('ai.prompt-templates.versions', $template) }}" class="btn btn-secondary btn-sm">History ({{ $template->versions->count() }} versions)</a>
        <a href="{{ route('ai.prompt-templates.index') }}" class="btn btn-secondary btn-sm">← Templates</a>
    </x-slot:actions>
</x-page-header>

<x-flash-message />

@if($errors->any())
<div style="background:#fee2e2; border:1px solid #fca5a5; border-radius:8px; padding:12px 16px; margin-bottom:16px; font-size:13px; color:#b91c1c;">
    <strong>Please fix the following errors:</strong>
    <ul style="margin:6px 0 0; padding-left:18px;">
        @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('ai.prompt-templates.update', $template) }}">
    @csrf
    @method('PUT')

    <div style="display:grid; grid-template-columns:2fr 1fr; gap:20px; align-items:start;">

        {{-- ── Left column ─────────────────────────────────── --}}
        <div style="display:flex; flex-direction:column; gap:18px;">

            {{-- Basic Info --}}
            <div style="background:#fff; border:1px solid #e9ecf0; border-radius:12px; padding:22px;">
                <h3 style="font-size:14px; font-weight:800; color:#111827; margin:0 0 16px;">Basic Information</h3>

                <div style="margin-bottom:14px;">
                    <label style="font-size:12.5px; font-weight:700; color:#374151; display:block; margin-bottom:5px;">Template Name <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="template_name" value="{{ old('template_name', $template->template_name) }}" required
                           style="width:100%; padding:9px 12px; border:1.5px solid #d1d5db; border-radius:7px; font-size:13.5px; box-sizing:border-box;">
                    @error('template_name')<p style="font-size:12px; color:#ef4444; margin:4px 0 0;">{{ $message }}</p>@enderror
                </div>

                <div style="margin-bottom:14px;">
                    <label style="font-size:12.5px; font-weight:700; color:#374151; display:block; margin-bottom:5px;">Template Code</label>
                    <input type="text" value="{{ $template->template_code }}" disabled
                           style="width:100%; padding:9px 12px; border:1.5px solid #e9ecf0; border-radius:7px; font-size:13px; font-family:monospace; background:#f8fafc; color:#6b7280; box-sizing:border-box;">
                    <p style="font-size:11.5px; color:#9ca3af; margin:4px 0 0;">Template code cannot be changed after creation.</p>
                </div>

                <div style="margin-bottom:14px;">
                    <label style="font-size:12.5px; font-weight:700; color:#374151; display:block; margin-bottom:5px;">Category <span style="color:#ef4444;">*</span></label>
                    <select name="category" required style="width:100%; padding:9px 12px; border:1.5px solid #d1d5db; border-radius:7px; font-size:13.5px; box-sizing:border-box;">
                        @foreach($categories as $group => $items)
                            <option value="{{ $group }}" {{ old('category', $template->category) === $group ? 'selected' : '' }}>{{ $group }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="font-size:12.5px; font-weight:700; color:#374151; display:block; margin-bottom:5px;">Description</label>
                    <textarea name="description" rows="2"
                              style="width:100%; padding:9px 12px; border:1.5px solid #d1d5db; border-radius:7px; font-size:13.5px; resize:vertical; box-sizing:border-box;">{{ old('description', $template->description) }}</textarea>
                </div>
            </div>

            {{-- System Prompt --}}
            <div style="background:#fff; border:1px solid #e9ecf0; border-radius:12px; padding:22px;">
                <h3 style="font-size:14px; font-weight:800; color:#111827; margin:0 0 6px;">System Prompt <span style="color:#ef4444;">*</span></h3>
                <p style="font-size:12px; color:#6b7280; margin:0 0 12px;">The global SMS Master System Prompt is automatically prepended — do not repeat it here.</p>
                <textarea name="system_prompt" required rows="8"
                          style="width:100%; padding:10px 12px; border:1.5px solid #d1d5db; border-radius:7px; font-size:13px; font-family:monospace; resize:vertical; box-sizing:border-box; line-height:1.6;">{{ old('system_prompt', $template->system_prompt) }}</textarea>
                @error('system_prompt')<p style="font-size:12px; color:#ef4444; margin:4px 0 0;">{{ $message }}</p>@enderror
            </div>

            {{-- User Prompt Template --}}
            <div style="background:#fff; border:1px solid #e9ecf0; border-radius:12px; padding:22px;">
                <h3 style="font-size:14px; font-weight:800; color:#111827; margin:0 0 6px;">User Prompt Template <span style="color:#ef4444;">*</span></h3>
                <p style="font-size:12px; color:#6b7280; margin:0 0 12px;">Use <code style="background:#f1f5f9; padding:1px 5px; border-radius:3px;">{input}</code> as the variable placeholder.</p>
                <textarea name="user_prompt_template" required rows="12"
                          style="width:100%; padding:10px 12px; border:1.5px solid #d1d5db; border-radius:7px; font-size:13px; font-family:monospace; resize:vertical; box-sizing:border-box; line-height:1.6;">{{ old('user_prompt_template', $template->user_prompt_template) }}</textarea>
                @error('user_prompt_template')<p style="font-size:12px; color:#ef4444; margin:4px 0 0;">{{ $message }}</p>@enderror
            </div>

            {{-- Output Format --}}
            <div style="background:#fff; border:1px solid #e9ecf0; border-radius:12px; padding:22px;">
                <h3 style="font-size:14px; font-weight:800; color:#111827; margin:0 0 6px;">Output Format Instructions</h3>
                <p style="font-size:12px; color:#6b7280; margin:0 0 12px;">Appended after the user prompt.</p>
                <textarea name="output_format_instructions" rows="4"
                          style="width:100%; padding:10px 12px; border:1.5px solid #d1d5db; border-radius:7px; font-size:13px; font-family:monospace; resize:vertical; box-sizing:border-box; line-height:1.6;">{{ old('output_format_instructions', $template->output_format_instructions) }}</textarea>
            </div>

        </div>

        {{-- ── Right column ────────────────────────────────── --}}
        <div style="display:flex; flex-direction:column; gap:18px;">

            {{-- Version notice --}}
            <div style="background:#eff6ff; border:1px solid #bfdbfe; border-radius:12px; padding:16px;">
                <div style="font-size:13px; font-weight:700; color:#1e3a8a; margin-bottom:4px;">Version Control</div>
                <div style="font-size:12px; color:#1e40af;">
                    Current: <strong>v{{ $template->version_number }}</strong><br>
                    Saving will create a snapshot of the current version before applying your changes.
                    The version number will increment to <strong>v{{ $template->version_number + 1 }}</strong>.
                </div>
            </div>

            {{-- Model Settings --}}
            <div style="background:#fff; border:1px solid #e9ecf0; border-radius:12px; padding:22px;">
                <h3 style="font-size:14px; font-weight:800; color:#111827; margin:0 0 16px;">Model Settings</h3>

                <div style="margin-bottom:14px;">
                    <label style="font-size:12.5px; font-weight:700; color:#374151; display:block; margin-bottom:5px;">Model Override</label>
                    <select name="model_override" style="width:100%; padding:9px 12px; border:1.5px solid #d1d5db; border-radius:7px; font-size:13px; box-sizing:border-box;">
                        <option value="">Default ({{ config('ai.model', 'gpt-4o-mini') }})</option>
                        <option value="gpt-4o-mini"   {{ old('model_override', $template->model_override) === 'gpt-4o-mini'   ? 'selected' : '' }}>gpt-4o-mini</option>
                        <option value="gpt-4o"        {{ old('model_override', $template->model_override) === 'gpt-4o'        ? 'selected' : '' }}>gpt-4o</option>
                        <option value="gpt-4-turbo"   {{ old('model_override', $template->model_override) === 'gpt-4-turbo'   ? 'selected' : '' }}>gpt-4-turbo</option>
                        <option value="gpt-3.5-turbo" {{ old('model_override', $template->model_override) === 'gpt-3.5-turbo' ? 'selected' : '' }}>gpt-3.5-turbo</option>
                    </select>
                </div>

                <div style="margin-bottom:14px;">
                    <label style="font-size:12.5px; font-weight:700; color:#374151; display:block; margin-bottom:5px;">Temperature</label>
                    <input type="number" name="temperature" value="{{ old('temperature', $template->temperature ?? 0.70) }}" step="0.05" min="0" max="2"
                           style="width:100%; padding:9px 12px; border:1.5px solid #d1d5db; border-radius:7px; font-size:13px; box-sizing:border-box;">
                </div>

                <div>
                    <label style="font-size:12.5px; font-weight:700; color:#374151; display:block; margin-bottom:5px;">Max Tokens</label>
                    <input type="number" name="max_tokens" value="{{ old('max_tokens', $template->max_tokens ?? 2000) }}" min="100" max="16000"
                           style="width:100%; padding:9px 12px; border:1.5px solid #d1d5db; border-radius:7px; font-size:13px; box-sizing:border-box;">
                </div>
            </div>

            {{-- Status --}}
            <div style="background:#fff; border:1px solid #e9ecf0; border-radius:12px; padding:22px;">
                <h3 style="font-size:14px; font-weight:800; color:#111827; margin:0 0 14px;">Status</h3>
                <label style="display:flex; align-items:center; gap:10px; cursor:pointer;">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $template->is_active ? '1' : '0') == '1' ? 'checked' : '' }}
                           style="width:16px; height:16px; cursor:pointer;">
                    <div>
                        <div style="font-size:13.5px; font-weight:700; color:#111827;">Active</div>
                        <div style="font-size:12px; color:#6b7280;">Inactive templates cannot be tested or used in production.</div>
                    </div>
                </label>
            </div>

            {{-- Submit --}}
            <div style="background:#fff; border:1px solid #e9ecf0; border-radius:12px; padding:18px; display:flex; flex-direction:column; gap:10px;">
                <button type="submit" style="background:#1e3a8a; color:#fff; padding:11px; border:none; border-radius:8px; font-weight:800; font-size:14px; cursor:pointer; width:100%;">
                    Save Changes → v{{ $template->version_number + 1 }}
                </button>
                <a href="{{ route('ai.prompt-templates.show', $template) }}" style="text-align:center; padding:10px; background:#f1f5f9; color:#374151; border-radius:8px; font-size:13.5px; font-weight:600; text-decoration:none; display:block;">
                    Cancel
                </a>

                {{-- Danger zone --}}
                <div style="border-top:1px solid #f0f2f5; padding-top:12px; margin-top:4px;">
                    <form method="POST" action="{{ route('ai.prompt-templates.destroy', $template) }}" onsubmit="return confirm('Archive this template? This cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="background:#fff1f2; color:#be123c; padding:9px; border:1px solid #fecdd3; border-radius:8px; font-size:13px; font-weight:700; cursor:pointer; width:100%;">
                            Archive Template
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</form>

@endsection
