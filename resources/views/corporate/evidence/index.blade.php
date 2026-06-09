@extends('layouts.app')
@section('title', 'Training Evidence')
@section('content')

<div class="page-header">
    <div>
        <div style="font-size:13px;color:#9ca3af;margin-bottom:4px;">
            <a href="{{ route('corporate.sessions.show', $session) }}" style="color:#6b7280;text-decoration:none;">{{ $session->course_name }}</a>
            / Evidence
        </div>
        <h1 class="page-title">Training Evidence</h1>
        <p class="page-subtitle">{{ $session->project->company_name }} — {{ $evidences->count() }} file(s)</p>
    </div>
</div>

@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

{{-- Upload form --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-header"><h3 class="card-title">Upload Evidence</h3></div>
    <div class="card-body">
        <form method="POST" action="{{ route('corporate.sessions.evidence.store', $session) }}" enctype="multipart/form-data">
            @csrf
            <div style="display:grid;grid-template-columns:1fr 200px auto;gap:14px;align-items:flex-end;flex-wrap:wrap;">
                <div>
                    <label class="form-label">Files (images, PDF, PPT — multiple allowed)</label>
                    <input type="file" name="files[]" multiple accept="image/*,.pdf,.ppt,.pptx,.doc,.docx" class="form-control" style="padding:6px;" required>
                </div>
                <div>
                    <label class="form-label">Type</label>
                    <select name="type" class="form-control">
                        @foreach(['Training Photo','Group Photo','Presentation','Document','Other'] as $t)
                        <option value="{{ $t }}">{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Evidence by type --}}
@php
    $byType = $evidences->groupBy('type');
    $typeIcons = ['Training Photo'=>'📷','Group Photo'=>'👥','Presentation'=>'📊','Document'=>'📄','Other'=>'📎'];
    $imgExts = ['jpg','jpeg','png','gif','webp'];
@endphp

@if($evidences->isEmpty())
<div class="card"><div class="card-body" style="text-align:center;padding:48px;color:#9ca3af;">No evidence uploaded yet.</div></div>
@else
@foreach($byType as $type => $items)
<div class="card" style="margin-bottom:18px;">
    <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;">
        <h3 class="card-title">{{ $typeIcons[$type] ?? '📎' }} {{ $type }} ({{ $items->count() }})</h3>
    </div>
    <div class="card-body">
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:12px;">
            @foreach($items as $ev)
            @php
                $ext = strtolower(pathinfo($ev->original_name ?? $ev->file_path, PATHINFO_EXTENSION));
                $isImg = in_array($ext, $imgExts);
            @endphp
            <div style="background:#f9fafb;border:1px solid #e9ecf0;border-radius:10px;overflow:hidden;position:relative;">
                @if($isImg)
                <a href="{{ asset('storage/'.$ev->file_path) }}" target="_blank">
                    <img src="{{ asset('storage/'.$ev->file_path) }}" alt="{{ $ev->original_name }}"
                         style="width:100%;height:110px;object-fit:cover;display:block;">
                </a>
                @else
                <a href="{{ asset('storage/'.$ev->file_path) }}" target="_blank"
                   style="display:flex;align-items:center;justify-content:center;height:110px;font-size:40px;text-decoration:none;background:#f0f4ff;">
                    {{ in_array($ext, ['pdf']) ? '📄' : (in_array($ext, ['pptx','ppt']) ? '📊' : '📝') }}
                </a>
                @endif
                <div style="padding:8px 10px;font-size:12px;color:#374151;line-height:1.4;">
                    <div style="font-weight:700;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $ev->original_name }}">
                        {{ $ev->original_name ?? basename($ev->file_path) }}
                    </div>
                    <div style="color:#9ca3af;margin-top:2px;">{{ $ev->created_at->format('d M Y') }}</div>
                </div>
                <form method="POST" action="{{ route('corporate.sessions.evidence.destroy', [$session, $ev]) }}"
                      onsubmit="return confirm('Delete this file?')"
                      style="position:absolute;top:6px;right:6px;">
                    @csrf @method('DELETE')
                    <button type="submit" style="background:#ef4444;color:#fff;border:none;border-radius:6px;width:24px;height:24px;font-size:13px;cursor:pointer;line-height:1;">✕</button>
                </form>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endforeach
@endif
@endsection
