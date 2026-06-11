@extends('layouts.app')
@section('page-title', 'Create Lesson — ' . $course->name)

@section('content')
<x-flash-message />

<x-page-header
    title="Create New Lesson"
    desc="{{ $course->name }}"
>
    <x-slot name="actions">
        <a href="{{ route('elearning.lessons.index', $course) }}" class="btn btn-ghost btn-sm">← Back to Lessons</a>
    </x-slot>
</x-page-header>

<div style="max-width:760px;">
    <div class="card card-flush" style="border-radius:var(--r-xl); box-shadow:var(--shadow-md);">

        {{-- Premium header --}}
        <div style="background:linear-gradient(135deg,var(--sms-primary) 0%,#2563eb 100%); padding:20px 26px; border-radius:var(--r-xl) var(--r-xl) 0 0;">
            <div style="display:flex; align-items:center; gap:14px;">
                <div style="width:44px; height:44px; background:rgba(255,255,255,0.15); border-radius:11px; border:1px solid rgba(255,255,255,0.2); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.2"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2"/></svg>
                </div>
                <div>
                    <div style="font-size:15px; font-weight:800; color:white; line-height:1.2;">New Lesson</div>
                    <div style="font-size:12px; color:rgba(255,255,255,0.65); margin-top:2px;">{{ $course->name }}</div>
                </div>
            </div>
        </div>

        {{-- Form body --}}
        <div class="card-body" style="padding:28px;">
            <form action="{{ route('elearning.lessons.store', $course) }}" method="POST">
                @csrf
                @include('elearning.lessons.form', ['lesson' => null])

                <div style="display:flex; gap:10px; margin-top:24px; padding-top:20px; border-top:1px solid var(--border);">
                    <button type="submit" class="btn btn-primary">
                        Create Lesson &amp; Open Builder →
                    </button>
                    <a href="{{ route('elearning.lessons.index', $course) }}" class="btn btn-ghost">Cancel</a>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection
