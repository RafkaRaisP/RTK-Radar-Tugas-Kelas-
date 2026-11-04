<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\PjAssignment;
use App\Models\Subject;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PjTest extends TestCase
{
    use RefreshDatabase;

    private function year(): AcademicYear
    {
        return AcademicYear::firstOrCreate(
            ['name' => '2025/2026'],
            ['is_active' => true]
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

    private function subject(string $name): Subject
    {
        return Subject::firstOrCreate(['name' => $name]);
    }

    private function assignPj(User $user, Subject $subject, string $className = 'XI RPL 1'): PjAssignment
    {
        $assignment = PjAssignment::create([
            'user_id' => $user->id,
            'subject_id' => $subject->id,
            'academic_year_id' => $this->year()->id,
            'class_name' => $className,
        ]);

        $user->promoteToPj($subject->name);

        return $assignment;
    }

    public function test_store_rejects_second_pj_for_same_class_and_subject(): void
    {
        $admin = $this->makeUser('admin');
        $math = $this->subject('Matematika');
        $existing = $this->makeUser('siswa', ['name' => 'Budi Santoso', 'username' => 'budi123']);
        $this->assignPj($existing, $math);

        $candidate = $this->makeUser('siswa', ['name' => 'Ani', 'username' => 'ani456']);

        $this->actingAs($admin)->post(route('admin.pj.store'), [
            'user_id' => $candidate->id,
            'subject_id' => $math->id,
            'academic_year_id' => $this->year()->id,
            'class_name' => 'XI RPL 1',
        ])->assertSessionHas('error');

        $error = session('error');
        $this->assertStringContainsString('Budi Santoso', $error);
        $this->assertStringContainsString('budi123', $error);
        $this->assertStringContainsString('Matematika', $error);
        $this->assertDatabaseCount('pj_assignments', 1);
        $this->assertFalse($candidate->fresh()->isPjMapel());
    }

    public function test_store_rejects_user_who_is_already_pj_of_another_subject(): void
    {
        $admin = $this->makeUser('admin');
        $math = $this->subject('Matematika');
        $indo = $this->subject('Bahasa Indonesia');

        $person = $this->makeUser('siswa', ['name' => 'Siti', 'username' => 'siti789']);
        $this->assignPj($person, $math);

        $otherClass = $this->makeUser('siswa', ['class_name' => 'XI RPL 2']);

        // Coba jadikan orang yang sama sebagai PJ mapel lain
        $this->actingAs($admin)->post(route('admin.pj.store'), [
            'user_id' => $person->id,
            'subject_id' => $indo->id,
            'academic_year_id' => $this->year()->id,
            'class_name' => 'XI RPL 1',
        ])->assertSessionHas('error');

        $error = session('error');
        $this->assertStringContainsString('satu orang hanya boleh menjadi pj satu mapel', strtolower($error));
        $this->assertDatabaseCount('pj_assignments', 1);

        // User lain yang belum PJ bisa ditugaskan di kelas berbeda
        $this->actingAs($admin)->post(route('admin.pj.store'), [
            'user_id' => $otherClass->id,
            'subject_id' => $indo->id,
            'academic_year_id' => $this->year()->id,
            'class_name' => 'XI RPL 2',
        ])->assertSessionHas('success');
        $this->assertDatabaseCount('pj_assignments', 2);
    }

    public function test_confirm_task_rejects_wrong_subject_or_class(): void
    {
        $mathPj = $this->makeUser('pj_mapel', ['pj_subject' => 'Matematika', 'username' => 'pjmath']);
        $math = $this->subject('Matematika');
        $indo = $this->subject('Bahasa Indonesia');

        $creator = $this->makeUser('ketua_kelas');

        $wrongSubject = Task::create([
            'user_id' => $creator->id,
            'class_name' => 'XI RPL 1',
            'title' => 'Tugas Bahasa',
            'subject' => 'Bahasa Indonesia',
            'deadline' => now()->addDay(),
            'academic_year_id' => $this->year()->id,
        ]);

        $wrongClass = Task::create([
            'user_id' => $creator->id,
            'class_name' => 'XI RPL 2',
            'title' => 'Tugas Matematika',
            'subject' => 'Matematika',
            'deadline' => now()->addDay(),
            'academic_year_id' => $this->year()->id,
        ]);

        $siswa = $this->makeUser('siswa');

        $this->actingAs($mathPj)
            ->post(route('pj.confirm', $wrongSubject), ['user_id' => $siswa->id])
            ->assertStatus(403);

        $this->actingAs($mathPj)
            ->post(route('pj.confirm', $wrongClass), ['user_id' => $siswa->id])
            ->assertStatus(403);
    }

    public function test_confirm_task_works_for_matching_subject_and_class(): void
    {
        $mathPj = $this->makeUser('pj_mapel', ['pj_subject' => 'Matematika', 'username' => 'pjmath']);
        $creator = $this->makeUser('ketua_kelas');
        $siswa = $this->makeUser('siswa');

        $task = Task::create([
            'user_id' => $creator->id,
            'class_name' => 'XI RPL 1',
            'title' => 'Tugas Matematika',
            'subject' => 'Matematika',
            'deadline' => now()->addDay(),
            'academic_year_id' => $this->year()->id,
        ]);

        $task->completedBy()->attach($siswa->id, [
            'created_at' => now(),
            'status' => 'pending',
        ]);

        $this->actingAs($mathPj)
            ->post(route('pj.confirm', $task), ['user_id' => $siswa->id])
            ->assertSessionHas('success');

        $this->assertTrue($task->fresh()->isConfirmedFor($siswa));
    }

    public function test_update_reverts_old_user_role_when_reassigned(): void
    {
        $admin = $this->makeUser('admin');
        $math = $this->subject('Matematika');

        $oldPj = $this->makeUser('ketua_kelas', ['username' => 'oldpj']);
        $assignment = $this->assignPj($oldPj, $math);
        $this->assertSame('pj_mapel', $oldPj->fresh()->role);
        $this->assertSame('ketua_kelas', $oldPj->fresh()->previous_role);

        $newPj = $this->makeUser('siswa', ['username' => 'newpj']);

        $this->actingAs($admin)->put(route('admin.pj.update', $assignment), [
            'user_id' => $newPj->id,
            'subject_id' => $math->id,
            'academic_year_id' => $this->year()->id,
            'class_name' => 'XI RPL 1',
        ])->assertSessionHas('success');

        $this->assertSame('pj_mapel', $newPj->fresh()->role);
        $this->assertSame('Matematika', $newPj->fresh()->pj_subject);
        $this->assertSame('ketua_kelas', $oldPj->fresh()->role);
        $this->assertNull($oldPj->fresh()->previous_role);
        $this->assertNull($oldPj->fresh()->pj_subject);
    }

    public function test_destroy_restores_previous_role(): void
    {
        $admin = $this->makeUser('admin');
        $math = $this->subject('Matematika');

        $pj = $this->makeUser('sekretaris', ['username' => 'sekrepj']);
        $assignment = $this->assignPj($pj, $math);
        $this->assertSame('pj_mapel', $pj->fresh()->role);

        $this->actingAs($admin)->delete(route('admin.pj.destroy', $assignment))
            ->assertSessionHas('success');

        $fresh = $pj->fresh();
        $this->assertSame('sekretaris', $fresh->role);
        $this->assertNull($fresh->previous_role);
        $this->assertNull($fresh->pj_subject);
    }

    public function test_destroy_defaults_to_siswa_when_no_previous_role(): void
    {
        $admin = $this->makeUser('admin');
        $math = $this->subject('Matematika');

        $pj = $this->makeUser('siswa', ['username' => 'siswapj']);
        $assignment = $this->assignPj($pj, $math);

        $this->actingAs($admin)->delete(route('admin.pj.destroy', $assignment))
            ->assertSessionHas('success');

        $fresh = $pj->fresh();
        $this->assertSame('siswa', $fresh->role);
        $this->assertNull($fresh->pj_subject);
    }
}
