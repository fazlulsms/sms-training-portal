@extends('layouts.app')

@section('title', isset($post) ? 'Edit Post' : 'New Blog Post')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ isset($post) ? 'Edit Post' : 'New Blog Post' }}</h1>
        <p class="page-subtitle">{{ isset($post) ? 'Update article content and settings' : 'Create a new blog article' }}</p>
    </div>
    <a href="{{ route('admin.blog.index') }}" class="btn btn-secondary">← Back to Posts</a>
</div>

<form method="POST" action="{{ isset($post) ? route('admin.blog.update', $post) : route('admin.blog.store') }}" enctype="multipart/form-data">
    @csrf
    @if(isset($post)) @method('PUT') @endif

    <div style="display:grid;grid-template-columns:1fr 320px;gap:24px;align-items:start;">

        {{-- Main content --}}
        <div>
            <div class="card" style="margin-bottom:20px;">
                <div class="card-header"><h3 class="card-title">Post Content</h3></div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Title *</label>
                        <input type="text" name="title" class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}"
                               value="{{ old('title', $post->title ?? '') }}" required
                               oninput="generateSlug(this.value)">
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Slug (URL)</label>
                        <input type="text" name="slug" id="slugField" class="form-control"
                               value="{{ old('slug', $post->slug ?? '') }}" placeholder="auto-generated">
                        <small class="text-muted">Will be: /blog/<span id="slugPreview">{{ $post->slug ?? '' }}</span></small>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Excerpt / Summary</label>
                        <textarea name="excerpt" class="form-control" rows="3"
                                  placeholder="Brief summary shown in listings (recommended 150 chars)">{{ old('excerpt', $post->excerpt ?? '') }}</textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Content *</label>
                        <textarea name="content" id="postContent" class="form-control {{ $errors->has('content') ? 'is-invalid' : '' }}"
                                  rows="20" required>{{ old('content', $post->content ?? '') }}</textarea>
                        @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <small class="text-muted">HTML is supported. Use headings, lists, blockquotes.</small>
                    </div>
                </div>
            </div>

            {{-- SEO --}}
            <div class="card">
                <div class="card-header"><h3 class="card-title">SEO Settings</h3></div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">SEO Title <small style="font-weight:500;color:#9ca3af;">(max 60 chars)</small></label>
                        <input type="text" name="seo_title" class="form-control" maxlength="70"
                               value="{{ old('seo_title', $post->seo_title ?? '') }}"
                               placeholder="Leave blank to use post title">
                    </div>
                    <div class="form-group">
                        <label class="form-label">SEO Description <small style="font-weight:500;color:#9ca3af;">(max 160 chars)</small></label>
                        <textarea name="seo_description" class="form-control" rows="3" maxlength="170"
                                  placeholder="Leave blank to use excerpt">{{ old('seo_description', $post->seo_description ?? '') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar settings --}}
        <div>
            {{-- Publish --}}
            <div class="card" style="margin-bottom:18px;">
                <div class="card-header"><h3 class="card-title">Publish Settings</h3></div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Status *</label>
                        <select name="status" class="form-control">
                            @foreach(['draft','published','archived'] as $s)
                            <option value="{{ $s }}" {{ old('status', $post->status ?? 'draft') === $s ? 'selected' : '' }}>
                                {{ ucfirst($s) }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Publish Date</label>
                        <input type="datetime-local" name="published_at" class="form-control"
                               value="{{ old('published_at', isset($post->published_at) ? $post->published_at?->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Author</label>
                        <input type="text" name="author" class="form-control"
                               value="{{ old('author', $post->author ?? auth()->user()->name) }}">
                    </div>
                </div>
                <div class="card-footer" style="display:flex;gap:10px;">
                    <button type="submit" class="btn btn-primary" style="flex:1;">
                        {{ isset($post) ? 'Update Post' : 'Publish Post' }}
                    </button>
                </div>
            </div>

            {{-- Category & Course --}}
            <div class="card" style="margin-bottom:18px;">
                <div class="card-header"><h3 class="card-title">Classification</h3></div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Category</label>
                        <select name="blog_category_id" class="form-control">
                            <option value="">No category</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('blog_category_id', $post->blog_category_id ?? '') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                            @endforeach
                        </select>
                        <small><a href="{{ route('admin.blog.categories') }}" style="font-size:12px;color:#1e3a8a;">+ Add category</a></small>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Related Course</label>
                        <select name="course_id" class="form-control">
                            <option value="">None</option>
                            @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ old('course_id', $post->course_id ?? '') == $course->id ? 'selected' : '' }}>
                                {{ $course->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Featured image --}}
            <div class="card">
                <div class="card-header"><h3 class="card-title">Featured Image</h3></div>
                <div class="card-body">
                    @if(isset($post) && $post->featured_image)
                    <div style="margin-bottom:12px;">
                        <img src="{{ asset('storage/'.$post->featured_image) }}" alt="Current"
                             style="width:100%;border-radius:8px;max-height:160px;object-fit:cover;">
                    </div>
                    @endif
                    <input type="file" name="featured_image" class="form-control" accept="image/*"
                           style="font-size:13.5px;padding:8px;">
                    <small class="text-muted">Recommended: 1200×630px, max 2MB</small>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
function generateSlug(title) {
    const slug = title.toLowerCase()
        .replace(/[^\w\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .trim();
    document.getElementById('slugField').value = slug;
    document.getElementById('slugPreview').textContent = slug;
}
// Update preview on slug manual change
document.getElementById('slugField')?.addEventListener('input', function() {
    document.getElementById('slugPreview').textContent = this.value;
});
</script>
@endsection
