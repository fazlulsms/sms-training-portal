@extends('layouts.app')

@section('content')

<h2 class="text-2xl font-bold mb-4">Add Course</h2>

<form method="POST" action="/admin/courses/store" class="bg-white p-6 rounded shadow">
    @csrf

    <div class="mb-3">
        <label>Course Name</label>
        <input type="text" name="name" class="border p-2 w-full" required>
    </div>

    <div class="mb-3">
        <label>Course Code</label>
        <input type="text" name="code" class="border p-2 w-full">
    </div>
    
<div class="mb-4">
<label>Certification Remarks</label>
<textarea name="certification_remarks" rows="4" style="width:100%; padding:10px; margin-bottom:15px;"></textarea>
        <label>Status</label>
        <select name="status" class="border p-2 w-full">
            <option value="1">Active</option>
            <option value="0">Inactive</option>
        </select>
    </div>

<div class="mb-4">
    <label>Training Type</label>
    <select name="course_type" class="form-control" required>
        <option value="manual" selected>Manual / Customized Training</option>
        <option value="elearning">Self-Paced eLearning</option>
    </select>
</div>

    <!-- ✅ IMPORTANT BUTTON -->
    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">
        Save Course
    </button>

</form>

@endsection