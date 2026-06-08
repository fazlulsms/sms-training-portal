<p>Dear Sir,</p>

<p>Greetings from Sustainable Management System Bangladesh.</p>

<p>
    Please find the proforma invoice
    <strong>{{ $invoice->invoice_number }}</strong>
    for your kind review and necessary action.
</p>

<p>
    Invoice Amount:
    <strong>{{ $invoice->currency }} {{ number_format($invoice->grand_total, 2) }}</strong>
</p>

<p>
    Thank you.
</p>

<p>
    Best regards,<br>
    Sustainable Management System Bangladesh
</p>