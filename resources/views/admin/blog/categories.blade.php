@extends('layouts.app')

@section('title', 'Blog Categories')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Blog Categories</h1>
        <p class="page-subtitle">Manage blog post categories</p>
    </div>
    <a href="{{ route('admin.blog.index') }}" class="btn btn-secondary">← Back to Posts</a>
</div>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<div style="display:grid;grid-template-columns:1fr 360px;gap:24px;align-items:start;">

    {{-- List --}}
    <div class="card">
        <div class="card-body" style="padding:0;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Slug</th>
                        <th>Colour</th>
                        <th>Posts</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $cat)
                    <tr>
                        <td style="font-weight:700;">{{ $cat->name }}</td>
                        <td style="font-size:13px;color:#9ca3af;">/blog?category={{ $cat->slug }}</td>
                        <td>
                            <span style="display:inline-block;width:18px;height:18px;border-radius:50%;background:{{ $cat->color }};vertical-align:middle;margin-right:6px;border:1px solid rgba(0,0,0,.1);"></span>
                            {{ $cat->color }}
                        </td>
                        <td style="text-align:center;">{{ $cat->posts_count }}</td>
                        <td>
                            <div style="display:flex;gap:8px;">
                                <a href="{{ route('admin.blog.categories') }}?edit={{ $cat->id }}" class="btn btn-sm btn-secondary">Edit</a>
                                <form method="POST" action="{{ route('admin.blog.categories.destroy', $cat) }}"
                                      onsubmit="return confirm('Delete category?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" style="text-align:center;padding:36px;color:#9ca3af;">No categories yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Add / Edit form --}}
    @php $editCat = request('edit') ? $categories->find(request('edit')) : null; @endphp
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ $editCat ? 'Edit Category' : 'Add Category' }}</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ $editCat ? route('admin.blog.categories.update', $editCat) : route('admin.blog.categories.store') }}">
                @csrf
                @if($editCat) @method('PUT') @endif
                <div class="form-group">
                    <label class="form-label">Name *</label>
                    <input type="text" name="name" class="form-control" required
                           value="{{ old('name', $editCat->name ?? '') }}" placeholder="e.g. Safety Tips">
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Slug</label>
                    <input type="text" name="slug" class="form-control"
                           value="{{ old('slug', $editCat->slug ?? '') }}" placeholder="auto-generated">
                </div>
                <div class="form-group">
                    <label class="form-label">Color</label>
                    <div style="display:flex;gap:10px;align-items:center;">
                        <input type="color" name="color" class="form-control" style="width:56px;height:40px;padding:4px;"
                               value="{{ old('color', $editCat->color ?? '#1e3a8a') }}">
                        <input type="text" id="colorHex" class="form-control"
                               value="{{ old('color', $editCat->color ?? '#1e3a8a') }}" style="flex:1;">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;">
                    {{ $editCat ? 'Update Category' : 'Add Category' }}
                </button>
                @if($editCat)
                <a href="{{ route('admin.blog.categories') }}" class="btn btn-secondary" style="width:100%;margin-top:8px;text-align:center;display:block;">Cancel</a>
                @endif
            </form>
        </div>
    </div>

</div>

<script>
// Sync color picker ↔ hex input
const colorInput = document.querySelector('input[type=color]');
const hexInput   = document.getElementById('colorHex');
if (colorInput && hexInput) {
    colorInput.addEventListener('input', () => hexInput.value = colorInput.value);
    hexInput.addEventListener('input', () => colorInput.value = hexInput.value);
}
</script>
@endsection
