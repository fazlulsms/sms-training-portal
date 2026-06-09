@extends('layouts.app')
@section('page-title', 'eLearning Enrollments')
@section('content')

<x-page-header title="eLearning Enrollments" desc="Manage online course enrollments, payment approvals, and certificate issuance.">
    <x-slot:actions>
        <a href="{{ route('elearning.enrollments.create') }}" class="btn btn-primary btn-sm">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add Enrollment
        </a>
    </x-slot:actions>
</x-page-header>

<x-flash-message />

<div class="filter-bar">
    <form method="GET" action="{{ route('elearning.enrollments.index') }}">
        <div class="filter-row">
            <div class="fi-search-wrap" style="flex:1;min-width:220px;">
                <span class="fi-search-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></span>
                <input class="fi fi-search" type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search participant, email, phone, company, course…" style="width:100%;">
            </div>
            <select class="fi" name="course_id" style="min-width:180px;">
                <option value="">All Courses</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                        {{ $course->name }}
                    </option>
                @endforeach
            </select>
            <select class="fi" name="payment_status" style="min-width:160px;">
                <option value="">Payment Status</option>
                <option value="pending"         {{ request('payment_status')==='pending'         ? 'selected':'' }}>Pending</option>
                <option value="paid"            {{ request('payment_status')==='paid'            ? 'selected':'' }}>Paid</option>
                <option value="manual_approved" {{ request('payment_status')==='manual_approved' ? 'selected':'' }}>Manual Approved</option>
                <option value="failed"          {{ request('payment_status')==='failed'          ? 'selected':'' }}>Failed</option>
                <option value="refunded"        {{ request('payment_status')==='refunded'        ? 'selected':'' }}>Refunded</option>
            </select>
            <select class="fi" name="access_status" style="min-width:140px;">
                <option value="">Access Status</option>
                <option value="locked"   {{ request('access_status')==='locked'   ? 'selected':'' }}>Locked</option>
                <option value="unlocked" {{ request('access_status')==='unlocked' ? 'selected':'' }}>Unlocked</option>
            </select>
        </div>
        <div class="filter-row" style="margin-top:10px;">
            <div class="filter-group">
                <label>From Date</label>
                <input class="fi" type="date" name="from_date" value="{{ request('from_date') }}" style="min-width:150px;">
            </div>
            <div class="filter-group">
                <label>To Date</label>
                <input class="fi" type="date" name="to_date" value="{{ request('to_date') }}" style="min-width:150px;">
            </div>
            <div style="display:flex;align-items:flex-end;gap:8px;">
                <button class="btn btn-primary btn-sm" type="submit">Filter</button>
                <a href="{{ route('elearning.enrollments.index') }}" class="btn btn-ghost btn-sm">Reset</a>
            </div>
        </div>
    </form>
</div>

<div class="dt-wrap">
    <div class="dt-scroll">
        <table class="dt">
            <thead>
                <tr>
                    <th>Participant</th>
                    <th>Course</th>
                    <th class="c">Payment</th>
                    <th class="c">Access</th>
                    <th>Expiry</th>
                    <th class="c">Certificate</th>
                    <th class="c">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($enrollments as $enrollment)
                @php
                    $payBadge = match($enrollment->payment_status) {
                        'paid', 'manual_approved' => 'badge-success',
                        'pending'                 => 'badge-warning',
                        'failed'                  => 'badge-danger',
                        'refunded'                => 'badge-secondary',
                        default                   => 'badge-secondary',
                    };
                    $payLabel = match($enrollment->payment_status) {
                        'manual_approved' => 'Approved',
                        default           => ucfirst($enrollment->payment_status),
                    };
                    $certBadge = match($enrollment->certificate_status) {
                        'issued'   => 'badge-success',
                        'eligible' => 'badge-purple',
                        default    => 'badge-secondary',
                    };
                @endphp
                <tr>
                    <td>
                        <div class="td-main">{{ $enrollment->participant_name }}</div>
                        <div class="td-sub">{{ $enrollment->email }}</div>
                    </td>
                    <td>{{ $enrollment->course->name ?? '—' }}</td>
                    <td class="c">
                        <span class="badge {{ $payBadge }}">{{ $payLabel }}</span>
                        <div class="td-sub" style="text-align:center;">{{ $enrollment->currency }} {{ number_format($enrollment->amount, 2) }}</div>
                    </td>
                    <td class="c">
                        @if($enrollment->access_status === 'unlocked')
                            <span class="badge badge-success">Unlocked</span>
                        @else
                            <span class="badge badge-danger">Locked</span>
                        @endif
                    </td>
                    <td class="text-muted text-small nowrap">
                        {{ $enrollment->expires_at ? \Carbon\Carbon::parse($enrollment->expires_at)->format('d M Y') : '—' }}
                    </td>
                    <td class="c"><span class="badge {{ $certBadge }}">{{ str_replace('_', ' ', ucfirst($enrollment->certificate_status)) }}</span></td>
                    <td class="c">
                        <div class="dt-actions" style="justify-content:center;">
                            <a href="{{ route('elearning.enrollments.show', $enrollment) }}" class="btn btn-view btn-xs">View</a>
                            <a href="{{ route('elearning.enrollments.edit', $enrollment) }}" class="btn btn-edit btn-xs">Edit</a>
                            <a href="/admin/invoices/payment/for-elearning/{{ $enrollment->id }}"
                               class="btn btn-xs" style="background:#f0fdf4;color:#15803d;border:1px solid #86efac;font-weight:700;">💳 Pay</a>
                            @if($enrollment->payment_status === 'pending')
                                <form action="{{ route('elearning.enrollments.approvePayment', $enrollment) }}" method="POST" style="margin:0;">
                                    @csrf
                                    <button type="submit" class="btn btn-approve btn-xs">Approve</button>
                                </form>
                            @endif
                            @if($enrollment->certificate_status === 'eligible')
                                <form action="{{ route('elearning.enrollments.issueCertificate', $enrollment) }}" method="POST"
                                      onsubmit="return confirm('Issue certificate for {{ addslashes($enrollment->participant_name) }}?')" style="margin:0;">
                                    @csrf
                                    <button type="submit" class="btn btn-purple btn-xs">Issue Cert</button>
                                </form>
                            @endif
                            <form action="{{ route('elearning.enrollments.destroy', $enrollment) }}" method="POST"
                                  onsubmit="return confirm('Delete this enrollment?')" style="margin:0;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-del btn-xs">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <div class="empty-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                            </div>
                            <p class="empty-title">No eLearning enrollments found</p>
                            <p class="empty-desc">Try adjusting your filters or add a new enrollment.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:14px 16px;">{{ $enrollments->links() }}</div>
</div>

@endsection
