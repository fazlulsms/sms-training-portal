@extends('layouts.app')
@section('content')
<div style="max-width:800px; margin:auto;">
    <div style="margin-bottom:16px;">
        <a href="/admin/corporate-inquiries" style="color:#1e3a8a; font-weight:600; text-decoration:none;">← Back to Inquiries</a>
    </div>

    @if(session('success'))
    <div style="background:#dcfce7; border:1px solid #86efac; border-radius:8px; padding:12px 16px; margin-bottom:16px; color:#166534; font-weight:600;">
        {{ session('success') }}
    </div>
    @endif

    <div style="background:#fff; padding:28px; border-radius:14px; box-shadow:0 4px 16px rgba(0,0,0,.07); margin-bottom:20px;">
        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:20px;">
            <div>
                <h2 style="font-size:22px; font-weight:800; color:#111827; margin:0 0 4px;">{{ $inquiry->company_name }}</h2>
                <p style="color:#6b7280; margin:0;">Received {{ $inquiry->created_at->format('d M Y, h:i A') }}</p>
            </div>
            <span style="background:#dbeafe; color:#1e40af; padding:6px 14px; border-radius:20px; font-weight:700; font-size:13px;">{{ $inquiry->status }}</span>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:20px;">
            <div>
                <p style="font-size:12px; font-weight:700; color:#6b7280; text-transform:uppercase; margin:0 0 4px;">Contact Person</p>
                <p style="font-weight:600; color:#111827; margin:0;">{{ $inquiry->contact_person }}</p>
            </div>
            <div>
                <p style="font-size:12px; font-weight:700; color:#6b7280; text-transform:uppercase; margin:0 0 4px;">Email</p>
                <p style="font-weight:600; color:#111827; margin:0;"><a href="mailto:{{ $inquiry->email }}" style="color:#1e3a8a;">{{ $inquiry->email }}</a></p>
            </div>
            <div>
                <p style="font-size:12px; font-weight:700; color:#6b7280; text-transform:uppercase; margin:0 0 4px;">Phone</p>
                <p style="color:#374151; margin:0;">{{ $inquiry->phone ?? '—' }}</p>
            </div>
            <div>
                <p style="font-size:12px; font-weight:700; color:#6b7280; text-transform:uppercase; margin:0 0 4px;">Country</p>
                <p style="color:#374151; margin:0;">{{ $inquiry->country ?? '—' }}</p>
            </div>
            <div>
                <p style="font-size:12px; font-weight:700; color:#6b7280; text-transform:uppercase; margin:0 0 4px;">Participants</p>
                <p style="color:#374151; margin:0;">{{ $inquiry->participants_count ?? '—' }}</p>
            </div>
            <div>
                <p style="font-size:12px; font-weight:700; color:#6b7280; text-transform:uppercase; margin:0 0 4px;">Preferred Date</p>
                <p style="color:#374151; margin:0;">{{ $inquiry->preferred_date ? $inquiry->preferred_date->format('d M Y') : '—' }}</p>
            </div>
            <div>
                <p style="font-size:12px; font-weight:700; color:#6b7280; text-transform:uppercase; margin:0 0 4px;">Preferred Mode</p>
                <p style="color:#374151; margin:0;">{{ $inquiry->preferred_mode ?? '—' }}</p>
            </div>
        </div>

        <div style="margin-bottom:16px;">
            <p style="font-size:12px; font-weight:700; color:#6b7280; text-transform:uppercase; margin:0 0 6px;">Training Requirement</p>
            <div style="background:#f8fafc; border-radius:8px; padding:14px; white-space:pre-line; font-size:14px; color:#374151;">{{ $inquiry->training_requirement }}</div>
        </div>

        @if($inquiry->message)
        <div>
            <p style="font-size:12px; font-weight:700; color:#6b7280; text-transform:uppercase; margin:0 0 6px;">Additional Message</p>
            <div style="background:#f8fafc; border-radius:8px; padding:14px; white-space:pre-line; font-size:14px; color:#374151;">{{ $inquiry->message }}</div>
        </div>
        @endif
    </div>

    <div style="background:#fff; padding:24px; border-radius:14px; box-shadow:0 4px 16px rgba(0,0,0,.07);">
        <h3 style="font-size:16px; font-weight:700; color:#111827; margin:0 0 16px;">Update Status & Notes</h3>
        <form method="POST" action="/admin/corporate-inquiries/{{ $inquiry->id }}/update">
            @csrf
            <div style="margin-bottom:14px;">
                <label style="display:block; font-weight:600; font-size:13.5px; color:#374151; margin-bottom:6px;">Status</label>
                <select name="status" style="width:100%; padding:10px 13px; border:1.5px solid #d1d5db; border-radius:8px; font-size:14px;">
                    @foreach(['New','In Progress','Replied','Closed'] as $st)
                    <option value="{{ $st }}" {{ $inquiry->status==$st?'selected':'' }}>{{ $st }}</option>
                    @endforeach
                </select>
            </div>
            <div style="margin-bottom:16px;">
                <label style="display:block; font-weight:600; font-size:13.5px; color:#374151; margin-bottom:6px;">Admin Notes</label>
                <textarea name="admin_notes" rows="4" style="width:100%; padding:10px 13px; border:1.5px solid #d1d5db; border-radius:8px; font-size:14px; font-family:inherit; box-sizing:border-box;">{{ $inquiry->admin_notes }}</textarea>
            </div>
            <button type="submit" style="background:#1e3a8a; color:#fff; padding:11px 24px; border:none; border-radius:8px; font-weight:700; cursor:pointer;">
                Save Changes
            </button>
        </form>
    </div>
</div>
@endsection
