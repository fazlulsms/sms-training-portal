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
.bd-breadcrumb a:hover { color:#1e3a8a; }
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
.bd-content blockquote { border-left:4px solid #1e3a8a; background:#f0f4ff; margin:1.5em 0; padding:16px 20px; border-radius:0 10px 10px 0; font-style:italic; color:#374151; }
.bd-content img { max-width:100%; border-radius:10px; margin:1em 0; }
.bd-content a { color:#1e3a8a; font-weight:600; }
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

        @if($post->category)
        @php $catColor = $post->category->color ?? '#1e3a8a'; @endphp
        <span class="bd-category" style="background:{{ $catColor }}22;color:{{ $catColor }};">{{ $post->category->name }}</span>
        @endif

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

        {{-- Related course CTA --}}
        @if($post->course && $post->course->is_public)
        <div style="background:linear-gradient(135deg,#f0f4ff,#dbeafe);border:1px solid #bfdbfe;border-radius:14px;padding:22px;margin:32px 0;display:flex;gap:18px;align-items:center;flex-wrap:wrap;">
            <div style="flex:1;">
                <div style="font-size:12px;font-weight:800;text-transform:uppercase;letter-spacing:.5px;color:#2563eb;margin-bottom:6px;">Related Course</div>
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
            <div style="width:60px;height:60px;background:#dbeafe;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:24px;font-weight:900;color:#1e3a8a;margin:0 auto 10px;">
                {{ strtoupper(substr($post->author, 0, 1)) }}
            </div>
            <div style="font-size:16px;font-weight:800;color:#111827;">{{ $post->author }}</div>
            <div style="font-size:12.5px;color:#9ca3af;margin-top:4px;">Author</div>
        </div>
        @endif

        {{-- Share --}}
        <div class="bd-sidebar-card">
            <h4 class="bd-sidebar-title">Share This Article</h4>
            <div style="display:flex;gap:10px;flex-wrap:wrap;">
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}"
                   target="_blank" rel="noopener"
                   style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;background:#1877f2;color:#fff;border-radius:8px;font-size:13px;font-weight:700;text-decoration:none;">
                    📘 Facebook
                </a>
                <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(request()->url()) }}"
                   target="_blank" rel="noopener"
                   style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;background:#0077b5;color:#fff;border-radius:8px;font-size:13px;font-weight:700;text-decoration:none;">
                    💼 LinkedIn
                </a>
            </div>
        </div>

        {{-- Browse all --}}
        <div class="bd-sidebar-card" style="background:linear-gradient(135deg,#1e3a8a,#2563eb);border-color:transparent;">
            <h4 style="color:#fff;font-size:16px;font-weight:900;margin:0 0 8px;">Explore Our Courses</h4>
            <p style="color:rgba(255,255,255,.8);font-size:13.5px;margin:0 0 14px;">Advance your career with professional certifications.</p>
            <a href="{{ route('public.courses') }}" class="pub-enroll-btn" style="background:#fff;color:#1e3a8a;display:block;text-align:center;">Browse Courses</a>
        </div>
    </aside>
</div>
</div>
@endsection
