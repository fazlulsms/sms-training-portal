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
    background: #ffffff; color: #1e293b;
}

/* â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
   PAGE SHELL
â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• */
.page {
    position: relative;
    width: 210mm; height: 297mm;
    overflow: hidden; background: #ffffff;
}

/* Gold outer frame */
.frame-gold {
    position: absolute;
    top: 4mm; left: 4mm; right: 4mm; bottom: 4mm;
    border: 1px solid #c9a227; z-index: 3;
}
/* Navy inner frame */
.frame-navy {
    position: absolute;
    top: 6.5mm; left: 6.5mm; right: 6.5mm; bottom: 6.5mm;
    border: 2px solid #0d1b4b; z-index: 3;
}
/* Corner ornaments */
.co { position: absolute; width: 7mm; height: 7mm; border-color: #c9a227; border-style: solid; z-index: 5; }
.co-tl { top: 8mm;    left: 8mm;   border-width: 2.5px 0 0 2.5px; }
.co-tr { top: 8mm;    right: 8mm;  border-width: 2.5px 2.5px 0 0; }
.co-bl { bottom: 8mm; left: 8mm;   border-width: 0 0 2.5px 2.5px; }
.co-br { bottom: 8mm; right: 8mm;  border-width: 0 2.5px 2.5px 0; }

/* â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
   HEADER BAND  (0 â†’ 58mm)  navy background
â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• */
.hdr {
    display: table;
    width: 100%; height: 58mm;
    background: #0d1b4b;
    border-bottom: 0;
}
/* subtle diagonal texture overlay */
.hdr-texture {
    position: absolute;
    top: 0; left: 0; right: 0; height: 58mm;
    opacity: 0.035;
    background-image: repeating-linear-gradient(
        45deg, #fff 0, #fff 1px, transparent 1px, transparent 12px
    );
    z-index: 1;
}
/* Left cell â€” logo */
.hdr-left {
    display: table-cell;
    width: 68mm; height: 58mm;
    vertical-align: middle;
    text-align: center;
    border-right: 1px solid rgba(201,162,39,0.4);
    position: relative; z-index: 2;
    padding: 0 6mm;
}
.hdr-left img { max-width: 52mm; max-height: 26mm; }
/* Right cell â€” title */
.hdr-right {
    display: table-cell;
    width: auto; height: 58mm;
    vertical-align: middle;
    text-align: center;
    padding: 0 8mm;
    position: relative; z-index: 2;
}
.hdr-co-name {
    font-size: 13.5pt; font-weight: 800; color: #ffffff;
    letter-spacing: 1.5px; text-transform: uppercase;
    line-height: 1.2; margin-bottom: 1.5mm;
}
.hdr-co-sub {
    font-size: 7pt; color: rgba(255,255,255,0.55);
    letter-spacing: 2px; text-transform: uppercase; margin-bottom: 3.5mm;
}
.hdr-gold-line {
    height: 1px; margin: 0 8mm 3.5mm;
    background: linear-gradient(to right, transparent, #c9a227, #f0d060, #c9a227, transparent);
}
.hdr-cert-title {
    font-size: 15pt; font-weight: 700; color: #f0d060;
    letter-spacing: 1.5px; text-transform: uppercase;
    line-height: 1.25; margin-bottom: 1.5mm;
}
.hdr-cert-prog {
    font-size: 7.5pt; color: rgba(255,255,255,0.65);
    letter-spacing: 2.5px; text-transform: uppercase;
}

/* â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
   GOLD RULE  (58 â†’ 61mm)
â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• */
.rule-gold {
    height: 3mm;
    background: linear-gradient(to right,
        #0d1b4b 0%, #1a5fa8 12%, #c9a227 28%,
        #f0d060 50%, #c9a227 72%, #1a5fa8 88%, #0d1b4b 100%);
}

/* â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
   BODY  â€” all centre-aligned text
â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• */
.body { padding: 7mm 16mm 0; text-align: center; }

/* â”€â”€ RECIPIENT â”€â”€ */
.present-line {
    font-size: 9pt; color: #64748b; font-style: italic; margin-bottom: 2.5mm;
}
.rec-name {
    font-size: 28pt; font-weight: 700; color: #0d1b4b;
    letter-spacing: 0.3px; line-height: 1.1; margin-bottom: 2mm;
}
/* Flanking ornament line */
.orn-wrap { text-align: center; margin-bottom: 5mm; }
.orn-line-l {
    display: inline-block; width: 28mm; height: 1px;
    background: linear-gradient(to right, transparent, #c9a227);
    vertical-align: middle;
}
.orn-line-r {
    display: inline-block; width: 28mm; height: 1px;
    background: linear-gradient(to left, transparent, #c9a227);
    vertical-align: middle;
}
.orn-dot {
    display: inline-block; width: 2.5mm; height: 2.5mm;
    background: #c9a227; vertical-align: middle; margin: 0 2.5mm;
}

/* â”€â”€ COURSE â”€â”€ */
.completed-line {
    font-size: 8.5pt; color: #64748b; font-style: italic; margin-bottom: 2mm;
}
.course-name {
    font-size: 13pt; font-weight: 700; color: #0d1b4b;
    line-height: 1.35; margin-bottom: 1.5mm;
}
.iso-tag {
    font-size: 10pt; font-weight: 600; color: #1a4fa8; margin-bottom: 5mm;
}

/* â”€â”€ BODY PARAGRAPH â”€â”€ */
.para {
    font-size: 8pt; color: #374151; line-height: 1.65;
    text-align: justify; margin-bottom: 3mm; padding: 0 1mm;
}

/* â”€â”€ ACCREDITATION STRIP â”€â”€ */
.accred {
    border-left: 3px solid #c9a227;
    background: #f8fafc;
    padding: 2.5mm 4mm;
    margin: 0 1mm 5mm;
    text-align: left;
}
.accred p { font-size: 7.5pt; color: #475569; line-height: 1.5; margin: 0; }

/* â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
   DETAILS PANEL
â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• */
.det-panel {
    background: #eef3fd;
    border-top: 3px solid #0d1b4b;
    border-left: 1px solid #c8d5ef;
    border-right: 1px solid #c8d5ef;
    border-bottom: 1px solid #c8d5ef;
    border-radius: 0 0 3px 3px;
    margin: 0 1mm 5mm;
}
/* outer table: fields (left) + QR (right) */
.det-outer  { display: table; width: 100%; }
.det-fields { display: table-cell; width: 74%; vertical-align: top; padding: 3mm 0 2mm 4mm; }
.det-qr     {
    display: table-cell; width: 26%; vertical-align: middle;
    text-align: center;
    border-left: 1px dashed #b8caec;
    padding: 3mm 4mm;
}
/* inner 2-col grid */
.det-grid   { display: table; width: 100%; }
.det-row    { display: table-row; }
.det-l      { display: table-cell; width: 50%; text-align: left;  padding: 1.5mm 3mm 1.5mm 0; vertical-align: top; }
.det-r      { display: table-cell; width: 50%; text-align: right; padding: 1.5mm 0 1.5mm 3mm; vertical-align: top; border-left: 1px solid #c8d5ef; }
.det-lbl    { font-size: 5.5pt; color: #64748b; text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 0.5mm; }
.det-val    { font-size: 8.5pt; font-weight: 700; color: #0d1b4b; line-height: 1.2; }
.det-val-cert { font-size: 8.5pt; font-weight: 700; font-family: monospace; color: #b45309; }
/* QR */
.qr-img { width: 20mm; height: 20mm; border: 1px solid #b8caec; padding: 1mm; background: #fff; display: block; margin: 0 auto 1.5mm; }
.qr-lbl { font-size: 5pt; font-weight: 700; color: #0d1b4b; text-transform: uppercase; letter-spacing: 0.8px; }

/* â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
   SEPARATOR
â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• */
.sep-gold {
    height: 2px; margin: 0 1mm 5mm;
    background: linear-gradient(to right, transparent, #c9a227 20%, #f0d060 50%, #c9a227 80%, transparent);
}

/* â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
   SIGNATURE ROW â€” 3 columns
â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• */
.sig-table { display: table; width: 100%; }

/* Left & right: signature columns */
.sig-col {
    display: table-cell; width: 32%; vertical-align: bottom;
    text-align: center; padding: 0 4mm;
}
/* Centre column */
.sig-mid {
    display: table-cell; width: 36%; vertical-align: middle;
    text-align: center; padding: 0 4mm;
}
/* CEO signature image */
.sig-img { height: 14mm; display: block; margin: 0 auto 1mm; }
/* Signature underline */
.sig-line { width: 44mm; border-top: 1.2px solid #0d1b4b; margin: 0 auto 1.5mm; }
.sig-name { font-size: 9.5pt; font-weight: 700; color: #0d1b4b; }
.sig-role { font-size: 7pt; color: #475569; line-height: 1.4; margin-top: 0.5mm; }

/* Centre box: seal + logos */
.mid-box {
    display: inline-block;
    border: 1px solid #c9a227;
    border-top: 3px solid #0d1b4b;
    background: #fefcf3;
    padding: 3mm 4mm 2.5mm;
    border-radius: 0 0 3px 3px;
    text-align: center;
}
.seal-img  { height: 19mm; display: block; margin: 0 auto 2mm; }
.logos-row { text-align: center; margin-bottom: 1.5mm; }
.logos-row img { height: 8mm; vertical-align: middle; margin: 0 1.5mm; }
.logos-lbl {
    font-size: 5pt; font-weight: 700; color: #0d1b4b;
    text-transform: uppercase; letter-spacing: 0.8px;
}

/* â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
   FOOTER  (absolute bottom)
â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• */
.footer {
    position: absolute;
    bottom: 0; left: 0; right: 0;
    height: 13mm; background: #0d1b4b;
    text-align: center; padding-top: 3.5mm; z-index: 10;
}
.footer-addr {
    font-size: 7pt; font-weight: 600;
    color: rgba(255,255,255,0.85); letter-spacing: 0.2px;
}
.footer-links { font-size: 6.5pt; color: #93c5fd; margin-top: 1mm; }

/* Rainbow strip â€” sits on top of footer */
.rainbow {
    position: absolute;
    bottom: 0; left: 0; right: 0; height: 2.5mm; z-index: 11;
    background: linear-gradient(to right,
        #0d1b4b 0%, #1e40af 10%, #0ea5e9 20%, #06b6d4 30%,
        #10b981 40%, #84cc16 50%, #f59e0b 60%,
        #ef4444 70%, #ec4899 80%, #8b5cf6 90%, #0d1b4b 100%);
}
</style>
</head>
<body>

@php
    $schedule = $enrollment->trainingSchedule ?? null;
    $course   = $schedule?->course ?? null;
    $fullName = $course?->name ?? 'Professional Training Programme';

    /* Dates */
    $start  = $schedule?->start_date ? \Carbon\Carbon::parse($schedule->start_date)->format('d M Y') : 'N/A';
    $end    = $schedule?->end_date   ? \Carbon\Carbon::parse($schedule->end_date)->format('d M Y')   : 'N/A';
    $dates  = ($start === $end) ? $start : $start . ' â€“ ' . $end;
    $issued = !empty($enrollment->certificate_issue_date)
                ? \Carbon\Carbon::parse($enrollment->certificate_issue_date)->format('d M Y') : 'N/A';

    /* Duration */
    $rawDur   = $schedule?->duration ?? '';
    $duration = $rawDur
                ? (preg_match('/\d/i', $rawDur) && !preg_match('/hour/i', $rawDur) ? $rawDur . ' Hours' : $rawDur)
                : 'â€”';

    /* Split course name: base + ISO tag */
    $courseBase = $fullName; $isoTag = '';
    if (preg_match('/^(.*?)\s+to\s+(ISO\s[\d]+(?::\d+)?(?:\s*\([^)]+\))?)\s*$/i', $fullName, $m)) {
        $courseBase = trim($m[1]); $isoTag = trim($m[2]);
    } elseif (preg_match('/^(.*?)\s*(\(ISO\s[\d]+(?::\d+)?(?:\s*\([^)]+\))?\))\s*$/i', $fullName, $m)) {
        $courseBase = trim($m[1]); $isoTag = trim($m[2]);
    }

    /* ISO scheme detection */
    $systemLong = 'Management System'; $schemeName = 'IRQAO Auditor Certification Scheme';
    if      (stripos($fullName,'45001')!==false){ $systemLong='Occupational Health &amp; Safety Management System'; $schemeName='IRQAO OHSMS Auditor Certification Scheme'; }
    elseif  (stripos($fullName,'14001')!==false){ $systemLong='Environmental Management System';                     $schemeName='IRQAO EMS Auditor Certification Scheme'; }
    elseif  (stripos($fullName,'9001') !==false){ $systemLong='Quality Management System';                           $schemeName='IRQAO QMS Auditor Certification Scheme'; }
    elseif  (stripos($fullName,'50001')!==false){ $systemLong='Energy Management System';                            $schemeName='IRQAO EnMS Auditor Certification Scheme'; }
    elseif  (stripos($fullName,'27001')!==false){ $systemLong='Information Security Management System';              $schemeName='IRQAO ISMS Auditor Certification Scheme'; }

    $isoDisplay = $isoTag ?: $fullName;

    $courseNo = $schedule?->batch_code
        ?: ('SMS/'.date('y', strtotime($schedule?->start_date ?? 'now')).'/'
            .str_pad($enrollment->training_schedule_id ?? $enrollment->id, 3, '0', STR_PAD_LEFT));

    $verifyUrl = url('/verify-certificate/'.($enrollment->certificate_number ?? 'N/A'));
    $qrUrl     = 'https://api.qrserver.com/v1/create-qr-code/?size=240x240&data='.urlencode($verifyUrl);

    $toB64 = fn($f) => file_exists(public_path($f))
                ? 'data:image/png;base64,'.base64_encode(file_get_contents(public_path($f))) : null;

    $logo  = $toB64('sms-logo.png');
    $sig   = $toB64('ceo-signature.png');
    $seal  = $toB64('sms-seal.png');
    $irqao = $toB64('Irqao-logo.png');
    $ascb  = $toB64('ascb-logo.png');
@endphp

<div class="page">

    {{-- Border frames --}}
    <div class="frame-gold"></div>
    <div class="frame-navy"></div>
    <div class="co co-tl"></div><div class="co co-tr"></div>
    <div class="co co-bl"></div><div class="co co-br"></div>

    {{-- â•â• HEADER BAND â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
    <div class="hdr">
        <div class="hdr-texture"></div>

        {{-- Left: Logo --}}
        <div class="hdr-left">
            @if($logo)
                <img src="{{ $logo }}">
            @else
                <span style="font-size:30pt;font-weight:900;color:#f0d060;letter-spacing:2px;">SMS</span>
            @endif
        </div>

        {{-- Right: Title --}}
        <div class="hdr-right">
            <div class="hdr-co-name">Sustainable Management System Inc.</div>
            <div class="hdr-co-sub">International Training Services &amp; Personnel Certifications</div>
            <div class="hdr-gold-line"></div>
            <div class="hdr-cert-title">Certificate of Successful<br>Completion</div>
            <div class="hdr-cert-prog">Auditor / Lead Auditor Training Programme</div>
        </div>
    </div>

    {{-- â•â• GOLD GRADIENT RULE â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
    <div class="rule-gold"></div>

    {{-- â•â• BODY â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
    <div class="body">

        {{-- Recipient --}}
        <div class="present-line" style="margin-top:5mm;">This is to certify that</div>
        <div class="rec-name">{{ $enrollment->full_name }}</div>

        {{-- Ornament line --}}
        <div class="orn-wrap">
            <span class="orn-line-l"></span>
            <span class="orn-dot"></span>
            <span class="orn-line-r"></span>
        </div>

        {{-- Course --}}
        <div class="completed-line">has successfully completed all requirements for</div>
        <div class="course-name">{{ $courseBase }}</div>
        @if($isoTag)
        <div class="iso-tag">Based on &nbsp;<strong>{{ $isoTag }}</strong></div>
        @endif

        {{-- Competency paragraph --}}
        <div class="para">
            and has demonstrated the knowledge, skills and competencies required to plan, conduct,
            report and follow up management system audits in accordance with
            <strong>{{ $isoDisplay }}</strong> and internationally accepted auditing principles.
            The participant has fulfilled all training requirements to perform first-party,
            second-party and third-party audits of a <strong>{{ $systemLong }}</strong>.
        </div>

        {{-- Accreditation strip --}}
        <div class="accred">
            <p>This training programme is certified and accredited by <strong>ASCB(E) Certified Auditors</strong>
            and satisfies the formal training requirements under the <strong>{{ $schemeName }}</strong>.</p>
        </div>

        {{-- â”€â”€ DETAILS PANEL â”€â”€ --}}
        <div class="det-panel">
            <div class="det-outer">
                {{-- Fields --}}
                <div class="det-fields">
                    <div class="det-grid">
                        <div class="det-row">
                            <div class="det-l">
                                <div class="det-lbl">Course ID</div>
                                <div class="det-val">{{ $courseNo }}</div>
                            </div>
                            <div class="det-r">
                                <div class="det-lbl">Standard</div>
                                <div class="det-val">{{ $isoTag ?: 'â€”' }}</div>
                            </div>
                        </div>
                        <div class="det-row">
                            <div class="det-l">
                                <div class="det-lbl">Training Duration</div>
                                <div class="det-val">{{ $duration }}</div>
                            </div>
                            <div class="det-r">
                                <div class="det-lbl">Course Dates</div>
                                <div class="det-val">{{ $dates }}</div>
                            </div>
                        </div>
                        <div class="det-row">
                            <div class="det-l">
                                <div class="det-lbl">Certificate Number</div>
                                <div class="det-val-cert">{{ $enrollment->certificate_number ?? 'N/A' }}</div>
                            </div>
                            <div class="det-r">
                                <div class="det-lbl">Issue Date</div>
                                <div class="det-val">{{ $issued }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- QR --}}
                <div class="det-qr">
                    <img src="{{ $qrUrl }}" class="qr-img">
                    <div class="qr-lbl">Scan to Verify</div>
                </div>
            </div>
        </div>

        {{-- â”€â”€ SEPARATOR â”€â”€ --}}
        <div class="sep-gold"></div>

        {{-- â”€â”€ SIGNATURES â”€â”€ --}}
        <div class="sig-table">

            {{-- Left: CEO --}}
            <div class="sig-col">
                @if($sig)
                    <img src="{{ $sig }}" class="sig-img">
                @else
                    <div style="height:14mm;"></div>
                @endif
                <div class="sig-line"></div>
                <div class="sig-name">Abdul Alim</div>
                <div class="sig-role">President &amp; Chief Executive Officer<br>Sustainable Management System Inc.</div>
            </div>

            {{-- Centre: Seal + Logos --}}
            <div class="sig-mid">
                <div class="mid-box">
                    @if($seal)
                        <img src="{{ $seal }}" class="seal-img">
                    @endif
                    @if($irqao || $ascb)
                    <div class="logos-row">
                        @if($irqao)<img src="{{ $irqao }}">@endif
                        @if($ascb)<img src="{{ $ascb }}">@endif
                    </div>
                    <div class="logos-lbl">Accredited Certification Provider</div>
                    @endif
                </div>
            </div>

            {{-- Right: Training Director --}}
            <div class="sig-col">
                <div style="height:14mm;"></div>
                <div class="sig-line"></div>
                <div class="sig-name">Training Director</div>
                <div class="sig-role">SMS Training Academy<br>Sustainable Management System Inc.</div>
            </div>

        </div>

    </div>{{-- end .body --}}

    {{-- â•â• FOOTER â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
    <div class="footer">
        <div class="footer-addr">
            Sustainable Management System Inc. &nbsp;|&nbsp;
            277 Cherry Street, Suite 12N, New York, NY 10002, United States of America
        </div>
        <div class="footer-links">
            www.smscert.com/verify &nbsp;Â·&nbsp; www.irqao.com &nbsp;Â·&nbsp; info@smscert.com
        </div>
    </div>

    {{-- Rainbow strip --}}
    <div class="rainbow"></div>

</div>
</body>
</html>
