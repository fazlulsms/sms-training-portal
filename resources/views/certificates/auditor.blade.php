<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Auditor Training Certificate</title>
<style>
@page { size: A4 portrait; margin: 5mm; }
*, *::before, *::after { box-sizing: border-box; }

html, body {
    margin: 0;
    padding: 0;
    width: 200mm;
    height: 286mm;
    overflow: hidden;
    background: #ffffff;
    color: #0f172a;
    font-family: "DejaVu Sans", Arial, Helvetica, sans-serif;
}

.page {
    position: relative;
    width: 200mm;
    height: 286mm;
    overflow: hidden;
    background: #ffffff;
    page-break-after: avoid;
    page-break-inside: avoid;
}

.frame {
    position: absolute;
    top: 8mm;
    left: 7mm;
    right: 7mm;
    bottom: 8mm;
    border: 2.8pt solid #082f5f;
}

.corner {
    position: absolute;
    width: 13mm;
    height: 13mm;
    border-color: #082f5f;
    border-style: solid;
}

.corner-tl { top: 11mm; left: 10mm; border-width: 3.6pt 0 0 3.6pt; }
.corner-tr { top: 11mm; right: 10mm; border-width: 3.6pt 3.6pt 0 0; }
.corner-bl { bottom: 11mm; left: 10mm; border-width: 0 0 3.6pt 3.6pt; }
.corner-br { bottom: 11mm; right: 10mm; border-width: 0 3.6pt 3.6pt 0; }

.content {
    position: absolute;
    top: 16mm;
    left: 19mm;
    right: 19mm;
    bottom: 31mm;
    text-align: center;
}

.sms-logo {
    height: 35mm;
    margin-top: 1mm;
    margin-bottom: 2mm;
}

.company {
    font-size: 10.5pt;
    font-weight: 800;
    letter-spacing: 1.2pt;
    color: #082f5f;
    text-transform: uppercase;
    margin-bottom: 5mm;
}

.cert-title {
    font-size: 20pt;
    line-height: 1;
    font-weight: 800;
    letter-spacing: 1.2pt;
    color: #082f5f;
    text-transform: uppercase;
    margin-bottom: 5mm;
}

.certify {
    font-size: 12pt;
    color: #475569;
    margin-bottom: 3mm;
}

.participant {
    width: 100%;
    font-size: {{ strlen($enrollment->full_name ?? '') > 34 ? '24pt' : '29pt' }};
    line-height: 1.08;
    font-weight: 800;
    color: #0f172a;
    text-transform: uppercase;
    margin-bottom: 5mm;
    overflow-wrap: break-word;
}

.completed {
    width: 157mm;
    margin: 0 auto 4mm auto;
    color: #334155;
    font-size: 8.7pt;
    line-height: 1.55;
    text-align: justify;
}

.course-block {
    width: 150mm;
    margin: 0 auto 5mm auto;
    color: #082f5f;
    text-transform: uppercase;
}

.course-name {
    font-size: {{ strlen($courseTitle ?? '') > 62 ? '12.5pt' : '15pt' }};
    line-height: 1.22;
    font-weight: 800;
    letter-spacing: .5pt;
}

.description {
    width: 157mm;
    margin: 0 auto 5mm auto;
    color: #334155;
    font-size: 8.2pt;
    line-height: 1.45;
    text-align: center;
}

.details-wrap {
    width: 157mm;
    margin: 0 auto 4.5mm auto;
    border: 1pt solid #cbd5e1;
    background: rgba(248, 250, 252, .92);
    display: table;
    table-layout: fixed;
}

.details {
    display: table-cell;
    width: 113mm;
    padding: 3mm 5mm 2.5mm 5mm;
    vertical-align: middle;
}

.verify {
    display: table-cell;
    width: 44mm;
    padding: 3mm 4mm 2.5mm 4mm;
    vertical-align: middle;
    text-align: center;
    border-left: 1pt solid #d8e0ea;
}

.detail-row {
    display: table;
    width: 100%;
    border-bottom: .55pt solid #dbe3ee;
}

.detail-row:last-child {
    border-bottom: 0;
}

.detail-label,
.detail-value {
    display: table-cell;
    padding: 1.15mm 0;
    font-size: 8pt;
    line-height: 1.15;
    white-space: nowrap;
}

.detail-label {
    width: 31mm;
    color: #475569;
    font-weight: 600;
}

.detail-value {
    color: #0f172a;
    font-weight: 800;
    text-align: right;
}

.qr-img {
    width: 25mm;
    height: 25mm;
    padding: 1mm;
    border: 1pt solid #082f5f;
    background: #ffffff;
    margin-bottom: 2mm;
}

.verify-title {
    color: #082f5f;
    font-size: 7.5pt;
    font-weight: 800;
    text-transform: uppercase;
    margin-bottom: 1mm;
}

