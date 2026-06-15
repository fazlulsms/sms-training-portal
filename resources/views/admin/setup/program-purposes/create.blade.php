@extends('layouts.app')
@section('content')
<div style="max-width:680px; margin:auto;">
    <div style="margin-bottom:20px;">
        <a href="{{ route('setup.program-purposes.index') }}" style="color:#6b7280; font-size:13px; text-decoration:none;">← Program Purposes</a>
    </div>
    <div style="background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,.06); padding:28px;">
        <h2 style="font-size:20px; font-weight:800; color:#111827; margin:0 0 6px;">Add Program Purpose</h2>
        <p style="font-size:13px; color:#6b7280; margin:0 0 24px;">The suggested framework drives auto-selection on the course form when this purpose is chosen.</p>

        @if($errors->any())
        <div style="background:#fee2e2; border:1px solid #fca5a5; border-radius:8px; padding:12px 16px; margin-bottom:18px; color:#b91c1c; font-size:13.5px;">
            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
        </div>
        @endif

        <form method="POST" action="{{ route('setup.program-purposes.store') }}">
            @csrf
            <div style="margin-bottom:16px;">
                <label style="display:block; font-weight:600; font-size:13.5px; color:#374151; margin-bottom:6px;">Name <span style="color:#dc2626;">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       style="width:100%; padding:10px 13px; border:1.5px solid #d1d5db; border-radius:8px; font-size:14px; font-family:inherit; box-sizing:border-box;">
            </div>
            <div style="margin-bottom:16px;">
                <label style="display:block; font-weight:600; font-size:13.5px; color:#374151; margin-bottom:6px;">Description</label>
                <textarea name="description" rows="3"
                          style="width:100%; padding:10px 13px; border:1.5px solid #d1d5db; border-radius:8px; font-size:14px; font-family:inherit; box-sizing:border-box; resize:vertical;">{{ old('description') }}</textarea>
            </div>
            <div style="margin-bottom:16px;">
                <label style="display:block; font-weight:600; font-size:13.5px; color:#374151; margin-bottom:6px;">Suggested Learning Framework</label>
                <select name="suggested_framework_id" style="width:100%; padding:10px 13px; border:1.5px solid #d1d5db; border-radius:8px; font-size:14px; font-family:inherit; box-sizing:border-box;">
                    <option value="">— None —</option>
                    @foreach($frameworks as $id => $name)
                    <option value="{{ $id }}" {{ old('suggested_framework_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
                <div style="font-size:12px; color:#9ca3af; margin-top:4px;">When a course is assigned this purpose, the framework dropdown pre-fills to this value.</div>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:24px;">
                <div>
                    <label style="display:block; font-weight:600; font-size:13.5px; color:#374151; margin-bottom:6px;">Display Order</label>
                    <input type="number" name="display_order" value="{{ old('display_order', 0) }}" min="0"
                           style="width:100%; padding:10px 13px; border:1.5px solid #d1d5db; border-radius:8px; font-size:14px; font-family:inherit; box-sizing:border-box;">
                </div>
                <div>
                    <label style="display:block; font-weight:600; font-size:13.5px; color:#374151; margin-bottom:6px;">Status</label>
                    <select name="status" style="width:100%; padding:10px 13px; border:1.5px solid #d1d5db; border-radius:8px; font-size:14px; font-family:inherit; box-sizing:border-box;">
                        <option value="active" {{ old('status','active')==='active' ? 'selected':'' }}>Active</option>
                        <option value="archived" {{ old('status')==='archived' ? 'selected':'' }}>Archived</option>
                    </select>
                </div>
            </div>
            <div style="display:flex; gap:12px;">
                <button type="submit" style="background:#1e3a8a; color:#fff; padding:10px 24px; border:none; border-radius:8px; font-weight:700; font-size:14px; cursor:pointer;">Save</button>
                <a href="{{ route('setup.program-purposes.index') }}" style="padding:10px 20px; border:1.5px solid #d1d5db; border-radius:8px; font-weight:600; font-size:14px; color:#374151; text-decoration:none;">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
