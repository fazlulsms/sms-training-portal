@php $headerTitle = '⏳ Payment Pending'; $accentColor = '#d97706'; $headerColor = '#78350f'; @endphp
@include('emails.partials.participant-header')

<p>Dear <strong>{{ $enrollment->full_name ?? $enrollment->participant_name }}</strong>,</p>
<p>Thank you for registering. Your enrollment is <strong>pending payment</strong>. Please complete the payment to confirm your seat.</p>

<div class="info-card">
  <table>
    <tr><td>Course</td><td>{{ $courseName }}</td></tr>
    <tr><td>Amount Due</td><td><strong style="color:#d97706;">{{ $currency ?? 'BDT' }} {{ number_format($amount, 2) }}</strong></td></tr>
    @if($invoice?->invoice_number)
    <tr><td>Invoice No.</td><td>{{ $invoice->invoice_number }}</td></tr>
    @endif
    <tr><td>Payment Methods</td><td>Bank Transfer · bKash · Nagad · Card</td></tr>
  </table>
</div>

<div class="alert-yellow">
  <strong>⚠ Your seat is reserved</strong> but will only be confirmed once payment is received. Please complete payment as soon as possible.
</div>

<p>For payment details and bank information, please reply to this email or contact us at <a href="mailto:elearning@smscert.com">elearning@smscert.com</a></p>

@include('emails.partials.participant-footer')
