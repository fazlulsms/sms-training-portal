@extends('emails.layouts.master')
@section('subject-strip')
  @if(($status ?? '') === 'failed') ❌ Payment Failed @else 💳 Payment Received @endif
@endsection
@section('subject-theme', ($status ?? '') === 'failed' ? 'red' : 'green')

@section('content')
<p style="margin:0 0 14px;line-height:1.7;color:#374151;">
  A payment {{ ($status ?? '') === 'failed' ? 'failure' : 'receipt' }} has been recorded. Here are the details:
</p>

<div class="em-info-card" style="background:#f0f6ff;border-left:4px solid {{ ($status ?? '') === 'failed' ? '#b91c1c' : '#15803d' }};border-radius:8px;padding:18px 22px;margin:20px 0;">
  <table style="width:100%;border-collapse:collapse;">
    <tr class="em-row" style="border-bottom:1px solid #dbeafe;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Participant</td>
      <td class="em-val" style="padding:7px 0;color:#111827;font-size:13px;font-weight:700;vertical-align:top;">{{ $name }}</td>
    </tr>
    <tr class="em-row" style="border-bottom:1px solid #dbeafe;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Email</td>
      <td class="em-val" style="padding:7px 0;color:#111827;font-size:13px;font-weight:700;vertical-align:top;">{{ $enrollment->email ?? '—' }}</td>
    </tr>
    <tr class="em-row" style="border-bottom:1px solid #dbeafe;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Course</td>
      <td class="em-val" style="padding:7px 0;color:#111827;font-size:13px;font-weight:700;vertical-align:top;">{{ $courseName }}</td>
    </tr>
    <tr class="em-row" style="border-bottom:1px solid #dbeafe;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Amount</td>
      <td class="em-val" style="padding:7px 0;color:#111827;font-size:13px;font-weight:700;vertical-align:top;"><strong>BDT {{ number_format($amount, 2) }}</strong></td>
    </tr>
    <tr class="em-row" style="border-bottom:1px solid #dbeafe;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Status</td>
      <td class="em-val" style="padding:7px 0;font-size:13px;vertical-align:top;">
        @if(($status ?? '') === 'failed')
          <span class="em-badge-red" style="display:inline-block;background:#fee2e2;color:#b91c1c;padding:2px 10px;border-radius:12px;font-size:12px;font-weight:700;">{{ ucfirst($status) }}</span>
        @else
          <span class="em-badge-green" style="display:inline-block;background:#dcfce7;color:#15803d;padding:2px 10px;border-radius:12px;font-size:12px;font-weight:700;">{{ ucfirst($status ?? 'completed') }}</span>
        @endif
      </td>
    </tr>
    <tr class="em-row" style="border-bottom:none;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Timestamp</td>
      <td class="em-val" style="padding:7px 0;color:#111827;font-size:13px;font-weight:700;vertical-align:top;">{{ now()->format('d M Y, H:i') }}</td>
    </tr>
  </table>
</div>

<div class="em-btn-wrap" style="text-align:center;margin:24px 0;">
  <a href="{{ url('/admin/invoices') }}" class="em-btn" style="display:inline-block;background:#2563eb;color:#ffffff !important;text-decoration:none;padding:13px 32px;border-radius:8px;font-size:14px;font-weight:700;">View Invoices →</a>
</div>

<p style="margin:0 0 14px;line-height:1.7;font-size:13px;color:#6b7280;">
  This is an automated admin notification from SMS Training Academy.
</p>
@endsection
