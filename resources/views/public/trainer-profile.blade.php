@extends('layouts.public')

@section('page-title', $trainer->name . ' â€” Trainer Profile')
@section('seo-title', $trainer->name . ' | SMS Training Academy')
@section('seo-desc', $trainer->short_bio ? Str::limit($trainer->short_bio, 160) : 'Expert trainer at SMS Training Academy')

@section('content')
<style>
.section-wrap { max-width: 1000px; margin: 0 auto; padding: 0 24px; }
.profile-header {
    background: linear-gradient(135deg, #0f172a 0%, #042C53 60%);
    padding: 56px 0 70px; color: #fff;
}
.profile-inner { display: flex; gap: 36px; align-items: flex-start; }
@media (max-width: 700px) { .profile-inner { flex-direction: column; align-items: center; text-align: center; } }
.profile-photo {
    width: 140px; height: 140px; border-radius: 50%; flex-shrink: 0;
    background: rgba(255,255,255,.12); display: flex; align-items: center;
    justify-content: center; font-size: 56px; font-weight: 900; color: rgba(255,255,255,.7);
    border: 3px solid rgba(255,255,255,.2); overflow: hidden;
}
.profile-photo img { width: 100%; height: 100%; object-fit: cover; }
.profile-name { font-size: 34px; font-weight: 900; margin: 0 0 6px; }
.profile-desig { font-size: 16px; opacity: .8; margin: 0 0 4px; font-weight: 600; }
.profile-org { font-size: 14.5px; opacity: .65; margin: 0 0 14px; }
.profile-badges { display: flex; flex-wrap: wrap; gap: 8px; }
.profile-badge { background: rgba(255,255,255,.12); border: 1px solid rgba(255,255,255,.2); padding: 5px 14px; border-radius: 20px; font-size: 12.5px; font-weight: 600; }
.profile-body { padding: 48px 0; }
.profile-section { margin-bottom: 36px; }
.profile-section h3 { font-size: 18px; font-weight: 800; color: #042C53; margin: 0 0 14px; padding-bottom: 10px; border-bottom: 2px solid #e0e7ff; }
.profile-text { font-size: 15px; color: #374151; line-height: 1.75; white-space: pre-line; }
.expertise-list { list-style: none; margin: 0; padding: 0; display: flex; flex-wrap: wrap; gap: 10px; }
.expertise-list li { background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; padding: 7px 16px; border-radius: 30px; font-size: 13.5px; font-weight: 600; }
.cert-list { list-style: none; margin: 0; padding: 0; }
.cert-list li { padding: 10px 0; border-bottom: 1px solid #f3f4f6; font-size: 14.5px; color: #374151; font-weight: 500; }
.cert-list li:before { content: 'âœ“'; color: #16a34a; font-weight: 900; margin-right: 10px; }
.schedule-card { background: #fff; border: 1.5px solid #e5e7eb; border-radius: 12px; padding: 18px 20px; margin-bottom: 12px; display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap; }
.schedule-course { font-weight: 700; color: #111827; font-size: 15px; }
.schedule-date { font-size: 13.5px; color: #6b7280; margin-top: 3px; }
.schedule-mode { font-size: 12px; padding: 3px 10px; border-radius: 20px; font-weight: 700; }
.schedule-register { background: #042C53; color: #fff; padding: 9px 18px; border-radius: 8px; font-weight: 700; font-size: 13.5px; text-decoration: none; white-space: nowrap; }
</style>

<section class="profile-header">
    <div class="section-wrap">
        <div class="profile-inner">
            <div class="profile-photo">
                @if($trainer->photo)
                <img src="{{ asset('storage/'.$trainer->photo) }}" alt="{{ $trainer->name }}">
                @else
                {{ substr($trainer->name,0,1) }}
                @endif
            </div>
            <div>
                <h1 class="profile-name">{{ $trainer->name }}</h1>
                @if($trainer->designation)<p class="profile-desig">{{ $trainer->designation }}</p>@endif
                @if($trainer->organization)<p class="profile-org">{{ $trainer->organization }}</p>@endif
                <div class="profile-badges">
                    @if($trainer->experience)<span class="profile-badge">â± {{ $trainer->experience }}</span>@endif
                    @if($trainer->email)<a href="mailto:{{ $trainer->email }}" class="profile-badge" style="color:#fff; text-decoration:none;">âœ‰ Contact</a>@endif
                </div>
            </div>
        </div>
    </div>
</section>

<section class="profile-body">
    <div class="section-wrap">

        @if($trainer->short_bio)
        <div class="profile-section">
            <h3>About</h3>
            <p class="profile-text">{{ $trainer->short_bio }}</p>
        </div>
        @endif

        @if($trainer->qualification)
        <div class="profile-section">
            <h3>Qualification</h3>
            <p class="profile-text">{{ $trainer->qualification }}</p>
        </div>
        @endif

        @if($trainer->expertise_areas)
        <div class="profile-section">
            <h3>Areas of Expertise</h3>
            <ul class="expertise-list">
                @foreach(array_filter(explode("\n", trim($trainer->expertise_areas))) as $area)
                <li>{{ trim($area) }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if($trainer->certifications)
        <div class="profile-section">
            <h3>Certifications &amp; Credentials</h3>
            <ul class="cert-list">
                @foreach(array_filter(explode("\n", trim($trainer->certifications))) as $cert)
                <li>{{ trim($cert) }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if($trainer->publicSchedules->isNotEmpty())
        <div class="profile-section">
            <h3>Upcoming Training Sessions</h3>
            @foreach($trainer->publicSchedules as $sched)
            <div class="schedule-card">
                <div>
                    <div class="schedule-course">{{ $sched->course->name ?? $sched->training_title }}</div>
                    <div class="schedule-date">
                        {{ $sched->start_date->format('d M Y') }}
                        @if($sched->end_date) â€“ {{ $sched->end_date->format('d M Y') }} @endif
                        @if($sched->city) Â· {{ $sched->city }} @endif
                        @if($sched->country), {{ $sched->country }} @endif
                    </div>
                </div>
                <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                    @php $mc = match($sched->training_mode) { 'Physical' => ['#dcfce7','#166534'], 'Online' => ['#dbeafe','#1e40af'], default => ['#fef3c7','#92400e'] }; @endphp
                    <span class="schedule-mode" style="background:{{ $mc[0] }}; color:{{ $mc[1] }};">{{ $sched->training_mode }}</span>
                    <a href="/register-training/{{ $sched->id }}" class="schedule-register">Register â†’</a>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        <div style="margin-top:32px; padding:24px; background:#f0f4ff; border-radius:12px; text-align:center;">
            <p style="font-size:15px; color:#374151; margin:0 0 14px;">
                Interested in inviting {{ $trainer->name }} for your organization's training?
            </p>
            <a href="/corporate-training" style="background:#042C53; color:#fff; padding:12px 28px; border-radius:10px; font-weight:700; font-size:15px; text-decoration:none; display:inline-block;">
                Request Corporate Training â†’
            </a>
        </div>
    </div>
</section>
@endsection
