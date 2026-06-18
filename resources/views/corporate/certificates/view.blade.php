<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate — {{ $certificate->certificate_number }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Inter:wght@400;600;700&display=swap');
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: #f5f5f5; display: flex; justify-content: center; align-items: flex-start; min-height: 100vh; padding: 40px 20px; font-family: 'Inter', sans-serif; }
        .print-btn { position: fixed; top: 20px; right: 20px; background: #1e3a8a; color: #fff; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-size: 14px; font-weight: 700; z-index: 100; }
        @media print { .print-btn { display: none; } body { background: #fff; padding: 0; } }

        .cert-page {
            width: 297mm; min-height: 210mm;
            background: #fff;
            position: relative;
            padding: 30mm 25mm;
            box-shadow: 0 8px 40px rgba(0,0,0,.12);
        }
        .cert-border-outer {
            position: absolute; inset: 10mm;
            border: 4px double #b8860b;
            pointer-events: none;
        }
        .cert-border-inner {
            position: absolute; inset: 13mm;
            border: 1.5px solid #c8a030;
            pointer-events: none;
        }
        .cert-corner {
            position: absolute; width: 16mm; height: 16mm;
            background: #b8860b22;
            border-radius: 50%;
        }
        .corner-tl { top: 8mm; left: 8mm; }
        .corner-tr { top: 8mm; right: 8mm; }
        .corner-bl { bottom: 8mm; left: 8mm; }
        .corner-br { bottom: 8mm; right: 8mm; }

        .cert-logo { text-align: center; margin-bottom: 6mm; }
        .cert-logo img { height: 18mm; }
        .cert-logo-text { font-family: 'Playfair Display', serif; font-size: 22pt; font-weight: 900; color: #1e3a8a; letter-spacing: 2px; }
        .cert-logo-sub { font-size: 9pt; color: #6b7280; letter-spacing: 3px; text-transform: uppercase; margin-top: 2px; }

        .cert-title-line { border-top: 2px solid #b8860b; border-bottom: 2px solid #b8860b; padding: 4mm 0; margin: 6mm 0; text-align: center; }
        .cert-title { font-family: 'Playfair Display', serif; font-size: 26pt; font-weight: 900; color: #1e3a8a; letter-spacing: 3px; text-transform: uppercase; }

        .cert-presented { text-align: center; font-size: 10pt; color: #6b7280; letter-spacing: 1px; margin-bottom: 5mm; }

        .cert-name { text-align: center; font-family: 'Playfair Display', serif; font-size: 30pt; font-weight: 700; color: #111827; margin: 3mm 0; border-bottom: 1.5px solid #b8860b; display: inline-block; padding: 0 20mm 2mm; }
        .cert-name-wrapper { text-align: center; margin-bottom: 5mm; }

        .cert-body { text-align: center; font-size: 11pt; color: #374151; line-height: 1.8; margin-bottom: 7mm; }
        .cert-body strong { color: #1e3a8a; font-weight: 700; }

        .cert-footer { display: flex; justify-content: space-between; align-items: flex-end; margin-top: 8mm; padding-top: 5mm; border-top: 1px solid #e5e7eb; }
        .cert-sign { text-align: center; min-width: 60mm; }
        .cert-sign .line { border-top: 1.5px solid #374151; margin-bottom: 4px; width: 50mm; }
        .cert-sign .name { font-weight: 700; font-size: 10pt; color: #111827; }
        .cert-sign .role { font-size: 9pt; color: #6b7280; }

        .cert-number-box { background: #f0f4ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 8px 16px; text-align: center; }
        .cert-number-box .label { font-size: 8pt; color: #6b7280; text-transform: uppercase; letter-spacing: 1px; }
        .cert-number-box .number { font-family: monospace; font-size: 12pt; font-weight: 700; color: #1e3a8a; }
    </style>
</head>
<body>
<button class="print-btn" onclick="window.print()">Print Certificate</button>

<div class="cert-page">
    <div class="cert-border-outer"></div>
    <div class="cert-border-inner"></div>
    <div class="cert-corner corner-tl"></div>
    <div class="cert-corner corner-tr"></div>
    <div class="cert-corner corner-bl"></div>
    <div class="cert-corner corner-br"></div>

    <div class="cert-logo">
        <div class="cert-logo-text">SMS Training Academy</div>
        <div class="cert-logo-sub">Professional Training &amp; Development</div>
    </div>

    <div class="cert-title-line">
        <div class="cert-title">Certificate of Completion</div>
    </div>

    <div class="cert-presented">This is to certify that</div>

    <div class="cert-name-wrapper">
        <div class="cert-name">{{ $certificate->participant->participant_name }}</div>
    </div>

    <div class="cert-body">
        @if($certificate->participant->position || $certificate->participant->department)
        <em>{{ $certificate->participant->position }}{{ $certificate->participant->department ? ' — '.$certificate->participant->department : '' }}</em><br>
        @endif
        has successfully completed the training programme

        <br><strong>{{ $certificate->session->course_name }}</strong><br>

        organized by <strong>SMS Training Academy</strong> for<br>
        <strong>{{ $certificate->session->project->company_name }}</strong><br>

        @if($certificate->session->training_date)
        held on <strong>{{ $certificate->session->training_date->format('d F Y') }}
        @if($certificate->session->training_date_end && $certificate->session->training_date_end != $certificate->session->training_date)
        – {{ $certificate->session->training_date_end->format('d F Y') }}
        @endif
        </strong>
        @if($certificate->session->venue)
        at <strong>{{ $certificate->session->venue }}</strong>
        @endif
        @endif
    </div>

    <div class="cert-footer">
        <div class="cert-sign">
            <div class="line"></div>
            <div class="name">SMS Training Academy</div>
            <div class="role">Authorised Signatory</div>
        </div>

        <div class="cert-number-box">
            <div class="label">Certificate No.</div>
            <div class="number">{{ $certificate->certificate_number }}</div>
            <div style="font-size:8.5pt;color:#9ca3af;margin-top:2px;">Issued: {{ $certificate->created_at->format('d M Y') }}</div>
        </div>

        @if($certificate->session->trainer_name)
        <div class="cert-sign">
            <div class="line"></div>
            <div class="name">{{ $certificate->session->trainer_name }}</div>
            <div class="role">Lead Trainer</div>
        </div>
        @else
        <div style="min-width:60mm;"></div>
        @endif
    </div>
</div>
</body>
</html>
