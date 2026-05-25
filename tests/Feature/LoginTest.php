<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Livewire\Livewire;
use App\Livewire\Login;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_can_view_login_page()
    {
        $this->get('/login')->assertStatus(200);
    }

    public function test_can_login_with_valid_credentials()
    {
        Livewire::test(Login::class)
            ->set('email', 'admin@example.com')
            ->set('password', 'apanel')
            ->call('login')
            ->assertRedirect('/');

        $this->assertAuthenticated();
    }

    public function test_cannot_login_with_invalid_credentials()
    {
        Livewire::test(Login::class)
            ->set('email', 'admin@example.com')
            ->set('password', 'wrong-password')
            ->call('login')
            ->assertSet('errorMessage', 'Invalid credentials. Please try again.');

        $this->assertGuest();
    }
}
