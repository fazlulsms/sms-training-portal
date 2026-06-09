<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>ILT Report</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 10px; color: #111827; background: #fff; }
  .pdf-header { background: #0f1e45; color: #fff; padding: 18px 24px 14px; display: flex; align-items: center; justify-content: space-between; }
  .pdf-logo-text { font-size: 16px; font-weight: 700; }
  .pdf-logo-sub  { font-size: 8px; color: #93c5fd; text-transform: uppercase; letter-spacing: 1px; margin-top: 2px; }
  .pdf-report-title { font-size: 14px; font-weight: 700; text-align: right; }
  .pdf-report-sub   { font-size: 9px; color: #93c5fd; margin-top: 2px; text-align: right; }
  .stats-row { display: flex; gap: 8px; padding: 10px 20px; background: #f8fafc; border-bottom: 1px solid #e5e9f0; flex-wrap: wrap; }
  .stat-box { flex: 1; min-width: 80px; background: #fff; border: 1px solid #e5e9f0; border-radius: 6px; padding: 7px 8px; text-align: center; }
  .stat-box-num   { font-size: 15px; font-weight: 900; color: #1e3a8a; }
  .stat-box-label { font-size: 7px; color: #6b7280; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; margin-top: 2px; }
  .filter-summary { padding: 7px 20px; font-size: 8.5px; color: #6b7280; background: #fff7ed; border-bottom: 1px solid #fed7aa; }
  .filter-summary strong { color: #92400e; }
  .pdf-table { width: 100%; border-collapse: collapse; }
  .pdf-table thead tr { background: #0f1e45; color: #fff; }
  .pdf-table th { padding: 7px 8px; font-size: 7.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; text-align: left; }
  .pdf-table td { padding: 5px 8px; font-size: 8px; border-bottom: 1px solid #f0f2f5; }
  .pdf-table tr:nth-child(even) td { background: #f8fafc; }
  .pdf-footer { background: #0f1e45; color: #94a3b8; padding: 8px 24px; font-size: 7.5px; display: flex; justify-content: space-between; }
  .badge { padding: 1px 5px; border-radius: 8px; font-size: 7px; font-weight: 700; display: inline-block; }
  .b-green  { background:#dcfce7;color:#16a34a; }
  .b-red    { background:#fee2e2;color:#dc2626; }
  .b-yellow { background:#fff7ed;color:#d97706; }
  .b-gray   { background:#f3f4f6;color:#6b7280; }
  .b-purple { background:#ede9fe;color:#7c3aed; }
</style>
</head>
<body>

  <div class="pdf-header">
    <div>
      <div class="pdf-logo-text">SMS Training Services</div>
      <div class="pdf-logo-sub">Sustainable Management System Inc.</div>
    </div>
    <div>
      <div class="pdf-report-title">Instructor-Led Training Report</div>
      <div class="pdf-report-sub">Generated: {{ now()->format('d M Y, H:i') }}</div>
    </div>
  </div>

  <div class="stats-row">
    <div class="stat-box"><div class="stat-box-num">{{ $stats['total'] }}</div><div class="stat-box-label">Participants</div></div>
    <div class="stat-box"><div class="stat-box-num">{{ $stats['present'] }}</div><div class="stat-box-label">Present</div></div>
    <div class="stat-box"><div class="stat-box-num">{{ $stats['absent'] }}</div><div class="stat-box-label">Absent</div></div>
    <div class="stat-box"><div class="stat-box-num">{{ $stats['certificates'] }}</div><div class="stat-box-label">Certificates</div></div>
    <div class="stat-box"><div class="stat-box-num">{{ $stats['total'] > 0 ? round($stats['present']/$stats['total']*100) : 0 }}%</div><div class="stat-box-label">Attendance Rate</div></div>
  </div>

  @if(collect($filters)->filter()->isNotEmpty())
  <div class="filter-summary">
    <strong>Filters:</strong>
    @if(!empty($filters['date_from'])) From: {{ $filters['date_from'] }} @endif
    @if(!empty($filters['date_to']))   To: {{ $filters['date_to'] }} @endif
    @if(!empty($filters['country']))   Country: {{ $filters['country'] }} @endif
    @if(!empty($filters['attendance_status'])) Attendance: {{ $filters['attendance_status'] }} @endif
  </div>
  @endif

  <table class="pdf-table">
    <thead>
      <tr>
        <th>#</th><th>Date</th><th>Course</th><th>Batch</th>
        <th>Trainer</th><th>Venue</th><th>Participant</th>
        <th>Company</th><th>Country</th><th>Attendance</th><th>Certificate</th>
      </tr>
    </thead>
    <tbody>
      @forelse($enrollments as $i => $e)
      <tr>
        <td>{{ $i+1 }}</td>
        <td style="white-space:nowrap;">{{ $e->trainingSchedule?->start_date?->format('d M Y') ?? '—' }}</td>
        <td>{{ $e->trainingSchedule?->course?->name ?? '—' }}</td>
        <td>{{ $e->trainingSchedule?->batch_code ?? '—' }}</td>
        <td>{{ $e->trainingSchedule?->trainer?->name ?? '—' }}</td>
        <td>{{ $e->trainingSchedule?->venue ?? '—' }}</td>
        <td>{{ $e->full_name }}</td>
        <td>{{ $e->company ?? '—' }}</td>
        <td>{{ $e->country ?? '—' }}</td>
        <td>
          <span class="badge {{ match($e->attendance_status) { 'Present'=>'b-green','Absent'=>'b-red','Partial'=>'b-yellow',default=>'b-gray' } }}">
            {{ $e->attendance_status ?? 'Pending' }}
          </span>
        </td>
        <td><span class="badge {{ $e->certificate_generated ? 'b-purple' : 'b-gray' }}">{{ $e->certificate_generated ? 'Issued' : 'Pending' }}</span></td>
      </tr>
      @empty
      <tr><td colspan="11" style="text-align:center;padding:16px;color:#9ca3af;">No records.</td></tr>
      @endforelse
    </tbody>
  </table>

  <div class="pdf-footer">
    <span>Sustainable Management System Inc. — Confidential, internal use only</span>
    <span>Total: {{ $enrollments->count() }} records</span>
  </div>

</body>
</html>
