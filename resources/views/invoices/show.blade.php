@extends('layouts.app')

@section('content')

<div style="background:#f4f6f9; padding:24px;">

    <div style="max-width:1050px; margin:auto; display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
        <div>
            <h2 style="margin:0; font-size:26px; font-weight:800; color:#111827;">Invoice Preview</h2>
            <p style="margin:4px 0 0; color:#6b7280;">{{ $invoice->invoice_number }}</p>
        </div>

        <div style="display:flex; gap:10px;">
            <a href="/admin/invoices/pdf/{{ $invoice->id }}" target="_blank"
               style="background:#173a8a; color:white; padding:10px 16px; text-decoration:none; border-radius:4px; font-weight:700;">
                Download PDF
            </a>

            <a href="/admin/invoices"
               style="background:#6b7280; color:white; padding:10px 16px; text-decoration:none; border-radius:4px; font-weight:700;">
                Back
            </a>
        </div>
    </div>

    <div style="max-width:1050px; margin:auto; background:white; padding:34px; border:1px solid #e5e7eb; box-shadow:0 8px 24px rgba(15,23,42,.08);">

        <div style="display:flex; justify-content:space-between; align-items:flex-start; border-bottom:4px solid #173a8a; padding-bottom:18px;">
            <div>
                <h1 style="margin:0; font-size:38px; color:#173a8a; letter-spacing:1px;">INVOICE</h1>
                <p style="margin:6px 0 0; color:#6b7280; font-size:14px;">Training & Capacity Building Services</p>

                <div style="margin-top:18px; display:grid; grid-template-columns:repeat(4, auto); gap:18px; font-size:13px;">
                    <div><strong>Invoice No</strong><br>{{ $invoice->invoice_number }}</div>
                    <div><strong>Date</strong><br>{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d M Y') }}</div>
                    <div><strong>Currency</strong><br>{{ $invoice->currency }}</div>
                    <div><strong>Status</strong><br>{{ $invoice->payment_status }}</div>
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

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:18px; margin-top:22px;">
            <div style="background:#f8fafc; border-left:4px solid #173a8a; padding:16px;">
                <h3 style="margin:0 0 10px; font-size:15px; color:#173a8a;">CLIENT DETAILS</h3>
                <p style="margin:0; font-size:14px;"><strong>{{ $invoice->client_name }}</strong></p>
                <p style="margin:4px 0; font-size:13px;">Contact: {{ $invoice->contact_person }}</p>
                <p style="margin:4px 0; font-size:13px;">Email: {{ $invoice->client_email }}</p>
                <p style="margin:4px 0; font-size:13px;">Phone: {{ $invoice->client_phone }}</p>
                <p style="margin:4px 0; font-size:13px;">Country: {{ $invoice->client_country }}</p>
                <p style="margin:4px 0 0; font-size:13px;">{{ $invoice->client_address }}</p>
            </div>

            <div style="background:#f8fafc; border-left:4px solid #f5a000; padding:16px;">
                <h3 style="margin:0 0 10px; font-size:15px; color:#173a8a;">TRAINING DETAILS</h3>
                <p style="margin:0; font-size:14px;"><strong>{{ $invoice->training_name }}</strong></p>
                <p style="margin:4px 0; font-size:13px;">Date: {{ $invoice->training_date }}</p>
                <p style="margin:4px 0; font-size:13px;">Duration: {{ $invoice->training_duration }}</p>
                <p style="margin:4px 0; font-size:13px;">Mode/Venue: {{ $invoice->training_method_venue }}</p>
                <p style="margin:4px 0; font-size:13px;">Participants: {{ $invoice->number_of_participants }}</p>
                <p style="margin:4px 0 0; font-size:13px;">Fee Per Person: {{ $invoice->currency }} {{ number_format($invoice->fee_per_person, 2) }}</p>
            </div>
        </div>

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
                <tr>
                    <td style="padding:12px; border:1px solid #e5e7eb;">
                        {{ $invoice->service_type }}<br>
                        <span style="font-size:12px; color:#6b7280;">{{ $invoice->training_name }}</span>
                    </td>
                    <td style="padding:12px; border:1px solid #e5e7eb; text-align:center;">{{ $invoice->number_of_participants }}</td>
                    <td style="padding:12px; border:1px solid #e5e7eb; text-align:right;">{{ number_format($invoice->fee_per_person, 2) }}</td>
                    <td style="padding:12px; border:1px solid #e5e7eb; text-align:right;">{{ number_format($invoice->charge_for, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <div style="display:flex; justify-content:flex-end; margin-top:18px;">
            <table style="width:380px; border-collapse:collapse; font-size:14px;">
                <tr>
                    <td style="padding:8px; border-bottom:1px solid #e5e7eb;">Charge For</td>
                    <td style="padding:8px; border-bottom:1px solid #e5e7eb; text-align:right;">{{ $invoice->currency }} {{ number_format($invoice->charge_for, 2) }}</td>
                </tr>
                <tr>
                    <td style="padding:8px; border-bottom:1px solid #e5e7eb;">
                        Discount ({{ number_format(($invoice->charge_for > 0 ? ($invoice->discount_amount / $invoice->charge_for) * 100 : 0), 2) }}%)
                    </td>
                    <td style="padding:8px; border-bottom:1px solid #e5e7eb; text-align:right;">{{ $invoice->currency }} {{ number_format($invoice->discount_amount, 2) }}</td>
                </tr>
                <tr>
                    <td style="padding:8px; border-bottom:1px solid #e5e7eb;">Sub-total</td>
                    <td style="padding:8px; border-bottom:1px solid #e5e7eb; text-align:right;">{{ $invoice->currency }} {{ number_format($invoice->subtotal, 2) }}</td>
                </tr>
                <tr>
                    <td style="padding:8px; border-bottom:1px solid #e5e7eb;">VAT ({{ number_format($invoice->vat_percent, 2) }}%)%)</td>
                    <td style="padding:8px; border-bottom:1px solid #e5e7eb; text-align:right;">{{ $invoice->currency }} {{ number_format($invoice->vat_amount, 2) }}</td>
                </tr>
                <tr style="background:#173a8a; color:white;">
                    <td style="padding:12px; font-size:16px; font-weight:800;">TOTAL PAYABLE</td>
                    <td style="padding:12px; font-size:16px; font-weight:800; text-align:right;">{{ $invoice->currency }} {{ number_format(($invoice->subtotal + $invoice->vat_amount), 2) }}</td>
                </tr>
            </table>
        </div>

        <div style="margin-top:14px; background:#fff7e6; border-left:4px solid #f5a000; padding:10px 14px; font-size:14px;">
            <strong>Amount in Words:</strong> {{ $invoice->amount_in_words ?? 'Amount in words will be available for newly generated invoices.' }}
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:18px; margin-top:22px;">
            <div style="border:1px solid #e5