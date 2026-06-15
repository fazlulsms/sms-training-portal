<?php

namespace App\Http\Controllers\Setup;

use App\Http\Controllers\Controller;
use App\Models\LtfCourseType;
use Illuminate\Http\Request;

class LtfCourseTypeController extends Controller
{
    public function index()
    {
        $records = LtfCourseType::withCount('courses')
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();
        return view('admin.setup.course-types.index', compact('records'));
    }

    public function create()
    {
        return view('admin.setup.course-types.create', ['groups' => LtfCourseType::GROUPS]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'group' => 'required|in:' . implode(',', array_keys(LtfCourseType::GROUPS)),
        ]);
        LtfCourseType::create($request->only(['name', 'group', 'description', 'display_order', 'status']));
        return redirect()->route('setup.course-types.index')->with('success', 'Course type created.');
    }

    public function edit(LtfCourseType $ltfCourseType)
    {
        return view('admin.setup.course-types.edit', [
            'record' => $ltfCourseType,
            'groups' => LtfCourseType::GROUPS,
        ]);
    }

    public function update(Request $request, LtfCourseType $ltfCourseType)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'group' => 'required|in:' . implode(',', array_keys(LtfCourseType::GROUPS)),
        ]);
        $ltfCourseType->update($request->only(['name', 'group', 'description', 'display_order', 'status']));
        return redirect()->route('setup.course-types.index')->with('success', 'Course type updated.');
    }

    public function toggle(LtfCourseType $ltfCourseType)
    {
        $ltfCourseType->status = $ltfCourseType->status === 'active' ? 'archived' : 'active';
        $ltfCourseType->save();
        return back()->with('success', 'Status updated.');
    }

    public function destroy(LtfCourseType $ltfCourseType)
    {
        $ltfCourseType->delete();
        return redirect()->route('setup.course-types.index')->with('success', 'Course type deleted.');
    }
}
