<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ ($template ?? 'attendance') === 'completion' ? 'Certificate of Completion' : 'Certificate of Attendance' }}</title>

    <style>
        @page { size: A4 landscape; margin: 0; }

        html, body {
            margin: 0;
            padding: 0;
            width: 841.89pt;
            height: 595.28pt;
            overflow: hidden;
            background: #ffffff;
            color: #000000;
            font-family: Helvetica, DejaVu Sans, Arial, sans-serif;
        }

        * {
            box-sizing: border-box;
        }

        .page {
            position: relative;
            width: 841.89pt;
            height: 595.28pt;
            overflow: hidden;
            background: #ffffff;
        }

        .border {
            position: absolute;
            top: 18pt;
            left: 18pt;
            width: 805.89pt;
            height: 559.28pt;
            border: 4pt solid {{ ($template ?? 'attendance') === 'completion' ? '#1d4ed8' : '#0f766e' }};
        }

        .logo {
            position: absolute;
            top: 51pt;
            left: 0;
            width: 841.89pt;
            text-align: center;
        }

        .logo img {
            height: 78pt;
        }

        .title {
            position: absolute;
            top: 154pt;
            left: 0;
            width: 841.89pt;
            text-align: center;
            font-size: 30pt;
            line-height: 1;
            font-weight: 700;
            letter-spacing: .1pt;
            text-transform: uppercase;
        }

        .subtitle {
            position: absolute;
            top: 191pt;
            left: 0;
            width: 841.89pt;
            text-align: center;
            color: #1f2937;
            font-size: 13pt;
            line-height: 1.2;
        }

        .presented {
            position: absolute;
            top: 230pt;
            left: 0;
            width: 841.89pt;
            text-align: center;
            font-size: 15pt;
            line-height: 1.2;
            font-weight: 700;
        }

        .participant {
            position: absolute;
            top: 258pt;
            left: 0;
            width: 841.89pt;
            text-align: center;
            font-size: 27pt;
            line-height: 1.1;
            font-weight: 700;
            text-transform: uppercase;
        }

        .course-text {
            position: absolute;
            top: 305pt;
            left: 92pt;
            width: 657.89pt;
            text-align: center;
            font-size: 16pt;
            line-height: 1.42;
        }

        .course-name {
            color: {{ ($template ?? 'attendance') === 'completion' ? '#1d4ed8' : '#0f766e' }};
            font-size: 18pt;
            font-weight: 800;
        }

        .sig-left,
        .sig-right {
            position: absolute;
            top: 424pt;
            width: 160pt;
            text-align: center;
        }

        .sig-left {
            left: 102pt;
        }

        .sig-right {
            right: 102pt;
        }

        .signature-img {
            height: 48pt;
            margin-bottom: 0;
        }

        .sig-line {
            border-top: 1pt solid #000000;
            width: 150pt;
            height: 1pt;
            margin: 0 auto 7pt auto;
        }

        .sig-title {
            font-size: 10.5pt;
            font-weight: 800;
            line-height: 1.1;
        }

        .center-assets {
            position: absolute;
            top: 418pt;
            left: 0;
            width: 841.89pt;
            text-align: center;
        }

        .seal-img {
            width: 70pt;
            height: 70pt;
            vertical-align: middle;
            margin-right: 15pt;
        }

        .qr-img {
            width: 75pt;
            height: 75pt;
            vertical-align: middle;
            border: 1pt solid {{ ($template ?? 'attendance') === 'completion' ? '#1d4ed8' : '#0f766e' }};
            padding: 3pt;
            background: #ffffff;
        }

        .meta-bar {
            position: absolute;
            left: 54pt;
            bottom: 24pt;
            width: 733.89pt;
            min-height: 55pt;
            padding: 10pt 18pt 8pt 18pt;
            border-radius: 5pt;
            background: #fcfcfc;
            text-align: center;
        }

        .meta-company {
            color: #0f172a;
            font-size: 12.5pt;
            line-height: 1.1;
            font-weight: 800;
            margin-bottom: 5pt;
        }

        .meta-statement {
            color: #4b5563;
            font-size: 7.5pt;
            line-height: 1.2;
            margin-bottom: 8pt;
        }

        .meta-details {
            color: #000000;
            font-size: 7.5pt;
            line-height: 1.2;
            white-space: nowrap;
        }
    </style>
</head>

<body>

@php
    $template = $template ?? 'attendance';
    $isCompletion = $template === 'completion';
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

<div class="page">
    <div class="border"></div>

    <div class="logo">
        @if($logo)
            <img src="{{ $logo }}" alt="SMS">
        @endif
    </div>

    <div class="title">
        Certificate of {{ $isCompletion ? 'Completion' : 'Attendance' }}
    </div>

    <div class="subtitle">
        Awarded for successful {{ $isCompletion ? 'completion of' : 'participation in' }} the professional training program
    </div>

    <div class="presented">Presented to</div>

    <div class="participant">
        {{ $enrollment->full_name }}
    </div>

    <div class="course-text">
        This is to certify that the above-named participant has successfully
        {{ $isCompletion ? 'completed' : 'attended' }} the
        <strong>{{ $duration }}</strong> long professional training program titled
        <span class="course-name">"{{ $course->name ?? 'Training Program' }}"</span>
        organized and delivered by Sustainable Management System Inc.
    </div>

    <div class="sig-left">
        @if($signature)
            <img src="{{ $signature }}" class="signature-img" alt="">
        @endif
        <div class="sig-line"></div>
        <div class="sig-title">Authorized Signature</div>
    </div>

    <div class="center-assets">
        @if($seal)
            <img src="{{ $seal }}" class="seal-img" alt="">
        @endif
        <img src="{{ $qrUrl }}" class="qr-img" alt="QR Code">
    </div>

    <div class="sig-right">
        @if($elearningSignature)
            <img src="{{ $elearningSignature }}" class="signature-img" alt="">
        @endif
        <div class="sig-line"></div>
        <div class="sig-title">Training Division</div>
    </div>

    <div class="meta-bar">
        <div class="meta-company">Sustainable Management System Inc.</div>

        <div class="meta-statement">
            This certificate is digitally verifiable through the QR code and confirms
            {{ $isCompletion ? 'successful completion of' : 'participation in' }} the stated professional training program.
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

</body>
</html>
