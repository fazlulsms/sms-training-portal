@extends('layouts.app')
@section('page-title', 'AI Question Bank')
@section('content')
<div class="page-wrap">
    <x-page-header title="AI Question Bank" desc="Reusable, source-linked questions generated from approved Knowledge Hub content." />
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <form class="filter-bar" method="GET">
        <div class="filter-row">
            <div class="filter-group"><label>Course</label><select class="filter-select" name="course_id"><option value="">All courses</option>@foreach($courses as $course)<option value="{{ $course->id }}" @selected(request('course_id') == $course->id)>{{ $course->name }}</option>@endforeach</select></div>
            <div class="filter-group"><label>Difficulty</label><select class="filter-select" name="difficulty"><option value="">All</option>@foreach(['easy','medium','hard'] as $value)<option value="{{ $value }}" @selected(request('difficulty')===$value)>{{ ucfirst($value) }}</option>@endforeach</select></div>
            <div class="filter-group"><label>Status</label><select class="filter-select" name="status"><option value="">All</option>@foreach(['draft','approved','archived'] as $value)<option value="{{ $value }}" @selected(request('status')===$value)>{{ ucfirst($value) }}</option>@endforeach</select></div>
            <button class="btn btn-primary">Filter</button>
        </div>
    </form>
    <div class="dt-wrap"><div class="dt-scroll"><table class="dt">
        <thead><tr><th>Question</th><th>Type</th><th>Difficulty</th><th>Source</th><th>Lesson</th><th>Status</th></tr></thead>
        <tbody>
        @forelse($questions as $question)
            <tr>
                <td><div class="td-main">{{ $question->question_text }}</div><div class="td-sub">{{ $question->explanation }}</div></td>
                <td>{{ ucfirst($question->question_type) }}</td>
                <td><span class="badge badge-secondary">{{ ucfirst($question->difficulty) }}</span></td>
                <td>@if($question->resource)<a href="{{ route('knowledge-hub.show', $question->resource) }}">{{ $question->resource->title }}</a>@else<span class="badge badge-danger">Missing source</span>@endif</td>
                <td>{{ $question->lesson?->title ?: 'Final exam pool' }}</td>
                <td>
                    <form method="POST" action="{{ route('ai.question-bank.status', $question) }}">@csrf @method('PATCH')
                        <select class="filter-select" name="status" onchange="this.form.submit()">@foreach(['draft','approved','archived'] as $value)<option value="{{ $value }}" @selected($question->status===$value)>{{ ucfirst($value) }}</option>@endforeach</select>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="6"><div class="empty-state"><div class="empty-title">No questions generated yet</div></div></td></tr>
        @endforelse
        </tbody>
    </table></div></div>
    {{ $questions->links() }}
</div>
@endsection
