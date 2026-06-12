@extends('layouts.app')
@section('page-title', 'Feedback Responses')
@section('content')
<div style="max-width:1100px; margin:auto;">

    {{-- Header --}}
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
        <div>
            <h2 style="font-size:22px; font-weight:800; color:#111827; margin:0;">Feedback Responses</h2>
            <p style="font-size:13px; color:#6b7280; margin:4px 0 0;">All completed evaluation submissions.</p>
        </div>
        <a href="{{ route('feedback.templates.index') }}" style="background:#f1f5f9; color:#374151; padding:9px 18px; border-radius:8px; font-size:13px; font-weight:600; text-decoration:none;">Manage Templates</a>
    </div>

    {{-- Stats --}}
    <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:14px; margin-bottom:20px;">
        <div style="background:#fff; border-radius:10px; padding:16px 20px; box-shadow:0 2px 8px rgba(0,0,0,.06);">
            <div style="font-size:26px; font-weight:800; color:#1e3a8a;">{{ $totalCount }}</div>
            <div style="font-size:12px; color:#6b7280; margin-top:2px;">Total Responses</div>
        </div>
        <div style="background:#fff; border-radius:10px; padding:16px 20px; box-shadow:0 2px 8px rgba(0,0,0,.06);">
            <div style="font-size:26px; font-weight:800; color:#d97706;">{{ $pendingCount }}</div>
            <div style="font-size:12px; color:#6b7280; margin-top:2px;">Pending</div>
        </div>
        <div style="background:#fff; border-radius:10px; padding:16px 20px; box-shadow:0 2px 8px rgba(0,0,0,.06);">
            <div style="font-size:26px; font-weight:800; color:#059669;">{{ $avgRating ? number_format($avgRating, 1) . ' / 5' : '—' }}</div>
            <div style="font-size:12px; color:#6b7280; margin-top:2px;">Avg. Rating</div>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" style="background:#fff; border-radius:10px; padding:14px 18px; box-shadow:0 2px 8px rgba(0,0,0,.06); margin-bottom:18px; display:flex; gap:12px; flex-wrap:wrap; align-items:center;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email…"
               style="padding:8px 12px; border:1.5px solid #d1d5db; border-radius:7px; font-size:13.5px; min-width:220px;">
        <select name="type" style="padding:8px 12px; border:1.5px solid #d1d5db; border-radius:7px; font-size:13.5px;">
            <option value="">All Types</option>
            @foreach(\App\Models\FeedbackTemplate::$TYPES as $val => $label)
            <option value="{{ $val }}" {{ request('type') === $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit" style="background:#1e3a8a; color:#fff; padding:8px 18px; border:none; border-radius:7px; font-size:13px; font-weight:700; cursor:pointer;">Filter</button>
        @if(request()->hasAny(['search','type']))
        <a href="{{ route('feedback.responses.index') }}" style="color:#6b7280; font-size:13px; text-decoration:none;">Clear</a>
        @endif
    </form>

    {{-- Table --}}
    @if($responses->isEmpty())
    <div style="background:#fff; border-radius:12px; padding:40px; text-align:center; box-shadow:0 2px 8px rgba(0,0,0,.06);">
        <div style="font-size:36px; margin-bottom:10px;">📭</div>
        <div style="font-size:15px; font-weight:700; color:#111827; margin-bottom:6px;">No responses yet</div>
        <div style="font-size:13px; color:#6b7280;">Responses will appear here once participants submit feedback.</div>
    </div>
    @else
    <div style="background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,.06); overflow:hidden;">
        <table style="width:100%; border-collapse:collapse; font-size:13.5px;">
            <thead>
                <tr style="background:#f8fafc; border-bottom:2px solid #e5e7eb;">
                    <th style="padding:12px 16px; text-align:left; font-weight:700; color:#374151;">Respondent</th>
                    <th style="padding:12px 16px; text-align:left; font-weight:700; color:#374151;">Template</th>
                    <th style="padding:12px 16px; text-align:center; font-weight:700; color:#374151;">Rating</th>
                    <th style="padding:12px 16px; text-align:center; font-weight:700; color:#374151;">Submitted</th>
                    <th style="padding:12px 16px; text-align:right; font-weight:700; color:#374151;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($responses as $r)
                <tr style="border-bottom:1px solid #f3f4f6;">
                    <td style="padding:11px 16px;">
                        <div style="font-weight:600; color:#111827;">{{ $r->respondent_name ?: ($r->user?->name ?? 'Anonymous') }}</div>
                        <div style="font-size:12px; color:#9ca3af;">{{ $r->respondent_email ?: $r->user?->email }}</div>
                        @if($r->is_demo)
                        <span style="background:#fef3c7; color:#92400e; font-size:10px; font-weight:700; padding:1px 6px; border-radius:99px;">DEMO</span>
                        @endif
                    </td>
                    <td style="padding:11px 16px;">
                        <div style="font-size:13px; color:#374151;">{{ $r->assignment?->template?->name ?? '—' }}</div>
                        <div style="font-size:11.5px; color:#9ca3af;">{{ $r->assignment?->template?->type_label }}</div>
                    </td>
                    <td style="padding:11px 16px; text-align:center;">
                        @if($r->overall_rating)
                        <span style="font-weight:700; color:#d97706;">{{ number_format($r->overall_rating, 1) }}</span>
                        <span style="color:#d97706; font-size:13px;">★</span>
                        @else
                        <span style="color:#9ca3af;">—</span>
                        @endif
                    </td>
                    <td style="padding:11px 16px; text-align:center; font-size:12.5px; color:#6b7280;">
                        {{ $r->submitted_at?->format('d M Y') }}
                    </td>
                    <td style="padding:11px 16px; text-align:right;">
                        <div style="display:flex; gap:6px; justify-content:flex-end;">
                            <a href="{{ route('feedback.responses.show', $r) }}" style="background:#e0e7ff; color:#3730a3; padding:5px 12px; border-radius:6px; font-size:12px; font-weight:600; text-decoration:none;">View</a>
                            <form method="POST" action="{{ route('feedback.responses.destroy', $r) }}" style="display:inline;" onsubmit="return confirm('Delete this response?')">
                                @csrf @method('DELETE')
                                <button type="submit" style="background:#fee2e2; color:#b91c1c; padding:5px 12px; border-radius:6px; font-size:12px; font-weight:600; border:none; cursor:pointer;">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div style="margin-top:16px;">{{ $responses->links() }}</div>
    @endif
</div>
@endsection
