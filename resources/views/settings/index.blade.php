@extends('layouts.app')

@section('page-title', 'E-Learning Settings')

@section('content')

<style>
.s-wrap { padding: 28px; max-width: 820px; }
.s-wrap h2 { font-size:22px; font-weight:800; color:#111827; margin:0 0 6px; }
.s-wrap p  { color:#6b7280; font-size:14px; margin:0 0 24px; }

.alert { padding:12px 16px; border-radius:8px; font-weight:600; margin-bottom:16px; font-size:13px; }
.alert-success { background:#dcfce7; color:#166534; }

.s-card { background:#fff; border:1px solid #e5e7eb; border-radius:14px; overflow:hidden; box-shadow:0 1px 4px rgba(15,23,42,.06); margin-bottom:18px; }
.s-card-header { padding:14px 20px; border-bottom:1px solid #f3f4f6; font-size:13px; font-weight:800; color:#374151; text-transform:uppercase; letter-spacing:.5px; background:#f9fafb; }
.s-card-body { padding:22px; }

.s-row { display:flex; align-items:center; justify-content:space-between; padding:12px 0; border-bottom:1px solid #f3f4f6; gap:20px; }
.s-row:last-child { border-bottom:none; }
.s-label { font-size:14px; font-weight:700; color:#111827; }
.s-desc  { font-size:12px; color:#9ca3af; margin-top:2px; }

.s-input { padding:8px 12px; border:1px solid #d1d5db; border-radius:8px; font-size:14px; font-family:inherit; width:120px; text-align:center; }
.s-select { padding:8px 12px; border:1px solid #d1d5db; border-radius:8px; font-size:14px; font-family:inherit; }

.btn-save { background:#1e3a8a; color:white; padding:11px 28px; border:none; border-radius:9px; font-weight:700; font-size:14px; cursor:pointer; font-family:inherit; margin-top:8px; }
</style>

<div class="s-wrap">
    <h2>E-Learning Settings</h2>
    <p>Configure completion rules, certificate eligibility, and participant access.</p>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

    <form method="POST" action="{{ route('settings.update') }}">
        @csrf @method('PUT')

        {{-- General --}}
        <div class="s-card">
            <div class="s-card-header">General</div>
            <div class="s-card-body">
                <div class="s-row">
                    <div>
                        <div class="s-label">Default Pass Mark (%)</div>
                        <div class="s-desc">Minimum quiz score required to pass a lesson quiz.</div>
                    </div>
                    <input type="number" name="settings[elearning.default_pass_mark]"
                           value="{{ $settings['elearning.default_pass_mark']->value ?? 70 }}"
                           class="s-input" min="1" max="100">
                </div>
                <div class="s-row">
                    <div>
                        <div class="s-label">Minimum Attendance for Certificate (%)</div>
                        <div class="s-desc">For hybrid/live training — minimum attendance % required.</div>
                    </div>
                    <input type="number" name="settings[elearning.min_attendance_pct]"
                           value="{{ $settings['elearning.min_attendance_pct']->value ?? 80 }}"
                           class="s-input" min="0" max="100">
                </div>
            </div>
        </div>

        {{-- Completion --}}
        <div class="s-card">
            <div class="s-card-header">Completion Rules</div>
            <div class="s-card-body">
                <div class="s-row">
                    <div>
                        <div class="s-label">Completion Requires Quiz Pass</div>
                        <div class="s-desc">If enabled, participants must pass the lesson quiz to mark a lesson complete.</div>
                    </div>
                    <select name="settings[elearning.completion_requires_quiz]" class="s-select">
                        <option value="1" {{ ($settings['elearning.completion_requires_quiz']->value ?? '1') === '1' ? 'selected' : '' }}>Yes (Required)</option>
                        <option value="0" {{ ($settings['elearning.completion_requires_quiz']->value ?? '1') === '0' ? 'selected' : '' }}>No (Optional)</option>
                    </select>
                </div>
                <div class="s-row">
                    <div>
                        <div class="s-label">Payment Must Be Cleared for Completion</div>
                        <div class="s-desc">If enabled, completion status is only set when payment is paid or approved.</div>
                    </div>
                    <select name="settings[elearning.completion_requires_payment]" class="s-select">
                        <option value="1" {{ ($settings['elearning.completion_requires_payment']->value ?? '1') === '1' ? 'selected' : '' }}>Yes (Required)</option>
                        <option value="0" {{ ($settings['elearning.completion_requires_payment']->value ?? '1') === '0' ? 'selected' : '' }}>No</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Certificate --}}
        <div class="s-card">
            <div class="s-card-header">Certificate</div>
            <div class="s-card-body">
                <div class="s-row">
                    <div>
                        <div class="s-label">Auto-Set Certificate Eligible</div>
                        <div class="s-desc">Automatically mark enrollment as "eligible" when all conditions are met.</div>
                    </div>
                    <select name="settings[elearning.auto_eligible]" class="s-select">
                        <option value="1" {{ ($settings['elearning.auto_eligible']->value ?? '1') === '1' ? 'selected' : '' }}>Yes (Automatic)</option>
                        <option value="0" {{ ($settings['elearning.auto_eligible']->value ?? '1') === '0' ? 'selected' : '' }}>No (Manual only)</option>
                    </select>
                </div>
                <div class="s-row">
                    <div>
                        <div class="s-label">Admin Approval Required to Issue Certificate</div>
                        <div class="s-desc">If yes, eligible participants wait for admin to manually issue.</div>
                    </div>
                    <select name="settings[elearning.admin_approval_required]" class="s-select">
                        <option value="1" {{ ($settings['elearning.admin_approval_required']->value ?? '1') === '1' ? 'selected' : '' }}>Yes (Approval needed)</option>
                        <option value="0" {{ ($settings['elearning.admin_approval_required']->value ?? '1') === '0' ? 'selected' : '' }}>No (Auto-issue)</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Participant Access --}}
        <div class="s-card">
            <div class="s-card-header">Participant Access</div>
            <div class="s-card-body">
                <div class="s-row">
                    <div>
                        <div class="s-label">Allow Self-Registration</div>
                        <div class="s-desc">If enabled, anyone can register for a participant account via the login page.</div>
                    </div>
                    <select name="settings[elearning.allow_self_registration]" class="s-select">
                        <option value="1" {{ ($settings['elearning.allow_self_registration']->value ?? '1') === '1' ? 'selected' : '' }}>Yes (Public registration on)</option>
                        <option value="0" {{ ($settings['elearning.allow_self_registration']->value ?? '1') === '0' ? 'selected' : '' }}>No (Admin creates accounts only)</option>
                    </select>
                </div>
                <div class="s-row">
                    <div>
                        <div class="s-label">Auto-Link Enrollment by Email</div>
                        <div class="s-desc">When a user logs in, automatically link their email-matched enrollments to their account.</div>
                    </div>
                    <select name="settings[elearning.auto_link_enrollment]" class="s-select">
                        <option value="1" {{ ($settings['elearning.auto_link_enrollment']->value ?? '1') === '1' ? 'selected' : '' }}>Yes (Auto-link)</option>
                        <option value="0" {{ ($settings['elearning.auto_link_enrollment']->value ?? '1') === '0' ? 'selected' : '' }}>No</option>
                    </select>
                </div>
            </div>
        </div>

        <button type="submit" class="btn-save">Save Settings</button>
    </form>
</div>

@endsection
