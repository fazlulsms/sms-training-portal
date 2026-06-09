<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Financial Report</title>
<style>
  * { box-sizing:border-box; margin:0; padding:0; }
  body { font-family: DejaVu Sans, Arial, sans-serif; font-size:10px; color:#111827; background:#fff; }
  .pdf-header { background:#0f1e45; color:#fff; padding:18px 24px 14px; display:flex; align-items:center; justify-content:space-between; }
  .pdf-logo-text { font-size:16px; font-weight:700; }
  .pdf-logo-sub  { font-size:8px; color:#93c5fd; text-transform:uppercase; letter-spacing:1px; margin-top:2px; }
  .pdf-report-title { font-size:14px; font-weight:700; text-align:right; }
  .pdf-report-sub   { font-size:9px; color:#93c5fd; margin-top:2px; text-align:right; }
  .stats-row { display:flex; gap:8px; padding:10px 20px; background:#f8fafc; border-bottom:1px solid #e5e9f0; }
  .stat-box { flex:1; background:#fff; border:1px solid #e5e9f0; border-radius:6px; padding:7px 10px; text-align:center; }
  .stat-box-num   { font-size:15px; font-weight:900; color:#1e3a8a; }
  .stat-box-label { font-size:7px; color:#6b7280; font-weight:700; text-transform:uppercase; margin-top:2px; }
  .filter-summary { padding:7px 20px; font-size:8.5px; color:#6b7280; background:#fff7ed; border-bottom:1px solid #fed7aa; }
  .filter-summary strong { color:#92400e; }
  .pdf-table { width:100%; border-collapse:collapse; }
  .pdf-table thead tr { background:#0f1e45; color:#fff; }
  .pdf-table th { padding:7px 10px; font-size:8px; font-weight:700; text-transform:uppercase; letter-spacing:.4px; text-align:left; }
  .pdf-table td { padding:6px 10px; font-size:8.5px; border-bottom:1px solid #f0f2f5; }
  .pdf-table tr:nth-child(even) td { background:#f8fafc; }
  .r { text-align:right; }
  .pdf-footer { background:#0f1e45; color:#94a3b8; padding:8px 24px; font-size:7.5px; display:flex; justify-content:space-between; }
  .badge { padding:1px 6px; border-radius:10px; font-size:7.5px; font-weight:700; display:inline-block; }
  .b-green  { background:#dcfce7;color:#16a34a; }
  .b-yellow { background:#fef9c3;color:#ca8a04; }
  .b-red    { background:#fee2e2;color:#dc2626; }
  .b-gray   { background:#f3f4f6;color:#6b7280; }
  .totals-row { padding:10px 20px; background:#f0fdf4; border-bottom:1px solid #bbf7d0; font-size:11px; display:flex; gap:20px; }
  .totals-row strong { color:#15803d; }
</style>
</head>
<body>

  <div class="pdf-header">
    <div>
      <div class="pdf-logo-text">SMS Training Services</div>
      <div class="pdf-logo-sub">Sustainable Management System Inc.</div>
    </div>
    <div>
      <div class="pdf-report-title">Financial Report</div>
      <div class="pdf-report-sub">Generated: {{ now()->format('d M Y, H:i') }}</div>
    </div>
  </div>

  <div class="stats-row">
    <div class="stat-box"><div class="stat-box-num">{{ $stats['count'] }}</div><div class="stat-box-label">Invoices</div></div>
    <div class="stat-box"><div class="stat-box-num">{{ number_format($stats['total_amount'],0) }}</div><div class="stat-box-label">Total Invoiced</div></div>
    <div class="stat-box"><div class="stat-box-num">{{ number_format($stats['paid_amount'],0) }}</div><div class="stat-box-label">Total Paid</div></div>
    <div class="stat-box"><div class="stat-box-num">{{ number_format($stats['due_amount'],0) }}</div><div class="stat-box-label">Total Due</div></div>
  </div>

  @if(collect($filters)->filter()->isNotEmpty())
  <div class="filter-summary">
    <strong>Filters:</strong>
    @if(!empty($filters['date_from'])) From: {{ $filters['date_from'] }} @endif
    @if(!empty($filters['date_to']))   To: {{ $filters['date_to'] }} @endif
    @if(!empty($filters['payment_method'])) Method: {{ $filters['payment_method'] }} @endif
    @if(!empty($filters['payment_status'])) Status: {{ $filters['payment_status'] }} @endif
  </div>
  @endif

  <table class="pdf-table">
    <thead>
      <tr>
        <th>#</th><th>Date</th><th>Invoice No.</th><th>Service</th>
        <th>Client</th><th>Company</th><th>Country</th>
        <th>Method</th><th class="r">Total</th><th class="r">Paid</th><th class="r">Due</th><th>Status</th>
      </tr>
    </thead>
    <tbody>
      @forelse($invoices as $i => $inv)
      @php $due = max(0, (float)$inv->total_amount - (float)$inv->amount_paid); @endphp
      <tr>
        <td>{{ $i+1 }}</td>
        <td style="white-space:nowrap;">{{ ($inv->invoice_date instanceof \Carbon\Carbon ? $inv->invoice_date : \Carbon\Carbon::parse($inv->invoice_date))->format('d M Y') }}</td>
        <td style="font-family:monospace;font-size:8px;">{{ $inv->invoice_number }}</td>
        <td>{{ $inv->service_type ?? '—' }}</td>
        <td>{{ $inv->client_name }}</td>
        <td>{{ $inv->client_company ?? '—' }}</td>
        <td>{{ $inv->client_country ?? '—' }}</td>
        <td>{{ $inv->payment_method ?? '—' }}</td>
        <td class="r">{{ number_format($inv->total_amount,2) }}</td>
        <td class="r" style="color:#16a34a;font-weight:700;">{{ number_format($inv->amount_paid,2) }}</td>
        <td class="r" style="color:{{ $due > 0 ? '#dc2626' : '#6b7280' }};font-weight:700;">{{ number_format($due,2) }}</td>
        <td>
          <span class="badge {{ match(strtolower($inv->payment_status ?? '')) { 'paid'=>'b-green','pending'=>'b-yellow','overdue'=>'b-red',default=>'b-gray' } }}">
            {{ $inv->payment_status ?? 'Pending' }}
          </span>
        </td>
      </tr>
      @empty
      <tr><td colspan="12" style="text-align:center;padding:16px;color:#9ca3af;">No records.</td></tr>
      @endforelse
    </tbody>
  </table>

  <div class="pdf-footer">
    <span>Sustainable Management System Inc. — Confidential, internal use only</span>
    <span>{{ $invoices->count() }} invoices | Paid: BDT {{ number_format($stats['paid_amount'],2) }} | Due: BDT {{ number_format($stats['due_amount'],2) }}</span>
  </div>

</body>
</html>
