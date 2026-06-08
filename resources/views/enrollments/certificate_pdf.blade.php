<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            text-align: center;
            border: 8px solid #173a8a;
            padding: 40px;
        }

        h1 {
            color: #173a8a;
            font-size: 36px;
        }

        h2 {
            font-size: 28px;
        }

        .name {
            font-size: 30px;
            font-weight: bold;
            margin: 20px 0;
        }

        .details {
            font-size: 18px;
            line-height: 1.8;
        }

        .footer {
            margin-top: 40px;
            width: 100%;
        }

        .left {
            float: left;
            width: 40%;
            text-align: left;
        }

        .right {
            float: right;
            width: 40%;
            text-align: right;
        }
    </style>
</head>
<body>

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
        <div class="left">
            ________________________
            <br>
            Authorized Signature
        </div>

        <div class="right">
            Sustainable Management System Inc.
            <br>
            www.smscert.com
        </div>
    </div>

</body>
</html>