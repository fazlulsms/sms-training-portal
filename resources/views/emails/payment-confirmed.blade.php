@extends('emails.layouts.master')
@section('subject-strip') ✅ Payment Confirmed @endsection
@section('subject-theme', 'green')

@section('content')
<p class="em-greeting" style="font-size:16px;font-weight:600;color:#111827;margin:0 0 16px;">Dear <strong>{{ $invoice->client_name }}</strong>,</p>

<p style="margin:0 0 14px;line-height:1.7;color:#374151;">
  We are pleased to confirm that your payment has been received and processed successfully.
  Please find the paid invoice and official money receipt attached to this email.
</p>

{{-- Amount Hero --}}
<div class="em-highlight" style="background:#f0fdf4;border:2px solid #86efac;border-radius:10px;text-align:center;padding:20px 24px;margin:20px 0;">
  <div class="em-highlight-eyebrow" style="font-size:11px;color:#6b7280;text-transform:uppercase;letter-spacing:0.6px;margin-bottom:6px;">Amount Received</div>
  <div class="em-highlight-main" style="font-size:28px;font-weight:900;color:#15803d;margin:4px 0;">{{ $invoice->currency ?? 'BDT' }} {{ number_format($log->amount, 2) }}</div>
</div>

{{-- Payment Summary --}}
<div class="em-info-card" style="background:#f0f6ff;border-left:4px solid #2563eb;border-radius:8px;padding:18px 22px;margin:20px 0;">
  <table style="width:100%;border-collapse:collapse;">
    <tr class="em-row" style="border-bottom:1px solid #dbeafe;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Invoice No.</td>
      <td class="em-val" style="padding:7px 0;color:#111827;font-size:13px;font-weight:700;vertical-align:top;">{{ $invoice->invoice_number }}</td>
    </tr>
    <tr class="em-row" style="border-bottom:1px solid #dbeafe;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Course / Programme</td>
      <td class="em-val" style="padding:7px 0;color:#111827;font-size:13px;font-weight:700;vertical-align:top;">{{ $courseName }}</td>
    </tr>
    <tr class="em-row" style="border-bottom:1px solid #dbeafe;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Amount Received</td>
      <td class="em-val" style="padding:7px 0;font-size:13px;font-weight:700;vertical-align:top;"><strong style="color:#15803d;">{{ $invoice->currency ?? 'BDT' }} {{ number_format($log->amount, 2) }}</strong></td>
    </tr>
    <tr class="em-row" style="border-bottom:1px solid #dbeafe;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Payment Method</td>
      <td class="em-val" style="padding:7px 0;color:#111827;font-size:13px;font-weight:700;vertical-align:top;">{{ $log->payment_method ?? '—' }}</td>
    </tr>
    @if($log->transaction_id)
    <tr class="em-row" style="border-bottom:1px solid #dbeafe;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Transaction ID</td>
      <td class="em-val" style="padding:7px 0;color:#111827;font-size:12px;font-weight:700;vertical-align:top;font-family:'Courier New',monospace;">{{ $log->transaction_id }}</td>
    </tr>
    @endif
    <tr class="em-row" style="border-bottom:1px solid #dbeafe;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Payment Date</td>
      <td class="em-val" style="padding:7px 0;color:#111827;font-size:13px;font-weight:700;vertical-align:top;">{{ $log->payment_date ? \Carbon\Carbon::parse($log->payment_date)->format('d M Y') : now()->format('d M Y') }}</td>
    </tr>
    <tr class="em-row" style="border-bottom:1px solid #dbeafe;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Invoice Total</td>
      <td class="em-val" style="padding:7px 0;color:#111827;font-size:13px;font-weight:700;vertical-align:top;">{{ $invoice->currency ?? 'BDT' }} {{ number_format($invoice->grand_total ?? $invoice->total_amount, 2) }}</td>
    </tr>
    @php $balance = max(0, (float)($invoice->grand_total ?? $invoice->total_amount) - (float)$log->amount); @endphp
    <tr class="em-row" style="border-bottom:none;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Balance Due</td>
      <td class="em-val" style="padding:7px 0;font-size:13px;font-weight:700;vertical-align:top;color:{{ $balance > 0 ? '#b91c1c' : '#15803d' }};">
        {{ $balance > 0 ? ($invoice->currency ?? 'BDT') . ' ' . number_format($balance, 2) : 'Nil — Fully Paid' }}
      </td>
    </tr>
  </table>
</div>

{{-- What happens next --}}
<div class="em-alert em-alert-blue" style="background:#eff6ff;border:1px solid #bfdbfe;color:#1d4ed8;border-radius:8px;padding:14px 18px;margin:16px 0;font-size:13px;line-height:1.6;">
  <strong>📋 What happens next?</strong><br>
  @if($type === 'eLearning')
    🎓 Your course access has been <strong>activated</strong>. Log in to your learner portal to start learning.<br>
    🏆 Upon successful completion, your certificate will be issued automatically.
  @else
    📅 Your attendance is confirmed for the scheduled training session.<br>
    🏆 Your participation certificate will be issued upon completion of the programme.
  @endif
  <br>📎 Your paid invoice and money receipt are attached to this email for your records.
</div>

<div class="em-btn-wrap" style="text-align:center;margin:24px 0;">
  <a href="{{ $loginUrl }}" class="em-btn em-btn-green" style="display:inline-block;background:#15803d;color:#ffffff !important;text-decoration:none;padding:13px 32px;border-radius:8px;font-size:14px;font-weight:700;">🔐 Access Your Account</a>
</div>

<p style="margin:0 0 14px;line-height:1.7;color:#374151;">
  If you have any questions, please reply to this email or contact our training team at <a href="mailto:training@smscert.com" style="color:#2563eb;">training@smscert.com</a>.
</p>
@endsection
