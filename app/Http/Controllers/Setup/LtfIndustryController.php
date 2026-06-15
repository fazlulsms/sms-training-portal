<?php

namespace App\Http\Controllers\Setup;

use App\Http\Controllers\Controller;
use App\Models\LtfIndustry;
use Illuminate\Http\Request;

class LtfIndustryController extends Controller
{
    public function index()
    {
        $records = LtfIndustry::withCount('courses')
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();
        return view('admin.setup.industries.index', compact('records'));
    }

    public function create()
    {
        return view('admin.setup.industries.create');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        LtfIndustry::create($request->only(['name', 'description', 'display_order', 'status']));
        return redirect()->route('setup.industries.index')->with('success', 'Industry created.');
    }

    public function edit(LtfIndustry $industry)
    {
        return view('admin.setup.industries.edit', ['record' => $industry]);
    }

    public function update(Request $request, LtfIndustry $industry)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $industry->update($request->only(['name', 'description', 'display_order', 'status']));
        return redirect()->route('setup.industries.index')->with('success', 'Industry updated.');
    }

    public function toggle(LtfIndustry $industry)
    {
        $industry->status = $industry->status === 'active' ? 'archived' : 'active';
        $industry->save();
        return back()->with('success', 'Status updated.');
    }

    public function destroy(LtfIndustry $industry)
    {
        $industry->delete();
        return redirect()->route('setup.industries.index')->with('success', 'Industry deleted.');
    }
}
