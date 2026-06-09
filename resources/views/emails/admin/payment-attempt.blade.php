<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Payment {{ ucfirst($status ?? 'Attempt') }}</title>
<style>
  body { font-family: Arial, sans-serif; font-size: 14px; color: #1f2937; background: #f3f4f6; margin: 0; padding: 0; }
  .wrapper { max-width: 600px; margin: 24px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
  .hdr { background: {{ ($status??'') === 'failed' ? '#7f1d1d' : '#14532d' }}; padding: 20px 28px; }
  .hdr h1 { color: #fff; font-size: 18px; margin: 0; }
  .hdr p  { color: #bbf7d0; font-size: 12px; margin: 3px 0 0; }
  .body { padding: 26px 28px; }
  .card { background: #f8fafc; border-left: 4px solid {{ ($status??'') === 'failed' ? '#dc2626' : '#15803d' }}; border-radius: 6px; padding: 14px 18px; margin: 14px 0; }
  .card table { width: 100%; border-collapse: collapse; font-size: 13px; }
  .card td { padding: 4px 0; }
  .card td:first-child { color: #6b7280; width: 40%; font-weight: 600; }
  .card td:last-child  { color: #111827; font-weight: 700; }
  .btn { display:inline-block; background:#1d4ed8; color:#fff !important; text-decoration:none; padding:10px 22px; border-radius:6px; font-weight:700; font-size:13px; }
  .footer { background:#f1f5f9; padding:14px 28px; text-align:center; font-size:11px; color:#6b7280; }
</style>
</head>
<body>
<div class="wrapper">
  <div class="hdr">
    <h1>{{ ($status??'') === 'failed' ? '❌ Payment Failed' : '💳 Payment Received' }}</h1>
    <p>SMS Training Management System</p>
  </div>
  <div class="body">
    <div class="card">
      <table>
        <tr><td>Participant</td><td>{{ $name }}</td></tr>
        <tr><td>Email</td><td>{{ $enrollment->email ?? '—' }}</td></tr>
        <tr><td>Course</td><td>{{ $courseName }}</td></tr>
        <tr><td>Amount</td><td><strong>BDT {{ number_format($amount, 2) }}</strong></td></tr>
        <tr><td>Status</td><td><strong style="color:{{ ($status??'') === 'failed' ? '#dc2626' : '#15803d' }};">{{ ucfirst($status ?? 'completed') }}</strong></td></tr>
        <tr><td>Timestamp</td><td>{{ now()->format('d M Y, H:i') }}</td></tr>
      </table>
    </div>
    <div style="text-align:center;margin:18px 0;">
      <a href="{{ url('/admin/invoices') }}" class="btn">View Invoices →</a>
    </div>
  </div>
  <div class="footer">SMS Training Management System · {{ now()->format('d M Y') }}</div>
</div>
</body>
</html>
