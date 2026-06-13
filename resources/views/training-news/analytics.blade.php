@extends('layouts.app')
@section('page-title', 'Training News Analytics')

@section('content')

<x-page-header title="Content Analytics" desc="Performance overview of all training news articles.">
    <x-slot:actions>
        <a href="{{ route('training-news.index') }}" class="btn btn-ghost btn-sm">← All Articles</a>
    </x-slot:actions>
</x-page-header>

{{-- Top stat tiles --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:14px;margin-bottom:28px;">
    @php
    $totalArt = array_sum($byStatus->toArray());
    $published = $byStatus['published'] ?? 0;
    $draft     = $byStatus['draft'] ?? 0;
    $review    = $byStatus['under_review'] ?? 0;
    @endphp
    @foreach([
        ['label'=>'Total Articles','val'=>$totalArt,'color'=>'#1e3a8a','bg'=>'#dbeafe'],
        ['label'=>'Published','val'=>$published,'color'=>'#16a34a','bg'=>'#dcfce7'],
        ['label'=>'Total Views','val'=>number_format($totalViews),'color'=>'#7c3aed','bg'=>'#f3e8ff'],
        ['label'=>'AI Generated','val'=>$aiGenerated,'color'=>'#d97706','bg'=>'#fef3c7'],
        ['label'=>'Under Review','val'=>$review,'color'=>'#0369a1','bg'=>'#e0f2fe'],
        ['label'=>'Drafts','val'=>$draft,'color'=>'#6b7280','bg'=>'#f3f4f6'],
    ] as $tile)
    <div style="background:#fff;border:1px solid #e9ecef;border-radius:12px;padding:16px;text-align:center;">
        <div style="width:36px;height:36px;border-radius:10px;background:{{ $tile['bg'] }};display:flex;align-items:center;justify-content:center;margin:0 auto 8px;">
            <div style="width:10px;height:10px;border-radius:50%;background:{{ $tile['color'] }};"></div>
        </div>
        <div style="font-size:1.6rem;font-weight:800;color:#111827;">{{ $tile['val'] }}</div>
        <div style="font-size:11px;color:#6b7280;margin-top:3px;">{{ $tile['label'] }}</div>
    </div>
    @endforeach
</div>

<div class="row g-4">

    {{-- Top Articles by Views --}}
    <div class="col-lg-7">
        <div class="card shadow-sm">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Top Articles by Views</h6>
                @forelse($topArticles as $i => $art)
                <div style="display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:1px solid #f0f2f5;">
                    <div style="width:24px;height:24px;border-radius:6px;background:#f0f4ff;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:800;color:#1e3a8a;flex-shrink:0;">#{{ $i+1 }}</div>
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:13px;font-weight:600;color:#111827;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $art->title }}</div>
                        <div style="font-size:11px;color:#9ca3af;">{{ $art->article_type_label }} · {{ $art->published_at?->format('d M Y') }}</div>
                    </div>
                    <div style="font-size:14px;font-weight:700;color:#1e3a8a;white-space:nowrap;">{{ number_format($art->view_count) }} views</div>
                </div>
                @empty
                <p class="text-muted small">No published articles yet.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Status & Type Breakdown --}}
    <div class="col-lg-5">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h6 class="fw-bold mb-3">By Status</h6>
                @foreach(['published'=>['Published','#16a34a'],'draft'=>['Draft','#9ca3af'],'under_review'=>['Under Review','#d97706'],'approved'=>['Approved','#3b82f6'],'archived'=>['Archived','#6b7280']] as $key=>[$label,$color])
                @php $count = $byStatus[$key] ?? 0; $pct = $totalArt > 0 ? round($count/$totalArt*100) : 0; @endphp
                @if($count)
                <div style="margin-bottom:10px;">
                    <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:3px;">
                        <span style="font-weight:600;">{{ $label }}</span>
                        <span style="color:#6b7280;">{{ $count }} ({{ $pct }}%)</span>
                    </div>
                    <div style="height:6px;background:#f0f2f5;border-radius:4px;overflow:hidden;">
                        <div style="height:100%;width:{{ $pct }}%;background:{{ $color }};border-radius:4px;transition:width .5s;"></div>
                    </div>
                </div>
                @endif
                @endforeach
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <h6 class="fw-bold mb-3">By Type</h6>
                @foreach(['training_news'=>['Training News','#1e3a8a'],'success_story'=>['Success Story','#7c3aed'],'course_announcement'=>['Announcement','#d97706'],'blog_post'=>['Blog Post','#16a34a']] as $key=>[$label,$color])
                @php $count = $byType[$key] ?? 0; @endphp
                @if($count)
                <div style="display:flex;align-items:center;justify-content:space-between;padding:7px 0;border-bottom:1px solid #f0f2f5;font-size:13px;">
                    <span style="display:flex;align-items:center;gap:6px;">
                        <span style="width:8px;height:8px;border-radius:50%;background:{{ $color }};display:inline-block;"></span>
                        {{ $label }}
                    </span>
                    <span style="font-weight:700;">{{ $count }}</span>
                </div>
                @endif
                @endforeach
            </div>
        </div>
    </div>

    {{-- Monthly Published --}}
    @if($monthlyPublished->count())
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Monthly Publishing (Last 6 Months)</h6>
                <div style="display:flex;align-items:flex-end;gap:8px;height:100px;">
                    @php $maxM = $monthlyPublished->max() ?: 1; @endphp
                    @foreach($monthlyPublished as $month => $count)
                    <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:4px;">
                        <div style="font-size:11px;font-weight:700;color:#1e3a8a;">{{ $count }}</div>
                        <div style="width:100%;background:#1e3a8a;border-radius:4px 4px 0 0;height:{{ round(($count/$maxM)*70) }}px;min-height:4px;"></div>
                        <div style="font-size:10px;color:#9ca3af;">{{ \Carbon\Carbon::createFromFormat('Y-m',$month)->format('M') }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Category Performance --}}
    @if($byCategory->count())
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Category Performance</h6>
                <div class="dt-scroll">
                    <table class="dt">
                        <thead>
                            <tr><th>Category</th><th class="c">Articles</th><th class="c">Total Views</th></tr>
                        </thead>
                        <tbody>
                            @foreach($byCategory as $row)
                            <tr>
                                <td>{{ $row->category->name ?? 'Uncategorized' }}</td>
                                <td class="c">{{ $row->total }}</td>
                                <td class="c">{{ number_format($row->views) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Recently Published --}}
    @if($recentlyPublished->count())
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Recently Published</h6>
                @foreach($recentlyPublished as $art)
                <div style="display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:1px solid #f0f2f5;">
                    <div style="flex:1;">
                        <a href="{{ route('training-news.edit', $art->id) }}" style="font-size:13px;font-weight:600;color:#111827;text-decoration:none;">{{ Str::limit($art->title, 80) }}</a>
                        <div style="font-size:11px;color:#9ca3af;">{{ $art->published_at?->format('d M Y') }}</div>
                    </div>
                    <div style="font-size:12px;color:#6b7280;">{{ number_format($art->view_count) }} views</div>
                    <a href="{{ route('public.blog.detail', $art->slug) }}" target="_blank" style="font-size:11px;color:#1e3a8a;">View →</a>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

</div>
@endsection
