@extends('layouts.public')

@section('page-title', 'Blog & Resources')
@section('seo-title', 'Training Blog & Insights — SMS Training Services')
@section('seo-desc', 'Read the latest articles, training insights, and career development tips from SMS Training Services.')

@section('content')
<style>
.blog-hero { background:linear-gradient(135deg,#0f172a,#1e3a8a); padding:56px 0; color:#fff; text-align:center; }
.blog-hero h1 { font-size:38px; font-weight:900; margin:0 0 10px; }
.blog-hero p  { font-size:16px; opacity:.75; margin:0 0 24px; }
.blog-search { display:flex; gap:10px; max-width:480px; margin:0 auto; }
.blog-search input {
    flex:1; padding:12px 16px; border-radius:10px; border:1.5px solid rgba(255,255,255,.3);
    background:rgba(255,255,255,.12); color:#fff; font-size:14px; font-family:inherit; outline:none;
}
.blog-search input::placeholder { color:rgba(255,255,255,.5); }
.blog-search button {
    padding:12px 20px; background:#fff; color:#1e3a8a; border:none; border-radius:10px;
    font-weight:800; font-size:14px; cursor:pointer; font-family:inherit;
}

.blog-body { display:grid; grid-template-columns:1fr 280px; gap:36px; padding:48px 0 60px; }
@media(max-width:900px){ .blog-body{grid-template-columns:1fr;} .blog-sidebar{display:none;} }

.blog-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(300px,1fr)); gap:22px; }

