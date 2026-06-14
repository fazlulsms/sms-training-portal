@extends('emails.layouts.master')
@section('subject-strip') 📄 Invoice / Proforma Invoice @endsection
@section('subject-theme', 'blue')

@section('content')
<p class="em-greeting" style="font-size:16px;font-weight:600;color:#111827;margin:0 0 16px;">Dear Sir/Madam,</p>

<p style="margin:0 0 14px;line-height:1.7;color:#374151;">
  Greetings from <strong>SMS Training Academy</strong>.
</p>

<p style="margin:0 0 14px;line-height:1.7;color:#374151;">
  Please find the attached proforma invoice <strong>{{ $invoice->invoice_number }}</strong> for your kind review and necessary action.
</p>

<div class="em-info-card" style="background:#f0f6ff;border-left:4px solid #2563eb;border-radius:8px;padding:18px 22px;margin:20px 0;">
  <table style="width:100%;border-collapse:collapse;">
    <tr class="em-row" style="border-bottom:1px solid #dbeafe;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Invoice Number</td>
      <td class="em-val" style="padding:7px 0;color:#111827;font-size:13px;font-weight:700;vertical-align:top;">{{ $invoice->invoice_number }}</td>
    </tr>
    <tr class="em-row" style="border-bottom:none;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Amount</td>
      <td class="em-val" style="padding:7px 0;color:#1e3a8a;font-size:15px;font-weight:700;vertical-align:top;">{{ $invoice->currency }} {{ number_format($invoice->grand_total, 2) }}</td>
    </tr>
  </table>
</div>

<p style="margin:0 0 14px;line-height:1.7;color:#374151;">
  Please complete payment at your earliest convenience. Our team will be in touch with bank details and further instructions.
</p>

<p style="margin:0 0 14px;line-height:1.7;color:#374151;">
  For any queries, please do not hesitate to contact us at <a href="mailto:training@smscert.com" style="color:#2563eb;">training@smscert.com</a>.
</p>
@endsection
