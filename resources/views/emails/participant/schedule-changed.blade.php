@php $headerTitle = '📅 Training Schedule Updated'; $accentColor = '#7c3aed'; $headerColor = '#4c1d95'; @endphp
@include('emails.partials.participant-header')

<p>Dear <strong>{{ $enrollment->full_name }}</strong>,</p>
<p>Please note that the schedule for <strong>{{ $courseName }}</strong> has been updated. Below are the latest details:</p>

<div class="info-card">
  <table>
    <tr><td>Course</td><td>{{ $courseName }}</td></tr>
    <tr><td>Batch Code</td><td>{{ $schedule?->batch_code ?? '—' }}</td></tr>
    <tr><td>New Start Date</td><td><strong>{{ $schedule?->start_date?->format('d M Y') ?? '—' }}</strong></td></tr>
    <tr><td>End Date</td><td>{{ $schedule?->end_date?->format('d M Y') ?? '—' }}</td></tr>
    <tr><td>Venue</td><td>{{ $schedule?->venue ?? '—' }}</td></tr>
    <tr><td>Mode</td><td>{{ $enrollment->selected_mode ?? '—' }}</td></tr>
  </table>
</div>

@if(!empty($changes))
<div class="alert-blue">
  <strong>What changed:</strong>
  <ul style="margin:6px 0 0;padding-left:18px;">
    @foreach($changes as $field => $change)
    <li>{{ $field }}: <s style="color:#6b7280;">{{ $change['from'] }}</s> → <strong>{{ $change['to'] }}</strong></li>
    @endforeach
  </ul>
</div>
@endif

<p>Please update your calendar accordingly. If you have any conflicts, contact us as soon as possible.</p>

@include('emails.partials.participant-footer')
