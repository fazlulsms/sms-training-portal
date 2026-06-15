@extends('layouts.app')
@section('content')
<div style="max-width:700px; margin:auto;">

    <div style="display:flex; align-items:center; gap:12px; margin-bottom:24px;">
        <a href="{{ route('setup.course-types.index') }}" style="color:#6b7280; text-decoration:none; font-size:22px; line-height:1;">&#8592;</a>
        <h2 style="font-size:22px; font-weight:800; color:#111827; margin:0;">Add Course Type</h2>
    </div>

    @if($errors->any())
    <div style="background:#fee2e2; border:1px solid #fca5a5; border-radius:8px; padding:12px 16px; margin-bottom:16px; color:#991b1b;">
        @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
    </div>
    @endif

    <div style="background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,.06); padding:28px;">
        <form method="POST" action="{{ route('setup.course-types.store') }}">
            @csrf
            @include('admin.setup.course-types._form', ['record' => null])
            <div style="display:flex; gap:12px; margin-top:24px;">
                <button type="submit" style="background:#1e3a8a; color:#fff; padding:10px 24px; border:none; border-radius:8px; font-weight:700; font-size:14px; cursor:pointer;">
                    Create Course Type
                </button>
                <a href="{{ route('setup.course-types.index') }}" style="padding:10px 20px; border:1px solid #e5e7eb; border-radius:8px; font-weight:600; color:#374151; text-decoration:none; font-size:14px;">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
