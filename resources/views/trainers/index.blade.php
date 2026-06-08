@extends('layouts.app')
@section('page-title', 'Trainers')
@section('content')

<x-page-header title="Trainers" desc="Manage trainer profiles and assignments.">
    <x-slot:actions>
        <a href="/trainers/create" class="btn btn-primary btn-sm">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add Trainer
        </a>
    </x-slot:actions>
</x-page-header>

<x-flash-message />

<div class="filter-bar">
    <form method="GET" action="/trainers">
        <div class="filter-row">
            <div class="fi-search-wrap" style="flex:1;min-width:220px;">
                <span class="fi-search-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></span>
                <input class="fi fi-search" type="text" name="search" value="{{ request('search') }}" placeholder="Search trainers…" style="width:100%;">
            </div>
            <select class="fi" name="status" onchange="this.form.submit()" style="min-width:140px;">
                <option value="">All Status</option>
                <option value="1" {{ request('status')==='1' ? 'selected':'' }}>Active</option>
                <option value="0" {{ request('status')==='0' ? 'selected':'' }}>Inactive</option>
            </select>
            <button class="btn btn-primary btn-sm" type="submit">Search</button>
            <a href="/trainers" class="btn btn-ghost btn-sm">Reset</a>
        </div>
    </form>
</div>

<div class="dt-wrap">
    <div class="dt-scroll">
        <table class="dt">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Trainer</th>
                    <th>Designation</th>
                    <th>Phone</th>
                    <th class="c">Status</th>
                    <th class="c">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($trainers as $i => $trainer)
                <tr>
                    <td class="text-muted text-small">{{ $i + 1 }}</td>
                    <td>
                        <div class="td-main">{{ $trainer->name }}</div>
                        @if($trainer->email)
                            <div class="td-sub">{{ $trainer->email }}</div>
                        @endif
                    </td>
                    <td>{{ $trainer->designation ?? '—' }}</td>
                    <td>{{ $trainer->phone ?? '—' }}</td>
                    <td class="c">
                        @if($trainer->status)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-secondary">Inactive</span>
                        @endif
                    </td>
                    <td class="c">
                        <div class="dt-actions" style="justify-content:center;">
                            <a href="/trainers/edit/{{ $trainer->id }}" class="btn btn-edit btn-xs">Edit</a>
                            <a href="/trainers/delete/{{ $trainer->id }}"
                               onclick="return confirm('Delete trainer {{ addslashes($trainer->name) }}?')"
                               class="btn btn-del btn-xs">Delete</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <div class="empty-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            </div>
                            <p class="empty-title">No trainers found</p>
                            <p class="empty-desc">Add your first trainer to get started.</p>
                            <a href="/trainers/create" class="btn btn-primary btn-sm">Add Trainer</a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
