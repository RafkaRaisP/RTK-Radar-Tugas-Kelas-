<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    private function makeStudent(string $nis = '262700001', string $class = 'X RPL 1'): Student
    {
        $year = AcademicYear::firstOrCreate(
            ['name' => '2026/2027'],
            ['is_active' => true]
        );

        return Student::create([
            'nis' => $nis,
            'name' => 'Siswa Uji Coba',
            'class_name' => $class,
            'academic_year_id' => $year->id,
        ]);
    }

    public function test_login_page_loads(): void
    {
        $this->get('/login')->assertStatus(200);
    }

    public function test_register_page_loads(): void
    {
        $this->get('/register')->assertStatus(200);
    }

    public function test_check_nis_returns_404_for_unknown_nis(): void
    {
        $this->getJson('/students/999999999/check')->assertStatus(404);
    }

    public function test_user_can_register_with_nis(): void
    {
        $student = $this->makeStudent();

        $this->post('/register', [
            'nis' => $student->nis,
            'username' => 'testuser',
            'email' => 'testuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect('/dashboard');

        $this->assertDatabaseHas('users', [
            'username' => 'testuser',
            'nis' => $student->nis,
            'class_name' => 'X RPL 1',
            'role' => 'siswa',
            'academic_year_id' => $student->academic_year_id,
        ]);
    }

    public function test_register_fails_with_unknown_nis(): void
    {
        $this->post('/register', [
            'nis' => '262799999',
            'username' => 'testuser',
            'email' => 'testuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertSessionHasErrors('nis');

        $this->assertDatabaseMissing('users', ['username' => 'testuser']);
    }

    public function test_register_fails_with_bad_nis_format(): void
    {
        $this->post('/register', [
            'nis' => '1234',
            'username' => 'testuser',
            'email' => 'testuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertSessionHasErrors('nis');
    }

    public function test_register_fails_when_nis_already_registered(): void
    {
        $student = $this->makeStudent('262700002', 'X RPL 2');

        $payload = [
            'nis' => $student->nis,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $this->post('/register', $payload + [
            'username' => 'first',
            'email' => 'first@example.com',
        ])->assertRedirect('/dashboard');

        $this->post('/register', $payload + [
            'username' => 'second',
            'email' => 'second@example.com',
        ])->assertSessionHasErrors('nis');
    }

    public function test_user_can_login_without_class_selection(): void
    {
        User::factory()->create([
            'username' => 'testuser',
            'password' => bcrypt('password123'),
            'class_name' => 'XI RPL 1',
        ]);

        $this->post('/login', [
            'username' => 'testuser',
            'password' => 'password123',
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
        ])->assertSessionHasErrors('username');
    }
}
