<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $invoice->invoice_number }}</title>

    <style>
@page {
    margin: 24px 26px;
}
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #111827;
            margin: 0;
        }

        .page {
            padding: 20px 22px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .blue {
            color: #173a8a;
        }

        .header-line {
            border-bottom: 3px solid #173a8a;
            padding-bottom: 10px;
        }

        .title {
            font-size: 30px;
            font-weight: bold;
            color: #173a8a;
            margin: 0;
        }

        .small {
            font-size: 10px;
            color: #4b5563;
        }

        .info-box {
            background: #f8fafc;
            border-left: 4px solid #173a8a;
            padding: 10px;
            vertical-align: top;
        }

        .info-box-gold {
            background: #f8fafc;
            border-left: 4px solid #f5a000;
            padding: 10px;
            vertical-align: top;
        }

        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #173a8a;
            margin-bottom: 6px;
        }

        .items th {
            background: #173a8a;
            color: #ffffff;
            padding: 8px;
            border: 1px solid #d1d5db;
        }

        .items td {
            padding: 8px;
            border: 1px solid #d1d5db;
        }

        .summary td {
            padding: 7px;
            border-bottom: 1px solid #e5e7eb;
        }

        .total-row td {
            background: #173a8a;
            color: #ffffff;
            font-weight: bold;
            font-size: 13px;
            padding: 9px;
        }

        .amount-words {
            background: #fff7e6;
            border-left: 4px solid #f5a000;
            padding: 9px;
            margin-top: 12px;
        }

        .box {
            border: 1px solid #d1d5db;
            padding: 10px;
            vertical-align: top;
        }

        .footer {
            margin-top: 16px;
            border-top: 1px solid #d1d5db;
            padding-top: 8px;
            text-align: center;
            font-size: 9px;
            color: #6b7280;
        }

        p {
            margin: 3px 0;
        }
    </style>
</head>

