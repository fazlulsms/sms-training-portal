@extends('layouts.app')

@section('page-title', 'Edit Invoice — ' . $invoice->invoice_number)

@section('content')

<style>
*, *::before, *::after { box-sizing: border-box; }

/* ── Page header ──────────────────────────────────────── */
.inv-page-header {
    display: flex; align-items: flex-start; justify-content: space-between;
    flex-wrap: wrap; gap: 14px;
    margin-bottom: 24px;
}
.inv-page-title   { font-size: 24px; font-weight: 900; color: #111827; margin: 0; }
.inv-page-sub     { font-size: 14px; color: #6b7280; margin: 4px 0 0; }
.inv-header-btns  { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }

/* ── Cards ────────────────────────────────────────────── */
.inv-card {
    background: #fff;
    border: 1px solid #e9ecf0;
    border-radius: 14px;
    overflow: hidden;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(15,23,42,.05);
}
.inv-card-head {
    padding: 14px 22px;
    border-bottom: 1px solid #f0f2f5;
    background: #fafbfc;
    display: flex; align-items: center; gap: 10px;
}
.inv-card-icon {
    width: 32px; height: 32px; border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 15px; flex-shrink: 0;
}
.ic-blue   { background: #eff6ff; }
.ic-amber  { background: #fffbeb; }
.ic-green  { background: #f0fdf4; }
.ic-purple { background: #faf5ff; }
.inv-card-label {
    font-size: 11px; font-weight: 800; text-transform: uppercase;
    letter-spacing: .6px; color: #6b7280;
}
.inv-card-title { font-size: 15px; font-weight: 700; color: #111827; }
.inv-card-body  { padding: 22px 24px; }

/* ── Grid ─────────────────────────────────────────────── */
.fg   { display: grid; gap: 16px; margin-bottom: 16px; }
.fg-2 { grid-template-columns: 1fr 1fr; }
.fg-3 { grid-template-columns: 1fr 1fr 1fr; }
.fg-4 { grid-template-columns: 1fr 1fr 1fr 1fr; }
.fg-2-1 { grid-template-columns: 2fr 1fr; }
.fg-full { grid-column: 1 / -1; }
@media (max-width: 768px) {
    .fg-2, .fg-3, .fg-4, .fg-2-1 { grid-template-columns: 1fr; }
}
@media (max-width: 1024px) {
    .fg-4 { grid-template-columns: 1fr 1fr; }
}

/* ── Fields ───────────────────────────────────────────── */
.field { display: flex; flex-direction: column; gap: 5px; }
.field label {
    font-size: 12.5px; font-weight: 700; color: #374151;
    display: flex; align-items: center; gap: 4px;
}
.field label .req { color: #ef4444; }
.field input,
.field select,
.field textarea {
    width: 100%; padding: 9px 12px;
    border: 1.5px solid #e5e7eb; border-radius: 9px;
    font-size: 14px; font-family: inherit; color: #111827;
    background: #fafbfc;
    transition: border-color .14s, box-shadow .14s;
    outline: none; appearance: none;
}
.field input:focus,
.field select:focus,
.field textarea:focus {
    border-color: #2563eb; background: #fff;
    box-shadow: 0 0 0 3px rgba(37,99,235,.1);
}
.field input[readonly],
.field input.computed {
    background: #f3f4f6; color: #6b7280; cursor: not-allowed;
}
.field textarea { resize: vertical; min-height: 70px; }

/* Select arrow */
.sel-wrap { position: relative; }
.sel-wrap select { padding-right: 32px; cursor: pointer; }
.sel-wrap::after {
    content: ''; pointer-events: none;
    position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
    border-left: 5px solid transparent; border-right: 5px solid transparent;
    border-top: 6px solid #9ca3af;
}

/* ── Totals panel ─────────────────────────────────────── */
.totals-panel {
    background: linear-gradient(135deg, #1e3a8a 0%, #1d4ed8 100%);
    border-radius: 14px; padding: 22px 24px; color: #fff;
    margin-bottom: 20px;
}
.totals-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px 24px; }
@media (max-width: 600px) { .totals-grid { grid-template-columns: 1fr; } }
.tot-row { display: flex; align-items: center; justify-content: space-between; padding: 7px 0; }
.tot-row + .tot-row { border-top: 1px solid rgba(255,255,255,.1); }
.tot-label { font-size: 13px; font-weight: 600; opacity: .8; }
.tot-val   { font-size: 14px; font-weight: 800; }
.tot-grand {
    grid-column: 1 / -1;
    background: rgba(255,255,255,.12); border-radius: 10px; padding: 14px 18px;
    display: flex; align-items: center; justify-content: space-between; margin-top: 6px;
}
.tot-grand-label { font-size: 15px; font-weight: 700; opacity: .9; }
.tot-grand-val   { font-size: 22px; font-weight: 900; }
.tot-words {
    grid-column: 1 / -1;
    font-size: 12px; font-weight: 600; opacity: .65; padding: 6px 0 0; font-style: italic;
}

/* ── Buttons ──────────────────────────────────────────── */
.btn {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 10px 20px; border-radius: 10px;
    font-weight: 700; font-size: 13.5px; font-family: inherit;
    border: none; cursor: pointer; text-decoration: none;
    transition: opacity .14s, transform .1s; white-space: nowrap;
}
.btn:active { transform: scale(.97); }
.btn-primary { background: #1e3a8a; color: #fff; box-shadow: 0 4px 12px rgba(30,58,138,.3); }
.btn-primary:hover { background: #1d4ed8; }
.btn-ghost   { background: #f3f4f6; color: #374151; border: 1px solid #e5e7eb; }
.btn-ghost:hover { background: #e9ecf0; }
.btn-lg { padding: 13px 28px; font-size: 15px; border-radius: 12px; }

/* ── Status badge ─────────────────────────────────────── */
.inv-status-badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 12px; border-radius: 20px;
    font-size: 12px; font-weight: 700;
}
.st-unpaid  { background: #fee2e2; color: #991b1b; }
.st-paid    { background: #dcfce7; color: #166534; }
.st-partial { background: #fffbeb; color: #92400e; }
</style>

<form action="/admin/invoices/update/{{ $invoice->id }}" method="POST" id="invoiceForm">
@csrf

<div class="inv-page-header">
    <div>
        <h1 class="inv-page-title">Edit Invoice</h1>
        <p class="inv-page-sub">
            {{ $invoice->invoice_number }}
            &nbsp;·&nbsp;
            <span class="inv-status-badge {{ match($invoice->payment_status) { 'Paid' => 'st-paid', 'Partial' => 'st-partial', default => 'st-unpaid' } }}">
                {{ $invoice->payment_status }}
            </span>
        </p>
    </div>
    <div class="inv-header-btns">
        <a href="/admin/invoices/view/{{ $invoice->id }}" class="btn btn-ghost">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
            Back
        </a>
        <button type="submit" class="btn btn-primary">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            Save Changes
        </button>
    </div>
</div>

{{-- ── Row 1: Invoice Info + Enrollment ─────────────────── --}}
<div class="inv-card">
    <div class="inv-card-head">
        <div class="inv-card-icon ic-blue">📋</div>
        <div>
            <div class="inv-card-label">Invoice</div>
            <div class="inv-card-title">Invoice Details</div>
        </div>
    </div>
    <div class="inv-card-body">
        <div class="fg fg-3">
            <div class="field">
                <label>Invoice Type</label>
                <div class="sel-wrap">
                    <select name="invoice_type">
                        <option value="Individual"      {{ $invoice->invoice_type == 'Individual'      ? 'selected' : '' }}>Individual</option>
                        <option value="Group/Corporate" {{ $invoice->invoice_type == 'Group/Corporate' ? 'selected' : '' }}>Group / Corporate</option>
                        <option value="Corporate"       {{ $invoice->invoice_type == 'Corporate'       ? 'selected' : '' }}>Corporate</option>
                    </select>
                </div>
            </div>
            <div class="field">
                <label>Invoice Date</label>
                <input type="date" name="invoice_date"
                       value="{{ old('invoice_date', $invoice->invoice_date ? \Carbon\Carbon::parse($invoice->invoice_date)->format('Y-m-d') : date('Y-m-d')) }}">
            </div>
            <div class="field">
                <label>Due Date</label>
                <input type="date" name="due_date"
                       value="{{ old('due_date', $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('Y-m-d') : '') }}">
            </div>
        </div>

        <div class="fg fg-2">
            <div class="field">
                <label>Enrollment Link <span style="font-weight:500; color:#9ca3af;">(optional)</span></label>
                <div class="sel-wrap">
                    <select name="enrollment_id">
                        <option value="">— Not linked —</option>
                        @foreach($enrollments as $enr)
                        <option value="{{ $enr->id }}"
                            {{ old('enrollment_id', optional($invoice->items->first())->enrollment_id) == $enr->id ? 'selected' : '' }}>
                            {{ $enr->full_name ?? $enr->participant_name ?? 'N/A' }}
                            — {{ $enr->trainingSchedule->course->name ?? 'N/A' }}
                            ({{ $enr->trainingSchedule->batch_code ?? 'N/A' }})
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="field">
                <label>Currency</label>
                <input type="text" name="currency" id="currencyField"
                       value="{{ old('currency', $invoice->currency ?? 'BDT') }}"
                       placeholder="BDT">
            </div>
        </div>
    </div>
</div>

{{-- ── Row 2: Client Info ─────────────────────────────────── --}}
<div class="inv-card">
    <div class="inv-card-head">
        <div class="inv-card-icon ic-blue">👤</div>
        <div>
            <div class="inv-card-label">Client</div>
            <div class="inv-card-title">Client Information</div>
        </div>
    </div>
    <div class="inv-card-body">
        <div class="fg fg-3">
            <div class="field">
                <label>Client Name / Company <span class="req">*</span></label>
                <input type="text" name="client_name"
                       value="{{ old('client_name', $invoice->client_name) }}"
                       placeholder="Company or individual name">
            </div>
            <div class="field">
                <label>Contact Person</label>
                <input type="text" name="contact_person"
                       value="{{ old('contact_person', $invoice->contact_person) }}"
                       placeholder="Person's name">
            </div>
            <div class="field">
                <label>Email</label>
                <input type="email" name="client_email"
                       value="{{ old('client_email', $invoice->client_email) }}"
                       placeholder="email@company.com">
            </div>
        </div>
        <div class="fg fg-3">
            <div class="field">
                <label>Phone</label>
                <input type="text" name="client_phone"
                       value="{{ old('client_phone', $invoice->client_phone) }}"
                       placeholder="+880 1X XXX XXXX">
            </div>
            <div class="field">
                <label>Country</label>
                <input type="text" name="client_country"
                       value="{{ old('client_country', $invoice->client_country) }}"
                       placeholder="Bangladesh">
            </div>
            <div class="field">
                <label>Address</label>
                <input type="text" name="client_address"
                       value="{{ old('client_address', $invoice->client_address) }}"
                       placeholder="Street, City">
            </div>
        </div>
    </div>
</div>

{{-- ── Row 3: Training Info ───────────────────────────────── --}}
<div class="inv-card">
    <div class="inv-card-head">
        <div class="inv-card-icon ic-amber">🎓</div>
        <div>
            <div class="inv-card-label">Training</div>
            <div class="inv-card-title">Training Information</div>
        </div>
    </div>
    <div class="inv-card-body">
        <div class="fg fg-3">
            <div class="field fg-full" style="grid-column: 1 / 3;">
                <label>Training Name <span class="req">*</span></label>
                <input type="text" name="training_name"
                       value="{{ old('training_name', $invoice->training_name) }}"
                       placeholder="Course / program name">
            </div>
            <div class="field">
                <label>Service Type</label>
                <input type="text" name="service_type"
                       value="{{ old('service_type', $invoice->service_type ?? 'Capacity Building Training Program') }}"
                       placeholder="e.g. Capacity Building Training">
            </div>
        </div>
        <div class="fg fg-3">
            <div class="field">
                <label>Training Date</label>
                <input type="text" name="training_date"
                       value="{{ old('training_date', $invoice->training_date) }}"
                       placeholder="e.g. 10–12 Jun 2026">
            </div>
            <div class="field">
                <label>Duration</label>
                <input type="text" name="training_duration"
                       value="{{ old('training_duration', $invoice->training_duration) }}"
                       placeholder="e.g. 3 Days">
            </div>
            <div class="field">
                <label>Method / Venue</label>
                <input type="text" name="training_method_venue"
                       value="{{ old('training_method_venue', $invoice->training_method_venue) }}"
                       placeholder="Physical / Online / Venue name">
            </div>
        </div>
    </div>
</div>

{{-- ── Row 4: Fee Calculation ─────────────────────────────── --}}
<div class="inv-card">
    <div class="inv-card-head">
        <div class="inv-card-icon ic-green">🧮</div>
        <div>
            <div class="inv-card-label">Fees</div>
            <div class="inv-card-title">Fee Calculation</div>
        </div>
    </div>
    <div class="inv-card-body">
        <div class="fg fg-4">
            <div class="field">
                <label>No. of Participants</label>
                <input type="number" name="number_of_participants" id="numParticipants"
                       value="{{ old('number_of_participants', $invoice->number_of_participants ?? 1) }}"
                       min="1" step="1">
            </div>
            <div class="field">
                <label>Fee Per Person</label>
                <input type="number" name="fee_per_person" id="feePerPerson"
                       value="{{ old('fee_per_person', $invoice->fee_per_person ?? 0) }}"
                       min="0" step="0.01">
            </div>
            <div class="field">
                <label>Discount %</label>
                <input type="number" name="discount_percent" id="discountPercent"
                       value="{{ old('discount_percent', $invoice->discount_percent ?? 0) }}"
                       min="0" max="100" step="0.01">
            </div>
            <div class="field">
                <label>VAT %</label>
                <input type="number" name="vat_percent" id="vatPercent"
                       value="{{ old('vat_percent', $invoice->vat_percent ?? 0) }}"
                       min="0" step="0.01">
            </div>
        </div>

        {{-- Computed hidden fields --}}
        <input type="hidden" name="charge_for"       id="chargeFor">
        <input type="hidden" name="discount_amount"  id="discountAmount">
        <input type="hidden" name="subtotal"         id="subtotalHidden">
        <input type="hidden" name="vat_amount"       id="vatAmountHidden">
        <input type="hidden" name="grand_total"      id="grandTotalHidden">
    </div>
</div>

{{-- ── Totals summary panel ────────────────────────────────── --}}
<div class="totals-panel" id="totalsPanelDisplay">
    <div class="totals-grid">
        <div class="tot-row">
            <span class="tot-label">Charge For</span>
            <span class="tot-val" id="dispChargeFor">0.00</span>
        </div>
        <div class="tot-row">
            <span class="tot-label">Discount</span>
            <span class="tot-val" id="dispDiscountAmt">– 0.00</span>
        </div>
        <div class="tot-row">
            <span class="tot-label">Subtotal</span>
            <span class="tot-val" id="dispSubtotal">0.00</span>
        </div>
        <div class="tot-row">
            <span class="tot-label">VAT</span>
            <span class="tot-val" id="dispVatAmt">0.00</span>
        </div>
        <div class="tot-grand">
            <span class="tot-grand-label">Grand Total</span>
            <span class="tot-grand-val"><span id="dispCurrency">{{ $invoice->currency ?? 'BDT' }}</span> <span id="dispGrandTotal">0.00</span></span>
        </div>
        <div class="tot-words" id="dispAmountWords"></div>
    </div>
</div>

{{-- ── Row 5: Payment ──────────────────────────────────────── --}}
<div class="inv-card">
    <div class="inv-card-head">
        <div class="inv-card-icon ic-green">💳</div>
        <div>
            <div class="inv-card-label">Payment</div>
            <div class="inv-card-title">Payment Details</div>
        </div>
    </div>
    <div class="inv-card-body">
        <div class="fg fg-3">
            <div class="field">
                <label>Payment Status</label>
                <div class="sel-wrap">
                    <select name="payment_status">
                        <option value="Unpaid"   {{ $invoice->payment_status == 'Unpaid'   ? 'selected' : '' }}>Unpaid</option>
                        <option value="Partial"  {{ $invoice->payment_status == 'Partial'  ? 'selected' : '' }}>Partial</option>
                        <option value="Paid"     {{ $invoice->payment_status == 'Paid'     ? 'selected' : '' }}>Paid</option>
                    </select>
                </div>
            </div>
            <div class="field">
                <label>Amount Paid</label>
                <input type="number" name="amount_paid" step="0.01" min="0"
                       value="{{ old('amount_paid', $invoice->amount_paid ?? 0) }}"
                       placeholder="0.00">
            </div>
            <div class="field">
                <label>Payment Method</label>
                <input type="text" name="payment_method"
                       value="{{ old('payment_method', $invoice->payment_method ?? 'Account Payee Cheque/Wire Transfer') }}"
                       placeholder="e.g. Bank Transfer">
            </div>
        </div>
    </div>
</div>

{{-- ── Row 6: Footer / Notes ──────────────────────────────── --}}
<div class="inv-card">
    <div class="inv-card-head">
        <div class="inv-card-icon ic-purple">📝</div>
        <div>
            <div class="inv-card-label">Notes</div>
            <div class="inv-card-title">Footer & Prepared By</div>
        </div>
    </div>
    <div class="inv-card-body">
        <div class="fg fg-2">
            <div class="field">
                <label>Prepared By</label>
                <input type="text" name="prepared_by"
                       value="{{ old('prepared_by', $invoice->prepared_by ?? 'Imran Mahedi') }}"
                       placeholder="Staff name">
            </div>
            <div class="field">
                <label>Notes / Remarks</label>
                <input type="text" name="notes"
                       value="{{ old('notes', $invoice->notes) }}"
                       placeholder="Optional internal notes">
            </div>
        </div>
    </div>
</div>

{{-- ── Bottom action bar ───────────────────────────────────── --}}
<div style="display:flex; align-items:center; justify-content:flex-end; gap:12px; padding: 6px 0 32px;">
    <a href="/admin/invoices/view/{{ $invoice->id }}" class="btn btn-ghost btn-lg">Cancel</a>
    <button type="submit" class="btn btn-primary btn-lg">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
        Save Changes
    </button>
</div>

</form>

<script>
(function () {
    const currData = {
        'BDT': 'Taka Only',
        'USD': 'US Dollar Only',
        'VND': 'Vietnamese Dong Only',
        'AED': 'UAE Dirham Only',
    };

    const ones  = ['','One','Two','Three','Four','Five','Six','Seven','Eight','Nine','Ten','Eleven','Twelve','Thirteen','Fourteen','Fifteen','Sixteen','Seventeen','Eighteen','Nineteen'];
    const tens  = ['','','Twenty','Thirty','Forty','Fifty','Sixty','Seventy','Eighty','Ninety'];

    function convertToWords(num) {
        num = Math.round(num);
        if (num === 0) return 'Zero';
        function c(n) {
            if (n < 20)    return ones[n];
            if (n < 100)   return tens[Math.floor(n/10)] + (n%10 ? ' ' + ones[n%10] : '');
            if (n < 1000)  return ones[Math.floor(n/100)] + ' Hundred' + (n%100 ? ' ' + c(n%100) : '');
            if (n < 100000)return c(Math.floor(n/1000)) + ' Thousand' + (n%1000 ? ' ' + c(n%1000) : '');
            if (n < 10000000) return c(Math.floor(n/100000)) + ' Lakh' + (n%100000 ? ' ' + c(n%100000) : '');
            return c(Math.floor(n/10000000)) + ' Crore' + (n%10000000 ? ' ' + c(n%10000000) : '');
        }
        return c(num).replace(/\s+/g,' ').trim();
    }

    function fmt(v) { return parseFloat(v || 0).toFixed(2); }

    function recalc() {
        const n   = parseFloat(document.getElementById('numParticipants').value) || 0;
        const fee = parseFloat(document.getElementById('feePerPerson').value)    || 0;
        const dp  = parseFloat(document.getElementById('discountPercent').value) || 0;
        const vp  = parseFloat(document.getElementById('vatPercent').value)      || 0;
        const cur = (document.getElementById('currencyField').value || 'BDT').trim().toUpperCase();

        const chargeFor     = n * fee;
        const discountAmt   = chargeFor * dp / 100;
        const subtotal      = chargeFor - discountAmt;
        const vatAmt        = subtotal * vp / 100;
        const grandTotal    = subtotal + vatAmt;

        // Hidden fields for form submission
        document.getElementById('chargeFor').value      = fmt(chargeFor);
        document.getElementById('discountAmount').value = fmt(discountAmt);
        document.getElementById('subtotalHidden').value = fmt(subtotal);
        document.getElementById('vatAmountHidden').value= fmt(vatAmt);
        document.getElementById('grandTotalHidden').value = fmt(grandTotal);

        // Display panel
        document.getElementById('dispChargeFor').textContent   = cur + ' ' + chargeFor.toLocaleString('en-US', {minimumFractionDigits:2});
        document.getElementById('dispDiscountAmt').textContent = '– ' + cur + ' ' + discountAmt.toLocaleString('en-US', {minimumFractionDigits:2});
        document.getElementById('dispSubtotal').textContent    = cur + ' ' + subtotal.toLocaleString('en-US', {minimumFractionDigits:2});
        document.getElementById('dispVatAmt').textContent      = cur + ' ' + vatAmt.toLocaleString('en-US', {minimumFractionDigits:2});
        document.getElementById('dispCurrency').textContent    = cur;
        document.getElementById('dispGrandTotal').textContent  = grandTotal.toLocaleString('en-US', {minimumFractionDigits:2});

        const wordSuffix = currData[cur] || (cur + ' Only');
        document.getElementById('dispAmountWords').textContent = convertToWords(grandTotal) + ' ' + wordSuffix;
    }

    // Wire up listeners
    ['numParticipants','feePerPerson','discountPercent','vatPercent','currencyField'].forEach(id => {
        const el = document.getElementById(id);
        if (el) { el.addEventListener('input', recalc); el.addEventListener('change', recalc); }
    });

    // Run on load
    recalc();
})();
</script>

@endsection
