@php
    $isElearning = ($course->delivery_type === 'eLearning' || $course->course_type === 'elearning');
    $elFee   = $isElearning ? ($course->public_price ?: $course->course_fee ?: null) : null;
    $minFee  = $elFee ?? $course->min_fee;
    $maxFee  = $elFee ?? $course->max_fee;
    $dtClass = match($course->delivery_type ?? 'Instructor-Led') {
        'eLearning'      => 'db-elearning',
        'Instructor-Led' => 'db-instructor',
        default          => 'db-hybrid',
    };
    $openCount = $course->open_schedules_count ?? 0;

    // Category-based default image: gradient + SVG icon
    $catRaw = strtolower($course->category ?? '');
    [$catGrad, $catIcon] = match(true) {
        str_contains($catRaw, 'environment')     => ['#065f46,#059669', '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" stroke="#fff" stroke-width="1.6" fill="rgba(255,255,255,.15)"/><path d="M7 13c1-2 3-4 5-4s4 2 5 4" stroke="#fff" stroke-width="1.6" stroke-linecap="round"/><path d="M12 9v4" stroke="#fff" stroke-width="1.6" stroke-linecap="round"/>'],
        str_contains($catRaw, 'esg')             => ['#0c4a6e,#0284c7', '<circle cx="12" cy="12" r="9" stroke="rgba(255,255,255,.4)" stroke-width="1.5"/><path d="M12 3a9 9 0 0 1 0 18" stroke="#fff" stroke-width="1.6"/><path d="M3 12h18M12 3c-3 2-5 5-5 9s2 7 5 9" stroke="rgba(255,255,255,.5)" stroke-width="1.3"/>'],
        str_contains($catRaw, 'social')          => ['#4c1d95,#7c3aed', '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke="#fff" stroke-width="1.6" stroke-linecap="round"/><circle cx="9" cy="7" r="4" stroke="#fff" stroke-width="1.6"/><path d="M23 21v-2a4 4 0 0 0-3-3.87" stroke="rgba(255,255,255,.6)" stroke-width="1.6" stroke-linecap="round"/><path d="M16 3.13a4 4 0 0 1 0 7.75" stroke="rgba(255,255,255,.6)" stroke-width="1.6" stroke-linecap="round"/>'],
        str_contains($catRaw, 'health')          => ['#7f1d1d,#dc2626', '<path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" stroke="#fff" stroke-width="1.6" fill="rgba(255,255,255,.15)"/>'],
        str_contains($catRaw, 'hr') ||
        str_contains($catRaw, 'human')           => ['#92400e,#d97706', '<rect x="2" y="7" width="20" height="14" rx="2" stroke="#fff" stroke-width="1.6" fill="rgba(255,255,255,.12)"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16" stroke="#fff" stroke-width="1.6"/>'],
        str_contains($catRaw, 'quality')         => ['#042C53,#378ADD', '<polyline points="22 11.08 12 2 2 11.08" stroke="#fff" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/><path d="M9 22V12h6v10" stroke="#fff" stroke-width="1.6" stroke-linecap="round"/><path d="M2 22h20" stroke="rgba(255,255,255,.5)" stroke-width="1.5" stroke-linecap="round"/><path d="M12 6l1.5 3 3.3.5-2.4 2.3.6 3.2L12 13.5l-3 1.5.6-3.2-2.4-2.3 3.3-.5z" stroke="#fff" stroke-width="1.3" fill="rgba(255,255,255,.2)"/>'],
        str_contains($catRaw, 'leader') ||
        str_contains($catRaw, 'manag')           => ['#042C53,#042C53', '<circle cx="12" cy="8" r="5" stroke="#fff" stroke-width="1.6"/><path d="M12 13v9" stroke="#fff" stroke-width="1.6" stroke-linecap="round"/><path d="M8 22h8" stroke="rgba(255,255,255,.6)" stroke-width="1.6" stroke-linecap="round"/><path d="M6 17c1-1 3-1.5 6-1.5s5 .5 6 1.5" stroke="rgba(255,255,255,.5)" stroke-width="1.4" stroke-linecap="round"/>'],
        str_contains($catRaw, 'audit')           => ['#134e4a,#0f766e', '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" stroke="#fff" stroke-width="1.6" fill="rgba(255,255,255,.12)"/><polyline points="14 2 14 8 20 8" stroke="#fff" stroke-width="1.6"/><line x1="16" y1="13" x2="8" y2="13" stroke="#fff" stroke-width="1.5" stroke-linecap="round"/><line x1="16" y1="17" x2="8" y2="17" stroke="#fff" stroke-width="1.5" stroke-linecap="round"/><polyline points="10 9 9 9 8 9" stroke="#fff" stroke-width="1.5" stroke-linecap="round"/>'],
        str_contains($catRaw, 'safety') ||
        str_contains($catRaw, 'fire') ||
        str_contains($catRaw, 'iso')             => ['#7c2d12,#ea580c', '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" stroke="#fff" stroke-width="1.6" fill="rgba(255,255,255,.12)"/><polyline points="9 12 11 14 15 10" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>'],
        default                                  => ['#042C53,#3b82f6', '<path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" stroke="#fff" stroke-width="1.6" stroke-linecap="round"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z" stroke="#fff" stroke-width="1.6" fill="rgba(255,255,255,.1)"/><line x1="10" y1="7" x2="16" y2="7" stroke="#fff" stroke-width="1.5" stroke-linecap="round"/><line x1="10" y1="11" x2="16" y2="11" stroke="#fff" stroke-width="1.5" stroke-linecap="round"/>'],
    };
