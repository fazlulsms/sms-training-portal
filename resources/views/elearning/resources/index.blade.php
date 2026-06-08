@extends('layouts.app')
@section('page-title', 'Resources — ' . $lesson->title)
@section('content')

<x-page-header title="Lesson Resources" desc="{{ $course->name }} → {{ $lesson->title }}">
    <x-slot:actions>
        <a href="{{ route('elearning.lessons.index', $course) }}" class="btn btn-ghost btn-sm">← Lessons</a>
        <a href="{{ route('elearning.resources.create', [$course, $lesson]) }}" class="btn btn-primary btn-sm">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add Resource
        </a>
    </x-slot:actions>
</x-page-header>

<x-flash-message />

<div class="dt-wrap">
    <div class="dt-scroll">
        <table class="dt">
            <thead>
                <tr>
                    <th>Title</th>
                    <th class="c">Type</th>
                    <th>Link / File</th>
                    <th class="c">Status</th>
                    <th class="c">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($resources as $resource)
                <tr>
                    <td class="td-main">{{ $resource->title }}</td>
                    <td class="c"><span class="badge badge-info">{{ ucfirst($resource->resource_type) }}</span></td>
                    <td>
                        @if($resource->file_path)
                            <a href="{{ asset('storage/' . $resource->file_path) }}" target="_blank"
                               class="btn btn-view btn-xs">View File</a>
                        @elseif($resource->external_url)
                            <a href="{{ $resource->external_url }}" target="_blank"
                               class="btn btn-view btn-xs">Open Link</a>
                        @else
                            <span class="text-muted text-small">—</span>
                        @endif
                    </td>
                    <td class="c">
                        @if($resource->status === 'active')
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-secondary">{{ ucfirst($resource->status) }}</span>
                        @endif
                    </td>
                    <td class="c">
                        <form action="{{ route('elearning.resources.destroy', [$course, $lesson, $resource]) }}" method="POST"
                              onsubmit="return confirm('Delete this resource?')" style="margin:0;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-del btn-xs">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5">
                        <div class="empty-state">
                            <div class="empty-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>
                            </div>
                            <p class="empty-title">No resources yet</p>
                            <p class="empty-desc">Attach files or links to support learner study for this lesson.</p>
                            <a href="{{ route('elearning.resources.create', [$course, $lesson]) }}" class="btn btn-primary btn-sm">Add Resource</a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
