<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', ['user' => $request->user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $request->validate([
            'name'                    => 'required|string|max:255',
            'email'                   => 'required|email|unique:users,email,' . $user->id,
            'phone'                   => 'nullable|string|max:50',
            'company'                 => 'nullable|string|max:255',
            'designation'             => 'nullable|string|max:255',
            'country'                 => 'nullable|string|max:100',
            'department'              => 'nullable|string|max:150',
            'linkedin_url'            => 'nullable|url|max:255',
            'preferred_language'      => 'nullable|string|max:10',
            'bio'                     => 'nullable|string|max:1000',
            'emergency_contact_name'  => 'nullable|string|max:150',
            'emergency_contact_phone' => 'nullable|string|max:50',
            'photo'                   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            if ($user->photo_path) {
                Storage::disk('public')->delete($user->photo_path);
            }
            $path = $request->file('photo')->store('profile-photos', 'public');
            $user->photo_path = $path;
        }

        $user->fill([
            'name'                    => $request->name,
            'email'                   => $request->email,
            'phone'                   => $request->phone,
            'company'                 => $request->company,
            'designation'             => $request->designation,
            'country'                 => $request->country,
            'department'              => $request->department,
            'linkedin_url'            => $request->linkedin_url,
            'preferred_language'      => $request->preferred_language ?? 'en',
            'bio'                     => $request->bio,
            'emergency_contact_name'  => $request->emergency_contact_name,
            'emergency_contact_phone' => $request->emergency_contact_phone,
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password'         => ['required', 'confirmed', Rules\Password::min(8)],
        ]);

        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password changed successfully.');
    }
}
