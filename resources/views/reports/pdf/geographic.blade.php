<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Geographic Coverage Report</title>
<style>
  * { box-sizing:border-box; margin:0; padding:0; }
  body { font-family: DejaVu Sans, Arial, sans-serif; font-size:10px; color:#111827; }
  .pdf-header { background:#0f1e45; color:#fff; padding:18px 24px 14px; display:flex; align-items:center; justify-content:space-between; }
  .pdf-logo-text { font-size:16px; font-weight:700; }
  .pdf-logo-sub  { font-size:8px; color:#93c5fd; text-transform:uppercase; letter-spacing:1px; margin-top:2px; }
  .pdf-report-title { font-size:14px; font-weight:700; text-align:right; }
  .pdf-report-sub   { font-size:9px; color:#93c5fd; margin-top:2px; text-align:right; }
  .section-title { font-size:11px; font-weight:700; color:#1e3a8a; background:#eff6ff; padding:8px 20px; margin:0; border-bottom:2px solid #bfdbfe; }
  .two-col { display:table; width:100%; border-collapse:collapse; }
  .col { display:table-cell; width:50%; vertical-align:top; padding:10px 20px; border-right:1px solid #f0f2f5; }
  .col-title { font-size:10px; font-weight:700; color:#374151; margin-bottom:8px; text-transform:uppercase; letter-spacing:.4px; }
  .geo-row { display:flex; justify-content:space-between; padding:5px 0; border-bottom:1px solid #f4f5f8; font-size:9px; }
  .geo-row:last-child { border-bottom:none; }
  .geo-name  { color:#111827; font-weight:600; }
  .geo-count { color:#6b7280; font-weight:700; }
  .pdf-footer { background:#0f1e45; color:#94a3b8; padding:8px 24px; font-size:7.5px; display:flex; justify-content:space-between; margin-top:20px; }
</style>
</head>
<body>

  <div class="pdf-header">
    <div>
      <div class="pdf-logo-text">SMS Training Services</div>
      <div class="pdf-logo-sub">Sustainable Management System Inc.</div>
    </div>
    <div>
      <div class="pdf-report-title">Geographic Coverage Report</div>
      <div class="pdf-report-sub">Generated: {{ now()->format('d M Y, H:i') }}</div>
    </div>
  </div>

  <div class="section-title">ILT & eLearning Participants by Country</div>
  <div class="two-col">
    <div class="col">
      <div class="col-title">ILT Participants</div>
      @forelse($iltByCountry as $row)
      <div class="geo-row"><span class="geo-name">{{ $row->country }}</span><span class="geo-count">{{ number_format($row->count) }}</span></div>
      @empty <div style="color:#9ca3af;font-size:9px;padding:8px 0;">No data.</div> @endforelse
    </div>
    <div class="col">
      <div class="col-title">eLearning Participants</div>
      @forelse($elByCountry as $row)
      <div class="geo-row"><span class="geo-name">{{ $row->country }}</span><span class="geo-count">{{ number_format($row->count) }}</span></div>
      @empty <div style="color:#9ca3af;font-size:9px;padding:8px 0;">No data.</div> @endforelse
    </div>
  </div>

  <div class="section-title" style="margin-top:16px;">Sessions by Venue / City</div>
  <div style="padding:10px 20px;">
    @forelse($sessionsByVenue as $row)
    <div class="geo-row"><span class="geo-name">{{ $row->venue }}</span><span class="geo-count">{{ $row->count }} session(s)</span></div>
    @empty <div style="color:#9ca3af;font-size:9px;padding:8px 0;">No session venues recorded.</div> @endforelse
  </div>

  <div class="pdf-footer">
    <span>Sustainable Management System Inc. — Confidential, internal use only</span>
    <span>Generated: {{ now()->format('d M Y H:i') }}</span>
  </div>

</body>
</html>
