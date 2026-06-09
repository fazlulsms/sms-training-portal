<?php

namespace App\Http\Controllers;

use App\Mail\InvoiceEmail;
use App\Models\Enrollment;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Services\PaymentConfirmationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::orderBy('id', 'desc')->paginate(15);
        return view('invoices.index', compact('invoices'));
    }

   public function create(Request $request)
{
    $enrollments = Enrollment::with('trainingSchedule.course')
        ->orderBy('id', 'desc')
        ->get();

    $selectedEnrollment = null;

    if ($request->filled('enrollment_id')) {
        $selectedEnrollment = Enrollment::with('trainingSchedule.course')
            ->find($request->enrollment_id);
    }

    return view('invoices.create', compact('enrollments', 'selectedEnrollment'));
}

    private function numberToWords($number, $currency = 'BDT')
    {
        $number = (int) round($number);

        $currencyWords = [
            'BDT' => 'Taka Only',
            'USD' => 'US Dollar Only',
            'VND' => 'Vietnamese Dong Only',
            'AED' => 'UAE Dirham Only',
        ];

        $currencyText = $currencyWords[$currency] ?? ($currency . ' Only');

        if ($number == 0) {
            return 'Zero ' . $currencyText;
        }

        $words = [
            0 => '', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four',
            5 => 'Five', 6 => 'Six', 7 => 'Seven', 8 => 'Eight',
            9 => 'Nine', 10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve',
            13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen',
            16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen',
            19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty',
            40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty',
            70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety',
        ];

        $convert = function ($num) use (&$convert, $words) {
            if ($num < 21) return $words[$num];
            if ($num < 100) return trim($words[10 * floor($num / 10)] . ' ' . $words[$num % 10]);
            if ($num < 1000) return trim($words[floor($num / 100)] . ' Hundred ' . $convert($num % 100));
            if ($num < 100000) return trim($convert(floor($num / 1000)) . ' Thousand ' . $convert($num % 1000));
            if ($num < 10000000) return trim($convert(floor($num / 100000)) . ' Lakh ' . $convert($num % 100000));
            return trim($convert(floor($num / 10000000)) . ' Crore ' . $convert($num % 10000000));
        };

        return trim(preg_replace('/\s+/', ' ', $convert($number))) . ' ' . $currencyText;
    }

    public function store(Request $request)
{
    $lastInvoice = Invoice::orderBy('id', 'desc')->first();
    $nextNumber = $lastInvoice ? $lastInvoice->id + 1 : 1;

    $invoiceNumber = 'SMS/BD/' . date('y') . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

    $enrollmentId = $request->enrollment_id;

    $enrollment = Enrollment::with('trainingSchedule.course')->find($enrollmentId);

    $participants = (float) ($request->number_of_participants ?? 1);
    $feePerPerson = (float) ($request->fee_per_person ?? 0);

    $chargeFor = $participants * $feePerPerson;

$discountPercent = $request->has('discount_percent')
    ? (float) $request->discount_percent
    : 0;
    $discountAmount = ($chargeFor * $discountPercent) / 100;

    $subtotal = $chargeFor - $discountAmount;

$vatPercent = (float) ($request->invoice_vat_percent ?? 0);
    $vatAmount = ($subtotal * $vatPercent) / 100;

$grandTotal = $subtotal + $vatAmount;

$amountInWords = $this->numberToWords($grandTotal, $request->currency ?? 'BDT');

$invoice = Invoice::create([
        'invoice_number' => $invoiceNumber,
        'invoice_type' => $request->invoice_type,
        'client_name' => $request->client_name,
        'client_phone' => $request->client_phone,
        'client_email' => $request->client_email,
        'client_address' => $request->client_address,
        'client_country' => $request->client_country,
        'contact_person' => $request->contact_person,
        'service_type' => $request->service_type,
        'training_name' => $request->training_name,
        'training_date' => $request->training_date,
        'training_duration' => $request->training_duration,
        'training_method_venue' => $request->training_method_venue,
        'number_of_participants' => $participants,
        'fee_per_person' => $feePerPerson,
        'charge_for' => $chargeFor,
        'invoice_date' => $request->invoice_date,
        'currency' => $request->currency,
       'subtotal' => $subtotal,
'discount_percent' => $discountPercent,
'discount_amount' => $discountAmount,
'vat_percent' => $vatPercent,
'vat_amount' => $vatAmount,
'grand_total' => $grandTotal,
'total_amount' => $grandTotal,
'amount_in_words' => $amountInWords,
        'payment_status' => 'Unpaid',
        'amount_paid' => 0,
        'payment_method' => $request->payment_method,
        'prepared_by' => $request->prepared_by,
        'status' => 'Issued',
    ]);

    InvoiceItem::create([
        'invoice_id' => $invoice->id,
        'enrollment_id' => $enrollmentId,
        'participant_name' => $enrollment
            ? ($enrollment->full_name ?? $enrollment->participant_full_name)
            : $request->contact_person,
        'description' => $request->training_name ?? 'Training Participation Fee',
        'quantity' => $participants,
        'unit_price' => $feePerPerson,
        'line_total' => $chargeFor,
    ]);

    return redirect('/admin/invoices/view/' . $invoice->id)
        ->with('success', 'Invoice created successfully.');
}

    public function show($id)
    {
        $invoice = Invoice::with(['items.enrollment.trainingSchedule.course', 'paymentLogs'])->findOrFail($id);
        return view('invoices.show', compact('invoice'));
    }

    public function pdf($id)
    {
        $invoice = Invoice::with('items.enrollment.trainingSchedule.course')->findOrFail($id);

        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'))
            ->setPaper('a4', 'portrait')
            ->setOption('dpi', 96)
            ->setOption('defaultFont', 'DejaVu Sans');

        $safeFileName = str_replace(['/', '\\'], '-', $invoice->invoice_number);

        return $pdf->stream($safeFileName . '.pdf');
    }

    public function edit($id)
    {
        $invoice = Invoice::with('items')->findOrFail($id);

        $enrollments = Enrollment::with('trainingSchedule.course')
            ->orderBy('id', 'desc')
            ->get();

        return view('invoices.edit', compact('invoice', 'enrollments'));
    }

    public function update(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        // Capture payment status before update (for duplicate-prevention)
        $wasAlreadyPaid = strtolower($invoice->payment_status ?? '') === 'paid';

       $participants = $request->number_of_participants ?? 1;
$feePerPerson = $request->fee_per_person ?? 0;
$chargeFor = $participants * $feePerPerson;

$discountPercent = $request->has('discount_percent')
    ? (float) $request->discount_percent
    : 0;

$discountAmount = ($chargeFor * $discountPercent) / 100;

$subtotal = $chargeFor - $discountAmount;

$vatPercent = $request->has('vat_percent')
    ? (float) $request->vat_percent
    : 0;

$vatAmount = ($subtotal * $vatPercent) / 100;

$grandTotal = $subtotal + $vatAmount;

$amountInWords = $this->numberToWords($grandTotal, $request->currency ?? 'BDT');

        $invoice->update([
            'invoice_type' => $request->invoice_type,

            'client_name' => $request->client_name,
            'client_company' => $request->client_company ?? $request->client_name,
            'contact_person' => $request->contact_person,
            'client_email' => $request->client_email,
            'client_phone' => $request->client_phone,
            'client_address' => $request->client_address,
            'client_country' => $request->client_country,

            'invoice_date' => $request->invoice_date ?? $invoice->invoice_date ?? now()->toDateString(),
            'due_date' => $request->due_date,

            'service_type' => $request->service_type ?? 'Capacity Building Training Program',
            'training_name' => $request->training_name,
            'training_date' => $request->training_date,
            'training_duration' => $request->training_duration,
            'training_method_venue' => $request->training_method_venue,
            'number_of_participants' => $participants,
            'fee_per_person' => $feePerPerson,
            'charge_for' => $chargeFor,

            'currency' => $request->currency ?? 'BDT',
            'subtotal' => $subtotal,
            'vat_percent' => $vatPercent,
            'vat_amount' => $vatAmount,
            'discount_percent' => $discountPercent,
            'discount_amount' => $discountAmount,
            'total_amount' => $grandTotal,
            'grand_total'  => $grandTotal,
            'amount_in_words' => $amountInWords,

            'payment_status' => $request->payment_status ?? $invoice->payment_status,
            'amount_paid' => ($request->amount_paid !== null && $request->amount_paid !== '') ? (float)$request->amount_paid : ($invoice->amount_paid ?? 0),
            'payment_method' => $request->payment_method,
            'prepared_by' => $request->prepared_by,
        ]);

        $invoice->items()->delete();

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'enrollment_id' => $request->enrollment_id,
            'participant_name' => $request->contact_person ?? $request->client_name,
            'description' => $request->training_name ?? 'Training Participation Fee',
            'quantity' => $participants,
            'unit_price' => $feePerPerson,
            'line_total' => $subtotal,
        ]);

        // Trigger payment confirmation if payment_status just became Paid
        $nowPaid = strtolower($request->payment_status ?? '') === 'paid';
        if ($nowPaid && !$wasAlreadyPaid) {
            try {
                PaymentConfirmationService::handleInvoice($invoice->fresh(), [
                    'amount'         => ($request->amount_paid !== null && $request->amount_paid !== '') ? (float)$request->amount_paid : $grandTotal,
                    'payment_method' => $request->payment_method,
                    'received_by'    => $request->prepared_by,
                    'remarks'        => $request->notes,
                ]);
            } catch (\Throwable $e) {
                Log::error('PaymentConfirmation failed (Invoice update)', [
                    'invoice_id' => $invoice->id,
                    'error'      => $e->getMessage(),
                ]);
            }
        }

        return redirect('/admin/invoices/view/' . $invoice->id)
            ->with('success', 'Invoice updated successfully.');
    }

    public function email($id)
    {
        $invoice = Invoice::findOrFail($id);

        if (!$invoice->client_email) {
            return redirect()->back()->with('error', 'No recipient email found for this invoice.');
        }

        Mail::to($invoice->client_email)->send(new InvoiceEmail($invoice));

        return redirect()->back()->with('success', 'Invoice email sent successfully.');
    }

    public function sendEmail($id)
    {
        return $this->email($id);
    }

    public function delete($id)
    {
        $invoice = Invoice::findOrFail($id);

        $invoice->items()->delete();
        $invoice->delete();

        return redirect('/admin/invoices')->with('success', 'Invoice deleted successfully.');
    }

    public function getEnrollmentDetails($id)
    {
        $enrollment = Enrollment::with(['trainingSchedule.course'])->find($id);

        if (!$enrollment) {
            return response()->json(['error' => 'Enrollment not found'], 404);
        }

        $schedule = $enrollment->trainingSchedule;
        $course = $schedule ? $schedule->course : null;

        $fee = $enrollment->applied_fee ?? 0;

        if (!$fee && $schedule) {
            if ($enrollment->selected_mode === 'Physical') {
                $fee = $schedule->physical_fee ?? 0;
            } elseif ($enrollment->selected_mode === 'Online') {
                $fee = $schedule->online_fee ?? 0;
            }
        }

        return response()->json([
            'client_name' => $enrollment->full_name ?? '',
            'client_email' => $enrollment->email ?? '',
            'client_phone' => $enrollment->mobile_number ?? $enrollment->phone ?? '',
            'client_company' => $enrollment->company ?? '',
            'client_address' => $enrollment->full_address ?? '',
            'client_country' => $enrollment->country ?? 'Bangladesh',
            'training_name' => $course?->name ?? '',
            'training_date' => $schedule?->start_date ?? '',
            'currency' => $schedule?->currency ?? 'BDT',
            'selected_mode' => $enrollment->selected_mode ?? '',
            'unit_price' => $fee,
            'quantity' => 1,
        ]);
    }
} // Make sure this last closing brace remains to close your Controller class definition completely!