@extends('layouts.app')
@section('page-title', 'Add Knowledge Resource')

@section('content')
<div class="page-wrap">
    <x-page-header title="Add Knowledge Resource" desc="Upload an approved source, working draft, or reference for the SMS knowledge library." />

    <form method="POST" action="{{ route('knowledge-hub.store') }}" enctype="multipart/form-data">
        @csrf
        @include('knowledge-hub._form')
    </form>
</div>
@endsection
