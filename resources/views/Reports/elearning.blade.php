@extends('layouts.app')
@section('title','eLearning Reports')
@section('content')

<style>
/* ── Report layout ─────────── */
.rpt-stats { display:grid; grid-template-columns:repeat(5,1fr); gap:12px; margin-bottom:20px; }
@media(max-width:1100px){ .rpt-stats{grid-template-columns:repeat(3,1fr);} }
@media(max-width:640px) { .rpt-stats{grid-template-columns:repeat(2,1fr);} }
.rpt-stat {
    background:#fff; border:1px solid #e5e9f0; border-radius:14px;
    padding:16px 18px 13px; position:relative; overflow:hidden;
    box-shadow:0 1px 4px rgba(15,23,42,.04);
}
.rpt-stat-accent { position:absolute; top:0; left:0; right:0; height:3px; border-radius:14px 14px 0 0; }
.rpt-stat-icon   { font-size:20px; margin-bottom:8px; }
.rpt-stat-num    { font-size:24px; font-weight:900; line-height:1; margin-bottom:3px; }
.rpt-stat-label  { font-size:11px; color:#6b7280; font-weight:700; text-transform:uppercase; letter-spacing:.5px; }

/* ── Compact filter bar ──────── */
.rpt-filter {
    background:#fff; border:1px solid #e5e9f0; border-radius:14px;
    padding:14px 18px; margin-bottom:16px;
    box-shadow:0 1px 3px rgba(15,23,42,.03);
}
.rpt-filter-row { display:flex; gap:8px; align-items:flex-end; flex-wrap:wrap; }
.rft { height:36px; padding:0 10px; border:1.5px solid #e5e9f0; border-radius:8px; font-size:13px; font-family:inherit; color:#374151; background:#fafbfd; outline:none; }
.rft:focus { border-color:#1e3a8a; box-shadow:0 0 0 3px rgba(30,58,138,.08); }
.rft-search { padding-left:32px; min-width:200px; flex:1; }
.rft-wrap { position:relative; flex:1; min-width:180px; }
.rft-icon { position:absolute; left:10px; top:50%; transform:translateY(-50%); color:#9ca3af; pointer-events:none; }

/* ── Chart row ──── */
.rpt-charts { display:grid; grid-template-columns:2fr 1fr; gap:16px; margin-bottom:20px; }
@media(max-width:900px){ .rpt-charts{grid-template-columns:1fr;} }
.chart-card { background:#fff; border:1px solid #e5e9f0; border-radius:14px; padding:18px 20px; box-shadow:0 1px 4px rgba(15,23,42,.04); }
.chart-card-title { font-size:13px; font-weight:800; color:#374151; margin-bottom:14px; text-transform:uppercase; letter-spacing:.4px; }

/* ── Export bar ──── */
.export-bar { display:flex; gap:8px; flex-wrap:wrap; margin-bottom:16px; align-items:center; }
.export-bar span { font-size:12.5px; color:#9ca3af; font-weight:700; margin-right:4px; }
.btn-export-pdf   { background:#ef4444; color:#fff; border:none; padding:7px 14px; border-radius:8px; font-size:12.5px; font-weight:700; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:5px; }
.btn-export-csv   { background:#16a34a; color:#fff; border:none; padding:7px 14px; border-radius:8px; font-size:12.5px; font-weight:700; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:5px; }
.btn-export-excel { background:#1d6c3a; color:#fff; border:none; padding:7px 14px; border-radius:8px; font-size:12.5px; font-weight:700; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:5px; }
.btn-export-pdf:hover,.btn-export-csv:hover,.btn-export-excel:hover { opacity:.88; }
</style>

{{-- Page header --}}
<div class="page-header">
    <div>
        <div style="font-size:13px;color:#9ca3af;margin-bottom:4px;">
            <a href="{{ route('reports.index') }}" style="color:#6b7280;text-decoration:none;">Reports</a> /
        </div>
        <h1 class="page-title">eLearning Reports</h1>
        <p class="page-subtitle">Self-paced course enrollments, completions, and revenue</p>
    </div>
</div>

{{-- Summary stat cards --}}
<div class="rpt-stats">
    <div class="rpt-stat">
        <div class="rpt-stat-accent" style="background:#2563eb;"></div>
        <div class="rpt-stat-icon">📚</div>
        <div class="rpt-stat-num" style="color:#2563eb;">{{ $stats['total_courses'] }}</div>
        <div class="rpt-stat-label">Total Courses</div>
    </div>
    <div class="rpt-stat">
        <div class="rpt-stat-accent" style="background:#16a34a;"></div>
        <div class="rpt-stat-icon">✅</div>
        <div class="rpt-stat-num" style="color:#16a34a;">{{ $stats['published_courses'] }}</div>
        <div class="rpt-stat-label">Published</div>
    </div>
    <div class="rpt-stat">
        <div class="rpt-stat-accent" style="background:#0891b2;"></div>
        <div class="rpt-stat-icon">👥</div>
        <div class="rpt-stat-num" style="color:#0891b2;">{{ number_format($stats['total_enrollments']) }}</div>
        <div class="rpt-stat-label">Enrollments</div>
    </div>
    <div class="rpt-stat">
        <div class="rpt-stat-accent" style="background:#d97706;"></div>
        <div class="rpt-stat-icon">⏳</div>
        <div class="rpt-stat-num" style="color:#d97706;">{{ number_format($stats['in_progress']) }}</div>
        <div class="rpt-stat-label">In Progress</div>
    </div>
    <div class="rpt-stat">
        <div class="rpt-stat-accent" style="background:#7c3aed;"></div>
        <div class="rpt-stat-icon">🏆</div>
        <div class="rpt-stat-num" style="color:#7c3aed;">{{ number_format($stats['completed']) }}</div>
        <div class="rpt-stat-label">Completed</div>
    </div>
    <div class="rpt-stat">
        <div class="rpt-stat-accent" style="background:#7c3aed;"></div>
        <div class="rpt-stat-icon">🎖</div>
        <div class="rpt-stat-num" style="color:#7c3aed;">{{ number_format($stats['certificates']) }}</div>
        <div class="rpt-stat-label">Certificates</div>
    </div>
    <div class="rpt-stat">
        <div class="rpt-stat-accent" style="background:#0891b2;"></div>
        <div class="rpt-stat-icon">📈</div>
        <div class="rpt-stat-num" style="color:#0891b2;">{{ $stats['completion_pct'] }}%</div>
        <div class="rpt-stat-label">Completion Rate</div>
    </div>
    <div class="rpt-stat">
        <div class="rpt-stat-accent" style="background:#16a34a;"></div>
        <div class="rpt-stat-icon">💰</div>
        <div class="rpt-stat-num" style="color:#16a34a;">{{ number_format($stats['paid_amount'],0) }}</div>
        <div class="rpt-stat-label">Paid (BDT)</div>
    </div>
    <div class="rpt-stat">
        <div class="rpt-stat-accent" style="background:#dc2626;"></div>
        <div class="rpt-stat-icon">⚠️</div>
        <div class="rpt-stat-num" style="color:#dc2626;">{{ number_format($stats['due_amount'],0) }}</div>
        <div class="rpt-stat-label">Due (BDT)</div>
    </div>
    <div class="rpt-stat" style="background:linear-gradient(135deg,#eff6ff,#f0fdf4);">
        <div class="rpt-stat-accent" style="background:#60a5fa;"></div>
        <div class="rpt-stat-icon">📊</div>
        <div class="rpt-stat-num" style="color:#1e3a8a;">{{ $enrollments->total() }}</div>
        <div class="rpt-stat-label">Filtered Results</div>
    </div>
</div>

{{-- Charts --}}
<div class="rpt-charts">
    <div class="chart-card">
        <div class="chart-card-title">📅 Monthly Enrollment Trend</div>
        <canvas id="monthlyChart" height="90"></canvas>
    </div>
    <div class="chart-card">
        <div class="chart-card-title">📊 Status Breakdown</div>
        <canvas id="statusChart" height="140"></canvas>
    </div>
</div>

{{-- Filter bar --}}
<div class="rpt-filter">
    <form method="GET" action="{{ route('reports.elearning') }}">
        <div class="rpt-filter-row">
            <div class="rft-wrap">
                <span class="rft-icon"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></span>
                <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Search participant, email, company…" class="rft rft-search">
            </div>
            <select name="course_id" class="rft" style="min-width:160px;">
                <option value="">All Courses</option>
                @foreach($courses as $c)
                <option value="{{ $c->id }}" {{ ($filters['course_id'] ?? '') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
            <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="rft" title="From">
            <input type="date" name="date_to"   value="{{ $filters['date_to']   ?? '' }}" class="rft" title="To">
            <select name="payment_status" class="rft">
                <option value="">Payment</option>
                @foreach(['paid','pending','manual_approved','waived','free'] as $s)
                <option value="{{ $s }}" {{ ($filters['payment_status'] ?? '') === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                @endforeach
            </select>
            <select name="completion_status" class="rft">
                <option value="">Completion</option>
                @foreach(['not_started','in_progress','completed'] as $s)
                <option value="{{ $s }}" {{ ($filters['completion_status'] ?? '') === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                @endforeach
            </select>
            <select name="certificate_status" class="rft">
                <option value="">Certificate</option>
                <option value="issued"  {{ ($filters['certificate_status'] ?? '') === 'issued'  ? 'selected' : '' }}>Issued</option>
                <option value="pending" {{ ($filters['certificate_status'] ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
            </select>
            <button type="submit" class="btn btn-primary" style="height:36px;padding:0 16px;font-size:13px;">Filter</button>
            @if(collect($filters)->filter()->isNotEmpty())
            <a href="{{ route('reports.elearning') }}" style="font-size:12.5px;color:#9ca3af;font-weight:600;padding:4px 8px;border-radius:7px;text-decoration:none;white-space:nowrap;">✕ Clear</a>
            @endif
        </div>
    </form>
</div>

{{-- Export bar --}}
<div class="export-bar">
    <span>Export:</span>
    <a href="{{ route('reports.elearning.pdf', request()->query()) }}"   class="btn-export-pdf">📄 PDF</a>
    <a href="{{ route('reports.elearning.csv', request()->query()) }}"   class="btn-export-csv">⬇ CSV</a>
    <a href="{{ route('reports.elearning.excel', request()->query()) }}" class="btn-export-excel">📊 Excel</a>
    <span style="margin-left:auto;font-size:12.5px;color:#9ca3af;font-weight:600;">{{ $enrollments->total() }} record(s)</span>
</div>

{{-- Data table --}}
<div class="dt-wrap">
    <div class="dt-scroll">
        <table class="dt">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Participant</th>
                    <th>Course</th>
                    <th>Company</th>
                    <th>Country</th>
                    <th class="c">Payment</th>
                    <th class="c">Amount</th>
                    <th class="c">Progress</th>
                    <th class="c">Completion</th>
                    <th class="c">Certificate</th>
                    <th>Enrolled</th>
                </tr>
            </thead>
            <tbody>
                @forelse($enrollments as $e)
                @php
                    $payColor = match($e->payment_status) {
                        'paid','manual_approved','waived','free' => ['#dcfce7','#16a34a'],
                        'pending' => ['#fef9c3','#d97706'],
                        default   => ['#f3f4f6','#6b7280'],
                    };
                    $compColor = match($e->completion_status ?? '') {
                        'completed'   => ['#ede9fe','#7c3aed'],
                        'in_progress' => ['#fff7ed','#d97706'],
                        default       => ['#f3f4f6','#6b7280'],
                    };
                    $certColor = ($e->certificate_status === 'issued') ? ['#ede9fe','#7c3aed'] : ['#f3f4f6','#9ca3af'];
                @endphp
                <tr>
                    <td class="text-muted text-small">{{ $enrollments->firstItem() + $loop->index }}</td>
                    <td>
                        <div style="font-weight:700;font-size:13.5px;">{{ $e->participant_name ?? $e->email }}</div>
                        <div style="font-size:12px;color:#9ca3af;">{{ $e->email }}</div>
                    </td>
                    <td style="font-size:13px;font-weight:600;color:#1e3a8a;">{{ $e->course?->name ?? '—' }}</td>
                    <td style="font-size:13px;">{{ $e->company ?? '—' }}</td>
                    <td style="font-size:13px;">{{ $e->country ?? '—' }}</td>
                    <td class="c">
                        <span style="background:{{ $payColor[0] }};color:{{ $payColor[1] }};padding:2px 8px;border-radius:20px;font-size:11.5px;font-weight:700;white-space:nowrap;">
                            {{ ucfirst(str_replace('_',' ',$e->payment_status)) }}
                        </span>
                    </td>
                    <td class="c" style="font-size:13px;font-weight:700;">{{ number_format($e->amount,0) }}</td>
                    <td class="c">
                        @if($e->progress_percentage !== null)
                        <div style="background:#e5e9f0;border-radius:10px;height:6px;width:60px;overflow:hidden;margin:0 auto;">
                            <div style="background:#2563eb;height:100%;border-radius:10px;width:{{ $e->progress_percentage }}%;"></div>
                        </div>
                        <div style="font-size:11px;color:#6b7280;margin-top:2px;">{{ $e->progress_percentage }}%</div>
                        @else <span style="color:#d1d5db;">—</span> @endif
                    </td>
                    <td class="c">
                        <span style="background:{{ $compColor[0] }};color:{{ $compColor[1] }};padding:2px 8px;border-radius:20px;font-size:11.5px;font-weight:700;">
                            {{ ucfirst(str_replace('_',' ',$e->completion_status ?? 'not started')) }}
                        </span>
                    </td>
                    <td class="c">
                        <span style="background:{{ $certColor[0] }};color:{{ $certColor[1] }};padding:2px 8px;border-radius:20px;font-size:11.5px;font-weight:700;">
                            {{ ucfirst($e->certificate_status ?? 'pending') }}
                        </span>
                    </td>
                    <td style="font-size:12.5px;color:#6b7280;white-space:nowrap;">{{ $e->created_at?->format('d M Y') }}</td>
                </tr>
                @empty
                <tr><td colspan="11" style="text-align:center;padding:48px;color:#9ca3af;">
                    <div style="font-size:32px;margin-bottom:8px;">📊</div>
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
    type: 'line',
    data: {
        labels: monthlyData.map(d => d.month),
        datasets: [{
            label: 'Enrollments',
            data: monthlyData.map(d => d.count),
            borderColor: '#2563eb', backgroundColor: 'rgba(37,99,235,.1)',
            tension: .35, fill: true, pointRadius: 4, pointBackgroundColor: '#2563eb',
        },{
            label: 'Revenue (BDT)',
            data: monthlyData.map(d => d.revenue || 0),
            borderColor: '#16a34a', backgroundColor: 'rgba(22,163,74,.07)',
            tension: .35, fill: true, pointRadius: 4, pointBackgroundColor: '#16a34a',
            yAxisID: 'y2',
        }]
    },
    options: { responsive:true, plugins:{ legend:{ labels:{ font:{ size:11 } } } },
               scales:{ y:{ beginAtZero:true, ticks:{font:{size:11}} }, y2:{ position:'right', beginAtZero:true, ticks:{font:{size:11}} } } }
});

new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: ['Completed','In Progress','Not Started'],
        datasets: [{ data: [{{ $stats['completed'] }}, {{ $stats['in_progress'] }}, {{ max(0,$stats['total_enrollments']-$stats['completed']-$stats['in_progress']) }}],
            backgroundColor: ['#7c3aed','#d97706','#e5e9f0'], borderWidth: 0 }]
    },
    options: { responsive:true, plugins:{ legend:{ position:'bottom', labels:{ font:{ size:11 } } } } }
});
</script>
@endsection
