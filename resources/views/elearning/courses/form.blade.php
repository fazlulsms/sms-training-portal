<div class="space-y-4">

    <div>
        <label class="block text-sm font-medium mb-1">Course Name</label>
        <input type="text" name="name"
               value="{{ old('name', $course->name ?? '') }}"
               class="w-full border rounded-lg px-4 py-2" required>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Course Code</label>
        <input type="text" name="code"
               value="{{ old('code', $course->code ?? '') }}"
               class="w-full border rounded-lg px-4 py-2" required>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Description</label>
        <textarea name="description"
                  rows="4"
                  class="w-full border rounded-lg px-4 py-2">{{ old('description', $course->description ?? '') }}</textarea>
    </div>

<div class="grid grid-cols-2 gap-4">

        <div class="grid grid-cols-5 gap-4">

    <div>
        <label class="block text-sm font-medium mb-1">Course Fee</label>
        <input type="number" step="0.01" name="course_fee"
               value="{{ old('course_fee', $course->course_fee ?? '') }}"
               class="w-full border rounded-lg px-4 py-2">
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Access Days</label>
        <input type="number" name="access_days"
               value="{{ old('access_days', $course->access_days ?? 30) }}"
               class="w-full border rounded-lg px-4 py-2">
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Passing Score (%)</label>
        <input type="number" name="passing_score"
               value="{{ old('passing_score', $course->passing_score ?? 70) }}"
               class="w-full border rounded-lg px-4 py-2">
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Duration</label>
        <input type="text" name="duration"
               value="{{ old('duration', $course->duration ?? '') }}"
               placeholder="24 Hours"
               class="w-full border rounded-lg px-4 py-2">
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">CPD Hours</label>
        <input type="number" name="cpd_hours"
               value="{{ old('cpd_hours', $course->cpd_hours ?? '') }}"
               placeholder="24"
               class="w-full border rounded-lg px-4 py-2">
    </div>

</div>

   <div>
    <label class="block text-sm font-medium mb-1">Status</label>
    <select name="status" class="w-full border rounded-lg px-4 py-2">
        <option value="1" {{ old('status', $course->status ?? 1) == 1 ? 'selected' : '' }}>
            Active
        </option>

        <option value="0" {{ old('status', $course->status ?? '') == 0 ? 'selected' : '' }}>
            Inactive
        </option>
    </select>
</div>

    <div>
        <button type="submit"
                class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
            Save Course
        </button>
    </div>

</div>