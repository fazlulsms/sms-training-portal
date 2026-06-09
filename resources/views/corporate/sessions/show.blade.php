@extends('layouts.app')
@section('title', $session->course_name)
@section('content')

@php
    $sc  = \App\Models\CorporateSession::statusColors()[$session->status] ?? '#6b7280';
    $sbg = ['Planned'=>'#f0f4ff','Ongoing'=>'#f0fdf4','Completed'=>'#dbeafe','Cancelled'=>'#fee2e2'][$session->status] ?? '#f3f4f6';
@endphp

<style>
/* ── Breadcrumb ──────── */
.breadcrumb { display:flex; align-items:center; gap:6px; font-size:12.5px; color:#9ca3af; margin-bottom:6px; }
.breadcrumb a { color:#6b7280; text-decoration:none; font-weight:600; }
.breadcrumb a:hover { color:#1e3a8a; }
.breadcrumb-sep { color:#d1d5db; }

/* ── Quick action strip ──── */
.action-strip {
    background:#fff; border:1px solid #e5e9f0; border-radius:14px;
    padding:14px 20px; margin-bottom:20px;
    display:flex; gap:10px; align-items:center; flex-wrap:wrap;
    box-shadow:0 1px 3px rgba(15,23,42,.03);
}
.action-strip-label { font-size:12px; font-weight:800; color:#9ca3af; text-transform:uppercase; letter-spacing:.5px; margin-right:4px; }

/* ── Stat cards ──── */
.att-stats { display:grid; grid-template-columns:repeat(4,1fr); gap:12px; margin-bottom:20px; }
@media(max-width:600px){ .att-stats{ grid-template-columns:repeat(2,1fr); } }
.att-stat { background:#fff; border:1px solid #e5e9f0; border-radius:14px; padding:16px; text-align:center; position:relative; overflow:hidden; box-shadow:0 1px 3px rgba(15,23,42,.04); }
.att-stat-accent { position:absolute; top:0; left:0; right:0; height:3px; border-radius:14px 14px 0 0; }
.att-stat-num   { font-size:28px; font-weight:900; line-height:1; margin-bottom:4px; }
.att-stat-label { font-size:11px; color:#6b7280; font-weight:700; text-transform:uppercase; letter-spacing:.5px; }

/* ── Detail card ──── */
.detail-card { background:#fff; border:1px solid #e5e9f0; border-radius:16px; overflow:hidden; margin-bottom:16px; box-shadow:0 1px 4px rgba(15,23,42,.04); }
.detail-card-header { padding:14px 20px 12px; border-bottom:1px solid #f0f2f7; background:#fafbfd; display:flex; align-items:center; justify-content:space-between; gap:10px; }
.detail-card-title { font-size:13px; font-weight:800; color:#111827; text-transform:uppercase; letter-spacing:.3px; }

/* ── Meta rows ──── */
.meta-row { padding:10px 20px; border-bottom:1px solid #f4f5f8; display:flex; justify-content:space-between; align-items:center; gap:10px; font-size:13.5px; }
.meta-row:last-child { border-bottom:none; }
.meta-label { color:#9ca3af; font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.4px; flex-shrink:0; }
.meta-value { font-weight:700; color:#111827; text-align:right; }

/* ── Participant table ──── */
.p-table { width:100%; border-collapse:collapse; }
.p-table th { background:#fafbfd; padding:9px 16px; font-size:11.5px; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:.4px; border-bottom:1px solid #e9ecf0; text-align:left; }
.p-table td { padding:11px 16px; border-bottom:1px solid #f4f5f8; font-size:13.5px; vertical-align:middle; }
.p-table tr:last-child td { border-bottom:none; }
.p-table tr:hover td { background:#fafbfd; }

/* ── Status badge ──── */
.status-badge { padding:3px 11px; border-radius:20px; font-size:11.5px; font-weight:800; white-space:nowrap; }

/* ── Chip ──── */
.chip { display:inline-flex; align-items:center; gap:4px; background:#f4f6fb; border:1px solid #e5e9f0; padding:3px 9px; border-radius:20px; font-size:12px; font-weight:600; color:#4b5563; }

/* ── Evidence grid ──── */
.ev-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:8px; padding:14px 16px; }
.ev-thumb { height:66px; border-radius:9px; overflow:hidden; background:#f0f2f5; border:1px solid #e9ecf0; display:flex; align-items:center; justify-content:center; font-size:26px; }
.ev-thumb img { width:100%; height:100%; object-fit:cover; display:block; }
</style>

{{-- Breadcrumb --}}
<div class="breadcrumb">
    <a href="{{ route('corporate.projects.index') }}">Corporate</a>
    <span class="breadcrumb-sep">/</span>
    <a href="{{ route('corporate.projects.show', $session->project) }}">{{ $session->project->project_name }}</a>
    <span class="breadcrumb-sep">/</span>
    <span>{{ $session->course_name }}</span>
</div>

{{-- Page header --}}
<div class="page-header" style="margin-bottom:20px;">
    <div>
        <h1 class="page-title" style="margin-bottom:6px;">{{ $session->course_name }}</h1>
        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
            <span class="status-badge" style="background:{{ $sbg }};color:{{ $sc }};">{{ $session->status }}</span>
            <span style="font-size:13px;color:#6b7280;">{{ $session->project->company_name }}</span>
            @if($session->training_date)
            <span style="font-size:13px;color:#9ca3af;">·</span>
            <span style="font-size:13px;color:#6b7280;">📅 {{ $session->training_date->format('d M Y') }}
                @if($session->training_date_end && $session->training_date_end != $session->training_date)
                – {{ $session->training_date_end->format('d M Y') }}
                @endif
            </span>
            @endif
        </div>
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap;">
        <a href="{{ route('corporate.sessions.edit', $session) }}" class="btn btn-secondary">✏ Edit</a>
        <a href="{{ route('corporate.projects.report', $session->project) }}" class="btn btn-secondary">📊 Report</a>
    </div>
</div>

@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

{{-- Quick action strip --}}
<div class="action-strip">
    <span class="action-strip-label">Actions</span>
    <a href="{{ route('corporate.sessions.participants.index', $session) }}" class="btn btn-secondary" style="font-size:13px;">👥 Participants</a>
    <a href="{{ route('corporate.sessions.attendance', $session) }}" class="btn btn-sm" style="background:#eff6ff;color:#1e3a8a;border:1.5px solid #bfdbfe;font-weight:700;padding:7px 14px;border-radius:9px;font-size:13px;text-decoration:none;">✅ Mark Attendance</a>
    <a href="{{ route('corporate.sessions.certificates.index', $session) }}" class="btn btn-sm" style="background:#f5f3ff;color:#7c3aed;border:1.5px solid #ddd6fe;font-weight:700;padding:7px 14px;border-radius:9px;font-size:13px;text-decoration:none;">🏆 Certificates</a>
    <a href="{{ route('corporate.sessions.evidence.index', $session) }}" class="btn btn-sm" style="background:#fff7ed;color:#c2410c;border:1.5px solid #fed7aa;font-weight:700;padding:7px 14px;border-radius:9px;font-size:13px;text-decoration:none;">📷 Evidence</a>
    <a href="{{ route('corporate.sessions.evaluation.index', $session) }}" class="btn btn-sm" style="background:#fefce8;color:#854d0e;border:1.5px solid #fef08a;font-weight:700;padding:7px 14px;border-radius:9px;font-size:13px;text-decoration:none;">⭐ Evaluation</a>
    <a href="{{ route('corporate.sessions.certificates.zip', $session) }}" class="btn btn-sm" style="background:#f0fdf4;color:#16a34a;border:1.5px solid #bbf7d0;font-weight:700;padding:7px 14px;border-radius:9px;font-size:13px;text-decoration:none;">⬇ ZIP</a>
</div>

{{-- Two-column layout --}}
<style>@media(max-width:768px){.session-two-col{grid-template-columns:1fr!important;}}</style>
<div style="display:grid;grid-template-columns:1fr 300px;gap:18px;align-items:start;" class="session-two-col">

    {{-- LEFT: stats + participants --}}
    <div>
        {{-- Attendance stats --}}
        <div class="att-stats">
            <div class="att-stat">
                <div class="att-stat-accent" style="background:#6b7280;"></div>
                <div class="att-stat-num" style="color:#374151;">{{ $attendanceSummary['total'] }}</div>
                <div class="att-stat-label">Total</div>
            </div>
            <div class="att-stat">
                <div class="att-stat-accent" style="background:#16a34a;"></div>
                <div class="att-stat-num" style="color:#16a34a;">{{ $attendanceSummary['present'] }}</div>
                <div class="att-stat-label">Present</div>
            </div>
            <div class="att-stat">
                <div class="att-stat-accent" style="background:#dc2626;"></div>
                <div class="att-stat-num" style="color:#dc2626;">{{ $attendanceSummary['absent'] }}</div>
                <div class="att-stat-label">Absent</div>
            </div>
            <div class="att-stat">
                <div class="att-stat-accent" style="background:#d97706;"></div>
                <div class="att-stat-num" style="color:#d97706;">{{ $attendanceSummary['partial'] }}</div>
                <div class="att-stat-label">Partial</div>
            </div>
        </div>

        {{-- Participants table --}}
        <div class="detail-card">
            <div class="detail-card-header">
                <div style="display:flex;align-items:center;gap:10px;">
                    <span class="detail-card-title">Participants</span>
                    <span style="background:#f0f4ff;color:#1e3a8a;padding:2px 8px;border-radius:20px;font-size:12px;font-weight:800;">{{ $session->participants->count() }}</span>
                </div>
                <div style="display:flex;gap:8px;">
                    <a href="{{ route('corporate.sessions.participants.create', $session) }}" class="btn btn-sm btn-primary" style="font-size:12px;padding:5px 12px;">+ Add</a>
                    <a href="{{ route('corporate.sessions.participants.index', $session) }}" style="font-size:12.5px;color:#1e3a8a;font-weight:700;text-decoration:none;padding:5px 0;">Manage all →</a>
                </div>
            </div>
            <div style="overflow-x:auto;">
                <table class="p-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Position / Dept</th>
                            <th>Attendance</th>
                            <th>Certificate</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($session->participants->take(12) as $p)
                        @php
                            $att = $p->attendance;
                            $aColor = match($att?->status) { 'Present'=>'#16a34a','Absent'=>'#dc2626','Partial'=>'#d97706', default=>'#9ca3af' };
                            $aBg    = match($att?->status) { 'Present'=>'#dcfce7','Absent'=>'#fee2e2','Partial'=>'#fff7ed', default=>'#f3f4f6' };
                        @endphp
                        <tr>
                            <td style="font-weight:700;">{{ $p->participant_name }}</td>
                            <td>
                                @if($p->position || $p->department)
                                <span class="chip">{{ $p->position }}{{ $p->department ? ' · '.$p->department : '' }}</span>
                                @else <span style="color:#d1d5db;">—</span> @endif
                            </td>
                            <td>
                                <span class="status-badge" style="background:{{ $aBg }};color:{{ $aColor }};">
                                    {{ $att?->status ?? 'Not Marked' }}
                                </span>
                            </td>
                            <td>
                                @if($p->certificate)
                                <a href="{{ route('corporate.certificates.view', $p->certificate) }}" target="_blank"
                                   style="font-size:12px;font-family:monospace;font-weight:700;color:#7c3aed;text-decoration:none;">
                                    {{ $p->certificate->certificate_number }}
                                </a>
                                @else <span style="color:#d1d5db;font-size:13px;">—</span> @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" style="text-align:center;padding:36px;color:#9ca3af;">
                                No participants yet.
                                <a href="{{ route('corporate.sessions.participants.create', $session) }}" style="color:#1e3a8a;font-weight:700;margin-left:4px;">Add first →</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($session->participants->count() > 12)
            <div style="padding:10px 20px;border-top:1px solid #f0f2f5;text-align:center;font-size:13px;color:#6b7280;">
                + {{ $session->participants->count() - 12 }} more —
                <a href="{{ route('corporate.sessions.participants.index', $session) }}" style="color:#1e3a8a;font-weight:700;">View all</a>
            </div>
            @endif
        </div>
    </div>

    {{-- RIGHT SIDEBAR --}}
    <aside>
        {{-- Session details --}}
        <div class="detail-card" style="margin-bottom:14px;">
            <div class="detail-card-header">
                <span class="detail-card-title">Session Details</span>
            </div>
            @php $detailRows = [
                ['Status',   '<span class="status-badge" style="background:'.$sbg.';color:'.$sc.';">'.$session->status.'</span>'],
                ['Date',     $session->training_date->format('d M Y').($session->training_date_end && $session->training_date_end != $session->training_date ? ' – '.$session->training_date_end->format('d M Y') : '')],
                ['Duration', $session->duration ?? '—'],
                ['Trainer',  $session->trainer_name ?? '—'],
                ['Venue',    $session->venue ?? '—'],
                ['Target',   $session->target_group ?? '—'],
            ]; @endphp
            @foreach($detailRows as [$label, $value])
            <div class="meta-row">
                <span class="meta-label">{{ $label }}</span>
                <span class="meta-value">{!! $value !!}</span>
            </div>
            @endforeach
            @if($session->description)
            <div style="padding:12px 20px;font-size:13px;color:#6b7280;line-height:1.65;border-top:1px solid #f4f5f8;">
                {{ $session->description }}
            </div>
            @endif
        </div>

        {{-- Evaluation summary --}}
        @if($avgScore)
        <div class="detail-card" style="margin-bottom:14px;">
            <div class="detail-card-header">
                <span class="detail-card-title">Evaluation</span>
                <a href="{{ route('corporate.sessions.evaluation.index', $session) }}" style="font-size:12px;color:#1e3a8a;font-weight:700;text-decoration:none;">View →</a>
            </div>
            <div style="padding:18px 20px;text-align:center;">
                <div style="font-size:38px;font-weight:900;color:#1e3a8a;line-height:1;">{{ number_format($avgScore,1) }}</div>
                <div style="font-size:11px;color:#9ca3af;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px;">/5 average score</div>
                <div style="color:#f59e0b;font-size:22px;letter-spacing:2px;">
                    {{ str_repeat('★', round($avgScore)) }}<span style="color:#e5e7eb;">{{ str_repeat('★', 5-round($avgScore)) }}</span>
                </div>
                <div style="font-size:12px;color:#9ca3af;margin-top:6px;">{{ $session->evaluations->count() }} response(s)</div>
            </div>
        </div>
        @endif

        {{-- Evidence thumbnails --}}
        @if($session->evidences->count())
        <div class="detail-card">
            <div class="detail-card-header">
                <span class="detail-card-title">Evidence</span>
                <div style="display:flex;align-items:center;gap:8px;">
                    <span style="background:#f0f4ff;color:#1e3a8a;padding:2px 8px;border-radius:20px;font-size:12px;font-weight:800;">{{ $session->evidences->count() }}</span>
                    <a href="{{ route('corporate.sessions.evidence.index', $session) }}" style="font-size:12px;color:#1e3a8a;font-weight:700;text-decoration:none;">View →</a>
                </div>
            </div>
            <div class="ev-grid">
                @foreach($session->evidences->take(6) as $ev)
                @php $ext = strtolower(pathinfo($ev->original_name ?? $ev->file_path, PATHINFO_EXTENSION)); @endphp
                <div class="ev-thumb">
                    @if(in_array($ext, ['jpg','jpeg','png','gif','webp']))
                        <img src="{{ asset('storage/'.$ev->file_path) }}" alt="">
                    @else
                        {{ in_array($ext, ['pdf']) ? '📄' : (in_array($ext, ['pptx','ppt']) ? '📊' : '📝') }}
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Danger zone --}}
        <div style="margin-top:14px;">
            <form method="POST" action="{{ route('corporate.sessions.destroy', $session) }}"
                  onsubmit="return confirm('Delete this session and ALL its data?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger" style="width:100%;font-size:13px;">🗑 Delete Session</button>
            </form>
        </div>
    </aside>
</div>
@endsection
