<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Auditor / Lead Auditor Training Certificate</title>
<style>
@page { size: A4 portrait; margin: 0; }
*, *::before, *::after { box-sizing: border-box; }
html, body {
    margin: 0; padding: 0;
    width: 210mm; height: 297mm;
    font-family: 'DejaVu Sans', Arial, sans-serif;
    background: #ffffff;
    color: #1e293b;
}

/* ── Page wrapper ──────────────────────────────── */
.cert {
    position: relative;
    width: 210mm;
    height: 297mm;
    overflow: hidden;
    background: #ffffff;
}

/* ── Border framing ────────────────────────────── */
.b1 { position: absolute; top: 4mm;   left: 4mm;   right: 4mm;   bottom: 4mm;   border: 1px solid #c9a227; z-index: 2; }
.b2 { position: absolute; top: 6.5mm; left: 6.5mm; right: 6.5mm; bottom: 6.5mm; border: 2px solid #0f2055; z-index: 2; }

/* Corner ornaments */
.co { position: absolute; width: 6mm; height: 6mm; border-color: #c9a227; border-style: solid; z-index: 3; }
.co-tl { top: 7.5mm;    left: 7.5mm;   border-width: 2px 0 0 2px; }
.co-tr { top: 7.5mm;    right: 7.5mm;  border-width: 2px 2px 0 0; }
.co-bl { bottom: 7.5mm; left: 7.5mm;   border-width: 0 0 2px 2px; }
.co-br { bottom: 7.5mm; right: 7.5mm;  border-width: 0 2px 2px 0; }

/* ── Watermark removed ── */

/* ── Content stack ─────────────────────────────── */
.body {
    position: relative;
    z-index: 10;
    padding: 11mm 15mm 10mm;
    text-align: center;
}

/* Header */
.hdr-logo { margin-bottom: 2mm; }
.hdr-logo img { height: 19.2mm; }
.hdr-name {
    font-size: 11pt; font-weight: 800; color: #0f2055;
    letter-spacing: 1.2px; text-transform: uppercase;
}
.hdr-loc {
    font-size: 6.5pt; color: #64748b;
    letter-spacing: 1px; text-transform: uppercase; margin-top: 1px;
}
.hdr-rule {
    height: 1px; width: 50mm; margin: 3mm auto 3mm;
    background: linear-gradient(to right, transparent, #c9a227, transparent);
}

/* Title */
.cert-title {
    font-size: 20pt; font-weight: 700; color: #0f2055;
    text-transform: uppercase; letter-spacing: 0.5px; line-height: 1.15;
    margin-bottom: 1mm;
}
.cert-sub {
    font-size: 8.5pt; font-weight: 600; color: #b45309;
    letter-spacing: 2.5px; text-transform: uppercase; margin-bottom: 4mm;
}

/* Gold rule */
.gold-rule {
    height: 2px; margin: 0 10mm 4mm;
    background: linear-gradient(to right, transparent, #c9a227 20%, #f0d060 50%, #c9a227 80%, transparent);
}

/* Recipient */
.present-line { font-size: 9pt; color: #64748b; font-style: italic; margin-bottom: 1.5mm; }
.rec-name {
    font-size: 22pt; font-weight: 700; color: #0f2055;
    letter-spacing: 0.3px; line-height: 1.1; margin-bottom: 1mm;
}
.name-rule {
    height: 1px; width: 80mm; margin: 0 auto 3.5mm;
    background: linear-gradient(to right, transparent, #c9a227, transparent);
}

/* Course */
.completed-line { font-size: 8.5pt; color: #64748b; font-style: italic; margin-bottom: 1.5mm; }
.course-name {
    font-size: 12.5pt; font-weight: 700; color: #1a5fa8;
    line-height: 1.3; margin-bottom: 1.5mm;
}
.iso-tag { font-size: 9.5pt; font-weight: 600; color: #1a5fa8; margin-bottom: 2.5mm; }
.body-para {
    font-size: 8pt; color: #374151; line-height: 1.55;
    text-align: justify; margin-bottom: 2mm; padding: 0 2mm;
}

/* Accreditation strip */
.accred {
    border-left: 3px solid #c9a227;
    background: #f8fafc;
    padding: 2mm 4mm;
    margin: 0 2mm 3mm;
    text-align: left;
}
.accred p { font-size: 7.5pt; color: #475569; line-height: 1.4; margin: 0; }

/* Details box */
.det-box {
    background: #f1f5f9;
    border: 1px solid #dde3ee;
    border-top: 3px solid #0f2055;
    border-radius: 3px;
    margin: 0 2mm 3mm;
    padding: 3mm 4mm;
}
.det-inner { display: table; width: 100%; }
.det-left  { display: table-cell; width: 72%; vertical-align: top; padding-right: 3mm; }
.det-right { display: table-cell; width: 28%; vertical-align: middle; text-align: center; border-left: 1px dashed #c3cedf; padding-left: 3mm; }
.det-grid  { display: table; width: 100%; }
.det-row   { display: table-row; }
.det-cell  { display: table-cell; width: 50%; padding: 1.2mm 1.5mm; text-align: left; vertical-align: top; }
.det-lbl   { font-size: 5.5pt; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 1px; }
.det-val   { font-size: 8pt; font-weight: 700; color: #0f2055; line-height: 1.2; }
.qr-img    { width: 19mm; height: 19mm; border: 1px solid #c3cedf; padding: 0.8mm; background: #fff; }
.qr-lbl    { font-size: 5pt; font-weight: 700; color: #0f2055; text-transform: uppercase; letter-spacing: 0.8px; margin-top: 1mm; }

/* Badge */
.badge-row { margin-bottom: 3mm; }
.badge-box {
    display: inline-block;
    border: 1px double #c9a227;
    background: #fffdf5;
    padding: 1.5mm 5mm;
}
.badge-logos { display: inline-block; vertical-align: middle; }
.badge-logos img { height: 8mm; vertical-align: middle; margin: 0 2mm; }
.badge-text {
    font-size: 8pt; font-weight: 700; color: #0f2055;
    letter-spacing: 0.5px; display: inline-block; vertical-align: middle;
}
.badge-sub {
    font-size: 5.5pt; color: #64748b; text-transform: uppercase;
    letter-spacing: 0.5px; display: block; margin-top: 1px;
}

/* Signatures */
.sig-table { display: table; width: 100%; margin-bottom: 3mm; }
.sig-cell  { display: table-cell; width: 50%; text-align: center; vertical-align: bottom; padding: 0 4mm; }
.sig-img   { height: 13mm; margin-bottom: 0.5mm; }
.sig-seal  { height: 17mm; margin-bottom: 0.5mm; }
.sig-line  { width: 48mm; border-top: 1.2px solid #0f2055; margin: 0 auto 1.5mm; }
.sig-name  { font-size: 9pt; font-weight: 700; color: #0f2055; }
.sig-role  { font-size: 7pt; color: #475569; line-height: 1.35; margin-top: 1px; }

/* Footer */
.footer {
    position: absolute;
    bottom: 0; left: 0; right: 0; height: 13mm;
    background: #0f2055;
    text-align: center;
    padding-top: 3mm;
}
.footer-addr {
    font-size: 7pt; color: rgba(255,255,255,0.85);
    font-weight: 600; letter-spacing: 0.2px;
}
.footer-links { font-size: 6.5pt; color: #7dd3fc; margin-top: 1mm; }

/* Colorful strip */
.color-strip {
    position: absolute;
    bottom: 0; left: 0; right: 0; height: 2.5mm; z-index: 5;
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

    /* Duration */
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
    if      (stripos($fullName,'45001')!==false){ $systemLong='Occupational Health & Safety Management System'; $schemeName='IRQAO OHSMS Auditor Certification Scheme'; }
    elseif  (stripos($fullName,'14001')!==false){ $systemLong='Environmental Management System';                 $schemeName='IRQAO EMS Auditor Certification Scheme';   }
    elseif  (stripos($fullName,'9001') !==false){ $systemLong='Quality Management System';                       $schemeName='IRQAO QMS Auditor Certification Scheme';   }
    elseif  (stripos($fullName,'50001')!==false){ $systemLong='Energy Management System';                        $schemeName='IRQAO EnMS Auditor Certification Scheme';  }
    elseif  (stripos($fullName,'27001')!==false){ $systemLong='Information Security Management System';          $schemeName='IRQAO ISMS Auditor Certification Scheme';  }

    $isoDisplay = $isoTag ?: $fullName;

    /* Course number */
    $courseNo = $schedule?->batch_code
        ?: ('SMS/'.date('y', strtotime($schedule?->start_date ?? 'now')).'/'
            .str_pad($enrollment->training_schedule_id ?? $enrollment->id, 3, '0', STR_PAD_LEFT));

    /* QR */
    $verifyUrl = url('/verify-certificate/'.($enrollment->certificate_number ?? 'N/A'));
    $qrUrl     = 'https://api.qrserver.com/v1/create-qr-code/?size=240x240&data='.urlencode($verifyUrl);

    /* Assets — base64 embed for DomPDF */
    $toB64 = fn($file) => file_exists(public_path($file))
                ? 'data:image/png;base64,'.base64_encode(file_get_contents(public_path($file)))
                : null;

    $logo  = $toB64('sms-logo.png');
    $sig   = $toB64('ceo-signature.png');
    $seal  = $toB64('sms-seal.png');
    $irqao = $toB64('Irqao-logo.png');   // capital I
    $ascb  = $toB64('ascb-logo.png');
@endphp

<div class="cert">

    {{-- Borders & corners --}}
    <div class="b1"></div>
    <div class="b2"></div>
    <div class="co co-tl"></div><div class="co co-tr"></div>
    <div class="co co-bl"></div><div class="co co-br"></div>

    <div class="body">

        {{-- ── HEADER ── --}}
        <div class="hdr-logo">
            @if($logo)<img src="{{ $logo }}">
            @else<span style="font-size:22pt;font-weight:900;color:#0f2055;">SMS</span>
            @endif
        </div>
        <div class="hdr-name">Sustainable Management System Inc.</div>
        <div class="hdr-loc">International Training Services &amp; Personnel Certifications</div>
        <div class="hdr-rule"></div>

        {{-- ── TITLE ── --}}
        <div class="cert-title">Certificate of Attainment</div>
        <div class="cert-sub">Auditor / Lead Auditor Training Program</div>

        <div class="gold-rule"></div>

        {{-- ── RECIPIENT ── --}}
        <div class="present-line">This is to officially certify that</div>
        <div class="rec-name">{{ $enrollment->full_name }}</div>
        <div class="name-rule"></div>

        {{-- ── COURSE ── --}}
        <div class="completed-line">has successfully completed all requirements for</div>
        <div class="course-name">{{ $courseBase }}</div>
        @if($isoTag)<div class="iso-tag">Based on <strong>{{ $isoTag }}</strong></div>@endif

        <div class="body-para">
            and has demonstrated the knowledge, skills and competencies required to plan, conduct, report and
            follow up management system audits in accordance with <strong>{{ $isoDisplay }}</strong> and internationally
            accepted auditing principles. The participant has fulfilled all training requirements to perform
            first-party, second-party and third-party audits of a <strong>{{ $systemLong }}</strong>.
        </div>

        {{-- ── ACCREDITATION ── --}}
        <div class="accred">
            <p>This training course is certified and accredited by <strong>ASCB(E) Certified Auditors</strong>
            and satisfies the formal training requirements under the <strong>{{ $schemeName }}</strong>.</p>
        </div>

        {{-- ── DETAILS BOX ── --}}
        <div class="det-box">
            <div class="det-inner">
                <div class="det-left">
                    <div class="det-grid">
                        <div class="det-row">
                            <div class="det-cell">
                                <div class="det-lbl">Course ID</div>
                                <div class="det-val">{{ $courseNo }}</div>
                            </div>
                            <div class="det-cell">
                                <div class="det-lbl">Standard</div>
                                <div class="det-val">{{ $isoTag ?: '—' }}</div>
                            </div>
                        </div>
                        <div class="det-row">
                            <div class="det-cell">
                                <div class="det-lbl">Duration</div>
                                <div class="det-val">{{ $duration }}</div>
                            </div>
                            <div class="det-cell">
                                <div class="det-lbl">Course Dates</div>
                                <div class="det-val">{{ $dates }}</div>
                            </div>
                        </div>
                        <div class="det-row">
                            <div class="det-cell">
                                <div class="det-lbl">Certificate No.</div>
                                <div class="det-val" style="color:#b45309;font-family:monospace;">{{ $enrollment->certificate_number ?? 'N/A' }}</div>
                            </div>
                            <div class="det-cell">
                                <div class="det-lbl">Issue Date</div>
                                <div class="det-val">{{ $issued }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="det-right">
                    <img src="{{ $qrUrl }}" class="qr-img">
                    <div class="qr-lbl">Scan to Verify</div>
                </div>
            </div>
        </div>

        {{-- ── ACCREDITATION LOGOS BADGE ── --}}
        <div class="badge-row">
            <div class="badge-box">
                @if($irqao || $ascb)
                    <span class="badge-logos">
                        @if($irqao)<img src="{{ $irqao }}" style="height:8mm;">@endif
                        @if($ascb)<img src="{{ $ascb }}" style="height:8mm;">@endif
                    </span>
                @else
                    <span class="badge-text">IRQAO &nbsp;|&nbsp; ASCB(E)</span>
                @endif
                <span class="badge-sub">Accredited Certification Provider</span>
            </div>
        </div>

        {{-- ── SIGNATURES ── --}}
        <div class="sig-table">

            <div class="sig-cell">
                @if($sig)<img src="{{ $sig }}" class="sig-img">
                @else<div style="height:13mm;"></div>@endif
                <div class="sig-line"></div>
                <div class="sig-name">Abdul Alim</div>
                <div class="sig-role">President &amp; Chief Executive Officer<br>Sustainable Management System Inc.</div>
            </div>

            <div class="sig-cell">
                @if($seal)<img src="{{ $seal }}" class="sig-seal">
                @else<div style="height:17mm;"></div>@endif
                <div class="sig-line"></div>
                <div class="sig-name">Training Director</div>
                <div class="sig-role">SMS Training Services<br>Sustainable Management System Inc.</div>
            </div>

        </div>

    </div>{{-- end .body --}}

    {{-- ── FOOTER ── --}}
    <div class="footer">
        <div class="footer-addr">
            Sustainable Management System Inc. &nbsp;|&nbsp;
            277 Cherry Street, Suite 12N, New York, NY 10002, United States of America
        </div>
        <div class="footer-links">
            www.smscert.com/verify &nbsp;·&nbsp; www.irqao.com &nbsp;·&nbsp; info@smscert.com
        </div>
    </div>

    {{-- ── COLORFUL STRIP ── --}}
    <div class="color-strip"></div>

</div>
</body>
</html>
