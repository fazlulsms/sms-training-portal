@php
    $ps       = strtolower($invoice->payment_status ?? 'unpaid');
    $showPaid = isset($paid) ? (bool)$paid : ($ps === 'paid');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Invoice {{ $invoice->invoice_number }}</title>
<style>
  @page { margin: 20px 24px; }
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; color: #111827; background: #fff; }

  /* ── Header ─────────────────────────────────────────── */
  .header { background: #0f1e45; color: #fff; padding: 20px 24px; display: table; width: 100%; }
  .h-left  { display: table-cell; vertical-align: middle; width: 60%; }
  .h-right { display: table-cell; vertical-align: middle; text-align: right; width: 40%; }
  .company-name { font-size: 17px; font-weight: 900; letter-spacing: .5px; }
  .company-sub  { font-size: 8px; color: #93c5fd; text-transform: uppercase; letter-spacing: 1px; margin-top: 2px; }
  .inv-title { font-size: 22px; font-weight: 900; color: #fff; letter-spacing: 1px; }
  .inv-number{ font-size: 10px; color: #93c5fd; margin-top: 4px; }

  /* ── Meta row ─────────────────────────────────────────── */
  .meta-row { display: table; width: 100%; border-bottom: 2px solid #e5e9f0; }
  .meta-cell { display: table-cell; padding: 14px 24px; width: 50%; vertical-align: top; }
  .meta-cell:last-child { border-left: 1px solid #e5e9f0; }
  .meta-label { font-size: 8px; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 4px; }
  .meta-value { font-size: 12px; font-weight: 700; color: #111827; }
  .meta-value-sub { font-size: 10px; color: #4b5563; margin-top: 2px; }

  /* ── Bill to / Training info row ─────────────────────── */
  .two-col { display: table; width: 100%; }
  .col      { display: table-cell; width: 50%; padding: 14px 24px; vertical-align: top; }
  .col:last-child { border-left: 1px solid #e5e9f0; }
  .col-header { font-size: 8px; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 8px; border-bottom: 1px solid #e5e9f0; padding-bottom: 4px; }
  .col p { font-size: 11px; color: #374151; margin-bottom: 3px; }
  .col strong { color: #111827; }

  /* ── Items table ────────────────────────────────────── */
  .section-title { background: #0f1e45; color: #fff; padding: 7px 24px; font-size: 8.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; }
  .items-table { width: 100%; border-collapse: collapse; }
  .items-table th { background: #eff6ff; color: #1e3a8a; padding: 7px 10px; font-size: 8.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; text-align: left; border-bottom: 2px solid #bfdbfe; }
  .items-table th.r { text-align: right; }
  .items-table td { padding: 8px 10px; font-size: 10px; border-bottom: 1px solid #f0f2f5; }
  .items-table td.r { text-align: right; }
  .items-table tr:last-child td { border-bottom: none; }

  /* ── Totals ─────────────────────────────────────────── */
  .totals-wrap { display: table; width: 100%; margin-top: 0; border-top: 2px solid #e5e9f0; }
  .totals-left  { display: table-cell; width: 55%; padding: 14px 24px; vertical-align: top; }
  .totals-right { display: table-cell; width: 45%; padding: 14px 24px; vertical-align: top; }
  .total-row { display: flex; justify-content: space-between; padding: 4px 0; font-size: 11px; }
  .total-row.grand { border-top: 2px solid #0f1e45; margin-top: 6px; padding-top: 8px; font-size: 13px; font-weight: 900; color: #0f1e45; }
  .in-words { background: #f0fdf4; border-left: 3px solid #16a34a; padding: 8px 12px; font-size: 9px; color: #374151; margin-top: 8px; }

  /* ── Payment status ─────────────────────────────────── */
  .status-band { padding: 10px 24px; font-size: 10px; }
  .badge { padding: 2px 10px; border-radius: 10px; font-weight: 700; font-size: 9px; }
  .b-paid    { background: #dcfce7; color: #15803d; }
  .b-pending { background: #fef3c7; color: #b45309; }
  .b-overdue { background: #fee2e2; color: #dc2626; }

  /* ── Footer ─────────────────────────────────────────── */
  .footer { background: #0f1e45; color: #94a3b8; padding: 10px 24px; font-size: 7.5px; display: table; width: 100%; margin-top: 20px; }
  .footer-left  { display: table-cell; }
  .footer-right { display: table-cell; text-align: right; }

  /* ── PAID Watermark ─────────────────────────────────── */
  .paid-watermark {
    position: fixed;
    top: 38%;
    left: 12%;
    width: 76%;
    text-align: center;
    font-size: 72px;
    font-weight: 900;
    color: rgba(21, 128, 61, 0.13);
    letter-spacing: 18px;
    transform: rotate(-35deg);
    pointer-events: none;
    z-index: 999;
    border: 6px solid rgba(21, 128, 61, 0.12);
    padding: 6px 18px;
    border-radius: 8px;
  }
</style>
</head>
<body>

@if($showPaid)
<div class="paid-watermark">PAID</div>
@endif

<!-- Header -->
<div class="header">
  <div class="h-left">
    <div class="company-name">SMS Training Services</div>
    <div class="company-sub">Sustainable Management System Inc.</div>
  </div>
  <div class="h-right">
    <div class="inv-title">INVOICE</div>
    <div class="inv-number">{{ $invoice->invoice_number }}</div>
  </div>
</div>

<!-- Meta row: Invoice date + Due date -->
<div class="meta-row">
  <div class="meta-cell">
    <div class="meta-label">Invoice Date</div>
    <div class="meta-value">{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d M Y') }}</div>
  </div>
  <div class="meta-cell">
    <div class="meta-label">Payment Due</div>
    <div class="meta-value">{{ $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('d M Y') : '—' }}</div>
    <div class="meta-value-sub">
        <span class="badge {{ $showPaid ? 'b-paid' : (($ps === 'overdue') ? 'b-overdue' : 'b-pending') }}">
        {{ $showPaid ? 'PAID' : ucfirst($invoice->payment_status ?? 'Pending') }}
      </span>
    </div>
  </div>
</div>

<!-- Bill To / Training Details -->
<div class="two-col">
  <div class="col">
    <div class="col-header">Bill To</div>
    <p><strong>{{ $invoice->client_name }}</strong></p>
    @if($invoice->client_company)<p>{{ $invoice->client_company }}</p>@endif
    @if($invoice->client_email)<p>{{ $invoice->client_email }}</p>@endif
    @if($invoice->client_phone)<p>{{ $invoice->client_phone }}</p>@endif
    @if($invoice->client_address)<p>{{ $invoice->client_address }}</p>@endif
    @if($invoice->client_country)<p>{{ $invoice->client_country }}</p>@endif
  </div>
  <div class="col">
    <div class="col-header">Training Details</div>
    <p><strong>{{ $invoice->training_name }}</strong></p>
    @if($invoice->service_type)<p>Type: {{ $invoice->service_type }}</p>@endif
    @if($invoice->training_date)<p>Date: {{ \Carbon\Carbon::parse($invoice->training_date)->format('d M Y') }}</p>@endif
    @if($invoice->training_method_venue)<p>Mode / Venue: {{ $invoice->training_method_venue }}</p>@endif
    @if($invoice->training_duration)<p>Duration: {{ $invoice->training_duration }}</p>@endif
  </div>
</div>

<!-- Line Items -->
<div class="section-title">Description</div>
<table class="items-table">
  <thead>
    <tr>
      <th>#</th>
      <th>Description</th>
      <th>Mode</th>
      <th class="r">Qty</th>
      <th class="r">Unit Fee ({{ $invoice->currency ?? 'BDT' }})</th>
      <th class="r">Amount ({{ $invoice->currency ?? 'BDT' }})</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>1</td>
      <td>{{ $invoice->training_name }}</td>
      <td>{{ $invoice->service_type ?? '—' }}</td>
      <td class="r">{{ $invoice->number_of_participants ?? 1 }}</td>
      <td class="r">{{ number_format($invoice->fee_per_person ?? $invoice->total_amount, 2) }}</td>
      <td class="r">{{ number_format($invoice->charge_for ?? $invoice->total_amount, 2) }}</td>
    </tr>
  </tbody>
</table>

<!-- Totals + Amount in Words -->
<div class="totals-wrap">
  <div class="totals-left">
    @if($invoice->amount_in_words)
    <div class="in-words">
      <strong>Amount in Words:</strong><br>
      {{ $invoice->amount_in_words }}
    </div>
    @endif
    @if($invoice->notes)
    <div style="margin-top:10px;font-size:9px;color:#6b7280;"><strong>Notes:</strong> {{ $invoice->notes }}</div>
    @endif
  </div>
  <div class="totals-right">
    <div class="total-row">
      <span>Sub-Total</span>
      <span>{{ $invoice->currency ?? 'BDT' }} {{ number_format($invoice->subtotal ?? $invoice->total_amount, 2) }}</span>
    </div>
    @if(($invoice->discount_amount ?? 0) > 0)
    <div class="total-row" style="color:#dc2626;">
      <span>Discount</span>
      <span>- {{ $invoice->currency ?? 'BDT' }} {{ number_format($invoice->discount_amount, 2) }}</span>
    </div>
    @endif
    @if(($invoice->vat_amount ?? 0) > 0)
    <div class="total-row">
      <span>VAT ({{ $invoice->vat_percent ?? 0 }}%)</span>
      <span>{{ $invoice->currency ?? 'BDT' }} {{ number_format($invoice->vat_amount, 2) }}</span>
    </div>
    @endif
    <div class="total-row grand">
      <span>Total Payable</span>
      <span>{{ $invoice->currency ?? 'BDT' }} {{ number_format($invoice->grand_total ?? $invoice->total_amount, 2) }}</span>
    </div>
    @if(($invoice->amount_paid ?? 0) > 0)
    <div class="total-row" style="color:#15803d;">
      <span>Amount Received</span>
      <span>{{ $invoice->currency ?? 'BDT' }} {{ number_format($invoice->amount_paid, 2) }}</span>
    </div>
    @endif
  </div>
</div>

<!-- Footer -->
<div class="footer">
  <div class="footer-left">Sustainable Management System Inc. — Confidential, internal use only</div>
  <div class="footer-right">Generated: {{ now()->format('d M Y, H:i') }}</div>
</div>

</body>
</html>
