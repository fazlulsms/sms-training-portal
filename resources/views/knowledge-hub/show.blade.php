@extends('layouts.app')
@section('page-title', $resource->title)

@section('content')
<div class="page-wrap">
    <x-page-header :title="$resource->title" desc="Knowledge resource details and secure file access.">
        <x-slot:actions>
            <a href="{{ route('knowledge-hub.index') }}" class="btn btn-ghost">Back</a>
            <a href="{{ route('knowledge-hub.file', $resource) }}" class="btn btn-view" target="_blank">View File</a>
            <a href="{{ route('knowledge-hub.download', $resource) }}" class="btn btn-primary">Download</a>
            @can('update', $resource)
                <a href="{{ route('knowledge-hub.edit', $resource) }}" class="btn btn-edit">Edit</a>
            @endcan
        </x-slot:actions>
    </x-page-header>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="kh-detail-grid">
        <div class="card">
            <div class="card-header"><h3>Resource Details</h3></div>
            <div class="card-body">
                <dl class="kh-details">
                    <div><dt>Title</dt><dd>{{ $resource->title }}</dd></div>
                    <div><dt>Resource Type</dt><dd>{{ $resource->resource_type }}</dd></div>
                    <div><dt>Category</dt><dd>{{ $resource->category }}</dd></div>
                    <div><dt>Standard / Framework</dt><dd>{{ $resource->standard_framework }}</dd></div>
                    <div><dt>Version</dt><dd>{{ $resource->version ?: '—' }}</dd></div>
                    <div>
                        <dt>Status</dt>
                        <dd><span class="badge {{ $resource->status === 'approved' ? 'badge-success' : ($resource->status === 'archived' ? 'badge-secondary' : 'badge-warning') }}">{{ ucfirst($resource->status) }}</span></dd>
                    </div>
                    <div><dt>Uploaded By</dt><dd>{{ $resource->uploader?->name ?: 'Unknown' }}</dd></div>
                    <div><dt>Uploaded</dt><dd>{{ $resource->created_at->format('d M Y, g:i A') }}</dd></div>
                </dl>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h3>Uploaded File</h3></div>
            <div class="card-body">
                <div style="font-weight:700;color:#111827;word-break:break-word;">{{ $resource->original_file_name }}</div>
                <div style="font-size:12px;color:#6b7280;margin-top:5px;">{{ $resource->mime_type ?: 'Unknown file type' }} · {{ $resource->file_size_human }}</div>
                <div style="display:flex;gap:8px;margin-top:18px;flex-wrap:wrap;">
                    <a href="{{ route('knowledge-hub.file', $resource) }}" class="btn btn-view btn-sm" target="_blank">View</a>
                    <a href="{{ route('knowledge-hub.download', $resource) }}" class="btn btn-primary btn-sm">Download</a>
                </div>
            </div>
        </div>
    </div>

    <div class="card" style="margin-top:16px;">
        <div class="card-header"><h3>Notes / Remarks</h3></div>
        <div class="card-body" style="white-space:pre-wrap;line-height:1.65;color:#374151;">{{ $resource->notes ?: 'No notes added.' }}</div>
    </div>

    @can('archive', $resource)
        @if($resource->status !== 'archived')
            <form method="POST" action="{{ route('knowledge-hub.archive', $resource) }}" style="margin-top:16px;" onsubmit="return confirm('Archive this resource?')">
                @csrf
                <button type="submit" class="btn btn-del">Archive Resource</button>
            </form>
        @endif
    @endcan
</div>

<style>
    .kh-detail-grid { display:grid;grid-template-columns:2fr 1fr;gap:16px; }
    .kh-details { display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:20px;margin:0; }
    .kh-details div { min-width:0; }
    .kh-details dt { font-size:11px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280;font-weight:700;margin-bottom:5px; }
    .kh-details dd { margin:0;color:#111827;font-size:13.5px;font-weight:600;overflow-wrap:anywhere; }
    @media(max-width:850px) {
        .kh-detail-grid,.kh-details { grid-template-columns:1fr; }
    }
</style>
@endsection
