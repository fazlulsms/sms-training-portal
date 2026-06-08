@extends('layouts.app')
@section('page-title', 'eLearning Courses')
@section('content')

<x-page-header title="eLearning Courses" desc="Manage online courses, lessons, quizzes, and enrollments.">
    <x-slot:actions>
        <a href="{{ route('elearning.courses.create') }}" class="btn btn-primary btn-sm">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Create Course
        </a>
    </x-slot:actions>
</x-page-header>

<x-flash-message />

<div class="filter-bar">
    <div class="filter-row">
        <div class="fi-search-wrap" style="flex:1;min-width:220px;">
            <span class="fi-search-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></span>
            <input class="fi fi-search" id="elSearch" type="text" placeholder="Search course name or code…" style="width:100%;">
        </div>
        <select class="fi" id="elStatus" style="min-width:130px;">
            <option value="">All Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </select>
    </div>
</div>

<div class="dt-wrap">
    <div class="dt-scroll">
        <table class="dt" id="elTable">
            <thead>
                <tr>
                    <th>Course</th>
                    <th>Code</th>
                    <th class="r">Fee</th>
                    <th class="c">Access</th>
                    <th class="c">Pass %</th>
                    <th class="c">Status</th>
                    <th class="c">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($courses as $course)
                <tr data-status="{{ $course->status == 1 ? 'active' : 'inactive' }}">
                    <td class="td-main">{{ $course->name }}</td>
                    <td><span class="td-mono">{{ $course->code }}</span></td>
                    <td class="r fw-bold">{{ number_format($course->course_fee, 2) }}</td>
                    <td class="c text-muted">{{ $course->access_days }} days</td>
                    <td class="c text-muted">{{ $course->passing_score }}%</td>
                    <td class="c">
                        @if($course->status == 1)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-secondary">Inactive</span>
                        @endif
                    </td>
                    <td class="c">
                        <div class="dt-actions" style="justify-content:center;">
                            <a href="{{ route('elearning.lessons.index', $course) }}" class="btn btn-primary btn-xs">Lessons</a>
                            <a href="{{ route('elearning.courses.edit', $course) }}" class="btn btn-edit btn-xs">Edit</a>
                            <form action="{{ route('elearning.courses.destroy', $course) }}" method="POST"
                                  onsubmit="return confirm('Delete course {{ addslashes($course->name) }}?')" style="margin:0;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-del btn-xs">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <div class="empty-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                            </div>
                            <p class="empty-title">No eLearning courses yet</p>
                            <p class="empty-desc">Create your first online course to start enrolling participants.</p>
                            <a href="{{ route('elearning.courses.create') }}" class="btn btn-primary btn-sm">Create Course</a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:14px 16px;">{{ $courses->links() }}</div>
</div>

<script>
(function () {
    const s = document.getElementById('elSearch');
    const f = document.getElementById('elStatus');
    function filter() {
        const q = s.value.toLowerCase(), st = f.value;
        document.querySelectorAll('#elTable tbody tr[data-status]').forEach(r => {
            r.style.display = (!q || r.innerText.toLowerCase().includes(q)) && (!st || r.dataset.status === st) ? '' : 'none';
        });
    }
    [s, f].forEach(el => el.addEventListener('input', filter));
})();
</script>

@endsection
