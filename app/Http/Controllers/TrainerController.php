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
        Trainer::create($request->only([
            'name', 'designation', 'organization', 'email', 'phone', 'qualification', 'status',
        ]));

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

        $trainer->update($request->only([
            'name', 'designation', 'organization', 'email', 'phone', 'qualification', 'status', 'user_id',
        ]));

        // Guard: never change the currently logged-in user's own role.
        $newUserId = $request->input('user_id') ?: null;

        if ($newUserId && $newUserId == auth()->id()) {
            return redirect('/trainers')
                ->with('error', 'You cannot link your own account to a trainer profile. Create a separate user account for the trainer.');
        }

        // Revert role on previously linked user if changed
        if ($trainer->getOriginal('user_id') && $trainer->getOriginal('user_id') != $newUserId) {
            User::where('id', $trainer->getOriginal('user_id'))->update(['role' => 'admin']);
        }

        if ($newUserId) {
            User::where('id', $newUserId)->update(['role' => 'trainer']);
        }

        return redirect('/trainers')->with('success', 'Trainer updated successfully.');
    }

    public function delete($id)
    {
        $trainer = Trainer::findOrFail($id);

        // Revert linked user's role before deleting
        if ($trainer->user_id) {
            User::where('id', $trainer->user_id)->update(['role' => 'admin']);
        }

        $trainer->delete();

        return redirect('/trainers')->with('success', 'Trainer deleted successfully.');
    }
}
