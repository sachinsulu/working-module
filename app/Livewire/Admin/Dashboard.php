<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.admin.dashboard', [
            'stats' => $this->getStats(),
        ]);
    }

    private function getStats(): array
    {
        $totalUsers = User::count();
        $totalRoles = Role::count();
        $totalPermissions = Permission::count();

        // Distribution of departments
        $departments = User::select('department')
            ->whereNotNull('department')
            ->where('department', '!=', '')
            ->groupBy('department')
            ->selectRaw('department, count(*) as count')
            ->get()
            ->toArray();

        // Recent users logged
        $recentUsers = User::latest()->limit(5)->get();

        return [
            'total_users'       => $totalUsers,
            'total_roles'       => $totalRoles,
            'total_permissions' => $totalPermissions,
            'departments'       => $departments,
            'recent_users'      => $recentUsers,
        ];
    }
}
