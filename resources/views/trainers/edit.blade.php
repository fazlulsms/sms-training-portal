@extends('layouts.app')
@section('content')
<style>
.fg { margin-bottom:18px; }
.fg label { display:block; font-weight:600; font-size:13.5px; color:#374151; margin-bottom:6px; }
.fg input,.fg select,.fg textarea { width:100%; padding:10px 13px; border:1.5px solid #d1d5db; border-radius:8px; font-size:14px; font-family:inherit; outline:none; box-sizing:border-box; }
.fg input:focus,.fg select:focus,.fg textarea:focus { border-color:#1e3a8a; }
.frow { display:grid; grid-template-columns:1fr 1fr; gap:18px; }
.toggle-row { display:flex; align-items:center; gap:12px; padding:13px 0; }
.tl { font-weight:600; font-size:14px; color:#374151; flex:1; }
.toggle-switch { position:relative; display:inline-block; width:44px; height:24px; }
.toggle-switch input { opacity:0; width:0; height:0; }
.slider { position:absolute; inset:0; background:#d1d5db; border-radius:24px; cursor:pointer; transition:.25s; }
.slider:before { content:''; position:absolute; left:3px; bottom:3px; width:18px; height:18px; background:#fff; border-radius:50%; transition:.25s; }
.toggle-switch input:checked + .slider { background:#1e3a8a; }
.toggle-switch input:checked + .slider:before { transform:translateX(20px); }
.sec-title { font-size:15px; font-weight:700; color:#1e3a8a; margin:24px 0 14px; padding-bottom:8px; border-bottom:1px solid #e5e7eb; }
</style>
<div style="max-width:900px; margin:auto;">
<div style="background:#fff; padding:28px; border-radius:14px; box-shadow:0 4px 16px rgba(0,0,0,.07);">
    <h2 style="font-size:24px; font-weight:800; color:#111827; margin-bottom:24px;">Edit Trainer: {{ $trainer->name }}</h2>
    @if($errors->any())
    <div style="background:#fee2e2; border:1px solid #fca5a5; border-radius:8px; padding:14px; margin-bottom:20px;">
        <ul style="margin:0; padding-left:18px; color:#b91c1c; font-size:13.5px;">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif
    @if(session('success'))
    <div style="background:#dcfce7; border:1px solid #86efac; border-radius:8px; padding:12px 16px; margin-bottom:20px; color:#166534; font-weight:600;">{{ session('success') }}</div>
    @endif

    <form method="POST" action="/admin/trainers/update/{{ $trainer->id }}" enctype="multipart/form-data">
        @csrf
        <p class="sec-title">Basic Information</p>
        <div class="frow">
            <div class="fg">
                <label>Name <span style="color:red">*</span></label>
                <input type="text" name="name" value="{{ old('name', $trainer->name) }}" required>
            </div>
            <div class="fg">
                <label>Designation</label>
                <input type="text" name="designation" value="{{ old('designation', $trainer->designation) }}">
            </div>
        </div>
        <div class="frow">
            <div class="fg">
                <label>Organization</label>
                <input type="text" name="organization" value="{{ old('organization', $trainer->organization) }}">
            </div>
            <div class="fg">
                <label>Experience</label>
                <input type="text" name="experience" value="{{ old('experience', $trainer->experience) }}" placeholder="e.g. 15+ years">
            </div>
        </div>
        <div class="frow">
            <div class="fg">
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email', $trainer->email) }}">
            </div>
            <div class="fg">
                <label>Phone</label>
                <input type="text" name="phone" value="{{ old('phone', $trainer->phone) }}">
            </div>
        </div>
        <div class="fg">
            <label>Qualification</label>
            <textarea name="qualification" rows="3">{{ old('qualification', $trainer->qualification) }}</textarea>
        </div>
        <div class="frow">
            <div class="fg">
                <label>Status</label>
                <select name="status">
                    <option value="1" {{ old('status', $trainer->status)==1?'selected':'' }}>Active</option>
                    <option value="0" {{ old('status', $trainer->status)==0?'selected':'' }}>Inactive</option>
                </select>
            </div>
            <div class="fg">
                <label>Display Order</label>
                <input type="number" name="display_order" value="{{ old('display_order', $trainer->display_order ?? 0) }}" min="0">
            </div>
        </div>

        <p class="sec-title">Public Profile</p>
        <div class="fg">
            <label>Profile Photo (leave blank to keep existing)</label>
            @if($trainer->photo)
            <div style="margin-bottom:8px;">
                <img src="{{ asset('storage/'.$trainer->photo) }}" style="height:80px; width:80px; border-radius:50%; object-fit:cover; border:2px solid #e5e7eb;">
            </div>
            @endif
            <input type="file" name="photo" accept="image/*" style="padding:6px;">
        </div>
        <div class="fg">
            <label>Short Bio (shown on public profile)</label>
            <textarea name="short_bio" rows="4">{{ old('short_bio', $trainer->short_bio) }}</textarea>
        </div>
        <div class="fg">
            <label>Expertise Areas (one per line)</label>
            <textarea name="expertise_areas" rows="4">{{ old('expertise_areas', $trainer->expertise_areas) }}</textarea>
        </div>
        <div class="fg">
            <label>Certifications / Credentials (one per line)</label>
            <textarea name="certifications" rows="4">{{ old('certifications', $trainer->certifications) }}</textarea>
        </div>
        <div style="background:#f8fafc; border-radius:10px; padding:16px 20px; margin-bottom:20px;">
            <div class="toggle-row">
                <span class="tl">Show on Public Website</span>
                <label class="toggle-switch">
                    <input type="checkbox" name="is_public" value="1" {{ old('is_public', $trainer->is_public) ? 'checked':'' }}>
                    <span class="slider"></span>
                </label>
            </div>
        </div>

        <p class="sec-title">Link User Account</p>
        <div class="fg">
            <label>Link to User Account <small style="color:#6b7280; font-weight:400;">(gives trainer portal access)</small></label>
            <div style="background:#fef3c7; border:1px solid #fde68a; border-radius:8px; padding:10px 14px; margin-bottom:8px; font-size:13px; color:#92400e;">
                ⚠️ <strong>Do not link your own admin account.</strong> Create a separate user account for the trainer first.
            </div>
            <select name="user_id">
                <option value="">— Not linked —</option>
                @foreach($users as $user)
                    @if($user->id !== auth()->id())
                        <option value="{{ $user->id }}" {{ old('user_id', $trainer->user_id)==$user->id?'selected':'' }}>
                            {{ $user->name }} ({{ $user->email }}) — {{ ucfirst($user->role) }}
                        </option>
                    @endif
                @endforeach
            </select>
            <small style="color:#6b7280;">Linking sets the user's role to "trainer" automatically.</small>
        </div>

        <div style="display:flex; gap:12px; margin-top:8px; padding-top:20px; border-top:1px solid #e5e7eb;">
            <button type="submit" style="background:#16a34a; color:#fff; padding:12px 28px; border:none; border-radius:8px; font-weight:700; font-size:15px; cursor:pointer;">
                Update Trainer
            </button>
            <a href="/admin/trainers" style="background:#6b7280; color:#fff; padding:12px 20px; border-radius:8px; text-decoration:none; font-weight:600; font-size:15px;">
                Back
            </a>
        </div>
    </form>
</div>
</div>
@endsection
