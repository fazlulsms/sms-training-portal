@php
    $minFee  = $course->min_fee;
    $maxFee  = $course->max_fee;
    $dtClass = match($course->delivery_type ?? 'Instructor-Led') {
        'eLearning'      => 'db-elearning',
        'Instructor-Led' => 'db-instructor',
        default          => 'db-hybrid',
    };
    $dtIcon = match($course->delivery_type ?? 'Instructor-Led') {
        'eLearning'      => '💻',
        'Instructor-Led' => '👨‍🏫',
        default          => '🔀',
    };
    $openCount = $course->open_schedules_count ?? 0;
@endphp
<div class="course-card">
    <div class="course-card-img">
        @if($course->banner_image)
            <img src="{{ asset('storage/' . $course->banner_image) }}" alt="{{ $course->name }}" loading="lazy">
        @else
            <div style="width:100%;height:100%;background:linear-gradient(135deg,#1e3a8a22,#1d4ed822);display:flex;align-items:center;justify-content:center;font-size:40px;">🎓</div>
        @endif
    </div>
    <div class="course-card-body">
        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
            @if($course->category)
            <span class="course-card-category">{{ $course->category }}</span>
            @endif
            <span class="delivery-badge {{ $dtClass }}">{{ $dtIcon }} {{ $course->delivery_type ?? 'Instructor-Led' }}</span>
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
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                {{ $course->duration }}
            </span>
            @endif
            @if($course->language)
            <span class="course-card-meta-item">🌐 {{ $course->language }}</span>
            @endif
            @if($openCount > 0)
            <span class="course-card-meta-item" style="color:#16a34a; font-weight:700;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                {{ $openCount }} schedule{{ $openCount > 1 ? 's' : '' }} open
            </span>
            @endif
        </div>
    </div>
    <div class="course-card-footer">
        <div class="course-price">
            @if($minFee)
                @if($minFee == $maxFee) BDT {{ number_format($minFee) }}
                @else BDT {{ number_format($minFee) }} – {{ number_format($maxFee) }}
                @endif
                <small>per participant</small>
            @else
                <span style="font-size:13px;font-weight:600;color:#6b7280;">Contact for fee</span>
            @endif
        </div>
        <a href="{{ route('public.course.detail', $course->slug ?? $course->id) }}" class="pub-enroll-btn">
            View Details
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
        </a>
    </div>
</div>
