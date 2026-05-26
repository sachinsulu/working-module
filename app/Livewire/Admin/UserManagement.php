<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use App\Models\User;
use App\Models\Department;
use Spatie\Permission\Models\Role;

#[Layout('layouts.app')]
class UserManagement extends Component
{
    use WithPagination;

    public function mount()
    {
        abort_unless(auth()->user()->can('manage users'), 403);
    }

    #[Url(history: true)]
    public $search = '';

    #[Url(history: true)]
    public $filterDepartment = '';

    #[Url(history: true)]
    public $filterRole = '';

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['search', 'filterDepartment', 'filterRole'])) {
            $this->resetPage();
        }
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->filterDepartment = '';
        $this->filterRole = '';
        $this->resetPage();
    }

    public function render()
    {
        $users = User::query()
            ->when($this->search, fn($q) => $q->where(fn($sub) =>
                $sub->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%")
                    ->orWhere('employee_id', 'like', "%{$this->search}%")))
            ->when($this->filterDepartment, fn($q) => $q->where('department', $this->filterDepartment))
            ->when($this->filterRole, fn($q) => $q->whereHas('roles', fn($sq) => $sq->where('name', $this->filterRole)))
            ->paginate(10);

        $allDepartments = User::select('department')
            ->distinct()
            ->orderBy('department')
            ->pluck('department');

        $roles = Role::orderBy('name')->get();
        $departments = Department::orderBy('title')->get();

        return view('livewire.admin.user-management', [
            'users'          => $users,
            'allDepartments' => $allDepartments,
            'roles'          => $roles,
            'departments'    => $departments,
        ]);
    }
}
