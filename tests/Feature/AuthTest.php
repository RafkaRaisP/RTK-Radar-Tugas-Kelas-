<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_loads(): void
    {
        $this->get('/login')->assertStatus(200);
    }

    public function test_register_page_loads(): void
    {
        $this->get('/register')->assertStatus(200);
    }

    public function test_user_can_register(): void
    {
        $this->post('/register', [
            'name' => 'Test User',
            'username' => 'testuser',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'class_name' => 'XI RPL 1',
        ])->assertRedirect('/dashboard');

        $this->assertDatabaseHas('users', ['username' => 'testuser']);
    }

    public function test_user_can_login(): void
    {
        User::factory()->create([
            'username' => 'testuser',
            'password' => bcrypt('password123'),
            'class_name' => 'XI RPL 1',
        ]);

        $this->post('/login', [
            'username' => 'testuser',
            'password' => 'password123',
            'class_name' => 'XI RPL 1',
        ])->assertRedirect('/dashboard');
    }

    public function test_login_fails_with_wrong_password(): void
    {
        User::factory()->create([
            'username' => 'testuser',
            'password' => bcrypt('password123'),
            'class_name' => 'XI RPL 1',
        ]);

        $this->post('/login', [
            'username' => 'testuser',
            'password' => 'wrongpassword',
            'class_name' => 'XI RPL 1',
        ])->assertSessionHasErrors('username');
    }
}
