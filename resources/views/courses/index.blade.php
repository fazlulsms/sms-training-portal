@extends('layouts.app')
@section('page-title', 'Courses')
@section('content')

<x-page-header title="Courses" desc="Manage all training and eLearning course records.">
    <x-slot:actions>
        <a href="/admin/courses/create" class="btn btn-primary btn-sm">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add Course
        </a>
    </x-slot:actions>
</x-page-header>

<x-flash-message />

<div class="filter-bar">
    <form method="GET" action="/admin/courses" style="display:contents;">
        <div class="filter-row">
            <div class="fi-search-wrap" style="flex:1;min-width:220px;">
                <span class="fi-search-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></span>
                <input class="fi fi-search" type="text" name="q" value="{{ request('q') }}" placeholder="Search by name or code…" style="width:100%;">
            </div>
            <select class="fi" name="course_type" style="min-width:150px;">
                <option value="">All Types</option>
                <option value="elearning" {{ request('course_type') === 'elearning' ? 'selected' : '' }}>eLearning</option>
                <option value="manual" {{ request('course_type') === 'manual' ? 'selected' : '' }}>Manual Training</option>
            </select>
            <select class="fi" name="status" style="min-width:130px;">
                <option value="">All Status</option>
                <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            <button type="submit" class="btn btn-primary btn-sm">Filter</button>
            @if(request()->hasAny(['q','course_type','status']))
            <a href="/admin/courses" class="btn btn-ghost btn-sm">✕ Clear</a>
            @endif
            <a href="/admin/courses/export?{{ http_build_query(request()->only(['q','course_type','status'])) }}" class="btn btn-secondary btn-sm">⬇ CSV</a>
        </div>
    </form>
</div>

<div class="dt-wrap">
    <div class="dt-scroll">
        <table class="dt" id="coursesTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Course</th>
                    <th>Code</th>
                    <th class="c">Type</th>
                    <th class="c">Status</th>
                    <th class="c">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($courses as $i => $course)
                <tr>
                    <td class="text-muted text-small">{{ $courses->firstItem() + $loop->index }}</td>
                    <td class="td-main">{{ $course->name }}</td>
                    <td><span class="td-mono">{{ $course->code }}</span></td>
                    <td class="c">
                        @if($course->course_type === 'elearning')
                            <span class="badge badge-info">eLearning</span>
                        @else
                            <span class="badge badge-secondary">Manual</span>
                        @endif
                    </td>
                    <td class="c">
                        @if($course->status)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-secondary">Inactive</span>
                        @endif
                    </td>
                    <td class="c">
                        <div class="dt-actions" style="justify-content:center;">
                            <a href="/admin/courses/edit/{{ $course->id }}" class="btn btn-edit btn-xs">Edit</a>
                            <a href="/admin/courses/delete/{{ $course->id }}"
                               onclick="return confirm('Delete course {{ addslashes($course->name) }}?')"
                               class="btn btn-del btn-xs">Delete</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <div class="empty-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                            </div>
                            <p class="empty-title">No courses found</p>
                            <p class="empty-desc">Create your first course to begin scheduling training.</p>
                            <a href="/admin/courses/create" class="btn btn-primary btn-sm">Add Course</a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($courses->hasPages())
    <div style="padding:14px 16px;border-top:1px solid #f0f2f5;">{{ $courses->links() }}</div>
    @endif
</div>

@endsection
