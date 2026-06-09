@extends('layouts.app')
@section('title','Instructor-Led Training Reports')
@section('content')

@php
// Reuse same CSS defined in elearning view — inline it here too
@endphp
<style>
.rpt-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:20px;}
@media(max-width:1100px){.rpt-stats{grid-template-columns:repeat(3,1fr);}}
@media(max-width:640px){.rpt-stats{grid-template-columns:repeat(2,1fr);}}
.rpt-stat{background:#fff;border:1px solid #e5e9f0;border-radius:14px;padding:16px 18px 13px;position:relative;overflow:hidden;box-shadow:0 1px 4px rgba(15,23,42,.04);}
.rpt-stat-accent{position:absolute;top:0;left:0;right:0;height:3px;border-radius:14px 14px 0 0;}
.rpt-stat-icon{font-size:20px;margin-bottom:8px;}
.rpt-stat-num{font-size:24px;font-weight:900;line-height:1;margin-bottom:3px;}
.rpt-stat-label{font-size:11px;color:#6b7280;font-weight:700;text-transform:uppercase;letter-spacing:.5px;}
.rpt-filter{background:#fff;border:1px solid #e5e9f0;border-radius:14px;padding:14px 18px;margin-bottom:16px;box-shadow:0 1px 3px rgba(15,23,42,.03);}
.rpt-filter-row{display:flex;gap:8px;align-items:flex-end;flex-wrap:wrap;}
.rft{height:36px;padding:0 10px;border:1.5px solid #e5e9f0;border-radius:8px;font-size:13px;font-family:inherit;color:#374151;background:#fafbfd;outline:none;}
.rft:focus{border-color:#1e3a8a;box-shadow:0 0 0 3px rgba(30,58,138,.08);}
.rft-search{padding-left:32px;min-width:200px;flex:1;}
.rft-wrap{position:relative;flex:1;min-width:180px;}
.rft-icon{position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#9ca3af;pointer-events:none;}
.rpt-charts{display:grid;grid-template-columns:2fr 1fr;gap:16px;margin-bottom:20px;}
@media(max-width:900px){.rpt-charts{grid-template-columns:1fr;}}
.chart-card{background:#fff;border:1px solid #e5e9f0;border-radius:14px;padding:18px 20px;box-shadow:0 1px 4px rgba(15,23,42,.04);}
.chart-card-title{font-size:13px;font-weight:800;color:#374151;margin-bottom:14px;text-transform:uppercase;letter-spacing:.4px;}
.export-bar{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px;align-items:center;}
.export-bar span{font-size:12.5px;color:#9ca3af;font-weight:700;margin-right:4px;}
.btn-export-pdf{background:#ef4444;color:#fff;border:none;padding:7px 14px;border-radius:8px;font-size:12.5px;font-weight:700;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:5px;}
.btn-export-csv{background:#16a34a;color:#fff;border:none;padding:7px 14px;border-radius:8px;font-size:12.5px;font-weight:700;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:5px;}
.btn-export-excel{background:#1d6c3a;color:#fff;border:none;padding:7px 14px;border-radius:8px;font-size:12.5px;font-weight:700;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:5px;}
.btn-export-pdf:hover,.btn-export-csv:hover,.btn-export-excel:hover{opacity:.88;}
</style>

<div class="page-header">
    <div>
        <div style="font-size:13px;color:#9ca3af;margin-bottom:4px;">
            <a href="{{ route('reports.index') }}" style="color:#6b7280;text-decoration:none;">Reports</a> /
        </div>
        <h1 class="page-title">Instructor-Led Training Reports</h1>
        <p class="page-subtitle">Session-based training, attendance, trainer performance, and facility coverage</p>
    </div>
</div>

{{-- Summary stat cards --}}
<div class="rpt-stats">
    <div class="rpt-stat">
        <div class="rpt-stat-accent" style="background:#1e3a8a;"></div>
        <div class="rpt-stat-icon">📖</div>
        <div class="rpt-stat-num" style="color:#1e3a8a;">{{ $stats['total_manual_courses'] }}</div>
        <div class="rpt-stat-label">Manual Courses</div>
    </div>
    <div class="rpt-stat">
        <div class="rpt-stat-accent" style="background:#d97706;"></div>
        <div class="rpt-stat-icon">📅</div>
        <div class="rpt-stat-num" style="color:#d97706;">{{ number_format($stats['total_sessions']) }}</div>
        <div class="rpt-stat-label">Sessions</div>
    </div>
    <div class="rpt-stat">
        <div class="rpt-stat-accent" style="background:#0891b2;"></div>
        <div class="rpt-stat-icon">👥</div>
        <div class="rpt-stat-num" style="color:#0891b2;">{{ number_format($stats['total_participants']) }}</div>
        <div class="rpt-stat-label">Participants</div>
    </div>
    <div class="rpt-stat">
        <div class="rpt-stat-accent" style="background:#16a34a;"></div>
        <div class="rpt-stat-icon">✅</div>
        <div class="rpt-stat-num" style="color:#16a34a;">{{ number_format($stats['present']) }}</div>
        <div class="rpt-stat-label">Present</div>
    </div>
    <div class="rpt-stat">
        <div class="rpt-stat-accent" style="background:#dc2626;"></div>
        <div class="rpt-stat-icon">❌</div>
        <div class="rpt-stat-num" style="color:#dc2626;">{{ number_format($stats['absent']) }}</div>
        <div class="rpt-stat-label">Absent</div>
    </div>
    <div class="rpt-stat">
        <div class="rpt-stat-accent" style="background:#7c3aed;"></div>
        <div class="rpt-stat-icon">🎖</div>
        <div class="rpt-stat-num" style="color:#7c3aed;">{{ number_format($stats['certificates']) }}</div>
        <div class="rpt-stat-label">Certificates</div>
    </div>
    <div class="rpt-stat">
        <div class="rpt-stat-accent" style="background:#6b7280;"></div>
        <div class="rpt-stat-icon">⏱</div>
        <div class="rpt-stat-num" style="color:#6b7280;">{{ number_format($stats['total_hours'],0) }}</div>
        <div class="rpt-stat-label">Training Hours</div>
    </div>
    <div class="rpt-stat">
        <div class="rpt-stat-accent" style="background:#0891b2;"></div>
        <div class="rpt-stat-icon">🏢</div>
        <div class="rpt-stat-num" style="color:#0891b2;">{{ $stats['facilities'] }}</div>
        <div class="rpt-stat-label">Facilities</div>
    </div>
    <div class="rpt-stat">
        <div class="rpt-stat-accent" style="background:#d97706;"></div>
        <div class="rpt-stat-icon">🌆</div>
        <div class="rpt-stat-num" style="color:#d97706;">{{ $stats['cities'] }}</div>
        <div class="rpt-stat-label">Cities</div>
    </div>
    <div class="rpt-stat">
        <div class="rpt-stat-accent" style="background:#16a34a;"></div>
        <div class="rpt-stat-icon">🌍</div>
        <div class="rpt-stat-num" style="color:#16a34a;">{{ $stats['countries'] }}</div>
        <div class="rpt-stat-label">Countries</div>
    </div>
    <div class="rpt-stat">
        <div class="rpt-stat-accent" style="background:#1e3a8a;"></div>
        <div class="rpt-stat-icon">🧑‍🏫</div>
        <div class="rpt-stat-num" style="color:#1e3a8a;">{{ $stats['trainers'] }}</div>
        <div class="rpt-stat-label">Trainers</div>
    </div>
</div>

{{-- Charts --}}
<div class="rpt-charts">
    <div class="chart-card">
        <div class="chart-card-title">📅 Monthly Participant Trend</div>
        <canvas id="monthlyChart" height="90"></canvas>
    </div>
    <div class="chart-card">
        <div class="chart-card-title">📊 Attendance Breakdown</div>
        <canvas id="attChart" height="140"></canvas>
    </div>
</div>

{{-- Filter --}}
<div class="rpt-filter">
    <form method="GET" action="{{ route('reports.ilt') }}">
        <div class="rpt-filter-row">
            <div class="rft-wrap">
                <span class="rft-icon"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></span>
                <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Search participant, company…" class="rft rft-search">
            </div>
            <select name="course_id" class="rft" style="min-width:150px;">
                <option value="">All Courses</option>
                @foreach($courses as $c)
                <option value="{{ $c->id }}" {{ ($filters['course_id'] ?? '') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
            <select name="trainer_id" class="rft" style="min-width:140px;">
                <option value="">All Trainers</option>
                @foreach($trainers as $t)
                <option value="{{ $t->id }}" {{ ($filters['trainer_id'] ?? '') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                @endforeach
            </select>
            <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="rft" title="From">
            <input type="date" name="date_to"   value="{{ $filters['date_to']   ?? '' }}" class="rft" title="To">
            <select name="country" class="rft" style="min-width:120px;">
                <option value="">All Countries</option>
                @foreach($countries as $c)
                <option value="{{ $c }}" {{ ($filters['country'] ?? '') === $c ? 'selected' : '' }}>{{ $c }}</option>
                @endforeach
            </select>
            <select name="attendance_status" class="rft">
                <option value="">Attendance</option>
                @foreach(['Present','Absent','Partial'] as $s)
                <option value="{{ $s }}" {{ ($filters['attendance_status'] ?? '') === $s ? 'selected' : '' }}>{{ $s }}</option>
                @endforeach
            </select>
            <select name="certificate_status" class="rft">
                <option value="">Certificate</option>
                <option value="issued"  {{ ($filters['certificate_status'] ?? '') === 'issued'  ? 'selected' : '' }}>Issued</option>
                <option value="pending" {{ ($filters['certificate_status'] ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
            </select>
            <button type="submit" class="btn btn-primary" style="height:36px;padding:0 16px;font-size:13px;">Filter</button>
            @if(collect($filters)->filter()->isNotEmpty())
            <a href="{{ route('reports.ilt') }}" style="font-size:12.5px;color:#9ca3af;font-weight:600;padding:4px 8px;border-radius:7px;text-decoration:none;white-space:nowrap;">✕ Clear</a>
            @endif
        </div>
    </form>
</div>

{{-- Export bar --}}
<div class="export-bar">
    <span>Export:</span>
    <a href="{{ route('reports.ilt.pdf',   request()->query()) }}" class="btn-export-pdf">📄 PDF</a>
    <a href="{{ route('reports.ilt.csv',   request()->query()) }}" class="btn-export-csv">⬇ CSV</a>
    <a href="{{ route('reports.ilt.excel', request()->query()) }}" class="btn-export-excel">📊 Excel</a>
    <span style="margin-left:auto;font-size:12.5px;color:#9ca3af;font-weight:600;">{{ $enrollments->total() }} record(s)</span>
</div>

{{-- Data table --}}
<div class="dt-wrap">
    <div class="dt-scroll">
        <table class="dt">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Course</th>
                    <th>Batch</th>
                    <th>Trainer</th>
                    <th>Venue</th>
                    <th>Participant</th>
                    <th>Company</th>
                    <th>Country</th>
                    <th class="c">Attendance</th>
                    <th class="c">Certificate</th>
                </tr>
            </thead>
            <tbody>
                @forelse($enrollments as $e)
                @php
                    $attColor = match($e->attendance_status) {
                        'Present' => ['#dcfce7','#16a34a'],
                        'Absent'  => ['#fee2e2','#dc2626'],
                        'Partial' => ['#fff7ed','#d97706'],
                        default   => ['#f3f4f6','#6b7280'],
                    };
                @endphp
                <tr>
                    <td class="text-muted text-small">{{ $enrollments->firstItem() + $loop->index }}</td>
                    <td style="font-size:12.5px;white-space:nowrap;color:#6b7280;">{{ $e->trainingSchedule?->start_date?->format('d M Y') ?? '—' }}</td>
                    <td style="font-size:13px;font-weight:600;color:#1e3a8a;max-width:160px;">{{ $e->trainingSchedule?->course?->name ?? '—' }}</td>
                    <td style="font-size:12.5px;color:#6b7280;">{{ $e->trainingSchedule?->batch_code ?? '—' }}</td>
                    <td style="font-size:13px;">{{ $e->trainingSchedule?->trainer?->name ?? '—' }}</td>
                    <td style="font-size:12.5px;color:#6b7280;">{{ $e->trainingSchedule?->venue ?? '—' }}</td>
                    <td>
                        <div style="font-weight:700;font-size:13.5px;">{{ $e->full_name }}</div>
                        <div style="font-size:12px;color:#9ca3af;">{{ $e->email }}</div>
                    </td>
                    <td style="font-size:13px;">{{ $e->company ?? '—' }}</td>
                    <td style="font-size:13px;">{{ $e->country ?? '—' }}</td>
                    <td class="c">
                        <span style="background:{{ $attColor[0] }};color:{{ $attColor[1] }};padding:2px 8px;border-radius:20px;font-size:11.5px;font-weight:700;">
                            {{ $e->attendance_status ?? 'Pending' }}
                        </span>
                    </td>
                    <td class="c">
                        @if($e->certificate_generated)
                        <span style="background:#ede9fe;color:#7c3aed;padding:2px 8px;border-radius:20px;font-size:11.5px;font-weight:700;">Issued</span>
                        @else
                        <span style="background:#f3f4f6;color:#9ca3af;padding:2px 8px;border-radius:20px;font-size:11.5px;font-weight:700;">Pending</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="11" style="text-align:center;padding:48px;color:#9ca3af;">
                    <div style="font-size:32px;margin-bottom:8px;">🎓</div>
                    No records found for the selected filters.
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($enrollments->hasPages())
    <div style="padding:16px 20px;border-top:1px solid #f0f2f5;">{{ $enrollments->links() }}</div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const monthlyData = @json($monthly);
new Chart(document.getElementById('monthlyChart'), {
    type: 'bar',
    data: {
        labels: monthlyData.map(d => d.month),
        datasets: [{ label: 'Participants', data: monthlyData.map(d => d.count),
            backgroundColor: 'rgba(30,58,138,.7)', borderRadius: 6 }]
    },
    options: { responsive:true, plugins:{ legend:{ display:false } },
               scales:{ y:{ beginAtZero:true, ticks:{font:{size:11}} }, x:{ ticks:{font:{size:11}} } } }
});
new Chart(document.getElementById('attChart'), {
    type: 'doughnut',
    data: {
        labels: ['Present','Absent','Pending'],
        datasets: [{ data: [{{ $stats['present'] }}, {{ $stats['absent'] }}, {{ max(0,$stats['total_participants']-$stats['present']-$stats['absent']) }}],
            backgroundColor: ['#16a34a','#dc2626','#e5e9f0'], borderWidth:0 }]
    },
    options: { responsive:true, plugins:{ legend:{ position:'bottom', labels:{ font:{size:11} } } } }
});
</script>
@endsection
