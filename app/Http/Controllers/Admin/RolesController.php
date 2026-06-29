<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesController extends Controller
{
    public function index(): View
    {
        if (! auth()->user()->can('view roles')) {
            abort(403, 'Unauthorized action.');
        }

        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();

        return view('admin.roles.index', compact('roles', 'permissions'));
    }

    public function create(): View
    {
        if (! auth()->user()->can('add roles')) {
            abort(403, 'Unauthorized action.');
        }

        $permissions = Permission::all();

        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request): RedirectResponse
    {
        if (! auth()->user()->can('add roles')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'permissions' => ['required', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $role = Role::create([
            'name' => $validated['name'],
        ]);

        $role->syncPermissions($validated['permissions']);

        return redirect()->route('admin.roles.index')->with('success', 'Role created successfully.');
    }

    public function edit(Role $role): View
    {
        if (! auth()->user()->can('edit roles')) {
            abort(403, 'Unauthorized action.');
        }

        if ($role->name === 'Super Admin') {
            abort(403, 'The Super Admin role cannot be edited.');
        }

        $permissions = Permission::all();

        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        if (! auth()->user()->can('edit roles')) {
            abort(403, 'Unauthorized action.');
        }

        if ($role->name === 'Super Admin') {
            return redirect()->route('admin.roles.index')->with('error', 'The Super Admin role cannot be modified.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name,'.$role->id],
            'permissions' => ['required', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $role->update([
            'name' => $validated['name'],
        ]);

        $role->syncPermissions($validated['permissions']);

        return redirect()->route('admin.roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        if (! auth()->user()->can('delete roles')) {
            abort(403, 'Unauthorized action.');
        }

        if ($role->name === 'Super Admin') {
            return redirect()->route('admin.roles.index')->with('error', 'The Super Admin role cannot be deleted.');
        }

        $role->delete();

        return redirect()->route('admin.roles.index')->with('success', 'Role deleted successfully.');
    }
}
