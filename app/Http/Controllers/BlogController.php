<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\BlogCategory;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    // ── Admin CRUD ────────────────────────────────────────────
    public function index()
    {
        $posts = BlogPost::with('category')->latest()->paginate(20);
        return view('admin.blog.index', compact('posts'));
    }

    public function create()
    {
        $categories = BlogCategory::orderBy('name')->get();
        $courses    = Course::where('is_public', true)->orderBy('name')->get();
        return view('admin.blog.form', compact('categories', 'courses'));
    }

    public function store(Request $request)
    {
        $validated = $this->validatePost($request);

        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')->store('blog', 'public');
        }

        if (empty($validated['slug'])) {
            $validated['slug'] = BlogPost::generateSlug($validated['title']);
        }

        if ($validated['status'] === 'published' && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        BlogPost::create($validated);

        return redirect()->route('admin.blog.index')->with('success', 'Blog post created.');
    }

    public function edit(BlogPost $post)
    {
        $categories = BlogCategory::orderBy('name')->get();
        $courses    = Course::where('is_public', true)->orderBy('name')->get();
        return view('admin.blog.form', compact('post', 'categories', 'courses'));
    }

    public function update(Request $request, BlogPost $post)
    {
        $validated = $this->validatePost($request, $post->id);

        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')->store('blog', 'public');
        }

        if ($validated['status'] === 'published' && empty($post->published_at)) {
            $validated['published_at'] = now();
        }

        $post->update($validated);

        return redirect()->route('admin.blog.index')->with('success', 'Blog post updated.');
    }

    public function destroy(BlogPost $post)
    {
        $post->delete();
        return redirect()->route('admin.blog.index')->with('success', 'Post deleted.');
    }

    // ── Blog Categories ───────────────────────────────────────
    public function categories()
    {
        $categories = BlogCategory::withCount('posts')->get();
        return view('admin.blog.categories', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $request->validate(['name' => 'required|string|max:100', 'color' => 'nullable|string|max:20']);
        BlogCategory::create([
            'name'  => $request->name,
            'slug'  => Str::slug($request->name),
            'color' => $request->color ?? '#1e3a8a',
        ]);
        return back()->with('success', 'Category created.');
    }

    public function updateCategory(Request $request, BlogCategory $category)
    {
        $request->validate(['name' => 'required|string|max:100', 'color' => 'nullable|string|max:20']);
        $category->update([
            'name'  => $request->name,
            'slug'  => $request->slug ?: Str::slug($request->name),
            'color' => $request->color ?? '#1e3a8a',
        ]);
        return redirect()->route('admin.blog.categories')->with('success', 'Category updated.');
    }

    public function destroyCategory(BlogCategory $category)
    {
        $category->delete();
        return back()->with('success', 'Category deleted.');
    }

    private function validatePost(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'title'            => 'required|string|max:300',
            'slug'             => "nullable|string|max:320|unique:blog_posts,slug,$ignoreId",
            'blog_category_id' => 'nullable|exists:blog_categories,id',
            'course_id'        => 'nullable|exists:courses,id',
            'featured_image'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
            'excerpt'          => 'nullable|string|max:600',
            'content'          => 'required|string',
            'seo_title'        => 'nullable|string|max:160',
            'seo_description'  => 'nullable|string|max:320',
            'author'           => 'nullable|string|max:80',
            'status'           => 'required|in:draft,published,archived',
            'published_at'     => 'nullable|date',
        ]);
    }
}
