@extends('layouts.app')
@section('page-title', 'Feedback Templates')
@section('content')

<div style="max-width:1100px; margin:auto;">

    {{-- Header --}}
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:22px; flex-wrap:wrap; gap:10px;">
        <div>
            <h2 style="font-size:22px; font-weight:800; color:#111827; margin:0;">Feedback Templates</h2>
            <p style="font-size:13px; color:#6b7280; margin:4px 0 0;">Reusable evaluation forms for ILT, eLearning, and more.</p>
        </div>
        <a href="{{ route('feedback.templates.create') }}"
           style="background:linear-gradient(135deg,#1e3a8a,#2563eb); color:#fff; padding:10px 20px; border-radius:8px; font-weight:700; font-size:13.5px; text-decoration:none; display:inline-flex; align-items:center; gap:7px;">
            + New Template
        </a>
    </div>

    @if(session('success'))
    <div style="background:#d1fae5; border:1px solid #a7f3d0; border-radius:8px; padding:11px 16px; margin-bottom:16px; font-size:13.5px; color:#065f46;">
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div style="background:#fee2e2; border:1px solid #fca5a5; border-radius:8px; padding:11px 16px; margin-bottom:16px; font-size:13.5px; color:#b91c1c;">
        {{ session('error') }}
    </div>
    @endif

    @if($templates->isEmpty())
    <div style="background:#fff; border-radius:12px; padding:48px; text-align:center; box-shadow:0 2px 8px rgba(0,0,0,.06);">
        <div style="font-size:40px; margin-bottom:12px;">📋</div>
        <div style="font-size:16px; font-weight:700; color:#111827; margin-bottom:6px;">No templates yet</div>
        <div style="font-size:13px; color:#6b7280; margin-bottom:20px;">Create your first feedback template to start collecting evaluations.</div>
        <a href="{{ route('feedback.templates.create') }}" style="background:#1e3a8a; color:#fff; padding:10px 22px; border-radius:8px; font-weight:700; text-decoration:none;">Create First Template</a>
    </div>
    @else
    <div style="background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,.06); overflow:hidden;">
        <table style="width:100%; border-collapse:collapse; font-size:13.5px;">
            <thead>
                <tr style="background:#f8fafc; border-bottom:2px solid #e5e7eb;">
                    <th style="padding:12px 16px; text-align:left; font-weight:700; color:#374151;">Template</th>
                    <th style="padding:12px 16px; text-align:left; font-weight:700; color:#374151;">Type</th>
                    <th style="padding:12px 16px; text-align:center; font-weight:700; color:#374151;">Questions</th>
                    <th style="padding:12px 16px; text-align:center; font-weight:700; color:#374151;">Responses</th>
                    <th style="padding:12px 16px; text-align:center; font-weight:700; color:#374151;">Status</th>
                    <th style="padding:12px 16px; text-align:right; font-weight:700; color:#374151;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($templates as $t)
                <tr style="border-bottom:1px solid #f3f4f6;">
                    <td style="padding:12px 16px;">
                        <div style="font-weight:700; color:#111827;">{{ $t->name }}</div>
                        @if($t->description)
                        <div style="font-size:12px; color:#9ca3af; margin-top:2px;">{{ Str::limit($t->description, 60) }}</div>
                        @endif
                        @if($t->is_default)
                        <span style="background:#fef3c7; color:#92400e; font-size:10px; font-weight:700; padding:1px 7px; border-radius:99px;">DEFAULT</span>
                        @endif
                    </td>
                    <td style="padding:12px 16px;">
                        <span style="background:#e0e7ff; color:#3730a3; font-size:11.5px; font-weight:600; padding:3px 10px; border-radius:99px;">
                            {{ $t->type_label }}
                        </span>
                    </td>
                    <td style="padding:12px 16px; text-align:center; font-weight:700; color:#374151;">{{ $t->questions_count }}</td>
                    <td style="padding:12px 16px; text-align:center; font-weight:700; color:#059669;">{{ $t->responses_count ?? 0 }}</td>
                    <td style="padding:12px 16px; text-align:center;">
                        @if($t->is_active)
                        <span style="background:#d1fae5; color:#065f46; font-size:11px; font-weight:700; padding:2px 9px; border-radius:99px;">Active</span>
                        @else
                        <span style="background:#f3f4f6; color:#6b7280; font-size:11px; font-weight:700; padding:2px 9px; border-radius:99px;">Inactive</span>
                        @endif
                    </td>
                    <td style="padding:12px 16px; text-align:right;">
                        <div style="display:flex; gap:6px; justify-content:flex-end;">
                            <a href="{{ route('feedback.templates.show', $t) }}" style="background:#f1f5f9; color:#374151; padding:5px 12px; border-radius:6px; font-size:12px; font-weight:600; text-decoration:none;">View</a>
                            <a href="{{ route('feedback.templates.edit', $t) }}" style="background:#e0e7ff; color:#3730a3; padding:5px 12px; border-radius:6px; font-size:12px; font-weight:600; text-decoration:none;">Edit</a>
                            <form method="POST" action="{{ route('feedback.templates.clone', $t) }}" style="display:inline;">
                                @csrf
                                <button type="submit" style="background:#f0fdf4; color:#166534; padding:5px 12px; border-radius:6px; font-size:12px; font-weight:600; border:none; cursor:pointer;">Clone</button>
                            </form>
                            @if(!$t->is_default)
                            <form method="POST" action="{{ route('feedback.templates.destroy', $t) }}" style="display:inline;" onsubmit="return confirm('Delete this template?')">
                                @csrf @method('DELETE')
                                <button type="submit" style="background:#fee2e2; color:#b91c1c; padding:5px 12px; border-radius:6px; font-size:12px; font-weight:600; border:none; cursor:pointer;">Delete</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection
