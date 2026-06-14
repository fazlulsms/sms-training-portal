@extends('emails.layouts.master')
@section('subject-strip') Enrollment Update @endsection
@section('subject-theme', 'red')

@section('content')
<p class="em-greeting" style="font-size:16px;font-weight:600;color:#111827;margin:0 0 16px;">Dear <strong>{{ $enrollment->full_name ?? $enrollment->participant_name }}</strong>,</p>

<p style="margin:0 0 14px;line-height:1.7;color:#374151;">
  We regret to inform you that your enrollment for <strong>{{ $courseName }}</strong> could not be processed at this time.
</p>

@if($reason)
<div class="em-alert em-alert-red" style="background:#fef2f2;border:1px solid #fca5a5;color:#b91c1c;border-radius:8px;padding:14px 18px;margin:16px 0;font-size:13px;line-height:1.6;">
  <strong>Reason:</strong> {{ $reason }}
</div>
@endif

<p style="margin:0 0 14px;line-height:1.7;color:#374151;">
  If you believe this is an error or would like to discuss alternatives, please contact our team. We would be happy to assist you find a suitable programme.
</p>

<div class="em-btn-wrap" style="text-align:center;margin:24px 0;">
  <a href="mailto:training@smscert.com" class="em-btn em-btn-red" style="display:inline-block;background:#b91c1c;color:#ffffff !important;text-decoration:none;padding:13px 32px;border-radius:8px;font-size:14px;font-weight:700;">Contact Us</a>
</div>
@endsection
