@extends('layouts.app')
@section('page-title', 'Create Lesson')

@section('content')
<x-flash-message />

<x-page-header
    title="Create New Lesson"
    desc="{{ $course->name }}"
>
    <x-slot name="actions">
        <a href="{{ route('elearning.lessons.index', $course) }}" class="btn btn-ghost btn-sm">← Back to Lessons</a>
    </x-slot>
</x-page-header>

<div style="max-width:780px;">
    <div class="card">
        <div class="card-header">Lesson Settings</div>
        <div class="card-body">

            <form action="{{ route('elearning.lessons.store', $course) }}" method="POST">
                @csrf

                @include('elearning.lessons.form', ['lesson' => null])

                <div style="display:flex; gap:10px; margin-top:20px;">
                    <button type="submit" class="btn btn-primary">
                        Create Lesson &amp; Open Builder →
                    </button>
                    <a href="{{ route('elearning.lessons.index', $course) }}" class="btn btn-ghost">Cancel</a>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection
