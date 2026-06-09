<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Certificate of Completion</title>

<style>
@page { size: A4 portrait; margin: 0; }

html, body {
    margin: 0; padding: 0;
    width: 210mm; height: 297mm;
    font-family: DejaVu Sans, sans-serif;
    background: #fffef8;
    color: #111;
}

/* ── Outer wrapper ────────────────────────────────────── */
.cert-page {
    position: relative;
    width: 210mm;
    height: 297mm;
    background: #fffef8;
    overflow: hidden;
}

/* ── Watermark logo background ───────────────────────── */
.watermark {
    position: absolute;
    top: 50%;
    left: 50%;
    width: 160mm;
    height: 160mm;
    margin-top: -80mm;
    margin-left: -80mm;
    opacity: 0.04;
}
.watermark img { width: 100%; height: 100%; }

/* ── Decorative border lines ─────────────────────────── */
.border-outer {
    position: absolute;
    top: 6mm; left: 6mm; right: 6mm; bottom: 6mm;
    border: 2.5px solid #1a3a7c;
    box-sizing: border-box;
}
.border-inner {
    position: absolute;
    top: 8.5mm; left: 8.5mm; right: 8.5mm; bottom: 8.5mm;
    border: 0.8px solid #1a3a7c;
    box-sizing: border-box;
}

/* ── Corner ornaments ────────────────────────────────── */
.corner {
    position: absolute;
    width: 10mm; height: 10mm;
    border-color: #c8a84b;
    border-style: solid;
}
.c-tl { top: 11mm;  left: 11mm;  border-width: 2px 0 0 2px; }
.c-tr { top: 11mm;  right: 11mm; border-width: 2px 2px 0 0; }
.c-bl { bottom: 11mm; left: 11mm;  border-width: 0 0 2px 2px; }
.c-br { bottom: 11mm; right: 11mm; border-width: 0 2px 2px 0; }

