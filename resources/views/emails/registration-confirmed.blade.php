@extends('emails.layouts.master')
@section('subject-strip') ✅ Registration Confirmed @endsection
@section('subject-theme', 'blue')

@section('content')
<p class="em-greeting" style="font-size:16px;font-weight:600;color:#111827;margin:0 0 16px;">Dear <strong>{{ $invoice->client_name }}</strong>,</p>

<p style="margin:0 0 14px;line-height:1.7;color:#374151;">
  Thank you for registering with <strong>SMS Training Academy</strong>. Your enrollment has been received and confirmed.
  Please find your invoice attached to this email as a PDF.
</p>

{{-- Enrollment Summary --}}
<div class="em-info-card" style="background:#f0f6ff;border-left:4px solid #2563eb;border-radius:8px;padding:18px 22px;margin:20px 0;">
  <table style="width:100%;border-collapse:collapse;">
    <tr class="em-row" style="border-bottom:1px solid #dbeafe;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Invoice No.</td>
      <td class="em-val" style="padding:7px 0;color:#111827;font-size:13px;font-weight:700;vertical-align:top;">{{ $invoice->invoice_number }}</td>
    </tr>
    <tr class="em-row" style="border-bottom:1px solid #dbeafe;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Programme</td>
      <td class="em-val" style="padding:7px 0;color:#111827;font-size:13px;font-weight:700;vertical-align:top;">{{ $courseName }}</td>
    </tr>
    @if($scheduleInfo)
    <tr class="em-row" style="border-bottom:1px solid #dbeafe;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Schedule / Batch</td>
      <td class="em-val" style="padding:7px 0;color:#111827;font-size:13px;font-weight:700;vertical-align:top;">{{ $scheduleInfo }}</td>
    </tr>
    @endif
    <tr class="em-row" style="border-bottom:1px solid #dbeafe;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Training Mode</td>
      <td class="em-val" style="padding:7px 0;color:#111827;font-size:13px;font-weight:700;vertical-align:top;">{{ $invoice->training_method_venue ?? $type }}</td>
    </tr>
    <tr class="em-row" style="border-bottom:1px solid #dbeafe;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Total Payable</td>
      <td class="em-val" style="padding:7px 0;color:#111827;font-size:15px;font-weight:700;vertical-align:top;">{{ $invoice->currency ?? 'BDT' }} {{ number_format($invoice->total_amount, 2) }}</td>
    </tr>
    <tr class="em-row" style="border-bottom:none;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Payment Status</td>
      <td class="em-val" style="padding:7px 0;font-size:13px;vertical-align:top;">
        @php $ps = strtolower($invoice->payment_status ?? 'pending'); @endphp
        @if(in_array($ps, ['paid','manual_approved']))
          <span class="em-badge-green" style="display:inline-block;background:#dcfce7;color:#15803d;padding:2px 10px;border-radius:12px;font-size:12px;font-weight:700;">Paid</span>
        @else
          <span class="em-badge-amber" style="display:inline-block;background:#fef3c7;color:#b45309;padding:2px 10px;border-radius:12px;font-size:12px;font-weight:700;">{{ ucfirst($invoice->payment_status ?? 'Pending') }}</span>
        @endif
      </td>
    </tr>
  </table>
</div>

@if($tempPassword)
{{-- New account credentials --}}
<div class="em-cred-card" style="background:#0f1e45;border-radius:10px;padding:20px 24px;margin:20px 0;">
  <p style="margin:0 0 14px;color:#bfdbfe;font-size:13px;font-weight:700;">🎓 Your learner account has been created. Use the credentials below to log in:</p>
  <span class="em-cred-label" style="color:#93c5fd;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;display:block;margin-bottom:2px;">Portal URL</span>
  <span class="em-cred-val" style="color:#e2e8f0;font-size:14px;font-weight:600;display:block;margin-bottom:12px;"><a href="{{ url('/login') }}" style="color:#bfdbfe;">{{ url('/login') }}</a></span>
  <span class="em-cred-label" style="color:#93c5fd;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;display:block;margin-bottom:2px;">Email Address</span>
  <span class="em-cred-val" style="color:#e2e8f0;font-size:14px;font-weight:600;display:block;margin-bottom:12px;">{{ $invoice->client_email }}</span>
  <span class="em-cred-label" style="color:#93c5fd;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;display:block;margin-bottom:6px;">Temporary Password</span>
  <span class="em-cred-pw" style="color:#fbbf24;font-family:'Courier New',Courier,monospace;font-size:16px;font-weight:700;letter-spacing:1px;display:inline-block;background:rgba(255,255,255,.08);padding:6px 14px;border-radius:6px;border:1px solid rgba(251,191,36,.3);">{{ $tempPassword }}</span>
  <p style="margin:12px 0 0;color:#64748b;font-size:11px;">Please change your password after your first login for security.</p>
</div>
<div class="em-btn-wrap" style="text-align:center;margin:24px 0;">
  <a href="{{ $loginUrl }}" class="em-btn" style="display:inline-block;background:#2563eb;color:#ffffff !important;text-decoration:none;padding:13px 32px;border-radius:8px;font-size:14px;font-weight:700;">🔐 Log In to Your Account</a>
</div>
@endif

<p style="margin:0 0 14px;line-height:1.7;color:#374151;">
  If you have any questions about your registration or payment, please reply to this email or contact
  our training team at <a href="mailto:training@smscert.com" style="color:#2563eb;">training@smscert.com</a>.
</p>
<p style="margin:0 0 14px;line-height:1.7;color:#374151;">We look forward to welcoming you to the programme.</p>
@endsection
