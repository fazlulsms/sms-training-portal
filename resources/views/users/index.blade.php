@extends('layouts.app')
@section('page-title', 'User Management')
@section('content')

<x-page-header title="User Management" desc="Manage all user accounts, roles, and access.">
    <x-slot:actions>
        <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add User
        </a>
    </x-slot:actions>
</x-page-header>

<x-flash-message />

<div class="filter-bar">
    <form method="GET" action="{{ route('users.index') }}">
        <div class="filter-row">
            <div class="fi-search-wrap" style="flex:1;min-width:220px;">
                <span class="fi-search-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></span>
                <input class="fi fi-search" type="text" name="search" value="{{ request('search') }}" placeholder="Search name, email, company…" style="width:100%;">
            </div>
            <select class="fi" name="role" onchange="this.form.submit()" style="min-width:140px;">
                <option value="">All Roles</option>
                <option value="admin"       {{ request('role')==='admin'       ? 'selected':'' }}>Admin</option>
                <option value="trainer"     {{ request('role')==='trainer'     ? 'selected':'' }}>Trainer</option>
                <option value="participant" {{ request('role')==='participant' ? 'selected':'' }}>Participant</option>
            </select>
            <button class="btn btn-primary btn-sm" type="submit">Search</button>
            <a href="{{ route('users.index') }}" class="btn btn-ghost btn-sm">Reset</a>
        </div>
    </form>
</div>

<div class="dt-wrap">
    <div class="dt-scroll">
        <table class="dt">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Role</th>
                    <th>Company / Designation</th>
                    <th>Phone</th>
                    <th class="c">Status</th>
                    <th class="c">eLearning</th>
                    <th>Joined</th>
                    <th class="c">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>
                        <div class="td-main">{{ $user->name }}</div>
                        <div class="td-sub">{{ $user->email }}</div>
                    </td>
                    <td>
                        @php
                            $roleClass = match($user->role) {
                                'admin'       => 'badge-warning',
                                'trainer'     => 'badge-blue',
                                'participant' => 'badge-success',
                                default       => 'badge-secondary',
                            };
                        @endphp
                        <span class="badge {{ $roleClass }}">{{ ucfirst($user->role) }}</span>
                    </td>
                    <td>
                        @if($user->company || $user->designation)
                            <div style="font-size:13px;">{{ $user->company ?? '—' }}</div>
                            <div class="td-sub">{{ $user->designation ?? '' }}</div>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>{{ $user->phone ?? '—' }}</td>
                    <td class="c">
                        @if($user->is_active)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-danger">Inactive</span>
                        @endif
                    </td>
                    <td class="c text-muted text-small">{{ $user->elearningEnrollments()->count() }}</td>
                    <td class="text-muted text-small nowrap">{{ $user->created_at->format('d M Y') }}</td>
                    <td class="c">
                        <div class="dt-actions" style="justify-content:center;">
                            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-edit btn-xs">Edit</a>
                            @if($user->id !== auth()->id())
                                <form method="POST" action="{{ route('users.toggle-active', $user->id) }}" style="margin:0;">
                                    @csrf
                                    <button type="submit"
                                        class="btn btn-xs {{ $user->is_active ? 'btn-del' : 'btn-approve' }}"
                                        onclick="return confirm('{{ $user->is_active ? 'Deactivate' : 'Activate' }} this user?')">
                                        {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="empty-state">
                            <div class="empty-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                            </div>
                            <p class="empty-title">No users found</p>
                            <p class="empty-desc">Try adjusting your search or add a new user.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:14px 16px;">{{ $users->links() }}</div>
</div>

@endsection
