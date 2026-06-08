@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto bg-white shadow rounded-xl p-6">

    <h1 class="text-2xl font-bold mb-2">Edit Quiz Question</h1>
    <p class="text-gray-500 mb-6">{{ $quiz->title }}</p>

    @if ($errors->any())
        <div class="bg-red-100 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc ml-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('elearning.quiz-questions.update', [$course, $lesson, $quiz, $question]) }}" method="POST">
        @csrf
        @method('PUT')

        @include('elearning.quiz_questions.form')
    </form>

</div>
@endsection