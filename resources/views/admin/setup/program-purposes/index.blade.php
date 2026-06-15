@extends('layouts.app')
@section('content')
<div style="max-width:1200px; margin:auto;">

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
        <div>
            <h2 style="font-size:22px; font-weight:800; color:#111827; margin:0 0 4px;">Program Purposes</h2>
            <div style="font-size:13px; color:#6b7280;">
                Dimension 3 · LTF Taxonomy ·
                <span style="font-weight:700; color:#1e3a8a;">{{ $records->count() }} records</span>
                <span style="color:#d1d5db;"> / </span>
                <span style="color:#166534;">{{ $records->where('status','active')->count() }} active</span>
            </div>
        </div>
        <a href="{{ route('setup.program-purposes.create') }}"
           style="background:#1e3a8a; color:#fff; padding:10px 20px; border-radius:8px; font-weight:700; text-decoration:none; font-size:14px;">
            + Add Purpose
        </a>
    </div>

    @if(session('success'))
    <div style="background:#dcfce7; border:1px solid #86efac; border-radius:8px; padding:12px 16px; margin-bottom:16px; color:#166534; font-weight:600;">{{ session('success') }}</div>
    @endif

    @include('admin.setup._tabs', ['active' => 'program-purposes'])

    <div style="background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,.06); overflow:hidden;">
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="background:#f8fafc; border-bottom:2px solid #e5e7eb;">
                    <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:.5px;">Name</th>
                    <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:.5px;">Suggested Framework</th>
                    <th style="padding:11px 16px; text-align:center; font-size:11px; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:.5px;">Order</th>
                    <th style="padding:11px 16px; text-align:center; font-size:11px; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:.5px;">Courses</th>
                    <th style="padding:11px 16px; text-align:center; font-size:11px; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:.5px;">Status</th>
                    <th style="padding:11px 16px; text-align:center; font-size:11px; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:.5px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $rec)
                <tr style="border-bottom:1px solid #f3f4f6;">
                    <td style="padding:12px 16px;">
                        <div style="font-weight:600; color:#111827;">{{ $rec->name }}</div>
                        @if($rec->description)
                        <div style="font-size:12px; color:#9ca3af; margin-top:2px;">{{ \Illuminate\Support\Str::limit($rec->description, 70) }}</div>
                        @endif
                    </td>
                    <td style="padding:12px 16px;">
                        @if($rec->suggestedFramework)
                            <span style="background:#eff6ff; color:#1e40af; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:600;">
                                {{ $rec->suggestedFramework->name }}
                            </span>
                        @else
                            <span style="color:#d1d5db; font-size:13px;">—</span>
                        @endif
                    </td>
                    <td style="padding:12px 16px; text-align:center; color:#6b7280;">{{ $rec->display_order }}</td>
                    <td style="padding:12px 16px; text-align:center; font-weight:700; color:#1e3a8a;">{{ $rec->courses_count }}</td>
                    <td style="padding:12px 16px; text-align:center;">
                        <form method="POST" action="{{ route('setup.program-purposes.toggle', $rec) }}" style="display:inline;">
                            @csrf @method('PATCH')
                            <button type="submit" style="background:{{ $rec->status==='active' ? '#dcfce7' : '#f3f4f6' }}; color:{{ $rec->status==='active' ? '#166534' : '#6b7280' }}; border:none; padding:3px 12px; border-radius:20px; font-size:12px; font-weight:700; cursor:pointer;">{{ ucfirst($rec->status) }}</button>
                        </form>
                    </td>
                    <td style="padding:12px 16px; text-align:center; white-space:nowrap;">
                        <a href="{{ route('setup.program-purposes.edit', $rec) }}" style="color:#1e3a8a; font-weight:600; font-size:13px; text-decoration:none; margin-right:12px;">Edit</a>
                        <form method="POST" action="{{ route('setup.program-purposes.destroy', $rec) }}" style="display:inline;" onsubmit="return confirm('Delete {{ addslashes($rec->name) }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" style="background:none; border:none; color:#dc2626; font-weight:600; font-size:13px; cursor:pointer; padding:0;">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" style="padding:40px; text-align:center; color:#9ca3af;">No program purposes yet. <a href="{{ route('setup.program-purposes.create') }}" style="color:#1e3a8a;">Add the first one.</a></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
