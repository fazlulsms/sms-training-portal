@extends('layouts.app')

@section('page-title', 'Add User')

@section('content')

<style>
.form-wrap { padding: 28px; max-width: 700px; }
.back-btn { display:inline-flex; align-items:center; gap:6px; color:#1e3a8a; font-weight:700; text-decoration:none; font-size:14px; margin-bottom:18px; }
.form-card { background:#fff; border:1px solid #e5e7eb; border-radius:14px; padding:28px; box-shadow:0 1px 4px rgba(15,23,42,.06); }
.form-card h3 { font-size:16px; font-weight:800; color:#111827; margin:0 0 20px; }
.form-group { margin-bottom:18px; }
.form-label { display:block; font-size:13px; font-weight:700; color:#374151; margin-bottom:6px; }
.form-input {
    width:100%; padding:10px 12px; border:1px solid #d1d5db; border-radius:8px;
    font-size:14px; font-family:inherit; color:#111827;
}
.form-input:focus { outline:none; border-color:#1e3a8a; box-shadow:0 0 0 3px rgba(30,58,138,.1); }
.form-grid { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
.form-section { border-top:1px solid #f3f4f6; padding-top:18px; margin-top:18px; }
.form-section h4 { font-size:13px; font-weight:800; color:#6b7280; text-transform:uppercase; letter-spacing:.5px; margin:0 0 14px; }
.btn-submit { background:#1e3a8a; color:white; padding:11px 24px; border:none; border-radius:9px; font-weight:700; font-size:14px; cursor:pointer; font-family:inherit; }
.btn-cancel { background:#f3f4f6; color:#374151; padding:11px 20px; border-radius:9px; text-decoration:none; font-weight:700; font-size:14px; }
.error-msg { color:#dc2626; font-size:12px; margin-top:4px; }
.role-note { font-size:12px; color:#6b7280; margin-top:4px; }
</style>

<div class="form-wrap">
    <a href="{{ route('users.index') }}" class="back-btn">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
        Back to Users
    </a>

    <div class="form-card">
        <h3>Create New User Account</h3>

        <form method="POST" action="{{ route('users.store') }}">
            @csrf

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Full Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-input" required>
                    @error('name') <div class="error-msg">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Email Address *</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-input" required>
                    @error('email') <div class="error-msg">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Password *</label>
                    <input type="password" name="password" class="form-input" required>
                    @error('password') <div class="error-msg">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Confirm Password *</label>
                    <input type="password" name="password_confirmation" class="form-input" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Role *</label>
                <select name="role" class="form-input" required>
                    <option value="participant" {{ old('role','participant')==='participant' ? 'selected':'' }}>Participant — Access own courses only</option>
                    <option value="trainer"     {{ old('role')==='trainer'     ? 'selected':'' }}>Trainer — Access assigned training schedules</option>
                    <option value="admin"       {{ old('role')==='admin'       ? 'selected':'' }}>Admin — Full system access</option>
                </select>
                <div class="role-note">⚠️ Admin role gives full access to all management screens.</div>
                @error('role') <div class="error-msg">{{ $message }}</div> @enderror
            </div>

            <div class="form-section">
                <h4>Profile Details (optional)</h4>
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Company</label>
                        <input type="text" name="company" value="{{ old('company') }}" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Designation</label>
                        <input type="text" name="designation" value="{{ old('designation') }}" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Country</label>
                        <input type="text" name="country" value="{{ old('country') }}" class="form-input">
                    </div>
                </div>
            </div>

            <div style="display:flex; gap:10px; margin-top:4px;">
                <button type="submit" class="btn-submit">Create User</button>
                <a href="{{ route('users.index') }}" class="btn-cancel">Cancel</a>
            </div>
        </form>
    </div>
</div>

@endsection
