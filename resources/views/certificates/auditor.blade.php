<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Auditor / Lead Auditor Training Certificate</title>
<style>
@page { size: A4 landscape; margin: 0; }
html, body {
    margin: 0; padding: 0;
    width: 297mm; height: 210mm;
    font-family: DejaVu Sans, sans-serif;
    background: #fff;
    color: #111;
    font-size: 10px;
}

/* ═══════════════════════════════════════════════════════
   PAGE WRAPPER
═══════════════════════════════════════════════════════ */
.page {
    position: relative;
    width: 297mm;
    height: 210mm;
    background: #f8faff;
    overflow: hidden;
}

/* ═══════════════════════════════════════════════════════
   WATERMARK — faint SMS logo in page centre
═══════════════════════════════════════════════════════ */
.wm {
    position: absolute;
    top: 50%; left: 50%;
    width: 120mm; height: 120mm;
    margin-top: -60mm; margin-left: -60mm;
    opacity: 0.04;
    z-index: 0;
}
.wm img { width: 100%; height: 100%; }

/* ═══════════════════════════════════════════════════════
   MICRO-TEXT SECURITY WATERMARK (behind body)
═══════════════════════════════════════════════════════ */
.micro-wm {
    position: absolute;
    top: 44mm; left: 0; right: 0; bottom: 26mm;
    opacity: 0.04;
    z-index: 0;
    font-size: 5.5px;
    color: #1a3a8a;
    word-spacing: 6mm;
    line-height: 6mm;
    overflow: hidden;
}

