{{-- Usage: <x-page-header title="Trainers" desc="Manage all trainers" icon="users">
       <x-slot:actions><a href="..." class="btn btn-primary btn-sm">+ Add</a></x-slot:actions>
   </x-page-header> --}}
@props(['title', 'desc' => null, 'icon' => null])
<div class="page-header">
    <div class="page-header-top">
        <div>
            <h1 class="page-title">{{ $title }}</h1>
            @if($desc)
                <p class="page-desc">{{ $desc }}</p>
            @endif
        </div>
        @if(isset($actions))
            <div class="page-actions">{{ $actions }}</div>
        @endif
    </div>
    @if(isset($stats))
        {{ $stats }}
    @endif
</div>
