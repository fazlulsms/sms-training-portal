@php $headerTitle = '🎉 Course Completed!'; $accentColor = '#15803d'; $headerColor = '#14532d'; @endphp
@include('emails.partials.participant-header')

<p>Dear <strong>{{ $enrollment->full_name ?? $enrollment->participant_name }}</strong>,</p>
<p>Congratulations! You have successfully <strong>completed</strong> the following programme:</p>

<div style="background:#f0fdf4;border:2px solid #86efac;border-radius:8px;text-align:center;padding:18px 20px;margin:18px 0;">
  <div style="font-size:11px;color:#6b7280;text-transform:uppercase;letter-spacing:.5px;">Programme Completed</div>
  <div style="font-size:20px;font-weight:900;color:#15803d;margin-top:4px;">{{ $courseName }}</div>
</div>

<div class="info-card">
  <table>
    <tr><td>Completion Date</td><td>{{ now()->format('d M Y') }}</td></tr>
    <tr><td>Completion Status</td><td><span style="color:#15803d;font-weight:700;">Completed ✓</span></td></tr>
    @if(isset($enrollment->certificate_number) && $enrollment->certificate_number)
    <tr><td>Certificate No.</td><td>{{ $enrollment->certificate_number }}</td></tr>
    @endif
  </table>
</div>

<div class="alert-green">
  🏆 Your certificate will be processed and issued shortly. You will receive another email when it is ready.
</div>

@include('emails.partials.participant-footer')
