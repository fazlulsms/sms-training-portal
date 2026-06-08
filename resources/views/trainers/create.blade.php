@extends('layouts.app')

@section('content')

<div class="max-w-6xl mx-auto">
    <h2 class="text-2xl font-bold mb-6">Add Trainer</h2>

    <form method="POST" action="/trainers/store" class="bg-white p-6 rounded shadow">
        @csrf
<div style="margin-bottom: 25px;">
    <button type="submit" style="background:#16a34a; color:white; padding:12px 20px; border:none; border-radius:6px; cursor:pointer; font-size:15px;">
        Save Trainer
    </button>

    <a href="/trainers" style="background:#6b7280; color:white; padding:12px 20px; border-radius:6px; text-decoration:none; margin-left:10px; font-size:15px;">
        Back
    </a>
</div>
        <div class="mb-4">
            <label class="block mb-2">Name</label>
            <input type="text" name="name" class="border p-3 w-full rounded" required>
        </div>

        <div class="mb-4">
            <label class="block mb-2">Designation</label>
            <input type="text" name="designation" class="border p-3 w-full rounded">
        </div>

        <div class="mb-4">
            <label class="block mb-2">Organization</label>
            <input type="text" name="organization" class="border p-3 w-full rounded">
        </div>

        <div class="mb-4">
            <label class="block mb-2">Email</label>
            <input type="email" name="email" class="border p-3 w-full rounded">
        </div>

        <div class="mb-4">
            <label class="block mb-2">Phone</label>
            <input type="text" name="phone" class="border p-3 w-full rounded">
        </div>

        <div class="mb-4">
            <label class="block mb-2">Qualification</label>
            <textarea name="qualification" rows="4" class="border p-3 w-full rounded"></textarea>
        </div>

        <div class="mb-6">
            <label class="block mb-2">Status</label>
            <select name="status" class="border p-3 w-full rounded">
                <option value="1">Active</option>
                <option value="0">Inactive</option>
            </select>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="bg-green-600 text-white px-6 py-3 rounded">
                Save Trainer
            </button>

            <a href="/trainers" class="bg-gray-600 text-white px-6 py-3 rounded">
                Back
            </a>
        </div>

    </form>
</div>

@endsection