@extends('layouts.app')

@section('content')

<div style="background:#f4f6f9; padding:24px;">

    <div style="max-width:1050px; margin:auto; display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
        <div>
            <h2 style="margin:0; font-size:26px; font-weight:800; color:#111827;">Invoice Preview</h2>
            <p style="margin:4px 0 0; color:#6b7280;">{{ $invoice->invoice_number }}</p>
        </div>

        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <a href="/admin/invoices/pdf/{{ $invoice->id }}" target="_blank"
               style="background:#173a8a; color:white; padding:10px 16px; text-decoration:none; border-radius:6px; font-weight:700; font-size:13px;">
                ⬇ Download PDF
            </a>
            <a href="/admin/invoices/edit/{{ $invoice->id }}"
               style="background:#f59e0b; color:white; padding:10px 16px; text-decoration:none; border-radius:6px; font-weight:700; font-size:13px;">
                ✏ Edit Invoice
            </a>
            <a href="/admin/invoices/{{ $invoice->id }}/send-email"
               style="background:#0ea5e9; color:white; padding:10px 16px; text-decoration:none; border-radius:6px; font-weight:700; font-size:13px;">
                📧 Send Email
            </a>
            <a href="/admin/invoices"
               style="background:#6b7280; color:white; padding:10px 16px; text-decoration:none; border-radius:6px; font-weight:700; font-size:13px;">
                ← Back
            </a>
        </div>
    </div>

    @if(session('success'))
    <div style="max-width:1050px; margin:auto 0 16px; background:#d1fae5; border:1px solid #6ee7b7; color:#065f46; padding:12px 18px; border-radius:8px; font-weight:600;">
        ✓ {{ session('success') }}
    </div>
    @endif

    <div style="max-width:1050px; margin:auto; background:white; padding:34px; border:1px solid #e5e7eb; box-shadow:0 8px 24px rgba(15,23,42,.08);">

        {{-- ── Header ── --}}
        <div style="display:flex; justify-content:space-between; align-items:flex-start; border-bottom:4px solid #173a8a; padding-bottom:18px;">
            <div>
                <h1 style="margin:0; font-size:38px; color:#173a8a; letter-spacing:1px;">INVOICE</h1>
                <p style="margin:6px 0 0; color:#6b7280; font-size:14px;">Training & Capacity Building Services</p>

                <div style="margin-top:18px; display:grid; grid-template-columns:repeat(4, auto); gap:18px; font-size:13px;">
                    <div><strong>Invoice No</strong><br>{{ $invoice->invoice_number }}</div>
                    <div><strong>Date</strong><br>{{ $invoice->invoice_date ? \Carbon\Carbon::parse($invoice->invoice_date)->format('d M Y') : '—' }}</div>
                    <div><strong>Currency</strong><br>{{ $invoice->currency ?? 'BDT' }}</div>
                    <div>
                        <strong>Status</strong><br>
                        @php $ps = $invoice->payment_status ?? 'Unpaid'; @endphp
                        <span style="display:inline-block; padding:2px 10px; border-radius:20px; font-size:12px; font-weight:700;
                            {{ $ps === 'Paid' ? 'background:#dcfce7; color:#166534;' : ($ps === 'Partial' ? 'background:#fffbeb; color:#92400e;' : 'background:#fee2e2; color:#991b1b;') }}">
                            {{ $ps }}
                        </span>
                    </div>
                </div>
            </div>

            @php
                $logo = file_exists(public_path('sms-logo.png'))
                    ? 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('sms-logo.png')))
                    : null;
            @endphp

            <div style="text-align:right;">
                @if($logo)
                    <img src="{{ $logo }}" style="height:72px; margin-bottom:10px;">
                @endif
                <div style="font-size:13px; line-height:1.5; color:#374151;">
                    <strong>Sustainable Management System Bangladesh</strong><br>
                    01, Sonargaon Janapath Avenue, Sector#12<br>
                    Uttara Model Town, Dhaka-1230, Bangladesh<br>
                    info@smscert.com | www.smscert.com
                </div>
            </div>
        </div>

        {{-- ── Client & Training ── --}}
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:18px; margin-top:22px;">
            <div style="background:#f8fafc; border-left:4px solid #173a8a; padding:16px;">
                <h3 style="margin:0 0 10px; font-size:15px; color:#173a8a;">CLIENT DETAILS</h3>
                <p style="margin:0; font-size:14px;"><strong>{{ $invoice->client_name }}</strong></p>
                @if($invoice->contact_person)
                <p style="margin:4px 0; font-size:13px;">Contact: {{ $invoice->contact_person }}</p>
                @endif
                @if($invoice->client_email)
                <p style="margin:4px 0; font-size:13px;">Email: {{ $invoice->client_email }}</p>
                @endif
                @if($invoice->client_phone)
                <p style="margin:4px 0; font-size:13px;">Phone: {{ $invoice->client_phone }}</p>
                @endif
                @if($invoice->client_country)
                <p style="margin:4px 0; font-size:13px;">Country: {{ $invoice->client_country }}</p>
                @endif
                @if($invoice->client_address)
                <p style="margin:4px 0 0; font-size:13px;">{{ $invoice->client_address }}</p>
                @endif
            </div>

            <div style="background:#f8fafc; border-left:4px solid #f5a000; padding:16px;">
                <h3 style="margin:0 0 10px; font-size:15px; color:#173a8a;">TRAINING DETAILS</h3>
                <p style="margin:0; font-size:14px;"><strong>{{ $invoice->training_name ?? '—' }}</strong></p>
                @if($invoice->training_date)
                <p style="margin:4px 0; font-size:13px;">Date: {{ $invoice->training_date }}</p>
                @endif
                @if($invoice->training_duration)
                <p style="margin:4px 0; font-size:13px;">Duration: {{ $invoice->training_duration }}</p>
                @endif
                @if($invoice->training_method_venue)
                <p style="margin:4px 0; font-size:13px;">Mode/Venue: {{ $invoice->training_method_venue }}</p>
                @endif
                @if($invoice->number_of_participants)
                <p style="margin:4px 0; font-size:13px;">Participants: {{ $invoice->number_of_participants }}</p>
                @endif
                <p style="margin:4px 0 0; font-size:13px;">Fee Per Person: {{ $invoice->currency ?? 'BDT' }} {{ number_format($invoice->fee_per_person ?? 0, 2) }}</p>
            </div>
        </div>

        {{-- ── Line items ── --}}
        <table style="width:100%; border-collapse:collapse; margin-top:24px; font-size:14px;">
            <thead>
                <tr style="background:#173a8a; color:white;">
                    <th style="padding:11px; text-align:left;">Service Description</th>
                    <th style="padding:11px; text-align:center;">Qty</th>
                    <th style="padding:11px; text-align:right;">Rate</th>
                    <th style="padding:11px; text-align:right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @if($invoice->invoice_type === 'auto')
                {{-- Auto invoices render a single line from the invoice record itself --}}
                <tr>
                    <td style="padding:12px; border:1px solid #e5e7eb;">
                        {{ $invoice->service_type ?? 'Capacity Building Training Program' }}<br>
                        <span style="font-size:12px; color:#6b7280;">{{ $invoice->training_name }}</span>
                    </td>
                    <td style="padding:12px; border:1px solid #e5e7eb; text-align:center;">{{ $invoice->number_of_participants ?? 1 }}</td>
                    <td style="padding:12px; border:1px solid #e5e7eb; text-align:right;">{{ number_format($invoice->fee_per_person ?? 0, 2) }}</td>
                    <td style="padding:12px; border:1px solid #e5e7eb; text-align:right;">{{ number_format($invoice->charge_for ?? 0, 2) }}</td>
                </tr>
                @else
                {{-- Manual invoices may have InvoiceItem records --}}
                @forelse($invoice->items as $item)
                <tr>
                    <td style="padding:12px; border:1px solid #e5e7eb;">
                        {{ $item->description ?? $invoice->service_type ?? 'Training Participation Fee' }}<br>
                        @if($item->enrollment)
                        <span style="font-size:12px; color:#6b7280;">
                            {{ $item->enrollment->full_name ?? '' }}
                            @if($item->enrollment->trainingSchedule?->course)
                            — {{ $item->enrollment->trainingSchedule->course->name }}
                            @endif
                        </span>
                        @endif
                    </td>
                    <td style="padding:12px; border:1px solid #e5e7eb; text-align:center;">{{ $item->quantity ?? 1 }}</td>
                    <td style="padding:12px; border:1px solid #e5e7eb; text-align:right;">{{ number_format($item->unit_price ?? 0, 2) }}</td>
                    <td style="padding:12px; border:1px solid #e5e7eb; text-align:right;">{{ number_format($item->line_total ?? 0, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td style="padding:12px; border:1px solid #e5e7eb;">
                        {{ $invoice->service_type ?? 'Capacity Building Training Program' }}<br>
                        <span style="font-size:12px; color:#6b7280;">{{ $invoice->training_name }}</span>
                    </td>
                    <td style="padding:12px; border:1px solid #e5e7eb; text-align:center;">{{ $invoice->number_of_participants ?? 1 }}</td>
                    <td style="padding:12px; border:1px solid #e5e7eb; text-align:right;">{{ number_format($invoice->fee_per_person ?? 0, 2) }}</td>
                    <td style="padding:12px; border:1px solid #e5e7eb; text-align:right;">{{ number_format($invoice->charge_for ?? 0, 2) }}</td>
                </tr>
                @endforelse
                @endif
            </tbody>
        </table>

        {{-- ── Totals ── --}}
        <div style="display:flex; justify-content:flex-end; margin-top:18px;">
            <table style="width:380px; border-collapse:collapse; font-size:14px;">
                <tr>
                    <td style="padding:8px; border-bottom:1px solid #e5e7eb;">Charge For</td>
                    <td style="padding:8px; border-bottom:1px solid #e5e7eb; text-align:right;">
                        {{ $invoice->currency ?? 'BDT' }} {{ number_format($invoice->charge_for ?? 0, 2) }}
                    </td>
                </tr>
                <tr>
                    <td style="padding:8px; border-bottom:1px solid #e5e7eb;">
                        Discount ({{ number_format(($invoice->discount_percent ?? 0), 2) }}%)
                    </td>
                    <td style="padding:8px; border-bottom:1px solid #e5e7eb; text-align:right;">
                        {{ $invoice->currency ?? 'BDT' }} {{ number_format($invoice->discount_amount ?? 0, 2) }}
                    </td>
                </tr>
                <tr>
                    <td style="padding:8px; border-bottom:1px solid #e5e7eb;">Sub-total</td>
                    <td style="padding:8px; border-bottom:1px solid #e5e7eb; text-align:right;">
                        {{ $invoice->currency ?? 'BDT' }} {{ number_format($invoice->subtotal ?? 0, 2) }}
                    </td>
                </tr>
                <tr>
                    <td style="padding:8px; border-bottom:1px solid #e5e7eb;">
                        VAT ({{ number_format($invoice->vat_percent ?? 0, 2) }}%)
                    </td>
                    <td style="padding:8px; border-bottom:1px solid #e5e7eb; text-align:right;">
                        {{ $invoice->currency ?? 'BDT' }} {{ number_format($invoice->vat_amount ?? 0, 2) }}
                    </td>
                </tr>
                <tr style="background:#173a8a; color:white;">
                    <td style="padding:12px; font-size:16px; font-weight:800;">TOTAL PAYABLE</td>
                    <td style="padding:12px; font-size:16px; font-weight:800; text-align:right;">
                        {{ $invoice->currency ?? 'BDT' }} {{ number_format(($invoice->grand_total ?? $invoice->total_amount ?? 0), 2) }}
                    </td>
                </tr>
            </table>
        </div>

        {{-- ── Amount in words ── --}}
        <div style="margin-top:14px; background:#fff7e6; border-left:4px solid #f5a000; padding:10px 14px; font-size:14px;">
            <strong>Amount in Words:</strong> {{ $invoice->amount_in_words ?? '—' }}
        </div>

        {{-- ── Payment & Prepared By ── --}}
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:18px; margin-top:22px;">
            <div style="border:1px solid #e5e7eb; border-radius:8px; padding:16px;">
                <h3 style="margin:0 0 12px; font-size:14px; font-weight:800; color:#173a8a; text-transform:uppercase; letter-spacing:.5px;">Payment Information</h3>
                <table style="width:100%; font-size:13px;">
                    <tr>
                        <td style="padding:5px 0; color:#6b7280; width:50%;">Payment Status</td>
                        <td style="padding:5px 0; font-weight:700;">
                            <span style="padding:2px 10px; border-radius:20px; font-size:12px;
                                {{ ($invoice->payment_status ?? '') === 'Paid' ? 'background:#dcfce7; color:#166534;' : (($invoice->payment_status ?? '') === 'Partial' ? 'background:#fffbeb; color:#92400e;' : 'background:#fee2e2; color:#991b1b;') }}">
                                {{ $invoice->payment_status ?? 'Unpaid' }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:5px 0; color:#6b7280;">Amount Paid</td>
                        <td style="padding:5px 0; font-weight:700; color:#15803d;">
                            {{ $invoice->currency ?? 'BDT' }} {{ number_format($invoice->amount_paid ?? 0, 2) }}
                        </td>
                    </tr>
                    @php
                        $balance = max(0, (float)($invoice->grand_total ?? $invoice->total_amount ?? 0) - (float)($invoice->amount_paid ?? 0));
                    @endphp
                    <tr>
                        <td style="padding:5px 0; color:#6b7280;">Balance Due</td>
                        <td style="padding:5px 0; font-weight:700; {{ $balance > 0 ? 'color:#dc2626;' : 'color:#15803d;' }}">
                            {{ $balance > 0 ? ($invoice->currency ?? 'BDT') . ' ' . number_format($balance, 2) : 'Nil — Fully Paid' }}
                        </td>
                    </tr>
                    @if($invoice->payment_method)
                    <tr>
                        <td style="padding:5px 0; color:#6b7280;">Payment Method</td>
                        <td style="padding:5px 0;">{{ $invoice->payment_method }}</td>
                    </tr>
                    @endif
                </table>
            </div>

            <div style="border:1px solid #e5e7eb; border-radius:8px; padding:16px;">
                <h3 style="margin:0 0 12px; font-size:14px; font-weight:800; color:#173a8a; text-transform:uppercase; letter-spacing:.5px;">Invoice Meta</h3>
                <table style="width:100%; font-size:13px;">
                    <tr>
                        <td style="padding:5px 0; color:#6b7280; width:50%;">Invoice Type</td>
                        <td style="padding:5px 0;">{{ $invoice->invoice_type }}</td>
                    </tr>
                    <tr>
                        <td style="padding:5px 0; color:#6b7280;">Prepared By</td>
                        <td style="padding:5px 0;">{{ $invoice->prepared_by ?? '—' }}</td>
                    </tr>
                    @if($invoice->due_date)
                    <tr>
                        <td style="padding:5px 0; color:#6b7280;">Due Date</td>
                        <td style="padding:5px 0;">{{ \Carbon\Carbon::parse($invoice->due_date)->format('d M Y') }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td style="padding:5px 0; color:#6b7280;">Created</td>
                        <td style="padding:5px 0;">{{ $invoice->created_at->format('d M Y, H:i') }}</td>
                    </tr>
                    @if($invoice->payment_confirmed_email_sent)
                    <tr>
                        <td style="padding:5px 0; color:#6b7280;">Confirmation Email</td>
                        <td style="padding:5px 0; color:#15803d; font-weight:700;">✓ Sent</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>

        {{-- ── Payment logs ── --}}
        @if($invoice->paymentLogs && $invoice->paymentLogs->count())
        <div style="margin-top:22px; border:1px solid #e5e7eb; border-radius:8px; overflow:hidden;">
            <div style="background:#f8fafc; padding:12px 18px; border-bottom:1px solid #e5e7eb;">
                <strong style="font-size:14px; color:#173a8a;">Payment Log</strong>
            </div>
            <table style="width:100%; border-collapse:collapse; font-size:13px;">
                <thead>
                    <tr style="background:#f1f5f9;">
                        <th style="padding:10px 14px; text-align:left; font-weight:700; color:#374151;">Date</th>
                        <th style="padding:10px 14px; text-align:left; font-weight:700; color:#374151;">Amount</th>
                        <th style="padding:10px 14px; text-align:left; font-weight:700; color:#374151;">Method</th>
                        <th style="padding:10px 14px; text-align:left; font-weight:700; color:#374151;">Transaction ID</th>
                        <th style="padding:10px 14px; text-align:left; font-weight:700; color:#374151;">Received By</th>
                        <th style="padding:10px 14px; text-align:left; font-weight:700; color:#374151;">Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->paymentLogs as $plog)
                    <tr style="border-top:1px solid #f0f2f5;">
                        <td style="padding:10px 14px;">{{ $plog->payment_date ? \Carbon\Carbon::parse($plog->payment_date)->format('d M Y') : '—' }}</td>
                        <td style="padding:10px 14px; font-weight:700; color:#15803d;">{{ $invoice->currency ?? 'BDT' }} {{ number_format($plog->amount, 2) }}</td>
                        <td style="padding:10px 14px;">{{ $plog->payment_method ?? '—' }}</td>
                        <td style="padding:10px 14px; font-family:monospace; font-size:12px;">{{ $plog->transaction_id ?? '—' }}</td>
                        <td style="padding:10px 14px;">{{ $plog->received_by ?? '—' }}</td>
                        <td style="padding:10px 14px; color:#6b7280;">{{ $plog->remarks ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

    </div>
</div>

@endsection
