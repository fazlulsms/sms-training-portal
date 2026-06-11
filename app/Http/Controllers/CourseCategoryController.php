<?php

namespace App\Http\Controllers;

use App\Models\CourseCategory;
use Illuminate\Http\Request;

class CourseCategoryController extends Controller
{
    public function index()
    {
        $categories = CourseCategory::withCount('courses')->orderBy('display_order')->orderBy('name')->get();
        return view('course-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('course-categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'slug'  => 'nullable|string|max:255|unique:course_categories,slug',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $request->only(['name', 'slug', 'description', 'icon', 'display_order', 'status']);
        $data['is_public'] = $request->boolean('is_public');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        CourseCategory::create($data);

        return redirect('/admin/course-categories')->with('success', 'Category created.');
    }

    public function edit($id)
    {
        $category = CourseCategory::findOrFail($id);
        return view('course-categories.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $category = CourseCategory::findOrFail($id);

        $request->validate([
            'name'  => 'required|string|max:255',
            'slug'  => 'nullable|string|max:255|unique:course_categories,slug,' . $id,
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $request->only(['name', 'slug', 'description', 'icon', 'display_order', 'status']);
        $data['is_public'] = $request->boolean('is_public');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($data);

        return redirect('/admin/course-categories')->with('success', 'Category updated.');
    }

    public function destroy($id)
    {
        CourseCategory::findOrFail($id)->delete();
        return redirect('/admin/course-categories')->with('success', 'Category deleted.');
    }
}