.verify-url,
.verify-id {
    color: #475569;
    font-size: 6.4pt;
    line-height: 1.25;
}

.verify-id {
    color: #475569;
    font-weight: 400;
}

.credential-footer {
    width: 157mm;
    margin: 0 auto;
    display: table;
    table-layout: fixed;
    border: 1pt solid #0f172a;
    background: rgba(255, 255, 255, .96);
}

.credential-cell {
    display: table-cell;
    height: 34mm;
    text-align: center;
    vertical-align: middle;
    border-right: 1pt solid #0f172a;
    padding: 3mm 4mm;
}

.credential-cell:last-child {
    border-right: 0;
}

.credential-signature {
    width: 31%;
}

.credential-ascb {
    width: 25%;
}

.credential-irqao {
    width: 19%;
}

.credential-ceo {
    width: 25%;
}

.signature-img {
    height: 10mm;
    max-width: 42mm;
    margin-bottom: 1.4mm;
}

.signature-space {
    height: 10mm;
    margin-bottom: 1.4mm;
}

.sig-line {
    border-top: 1pt solid #082f5f;
    width: 38mm;
    margin: 0 auto 2mm auto;
}

.sig-name {
    color: #0f172a;
    font-size: 9.2pt;
    line-height: 1.2;
    font-weight: 800;
}

.sig-role {
    color: #475569;
    font-size: 7.2pt;
    line-height: 1.25;
}

.accreditation-logo {
    max-width: 100%;
    vertical-align: middle;
}

.ascb-logo {
    max-height: 18mm;
}

.irqao-logo {
    max-height: 23mm;
}

.footer {
    position: absolute;
    left: 19mm;
    right: 19mm;
    bottom: 13mm;
    padding-top: 2mm;
    border-top: 1pt solid #d8e0ea;
    color: #475569;
    font-size: 6.8pt;
    line-height: 1.45;
    text-align: center;
}

@media screen {
    body {
        width: auto;
        height: auto;
        min-height: 100vh;
        background: #e2e8f0;
        padding: 24px;
    }

    .page {
        margin: 0 auto;
        box-shadow: 0 18px 55px rgba(15, 23, 42, .22);
    }
}
</style>
</head>
<body>
@php
    $schedule = $enrollment->trainingSchedule ?? null;
    $course = $schedule?->course ?? null;
    $trainer = $schedule?->trainer ?? null;

    $courseTitle = $schedule?->training_title ?: ($course?->name ?? 'ISO 9001:2015 QMS Lead Auditor Training Course');

    $standard = 'ISO 9001:2015';
    if (preg_match('/ISO\s*[\d]+(?::\d+)?/i', $courseTitle, $match)) {
        $standard = strtoupper(trim($match[0]));
    }

    $systemName = 'QUALITY MANAGEMENT SYSTEMS';
    $schemeShort = 'QMS';
    if (stripos($courseTitle, '14001') !== false) {
        $systemName = 'ENVIRONMENTAL MANAGEMENT SYSTEMS';
        $schemeShort = 'EMS';
    } elseif (stripos($courseTitle, '45001') !== false) {
        $systemName = 'OCCUPATIONAL HEALTH & SAFETY MANAGEMENT SYSTEMS';
        $schemeShort = 'OHSMS';
    } elseif (stripos($courseTitle, '27001') !== false) {
        $systemName = 'INFORMATION SECURITY MANAGEMENT SYSTEMS';
        $schemeShort = 'ISMS';
    } elseif (stripos($courseTitle, '50001') !== false) {
        $systemName = 'ENERGY MANAGEMENT SYSTEMS';
        $schemeShort = 'EnMS';
    }

    $leadAuditorTitle = 'LEAD AUDITOR TRAINING COURSE';
    if (stripos($courseTitle, 'internal auditor') !== false) {
        $leadAuditorTitle = 'INTERNAL AUDITOR TRAINING COURSE';
    } elseif (stripos($courseTitle, 'auditor') !== false && stripos($courseTitle, 'lead') === false) {
        $leadAuditorTitle = 'AUDITOR TRAINING COURSE';
    }

    $start = $schedule?->start_date ? \Carbon\Carbon::parse($schedule->start_date)->format('d M Y') : 'N/A';
    $end = $schedule?->end_date ? \Carbon\Carbon::parse($schedule->end_date)->format('d M Y') : $start;
    $trainingDates = $start === $end ? $start : $start . ' - ' . $end;

    $issueDate = !empty($enrollment->certificate_issue_date)
        ? \Carbon\Carbon::parse($enrollment->certificate_issue_date)->format('d M Y')
        : 'N/A';

    $duration = $schedule?->duration ?: 'N/A';
    $durationDisplay = $duration !== 'N/A' && preg_match('/^\d+$/', trim((string) $duration))
        ? $duration . ' Hours'
        : $duration;

    $courseNo = $schedule?->batch_code
        ?: ('SMS/' . date('y', strtotime($schedule?->start_date ?? 'now')) . '/'
            . str_pad($enrollment->training_schedule_id ?? $enrollment->id, 3, '0', STR_PAD_LEFT));

    $certificateNo = $enrollment->certificate_number ?? 'N/A';
    $irqaoRegId = data_get($enrollment, 'irqao_reg_id')
        ?: data_get($schedule, 'irqao_reg_id')
        ?: $certificateNo;
    $verificationId = $certificateNo;

    $verifyUrl = url('/verify-certificate/' . $certificateNo);
    $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=240x240&data=' . urlencode($verifyUrl);

    $asset = fn (string $file) => file_exists(public_path($file))
        ? 'data:image/png;base64,' . base64_encode(file_get_contents(public_path($file)))
        : null;

    $smsLogo = $asset('sms-logo.png');
    $ascbLogo = $asset('ascb-logo.png');
    $irqaoLogo = $asset('Irqao-logo.png');
    $leadTrainerSignature = $asset('sign-elearning.png');
    $ceoSignature = $asset('ceo-signature.png');

    $leadTrainerName = $trainer?->name ?: 'Lead Trainer';
    $ceoName = 'Abdul Alim';
