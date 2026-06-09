@php $headerTitle = '⏰ Training Reminder'; $accentColor = '#0891b2'; $headerColor = '#164e63'; @endphp
@include('emails.partials.participant-header')

<p>Dear <strong>{{ $enrollment->full_name }}</strong>,</p>
<p>This is a friendly reminder that your training session begins in <strong>{{ $daysAhead }} day(s)</strong>. Please make sure you are prepared.</p>

<div class="info-card">
  <table>
    <tr><td>Course</td><td>{{ $courseName }}</td></tr>
    <tr><td>Batch</td><td>{{ $schedule?->batch_code ?? '—' }}</td></tr>
    <tr><td>Date</td><td><strong>{{ $schedule?->start_date?->format('d M Y') ?? '—' }}</strong></td></tr>
    <tr><td>Time</td><td>{{ $schedule?->start_time ?? '9:00 AM' }}</td></tr>
    <tr><td>Venue / Mode</td><td>{{ $schedule?->venue ?? $enrollment->selected_mode ?? '—' }}</td></tr>
  </table>
</div>

<div class="alert-blue">
  📋 <strong>What to bring:</strong> Valid photo ID, pen/notebook, and any materials provided during registration.
</div>

@include('emails.partials.participant-footer')
