<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Department;
use Spatie\Permission\Models\Role;
use Database\Seeders\DatabaseSeeder;

class DepartmentHeadUserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $deptHead;
    protected $department;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        // Find the seeded dept head
        $this->deptHead = User::role('dept head')->first();

        // Create a department and set Sarah Connor (dept head) as head
        $this->department = Department::create([
            'title' => 'Engineering',
            'head_user_id' => $this->deptHead->id,
        ]);
    }

    public function test_super_admin_can_see_department_select_and_choose_roles()
    {
        $admin = User::role('super admin')->first();

        $response = $this->actingAs($admin)
            ->get(route('admin.users.create'));

        $response->assertStatus(200);
        $response->assertSee('name="department"', false);
        $response->assertSee('Select a role');
    }

    public function test_department_head_cannot_see_department_select_and_role_is_locked_to_team()
    {
        $response = $this->actingAs($this->deptHead)
            ->get(route('admin.users.create'));

        $response->assertStatus(200);
        // Department select should not be visible
        $response->assertDontSee('name="department"', false);
        // Should have a disabled select showing team and a hidden input for team role
        $response->assertSee('value="team"', false);
    }

    public function test_department_head_can_add_member_automatically_assigned_to_their_department_with_team_role()
    {
        $newMemberData = [
            'name' => 'New Team Member',
            'email' => 'newmember@example.com',
            'employee_id' => 'EMP-999',
            'password' => 'secret123',
            'contact_no' => '+123456789',
            'address' => 'Test Address',
        ];

        // Ensure new member does not exist
        $this->assertDatabaseMissing('users', ['email' => 'newmember@example.com']);

        $response = $this->actingAs($this->deptHead)
            ->post(route('admin.users.store'), $newMemberData);

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('message');

        // Check user was created
        $newMember = User::where('email', 'newmember@example.com')->first();
        $this->assertNotNull($newMember);

        // Verify the department is taken from the department they head
        $this->assertEquals('Engineering', $newMember->department);

        // Verify the role is default team
        $this->assertTrue($newMember->hasRole('team'));
        $this->assertFalse($newMember->hasRole('dept head'));
    }

    public function test_department_head_without_department_gets_validation_error_when_adding_member()
    {
        // Create another department head who doesn't head any department
        $unassignedDeptHead = User::create([
            'name' => 'Unassigned Head',
            'email' => 'unassigned@example.com',
            'password' => bcrypt('password'),
            'employee_id' => 'EMP-888',
        ]);
        $unassignedDeptHead->assignRole(Role::findByName('dept head'));

        $newMemberData = [
            'name' => 'Another Member',
            'email' => 'another@example.com',
            'employee_id' => 'EMP-777',
            'password' => 'secret123',
        ];

        $response = $this->actingAs($unassignedDeptHead)
            ->post(route('admin.users.store'), $newMemberData);

        $response->assertSessionHasErrors(['department']);
        $this->assertDatabaseMissing('users', ['email' => 'another@example.com']);
    }

    public function test_department_head_can_update_member_automatically_retaining_their_department_and_team_role()
    {
        // Create an existing team member
        $teamUser = User::create([
            'name' => 'Existing Team User',
            'email' => 'existing@example.com',
            'password' => bcrypt('password'),
            'employee_id' => 'EMP-555',
            'department' => 'Engineering',
        ]);
        $teamUser->assignRole(Role::findByName('team'));

        $updateData = [
            'name' => 'Updated Team User',
            'email' => 'existing@example.com',
            'employee_id' => 'EMP-555',
            'contact_no' => '+999999',
            'address' => 'Updated Address',
        ];

        $response = $this->actingAs($this->deptHead)
            ->put(route('admin.users.update', $teamUser), $updateData);

        $response->assertRedirect(route('admin.users.index'));

        // Refresh and check changes
        $teamUser->refresh();
        $this->assertEquals('Updated Team User', $teamUser->name);
        $this->assertEquals('Engineering', $teamUser->department);
        $this->assertTrue($teamUser->hasRole('team'));
    }
}
