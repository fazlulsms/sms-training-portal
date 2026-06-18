@extends('layouts.public')

@section('page-title', 'All Courses')
@section('seo-title', 'Browse All Training Courses — SMS Training Academy')
@section('seo-desc', 'Browse all professional training and certification courses offered by SMS Training Academy — eLearning, instructor-led, and hybrid formats.')

@section('content')
<style>
.catalog-hero {
    background: linear-gradient(135deg,#0f172a 0%,#042C53 55%,#378ADD 100%);
    padding: 48px 0 56px; color:#fff;
}
.catalog-hero h1 { font-size:34px; font-weight:900; margin:0 0 8px; }
.catalog-hero p  { font-size:16px; opacity:.8; margin:0 0 24px; }
.catalog-search-bar {
    display:flex; gap:10px; max-width:560px;
}
.catalog-search-bar input {
    flex:1; padding:12px 16px; border-radius:10px; border:1.5px solid rgba(255,255,255,.3);
    background:rgba(255,255,255,.12); color:#fff; font-size:14.5px; font-family:inherit; outline:none;
}
.catalog-search-bar input::placeholder { color:rgba(255,255,255,.5); }
.catalog-search-bar input:focus { border-color:rgba(255,255,255,.6); background:rgba(255,255,255,.18); }
.catalog-search-btn {
    padding:12px 22px; background:#fff; color:#042C53; border:none; border-radius:10px;
    font-weight:800; font-size:14px; cursor:pointer; white-space:nowrap; font-family:inherit;
}

.catalog-body { display:grid; grid-template-columns: 260px 1fr; gap:32px; padding:40px 0 60px; }
@media (max-width:900px) { .catalog-body { grid-template-columns:1fr; } .filter-sidebar { display:none; } }

/* Filter sidebar */
.filter-sidebar { }
.filter-card { background:#fff; border:1px solid #e9ecf0; border-radius:14px; padding:20px; margin-bottom:18px; }
.filter-title { font-size:12px; font-weight:800; text-transform:uppercase; letter-spacing:.6px; color:#6b7280; margin-bottom:14px; }
.filter-group label { display:flex; align-items:center; gap:10px; font-size:14px; color:#374151; cursor:pointer; padding:6px 0; }
.filter-group input[type=checkbox], .filter-group input[type=radio] { accent-color:#042C53; }
.filter-apply-btn {
    width:100%; background:#042C53; color:#fff; border:none; padding:11px; border-radius:10px;
    font-weight:800; font-size:14px; cursor:pointer; font-family:inherit; margin-top:8px;
}
.filter-clear-link { display:block; text-align:center; margin-top:8px; font-size:13px; color:#6b7280; text-decoration:none; }
.filter-clear-link:hover { color:#042C53; }

/* Results header */
.results-header {
    display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px;
    margin-bottom:20px;
}
.results-count { font-size:14px; color:#6b7280; font-weight:600; }
.results-count strong { color:#111827; }
.active-filter-chips { display:flex; flex-wrap:wrap; gap:8px; }
.filter-chip {
    display:inline-flex; align-items:center; gap:6px;
    background:#eff6ff; color:#042C53; border:1px solid #bfdbfe;
    padding:4px 10px; border-radius:20px; font-size:12.5px; font-weight:600; text-decoration:none;
}
.filter-chip:hover { background:#dbeafe; }

/* Empty state */
.empty-state { text-align:center; padding:80px 20px; }
.empty-state-icon { font-size:56px; margin-bottom:16px; }
.empty-state h3 { font-size:22px; font-weight:800; color:#111827; margin:0 0 8px; }
.empty-state p  { color:#6b7280; font-size:15px; }
</style>

{{-- Hero --}}
<div class="catalog-hero">
    <div class="pub-container">
        @if($activeCategory)
        <h1>{{ $activeCategory->icon ? $activeCategory->icon . ' ' : '' }}{{ $activeCategory->name }}</h1>
        <p>{{ $activeCategory->description ?? 'Professional certification programs for individuals and organisations.' }}</p>
        @else
        <h1>Training Courses</h1>
        <p>Professional certification programs for individuals and organisations — worldwide delivery</p>
        @endif
        <form method="GET" action="{{ route('public.courses') }}">
            <div class="catalog-search-bar">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search courses, topics, keywords…">
                @foreach(request()->except('q','page') as $key => $val)
                    <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                @endforeach
                <button type="submit" class="catalog-search-btn">🔍 Search</button>
            </div>
        </form>
    </div>
</div>

<div class="pub-container">
<div class="catalog-body">

    {{-- Sidebar Filters --}}
    <aside class="filter-sidebar">
        {{-- Category nav (links, not form) --}}
        @if($navCategories->count())
        <div class="filter-card">
            <div class="filter-title">Browse by Category</div>
            <div class="filter-group" style="margin-top:4px;">
                <a href="{{ route('public.courses') }}{{ request()->only(['q','type','has_schedule']) ? '?' . http_build_query(request()->only(['q','type','has_schedule'])) : '' }}"
                   style="display:flex; align-items:center; justify-content:space-between; padding:7px 0; text-decoration:none; font-size:14px; font-weight:{{ !request('cat') ? '700' : '400' }}; color:{{ !request('cat') ? '#042C53' : '#374151' }}; border-bottom:1px solid #f3f4f6;">
                    <span>🎯 All Categories</span>
                    <span style="font-size:12px; color:#9ca3af;">{{ $navCategories->sum('public_courses_count') }}</span>
                </a>
                @foreach($navCategories as $navCat)
                @php
                    $catParams = array_merge(request()->only(['q','type','has_schedule']), ['cat' => $navCat->slug]);
                    $isActive  = request('cat') === $navCat->slug;
                @endphp
                <a href="{{ route('public.courses') }}?{{ http_build_query($catParams) }}"
                   style="display:flex; align-items:center; justify-content:space-between; padding:7px 0; text-decoration:none; font-size:13.5px; font-weight:{{ $isActive ? '700' : '400' }}; color:{{ $isActive ? '#042C53' : '#374151' }}; border-bottom:1px solid #f3f4f6;">
                    <span>@if($navCat->icon){{ $navCat->icon }} @endif{{ $navCat->name }}</span>
                    <span style="font-size:12px; color:{{ $isActive ? '#042C53' : '#9ca3af' }}; font-weight:600;">{{ $navCat->public_courses_count }}</span>
                </a>
                @endforeach
            </div>
        </div>
        @endif

        <form method="GET" action="{{ route('public.courses') }}" id="filterForm">
            @if(request('q'))<input type="hidden" name="q" value="{{ request('q') }}">@endif
            @if(request('cat'))<input type="hidden" name="cat" value="{{ request('cat') }}">@endif

            <div class="filter-card">
                <div class="filter-title">Delivery Type</div>
                <div class="filter-group">
                    @foreach($deliveryTypes as $type)
                    <label>
                        <input type="checkbox" name="type" value="{{ $type }}"
                               {{ request('type') == $type ? 'checked' : '' }}
                               onchange="document.getElementById('filterForm').submit()">
                        {{ $type }}
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="filter-card">
                <div class="filter-title">Schedule Availability</div>
                <div class="filter-group">
                    <label>
                        <input type="checkbox" name="has_schedule" value="1"
                               {{ request('has_schedule') ? 'checked' : '' }}
                               onchange="document.getElementById('filterForm').submit()">
                        Has upcoming schedule
                    </label>
                </div>
            </div>

            @if(request()->hasAny(['cat','type','has_schedule','q']))
            <a href="{{ route('public.courses') }}" class="filter-clear-link">✕ Clear all filters</a>
            @endif
        </form>
    </aside>

    {{-- Results --}}
    <div class="catalog-results">
        <div class="results-header">
            <div class="results-count">
                <strong>{{ $courses->total() }}</strong> course{{ $courses->total() !== 1 ? 's' : '' }} found
            </div>
            <div class="active-filter-chips">
                @if(request('q'))
                <a href="{{ request()->fullUrlWithoutQuery(['q']) }}" class="filter-chip">🔍 "{{ request('q') }}" ✕</a>
                @endif
                @if(request('type'))
                <a href="{{ request()->fullUrlWithoutQuery(['type']) }}" class="filter-chip">{{ request('type') }} ✕</a>
                @endif
                @if(request('cat') && $activeCategory)
                <a href="{{ request()->fullUrlWithoutQuery(['cat']) }}" class="filter-chip">
                    @if($activeCategory->icon){{ $activeCategory->icon }} @endif{{ $activeCategory->name }} ✕
                </a>
                @endif
                @if(request('has_schedule'))
                <a href="{{ request()->fullUrlWithoutQuery(['has_schedule']) }}" class="filter-chip">Has schedule ✕</a>
                @endif
            </div>
        </div>

        @if($courses->isEmpty())
        <div class="empty-state">
            <div class="empty-state-icon">🔍</div>
            <h3>No courses found</h3>
            <p>Try adjusting your filters or <a href="{{ route('public.courses') }}" style="color:#042C53;font-weight:700;">view all courses</a></p>
        </div>
        @else
        <div class="courses-grid">
            @foreach($courses as $course)
            @include('public.partials.course-card', ['course' => $course])
            @endforeach
        </div>

        <div style="margin-top:32px;">
            {{ $courses->links() }}
        </div>
        @endif
    </div>

</div>
</div>

@endsection