/* ═══════════════════════════════════════════════════════
   OUTER THIN BORDER (gold)
═══════════════════════════════════════════════════════ */
.border-gold {
    position: absolute;
    top: 3mm; left: 3mm; right: 3mm; bottom: 3mm;
    border: 1px solid #c9a227;
    z-index: 1;
}
/* Inner navy line */
.border-navy {
    position: absolute;
    top: 4.5mm; left: 4.5mm; right: 4.5mm; bottom: 4.5mm;
    border: 2.5px solid #0f2055;
    z-index: 1;
}
/* Gold corner accents */
.c { position: absolute; width: 8mm; height: 8mm;
     border-color: #c9a227; border-style: solid; z-index: 2; }
.c-tl { top: 6mm;  left: 6mm;  border-width: 2px 0 0 2px; }
.c-tr { top: 6mm;  right: 6mm; border-width: 2px 2px 0 0; }
.c-bl { bottom: 6mm; left: 6mm;  border-width: 0 0 2px 2px; }
.c-br { bottom: 6mm; right: 6mm; border-width: 0 2px 2px 0; }

/* ═══════════════════════════════════════════════════════
   HEADER BAND  (0 → 43mm)
═══════════════════════════════════════════════════════ */
.hdr {
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 43mm;
    background: #0f2055;
    z-index: 2;
}
/* Subtle diagonal stripe overlay on header */
.hdr-pattern {
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    opacity: 0.07;
    background-image: repeating-linear-gradient(
        45deg,
        #ffffff 0px, #ffffff 1px,
        transparent 1px, transparent 12px
    );
}

/* Logo box — left of header */
.hdr-logo {
    position: absolute;
    top: 0; left: 0;
    width: 68mm; height: 43mm;
    text-align: center;
    border-right: 1px solid rgba(201,162,39,0.4);
    z-index: 3;
}
.hdr-logo-inner {
    position: absolute;
    top: 50%; left: 50%;
    width: 52mm;
    margin-top: -12mm;
    margin-left: -26mm;
    text-align: center;
}
.hdr-logo-inner img { height: 20mm; }
.hdr-org-mini {
    font-size: 7.5px;
    color: rgba(255,255,255,0.75);
    font-weight: 700;
    letter-spacing: 0.5px;
    margin-top: 2mm;
}

/* Text block — right of logo */
.hdr-text {
    position: absolute;
    top: 0; left: 68mm; right: 8mm;
    height: 43mm;
    z-index: 3;
    text-align: center;
}
.hdr-text-inner {
    position: absolute;
    top: 50%; left: 0; right: 0;
    margin-top: -14mm;
}
.hdr-company {
    font-size: 17px;
    font-weight: 700;
    color: #ffffff;
    letter-spacing: 0.6px;
    margin-bottom: 1mm;
}
.hdr-location {
    font-size: 9px;
    color: rgba(255,255,255,0.7);
    letter-spacing: 1.5px;
    text-transform: uppercase;
    margin-bottom: 3mm;
}
/* Gold divider in header */
.hdr-rule {
    height: 1px;
    background: linear-gradient(to right, transparent, #c9a227, #f0d060, #c9a227, transparent);
    margin: 0 10mm 3mm 10mm;
}
.hdr-title {
    font-size: 18px;
    font-weight: 700;
    color: #f0d060;
    letter-spacing: 1.5px;
    line-height: 1.3;
    text-transform: uppercase;
}

/* ═══════════════════════════════════════════════════════
   GOLD RULE — below header (43mm)
═══════════════════════════════════════════════════════ */
.rule-top {
    position: absolute;
    top: 43mm; left: 0; right: 0;
    height: 2.5mm;
    background: linear-gradient(to right, #0f2055 0%, #c9a227 15%, #f0d060 50%, #c9a227 85%, #0f2055 100%);
    z-index: 2;
}

/* ═══════════════════════════════════════════════════════
   CONTENT AREA  (45.5mm → 161mm)
═══════════════════════════════════════════════════════ */
/* LEFT BODY  */
.body-left {
    position: absolute;
    top: 46mm; left: 8mm;
    width: 185mm;
    z-index: 2;
}
.certify-line {
    font-size: 10.5px;
    color: #334155;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    margin-bottom: 1.5mm;
}
.participant-name {
    font-size: 30px;
    font-weight: 700;
    color: #0f2055;
    letter-spacing: 0.5px;
    margin-bottom: 2mm;
    line-height: 1.15;
}
.name-underline {
    height: 0.8mm;
    width: 120mm;
    background: linear-gradient(to right, #c9a227, transparent);
    margin-bottom: 2.5mm;
}
.completed-line {
    font-size: 10px;
    color: #475569;
    font-style: italic;
    margin-bottom: 1.5mm;
}
.course-name {
    font-size: 13.5px;
    font-weight: 700;
    color: #0f2055;
    line-height: 1.35;
    margin-bottom: 1mm;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}
.course-iso {
    font-size: 11px;
    font-weight: 600;
    color: #1a4fa8;
    margin-bottom: 3mm;
}
.competency-text {
    font-size: 9px;
    color: #374151;
    line-height: 1.55;
    margin-bottom: 2mm;
    text-align: justify;
}
.accred-text {
    font-size: 8.8px;
    color: #4b5563;
    line-height: 1.5;
    text-align: justify;
    border-left: 2px solid #c9a227;
    padding-left: 3mm;
    margin-top: 2mm;
}

/* RIGHT DETAILS BOX */
.details-box {
    position: absolute;
    top: 46mm; left: 196mm; right: 7mm;
    z-index: 2;
    background: #eff4ff;
    border: 1px solid #c9d8f5;
    border-top: 3px solid #0f2055;
    border-radius: 2px;
    padding: 3.5mm 4mm;
}
.det-box-title {
    font-size: 9px;
    font-weight: 700;
    color: #0f2055;
    text-transform: uppercase;
    letter-spacing: 1px;
    border-bottom: 1px solid #c9d8f5;
    padding-bottom: 1.5mm;
    margin-bottom: 2mm;
}
.det-row {
    margin-bottom: 2.2mm;
}
.det-label {
    font-size: 7.5px;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    line-height: 1;
    margin-bottom: 0.5mm;
}
.det-value {
    font-size: 9px;
    font-weight: 700;
    color: #0f2055;
    line-height: 1.2;
}
.det-divider {
    height: 0.4mm;
    background: #d1ddf5;
    margin: 1.5mm 0;
}

/* QR block inside details box */
.qr-block {
    text-align: center;
    margin-top: 2.5mm;
    padding-top: 2mm;
    border-top: 1px solid #c9d8f5;
}
.qr-block img {
    width: 22mm; height: 22mm;
    border: 1px solid #0f2055;
    padding: 0.8mm;
    background: #fff;
}
.qr-label {
    font-size: 6.5px;
    font-weight: 700;
    color: #0f2055;
    letter-spacing: 1px;
    text-transform: uppercase;
    margin-top: 1mm;
}
.cert-id-micro {
    font-size: 6px;
    color: #94a3b8;
    margin-top: 0.5mm;
    word-break: break-all;
}

/* ═══════════════════════════════════════════════════════
   GOLD RULE — above bottom bar (161mm)
═══════════════════════════════════════════════════════ */
.rule-bottom {
    position: absolute;
    top: 161mm; left: 0; right: 0;
    height: 2mm;
    background: linear-gradient(to right, #0f2055 0%, #c9a227 15%, #f0d060 50%, #c9a227 85%, #0f2055 100%);
    z-index: 2;
}

/* ═══════════════════════════════════════════════════════
   BOTTOM BAR  (163mm → 183mm)
═══════════════════════════════════════════════════════ */
.bottom-bar {
    position: absolute;
    top: 163mm; left: 8mm; right: 8mm;
    height: 20mm;
    z-index: 2;
}

/* Signature block — left */
.sig-block {
    position: absolute;
    left: 0;
    width: 72mm;
    text-align: center;
}
.sig-img {
    height: 12mm;
    margin-bottom: 0.5mm;
}
.sig-line {
    border-top: 1.2px solid #0f2055;
    width: 55mm;
    margin: 0 auto 1mm;
}
.sig-name {
    font-size: 10px;
    font-weight: 700;
    color: #0f2055;
    letter-spacing: 0.3px;
}
.sig-title {
    font-size: 8px;
    color: #475569;
    margin-top: 0.5mm;
    line-height: 1.4;
}

/* Centre: seal + accreditation logos */
.centre-block {
    position: absolute;
    left: 74mm; right: 74mm;
    text-align: center;
    top: 0;
}
.seal-img {
    height: 20mm;
    vertical-align: middle;
}
.logo-row {
    text-align: center;
    margin-top: 1mm;
}
.third-logo {
    height: 9mm;
    vertical-align: middle;
    margin: 0 2mm;
}

/* Right: approved by block */
.approved-block {
    position: absolute;
    right: 0;
    width: 72mm;
    text-align: center;
    top: 2mm;
}
.approved-label {
    font-size: 7.5px;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    margin-bottom: 4mm;
}
.approved-line {
    border-top: 1.2px solid #0f2055;
    width: 55mm;
    margin: 0 auto 1mm;
}
.approved-name {
    font-size: 9px;
    font-weight: 700;
    color: #0f2055;
}
.approved-role {
    font-size: 7.5px;
    color: #475569;
    margin-top: 0.5mm;
}

/* ═══════════════════════════════════════════════════════
   FOOTER BAND  (183mm → 210mm, navy)
═══════════════════════════════════════════════════════ */
.footer {
    position: absolute;
    top: 183mm; left: 0; right: 0; bottom: 0;
    background: #0a1628;
    z-index: 2;
    padding: 3mm 12mm 0 12mm;
}
.footer-property {
    font-size: 7px;
    color: #94a3b8;
    text-align: center;
    margin-bottom: 1.5mm;
    letter-spacing: 0.3px;
}
.footer-cols {
    width: 100%;
}
.footer-col {
    vertical-align: top;
    text-align: center;
    width: 33%;
    padding: 0 3mm;
}
.footer-col-head {
    font-size: 7px;
    font-weight: 700;
    color: #c9a227;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    margin-bottom: 1mm;
    border-bottom: 0.5px solid rgba(201,162,39,0.3);
    padding-bottom: 0.8mm;
}
.footer-col-body {
    font-size: 7px;
    color: #94a3b8;
    line-height: 1.5;
}
.footer-col-body a, .footer-col-body span { color: #7dd3fc; }
</style>
</head>
<body>

@php
    $schedule  = $enrollment->trainingSchedule ?? null;
    $course    = $schedule?->course ?? null;

    /* Dates */
    $startDate = $schedule?->start_date
        ? \Carbon\Carbon::parse($schedule->start_date)->format('d M Y') : 'N/A';
    $endDate = $schedule?->end_date
        ? \Carbon\Carbon::parse($schedule->end_date)->format('d M Y') : 'N/A';
    $trainingDate = ($startDate === $endDate) ? $startDate : $startDate . ' – ' . $endDate;
    $issueDate = !empty($enrollment->certificate_issue_date)
        ? \Carbon\Carbon::parse($enrollment->certificate_issue_date)->format('d M Y') : 'N/A';

    /* Duration from schedule */
    $duration = $schedule?->duration ?? '40 Hours';

    /* Course name splitting */
    $fullCourseName = $course?->name ?? 'Professional Training Programme';
    $courseBase = $fullCourseName;
    $isoTag = '';
    if (preg_match('/^(.*?)\s*(\(ISO\s[\d:]+(?:\s*\([^)]+\))?\))\s*$/i', $fullCourseName, $m)) {
        $courseBase = trim($m[1]);
        $isoTag = $m[2];
    } elseif (preg_match('/^(.*?)\s+to\s+(ISO\s[\d:]+(?::\d+)?(?:\s*\([^)]+\))?)\s*$/i', $fullCourseName, $m)) {
        $courseBase = trim($m[1]);
        $isoTag = $m[2];
    }

    /* Determine ISO standard for competency text */
    $isoDisplay = $isoTag ?: ($fullCourseName);
    $systemType = 'management system';
    $systemLong = 'management system';
    if (stripos($fullCourseName, '45001') !== false) {
        $systemType = 'OHSMS'; $systemLong = 'Occupational Health and Safety Management System';
        $schemeName = 'IRQAO OHSMS Auditor Certification Scheme';
    } elseif (stripos($fullCourseName, '14001') !== false) {
        $systemType = 'EMS'; $systemLong = 'Environmental Management System';
        $schemeName = 'IRQAO EMS Auditor Certification Scheme';
    } elseif (stripos($fullCourseName, '9001') !== false) {
        $systemType = 'QMS'; $systemLong = 'Quality Management System';
        $schemeName = 'IRQAO QMS Auditor Certification Scheme';
    } elseif (stripos($fullCourseName, '50001') !== false) {
        $systemType = 'EnMS'; $systemLong = 'Energy Management System';
        $schemeName = 'IRQAO EnMS Auditor Certification Scheme';
    } elseif (stripos($fullCourseName, '27001') !== false) {
        $systemType = 'ISMS'; $systemLong = 'Information Security Management System';
        $schemeName = 'IRQAO ISMS Auditor Certification Scheme';
    } elseif (stripos($fullCourseName, '46001') !== false) {
        $systemType = 'WEMS'; $systemLong = 'Water Efficiency Management System';
        $schemeName = 'IRQAO Auditor Certification Scheme';
    } else {
        $schemeName = 'IRQAO Auditor Certification Scheme';
    }

    /* Course number */
    $courseNo = $schedule?->batch_code
        ?: ('SMS/' . date('y', strtotime($schedule?->start_date ?? 'now')) . '/' . str_pad($enrollment->training_schedule_id ?? $enrollment->id, 3, '0', STR_PAD_LEFT));

    /* QR / Verify */
    $verifyUrl = url('/verify-certificate/' . $enrollment->certificate_number);
    $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=280x280&data=' . urlencode($verifyUrl);

    /* Assets */
    $logo = file_exists(public_path('sms-logo.png'))
        ? 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('sms-logo.png'))) : null;
    $signature = file_exists(public_path('ceo-signature.png'))
        ? 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('ceo-signature.png'))) : null;
    $seal = file_exists(public_path('sms-seal.png'))
        ? 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('sms-seal.png'))) : null;
    $irqaoLogo = file_exists(public_path('irqao-logo.png'))
        ? 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('irqao-logo.png'))) : null;
    $ascbLogo = file_exists(public_path('ascb-logo.png'))
        ? 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('ascb-logo.png'))) : null;

    /* Micro-text security string */
    $microStr = str_repeat('SUSTAINABLE MANAGEMENT SYSTEM INC. · SMS TRAINING CERTIFICATE · ' . ($enrollment->certificate_number ?? '') . ' · ', 60);
