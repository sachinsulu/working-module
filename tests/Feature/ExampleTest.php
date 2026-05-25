<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    /**
     * Test that unauthenticated users are redirected to login.
     */
    public function test_unauthenticated_users_are_redirected_to_login(): void
    {
        $response = $this->get('/');
        $response->assertRedirect('/login');
    }

    /**
     * Test that authenticated users can view the dashboard.
     */
    public function test_authenticated_users_can_view_dashboard(): void
    {
        $user = User::where('email', 'admin@example.com')->first();
        $response = $this->actingAs($user)->get('/admin');
        $response->assertStatus(200);
    }
}
