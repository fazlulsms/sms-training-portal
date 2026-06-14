@extends('layouts.public')

@section('page-title', 'Enrollment Confirmed — SMS Training Academy')

@push('head')
<style>
.enroll-success-wrap { min-height: 60vh; display: flex; align-items: center; justify-content: center; padding: 48px 16px 64px; }
.success-card-lg {
    background: #fff; border: 1px solid #e5e7eb; border-radius: 20px;
    box-shadow: 0 12px 48px rgba(30,58,138,.1); padding: 52px 44px;
    max-width: 620px; width: 100%; text-align: center;
}
@media(max-width:540px){ .success-card-lg { padding: 36px 24px; } }
.success-icon-wrap {
    width: 84px; height: 84px; background: linear-gradient(135deg,#dcfce7,#bbf7d0);
    border-radius: 50%; display: flex; align-items: center; justify-content: center;
    margin: 0 auto 22px; font-size: 38px;
}
.success-title  { font-size: 28px; font-weight: 900; color: #111827; margin: 0 0 10px; }
.success-sub    { font-size: 15px; color: #6b7280; margin: 0 0 28px; line-height: 1.6; }
.confirm-ref {
    display: inline-block; background: #f0f4ff; border: 1px solid #bfdbfe;
    border-radius: 10px; padding: 10px 22px; font-size: 14px; font-weight: 700;
    color: #1e3a8a; letter-spacing: .5px; margin-bottom: 28px;
}
.detail-grid {
    display: grid; grid-template-columns: 1fr 1fr;
    border: 1px solid #e9ecf0; border-radius: 14px; overflow: hidden; margin-bottom: 28px; text-align: left;
}
.detail-cell { padding: 14px 16px; border-bottom: 1px solid #e9ecf0; }
.detail-cell:nth-last-child(-n+2) { border-bottom: none; }
.detail-label { font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: .5px; color: #9ca3af; margin-bottom: 4px; }
.detail-value { font-size: 14.5px; font-weight: 700; color: #111827; }
@media(max-width:440px){ .detail-grid { grid-template-columns: 1fr; } .detail-cell:last-child { border-bottom: none; } }
.action-btns { display: flex; gap: 12px; flex-wrap: wrap; justify-content: center; margin-bottom: 22px; }
.btn-primary  { background: linear-gradient(135deg,#1e3a8a,#2563eb); color: #fff; padding: 13px 24px; border-radius: 10px; font-weight: 800; font-size: 14.5px; text-decoration: none; }
.btn-secondary{ background: #f0f4ff; color: #1e3a8a; border: 1.5px solid #bfdbfe; padding: 12px 24px; border-radius: 10px; font-weight: 700; font-size: 14.5px; text-decoration: none; }
.success-note { font-size: 13px; color: #9ca3af; line-height: 1.7; }
.success-note a { color: #6b7280; }
.next-steps { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 16px 18px; text-align: left; margin-bottom: 24px; }
.next-step-title { font-size: 13px; font-weight: 700; color: #166534; margin-bottom: 8px; }
.next-step-item { display: flex; align-items: center; gap: 8px; font-size: 13px; color: #166534; padding: 4px 0; }
</style>
@endpush

@section('content')

<div class="pub-container">
<div class="enroll-success-wrap">
<div class="success-card-lg">
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
            <div class="detail-value">{{ $enrollment->participant_name ?? $enrollment->full_name ?? $enrollment->user?->name }}</div>
        </div>
        <div class="detail-cell">
            <div class="detail-label">Email</div>
            <div class="detail-value" style="font-size:13px;">{{ $enrollment->participant_email ?? $enrollment->email ?? $enrollment->user?->email }}</div>
        </div>
        <div class="detail-cell">
            <div class="detail-label">Course</div>
            <div class="detail-value" style="font-size:13.5px;">{{ $enrollment->trainingSchedule?->course?->name }}</div>
        </div>
        <div class="detail-cell">
            <div class="detail-label">Batch / Date</div>
            <div class="detail-value">
                {{ $enrollment->trainingSchedule?->batch_code }}<br>
                <span style="font-size:12.5px;color:#6b7280;">
                    {{ $enrollment->trainingSchedule?->start_date ? \Carbon\Carbon::parse($enrollment->trainingSchedule->start_date)->format('d M Y') : '' }}
                </span>
            </div>
        </div>
        <div class="detail-cell">
            <div class="detail-label">Mode</div>
            <div class="detail-value">{{ $enrollment->participation_mode ?? $enrollment->selected_mode ?? $enrollment->trainingSchedule?->training_mode }}</div>
        </div>
        <div class="detail-cell">
            <div class="detail-label">Fee</div>
            <div class="detail-value">
                @php $fee = $enrollment->applied_fee ?? 0; @endphp
                {{ $fee ? 'BDT ' . number_format($fee) : 'TBA' }}
            </div>
        </div>
    </div>

    <div class="next-steps">
        <div class="next-step-title">What happens next?</div>
        @foreach(['You will receive a confirmation email with payment instructions','Make payment via bank transfer, bKash, or our online gateway','Attendance confirmation sent once payment is verified','Access training materials and join the session on the scheduled date'] as $step)
        <div class="next-step-item">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            {{ $step }}
        </div>
        @endforeach
    </div>

    <div class="action-btns">
        <a href="{{ route('login') }}" class="btn-primary">🔑 Login to My Account</a>
        <a href="{{ route('public.courses') }}" class="btn-secondary">Browse More Courses</a>
    </div>

    <p class="success-note">
        💡 A welcome email with your login credentials and enrollment details has been sent to your registered email address.<br>
        Need help? <a href="mailto:training@smscert.com">training@smscert.com</a>
    </p>
</div>
</div>
</div>

@endsection
