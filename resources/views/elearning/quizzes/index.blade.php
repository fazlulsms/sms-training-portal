@extends('layouts.app')
@section('page-title', 'Quizzes — ' . $lesson->title)
@section('content')

<x-page-header title="Lesson Quizzes" desc="{{ $course->name }} → {{ $lesson->title }}">
    <x-slot:actions>
        <a href="{{ route('elearning.lessons.index', $course) }}" class="btn btn-ghost btn-sm">← Lessons</a>
        <a href="{{ route('elearning.quizzes.create', [$course, $lesson]) }}" class="btn btn-primary btn-sm">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add Quiz
        </a>
    </x-slot:actions>
</x-page-header>

<x-flash-message />

<div class="dt-wrap">
    <div class="dt-scroll">
        <table class="dt">
            <thead>
                <tr>
                    <th>Quiz Title</th>
                    <th class="c">Pass Mark</th>
                    <th class="c">Max Attempts</th>
                    <th class="c">Status</th>
                    <th class="c">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($quizzes as $quiz)
                <tr>
                    <td class="td-main">{{ $quiz->title }}</td>
                    <td class="c fw-bold">{{ $quiz->pass_mark }}%</td>
                    <td class="c text-muted">{{ $quiz->max_attempt ?: 'Unlimited' }}</td>
                    <td class="c">
                        @if($quiz->status === 'active')
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-secondary">{{ ucfirst($quiz->status) }}</span>
                        @endif
                    </td>
                    <td class="c">
                        <div class="dt-actions" style="justify-content:center;">
                            <a href="{{ route('elearning.quiz-questions.index', [$course, $lesson, $quiz]) }}"
                               class="btn btn-xs" style="background:#eef2ff;color:#4338ca;">Questions</a>
                            <a href="{{ route('elearning.quizzes.edit', [$course, $lesson, $quiz]) }}" class="btn btn-edit btn-xs">Edit</a>
                            <form action="{{ route('elearning.quizzes.destroy', [$course, $lesson, $quiz]) }}" method="POST"
                                  onsubmit="return confirm('Delete this quiz?')" style="margin:0;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-del btn-xs">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5">
                        <div class="empty-state">
                            <div class="empty-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                            </div>
                            <p class="empty-title">No quizzes yet</p>
                            <p class="empty-desc">Add a quiz to test learners on this lesson.</p>
                            <a href="{{ route('elearning.quizzes.create', [$course, $lesson]) }}" class="btn btn-primary btn-sm">Add Quiz</a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
