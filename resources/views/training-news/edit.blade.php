@extends('layouts.app')
@section('page-title', 'Edit Article — ' . Str::limit($article->title, 50))

@section('content')
<style>
.edit-section { background:#fff;border:1px solid #e9ecef;border-radius:14px;padding:24px;margin-bottom:20px; }
.edit-section-title { font-size:13px;font-weight:800;color:#374151;margin:0 0 16px;display:flex;align-items:center;gap:7px;text-transform:uppercase;letter-spacing:.5px; }
.field-group { margin-bottom:14px; }
.field-group label { display:block;font-size:12px;font-weight:700;color:#6b7280;margin-bottom:5px;text-transform:uppercase;letter-spacing:.4px; }
.field-group input, .field-group select, .field-group textarea { width:100%;padding:9px 12px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:13px;font-family:inherit;color:#111827;background:#fff;box-sizing:border-box; }
.field-group textarea { resize:vertical; }
.field-group input:focus, .field-group select:focus, .field-group textarea:focus { outline:none;border-color:#6366f1; }
.workflow-bar { display:flex;align-items:center;gap:0;background:#f8fafc;border:1px solid #e9ecef;border-radius:12px;padding:4px;margin-bottom:20px;flex-wrap:wrap;gap:4px; }
.wf-step { display:flex;align-items:center;gap:6px;padding:7px 14px;border-radius:9px;font-size:12px;font-weight:700;color:#9ca3af;cursor:default;flex:1;justify-content:center;white-space:nowrap; }
.wf-step.active { background:#1e3a8a;color:#fff; }
.wf-step.done   { background:#dcfce7;color:#166534; }
.change-log-item { display:flex;gap:10px;padding:6px 0;border-bottom:1px solid #f0f2f5;font-size:12px; }
.change-log-item:last-child { border-bottom:none; }
</style>

{{-- Header --}}
<div class="d-flex align-items-center gap-3 mb-4 flex-wrap">
    <div>
        <h4 class="mb-1 fw-bold">{{ Str::limit($article->title, 70) }}</h4>
        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
            <span class="badge {{ $article->status_badge_class }}">{{ $article->status_label }}</span>
            <span style="font-size:11px;padding:2px 8px;border-radius:20px;background:#f0f4ff;color:#1e3a8a;font-weight:600;">{{ $article->article_type_label }}</span>
            @if($article->ai_generated)<span style="font-size:11px;color:#7c3aed;">🤖 AI Generated</span>@endif
            <span style="font-size:12px;color:#9ca3af;">Updated {{ $article->updated_at->diffForHumans() }}</span>
        </div>
    </div>
    <div class="ms-auto d-flex gap-2 flex-wrap">
        @if($article->status === 'published')
        <a href="{{ route('public.blog.detail', $article->slug) }}" target="_blank" class="btn btn-sm" style="background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
            View Live
        </a>
        @endif
        @if($article->trainingSchedule)
        <a href="{{ route('training-media.index', $article->training_schedule_id) }}" class="btn btn-outline-secondary btn-sm">Photos</a>
        <a href="{{ route('training-news.create', $article->training_schedule_id) }}" class="btn btn-outline-secondary btn-sm">New Article</a>
        @endif
        <a href="{{ route('training-news.index') }}" class="btn btn-ghost btn-sm">← Back</a>
    </div>
</div>

{{-- Workflow bar --}}
@php
$steps = [
    'draft'        => 'Draft',
    'under_review' => 'Under Review',
    'approved'     => 'Approved',
    'published'    => 'Published',
];
$order  = array_keys($steps);
$curIdx = array_search($article->status, $order) ?? 0;
@endphp
<div class="workflow-bar">
    @foreach($steps as $key => $label)
    @php $idx = array_search($key, $order); @endphp
    <div class="wf-step {{ $article->status === $key ? 'active' : ($idx < $curIdx ? 'done' : '') }}">
        @if($idx < $curIdx) ✓ @endif {{ $label }}
    </div>
    @endforeach
</div>

{{-- Workflow action buttons --}}
<div class="edit-section" style="padding:16px;">
    <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
        <strong style="font-size:12px;color:#6b7280;text-transform:uppercase;letter-spacing:.5px;">Actions:</strong>

        @if($article->status === 'draft')
        <form method="POST" action="{{ route('training-news.submit-review', $article->id) }}" style="margin:0;">@csrf
            <button class="btn btn-warning btn-sm">Submit for Review</button>
        </form>
        @endif

        @if($article->status === 'under_review' && auth()->user()->isSuperAdmin())
        <form method="POST" action="{{ route('training-news.approve', $article->id) }}" style="margin:0;">@csrf
            <button class="btn btn-info btn-sm">Approve</button>
        </form>
        @endif

        @if(in_array($article->status, ['approved','draft','under_review']) && auth()->user()->isSuperAdmin())
        <form method="POST" action="{{ route('training-news.publish', $article->id) }}" style="margin:0;">@csrf
            <button class="btn btn-success btn-sm">Publish Now</button>
        </form>
        @endif

        @if($article->status === 'published')
        <form method="POST" action="{{ route('training-news.unpublish', $article->id) }}" style="margin:0;" onsubmit="return confirm('Move back to draft?')">@csrf
            <button class="btn btn-secondary btn-sm">Unpublish</button>
        </form>
        @endif

        @if($article->status !== 'archived')
        <form method="POST" action="{{ route('training-news.archive', $article->id) }}" style="margin:0;" onsubmit="return confirm('Archive this article?')">@csrf
            <button class="btn btn-outline-secondary btn-sm">Archive</button>
        </form>
        @endif

        <form method="POST" action="{{ route('training-news.destroy', $article->id) }}" style="margin:0;margin-left:auto;" onsubmit="return confirm('Permanently delete this article?')">
            @csrf @method('DELETE')
            <button class="btn btn-del btn-sm">Delete</button>
        </form>
    </div>
</div>

<x-flash-message />

<form method="POST" action="{{ route('training-news.update', $article->id) }}" enctype="multipart/form-data">
@csrf @method('PUT')

<div class="row g-4">
<div class="col-lg-8">

    {{-- Article Content --}}
    <div class="edit-section">
        <div class="edit-section-title">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
            Article Content
        </div>
        <div class="field-group">
            <label>Headline *</label>
            <input type="text" name="title" value="{{ old('title', $article->title) }}" required>
        </div>
        <div class="field-group">
            <label>Excerpt *</label>
            <textarea name="excerpt" style="min-height:80px;" required>{{ old('excerpt', $article->excerpt) }}</textarea>
        </div>
        <div class="field-group">
            <label>Full Content (HTML) *</label>
            <textarea name="content" style="min-height:400px;font-family:monospace;font-size:12px;" required>{{ old('content', $article->content) }}</textarea>
        </div>
    </div>

    {{-- SEO --}}
    <div class="edit-section">
        <div class="edit-section-title">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            SEO & Meta
        </div>
        <div class="row g-3">
            <div class="col-md-6">
                <div class="field-group">
                    <label>SEO Title</label>
                    <input type="text" name="seo_title" value="{{ old('seo_title', $article->seo_title) }}" maxlength="70">
                </div>
            </div>
            <div class="col-md-6">
                <div class="field-group">
                    <label>OG Title</label>
                    <input type="text" name="og_title" value="{{ old('og_title', $article->og_title) }}" maxlength="255">
                </div>
            </div>
            <div class="col-12">
                <div class="field-group">
                    <label>Meta Description</label>
                    <textarea name="seo_description" style="min-height:70px;">{{ old('seo_description', $article->seo_description) }}</textarea>
                </div>
            </div>
            <div class="col-12">
                <div class="field-group">
                    <label>OG Description</label>
                    <textarea name="og_description" style="min-height:60px;">{{ old('og_description', $article->og_description) }}</textarea>
                </div>
            </div>
            <div class="col-md-6">
                <div class="field-group">
                    <label>Focus Keywords</label>
                    <input type="text" name="focus_keywords" value="{{ old('focus_keywords', $article->focus_keywords) }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="field-group">
                    <label>Tags (comma separated)</label>
                    <input type="text" name="tags" value="{{ old('tags', is_array($article->tags) ? implode(', ', $article->tags) : $article->tags) }}">
                </div>
            </div>
        </div>
    </div>

    {{-- Social Media --}}
    <div class="edit-section">
        <div class="edit-section-title">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
            Social Media Posts
        </div>
        @foreach([
            ['name'=>'social_linkedin','label'=>'💼 LinkedIn','rows'=>6],
            ['name'=>'social_facebook','label'=>'👥 Facebook','rows'=>5],
            ['name'=>'social_twitter','label'=>'🐦 X (Twitter)','rows'=>3],
            ['name'=>'social_instagram','label'=>'📷 Instagram','rows'=>5],
        ] as $s)
        <div class="field-group">
            <label>{{ $s['label'] }}</label>
            <textarea name="{{ $s['name'] }}" style="min-height:{{ $s['rows']*22 }}px;">{{ old($s['name'], $article->{$s['name']}) }}</textarea>
        </div>
        @endforeach
        <div class="field-group">
            <label>Hashtags</label>
            <input type="text" name="hashtags" value="{{ old('hashtags', $article->hashtags) }}">
        </div>
    </div>

</div>
<div class="col-lg-4">

    {{-- Settings --}}
    <div class="edit-section">
        <div class="edit-section-title">Settings</div>
        <div class="field-group">
            <label>Article Type</label>
            <select name="article_type">
                @foreach(['training_news'=>'Training News','success_story'=>'Success Story','course_announcement'=>'Course Announcement','blog_post'=>'Blog Post'] as $v=>$l)
                <option value="{{ $v }}" {{ $article->article_type===$v?'selected':'' }}>{{ $l }}</option>
                @endforeach
            </select>
        </div>
        <div class="field-group">
            <label>Category</label>
            <select name="blog_category_id">
                <option value="">— None —</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ $article->blog_category_id==$cat->id?'selected':'' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="field-group">
            <label>Featured Image</label>
            @if($article->featured_image)
            <img src="{{ asset('storage/'.$article->featured_image) }}" alt="" style="width:100%;border-radius:8px;margin-bottom:8px;object-fit:cover;max-height:140px;">
            @endif
            <input type="file" name="featured_image" accept="image/*" class="fi" style="padding:6px;">
        </div>
    </div>

    {{-- Article Info --}}
    <div class="edit-section">
        <div class="edit-section-title">Info</div>
        <table style="width:100%;font-size:12px;border-collapse:collapse;">
            @foreach([
                ['Author', $article->author ?? '—'],
                ['Views', number_format($article->view_count)],
                ['Reading Time', ($article->reading_time ?? 1) . ' min'],
                ['Created', $article->created_at->format('d M Y')],
                ['Updated', $article->updated_at->format('d M Y H:i')],
                ['Published', $article->published_at ? $article->published_at->format('d M Y') : '—'],
                ['Approved By', $article->approvedBy->name ?? '—'],
            ] as [$label, $val])
            <tr>
                <td style="padding:5px 0;color:#6b7280;font-weight:600;">{{ $label }}</td>
                <td style="padding:5px 0;text-align:right;color:#111827;">{{ $val }}</td>
            </tr>
            @endforeach
        </table>
    </div>

    {{-- Change Log --}}
    @if($article->change_log)
    <div class="edit-section">
        <div class="edit-section-title">Audit Trail</div>
        @foreach(array_reverse($article->change_log) as $entry)
        <div class="change-log-item">
            <div style="color:#9ca3af;min-width:90px;">{{ \Carbon\Carbon::parse($entry['timestamp'])->format('d M H:i') }}</div>
            <div style="color:#374151;font-weight:600;">{{ ucwords(str_replace('_',' ',$entry['action'])) }}</div>
        </div>
        @endforeach
    </div>
    @endif

</div>
</div>

<div style="display:flex;gap:10px;padding:16px;background:#f8fafc;border-radius:12px;margin-top:4px;">
    <button type="submit" class="btn btn-primary btn-sm">Save Changes</button>
    <a href="{{ route('training-news.index') }}" class="btn btn-ghost btn-sm">Cancel</a>
</div>

</form>
@endsection
