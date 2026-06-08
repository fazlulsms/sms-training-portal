<!DOCTYPE html>
<html>
<head>
    <title>Training Certificate</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f3f4f6;
            margin: 0;
            padding: 20px;
        }

        .certificate {
            width: 900px;
            margin: 20px auto;
            background: white;
            border: 8px solid #173a8a;
            padding: 50px;
            text-align: center;
            box-sizing: border-box;
        }

        h1 {
            color: #173a8a;
            font-size: 38px;
            margin-bottom: 10px;
        }

        h2 {
            font-size: 28px;
            margin: 25px 0;
            color: #111827;
        }

        .name {
            font-size: 32px;
            font-weight: bold;
            color: #111827;
            margin: 25px 0;
        }

        .details {
            font-size: 18px;
            line-height: 1.8;
            margin-top: 20px;
        }

        .footer {
            margin-top: 60px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            text-align: center;
        }

        .signature {
            text-align: left;
        }

        .organization {
            text-align: right;
        }

        .qr-section {
            text-align: center;
        }

        .qr-section p {
            font-size: 12px;
            margin-top: 8px;
        }

        .print-btn {
            text-align: center;
            margin-bottom: 20px;
        }

        .print-btn button {
            background: #173a8a;
            color: white;
            padding: 12px 20px;
            border: none;
            cursor: pointer;
            font-size: 15px;
            border-radius: 5px;
        }

        @media print {
            .print-btn {
                display: none;
            }

            body {
                background: white;
                padding: 0;
            }

            .certificate {
                margin: 0;
                width: 100%;
                border: 8px solid #173a8a;
            }
        }
    </style>
</head>
<body>

@php
    $verifyUrl = url('/verify-certificate/' . $enrollment->certificate_number);
@endphp

<div class="print-btn">
    <button onclick="window.print()">Print Certificate</button>
</div>

<div class="certificate">

    <h1>Certificate of Completion</h1>

    <p>This is to certify that</p>

    <div class="name">
        {{ $enrollment->full_name }}
    </div>

    <p>has successfully completed the training program</p>

    <h2>Training Management Program</h2>

    <div class="details">
        <p><strong>Certificate No:</strong> {{ $enrollment->certificate_number }}</p>
        <p><strong>Issue Date:</strong> {{ $enrollment->certificate_issue_date }}</p>
        <p><strong>Company:</strong> {{ $enrollment->company }}</p>
        <p><strong>Validity:</strong> No Expiry</p>
    </div>

    <div class="footer">

        <div class="signature">
            <p>________________________</p>
            <p>Authorized Signature</p>
        </div>

        <div class="qr-section">
<img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={{ urlencode($verifyUrl) }}" alt="QR Code">
            <p>Scan to Verify</p>
        </div>

        <div class="organization">
            <p><strong>Sustainable Management System Inc.</strong></p>
            <p>www.smscert.com</p>
        </div>

    </div>

</div>

</body>
</html>