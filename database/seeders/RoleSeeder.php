<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Clear cached permissions/roles
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Super Admin – gets all permissions (already done in PermissionSeeder, but ensure association)
        $superAdmin = Role::firstOrCreate(['name' => 'super admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // Dept Head – department CRUD permissions
        $deptHead = Role::firstOrCreate(['name' => 'dept head']);
        $deptHead->givePermissionTo([
            'view departments',
            'create departments',
            'edit departments',
            'delete departments',
        ]);

        // Mgmt – full CRUD on all core modules
        $mgmt = Role::firstOrCreate(['name' => 'mgmt']);
        $mgmt->givePermissionTo([
            'view users', 'create users', 'edit users', 'delete users',
            'view clients', 'create clients', 'edit clients', 'delete clients',
            'view projects', 'create projects', 'edit projects', 'delete projects',
            'view roles', 'create roles', 'edit roles', 'delete roles',
            'view departments', 'create departments', 'edit departments', 'delete departments',
        ]);

        // Teams – permission to view dashboard
        $teams = Role::firstOrCreate(['name' => 'teams']);
        $teams->givePermissionTo(['view dashboard']);
    }
}
