@extends('layouts.app')
@section('page-title', 'Prompt Templates')
@section('content')

<x-page-header title="Prompt Templates" desc="Manage AI prompt templates for all features. Super Admin only.">
    <x-slot:actions>
        <a href="{{ route('ai.prompt-templates.create') }}" class="btn btn-primary btn-sm">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            New Template
        </a>
        <a href="{{ route('ai.settings') }}" class="btn btn-secondary btn-sm">← AI Settings</a>
    </x-slot:actions>
</x-page-header>

<x-flash-message />

{{-- ── Filters ────────────────────────────────────────────── --}}
<form method="GET" action="{{ route('ai.prompt-templates.index') }}"
      style="background:#fff; border:1px solid #e9ecf0; border-radius:10px; padding:14px 18px; margin-bottom:20px;
             display:flex; gap:10px; align-items:flex-end; flex-wrap:wrap;">
    <div>
        <label style="font-size:11px; font-weight:700; color:#6b7280; display:block; margin-bottom:4px; text-transform:uppercase; letter-spacing:.5px;">Category</label>
        <select name="category" style="padding:7px 10px; border:1.5px solid #d1d5db; border-radius:6px; font-size:13px; min-width:180px;">
            <option value="">All Categories</option>
            @foreach($allCategories as $cat)
                <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label style="font-size:11px; font-weight:700; color:#6b7280; display:block; margin-bottom:4px; text-transform:uppercase; letter-spacing:.5px;">Status</label>
        <select name="status" style="padding:7px 10px; border:1.5px solid #d1d5db; border-radius:6px; font-size:13px;">
            <option value="">All</option>
            <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
    </div>
    <div style="flex:1; min-width:200px;">
        <label style="font-size:11px; font-weight:700; color:#6b7280; display:block; margin-bottom:4px; text-transform:uppercase; letter-spacing:.5px;">Search</label>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Template name or code…"
               style="padding:7px 10px; border:1.5px solid #d1d5db; border-radius:6px; font-size:13px; width:100%;">
    </div>
    <button type="submit" style="background:#1e3a8a; color:#fff; padding:7px 18px; border:none; border-radius:6px; font-size:13px; font-weight:700; cursor:pointer;">Filter</button>
    @if(request()->hasAny(['category','status','search']))
        <a href="{{ route('ai.prompt-templates.index') }}" style="padding:7px 14px; background:#f1f5f9; color:#374151; border-radius:6px; font-size:13px; font-weight:600; text-decoration:none;">Clear</a>
    @endif
</form>

{{-- ── Stats ──────────────────────────────────────────────── --}}
@php
    $total    = $templates->count();
    $active   = $templates->where('is_active', true)->count();
    $inactive = $total - $active;
    $grouped  = $templates->groupBy('category');
@endphp
<div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(140px,1fr)); gap:12px; margin-bottom:20px;">
    <div style="background:#fff; border:1px solid #e9ecf0; border-radius:10px; padding:14px 18px;">
        <div style="font-size:22px; font-weight:800; color:#1e3a8a;">{{ $total }}</div>
        <div style="font-size:12px; color:#6b7280; margin-top:2px;">Total Templates</div>
    </div>
    <div style="background:#fff; border:1px solid #e9ecf0; border-radius:10px; padding:14px 18px;">
        <div style="font-size:22px; font-weight:800; color:#16a34a;">{{ $active }}</div>
        <div style="font-size:12px; color:#6b7280; margin-top:2px;">Active</div>
    </div>
    <div style="background:#fff; border:1px solid #e9ecf0; border-radius:10px; padding:14px 18px;">
        <div style="font-size:22px; font-weight:800; color:#9ca3af;">{{ $inactive }}</div>
        <div style="font-size:12px; color:#6b7280; margin-top:2px;">Inactive</div>
    </div>
    <div style="background:#fff; border:1px solid #e9ecf0; border-radius:10px; padding:14px 18px;">
        <div style="font-size:22px; font-weight:800; color:#f59e0b;">{{ $grouped->count() }}</div>
        <div style="font-size:12px; color:#6b7280; margin-top:2px;">Categories Used</div>
    </div>
</div>

