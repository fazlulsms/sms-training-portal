@extends('layouts.app')
@section('page-title', 'Invoices')
@section('content')

<x-page-header title="Invoice Management" desc="Manage individual and corporate training invoices.">
    <x-slot:actions>
        <a href="/admin/invoices/create" class="btn btn-primary btn-sm">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Create Invoice
        </a>
    </x-slot:actions>
</x-page-header>

<x-flash-message />

<div class="dt-wrap">
    <div class="dt-scroll">
        <table class="dt">
            <thead>
                <tr>
                    <th>Invoice No</th>
                    <th>Client</th>
                    <th>Type</th>
                    <th>Date</th>
                    <th class="r">Total</th>
                    <th class="c">Payment</th>
                    <th class="c">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $invoice)
                @php
                    $payBadge = match($invoice->payment_status) {
                        'Paid'    => 'badge-success',
                        'Partial' => 'badge-warning',
                        default   => 'badge-danger',
                    };
                @endphp
                <tr>
                    <td class="td-mono fw-bold">{{ $invoice->invoice_number }}</td>
                    <td class="td-main">{{ $invoice->client_name }}</td>
                    <td class="text-muted">{{ $invoice->invoice_type }}</td>
                    <td class="nowrap text-muted">{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d M Y') }}</td>
                    <td class="r fw-bold">
                        {{ $invoice->currency }}
                        {{ number_format($invoice->subtotal + $invoice->vat_amount, 2) }}
                    </td>
                    <td class="c"><span class="badge {{ $payBadge }}">{{ $invoice->payment_status }}</span></td>
                    <td class="c">
                        <div class="dt-actions" style="justify-content:center;">
                            <a href="/admin/invoices/view/{{ $invoice->id }}" class="btn btn-view btn-xs">View</a>
                            <a href="/admin/invoices/payment/{{ $invoice->id }}"
                               class="btn btn-xs" style="background:#f0fdf4;color:#15803d;border:1px solid #86efac;font-weight:700;">💳 Pay</a>
                            <a href="/admin/invoices/edit/{{ $invoice->id }}" class="btn btn-edit btn-xs">Edit</a>
                            <a href="{{ url('/admin/invoices/email/' . $invoice->id) }}"
                               onclick="return confirm('Send invoice email to client?')"
                               class="btn btn-xs" style="background:#f0fdfa;color:#0f766e;">Email</a>
                            <a href="/admin/invoices/delete/{{ $invoice->id }}"
                               onclick="return confirm('Delete this invoice?')"
                               class="btn btn-del btn-xs">Delete</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <div class="empty-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                            </div>
                            <p class="empty-title">No invoices found</p>
                            <p class="empty-desc">Create your first invoice to get started.</p>
                            <a href="/admin/invoices/create" class="btn btn-primary btn-sm">Create Invoice</a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($invoices->hasPages())
        <div style="padding:14px 16px;">{{ $invoices->links() }}</div>
    @endif
</div>

@endsection
