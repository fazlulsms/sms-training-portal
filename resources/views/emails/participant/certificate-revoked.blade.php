@php $headerTitle = 'Certificate Revoked'; $accentColor = '#dc2626'; $headerColor = '#7f1d1d'; @endphp
@include('emails.partials.participant-header')

<p>Dear <strong>{{ $enrollment->full_name ?? $enrollment->participant_name }}</strong>,</p>
<p>We regret to inform you that the certificate previously issued for <strong>{{ $courseName }}</strong> has been <strong>revoked</strong>.</p>

@if($reason)
<div class="alert-red">
  <strong>Reason for revocation:</strong> {{ $reason }}
</div>
@endif

<p>If you believe this action was taken in error or would like to appeal, please contact our team immediately.</p>

<div style="text-align:center;margin:20px 0;">
  <a href="mailto:elearning@smscert.com" class="btn" style="background:#dc2626;">Contact Support</a>
</div>

@include('emails.partials.participant-footer')
