@php $headerTitle = 'Enrollment Update'; $accentColor = '#dc2626'; $headerColor = '#7f1d1d'; @endphp
@include('emails.partials.participant-header')

<p>Dear <strong>{{ $enrollment->full_name ?? $enrollment->participant_name }}</strong>,</p>
<p>We regret to inform you that your enrollment for <strong>{{ $courseName }}</strong> could not be processed at this time.</p>

@if($reason)
<div class="alert-red">
  <strong>Reason:</strong> {{ $reason }}
</div>
@endif

<p>If you believe this is an error or would like to discuss alternatives, please contact our team. We would be happy to assist you find a suitable programme.</p>
<div style="text-align:center;margin:20px 0;">
  <a href="mailto:elearning@smscert.com" class="btn" style="background:#dc2626;">Contact Us</a>
</div>

@include('emails.partials.participant-footer')
