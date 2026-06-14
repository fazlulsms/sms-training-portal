@php $isParticipant = Auth::user()?->isParticipant(); @endphp
@extends($isParticipant ? 'layouts.participant' : 'layouts.app')
@section('page-title', 'My Profile')
@section('content')

<style>
.pf-wrap { max-width: 760px; }
.pf-wrap h2 { font-size: 22px; font-weight: 800; color: #111827; margin: 0 0 6px; }
.pf-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 14px; padding: 26px; box-shadow: 0 1px 4px rgba(15,23,42,.06); margin-bottom: 18px; }
.pf-card h3 { font-size: 15px; font-weight: 800; color: #111827; margin: 0 0 20px; display: flex; align-items: center; gap: 7px; }
.fg { margin-bottom: 16px; }
.fg label { display: block; font-size: 13px; font-weight: 700; color: #374151; margin-bottom: 5px; }
.fg input, .fg select, .fg textarea {
    width: 100%; padding: 9px 12px; border: 1px solid #d1d5db;
    border-radius: 8px; font-size: 14px; font-family: inherit; outline: none;
    box-sizing: border-box;
}
.fg input:focus, .fg select:focus, .fg textarea:focus { border-color: #1e3a8a; box-shadow: 0 0 0 3px rgba(30,58,138,.1); }
.fg small { display: block; margin-top: 4px; font-size: 12px; color: #6b7280; }
.frow { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
@media (max-width: 600px) { .frow { grid-template-columns: 1fr; } }
.btn-save { background: #1e3a8a; color: white; padding: 10px 22px; border: none; border-radius: 8px; font-weight: 700; font-size: 13px; cursor: pointer; font-family: inherit; }
.btn-save:hover { background: #1d4ed8; }
.alert-ok  { padding: 12px 14px; border-radius: 8px; font-weight: 600; margin-bottom: 14px; font-size: 13px; background: #dcfce7; color: #166534; }
.alert-err { padding: 12px 14px; border-radius: 8px; font-weight: 600; margin-bottom: 14px; font-size: 13px; background: #fee2e2; color: #991b1b; }
.err-msg { color: #dc2626; font-size: 12px; margin-top: 4px; }
.role-chip { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; background: #eff6ff; color: #1e3a8a; margin-left: 8px; }

/* Photo upload */
.photo-section { display: flex; align-items: center; gap: 20px; margin-bottom: 22px; flex-wrap: wrap; }
.photo-preview {
    width: 80px; height: 80px; border-radius: 50%;
    background: #1e3a8a; color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-size: 26px; font-weight: 800;
    overflow: hidden; flex-shrink: 0;
    border: 3px solid #e5e7eb;
}
.photo-preview img { width: 100%; height: 100%; object-fit: cover; }
.photo-upload-btn {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 8px 16px; background: #f8fafc;
    border: 1.5px solid #d1d5db; border-radius: 8px;
    font-size: 13px; font-weight: 600; color: #374151;
    cursor: pointer; transition: border-color .15s;
}
.photo-upload-btn:hover { border-color: #1e3a8a; color: #1e3a8a; }
#photo-file { display: none; }
</style>

<div class="pf-wrap">
    <h2>My Profile <span class="role-chip">{{ ucfirst(Auth::user()->role) }}</span></h2>
    <p style="color:#6b7280;font-size:14px;margin:0 0 22px;">Update your personal information and change your password.</p>

    @if(session('success')) <div class="alert-ok">{{ session('success') }}</div> @endif
    @if($errors->any()) <div class="alert-err">{{ $errors->first() }}</div> @endif

    {{-- ── Profile form ── --}}
    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
        @csrf @method('PATCH')

        {{-- Photo --}}
        <div class="pf-card">
            <h3>
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                Profile Photo
            </h3>
            <div class="photo-section">
                <div class="photo-preview" id="photo-preview-wrap">
                    @if($user->photo_path)
                        <img src="{{ $user->photoUrl() }}" id="photo-img" alt="{{ $user->name }}">
                    @else
                        <span id="photo-initials">{{ $user->initials() }}</span>
                        <img src="" id="photo-img" style="display:none;" alt="">
                    @endif
                </div>
                <div>
                    <label class="photo-upload-btn" for="photo-file">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                        Upload Photo
                    </label>
                    <input type="file" name="photo" id="photo-file" accept="image/jpeg,image/png,image/webp">
                    <div style="margin-top:6px;font-size:12px;color:#6b7280;">JPG, PNG, or WEBP · Max 2MB</div>
                    @error('photo') <div class="err-msg">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        {{-- Personal info --}}
        <div class="pf-card">
            <h3>
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                Personal Information
            </h3>
            <div class="frow">
                <div class="fg">
                    <label>Full Name <span style="color:#dc2626">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
                    @error('name') <div class="err-msg">{{ $message }}</div> @enderror
                </div>
                <div class="fg">
                    <label>Email Address <span style="color:#dc2626">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
                    @error('email') <div class="err-msg">{{ $message }}</div> @enderror
                </div>
                <div class="fg">
                    <label>Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}">
                </div>
                <div class="fg">
                    <label>Country</label>
                    <input type="text" name="country" value="{{ old('country', $user->country) }}" placeholder="e.g. Bangladesh">
                </div>
                <div class="fg">
                    <label>Organisation / Company</label>
                    <input type="text" name="company" value="{{ old('company', $user->company) }}">
                </div>
                <div class="fg">
                    <label>Designation / Job Title</label>
                    <input type="text" name="designation" value="{{ old('designation', $user->designation) }}">
                </div>
                <div class="fg">
                    <label>Department</label>
                    <input type="text" name="department" value="{{ old('department', $user->department) }}" placeholder="e.g. Finance, HR, Operations">
                </div>
                <div class="fg">
                    <label>Preferred Language</label>
                    <select name="preferred_language">
                        @foreach(['en' => 'English', 'bn' => 'Bengali (বাংলা)', 'ar' => 'Arabic', 'fr' => 'French', 'es' => 'Spanish'] as $code => $lang)
                        <option value="{{ $code }}" {{ old('preferred_language', $user->preferred_language ?? 'en') === $code ? 'selected' : '' }}>{{ $lang }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="fg">
                <label>LinkedIn Profile URL</label>
                <input type="url" name="linkedin_url" value="{{ old('linkedin_url', $user->linkedin_url) }}" placeholder="https://linkedin.com/in/yourprofile">
                @error('linkedin_url') <div class="err-msg">{{ $message }}</div> @enderror
            </div>
            <div class="fg" style="margin-bottom:0;">
                <label>Short Bio</label>
                <textarea name="bio" rows="3" placeholder="A brief introduction about yourself…" style="resize:vertical;">{{ old('bio', $user->bio) }}</textarea>
                <small>Max 1000 characters. Visible to admin and trainers.</small>
                @error('bio') <div class="err-msg">{{ $message }}</div> @enderror
            </div>
        </div>

        {{-- Emergency contact --}}
        <div class="pf-card">
            <h3>
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13 19.79 19.79 0 0 1 1.61 4.39 2 2 0 0 1 3.58 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 9.91a16 16 0 0 0 6 6l.92-.92a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 17z"/></svg>
                Emergency Contact
                <span style="font-size:11px;font-weight:400;color:#9ca3af;">(Optional)</span>
            </h3>
            <div class="frow">
                <div class="fg" style="margin-bottom:0;">
                    <label>Contact Name</label>
                    <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name', $user->emergency_contact_name) }}" placeholder="e.g. Jane Doe">
                </div>
                <div class="fg" style="margin-bottom:0;">
                    <label>Contact Phone</label>
                    <input type="text" name="emergency_contact_phone" value="{{ old('emergency_contact_phone', $user->emergency_contact_phone) }}" placeholder="e.g. +880 1700-000000">
                </div>
            </div>
        </div>

        <div style="margin-bottom:24px;">
            <button type="submit" class="btn-save">Save Profile</button>
        </div>
    </form>

    {{-- ── Change password ── --}}
    <div class="pf-card">
        <h3>
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            Change Password
        </h3>
        <form method="POST" action="{{ route('profile.password') }}">
            @csrf
            <div class="fg">
                <label>Current Password <span style="color:#dc2626">*</span></label>
                <input type="password" name="current_password" required>
                @error('current_password') <div class="err-msg">{{ $message }}</div> @enderror
            </div>
            <div class="frow">
                <div class="fg" style="margin-bottom:0;">
                    <label>New Password <span style="color:#dc2626">*</span></label>
                    <input type="password" name="password" required minlength="8">
                    @error('password') <div class="err-msg">{{ $message }}</div> @enderror
                </div>
                <div class="fg" style="margin-bottom:0;">
                    <label>Confirm New Password <span style="color:#dc2626">*</span></label>
                    <input type="password" name="password_confirmation" required>
                </div>
            </div>
            <div style="margin-top:18px;">
                <button type="submit" class="btn-save" style="background:#dc2626;">Change Password</button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('photo-file').addEventListener('change', function() {
    var file = this.files[0];
    if (!file) return;
    var reader = new FileReader();
    reader.onload = function(e) {
        var img = document.getElementById('photo-img');
        var initials = document.getElementById('photo-initials');
        img.src = e.target.result;
        img.style.display = 'block';
        if (initials) initials.style.display = 'none';
    };
    reader.readAsDataURL(file);
});
</script>

@endsection
