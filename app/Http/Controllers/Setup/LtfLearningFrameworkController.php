<?php

namespace App\Http\Controllers\Setup;

use App\Http\Controllers\Controller;
use App\Models\LtfLearningFramework;
use Illuminate\Http\Request;

class LtfLearningFrameworkController extends Controller
{
    public function index()
    {
        $records = LtfLearningFramework::withCount('courses')
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();
        return view('admin.setup.frameworks.index', compact('records'));
    }

    public function create()
    {
        return view('admin.setup.frameworks.create');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        LtfLearningFramework::create($request->only([
            'name', 'description', 'ai_block_hint', 'typical_duration_days', 'display_order', 'status',
        ]));
        return redirect()->route('setup.frameworks.index')->with('success', 'Learning framework created.');
    }

    public function edit(LtfLearningFramework $framework)
    {
        return view('admin.setup.frameworks.edit', ['record' => $framework]);
    }

    public function update(Request $request, LtfLearningFramework $framework)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $framework->update($request->only([
            'name', 'description', 'ai_block_hint', 'typical_duration_days', 'display_order', 'status',
        ]));
        return redirect()->route('setup.frameworks.index')->with('success', 'Learning framework updated.');
    }

    public function toggle(LtfLearningFramework $framework)
    {
        $framework->status = $framework->status === 'active' ? 'archived' : 'active';
        $framework->save();
        return back()->with('success', 'Status updated.');
    }

    public function destroy(LtfLearningFramework $framework)
    {
        $framework->delete();
        return redirect()->route('setup.frameworks.index')->with('success', 'Learning framework deleted.');
    }
}
