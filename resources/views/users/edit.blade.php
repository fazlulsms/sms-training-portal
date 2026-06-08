@extends('layouts.app')

@section('page-title', 'Edit User — ' . $user->name)

@section('content')

<style>
.form-wrap { padding: 28px; max-width: 780px; }
.back-btn { display:inline-flex; align-items:center; gap:6px; color:#1e3a8a; font-weight:700; text-decoration:none; font-size:14px; margin-bottom:18px; }
.form-card { background:#fff; border:1px solid #e5e7eb; border-radius:14px; padding:28px; box-shadow:0 1px 4px rgba(15,23,42,.06); margin-bottom:18px; }
.form-card h3 { font-size:15px; font-weight:800; color:#111827; margin:0 0 20px; display:flex; align-items:center; gap:8px; }
.form-group { margin-bottom:16px; }
.form-label { display:block; font-size:13px; font-weight:700; color:#374151; margin-bottom:5px; }
.form-input { width:100%; padding:9px 12px; border:1px solid #d1d5db; border-radius:8px; font-size:14px; font-family:inherit; color:#111827; }
.form-input:focus { outline:none; border-color:#1e3a8a; box-shadow:0 0 0 3px rgba(30,58,138,.1); }
.form-grid { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
.btn-submit { background:#1e3a8a; color:white; padding:10px 22px; border:none; border-radius:8px; font-weight:700; font-size:13px; cursor:pointer; font-family:inherit; }
.btn-danger { background:#dc2626; color:white; padding:10px 18px; border:none; border-radius:8px; font-weight:700; font-size:13px; cursor:pointer; font-family:inherit; }
.error-msg { color:#dc2626; font-size:12px; margin-top:4px; }
.alert { padding:12px 14px; border-radius:8px; font-weight:600; margin-bottom:14px; font-size:13px; }
.alert-success { background:#dcfce7; color:#166534; }
.alert-error   { background:#fee2e2; color:#991b1b; }

.enr-table { width:100%; border-collapse:collapse; }
.enr-table th { padding:9px 12px; font-size:11px; font-weight:700; color:#6b7280; text-transform:uppercase; background:#f9fafb; border-bottom:1px solid #e5e7eb; }
.enr-table td { padding:10px 12px; border-bottom:1px solid #f3f4f6; font-size:13px; }

.role-badge { display:inline-block; padding:3px 9px; border-radius:20px; font-size:11px; font-weight:700; }
.badge-admin       { background:#fef3c7; color:#92400e; }
.badge-trainer     { background:#dbeafe; color:#1e40af; }
.badge-participant { background:#f0fdf4; color:#166534; }

.toggle-switch { display:flex; align-items:center; gap:10px; }
.toggle-switch input[type=checkbox] { width:18px; height:18px; cursor:pointer; accent-color:#1e3a8a; }
</style>

<div class="form-wrap">

    <a href="{{ route('users.index') }}" class="back-btn">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
        Back to Users
    </a>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if(session('error'))   <div class="alert alert-error">{{ session('error') }}</div>   @endif

    {{-- Profile info --}}
    <div class="form-card">
        <h3>
            <div style="width:34px;height:34px;background:#1e3a8a;border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-weight:800;font-size:14px;">
                {{ strtoupper(substr($user->name,0,1)) }}
            </div>
            {{ $user->name }}
            <span class="role-badge badge-{{ $user->role }}">{{ ucfirst($user->role) }}</span>
        </h3>

        <form method="POST" action="{{ route('users.update', $user->id) }}">
            @csrf @method('PUT')

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
                    <label class="form-label">Role *</label>
                    <select name="role" class="form-input" required {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                        <option value="participant" {{ $user->role==='participant' ? 'selected':'' }}>Participant</option>
                        <option value="trainer"     {{ $user->role==='trainer'     ? 'selected':'' }}>Trainer</option>
                        <option value="admin"       {{ $user->role==='admin'       ? 'selected':'' }}>Admin</option>
                    </select>
                    @if($user->id === auth()->id())
                        <input type="hidden" name="role" value="{{ $user->role }}">
                        <div style="font-size:12px; color:#6b7280; margin-top:4px;">You cannot change your own role.</div>
                    @endif
                </div>
                <div class="form-group">
                    <label class="form-label">Account Status</label>
                    <div class="toggle-switch" style="margin-top:8px;">
                        <input type="checkbox" name="is_active" value="1" id="is_active"
                            {{ $user->is_active ? 'checked' : '' }}
                            {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                        <label for="is_active" style="font-size:14px; font-weight:600; cursor:pointer;">
                            Active — user can log in
                        </label>
                    </div>
                    @if($user->id === auth()->id())
                        <input type="hidden" name="is_active" value="1">
                    @endif
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

            <button type="submit" class="btn-submit">Save Changes</button>
        </form>
    </div>

    {{-- Reset password --}}
    <div class="form-card">
        <h3>
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            Reset Password
        </h3>
        <form method="POST" action="{{ route('users.reset-password', $user->id) }}">
            @csrf
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
            <button type="submit" class="btn-danger"
                onclick="return confirm('Reset password for {{ $user->name }}?')">
                Reset Password
            </button>
        </form>
    </div>

    {{-- eLearning enrollments --}}
    @if($enrollments->count() > 0)
    <div class="form-card">
        <h3>
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5-10-5z"/></svg>
            eLearning Enrollments ({{ $enrollments->count() }})
        </h3>
        <table class="enr-table">
            <thead>
                <tr>
                    <th>Course</th>
                    <th>Payment</th>
                    <th>Progress</th>
                    <th>Status</th>
                    <th>Certificate</th>
                </tr>
            </thead>
            <tbody>
                @foreach($enrollments as $enr)
                    <tr>
                        <td><strong>{{ $enr->course->name ?? '—' }}</strong></td>
                        <td>{{ ucfirst(str_replace('_',' ',$enr->payment_status)) }}</td>
                        <td>{{ $enr->progress_percentage }}%</td>
                        <td>{{ ucfirst(str_replace('_',' ',$enr->completion_status)) }}</td>
                        <td>{{ ucfirst(str_replace('_',' ',$enr->certificate_status)) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

</div>

@endsection
