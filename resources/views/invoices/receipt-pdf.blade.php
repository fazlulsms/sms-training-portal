@php
    $logoPath = public_path('sms-logo.png');
    $logoSrc  = file_exists($logoPath)
        ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
        : null;
    $currency  = $log->currency ?? $invoice->currency ?? 'BDT';
    $amount    = $log->amount   ?? $invoice->amount_paid ?? $invoice->total_amount;
    $method    = $log->payment_method ?? $invoice->payment_method ?? '—';
    $txnId     = $log->transaction_id ?? '—';
    $paidDate  = $log->payment_date
        ? \Carbon\Carbon::parse($log->payment_date)->format('d M Y')
        : now()->format('d M Y');
    $receiptNo = 'RCT-' . str_pad($log->id ?? $invoice->id, 6, '0', STR_PAD_LEFT);
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Payment Receipt {{ $receiptNo }}</title>
<style>
@page { margin: 20px 24px; }
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; color: #1e2935; background: #fff; }

/* Header */
.header { display: table; width: 100%; border-bottom: 3px solid #0f1e45; padding-bottom: 12px; margin-bottom: 14px; }
.h-left  { display: table-cell; vertical-align: middle; width: 65%; }
.h-right { display: table-cell; vertical-align: middle; text-align: right; width: 35%; }
.logo-wrap { display: table-cell; width: 56px; vertical-align: middle; padding-right: 10px; }
.logo-wrap img { width: 50px; }
.brand { display: table-cell; vertical-align: middle; }
.brand-name { font-size: 14px; font-weight: 900; color: #0f1e45; }
.brand-sub  { font-size: 7.5px; color: #4b5563; text-transform: uppercase; letter-spacing: .8px; margin-top: 2px; }
.rcpt-title { font-size: 20px; font-weight: 900; color: #15803d; letter-spacing: 1px; }
.rcpt-no    { font-size: 9px; color: #6b7280; margin-top: 3px; }

/* Paid badge */
.paid-stamp {
    text-align: center;
    margin: 0 auto 14px;
    border: 3px solid #15803d;
    border-radius: 6px;
    padding: 6px 0;
    width: 140px;
    display: block;
}
.paid-stamp span {
    font-size: 22px;
    font-weight: 900;
    color: #15803d;
    letter-spacing: 5px;
}
.paid-stamp small {
    display: block;
    font-size: 8px;
    color: #6b7280;
    margin-top: 2px;
    letter-spacing: .5px;
}

/* Amount highlight */
.amount-box {
    background: #f0fdf4;
    border: 2px solid #86efac;
    border-radius: 8px;
    text-align: center;
    padding: 14px 20px;
    margin-bottom: 14px;
}
.amount-label { font-size: 9px; color: #6b7280; text-transform: uppercase; letter-spacing: .7px; }
.amount-value { font-size: 28px; font-weight: 900; color: #15803d; margin-top: 2px; }
.amount-words { font-size: 9px; color: #374151; margin-top: 4px; font-style: italic; }

/* Details table */
.detail-table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
.detail-table tr:nth-child(odd) td { background: #f8fafc; }
.detail-table td {
    padding: 7px 10px;
    font-size: 10px;
    border-bottom: 1px solid #e5e9f0;
}
.detail-table td:first-child {
    color: #6b7280;
    font-weight: 700;
    font-size: 9px;
    text-transform: uppercase;
    letter-spacing: .4px;
    width: 38%;
}
.detail-table td:last-child { color: #111827; font-weight: 600; }

/* Thank you note */
.thankyou {
    background: #eff6ff;
    border-left: 4px solid #1d4ed8;
    border-radius: 4px;
    padding: 10px 14px;
    font-size: 10px;
    color: #1e3a8a;
    margin-bottom: 14px;
    line-height: 1.6;
}

/* Footer */
.footer-line { border-top: 1px solid #e2e8f0; padding-top: 8px; margin-top: 4px; }
.footer-gen  { font-size: 7.5px; color: #9ca3af; font-style: italic; text-align: center; margin-bottom: 5px; }
.footer-addr { display: table; width: 100%; }
.footer-addr td { display: table-cell; vertical-align: top; font-size: 7.5px; color: #4b5563; line-height: 1.55; width: 33%; padding: 0 6px 0 0; }
.footer-addr td:last-child { border-left: 1px solid #e2e8f0; padding-left: 8px; }
.footer-lbl  { font-weight: 700; color: #0f1e45; font-size: 8px; display: block; margin-bottom: 2px; }
.footer-tag  { text-align: center; font-size: 7.5px; color: #9ca3af; font-style: italic; margin-top: 5px; letter-spacing: .3px; }
</style>
</head>
<body>

{{-- Header --}}
<div class="header">
  <div class="h-left">
    <table style="border-collapse:collapse;">
      <tr>
        <td class="logo-wrap">
          @if($logoSrc)<img src="{{ $logoSrc }}" alt="SMS">@endif
        </td>
        <td class="brand">
          <div class="brand-name">SMS Training Services</div>
          <div class="brand-sub">Sustainable Management System Inc.</div>
        </td>
      </tr>
    </table>
  </div>
  <div class="h-right">
    <div class="rcpt-title">MONEY RECEIPT</div>
    <div class="rcpt-no">{{ $receiptNo }} &nbsp;·&nbsp; {{ $paidDate }}</div>
  </div>
</div>

{{-- PAID Stamp --}}
<div style="text-align:center;margin-bottom:14px;">
  <div class="paid-stamp">
    <span>PAID</span>
    <small>{{ $paidDate }}</small>
  </div>
</div>

{{-- Amount --}}
<div class="amount-box">
  <div class="amount-label">Amount Received</div>
  <div class="amount-value">{{ $currency }} {{ number_format($amount, 2) }}</div>
  @if(!empty($invoice->amount_in_words))
  <div class="amount-words">{{ $invoice->amount_in_words }}</div>
  @endif
</div>

{{-- Payment Details --}}
<table class="detail-table">
  <tr><td>Receipt No.</td>     <td>{{ $receiptNo }}</td></tr>
  <tr><td>Payment Date</td>    <td>{{ $paidDate }}</td></tr>
  <tr><td>Invoice No.</td>     <td>{{ $invoice->invoice_number }}</td></tr>
  <tr><td>Received From</td>   <td>{{ $invoice->client_name }}</td></tr>
  @if($invoice->client_company)
  <tr><td>Organisation</td>    <td>{{ $invoice->client_company }}</td></tr>
  @endif
  <tr><td>Course / Service</td><td>{{ $invoice->training_name }}</td></tr>
  <tr><td>Payment Method</td>  <td>{{ $method }}</td></tr>
  @if($txnId !== '—')
  <tr><td>Transaction ID</td>  <td style="font-family:monospace;">{{ $txnId }}</td></tr>
  @endif
  @if(!empty($log->received_by))
  <tr><td>Received By</td>     <td>{{ $log->received_by }}</td></tr>
  @endif
  <tr><td>Invoice Total</td>   <td>{{ $currency }} {{ number_format($invoice->grand_total ?? $invoice->total_amount, 2) }}</td></tr>
  <tr><td>Amount Received</td> <td style="color:#15803d;font-weight:900;">{{ $currency }} {{ number_format($amount, 2) }}</td></tr>
  @php $balance = max(0, (float)($invoice->grand_total ?? $invoice->total_amount) - (float)$amount); @endphp
  @if($balance > 0)
  <tr><td>Balance Due</td>     <td style="color:#dc2626;font-weight:700;">{{ $currency }} {{ number_format($balance, 2) }}</td></tr>
  @else
  <tr><td>Balance Due</td>     <td style="color:#15803d;font-weight:700;">Nil — Fully Paid</td></tr>
  @endif
</table>

{{-- Thank You --}}
<div class="thankyou">
  Thank you for your payment. This is an official receipt confirming the amount received above against
  Invoice <strong>{{ $invoice->invoice_number }}</strong>.
  Please retain this receipt for your records.
</div>

{{-- Footer --}}
<div class="footer-line">
  <div class="footer-gen">* This is a computer generated receipt, therefore signature is not required.</div>
  <table class="footer-addr">
    <tr>
      <td><span class="footer-lbl">Global HQ</span>277 Cherry Street, Suite-12N,<br>New York, New York 10002,<br>United States of America</td>
      <td><span class="footer-lbl">Regional HQ</span>01, Sonargaon Janapath Avenue,<br>Sector#12, Uttara Model Town,<br>Dhaka-1230, Bangladesh</td>
      <td><span class="footer-lbl">Contact</span>E-mail: elearning@smscert.com<br>Website: www.smscert.com<br><span style="color:#0f1e45;font-weight:700;">Sustainable Management System Inc.</span></td>
    </tr>
  </table>
  <div class="footer-tag">Assessment &nbsp;|&nbsp; Certification &nbsp;|&nbsp; Verification &nbsp;|&nbsp; Capacity Building</div>
</div>

</body>
</html>
