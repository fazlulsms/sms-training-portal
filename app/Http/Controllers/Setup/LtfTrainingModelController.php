<?php

namespace App\Http\Controllers\Setup;

use App\Http\Controllers\Controller;
use App\Models\LtfTrainingModel;
use Illuminate\Http\Request;

class LtfTrainingModelController extends Controller
{
    public function index()
    {
        $records = LtfTrainingModel::withCount('courses')
            ->orderBy('display_order')->orderBy('name')->get();
        return view('admin.setup.training-models.index', compact('records'));
    }

    public function create()
    {
        return view('admin.setup.training-models.create');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        LtfTrainingModel::create($request->only(['name', 'description', 'display_order', 'status']));
        return redirect()->route('setup.training-models.index')->with('success', 'Training model created.');
    }

    public function edit(LtfTrainingModel $trainingModel)
    {
        return view('admin.setup.training-models.edit', ['record' => $trainingModel]);
    }

    public function update(Request $request, LtfTrainingModel $trainingModel)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $trainingModel->update($request->only(['name', 'description', 'display_order', 'status']));
        return redirect()->route('setup.training-models.index')->with('success', 'Training model updated.');
    }

    public function toggle(LtfTrainingModel $trainingModel)
    {
        $trainingModel->status = $trainingModel->status === 'active' ? 'archived' : 'active';
        $trainingModel->save();
        return back()->with('success', 'Status updated.');
    }

    public function destroy(LtfTrainingModel $trainingModel)
    {
        $trainingModel->delete();
        return redirect()->route('setup.training-models.index')->with('success', 'Training model deleted.');
    }
}
