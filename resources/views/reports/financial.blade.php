@extends('layouts.app')
@section('title','Financial Reports')
@section('content')

<style>
.rpt-stats{display:grid;grid-template-columns:repeat(5,1fr);gap:12px;margin-bottom:20px;}
@media(max-width:1100px){.rpt-stats{grid-template-columns:repeat(3,1fr);}}
@media(max-width:640px){.rpt-stats{grid-template-columns:repeat(2,1fr);}}
.rpt-stat{background:#fff;border:1px solid #e5e9f0;border-radius:14px;padding:16px 18px 13px;position:relative;overflow:hidden;box-shadow:0 1px 4px rgba(15,23,42,.04);}
.rpt-stat-accent{position:absolute;top:0;left:0;right:0;height:3px;border-radius:14px 14px 0 0;}
.rpt-stat-icon{font-size:20px;margin-bottom:8px;}
.rpt-stat-num{font-size:22px;font-weight:900;line-height:1;margin-bottom:3px;}
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
.export-bar span{font-size:12.5px;color:#9ca3af;font-weight:700;}
.btn-export-pdf{background:#ef4444;color:#fff;border:none;padding:7px 14px;border-radius:8px;font-size:12.5px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:5px;}
.btn-export-csv{background:#16a34a;color:#fff;border:none;padding:7px 14px;border-radius:8px;font-size:12.5px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:5px;}
.btn-export-excel{background:#1d6c3a;color:#fff;border:none;padding:7px 14px;border-radius:8px;font-size:12.5px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:5px;}
.btn-export-pdf:hover,.btn-export-csv:hover,.btn-export-excel:hover{opacity:.88;}

/* method pills */
.method-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-bottom:20px;}
@media(max-width:700px){.method-grid{grid-template-columns:repeat(2,1fr);}}
.method-card{background:#fff;border:1px solid #e5e9f0;border-radius:12px;padding:14px 16px;box-shadow:0 1px 3px rgba(15,23,42,.03);text-align:center;}
.method-card-name{font-size:12px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;}
.method-card-count{font-size:20px;font-weight:900;color:#111827;margin-bottom:2px;}
.method-card-amt{font-size:12px;color:#16a34a;font-weight:700;}
</style>

<div class="page-header">
    <div>
        <div style="font-size:13px;color:#9ca3af;margin-bottom:4px;">
            <a href="{{ route('reports.index') }}" style="color:#6b7280;text-decoration:none;">Reports</a> /
        </div>
        <h1 class="page-title">Financial Reports</h1>
        <p class="page-subtitle">Invoice summaries, payment collections, and outstanding dues</p>
    </div>
</div>

{{-- Summary stat cards --}}
<div class="rpt-stats">
    <div class="rpt-stat">
        <div class="rpt-stat-accent" style="background:#1e3a8a;"></div>
        <div class="rpt-stat-icon">🧾</div>
        <div class="rpt-stat-num" style="color:#1e3a8a;">{{ $stats['invoice_count'] }}</div>
        <div class="rpt-stat-label">Invoices</div>
    </div>
    <div class="rpt-stat">
        <div class="rpt-stat-accent" style="background:#0891b2;"></div>
        <div class="rpt-stat-icon">💵</div>
        <div class="rpt-stat-num" style="color:#0891b2;">{{ number_format($stats['total_amount'],0) }}</div>
        <div class="rpt-stat-label">Total Invoiced</div>
    </div>
    <div class="rpt-stat">
        <div class="rpt-stat-accent" style="background:#16a34a;"></div>
        <div class="rpt-stat-icon">✅</div>
        <div class="rpt-stat-num" style="color:#16a34a;">{{ number_format($stats['paid_amount'],0) }}</div>
        <div class="rpt-stat-label">Total Paid</div>
    </div>
    <div class="rpt-stat">
        <div class="rpt-stat-accent" style="background:#dc2626;"></div>
        <div class="rpt-stat-icon">⚠️</div>
        <div class="rpt-stat-num" style="color:#dc2626;">{{ number_format($stats['due_amount'],0) }}</div>
        <div class="rpt-stat-label">Total Due</div>
    </div>
    <div class="rpt-stat">
        <div class="rpt-stat-accent" style="background:#d97706;"></div>
        <div class="rpt-stat-icon">⏳</div>
        <div class="rpt-stat-num" style="color:#d97706;">{{ $stats['pending_count'] }}</div>
        <div class="rpt-stat-label">Pending Payments</div>
    </div>
</div>

{{-- Payment method breakdown --}}
<div class="method-grid">
    @foreach($paymentMethods as $method)
    @php $mb = $methodBreakdown[$method] ?? ['count'=>0,'amount'=>0]; @endphp
    <div class="method-card">
        <div class="method-card-name">{{ $method }}</div>
        <div class="method-card-count">{{ $mb['count'] }}</div>
        <div class="method-card-amt">BDT {{ number_format($mb['amount'],0) }}</div>
    </div>
    @endforeach
</div>

{{-- Charts --}}
<div class="rpt-charts">
    <div class="chart-card">
        <div class="chart-card-title">📈 Monthly Income Trend</div>
        <canvas id="incomeChart" height="90"></canvas>
    </div>
    <div class="chart-card">
        <div class="chart-card-title">💳 Payment Method Distribution</div>
        <canvas id="methodChart" height="140"></canvas>
    </div>
</div>

{{-- Filter --}}
<div class="rpt-filter">
    <form method="GET" action="{{ route('reports.financial') }}">
        <div class="rpt-filter-row">
            <div class="rft-wrap">
                <span class="rft-icon"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></span>
                <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Search invoice no., client, company…" class="rft rft-search">
            </div>
            <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="rft" title="Invoice date from">
            <input type="date" name="date_to"   value="{{ $filters['date_to']   ?? '' }}" class="rft" title="Invoice date to">
            <select name="service_type" class="rft" style="min-width:140px;">
                <option value="">All Service Types</option>
                @foreach(['Training','Consultancy','eLearning','Corporate','Other'] as $s)
                <option value="{{ $s }}" {{ ($filters['service_type'] ?? '') === $s ? 'selected' : '' }}>{{ $s }}</option>
                @endforeach
            </select>
            <select name="payment_method" class="rft" style="min-width:140px;">
                <option value="">All Methods</option>
                @foreach($paymentMethods as $m)
                <option value="{{ $m }}" {{ ($filters['payment_method'] ?? '') === $m ? 'selected' : '' }}>{{ $m }}</option>
                @endforeach
            </select>
            <select name="payment_status" class="rft">
                <option value="">All Statuses</option>
                @foreach(['Paid','Partial','Pending','Overdue'] as $s)
                <option value="{{ $s }}" {{ ($filters['payment_status'] ?? '') === $s ? 'selected' : '' }}>{{ $s }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary" style="height:36px;padding:0 16px;font-size:13px;">Filter</button>
            @if(collect($filters)->filter()->isNotEmpty())
            <a href="{{ route('reports.financial') }}" style="font-size:12.5px;color:#9ca3af;font-weight:600;padding:4px 8px;border-radius:7px;text-decoration:none;white-space:nowrap;">✕ Clear</a>
            @endif
        </div>
    </form>
</div>

{{-- Export --}}
<div class="export-bar">
    <span>Export:</span>
    <a href="{{ route('reports.financial.pdf',   request()->query()) }}" class="btn-export-pdf">📄 PDF</a>
    <a href="{{ route('reports.financial.csv',   request()->query()) }}" class="btn-export-csv">⬇ CSV</a>
    <a href="{{ route('reports.financial.excel', request()->query()) }}" class="btn-export-excel">📊 Excel</a>
    <span style="margin-left:auto;font-size:12.5px;color:#9ca3af;font-weight:600;">{{ $invoices->total() }} record(s)</span>
</div>

{{-- Data table --}}
<div class="dt-wrap">
    <div class="dt-scroll">
        <table class="dt">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Invoice No.</th>
                    <th>Service Type</th>
                    <th>Client / Company</th>
                    <th>Country</th>
                    <th>Method</th>
                    <th class="r">Total (BDT)</th>
                    <th class="r">Paid (BDT)</th>
                    <th class="r">Due (BDT)</th>
                    <th class="c">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $inv)
                @php
                    $paidColor = match(strtolower($inv->payment_status ?? '')) {
                        'paid'     => ['#dcfce7','#16a34a'],
                        'partial'  => ['#fff7ed','#d97706'],
                        'pending'  => ['#fef9c3','#ca8a04'],
                        'overdue'  => ['#fee2e2','#dc2626'],
                        default    => ['#f3f4f6','#6b7280'],
                    };
                    $due = max(0, (float)$inv->total_amount - (float)$inv->amount_paid);
                @endphp
                <tr>
                    <td class="text-muted text-small">{{ $invoices->firstItem() + $loop->index }}</td>
                    <td style="font-size:12.5px;white-space:nowrap;color:#6b7280;">
                        {{ ($inv->invoice_date instanceof \Carbon\Carbon ? $inv->invoice_date : \Carbon\Carbon::parse($inv->invoice_date))->format('d M Y') }}
                    </td>
                    <td style="font-size:13px;font-weight:700;color:#1e3a8a;font-family:monospace;">{{ $inv->invoice_number }}</td>
                    <td style="font-size:12.5px;">{{ $inv->service_type ?? '—' }}</td>
                    <td>
                        <div style="font-weight:600;font-size:13px;">{{ $inv->client_name }}</div>
                        <div style="font-size:12px;color:#9ca3af;">{{ $inv->client_company }}</div>
                    </td>
                    <td style="font-size:13px;">{{ $inv->client_country ?? '—' }}</td>
                    <td style="font-size:13px;">{{ $inv->payment_method ?? '—' }}</td>
                    <td class="r" style="font-weight:700;font-size:13px;">{{ number_format($inv->total_amount,2) }}</td>
                    <td class="r" style="color:#16a34a;font-weight:700;font-size:13px;">{{ number_format($inv->amount_paid,2) }}</td>
                    <td class="r" style="color:{{ $due > 0 ? '#dc2626' : '#6b7280' }};font-weight:700;font-size:13px;">{{ number_format($due,2) }}</td>
                    <td class="c">
                        <span style="background:{{ $paidColor[0] }};color:{{ $paidColor[1] }};padding:2px 8px;border-radius:20px;font-size:11.5px;font-weight:700;white-space:nowrap;">
                            {{ $inv->payment_status ?? 'Pending' }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="11" style="text-align:center;padding:48px;color:#9ca3af;">
                    <div style="font-size:32px;margin-bottom:8px;">💰</div>
                    No invoices found for the selected filters.
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($invoices->hasPages())
    <div style="padding:16px 20px;border-top:1px solid #f0f2f5;">{{ $invoices->links() }}</div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const monthlyData = @json($monthly);
new Chart(document.getElementById('incomeChart'), {
    type: 'line',
    data: {
        labels: monthlyData.map(d => d.month),
        datasets: [{
            label: 'Invoiced',
            data: monthlyData.map(d => d.total || 0),
            borderColor: '#1e3a8a', backgroundColor: 'rgba(30,58,138,.08)',
            tension: .35, fill: true, pointRadius: 4, pointBackgroundColor: '#1e3a8a',
        },{
            label: 'Collected',
            data: monthlyData.map(d => d.paid || 0),
            borderColor: '#16a34a', backgroundColor: 'rgba(22,163,74,.07)',
            tension: .35, fill: true, pointRadius: 4, pointBackgroundColor: '#16a34a',
        }]
    },
    options: { responsive:true, plugins:{ legend:{ labels:{ font:{size:11} } } },
               scales:{ y:{ beginAtZero:true, ticks:{font:{size:11}} } } }
});

@php
$mbLabels = $methodBreakdown->keys()->toArray();
$mbAmts   = $methodBreakdown->map(fn($v) => $v['amount'])->values()->toArray();
@endphp
const mbColors = ['#1e3a8a','#16a34a','#d97706','#7c3aed','#0891b2','#dc2626','#6b7280'];
new Chart(document.getElementById('methodChart'), {
    type: 'doughnut',
    data: {
        labels: @json($mbLabels),
        datasets: [{ data: @json($mbAmts), backgroundColor: mbColors, borderWidth: 0 }]
    },
    options: { responsive:true, plugins:{ legend:{ position:'bottom', labels:{ font:{size:11} } } } }
});
</script>
@endsection
