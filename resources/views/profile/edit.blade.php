@extends('layouts.app')

@section('page-title', 'My Profile')

@section('content')

<style>
.profile-wrap { padding: 28px; max-width: 720px; }
.profile-wrap h2 { font-size:22px; font-weight:800; color:#111827; margin:0 0 6px; }

.p-card { background:#fff; border:1px solid #e5e7eb; border-radius:14px; padding:26px; box-shadow:0 1px 4px rgba(15,23,42,.06); margin-bottom:18px; }
.p-card h3 { font-size:15px; font-weight:800; color:#111827; margin:0 0 20px; display:flex; align-items:center; gap:7px; }

.form-group { margin-bottom:16px; }
.form-label { display:block; font-size:13px; font-weight:700; color:#374151; margin-bottom:5px; }
.form-input { width:100%; padding:9px 12px; border:1px solid #d1d5db; border-radius:8px; font-size:14px; font-family:inherit; }
.form-input:focus { outline:none; border-color:#1e3a8a; box-shadow:0 0 0 3px rgba(30,58,138,.1); }
.form-grid { display:grid; grid-template-columns:1fr 1fr; gap:14px; }

.btn-save { background:#1e3a8a; color:white; padding:10px 22px; border:none; border-radius:8px; font-weight:700; font-size:13px; cursor:pointer; font-family:inherit; }

.alert { padding:12px 14px; border-radius:8px; font-weight:600; margin-bottom:14px; font-size:13px; }
.alert-success { background:#dcfce7; color:#166534; }
.alert-error   { background:#fee2e2; color:#991b1b; }
.error-msg { color:#dc2626; font-size:12px; margin-top:4px; }

.role-chip { display:inline-block; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:700; background:#eff6ff; color:#1e3a8a; margin-left:8px; }
</style>

<div class="profile-wrap">
    <h2>My Profile <span class="role-chip">{{ ucfirst(Auth::user()->role) }}</span></h2>
    <p style="color:#6b7280; font-size:14px; margin:0 0 22px;">Update your personal information and change your password.</p>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if($errors->any())     <div class="alert alert-error">{{ $errors->first() }}</div>     @endif

    {{-- Profile --}}
    <div class="p-card">
        <h3>
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            Personal Information
        </h3>
        <form method="POST" action="{{ route('profile.update') }}">
            @csrf @method('PATCH')

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Full Name *</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-input" required>
                    @error('name') <div class="error-msg">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Email Address *</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-input" required>
                    @error('email') <div class="error-msg">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Company</label>
                    <input type="text" name="company" value="{{ old('company', $user->company) }}" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Designation</label>
                    <input type="text" name="designation" value="{{ old('designation', $user->designation) }}" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Country</label>
                    <input type="text" name="country" value="{{ old('country', $user->country) }}" class="form-input">
                </div>
            </div>
            <button type="submit" class="btn-save">Save Profile</button>
        </form>
    </div>

    {{-- Change password --}}
    <div class="p-card">
        <h3>
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            Change Password
        </h3>
        <form method="POST" action="{{ route('profile.password') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Current Password *</label>
                <input type="password" name="current_password" class="form-input" required>
                @error('current_password') <div class="error-msg">{{ $message }}</div> @enderror
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">New Password *</label>
                    <input type="password" name="password" class="form-input" required minlength="8">
                    @error('password') <div class="error-msg">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Confirm New Password *</label>
                    <input type="password" name="password_confirmation" class="form-input" required>
                </div>
            </div>
            <button type="submit" class="btn-save" style="background:#dc2626;">Change Password</button>
        </form>
    </div>
</div>

@endsection