@endphp

<div class="page">

    {{-- Watermark --}}
    @if($logo)
    <div class="wm"><img src="{{ $logo }}"></div>
    @endif

    {{-- Micro-text security watermark --}}
    <div class="micro-wm">{{ $microStr }}</div>

    {{-- Borders & corners --}}
    <div class="border-gold"></div>
    <div class="border-navy"></div>
    <div class="c c-tl"></div>
    <div class="c c-tr"></div>
    <div class="c c-bl"></div>
    <div class="c c-br"></div>

    {{-- ══ HEADER BAND ══════════════════════════════════════════ --}}
    <div class="hdr">
        <div class="hdr-pattern"></div>

        {{-- Logo column --}}
        <div class="hdr-logo">
            <div class="hdr-logo-inner">
                @if($logo)
                <img src="{{ $logo }}">
                @else
                <div style="color:#fff;font-size:22px;font-weight:900;">SMS</div>
                @endif
                <div class="hdr-org-mini">SUSTAINABLE MANAGEMENT SYSTEM INC.</div>
            </div>
        </div>

        {{-- Title column --}}
        <div class="hdr-text">
            <div class="hdr-text-inner">
                <div class="hdr-company">SUSTAINABLE MANAGEMENT SYSTEM INC.</div>
                <div class="hdr-location">New York, United States of America</div>
                <div class="hdr-rule"></div>
                <div class="hdr-title">Auditor / Lead Auditor<br>Training Certificate</div>
            </div>
        </div>
    </div>

    {{-- Top gold rule --}}
    <div class="rule-top"></div>

    {{-- ══ BODY LEFT ════════════════════════════════════════════ --}}
    <div class="body-left">

        <div class="certify-line">This Is To Certify That</div>

        <div class="participant-name">{{ $enrollment->full_name }}</div>
        <div class="name-underline"></div>

        <div class="completed-line">has successfully completed the</div>

        <div class="course-name">{{ $courseBase }}</div>

        @if($isoTag)
        <div class="course-iso">Based on &nbsp;<strong>{{ $isoTag }}</strong></div>
        @endif

        <div class="competency-text">
            and has demonstrated the knowledge, skills, and competencies required to plan, conduct, report, and
            follow up management system audits in accordance with <strong>{{ $isoDisplay }}</strong> and
            internationally accepted auditing principles. The participant has successfully fulfilled all training
            requirements and achieved the learning objectives necessary to perform first-party, second-party,
            and third-party audits of a <strong>{{ $systemLong }}</strong>.
        </div>

        <div class="accred-text">
            This training course is certified and accredited by <strong>ASCB(E) Certified Auditors</strong>
            and satisfies part of the formal training requirements for individuals seeking certification under
            the <strong>{{ $schemeName }}</strong>.
        </div>

    </div>

    {{-- ══ DETAILS BOX (RIGHT) ══════════════════════════════════ --}}
    <div class="details-box">
        <div class="det-box-title">Training Details</div>

        <div class="det-row">
            <div class="det-label">Course Number</div>
            <div class="det-value">{{ $courseNo }}</div>
        </div>
        <div class="det-divider"></div>

        @if($isoTag)
        <div class="det-row">
            <div class="det-label">Training Standard</div>
            <div class="det-value">{{ $isoTag }}</div>
        </div>
        <div class="det-divider"></div>
        @endif

        <div class="det-row">
            <div class="det-label">Training Duration</div>
            <div class="det-value">{{ $duration }}</div>
        </div>
        <div class="det-divider"></div>

        <div class="det-row">
            <div class="det-label">Course Dates</div>
            <div class="det-value">{{ $trainingDate }}</div>
        </div>
        <div class="det-divider"></div>

        <div class="det-row">
            <div class="det-label">Certificate Number</div>
            <div class="det-value">{{ $enrollment->certificate_number }}</div>
        </div>
        <div class="det-divider"></div>

        <div class="det-row">
            <div class="det-label">Issue Date</div>
            <div class="det-value">{{ $issueDate }}</div>
        </div>

        @if(!empty($enrollment->irqao_reg_id))
        <div class="det-divider"></div>
        <div class="det-row">
            <div class="det-label">IRQAO Registration ID</div>
            <div class="det-value">{{ $enrollment->irqao_reg_id }}</div>
        </div>
        @endif

        {{-- QR Code --}}
        <div class="qr-block">
            <img src="{{ $qrUrl }}">
            <div class="qr-label">Scan to Verify</div>
            <div class="cert-id-micro">ID: {{ $enrollment->certificate_number }}</div>
        </div>
    </div>

    {{-- Bottom gold rule --}}
    <div class="rule-bottom"></div>

    {{-- ══ BOTTOM BAR ═══════════════════════════════════════════ --}}
    <div class="bottom-bar">

        {{-- Left: CEO signature --}}
        <div class="sig-block">
            @if($signature)
            <div><img src="{{ $signature }}" class="sig-img"></div>
            @else
            <div style="height:12mm;"></div>
            @endif
            <div class="sig-line"></div>
            <div class="sig-name">Abdul Alim</div>
            <div class="sig-title">President &amp; Chief Executive Officer<br>Sustainable Management System Inc.</div>
        </div>

        {{-- Centre: Seal + third-party logos --}}
        <div class="centre-block">
            @if($seal)
            <img src="{{ $seal }}" class="seal-img">
            @endif
            <div class="logo-row">
                @if($irqaoLogo)
                <img src="{{ $irqaoLogo }}" class="third-logo">
                @endif
                @if($ascbLogo)
                <img src="{{ $ascbLogo }}" class="third-logo">
                @endif
                @if(!$irqaoLogo && !$ascbLogo)
                <div style="font-size:7px;color:#64748b;margin-top:2mm;">
                    <strong style="color:#1a3a8a;">IRQAO</strong> &nbsp;|&nbsp; <strong style="color:#1a3a8a;">ASCB(E)</strong>
                    &nbsp; Accredited
                </div>
                @endif
            </div>
        </div>

        {{-- Right: Certification Approved By --}}
        <div class="approved-block">
            <div class="approved-label">Certification Approved By</div>
            <div class="approved-line"></div>
            <div class="approved-name">Training Director</div>
            <div class="approved-role">SMS Training Services<br>Sustainable Management System Inc.</div>
        </div>

    </div>

    {{-- ══ FOOTER BAND ══════════════════════════════════════════ --}}
    <div class="footer">
        <div class="footer-property">
            This certificate is the property of Sustainable Management System Inc. and is subject to verification. &nbsp;|&nbsp;
            Verification: <span>www.smscert.com/verify</span> &nbsp;|&nbsp;
            Email: <span>info@smscert.com</span>
        </div>
        <table class="footer-cols" cellpadding="0" cellspacing="0">
            <tr>
                <td class="footer-col">
                    <div class="footer-col-head">Head Quarter</div>
                    <div class="footer-col-body">
                        Sustainable Management System Inc.<br>
                        277 Cherry Street, Suite 12N<br>
                        New York, NY 10002, United States of America
                    </div>
                </td>
                <td class="footer-col">
                    <div class="footer-col-head">Regional Head Quarter</div>
                    <div class="footer-col-body">
                        Sustainable Management System Bangladesh<br>
                        House 34 (Level 5), Road 2, Dhanmondi<br>
                        Dhaka 1205, Bangladesh
                    </div>
                </td>
                <td class="footer-col">
                    <div class="footer-col-head">Certificate Verification</div>
                    <div class="footer-col-body">
                        <span>www.smscert.com/verify</span><br>
                        <span>www.irqao.com</span><br>
                        <span>info@smscert.com</span> &nbsp;|&nbsp; <span>www.smscert.com</span>
                    </div>
                </td>
            </tr>
        </table>
    </div>

</div>
</body>
</html>
