@extends('layouts.public')

@section('page-title', $post->seo_title ?? $post->title)
@section('seo-title', $post->seo_title ?? $post->title)
@section('seo-desc', $post->seo_description ?? $post->excerpt)

@section('content')
<style>
.bd-layout { display:grid; grid-template-columns:1fr 300px; gap:40px; padding:48px 0 60px; }
@media(max-width:900px){ .bd-layout{grid-template-columns:1fr;} .bd-sidebar{display:none;} }

.bd-article { max-width:760px; }
.bd-breadcrumb { font-size:13px; color:#9ca3af; display:flex; align-items:center; gap:6px; flex-wrap:wrap; margin-bottom:20px; }
.bd-breadcrumb a { color:#6b7280; text-decoration:none; }
.bd-breadcrumb a:hover { color:#042C53; }
.bd-category { display:inline-block; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; margin-bottom:14px; }
.bd-title { font-size:34px; font-weight:900; color:#111827; margin:0 0 14px; line-height:1.25; }
@media(max-width:768px){ .bd-title{font-size:26px;} }
.bd-meta { display:flex; gap:16px; font-size:13px; color:#9ca3af; flex-wrap:wrap; margin-bottom:28px; }
.bd-meta span { display:inline-flex; align-items:center; gap:5px; }
.bd-featured { width:100%; border-radius:14px; overflow:hidden; margin-bottom:32px; }
.bd-featured img { width:100%; max-height:420px; object-fit:cover; }
.bd-content { font-size:16px; line-height:1.85; color:#374151; }
.bd-content h2, .bd-content h3 { color:#111827; font-weight:800; margin:1.5em 0 .6em; }
.bd-content h2 { font-size:22px; }
.bd-content h3 { font-size:18px; }
.bd-content p { margin:0 0 1.2em; }
.bd-content ul, .bd-content ol { padding-left:1.5em; margin:0 0 1.2em; }
.bd-content li { margin-bottom:.5em; }
.bd-content blockquote { border-left:4px solid #042C53; background:#f0f4ff; margin:1.5em 0; padding:16px 20px; border-radius:0 10px 10px 0; font-style:italic; color:#374151; }
.bd-content img { max-width:100%; border-radius:10px; margin:1em 0; }
.bd-content a { color:#042C53; font-weight:600; }
.bd-content a:hover { text-decoration:underline; }

.bd-related-title { font-size:22px; font-weight:900; color:#111827; margin:48px 0 20px; padding-top:32px; border-top:2px solid #f0f2f5; }
.bd-related-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(220px,1fr)); gap:18px; }
.bd-rel-card { background:#fff; border:1px solid #e9ecf0; border-radius:12px; overflow:hidden; text-decoration:none; transition:box-shadow .14s; }
.bd-rel-card:hover { box-shadow:0 4px 16px rgba(0,0,0,.1); }
.bd-rel-card-img { height:120px; background:#f0f4ff; display:flex; align-items:center; justify-content:center; font-size:28px; overflow:hidden; }
.bd-rel-card-img img { width:100%; height:120px; object-fit:cover; }
.bd-rel-card-body { padding:12px 14px; }
.bd-rel-card-title { font-size:13.5px; font-weight:700; color:#111827; line-height:1.4; }
.bd-rel-card-date { font-size:11.5px; color:#9ca3af; margin-top:5px; }

/* Sidebar */
.bd-sidebar-card { background:#fff; border:1px solid #e9ecf0; border-radius:14px; padding:20px; margin-bottom:20px; }
.bd-sidebar-title { font-size:12px; font-weight:800; text-transform:uppercase; letter-spacing:.6px; color:#6b7280; margin:0 0 14px; }
</style>

<div class="pub-container">
<div class="bd-layout">
    <article class="bd-article">
        <div class="bd-breadcrumb">
            <a href="{{ route('public.home') }}">Home</a> /
            <a href="{{ route('public.blog') }}">Blog</a>
            @if($post->category) / <a href="{{ route('public.blog') }}?category={{ $post->category->slug }}">{{ $post->category->name }}</a> @endif
        </div>

        <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:14px;">
            @if($post->category)
            @php $catColor = $post->category->color ?? '#042C53'; @endphp
            <a href="{{ route('public.blog') }}?category={{ $post->category->slug }}"
               class="bd-category" style="background:{{ $catColor }}22;color:{{ $catColor }};text-decoration:none;">
                {{ $post->category->name }}
            </a>
            @endif
            @if($post->article_type && $post->article_type !== 'blog_post')
            @php $typeColors = ['training_news'=>['#042C53','#dbeafe'],'success_story'=>['#7c3aed','#f3e8ff'],'course_announcement'=>['#d97706','#fef3c7']]; $tc = $typeColors[$post->article_type] ?? ['#6b7280','#f3f4f6']; @endphp
            <span style="display:inline-block;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.4px;background:{{ $tc[1] }};color:{{ $tc[0] }};">{{ $post->article_type_label }}</span>
            @endif
            @if($post->ai_generated)<span style="display:inline-block;padding:4px 10px;border-radius:20px;font-size:11px;font-weight:700;background:#f5f3ff;color:#7c3aed;">🤖 AI Generated</span>@endif
        </div>

        <h1 class="bd-title">{{ $post->title }}</h1>

        <div class="bd-meta">
            @if($post->author)<span>✍️ {{ $post->author }}</span>@endif
            @if($post->published_at)<span>📅 {{ $post->published_at->format('d M Y') }}</span>@endif
            <span>⏱ {{ $post->reading_time }} min read</span>
            @if($post->view_count)<span>👁 {{ number_format($post->view_count) }} views</span>@endif
        </div>

        @if($post->featured_image)
        <div class="bd-featured">
            <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}">
        </div>
        @endif

        <div class="bd-content">
            {!! $post->content !!}
        </div>

        {{-- Training schedule context --}}
        @if($post->trainingSchedule)
        @php $sched = $post->trainingSchedule; @endphp
        <div style="background:#f8fafc;border:1px solid #e9ecef;border-radius:14px;padding:18px 22px;margin:28px 0;display:flex;gap:20px;flex-wrap:wrap;align-items:center;">
            <div style="flex:1;min-width:200px;">
                <div style="font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.6px;color:#9ca3af;margin-bottom:5px;">From Training Program</div>
                <div style="font-size:17px;font-weight:900;color:#111827;">{{ $sched->course->name ?? $sched->training_title }}</div>
                <div style="font-size:13px;color:#6b7280;margin-top:4px;display:flex;flex-wrap:wrap;gap:12px;">
                    <span>📅 {{ \Carbon\Carbon::parse($sched->start_date)->format('d M') }} – {{ \Carbon\Carbon::parse($sched->end_date)->format('d M Y') }}</span>
                    @if($sched->city)<span>📍 {{ $sched->city }}</span>@endif
                    @if($sched->trainer)<span>🎓 {{ $sched->trainer->name }}</span>@endif
                </div>
            </div>
            @if($sched->course?->slug)
            <a href="{{ route('public.course.detail', $sched->course->slug) }}" class="pub-enroll-btn" style="white-space:nowrap;">View Course →</a>
            @endif
        </div>
        @endif

        {{-- Tags --}}
        @if($post->tags && count((array)$post->tags))
        <div style="display:flex;flex-wrap:wrap;gap:6px;margin:0 0 28px;">
            @foreach((array)$post->tags as $tag)
            <a href="{{ route('public.blog') }}?q={{ urlencode($tag) }}"
               style="display:inline-block;padding:4px 12px;background:#f0f4ff;color:#042C53;border-radius:20px;font-size:12px;font-weight:600;text-decoration:none;">#{{ $tag }}</a>
            @endforeach
        </div>
        @endif

        {{-- Related course CTA --}}
        @if($post->course && $post->course->is_public)
        <div style="background:linear-gradient(135deg,#f0f4ff,#dbeafe);border:1px solid #bfdbfe;border-radius:14px;padding:22px;margin:32px 0;display:flex;gap:18px;align-items:center;flex-wrap:wrap;">
            <div style="flex:1;">
                <div style="font-size:12px;font-weight:800;text-transform:uppercase;letter-spacing:.5px;color:#378ADD;margin-bottom:6px;">Related Course</div>
                <div style="font-size:18px;font-weight:900;color:#111827;">{{ $post->course->name }}</div>
                @if($post->course->short_description)
                <p style="font-size:13.5px;color:#6b7280;margin:6px 0 0;line-height:1.5;">{{ Str::limit($post->course->short_description, 120) }}</p>
                @endif
            </div>
            <a href="{{ route('public.course.detail', $post->course->slug ?? $post->course->id) }}" class="pub-enroll-btn">
                View Course →
            </a>
        </div>
        @endif

        {{-- Related posts --}}
        @if($related->count())
        <h3 class="bd-related-title">Related Articles</h3>
        <div class="bd-related-grid">
            @foreach($related as $rp)
            <a href="{{ route('public.blog.detail', $rp->slug) }}" class="bd-rel-card">
                <div class="bd-rel-card-img">
                    @if($rp->featured_image)<img src="{{ asset('storage/'.$rp->featured_image) }}" alt="">
                    @else 📰 @endif
                </div>
                <div class="bd-rel-card-body">
                    <div class="bd-rel-card-title">{{ $rp->title }}</div>
                    <div class="bd-rel-card-date">{{ $rp->published_at?->format('d M Y') }}</div>
                </div>
            </a>
            @endforeach
        </div>
        @endif
    </article>

    <aside class="bd-sidebar">
        {{-- Author card --}}
        @if($post->author)
        <div class="bd-sidebar-card" style="text-align:center;">
            <div style="width:60px;height:60px;background:#dbeafe;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:24px;font-weight:900;color:#042C53;margin:0 auto 10px;">
                {{ strtoupper(substr($post->author, 0, 1)) }}
            </div>
            <div style="font-size:16px;font-weight:800;color:#111827;">{{ $post->author }}</div>
            <div style="font-size:12.5px;color:#9ca3af;margin-top:4px;">Author</div>
        </div>
        @endif

        {{-- Share --}}
        <div class="bd-sidebar-card">
            <h4 class="bd-sidebar-title">Share This Article</h4>
            <div style="display:flex;flex-direction:column;gap:8px;">
                <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(request()->url()) }}"
                   target="_blank" rel="noopener"
                   style="display:flex;align-items:center;gap:8px;padding:9px 14px;background:#0077b5;color:#fff;border-radius:8px;font-size:13px;font-weight:700;text-decoration:none;">
                    💼 Share on LinkedIn
                </a>
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}"
                   target="_blank" rel="noopener"
                   style="display:flex;align-items:center;gap:8px;padding:9px 14px;background:#1877f2;color:#fff;border-radius:8px;font-size:13px;font-weight:700;text-decoration:none;">
                    📘 Share on Facebook
                </a>
                <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($post->title) }}"
                   target="_blank" rel="noopener"
                   style="display:flex;align-items:center;gap:8px;padding:9px 14px;background:#000;color:#fff;border-radius:8px;font-size:13px;font-weight:700;text-decoration:none;">
                    𝕏 Post on X
                </a>
            </div>
        </div>

        {{-- Browse all --}}
        <div class="bd-sidebar-card" style="background:linear-gradient(135deg,#042C53,#378ADD);border-color:transparent;">
            <h4 style="color:#fff;font-size:16px;font-weight:900;margin:0 0 8px;">Explore Our Courses</h4>
            <p style="color:rgba(255,255,255,.8);font-size:13.5px;margin:0 0 14px;">Advance your career with professional certifications.</p>
            <a href="{{ route('public.courses') }}" class="pub-enroll-btn" style="background:#fff;color:#042C53;display:block;text-align:center;">Browse Courses</a>
        </div>
    </aside>
</div>
</div>
@endsection
