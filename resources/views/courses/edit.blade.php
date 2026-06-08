@extends('layouts.app')

@section('content')

<h2 class="text-2xl font-bold mb-4">Edit Course</h2>

<form method="POST" action="/admin/courses/update/{{ $course->id }}" class="bg-white p-6 rounded shadow">
    @csrf

    <div class="mb-3">
        <label>Course Name</label>
        <input type="text" name="name" value="{{ $course->name }}" class="border p-2 w-full" required>
    </div>

    <div class="mb-3">
        <label>Course Code</label>
        <input type="text" name="code" value="{{ $course->code }}" class="border p-2 w-full">
    </div>

    <div class="mb-4">
<label>Certification Remarks</label>
<textarea name="certification_remarks" rows="4" style="width:100%; padding:10px; margin-bottom:15px;">{{ $course->certification_remarks }}</textarea>
        <label>Status</label>
        <select name="status" class="border p-2 w-full">
            <option value="1" {{ $course->status == 1 ? 'selected' : '' }}>Active</option>
            <option value="0" {{ $course->status == 0 ? 'selected' : '' }}>Inactive</option>
        </select>
    </div>

<div class="mb-4">
    <label>Training Type</label>
    <select name="course_type" class="form-control" required>
        <option value="manual" {{ $course->course_type == 'manual' ? 'selected' : '' }}>
            Manual / Customized Training
        </option>
        <option value="elearning" {{ $course->course_type == 'elearning' ? 'selected' : '' }}>
            Self-Paced eLearning
        </option>
    </select>
</div>

    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">
        Update Course
    </button>

    <a href="/admin/courses" class="bg-gray-500 text-white px-4 py-2 rounded">
        Back
    </a>
</form>

@endsection