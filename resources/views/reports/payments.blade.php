@extends('layouts.app')

@section('page-title', 'Payment Reports')

@section('content')

<div style="padding:20px 24px;">

    <h2 style="font-size:26px; font-weight:800; margin:0;">Payment Report</h2>
    <p style="color:#6b7280; margin:5px 0 20px;">Track paid amount, due amount, and payment methods.</p>

    <div style="background:white; border:1px solid #e5e7eb; padding:16px; margin-bottom:18px;">
        <form method="GET" action="/admin/reports/payments" style="display:grid; grid-template-columns:repeat(4, 1fr); gap:12px;">
            <input type="date" name="from_date" value="{{ request('from_date') }}">
            <input type="date" name="to_date" value="{{ request('to_date') }}">

            <select name="course_id">
                <option value="">All Courses</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                        {{ $course->name }}
                    </option>
                @endforeach
            </select>

            <select name="training_schedule_id">
                <option value="">All Batches</option>
                @foreach($schedules as $schedule)
                    <option value="{{ $schedule->id }}" {{ request('training_schedule_id') == $schedule->id ? 'selected' : '' }}>
                        {{ $schedule->batch_code }} | {{ $schedule->course->name ?? 'N/A' }}
                    </option>
                @endforeach
            </select>

            <select name="payment_status">
                <option value="">Payment Status</option>
                <option value="Paid" {{ request('payment_status') == 'Paid' ? 'selected' : '' }}>Paid</option>
                <option value="Pending" {{ request('payment_status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                <option value="Unpaid" {{ request('payment_status') == 'Unpaid' ? 'selected' : '' }}>Unpaid</option>
                <option value="Partial" {{ request('payment_status') == 'Partial' ? 'selected' : '' }}>Partial</option>
            </select>

            <select name="payment_method">
                <option value="">Payment Method</option>
                <option value="Cash" {{ request('payment_method') == 'Cash' ? 'selected' : '' }}>Cash</option>
                <option value="Bank Transfer" {{ request('payment_method') == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                <option value="Mobile Banking" {{ request('payment_method') == 'Mobile Banking' ? 'selected' : '' }}>Mobile Banking</option>
                <option value="Card" {{ request('payment_method') == 'Card' ? 'selected' : '' }}>Card</option>
                <option value="Cheque" {{ request('payment_method') == 'Cheque' ? 'selected' : '' }}>Cheque</option>
                <option value="Other" {{ request('payment_method') == 'Other' ? 'selected' : '' }}>Other</option>
            </select>

            <button type="submit" style="background:#173a8a; color:white; border:none; font-weight:700;">Filter</button>

            <a href="/admin/reports/payments"
               style="background:#6b7280; color:white; text-align:center; padding:10px; text-decoration:none; font-weight:700;">
                Reset
            </a>
        </form>
    </div>

    <div style="display:grid; grid-template-columns:repeat(4, 1fr); gap:14px; margin-bottom:18px;">
        <div class="summary-card">
            <div>Total Payable</div>
            <strong>BDT {{ number_format($totalPayable, 2) }}</strong>
        </div>

        <div class="summary-card">
            <div>Total Collected</div>
            <strong>BDT {{ number_format($totalCollected, 2) }}</strong>
        </div>

        <div class="summary-card">
            <div>Total Due</div>
            <strong>BDT {{ number_format($totalDueAmount, 2) }}</strong>
        </div>

        <div class="summary-card">
            <div>Paid Participants</div>
            <strong>{{ $totalPaidCount }}</strong>
        </div>

        <div class="summary-card">
            <div>Cash Collection</div>
            <strong>BDT {{ number_format($cashAmount, 2) }}</strong>
        </div>

        <div class="summary-card">
            <div>Bank Collection</div>
            <strong>BDT {{ number_format($bankAmount, 2) }}</strong>
        </div>

        <div class="summary-card">
            <div>Mobile Banking</div>
            <strong>BDT {{ number_format($mobileAmount, 2) }}</strong>
        </div>
    </div>

    <div style="background:white; border:1px solid #e5e7eb; padding:12px; overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:13px;">
            <thead>
                <tr style="background:#f8fafc;">
                    <th style="padding:10px; text-align:left;">Participant</th>
                    <th style="padding:10px; text-align:left;">Company</th>
                    <th style="padding:10px; text-align:left;">Course</th>
                    <th style="padding:10px; text-align:left;">Batch</th>
                    <th style="padding:10px; text-align:right;">Fee</th>
                    <th style="padding:10px; text-align:right;">Paid</th>
                    <th style="padding:10px; text-align:right;">Due</th>
                    <th style="padding:10px; text-align:left;">Method</th>
                    <th style="padding:10px; text-align:center;">Status</th>
                </tr>
            </thead>

            <tbody>
                @forelse($payments as $payment)
                    @php
                        $fee = $payment->applied_fee ?? 0;
                        $paid = $payment->amount_received ?? 0;
                        $due = max($fee - $paid, 0);
                    @endphp

                    <tr style="border-bottom:1px solid #e5e7eb;">
                        <td style="padding:10px;">
                            <strong>{{ $payment->full_name }}</strong><br>
                            <span style="color:#6b7280;">{{ $payment->email }}</span>
                        </td>

                        <td style="padding:10px;">{{ $payment->company ?? 'N/A' }}</td>

                        <td style="padding:10px;">{{ $payment->trainingSchedule->course->name ?? 'N/A' }}</td>

                        <td style="padding:10px;">{{ $payment->trainingSchedule->batch_code ?? 'N/A' }}</td>

                        <td style="padding:10px; text-align:right;">{{ number_format($fee, 2) }}</td>

                        <td style="padding:10px; text-align:right;">{{ number_format($paid, 2) }}</td>

                        <td style="padding:10px; text-align:right;">{{ number_format($due, 2) }}</td>

                        <td style="padding:10px;">{{ $payment->payment_method ?? 'N/A' }}</td>

                        <td style="padding:10px; text-align:center;">
                            @if($payment->payment_status == 'Paid')
                                <span class="badge success">Paid</span>
                            @elseif($payment->payment_status == 'Partial')
                                <span class="badge warning">Partial</span>
                            @else
                                <span class="badge danger">{{ $payment->payment_status ?? 'Pending' }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="padding:25px; text-align:center; color:#6b7280;">
                            No payment records found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top:14px;">
            {{ $payments->links() }}
        </div>
    </div>

</div>

<style>
    input, select {
        height:42px;
        padding:8px;
        border:1px solid #d1d5db;
        box-sizing:border-box;
    }

    .summary-card {
        background:white;
        border:1px solid #e5e7eb;
        border-left:5px solid #173a8a;
        padding:16px;
        color:#6b7280;
        font-weight:700;
    }

    .summary-card strong {
        display:block;
        margin-top:8px;
        font-size:22px;
        color:#111827;
    }

    .badge {
        padding:5px 10px;
        font-size:12px;
        font-weight:700;
        border-radius:4px;
    }

    .success {
        background:#dcfce7;
        color:#166534;
    }

    .warning {
        background:#fef3c7;
        color:#92400e;
    }

    .danger {
        background:#fee2e2;
        color:#991b1b;
    }
</style>

@endsection