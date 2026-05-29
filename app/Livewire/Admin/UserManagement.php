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
        abort_unless(
            auth()->user()->can('view users') ||
            auth()->user()->can('create users') ||
            auth()->user()->can('edit users') ||
            auth()->user()->can('delete users'),
            403
        );
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

    public function toggleStatus($id)
    {
        $user = User::find($id);
        if ($user) {
            $user->status = $user->status === 'active' ? 'inactive' : 'active';
            $user->save();
            $this->dispatch('notify', message: "Status for {$user->name} updated successfully.");
        }
    }

    public function render()
    {
        $usersQuery = User::query();
        if (!auth()->user()->hasRole('super admin')) {
            $usersQuery->whereDoesntHave('roles', fn($q) => $q->where('name', 'super admin'));
        }

        $users = $usersQuery
            ->when($this->search, fn($q) => $q->where(fn($sub) =>
                $sub->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%")
                    ->orWhere('employee_id', 'like', "%{$this->search}%")))
            ->when($this->filterDepartment, fn($q) => $q->where('department', $this->filterDepartment))
            ->when($this->filterRole, fn($q) => $q->whereHas('roles', fn($sq) => $sq->where('name', $this->filterRole)))
            ->paginate(10);

        $allDepartmentsQuery = User::select('department')
            ->distinct()
            ->orderBy('department');
        if (!auth()->user()->hasRole('super admin')) {
            $allDepartmentsQuery->whereDoesntHave('roles', fn($q) => $q->where('name', 'super admin'));
        }
        $allDepartments = $allDepartmentsQuery->pluck('department');

        $rolesQuery = Role::orderBy('name');
        if (!auth()->user()->hasRole('super admin')) {
            $rolesQuery->where('name', '!=', 'super admin');
        }
        $roles = $rolesQuery->get();

        $departments = Department::orderBy('title')->get();

        return view('livewire.admin.user-management', [
            'users'          => $users,
            'allDepartments' => $allDepartments,
            'roles'          => $roles,
            'departments'    => $departments,
        ]);
    }
}
