@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto bg-white shadow rounded-xl p-6">

    <h1 class="text-2xl font-bold mb-2">{{ $quiz->title }}</h1>
    <p class="text-gray-500 mb-6">
        Participant: {{ $enrollment->participant_name }} |
        Attempt: {{ $attempt->attempt_no }}
    </p>

    <form action="{{ route('elearning.quiz-attempts.submit', [$enrollment, $quiz, $attempt]) }}" method="POST">
        @csrf

        <div class="space-y-6">
            @foreach($questions as $index => $question)
                <div class="border rounded-xl p-5">
                    <div class="font-semibold mb-3">
                        Q{{ $index + 1 }}. {{ $question->question_text }}
                        <span class="text-sm text-gray-500">({{ $question->marks }} mark)</span>
                    </div>

                    @if($question->question_type == 'true_false')
                        <label class="block mb-2">
                            <input type="radio" name="question_{{ $question->id }}" value="true" required>
                            True
                        </label>

                        <label class="block">
                            <input type="radio" name="question_{{ $question->id }}" value="false" required>
                            False
                        </label>
                    @else
                        <label class="block mb-2">
                            <input type="radio" name="question_{{ $question->id }}" value="A" required>
                            A. {{ $question->option_a }}
                        </label>

                        <label class="block mb-2">
                            <input type="radio" name="question_{{ $question->id }}" value="B" required>
                            B. {{ $question->option_b }}
                        </label>

                        <label class="block mb-2">
                            <input type="radio" name="question_{{ $question->id }}" value="C" required>
                            C. {{ $question->option_c }}
                        </label>

                        <label class="block">
                            <input type="radio" name="question_{{ $question->id }}" value="D" required>
                            D. {{ $question->option_d }}
                        </label>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="mt-6 flex gap-3">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg">
                Submit Quiz
            </button>

            <a href="{{ route('elearning.enrollments.show', $enrollment) }}"
               class="bg-gray-200 text-gray-800 px-6 py-2 rounded-lg">
                Cancel
            </a>
        </div>
    </form>

</div>
@endsection