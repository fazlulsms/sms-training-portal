@extends('layouts.app')

@section('content')

@php
    $participantName = $selectedEnrollment->full_name
        ?? $selectedEnrollment->participant_full_name
        ?? '';

    $company = $selectedEnrollment->company ?? '';
    $email = $selectedEnrollment->email ?? '';
    $phone = $selectedEnrollment->mobile_number ?? '';
    $country = $selectedEnrollment->country ?? '';
$address = $selectedEnrollment->full_address
    ?? $selectedEnrollment->address
    ?? $selectedEnrollment->present_address
    ?? $selectedEnrollment->mailing_address
    ?? '';

    $schedule = $selectedEnrollment->trainingSchedule ?? null;
    $course = $schedule->course ?? null;

    $courseName = $course->name ?? $course->course_name ?? '';
    $trainingDate = $schedule
        ? \Carbon\Carbon::parse($schedule->start_date)->format('d M Y') . ' - ' . \Carbon\Carbon::parse($schedule->end_date)->format('d M Y')
        : '';

    $duration = $schedule->duration ?? '';
    $mode = $selectedEnrollment->selected_mode ?? '';
    $venue = $mode == 'Online'
        ? ($schedule->zoom_link ?? 'Online')
        : ($schedule->venue ?? $mode);

    $currency = $schedule->currency ?? 'BDT';
$participants = (float) request('number_of_participants', 1);
$fee = (float) request('fee_per_person', $selectedEnrollment->applied_fee ?? 0);
$discountPercent = (float) request('discount_percent', 0);
$vatPercent = (float) request('vat_percent', 15);

$chargeFor = $participants * $fee;
$discountAmount = ($chargeFor * $discountPercent) / 100;
$subtotal = $chargeFor - $discountAmount;
$vatAmount = ($subtotal * $vatPercent) / 100;
$grandTotal = $subtotal + $vatAmount;
@endphp

