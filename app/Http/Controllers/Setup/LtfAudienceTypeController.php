<?php

namespace App\Http\Controllers\Setup;

use App\Http\Controllers\Controller;
use App\Models\LtfAudienceType;
use Illuminate\Http\Request;

class LtfAudienceTypeController extends Controller
{
    public function index()
    {
        $records = LtfAudienceType::withCount('courses')
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();
        return view('admin.setup.audiences.index', compact('records'));
    }

    public function create()
    {
        return view('admin.setup.audiences.create');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        LtfAudienceType::create($request->only(['name', 'description', 'display_order', 'status']));
        return redirect()->route('setup.audiences.index')->with('success', 'Audience type created.');
    }

    public function edit(LtfAudienceType $audienceType)
    {
        return view('admin.setup.audiences.edit', ['record' => $audienceType]);
    }

    public function update(Request $request, LtfAudienceType $audienceType)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $audienceType->update($request->only(['name', 'description', 'display_order', 'status']));
        return redirect()->route('setup.audiences.index')->with('success', 'Audience type updated.');
    }

    public function toggle(LtfAudienceType $audienceType)
    {
        $audienceType->status = $audienceType->status === 'active' ? 'archived' : 'active';
        $audienceType->save();
        return back()->with('success', 'Status updated.');
    }

    public function destroy(LtfAudienceType $audienceType)
    {
        $audienceType->delete();
        return redirect()->route('setup.audiences.index')->with('success', 'Audience type deleted.');
    }
}
