@extends('layouts.app')
@section('content')
<div style="max-width:1100px; margin:auto;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; flex-wrap:wrap; gap:12px;">
        <h2 style="font-size:22px; font-weight:800; color:#111827; margin:0;">Corporate Training Inquiries</h2>
        <div style="display:flex; gap:10px;">
            @foreach(['','New','In Progress','Replied','Closed'] as $st)
            <a href="?status={{ $st }}" style="padding:7px 14px; border-radius:20px; font-size:13px; font-weight:600; text-decoration:none;
                background:{{ request('status')==$st ? '#1e3a8a' : '#f3f4f6' }};
                color:{{ request('status')==$st ? '#fff' : '#374151' }};">
                {{ $st ?: 'All' }}
            </a>
            @endforeach
        </div>
    </div>

    @if(session('success'))
    <div style="background:#dcfce7; border:1px solid #86efac; border-radius:8px; padding:12px 16px; margin-bottom:16px; color:#166534; font-weight:600;">
        {{ session('success') }}
    </div>
    @endif

    <div style="background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,.06); overflow:hidden;">
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="background:#f8fafc; border-bottom:2px solid #e5e7eb;">
                    <th style="padding:12px 16px; text-align:left; font-size:12px; font-weight:700; color:#6b7280; text-transform:uppercase;">Company</th>
                    <th style="padding:12px 16px; text-align:left; font-size:12px; font-weight:700; color:#6b7280; text-transform:uppercase;">Contact</th>
                    <th style="padding:12px 16px; text-align:left; font-size:12px; font-weight:700; color:#6b7280; text-transform:uppercase;">Requirement</th>
                    <th style="padding:12px 16px; text-align:center; font-size:12px; font-weight:700; color:#6b7280; text-transform:uppercase;">Pax</th>
                    <th style="padding:12px 16px; text-align:center; font-size:12px; font-weight:700; color:#6b7280; text-transform:uppercase;">Status</th>
                    <th style="padding:12px 16px; text-align:center; font-size:12px; font-weight:700; color:#6b7280; text-transform:uppercase;">Received</th>
                    <th style="padding:12px 16px; text-align:center; font-size:12px; font-weight:700; color:#6b7280; text-transform:uppercase;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($inquiries as $inq)
                @php
                $statusColor = match($inq->status) {
                    'New' => ['bg'=>'#dbeafe','c'=>'#1e40af'],
                    'In Progress' => ['bg'=>'#fef3c7','c'=>'#92400e'],
                    'Replied' => ['bg'=>'#dcfce7','c'=>'#166534'],
                    'Closed' => ['bg'=>'#f3f4f6','c'=>'#6b7280'],
                    default => ['bg'=>'#f3f4f6','c'=>'#6b7280'],
                };
                @endphp
                <tr style="border-bottom:1px solid #f3f4f6;" onclick="location.href='/admin/corporate-inquiries/{{ $inq->id }}'" style="cursor:pointer;">
                    <td style="padding:13px 16px; cursor:pointer;">
                        <div style="font-weight:700; color:#111827;">{{ $inq->company_name }}</div>
                        <div style="font-size:12px; color:#6b7280;">{{ $inq->country }}</div>
                    </td>
                    <td style="padding:13px 16px;">
                        <div style="font-weight:600; color:#374151;">{{ $inq->contact_person }}</div>
                        <div style="font-size:12px; color:#6b7280;">{{ $inq->email }}</div>
                    </td>
                    <td style="padding:13px 16px; font-size:13px; color:#374151; max-width:250px;">
                        {{ Str::limit($inq->training_requirement, 80) }}
                    </td>
                    <td style="padding:13px 16px; text-align:center; font-weight:700;">{{ $inq->participants_count ?? '—' }}</td>
                    <td style="padding:13px 16px; text-align:center;">
                        <span style="background:{{ $statusColor['bg'] }}; color:{{ $statusColor['c'] }}; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:700;">
                            {{ $inq->status }}
                        </span>
                    </td>
                    <td style="padding:13px 16px; text-align:center; font-size:12.5px; color:#6b7280;">
                        {{ $inq->created_at->format('d M Y') }}
                    </td>
                    <td style="padding:13px 16px; text-align:center;">
                        <a href="/admin/corporate-inquiries/{{ $inq->id }}" style="color:#1e3a8a; font-weight:600; font-size:13px; text-decoration:none;">View</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="padding:40px; text-align:center; color:#9ca3af;">No inquiries yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:16px;">{{ $inquiries->links() }}</div>
</div>
@endsection
