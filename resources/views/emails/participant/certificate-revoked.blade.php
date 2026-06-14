@extends('emails.layouts.master')
@section('subject-strip') Certificate Revoked @endsection
@section('subject-theme', 'red')

@section('content')
<p class="em-greeting" style="font-size:16px;font-weight:600;color:#111827;margin:0 0 16px;">Dear <strong>{{ $enrollment->full_name ?? $enrollment->participant_name }}</strong>,</p>

<p style="margin:0 0 14px;line-height:1.7;color:#374151;">
  We regret to inform you that the certificate previously issued for <strong>{{ $courseName }}</strong> has been <strong>revoked</strong>.
</p>

@if($reason)
<div class="em-alert em-alert-red" style="background:#fef2f2;border:1px solid #fca5a5;color:#b91c1c;border-radius:8px;padding:14px 18px;margin:16px 0;font-size:13px;line-height:1.6;">
  <strong>Reason for revocation:</strong> {{ $reason }}
</div>
@endif

<p style="margin:0 0 14px;line-height:1.7;color:#374151;">
  If you believe this action was taken in error or would like to appeal, please contact our team immediately.
</p>

<div class="em-btn-wrap" style="text-align:center;margin:24px 0;">
  <a href="mailto:training@smscert.com" class="em-btn em-btn-red" style="display:inline-block;background:#b91c1c;color:#ffffff !important;text-decoration:none;padding:13px 32px;border-radius:8px;font-size:14px;font-weight:700;">Contact Support</a>
</div>
@endsection
