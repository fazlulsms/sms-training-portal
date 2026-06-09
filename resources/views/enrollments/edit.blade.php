@extends('layouts.app')

@section('content')

<style>
    .enrollment-container { width: 100%; padding: 30px 15px; display: flex; justify-content: center; background-color: #f8fafc; }
    .enrollment-card { width: 100%; max-width: 1000px; background: #ffffff; border-radius: 12px; padding: 32px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05); border: 1px solid #e2e8f0; }
    .page-title { font-size: 26px; font-weight: 700; color: #1e293b; margin-bottom: 8px; }
    .page-subtitle { font-size: 14px; color: #64748b; margin-bottom: 28px; }
    
    .form-section-title { font-size: 16px; font-weight: 700; color: #173a8a; margin: 24px 0 14px 0; padding-bottom: 6px; border-bottom: 2px solid #e2e8f0; }
    .form-section-title:first-of-type { margin-top: 0; }
    
    .form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
    .form-group { display: flex; flex-direction: column; }
    .form-group.full { grid-column: 1 / -1; }
    
    label { font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; }
    input, select, textarea { width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px 14px; font-size: 14px; color: #334155; background: #fff; box-sizing: border-box; transition: all 0.2s ease; }
    input[readonly] { background-color: #f1f5f9; color: #64748b; cursor: not-allowed; }
    textarea { min-height: 80px; resize: vertical; }
    
    input:focus, select:focus, textarea:focus { border-color: #173a8a; outline: none; box-shadow: 0 0 0 3px rgba(23, 58, 138, 0.15); }
    
    .actions { margin-top: 32px; display: flex; justify-content: flex-end; gap: 12px; border-top: 1px solid #e2e8f0; padding-top: 20px; }
    .btn { border: none; border-radius: 8px; padding: 12px 24px; font-size: 14px; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; transition: all 0.2s ease; }
    .btn-primary { background: #173a8a; color: #fff; }
    .btn-primary:hover { background: #112b66; }
    .btn-secondary { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
    .btn-secondary:hover { background: #e2e8f0; color: #1e293b; }
    
    .error-box { background: #fef2f2; border-left: 4px solid #ef4444; color: #991b1b; padding: 14px; border-radius: 6px; margin-bottom: 24px; font-size: 14px; }
    
    @media(max-width: 768px) { 
        .form-grid { grid-template-columns: 1fr; } 
        .enrollment-card { padding: 20px; }
        .actions { flex-direction: column-reverse; }
        .btn { width: 100%; }
    }
</style>

<div class="enrollment-container">
    <div class="enrollment-card">
        <div class="page-title">Edit Enrollment</div>
        <div class="page-subtitle">Modify the details below to update the trainee's record and status parameters.</div>

        @if ($errors->any())
            <div class="error-box">
                <strong style="display:block; margin-bottom: 5px;">Please fix the following errors:</strong>
                @foreach ($errors->all() as $error)
                    <div>• {{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form action="{{ url('/enrollments/' . $enrollment->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Section 1: Course Allocation -->
            <div class="form-section-title">Course & Schedule Details</div>
            <div class="form-grid">
                <div class="form-group full">
                    <label for="training_schedule_id">Training Schedule <span style="color:#ef4444">*</span></label>
                    <select name="training_schedule_id" id="training_schedule_id" required>
                        <option value="">Select Schedule</option>
                        @foreach($schedules as $schedule)
                            <option value="{{ $schedule->id }}"
                                data-physical="{{ $schedule->physical_fee ?? 0 }}"
                                data-online="{{ $schedule->online_fee ?? 0 }}"
                                {{ (old('training_schedule_id', $enrollment->training_schedule_id) == $schedule->id) ? 'selected' : '' }}>
                                {{ $schedule->course->name ?? 'N/A' }} 
                                | Batch: {{ $schedule->batch_code ?? 'N/A' }} 
                                | Start: {{ $schedule->start_date ?? 'N/A' }} 
                                | [Physical: {{ $schedule->physical_fee ?? 0 }} | Online: {{ $schedule->online_fee ?? 0 }}]
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="selected_mode">Training Mode <span style="color:#ef4444">*</span></label>
                    <select name="selected_mode" id="selected_mode" required>
                        <option value="">Select Mode</option>
                        <option value="Physical" {{ (old('selected_mode', $enrollment->selected_mode) == 'Physical') ? 'selected' : '' }}>Physical</option>
                        <option value="Online" {{ (old('selected_mode', $enrollment->selected_mode) == 'Online') ? 'selected' : '' }}>Online</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="applied_fee">Applied Fee</label>
                    <input type="text" name="applied_fee" id="applied_fee" value="{{ old('applied_fee', $enrollment->applied_fee) }}" readonly placeholder="Calculated automatically">
                </div>
            </div>

            <!-- Section 2: Personal Information -->
            <div class="form-section-title">Trainee Personal Profile</div>
            <div class="form-grid">
                <div class="form-group">
                    <label for="full_name">Full Name <span style="color:#ef4444">*</span></label>
                    <input type="text" name="full_name" id="full_name" value="{{ old('full_name', $enrollment->full_name) }}" required placeholder="John Doe">
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $enrollment->email) }}" placeholder="example@domain.com">
                </div>

                <div class="form-group">
                    <label for="company">Company / Organization</label>
                    <input type="text" name="company" id="company" value="{{ old('company', $enrollment->company) }}">
                </div>

                <div class="form-group">
                    <label for="designation">Designation</label>
                    <input type="text" name="designation" id="designation" value="{{ old('designation', $enrollment->designation) }}">
                </div>

                <div class="form-group">
                    <label for="country">Country</label>
                    <select name="country" id="country">
                        @php
                            $countries = [
                                'Bangladesh' => '+880', 'India' => '+91', 'Sri Lanka' => '+94', 
                                'Nepal' => '+977', 'Myanmar' => '+95', 'Vietnam' => '+84', 
                                'Thailand' => '+66', 'Malaysia' => '+60', 'Singapore' => '+65', 
                                'UAE' => '+971', 'USA' => '+1', 'UK' => '+44'
                            ];
                            $currentCountry = old('country', $enrollment->country ?? 'Bangladesh');
                        @endphp
                        @foreach($countries as $cName => $cCode)
                            <option value="{{ $cName }}" data-code="{{ $cCode }}" {{ $currentCountry == $cName ? 'selected' : '' }}>{{ $cName }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-grid" style="grid-template-columns: 80px 1fr; gap: 10px;">
                    <div class="form-group">
                        <label for="country_code">Code</label>
                        <input type="text" name="country_code" id="country_code" value="{{ old('country_code', $enrollment->country_code ?? '+880') }}" readonly>
                    </div>
                    <div class="form-group">
                        <label for="mobile_number">Mobile Number</label>
                        <input type="text" name="mobile_number" id="mobile_number" value="{{ old('mobile_number', $enrollment->mobile_number) }}" placeholder="1712345678">
                    </div>
                </div>

                <div class="form-group full">
                    <label for="full_address">Full Postal Address</label>
                    <textarea name="full_address" id="full_address">{{ old('full_address', $enrollment->full_address) }}</textarea>
                </div>
            </div>

            <!-- Section 3: Status & Pricing Details -->
            <div class="form-section-title">Administration Status</div>

            {{-- Payment info: read-only display. Update via Invoice → 💳 Pay --}}
            @php
                $payBadgeColor = match($enrollment->payment_status) {
                    'manual_approved', 'Paid' => '#dcfce7',
                    'Partial'                  => '#fef9c3',
                    default                    => '#f3f4f6',
                };
                $payTextColor = match($enrollment->payment_status) {
                    'manual_approved', 'Paid' => '#15803d',
                    'Partial'                  => '#854d0e',
                    default                    => '#6b7280',
                };
                $payLabel = match($enrollment->payment_status) {
                    'manual_approved' => 'Paid (Manual)',
                    default           => $enrollment->payment_status ?? 'Pending',
                };
            @endphp
            <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:16px 20px;margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;gap:16px;">
                <div style="display:flex;align-items:center;gap:20px;flex-wrap:wrap;">
                    <div>
                        <div style="font-size:11px;font-weight:700;color:#92400e;text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;">Payment Status</div>
                        <span style="background:{{ $payBadgeColor }};color:{{ $payTextColor }};padding:4px 12px;border-radius:20px;font-size:13px;font-weight:700;">{{ $payLabel }}</span>
                    </div>
                    <div>
                        <div style="font-size:11px;font-weight:700;color:#92400e;text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;">Amount Received</div>
                        <span style="font-size:14px;font-weight:700;color:#1e293b;">{{ number_format($enrollment->amount_received ?? 0, 2) }}</span>
                    </div>
                    @if($enrollment->payment_method)
                    <div>
                        <div style="font-size:11px;font-weight:700;color:#92400e;text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;">Method</div>
                        <span style="font-size:13px;color:#374151;">{{ $enrollment->payment_method }}</span>
                    </div>
                    @endif
                </div>
                <a href="/admin/invoices/payment/for-enrollment/{{ $enrollment->id }}"
                   style="background:#15803d;color:#fff;padding:9px 18px;border-radius:8px;font-size:13px;font-weight:700;text-decoration:none;white-space:nowrap;display:inline-flex;align-items:center;gap:6px;">
                   💳 Update Payment
                </a>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label for="attendance_status">Attendance Status</label>
                    <select name="attendance_status" id="attendance_status">
                        <option value="Pending" {{ (old('attendance_status', $enrollment->attendance_status) == 'Pending') ? 'selected' : '' }}>Pending</option>
                        <option value="Present" {{ (old('attendance_status', $enrollment->attendance_status) == 'Present') ? 'selected' : '' }}>Present</option>
                        <option value="Absent" {{ (old('attendance_status', $enrollment->attendance_status) == 'Absent') ? 'selected' : '' }}>Absent</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="completion_status">Completion Status</label>
                    <select name="completion_status" id="completion_status">
                        <option value="Pending" {{ (old('completion_status', $enrollment->completion_status) == 'Pending') ? 'selected' : '' }}>Pending</option>
                        <option value="Completed" {{ (old('completion_status', $enrollment->completion_status) == 'Completed') ? 'selected' : '' }}>Completed</option>
                        <option value="Not Completed" {{ (old('completion_status', $enrollment->completion_status) == 'Not Completed') ? 'selected' : '' }}>Not Completed</option>
                    </select>
                </div>

                <div class="form-group full">
                    <label for="remarks">Internal Remarks</label>
                    <textarea name="remarks" id="remarks">{{ old('remarks', $enrollment->remarks) }}</textarea>
                </div>
            </div>

            <div class="actions">
                <a href="{{ url('/enrollments') }}" class="btn btn-secondary">Back</a>
                <button type="submit" class="btn btn-primary">Update Enrollment</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const schedule = document.getElementById('training_schedule_id');
    const mode = document.getElementById('selected_mode');
    const fee = document.getElementById('applied_fee');
    const country = document.getElementById('country');
    const countryCode = document.getElementById('country_code');

    // Sync values for fee based on schedule and mode selection
    function syncEnrollmentFee() {
        if (!schedule || !mode || !fee) return;

        const selectedOption = schedule.options[schedule.selectedIndex];

        if (!selectedOption || !selectedOption.value || !mode.value) {
            fee.value = '';
            return;
        }

        const selectedMode = mode.value.toLowerCase();

        if (selectedMode === 'physical') {
            fee.value = selectedOption.dataset.physical || 0;
        } else if (selectedMode === 'online') {
            fee.value = selectedOption.dataset.online || 0;
        } else {
            fee.value = '';
        }
    }

    // Sync Country dialing codes
    function syncCountryCode() {
        if (!country || !countryCode) return;
        const selectedCountryOption = country.options[country.selectedIndex];
        if (selectedCountryOption) {
            countryCode.value = selectedCountryOption.dataset.code || '';
        }
    }

    // Event Listeners
    schedule.addEventListener('change', syncEnrollmentFee);
    mode.addEventListener('change', syncEnrollmentFee);
    country.addEventListener('change', syncCountryCode);

    // Initial Execution Runtime (Runs on Edit view load to keep values in place)
    syncEnrollmentFee();
    syncCountryCode();
});
</script>
@endsection