<div style="padding:20px 24px;">

    <h2 style="font-size:28px; font-weight:800; margin:0;">Create Invoice</h2>
    <p style="color:#6b7280; margin:5px 0 20px;">Select enrolled participant first, then generate invoice.</p>

    <div style="background:white; border:1px solid #e5e7eb; padding:18px; margin-bottom:18px;">
        <form method="GET" action="/admin/invoices/create">
            <label>Select Enrolled Participant</label>
            <div style="display:grid; grid-template-columns:1fr auto; gap:12px;">
                <select name="enrollment_id" required class="form-control">
                    <option value="">Select Participant / Batch</option>
                    @foreach($enrollments as $enrollment)
                        @php
                            $pName = $enrollment->full_name ?? $enrollment->participant_full_name ?? '';
                            $s = $enrollment->trainingSchedule ?? null;
                            $c = $s->course ?? null;
                            $cName = $c->name ?? $c->course_name ?? '';
                        @endphp

                        <option value="{{ $enrollment->id }}"
                            {{ request('enrollment_id') == $enrollment->id ? 'selected' : '' }}>
                            {{ $pName }} | {{ $enrollment->company ?? 'N/A' }} | {{ $cName }} | {{ $s->batch_code ?? 'N/A' }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" style="background:#173a8a; color:white; border:none; padding:0 20px; font-weight:800;">
                    Load
                </button>
            </div>
        </form>
    </div>

    @if($selectedEnrollment)

    <div style="background:white; border:1px solid #e5e7eb; padding:18px;">

        <form method="POST" action="/admin/invoices/store">
            @csrf

<input type="hidden" name="enrollment_id" value="{{ $selectedEnrollment->id }}">

            <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:15px;">
                <div>
                    <label>Invoice Type</label>
                    <select name="invoice_type" class="form-control">
                        <option value="Individual">Individual</option>
                        <option value="Corporate">Corporate / Group</option>
                    </select>
                </div>

                <div>
                    <label>Invoice Date</label>
                    <input type="date" name="invoice_date" value="{{ date('Y-m-d') }}" class="form-control">
                </div>

                <div>
                    <label>Currency</label>
                    <input type="text" name="currency" value="{{ $currency }}" readonly class="form-control readonly">
                </div>
            </div>

            <h3>Client Information</h3>

            <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:15px;">
                <div>
                    <label>Client Name / Company</label>
                    <input type="text" name="client_name" value="{{ $company }}" class="form-control">
                </div>

                <div>
                    <label>Contact Person</label>
                    <input type="text" name="contact_person" value="{{ $participantName }}" class="form-control">
                </div>

                <div>
                    <label>Email</label>
                    <input type="email" name="client_email" value="{{ $email }}" class="form-control">
                </div>
            </div>

            <div style="display:grid; grid-template-columns:2fr 1fr 1fr; gap:15px; margin-top:15px;">
                <div>
                    <label>Address</label>
                    <input type="text" name="client_address" value="{{ $address }}" class="form-control">
                </div>

                <div>
                    <label>Country</label>
                    <input type="text" name="client_country" value="{{ $country }}" class="form-control">
                </div>

                <div>
                    <label>Phone</label>
                    <input type="text" name="client_phone" value="{{ $phone }}" class="form-control">
                </div>
            </div>

            <h3>Training Information</h3>

            <div style="display:grid; grid-template-columns:2fr 1fr 1fr; gap:15px;">
                <div>
                    <label>Name of Training</label>
                    <input type="text" name="training_name" value="{{ $courseName }}" class="form-control">
                </div>

                <div>
                    <label>Training Date</label>
                    <input type="text" name="training_date" value="{{ $trainingDate }}" class="form-control">
                </div>

                <div>
                    <label>Duration</label>
                    <input type="text" name="training_duration" value="{{ $duration }}" class="form-control">
                </div>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-top:15px;">
                <div>
                    <label>Method / Venue</label>
                    <input type="text" name="training_method_venue" value="{{ $venue }}" class="form-control">
                </div>

                <div>
                    <label>Service Type</label>
                    <input type="text" name="service_type" value="Capacity Building Training Program" class="form-control">
                </div>
            </div>

            <h3>Fee & Calculation</h3>

<div style="display:grid; grid-template-columns:repeat(4, 1fr); gap:15px;">
    <div>
        <label>No. of Participants</label>
        <input type="number" name="number_of_participants" value="1" min="1" class="form-control">
    </div>

    <div>
        <label>Fee Per Person</label>
        <input type="number" name="fee_per_person" value="{{ $fee }}" class="form-control">
    </div>

    <div>
        <label>Discount %</label>
        <input type="number" name="discount_percent" value="0" min="0" class="form-control">
    </div>

    <div>
        <label>VAT %</label>
<input type="number" name="invoice_vat_percent" value="0" min="0" class="form-control">
    </div>
</div>

<input type="hidden" name="description[]" value="{{ $courseName }}">

<input type="hidden" name="description[]" value="{{ $courseName }}">
<input type="hidden" id="quantity" name="quantity[]">
<input type="hidden" id="unitPrice" name="unit_price[]">
<input type="hidden" id="lineTotal" name="line_total[]">

           <input type="hidden" name="number_of_participants" value="{{ $participants }}">
<input type="hidden" name="number_of_participants" value="{{ $participants }}">
<input type="hidden" name="fee_per_person" value="{{ number_format($fee, 2, '.', '') }}">
<input type="hidden" name="grand_total" value="0">
<input type="hidden" name="vat_percent" value="{{ number_format($vatPercent, 2, '.', '') }}">

<input type="hidden" name="charge_for" value="{{ number_format($chargeFor, 2, '.', '') }}">
<input type="hidden" name="discount_amount" value="{{ number_format($discountAmount, 2, '.', '') }}">
<input type="hidden" name="subtotal" value="{{ number_format($subtotal, 2, '.', '') }}">
<input type="hidden" name="vat_amount" value="{{ number_format($vatAmount, 2, '.', '') }}">
<input type="hidden" name="grand_total" value="{{ number_format($grandTotal, 2, '.', '') }}">

<input type="hidden" name="description[]" value="{{ $courseName }}">
<input type="hidden" name="quantity[]" value="{{ $participants }}">
<input type="hidden" name="unit_price[]" value="{{ number_format($fee, 2, '.', '') }}">
<input type="hidden" name="line_total[]" value="{{ number_format($chargeFor, 2, '.', '') }}">

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-top:25px;">
                <div>
                    <label>Payment Method</label>
                    <input type="text" name="payment_method" value="Account Payee Cheque/Wire Transfer" class="form-control">
                </div>

                <div>
                    <label>Prepared By</label>
                    <input type="text" name="prepared_by" value="Imran Mahedi" class="form-control">
                </div>
            </div>
<input type="hidden" name="quantity[]" value="{{ $participants }}">
<input type="hidden" name="unit_price[]" value="{{ number_format($fee, 2, '.', '') }}">
<input type="hidden" name="line_total[]" value="{{ number_format($chargeFor, 2, '.', '') }}">
            
<button type="submit" style="margin-top:25px; background:#173a8a; color:white; border:none; padding:12px 22px; font-weight:800;">
                Save Invoice
            </button>

        </form>
    </div>

    @endif

</div>

<style>
    label {
        display:block;
        font-weight:700;
        color:#374151;
        margin-bottom:6px;
        font-size:14px;
    }

    h3 {
        font-size:18px;
        margin-top:25px;
        margin-bottom:12px;
        color:#111827;
    }

    .form-control {
        width:100%;
        height:42px;
        padding:8px;
        border:1px solid #d1d5db;
        box-sizing:border-box;
        font-size:14px;
    }

    .readonly {
        background:#f9fafb;
    }
</style>

<script>
function calculateInvoice() {
    let participants = parseFloat(document.getElementById('participants').value) || 1;
    let fee = parseFloat(document.getElementById('feePerPerson').value) || 0;
    let discountPercent = parseFloat(document.getElementById('discountPercent').value) || 0;
    let vatPercent = parseFloat(document.getElementById('vatPercent').value) || 0;

    let chargeFor = participants * fee;
    let discountAmount = chargeFor * discountPercent / 100;
    let subtotal = chargeFor - discountAmount;
    let vatAmount = subtotal * vatPercent / 100;
    let grandTotal = subtotal + vatAmount;

    document.getElementById('chargeFor').value = chargeFor.toFixed(2);
    document.getElementById('discountAmount').value = discountAmount.toFixed(2);
    document.getElementById('subtotal').value = subtotal.toFixed(2);
    document.getElementById('vatAmount').value = vatAmount.toFixed(2);
    document.getElementById('grandTotal').value = grandTotal.toFixed(2);

    document.getElementById('quantity').value = participants;
    document.getElementById('unitPrice').value = fee.toFixed(2);
    document.getElementById('lineTotal').value = chargeFor.toFixed(2);
}

window.onload = calculateInvoice;
</script>

@endsection