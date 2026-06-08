<div class="space-y-4">

    <div>
        <label class="block text-sm font-medium mb-1">Quiz Title</label>
        <input type="text"
               name="title"
               value="{{ old('title', $quiz->title ?? '') }}"
               class="w-full border rounded-lg px-4 py-2"
               required>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Description</label>
        <textarea name="description"
                  rows="4"
                  class="w-full border rounded-lg px-4 py-2">{{ old('description', $quiz->description ?? '') }}</textarea>
    </div>

    <div class="grid grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">Pass Mark (%)</label>
            <input type="number"
                   name="pass_mark"
                   value="{{ old('pass_mark', $quiz->pass_mark ?? 70) }}"
                   class="w-full border rounded-lg px-4 py-2"
                   required>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Max Attempt</label>
            <input type="number"
                   name="max_attempt"
                   value="{{ old('max_attempt', $quiz->max_attempt ?? 3) }}"
                   class="w-full border rounded-lg px-4 py-2"
                   required>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Status</label>
            <select name="status" class="w-full border rounded-lg px-4 py-2">
                <option value="active" {{ old('status', $quiz->status ?? 'active') == 'active' ? 'selected' : '' }}>
                    Active
                </option>
                <option value="inactive" {{ old('status', $quiz->status ?? '') == 'inactive' ? 'selected' : '' }}>
                    Inactive
                </option>
            </select>
        </div>
    </div>

    <div class="flex gap-3">
        <button type="submit"
                class="bg-blue-600 text-white px-6 py-2 rounded-lg">
            Save Quiz
        </button>

        <a href="{{ route('elearning.quizzes.index', [$course, $lesson]) }}"
           class="bg-gray-200 text-gray-800 px-6 py-2 rounded-lg">
            Back
        </a>
    </div>

</div>