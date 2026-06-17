@extends('layouts.public')

@section('page-title', 'Professional Training & Certification')
@section('seo-title', 'SMS Training Academy')
@section('seo-desc', 'SMS Training Academy — Professional capacity building, compliance training, and internationally recognised certification programs. Instructor-led and eLearning courses worldwide.')

@section('content')

<style>
/* ── Hero ─────────────────────────────────────────────── */
.hero-section {
    background: linear-gradient(135deg, #0f172a 0%, #042C53 55%, #1d4ed8 100%);
    padding: 80px 0 90px;
    position: relative; overflow: hidden;
    color: #fff;
}
.hero-section::before {
    content: '';
    position: absolute; inset: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}
.hero-inner {
    max-width: 1240px; margin: 0 auto; padding: 0 24px;
    display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: center;
    position: relative; z-index: 1;
}
@media (max-width: 900px) { .hero-inner { grid-template-columns: 1fr; gap: 40px; } .hero-visual { display: none; } }
.hero-eyebrow {
    display: inline-flex; align-items: center; gap: 8px;
    background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.18);
    padding: 6px 14px; border-radius: 20px;
    font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .7px;
    margin-bottom: 18px;
}
.hero-title {
    font-size: 46px; font-weight: 900; line-height: 1.15; margin: 0 0 18px;
    background: linear-gradient(135deg, #fff 0%, rgba(255,255,255,.85) 100%);
    -webkit-background-clip: text; -webkit-text-fill-color: transparent;
}
@media (max-width: 768px) { .hero-title { font-size: 32px; } }
.hero-sub { font-size: 17px; opacity: .8; line-height: 1.7; margin: 0 0 32px; }
.hero-ctas { display: flex; gap: 14px; flex-wrap: wrap; }
.hero-cta-primary {
    display: inline-flex; align-items: center; gap: 8px;
    background: #fff; color: #042C53;
    padding: 14px 28px; border-radius: 12px;
    font-weight: 800; font-size: 15px; text-decoration: none;
    box-shadow: 0 6px 20px rgba(0,0,0,.2); transition: all .15s;
}
.hero-cta-primary:hover { transform: translateY(-2px); box-shadow: 0 10px 28px rgba(0,0,0,.25); }
.hero-cta-secondary {
    display: inline-flex; align-items: center; gap: 8px;
    background: rgba(255,255,255,.12); border: 1.5px solid rgba(255,255,255,.3);
    color: #fff; padding: 14px 28px; border-radius: 12px;
    font-weight: 700; font-size: 15px; text-decoration: none; transition: all .15s;
}
.hero-cta-secondary:hover { background: rgba(255,255,255,.2); }
.hero-stats { display: flex; gap: 28px; margin-top: 36px; flex-wrap: wrap; }
.hero-stat-item .stat-num { font-size: 28px; font-weight: 900; }
.hero-stat-item .stat-lbl { font-size: 12.5px; opacity: .6; font-weight: 500; margin-top: 2px; }

/* Hero search */
.hero-search-box {
    background: rgba(255,255,255,.1); backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,.2);
    border-radius: 16px; padding: 24px;
    margin-top: 40px;
}
.hero-search-label { font-size: 13px; font-weight: 700; opacity: .8; margin-bottom: 10px; }
.hero-search-row { display: flex; gap: 10px; }
.hero-search-row input {
    flex: 1; padding: 12px 16px; border-radius: 10px;
    border: 1.5px solid rgba(255,255,255,.2); background: rgba(255,255,255,.1);
    color: #fff; font-size: 14.5px; font-family: inherit; outline: none;
}
.hero-search-row input::placeholder { color: rgba(255,255,255,.5); }
.hero-search-row input:focus { border-color: rgba(255,255,255,.5); background: rgba(255,255,255,.15); }
.hero-search-btn {
    padding: 12px 20px; background: #fff; color: #042C53;
    border: none; border-radius: 10px; font-weight: 800; cursor: pointer; white-space: nowrap;
    font-family: inherit; font-size: 14px;
}
.hero-cat-chips { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 14px; }
.hero-cat-chip {
    padding: 5px 12px; background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.18);
    border-radius: 20px; font-size: 12px; font-weight: 600; color: rgba(255,255,255,.8);
    text-decoration: none; transition: background .14s;
}
.hero-cat-chip:hover { background: rgba(255,255,255,.2); color: #fff; }

/* Hero visual */
.hero-visual { display: flex; flex-direction: column; gap: 14px; }
.hv-card {
    background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.12);
    backdrop-filter: blur(8px); border-radius: 14px; padding: 18px 20px;
    display: flex; align-items: center; gap: 14px;
}
.hv-icon {
    width: 48px; height: 48px; border-radius: 12px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center; font-size: 22px;
}
.hv-name { font-weight: 800; font-size: 14px; }
.hv-meta { font-size: 12px; opacity: .65; margin-top: 3px; }
.hv-badge { background: rgba(52,211,153,.2); color: #34d399; padding: 2px 8px; border-radius: 20px; font-size: 11px; font-weight: 700; }

/* ── Categories strip ─────────────────────────────────── */
.cats-strip { background: #f8fafc; border-bottom: 1px solid #e9ecf0; padding: 20px 0; }
.cats-row { display: flex; gap: 10px; overflow-x: auto; padding-bottom: 4px; scrollbar-width: none; }
.cats-row::-webkit-scrollbar { display: none; }
.cat-pill {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 9px 18px; border-radius: 30px; white-space: nowrap;
    background: #fff; border: 1.5px solid #e5e7eb;
    font-size: 13.5px; font-weight: 600; color: #374151;
    text-decoration: none; transition: all .14s; flex-shrink: 0;
}
.cat-pill:hover { border-color: #042C53; color: #042C53; background: #f0f4ff; }

/* ── Section headings ─────────────────────────────────── */
.section-header { margin-bottom: 36px; }
.section-header-row { display: flex; align-items: flex-end; justify-content: space-between; flex-wrap: wrap; gap: 12px; }
.view-all-link {
    font-size: 14px; font-weight: 700; color: #042C53; text-decoration: none;
    display: inline-flex; align-items: center; gap: 5px;
}
.view-all-link:hover { color: #1d4ed8; }

/* ── Course grid ──────────────────────────────────────── */
.courses-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
@media (max-width: 1024px) { .courses-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 600px)  { .courses-grid { grid-template-columns: 1fr; } }

/* ── Schedule cards ───────────────────────────────────── */
.schedules-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 18px; }
@media (max-width: 768px) { .schedules-grid { grid-template-columns: 1fr; } }
.schedule-card {
    background: #fff; border: 1px solid #e9ecf0; border-radius: 14px;
    padding: 20px; display: flex; gap: 18px;
    transition: box-shadow .2s; box-shadow: 0 2px 8px rgba(15,23,42,.05);
}
.schedule-card:hover { box-shadow: 0 6px 20px rgba(15,23,42,.1); }
.schedule-date-block {
    background: #042C53; color: #fff;
    border-radius: 10px; padding: 10px 14px; text-align: center;
    flex-shrink: 0; min-width: 56px;
}
.sc-day  { font-size: 22px; font-weight: 900; line-height: 1; }
.sc-mon  { font-size: 11px; font-weight: 700; opacity: .8; text-transform: uppercase; }
.sc-body { flex: 1; min-width: 0; }
.sc-title { font-size: 14.5px; font-weight: 800; color: #111827; margin: 0 0 6px; }
.sc-meta  { font-size: 12.5px; color: #6b7280; display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 12px; }
.sc-meta-item { display: inline-flex; align-items: center; gap: 4px; }
.sc-mode-badge {
    padding: 2px 8px; border-radius: 20px; font-size: 11px; font-weight: 700;
}
.scm-physical { background: #f0fdf4; color: #15803d; }
.scm-online   { background: #eff6ff; color: #1d4ed8; }
.scm-hybrid   { background: #fff7ed; color: #c2410c; }
.sc-fee { font-size: 15px; font-weight: 900; color: #042C53; }

/* ── Testimonial cards ────────────────────────────────── */
.testimonials-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
@media (max-width: 900px) { .testimonials-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 600px) { .testimonials-grid { grid-template-columns: 1fr; } }
.testi-card {
    background: #fff; border: 1px solid #e9ecf0; border-radius: 16px;
    padding: 24px; box-shadow: 0 2px 8px rgba(15,23,42,.05);
}
.testi-stars { color: #f59e0b; font-size: 15px; margin-bottom: 12px; }
.testi-text { font-size: 14.5px; color: #374151; line-height: 1.7; margin-bottom: 16px; font-style: italic; }
.testi-author { display: flex; align-items: center; gap: 12px; }
.testi-avatar {
    width: 40px; height: 40px; border-radius: 50%; object-fit: cover;
    background: #dbeafe; display: flex; align-items: center; justify-content: center;
    font-size: 16px; font-weight: 800; color: #042C53; flex-shrink: 0;
}
.testi-avatar img { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; }
.testi-name { font-weight: 800; font-size: 14px; color: #111827; }
.testi-role { font-size: 12px; color: #6b7280; margin-top: 1px; }

/* ── Blog cards ───────────────────────────────────────── */
.blog-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
@media (max-width: 900px) { .blog-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 600px) { .blog-grid { grid-template-columns: 1fr; } }
.blog-card {
    background: #fff; border: 1px solid #e9ecf0; border-radius: 14px; overflow: hidden;
    transition: box-shadow .2s, transform .2s; box-shadow: 0 2px 8px rgba(15,23,42,.05);
}
.blog-card:hover { box-shadow: 0 8px 24px rgba(15,23,42,.1); transform: translateY(-2px); }
.blog-card-img { width: 100%; height: 180px; object-fit: cover; background: #f0f4ff; display: block; }
.blog-card-body { padding: 18px; }
.blog-cat-tag { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: #7c3aed; }
.blog-card-title { font-size: 15px; font-weight: 800; color: #111827; margin: 8px 0 8px; line-height: 1.4; text-decoration: none; display: block; }
.blog-card-title:hover { color: #042C53; }
.blog-card-excerpt { font-size: 13.5px; color: #6b7280; line-height: 1.65; margin: 0; }
.blog-card-meta { padding: 12px 18px; border-top: 1px solid #f0f2f5; font-size: 12.5px; color: #9ca3af; display: flex; align-items: center; justify-content: space-between; }

/* ── CTA section ──────────────────────────────────────── */
.cta-section {
    background: linear-gradient(135deg, #0f172a 0%, #042C53 100%);
    border-radius: 20px; padding: 60px;
    text-align: center; color: #fff;
    margin: 0 24px;
}
@media (max-width: 768px) { .cta-section { padding: 40px 24px; margin: 0 16px; } }
.cta-title { font-size: 32px; font-weight: 900; margin: 0 0 14px; }
.cta-sub { font-size: 17px; opacity: .8; margin: 0 0 28px; }
.cta-btns { display: flex; gap: 14px; justify-content: center; flex-wrap: wrap; }
</style>

{{-- ══ HERO ══════════════════════════════════════════════ --}}
<section class="hero-section">
    <div class="hero-inner">
        <div class="hero-content">
            <div class="hero-eyebrow">🏆 International Professional Training Academy</div>
            <h1 class="hero-title">
                Build Skills.<br>
                Earn Certificates.<br>
                Grow Your Career.
            </h1>
            <p class="hero-sub">
                Professional capacity building and internationally recognised certification programs for individuals and organisations — instructor-led, eLearning, and hybrid formats.
            </p>
            <div class="hero-ctas">
                <a href="{{ route('public.courses') }}" class="hero-cta-primary">
                    Browse All Courses
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                </a>
                <a href="{{ route('public.calendar') }}" class="hero-cta-secondary">
                    📅 View Schedule
                </a>
            </div>

            <div class="hero-stats">
                <div class="hero-stat-item">
                    <div class="stat-num">{{ $stats['courses'] }}+</div>
                    <div class="stat-lbl">Courses</div>
                </div>
                <div class="hero-stat-item">
                    <div class="stat-num">{{ $stats['schedules'] }}+</div>
                    <div class="stat-lbl">Scheduled Batches</div>
                </div>
                <div class="hero-stat-item">
                    <div class="stat-num">{{ $stats['testimonials'] }}+</div>
                    <div class="stat-lbl">Happy Participants</div>
                </div>
            </div>

            {{-- Search --}}
            <div class="hero-search-box">
                <div class="hero-search-label">🔍 Search Courses</div>
                <form action="{{ route('public.courses') }}" method="GET">
                    <div class="hero-search-row">
                        <input type="text" name="q" placeholder="e.g. Fire Safety, ISO 45001, First Aid…">
                        <button type="submit" class="hero-search-btn">Search</button>
                    </div>
                </form>
                @if($categories->count())
                <div class="hero-cat-chips">
                    @foreach($categories->take(6) as $cat)
                    <a href="{{ route('public.courses') }}?cat={{ $cat->slug }}" class="hero-cat-chip">
                        @if($cat->icon)<span>{{ $cat->icon }}</span> @endif{{ $cat->name }}
                    </a>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        <div class="hero-visual">
            @foreach($upcomingSchedules->take(3) as $s)
            <div class="hv-card">
                <div class="hv-icon" style="background:rgba(255,255,255,.1);">🎓</div>
                <div>
                    <div class="hv-name">{{ Str::limit($s->course->name ?? $s->training_title, 50) }}</div>
                    <div class="hv-meta">
                        {{ \Carbon\Carbon::parse($s->start_date)->format('d M Y') }}
                        &nbsp;·&nbsp; {{ $s->training_mode }}
                        <span class="hv-badge" style="margin-left:6px;">Enrolling</span>
                    </div>
                </div>
            </div>
            @endforeach

            <div class="hv-card" style="background:rgba(52,211,153,.08); border-color:rgba(52,211,153,.2);">
                <div class="hv-icon" style="background:rgba(52,211,153,.15);">✅</div>
                <div>
                    <div class="hv-name" style="color:#34d399;">Internationally Recognised Certificates</div>
                    <div class="hv-meta">CPD accredited · QR-verified · Instant download</div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ══ CATEGORIES STRIP ════════════════════════════════ --}}
@if($categories->count())
<div class="cats-strip">
    <div class="pub-container">
        <div class="cats-row">
            <a href="{{ route('public.courses') }}" class="cat-pill">🎯 All Courses</a>
            <a href="{{ route('public.courses') }}?type=eLearning" class="cat-pill">💻 eLearning</a>
            <a href="{{ route('public.courses') }}?type=Instructor-Led" class="cat-pill">👨‍🏫 Instructor-Led</a>
            <a href="{{ route('public.courses') }}?type=Hybrid" class="cat-pill">🔀 Hybrid</a>
            @foreach($categories as $cat)
            <a href="{{ route('public.courses') }}?cat={{ $cat->slug }}" class="cat-pill">
                @if($cat->icon){{ $cat->icon }} @endif{{ $cat->name }}
            </a>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- ══ FEATURED COURSES ═════════════════════════════════ --}}
@if($featuredCourses->count())
<section class="pub-section">
    <div class="pub-container">
        <div class="section-header">
            <div class="section-header-row">
                <div>
                    <div class="section-eyebrow">⭐ Featured</div>
                    <h2 class="section-heading">Top Training Programs</h2>
                    <p class="section-subheading" style="margin-bottom:0;">Most popular courses chosen by professionals</p>
                </div>
                <a href="{{ route('public.courses') }}" class="view-all-link">
                    View All Courses <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                </a>
            </div>
        </div>

        <div class="courses-grid">
            @foreach($featuredCourses as $course)
            @include('public.partials.course-card', ['course' => $course])
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ══ UPCOMING SCHEDULES ══════════════════════════════ --}}
@if($upcomingSchedules->count())
<section class="pub-section" style="background:#f8fafc; padding: 64px 0;">
    <div class="pub-container">
        <div class="section-header">
            <div class="section-header-row">
                <div>
                    <div class="section-eyebrow">📅 Open Enrollment</div>
                    <h2 class="section-heading">Upcoming Training Schedules</h2>
                    <p class="section-subheading" style="margin-bottom:0;">Register now — seats are limited</p>
                </div>
                <a href="{{ route('public.calendar') }}" class="view-all-link">
                    Full Calendar <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                </a>
            </div>
        </div>

        <div class="schedules-grid">
            @foreach($upcomingSchedules as $s)
            @include('public.partials.schedule-card', ['schedule' => $s])
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ══ eLEARNING COURSES ═══════════════════════════════ --}}
@if($elearningCourses->count())
<section class="pub-section">
    <div class="pub-container">
        <div class="section-header">
            <div class="section-header-row">
                <div>
                    <div class="section-eyebrow">💻 Online Learning</div>
                    <h2 class="section-heading">eLearning Courses</h2>
                    <p class="section-subheading" style="margin-bottom:0;">Learn at your own pace, anywhere, anytime</p>
                </div>
                <a href="{{ route('public.courses') }}?type=eLearning" class="view-all-link">
                    All eLearning <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                </a>
            </div>
        </div>
        <div class="courses-grid">
            @foreach($elearningCourses as $course)
            @include('public.partials.course-card', ['course' => $course])
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ══ CORPORATE CTA ════════════════════════════════════ --}}
<section class="pub-section-sm">
    <div class="pub-container">
        <div class="cta-section">
            <div class="section-eyebrow" style="color:rgba(255,255,255,.6);">For Organizations</div>
            <h2 class="cta-title">Corporate &amp; Group Training</h2>
            <p class="cta-sub">Customised training programs for your entire team — on-site, virtual, or blended delivery worldwide.</p>
            <div class="cta-btns">
                <a href="mailto:training@smscert.com" class="hero-cta-primary">📧 Request a Quote</a>
                <a href="{{ route('public.courses') }}" class="hero-cta-secondary">Browse Courses</a>
            </div>
        </div>
    </div>
</section>

{{-- ══ TESTIMONIALS ════════════════════════════════════ --}}
@if($featuredTestimonials->count())
<section class="pub-section" style="background:#f8fafc;">
    <div class="pub-container">
        <div class="section-header">
            <div class="section-header-row">
                <div>
                    <div class="section-eyebrow">⭐ Testimonials</div>
                    <h2 class="section-heading">What Our Participants Say</h2>
                </div>
                <a href="{{ route('public.testimonials') }}" class="view-all-link">All Reviews</a>
            </div>
        </div>
        <div class="testimonials-grid">
            @foreach($featuredTestimonials as $t)
            <div class="testi-card">
                <div class="testi-stars">{{ str_repeat('★', $t->rating) }}{{ str_repeat('☆', 5 - $t->rating) }}</div>
                <p class="testi-text">"{{ Str::limit($t->feedback, 180) }}"</p>
                <div class="testi-author">
                    <div class="testi-avatar">
                        @if($t->photo)
                            <img src="{{ asset('storage/' . $t->photo) }}" alt="{{ $t->name }}">
                        @else
                            {{ strtoupper(substr($t->name, 0, 1)) }}
                        @endif
                    </div>
                    <div>
                        <div class="testi-name">{{ $t->name }}</div>
                        <div class="testi-role">{{ $t->designation }}{{ $t->company ? ' · ' . $t->company : '' }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ══ BLOG POSTS ══════════════════════════════════════ --}}
@if($latestBlogs->count())
<section class="pub-section">
    <div class="pub-container">
        <div class="section-header">
            <div class="section-header-row">
                <div>
                    <div class="section-eyebrow">✍️ Knowledge Hub</div>
                    <h2 class="section-heading">Latest Articles & Insights</h2>
                </div>
                <a href="{{ route('public.blog') }}" class="view-all-link">All Articles</a>
            </div>
        </div>
        <div class="blog-grid">
            @foreach($latestBlogs as $post)
            <div class="blog-card">
                @if($post->featured_image)
                <img class="blog-card-img" src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}">
                @else
                <div class="blog-card-img" style="display:flex;align-items:center;justify-content:center;font-size:40px;">📝</div>
                @endif
                <div class="blog-card-body">
                    @if($post->category)<div class="blog-cat-tag">{{ $post->category->name }}</div>@endif
                    <a href="{{ route('public.blog.detail', $post->slug) }}" class="blog-card-title">{{ $post->title }}</a>
                    <p class="blog-card-excerpt">{{ Str::limit($post->excerpt ?? strip_tags($post->content), 120) }}</p>
                </div>
                <div class="blog-card-meta">
                    <span>{{ $post->published_at?->format('d M Y') }}</span>
                    <span>{{ $post->reading_time }} min read</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ══ CERT VERIFY CTA ════════════════════════════════ --}}
<section class="pub-section-sm">
    <div class="pub-container">
        <div style="background:linear-gradient(135deg,#065f46 0%,#059669 100%); border-radius:20px; padding:48px; text-align:center; color:#fff;">
            <div style="font-size:48px; margin-bottom:14px;">🎓</div>
            <h2 style="font-size:26px; font-weight:900; margin:0 0 10px;">Verify a Certificate</h2>
            <p style="font-size:16px; opacity:.85; margin:0 0 24px;">Check the authenticity of any SMS Training Academy certificate instantly online.</p>
            <a href="{{ route('public.verify-certificate') }}" class="hero-cta-primary" style="display:inline-flex;">
                Verify Now
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
        </div>
    </div>
</section>

@endsection
