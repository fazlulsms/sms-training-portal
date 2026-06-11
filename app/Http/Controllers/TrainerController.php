<?php

namespace App\Http\Controllers;

use App\Models\Trainer;
use App\Models\User;
use Illuminate\Http\Request;

class TrainerController extends Controller
{
    public function index()
    {
        $trainers = Trainer::with('user')->get();
        return view('trainers.index', compact('trainers'));
    }

    public function create()
    {
        return view('trainers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $request->only([
            'name', 'designation', 'organization', 'email', 'phone',
            'qualification', 'short_bio', 'expertise_areas', 'certifications',
            'experience', 'display_order', 'status',
        ]);
        $data['is_public'] = $request->boolean('is_public');

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('trainers', 'public');
        }

        Trainer::create($data);

        return redirect('/trainers')->with('success', 'Trainer added successfully.');
    }

    public function edit($id)
    {
        $trainer = Trainer::findOrFail($id);
        $users   = User::orderBy('name')->get();

        return view('trainers.edit', compact('trainer', 'users'));
    }

    public function update(Request $request, $id)
    {
        $trainer = Trainer::findOrFail($id);

        $request->validate([
            'name'  => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $request->only([
            'name', 'designation', 'organization', 'email', 'phone',
            'qualification', 'short_bio', 'expertise_areas', 'certifications',
            'experience', 'display_order', 'status', 'user_id',
        ]);
        $data['is_public'] = $request->boolean('is_public');

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('trainers', 'public');
        }

        $newUserId = $request->input('user_id') ?: null;

        if ($newUserId && $newUserId == auth()->id()) {
            return redirect('/trainers')
                ->with('error', 'You cannot link your own account to a trainer profile. Create a separate user account for the trainer.');
        }

        if ($trainer->getOriginal('user_id') && $trainer->getOriginal('user_id') != $newUserId) {
            User::where('id', $trainer->getOriginal('user_id'))->update(['role' => 'admin']);
        }

        if ($newUserId) {
            User::where('id', $newUserId)->update(['role' => 'trainer']);
        }

        $trainer->update($data);

        return redirect('/trainers')->with('success', 'Trainer updated successfully.');
    }

    public function delete($id)
    {
        $trainer = Trainer::findOrFail($id);

        if ($trainer->user_id) {
            User::where('id', $trainer->user_id)->update(['role' => 'admin']);
        }

        $trainer->delete();

        return redirect('/trainers')->with('success', 'Trainer deleted successfully.');
    }
}
