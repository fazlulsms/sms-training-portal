@extends('layouts.app')
@section('page-title', 'New eLearning Enrollment')
@section('content')

<style>
.el-card{background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:32px;max-width:820px;margin:0 auto;box-shadow:0 1px 8px rgba(0,0,0,.06)}
.el-title{font-size:22px;font-weight:800;color:#1e293b;margin-bottom:4px}
.el-sub{font-size:13px;color:#64748b;margin-bottom:28px}
.el-section-title{font-size:13px;font-weight:800;color:#1e3a8a;text-transform:uppercase;letter-spacing:.06em;margin:24px 0 12px;padding-bottom:6px;border-bottom:2px solid #e2e8f0}
.el-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:16px}
.el-fg{display:flex;flex-direction:column}
.el-fg.full{grid-column:1/-1}
.el-fg label{font-size:12px;font-weight:700;color:#475569;margin-bottom:5px}
.el-fg input,.el-fg select{border:1px solid #cbd5e1;border-radius:8px;padding:9px 13px;font-size:13px;color:#334155;background:#fff;width:100%;box-sizing:border-box}
.el-fg input:focus,.el-fg select:focus{border-color:#1e3a8a;outline:none;box-shadow:0 0 0 3px rgba(30,58,138,.1)}
.el-actions{margin-top:28px;display:flex;gap:12px;justify-content:flex-end;border-top:1px solid #e2e8f0;padding-top:20px}
.btn-primary{background:#1e3a8a;color:#fff;border:none;border-radius:8px;padding:11px 26px;font-size:13px;font-weight:700;cursor:pointer}
.btn-secondary{background:#f1f5f9;color:#475569;border:1px solid #e2e8f0;border-radius:8px;padding:11px 20px;font-size:13px;font-weight:600;text-decoration:none}
.seq-box{background:linear-gradient(135deg,#f0f9ff,#dbeafe);border:1px solid #bfdbfe;border-radius:12px;padding:16px 20px;margin-bottom:20px}
.seq-box-title{font-size:13px;font-weight:800;color:#1e40af;margin-bottom:10px}
.seq-step{display:flex;align-items:flex-start;gap:10px;margin-bottom:8px;font-size:13px;color:#374151}
.seq-num{width:22px;height:22px;background:#1e3a8a;color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:900;flex-shrink:0;margin-top:1px}
.seq-step:last-child{margin-bottom:0}
.pay-info-box{background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:14px 18px;display:flex;align-items:center;gap:12px;font-size:13px;color:#92400e;margin-top:16px}
@media(max-width:640px){.el-grid{grid-template-columns:1fr}}
</style>

<x-page-header title="New eLearning Enrollment" desc="Register a participant for an eLearning course." />

<div class="el-card">

    @if($errors->any())
    <div style="background:#fef2f2;border:1px solid #fecaca;color:#b91c1c;border-radius:8px;padding:14px 18px;margin-bottom:20px;font-size:13px;">
        @foreach($errors->all() as $err)<div>• {{ $err }}</div>@endforeach
    </div>
    @endif

    {{-- Enrollment sequence info --}}
    <div class="seq-box">
        <div class="seq-box-title">📋 Enrollment Sequence</div>
        <div class="seq-step"><div class="seq-num">1</div><div><strong>Register</strong> — Fill this form. Registration confirmation email + invoice sent automatically.</div></div>
        <div class="seq-step"><div class="seq-num">2</div><div><strong>Invoice Created</strong> — Auto-invoice generated with payment pending status.</div></div>
        <div class="seq-step"><div class="seq-num">3</div><div><strong>Update Payment</strong> — Admin clicks <strong>💳 Pay</strong> on the enrollment or invoice to record payment.</div></div>
        <div class="seq-step"><div class="seq-num">4</div><div><strong>Access Granted</strong> — Course unlocked automatically. Welcome email with login credentials sent to participant.</div></div>
    </div>

    <form action="{{ route('elearning.enrollments.store') }}" method="POST">
        @csrf

        <div class="el-section-title">Course</div>
        <div class="el-fg">
            <label>eLearning Course <span style="color:#ef4444">*</span></label>
            <select name="course_id" required>
                <option value="">Select Course…</option>
                @foreach($courses as $course)
                <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                    {{ $course->name }} — {{ number_format($course->course_fee ?? 0) }} BDT
                </option>
                @endforeach
            </select>
        </div>

        <div class="el-section-title">Participant Information</div>
        <div class="el-grid">
            <div class="el-fg full">
                <label>Full Name <span style="color:#ef4444">*</span></label>
                <input type="text" name="participant_name" value="{{ old('participant_name') }}" required placeholder="Participant's full name">
            </div>
            <div class="el-fg">
                <label>Email Address <span style="color:#ef4444">*</span></label>
                <input type="email" name="email" value="{{ old('email') }}" required placeholder="email@example.com">
            </div>
            <div class="el-fg">
                <label>Phone</label>
                <input type="text" name="phone" value="{{ old('phone') }}" placeholder="+880…">
            </div>
            <div class="el-fg">
                <label>Company / Organisation</label>
                <input type="text" name="company" value="{{ old('company') }}">
            </div>
            <div class="el-fg">
                <label>Designation</label>
                <input type="text" name="designation" value="{{ old('designation') }}">
            </div>
        </div>

        {{-- Payment info notice (not a form field) --}}
        <div class="pay-info-box">
            💰 <div><strong>Payment fields are not set during registration.</strong> An invoice will be auto-created with Pending status. Use the <strong>💳 Pay</strong> button after registration to record payment and activate course access.</div>
        </div>

        <div class="el-actions">
            <a href="{{ route('elearning.enrollments.index') }}" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary">✅ Create Enrollment</button>
        </div>
    </form>
</div>

@endsection