/* ── Top gold rule ───────────────────────────────────── */
.gold-rule-top {
    position: absolute;
    top: 14mm; left: 16mm; right: 16mm;
    height: 1.2mm;
    background: linear-gradient(to right, transparent, #c8a84b 20%, #c8a84b 80%, transparent);
}
.gold-rule-bottom {
    position: absolute;
    bottom: 14mm; left: 16mm; right: 16mm;
    height: 1.2mm;
    background: linear-gradient(to right, transparent, #c8a84b 20%, #c8a84b 80%, transparent);
}

/* ── Header ──────────────────────────────────────────── */
.header {
    position: absolute;
    top: 16mm;
    left: 0; right: 0;
    text-align: center;
}
.logo-img { height: 24mm; }

.company-name {
    margin-top: 2.5mm;
    font-size: 15px;
    font-weight: 700;
    color: #1a3a7c;
    letter-spacing: 0.4px;
}

/* ── Certificate of Completion title ─────────────────── */
.cert-title {
    position: absolute;
    top: 58mm;
    left: 0; right: 0;
    text-align: center;
}
.cert-title-text {
    font-family: DejaVu Serif, serif;
    font-size: 30px;
    color: #1a3a7c;
    letter-spacing: 1.5px;
    font-style: italic;
}

/* ── Thin divider under title ────────────────────────── */
.title-divider {
    position: absolute;
    top: 71mm;
    left: 35mm; right: 35mm;
    height: 0.6mm;
    background: linear-gradient(to right, transparent, #c8a84b 30%, #c8a84b 70%, transparent);
}

/* ── Body text ───────────────────────────────────────── */
.body-area {
    position: absolute;
    top: 74mm;
    left: 0; right: 0;
    text-align: center;
}
.certify-text {
    font-size: 13px;
    color: #333;
    font-style: italic;
}
.participant-name {
    font-size: 26px;
    font-weight: 700;
    color: #0f1e45;
    margin: 3mm 0 2mm;
    letter-spacing: 0.8px;
}
.completed-text {
    font-size: 13px;
    color: #333;
    margin-bottom: 3mm;
    font-style: italic;
}
.course-name-line {
    font-size: 15px;
    color: #111;
    margin: 0 18mm;
    line-height: 1.45;
}
.course-std {
    font-size: 16px;
    font-weight: 700;
    color: #0f1e45;
    margin-top: 2mm;
}

/* ── Accreditation paragraph ─────────────────────────── */
.accreditation {
    position: absolute;
    top: 147mm;
    left: 18mm; right: 18mm;
    font-size: 10.5px;
    color: #444;
    line-height: 1.55;
    text-align: justify;
}

/* ── Details block + QR ──────────────────────────────── */
.details-area {
    position: absolute;
    top: 173mm;
    left: 18mm; right: 18mm;
}
.details-table {
    width: 68%;
    float: left;
    font-size: 11.5px;
    line-height: 1.85;
    color: #222;
}
.det-lbl { color: #555; width: 32mm; }
.det-colon { width: 4mm; }
.det-val { font-weight: 600; color: #111; }
.qr-box {
    float: right;
    width: 25mm;
    margin-top: 1mm;
}
.qr-box img {
    width: 25mm; height: 25mm;
    border: 1px solid #1a3a7c;
    padding: 1mm;
    background: #fff;
}
.clearfix:after { content:''; display:table; clear:both; }

/* ── Thin divider above footer ───────────────────────── */
.footer-divider {
    position: absolute;
    top: 215mm;
    left: 18mm; right: 18mm;
    height: 0.5mm;
    background: linear-gradient(to right, transparent, #c8a84b 20%, #c8a84b 80%, transparent);
}

/* ── Signatures / logos ──────────────────────────────── */
.sig-area {
    position: absolute;
    top: 218mm;
    left: 18mm; right: 18mm;
    height: 30mm;
}
.sig-left {
    float: left;
    width: 42mm;
    text-align: center;
}
.sig-center {
    float: left;
    width: calc(100% - 84mm);
    text-align: center;
}
.sig-right {
    float: right;
    width: 42mm;
    text-align: center;
}
.sig-img {
    height: 14mm;
    margin-bottom: 1mm;
}
.sig-line {
    border-top: 1.2px solid #333;
    width: 38mm;
    margin: 0 auto 1.5mm;
}
.sig-name {
    font-size: 10.5px;
    font-weight: 700;
    color: #111;
}
.sig-role {
    font-size: 9.5px;
    color: #555;
    margin-top: 0.5mm;
}
.seal-img {
    width: 26mm; height: 26mm;
    vertical-align: middle;
    margin-right: 2mm;
}
.third-party-logos {
    display: inline-block;
    vertical-align: middle;
}
.third-logo {
    height: 16mm;
    vertical-align: middle;
    margin: 0 2mm;
}

/* ── Footer bar ──────────────────────────────────────── */
.footer-bar {
    position: absolute;
    bottom: 9.5mm;
    left: 18mm; right: 18mm;
    text-align: center;
    font-size: 8.5px;
    color: #555;
    line-height: 1.55;
}
.footer-bar strong { color: #222; }
</style>
</head>
<body>

@php
    $schedule  = $enrollment->trainingSchedule ?? null;
    $course    = $schedule?->course ?? null;

    /* ── Dynamic dates ── */
    $startDate = $schedule?->start_date
        ? \Carbon\Carbon::parse($schedule->start_date)->format('d M Y') : 'N/A';
    $endDate   = $schedule?->end_date
        ? \Carbon\Carbon::parse($schedule->end_date)->format('d M Y') : 'N/A';
    $trainingDate = ($startDate === $endDate) ? $startDate : $startDate . ' – ' . $endDate;

    $issueDate = !empty($enrollment->certificate_issue_date)
        ? \Carbon\Carbon::parse($enrollment->certificate_issue_date)->format('d M Y') : 'N/A';

    /* ── Course name & ISO standard detection ── */
    $fullCourseName = $course?->name ?? 'Training Programme';
    // Try to split "Auditor/Lead Auditor Training Course to ISO 14001:2016 (EMS)"
    // into base name and ISO tag like "(ISO 14001:2016 (EMS))"
    $courseBase = $fullCourseName;
    $isoTag     = '';
    if (preg_match('/^(.*?)\s*(\(ISO\s[\d:]+(?:\s*\([^)]+\))?\))\s*$/i', $fullCourseName, $m)) {
        $courseBase = trim($m[1]);
        $isoTag     = $m[2];
    } elseif (preg_match('/^(.*?)\s*(ISO\s[\d:]+(?::\d+)?(?:\s*\([^)]+\))?)\s*$/i', $fullCourseName, $m)) {
        $courseBase = trim($m[1]);
        $isoTag     = '(' . $m[2] . ')';
    }

    /* ── Accreditation scheme name based on ISO tag ── */
    $schemeName = 'Management System Auditor Certification Scheme';
    if (stripos($fullCourseName, '45001') !== false) {
        $schemeName = 'OHSMS Auditor Certification Scheme';
    } elseif (stripos($fullCourseName, '14001') !== false) {
        $schemeName = 'EMS Auditor Certification Scheme';
    } elseif (stripos($fullCourseName, '9001') !== false) {
        $schemeName = 'QMS Auditor Certification Scheme';
    } elseif (stripos($fullCourseName, '50001') !== false) {
        $schemeName = 'EnMS Auditor Certification Scheme';
    } elseif (stripos($fullCourseName, '27001') !== false) {
        $schemeName = 'ISMS Auditor Certification Scheme';
    }

    /* ── Course number — use batch_code if available ── */
    $courseNo = $schedule?->batch_code ?: ('SMS/' . date('y', strtotime($schedule?->start_date ?? 'now')) . '/' . str_pad($enrollment->id, 3, '0', STR_PAD_LEFT));

    /* ── QR code URL ── */
    $verifyUrl = url('/verify-certificate/' . $enrollment->certificate_number);
    $qrUrl     = 'https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=' . urlencode($verifyUrl);

    /* ── Embedded assets ── */
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
@endphp

<div class="cert-page">

    {{-- Watermark --}}
    @if($logo)
    <div class="watermark"><img src="{{ $logo }}"></div>
    @endif

    {{-- Borders & corners --}}
    <div class="border-outer"></div>
    <div class="border-inner"></div>
    <div class="corner c-tl"></div>
    <div class="corner c-tr"></div>
    <div class="corner c-bl"></div>
    <div class="corner c-br"></div>
    <div class="gold-rule-top"></div>
    <div class="gold-rule-bottom"></div>

    {{-- Header: logo + company name --}}
    <div class="header">
        @if($logo)
        <div><img src="{{ $logo }}" class="logo-img"></div>
        @endif
        <div class="company-name">Sustainable Management System Inc.</div>
    </div>

    {{-- Title --}}
    <div class="cert-title">
        <span class="cert-title-text">Certificate of Completion</span>
    </div>
    <div class="title-divider"></div>

    {{-- Body --}}
    <div class="body-area">
        <div class="certify-text">This is to certify that</div>

        <div class="participant-name">{{ $enrollment->full_name }}</div>

        <div class="completed-text">has successfully completed the</div>

        <div class="course-name-line">{{ $courseBase }}</div>
        @if($isoTag)
        <div class="course-std">{{ $isoTag }}</div>
        @endif
    </div>

    {{-- Accreditation paragraph --}}
    <div class="accreditation">
        This course is certified and accredited by ASCB(E) Certified Auditors and satisfies
        part of the formal training requirements for individuals seeking certification
        under the IRQAO {{ $schemeName }}.
    </div>

    {{-- Details + QR --}}
    <div class="details-area">
        <div class="clearfix">
            <div class="details-table">
                <table cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="det-lbl">Course No</td>
                        <td class="det-colon">&nbsp;:&nbsp;</td>
                        <td class="det-val">{{ $courseNo }}</td>
                    </tr>
                    <tr>
                        <td class="det-lbl">Course Date</td>
                        <td class="det-colon">&nbsp;:&nbsp;</td>
                        <td class="det-val">{{ $trainingDate }}</td>
                    </tr>
                    <tr>
                        <td class="det-lbl">Certificate No</td>
                        <td class="det-colon">&nbsp;:&nbsp;</td>
                        <td class="det-val">{{ $enrollment->certificate_number }}</td>
                    </tr>
                    <tr>
                        <td class="det-lbl">Issue Date</td>
                        <td class="det-colon">&nbsp;:&nbsp;</td>
                        <td class="det-val">{{ $issueDate }}</td>
                    </tr>
                    @if(!empty($enrollment->irqao_reg_id))
                    <tr>
                        <td class="det-lbl">IRQAO Reg. ID</td>
                        <td class="det-colon">&nbsp;:&nbsp;</td>
                        <td class="det-val">{{ $enrollment->irqao_reg_id }}</td>
                    </tr>
                    @endif
                </table>
            </div>
            <div class="qr-box">
                <img src="{{ $qrUrl }}">
            </div>
        </div>
    </div>

    {{-- Divider --}}
    <div class="footer-divider"></div>

    {{-- Signatures & logos --}}
    <div class="sig-area">
        <div class="clearfix">

            {{-- Left: President & CEO --}}
            <div class="sig-left">
                @if($signature)
                <div><img src="{{ $signature }}" class="sig-img"></div>
                @else
                <div style="height:14mm;"></div>
                @endif
                <div class="sig-line"></div>
                <div class="sig-name">President &amp; CEO</div>
                <div class="sig-role">Sustainable Management System Inc.</div>
            </div>

            {{-- Centre: Seal + third-party logos --}}
            <div class="sig-center" style="padding-top:2mm;">
                @if($seal)
                <img src="{{ $seal }}" class="seal-img">
                @endif
                <div class="third-party-logos">
                    @if($irqaoLogo)
                    <img src="{{ $irqaoLogo }}" class="third-logo">
                    @endif
                    @if($ascbLogo)
                    <img src="{{ $ascbLogo }}" class="third-logo">
                    @endif
                </div>
            </div>

            {{-- Right: Certification Approved By --}}
            <div class="sig-right">
                <div style="height:7mm;font-size:9.5px;color:#555;text-align:center;line-height:1.3;">
                    Certification<br>Approved By
                </div>
                <div style="height:7mm;"></div>
                <div class="sig-line"></div>
                <div class="sig-name">Training Director</div>
                <div class="sig-role">SMS Training Services</div>
            </div>

        </div>
    </div>

    {{-- Footer --}}
    <div class="footer-bar">
        <strong>This certificate is the property of Sustainable Management System Inc.</strong><br>
        3706 Suite#6B I 69 Street I Woodside I New York 11377 I USA<br>
        The validity of this certificate can be checked at
        '<strong>www.irqao.com</strong>' &amp; '<strong>www.smscert.com/certificate-check</strong>'.
    </div>

</div>
</body>
</html>
