<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ElearningEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhere('company', 'like', "%{$request->search}%");
            });
        }

        $users = $query->orderBy('role')->orderBy('name')->paginate(20)->appends($request->query());

        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email',
            'password'    => ['required', 'confirmed', Rules\Password::min(8)],
            'role'        => 'required|in:admin,trainer,participant',
            'phone'       => 'nullable|string|max:50',
            'company'     => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:255',
            'country'     => 'nullable|string|max:100',
        ]);

        $user = User::create([
            'name'        => $request->name,
            'email'       => $request->email,
            'password'    => Hash::make($request->password),
            'role'        => $request->role,
            'phone'       => $request->phone,
            'company'     => $request->company,
            'designation' => $request->designation,
            'country'     => $request->country,
            'is_active'   => true,
        ]);

        // Auto-link existing eLearning enrollments by email
        ElearningEnrollment::where('email', $user->email)
            ->whereNull('user_id')
            ->update(['user_id' => $user->id]);

        return redirect()->route('users.index')->with('success', "User '{$user->name}' created successfully.");
    }

    public function edit(User $user)
    {
        $enrollments = ElearningEnrollment::with('course')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return view('users.edit', compact('user', 'enrollments'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email,'.$user->id,
            'role'        => 'required|in:admin,trainer,participant',
            'phone'       => 'nullable|string|max:50',
            'company'     => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:255',
            'country'     => 'nullable|string|max:100',
            'is_active'   => 'boolean',
        ]);

        // Prevent admin from deactivating own account
        if ($user->id === Auth::id() && !$request->boolean('is_active')) {
            return back()->with('error', 'You cannot deactivate your own account.');
        }

        $user->update([
            'name'        => $request->name,
            'email'       => $request->email,
            'role'        => $request->role,
            'phone'       => $request->phone,
            'company'     => $request->company,
            'designation' => $request->designation,
            'country'     => $request->country,
            'is_active'   => $request->boolean('is_active', true),
        ]);

        return redirect()->route('users.index')->with('success', "User '{$user->name}' updated successfully.");
    }

    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::min(8)],
        ]);

        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Password reset successfully.');
    }

    public function toggleActive(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot deactivate your own account.');
        }

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Account {$status} successfully.");
    }
}
