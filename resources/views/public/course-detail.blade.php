@extends('layouts.public')

@section('page-title', $course->name)
@section('seo-title', $course->name)
@section('seo-desc', $course->short_description ?? Str::limit(strip_tags($course->description), 160))

@section('content')
<style>
.cd-hero {
    background: linear-gradient(135deg,#0f172a 0%,#1e3a8a 100%);
    padding:48px 0; color:#fff;
}
.cd-hero-inner { display:grid; grid-template-columns:1fr 380px; gap:40px; align-items:start; }
@media(max-width:900px){ .cd-hero-inner{grid-template-columns:1fr;} .cd-enroll-card{order:-1;} }

.cd-breadcrumb { font-size:13px; opacity:.6; margin-bottom:16px; display:flex; align-items:center; gap:6px; flex-wrap:wrap; }
.cd-breadcrumb a { color:inherit; text-decoration:none; }
.cd-title { font-size:32px; font-weight:900; margin:0 0 14px; line-height:1.2; }
@media(max-width:768px){ .cd-title{font-size:24px;} }
.cd-subtitle { font-size:16px; opacity:.8; line-height:1.7; margin:0 0 24px; }
.cd-meta-row { display:flex; flex-wrap:wrap; gap:14px 22px; margin-bottom:18px; }
.cd-meta-item { display:inline-flex; align-items:center; gap:7px; font-size:13.5px; opacity:.85; font-weight:600; }
.cd-badges { display:flex; gap:8px; flex-wrap:wrap; }

/* Sticky enroll card */
.cd-enroll-card {
    background:#fff; border-radius:16px; box-shadow:0 12px 40px rgba(0,0,0,.25);
    overflow:hidden; position:sticky; top:80px;
}
.cd-enroll-card-banner { height:180px; overflow:hidden; background:#1e3a8a; display:flex; align-items:center; justify-content:center; font-size:56px; }
.cd-enroll-card-banner img { width:100%; height:100%; object-fit:cover; }
.cd-enroll-card-body { padding:22px; }
.cd-enroll-price { font-size:26px; font-weight:900; color:#1e3a8a; margin-bottom:4px; }
.cd-enroll-price small { font-size:13px; color:#6b7280; font-weight:500; }
.cd-enroll-divider { border:none; border-top:1px solid #f0f2f5; margin:16px 0; }
.cd-enroll-btn {
    display:block; background:linear-gradient(135deg,#1e3a8a,#2563eb); color:#fff;
    padding:14px; border-radius:12px; text-align:center; font-weight:800; font-size:15px;
    text-decoration:none; margin-bottom:10px; transition:opacity .14s;
}
.cd-enroll-btn:hover { opacity:.9; }
.cd-enroll-feature { font-size:13px; color:#374151; display:flex; align-items:center; gap:8px; margin-bottom:8px; }
.cd-enroll-feature svg { flex-shrink:0; color:#16a34a; }

/* Content tabs */
.cd-tabs { display:flex; gap:4px; border-bottom:2px solid #f0f2f5; margin-bottom:28px; overflow-x:auto; }
.cd-tab {
    padding:12px 20px; font-size:14px; font-weight:700; color:#6b7280;
    cursor:pointer; border-bottom:2.5px solid transparent; margin-bottom:-2px; white-space:nowrap;
    border-radius:8px 8px 0 0; transition:all .14s; background:none; border-top:none; border-left:none; border-right:none;
    font-family:inherit;
}
.cd-tab.active { color:#1e3a8a; border-bottom-color:#1e3a8a; background:#f0f4ff; }
.cd-tab-panel { display:none; }
.cd-tab-panel.active { display:block; }

/* Content sections */
.cd-prose { font-size:15.5px; line-height:1.8; color:#374151; }
.cd-prose h2, .cd-prose h3 { color:#111827; }
.cd-prose ul, .cd-prose ol { padding-left:1.4em; }
.cd-prose li { margin-bottom:.5em; }

.checklist { list-style:none; padding:0; margin:0; }
.checklist li { display:flex; align-items:flex-start; gap:10px; padding:8px 0; border-bottom:1px solid #f0f2f5; font-size:15px; color:#374151; }
.checklist li:last-child { border-bottom:none; }
.checklist-icon { color:#16a34a; flex-shrink:0; margin-top:2px; }

/* Schedule table */
.schedule-table { width:100%; border-collapse:collapse; font-size:14px; }
.schedule-table th { background:#f8fafc; padding:12px 14px; text-align:left; font-size:11.5px; font-weight:800; text-transform:uppercase; letter-spacing:.5px; color:#6b7280; border-bottom:2px solid #e9ecf0; }
.schedule-table td { padding:14px; border-bottom:1px solid #f0f2f5; color:#374151; vertical-align:middle; }
.schedule-table tr:hover td { background:#f8fafc; }

/* Trainer card */
.trainer-card { display:flex; gap:20px; align-items:flex-start; background:#f8fafc; border-radius:14px; padding:22px; }
.trainer-avatar { width:72px; height:72px; border-radius:50%; object-fit:cover; background:#dbeafe; display:flex; align-items:center; justify-content:center; font-size:28px; font-weight:900; color:#1e3a8a; flex-shrink:0; }
.trainer-avatar img { width:72px; height:72px; border-radius:50%; object-fit:cover; }
.trainer-name { font-size:18px; font-weight:800; color:#111827; margin:0 0 4px; }
.trainer-title { font-size:13.5px; color:#6b7280; margin:0 0 10px; }
.trainer-bio { font-size:14px; color:#374151; line-height:1.7; margin:0; }

/* Right sidebar */
.cd-layout { display:grid; grid-template-columns:1fr 340px; gap:40px; padding:40px 0 60px; }
@media(max-width:900px){ .cd-layout{grid-template-columns:1fr;} }
</style>

{{-- Course Hero --}}
<div class="cd-hero">
    <div class="pub-container">
        <div class="cd-hero-inner">
            <div>
                <div class="cd-breadcrumb">
                    <a href="{{ route('public.home') }}">Home</a>
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                    <a href="{{ route('public.courses') }}">Courses</a>
                    @if($course->category)
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                    <a href="{{ route('public.courses') }}?category={{ urlencode($course->category) }}">{{ $course->category }}</a>
                    @endif
                </div>

                <h1 class="cd-title">{{ $course->name }}</h1>
                <p class="cd-subtitle">{{ $course->short_description ?? Str::limit(strip_tags($course->description), 200) }}</p>

                <div class="cd-meta-row">
                    @if($course->duration)
                    <span class="cd-meta-item"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> {{ $course->duration }}</span>
                    @endif
                    <span class="cd-meta-item">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        {{ $course->certificate_type ?? 'Certificate of Completion' }}
                    </span>
                    @if($course->language)
                    <span class="cd-meta-item">🌐 {{ $course->language }}</span>
                    @endif
                    @if($course->cpd_hours)
                    <span class="cd-meta-item">⭐ {{ $course->cpd_hours }} CPD Hours</span>
                    @endif
                </div>

                <div class="cd-badges">
                    <span class="delivery-badge {{ match($course->delivery_type ?? '') { 'eLearning'=>'db-elearning', 'Hybrid'=>'db-hybrid', default=>'db-instructor' } }}">
                        {{ $course->delivery_type ?? 'Instructor-Led' }}
                    </span>
                    @if($course->category)<span class="tag-badge">{{ $course->category }}</span>@endif
                </div>
            </div>

            {{-- Enroll card (desktop right column) --}}
            <div class="cd-enroll-card">
                <div class="cd-enroll-card-banner">
                    @if($course->banner_image)
                        <img src="{{ asset('storage/' . $course->banner_image) }}" alt="{{ $course->name }}">
                    @else 🎓 @endif
                </div>
                <div class="cd-enroll-card-body">
                    @if($course->min_fee)
                    <div class="cd-enroll-price">
                        BDT {{ number_format($course->min_fee) }}
                        @if($course->min_fee != $course->max_fee) – {{ number_format($course->max_fee) }} @endif
                        <small>per participant</small>
                    </div>
                    @endif

                    @if($course->publicSchedules->count())
                    <a href="#schedules" class="cd-enroll-btn">📅 View Open Schedules</a>
                    @else
                    <a href="mailto:training@smscert.com" class="cd-enroll-btn">📧 Contact to Enroll</a>
                    @endif

                    <hr class="cd-enroll-divider">
                    <div class="cd-enroll-feature"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> Professional certificate</div>
                    <div class="cd-enroll-feature"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> Expert instructors</div>
                    <div class="cd-enroll-feature"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> Flexible delivery modes</div>
                    @if($course->cpd_hours)
                    <div class="cd-enroll-feature"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> {{ $course->cpd_hours }} CPD Hours</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Course Body --}}
<div class="pub-container">
<div class="cd-layout">

    {{-- Main Content --}}
    <div>
        {{-- Tabs --}}
        <div class="cd-tabs">
            <button class="cd-tab active" onclick="showTab('overview', this)">Overview</button>
            @if($course->learning_objectives)
            <button class="cd-tab" onclick="showTab('objectives', this)">Learning Objectives</button>
            @endif
            @if($course->course_outline)
            <button class="cd-tab" onclick="showTab('outline', this)">Course Outline</button>
            @endif
            @if($course->who_should_attend || $course->prerequisites)
            <button class="cd-tab" onclick="showTab('audience', this)">Who Should Attend</button>
            @endif
            @if($course->publicSchedules->count())
            <button class="cd-tab" onclick="showTab('schedules', this)" id="tab-schedules">Schedule & Fees</button>
            @endif
            @if($course->testimonials->count())
            <button class="cd-tab" onclick="showTab('reviews', this)">Reviews ({{ $course->testimonials->count() }})</button>
            @endif
        </div>

        {{-- Overview tab --}}
        <div class="cd-tab-panel active" id="tab-panel-overview">
            @if($course->full_description ?? $course->description)
            <div class="cd-prose">{!! nl2br(e($course->full_description ?? $course->description)) !!}</div>
            @endif
        </div>

        {{-- Objectives tab --}}
        @if($course->learning_objectives)
        <div class="cd-tab-panel" id="tab-panel-objectives">
            <h3 style="font-size:20px;font-weight:900;margin:0 0 18px;color:#111827;">What You Will Learn</h3>
            <ul class="checklist">
                @foreach(array_filter(explode("\n", $course->learning_objectives)) as $obj)
                <li>
                    <svg class="checklist-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    {{ trim($obj) }}
                </li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Outline tab --}}
        @if($course->course_outline)
        <div class="cd-tab-panel" id="tab-panel-outline">
            <h3 style="font-size:20px;font-weight:900;margin:0 0 18px;color:#111827;">Course Outline</h3>
            <div class="cd-prose">{!! nl2br(e($course->course_outline)) !!}</div>
        </div>
        @endif

        {{-- Audience tab --}}
        @if($course->who_should_attend || $course->prerequisites)
        <div class="cd-tab-panel" id="tab-panel-audience">
            @if($course->who_should_attend)
            <h3 style="font-size:20px;font-weight:900;margin:0 0 14px;color:#111827;">Who Should Attend</h3>
            <ul class="checklist" style="margin-bottom:28px;">
                @foreach(array_filter(explode("\n", $course->who_should_attend)) as $line)
                <li>
                    <svg class="checklist-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    {{ trim($line) }}
                </li>
                @endforeach
            </ul>
            @endif
            @if($course->prerequisites)
            <h3 style="font-size:20px;font-weight:900;margin:0 0 14px;color:#111827;">Prerequisites</h3>
            <div class="cd-prose">{!! nl2br(e($course->prerequisites)) !!}</div>
            @endif
        </div>
        @endif

        {{-- Schedules tab --}}
        @if($course->publicSchedules->count())
        <div class="cd-tab-panel" id="tab-panel-schedules">
            <h3 style="font-size:20px;font-weight:900;margin:0 0 18px;color:#111827;" id="schedules">Available Schedules</h3>
            <div style="overflow-x:auto;">
                <table class="schedule-table">
                    <thead>
                        <tr>
                            <th>Batch</th>
                            <th>Dates</th>
                            <th>Mode</th>
                            <th>Venue</th>
                            <th>Trainer</th>
                            <th>Fee</th>
                            <th>Seats</th>
                            <th>Deadline</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($course->publicSchedules as $s)
                        @php
                            $fee = $s->discount_fee ?? ($s->training_mode === 'Online' ? $s->online_fee : $s->physical_fee);
                            $origFee = $s->training_mode === 'Online' ? $s->online_fee : $s->physical_fee;
                            $seatsLeft = $s->seats_left;
                        @endphp
                        <tr>
                            <td style="font-weight:700;">{{ $s->batch_code }}</td>
                            <td>
                                {{ \Carbon\Carbon::parse($s->start_date)->format('d M') }}
                                – {{ \Carbon\Carbon::parse($s->end_date)->format('d M Y') }}
                                @if($s->time_start)
                                <div style="font-size:12px;color:#6b7280;margin-top:2px;">
                                    {{ \Carbon\Carbon::parse($s->time_start)->format('h:i A') }}
                                    – {{ \Carbon\Carbon::parse($s->time_end)->format('h:i A') }}
                                </div>
                                @endif
                            </td>
                            <td>
                                <span class="sc-mode-badge {{ match(strtolower($s->training_mode ?? '')) { 'online'=>'scm-online', 'hybrid'=>'scm-hybrid', default=>'scm-physical' } }}">
                                    {{ $s->training_mode }}
                                </span>
                            </td>
                            <td style="font-size:13px;">{{ $s->training_mode === 'Online' ? 'Zoom' : ($s->venue ?? 'TBA') }}</td>
                            <td style="font-size:13px;">{{ $s->trainer?->name ?? 'TBA' }}</td>
                            <td>
                                <strong>{{ $s->currency }} {{ number_format($fee) }}</strong>
                                @if($s->discount_fee && $origFee && $s->discount_fee < $origFee)
                                <div style="font-size:12px;color:#9ca3af;text-decoration:line-through;">{{ number_format($origFee) }}</div>
                                @endif
                            </td>
                            <td>
                                @if(!is_null($seatsLeft))
                                <span style="font-size:13px;font-weight:700;color:{{ $seatsLeft <= 5 ? '#ef4444' : '#16a34a' }};">
                                    {{ $seatsLeft <= 0 ? 'Full' : $seatsLeft . ' left' }}
                                </span>
                                @else
                                <span style="color:#9ca3af;font-size:13px;">Open</span>
                                @endif
                            </td>
                            <td>
                                @if($s->registration_deadline)
                                <div style="font-size:12px;color:#6b7280;">{{ \Carbon\Carbon::parse($s->registration_deadline)->format('d M Y') }}</div>
                                @endif
                            </td>
                            <td>
                                @if($s->is_open)
                                <a href="{{ route('public.enroll', $s->id) }}" class="pub-enroll-btn" style="padding:7px 14px;font-size:12.5px;">
                                    Enroll Now
                                </a>
                                @else
                                <span style="font-size:12px;color:#9ca3af;font-weight:600;">Closed</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- Reviews tab --}}
        @if($course->testimonials->count())
        <div class="cd-tab-panel" id="tab-panel-reviews">
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:18px;">
                @foreach($course->testimonials as $t)
                <div class="testi-card" style="background:#f8fafc;">
                    <div class="testi-stars">{{ str_repeat('★', $t->rating) }}{{ str_repeat('☆', 5 - $t->rating) }}</div>
                    <p class="testi-text">"{{ Str::limit($t->feedback, 200) }}"</p>
                    <div class="testi-author">
                        <div class="testi-avatar">{{ strtoupper(substr($t->name,0,1)) }}</div>
                        <div>
                            <div class="testi-name">{{ $t->name }}</div>
                            <div class="testi-role">{{ $t->designation }}{{ $t->company ? ' · '.$t->company : '' }}</div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- Right sidebar --}}
    <aside>
        {{-- Quick facts --}}
        <div style="background:#fff;border:1px solid #e9ecf0;border-radius:14px;padding:22px;margin-bottom:20px;">
            <h4 style="font-size:13px;font-weight:800;text-transform:uppercase;letter-spacing:.6px;color:#6b7280;margin:0 0 16px;">Course Facts</h4>
            @if($course->duration)
            <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid #f0f2f5;font-size:14px;">
                <span style="color:#6b7280;">Duration</span> <strong>{{ $course->duration }}</strong>
            </div>
            @endif
            @if($course->language)
            <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid #f0f2f5;font-size:14px;">
                <span style="color:#6b7280;">Language</span> <strong>{{ $course->language }}</strong>
            </div>
            @endif
            @if($course->certificate_type)
            <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid #f0f2f5;font-size:14px;">
                <span style="color:#6b7280;">Certificate</span> <strong>{{ $course->certificate_type }}</strong>
            </div>
            @endif
            @if($course->cpd_hours)
            <div style="display:flex;justify-content:space-between;padding:10px 0;font-size:14px;">
                <span style="color:#6b7280;">CPD Hours</span> <strong>{{ $course->cpd_hours }}</strong>
            </div>
            @endif
        </div>

        {{-- Related courses --}}
        @if($relatedCourses->count())
        <div style="background:#fff;border:1px solid #e9ecf0;border-radius:14px;padding:22px;">
            <h4 style="font-size:13px;font-weight:800;text-transform:uppercase;letter-spacing:.6px;color:#6b7280;margin:0 0 16px;">Related Courses</h4>
            @foreach($relatedCourses as $rc)
            <a href="{{ route('public.course.detail', $rc->slug ?? $rc->id) }}"
               style="display:flex;align-items:flex-start;gap:12px;padding:10px 0;border-bottom:1px solid #f0f2f5;text-decoration:none;color:inherit;">
                <div style="width:48px;height:48px;border-radius:8px;background:#f0f4ff;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;">🎓</div>
                <div>
                    <div style="font-size:13.5px;font-weight:700;color:#111827;line-height:1.3;">{{ Str::limit($rc->name, 50) }}</div>
                    @if($rc->category)<div style="font-size:12px;color:#6b7280;margin-top:3px;">{{ $rc->category }}</div>@endif
                </div>
            </a>
            @endforeach
        </div>
        @endif

        {{-- Related blog posts --}}
        @if($course->blogPosts->count())
        <div style="background:#fff;border:1px solid #e9ecf0;border-radius:14px;padding:22px;margin-top:20px;">
            <h4 style="font-size:13px;font-weight:800;text-transform:uppercase;letter-spacing:.6px;color:#6b7280;margin:0 0 16px;">Related Articles</h4>
            @foreach($course->blogPosts as $bp)
            <a href="{{ route('public.blog.detail', $bp->slug) }}"
               style="display:block;font-size:14px;font-weight:700;color:#1e3a8a;text-decoration:none;padding:8px 0;border-bottom:1px solid #f0f2f5;line-height:1.4;">
                {{ $bp->title }}
            </a>
            @endforeach
        </div>
        @endif
    </aside>

</div>
</div>

<script>
function showTab(name, btn) {
    // Hide all panels
    document.querySelectorAll('.cd-tab-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.cd-tab').forEach(b => b.classList.remove('active'));
    // Show selected
    const panel = document.getElementById('tab-panel-' + name);
    if (panel) panel.classList.add('active');
    if (btn) btn.classList.add('active');
    // Scroll to section if schedules
    if (name === 'schedules') {
        const el = document.getElementById('schedules');
        if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

// If URL has #schedules anchor, open that tab
window.addEventListener('load', function() {
    if (window.location.hash === '#schedules') {
        const btn = document.getElementById('tab-schedules');
        if (btn) btn.click();
    }
});
</script>

@endsection
