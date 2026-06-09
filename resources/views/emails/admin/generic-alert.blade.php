<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>{{ $subject ?? 'Admin Alert' }}</title>
<style>
  body { font-family: Arial, sans-serif; font-size: 14px; color: #1f2937; background: #f3f4f6; margin: 0; padding: 0; }
  .wrapper { max-width: 600px; margin: 24px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
  .hdr { background: #374151; padding: 20px 28px; }
  .hdr h1 { color: #fff; font-size: 18px; margin: 0; }
  .body { padding: 26px 28px; }
  .card { background: #f8fafc; border-left: 4px solid #6b7280; border-radius: 6px; padding: 14px 18px; margin: 14px 0; font-size: 13px; }
  .btn { display:inline-block; background:#374151; color:#fff !important; text-decoration:none; padding:10px 22px; border-radius:6px; font-weight:700; font-size:13px; }
  .footer { background:#f1f5f9; padding:14px 28px; text-align:center; font-size:11px; color:#6b7280; }
</style>
</head>
<body>
<div class="wrapper">
  <div class="hdr"><h1>🔔 {{ $subject ?? 'Admin Alert' }}</h1></div>
  <div class="body">
    @if(!empty($message))
    <div class="card">{{ $message }}</div>
    @endif
    @if(!empty($details) && is_array($details))
    <div class="card">
      <table style="width:100%;border-collapse:collapse;font-size:13px;">
        @foreach($details as $key => $val)
        <tr>
          <td style="padding:4px 0;color:#6b7280;width:40%;font-weight:600;">{{ $key }}</td>
          <td style="padding:4px 0;font-weight:700;">{{ $val }}</td>
        </tr>
        @endforeach
      </table>
    </div>
    @endif
    @if(!empty($actionUrl) && !empty($actionLabel))
    <div style="text-align:center;margin:18px 0;">
      <a href="{{ $actionUrl }}" class="btn">{{ $actionLabel }} →</a>
    </div>
    @endif
  </div>
  <div class="footer">SMS Training Management System · {{ now()->format('d M Y') }}</div>
</div>
</body>
</html>
