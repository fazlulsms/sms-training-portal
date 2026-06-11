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
    <h2 style="font-size:24px; font-weight:800; color:#111827; margin-bottom:24px;">Add Trainer</h2>
    @if($errors->any())
    <div style="background:#fee2e2; border:1px solid #fca5a5; border-radius:8px; padding:14px; margin-bottom:20px;">
        <ul style="margin:0; padding-left:18px; color:#b91c1c; font-size:13.5px;">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif
    <form method="POST" action="/admin/trainers/store" enctype="multipart/form-data">
        @csrf
        <p class="sec-title">Basic Information</p>
        <div class="frow">
            <div class="fg">
                <label>Name <span style="color:red">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required>
            </div>
            <div class="fg">
                <label>Designation</label>
                <input type="text" name="designation" value="{{ old('designation') }}" placeholder="e.g. Lead Auditor, ISO Expert">
            </div>
        </div>
        <div class="frow">
            <div class="fg">
                <label>Organization</label>
                <input type="text" name="organization" value="{{ old('organization') }}">
            </div>
            <div class="fg">
                <label>Experience</label>
                <input type="text" name="experience" value="{{ old('experience') }}" placeholder="e.g. 15+ years">
            </div>
        </div>
        <div class="frow">
            <div class="fg">
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email') }}">
            </div>
            <div class="fg">
                <label>Phone</label>
                <input type="text" name="phone" value="{{ old('phone') }}">
            </div>
        </div>
        <div class="fg">
            <label>Qualification</label>
            <textarea name="qualification" rows="3">{{ old('qualification') }}</textarea>
        </div>
        <div class="frow">
            <div class="fg">
                <label>Status</label>
                <select name="status">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>
            <div class="fg">
                <label>Display Order</label>
                <input type="number" name="display_order" value="{{ old('display_order', 0) }}" min="0">
            </div>
        </div>

        <p class="sec-title">Public Profile</p>
        <div class="fg">
            <label>Profile Photo</label>
            <input type="file" name="photo" accept="image/*" style="padding:6px;">
        </div>
        <div class="fg">
            <label>Short Bio (shown on public profile)</label>
            <textarea name="short_bio" rows="4" placeholder="Brief professional biography...">{{ old('short_bio') }}</textarea>
        </div>
        <div class="fg">
            <label>Expertise Areas (one per line)</label>
            <textarea name="expertise_areas" rows="4" placeholder="ISO 9001 Quality Management&#10;ISO 14001 Environmental Management">{{ old('expertise_areas') }}</textarea>
        </div>
        <div class="fg">
            <label>Certifications / Credentials (one per line)</label>
            <textarea name="certifications" rows="4" placeholder="IRCA Certified Lead Auditor – ISO 9001&#10;NEBOSH International Diploma">{{ old('certifications') }}</textarea>
        </div>
        <div style="background:#f8fafc; border-radius:10px; padding:16px 20px; margin-bottom:20px;">
            <div class="toggle-row">
                <span class="tl">Show on Public Website</span>
                <label class="toggle-switch">
                    <input type="checkbox" name="is_public" value="1" {{ old('is_public') ? 'checked':'' }}>
                    <span class="slider"></span>
                </label>
            </div>
        </div>

        <div style="display:flex; gap:12px; margin-top:8px; padding-top:20px; border-top:1px solid #e5e7eb;">
            <button type="submit" style="background:#16a34a; color:#fff; padding:12px 28px; border:none; border-radius:8px; font-weight:700; font-size:15px; cursor:pointer;">
                Save Trainer
            </button>
            <a href="/admin/trainers" style="background:#6b7280; color:#fff; padding:12px 20px; border-radius:8px; text-decoration:none; font-weight:600; font-size:15px;">
                Back
            </a>
        </div>
    </form>
</div>
</div>
@endsection
