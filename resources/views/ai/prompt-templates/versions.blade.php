@extends('layouts.app')
@section('page-title', 'Version History: ' . $template->template_name)
@section('content')

<x-page-header title="Version History" desc="{{ $template->template_name }} — {{ $versions->count() }} saved versions">
    <x-slot:actions>
        <a href="{{ route('ai.prompt-templates.show', $template) }}" class="btn btn-secondary btn-sm">← Back to Template</a>
        <a href="{{ route('ai.prompt-templates.edit', $template) }}" class="btn btn-primary btn-sm">Edit Current</a>
    </x-slot:actions>
</x-page-header>

<x-flash-message />

{{-- ── Current version banner ──────────────────────────────── --}}
<div style="background:#eff6ff; border:1px solid #bfdbfe; border-radius:10px; padding:14px 20px; margin-bottom:20px; display:flex; justify-content:space-between; align-items:center;">
    <div>
        <span style="font-size:12px; font-weight:700; color:#1e40af; text-transform:uppercase; letter-spacing:.5px;">Current Version</span>
        <div style="font-size:16px; font-weight:800; color:#1e3a8a; margin-top:2px;">v{{ $template->version_number }}</div>
    </div>
    <div style="text-align:right; font-size:12.5px; color:#1e40af;">
        Last updated: {{ $template->updated_at->format('d M Y H:i') }}<br>
        Status: <strong>{{ $template->is_active ? 'Active' : 'Inactive' }}</strong>
    </div>
</div>

@if($versions->isEmpty())
    <div style="background:#fff; border:1px solid #e9ecf0; border-radius:12px; padding:40px; text-align:center; color:#9ca3af; font-size:14px;">
        No previous versions yet. Version history is created whenever you save changes to this template.
    </div>
@else
    {{-- ── Version list ─────────────────────────────────────── --}}
    <div style="display:flex; flex-direction:column; gap:14px;">
        @foreach($versions as $version)
        <div style="background:#fff; border:1px solid #e9ecf0; border-radius:12px; overflow:hidden;">
            {{-- Header --}}
            <div style="padding:14px 20px; background:#f8fafc; border-bottom:1px solid #f0f2f5; display:flex; justify-content:space-between; align-items:center;">
                <div style="display:flex; align-items:center; gap:10px;">
                    <span style="font-size:14px; font-weight:800; color:#374151;">v{{ $version->version_number }}</span>
                    <span style="font-size:12px; background:#f1f5f9; color:#6b7280; padding:2px 9px; border-radius:20px;">
                        {{ $version->template_name }}
                    </span>
                    @if($version->category !== $template->category)
                        <span style="font-size:11.5px; background:#fef3c7; color:#d97706; padding:2px 9px; border-radius:20px;">category changed</span>
                    @endif
                </div>
                <div style="display:flex; align-items:center; gap:10px;">
                    <span style="font-size:12px; color:#9ca3af;">
                        {{ $version->savedBy?->name ?? 'Unknown' }} — {{ $version->created_at->format('d M Y H:i') }}
                    </span>
                    <form method="POST" action="{{ route('ai.prompt-templates.rollback', [$template, $version]) }}"
                          onsubmit="return confirm('Roll back to v{{ $version->version_number }}? The current version will be saved to history first.')">
                        @csrf
                        <button type="submit"
                                style="background:#fff7ed; color:#ea580c; border:1px solid #fed7aa; padding:5px 14px; border-radius:6px; font-size:12px; font-weight:700; cursor:pointer;">
                            Rollback to v{{ $version->version_number }}
                        </button>
                    </form>
                </div>
            </div>

            {{-- Collapsible content --}}
            <div id="ver-{{ $version->id }}" style="display:none;">
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:0;">
                    <div style="padding:16px 20px; border-right:1px solid #f0f2f5;">
                        <div style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#6b7280; margin-bottom:8px;">System Prompt</div>
                        <pre style="margin:0; font-family:monospace; font-size:12px; line-height:1.6; white-space:pre-wrap; color:#374151; max-height:200px; overflow-y:auto;">{{ $version->system_prompt }}</pre>
                    </div>
                    <div style="padding:16px 20px;">
                        <div style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#6b7280; margin-bottom:8px;">User Prompt Template</div>
                        <pre style="margin:0; font-family:monospace; font-size:12px; line-height:1.6; white-space:pre-wrap; color:#374151; max-height:200px; overflow-y:auto;">{{ $version->user_prompt_template }}</pre>
                    </div>
                </div>
                @if($version->output_format_instructions)
                <div style="padding:12px 20px; border-top:1px solid #f0f2f5; background:#f8fafc;">
                    <div style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#6b7280; margin-bottom:6px;">Output Format Instructions</div>
                    <pre style="margin:0; font-family:monospace; font-size:12px; line-height:1.5; white-space:pre-wrap; color:#374151;">{{ $version->output_format_instructions }}</pre>
                </div>
                @endif
                <div style="padding:10px 20px; border-top:1px solid #f0f2f5; background:#f8fafc; display:flex; gap:20px; flex-wrap:wrap;">
                    <span style="font-size:12px; color:#6b7280;">Model: <strong>{{ $version->model_override ?: 'default' }}</strong></span>
                    <span style="font-size:12px; color:#6b7280;">Temperature: <strong>{{ $version->temperature ?? 'default' }}</strong></span>
                    <span style="font-size:12px; color:#6b7280;">Max tokens: <strong>{{ $version->max_tokens ?? 'default' }}</strong></span>
                </div>
            </div>

            {{-- Expand toggle --}}
            <button onclick="toggleVersion({{ $version->id }})" id="toggle-{{ $version->id }}"
                    style="display:flex; width:100%; padding:10px 20px; background:#fff; border:none; border-top:1px solid #f0f2f5; font-size:12.5px; font-weight:600; color:#1e3a8a; cursor:pointer; align-items:center; gap:6px;">
                <svg id="chevron-{{ $version->id }}" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                Show prompt content
            </button>
        </div>
        @endforeach
    </div>
@endif

<script>
function toggleVersion(id) {
    const panel   = document.getElementById('ver-' + id);
    const chevron = document.getElementById('chevron-' + id);
    const btn     = document.getElementById('toggle-' + id);
    const open    = panel.style.display !== 'none';
    panel.style.display   = open ? 'none' : 'block';
    chevron.style.transform = open ? '' : 'rotate(180deg)';
    btn.childNodes[1].textContent = open ? ' Show prompt content' : ' Hide prompt content';
}
</script>

@endsection
