<div class="space-y-4">

    <div>
        <label class="block text-sm font-medium mb-1">Question</label>
        <textarea name="question_text"
                  rows="4"
                  class="w-full border rounded-lg px-4 py-2"
                  required>{{ old('question_text', $question->question_text ?? '') }}</textarea>
    </div>

    <div class="grid grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">Question Type</label>
            <select name="question_type" class="w-full border rounded-lg px-4 py-2">
                <option value="mcq" {{ old('question_type', $question->question_type ?? 'mcq') == 'mcq' ? 'selected' : '' }}>
                    MCQ
                </option>
                <option value="true_false" {{ old('question_type', $question->question_type ?? '') == 'true_false' ? 'selected' : '' }}>
                    True / False
                </option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Marks</label>
            <input type="number"
                   name="marks"
                   value="{{ old('marks', $question->marks ?? 1) }}"
                   class="w-full border rounded-lg px-4 py-2"
                   required>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Status</label>
            <select name="status" class="w-full border rounded-lg px-4 py-2">
                <option value="active" {{ old('status', $question->status ?? 'active') == 'active' ? 'selected' : '' }}>
                    Active
                </option>
                <option value="inactive" {{ old('status', $question->status ?? '') == 'inactive' ? 'selected' : '' }}>
                    Inactive
                </option>
            </select>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">Option A</label>
            <input type="text"
                   name="option_a"
                   value="{{ old('option_a', $question->option_a ?? '') }}"
                   class="w-full border rounded-lg px-4 py-2">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Option B</label>
            <input type="text"
                   name="option_b"
                   value="{{ old('option_b', $question->option_b ?? '') }}"
                   class="w-full border rounded-lg px-4 py-2">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Option C</label>
            <input type="text"
                   name="option_c"
                   value="{{ old('option_c', $question->option_c ?? '') }}"
                   class="w-full border rounded-lg px-4 py-2">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Option D</label>
            <input type="text"
                   name="option_d"
                   value="{{ old('option_d', $question->option_d ?? '') }}"
                   class="w-full border rounded-lg px-4 py-2">
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Correct Answer</label>
        <select name="correct_answer" class="w-full border rounded-lg px-4 py-2">
            <option value="A" {{ old('correct_answer', $question->correct_answer ?? '') == 'A' ? 'selected' : '' }}>A</option>
            <option value="B" {{ old('correct_answer', $question->correct_answer ?? '') == 'B' ? 'selected' : '' }}>B</option>
            <option value="C" {{ old('correct_answer', $question->correct_answer ?? '') == 'C' ? 'selected' : '' }}>C</option>
            <option value="D" {{ old('correct_answer', $question->correct_answer ?? '') == 'D' ? 'selected' : '' }}>D</option>
            <option value="true" {{ old('correct_answer', $question->correct_answer ?? '') == 'true' ? 'selected' : '' }}>True</option>
            <option value="false" {{ old('correct_answer', $question->correct_answer ?? '') == 'false' ? 'selected' : '' }}>False</option>
        </select>
    </div>

    <div class="flex gap-3">
        <button type="submit"
                class="bg-blue-600 text-white px-6 py-2 rounded-lg">
            Save Question
        </button>

        <a href="{{ route('elearning.quiz-questions.index', [$course, $lesson, $quiz]) }}"
           class="bg-gray-200 text-gray-800 px-6 py-2 rounded-lg">
            Back
        </a>
    </div>

</div>