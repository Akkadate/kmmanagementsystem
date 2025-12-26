<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('department');

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter by department
        if ($request->filled('department')) {
            $query->where('department_id', $request->department);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ILIKE', "%{$search}%")
                  ->orWhere('email', 'ILIKE', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
        $departments = \App\Models\Department::where('is_active', true)->orderBy('name')->get();

        return view('admin.users.index', compact('users', 'departments'));
    }

    public function create()
    {
        $departments = \App\Models\Department::where('is_active', true)->orderBy('name')->get();
        return view('admin.users.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)],
            'role' => 'required|in:admin,editor,contributor,viewer',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'department_id' => $validated['department_id'] ?? null,
            'email_verified_at' => now(), // Auto-verify created users
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully!');
    }

    public function edit(User $user)
    {
        $departments = \App\Models\Department::where('is_active', true)->orderBy('name')->get();
        return view('admin.users.edit', compact('user', 'departments'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,editor,contributor,viewer',
            'department_id' => 'nullable|exists:departments,id',
            'password' => ['nullable', 'confirmed', Password::min(8)],
            'mark_email_verified' => 'nullable|boolean',
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'department_id' => $validated['department_id'] ?? null,
        ];

        // Only update password if provided
        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        // Handle email verification
        // Checkbox value: when checked = "1", when unchecked = not present in request
        if ($request->input('mark_email_verified') == '1') {
            // Checkbox is checked - verify the email
            $data['email_verified_at'] = $user->email_verified_at ?? now();
        } else {
            // Checkbox is unchecked - revoke verification
            $data['email_verified_at'] = null;
        }

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully!');
    }

    public function destroy(User $user)
    {
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return back()->withErrors(['user' => 'You cannot delete your own account.']);
        }

        // Check if user has articles
        $articleCount = $user->articles()->count();
        if ($articleCount > 0) {
            return back()->withErrors(['user' => "Cannot delete user with {$articleCount} articles. Please reassign articles first."]);
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully!');
    }
}
