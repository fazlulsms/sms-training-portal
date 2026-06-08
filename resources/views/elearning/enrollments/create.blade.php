@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto bg-white shadow rounded-xl p-6">

    <h1 class="text-2xl font-bold mb-6">Create eLearning Enrollment</h1>

    @if ($errors->any())
        <div class="bg-red-100 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc ml-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('elearning.enrollments.store') }}" method="POST">
        @csrf

        <div class="space-y-4">

            <div>
                <label class="block text-sm font-medium mb-1">Course</label>
                <select name="course_id" class="w-full border rounded-lg px-4 py-2" required>
                    <option value="">Select Course</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}">
                            {{ $course->name }} - {{ $course->course_fee }} BDT
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Participant Name</label>
                <input type="text" name="participant_name" class="w-full border rounded-lg px-4 py-2" required>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Email</label>
                    <input type="email" name="email" class="w-full border rounded-lg px-4 py-2" required>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Phone</label>
                    <input type="text" name="phone" class="w-full border rounded-lg px-4 py-2">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Company</label>
                    <input type="text" name="company" class="w-full border rounded-lg px-4 py-2">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Designation</label>
                    <input type="text" name="designation" class="w-full border rounded-lg px-4 py-2">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Amount</label>
                    <input type="number" step="0.01" name="amount" class="w-full border rounded-lg px-4 py-2">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Currency</label>
                    <select name="currency" class="w-full border rounded-lg px-4 py-2">
                        <option value="BDT">BDT</option>
                        <option value="USD">USD</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Payment Method</label>
                    <select name="payment_method" class="w-full border rounded-lg px-4 py-2">
                        <option value="manual">Manual</option>
                        <option value="bank">Bank</option>
                        <option value="cash">Cash</option>
                        <option value="bkash">bKash</option>
                        <option value="nagad">Nagad</option>
                        <option value="sslcommerz">SSLCommerz</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Payment Status</label>
                <select name="payment_status" class="w-full border rounded-lg px-4 py-2">
                    <option value="pending">Pending</option>
                    <option value="manual_approved">Manual Approved</option>
                    <option value="paid">Paid</option>
                    <option value="failed">Failed</option>
                    <option value="refunded">Refunded</option>
                </select>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg">
                    Save Enrollment
                </button>

                <a href="{{ route('elearning.enrollments.index') }}"
                   class="bg-gray-200 text-gray-800 px-6 py-2 rounded-lg">
                    Back
                </a>
            </div>

        </div>
    </form>
</div>
@endsection