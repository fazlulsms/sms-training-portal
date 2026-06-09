@extends('layouts.app')

@section('title', 'Blog Posts')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Blog & Articles</h1>
        <p class="page-subtitle">Manage published and draft blog posts</p>
    </div>
    <div style="display:flex;gap:10px;">
        <a href="{{ route('admin.blog.categories') }}" class="btn btn-secondary">🗂 Categories</a>
        <a href="{{ route('admin.blog.create') }}" class="btn btn-primary">+ New Post</a>
    </div>
</div>

{{-- Flash --}}
@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

{{-- Filters --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-body" style="padding:16px 20px;">
        <form method="GET" action="{{ route('admin.blog.index') }}" style="display:flex;gap:12px;align-items:center;flex-wrap:wrap;">
            <div style="position:relative;">
                <span style="position:absolute;left:9px;top:50%;transform:translateY(-50%);color:#9ca3af;pointer-events:none;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </span>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search title or author…"
                       style="padding:8px 12px 8px 30px;border:1px solid #e5e7eb;border-radius:8px;font-size:14px;width:220px;">
            </div>
            <select name="category_id" style="padding:8px 12px;border:1px solid #e5e7eb;border-radius:8px;font-size:14px;">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
            <select name="status" style="padding:8px 12px;border:1px solid #e5e7eb;border-radius:8px;font-size:14px;">
                <option value="">All Statuses</option>
                @foreach(['draft','published','archived'] as $s)
                <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary" style="padding:8px 16px;">Filter</button>
            @if(request()->hasAny(['q','status','category_id']))
            <a href="{{ route('admin.blog.index') }}" style="font-size:13px;color:#6b7280;text-decoration:none;">✕ Clear</a>
            @endif
            <div style="margin-left:auto;font-size:12.5px;color:#9ca3af;font-weight:600;">{{ $posts->total() }} post(s)</div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding:0;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Author</th>
                    <th>Status</th>
                    <th>Published</th>
                    <th>Views</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($posts as $post)
                <tr>
                    <td>
                        <div style="font-weight:700;color:#111827;">{{ $post->title }}</div>
                        <div style="font-size:12px;color:#9ca3af;margin-top:2px;">/blog/{{ $post->slug }}</div>
                    </td>
                    <td>
                        @if($post->category)
                        <span style="background:{{ $post->category->color ?? '#1e3a8a' }}22;color:{{ $post->category->color ?? '#1e3a8a' }};padding:3px 10px;border-radius:20px;font-size:12px;font-weight:700;">
                            {{ $post->category->name }}
                        </span>
                        @else <span style="color:#9ca3af;font-size:13px;">—</span> @endif
                    </td>
                    <td style="font-size:14px;">{{ $post->author ?? '—' }}</td>
                    <td>
                        @php $statusColors = ['published'=>'#16a34a','draft'=>'#d97706','archived'=>'#6b7280']; @endphp
                        <span style="background:{{ ($statusColors[$post->status] ?? '#6b7280') }}22;color:{{ $statusColors[$post->status] ?? '#6b7280' }};padding:3px 10px;border-radius:20px;font-size:12px;font-weight:700;">
                            {{ ucfirst($post->status) }}
                        </span>
                    </td>
                    <td style="font-size:13px;color:#6b7280;">
                        {{ $post->published_at?->format('d M Y') ?? '—' }}
                    </td>
                    <td style="font-size:14px;text-align:center;">{{ number_format($post->view_count) }}</td>
                    <td>
                        <div style="display:flex;gap:8px;align-items:center;">
                            @if($post->status === 'published')
                            <a href="{{ route('public.blog.detail', $post->slug) }}" target="_blank"
                               style="font-size:12px;color:#6b7280;text-decoration:none;">👁</a>
                            @endif
                            <a href="{{ route('admin.blog.edit', $post) }}" class="btn btn-sm btn-secondary">Edit</a>
                            <form method="POST" action="{{ route('admin.blog.destroy', $post) }}"
                                  onsubmit="return confirm('Delete this post?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;padding:40px;color:#9ca3af;">No posts found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($posts->hasPages())
    <div style="padding:16px 20px;border-top:1px solid #f0f2f5;">
        {{ $posts->links() }}
    </div>
    @endif
</div>
@endsection
