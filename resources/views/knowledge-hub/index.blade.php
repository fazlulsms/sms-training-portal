@extends('layouts.app')
@section('page-title', 'Knowledge Hub')

@section('content')
<div class="page-wrap">
    <x-page-header title="Knowledge Hub" desc="Approved standards, guidance, examples, and SMS expertise in one secure library.">
        @can('create', \App\Models\KnowledgeResource::class)
            <x-slot:actions>
                <a href="{{ route('knowledge-hub.create') }}" class="btn btn-primary">+ Add Resource</a>
            </x-slot:actions>
        @endcan
    </x-page-header>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="GET" action="{{ route('knowledge-hub.index') }}" class="filter-bar">
        <div class="filter-row">
            <div class="filter-group" style="flex:1;min-width:220px;">
                <label for="search">Keyword</label>
                <input class="filter-input" id="search" name="search" value="{{ request('search') }}"
                       placeholder="Search title or notes">
            </div>
            <div class="filter-group">
                <label for="resource_type">Resource Type</label>
                <select class="filter-select" id="resource_type" name="resource_type">
                    <option value="">All types</option>
                    @foreach(\App\Models\KnowledgeResource::RESOURCE_TYPES as $type)
                        <option value="{{ $type }}" @selected(request('resource_type') === $type)>{{ $type }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label for="category">Category</label>
                <select class="filter-select" id="category" name="category">
                    <option value="">All categories</option>
                    @foreach(\App\Models\KnowledgeResource::CATEGORIES as $category)
                        <option value="{{ $category }}" @selected(request('category') === $category)>{{ $category }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label for="standard_framework">Standard / Framework</label>
                <select class="filter-select" id="standard_framework" name="standard_framework">
                    <option value="">All frameworks</option>
                    @foreach($frameworks as $framework)
                        <option value="{{ $framework }}" @selected(request('standard_framework') === $framework)>{{ $framework }}</option>
                    @endforeach
                </select>
            </div>
            @if(auth()->user()->isAdmin())
                <div class="filter-group">
                    <label for="status">Status</label>
                    <select class="filter-select" id="status" name="status">
                        <option value="">All statuses</option>
                        @foreach(\App\Models\KnowledgeResource::STATUSES as $status)
                            <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
            <button class="btn btn-primary" type="submit">Filter</button>
            <a class="btn btn-ghost" href="{{ route('knowledge-hub.index') }}">Reset</a>
        </div>
    </form>

    <div class="dt-wrap">
        <div class="dt-scroll">
            <table class="dt">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Resource Type</th>
                        <th>Category</th>
                        <th>Standard / Framework</th>
                        <th>Status</th>
                        <th>File</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($resources as $resource)
                    <tr>
                        <td>
                            <div class="td-main">{{ $resource->title }}</div>
                            @if($resource->version)<div class="td-sub">Version {{ $resource->version }}</div>@endif
                        </td>
                        <td>{{ $resource->resource_type }}</td>
                        <td>{{ $resource->category }}</td>
                        <td>{{ $resource->standard_framework }}</td>
                        <td>
                            @php
                                $badge = match($resource->status) {
                                    'approved' => 'badge-success',
                                    'archived' => 'badge-secondary',
                                    default => 'badge-warning',
                                };
                            @endphp
                            <span class="badge {{ $badge }}">{{ ucfirst($resource->status) }}</span>
                        </td>
                        <td>
                            <div class="td-main" style="max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $resource->original_file_name }}">
                                {{ $resource->original_file_name }}
                            </div>
                            <div class="td-sub">{{ $resource->file_size_human }}</div>
                        </td>
                        <td>
                            <div class="dt-actions">
                                <a class="btn btn-view btn-xs" href="{{ route('knowledge-hub.show', $resource) }}">View</a>
                                @can('update', $resource)
                                    <a class="btn btn-edit btn-xs" href="{{ route('knowledge-hub.edit', $resource) }}">Edit</a>
                                @endcan
                                <a class="btn btn-ghost btn-xs" href="{{ route('knowledge-hub.download', $resource) }}">Download</a>
                                @can('archive', $resource)
                                    @if($resource->status !== 'archived')
                                        <form method="POST" action="{{ route('knowledge-hub.archive', $resource) }}" onsubmit="return confirm('Archive this resource?')">
                                            @csrf
                                            <button class="btn btn-del btn-xs" type="submit">Archive</button>
                                        </form>
                                    @endif
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <div class="empty-title">No knowledge resources found</div>
                                <p class="empty-desc">Try adjusting the filters or add the first source material.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $resources->links() }}
</div>
@endsection
