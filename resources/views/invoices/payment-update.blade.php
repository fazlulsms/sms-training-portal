@extends('layouts.app')
@section('page-title', 'Update Payment — ' . $invoice->invoice_number)

@section('content')
<style>
*, *::before, *::after { box-sizing: border-box; }

.pu-wrap   { max-width: 780px; margin: 0 auto; padding: 24px 16px 40px; }

/* Page header */
.pu-hdr    { display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:22px; }
.pu-hdr-left h1 { font-size:22px; font-weight:900; color:#111827; margin:0; }
.pu-hdr-left p  { font-size:13px; color:#6b7280; margin:4px 0 0; }

/* Invoice summary card (read-only, blue border) */
.inv-summary {
    background:#eff6ff;
    border:2px solid #bfdbfe;
    border-radius:14px;
    padding:20px 24px;
    margin-bottom:22px;
}
.inv-summary-grid {
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:14px;
    margin-bottom:12px;
}
@media(max-width:600px){ .inv-summary-grid { grid-template-columns:1fr 1fr; } }
.inv-sum-item label { font-size:11px; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:.5px; display:block; margin-bottom:3px; }
.inv-sum-item span  { font-size:14px; font-weight:700; color:#1e3a8a; }
.inv-sum-item .total-amount { font-size:20px; color:#1e3a8a; }

/* Payment fields card (green highlight) */
.pay-card {
    background:#f0fdf4;
    border:2px solid #86efac;
    border-radius:14px;
    overflow:hidden;
    margin-bottom:20px;
}
.pay-card-head {
    background:#15803d;
    padding:14px 22px;
    display:flex; align-items:center; gap:10px;
}
.pay-card-head h2 { color:#fff; font-size:15px; font-weight:800; margin:0; }
.pay-card-head p  { color:#bbf7d0; font-size:12px; margin:2px 0 0; }
.pay-card-body    { padding:22px 24px; }

.fg  { display:grid; gap:14px; margin-bottom:14px; }
.fg-2{ grid-template-columns:1fr 1fr; }
.fg-3{ grid-template-columns:1fr 1fr 1fr; }
@media(max-width:640px){ .fg-2,.fg-3 { grid-template-columns:1fr; } }

.field       { display:flex; flex-direction:column; gap:5px; }
.field label { font-size:12.5px; font-weight:700; color:#166534; }
.field input,
.field select,
.field textarea {
    width:100%; padding:10px 13px;
    border:2px solid #86efac; border-radius:9px;
    font-size:14px; font-family:inherit; color:#111827;
    background:#fff;
    outline:none; appearance:none;
    transition:border-color .14s, box-shadow .14s;
}
.field input:focus,
.field select:focus,
.field textarea:focus {
    border-color:#16a34a;
    box-shadow:0 0 0 3px rgba(22,163,74,.12);
}
.field textarea { resize:vertical; min-height:64px; }

/* Status selector buttons */
.status-btns { display:flex; gap:10px; flex-wrap:wrap; margin-bottom:6px; }
.status-btn  {
    flex:1; min-width:100px;
    padding:12px 10px;
    border:2px solid #e5e7eb; border-radius:10px;
    text-align:center; cursor:pointer;
    font-weight:700; font-size:13px; color:#374151;
    background:#fff; transition:.14s;
    user-select:none;
}
.status-btn:hover { border-color:#9ca3af; }
.status-btn.active-unpaid  { background:#fee2e2; border-color:#f87171; color:#991b1b; }
.status-btn.active-partial { background:#fffbeb; border-color:#fbbf24; color:#92400e; }
.status-btn.active-paid    { background:#dcfce7; border-color:#4ade80; color:#166534; }
.status-btn .icon          { font-size:18px; display:block; margin-bottom:3px; }

/* Amount highlight */
.amount-highlight {
    background:#fff;
    border:2px solid #4ade80;
    border-radius:10px;
    padding:10px 14px;
    display:flex; align-items:center; gap:10px;
}
.amount-highlight .currency-tag {
    background:#15803d; color:#fff;
    padding:6px 12px; border-radius:6px;
    font-weight:800; font-size:13px; white-space:nowrap;
}
.amount-highlight input {
    border:none !important;
    background:transparent !important;
    font-size:22px !important;
    font-weight:900 !important;
    color:#15803d !important;
    padding:0 !important;
    flex:1;
    box-shadow:none !important;
}
.amount-highlight input:focus { outline:none; }

/* Method grid */
.method-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:8px; }
@media(max-width:640px){ .method-grid { grid-template-columns:repeat(2,1fr); } }
.method-opt  {
    border:2px solid #e5e7eb; border-radius:9px;
    padding:9px 6px; text-align:center; cursor:pointer;
    font-size:12px; font-weight:700; color:#374151; background:#fff;
    user-select:none; transition:.14s;
}
.method-opt:hover  { border-color:#9ca3af; }
.method-opt.active { background:#dcfce7; border-color:#4ade80; color:#15803d; }
.method-opt .micon { font-size:18px; display:block; margin-bottom:3px; }

/* Log card */
.log-card {
    background:#fff;
    border:1px solid #e5e7eb;
    border-radius:14px;
    overflow:hidden;
    margin-bottom:20px;
}
.log-card-head { background:#f8fafc; padding:12px 18px; border-bottom:1px solid #e5e7eb; }
.log-card-head h3 { font-size:14px; font-weight:800; color:#374151; margin:0; }
.log-table { width:100%; border-collapse:collapse; font-size:13px; }
.log-table th { padding:9px 14px; text-align:left; font-weight:700; color:#6b7280; background:#f9fafb; border-bottom:1px solid #e5e7eb; }
.log-table td { padding:9px 14px; border-bottom:1px solid #f3f4f6; }
.log-table tr:last-child td { border-bottom:none; }

/* Buttons */
.btn-row   { display:flex; gap:12px; align-items:center; justify-content:flex-end; padding:6px 0 0; }
.btn-save  { background:#15803d; color:#fff; border:none; padding:13px 32px; border-radius:10px; font-weight:800; font-size:15px; cursor:pointer; display:inline-flex; align-items:center; gap:8px; }
.btn-save:hover  { background:#166534; }
.btn-cancel{ background:#f3f4f6; color:#374151; border:1px solid #e5e7eb; padding:13px 24px; border-radius:10px; font-weight:700; font-size:14px; text-decoration:none; display:inline-flex; align-items:center; }
.btn-cancel:hover { background:#e9ecf0; }
</style>

<div class="pu-wrap">

    {{-- Header --}}
    <div class="pu-hdr">
        <div class="pu-hdr-left">
            <h1>💳 Update Payment</h1>
            <p>Invoice {{ $invoice->invoice_number }} · {{ $invoice->client_name }}</p>
        </div>
        <a href="/admin/invoices/view/{{ $invoice->id }}" class="btn-cancel">← Back</a>
    </div>

    @if(session('error'))
    <div style="background:#fee2e2; border:1px solid #fca5a5; color:#991b1b; padding:12px 16px; border-radius:8px; margin-bottom:16px; font-weight:600;">
        ⚠ {{ session('error') }}
    </div>
    @endif

    {{-- Invoice summary (read-only) --}}
    <div class="inv-summary">
        <div class="inv-summary-grid">
            <div class="inv-sum-item">
                <label>Invoice No.</label>
                <span>{{ $invoice->invoice_number }}</span>
            </div>
            <div class="inv-sum-item">
                <label>Client</label>
                <span>{{ $invoice->client_name }}</span>
            </div>
            <div class="inv-sum-item">
                <label>Training</label>
                <span>{{ Str::limit($invoice->training_name ?? '—', 40) }}</span>
            </div>
            <div class="inv-sum-item">
                <label>Invoice Date</label>
                <span>{{ $invoice->invoice_date ? \Carbon\Carbon::parse($invoice->invoice_date)->format('d M Y') : '—' }}</span>
            </div>
            <div class="inv-sum-item">
                <label>Current Status</label>
                @php $ps = $invoice->payment_status ?? 'Unpaid'; @endphp
                <span style="padding:2px 10px; border-radius:20px; font-size:12px;
                    {{ $ps === 'Paid' ? 'background:#dcfce7; color:#166534;' : ($ps === 'Partial' ? 'background:#fffbeb; color:#92400e;' : 'background:#fee2e2; color:#991b1b;') }}">
                    {{ $ps }}
                </span>
            </div>
            <div class="inv-sum-item">
                <label>Total Payable</label>
                <span class="total-amount">{{ $invoice->currency ?? 'BDT' }} {{ number_format($invoice->grand_total ?? $invoice->total_amount ?? 0, 2) }}</span>
            </div>
        </div>
        @php
            $balance = max(0, (float)($invoice->grand_total ?? $invoice->total_amount ?? 0) - (float)($invoice->amount_paid ?? 0));
        @endphp
        <div style="font-size:13px; color:#1e3a8a;">
            Already Paid: <strong>{{ $invoice->currency ?? 'BDT' }} {{ number_format($invoice->amount_paid ?? 0, 2) }}</strong>
            &nbsp;·&nbsp;
            Balance Due: <strong style="{{ $balance > 0 ? 'color:#dc2626;' : 'color:#15803d;' }}">
                {{ $balance > 0 ? ($invoice->currency ?? 'BDT') . ' ' . number_format($balance, 2) : 'Fully Paid ✓' }}
            </strong>
        </div>
    </div>

    {{-- Payment form --}}
    <form action="/admin/invoices/payment/{{ $invoice->id }}" method="POST" id="payForm">
    @csrf

    <div class="pay-card">
        <div class="pay-card-head">
            <div>
                <h2>🟢 Payment Details</h2>
                <p>Fill in the payment information below and click Save</p>
            </div>
        </div>
        <div class="pay-card-body">

            {{-- Status selector --}}
            <div class="field" style="margin-bottom:18px;">
                <label>Payment Status <span style="color:#dc2626;">*</span></label>
                <div class="status-btns" id="statusBtns">
                    <div class="status-btn {{ ($invoice->payment_status ?? 'Unpaid') === 'Unpaid'  ? 'active-unpaid'  : '' }}" data-val="Unpaid">
                        <span class="icon">❌</span>Unpaid
                    </div>
                    <div class="status-btn {{ ($invoice->payment_status ?? 'Unpaid') === 'Partial' ? 'active-partial' : '' }}" data-val="Partial">
                        <span class="icon">🟡</span>Partial
                    </div>
                    <div class="status-btn {{ ($invoice->payment_status ?? 'Unpaid') === 'Paid'    ? 'active-paid'    : '' }}" data-val="Paid">
                        <span class="icon">✅</span>Paid
                    </div>
                </div>
                <input type="hidden" name="payment_status" id="paymentStatusInput"
                       value="{{ old('payment_status', $invoice->payment_status ?? 'Unpaid') }}">
            </div>

            {{-- Amount --}}
            <div class="field" style="margin-bottom:18px;">
                <label>Amount Paid <span style="color:#dc2626;">*</span></label>
                <div class="amount-highlight">
                    <span class="currency-tag">{{ $invoice->currency ?? 'BDT' }}</span>
                    <input type="number" name="amount_paid" id="amountPaidInput"
                           value="{{ old('amount_paid', $invoice->amount_paid ?? 0) }}"
                           step="0.01" min="0" placeholder="0.00">
                </div>
                <small style="color:#166534; font-size:12px;">
                    Total payable: {{ $invoice->currency ?? 'BDT' }} {{ number_format($invoice->grand_total ?? $invoice->total_amount ?? 0, 2) }}
                    &nbsp;·&nbsp;
                    <a href="#" id="fillFullAmount" style="color:#15803d; font-weight:700;">Fill full amount</a>
                </small>
            </div>

            {{-- Payment method --}}
            <div class="field" style="margin-bottom:18px;">
                <label>Payment Method</label>
                @php
                    $methods = [
                        ['val'=>'Cash',          'icon'=>'💵', 'label'=>'Cash'],
                        ['val'=>'Bank Transfer',  'icon'=>'🏦', 'label'=>'Bank Transfer'],
                        ['val'=>'Cheque',         'icon'=>'📄', 'label'=>'Cheque'],
                        ['val'=>'bKash',          'icon'=>'📱', 'label'=>'bKash'],
                        ['val'=>'Nagad',          'icon'=>'📲', 'label'=>'Nagad'],
                        ['val'=>'Card',           'icon'=>'💳', 'label'=>'Card'],
                        ['val'=>'SSLCommerz',     'icon'=>'🔒', 'label'=>'SSLCommerz'],
                        ['val'=>'Other',          'icon'=>'💰', 'label'=>'Other'],
                    ];
                    $currentMethod = old('payment_method', $invoice->payment_method ?? '');
                @endphp
                <div class="method-grid" id="methodGrid">
                    @foreach($methods as $m)
                    <div class="method-opt {{ $currentMethod === $m['val'] ? 'active' : '' }}" data-val="{{ $m['val'] }}">
                        <span class="micon">{{ $m['icon'] }}</span>
                        {{ $m['label'] }}
                    </div>
                    @endforeach
                </div>
                <input type="hidden" name="payment_method" id="paymentMethodInput" value="{{ $currentMethod }}">
            </div>

            {{-- Extra fields --}}
            <div class="fg fg-2">
                <div class="field">
                    <label>Transaction ID / Reference</label>
                    <input type="text" name="transaction_id"
                           value="{{ old('transaction_id') }}"
                           placeholder="Bank ref, bKash TrxID, etc.">
                </div>
                <div class="field">
                    <label>Received By</label>
                    <input type="text" name="received_by"
                           value="{{ old('received_by', $invoice->prepared_by ?? '') }}"
                           placeholder="Staff name">
                </div>
            </div>

            <div class="field" style="margin-top:4px;">
                <label>Remarks</label>
                <textarea name="remarks" placeholder="Optional notes about this payment">{{ old('remarks') }}</textarea>
            </div>

        </div>
    </div>

    {{-- Paid email notice --}}
    <div id="paidEmailNotice" style="display:none; background:#f0fdf4; border:1px solid #86efac; border-radius:10px; padding:12px 16px; margin-bottom:16px; font-size:13px; color:#15803d;">
        ✅ <strong>Marking as Paid</strong> will automatically send a payment confirmation email with the paid invoice and money receipt attached.
        @if($invoice->payment_confirmed_email_sent)
        <br><span style="color:#6b7280;">Note: A confirmation email was already sent for this invoice — it will NOT be resent.</span>
        @endif
    </div>

    <div id="partialNotice" style="display:none; background:#fffbeb; border:1px solid #fbbf24; border-radius:10px; padding:12px 16px; margin-bottom:16px; font-size:13px; color:#92400e;">
        🟡 <strong>Partial payment</strong> will update the invoice amount without sending an email. A confirmation email is only sent when fully Paid.
    </div>

    <div class="btn-row">
        <a href="/admin/invoices/view/{{ $invoice->id }}" class="btn-cancel">Cancel</a>
        <button type="submit" class="btn-save">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            Save Payment
        </button>
    </div>

    </form>

    {{-- Payment history log --}}
    @if($invoice->paymentLogs && $invoice->paymentLogs->count())
    <div class="log-card">
        <div class="log-card-head">
            <h3>📋 Payment History</h3>
        </div>
        <table class="log-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Method</th>
                    <th>Transaction ID</th>
                    <th>Received By</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->paymentLogs as $plog)
                <tr>
                    <td>{{ $plog->payment_date ? \Carbon\Carbon::parse($plog->payment_date)->format('d M Y') : '—' }}</td>
                    <td style="font-weight:700; color:#15803d;">{{ $invoice->currency ?? 'BDT' }} {{ number_format($plog->amount, 2) }}</td>
                    <td>{{ $plog->payment_method ?? '—' }}</td>
                    <td style="font-family:monospace; font-size:12px;">{{ $plog->transaction_id ?? '—' }}</td>
                    <td>{{ $plog->received_by ?? '—' }}</td>
                    <td style="color:#6b7280;">{{ $plog->remarks ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

</div>

<script>
(function () {
    const grandTotal  = {{ (float)($invoice->grand_total ?? $invoice->total_amount ?? 0) }};
    const amountInput = document.getElementById('amountPaidInput');

    // ── Status buttons ──
    const statusBtns = document.querySelectorAll('.status-btn');
    const statusInput = document.getElementById('paymentStatusInput');
    const paidNotice    = document.getElementById('paidEmailNotice');
    const partialNotice = document.getElementById('partialNotice');

    function setStatus(val) {
        statusInput.value = val;
        statusBtns.forEach(b => {
            b.classList.remove('active-unpaid','active-partial','active-paid');
            if (b.dataset.val === val) {
                b.classList.add(
                    val === 'Unpaid'  ? 'active-unpaid'  :
                    val === 'Partial' ? 'active-partial'  : 'active-paid'
                );
            }
        });
        paidNotice.style.display    = val === 'Paid'    ? 'block' : 'none';
        partialNotice.style.display = val === 'Partial' ? 'block' : 'none';

        // Auto-fill amount for Paid
        if (val === 'Paid' && (parseFloat(amountInput.value) || 0) === 0) {
            amountInput.value = grandTotal.toFixed(2);
        }
    }

    statusBtns.forEach(b => b.addEventListener('click', () => setStatus(b.dataset.val)));
    setStatus(statusInput.value || 'Unpaid');

    // ── Method buttons ──
    const methodBtns  = document.querySelectorAll('.method-opt');
    const methodInput = document.getElementById('paymentMethodInput');

    methodBtns.forEach(b => b.addEventListener('click', () => {
        methodBtns.forEach(x => x.classList.remove('active'));
        b.classList.add('active');
        methodInput.value = b.dataset.val;
    }));

    // ── Fill full amount ──
    document.getElementById('fillFullAmount').addEventListener('click', e => {
        e.preventDefault();
        amountInput.value = grandTotal.toFixed(2);
    });
})();
</script>

@endsection
