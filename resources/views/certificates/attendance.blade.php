<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Certificate of Attendance</title>

    <style>
        @page { size: A4 landscape; margin: 0; }

        *, *::before, *::after {
            box-sizing: border-box;
        }

        html, body {
            margin: 0;
            padding: 0;
            width: 297mm;
            height: 210mm;
            font-family: DejaVu Sans, sans-serif;
            color: #000;
            background: #fff;
            overflow: hidden;
        }

        .certificate {
            position: relative;
            width: 297mm;
            height: 210mm;
            background-color: #fff;
            overflow: hidden;
        }

        .border {
            position: absolute;
            top: 7mm;
            left: 7mm;
            width: 283mm;
            height: 196mm;
            border: 6px solid #0f766e;
            box-sizing: border-box;
        }

        .header-area {
            position: absolute;
            top: 12mm;
            left: 0;
            right: 0;
            text-align: center;
        }

        .logo img {
            height: 30mm;
        }

        .title {
            font-size: 38px;
            font-weight: 700;
            margin: 4mm 0 2mm 0;
            text-transform: uppercase;
        }

        .subtitle {
            font-size: 16px;
            color: #374151;
        }

        .content-area {
            position: absolute;
            top: 73mm;
            left: 0;
            right: 0;
            text-align: center;
        }

        .presented {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 3mm;
        }

        .participant {
            font-size: 34px;
            font-weight: 700;
            margin-bottom: 6mm;
            text-transform: uppercase;
        }

        .course-text {
            font-size: 19px;
            line-height: 1.25;
            width: 235mm;
            margin: 0 auto;
        }

        .course-name {
            font-weight: 800;
            color: #0f766e;
            font-size: 21px;
            display: inline;
        }

        .footer-area {
            position: absolute;
            bottom: 22mm;
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

        .signature-img {
            height: 16mm;
            margin-bottom: 1mm;
        }

        .line {
            border-top: 1.5px solid #000;
            width: 50mm;
            margin: 0 auto 2mm auto;
        }

        .sig-title {
            font-size: 13px;
            font-weight: 800;
        }

        .sig-subtitle {
            font-size: 10px;
            font-weight: 600;
            margin-top: 1.5mm;
            color: #374151;
        }

        .seal-img {
            width: 28mm;
            height: 28mm;
            vertical-align: middle;
            margin-right: 5mm;
        }

        .qr-img {
            width: 25mm;
            height: 25mm;
            vertical-align: middle;
            border: 1px solid #0f766e;
            padding: 1mm;
            background: #fff;
        }

        .meta-bar {
            position: absolute;
            bottom: 11mm;
            left: 12mm;
            width: 259mm;
            min-height: 20mm;
            background: #FCFCFC;
            padding: 3mm 5mm 2mm 5mm;
            border-radius: 6px;
            text-align: center;
            box-sizing: border-box;
        }

        .meta-company {
            font-size: 14px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 1.1mm;
        }

        .meta-statement {
            font-size: 10px;
            color: #4b5563;
            line-height: 1.1;
            margin-bottom: 2mm;
        }

        .meta-details {
            font-size: 10px;
            color: #000;
        }
    </style>
</head>

<body>

@php
    $course = $enrollment->trainingSchedule->course ?? null;

    $verifyUrl = url('/verify-certificate/' . $enrollment->certificate_number);
    $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=240x240&data=' . urlencode($verifyUrl);

    $logo = file_exists(public_path('sms-logo.png'))
        ? 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('sms-logo.png')))
        : null;

    $signature = file_exists(public_path('ceo-signature.png'))
        ? 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('ceo-signature.png')))
        : null;

    $elearningSignature = file_exists(public_path('sign-elearning.png'))
        ? 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('sign-elearning.png')))
        : null;

    $seal = file_exists(public_path('sms-seal.png'))
        ? 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('sms-seal.png')))
        : null;

    $startDate = optional($enrollment->trainingSchedule)->start_date
        ? \Carbon\Carbon::parse($enrollment->trainingSchedule->start_date)->format('d M Y')
        : 'N/A';

    $endDate = optional($enrollment->trainingSchedule)->end_date
        ? \Carbon\Carbon::parse($enrollment->trainingSchedule->end_date)->format('d M Y')
        : 'N/A';

    $trainingDate = $startDate === $endDate ? $startDate : $startDate . ' - ' . $endDate;

    $issueDate = !empty($enrollment->certificate_issue_date)
        ? \Carbon\Carbon::parse($enrollment->certificate_issue_date)->format('d M Y')
        : 'N/A';

    $duration = $enrollment->trainingSchedule->duration ?? 'N/A';
@endphp

<div class="certificate">
    <div class="border">

        <div class="header-area">
            <div class="logo">
                @if($logo)
                    <img src="{{ $logo }}">
                @endif
            </div>

            <div class="title">Certificate of Attendance</div>
            <div class="subtitle">Awarded for successful participation in the professional training program</div>
        </div>

        <div class="content-area">
            <div class="presented">Presented to</div>

            <div class="participant">
                {{ $enrollment->full_name }}
            </div>

            <div class="course-text">
                This is to certify that the above-named participant has successfully attended the
                <strong>{{ $duration }}</strong> long professional training program titled
                <span class="course-name">"{{ $course->name ?? 'Training Program' }}"</span>
                organized and delivered by Sustainable Management System Inc.
            </div>
        </div>

        <div class="footer-area">
            <div class="sig-block sig-left">
                @if($signature)
                    <img src="{{ $signature }}" class="signature-img">
                @endif
                <div class="line"></div>
                <div class="sig-title">Authorized Signature</div>
            </div>

            <div class="center-assets">
                @if($seal)
                    <img src="{{ $seal }}" class="seal-img">
                @endif
                <img src="{{ $qrUrl }}" class="qr-img">
            </div>

            <div class="sig-block sig-right">
                @if($elearningSignature)
                    <img src="{{ $elearningSignature }}" class="signature-img">
                @endif
                <div class="line"></div>
                <div class="sig-title">Training Division</div>
            </div>
        </div>

        <div class="meta-bar">
            <div class="meta-company">Sustainable Management System Inc.</div>

            <div class="meta-statement">
                This certificate is digitally verifiable through the QR code and confirms participation in the stated professional training program.
            </div>

            <div class="meta-details">
                <strong>Certificate No:</strong> {{ $enrollment->certificate_number }}
                &nbsp;&nbsp; | &nbsp;&nbsp;

                <strong>Training Date:</strong> {{ $trainingDate }}
                &nbsp;&nbsp; | &nbsp;&nbsp;

                <strong>Duration:</strong> {{ $duration }} Contact Hours
                &nbsp;&nbsp; | &nbsp;&nbsp;

                <strong>Issue Date:</strong> {{ $issueDate }}
            </div>
        </div>

    </div>
</div>

</body>
</html>
