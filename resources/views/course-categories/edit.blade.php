@extends('layouts.app')
@section('content')
<style>
.fg { margin-bottom:18px; }
.fg label { display:block; font-weight:600; font-size:13.5px; color:#374151; margin-bottom:6px; }
.fg input,.fg select,.fg textarea { width:100%; padding:10px 13px; border:1.5px solid #d1d5db; border-radius:8px; font-size:14px; font-family:inherit; outline:none; box-sizing:border-box; }
.fg input:focus,.fg select:focus,.fg textarea:focus { border-color:#1e3a8a; }
.frow { display:grid; grid-template-columns:1fr 1fr; gap:18px; }
.toggle-row { display:flex; align-items:center; gap:12px; padding:13px 0; }
.tl { font-weight:600; font-size:14px; color:#374151; flex:1; }
.toggle-switch { position:relative; display:inline-block; width:44px; height:24px; }
.toggle-switch input { opacity:0; width:0; height:0; }
.slider { position:absolute; inset:0; background:#d1d5db; border-radius:24px; cursor:pointer; transition:.25s; }
.slider:before { content:''; position:absolute; left:3px; bottom:3px; width:18px; height:18px; background:#fff; border-radius:50%; transition:.25s; }
.toggle-switch input:checked + .slider { background:#1e3a8a; }
.toggle-switch input:checked + .slider:before { transform:translateX(20px); }
</style>
<div style="max-width:700px; margin:auto;">
<div style="background:#fff; padding:28px; border-radius:14px; box-shadow:0 4px 16px rgba(0,0,0,.07);">
    <h2 style="font-size:22px; font-weight:800; color:#111827; margin-bottom:24px;">Edit Category: {{ $category->name }}</h2>
    @if($errors->any())
    <div style="background:#fee2e2; border:1px solid #fca5a5; border-radius:8px; padding:14px; margin-bottom:20px;">
        <ul style="margin:0; padding-left:18px; color:#b91c1c; font-size:13.5px;">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif
    <form method="POST" action="/admin/course-categories/update/{{ $category->id }}" enctype="multipart/form-data">
        @csrf
        <div class="frow">
            <div class="fg">
                <label>Name <span style="color:red">*</span></label>
                <input type="text" name="name" value="{{ old('name', $category->name) }}" required>
            </div>
            <div class="fg">
                <label>Slug</label>
                <input type="text" name="slug" value="{{ old('slug', $category->slug) }}">
            </div>
        </div>
        <div class="fg">
            <label>Description</label>
            <textarea name="description" rows="3">{{ old('description', $category->description) }}</textarea>
        </div>
        <div class="frow">
            <div class="fg">
                <label>Icon (emoji or icon class)</label>
                <input type="text" name="icon" value="{{ old('icon', $category->icon) }}">
            </div>
            <div class="fg">
                <label>Display Order</label>
                <input type="number" name="display_order" value="{{ old('display_order', $category->display_order) }}" min="0">
            </div>
        </div>
        <div class="frow">
            <div class="fg">
                <label>Status</label>
                <select name="status">
                    <option value="active" {{ ($category->status=='active')?'selected':'' }}>Active</option>
                    <option value="inactive" {{ ($category->status=='inactive')?'selected':'' }}>Inactive</option>
                </select>
            </div>
            <div class="fg">
                <label>Category Image (leave blank to keep existing)</label>
                @if($category->image)
                <div style="margin-bottom:8px;"><img src="{{ asset('storage/'.$category->image) }}" style="height:50px; border-radius:6px;"></div>
                @endif
                <input type="file" name="image" accept="image/*" style="padding:6px;">
            </div>
        </div>
        <div style="background:#f8fafc; border-radius:10px; padding:14px 20px; margin-bottom:20px;">
            <div class="toggle-row">
                <span class="tl">Show on Public Website</span>
                <label class="toggle-switch">
                    <input type="checkbox" name="is_public" value="1" {{ old('is_public', $category->is_public) ? 'checked':'' }}>
                    <span class="slider"></span>
                </label>
            </div>
        </div>
        <div style="display:flex; gap:12px;">
            <button type="submit" style="background:#1e3a8a; color:#fff; padding:12px 28px; border:none; border-radius:8px; font-weight:700; font-size:15px; cursor:pointer;">
                Update Category
            </button>
            <a href="/admin/course-categories" style="background:#6b7280; color:#fff; padding:12px 20px; border-radius:8px; text-decoration:none; font-weight:600; font-size:15px;">
                Back
            </a>
        </div>
    </form>
</div>
</div>
@endsection
