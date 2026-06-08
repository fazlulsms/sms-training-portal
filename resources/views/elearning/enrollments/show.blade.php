@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">

    <x-flash-message />

    {{-- ── Page Header ──────────────────────────────────────────────────── --}}
    <div class="bg-white shadow rounded-xl p-6 mb-6">
        <div class="flex justify-between items-start flex-wrap gap-3">
            <div>
                <h1 class="text-2xl font-bold">Enrollment Details</h1>
                <p class="text-gray-500 mt-1">Participant learning and payment information</p>
            </div>

            <div class="flex gap-2 flex-wrap">
                <a href="{{ route('elearning.enrollments.edit', $enrollment) }}"
                   class="bg-yellow-500 text-white px-5 py-2 rounded-lg font-semibold text-sm">Edit</a>

                @if($enrollment->payment_status === 'pending')
                    <form method="POST" action="{{ route('elearning.enrollments.approvePayment', $enrollment) }}" style="margin:0;">
                        @csrf
                        <button type="submit" class="bg-green-600 text-white px-5 py-2 rounded-lg font-semibold text-sm">
                            ✓ Approve Payment
                        </button>
                    </form>
                @endif

                @if($enrollment->certificate_status === 'eligible')
                    <form method="POST" action="{{ route('elearning.enrollments.issueCertificate', $enrollment) }}"
                          onsubmit="return confirm('Issue certificate for {{ addslashes($enrollment->participant_name) }}?');"
                          style="margin:0;">
                        @csrf
                        <button type="submit"
                                style="background:#7c3aed; color:white; padding:8px 20px; border-radius:8px; border:none; cursor:pointer; font-weight:700; font-size:13px;">
                            🎓 Issue Certificate
                        </button>
                    </form>
                @elseif($enrollment->certificate_status === 'issued')
                    <a href="{{ route('elearning.certificate.generate', $enrollment) }}"
                       target="_blank"
                       style="background:#059669; color:white; padding:8px 20px; border-radius:8px; text-decoration:none; font-weight:700; font-size:13px; display:inline-block;">
                        ⬇ Download Certificate
                    </a>
                @endif

                <a href="{{ route('elearning.enrollments.index') }}"
                   class="bg-gray-200 text-gray-800 px-5 py-2 rounded-lg font-semibold text-sm">Back</a>
            </div>
        </div>
    </div>

    {{-- ── Status Cards ─────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-4 gap-4 mb-6">
        <div class="bg-white shadow rounded-xl p-4">
            <div class="text-sm text-gray-500">Payment</div>
            <div class="text-xl font-bold">{{ ucfirst(str_replace('_', ' ', $enrollment->payment_status)) }}</div>
        </div>
        <div class="bg-white shadow rounded-xl p-4">
            <div class="text-sm text-gray-500">Access</div>
            <div class="text-xl font-bold">{{ ucfirst($enrollment->access_status) }}</div>
        </div>
        <div class="bg-white shadow rounded-xl p-4">
            <div class="text-sm text-gray-500">Completion</div>
            <div class="text-xl font-bold">{{ ucfirst(str_replace('_', ' ', $enrollment->completion_status)) }}</div>
        </div>
        <div class="shadow rounded-xl p-4 {{ match($enrollment->certificate_status) {
            'issued'   => 'bg-purple-50 border border-purple-200',
            'eligible' => 'bg-blue-50 border border-blue-200',
            default    => 'bg-white' } }}">
            <div class="text-sm text-gray-500">Certificate</div>
            <div class="text-xl font-bold {{ match($enrollment->certificate_status) {
                'issued'   => 'text-purple-700',
                'eligible' => 'text-blue-700',
                default    => '' } }}">
                {{ match($enrollment->certificate_status) {
                    'issued'    => '🎓 Issued',
                    'eligible'  => '✅ Eligible',
                    'not_issued'=> 'Not Issued',
                    default     => ucfirst(str_replace('_', ' ', $enrollment->certificate_status))
                } }}
            </div>
            @if($enrollment->certificate_number)
                <div class="text-sm text-gray-500 mt-1">No: {{ $enrollment->certificate_number }}</div>
            @endif
        </div>
    </div>

    {{-- ── Row 1: Participant + Payment ─────────────────────────────────── --}}
    <div class="grid grid-cols-2 gap-6 mb-6">

        <div class="bg-white shadow rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b bg-gray-50">
                <h2 class="font-bold text-lg">Participant Information</h2>
            </div>
            <div class="grid grid-cols-2">
                <div class="p-5 border-b">
                    <div class="text-sm text-gray-500">Name</div>
                    <div class="font-medium">{{ $enrollment->participant_name }}</div>
                </div>
                <div class="p-5 border-b">
                    <div class="text-sm text-gray-500">Email</div>
                    <div class="font-medium">{{ $enrollment->email }}</div>
                </div>
                <div class="p-5 border-b">
                    <div class="text-sm text-gray-500">Phone</div>
                    <div>{{ $enrollment->phone ?? '-' }}</div>
                </div>
                <div class="p-5 border-b">
                    <div class="text-sm text-gray-500">Company</div>
                    <div>{{ $enrollment->company ?? '-' }}</div>
                </div>
                <div class="p-5">
                    <div class="text-sm text-gray-500">Designation</div>
                    <div>{{ $enrollment->designation ?? '-' }}</div>
                </div>
                <div class="p-5">
                    <div class="text-sm text-gray-500">Course</div>
                    <div>{{ $enrollment->course->name ?? '-' }}</div>
                </div>
            </div>
        </div>

        <div class="bg-white shadow rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b bg-gray-50">
                <h2 class="font-bold text-lg">Payment & Access</h2>
            </div>
            <div class="grid grid-cols-2">
                <div class="p-5 border-b">
                    <div class="text-sm text-gray-500">Amount</div>
                    <div class="font-medium">{{ $enrollment->currency }} {{ number_format($enrollment->amount, 2) }}</div>
                </div>
                <div class="p-5 border-b">
                    <div class="text-sm text-gray-500">Payment Method</div>
                    <div>{{ ucfirst($enrollment->payment_method) }}</div>
                </div>
                <div class="p-5 border-b">
                    <div class="text-sm text-gray-500">Started At</div>
                    <div>{{ $enrollment->started_at ? \Carbon\Carbon::parse($enrollment->started_at)->format('d M Y, h:i A') : '-' }}</div>
                </div>
                <div class="p-5 border-b">
                    <div class="text-sm text-gray-500">Expires At</div>
                    <div>{{ $enrollment->expires_at ? \Carbon\Carbon::parse($enrollment->expires_at)->format('d M Y, h:i A') : 'No Expiry' }}</div>
                </div>
                <div class="p-5">
                    <div class="text-sm text-gray-500">Transaction ID</div>
                    <div>{{ $enrollment->transaction_id ?? '-' }}</div>
                </div>
                <div class="p-5">
                    <div class="text-sm text-gray-500">Gateway</div>
                    <div>{{ $enrollment->gateway_name ?? '-' }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Row 2: Participant Access (Learner Account) ──────────────────── --}}
    <div class="bg-white shadow rounded-xl overflow-hidden mb-6">
        <div class="px-6 py-4 border-b" style="background:linear-gradient(135deg,#1e3a8a 0%,#2563eb 100%);">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-bold text-lg text-white">Participant Access</h2>
                    <p class="text-blue-100 text-sm mt-0.5">Learner portal account details and actions</p>
                </div>
                @if($enrollment->user)
                    <span style="background:rgba(255,255,255,.15); color:#fff; font-size:12px; font-weight:700; padding:4px 12px; border-radius:20px; border:1px solid rgba(255,255,255,.3);">
                        ✓ Account Linked
                    </span>
                @else
                    <span style="background:rgba(239,68,68,.25); color:#fca5a5; font-size:12px; font-weight:700; padding:4px 12px; border-radius:20px; border:1px solid rgba(239,68,68,.4);">
                        ✗ No Account
                    </span>
                @endif
            </div>
        </div>

        @if($enrollment->user)
        @php $learner = $enrollment->user; @endphp
        <div class="grid grid-cols-2">

            {{-- Left column: account details --}}
            <div class="border-r">
                <div class="grid grid-cols-2">
                    <div class="p-5 border-b">
                        <div class="text-sm text-gray-500">Account Status</div>
                        <div class="mt-1">
                            @if($learner->is_active)
                                <span style="background:#dcfce7; color:#166534; font-size:12px; font-weight:700; padding:3px 10px; border-radius:20px;">● Active</span>
                            @else
                                <span style="background:#fee2e2; color:#991b1b; font-size:12px; font-weight:700; padding:3px 10px; border-radius:20px;">● Inactive</span>
                            @endif
                        </div>
                    </div>
                    <div class="p-5 border-b">
                        <div class="text-sm text-gray-500">Role</div>
                        <div class="font-medium mt-0.5">{{ ucfirst($learner->role) }}</div>
                    </div>
                    <div class="p-5 border-b">
                        <div class="text-sm text-gray-500">Email</div>
                        <div class="font-medium text-blue-700 mt-0.5">{{ $learner->email }}</div>
                    </div>
                    <div class="p-5 border-b">
                        <div class="text-sm text-gray-500">Account Created</div>
                        <div class="mt-0.5">{{ $learner->created_at->format('d M Y, h:i A') }}</div>
                    </div>
                    <div class="p-5 col-span-2">
                        <div class="text-sm text-gray-500">Last Login</div>
                        <div class="mt-0.5">
                            {{ $learner->last_login_at ? $learner->last_login_at->format('d M Y, h:i A') : '—  Never logged in' }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right column: action buttons --}}
            <div class="p-6 flex flex-col gap-3">
                <div class="text-sm font-bold text-gray-600 mb-1">Account Actions</div>

                {{-- Resend Welcome Email --}}
                <form method="POST" action="{{ route('elearning.enrollments.sendWelcomeEmail', $enrollment) }}"
                      onsubmit="return confirm('This will generate a new temporary password and send login credentials to {{ addslashes($enrollment->email) }}. Continue?');">
                    @csrf
                    <button type="submit"
                            style="width:100%; background:#2563eb; color:#fff; padding:9px 16px; border-radius:8px; border:none; cursor:pointer; font-size:13px; font-weight:700; text-align:left; display:flex; align-items:center; gap:8px;">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,12 2,6"/></svg>
                        Send Welcome Email
                    </button>
                </form>

                {{-- Reset Password --}}
                <form method="POST" action="{{ route('elearning.enrollments.resetLearnerPassword', $enrollment) }}"
                      onsubmit="return confirm('This will reset the learner\'s password and email new credentials to {{ addslashes($enrollment->email) }}. Continue?');">
                    @csrf
                    <button type="submit"
                            style="width:100%; background:#f59e0b; color:#fff; padding:9px 16px; border-radius:8px; border:none; cursor:pointer; font-size:13px; font-weight:700; text-align:left; display:flex; align-items:center; gap:8px;">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        Reset Password
                    </button>
                </form>

                {{-- View Learner Profile --}}
                <a href="{{ route('users.edit', $learner) }}"
                   style="width:100%; background:#f3f4f6; color:#374151; padding:9px 16px; border-radius:8px; font-size:13px; font-weight:700; text-align:left; display:flex; align-items:center; gap:8px; text-decoration:none; box-sizing:border-box;">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    View Learner Profile
                </a>

                {{-- Login As Learner --}}
                <a href="{{ route('elearning.enrollments.loginAsLearner', $enrollment) }}"
                   onclick="return confirm('You will be logged in as {{ addslashes($learner->name) }} and see their portal. Use the banner to return. Continue?')"
                   style="width:100%; background:#7c3aed; color:#fff; padding:9px 16px; border-radius:8px; font-size:13px; font-weight:700; text-align:left; display:flex; align-items:center; gap:8px; text-decoration:none; box-sizing:border-box;">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    Login As Learner
                </a>
            </div>

        </div>
        @else
        <div class="p-8 text-center text-gray-500">
            <div style="font-size:40px; margin-bottom:12px;">👤</div>
            <p class="font-semibold text-gray-700 mb-1">No learner account linked</p>
            <p class="text-sm">This enrollment was created before the automatic account creation feature. The next enrollment for this email will auto-create an account.</p>
            <p class="text-sm mt-3">
                To manually create an account, go to
                <a href="{{ route('users.create') }}" class="text-blue-600 font-semibold">User Management</a>
                and create a participant account with email <strong>{{ $enrollment->email }}</strong>.
            </p>
        </div>
        @endif
    </div>

</div>
@endsection
