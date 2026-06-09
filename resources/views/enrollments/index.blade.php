@extends('layouts.app')
@section('page-title', 'Enrollment Management')
@section('content')

<x-page-header title="Enrollment Management" desc="Manage training participants, payment, attendance, and completion status.">
    <x-slot:actions>
        <a href="/admin/enrollments/create" class="btn btn-primary btn-sm">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add Enrollment
        </a>
    </x-slot:actions>
</x-page-header>

<x-flash-message />

<div class="filter-bar">
    <form method="GET" action="/admin/enrollments">
        <div class="filter-row">
            <div class="fi-search-wrap" style="flex:1;min-width:220px;">
                <span class="fi-search-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></span>
                <input class="fi fi-search" type="text" name="search" value="{{ request('search') }}" placeholder="Search participant, email, company, course, batch…" style="width:100%;">
            </div>
            <select class="fi" name="payment_status" style="min-width:150px;">
                <option value="">Payment Status</option>
                <option value="Pending"  {{ request('payment_status')==='Pending'  ? 'selected':'' }}>Pending</option>
                <option value="Paid"     {{ request('payment_status')==='Paid'     ? 'selected':'' }}>Paid</option>
                <option value="Waived"   {{ request('payment_status')==='Waived'   ? 'selected':'' }}>Waived</option>
                <option value="Refunded" {{ request('payment_status')==='Refunded' ? 'selected':'' }}>Refunded</option>
            </select>
            <select class="fi" name="attendance_status" style="min-width:140px;">
                <option value="">Attendance</option>
                <option value="Pending" {{ request('attendance_status')==='Pending' ? 'selected':'' }}>Pending</option>
                <option value="Present" {{ request('attendance_status')==='Present' ? 'selected':'' }}>Present</option>
                <option value="Absent"  {{ request('attendance_status')==='Absent'  ? 'selected':'' }}>Absent</option>
                <option value="Partial" {{ request('attendance_status')==='Partial' ? 'selected':'' }}>Partial</option>
            </select>
            <select class="fi" name="completion_status" style="min-width:140px;">
                <option value="">Completion</option>
                <option value="Pending"       {{ request('completion_status')==='Pending'       ? 'selected':'' }}>Pending</option>
                <option value="Completed"     {{ request('completion_status')==='Completed'     ? 'selected':'' }}>Completed</option>
                <option value="Not Completed" {{ request('completion_status')==='Not Completed' ? 'selected':'' }}>Not Completed</option>
            </select>
            <button class="btn btn-primary btn-sm" type="submit">Filter</button>
            <a href="/admin/enrollments" class="btn btn-ghost btn-sm">Reset</a>
            <a href="/admin/enrollments/export?{{ http_build_query(request()->only(['search','payment_status','attendance_status','completion_status'])) }}" class="btn btn-secondary btn-sm">⬇ CSV</a>
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
                    <th>Batch</th>
                    <th>Mode</th>
                    <th>Company</th>
                    <th class="c">Payment</th>
                    <th class="c">Attendance</th>
                    <th class="c">Completion</th>
                    <th class="c">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($enrollments as $enrollment)
                @php
                    $payClass = match($enrollment->payment_status) {
                        'Paid'     => 'badge-success',
                        'Waived'   => 'badge-info',
                        'Refunded' => 'badge-warning',
                        default    => 'badge-secondary',
                    };
                    $attClass = match($enrollment->attendance_status) {
                        'Present' => 'badge-success',
                        'Absent'  => 'badge-danger',
                        'Partial' => 'badge-warning',
                        default   => 'badge-secondary',
                    };
                    $cmpClass = match($enrollment->completion_status) {
                        'Completed'     => 'badge-success',
                        'Not Completed' => 'badge-danger',
                        default         => 'badge-secondary',
                    };
                @endphp
                <tr>
                    <td>
                        <div class="td-main">{{ $enrollment->full_name ?? 'N/A' }}</div>
                        @if($enrollment->email)
                            <div class="td-sub">{{ $enrollment->email }}</div>
                        @endif
                    </td>
                    <td>{{ $enrollment->trainingSchedule->course->name ?? 'N/A' }}</td>
                    <td class="fw-bold">{{ $enrollment->trainingSchedule->batch_code ?? 'N/A' }}</td>
                    <td><span class="badge badge-info">{{ $enrollment->selected_mode ?? 'N/A' }}</span></td>
                    <td class="text-muted">{{ $enrollment->company ?? '—' }}</td>
                    <td class="c"><span class="badge {{ $payClass }}">{{ $enrollment->payment_status ?? 'Pending' }}</span></td>
                    <td class="c"><span class="badge {{ $attClass }}">{{ $enrollment->attendance_status ?? 'Pending' }}</span></td>
                    <td class="c"><span class="badge {{ $cmpClass }}">{{ $enrollment->completion_status ?? 'Pending' }}</span></td>
                    <td class="c">
                        <div class="dt-actions" style="justify-content:center;">
                            <a href="/admin/enrollments/edit/{{ $enrollment->id }}" class="btn btn-edit btn-xs">Edit</a>
                            <a href="/admin/invoices/payment/for-enrollment/{{ $enrollment->id }}"
                               class="btn btn-xs" style="background:#f0fdf4;color:#15803d;border:1px solid #86efac;font-weight:700;">💳 Pay</a>
                            <a href="/admin/enrollments/delete/{{ $enrollment->id }}"
                               onclick="return confirm('Delete this enrollment?')"
                               class="btn btn-del btn-xs">Delete</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9">
                        <div class="empty-state">
                            <div class="empty-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            </div>
                            <p class="empty-title">No enrollments found</p>
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
