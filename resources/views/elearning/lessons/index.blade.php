@extends('layouts.app')
@section('page-title', 'Lessons — ' . $course->name)
@section('content')

<x-page-header title="Lessons" desc="{{ $course->name }}">
    <x-slot:actions>
        <a href="{{ route('elearning.courses.index') }}" class="btn btn-ghost btn-sm">← Courses</a>
        <a href="{{ route('elearning.lessons.create', $course) }}" class="btn btn-primary btn-sm">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add Lesson
        </a>
    </x-slot:actions>
</x-page-header>

<x-flash-message />

<div class="dt-wrap">
    <div class="dt-scroll">
        <table class="dt">
            <thead>
                <tr>
                    <th class="c" style="width:60px;">#</th>
                    <th>Lesson Title</th>
                    <th class="c">Duration</th>
                    <th>Video</th>
                    <th class="c">Status</th>
                    <th class="c">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($lessons as $lesson)
                <tr>
                    <td class="c fw-bold text-muted">{{ $lesson->lesson_order }}</td>
                    <td class="td-main">{{ $lesson->title }}</td>
                    <td class="c text-muted">{{ $lesson->duration_minutes ? $lesson->duration_minutes . ' min' : '—' }}</td>
                    <td>
                        @if($lesson->video_url)
                            <a href="{{ $lesson->video_url }}" target="_blank" class="btn btn-view btn-xs">Watch</a>
                        @else
                            <span class="text-muted text-small">No video</span>
                        @endif
                    </td>
                    <td class="c">
                        @if($lesson->status === 'published')
                            <span class="badge badge-success">Published</span>
                        @else
                            <span class="badge badge-secondary">{{ ucfirst($lesson->status) }}</span>
                        @endif
                    </td>
                    <td class="c">
                        <div class="dt-actions" style="justify-content:center;">
                            <a href="{{ route('elearning.resources.index', [$course, $lesson]) }}" class="btn btn-xs" style="background:#eef2ff;color:#4338ca;">Resources</a>
                            <a href="{{ route('elearning.quizzes.index', [$course, $lesson]) }}" class="btn btn-xs" style="background:#faf5ff;color:#7c3aed;">Quizzes</a>
                            <a href="{{ route('elearning.lessons.edit', [$course, $lesson]) }}" class="btn btn-edit btn-xs">Edit</a>
                            <form action="{{ route('elearning.lessons.destroy', [$course, $lesson]) }}" method="POST"
                                  onsubmit="return confirm('Delete this lesson?')" style="margin:0;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-del btn-xs">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <div class="empty-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg>
                            </div>
                            <p class="empty-title">No lessons yet</p>
                            <p class="empty-desc">Add the first lesson to start building this course.</p>
                            <a href="{{ route('elearning.lessons.create', $course) }}" class="btn btn-primary btn-sm">Add Lesson</a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:14px 16px;">{{ $lessons->links() }}</div>
</div>

@endsection
