@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto bg-white shadow rounded-xl p-6">

    <h1 class="text-2xl font-bold mb-6">Edit eLearning Enrollment</h1>

    @if ($errors->any())
        <div class="bg-red-100 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc ml-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('elearning.enrollments.update', $enrollment) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="space-y-4">

            <div>
                <label class="block text-sm font-medium mb-1">Course</label>
                <select name="course_id" class="w-full border rounded-lg px-4 py-2" required>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}"
                            {{ old('course_id', $enrollment->course_id) == $course->id ? 'selected' : '' }}>
                            {{ $course->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Participant Name</label>
                <input type="text"
                       name="participant_name"
                       value="{{ old('participant_name', $enrollment->participant_name) }}"
                       class="w-full border rounded-lg px-4 py-2"
                       required>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Email</label>
                    <input type="email"
                           name="email"
                           value="{{ old('email', $enrollment->email) }}"
                           class="w-full border rounded-lg px-4 py-2"
                           required>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Phone</label>
                    <input type="text"
                           name="phone"
                           value="{{ old('phone', $enrollment->phone) }}"
                           class="w-full border rounded-lg px-4 py-2">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Company</label>
                    <input type="text"
                           name="company"
                           value="{{ old('company', $enrollment->company) }}"
                           class="w-full border rounded-lg px-4 py-2">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Designation</label>
                    <input type="text"
                           name="designation"
                           value="{{ old('designation', $enrollment->designation) }}"
                           class="w-full border rounded-lg px-4 py-2">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Amount</label>
                    <input type="number"
                           step="0.01"
                           name="amount"
                           value="{{ old('amount', $enrollment->amount) }}"
                           class="w-full border rounded-lg px-4 py-2">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Currency</label>
                    <select name="currency" class="w-full border rounded-lg px-4 py-2">
                        <option value="BDT" {{ old('currency', $enrollment->currency) == 'BDT' ? 'selected' : '' }}>BDT</option>
                        <option value="USD" {{ old('currency', $enrollment->currency) == 'USD' ? 'selected' : '' }}>USD</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Payment Method</label>
                    <select name="payment_method" class="w-full border rounded-lg px-4 py-2">
                        <option value="manual" {{ old('payment_method', $enrollment->payment_method) == 'manual' ? 'selected' : '' }}>Manual</option>
                        <option value="bank" {{ old('payment_method', $enrollment->payment_method) == 'bank' ? 'selected' : '' }}>Bank</option>
                        <option value="cash" {{ old('payment_method', $enrollment->payment_method) == 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="bkash" {{ old('payment_method', $enrollment->payment_method) == 'bkash' ? 'selected' : '' }}>bKash</option>
                        <option value="nagad" {{ old('payment_method', $enrollment->payment_method) == 'nagad' ? 'selected' : '' }}>Nagad</option>
                        <option value="sslcommerz" {{ old('payment_method', $enrollment->payment_method) == 'sslcommerz' ? 'selected' : '' }}>SSLCommerz</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Payment Status</label>
                    <select name="payment_status" class="w-full border rounded-lg px-4 py-2">
                        <option value="pending" {{ old('payment_status', $enrollment->payment_status) == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="manual_approved" {{ old('payment_status', $enrollment->payment_status) == 'manual_approved' ? 'selected' : '' }}>Manual Approved</option>
                        <option value="paid" {{ old('payment_status', $enrollment->payment_status) == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="failed" {{ old('payment_status', $enrollment->payment_status) == 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="refunded" {{ old('payment_status', $enrollment->payment_status) == 'refunded' ? 'selected' : '' }}>Refunded</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Access Status</label>
                    <select name="access_status" class="w-full border rounded-lg px-4 py-2">
                        <option value="locked" {{ old('access_status', $enrollment->access_status) == 'locked' ? 'selected' : '' }}>Locked</option>
                        <option value="unlocked" {{ old('access_status', $enrollment->access_status) == 'unlocked' ? 'selected' : '' }}>Unlocked</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Completion Status</label>
                    <select name="completion_status" class="w-full border rounded-lg px-4 py-2">
                        <option value="not_started" {{ old('completion_status', $enrollment->completion_status) == 'not_started' ? 'selected' : '' }}>Not Started</option>
                        <option value="in_progress" {{ old('completion_status', $enrollment->completion_status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ old('completion_status', $enrollment->completion_status) == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Certificate Status</label>
                <select name="certificate_status" class="w-full border rounded-lg px-4 py-2">
                    <option value="not_issued" {{ old('certificate_status', $enrollment->certificate_status) == 'not_issued' ? 'selected' : '' }}>Not Issued</option>
                    <option value="issued" {{ old('certificate_status', $enrollment->certificate_status) == 'issued' ? 'selected' : '' }}>Issued</option>
                </select>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg">
                    Update Enrollment
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