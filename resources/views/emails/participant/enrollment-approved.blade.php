@php $headerTitle = '✅ Enrollment Approved'; $accentColor = '#15803d'; $headerColor = '#14532d'; @endphp
@include('emails.partials.participant-header')

<p>Dear <strong>{{ $enrollment->full_name ?? $enrollment->participant_name }}</strong>,</p>
<p>Great news! Your enrollment has been <strong>approved</strong>. You are now confirmed for the following programme:</p>

<div class="info-card">
  <table>
    <tr><td>Course / Programme</td><td>{{ $courseName }}</td></tr>
    @if(isset($enrollment->trainingSchedule))
    <tr><td>Batch / Schedule</td><td>{{ $enrollment->trainingSchedule?->batch_code ?? '—' }}</td></tr>
    <tr><td>Date</td><td>{{ $enrollment->trainingSchedule?->start_date?->format('d M Y') ?? '—' }}</td></tr>
    <tr><td>Venue</td><td>{{ $enrollment->trainingSchedule?->venue ?? '—' }}</td></tr>
    @endif
    <tr><td>Mode</td><td>{{ $enrollment->selected_mode ?? $enrollment->access_status ?? '—' }}</td></tr>
  </table>
</div>

<div class="alert-green">
  ✅ Your seat is <strong>confirmed</strong>. Please arrive on time and bring a valid photo ID.
</div>

@include('emails.partials.participant-footer')
