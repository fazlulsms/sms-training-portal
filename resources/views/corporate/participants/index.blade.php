@extends('layouts.app')
@section('title', 'Participants')
@section('content')

<div class="page-header">
    <div>
        <div style="font-size:13px;color:#9ca3af;margin-bottom:4px;">
            <a href="{{ route('corporate.sessions.show', $session) }}" style="color:#6b7280;text-decoration:none;">{{ $session->course_name }}</a>
        </div>
        <h1 class="page-title">Participants</h1>
        <p class="page-subtitle">{{ $session->project->company_name }}</p>
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap;">
        <a href="{{ route('corporate.participants.csv-template') }}" class="btn btn-secondary" style="font-size:13px;">⬇ CSV Template</a>
        <a href="{{ route('corporate.sessions.participants.export', $session) }}" class="btn btn-secondary" style="font-size:13px;">⬇ Export CSV</a>
        <a href="{{ route('corporate.sessions.participants.create', $session) }}" class="btn btn-primary">+ Add</a>
    </div>
</div>

@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

{{-- Filter Bar --}}
<div class="filter-bar" style="background:#fff;border:1px solid #e5e9f0;border-radius:14px;padding:14px 18px;margin-bottom:18px;display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
    <form method="GET" action="{{ route('corporate.sessions.participants.index', $session) }}" style="display:contents;">
        <div style="position:relative;">
            <span style="position:absolute;left:11px;top:50%;transform:translateY(-50%);color:#9ca3af;pointer-events:none;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </span>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search name, ID, position, email…"
                   style="padding:8px 12px 8px 36px;border:1.5px solid #e5e9f0;border-radius:9px;font-size:13.5px;font-family:inherit;width:240px;background:#fafbfd;outline:none;">
        </div>
        <select name="department" style="padding:8px 12px;border:1.5px solid #e5e9f0;border-radius:9px;font-size:13.5px;font-family:inherit;color:#374151;background:#fafbfd;cursor:pointer;outline:none;">
            <option value="">All Departments</option>
            @foreach($departments as $dept)
            <option value="{{ $dept }}" {{ request('department') === $dept ? 'selected' : '' }}>{{ $dept }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-primary" style="padding:8px 18px;">Filter</button>
        @if(request()->hasAny(['q','department']))
        <a href="{{ route('corporate.sessions.participants.index', $session) }}" style="font-size:12.5px;color:#9ca3af;text-decoration:none;font-weight:600;padding:4px 8px;border-radius:7px;">✕ Clear</a>
        @endif
    </form>
    <div style="margin-left:auto;font-size:12.5px;color:#9ca3af;font-weight:600;white-space:nowrap;">{{ $participants->total() }} record(s)</div>
</div>

{{-- CSV Import --}}
<div class="card" style="margin-bottom:18px;">
    <div class="card-header"><h3 class="card-title">Import from CSV</h3></div>
    <div class="card-body">
        <form method="POST" action="{{ route('corporate.sessions.participants.import', $session) }}" enctype="multipart/form-data"
              style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap;">
            @csrf
            <div>
                <label style="font-size:12.5px;font-weight:700;color:#374151;display:block;margin-bottom:4px;">CSV File</label>
                <input type="file" name="csv_file" accept=".csv,.txt" required class="form-control" style="font-size:13.5px;padding:6px;">
            </div>
            <button type="submit" class="btn btn-primary" style="height:38px;">Import</button>
            <span style="font-size:12px;color:#9ca3af;align-self:center;">Columns: Name*, Employee ID, Position, Department, Email, Contact</span>
        </form>
    </div>
</div>

{{-- Standalone bulk-delete form (NOT wrapping the table — avoids nested-form breakage) --}}
<form method="POST" action="{{ route('corporate.sessions.participants.bulk-destroy', $session) }}" id="bulkForm">
    @csrf
    <div id="bulkIds"></div>{{-- JS injects <input name="ids[]"> here before submit --}}
</form>

<div class="card">
    <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;">
        <h3 class="card-title">Participant List</h3>
        <div style="display:flex;gap:8px;">
            <button type="button" onclick="bulkDelete(false)" class="btn btn-sm btn-danger">🗑 Delete Selected</button>
            <button type="button" onclick="bulkDelete(true)"  class="btn btn-sm btn-danger" style="background:#7f1d1d;">🗑 Delete All</button>
        </div>
    </div>
    <div class="card-body" style="padding:0;overflow-x:auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:36px;"><input type="checkbox" id="checkAll" onchange="document.querySelectorAll('.row-check').forEach(c => c.checked = this.checked)"></th>
                    <th>Name</th>
                    <th>Employee ID</th>
                    <th>Position / Dept</th>
                    <th>Email</th>
                    <th>Contact</th>
                    <th>Attendance</th>
                    <th>Certificate</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($participants as $p)
                @php
                    $att = $p->attendance;
                    $attColor = match($att?->status) { 'Present'=>'#16a34a','Absent'=>'#dc2626','Partial'=>'#d97706', default=>'#9ca3af' };
                @endphp
                <tr>
                    <td><input type="checkbox" class="row-check" value="{{ $p->id }}"></td>
                    <td style="font-weight:700;">{{ $p->participant_name }}</td>
                    <td style="color:#6b7280;font-size:13px;">{{ $p->employee_id ?? '—' }}</td>
                    <td style="font-size:13px;color:#6b7280;">{{ $p->position }}{{ $p->department ? ' / '.$p->department : '' }}</td>
                    <td style="font-size:13px;">{{ $p->email ?? '—' }}</td>
                    <td style="font-size:13px;">{{ $p->contact_number ?? '—' }}</td>
                    <td>
                        <span style="background:{{ $attColor }}22;color:{{ $attColor }};padding:2px 8px;border-radius:20px;font-size:11.5px;font-weight:700;">
                            {{ $att?->status ?? 'Not Marked' }}
                        </span>
                    </td>
                    <td style="font-size:12.5px;">
                        @if($p->certificate)
                        <a href="{{ route('corporate.certificates.view', $p->certificate) }}" target="_blank" style="color:#7c3aed;font-weight:700;">
                            {{ $p->certificate->certificate_number }}
                        </a>
                        @else <span style="color:#9ca3af;">—</span> @endif
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;">
                            <a href="{{ route('corporate.sessions.participants.edit', [$session, $p]) }}" class="btn btn-sm btn-secondary">Edit</a>
                            <form method="POST" action="{{ route('corporate.sessions.participants.destroy', [$session, $p]) }}" onsubmit="return confirm('Remove this participant?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">✕</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" style="text-align:center;padding:40px;color:#9ca3af;">No participants yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($participants->hasPages())
    <div style="padding:16px 20px;border-top:1px solid #f0f2f5;">{{ $participants->links() }}</div>
    @endif
</div>

<script>
function bulkDelete(all) {
    const checkboxes = document.querySelectorAll('.row-check');
    const ids = all
        ? Array.from(checkboxes).map(c => c.value)
        : Array.from(checkboxes).filter(c => c.checked).map(c => c.value);

    if (ids.length === 0) {
        alert(all ? 'No participants to delete.' : 'Select at least one participant.');
        return;
    }

    const label = all ? 'ALL ' + ids.length + ' participant(s)' : ids.length + ' selected participant(s)';
    if (!confirm('Delete ' + label + '? This cannot be undone.')) return;

    const container = document.getElementById('bulkIds');
    container.innerHTML = '';
    ids.forEach(id => {
        const inp = document.createElement('input');
        inp.type  = 'hidden';
        inp.name  = 'ids[]';
        inp.value = id;
        container.appendChild(inp);
    });

    document.getElementById('bulkForm').submit();
}
</script>
@endsection
