<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>eLearning Report</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 10px; color: #111827; background: #fff; }

  /* Header */
  .pdf-header { background: #0f1e45; color: #fff; padding: 18px 24px 14px; display: flex; align-items: center; justify-content: space-between; }
  .pdf-logo-text { font-size: 16px; font-weight: 700; letter-spacing: .5px; }
  .pdf-logo-sub  { font-size: 8px; letter-spacing: 1px; color: #93c5fd; text-transform: uppercase; margin-top: 2px; }
  .pdf-report-title { font-size: 14px; font-weight: 700; text-align: right; }
  .pdf-report-sub   { font-size: 9px; color: #93c5fd; margin-top: 2px; text-align: right; }

  /* Summary cards */
  .stats-row { display: flex; gap: 8px; padding: 12px 20px; background: #f8fafc; border-bottom: 1px solid #e5e9f0; flex-wrap: wrap; }
  .stat-box { flex: 1; min-width: 90px; background: #fff; border: 1px solid #e5e9f0; border-radius: 6px; padding: 8px 10px; text-align: center; }
  .stat-box-num   { font-size: 16px; font-weight: 900; color: #1e3a8a; }
  .stat-box-label { font-size: 7.5px; color: #6b7280; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; margin-top: 2px; }

  /* Filter summary */
  .filter-summary { padding: 8px 20px; font-size: 8.5px; color: #6b7280; background: #fff7ed; border-bottom: 1px solid #fed7aa; }
  .filter-summary strong { color: #92400e; }

  /* Table */
  .pdf-table { width: 100%; border-collapse: collapse; margin: 0; }
  .pdf-table thead tr { background: #0f1e45; color: #fff; }
  .pdf-table th { padding: 7px 10px; font-size: 8px; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; text-align: left; }
  .pdf-table td { padding: 6px 10px; font-size: 8.5px; border-bottom: 1px solid #f0f2f5; }
  .pdf-table tr:nth-child(even) td { background: #f8fafc; }

  /* Footer */
  .pdf-footer { background: #0f1e45; color: #94a3b8; padding: 8px 24px; font-size: 7.5px; display: flex; justify-content: space-between; margin-top: auto; }
  .pdf-wrap { display: flex; flex-direction: column; min-height: 100%; }

  .badge { padding: 1px 6px; border-radius: 10px; font-size: 7.5px; font-weight: 700; display: inline-block; }
  .badge-green  { background: #dcfce7; color: #16a34a; }
  .badge-yellow { background: #fef9c3; color: #ca8a04; }
  .badge-purple { background: #ede9fe; color: #7c3aed; }
  .badge-gray   { background: #f3f4f6; color: #6b7280; }
</style>
</head>
<body>
<div class="pdf-wrap">

  <div class="pdf-header">
    <div>
      <div class="pdf-logo-text">SMS Training Services</div>
      <div class="pdf-logo-sub">Sustainable Management System Inc.</div>
    </div>
    <div>
      <div class="pdf-report-title">eLearning Report</div>
      <div class="pdf-report-sub">Generated: {{ now()->format('d M Y, H:i') }}</div>
    </div>
  </div>

  {{-- Stats --}}
  <div class="stats-row">
    <div class="stat-box"><div class="stat-box-num">{{ $stats['total'] }}</div><div class="stat-box-label">Total Enrollments</div></div>
    <div class="stat-box"><div class="stat-box-num">{{ $stats['completed'] }}</div><div class="stat-box-label">Completed</div></div>
    <div class="stat-box"><div class="stat-box-num">{{ $stats['certificates'] }}</div><div class="stat-box-label">Certificates</div></div>
    <div class="stat-box"><div class="stat-box-num">{{ number_format($stats['paid_amount'],0) }}</div><div class="stat-box-label">Paid (BDT)</div></div>
    <div class="stat-box"><div class="stat-box-num">{{ $enrollments->count() > 0 ? round($stats['completed']/$enrollments->count()*100) : 0 }}%</div><div class="stat-box-label">Completion Rate</div></div>
  </div>

  {{-- Filter summary --}}
  @if(collect($filters)->filter()->isNotEmpty())
  <div class="filter-summary">
    <strong>Active Filters:</strong>
    @if(!empty($filters['date_from'])) Date From: {{ $filters['date_from'] }} @endif
    @if(!empty($filters['date_to']))   | Date To: {{ $filters['date_to'] }} @endif
    @if(!empty($filters['company']))   | Company: {{ $filters['company'] }} @endif
    @if(!empty($filters['payment_status'])) | Payment: {{ $filters['payment_status'] }} @endif
    @if(!empty($filters['completion_status'])) | Completion: {{ $filters['completion_status'] }} @endif
  </div>
  @endif

  {{-- Table --}}
  <table class="pdf-table">
    <thead>
      <tr>
        <th>#</th><th>Participant</th><th>Email</th><th>Course</th>
        <th>Company</th><th>Country</th><th>Payment</th>
        <th>Amount</th><th>Completion</th><th>Certificate</th><th>Enrolled</th>
      </tr>
    </thead>
    <tbody>
      @forelse($enrollments as $i => $e)
      <tr>
        <td>{{ $i+1 }}</td>
        <td>{{ $e->participant_name ?? '—' }}</td>
        <td>{{ $e->email }}</td>
        <td>{{ $e->course?->name ?? '—' }}</td>
        <td>{{ $e->company ?? '—' }}</td>
        <td>{{ $e->country ?? '—' }}</td>
        <td><span class="badge {{ in_array($e->payment_status,['paid','manual_approved']) ? 'badge-green' : 'badge-yellow' }}">{{ ucfirst(str_replace('_',' ',$e->payment_status)) }}</span></td>
        <td>{{ number_format($e->amount,0) }}</td>
        <td><span class="badge {{ $e->completion_status === 'completed' ? 'badge-purple' : 'badge-gray' }}">{{ ucfirst(str_replace('_',' ',$e->completion_status ?? 'n/a')) }}</span></td>
        <td><span class="badge {{ $e->certificate_status === 'issued' ? 'badge-purple' : 'badge-gray' }}">{{ ucfirst($e->certificate_status ?? 'pending') }}</span></td>
        <td>{{ $e->created_at?->format('d M Y') }}</td>
      </tr>
      @empty
      <tr><td colspan="11" style="text-align:center;padding:16px;color:#9ca3af;">No records found.</td></tr>
      @endforelse
    </tbody>
  </table>

  <div style="flex:1;"></div>
  <div class="pdf-footer">
    <span>Sustainable Management System Inc. — Confidential, internal use only</span>
    <span>Total records: {{ $enrollments->count() }}</span>
  </div>

</div>
</body>
</html>
