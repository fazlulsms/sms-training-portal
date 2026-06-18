@extends('layouts.app')
@section('page-title', $template->name)
@section('content')
<div style="max-width:860px; margin:auto;">
    <div style="margin-bottom:16px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <a href="{{ route('feedback.templates.index') }}" style="color:#6b7280; font-size:13px; text-decoration:none;">← Back to Templates</a>
        <div style="display:flex; gap:8px;">
            <a href="{{ route('feedback.templates.preview', $template) }}" target="_blank"
               style="background:#f0f9ff; color:#0369a1; padding:8px 18px; border-radius:7px; font-size:13px; font-weight:700; text-decoration:none; display:inline-flex; align-items:center; gap:6px; border:1px solid #bae6fd;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                Preview
            </a>
            <a href="{{ route('feedback.templates.edit', $template) }}" style="background:#e0e7ff; color:#3730a3; padding:8px 18px; border-radius:7px; font-size:13px; font-weight:700; text-decoration:none;">Edit</a>
            <form method="POST" action="{{ route('feedback.templates.clone', $template) }}" style="display:inline;">
                @csrf
                <button type="submit" style="background:#f0fdf4; color:#166534; padding:8px 18px; border-radius:7px; font-size:13px; font-weight:700; border:none; cursor:pointer;">Clone</button>
            </form>
        </div>
    </div>

    @if(session('success'))
    <div style="background:#d1fae5; border:1px solid #a7f3d0; border-radius:8px; padding:11px 16px; margin-bottom:16px; font-size:13.5px; color:#065f46;">{{ session('success') }}</div>
    @endif

    {{-- Template Header --}}
    <div style="background:#fff; border-radius:12px; padding:22px 24px; box-shadow:0 2px 8px rgba(0,0,0,.06); margin-bottom:18px;">
        <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:10px;">
            <div>
                <h2 style="font-size:20px; font-weight:800; color:#111827; margin:0 0 4px;">{{ $template->name }}</h2>
                @if($template->description)
                <p style="font-size:13px; color:#6b7280; margin:0;">{{ $template->description }}</p>
                @endif
            </div>
            <div style="display:flex; gap:8px; flex-wrap:wrap;">
                <span style="background:#e0e7ff; color:#3730a3; font-size:12px; font-weight:600; padding:4px 12px; border-radius:99px;">{{ $template->type_label }}</span>
                @if($template->is_active)
                <span style="background:#d1fae5; color:#065f46; font-size:12px; font-weight:600; padding:4px 12px; border-radius:99px;">Active</span>
                @endif
                @if($template->require_for_certificate)
                <span style="background:#fef3c7; color:#92400e; font-size:12px; font-weight:600; padding:4px 12px; border-radius:99px;">Required for Certificate</span>
                @endif
            </div>
        </div>
        <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-top:18px; padding-top:18px; border-top:1px solid #f3f4f6;">
            <div style="text-align:center;">
                <div style="font-size:24px; font-weight:800; color:#1e3a8a;">{{ $template->questions->count() }}</div>
                <div style="font-size:12px; color:#6b7280;">Questions</div>
            </div>
            <div style="text-align:center;">
                <div style="font-size:24px; font-weight:800; color:#7c3aed;">{{ $template->assignments->count() }}</div>
                <div style="font-size:12px; color:#6b7280;">Assignments</div>
            </div>
            <div style="text-align:center;">
                <div style="font-size:24px; font-weight:800; color:#059669;">{{ $totalResponses }}</div>
                <div style="font-size:12px; color:#6b7280;">Responses</div>
            </div>
        </div>
    </div>

    {{-- Questions List --}}
    <div style="background:#fff; border-radius:12px; padding:22px 24px; box-shadow:0 2px 8px rgba(0,0,0,.06);">
        <div style="font-size:15px; font-weight:700; color:#111827; margin-bottom:16px;">Questions ({{ $template->questions->count() }})</div>
        @foreach($template->questions as $i => $q)
        <div style="display:flex; gap:14px; align-items:flex-start; padding:12px 0; {{ !$loop->last ? 'border-bottom:1px solid #f3f4f6;' : '' }}">
            <div style="width:28px; height:28px; background:#e0e7ff; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:700; color:#3730a3; flex-shrink:0;">
                {{ $i + 1 }}
            </div>
            <div style="flex:1;">
                <div style="font-size:13.5px; font-weight:600; color:#111827;">{{ $q->question_text }}</div>
                <div style="display:flex; gap:8px; margin-top:5px; flex-wrap:wrap;">
                    <span style="background:#f1f5f9; color:#374151; font-size:11px; font-weight:600; padding:2px 8px; border-radius:99px;">
                        {{ \App\Models\FeedbackQuestion::$TYPES[$q->question_type] ?? $q->question_type }}
                    </span>
                    <span style="background:#f1f5f9; color:#374151; font-size:11px; font-weight:600; padding:2px 8px; border-radius:99px;">
                        {{ \App\Models\FeedbackQuestion::$CATEGORIES[$q->category] ?? $q->category }}
                    </span>
                    @if($q->is_required)
                    <span style="background:#fee2e2; color:#b91c1c; font-size:11px; font-weight:600; padding:2px 8px; border-radius:99px;">Required</span>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
