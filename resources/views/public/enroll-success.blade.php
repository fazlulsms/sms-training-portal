<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Enrollment Confirmed — SMS Training Services</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing:border-box; }
body { margin:0; font-family:'Inter',sans-serif; background:#f0f4ff; color:#111827; min-height:100vh; display:flex; flex-direction:column; }
.enroll-topbar { background:#0f172a; padding:14px 24px; display:flex; align-items:center; justify-content:space-between; }
.enroll-logo { color:#fff; font-size:18px; font-weight:900; text-decoration:none; }
.enroll-logo span { color:#3b82f6; }
.success-body { flex:1; display:flex; align-items:center; justify-content:center; padding:32px 16px; }
.success-card { background:#fff; border-radius:20px; box-shadow:0 12px 48px rgba(30,58,138,.1); padding:48px 40px; max-width:580px; width:100%; text-align:center; }
@media(max-width:540px){ .success-card{padding:32px 22px;} }
.success-icon-wrap { width:80px; height:80px; background:linear-gradient(135deg,#dcfce7,#bbf7d0); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 20px; font-size:36px; }
.success-title { font-size:28px; font-weight:900; color:#111827; margin:0 0 10px; }
.success-sub { font-size:15px; color:#6b7280; margin:0 0 28px; line-height:1.6; }

.confirm-ref { display:inline-block; background:#f0f4ff; border:1px solid #bfdbfe; border-radius:10px; padding:10px 20px; font-size:14px; font-weight:700; color:#1e3a8a; letter-spacing:.5px; margin-bottom:28px; }

.detail-grid { display:grid; grid-template-columns:1fr 1fr; gap:0; border:1px solid #e9ecf0; border-radius:14px; overflow:hidden; margin-bottom:28px; text-align:left; }
.detail-cell { padding:14px 16px; border-bottom:1px solid #e9ecf0; }
.detail-cell:nth-last-child(-n+2) { border-bottom:none; }
.detail-label { font-size:11.5px; font-weight:800; text-transform:uppercase; letter-spacing:.5px; color:#9ca3af; margin-bottom:4px; }
.detail-value { font-size:14.5px; font-weight:700; color:#111827; }
@media(max-width:420px){ .detail-grid{grid-template-columns:1fr;} .detail-cell:last-child{border-bottom:none;} }

.action-btns { display:flex; gap:12px; flex-wrap:wrap; justify-content:center; margin-bottom:20px; }
.btn-primary { background:linear-gradient(135deg,#1e3a8a,#2563eb); color:#fff; padding:13px 24px; border-radius:10px; font-weight:800; font-size:14.5px; text-decoration:none; }
.btn-secondary { background:#f0f4ff; color:#1e3a8a; border:1.5px solid #bfdbfe; padding:12px 24px; border-radius:10px; font-weight:700; font-size:14.5px; text-decoration:none; }
.success-note { font-size:13px; color:#9ca3af; line-height:1.6; }
.success-note a { color:#6b7280; }
</style>
</head>
<body>

<div class="enroll-topbar">
    <a href="{{ url('/') }}" class="enroll-logo">SMS <span>Training</span></a>
</div>

<div class="success-body">
    <div class="success-card">
        <div class="success-icon-wrap">✅</div>
        <h1 class="success-title">You're Enrolled!</h1>
        <p class="success-sub">
            Your enrollment has been successfully submitted.<br>
            We've sent a confirmation email with all the details.
        </p>

        <div class="confirm-ref">
            Reference: <strong>{{ $enrollment->enrollment_code ?? 'ENR-' . str_pad($enrollment->id, 6, '0', STR_PAD_LEFT) }}</strong>
        </div>

        <div class="detail-grid">
            <div class="detail-cell">
                <div class="detail-label">Participant</div>
                <div class="detail-value">{{ $enrollment->participant_name ?? $enrollment->user?->name }}</div>
            </div>
            <div class="detail-cell">
                <div class="detail-label">Email</div>
                <div class="detail-value" style="font-size:13.5px;">{{ $enrollment->participant_email ?? $enrollment->user?->email }}</div>
            </div>
            <div class="detail-cell">
                <div class="detail-label">Course</div>
                <div class="detail-value">{{ $enrollment->trainingSchedule?->course?->name }}</div>
            </div>
            <div class="detail-cell">
                <div class="detail-label">Batch</div>
                <div class="detail-value">{{ $enrollment->trainingSchedule?->batch_code }}</div>
            </div>
            <div class="detail-cell">
                <div class="detail-label">Start Date</div>
                <div class="detail-value">{{ $enrollment->trainingSchedule?->start_date ? \Carbon\Carbon::parse($enrollment->trainingSchedule->start_date)->format('d M Y') : '—' }}</div>
            </div>
            <div class="detail-cell">
                <div class="detail-label">Mode</div>
                <div class="detail-value">{{ $enrollment->participation_mode ?? $enrollment->trainingSchedule?->training_mode }}</div>
            </div>
        </div>

        <div class="action-btns">
            <a href="{{ route('login') }}" class="btn-primary">🔑 Login to My Account</a>
            <a href="{{ route('public.courses') }}" class="btn-secondary">Browse More Courses</a>
        </div>

        <p class="success-note">
            💡 A welcome email with your login credentials and course access details has been sent to your email.<br>
            Need help? <a href="mailto:training@smscert.com">training@smscert.com</a>
        </p>
    </div>
</div>

</body>
</html>