/* Blog card */
.blog-card { background:#fff; border:1px solid #e9ecf0; border-radius:14px; overflow:hidden; transition:box-shadow .15s; display:flex; flex-direction:column; }
.blog-card:hover { box-shadow:0 6px 24px rgba(0,0,0,.1); }
.blog-card-img { height:180px; overflow:hidden; background:#f0f4ff; display:flex; align-items:center; justify-content:center; font-size:40px; }
.blog-card-img img { width:100%; height:100%; object-fit:cover; transition:transform .3s; }
.blog-card:hover .blog-card-img img { transform:scale(1.04); }
.blog-card-body { padding:18px; flex:1; display:flex; flex-direction:column; }
.blog-card-cat { display:inline-block; padding:3px 10px; border-radius:20px; font-size:11.5px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; margin-bottom:10px; }
.blog-card-title { font-size:16px; font-weight:800; color:#111827; text-decoration:none; line-height:1.4; margin:0 0 8px; display:block; }
.blog-card-title:hover { color:#1e3a8a; }
.blog-card-excerpt { font-size:13.5px; color:#6b7280; line-height:1.6; margin:0 0 12px; flex:1; }
.blog-card-meta { display:flex; align-items:center; justify-content:space-between; font-size:12px; color:#9ca3af; margin-top:auto; padding-top:12px; border-top:1px solid #f0f2f5; }

/* Sidebar */
.blog-sidebar { }
.sidebar-card { background:#fff; border:1px solid #e9ecf0; border-radius:14px; padding:20px; margin-bottom:20px; }
.sidebar-title { font-size:12px; font-weight:800; text-transform:uppercase; letter-spacing:.6px; color:#6b7280; margin:0 0 14px; }
.cat-link { display:flex; align-items:center; justify-content:space-between; padding:8px 0; border-bottom:1px solid #f0f2f5; text-decoration:none; font-size:14px; color:#374151; font-weight:600; }
.cat-link:last-child { border-bottom:none; }
.cat-link:hover { color:#1e3a8a; }
.cat-count { background:#f0f4ff; color:#1e3a8a; padding:2px 8px; border-radius:20px; font-size:11.5px; font-weight:800; }
.featured-link { display:flex; gap:10px; align-items:flex-start; padding:8px 0; border-bottom:1px solid #f0f2f5; text-decoration:none; }
.featured-link:last-child { border-bottom:none; }
.featured-link-thumb { width:52px; height:40px; border-radius:6px; background:#f0f4ff; display:flex; align-items:center; justify-content:center; font-size:18px; flex-shrink:0; overflow:hidden; }
.featured-link-thumb img { width:52px; height:40px; object-fit:cover; border-radius:6px; }
.featured-link-text { font-size:13px; font-weight:700; color:#111827; line-height:1.4; }
.featured-link-text:hover { color:#1e3a8a; }
</style>

<div class="blog-hero">
    <div class="pub-container">
        <h1>📰 Blog & Resources</h1>
        <p>Insights, career tips, and training news from our expert team</p>
        <form method="GET" action="{{ route('public.blog') }}">
            <div class="blog-search">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search articles…">
                @if(request('category'))<input type="hidden" name="category" value="{{ request('category') }}">@endif
                <button type="submit">🔍</button>
            </div>
        </form>
    </div>
</div>

<div class="pub-container">
<div class="blog-body">

    <main>
        {{-- Active filters --}}
        @if(request('category') || request('q'))
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;flex-wrap:wrap;">
            <span style="font-size:13px;color:#6b7280;font-weight:600;">Filters:</span>
            @if(request('q'))
            <a href="{{ request()->fullUrlWithoutQuery(['q']) }}" class="filter-chip">🔍 "{{ request('q') }}" ✕</a>
            @endif
            @if(request('category'))
            <a href="{{ request()->fullUrlWithoutQuery(['category']) }}" class="filter-chip">{{ request('category') }} ✕</a>
            @endif
        </div>
        @endif

        @if($posts->isEmpty())
        <div style="text-align:center;padding:80px 20px;">
            <div style="font-size:56px;margin-bottom:16px;">📰</div>
            <h3 style="font-size:22px;font-weight:800;color:#111827;margin:0 0 8px;">No articles found</h3>
            <p style="color:#6b7280;font-size:15px;"><a href="{{ route('public.blog') }}" style="color:#1e3a8a;font-weight:700;">View all articles</a></p>
        </div>
        @else
        <div class="blog-grid">
            @foreach($posts as $post)
            @php $catColor = $post->category?->color ?? '#1e3a8a'; @endphp
            <div class="blog-card">
                <div class="blog-card-img">
                    @if($post->featured_image)
                    <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}" loading="lazy">
                    @else 📰 @endif
                </div>
                <div class="blog-card-body">
                    @if($post->category)
                    <a href="{{ route('public.blog') }}?category={{ $post->category->slug }}"
                       class="blog-card-cat"
                       style="background:{{ $catColor }}22;color:{{ $catColor }};">
                        {{ $post->category->name }}
                    </a>
                    @endif
                    <a href="{{ route('public.blog.detail', $post->slug) }}" class="blog-card-title">{{ $post->title }}</a>
                    @if($post->excerpt)
                    <p class="blog-card-excerpt">{{ Str::limit($post->excerpt, 110) }}</p>
                    @endif
                    <div class="blog-card-meta">
                        <span>{{ $post->published_at?->format('d M Y') }}</span>
                        <span>{{ $post->reading_time }} min read</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div style="margin-top:32px;">{{ $posts->links() }}</div>
        @endif
    </main>

    <aside class="blog-sidebar">
        {{-- Categories --}}
        @if($categories->count())
        <div class="sidebar-card">
            <h4 class="sidebar-title">Categories</h4>
            @foreach($categories as $cat)
            <a href="{{ route('public.blog') }}?category={{ $cat->slug }}" class="cat-link"
               style="{{ request('category') == $cat->slug ? 'color:#1e3a8a;' : '' }}">
                <span>{{ $cat->name }}</span>
                <span class="cat-count">{{ $cat->published_posts_count }}</span>
            </a>
            @endforeach
        </div>
        @endif

        {{-- Featured posts --}}
        @if($featured->count())
        <div class="sidebar-card">
            <h4 class="sidebar-title">Popular Articles</h4>
            @foreach($featured as $fp)
            <a href="{{ route('public.blog.detail', $fp->slug) }}" class="featured-link">
                <div class="featured-link-thumb">
                    @if($fp->featured_image)<img src="{{ asset('storage/'.$fp->featured_image) }}" alt="">
                    @else 📰 @endif
                </div>
                <div class="featured-link-text">{{ Str::limit($fp->title, 60) }}</div>
            </a>
            @endforeach
        </div>
        @endif

        {{-- CTA --}}
        <div class="sidebar-card" style="background:linear-gradient(135deg,#1e3a8a,#2563eb);border-color:transparent;">
            <h4 style="color:#fff;font-size:16px;font-weight:900;margin:0 0 8px;">Join Our Next Training</h4>
            <p style="color:rgba(255,255,255,.8);font-size:13.5px;margin:0 0 14px;line-height:1.6;">Professional certifications for your career growth.</p>
            <a href="{{ route('public.courses') }}" class="pub-enroll-btn" style="background:#fff;color:#1e3a8a;display:block;text-align:center;">Browse Courses</a>
        </div>
    </aside>

</div>
</div>
@endsection