@endphp

<div class="page">
    <div class="frame"></div>
    <div class="corner corner-tl"></div>
    <div class="corner corner-tr"></div>
    <div class="corner corner-bl"></div>
    <div class="corner corner-br"></div>

    <main class="content">
        @if($smsLogo)
            <img src="{{ $smsLogo }}" class="sms-logo" alt="SMS">
        @endif

        <div class="company">Sustainable Management System Inc.</div>
        <div class="cert-title">Certificate of Completion</div>

        <div class="certify">This is to certify that</div>
        <div class="participant">{{ $enrollment->full_name }}</div>

        <div class="completed">
            has successfully completed all course requirements, including continuous assessments
            and the final written examination, and has demonstrated the competencies required
            for successful completion of the ASCB Certified
        </div>

        <div class="course-block">
            <div class="course-name">{{ $courseTitle }}</div>
        </div>

        <div class="description">
            based on the auditing principles and guidance of ISO 19011:2026.
        </div>

        <div class="details-wrap">
            <div class="details">
                <div class="detail-row">
                    <div class="detail-label">Course No.:</div>
                    <div class="detail-value">{{ $courseNo }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Certificate No.:</div>
                    <div class="detail-value">{{ $certificateNo }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Issue Date:</div>
                    <div class="detail-value">{{ $issueDate }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Training Dates:</div>
                    <div class="detail-value">{{ $trainingDates }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Duration:</div>
                    <div class="detail-value">{{ $durationDisplay }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">IRQAO Reg. ID:</div>
                    <div class="detail-value">{{ $irqaoRegId }}</div>
                </div>
            </div>

            <div class="verify">
                <img src="{{ $qrUrl }}" class="qr-img" alt="QR Code">
                <div class="verify-title">SCAN TO VERIFY</div>
                <div class="verify-url">www.smscert.com/verify</div>
                <div class="verify-id">Verification ID: {{ $verificationId }}</div>
            </div>
        </div>

        <div class="credential-footer">
            <div class="credential-cell credential-signature">
                @if($leadTrainerSignature)
                    <img src="{{ $leadTrainerSignature }}" class="signature-img" alt="">
                @else
                    <div class="signature-space"></div>
                @endif
                <div class="sig-line"></div>
                <div class="sig-name">{{ $leadTrainerName }}</div>
                <div class="sig-role">Lead Trainer</div>
            </div>

            <div class="credential-cell credential-ascb">
                @if($ascbLogo)
                    <img src="{{ $ascbLogo }}" class="accreditation-logo ascb-logo" alt="ASCB">
                @endif
            </div>

            <div class="credential-cell credential-irqao">
                @if($irqaoLogo)
                    <img src="{{ $irqaoLogo }}" class="accreditation-logo irqao-logo" alt="IRQAO">
                @endif
            </div>

            <div class="credential-cell credential-ceo">
                @if($ceoSignature)
                    <img src="{{ $ceoSignature }}" class="signature-img" alt="">
                @else
                    <div class="signature-space"></div>
                @endif
                <div class="sig-line"></div>
                <div class="sig-name">{{ $ceoName }}</div>
                <div class="sig-role">President &amp; CEO</div>
            </div>
        </div>
    </main>

    <footer class="footer">
        277 Cherry Street, Suite-12N, New York, New York 10002, USA<br>
        info@smscert.com &nbsp; | &nbsp; www.smscert.com
    </footer>
</div>
</body>
</html>
