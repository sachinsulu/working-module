<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class DepartmentController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:departments.create', only: ['create', 'store']),
            new Middleware('permission:departments.edit', only: ['edit', 'update']),
            new Middleware('permission:departments.delete', only: ['destroy']),
        ];
    }

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
        $department->load('services');

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
            'services' => 'nullable|array',
            'services.*.id' => 'nullable|integer',
            'services.*.title' => 'required|string|max:255',
        ]);

        $department = DB::transaction(function () use ($validated) {
            $department = Department::create([
                'title' => $validated['title'],
                'head_user_id' => $validated['head_user_id'] ?? null,
            ]);

            $this->syncServices($department, $validated['services'] ?? []);

            return $department;
        });

        return redirect()->route('admin.departments.index')->with('message', "Department '{$department->title}' created successfully.");
    }

    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'head_user_id' => 'nullable|exists:users,id',
            'services' => 'nullable|array',
            'services.*.id' => 'nullable|integer',
            'services.*.title' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($validated, $department) {
            $department->update([
                'title' => $validated['title'],
                'head_user_id' => $validated['head_user_id'] ?? null,
            ]);

            $this->syncServices($department, $validated['services'] ?? []);
        });

        return redirect()->route('admin.departments.index')->with('message', "Department '{$department->title}' updated successfully.");
    }

    public function destroy(Department $department)
    {
        $title = $department->title;
        $department->delete();

        return redirect()->route('admin.departments.index')->with('message', "Department '{$title}' was deleted successfully.");
    }

    private function syncServices(Department $department, array $services): void
    {
        $existingIds = $department->services()->pluck('id')->map(fn ($id) => (int) $id);

        $rows = collect($services)
            ->map(function ($service) {
                $id = isset($service['id']) && $service['id'] !== '' ? (int) $service['id'] : null;
                $title = trim((string) ($service['title'] ?? ''));

                return ['id' => $id, 'title' => $title];
            })
            ->filter(fn ($service) => $service['title'] !== '');

        $keptIds = [];

        foreach ($rows as $row) {
            $serviceId = $row['id'];

            if ($serviceId && $existingIds->contains($serviceId)) {
                $department->services()->whereKey($serviceId)->update(['title' => $row['title']]);
                $keptIds[] = $serviceId;
                continue;
            }

            $created = $department->services()->create(['title' => $row['title']]);
            $keptIds[] = (int) $created->id;
        }

        if (empty($keptIds)) {
            $department->services()->delete();
            return;
        }

        $department->services()->whereNotIn('id', $keptIds)->delete();
    }
}
