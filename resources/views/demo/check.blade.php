@extends('layouts.app')

@section('page-title', '🧪 Demo LMS Check')

@section('content')

<style>
.demo-wrap { padding: 24px; }
.demo-wrap h2 { font-size:20px; font-weight:800; color:#111827; margin:0 0 4px; }

/* Alerts */
.alert { padding:12px 16px; border-radius:8px; font-weight:600; margin-bottom:16px; font-size:13px; }
.alert-success { background:#dcfce7; color:#166534; }
.alert-error   { background:#fee2e2; color:#991b1b; }
.alert-warn    { background:#fef3c7; color:#92400e; border:1px solid #fde68a; }

/* Cards */
.demo-card { background:#fff; border:1px solid #e5e7eb; border-radius:12px; overflow:hidden; box-shadow:0 1px 4px rgba(15,23,42,.06); margin-bottom:16px; }
.demo-card-header {
    padding:12px 18px; border-bottom:1px solid #f3f4f6; font-size:13px; font-weight:800; color:#374151;
    display:flex; align-items:center; justify-content:space-between; background:#f9fafb;
}
.demo-card-body { padding:18px; }

/* Section dividers */
.section-label {
    font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:.6px;
    color:#6b7280; margin:20px 0 10px; padding:0 2px;
}

/* Tables */
.dt { width:100%; border-collapse:collapse; font-size:13px; }
.dt th { padding:8px 12px; text-align:left; font-size:11px; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:.4px; background:#f9fafb; border-bottom:1px solid #e5e7eb; }
.dt td { padding:10px 12px; border-bottom:1px solid #f3f4f6; vertical-align:middle; }
.dt tbody tr:last-child td { border-bottom:none; }
.dt tbody tr:hover td { background:#fafafa; }

/* Badges */
.badge { display:inline-block; padding:2px 8px; border-radius:20px; font-size:11px; font-weight:700; }
.b-green  { background:#dcfce7; color:#166534; }
.b-red    { background:#fee2e2; color:#991b1b; }
.b-yellow { background:#fef3c7; color:#92400e; }
.b-blue   { background:#dbeafe; color:#1e40af; }
.b-purple { background:#f5f3ff; color:#6d28d9; }
.b-gray   { background:#f3f4f6; color:#6b7280; }
.b-orange { background:#ffedd5; color:#9a3412; }

/* Progress bar */
.pbar { background:#e5e7eb; border-radius:20px; height:8px; overflow:hidden; width:100px; display:inline-block; vertical-align:middle; margin-left:6px; }
.pbar-fill { height:8px; border-radius:20px; background:linear-gradient(90deg,#0f766e,#14b8a6); }
.pbar-fill.done { background:linear-gradient(90deg,#16a34a,#4ade80); }

/* Action buttons */
.btn-demo {
    display:inline-flex; align-items:center; gap:5px;
    padding:5px 10px; border-radius:7px; font-size:11.5px; font-weight:700;
    border:none; cursor:pointer; font-family:inherit; text-decoration:none; white-space:nowrap;
}
.btn-reset    { background:#fee2e2; color:#991b1b; }
.btn-complete { background:#dcfce7; color:#166534; }
.btn-pass     { background:#dbeafe; color:#1e40af; }
.btn-fail     { background:#fef3c7; color:#92400e; }
.btn-calc     { background:#f3f4f6; color:#374151; }
.btn-view     { background:#f3f4f6; color:#374151; }
.btn-demo:hover { filter:brightness(.94); }

/* Grid layouts */
.two-col   { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
.three-col { display:grid; grid-template-columns:1fr 1fr 1fr; gap:14px; }

/* Scenario steps */
.scenario { background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:14px 16px; margin-bottom:10px; }
.scenario h4 { margin:0 0 8px; font-size:13px; font-weight:800; color:#1e3a8a; }
.scenario ol  { margin:0; padding-left:18px; font-size:12px; color:#374151; line-height:1.8; }

/* Purpose chip */
.purpose-chip {
    font-size:11px; color:#6b7280; background:#f3f4f6; border-radius:6px;
    padding:2px 7px; display:inline-block; margin-top:3px;
}

/* Journey tab / highlight */
.journey-header {
    background:linear-gradient(135deg,#0f766e,#0d9488);
}

@media(max-width:900px) { .two-col, .three-col { grid-template-columns:1fr; } }
</style>

<div class="demo-wrap">

    {{-- ── Header ──────────────────────────────────────────── --}}
    <div style="background:linear-gradient(135deg,#1e3a8a,#1e40af); border-radius:12px; padding:18px 22px; color:white; margin-bottom:16px;">
        <h2 style="color:white; margin:0 0 4px;">🧪 LMS Demo Environment</h2>
        <p style="margin:0; opacity:.85; font-size:13px;">
            Admin-only test console. All users, courses, and enrollments on this page are <strong>demo data only</strong>.
            Demo actions are blocked in production.
        </p>
    </div>

    @if(session('demo_success'))
        <div class="alert alert-success">{{ session('demo_success') }}</div>
    @endif
    @if(session('demo_error'))
        <div class="alert alert-error">{{ session('demo_error') }}</div>
    @endif

    <div class="alert alert-warn">
        ⚠️ <strong>All demo records use the @sms.test email domain.</strong>
        Actions on this page only affect those enrollments. Real participant data is never touched.
    </div>

    {{-- ═══════════════════════════════════════════════════════════════
         SECTION A — DEMO-LMS-001 (original 3-participant demo)
    ══════════════════════════════════════════════════════════════════ --}}
    <div class="section-label">📦 Demo Course: DEMO-LMS-001 (Original)</div>

    {{-- A1: Credentials --}}
    <div class="demo-card">
        <div class="demo-card-header">🔐 Demo Login Credentials — DEMO-LMS-001</div>
        <div style="overflow-x:auto;">
            <table class="dt">
                <thead>
                    <tr><th>Role</th><th>Name</th><th>Email</th><th>Password</th><th>Landing</th><th>Status</th></tr>
                </thead>
                <tbody>
                    @foreach($credentials as [$role, $email, $pass, $roleKey, $landing])
                        @php $u = $users->get($email); @endphp
                        <tr>
                            <td><span class="badge b-blue">{{ $role }}</span></td>
                            <td>{{ $u?->name ?? '—' }}</td>
                            <td><code style="font-size:11.5px;">{{ $email }}</code></td>
                            <td><code style="font-size:11.5px; background:#f3f4f6; padding:2px 6px; border-radius:4px;">{{ $pass }}</code></td>
                            <td><a href="{{ $landing }}" style="color:#1e3a8a; font-size:12px;">{{ $landing }}</a></td>
                            <td>
                                @if($u)
                                    <span class="badge {{ $u->is_active ? 'b-green' : 'b-red' }}">{{ $u->is_active ? '✓ Active' : '✗ Inactive' }}</span>
                                @else
                                    <span class="badge b-red">Not created — run seeder</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- A2: Course + Lessons --}}
    <div class="two-col">
        <div class="demo-card">
            <div class="demo-card-header">📚 Demo Course — DEMO-LMS-001</div>
            <div class="demo-card-body">
                @if($course)
                    <table class="dt">
                        <tr><td style="color:#9ca3af;font-size:12px;">ID</td><td><strong>{{ $course->id }}</strong></td></tr>
                        <tr><td style="color:#9ca3af;font-size:12px;">Name</td><td>{{ $course->name }}</td></tr>
                        <tr><td style="color:#9ca3af;font-size:12px;">Code</td><td><code>{{ $course->code }}</code></td></tr>
                        <tr><td style="color:#9ca3af;font-size:12px;">Lessons</td><td>{{ $lessons->count() }}</td></tr>
                        <tr><td style="color:#9ca3af;font-size:12px;">Final Quiz</td><td>
                            {{ $quiz ? $quiz->title . ' (pass: ' . $quiz->pass_mark . '%)' : '—' }}
                        </td></tr>
                        <tr><td style="color:#9ca3af;font-size:12px;">Questions</td><td>{{ $quiz?->questions()->count() ?? '—' }}</td></tr>
                    </table>
                @else
                    <div class="alert alert-error" style="margin:0;">Not found. Run: <code>php artisan db:seed --class=DemoLmsSeeder</code></div>
                @endif
            </div>
        </div>
        <div class="demo-card">
            <div class="demo-card-header">📄 Lessons — DEMO-LMS-001</div>
            <div class="demo-card-body">
                @forelse($lessons as $lesson)
                    @php $hasQuiz = \App\Models\ElearningQuiz::where('lesson_id',$lesson->id)->exists(); @endphp
                    <div style="padding:8px 0; border-bottom:1px solid #f3f4f6; font-size:13px;">
                        <strong>{{ $lesson->lesson_order }}.</strong> {{ $lesson->title }}
                        <span class="badge {{ $hasQuiz ? 'b-blue' : 'b-gray' }}" style="margin-left:6px;">{{ $hasQuiz ? 'Has Quiz' : 'No Quiz' }}</span>
                    </div>
                @empty
                    <p style="color:#9ca3af;">No lessons found.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- A3: Enrollment Status --}}
    <div class="demo-card">
        <div class="demo-card-header">
            📋 Enrollment Status — DEMO-LMS-001
            <form method="POST" action="{{ route('demo.reset') }}" style="margin:0;"
                  onsubmit="return confirm('Reset ALL DEMO-LMS-001 progress?');">
                @csrf
                <button type="submit" class="btn-demo btn-reset">⟳ Reset All Progress</button>
            </form>
        </div>
        <div style="overflow-x:auto;">
            <table class="dt">
                <thead>
                    <tr>
                        <th>Participant</th><th>ID</th><th>Payment</th><th>Access</th>
                        <th>Progress</th><th>Lessons</th><th>Attempts</th><th>Best Score</th>
                        <th>Completion</th><th>Certificate</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $oldParticipantMap = [
                            'demo.participant.paid@sms.test'   => ['Paid',        'b-green'],
                            'demo.participant.unpaid@sms.test' => ['Unpaid',      'b-red'],
                            'demo.participant.failed@sms.test' => ['Failed Quiz', 'b-yellow'],
                        ];
                    @endphp
                    @foreach($oldParticipantMap as $email => [$label, $badgeClass])
                        @php
                            $status = $enrollmentStatus[$email] ?? null;
                            $enr    = $status['enrollment'] ?? null;
                        @endphp
                        <tr>
                            <td>
                                <span class="badge {{ $badgeClass }}">{{ $label }}</span>
                                <div style="font-size:11px;color:#9ca3af;margin-top:2px;">{{ $email }}</div>
                            </td>
                            <td>{{ $enr?->id ?? '—' }}</td>
                            <td>
                                @if($enr)
                                    <span class="badge {{ $enr->payment_status === 'paid' ? 'b-green' : 'b-yellow' }}">
                                        {{ ucfirst($enr->payment_status) }}
                                    </span>
                                @else —
                                @endif
                            </td>
                            <td>
                                @if($enr)
                                    <span class="badge {{ $enr->access_status === 'unlocked' ? 'b-green' : 'b-red' }}">
                                        {{ ucfirst($enr->access_status) }}
                                    </span>
                                @else —
                                @endif
                            </td>
                            <td>
                                @if($enr)
                                    {{ $enr->progress_percentage }}%
                                    <div class="pbar"><div class="pbar-fill {{ $enr->progress_percentage >= 100 ? 'done' : '' }}" style="width:{{ $enr->progress_percentage }}%"></div></div>
                                @else —
                                @endif
                            </td>
                            <td style="text-align:center;">{{ $status ? $status['completed_lessons'].'/'.$status['total_lessons'] : '—' }}</td>
                            <td style="text-align:center;">{{ $status ? $status['quiz_attempts'] : '—' }}</td>
                            <td>
                                @if($status && $status['best_quiz_score'] !== null)
                                    <span class="badge {{ $status['quiz_passed'] ? 'b-green' : 'b-red' }}">{{ $status['best_quiz_score'] }}%</span>
                                @else
                                    <span style="color:#9ca3af;">—</span>
                                @endif
                            </td>
                            <td>
                                @if($enr)
                                    <span class="badge {{ $enr->completion_status === 'completed' ? 'b-green' : 'b-gray' }}">
                                        {{ str_replace('_',' ',ucfirst($enr->completion_status)) }}
                                    </span>
                                @else —
                                @endif
                            </td>
                            <td>
                                @if($enr)
                                    <span class="badge {{ match($enr->certificate_status) {'issued'=>'b-green','eligible'=>'b-blue',default=>'b-gray'} }}">
                                        {{ str_replace('_',' ',ucfirst($enr->certificate_status)) }}
                                    </span>
                                @else —
                                @endif
                            </td>
                            <td>
                                @if($enr)
                                    <div style="display:flex;flex-wrap:wrap;gap:4px;">
                                        <form method="POST" action="{{ route('demo.mark-complete',$enr->id) }}" style="margin:0;">
                                            @csrf <button type="submit" class="btn-demo btn-complete">✓ All Lessons</button>
                                        </form>
                                        <form method="POST" action="{{ route('demo.pass-quiz',$enr->id) }}" style="margin:0;">
                                            @csrf <button type="submit" class="btn-demo btn-pass">✓ Pass Quiz</button>
                                        </form>
                                        <form method="POST" action="{{ route('demo.fail-quiz',$enr->id) }}" style="margin:0;">
                                            @csrf <button type="submit" class="btn-demo btn-fail">✗ Fail Quiz</button>
                                        </form>
                                        <form method="POST" action="{{ route('demo.recalculate',$enr->id) }}" style="margin:0;">
                                            @csrf <button type="submit" class="btn-demo btn-calc">⟳ Recalc</button>
                                        </form>
                                        @if($enr->user_id)
                                            <a href="{{ route('participant.my-courses') }}" class="btn-demo btn-view" target="_blank">Dashboard →</a>
                                        @endif
                                    </div>
                                @else
                                    <span style="color:#9ca3af;font-size:12px;">Run seeder first</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════
         SECTION B — DEMO-EL-JOURNEY-001 (6-participant journey demo)
    ══════════════════════════════════════════════════════════════════ --}}
    <div class="section-label" style="margin-top:28px;">🚀 Demo Course: DEMO-EL-JOURNEY-001 (Participant Journey Test)</div>

    {{-- B1: Course + Lessons --}}
    <div class="two-col">
        <div class="demo-card">
            <div class="demo-card-header journey-header" style="color:white;">📚 Journey Course</div>
            <div class="demo-card-body">
                @if($journeyCourse)
                    <table class="dt">
                        <tr><td style="color:#9ca3af;font-size:12px;">ID</td><td><strong>{{ $journeyCourse->id }}</strong></td></tr>
                        <tr><td style="color:#9ca3af;font-size:12px;">Name</td><td>{{ $journeyCourse->name }}</td></tr>
                        <tr><td style="color:#9ca3af;font-size:12px;">Code</td><td><code>{{ $journeyCourse->code }}</code></td></tr>
                        <tr><td style="color:#9ca3af;font-size:12px;">Status</td><td><span class="badge b-green">Published</span></td></tr>
                        <tr><td style="color:#9ca3af;font-size:12px;">Lessons</td><td>{{ $journeyLessons->count() }} lessons</td></tr>
                        <tr><td style="color:#9ca3af;font-size:12px;">Final Quiz</td><td>
                            @if($journeyQuiz)
                                {{ $journeyQuiz->title }}
                                <span class="badge b-blue" style="margin-left:4px;">pass: {{ $journeyQuiz->pass_mark }}%</span>
                            @else
                                <span class="badge b-red">Not found — run seeder</span>
                            @endif
                        </td></tr>
                        <tr><td style="color:#9ca3af;font-size:12px;">Questions</td><td>{{ $journeyQuiz?->questions()->count() ?? '—' }}</td></tr>
                    </table>
                    <div style="margin-top:12px;">
                        @php
                            $jEnr = $journeyEnrollments->first();
                        @endphp
                        @if($jEnr)
                            <a href="{{ route('participant.elearning-details', $jEnr->id) }}"
                               style="color:#0f766e; font-weight:700; font-size:12.5px;"
                               target="_blank">→ Open Course Page (as first participant)</a>
                        @endif
                    </div>
                @else
                    <div class="alert alert-error" style="margin:0;">
                        Course not found. Run:<br>
                        <code style="font-size:12px;">php artisan db:seed --class=DemoElearningJourneySeeder</code>
                    </div>
                @endif
            </div>
        </div>

        <div class="demo-card">
            <div class="demo-card-header journey-header" style="color:white;">📄 Journey Lessons</div>
            <div class="demo-card-body">
                @forelse($journeyLessons as $lesson)
                    @php
                        $hasQuiz      = \App\Models\ElearningQuiz::where('lesson_id',$lesson->id)->exists();
                        $resourceCount= \App\Models\ElearningLessonResource::where('lesson_id',$lesson->id)->count();
                    @endphp
                    <div style="padding:10px 0; border-bottom:1px solid #f3f4f6; font-size:13px;">
                        <strong>{{ $lesson->lesson_order }}.</strong> {{ $lesson->title }}
                        <div style="margin-top:4px; display:flex; gap:5px; flex-wrap:wrap;">
                            @if($hasQuiz)
                                <span class="badge b-blue">Has Quiz</span>
                            @else
                                <span class="badge b-gray">No Quiz</span>
                            @endif
                            @if($lesson->video_url)
                                <span class="badge b-purple">Video</span>
                            @endif
                            @if($resourceCount > 0)
                                <span class="badge b-orange">{{ $resourceCount }} Resource{{ $resourceCount > 1 ? 's' : '' }}</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <p style="color:#9ca3af;">No lessons. Run seeder first.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- B2: Journey Credentials --}}
    <div class="demo-card">
        <div class="demo-card-header journey-header" style="color:white;">🔐 Journey Participant Credentials</div>
        <div style="overflow-x:auto;">
            <table class="dt">
                <thead>
                    <tr><th>#</th><th>Label</th><th>Email</th><th>Password</th><th>Purpose</th><th>Status</th></tr>
                </thead>
                <tbody>
                    @foreach($journeyCredentials as $i => [$label, $email, $purpose])
                        @php $u = $users->get($email); @endphp
                        <tr>
                            <td style="color:#9ca3af;">{{ $i+1 }}</td>
                            <td><strong style="font-size:13px;">{{ $label }}</strong></td>
                            <td><code style="font-size:11.5px;">{{ $email }}</code><br>
                                <code style="font-size:11px; background:#f3f4f6; padding:1px 5px; border-radius:4px;">password</code>
                            </td>
                            <td></td>
                            <td style="font-size:12px; color:#6b7280; max-width:200px;">{{ $purpose }}</td>
                            <td>
                                @if($u)
                                    <span class="badge {{ $u->is_active ? 'b-green' : 'b-red' }}">{{ $u->is_active ? '✓ Active' : '✗ Inactive' }}</span>
                                @else
                                    <span class="badge b-red">Not created — run seeder</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- B3: Journey Enrollment Status --}}
    <div class="demo-card">
        <div class="demo-card-header journey-header" style="color:white;">
            📋 Journey Enrollment Status — All 6 Participants
            <form method="POST" action="{{ route('demo.reset-journey') }}" style="margin:0;"
                  onsubmit="return confirm('Reset ALL journey participant progress to zero? Re-run seeder to restore initial states.');">
                @csrf
                <button type="submit" class="btn-demo btn-reset" style="background:rgba(255,255,255,.15); color:white; border:1px solid rgba(255,255,255,.3);">⟳ Reset All Journey Progress</button>
            </form>
        </div>
        <div style="overflow-x:auto;">
            <table class="dt">
                <thead>
                    <tr>
                        <th>Participant</th><th>ID</th><th>Payment</th><th>Access</th>
                        <th>Progress</th><th>Lessons Done</th><th>Quiz Attempts</th>
                        <th>Best Score</th><th>Completion</th><th>Certificate</th>
                        <th>Expires</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $journeyParticipantMeta = [
                            'demo.paid.notstarted@sms.test'  => ['Not Started',  'b-gray',   'Tests new learner — no progress, lessons locked'],
                            'demo.paid.inprogress@sms.test'  => ['In Progress',  'b-blue',   'Tests continue learning card + partial progress bar'],
                            'demo.paid.completed@sms.test'   => ['Completed',    'b-green',  'Tests certificate eligible state + 100% progress'],
                            'demo.unpaid.completed@sms.test' => ['Unpaid ✗Cert', 'b-red',    'Tests payment gate — 100% done but no cert'],
                            'demo.paid.failedquiz@sms.test'  => ['Failed Quiz',  'b-yellow', 'Tests quiz fail — lesson 3 stays incomplete'],
                            'demo.expired@sms.test'          => ['Expired',      'b-orange', 'Tests expiry gate — 403 on lesson access'],
                        ];
                    @endphp
                    @foreach($journeyParticipantMeta as $email => [$label, $badgeClass, $note])
                        @php
                            $jStatus = $journeyEnrollmentStatus[$email] ?? null;
                            $jEnr    = $jStatus['enrollment'] ?? null;
                        @endphp
                        <tr>
                            <td>
                                <span class="badge {{ $badgeClass }}">{{ $label }}</span>
                                <div style="font-size:11px;color:#9ca3af;margin-top:1px;">{{ $email }}</div>
                                <div class="purpose-chip">{{ $note }}</div>
                            </td>
                            <td>{{ $jEnr?->id ?? '—' }}</td>

                            <td>
                                @if($jEnr)
                                    @php $ps = $jEnr->payment_status; @endphp
                                    <span class="badge {{ in_array($ps,['paid','manual_approved','free']) ? 'b-green' : 'b-red' }}">
                                        {{ ucfirst($ps) }}
                                    </span>
                                @else —
                                @endif
                            </td>

                            <td>
                                @if($jEnr)
                                    <span class="badge {{ $jEnr->access_status === 'unlocked' ? 'b-green' : 'b-red' }}">
                                        {{ ucfirst($jEnr->access_status) }}
                                    </span>
                                @else —
                                @endif
                            </td>

                            <td>
                                @if($jEnr)
                                    <strong>{{ $jEnr->progress_percentage }}%</strong>
                                    <div class="pbar" style="width:80px;">
                                        <div class="pbar-fill {{ $jEnr->progress_percentage >= 100 ? 'done' : '' }}"
                                             style="width:{{ $jEnr->progress_percentage }}%"></div>
                                    </div>
                                @else —
                                @endif
                            </td>

                            <td style="text-align:center;">
                                @if($jStatus)
                                    <strong>{{ $jStatus['completed_lessons'] }}</strong>
                                    @if($jStatus['in_progress_lessons'] > 0)
                                        <span style="color:#f59e0b;font-size:11px;">+{{ $jStatus['in_progress_lessons'] }}▶</span>
                                    @endif
                                    / {{ $jStatus['total_lessons'] }}
                                @else —
                                @endif
                            </td>

                            <td style="text-align:center;">{{ $jStatus ? $jStatus['quiz_attempts'] : '—' }}</td>

                            <td>
                                @if($jStatus && $jStatus['best_quiz_score'] !== null)
                                    <span class="badge {{ $jStatus['quiz_passed'] ? 'b-green' : 'b-red' }}">
                                        {{ $jStatus['best_quiz_score'] }}%
                                        {{ $jStatus['quiz_passed'] ? '✓' : '✗' }}
                                    </span>
                                @else
                                    <span style="color:#9ca3af;">—</span>
                                @endif
                            </td>

                            <td>
                                @if($jEnr)
                                    <span class="badge {{ $jEnr->completion_status === 'completed' ? 'b-green' : ($jEnr->completion_status === 'in_progress' ? 'b-blue' : 'b-gray') }}">
                                        {{ str_replace('_',' ',ucfirst($jEnr->completion_status)) }}
                                    </span>
                                @else —
                                @endif
                            </td>

                            <td>
                                @if($jEnr)
                                    <span class="badge {{ match($jEnr->certificate_status) {'issued'=>'b-green','eligible'=>'b-blue',default=>'b-gray'} }}">
                                        {{ str_replace('_',' ',ucfirst($jEnr->certificate_status)) }}
                                    </span>
                                @else —
                                @endif
                            </td>

                            <td style="font-size:12px;">
                                @if($jEnr && $jEnr->expires_at)
                                    @if($jEnr->expires_at->isPast())
                                        <span class="badge b-red">Expired</span>
                                        <div style="font-size:11px;color:#9ca3af;">{{ $jEnr->expires_at->format('d M Y') }}</div>
                                    @else
                                        <span class="badge b-green">Active</span>
                                        <div style="font-size:11px;color:#9ca3af;">{{ $jEnr->expires_at->format('d M Y') }}</div>
                                    @endif
                                @else
                                    <span style="color:#9ca3af;">No expiry</span>
                                @endif
                            </td>

                            <td>
                                @if($jEnr)
                                    <div style="display:flex;flex-wrap:wrap;gap:4px;">
                                        <form method="POST" action="{{ route('demo.mark-complete',$jEnr->id) }}" style="margin:0;">
                                            @csrf <button type="submit" class="btn-demo btn-complete" title="Mark all lessons complete">✓ Lessons</button>
                                        </form>
                                        <form method="POST" action="{{ route('demo.pass-quiz',$jEnr->id) }}" style="margin:0;">
                                            @csrf <button type="submit" class="btn-demo btn-pass" title="Create passing quiz attempt (80%)">✓ Pass</button>
                                        </form>
                                        <form method="POST" action="{{ route('demo.fail-quiz',$jEnr->id) }}" style="margin:0;">
                                            @csrf <button type="submit" class="btn-demo btn-fail" title="Create failing quiz attempt (40%)">✗ Fail</button>
                                        </form>
                                        <form method="POST" action="{{ route('demo.recalculate',$jEnr->id) }}" style="margin:0;">
                                            @csrf <button type="submit" class="btn-demo btn-calc">⟳ Recalc</button>
                                        </form>
                                        @if($jEnr->user_id)
                                            <a href="{{ route('participant.elearning-details',$jEnr->id) }}"
                                               class="btn-demo btn-view" target="_blank">Course →</a>
                                        @endif
                                    </div>
                                @else
                                    <span style="color:#9ca3af;font-size:12px;">Run seeder first</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── Section: Journey Test Scenarios ──────────────── --}}
    <div class="demo-card">
        <div class="demo-card-header journey-header" style="color:white;">🧪 Journey Test Scenarios</div>
        <div class="demo-card-body">
            <div class="three-col">

                <div class="scenario">
                    <h4>Test 1 — New Learner (Not Started)</h4>
                    <ol>
                        <li>Login: <code>demo.paid.notstarted@sms.test</code> / <code>password</code></li>
                        <li>Open My Courses dashboard</li>
                        <li>Expected: <em>Demo E-Learning Course</em> shows 0% progress</li>
                        <li>Click <strong>Start Course</strong></li>
                        <li>Lesson 1 should be accessible; Lessons 2 &amp; 3 locked</li>
                        <li>Click <strong>Mark as Complete</strong> on Lesson 1</li>
                        <li>Return to course — progress should update to 33%</li>
                        <li>Lesson 2 should now be unlocked</li>
                    </ol>
                </div>

                <div class="scenario">
                    <h4>Test 2 — Continue Learning (In Progress)</h4>
                    <ol>
                        <li>Login: <code>demo.paid.inprogress@sms.test</code> / <code>password</code></li>
                        <li>Dashboard should show <strong>Continue Learning</strong> card for this course</li>
                        <li>Progress bar should show ~33%</li>
                        <li>Open course — Lesson 1 shows ✓ completed</li>
                        <li>Lesson 2 should be accessible (in_progress)</li>
                        <li>Complete Lesson 2 → progress jumps to 67%</li>
                        <li>Lesson 3 unlocks (has quiz)</li>
                        <li>Take quiz → pass → Lesson 3 auto-completes → 100%</li>
                    </ol>
                </div>

                <div class="scenario">
                    <h4>Test 3 — Certificate Eligible (Completed)</h4>
                    <ol>
                        <li>Login: <code>demo.paid.completed@sms.test</code> / <code>password</code></li>
                        <li>Dashboard should show 100% progress</li>
                        <li>Open course — all lessons show ✓ completed</li>
                        <li>Certificate panel should show <strong>Eligible</strong> status</li>
                        <li>Open My Certificates page</li>
                        <li>Course should appear as <strong>Eligible</strong></li>
                        <li>Admin: go to eLearning Enrollments → Issue Certificate</li>
                        <li>After issue: Download PDF and Verify buttons appear</li>
                    </ol>
                </div>

                <div class="scenario" style="border-color:#fca5a5; background:#fff1f2;">
                    <h4 style="color:#dc2626;">Test 4 — Payment Gate (Unpaid)</h4>
                    <ol>
                        <li>Login: <code>demo.unpaid.completed@sms.test</code> / <code>password</code></li>
                        <li>Course access is <strong>locked</strong> — expect payment wall</li>
                        <li>Progress shows 100% (lessons done by seeder)</li>
                        <li><strong>Expected</strong>: completion_status is NOT completed</li>
                        <li><strong>Expected</strong>: certificate_status = not_issued</li>
                        <li>Admin: click ⟳ Recalc for this row</li>
                        <li>Check: recalculate does NOT set completion/certificate for unpaid</li>
                        <li>Approve payment → Recalc → should become eligible</li>
                    </ol>
                </div>

                <div class="scenario" style="border-color:#fde68a; background:#fffbeb;">
                    <h4 style="color:#b45309;">Test 5 — Failed Quiz</h4>
                    <ol>
                        <li>Login: <code>demo.paid.failedquiz@sms.test</code> / <code>password</code></li>
                        <li>Open course — Lessons 1 &amp; 2 show ✓ completed (67%)</li>
                        <li>Lesson 3 shows as <strong>In Progress</strong> (quiz not passed)</li>
                        <li>Take the quiz — answer incorrectly on purpose</li>
                        <li>Or use admin button <strong>✗ Fail</strong></li>
                        <li><strong>Expected</strong>: Lesson 3 stays incomplete</li>
                        <li><strong>Expected</strong>: progress stays at 67%</li>
                        <li>Retake and pass → Lesson 3 auto-completes → 100%</li>
                    </ol>
                </div>

                <div class="scenario" style="border-color:#e9d5ff; background:#faf5ff;">
                    <h4 style="color:#7c3aed;">Test 6 — Expired Access</h4>
                    <ol>
                        <li>Login: <code>demo.expired@sms.test</code> / <code>password</code></li>
                        <li>Open My Courses — enrollment shows (expired)</li>
                        <li>Click any lesson link</li>
                        <li><strong>Expected</strong>: HTTP 403 — access expired</li>
                        <li>expires_at was set 30 days in the past by seeder</li>
                        <li>The enrollment controller checks <code>$enrollment->expires_at->isPast()</code></li>
                        <li>Admin can re-enable by updating expires_at to a future date</li>
                    </ol>
                </div>

            </div>

            {{-- Security test --}}
            <div class="scenario" style="border-color:#fca5a5; background:#fff1f2; margin-top:6px;">
                <h4 style="color:#dc2626;">🔒 Test 7 — Authorization / Ownership Check</h4>
                <ol>
                    <li>Login as <code>demo.paid.notstarted@sms.test</code></li>
                    <li>Open a course lesson, note the enrollment ID in URL: <code>/my-elearning/{your_enrollment_id}/lessons/{lesson_id}</code></li>
                    <li>Change the enrollment ID to another participant's ID (e.g. completed participant)</li>
                    <li><strong>Expected</strong>: HTTP 403 Forbidden — ownership check fails</li>
                    <li>Try accessing <code>/courses</code> while logged in as participant</li>
                    <li><strong>Expected</strong>: Redirect to /my-courses</li>
                    <li>Try accessing <code>/trainer/dashboard</code> as participant</li>
                    <li><strong>Expected</strong>: Redirect to /my-courses</li>
                </ol>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════
         SECTION C — Shared: Settings & Known Issues
    ══════════════════════════════════════════════════════════════════ --}}
    <div class="section-label" style="margin-top:28px;">⚙️ System</div>

    {{-- Settings --}}
    <div class="demo-card">
        <div class="demo-card-header">⚙️ Current LMS Settings (eLearning)</div>
        <div class="demo-card-body">
            @if($settings->count())
                <table class="dt">
                    <thead><tr><th>Key</th><th>Label</th><th>Value</th></tr></thead>
                    <tbody>
                        @foreach($settings as $key => $setting)
                            <tr>
                                <td><code style="font-size:11px;">{{ $key }}</code></td>
                                <td style="font-size:12px;color:#6b7280;">{{ $setting->label }}</td>
                                <td>
                                    @php $v = $setting->value; @endphp
                                    @if(in_array($v, ['1','0']))
                                        <span class="badge {{ $v === '1' ? 'b-green' : 'b-red' }}">{{ $v === '1' ? 'Yes' : 'No' }}</span>
                                    @else
                                        <code style="font-size:12px;">{{ $v }}</code>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p style="color:#9ca3af;margin:0;">No eLearning settings found.</p>
            @endif
            <div style="margin-top:10px;">
                <a href="{{ route('settings.index') }}" style="font-size:13px; color:#1e3a8a; font-weight:700;">→ Edit Settings</a>
            </div>
        </div>
    </div>

    {{-- Known Issues --}}
    <div class="demo-card">
        <div class="demo-card-header">🐛 Known Issues &amp; Gaps</div>
        <div class="demo-card-body">
            <table class="dt">
                <thead><tr><th>#</th><th>Issue</th><th>Impact</th><th>Status</th></tr></thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>eLearning courses have no <code>trainer_id</code> column — trainer is not assigned to eLearning courses</td>
                        <td>Trainer cannot view eLearning participants through trainer portal</td>
                        <td><span class="badge b-yellow">Future Step</span></td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Manual <code>Enrollment</code> model has no <code>user_id</code> — ownership check uses email comparison</td>
                        <td>Weaker ownership check for manual training; acceptable since admin creates these</td>
                        <td><span class="badge b-yellow">Low Priority</span></td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td><code>Setting</code> values not wired into <code>LessonProgressService</code> — pass_mark uses quiz's own column</td>
                        <td>Per-quiz pass_mark works correctly; central setting is a future override</td>
                        <td><span class="badge b-yellow">Future Step</span></td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td>Sequential lesson lock works correctly from lesson progress DB records</td>
                        <td>If LessonProgress records are missing for early lessons, later lessons may appear locked</td>
                        <td><span class="badge b-green">Working</span></td>
                    </tr>
                    <tr>
                        <td>5</td>
                        <td><code>allow_self_registration = 0</code> setting exists but <code>/register</code> route is still public</td>
                        <td>Any visitor can register; setting is not enforced in route middleware</td>
                        <td><span class="badge b-red">Gap</span></td>
                    </tr>
                    <tr>
                        <td>6</td>
                        <td>Demo action <strong>✓ Lessons</strong> marks ALL lessons including Lesson 3 (quiz-gated) complete via service</td>
                        <td>Intentional for admin convenience — bypasses quiz gate for demo setup</td>
                        <td><span class="badge b-blue">By Design</span></td>
                    </tr>
                    <tr>
                        <td>7</td>
                        <td>Unpaid participant (DEMO-EL-JOURNEY-001) has <code>access_status=locked</code> but seeder pre-creates LessonProgress records</td>
                        <td>Seeder bypasses the access gate to set up the "100% but no cert" demo state — real participant could not do this</td>
                        <td><span class="badge b-blue">Demo Only</span></td>
                    </tr>
                    <tr>
                        <td>8</td>
                        <td>Demo video URL uses YouTube embed link — may not render if YouTube is blocked or requires cookie consent</td>
                        <td>Lesson player video iframe may show blank in restricted environments</td>
                        <td><span class="badge b-yellow">Environment</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection
