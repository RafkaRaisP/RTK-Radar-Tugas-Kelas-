<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmokeTest extends TestCase
{
    use RefreshDatabase;

    private function year(): AcademicYear
    {
        return AcademicYear::firstOrCreate(
            ['name' => '2025/2026'],
            ['is_active' => false]
        );
    }

    private function makeUser(string $role, array $extra = []): User
    {
        $year = $this->year();

        return User::factory()->create(array_merge([
            'role' => $role,
            'class_name' => 'XI RPL 1',
            'academic_year_id' => $year->id,
            'status' => 'active',
        ], $extra));
    }

    public function test_public_pages_render(): void
    {
        $this->get('/')->assertRedirect()->assertStatus(302);
        $this->get('/radar')->assertStatus(200);
        $this->get('/login')->assertStatus(200);
        $this->get('/register')->assertStatus(200);
        $this->get('/forgot-password')->assertStatus(200);
    }

    public function test_check_nis_found(): void
    {
        Student::create([
            'nis' => '262777777',
            'name' => 'Smoke Student',
            'class_name' => 'X RPL 1',
            'academic_year_id' => null,
        ]);

        $this->getJson('/students/262777777/check')
            ->assertStatus(200)
            ->assertJson(['found' => true, 'class_name' => 'X RPL 1']);
    }

    public function test_admin_pages_render(): void
    {
        $admin = $this->makeUser('admin');

        foreach (['/admin', '/admin/users', '/admin/tasks', '/admin/pj', '/admin/academic-years', '/admin/subjects', '/admin/verifications'] as $path) {
            $this->actingAs($admin)->get($path)->assertStatus(200);
        }
    }

    public function test_ketua_dashboard_and_pdf_export_render(): void
    {
        $ketua = $this->makeUser('ketua_kelas');

        $this->actingAs($ketua)->get('/dashboard')->assertStatus(200);

        $pdf = $this->actingAs($ketua)->get('/tasks/export');
        $pdf->assertStatus(200);
        $this->assertStringContainsString('pdf', strtolower($pdf->headers->get('Content-Type')));
        $this->assertStringStartsWith('%PDF', $pdf->getContent());
    }

    public function test_siswa_dashboard_renders(): void
    {
        $siswa = $this->makeUser('siswa', ['nis' => '262777778']);

        $this->actingAs($siswa)->get('/dashboard')->assertStatus(200);
    }

    public function test_pj_dashboard_renders(): void
    {
        $pj = $this->makeUser('pj_mapel', ['pj_subject' => 'Matematika']);

        $this->actingAs($pj)->get('/pj/dashboard')->assertStatus(200);
    }
}
