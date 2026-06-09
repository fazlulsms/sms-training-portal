<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Payment Received</title>
<style>
  body { font-family: Arial, sans-serif; font-size: 14px; color: #1f2937; background: #f3f4f6; margin: 0; padding: 0; }
  .wrapper { max-width: 620px; margin: 32px auto; background: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,.08); }
  .header  { background: #14532d; padding: 28px 32px; text-align: center; }
  .header h1 { color: #fff; font-size: 20px; margin: 0; }
  .header p  { color: #86efac; font-size: 12px; margin: 4px 0 0; letter-spacing: .5px; text-transform: uppercase; }
  .body    { padding: 32px; }
  .body p  { margin: 0 0 14px; line-height: 1.65; }
  .paid-banner {
    background: #f0fdf4;
    border: 2px solid #86efac;
    border-radius: 8px;
    text-align: center;
    padding: 14px 20px;
    margin: 18px 0;
  }
  .paid-banner .amount { font-size: 26px; font-weight: 900; color: #15803d; }
  .paid-banner .label  { font-size: 11px; color: #6b7280; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 4px; }
  .info-card { background: #f8fafc; border-left: 4px solid #15803d; border-radius: 6px; padding: 16px 20px; margin: 18px 0; }
  .info-card table { width: 100%; border-collapse: collapse; }
  .info-card td { padding: 5px 0; font-size: 13px; }
  .info-card td:first-child { color: #6b7280; width: 44%; font-weight: 600; }
  .info-card td:last-child  { color: #111827; font-weight: 700; }
  .next-box { background: #eff6ff; border-left: 4px solid #1d4ed8; border-radius: 6px; padding: 14px 18px; margin: 18px 0; }
  .next-box p { margin: 0 0 6px; font-size: 13px; color: #1e3a8a; }
  .next-box p:last-child { margin: 0; }
  .btn { display: inline-block; background: #15803d; color: #ffffff !important; text-decoration: none; padding: 12px 28px; border-radius: 6px; font-weight: 700; font-size: 14px; margin: 8px 0; }
  .divider { border: none; border-top: 1px solid #e5e7eb; margin: 24px 0; }
  .footer { background: #14532d; padding: 18px 32px; text-align: center; }
  .footer p { color: #86efac; font-size: 11px; margin: 0; line-height: 1.6; }
  .footer strong { color: #d1fae5; }
</style>
</head>
<body>
<div class="wrapper">

  <!-- Header -->
  <div class="header">
    <h1>✅ Payment Received — Thank You!</h1>
    <p>SMS Training Services · Sustainable Management System Inc.</p>
  </div>

  <!-- Body -->
  <div class="body">
    <p>Dear <strong>{{ $invoice->client_name }}</strong>,</p>
    <p>
      We are pleased to confirm that your payment has been received and processed successfully.
      Please find the paid invoice and official money receipt attached to this email.
    </p>

    <!-- Amount Banner -->
    <div class="paid-banner">
      <div class="label">Amount Received</div>
      <div class="amount">
        {{ $invoice->currency ?? 'BDT' }} {{ number_format($log->amount, 2) }}
      </div>
    </div>

    <!-- Payment Summary -->
    <div class="info-card">
      <table>
        <tr>
          <td>Invoice No.</td>
          <td>{{ $invoice->invoice_number }}</td>
        </tr>
        <tr>
          <td>Course / Programme</td>
          <td>{{ $courseName }}</td>
        </tr>
        <tr>
          <td>Amount Received</td>
          <td><strong style="color:#15803d;">{{ $invoice->currency ?? 'BDT' }} {{ number_format($log->amount, 2) }}</strong></td>
        </tr>
        <tr>
          <td>Payment Method</td>
          <td>{{ $log->payment_method ?? '—' }}</td>
        </tr>
        @if($log->transaction_id)
        <tr>
          <td>Transaction ID</td>
          <td style="font-family:monospace;font-size:12px;">{{ $log->transaction_id }}</td>
        </tr>
        @endif
        <tr>
          <td>Payment Date</td>
          <td>{{ $log->payment_date ? \Carbon\Carbon::parse($log->payment_date)->format('d M Y') : now()->format('d M Y') }}</td>
        </tr>
        <tr>
          <td>Invoice Total</td>
          <td>{{ $invoice->currency ?? 'BDT' }} {{ number_format($invoice->grand_total ?? $invoice->total_amount, 2) }}</td>
        </tr>
        @php
          $balance = max(0, (float)($invoice->grand_total ?? $invoice->total_amount) - (float)$log->amount);
        @endphp
        <tr>
          <td>Balance Due</td>
          <td style="color:{{ $balance > 0 ? '#dc2626' : '#15803d' }};font-weight:700;">
            {{ $balance > 0 ? ($invoice->currency ?? 'BDT') . ' ' . number_format($balance, 2) : 'Nil — Fully Paid' }}
          </td>
        </tr>
      </table>
    </div>

    <!-- Next Steps -->
    <div class="next-box">
      <p><strong>📋 What happens next?</strong></p>
      @if($type === 'eLearning')
        <p>🎓 Your course access has been <strong>activated</strong>. Log in to your learner portal to start learning.</p>
        <p>🏆 Upon successful completion, your certificate will be issued automatically.</p>
      @else
        <p>📅 Your attendance is confirmed for the scheduled training session.</p>
        <p>🏆 Your participation certificate will be issued upon completion of the programme.</p>
      @endif
      <p>📎 Your paid invoice and money receipt are attached to this email for your records.</p>
    </div>

    @if($invoice->client_email)
    <div style="text-align:center;">
      <a href="{{ $loginUrl }}" class="btn">🔐 Access Your Account</a>
    </div>
    @endif

    <hr class="divider">

    <p>If you have any questions, please reply to this email or contact our training team.</p>
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
      E-mail: elearning@smscert.com · Website: www.smscert.com
    </p>
  </div>

</div>
</body>
</html>
