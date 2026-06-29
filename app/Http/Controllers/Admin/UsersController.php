<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UsersController extends Controller
{
    public function index(): View
    {
        if (! auth()->user()->can('view users')) {
            abort(403, 'Unauthorized action.');
        }

        $users = User::with('roles')->get();
        $roles = Role::all();
        $authUser = auth()->user();

        $canManageMap = $users->mapWithKeys(function (User $user) use ($authUser): array {
            $isSuperAdmin = $user->hasRole('Super Admin');

            return [$user->id => $authUser->hasRole('Super Admin') || ! $isSuperAdmin];
        })->all();

        return view('admin.users.index', compact('users', 'roles', 'canManageMap'));
    }

    public function store(Request $request): RedirectResponse
    {
        if (! auth()->user()->can('add users')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'string', 'exists:roles,name'],
        ]);

        if ($validated['role'] === 'Super Admin' && ! auth()->user()->hasRole('Super Admin')) {
            return redirect()->route('admin.users.index')->with('error', 'You do not have permission to assign the Super Admin role.');
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);

        $user->assignRole($validated['role']);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        if (! auth()->user()->can('edit users')) {
            abort(403, 'Unauthorized action.');
        }

        if ($user->hasRole('Super Admin') && ! auth()->user()->hasRole('Super Admin')) {
            return redirect()->route('admin.users.index')->with('error', 'You do not have permission to edit a Super Admin user.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'password' => ['nullable', 'string', 'min:8'],
            'role' => ['required', 'string', 'exists:roles,name'],
        ]);

        if ($validated['role'] === 'Super Admin' && ! auth()->user()->hasRole('Super Admin')) {
            return redirect()->route('admin.users.index')->with('error', 'You do not have permission to assign the Super Admin role.');
        }

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if (! empty($validated['password'])) {
            $user->update([
                'password' => bcrypt($validated['password']),
            ]);
        }

        $user->syncRoles([$validated['role']]);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if (! auth()->user()->can('delete users')) {
            abort(403, 'Unauthorized action.');
        }

        if (auth()->id() === $user->id) {
            return redirect()->route('admin.users.index')->with('error', 'You cannot delete yourself.');
        }

        if ($user->hasRole('Super Admin') && ! auth()->user()->hasRole('Super Admin')) {
            return redirect()->route('admin.users.index')->with('error', 'You do not have permission to delete a Super Admin user.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
}
