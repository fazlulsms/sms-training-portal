@extends('layouts.app')
@section('title', 'Attendance Sheet')
@section('content')

<div class="page-header">
    <div>
        <div style="font-size:13px;color:#9ca3af;margin-bottom:4px;">
            <a href="{{ route('corporate.sessions.show', $session) }}" style="color:#6b7280;text-decoration:none;">{{ $session->course_name }}</a>
            / Attendance
        </div>
        <h1 class="page-title">Attendance Sheet</h1>
        <p class="page-subtitle">{{ $session->project->company_name }} — {{ $session->training_date->format('d M Y') }}</p>
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
        <a href="{{ route('corporate.sessions.attendance.export', $session) }}"
           class="btn btn-secondary" style="font-size:13px;">⬇ Export CSV</a>
        <a href="{{ route('corporate.sessions.show', $session) }}"
           class="btn btn-secondary" style="font-size:13px;">← Back to Session</a>
    </div>
</div>

@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

{{-- Bulk mark --}}
<div class="card" style="margin-bottom:18px;">
    <div class="card-body" style="padding:14px 20px;display:flex;gap:12px;align-items:center;flex-wrap:wrap;">
        <span style="font-size:13.5px;font-weight:700;color:#374151;">Mark all as:</span>
        <form method="POST" action="{{ route('corporate.sessions.attendance.bulk', $session) }}" style="display:inline-flex;gap:8px;flex-wrap:wrap;">
            @csrf
            <button name="status" value="Present" class="btn btn-sm" style="background:#dcfce7;color:#16a34a;border:1px solid #86efac;font-weight:700;">✓ All Present</button>
            <button name="status" value="Absent" class="btn btn-sm" style="background:#fee2e2;color:#dc2626;border:1px solid #fca5a5;font-weight:700;">✗ All Absent</button>
            <button name="status" value="Partial" class="btn btn-sm" style="background:#fff7ed;color:#d97706;border:1px solid #fed7aa;font-weight:700;">~ All Partial</button>
        </form>
        <span style="font-size:12px;color:#9ca3af;margin-left:auto;">{{ $participants->count() }} participants</span>
    </div>
</div>

<form method="POST" action="{{ route('corporate.sessions.attendance.save', $session) }}">
@csrf
<div class="card">
    <div class="card-body" style="padding:0;overflow-x:auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:40px;">#</th>
                    <th>Name</th>
                    <th>Employee ID</th>
                    <th>Position / Dept</th>
                    <th style="width:200px;">Status</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                @forelse($participants as $i => $p)
                @php $att = $p->attendance; @endphp
                <tr id="row-{{ $p->id }}">
                    <td style="color:#9ca3af;font-size:13px;">{{ $i + 1 }}</td>
                    <td style="font-weight:700;">{{ $p->participant_name }}</td>
                    <td style="font-size:13px;color:#6b7280;">{{ $p->employee_id ?? '—' }}</td>
                    <td style="font-size:13px;color:#6b7280;">{{ $p->position }}{{ $p->department ? ' / '.$p->department : '' }}</td>
                    <td>
                        <input type="hidden" name="attendance[{{ $p->id }}][participant_id]" value="{{ $p->id }}">
                        <div style="display:flex;gap:0;border-radius:8px;overflow:hidden;border:1px solid #e5e7eb;">
                            @foreach(['Present' => ['#dcfce7','#16a34a','✓'], 'Absent' => ['#fee2e2','#dc2626','✗'], 'Partial' => ['#fff7ed','#d97706','~']] as $status => [$bg, $color, $icon])
                            <label style="flex:1;text-align:center;cursor:pointer;">
                                <input type="radio" name="attendance[{{ $p->id }}][status]" value="{{ $status }}"
                                       {{ ($att?->status ?? 'Absent') === $status ? 'checked' : '' }}
                                       style="display:none;"
                                       onchange="updateRow({{ $p->id }}, '{{ $bg }}', '{{ $color }}')">
                                <span class="att-btn att-{{ strtolower($status) }}-{{ $p->id }}"
                                      style="display:block;padding:5px 0;font-size:12px;font-weight:700;background:{{ ($att?->status ?? 'Absent') === $status ? $bg : '#fff' }};color:{{ ($att?->status ?? 'Absent') === $status ? $color : '#9ca3af' }};transition:.15s;">
                                    {{ $icon }} {{ $status }}
                                </span>
                            </label>
                            @endforeach
                        </div>
                    </td>
                    <td>
                        <input type="text" name="attendance[{{ $p->id }}][remarks]"
                               class="form-control" style="font-size:13px;padding:5px 8px;"
                               value="{{ $att?->remarks ?? '' }}" placeholder="optional">
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center;padding:40px;color:#9ca3af;">No participants in this session.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($participants->count())
    <div style="padding:16px 20px;border-top:1px solid #f0f2f5;display:flex;gap:10px;">
        <button type="submit" class="btn btn-primary">💾 Save Attendance</button>
        <a href="{{ route('corporate.sessions.show', $session) }}" class="btn btn-secondary">Cancel</a>
    </div>
    @endif
</div>
</form>

<script>
function updateRow(id, bg, color) {
    // highlight selected radio visually — handled by CSS re-render on next load
    // for immediate feedback, update button backgrounds
    const row = document.getElementById('row-' + id);
    if (!row) return;
    row.querySelectorAll('input[type=radio]').forEach(radio => {
        const span = radio.nextElementSibling;
        if (radio.checked) {
            span.style.background = bg;
            span.style.color = color;
        } else {
            span.style.background = '#fff';
            span.style.color = '#9ca3af';
        }
    });
}

// Init all rows on load
document.querySelectorAll('input[type=radio][name^="attendance"]').forEach(radio => {
    const colors = {Present:['#dcfce7','#16a34a'], Absent:['#fee2e2','#dc2626'], Partial:['#fff7ed','#d97706']};
    const [bg, color] = colors[radio.value] || ['#fff','#9ca3af'];
    if (radio.checked) {
        radio.nextElementSibling.style.background = bg;
        radio.nextElementSibling.style.color = color;
    }
    radio.addEventListener('change', () => {
        const rowId = radio.name.match(/\[(\d+)\]/)[1];
        updateRow(rowId, bg, color);
    });
});
</script>
@endsection
