<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $superAdminRole = Role::findOrCreate('super admin');

        $superAdmin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'admin',
                'password' => Hash::make('apanel'),
                'employee_id' => 'EMP-001',
                'contact_no' => '+1 (555) 123-4567',
                'address' => '742 Evergreen Terrace, Sector 7G, Springfield',
            ]
        );
        $superAdmin->assignRole($superAdminRole);
    }
}
