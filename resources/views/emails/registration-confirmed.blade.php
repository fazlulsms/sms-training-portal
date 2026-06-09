<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Registration Confirmed</title>
<style>
  body { font-family: Arial, sans-serif; font-size: 14px; color: #1f2937; background: #f3f4f6; margin: 0; padding: 0; }
  .wrapper { max-width: 620px; margin: 32px auto; background: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,.08); }
  .header  { background: #0f1e45; padding: 28px 32px; text-align: center; }
  .header h1 { color: #fff; font-size: 20px; margin: 0; }
  .header p  { color: #93c5fd; font-size: 12px; margin: 4px 0 0; letter-spacing: .5px; text-transform: uppercase; }
  .body    { padding: 32px; }
  .body p  { margin: 0 0 14px; line-height: 1.65; }
  .info-card { background: #f0f6ff; border-left: 4px solid #1d4ed8; border-radius: 6px; padding: 16px 20px; margin: 20px 0; }
  .info-card table { width: 100%; border-collapse: collapse; }
  .info-card td  { padding: 5px 0; font-size: 13px; }
  .info-card td:first-child { color: #6b7280; width: 44%; font-weight: 600; }
  .info-card td:last-child  { color: #111827; font-weight: 700; }
  .badge-paid    { background: #dcfce7; color: #15803d; padding: 2px 10px; border-radius: 12px; font-size: 12px; font-weight: 700; }
  .badge-pending { background: #fef3c7; color: #b45309; padding: 2px 10px; border-radius: 12px; font-size: 12px; font-weight: 700; }
  .cred-box { background: #1e293b; color: #e2e8f0; border-radius: 8px; padding: 16px 20px; margin: 20px 0; font-size: 13px; }
  .cred-box strong { color: #93c5fd; }
  .btn { display: inline-block; background: #1d4ed8; color: #ffffff !important; text-decoration: none; padding: 12px 28px; border-radius: 6px; font-weight: 700; font-size: 14px; margin: 8px 0; }
  .divider { border: none; border-top: 1px solid #e5e7eb; margin: 24px 0; }
  .footer { background: #0f1e45; padding: 18px 32px; text-align: center; }
  .footer p { color: #94a3b8; font-size: 11px; margin: 0; line-height: 1.6; }
  .footer strong { color: #cbd5e1; }
</style>
</head>
<body>
<div class="wrapper">

  <!-- Header -->
  <div class="header">
    <h1>✅ Registration Confirmed</h1>
    <p>SMS Training Services · Sustainable Management System Inc.</p>
  </div>

  <!-- Body -->
  <div class="body">
    <p>Dear <strong>{{ $invoice->client_name }}</strong>,</p>
    <p>
      Thank you for registering with SMS Training Services. Your enrollment has been received and confirmed.
      Please find your invoice attached to this email as a PDF.
    </p>

    <!-- Enrollment Summary -->
    <div class="info-card">
      <table>
        <tr>
          <td>Invoice No.</td>
          <td>{{ $invoice->invoice_number }}</td>
        </tr>
        <tr>
          <td>Programme</td>
          <td>{{ $courseName }}</td>
        </tr>
        @if($scheduleInfo)
        <tr>
          <td>Schedule / Batch</td>
          <td>{{ $scheduleInfo }}</td>
        </tr>
        @endif
        <tr>
          <td>Training Mode</td>
          <td>{{ $invoice->training_method_venue ?? $type }}</td>
        </tr>
        <tr>
          <td>Total Payable</td>
          <td><strong style="font-size:15px;">{{ $invoice->currency ?? 'BDT' }} {{ number_format($invoice->total_amount, 2) }}</strong></td>
        </tr>
        <tr>
          <td>Payment Status</td>
          <td>
            @php $ps = strtolower($invoice->payment_status ?? 'pending'); @endphp
            @if(in_array($ps, ['paid','manual_approved']))
              <span class="badge-paid">Paid</span>
            @else
              <span class="badge-pending">{{ ucfirst($invoice->payment_status ?? 'Pending') }}</span>
            @endif
          </td>
        </tr>
      </table>
    </div>

    @if($tempPassword)
    <!-- New account credentials -->
    <div class="cred-box">
      <p style="margin:0 0 8px;">🎓 <strong>Your learner account has been created.</strong> Use the credentials below to log in:</p>
      <p style="margin:0;">Email: <strong>{{ $invoice->client_email }}</strong></p>
      <p style="margin:4px 0 0;">Password: <strong>{{ $tempPassword }}</strong></p>
      <p style="margin:8px 0 0;font-size:11px;color:#94a3b8;">Please change your password after your first login.</p>
    </div>
    <div style="text-align:center;">
      <a href="{{ $loginUrl }}" class="btn">🔐 Log In to Your Account</a>
    </div>
    @endif

    <hr class="divider">

    <p>
      If you have any questions about your registration or payment, please reply to this email or contact
      our training team directly.
    </p>
    <p>We look forward to welcoming you to the programme. 🎓</p>
    <p style="margin-top:24px;">
      Best regards,<br>
      <strong>SMS Training Services Team</strong><br>
      Sustainable Management System Inc.
    </p>
  </div>

  <!-- Footer -->
  <div class="footer">
    <p>
      <strong>SMS Training Services</strong> · Sustainable Management System Inc.<br>
      This email was sent automatically upon registration. Please do not reply if this was not you.
    </p>
  </div>

</div>
</body>
</html>
