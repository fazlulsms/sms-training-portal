@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto bg-white shadow rounded-xl p-6">

    <h1 class="text-2xl font-bold mb-6">
        Edit eLearning Course
    </h1>

    <form action="{{ route('elearning.courses.update', $course) }}" method="POST">
        @csrf
        @method('PUT')

        @include('elearning.courses.form')
    </form>

</div>
@endsection