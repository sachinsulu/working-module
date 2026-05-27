<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Create Permissions
        $permissions = [
            'view dashboard',
            'manage users',
            'manage clients',
            'manage projects',
            'manage roles',
            'view department stats',
        ];

        foreach ($permissions as $permissionName) {
            Permission::findOrCreate($permissionName);
        }

        // 2. Create Roles and Assign Permissions
        
        // Super Admin - receives all permissions
        $superAdminRole = Role::findOrCreate('super admin');
        $superAdminRole->givePermissionTo(Permission::all());

        // Dept Head
        $deptHeadRole = Role::findOrCreate('dept head');
        $deptHeadRole->givePermissionTo([
            'view dashboard',
            'view department stats',
            'manage users',
        ]);

        // Mgmt
        $mgmtRole = Role::findOrCreate('mgmt');
        $mgmtRole->givePermissionTo([
            'view dashboard',
            'view department stats',
        ]);

        // Team
        $teamRole = Role::findOrCreate('team');
        $teamRole->givePermissionTo([
            'view dashboard',
        ]);

        // 3. Create Demo Users and assign roles
        
        // Super Admin
        $superAdmin = User::create([
            'name' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('apanel'),
            'employee_id' => 'EMP-001',
            'department' => 'IT & Security',
            'contact_no' => '+1 (555) 123-4567',
            'address' => '742 Evergreen Terrace, Sector 7G, Springfield',
        ]);
        $superAdmin->assignRole($superAdminRole);

        // Dept Head
        $deptHead = User::create([
            'name' => 'Sarah Connor',
            'email' => 'depthead@example.com',
            'password' => Hash::make('password'),
            'employee_id' => 'EMP-002',
            'department' => 'Engineering',
            'contact_no' => '+1 (555) 987-6543',
            'address' => '404 Kernel Street, Cyberdyne District, Los Angeles',
        ]);
        $deptHead->assignRole($deptHeadRole);

        // Mgmt
        $mgmtUser = User::create([
            'name' => 'Jane Smith',
            'email' => 'mgmt@example.com',
            'password' => Hash::make('password'),
            'employee_id' => 'EMP-003',
            'department' => 'Operations & Finance',
            'contact_no' => '+1 (555) 456-7890',
            'address' => '999 Executive Way, Suite 400, New York',
        ]);
        $mgmtUser->assignRole($mgmtRole);

        // Team User
        $teamUser = User::create([
            'name' => 'John Doe',
            'email' => 'team@example.com',
            'password' => Hash::make('password'),
            'employee_id' => 'EMP-004',
            'department' => 'Marketing',
            'contact_no' => '+1 (555) 789-0123',
            'address' => '101 Creative Blvd, Design District, Portland',
        ]);
        $teamUser->assignRole($teamRole);
    }
}
