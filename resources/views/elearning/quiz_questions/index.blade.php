@extends('layouts.app')
@section('page-title', 'Quiz Questions — ' . $quiz->title)
@section('content')

<x-page-header title="Quiz Questions" desc="{{ $quiz->title }}">
    <x-slot:actions>
        <a href="{{ route('elearning.quizzes.index', [$course, $lesson]) }}" class="btn btn-ghost btn-sm">← Quizzes</a>
        <a href="{{ route('elearning.quiz-questions.create', [$course, $lesson, $quiz]) }}" class="btn btn-primary btn-sm">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add Question
        </a>
    </x-slot:actions>
</x-page-header>

<x-flash-message />

<div class="dt-wrap">
    <div class="dt-scroll">
        <table class="dt">
            <thead>
                <tr>
                    <th style="width:50%">Question</th>
                    <th class="c">Type</th>
                    <th>Correct Answer</th>
                    <th class="c">Marks</th>
                    <th class="c">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($questions as $i => $question)
                <tr>
                    <td>
                        <div class="td-main" style="white-space:normal;">
                            <span class="text-muted text-small" style="margin-right:6px;">{{ $i + 1 }}.</span>{{ $question->question_text }}
                        </div>
                    </td>
                    <td class="c"><span class="badge badge-secondary">{{ ucfirst(str_replace('_',' ',$question->question_type)) }}</span></td>
                    <td>
                        <span class="badge badge-success">{{ $question->correct_answer }}</span>
                    </td>
                    <td class="c fw-bold">{{ $question->marks }}</td>
                    <td class="c">
                        <div class="dt-actions" style="justify-content:center;">
                            <a href="{{ route('elearning.quiz-questions.edit', [$course, $lesson, $quiz, $question]) }}" class="btn btn-edit btn-xs">Edit</a>
                            <form action="{{ route('elearning.quiz-questions.destroy', [$course, $lesson, $quiz, $question]) }}" method="POST"
                                  onsubmit="return confirm('Delete this question?')" style="margin:0;">
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
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                            </div>
                            <p class="empty-title">No questions yet</p>
                            <p class="empty-desc">Add questions to enable this quiz for learners.</p>
                            <a href="{{ route('elearning.quiz-questions.create', [$course, $lesson, $quiz]) }}" class="btn btn-primary btn-sm">Add Question</a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
