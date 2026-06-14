@extends('emails.layouts.master')
@section('subject-strip') 🔔 {{ $subject ?? 'Admin Alert' }} @endsection
@section('subject-theme', 'blue')

@section('content')
<p style="margin:0 0 14px;line-height:1.7;color:#374151;">
  This is an automated admin notification from SMS Training Academy.
</p>

@if(!empty($message))
<div class="em-alert em-alert-blue" style="background:#eff6ff;border:1px solid #bfdbfe;color:#1d4ed8;border-radius:8px;padding:14px 18px;margin:16px 0;font-size:13px;line-height:1.6;">
  {{ $message }}
</div>
@endif

@if(!empty($details) && is_array($details))
<div class="em-info-card" style="background:#f0f6ff;border-left:4px solid #2563eb;border-radius:8px;padding:18px 22px;margin:20px 0;">
  <table style="width:100%;border-collapse:collapse;">
    @foreach($details as $key => $val)
    <tr class="em-row" style="border-bottom:{{ !$loop->last ? '1px solid #dbeafe' : 'none' }};">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">{{ $key }}</td>
      <td class="em-val" style="padding:7px 0;color:#111827;font-size:13px;font-weight:700;vertical-align:top;">{{ $val }}</td>
    </tr>
    @endforeach
  </table>
</div>
@endif

@if(!empty($actionUrl) && !empty($actionLabel))
<div class="em-btn-wrap" style="text-align:center;margin:24px 0;">
  <a href="{{ $actionUrl }}" class="em-btn" style="display:inline-block;background:#2563eb;color:#ffffff !important;text-decoration:none;padding:13px 32px;border-radius:8px;font-size:14px;font-weight:700;">{{ $actionLabel }} →</a>
</div>
@endif
@endsection