{{-- ── Table ──────────────────────────────────────────────── --}}
<div style="background:#fff; border:1px solid #e9ecf0; border-radius:12px; overflow:hidden;">
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:13px;">
            <thead>
                <tr style="background:#f8fafc;">
                    <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#6b7280; white-space:nowrap;">Name</th>
                    <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#6b7280;">Category</th>
                    <th style="padding:11px 16px; text-align:center; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#6b7280;">Version</th>
                    <th style="padding:11px 16px; text-align:center; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#6b7280;">Status</th>
                    <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#6b7280; white-space:nowrap;">Last Updated</th>
                    <th style="padding:11px 16px; text-align:center; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#6b7280;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($templates as $tpl)
                <tr style="border-top:1px solid #f0f2f5; {{ !$tpl->is_active ? 'opacity:.65;' : '' }}">
                    <td style="padding:11px 16px;">
                        <div style="font-weight:700; color:#111827;">{{ $tpl->template_name }}</div>
                        <div style="font-size:11.5px; color:#9ca3af; font-family:monospace; margin-top:2px;">{{ $tpl->template_code }}</div>
                        @if($tpl->description)
                            <div style="font-size:12px; color:#6b7280; margin-top:3px; max-width:320px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $tpl->description }}</div>
                        @endif
                    </td>
                    <td style="padding:11px 16px;">
                        <span style="font-size:12px; background:#eff6ff; color:#1e3a8a; padding:3px 10px; border-radius:20px; font-weight:600; white-space:nowrap;">{{ $tpl->category }}</span>
                    </td>
                    <td style="padding:11px 16px; text-align:center;">
                        <span style="font-size:12px; background:#f1f5f9; color:#374151; padding:3px 10px; border-radius:20px; font-weight:700;">v{{ $tpl->version_number }}</span>
                    </td>
                    <td style="padding:11px 16px; text-align:center;">
                        @if($tpl->is_active)
                            <span style="font-size:11.5px; background:#dcfce7; color:#16a34a; padding:3px 10px; border-radius:20px; font-weight:700;">Active</span>
                        @else
                            <span style="font-size:11.5px; background:#f1f5f9; color:#9ca3af; padding:3px 10px; border-radius:20px; font-weight:700;">Inactive</span>
                        @endif
                    </td>
                    <td style="padding:11px 16px; color:#6b7280; white-space:nowrap; font-size:12px;">{{ $tpl->updated_at->format('d M Y H:i') }}</td>
                    <td style="padding:11px 16px; text-align:center; white-space:nowrap;">
                        <div style="display:flex; gap:4px; justify-content:center; flex-wrap:wrap;">
                            {{-- View --}}
                            <a href="{{ route('ai.prompt-templates.show', $tpl) }}"
                               title="View" style="display:inline-flex; align-items:center; gap:4px; padding:5px 10px; background:#f0f4ff; color:#1e3a8a; border-radius:6px; font-size:12px; font-weight:600; text-decoration:none;">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                View
                            </a>
                            {{-- Edit --}}
                            <a href="{{ route('ai.prompt-templates.edit', $tpl) }}"
                               title="Edit" style="display:inline-flex; align-items:center; gap:4px; padding:5px 10px; background:#f0fdf4; color:#16a34a; border-radius:6px; font-size:12px; font-weight:600; text-decoration:none;">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                Edit
                            </a>
                            {{-- Test --}}
                            <a href="{{ route('ai.prompt-templates.show', $tpl) }}#test-section"
                               title="Test" style="display:inline-flex; align-items:center; gap:4px; padding:5px 10px; background:#fff7ed; color:#ea580c; border-radius:6px; font-size:12px; font-weight:600; text-decoration:none;">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                                Test
                            </a>
                            {{-- Clone --}}
                            <form method="POST" action="{{ route('ai.prompt-templates.clone', $tpl) }}" style="display:inline;" onsubmit="return confirm('Clone this template?')">
                                @csrf
                                <button type="submit" title="Clone"
                                        style="display:inline-flex; align-items:center; gap:4px; padding:5px 10px; background:#f5f3ff; color:#7c3aed; border:none; border-radius:6px; font-size:12px; font-weight:600; cursor:pointer;">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                    Clone
                                </button>
                            </form>
                            {{-- Toggle --}}
                            <form method="POST" action="{{ route('ai.prompt-templates.toggle', $tpl) }}" style="display:inline;">
                                @csrf
                                <button type="submit" title="{{ $tpl->is_active ? 'Deactivate' : 'Activate' }}"
                                        style="display:inline-flex; align-items:center; gap:4px; padding:5px 10px; background:{{ $tpl->is_active ? '#fff1f2' : '#f0fdf4' }}; color:{{ $tpl->is_active ? '#be123c' : '#16a34a' }}; border:none; border-radius:6px; font-size:12px; font-weight:600; cursor:pointer;">
                                    {{ $tpl->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>
                            {{-- Versions --}}
                            <a href="{{ route('ai.prompt-templates.versions', $tpl) }}"
                               title="Version History" style="display:inline-flex; align-items:center; gap:4px; padding:5px 10px; background:#f8fafc; color:#374151; border-radius:6px; font-size:12px; font-weight:600; text-decoration:none;">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.1"/></svg>
                                History
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="padding:40px; text-align:center; color:#9ca3af;">
                        No prompt templates found.
                        <a href="{{ route('ai.prompt-templates.create') }}" style="color:#1e3a8a; font-weight:700; text-decoration:none; margin-left:8px;">Create your first template →</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
