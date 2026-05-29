<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\Rule;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class RoleController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:create roles', only: ['create', 'store']),
            new Middleware('permission:edit roles', only: ['edit', 'update']),
            new Middleware('permission:delete roles', only: ['destroy']),
        ];
    }

    public function create()
    {
        $role = new Role();
        $permissions = Permission::all();

        return view('roles.form', [
            'role' => $role,
            'permissions' => $permissions,
        ]);
    }

    public function edit(Role $role)
    {
        $role->load('permissions');
        $permissions = Permission::all();

        return view('roles.form', [
            'role' => $role,
            'permissions' => $permissions,
        ]);
    }

    public function index()
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();

        return view('roles.index', [
            'roles' => $roles,
            'permissions' => $permissions,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'roleName' => 'required|string|max:255|unique:roles,name',
            'selectedPermissions' => 'array',
        ]);

        $formattedName = strtolower(trim($validated['roleName']));

        $role = Role::create([
            'name' => $formattedName,
            'guard_name' => 'web',
        ]);

        $permissions = $request->input('selectedPermissions', []);
        $role->syncPermissions($permissions);

        return redirect()->route('admin.roles.index')->with('message', "Role '{$formattedName}' created successfully.");
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'roleName' => ['required', 'string', 'max:255', Rule::unique('roles', 'name')->ignore($role->id)],
            'selectedPermissions' => 'array',
        ]);

        $formattedName = strtolower(trim($validated['roleName']));

        // Prevent renaming super admin
        if ($role->name === 'super admin' && $formattedName !== 'super admin') {
            return redirect()->route('admin.roles.index')->with('error', 'The "super admin" role cannot be renamed.');
        }

        $role->update(['name' => $formattedName]);

        $permissions = $request->input('selectedPermissions', []);
        $role->syncPermissions($permissions);

        return redirect()->route('admin.roles.index')->with('message', "Role '{$formattedName}' updated successfully.");
    }

    public function destroy(Role $role)
    {
        if ($role->name === 'super admin') {
            return redirect()->route('admin.roles.index')->with('error', 'The "super admin" role is protected and cannot be deleted.');
        }

        $name = $role->name;
        $role->delete();

        return redirect()->route('admin.roles.index')->with('message', "Role '{$name}' was deleted successfully.");
    }
}
