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
    background: #f4f7fc;
    color: #0a1628;
}

.page {
    position: relative;
    width: 297mm;
    height: 210mm;
    background: #f4f7fc;
    overflow: hidden;
}

/* ── Borders & corners ──────────────────────────────── */
.bdr-gold {
    position: absolute;
    top: 2.5mm; left: 2.5mm; right: 2.5mm; bottom: 2.5mm;
    border: 1px solid #c9a227; z-index: 3;
}
.bdr-navy {
    position: absolute;
    top: 4mm; left: 4mm; right: 4mm; bottom: 4mm;
    border: 2px solid #0f2055; z-index: 3;
}
.co { position: absolute; width: 7mm; height: 7mm; border-color: #c9a227; border-style: solid; z-index: 4; }
.co-tl { top: 5.5mm;    left: 5.5mm;   border-width: 2px 0 0 2px; }
.co-tr { top: 5.5mm;    right: 5.5mm;  border-width: 2px 2px 0 0; }
.co-bl { bottom: 5.5mm; left: 5.5mm;   border-width: 0 0 2px 2px; }
.co-br { bottom: 5.5mm; right: 5.5mm;  border-width: 0 2px 2px 0; }

/* ── Watermark ──────────────────────────────────────── */
.wm {
    position: absolute;
    top: 50%; left: 50%;
    width: 130mm; height: 130mm;
    margin-top: -65mm; margin-left: -65mm;
    opacity: 0.045; z-index: 1;
}
.wm img { width: 100%; height: 100%; }

/* ── Micro-text security ────────────────────────────── */
.micro {
    position: absolute;
    top: 44mm; left: 8mm; right: 104mm;
    font-size: 5px; color: #1a3a8a; opacity: 0.06;
    line-height: 5mm; word-break: break-all; z-index: 1;
    overflow: hidden; height: 112mm;
}

/* ══ HEADER  (0 → 42mm) ════════════════════════════ */
.hdr {
    position: absolute;
    top: 0; left: 0; right: 0; height: 42mm;
    background: #0f2055; z-index: 2;
}
.hdr-stripe {
    position: absolute; top: 0; left: 0; right: 0; bottom: 0; opacity: 0.04;
    background-image: repeating-linear-gradient(45deg, #fff 0, #fff 1px, transparent 1px, transparent 11px);
}
/* Logo column */
.hdr-logo-panel {
    position: absolute;
    top: 0; left: 0; width: 68mm; height: 42mm;
    border-right: 1px solid rgba(201,162,39,0.35);
    text-align: center; z-index: 3;
}
.hdr-logo-img {
    position: absolute;
    top: 50%; left: 50%;
    width: 52mm; margin-top: -10mm; margin-left: -26mm;
    text-align: center;
}
.hdr-logo-img img { height: 20mm; }
/* Title column */
.hdr-title-panel {
    position: absolute;
    top: 0; left: 68mm; right: 0; height: 42mm;
    text-align: center; z-index: 3;
}
.hdr-title-inner {
    position: absolute;
    top: 50%; left: 8mm; right: 8mm; margin-top: -14mm;
}
.hdr-company {
    font-size: 16px; font-weight: 700;
    color: #ffffff; letter-spacing: 0.8px; margin-bottom: 1mm;
}
.hdr-location {
    font-size: 8px; color: rgba(255,255,255,0.65);
    letter-spacing: 2px; text-transform: uppercase; margin-bottom: 2.5mm;
}
.hdr-gold-rule {
    height: 1px; margin: 0 12mm 2.5mm;
    background: linear-gradient(to right, transparent, #c9a227, #f0d060, #c9a227, transparent);
}
.hdr-cert-title {
    font-size: 17px; font-weight: 700;
    color: #f0d060; letter-spacing: 2px;
    text-transform: uppercase; line-height: 1.4;
}

/* ══ GOLD RULES ════════════════════════════════════ */
.rule { position: absolute; left: 0; right: 0; height: 2.5mm; z-index: 2; }
.rule-top    { top: 42mm; }
.rule-bottom { top: 159mm; }
.rule-fill {
    height: 100%;
    background: linear-gradient(to right,
        #0f2055 0%, #1a4fa8 12%, #c9a227 28%,
        #f0d060 50%, #c9a227 72%, #1a4fa8 88%, #0f2055 100%);
}

/* ══ BODY LEFT  (44.5 → 159mm) ════════════════════ */
.body-left {
    position: absolute;
    top: 46mm; left: 8mm; width: 184mm;
    text-align: center; z-index: 2;
}
.certify-line {
    font-size: 9.5px; color: #4b5563;
    letter-spacing: 2.5px; text-transform: uppercase; margin-bottom: 2mm;
}
.pname {
    font-size: 31px; font-weight: 700;
    color: #0f2055; letter-spacing: 0.5px;
    line-height: 1.1; margin-bottom: 1.5mm;
}
.pname-rule {
    height: 0.8mm; width: 90mm; margin: 0 auto 3mm;
    background: linear-gradient(to right, transparent, #c9a227, transparent);
}
.completed-line {
    font-size: 9.5px; color: #4b5563;
    font-style: italic; margin-bottom: 2.5mm;
}
.cname {
    font-size: 13px; font-weight: 700;
    color: #0f2055; text-transform: uppercase;
    letter-spacing: 0.5px; line-height: 1.4; margin-bottom: 1.5mm;
}
.iso-tag {
    font-size: 11px; font-weight: 600;
    color: #1a5fa8; margin-bottom: 4mm;
}
.para-wrap { text-align: left; }
.para {
    font-size: 8.8px; color: #374151;
    line-height: 1.6; margin-bottom: 2mm; text-align: justify;
}
.accred {
    font-size: 8.5px; color: #4b5563;
    line-height: 1.55; text-align: justify;
    border-left: 2px solid #c9a227; padding-left: 3mm;
}

/* ══ DETAILS BOX  (right) ══════════════════════════ */
.det-box {
    position: absolute;
    top: 46mm; left: 196mm; right: 7mm;
    background: #eef3fd;
    border-top: 3px solid #0f2055;
    border-left: 1px solid #c3d0ee;
    border-right: 1px solid #c3d0ee;
    border-bottom: 1px solid #c3d0ee;
    border-radius: 0 0 3px 3px;
    padding: 3.5mm 4mm 3mm; z-index: 2;
}
.det-head {
    font-size: 8px; font-weight: 700; color: #0f2055;
    text-transform: uppercase; letter-spacing: 1.2px;
    border-bottom: 1px solid #c3d0ee;
    padding-bottom: 1.5mm; margin-bottom: 2mm; text-align: center;
}
.det-row { margin-bottom: 2mm; }
.det-lbl {
    font-size: 7px; color: #64748b;
    text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 0.4mm;
}
.det-val { font-size: 9px; font-weight: 700; color: #0f2055; line-height: 1.2; }
.det-sep { height: 0.4mm; background: #d1ddf5; margin: 1.8mm 0; }
.qr-wrap {
    text-align: center; padding-top: 2mm;
    border-top: 1px solid #c3d0ee; margin-top: 2mm;
}
.qr-wrap img {
    width: 22mm; height: 22mm;
    border: 1px solid #0f2055; padding: 0.8mm; background: #fff;
}
.qr-lbl {
    font-size: 6px; font-weight: 700; color: #0f2055;
    letter-spacing: 1.2px; text-transform: uppercase; margin-top: 1mm;
}
.qr-id { font-size: 5.5px; color: #94a3b8; margin-top: 0.5mm; word-break: break-all; }

/* ══ BOTTOM BAR  (161.5 → 183mm)  ─ float layout ══ */
.bot-bar {
    position: absolute;
    top: 161.5mm; left: 0; right: 0; height: 22mm; z-index: 2;
}
.bb-col {
    float: left; width: 33.33%; height: 22mm;
    text-align: center; box-sizing: border-box; padding: 1.5mm 4mm 0;
}
.bb-clearfix:after { content: ''; display: table; clear: both; }
.sig-img { height: 11mm; margin-bottom: 0.8mm; }
.sig-line { border-top: 1.2px solid #0f2055; width: 52mm; margin: 0 auto 1mm; }
.sig-name { font-size: 10px; font-weight: 700; color: #0f2055; }
.sig-role { font-size: 7.5px; color: #475569; line-height: 1.4; margin-top: 0.5mm; }
.seal-img  { height: 17mm; vertical-align: middle; }
.logos-row { margin-top: 1mm; text-align: center; }
.third-logo { height: 8mm; vertical-align: middle; margin: 0 1.5mm; }
.logo-text  { font-size: 7px; color: #475569; }
.appr-lbl {
    font-size: 7px; color: #64748b;
    text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4mm;
}
.appr-line { border-top: 1.2px solid #0f2055; width: 52mm; margin: 0 auto 1mm; }
.appr-name { font-size: 10px; font-weight: 700; color: #0f2055; }
.appr-role { font-size: 7.5px; color: #475569; line-height: 1.4; margin-top: 0.5mm; }

/* ══ FOOTER  (183 → 207mm) ════════════════════════ */
.footer {
    position: absolute;
    top: 183mm; left: 0; right: 0; height: 24mm;
    background: #0a1628; z-index: 2;
    text-align: center; padding-top: 5mm;
}
.footer-prop {
    font-size: 7px; color: rgba(255,255,255,0.5);
    letter-spacing: 0.3px; margin-bottom: 2mm;
}
.footer-addr {
    font-size: 8px; color: rgba(255,255,255,0.8);
    font-weight: 600; letter-spacing: 0.3px; line-height: 1.5;
}
.footer-links {
    font-size: 7px; color: #7dd3fc; margin-top: 1.5mm; line-height: 1.5;
}

/* ══ COLORFUL BOTTOM STRIP  (207 → 210mm) ═════════ */
.color-strip {
    position: absolute;
    top: 207mm; left: 0; right: 0; height: 3mm; z-index: 5;
    background: linear-gradient(to right,
        #0f2055 0%, #1a5fa8 10%, #0ea5e9 20%, #06b6d4 30%,
        #10b981 40%, #84cc16 50%, #f59e0b 60%,
        #ef4444 70%, #ec4899 80%, #8b5cf6 90%, #0f2055 100%);
}
</style>
</head>
<body>

@php
    $schedule = $enrollment->trainingSchedule ?? null;
    $course   = $schedule?->course ?? null;
    $fullName = $course?->name ?? 'Professional Training Programme';

    /* Dates */
    $start  = $schedule?->start_date  ? \Carbon\Carbon::parse($schedule->start_date)->format('d M Y')  : 'N/A';
    $end    = $schedule?->end_date    ? \Carbon\Carbon::parse($schedule->end_date)->format('d M Y')    : 'N/A';
    $dates  = ($start === $end) ? $start : $start . ' – ' . $end;
    $issued = !empty($enrollment->certificate_issue_date)
                ? \Carbon\Carbon::parse($enrollment->certificate_issue_date)->format('d M Y') : 'N/A';

    /* Duration — add "Hours" if no unit present */
    $rawDur   = $schedule?->duration ?? '';
    $duration = $rawDur
                ? (preg_match('/\d/i', $rawDur) && !preg_match('/hour/i', $rawDur)
                    ? $rawDur . ' Hours' : $rawDur)
                : '—';

    /* Split course name into base + ISO tag */
    $courseBase = $fullName; $isoTag = '';
    if (preg_match('/^(.*?)\s+to\s+(ISO\s[\d]+(?::\d+)?(?:\s*\([^)]+\))?)\s*$/i', $fullName, $m)) {
        $courseBase = trim($m[1]); $isoTag = trim($m[2]);
    } elseif (preg_match('/^(.*?)\s*(\(ISO\s[\d]+(?::\d+)?(?:\s*\([^)]+\))?\))\s*$/i', $fullName, $m)) {
        $courseBase = trim($m[1]); $isoTag = trim($m[2]);
    }

    /* ISO scheme detection */
    $systemLong = 'Management System'; $schemeName = 'IRQAO Auditor Certification Scheme';
    if      (stripos($fullName,'45001')!==false){ $systemLong='Occupational Health and Safety Management System'; $schemeName='IRQAO OHSMS Auditor Certification Scheme'; }
    elseif  (stripos($fullName,'14001')!==false){ $systemLong='Environmental Management System';                  $schemeName='IRQAO EMS Auditor Certification Scheme';   }
    elseif  (stripos($fullName,'9001') !==false){ $systemLong='Quality Management System';                        $schemeName='IRQAO QMS Auditor Certification Scheme';   }
    elseif  (stripos($fullName,'50001')!==false){ $systemLong='Energy Management System';                         $schemeName='IRQAO EnMS Auditor Certification Scheme';  }
    elseif  (stripos($fullName,'27001')!==false){ $systemLong='Information Security Management System';           $schemeName='IRQAO ISMS Auditor Certification Scheme';  }
    elseif  (stripos($fullName,'46001')!==false){ $systemLong='Water Efficiency Management System';               $schemeName='IRQAO Auditor Certification Scheme';       }

    $isoDisplay = $isoTag ?: $fullName;

    /* Course number */
    $courseNo = $schedule?->batch_code
        ?: ('SMS/'.date('y', strtotime($schedule?->start_date ?? 'now')).'/'
            .str_pad($enrollment->training_schedule_id ?? $enrollment->id, 3, '0', STR_PAD_LEFT));

    /* QR */
    $verifyUrl = url('/verify-certificate/'.($enrollment->certificate_number ?? 'N/A'));
    $qrUrl     = 'https://api.qrserver.com/v1/create-qr-code/?size=280x280&data='.urlencode($verifyUrl);

    /* Assets */
    $logo  = file_exists(public_path('sms-logo.png'))
                ? 'data:image/png;base64,'.base64_encode(file_get_contents(public_path('sms-logo.png')))  : null;
    $sig   = file_exists(public_path('ceo-signature.png'))
                ? 'data:image/png;base64,'.base64_encode(file_get_contents(public_path('ceo-signature.png'))) : null;
    $seal  = file_exists(public_path('sms-seal.png'))
                ? 'data:image/png;base64,'.base64_encode(file_get_contents(public_path('sms-seal.png')))  : null;
    $irqao = file_exists(public_path('irqao-logo.png'))
                ? 'data:image/png;base64,'.base64_encode(file_get_contents(public_path('irqao-logo.png'))) : null;
    $ascb  = file_exists(public_path('ascb-logo.png'))
                ? 'data:image/png;base64,'.base64_encode(file_get_contents(public_path('ascb-logo.png')))  : null;

    $microTxt = str_repeat(
        'SUSTAINABLE MANAGEMENT SYSTEM INC. · SMS TRAINING CERTIFICATE · '
        .($enrollment->certificate_number ?? '').' · VERIFIED · www.smscert.com/verify · ', 60);
@endphp

<div class="page">

    {{-- Watermark --}}
    @if($logo)<div class="wm"><img src="{{ $logo }}"></div>@endif

    {{-- Security micro-text --}}
    <div class="micro">{{ $microTxt }}</div>

    {{-- Borders & corners --}}
    <div class="bdr-gold"></div>
    <div class="bdr-navy"></div>
    <div class="co co-tl"></div><div class="co co-tr"></div>
    <div class="co co-bl"></div><div class="co co-br"></div>

    {{-- ══ HEADER ═══════════════════════════════════════════ --}}
    <div class="hdr">
        <div class="hdr-stripe"></div>
        <div class="hdr-logo-panel">
            <div class="hdr-logo-img">
                @if($logo)<img src="{{ $logo }}">
                @else<div style="color:#fff;font-size:26px;font-weight:900;margin-top:6mm;">SMS</div>
                @endif
            </div>
        </div>
        <div class="hdr-title-panel">
            <div class="hdr-title-inner">
                <div class="hdr-company">SUSTAINABLE MANAGEMENT SYSTEM INC.</div>
                <div class="hdr-location">New York, United States of America</div>
                <div class="hdr-gold-rule"></div>
                <div class="hdr-cert-title">Auditor / Lead Auditor<br>Training Certificate</div>
            </div>
        </div>
    </div>

    {{-- ══ TOP GOLD RULE ════════════════════════════════════ --}}
    <div class="rule rule-top"><div class="rule-fill"></div></div>

    {{-- ══ BODY LEFT ═════════════════════════════════════════ --}}
    <div class="body-left">
        <div class="certify-line">This Is To Certify That</div>
        <div class="pname">{{ $enrollment->full_name }}</div>
        <div class="pname-rule"></div>
        <div class="completed-line">has successfully completed the</div>
        <div class="cname">{{ $courseBase }}</div>
        @if($isoTag)
        <div class="iso-tag">Based on &nbsp;<strong>{{ $isoTag }}</strong></div>
        @endif
        <div class="para-wrap">
            <div class="para">
                and has demonstrated the knowledge, skills, and competencies required to plan,
                conduct, report, and follow up management system audits in accordance with
                <strong>{{ $isoDisplay }}</strong> and internationally accepted auditing principles.
                The participant has successfully fulfilled all training requirements and achieved
                the learning objectives necessary to perform first-party, second-party, and
                third-party audits of a <strong>{{ $systemLong }}</strong>.
            </div>
            <div class="accred">
                This training course is certified and accredited by <strong>ASCB(E) Certified
                Auditors</strong> and satisfies part of the formal training requirements for
                individuals seeking certification under the <strong>{{ $schemeName }}</strong>.
            </div>
        </div>
    </div>

    {{-- ══ DETAILS BOX ═══════════════════════════════════════ --}}
    <div class="det-box">
        <div class="det-head">Training Details</div>

        <div class="det-row"><div class="det-lbl">Course Number</div><div class="det-val">{{ $courseNo }}</div></div>
        <div class="det-sep"></div>

        @if($isoTag)
        <div class="det-row"><div class="det-lbl">Training Standard</div><div class="det-val">{{ $isoTag }}</div></div>
        <div class="det-sep"></div>
        @endif

        <div class="det-row"><div class="det-lbl">Training Duration</div><div class="det-val">{{ $duration }}</div></div>
        <div class="det-sep"></div>

        <div class="det-row"><div class="det-lbl">Course Dates</div><div class="det-val">{{ $dates }}</div></div>
        <div class="det-sep"></div>

        <div class="det-row"><div class="det-lbl">Certificate Number</div><div class="det-val">{{ $enrollment->certificate_number ?? 'N/A' }}</div></div>
        <div class="det-sep"></div>

        <div class="det-row"><div class="det-lbl">Issue Date</div><div class="det-val">{{ $issued }}</div></div>

        @if(!empty($enrollment->irqao_reg_id))
        <div class="det-sep"></div>
        <div class="det-row"><div class="det-lbl">IRQAO Registration ID</div><div class="det-val">{{ $enrollment->irqao_reg_id }}</div></div>
        @endif

        <div class="qr-wrap">
            <img src="{{ $qrUrl }}">
            <div class="qr-lbl">Scan to Verify</div>
            <div class="qr-id">{{ $enrollment->certificate_number ?? '' }}</div>
        </div>
    </div>

    {{-- ══ BOTTOM GOLD RULE ══════════════════════════════════ --}}
    <div class="rule rule-bottom"><div class="rule-fill"></div></div>

    {{-- ══ BOTTOM BAR  (float — no nested absolute) ═════════ --}}
    <div class="bot-bar">
        <div class="bb-clearfix">

            <div class="bb-col">
                @if($sig)<img src="{{ $sig }}" class="sig-img">
                @else<div style="height:11mm;"></div>@endif
                <div class="sig-line"></div>
                <div class="sig-name">Abdul Alim</div>
                <div class="sig-role">President &amp; Chief Executive Officer<br>Sustainable Management System Inc.</div>
            </div>

            <div class="bb-col" style="padding-top:0.5mm;">
                @if($seal)<img src="{{ $seal }}" class="seal-img">@endif
                <div class="logos-row">
                    @if($irqao)<img src="{{ $irqao }}" class="third-logo">@endif
                    @if($ascb)<img src="{{ $ascb }}" class="third-logo">@endif
                    @if(!$irqao && !$ascb)
                    <div class="logo-text" style="margin-top:1mm;">
                        <strong style="color:#1a3a8a;font-size:8px;">IRQAO</strong>
                        &nbsp;|&nbsp;
                        <strong style="color:#1a3a8a;font-size:8px;">ASCB(E)</strong>
                        &nbsp;Accredited
                    </div>
                    @endif
                </div>
            </div>

            <div class="bb-col">
                <div class="appr-lbl">Certification Approved By</div>
                <div class="appr-line"></div>
                <div class="appr-name">Training Director</div>
                <div class="appr-role">SMS Training Services<br>Sustainable Management System Inc.</div>
            </div>

        </div>
    </div>

    {{-- ══ FOOTER ════════════════════════════════════════════ --}}
    <div class="footer">
        <div class="footer-prop">
            This certificate is the property of Sustainable Management System Inc. and is subject to verification.
        </div>
        <div class="footer-addr">
            Sustainable Management System Inc. &nbsp;|&nbsp;
            277 Cherry Street, Suite 12N, New York, NY 10002, United States of America
        </div>
        <div class="footer-links">
            www.smscert.com/verify &nbsp;&nbsp;·&nbsp;&nbsp;
            www.irqao.com &nbsp;&nbsp;·&nbsp;&nbsp;
            info@smscert.com &nbsp;&nbsp;·&nbsp;&nbsp;
            www.smscert.com
        </div>
    </div>

    {{-- ══ COLORFUL BOTTOM STRIP ════════════════════════════ --}}
    <div class="color-strip"></div>

</div>
</body>
</html>
