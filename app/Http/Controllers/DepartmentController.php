<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function create()
    {
        $department = new Department();
        $allDeptHeads = User::role('dept head')->orderBy('name')->get();

        return view('departments.form', [
            'department' => $department,
            'allDeptHeads' => $allDeptHeads,
        ]);
    }

    public function edit(Department $department)
    {
        $allDeptHeads = User::role('dept head')->orderBy('name')->get();

        return view('departments.form', [
            'department' => $department,
            'allDeptHeads' => $allDeptHeads,
        ]);
    }

    public function index()
    {
        $departments = Department::with('head')->orderBy('title')->get();
        // Assuming users with Spatie role "dept head" can lead departments
        $allDeptHeads = User::role('dept head')->orderBy('name')->get();

        return view('departments.index', [
            'departments' => $departments,
            'allDeptHeads' => $allDeptHeads,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'head_user_id' => 'nullable|exists:users,id',
        ]);

        $department = Department::create([
            'title' => $validated['title'],
            'head_user_id' => $validated['head_user_id'] ?? null,
        ]);

        return redirect()->route('admin.departments.index')->with('message', "Department '{$department->title}' created successfully.");
    }

    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'head_user_id' => 'nullable|exists:users,id',
        ]);

        $department->update([
            'title' => $validated['title'],
            'head_user_id' => $validated['head_user_id'] ?? null,
        ]);

        return redirect()->route('admin.departments.index')->with('message', "Department '{$department->title}' updated successfully.");
    }

    public function destroy(Department $department)
    {
        $title = $department->title;
        $department->delete();

        return redirect()->route('admin.departments.index')->with('message', "Department '{$title}' was deleted successfully.");
    }
}
