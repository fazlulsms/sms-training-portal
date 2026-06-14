<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>SMS Training Academy</title>
<style>
  /* ===== RESET & BASE ===== */
  * { box-sizing: border-box; }
  body { margin: 0; padding: 0; background: #eef2f7; font-family: 'Segoe UI', Arial, Helvetica, sans-serif; font-size: 14px; color: #1f2937; -webkit-text-size-adjust: 100%; }
  table { border-collapse: collapse; mso-table-lspace: 0; mso-table-rspace: 0; }
  img { border: 0; display: block; }
  a { color: #2563eb; text-decoration: none; }

  /* ===== WRAPPER ===== */
  .em-wrapper { max-width: 620px; margin: 32px auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 24px rgba(15,30,69,.12); }

  /* ===== HEADER ===== */
  .em-header { background: linear-gradient(135deg, #0f1e45 0%, #1e3a8a 100%); padding: 32px 40px; text-align: center; }
  .em-header-icon { font-size: 36px; line-height: 1; margin-bottom: 10px; }
  .em-header-brand { color: #ffffff; font-size: 22px; font-weight: 800; margin: 0 0 4px; letter-spacing: -0.3px; }
  .em-header-sub { color: #93c5fd; font-size: 11px; margin: 0; text-transform: uppercase; letter-spacing: 0.8px; }

  /* ===== SUBJECT STRIP ===== */
  .em-strip { padding: 10px 40px; text-align: center; font-size: 14px; font-weight: 700; letter-spacing: 0.2px; }
  .em-strip-blue   { background: #1e3a8a; color: #ffffff; }
  .em-strip-green  { background: #15803d; color: #ffffff; }
  .em-strip-amber  { background: #b45309; color: #ffffff; }
  .em-strip-red    { background: #b91c1c; color: #ffffff; }
  .em-strip-purple { background: #6d28d9; color: #ffffff; }

  /* ===== BODY ===== */
  .em-body { padding: 32px 40px; }
  .em-body p { margin: 0 0 14px; line-height: 1.7; color: #374151; }

  /* ===== GREETING ===== */
  .em-greeting { font-size: 16px; font-weight: 600; color: #111827; margin: 0 0 16px; }

  /* ===== INFO CARD ===== */
  .em-info-card { background: #f0f6ff; border-left: 4px solid #2563eb; border-radius: 8px; padding: 18px 22px; margin: 20px 0; }
  .em-info-card table { width: 100%; border-collapse: collapse; }
  .em-row { border-bottom: 1px solid #dbeafe; }
  .em-row:last-child { border-bottom: none; }
  .em-label { padding: 7px 0; color: #6b7280; font-size: 13px; font-weight: 600; width: 44%; vertical-align: top; }
  .em-val   { padding: 7px 0; color: #111827; font-size: 13px; font-weight: 700; vertical-align: top; }

  /* ===== CREDENTIALS CARD ===== */
  .em-cred-card { background: #0f1e45; border-radius: 10px; padding: 20px 24px; margin: 20px 0; }
  .em-cred-label { color: #93c5fd; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 2px; }
  .em-cred-val { color: #e2e8f0; font-size: 14px; font-weight: 600; display: block; margin-bottom: 12px; word-break: break-all; }
  .em-cred-pw { color: #fbbf24; font-family: 'Courier New', Courier, monospace; font-size: 16px; font-weight: 700; letter-spacing: 1px; display: inline-block; background: rgba(255,255,255,.08); padding: 6px 14px; border-radius: 6px; border: 1px solid rgba(251,191,36,.3); }

  /* ===== BUTTONS ===== */
  .em-btn-wrap { text-align: center; margin: 24px 0; }
  .em-btn { display: inline-block; background: #2563eb; color: #ffffff !important; text-decoration: none; padding: 13px 32px; border-radius: 8px; font-size: 14px; font-weight: 700; letter-spacing: 0.2px; mso-padding-alt: 0; }
  .em-btn-green  { background: #15803d !important; }
  .em-btn-amber  { background: #b45309 !important; }
  .em-btn-red    { background: #b91c1c !important; }

  /* ===== ALERTS ===== */
  .em-alert { border-radius: 8px; padding: 14px 18px; margin: 16px 0; font-size: 13px; line-height: 1.6; }
  .em-alert-green  { background: #f0fdf4; border: 1px solid #86efac; color: #15803d; }
  .em-alert-yellow { background: #fffbeb; border: 1px solid #fbbf24; color: #92400e; }
  .em-alert-blue   { background: #eff6ff; border: 1px solid #bfdbfe; color: #1d4ed8; }
  .em-alert-red    { background: #fef2f2; border: 1px solid #fca5a5; color: #b91c1c; }

  /* ===== HIGHLIGHT / HERO BOX ===== */
  .em-highlight { background: #f0f6ff; border: 2px solid #bfdbfe; border-radius: 10px; text-align: center; padding: 20px 24px; margin: 20px 0; }
  .em-highlight-eyebrow { font-size: 11px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 6px; }
  .em-highlight-main { font-size: 20px; font-weight: 900; color: #1e3a8a; margin: 4px 0; }
  .em-highlight-sub { font-size: 13px; color: #6b7280; margin-top: 4px; }

  /* ===== DIVIDER ===== */
  .em-divider { border: none; border-top: 1px solid #e5e7eb; margin: 24px 0; }

  /* ===== BADGES ===== */
  .em-badge-green  { display: inline-block; background: #dcfce7; color: #15803d; padding: 2px 10px; border-radius: 12px; font-size: 12px; font-weight: 700; }
  .em-badge-amber  { display: inline-block; background: #fef3c7; color: #b45309; padding: 2px 10px; border-radius: 12px; font-size: 12px; font-weight: 700; }
  .em-badge-red    { display: inline-block; background: #fee2e2; color: #b91c1c; padding: 2px 10px; border-radius: 12px; font-size: 12px; font-weight: 700; }
  .em-badge-blue   { display: inline-block; background: #dbeafe; color: #1e3a8a; padding: 2px 10px; border-radius: 12px; font-size: 12px; font-weight: 700; }

  /* ===== SECTION TITLE ===== */
  .em-section-title { font-size: 13px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.6px; color: #1e3a8a; margin: 20px 0 10px; padding-bottom: 6px; border-bottom: 2px solid #dbeafe; }

  /* ===== FOOTER ===== */
  .em-footer { background: linear-gradient(135deg, #0f1e45 0%, #1e3a8a 100%); padding: 24px 40px; text-align: center; }
  .em-footer-brand { color: #e2e8f0; font-size: 13px; font-weight: 700; margin: 0 0 6px; }
  .em-footer-address { color: #93c5fd; font-size: 11px; line-height: 1.8; margin: 0 0 8px; }
  .em-footer-contact a { color: #bfdbfe; font-size: 11px; text-decoration: none; }
  .em-footer-divider { border: none; border-top: 1px solid rgba(255,255,255,.15); margin: 12px 0; }
  .em-footer-notice { color: #64748b; font-size: 10px; margin: 0; line-height: 1.6; }

  /* ===== RESPONSIVE ===== */
  @media only screen and (max-width: 600px) {
    .em-wrapper { margin: 0 !important; border-radius: 0 !important; }
    .em-header { padding: 24px 20px !important; }
    .em-body { padding: 24px 20px !important; }
    .em-footer { padding: 20px !important; }
    .em-strip { padding: 10px 20px !important; }
    .em-btn { padding: 12px 24px !important; font-size: 13px !important; }
    .em-label { width: 40% !important; }
  }
</style>
</head>
<body>
<div class="em-wrapper" style="max-width:620px;margin:32px auto;background:#ffffff;border-radius:12px;overflow:hidden;">

  {{-- Header --}}
  <div class="em-header" style="background:linear-gradient(135deg,#0f1e45 0%,#1e3a8a 100%);padding:32px 40px;text-align:center;">
    <div class="em-header-icon" style="font-size:36px;line-height:1;margin-bottom:10px;">🎓</div>
    <div class="em-header-brand" style="color:#ffffff;font-size:22px;font-weight:800;margin:0 0 4px;letter-spacing:-0.3px;">SMS Training Academy</div>
    <div class="em-header-sub" style="color:#93c5fd;font-size:11px;margin:0;text-transform:uppercase;letter-spacing:0.8px;">Powered by Sustainable Management System Inc.</div>
  </div>

  {{-- Subject Strip --}}
  @hasSection('subject-strip')
  @php $__stripTheme = trim($__env->yieldContent('subject-theme', 'blue')); @endphp
  <div class="em-strip em-strip-{{ $__stripTheme }}" style="padding:10px 40px;text-align:center;font-size:14px;font-weight:700;
    @if($__stripTheme === 'green') background:#15803d;color:#ffffff;
    @elseif($__stripTheme === 'amber') background:#b45309;color:#ffffff;
    @elseif($__stripTheme === 'red') background:#b91c1c;color:#ffffff;
    @elseif($__stripTheme === 'purple') background:#6d28d9;color:#ffffff;
    @else background:#1e3a8a;color:#ffffff;
    @endif">
    @yield('subject-strip')
  </div>
  @endif

  {{-- Body --}}
  <div class="em-body" style="padding:32px 40px;">
    @yield('content')

    {{-- Closing --}}
    <hr class="em-divider" style="border:none;border-top:1px solid #e5e7eb;margin:28px 0;">
    <p style="margin:0 0 4px;line-height:1.7;color:#374151;">Best regards,</p>
    <p style="margin:0;line-height:1.7;color:#374151;"><strong style="color:#1e3a8a;">SMS Training Academy Team</strong><br>
    <span style="color:#6b7280;font-size:13px;">Sustainable Management System Inc.</span></p>
  </div>

  {{-- Footer --}}
  <div class="em-footer" style="background:linear-gradient(135deg,#0f1e45 0%,#1e3a8a 100%);padding:24px 40px;text-align:center;">
    <p class="em-footer-brand" style="color:#e2e8f0;font-size:13px;font-weight:700;margin:0 0 6px;">SMS Training Academy &middot; Sustainable Management System Inc.</p>
    <p class="em-footer-address" style="color:#93c5fd;font-size:11px;line-height:1.8;margin:0 0 6px;">
      277 Cherry Street, Suite-12N, New York, NY, USA
    </p>
    <p style="margin:0 0 8px;">
      <a href="mailto:training@smscert.com" style="color:#bfdbfe;font-size:11px;text-decoration:none;">training@smscert.com</a>
      &nbsp;&middot;&nbsp;
      <a href="mailto:info@smscert.com" style="color:#bfdbfe;font-size:11px;text-decoration:none;">info@smscert.com</a>
      &nbsp;&middot;&nbsp;
      <a href="https://training.smscert.com" style="color:#bfdbfe;font-size:11px;text-decoration:none;">training.smscert.com</a>
    </p>
    <hr class="em-footer-divider" style="border:none;border-top:1px solid rgba(255,255,255,.15);margin:12px 0;">
    <p class="em-footer-notice" style="color:#64748b;font-size:10px;margin:0;line-height:1.6;">
      This is an automated message from SMS Training Academy. Please do not reply directly to this email.<br>
      If you have questions, contact us at <a href="mailto:training@smscert.com" style="color:#64748b;">training@smscert.com</a>
    </p>
  </div>

</div>
</body>
</html>
