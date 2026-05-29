<?php

namespace App\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

#[Layout('layouts.app')]
class RoleManagement extends Component
{
    use WithPagination;

    public function mount()
    {
        abort_unless(
            auth()->user()->can('view roles') ||
            auth()->user()->can('create roles') ||
            auth()->user()->can('edit roles') ||
            auth()->user()->can('delete roles'),
            403
        );
    }

    #[Url(history: true)]
    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $roles = Role::query()
            ->with('permissions')
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderBy('name')
            ->paginate(15);

        return view('livewire.admin.role-management', [
            'roles'       => $roles,
            'permissions' => Permission::all(),
        ]);
    }
}
