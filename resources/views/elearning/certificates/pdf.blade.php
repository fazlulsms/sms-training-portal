<!DOCTYPE html>
<html>
<head>
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }

        html, body {
            margin: 0;
            padding: 0;
            width: 297mm;
            height: 210mm;
            font-family: DejaVu Sans, sans-serif;
            color: #000;

        }

        .certificate {
            position: relative;
            width: 297mm;
            height: 210mm;
            background-color: #fff;
        }

        /* Border */
        .border {
            position: absolute;
            top: 7mm; left: 7mm; right: 7mm; bottom: 7mm;
            border: 6px solid #0f766e;
            box-sizing: border-box;
        }

        /* Header - Fixed to Top */
        .header-area {
            position: absolute;
            top: 15mm;
            left: 0;
            right: 0;
            text-align: center;
        }
        .logo img { height: 28mm; }
        .title { font-size: 38px; font-weight: 700; margin: 4mm 0 2mm 0; text-transform: uppercase; }
        .subtitle { font-size: 16px; color: #374151; }

        /* Content Area - Fixed to Center */
        .content-area {
            position: absolute;
            top: 75mm;
            left: 0;
            right: 0;
            text-align: center;
 }
.participant {
    font-size: 34px;
    font-weight: 700;
    margin-bottom: 6mm;
}

.course-text {
    font-size: 20px;
    line-height: 1.15;
    width: 230mm;
    margin: 0 auto;
}

.course-name {
    font-weight: 800;
    color: #0f766e;
    font-size: 22px;
    display: inline;
    margin-top: 0;
}
        /* Signature Area - Fixed to Bottom */
        .footer-area {
            position: absolute;
            bottom: 25mm; /* Distance from bottom */
            left: 25mm;
            right: 25mm;
            height: 40mm;
        }

        .sig-block {
            position: absolute;
            width: 60mm;
            text-align: center;
        }
        .sig-left { left: 0; }
        .sig-right { right: 0; }
        
        .center-assets {
            position: absolute;
            left: 70mm;
            right: 70mm;
            text-align: center;
        }

        .signature-img { height: 16mm; margin-bottom: 1mm; }
        .line { border-top: 1.5px solid #000; width: 50mm; margin: 0 auto 2mm auto; }
        .sig-title { font-size: 13px; font-weight: 800; }

        .seal-img { width: 28mm; height: 28mm; vertical-align: middle; margin-right: 5mm; }
        .qr-img { width: 25mm; height: 25mm; vertical-align: middle; border: 1px solid #0f766e; padding: 1mm; background: #fff; }

        /* Meta Bar - Bottom edge */
       .meta-bar {
    position: absolute;
    bottom: 10mm;
    left: 15mm;
    right: 15mm;
    background: #f3f4f6;
    padding: 3mm 5mm;
    border-radius: 6px;
    text-align: center;
}

.meta-company {
    font-size: 14px;
    font-weight: 800;
    color: #0f172a;
    margin-bottom: 1.5mm;
}

.meta-statement {
    font-size: 10px;
    color: #4b5563;
    line-height: 1.35;
    margin-bottom: 2mm;
}

.meta-details {
    font-size: 12px;
    color: #000;
}
    </style>
</head>
<body>

<div class="certificate">
    <div class="border">
        
        <div class="header-area">
            <div class="logo"><img src="{{ public_path('sms-logo.png') }}"></div>
            <div class="title">Certificate of Completion</div>
            <div class="subtitle">Awarded for successful completion of the self-paced eLearning program</div>
        </div>

        <div class="content-area">
            <div class="participant">{{ $enrollment->participant_name }}</div>
            <div class="course-text">
   This is to certify that the above-named participant has successfully completed the self-paced eLearning course titled
<span class="course-name">"{{ $enrollment->course->name }}"</span>
and fulfilled all required learning and assessment criteria.
</div>
        </div>

        <div class="footer-area">
           <div class="sig-block sig-left">
    <img src="{{ public_path('ceo-signature.png') }}" class="signature-img">
    <div class="line"></div>
    <div class="sig-title">Authorized Signature</div>
</div>

            <div class="center-assets">
                <img src="{{ public_path('sms-seal.png') }}" class="seal-img">
<img src="data:image/png;base64,{{ $qrCode }}" class="qr-img">
            </div>

            <div class="sig-block sig-right">
    <img src="{{ public_path('sign-elearning.png') }}" class="signature-img">
    <div class="line"></div>
    <div class="sig-title">eLearning Division</div>
</div>
        </div>

        <div class="meta-bar">
    <div class="meta-company">Sustainable Management System Inc.</div>

    <div class="meta-statement">
        This certificate is digitally verifiable through the QR code and confirms successful completion of the stated self-paced eLearning program.
    </div>

   <div class="meta-details">
    <strong>Certificate No:</strong> {{ $enrollment->certificate_number }}
    &nbsp;&nbsp; | &nbsp;&nbsp;

    <strong>Duration:</strong> {{ $enrollment->course->duration ?? 'N/A' }}
    &nbsp;&nbsp; | &nbsp;&nbsp;

    <strong>CPD Hours:</strong> {{ $enrollment->course->cpd_hours ?? 'N/A' }}
    &nbsp;&nbsp; | &nbsp;&nbsp;

    <strong>Completion Date:</strong>
    {{ \Carbon\Carbon::parse($enrollment->completion_date)->format('d M Y') }}
</div>
</div>

    </div>
</div>

</body>
</html>