@extends('layouts.app')
@section('page-title', 'PPT eLearning Builder')

@section('content')
<div class="page-wrap">
    <x-page-header title="PPT eLearning Builder" desc="Transform PowerPoint presentations into narrated self-paced eLearning courses.">
        <x-slot:actions>
            <a href="{{ route('ppt-builder.create') }}" class="btn btn-primary">+ Upload Presentation</a>
        </x-slot:actions>
    </x-page-header>

    <x-flash-message />

    <form method="GET" action="{{ route('ppt-builder.index') }}" class="filter-bar">
        <div class="filter-row">
            <div class="filter-group" style="flex:1;min-width:220px;">
                <label>Keyword</label>
                <input class="filter-input" name="search" value="{{ request('search') }}" placeholder="Search by title">
            </div>
            <div class="filter-group">
                <label>Status</label>
                <select class="filter-select" name="status">
                    <option value="">All</option>
                    <option value="draft"      @selected(request('status')==='draft')>Draft</option>
                    <option value="processing" @selected(request('status')==='processing')>Processing</option>
                    <option value="ready"      @selected(request('status')==='ready')>Ready</option>
                    <option value="published"  @selected(request('status')==='published')>Published</option>
                </select>
            </div>
            <button class="btn btn-primary" type="submit">Filter</button>
            <a class="btn btn-ghost" href="{{ route('ppt-builder.index') }}">Reset</a>
        </div>
    </form>

    @if($courses->isEmpty())
        <div class="empty-state">
            <div class="empty-icon">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
            </div>
            <div class="empty-title">No PPT courses yet</div>
            <p class="empty-desc">Upload a PowerPoint presentation to get started. AI will help you turn it into a professional narrated eLearning course.</p>
            <a href="{{ route('ppt-builder.create') }}" class="btn btn-primary" style="margin-top:12px">Upload First Presentation</a>
        </div>
    @else
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:20px;margin-top:4px;">
            @foreach($courses as $course)
            @php
                $statusColor = match($course->status) {
                    'published'  => '#22c55e',
                    'ready'      => '#3b82f6',
                    'processing' => '#f59e0b',
                    default      => '#94a3b8',
                };
                $statusLabel = match($course->status) {
                    'published'  => 'Published',
                    'ready'      => 'Ready',
                    'processing' => 'Processing',
                    default      => 'Draft',
                };
            @endphp
            <div class="card" style="display:flex;flex-direction:column;">
                <div class="card-header" style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;">
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:15px;font-weight:700;color:#111827;line-height:1.3;">{{ $course->title }}</div>
                        @if($course->description)
                            <div style="font-size:12px;color:#6b7280;margin-top:3px;line-height:1.4;">{{ Str::limit($course->description, 80) }}</div>
                        @endif
                    </div>
                    <span class="badge" style="background:{{ $statusColor }}1a;color:{{ $statusColor }};border:1px solid {{ $statusColor }}33;flex-shrink:0;">{{ $statusLabel }}</span>
                </div>
                <div class="card-body" style="flex:1;">
                    <div style="display:flex;gap:20px;font-size:12px;color:#6b7280;margin-bottom:12px;">
                        <span><strong style="color:#374151;">{{ $course->total_slides }}</strong> slides</span>
                        <span><strong style="color:#374151;">{{ $course->file_size_human }}</strong></span>
                        <span>{{ $course->created_at->diffForHumans() }}</span>
                    </div>

                    @if($course->total_slides > 0)
                    @php $audioReady = $course->slides()->where('audio_status','ready')->count(); @endphp
                    <div style="margin-bottom:12px;">
                        <div style="display:flex;justify-content:space-between;font-size:11px;color:#6b7280;margin-bottom:4px;">
                            <span>Audio ready</span>
                            <span>{{ $audioReady }}/{{ $course->total_slides }}</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill {{ $audioReady === $course->total_slides && $course->total_slides > 0 ? 'done' : '' }}"
                                 style="width:{{ $course->total_slides > 0 ? round($audioReady/$course->total_slides*100) : 0 }}%"></div>
                        </div>
                    </div>
                    @endif

                    @if($course->processing_error)
                        <div class="alert alert-error" style="font-size:12px;padding:8px 12px;">{{ Str::limit($course->processing_error, 100) }}</div>
                    @endif
                </div>
                <div style="padding:12px 16px;border-top:1px solid #f1f5f9;display:flex;gap:8px;flex-wrap:wrap;">
                    @if($course->isReady())
                        <a class="btn btn-primary btn-sm" href="{{ route('ppt-builder.editor', $course) }}">Open Editor</a>
                    @endif
                    <form method="POST" action="{{ route('ppt-builder.destroy', $course) }}" onsubmit="return confirm('Delete this PPT course and all extracted slides?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-del btn-sm" type="submit">Delete</button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>

        <div style="margin-top:20px;">{{ $courses->links() }}</div>
    @endif
</div>
@endsection