@endphp

<div class="course-card">
    {{-- Card image or category-based default --}}
    <div class="course-card-img">
        @if($course->banner_image)
            <img src="{{ asset('storage/' . $course->banner_image) }}" alt="{{ $course->name }}" loading="lazy">
        @else
            <div style="width:100%;height:100%;background:linear-gradient(135deg,#{{ str_replace(',',',#',$catGrad) }});display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;padding:16px;">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">{!! $catIcon !!}</svg>
                @if($course->category)
                <span style="font-size:11px;font-weight:800;color:rgba(255,255,255,.7);text-transform:uppercase;letter-spacing:.6px;text-align:center;">{{ $course->category }}</span>
                @endif
            </div>
        @endif
    </div>

    <div class="course-card-body">
        <div style="display:flex;align-items:center;gap:7px;flex-wrap:wrap;">
            @if($course->category)
            <span class="course-card-category">{{ $course->category }}</span>
            @endif
            <span class="delivery-badge {{ $dtClass }}">{{ $course->delivery_type ?? 'Instructor-Led' }}</span>
        </div>

        <a href="{{ route('public.course.detail', $course->slug ?? $course->id) }}" class="course-card-title">
            {{ $course->name }}
        </a>

        @if($course->short_description ?? $course->description)
        <p class="course-card-desc">{{ $course->short_description ?? Str::limit(strip_tags($course->description), 120) }}</p>
        @endif

        <div class="course-card-meta">
            @if($course->duration)
            <span class="course-card-meta-item">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                {{ $course->duration }}
            </span>
            @endif
            @if($course->language)
            <span class="course-card-meta-item">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                {{ $course->language }}
            </span>
            @endif
            @if($openCount > 0)
            <span class="course-card-meta-item" style="color:#16a34a;font-weight:700;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                {{ $openCount }} open {{ Str::plural('batch', $openCount) }}
            </span>
            @endif
        </div>
    </div>

    <div class="course-card-footer">
        <div class="course-price">
            @if($minFee)
                @if($minFee == $maxFee) BDT {{ number_format($minFee) }}
                @else BDT {{ number_format($minFee) }}–{{ number_format($maxFee) }}
                @endif
                <small>{{ $isElearning ? 'one-time' : 'per participant' }}</small>
            @else
                <span style="font-size:12.5px;font-weight:600;color:#6b7280;">Contact for pricing</span>
            @endif
        </div>
        <a href="{{ route('public.course.detail', $course->slug ?? $course->id) }}" class="pub-enroll-btn">
            View Details
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
        </a>
    </div>
</div>
