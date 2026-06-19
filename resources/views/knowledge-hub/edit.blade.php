@extends('layouts.app')
@section('page-title', 'Edit Knowledge Resource')

@section('content')
<div class="page-wrap">
    <x-page-header title="Edit Knowledge Resource" desc="Update metadata, status, notes, or replace the uploaded source file." />

    <form method="POST" action="{{ route('knowledge-hub.update', $resource) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('knowledge-hub._form')
    </form>
</div>
@endsection
