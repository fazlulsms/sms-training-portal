<?php

namespace App\Http\Controllers\Setup;

use App\Http\Controllers\Controller;
use App\Models\LtfLearningFramework;
use App\Models\LtfProgramPurpose;
use Illuminate\Http\Request;

class LtfProgramPurposeController extends Controller
{
    public function index()
    {
        $records = LtfProgramPurpose::with('suggestedFramework')
            ->withCount('courses')
            ->orderBy('display_order')->orderBy('name')
            ->get();
        return view('admin.setup.program-purposes.index', compact('records'));
    }

    public function create()
    {
        $frameworks = LtfLearningFramework::forSelect();
        return view('admin.setup.program-purposes.create', compact('frameworks'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        LtfProgramPurpose::create($request->only([
            'name', 'description', 'suggested_framework_id', 'display_order', 'status',
        ]));
        return redirect()->route('setup.program-purposes.index')->with('success', 'Program purpose created.');
    }

    public function edit(LtfProgramPurpose $programPurpose)
    {
        $frameworks = LtfLearningFramework::forSelect();
        return view('admin.setup.program-purposes.edit', [
            'record'     => $programPurpose,
            'frameworks' => $frameworks,
        ]);
    }

    public function update(Request $request, LtfProgramPurpose $programPurpose)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $programPurpose->update($request->only([
            'name', 'description', 'suggested_framework_id', 'display_order', 'status',
        ]));
        return redirect()->route('setup.program-purposes.index')->with('success', 'Program purpose updated.');
    }

    public function toggle(LtfProgramPurpose $programPurpose)
    {
        $programPurpose->status = $programPurpose->status === 'active' ? 'archived' : 'active';
        $programPurpose->save();
        return back()->with('success', 'Status updated.');
    }

    public function destroy(LtfProgramPurpose $programPurpose)
    {
        $programPurpose->delete();
        return redirect()->route('setup.program-purposes.index')->with('success', 'Program purpose deleted.');
    }
}
