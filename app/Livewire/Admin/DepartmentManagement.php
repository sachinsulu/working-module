<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Department;
use App\Models\User;

#[Layout('layouts.app')]
class DepartmentManagement extends Component
{
    public function mount()
    {
        abort_unless(
            auth()->user()->can('view departments') ||
            auth()->user()->can('create departments') ||
            auth()->user()->can('edit departments') ||
            auth()->user()->can('delete departments'),
            403
        );
    }

    public function render()
    {
        $departments = Department::with('head')->orderBy('title')->get();
        $allDeptHeads = User::role('dept head')->orderBy('name')->get();

        return view('livewire.admin.department-management', [
            'departments'  => $departments,
            'allDeptHeads' => $allDeptHeads,
        ]);
    }
}
