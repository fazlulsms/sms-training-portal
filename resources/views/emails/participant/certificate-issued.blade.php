@php $headerTitle = '🏆 Certificate Issued'; $accentColor = '#b45309'; $headerColor = '#78350f'; @endphp
@include('emails.partials.participant-header')

<p>Dear <strong>{{ $enrollment->full_name ?? $enrollment->participant_name }}</strong>,</p>
<p>We are pleased to inform you that your <strong>certificate of completion</strong> has been officially issued for:</p>

<div style="background:#fffbeb;border:2px solid #fbbf24;border-radius:8px;text-align:center;padding:18px 20px;margin:18px 0;">
  <div style="font-size:11px;color:#6b7280;text-transform:uppercase;letter-spacing:.5px;">Certificate Issued For</div>
  <div style="font-size:18px;font-weight:900;color:#b45309;margin:4px 0;">{{ $courseName }}</div>
  @if($certNumber)
  <div style="font-size:13px;color:#6b7280;margin-top:4px;">Certificate No: <strong style="color:#111827;font-family:monospace;">{{ $certNumber }}</strong></div>
  @endif
</div>

<div class="info-card">
  <table>
    <tr><td>Issue Date</td><td>{{ $enrollment->certificate_issue_date ? \Carbon\Carbon::parse($enrollment->certificate_issue_date)->format('d M Y') : now()->format('d M Y') }}</td></tr>
    @if($certNumber)
    <tr><td>Certificate No.</td><td><code style="font-family:monospace;">{{ $certNumber }}</code></td></tr>
    @endif
    <tr><td>Verify Online</td><td><a href="{{ url('/verify') }}" style="color:#b45309;">{{ url('/verify') }}</a></td></tr>
  </table>
</div>

<div style="text-align:center;margin:20px 0;">
  <a href="{{ $loginUrl }}" class="btn" style="background:#b45309;">📥 Download Certificate</a>
</div>

<div class="alert-green">
  ✅ Your certificate can be verified online at any time using your certificate number.
</div>

@include('emails.partials.participant-footer')
