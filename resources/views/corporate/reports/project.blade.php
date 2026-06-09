@extends('layouts.app')
@section('title', 'Project Report')
@section('content')

<div class="page-header">
    <div>
        <div style="font-size:13px;color:#9ca3af;margin-bottom:4px;">
            <a href="{{ route('corporate.projects.show', $project) }}" style="color:#6b7280;text-decoration:none;">{{ $project->project_name }}</a>
            / Report
        </div>
        <h1 class="page-title">Project Summary Report</h1>
        <p class="page-subtitle">{{ $project->company_name }}</p>
    </div>
    <div style="display:flex;gap:8px;">
        <button onclick="window.print()" class="btn btn-secondary">🖨 Print Report</button>
        <a href="{{ route('corporate.projects.show', $project) }}" class="btn btn-secondary">← Back</a>
    </div>
</div>

<style>
@media print {
    .page-header .btn, nav, aside, .sidebar { display: none !important; }
    .card { box-shadow: none; border: 1px solid #ddd; }
}
</style>

{{-- Project info --}}
@php $sc = \App\Models\CorporateProject::statusColors()[$project->status] ?? '#6b7280'; @endphp
<div class="card" style="margin-bottom:20px;">
    <div class="card-header"><h3 class="card-title">Project Information</h3></div>
    <div class="card-body" style="display:grid;grid-template-columns:repeat(3,1fr);gap:0;">
        @php $fields = [
            ['Company', $project->company_name],
            ['Project', $project->project_name],
            ['Status', '<span style="background:'.$sc.'22;color:'.$sc.';padding:2px 8px;border-radius:20px;font-size:12px;font-weight:700;">'.$project->status.'</span>'],
            ['Contact', $project->contact_person ?? '—'],
            ['Email', $project->email ?? '—'],
            ['Phone', $project->phone ?? '—'],
            ['Address', $project->address ?? '—'],
            ['Total Sessions', $project->sessions->count()],
            ['Total Participants', $project->sessions->sum(fn($s) => $s->participants->count())],
        ]; @endphp
        @foreach($fields as [$label, $value])
        <div style="padding:10px 16px;border-bottom:1px solid #f0f2f5;font-size:13.5px;">
            <div style="color:#9ca3af;font-size:11px;text-transform:uppercase;letter-spacing:.4px;margin-bottom:2px;">{{ $label }}</div>
            <div style="font-weight:700;">{!! $value !!}</div>
        </div>
        @endforeach
    </div>
</div>

{{-- Sessions --}}
@foreach($project->sessions as $session)
@php
    $present = $session->participants->filter(fn($p) => $p->attendance?->status === 'Present')->count();
    $absent  = $session->participants->filter(fn($p) => $p->attendance?->status === 'Absent')->count();
    $partial = $session->participants->filter(fn($p) => $p->attendance?->status === 'Partial')->count();
    $certs   = $session->certificates->count();
    $avgEval = $session->evaluations->avg('feedback_score');
    $ss = \App\Models\CorporateSession::statusColors()[$session->status] ?? '#6b7280';
@endphp

<div class="card" style="margin-bottom:20px;page-break-inside:avoid;">
    <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;">
        <h3 class="card-title">📚 {{ $session->course_name }}</h3>
        <span style="background:{{ $ss }}22;color:{{ $ss }};padding:3px 12px;border-radius:20px;font-size:12px;font-weight:700;">{{ $session->status }}</span>
    </div>
    <div class="card-body" style="padding:0;">

        {{-- Session meta --}}
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:0;border-bottom:1px solid #f0f2f5;">
            @foreach([
                ['Date', $session->training_date->format('d M Y').($session->training_date_end && $session->training_date_end != $session->training_date ? ' – '.$session->training_date_end->format('d M Y') : '')],
                ['Trainer', $session->trainer_name ?? '—'],
                ['Venue', $session->venue ?? '—'],
                ['Duration', $session->duration ?? '—'],
            ] as [$l, $v])
            <div style="padding:10px 16px;font-size:13px;">
                <div style="color:#9ca3af;font-size:11px;text-transform:uppercase;letter-spacing:.4px;margin-bottom:2px;">{{ $l }}</div>
                <div style="font-weight:700;">{{ $v }}</div>
            </div>
            @endforeach
        </div>

        {{-- Attendance summary --}}
        <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:0;border-bottom:1px solid #f0f2f5;background:#fafafa;">
            @foreach([
                ['Total', $session->participants->count(), '#374151'],
                ['Present', $present, '#16a34a'],
                ['Absent', $absent, '#dc2626'],
                ['Partial', $partial, '#d97706'],
                ['Certificates', $certs, '#7c3aed'],
            ] as [$l, $v, $c])
            <div style="padding:14px 16px;text-align:center;border-right:1px solid #f0f2f5;">
                <div style="font-size:22px;font-weight:900;color:{{ $c }};">{{ $v }}</div>
                <div style="font-size:11px;color:#6b7280;font-weight:700;text-transform:uppercase;">{{ $l }}</div>
            </div>
            @endforeach
        </div>

        {{-- Participants table --}}
        @if($session->participants->count())
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead style="background:#f8f9fa;">
                <tr>
                    <th style="padding:8px 16px;text-align:left;color:#6b7280;font-weight:700;border-bottom:1px solid #e9ecf0;">#</th>
                    <th style="padding:8px 16px;text-align:left;color:#6b7280;font-weight:700;border-bottom:1px solid #e9ecf0;">Name</th>
                    <th style="padding:8px 16px;text-align:left;color:#6b7280;font-weight:700;border-bottom:1px solid #e9ecf0;">Employee ID</th>
                    <th style="padding:8px 16px;text-align:left;color:#6b7280;font-weight:700;border-bottom:1px solid #e9ecf0;">Position / Dept</th>
                    <th style="padding:8px 16px;text-align:center;color:#6b7280;font-weight:700;border-bottom:1px solid #e9ecf0;">Attendance</th>
                    <th style="padding:8px 16px;text-align:left;color:#6b7280;font-weight:700;border-bottom:1px solid #e9ecf0;">Certificate No.</th>
                </tr>
            </thead>
            <tbody>
                @foreach($session->participants as $i => $p)
                @php
                    $att = $p->attendance;
                    $attColor = match($att?->status) { 'Present'=>'#16a34a','Absent'=>'#dc2626','Partial'=>'#d97706', default=>'#9ca3af' };
                @endphp
                <tr style="border-bottom:1px solid #f0f2f5;">
                    <td style="padding:8px 16px;color:#9ca3af;">{{ $i+1 }}</td>
                    <td style="padding:8px 16px;font-weight:700;">{{ $p->participant_name }}</td>
                    <td style="padding:8px 16px;color:#6b7280;">{{ $p->employee_id ?? '—' }}</td>
                    <td style="padding:8px 16px;color:#6b7280;">{{ $p->position }}{{ $p->department ? ' / '.$p->department : '' }}</td>
                    <td style="padding:8px 16px;text-align:center;">
                        <span style="background:{{ $attColor }}22;color:{{ $attColor }};padding:2px 8px;border-radius:20px;font-size:11.5px;font-weight:700;">
                            {{ $att?->status ?? 'Not Marked' }}
                        </span>
                    </td>
                    <td style="padding:8px 16px;font-family:monospace;font-size:12px;color:#7c3aed;font-weight:700;">
                        {{ $p->certificate?->certificate_number ?? '—' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        {{-- Evaluation & Evidence summary --}}
        @if($avgEval || $session->evidences->count())
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:0;border-top:1px solid #f0f2f5;">
            @if($avgEval)
            <div style="padding:14px 20px;border-right:1px solid #f0f2f5;">
                <div style="font-size:12px;color:#6b7280;font-weight:700;text-transform:uppercase;letter-spacing:.4px;margin-bottom:4px;">Evaluation Score</div>
                <span style="color:#f59e0b;font-size:18px;">{{ str_repeat('★', round($avgEval)) }}<span style="color:#d1d5db;">{{ str_repeat('★', 5-round($avgEval)) }}</span></span>
                <span style="font-size:13px;color:#374151;font-weight:700;margin-left:6px;">{{ number_format($avgEval,1) }}/5</span>
                <span style="font-size:12px;color:#9ca3af;margin-left:4px;">({{ $session->evaluations->count() }} responses)</span>
            </div>
            @endif
            @if($session->evidences->count())
            <div style="padding:14px 20px;">
                <div style="font-size:12px;color:#6b7280;font-weight:700;text-transform:uppercase;letter-spacing:.4px;margin-bottom:4px;">Evidence Files</div>
                @foreach($session->evidences->groupBy('type') as $type => $items)
                <span style="background:#f0f4ff;color:#1e3a8a;padding:2px 8px;border-radius:20px;font-size:12px;font-weight:700;margin-right:4px;margin-bottom:4px;display:inline-block;">
                    {{ $type }}: {{ $items->count() }}
                </span>
                @endforeach
            </div>
            @endif
        </div>
        @endif
    </div>
</div>
@endforeach

@if($project->remarks)
<div class="card">
    <div class="card-header"><h3 class="card-title">Project Remarks</h3></div>
    <div class="card-body" style="font-size:14px;color:#374151;line-height:1.7;">{{ $project->remarks }}</div>
</div>
@endif

<div style="margin-top:24px;text-align:center;font-size:12px;color:#9ca3af;">
    Report generated on {{ now()->format('d F Y, H:i') }} — SMS Training Services
</div>
@endsection
