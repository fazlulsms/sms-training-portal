@extends('layouts.app')
@section('page-title', 'Quiz Management — ' . $enrollment->participant_name)
@section('content')

<x-page-header
    title="Quiz Management"
    desc="{{ $enrollment->participant_name }} — {{ $quiz->title }}">
    <x-slot:actions>
        <a href="{{ route('elearning.enrollments.show', $enrollment) }}" class="btn btn-ghost btn-sm">← Enrollment</a>
        <a href="{{ route('elearning.quizzes.preview', [$enrollment->course, $quiz->lesson, $quiz]) }}" class="btn btn-xs" style="background:#ede9fe;color:#4c1d95;">Preview Quiz</a>
    </x-slot:actions>
</x-page-header>

<x-flash-message />

<style>
.panel { background:#fff; border:1.5px solid #e2e8f0; border-radius:12px; overflow:hidden; margin-bottom:20px; }
.panel-header { padding:14px 20px; font-weight:700; font-size:14px; border-bottom:1px solid #f1f5f9; display:flex; align-items:center; gap:8px; }
.panel-body { padding:20px; }
.stat-row { display:grid; grid-template-columns:repeat(auto-fit,minmax(130px,1fr)); gap:12px; margin-bottom:4px; }
.stat { background:#f8fafc; border-radius:8px; padding:12px 14px; }
.stat-label { font-size:10.5px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#64748b; margin-bottom:3px; }
.stat-value { font-size:18px; font-weight:800; color:#1e293b; }
.badge-pass    { background:#dcfce7; color:#166534; font-size:11px; font-weight:700; padding:2px 9px; border-radius:20px; }
.badge-fail    { background:#fee2e2; color:#991b1b; font-size:11px; font-weight:700; padding:2px 9px; border-radius:20px; }
.badge-blocked { background:#fef3c7; color:#92400e; font-size:11px; font-weight:700; padding:2px 9px; border-radius:20px; }
.badge-pending { background:#f1f5f9; color:#475569; font-size:11px; font-weight:700; padding:2px 9px; border-radius:20px; }
.action-form { border:1.5px solid #e2e8f0; border-radius:10px; padding:16px 18px; }
.action-form h4 { font-size:13.5px; font-weight:700; margin-bottom:10px; display:flex; align-items:center; gap:8px; }
.action-form textarea {
    width:100%; border:1.5px solid #d1d5db; border-radius:7px; padding:9px 12px;
    font-size:13px; font-family:inherit; resize:vertical; min-height:70px; box-sizing:border-box;
}
.action-form textarea:focus { outline:none; border-color:#1e3a8a; }
.action-form .hint { font-size:11.5px; color:#6b7280; margin-bottom:8px; }
.at-table { width:100%; font-size:13px; border-collapse:collapse; }
.at-table th { background:#f8fafc; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:#64748b; padding:8px 12px; text-align:left; border-bottom:1.5px solid #e2e8f0; }
.at-table td { padding:9px 12px; border-bottom:1px solid #f1f5f9; vertical-align:top; }
.at-table tr:last-child td { border-bottom:none; }
.log-action { font-size:11px; font-weight:700; text-transform:uppercase; padding:2px 7px; border-radius:4px; }
.log-reset  { background:#fee2e2; color:#991b1b; }
.log-extra  { background:#dbeafe; color:#1e40af; }
.log-passed { background:#dcfce7; color:#166534; }
.blocked-banner {
    background:#fffbeb; border:2px solid #fcd34d; border-radius:10px;
    padding:14px 18px; margin-bottom:20px; display:flex; align-items:center; gap:12px;
    font-size:13.5px; color:#78350f;
}
</style>

{{-- Blocked warning --}}
@if($blocked)
<div class="blocked-banner">
    <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    <div>
        <strong>Learner is blocked.</strong>
        {{ $enrollment->participant_name }} has exhausted all {{ $effectiveMax }} attempt(s) without passing.
        Use one of the recovery actions below.
    </div>
</div>
@endif

{{-- Status overview --}}
<div class="panel">
    <div class="panel-header">
        📊 Quiz Status — {{ $quiz->title }}
    </div>
    <div class="panel-body">
        <div class="stat-row">
            <div class="stat">
                <div class="stat-label">Pass Mark</div>
                <div class="stat-value">{{ $quiz->pass_mark }}%</div>
            </div>
            <div class="stat">
                <div class="stat-label">Standard Max</div>
                <div class="stat-value">{{ $quiz->max_attempt }}</div>
            </div>
            <div class="stat">
                <div class="stat-label">Override Extras</div>
                <div class="stat-value">{{ $override?->extra_attempts ?? 0 }}</div>
            </div>
            <div class="stat">
                <div class="stat-label">Effective Max</div>
                <div class="stat-value">{{ $effectiveMax }}</div>
            </div>
            <div class="stat">
                <div class="stat-label">Attempts Taken</div>
                <div class="stat-value">{{ $attemptsTaken }}</div>
            </div>
            <div class="stat">
                <div class="stat-label">Best Score</div>
                <div class="stat-value">{{ $bestScore !== null ? number_format($bestScore, 1) . '%' : '—' }}</div>
            </div>
            <div class="stat">
                <div class="stat-label">Status</div>
                <div style="margin-top:5px;">
                    @if($passed)
                        <span class="badge-pass">✓ Passed</span>
                    @elseif($blocked)
                        <span class="badge-blocked">⚠ Blocked</span>
                    @elseif($attemptsTaken > 0)
                        <span class="badge-fail">✗ Failed</span>
                    @else
                        <span class="badge-pending">Not started</span>
                    @endif
                </div>
            </div>
        </div>

        @if($override)
        <div style="margin-top:14px; background:#eff6ff; border-radius:8px; padding:10px 14px; font-size:12.5px; color:#1e40af;">
            <strong>Active Override:</strong> {{ $override->extra_attempts }} extra attempt(s) granted by
            {{ $override->admin?->name ?? 'Admin' }} — "{{ $override->reason }}"
        </div>
        @endif
    </div>
</div>

{{-- Attempt history --}}
<div class="panel">
    <div class="panel-header">📋 Attempt History</div>
    <div class="panel-body" style="padding:0;">
        @if($attempts->isEmpty())
        <p style="padding:20px; color:#6b7280; text-align:center; font-size:13px;">No attempts recorded.</p>
        @else
        <table class="at-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Score</th>
                    <th>Correct / Total</th>
                    <th>Result</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attempts as $i => $a)
                <tr>
                    <td style="font-weight:700;">{{ $attempts->count() - $i }}</td>
                    <td style="font-weight:700; {{ $a->score >= $quiz->pass_mark ? 'color:#15803d' : 'color:#dc2626' }}">
                        {{ number_format($a->score, 1) }}%
                    </td>
                    <td>{{ $a->correct_answers }} / {{ $a->total_questions }}</td>
                    <td>
                        @if($a->score >= $quiz->pass_mark)
                            <span class="badge-pass">Passed</span>
                        @else
                            <span class="badge-fail">Failed</span>
                        @endif
                        @if($a->total_questions == $a->correct_answers && $a->score == 100)
                            <span style="font-size:10px;color:#6b7280;margin-left:4px;">(admin override)</span>
                        @endif
                    </td>
                    <td>{{ $a->created_at->format('d M Y, h:i A') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>

{{-- Admin recovery actions --}}
@if(!$passed)
<div class="panel">
    <div class="panel-header" style="background:#fef2f2; border-bottom-color:#fecaca;">
        🔧 Admin Recovery Actions
        <span style="font-size:11px; font-weight:400; color:#dc2626; margin-left:auto;">All actions are logged with audit trail.</span>
    </div>
    <div class="panel-body">
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">

            {{-- Reset Attempts --}}
            <div class="action-form">
                <h4 style="color:#dc2626;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.18"/></svg>
                    Reset Attempts
                </h4>
                <p class="hint">Deletes all existing attempts. Learner retakes from attempt 1.</p>
                <form method="POST" action="{{ route('elearning.quiz-admin.reset-attempts', [$enrollment, $quiz]) }}"
                      onsubmit="return confirm('This will delete all quiz attempts for {{ addslashes($enrollment->participant_name) }}. They will restart from attempt 1. Continue?');">
                    @csrf
                    <textarea name="reason" placeholder="Reason (required) — e.g. Learner had connectivity issues during exam" required></textarea>
                    <button type="submit" class="btn btn-sm" style="background:#dc2626;color:#fff;margin-top:10px;width:100%;">
                        Reset All Attempts
                    </button>
                </form>
            </div>

            {{-- Add Extra Attempt --}}
            <div class="action-form">
                <h4 style="color:#2563eb;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Add One Extra Attempt
                </h4>
                <p class="hint">Grants +1 attempt without changing the global quiz limit for other learners.</p>
                <form method="POST" action="{{ route('elearning.quiz-admin.add-extra-attempt', [$enrollment, $quiz]) }}">
                    @csrf
                    <textarea name="reason" placeholder="Reason (required) — e.g. Learner request approved by trainer" required></textarea>
                    <button type="submit" class="btn btn-primary btn-sm" style="margin-top:10px;width:100%;">
                        Grant Extra Attempt
                    </button>
                </form>
            </div>
        </div>

        {{-- Mark as Passed --}}
        <div class="action-form" style="border-color:#86efac;">
            <h4 style="color:#166534;">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                Mark as Passed (Admin Override)
            </h4>
            <p class="hint">Records a synthetic 100% attempt and marks the lesson complete. Use only with proper authorisation. This action is permanently logged.</p>
            <form method="POST" action="{{ route('elearning.quiz-admin.mark-passed', [$enrollment, $quiz]) }}"
                  onsubmit="return confirm('This will mark the quiz as passed for {{ addslashes($enrollment->participant_name) }} without them sitting the quiz again. The action will be permanently logged. Continue?');">
                @csrf
                <textarea name="reason" placeholder="Reason (required) — e.g. Learner attended live session and demonstrated competency. Approved by [Manager Name]." required></textarea>
                <button type="submit" class="btn btn-sm" style="background:#15803d;color:#fff;margin-top:10px;">
                    ✓ Mark as Passed
                </button>
            </form>
        </div>
    </div>
</div>
@else
<div class="panel" style="border-color:#86efac;">
    <div class="panel-body" style="text-align:center; padding:24px; color:#166534;">
        <div style="font-size:32px; margin-bottom:8px;">✅</div>
        <p style="font-weight:700;">Learner has passed this quiz. No recovery action needed.</p>
        <p style="font-size:12.5px; color:#15803d; margin-top:4px;">Best score: {{ number_format($bestScore, 1) }}% (Pass mark: {{ $quiz->pass_mark }}%)</p>
    </div>
</div>
@endif

{{-- Audit log --}}
@if($auditLog->isNotEmpty())
<div class="panel" style="margin-top:4px;">
    <div class="panel-header">🗒 Admin Action Audit Log</div>
    <div class="panel-body" style="padding:0;">
        <table class="at-table">
            <thead>
                <tr>
                    <th>Action</th>
                    <th>Admin</th>
                    <th>Reason</th>
                    <th>Prev Score</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($auditLog as $log)
                <tr>
                    <td>
                        <span class="log-action {{ match($log->action) {
                            'reset_attempts'    => 'log-reset',
                            'add_extra_attempt' => 'log-extra',
                            'mark_passed'       => 'log-passed',
                            default             => '',
                        } }}">{{ $log->actionLabel() }}</span>
                    </td>
                    <td>{{ $log->admin?->name ?? 'Admin #' . $log->admin_user_id }}</td>
                    <td style="max-width:260px; color:#475569;">{{ $log->reason }}</td>
                    <td>{{ $log->previous_score !== null ? number_format($log->previous_score, 1) . '%' : '—' }}</td>
                    <td style="white-space:nowrap;">{{ $log->created_at->format('d M Y, h:i A') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@endsection
