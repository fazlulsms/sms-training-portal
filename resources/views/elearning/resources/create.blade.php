@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto bg-white shadow rounded-xl p-6">

    <h1 class="text-2xl font-bold mb-2">Add Lesson Resource</h1>
    <p class="text-gray-500 mb-6">{{ $lesson->title }}</p>

    @if ($errors->any())
        <div class="bg-red-100 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc ml-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('elearning.resources.store', [$course, $lesson]) }}"
          method="POST"
          enctype="multipart/form-data">
        @csrf

        <div class="space-y-4">

            <div>
                <label class="block text-sm font-medium mb-1">Resource Title</label>
                <input type="text"
                       name="title"
                       class="w-full border rounded-lg px-4 py-2"
                       required>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Resource Type</label>
                <select name="resource_type" class="w-full border rounded-lg px-4 py-2">
                    <option value="file">Upload File</option>
                    <option value="link">External Link</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Upload File</label>
                <input type="file"
                       name="resource_file"
                       class="w-full border rounded-lg px-4 py-2">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">External URL</label>
                <input type="url"
                       name="external_url"
                       class="w-full border rounded-lg px-4 py-2"
                       placeholder="https://">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Status</label>
                <select name="status" class="w-full border rounded-lg px-4 py-2">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>

            <div class="flex gap-3">
                <button type="submit"
                        class="bg-blue-600 text-white px-6 py-2 rounded-lg">
                    Save Resource
                </button>

                <a href="{{ route('elearning.resources.index', [$course, $lesson]) }}"
                   class="bg-gray-200 text-gray-800 px-6 py-2 rounded-lg">
                    Back
                </a>
            </div>

        </div>
    </form>

</div>
@endsection