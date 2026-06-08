@extends('layouts.app')

@section('content')

<h2 class="text-2xl font-bold mb-4">Edit Trainer</h2>

<form method="POST" action="/trainers/update/{{ $trainer->id }}" class="bg-white p-6 rounded shadow">
    @csrf

    <div class="mb-3">
        <label>Name</label>
        <input type="text" name="name" value="{{ $trainer->name }}" class="border p-2 w-full" required>
    </div>

    <div class="mb-3">
        <label>Designation</label>
        <input type="text" name="designation" value="{{ $trainer->designation }}" class="border p-2 w-full">
    </div>

    <div class="mb-3">
        <label>Organization</label>
        <input type="text" name="organization" value="{{ $trainer->organization }}" class="border p-2 w-full">
    </div>

    <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" value="{{ $trainer->email }}" class="border p-2 w-full">
    </div>

    <div class="mb-3">
        <label>Phone</label>
        <input type="text" name="phone" value="{{ $trainer->phone }}" class="border p-2 w-full">
    </div>

    <div class="mb-3">
        <label>Qualification</label>
        <textarea name="qualification" class="border p-2 w-full">{{ $trainer->qualification }}</textarea>
    </div>

    <div class="mb-4">
        <label>Status</label>
        <select name="status" class="border p-2 w-full">
            <option value="1" {{ $trainer->status == 1 ? 'selected' : '' }}>Active</option>
            <option value="0" {{ $trainer->status == 0 ? 'selected' : '' }}>Inactive</option>
        </select>
    </div>

    <div class="mb-4">
        <label style="font-weight:700;">Link User Account <small style="color:#6b7280; font-weight:400;">(gives trainer portal access)</small></label>

        <div style="background:#fef3c7; border:1px solid #fde68a; border-radius:8px; padding:10px 14px; margin:8px 0; font-size:13px; color:#92400e;">
            ⚠️ <strong>Do not link your own admin account.</strong> Create a separate user account for the trainer first, then link it here.
        </div>

        <select name="user_id" class="border p-2 w-full">
            <option value="">— Not linked —</option>
            @foreach($users as $user)
                @if($user->id !== auth()->id())
                    <option value="{{ $user->id }}" {{ $trainer->user_id == $user->id ? 'selected' : '' }}>
                        {{ $user->name }} ({{ $user->email }}) — {{ ucfirst($user->role) }}
                    </option>
                @else
                    <option value="" disabled style="color:#9ca3af;">
                        {{ $user->name }} — Your account (cannot link)
                    </option>
                @endif
            @endforeach
        </select>
        <small style="color:#6b7280;">Linking a user sets their role to "trainer" automatically.</small>
    </div>

    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">
        Update Trainer
    </button>

    <a href="/trainers" class="bg-gray-500 text-white px-4 py-2 rounded">
        Back
    </a>
</form>

@endsection