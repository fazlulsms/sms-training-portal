@php $headerTitle = '✅ Attendance Confirmed'; $accentColor = '#15803d'; $headerColor = '#14532d'; @endphp
@include('emails.partials.participant-header')

<p>Dear <strong>{{ $enrollment->full_name }}</strong>,</p>
<p>Your attendance for <strong>{{ $courseName }}</strong> has been recorded as <strong>Attended</strong>.</p>

<div class="info-card">
  <table>
    <tr><td>Course</td><td>{{ $courseName }}</td></tr>
    <tr><td>Attendance Status</td><td><span style="color:#15803d;font-weight:700;">{{ $enrollment->attendance_status ?? 'Attended' }}</span></td></tr>
    <tr><td>Date Recorded</td><td>{{ now()->format('d M Y') }}</td></tr>
  </table>
</div>

<div class="alert-green">
  🏆 Upon successful completion of all requirements, your certificate will be issued and you will be notified.
</div>

@include('emails.partials.participant-footer')
