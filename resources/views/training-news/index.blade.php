@extends('layouts.app')
@section('page-title', 'Training News & Content')

@section('content')

<x-page-header title="Training News & Content" desc="AI-powered news articles generated from completed training programs.">
    <x-slot:actions>
        <a href="{{ route('training-news.analytics') }}" class="btn btn-secondary btn-sm">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
            Analytics
        </a>
    </x-slot:actions>
</x-page-header>

<x-flash-message />

{{-- Stat Tiles --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:14px;margin-bottom:24px;">
    @foreach([
        ['label'=>'Total Articles','val'=>$stats['total'],'color'=>'#1e3a8a','icon'=>'M19 20H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h10l6 6v8a2 2 0 0 1-2 2z'],
        ['label'=>'Published','val'=>$stats['published'],'color'=>'#16a34a','icon'=>'M22 11.08V12a10 10 0 1 1-5.93-9.14'],
        ['label'=>'Draft','val'=>$stats['draft'],'color'=>'#9ca3af','icon'=>'M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7'],
        ['label'=>'Under Review','val'=>$stats['review'],'color'=>'#d97706','icon'=>'M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z'],
        ['label'=>'Total Views','val'=>number_format($stats['views']),'color'=>'#7c3aed','icon'=>'M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z'],
    ] as $tile)
    <div style="background:#fff;border:1px solid #e9ecef;border-radius:12px;padding:16px;text-align:center;box-shadow:0 1px 4px rgba(0,0,0,.04);">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="{{ $tile['color'] }}" stroke-width="2" style="margin-bottom:6px;"><path d="{{ $tile['icon'] }}"/></svg>
        <div style="font-size:1.5rem;font-weight:800;color:#111827;line-height:1;">{{ $tile['val'] }}</div>
        <div style="font-size:11px;color:#6b7280;margin-top:4px;">{{ $tile['label'] }}</div>
    </div>
    @endforeach
</div>

{{-- Pending: Completed schedules without articles --}}
@if($pending->count())
<div class="card shadow-sm mb-4" style="border-left:4px solid #7c3aed;">
    <div class="card-body p-3">
        <div class="d-flex align-items-center gap-2 mb-3">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <strong style="font-size:13px;color:#7c3aed;">{{ $pending->count() }} completed training(s) need news articles</strong>
        </div>
        <div style="display:flex;flex-wrap:wrap;gap:8px;">
            @foreach($pending as $s)
            <a href="{{ route('training-news.create', $s->id) }}"
               style="display:inline-flex;align-items:center;gap:6px;padding:6px 12px;background:#f5f3ff;border:1px solid #ddd6fe;border-radius:8px;font-size:12px;font-weight:600;color:#5b21b6;text-decoration:none;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                {{ Str::limit($s->course->name ?? $s->training_title, 40) }}
                <span style="color:#9ca3af;font-weight:400;">{{ \Carbon\Carbon::parse($s->end_date)->format('M Y') }}</span>
            </a>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- Filter Bar --}}
<div class="filter-bar mb-3">
    <form method="GET" style="display:contents;">
        <div class="filter-row">
            <div class="fi-search-wrap" style="flex:1;min-width:200px;">
                <span class="fi-search-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></span>
                <input class="fi fi-search" type="text" name="q" value="{{ request('q') }}" placeholder="Search articles…" style="width:100%;">
            </div>
            <select class="fi" name="status" style="min-width:140px;">
                <option value="">All Status</option>
                @foreach(['draft'=>'Draft','under_review'=>'Under Review','approved'=>'Approved','published'=>'Published','archived'=>'Archived'] as $val=>$lbl)
                <option value="{{ $val }}" {{ request('status')===$val?'selected':'' }}>{{ $lbl }}</option>
                @endforeach
            </select>
            <select class="fi" name="type" style="min-width:160px;">
                <option value="">All Types</option>
                <option value="training_news" {{ request('type')==='training_news'?'selected':'' }}>Training News</option>
                <option value="success_story" {{ request('type')==='success_story'?'selected':'' }}>Success Story</option>
                <option value="course_announcement" {{ request('type')==='course_announcement'?'selected':'' }}>Announcement</option>
            </select>
            <button type="submit" class="btn btn-primary btn-sm">Filter</button>
            @if(request()->hasAny(['q','status','type']))
            <a href="{{ route('training-news.index') }}" class="btn btn-ghost btn-sm">✕ Clear</a>
            @endif
        </div>
    </form>
</div>

{{-- Articles Table --}}
<div class="dt-wrap">
    <div class="dt-scroll">
        <table class="dt">
            <thead>
                <tr>
                    <th>Article</th>
                    <th>Training</th>
                    <th class="c">Type</th>
                    <th class="c">Status</th>
                    <th class="c">Views</th>
                    <th class="c">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($articles as $article)
                <tr>
                    <td style="max-width:280px;">
                        <div class="td-main" style="font-weight:700;">{{ Str::limit($article->title, 70) }}</div>
                        <div class="td-sub">
                            {{ $article->ai_generated ? '🤖 AI Generated · ' : '' }}
                            {{ $article->category->name ?? '—' }} ·
                            {{ $article->updated_at->diffForHumans() }}
                        </div>
                    </td>
                    <td style="max-width:200px;">
                        @if($article->trainingSchedule)
                        <div class="td-main">{{ Str::limit($article->trainingSchedule->course->name ?? '—', 40) }}</div>
                        <div class="td-sub">{{ \Carbon\Carbon::parse($article->trainingSchedule->end_date)->format('M Y') }} · {{ $article->trainingSchedule->city }}</div>
                        @else
                        <span class="td-sub">—</span>
                        @endif
                    </td>
                    <td class="c">
                        <span style="font-size:11px;padding:3px 8px;border-radius:20px;background:#f0f4ff;color:#1e3a8a;font-weight:600;white-space:nowrap;">
                            {{ $article->article_type_label }}
                        </span>
                    </td>
                    <td class="c">
                        <span class="badge {{ $article->status_badge_class }}">{{ $article->status_label }}</span>
                    </td>
                    <td class="c" style="color:#6b7280;font-size:13px;">{{ number_format($article->view_count) }}</td>
                    <td class="c">
                        <div class="dt-actions" style="justify-content:center;flex-wrap:wrap;">
                            <a href="{{ route('training-news.edit', $article->id) }}" class="btn btn-edit btn-xs">Edit</a>
                            @if($article->status === 'published')
                            <a href="{{ route('public.blog.detail', $article->slug) }}" target="_blank" class="btn btn-xs" style="background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;">View</a>
                            @endif
                            @if($article->trainingSchedule)
                            <a href="{{ route('training-media.index', $article->trainingSchedule->id) }}" class="btn btn-xs" style="background:#f5f3ff;color:#7c3aed;border:1px solid #ddd6fe;">Media</a>
                            @endif
                            <form method="POST" action="{{ route('training-news.destroy', $article->id) }}" onsubmit="return confirm('Delete this article?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-del btn-xs">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <div class="empty-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M19 20H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h10l6 6v8a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg></div>
                            <p class="empty-title">No articles yet</p>
                            <p class="empty-desc">Generate your first news article from a completed training schedule.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($articles->hasPages())
    <div style="padding:14px 16px;border-top:1px solid #f0f2f5;">{{ $articles->links() }}</div>
    @endif
</div>

@endsection
