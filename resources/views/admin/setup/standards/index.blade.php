@extends('layouts.app')
@section('content')
<div style="max-width:1100px; margin:auto;">

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
        <div>
            <h2 style="font-size:22px; font-weight:800; color:#111827; margin:0 0 4px;">Standards & Frameworks</h2>
            <div style="font-size:13px; color:#6b7280;">
                Layer 3 · LTF Taxonomy ·
                <span style="font-weight:700; color:#1e3a8a;">{{ $records->flatten()->count() }} records</span>
                <span style="color:#d1d5db;"> / </span>
                <span style="color:#166534;">{{ $records->flatten()->where('status','active')->count() }} active</span>
                · <span style="color:#6b7280;">{{ $records->count() }} domains</span>
            </div>
        </div>
        <a href="{{ route('setup.standards.create') }}"
           style="background:#1e3a8a; color:#fff; padding:10px 20px; border-radius:8px; font-weight:700; text-decoration:none; font-size:14px;">
            + Add Standard
        </a>
    </div>

    @if(session('success'))
    <div style="background:#dcfce7; border:1px solid #86efac; border-radius:8px; padding:12px 16px; margin-bottom:16px; color:#166534; font-weight:600;">
        {{ session('success') }}
    </div>
    @endif

    @include('admin.setup._tabs', ['active' => 'standards'])

    @foreach($records as $domainKey => $group)
    @php $domainLabel = $domains[$domainKey] ?? ucfirst($domainKey); @endphp
    <div style="margin-bottom:20px;">
        <div style="padding:10px 16px; background:#f1f5f9; border-radius:8px 8px 0 0; border:1px solid #e5e7eb; border-bottom:none;">
            <span style="font-size:13px; font-weight:800; color:#334155; text-transform:uppercase; letter-spacing:.5px;">{{ $domainLabel }}</span>
            <span style="margin-left:10px; background:#e2e8f0; color:#475569; padding:1px 8px; border-radius:10px; font-size:11px; font-weight:700;">{{ $group->count() }}</span>
        </div>
        <div style="background:#fff; border-radius:0 0 8px 8px; box-shadow:0 2px 8px rgba(0,0,0,.04); overflow:hidden; border:1px solid #e5e7eb; border-top:none;">
            <table style="width:100%; border-collapse:collapse;">
                <tbody>
                    @foreach($group as $rec)
                    <tr style="border-bottom:1px solid #f3f4f6;">
                        <td style="padding:11px 16px; font-weight:700; color:#111827; width:160px;">
                            {{ $rec->name }}
                            @if($rec->version)
                            <span style="font-size:11px; color:#6b7280; font-weight:500;">({{ $rec->version }})</span>
                            @endif
                        </td>
                        <td style="padding:11px 16px; font-size:13px; color:#6b7280;">{{ $rec->full_name ?? '—' }}</td>
                        <td style="padding:11px 16px; text-align:center; width:70px; font-weight:700; color:#1e3a8a; font-size:13px;">{{ $rec->courses_count }}</td>
                        <td style="padding:11px 16px; text-align:center; width:110px;">
                            <form method="POST" action="{{ route('setup.standards.toggle', $rec) }}" style="display:inline;">
                                @csrf @method('PATCH')
                                <button type="submit" style="background:{{ $rec->status==='active' ? '#dcfce7' : '#f3f4f6' }}; color:{{ $rec->status==='active' ? '#166534' : '#6b7280' }}; border:none; padding:3px 12px; border-radius:20px; font-size:12px; font-weight:700; cursor:pointer;">
                                    {{ ucfirst($rec->status) }}
                                </button>
                            </form>
                        </td>
                        <td style="padding:11px 16px; text-align:right; white-space:nowrap; width:130px;">
                            <a href="{{ route('setup.standards.edit', $rec) }}" style="color:#1e3a8a; font-weight:600; font-size:13px; text-decoration:none; margin-right:12px;">Edit</a>
                            <form method="POST" action="{{ route('setup.standards.destroy', $rec) }}" style="display:inline;" onsubmit="return confirm('Delete {{ addslashes($rec->name) }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" style="background:none; border:none; color:#dc2626; font-weight:600; font-size:13px; cursor:pointer; padding:0;">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endforeach

    @if($records->isEmpty())
    <div style="background:#fff; border-radius:12px; padding:40px; text-align:center; color:#9ca3af;">
        No standards yet. <a href="{{ route('setup.standards.create') }}" style="color:#1e3a8a;">Add the first one.</a>
    </div>
    @endif
</div>
@endsection
