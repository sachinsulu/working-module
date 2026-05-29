<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use App\Models\Department;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class UserController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:users.create', only: ['create', 'store']),
            new Middleware('permission:users.edit', only: ['edit', 'update']),
            new Middleware('permission:users.delete', only: ['destroy']),
        ];
    }

    public function create()
    {
        $user = new User();
        $rolesQuery = Role::orderBy('name');
        if (!auth()->user()->hasRole('super admin')) {
            $rolesQuery->where('name', '!=', 'super admin');
        }
        $roles = $rolesQuery->get();
        $departments = Department::orderBy('title')->get();

        return view('users.form', [
            'user' => $user,
            'roles' => $roles,
            'departments' => $departments,
        ]);
    }

    public function edit(User $user)
    {
        if ($user->hasRole('super admin') && !auth()->user()->hasRole('super admin')) {
            abort(403);
        }

        $rolesQuery = Role::orderBy('name');
        if (!auth()->user()->hasRole('super admin')) {
            $rolesQuery->where('name', '!=', 'super admin');
        }
        $roles = $rolesQuery->get();
        $departments = Department::orderBy('title')->get();

        return view('users.form', [
            'user' => $user,
            'roles' => $roles,
            'departments' => $departments,
        ]);
    }

    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $filterDepartment = $request->input('department', '');
        $filterRole = $request->input('role', '');

        $usersQuery = User::with('roles');

        if ($search) {
            $queryStr = '%' . $search . '%';
            $usersQuery->where(function($query) use ($queryStr) {
                $query->where('name', 'like', $queryStr)
                      ->orWhere('email', 'like', $queryStr)
                      ->orWhere('employee_id', 'like', $queryStr)
                      ->orWhere('department', 'like', $queryStr);
            });
        }

        if ($filterDepartment) {
            $usersQuery->where('department', $filterDepartment);
        }

        if ($filterRole) {
            $usersQuery->role($filterRole);
        }

        $users = $usersQuery->paginate(6)->withQueryString();

        // Gather all Roles & Unique Departments for grids/checklists
        $rolesList = Role::all();
        $allDepartments = User::whereNotNull('department')
            ->where('department', '!=', '')
            ->distinct()
            ->pluck('department');

        return view('users.index', [
            'users' => $users,
            'roles' => $rolesList,
            'allDepartments' => $allDepartments,
            'search' => $search,
            'filterDepartment' => $filterDepartment,
            'filterRole' => $filterRole,
        ]);
    }

    public function store(Request $request)
    {
        if (auth()->user()->hasRole('dept head')) {
            $department = Department::where('head_user_id', auth()->id())->first();
            if (!$department) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['department' => 'You must be assigned as a head of a department before adding members.']);
            }
            $request->merge([
                'roles'                 => ['team'],
                'department'            => $department->title,
                'status'               => 'active',
                'password_confirmation' => $request->input('password'),
            ]);
        }

        $selectedRole = $request->input('roles.0');

        $rolesRules = ['array'];
        if (!auth()->user()->hasRole('super admin')) {
            $rolesRules[] = function ($attribute, $value, $fail) {
                if (in_array('super admin', $value)) {
                    $fail('You are not authorized to assign the super admin role.');
                }
            };
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'employee_id' => 'required|string|max:50|unique:users,employee_id',
            'password' => 'required|string|min:6|confirmed',
            'department' => [Rule::requiredIf($selectedRole === 'team'), 'nullable', 'string', 'max:100'],
            'contact_no' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'roles' => $rolesRules,
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'employee_id' => $validated['employee_id'],
            'department' => $selectedRole === 'team' ? ($validated['department'] ?? null) : null,
            'contact_no' => $validated['contact_no'] ?? null,
            'address' => $validated['address'] ?? null,
            'status' => $validated['status'],
        ]);

        if ($request->has('roles')) {
            $user->syncRoles($request->input('roles'));
        }

        return redirect()->route('admin.users.index')->with('message', "User '{$user->name}' created successfully.");
    }

    public function update(Request $request, User $user)
    {
        if ($user->hasRole('super admin') && !auth()->user()->hasRole('super admin')) {
            abort(403);
        }

        if (auth()->user()->hasRole('dept head')) {
            $department = Department::where('head_user_id', auth()->id())->first();
            if (!$department) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['department' => 'You must be assigned as a head of a department before editing members.']);
            }
            $request->merge([
                'roles'      => ['team'],
                'department' => $department->title,
                'status'     => $user->status ?? 'active',
            ]);
        }

        $selectedRole = $request->input('roles.0');

        $rolesRules = ['array'];
        if (!auth()->user()->hasRole('super admin')) {
            $rolesRules[] = function ($attribute, $value, $fail) {
                if (in_array('super admin', $value)) {
                    $fail('You are not authorized to assign the super admin role.');
                }
            };
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'employee_id' => ['required', 'string', 'max:50', Rule::unique('users', 'employee_id')->ignore($user->id)],
            'password' => 'nullable|string|min:6|confirmed',
            'department' => [Rule::requiredIf($selectedRole === 'team'), 'nullable', 'string', 'max:100'],
            'contact_no' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'roles' => $rolesRules,
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'employee_id' => $validated['employee_id'],
            'department' => $selectedRole === 'team' ? ($validated['department'] ?? null) : null,
            'contact_no' => $validated['contact_no'] ?? null,
            'address' => $validated['address'] ?? null,
            'status' => $validated['status'],
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        $roles = $request->input('roles', []);
        $user->syncRoles($roles);

        return redirect()->route('admin.users.index')->with('message', "User '{$user->name}' updated successfully.");
    }

    public function destroy(User $user)
    {
        if ($user->hasRole('super admin') && !auth()->user()->hasRole('super admin')) {
            abort(403);
        }

        // Avoid deleting self
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')->with('error', "You cannot delete your own authenticated account.");
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')->with('message', "User '{$name}' was deleted successfully.");
    }
}
