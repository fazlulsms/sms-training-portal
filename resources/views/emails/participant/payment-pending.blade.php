@extends('emails.layouts.master')
@section('subject-strip') ⏳ Payment Pending @endsection
@section('subject-theme', 'amber')

@section('content')
<p class="em-greeting" style="font-size:16px;font-weight:600;color:#111827;margin:0 0 16px;">Dear <strong>{{ $enrollment->full_name ?? $enrollment->participant_name }}</strong>,</p>

<p style="margin:0 0 14px;line-height:1.7;color:#374151;">
  Thank you for registering. Your enrollment is <strong>pending payment</strong>. Please complete the payment to confirm your seat.
</p>

<div class="em-info-card" style="background:#f0f6ff;border-left:4px solid #2563eb;border-radius:8px;padding:18px 22px;margin:20px 0;">
  <table style="width:100%;border-collapse:collapse;">
    <tr class="em-row" style="border-bottom:1px solid #dbeafe;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Course / Programme</td>
      <td class="em-val" style="padding:7px 0;color:#111827;font-size:13px;font-weight:700;vertical-align:top;">{{ $courseName }}</td>
    </tr>
    <tr class="em-row" style="border-bottom:1px solid #dbeafe;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Amount Due</td>
      <td class="em-val" style="padding:7px 0;font-size:14px;font-weight:700;vertical-align:top;color:#b45309;">{{ $currency ?? 'BDT' }} {{ number_format($amount, 2) }}</td>
    </tr>
    @if($invoice?->invoice_number)
    <tr class="em-row" style="border-bottom:1px solid #dbeafe;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Invoice No.</td>
      <td class="em-val" style="padding:7px 0;color:#111827;font-size:13px;font-weight:700;vertical-align:top;">{{ $invoice->invoice_number }}</td>
    </tr>
    @endif
    <tr class="em-row" style="border-bottom:none;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Payment Methods</td>
      <td class="em-val" style="padding:7px 0;color:#111827;font-size:13px;font-weight:700;vertical-align:top;">Bank Transfer · bKash · Nagad · Card</td>
    </tr>
  </table>
</div>

<div class="em-alert em-alert-yellow" style="background:#fffbeb;border:1px solid #fbbf24;color:#92400e;border-radius:8px;padding:14px 18px;margin:16px 0;font-size:13px;line-height:1.6;">
  ⚠️ <strong>Your seat is reserved</strong> but will only be confirmed once payment is received. Please complete payment as soon as possible.
</div>

<p style="margin:0 0 14px;line-height:1.7;color:#374151;">
  For payment details and bank information, please reply to this email or contact us at <a href="mailto:training@smscert.com" style="color:#2563eb;">training@smscert.com</a>.
</p>
@endsection
