<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $modules = [
            'users',
            'clients',
            'projects',
            'roles',
            'departments',
        ];

        $actions = ['view', 'create', 'edit', 'delete'];

        $permissions = ['view dashboard'];

        foreach ($modules as $module) {
            foreach ($actions as $action) {
                $permissions[] = "{$action} {$module}";
            }
        }
        
        $permissions[] = 'edit team';

        foreach ($permissions as $permissionName) {
            Permission::findOrCreate($permissionName);
        }

        $superAdminRole = Role::findOrCreate('super admin');
        $superAdminRole->givePermissionTo(Permission::all());
    }
}