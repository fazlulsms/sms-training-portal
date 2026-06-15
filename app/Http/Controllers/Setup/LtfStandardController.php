<?php

namespace App\Http\Controllers\Setup;

use App\Http\Controllers\Controller;
use App\Models\LtfStandard;
use Illuminate\Http\Request;

class LtfStandardController extends Controller
{
    public function index()
    {
        $records = LtfStandard::withCount('courses')
            ->orderBy('domain')
            ->orderBy('display_order')
            ->orderBy('name')
            ->get()
            ->groupBy('domain');
        return view('admin.setup.standards.index', [
            'records' => $records,
            'domains' => LtfStandard::DOMAINS,
        ]);
    }

    public function create()
    {
        return view('admin.setup.standards.create', ['domains' => LtfStandard::DOMAINS]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'   => 'required|string|max:255',
            'domain' => 'required|in:' . implode(',', array_keys(LtfStandard::DOMAINS)),
        ]);
        LtfStandard::create($request->only([
            'domain', 'name', 'full_name', 'version', 'description', 'display_order', 'status',
        ]));
        return redirect()->route('setup.standards.index')->with('success', 'Standard created.');
    }

    public function edit(LtfStandard $standard)
    {
        return view('admin.setup.standards.edit', [
            'record'  => $standard,
            'domains' => LtfStandard::DOMAINS,
        ]);
    }

    public function update(Request $request, LtfStandard $standard)
    {
        $request->validate([
            'name'   => 'required|string|max:255',
            'domain' => 'required|in:' . implode(',', array_keys(LtfStandard::DOMAINS)),
        ]);
        $standard->update($request->only([
            'domain', 'name', 'full_name', 'version', 'description', 'display_order', 'status',
        ]));
        return redirect()->route('setup.standards.index')->with('success', 'Standard updated.');
    }

    public function toggle(LtfStandard $standard)
    {
        $standard->status = $standard->status === 'active' ? 'archived' : 'active';
        $standard->save();
        return back()->with('success', 'Status updated.');
    }

    public function destroy(LtfStandard $standard)
    {
        $standard->delete();
        return redirect()->route('setup.standards.index')->with('success', 'Standard deleted.');
    }
}