<body>
<div class="page">

    <table class="header-line">
        <tr>
            <td style="width:60%; vertical-align:top;">
                <div class="title">INVOICE</div>
                <div class="small">Training & Capacity Building Services</div>

                <br>

                <table style="font-size:10px;">
                    <tr>
                        <td><strong>Invoice No</strong><br>{{ $invoice->invoice_number }}</td>
                        <td><strong>Date</strong><br>{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d M Y') }}</td>
                        <td><strong>Currency</strong><br>{{ $invoice->currency }}</td>
                        <td><strong>Status</strong><br>{{ $invoice->payment_status }}</td>
                    </tr>
                </table>
            </td>

            <td style="width:40%; text-align:right; vertical-align:top;">
                @if(file_exists(public_path('sms-logo.png')))
                    <img src="{{ public_path('sms-logo.png') }}" style="height:65px;"><br>
                @endif

                <strong>Sustainable Management System Inc.</strong><br>
                <span class="small">
                                        info@smscert.com | www.smscert.com
                </span>
            </td>
        </tr>
    </table>

    <br>

    <table>
        <tr>
            <td class="info-box" style="width:49%;">
                <div class="section-title">CLIENT DETAILS</div>
                <p><strong>{{ $invoice->client_name }}</strong></p>
                <p>Contact: {{ $invoice->contact_person }}</p>
                <p>Email: {{ $invoice->client_email }}</p>
                <p>Phone: {{ $invoice->client_phone }}</p>
                <p>Country: {{ $invoice->client_country }}</p>
                <p>{{ $invoice->client_address }}</p>
            </td>

            <td style="width:2%;"></td>

            <td class="info-box-gold" style="width:49%;">
                <div class="section-title">TRAINING DETAILS</div>
                <p><strong>{{ $invoice->training_name }}</strong></p>
                <p>Date: {{ $invoice->training_date }}</p>
                <p>Duration: {{ $invoice->training_duration }}</p>
                <p>Mode/Venue: {{ $invoice->training_method_venue }}</p>
                <p>Participants: {{ $invoice->number_of_participants }}</p>
                <p>Fee Per Person: {{ $invoice->currency }} {{ number_format($invoice->fee_per_person, 2) }}</p>
            </td>
        </tr>
    </table>

    <br>

    <table class="items">
        <thead>
            <tr>
                <th style="text-align:left;">Service Description</th>
                <th style="width:12%; text-align:center;">Qty</th>
                <th style="width:18%; text-align:right;">Rate</th>
                <th style="width:20%; text-align:right;">Amount</th>
            </tr>
        </thead>

        <tbody>
            <tr>
                <td>
                    {{ $invoice->service_type }}<br>
                    <span class="small">{{ $invoice->training_name }}</span>
                </td>
                <td style="text-align:center;">{{ $invoice->number_of_participants }}</td>
                <td style="text-align:right;">{{ number_format($invoice->fee_per_person, 2) }}</td>
                <td style="text-align:right;">{{ number_format($invoice->charge_for, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <br>

    <table>
        <tr>
            <td style="width:55%;"></td>
            <td style="width:45%;">
                <table class="summary">
                    <tr>
                        <td>Charge For</td>
                        <td style="text-align:right;">{{ $invoice->currency }} {{ number_format($invoice->charge_for, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Discount ({{ number_format(($invoice->charge_for > 0 ? ($invoice->discount_amount / $invoice->charge_for) * 100 : 0), 2) }}%)</td>
                        <td style="text-align:right;">{{ $invoice->currency }} {{ number_format($invoice->discount_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Sub-total</td>
                        <td style="text-align:right;">{{ $invoice->currency }} {{ number_format($invoice->subtotal, 2) }}</td>
                    </tr>
                    <tr>
                        <td>VAT ({{ number_format($invoice->vat_percent, 2) }}%)</td>
                        <td style="text-align:right;">{{ $invoice->currency }} {{ number_format($invoice->vat_amount, 2) }}</td>
                    </tr>
                    <tr class="total-row">
                        <td>TOTAL PAYABLE</td>
                        <td style="text-align:right;">{{ $invoice->currency }} {{ number_format(($invoice->subtotal + $invoice->vat_amount), 2) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="amount-words">
        <strong>Amount in Words:</strong>
        {{ $invoice->amount_in_words ?? 'Amount in words will be generated for new invoices.' }}
    </div>

    <br>

    <table>
        <tr>
            <td class="box" style="width:49%;">
                <div class="section-title">PAYMENT INFORMATION</div>
                <p><strong>Payment Method:</strong> {{ $invoice->payment_method }}</p>
                <p><strong>Account Name:</strong> Sustainable Management System Bangladesh</p>
                <p><strong>Account No:</strong> 1462112000001076</p>
                <p><strong>Bank:</strong> United Commercial Bank Ltd (UCB)</p>
                <p><strong>Branch:</strong> Gausul Azam Avenue Branch, Uttara, Dhaka</p>
                <p><strong>Routing:</strong> 245260450</p>
                <p><strong>SWIFT:</strong> UCBLBDDHCML</p>
            </td>

            <td style="width:2%;"></td>

            <td class="box" style="width:49%;">
                <div class="section-title">PAYMENT TERMS</div>
                <p>1. Full advance payment is required to confirm participation.</p>
                <p>2. Payment may be made by cheque, pay order, or bank transfer.</p>
                <p>3. Participation will be confirmed after registration and payment.</p>

                <br>
                <p><strong>Prepared By:</strong> {{ $invoice->prepared_by ?? 'Imran Mahedi' }}</p>
            </td>
        </tr>
    </table>

    <div class="footer">
        *This is a computer generated invoice, therefore signature is not required.<br>
	<br>
        Sustainable Management System Inc.<br>
	Global HQ: 277 Cherry Street, Suite-12N, New York, New York 10002, USA<br>
	Regional HQ: 01, Sonargaon Janapath Avenue, Sector#12, Uttara Model Town, Dhaka-1230, Bangladesh<br>
	Contact: +8801873035178 | E-mail: coordinator@smscert.com | www.smscert.com<br>
        Assessment | Certification | Verification | Capacity Building    </div>

</div>
</body>
</html>