<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\ProofPhoto;
use App\Models\Task;
use App\Models\User;
use App\Support\PhotoCompressor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProofPhotoTest extends TestCase
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

    private function makeTask(User $creator): Task
    {
        return Task::create([
            'user_id' => $creator->id,
            'class_name' => 'XI RPL 1',
            'title' => 'Tugas Matematika',
            'subject' => 'Matematika',
            'deadline' => now()->addDay(),
            'academic_year_id' => $this->year()->id,
        ]);
    }

    /** Bikin JPEG valid berukuran kurang-lebih $targetBytes. */
    private function makeJpeg(int $targetBytes): UploadedFile
    {
        $width = 2400;
        $height = 1800;
        $img = imagecreatetruecolor($width, $height);

        // Pola acak biar JPEG-nya gak terkompres terlalu kecil
        for ($y = 0; $y < $height; $y += 4) {
            for ($x = 0; $x < $width; $x += 4) {
                $color = imagecolorallocate(
                    $img,
                    random_int(0, 255),
                    random_int(0, 255),
                    random_int(0, 255)
                );
                imagefilledrectangle($img, $x, $y, $x + 3, $y + 3, $color);
            }
        }

        $path = tempnam(sys_get_temp_dir(), 'jpg');
        imagejpeg($img, $path, 100);
        imagedestroy($img);

        // Naikin ukuran kalau kurang dari target (tambah noise band)
        while (filesize($path) < $targetBytes) {
            $append = random_bytes(min(65536, $targetBytes - filesize($path)));
            // JPEG marker APP segment palsu di akhir file (diabaikan decoder)
            file_put_contents($path, "\xFF\xE1".pack('n', strlen($append) + 2).$append, FILE_APPEND);
            if (filesize($path) > $targetBytes + 100000) {
                break;
            }
        }

        return new UploadedFile($path, 'bukti.jpg', 'image/jpeg', null, true);
    }

    public function test_compressor_caps_file_at_2mb(): void
    {
        Storage::fake('public');

        $file = $this->makeJpeg(3_000_000);
        $this->assertGreaterThan(PhotoCompressor::MAX_BYTES, $file->getSize());

        $path = PhotoCompressor::compressToPublic($file, 'proof-photos/1/1');
        $this->assertNotNull($path);

        $size = Storage::disk('public')->size($path);
        $this->assertLessThanOrEqual(PhotoCompressor::MAX_BYTES, $size);
        $this->assertGreaterThan(0, $size);
    }

    public function test_upload_saves_compressed_photo_to_db_and_disk(): void
    {
        Storage::fake('public');

        $siswa = $this->makeUser('siswa');
        $ketua = $this->makeUser('ketua_kelas');
        $task = $this->makeTask($ketua);

        $file = $this->makeJpeg(3_000_000);

        $this->actingAs($siswa)->post(route('tasks.done', $task), [
            'photos' => [$file],
        ])->assertSessionHas('success');

        $photo = ProofPhoto::where('task_id', $task->id)->where('user_id', $siswa->id)->first();
        $this->assertNotNull($photo, 'Foto harus masuk ke tabel proof_photos');

        $this->assertTrue(Storage::disk('public')->exists($photo->photo_path));
        $this->assertLessThanOrEqual(
            PhotoCompressor::MAX_BYTES,
            Storage::disk('public')->size($photo->photo_path)
        );

        $this->assertTrue($task->fresh()->isPendingFor($siswa));
    }

    public function test_pj_dashboard_shows_photos_in_pending_and_history(): void
    {
        Storage::fake('public');

        $pj = $this->makeUser('pj_mapel', ['pj_subject' => 'Matematika']);
        $ketua = $this->makeUser('ketua_kelas');
        $siswa = $this->makeUser('siswa', ['name' => 'Budi']);
        $task = $this->makeTask($ketua);

        // Pending + foto
        $task->completedBy()->attach($siswa->id, ['created_at' => now(), 'status' => 'pending']);
        $path = PhotoCompressor::compressToPublic($this->makeJpeg(500_000), 'proof-photos/'.$task->id.'/'.$siswa->id);
        ProofPhoto::create([
            'task_id' => $task->id,
            'user_id' => $siswa->id,
            'photo_path' => $path,
        ]);

        $html = $this->actingAs($pj)->get('/pj/dashboard')->assertStatus(200)->getContent();
        $this->assertStringContainsString('storage/'.$path, $html);
        $this->assertStringNotContainsString('Tidak ada bukti foto', $html);

        // Pindah ke riwayat (confirmed) — foto tetap tampil
        $task->completedBy()->updateExistingPivot($siswa->id, ['status' => 'confirmed', 'confirmed_by' => $pj->id]);

        $html = $this->actingAs($pj)->get('/pj/dashboard')->assertStatus(200)->getContent();
        $this->assertStringContainsString('storage/'.$path, $html);
        $this->assertStringContainsString('Riwayat Verifikasi', $html);
        $this->assertStringNotContainsString('Tidak ada bukti foto', $html);
    }

    public function test_pending_without_photos_can_still_upload(): void
    {
        Storage::fake('public');

        $siswa = $this->makeUser('siswa');
        $ketua = $this->makeUser('ketua_kelas');
        $task = $this->makeTask($ketua);

        // Status pending tapi tanpa foto (gap lama)
        $task->completedBy()->attach($siswa->id, ['created_at' => now(), 'status' => 'pending']);

        $this->actingAs($siswa)->post(route('tasks.done', $task), [
            'photos' => [$this->makeJpeg(300_000)],
        ])->assertSessionHas('success');

        $this->assertSame(
            1,
            ProofPhoto::where('task_id', $task->id)->where('user_id', $siswa->id)->count()
        );
        $this->assertTrue($task->fresh()->isPendingFor($siswa));
    }

    public function test_pending_with_photos_blocks_duplicate_upload(): void
    {
        Storage::fake('public');

        $siswa = $this->makeUser('siswa');
        $ketua = $this->makeUser('ketua_kelas');
        $task = $this->makeTask($ketua);

        $task->completedBy()->attach($siswa->id, ['created_at' => now(), 'status' => 'pending']);
        $path = PhotoCompressor::compressToPublic($this->makeJpeg(100_000), 'proof-photos/'.$task->id.'/'.$siswa->id);
        ProofPhoto::create([
            'task_id' => $task->id,
            'user_id' => $siswa->id,
            'photo_path' => $path,
        ]);

        $this->actingAs($siswa)->post(route('tasks.done', $task), [
            'photos' => [$this->makeJpeg(200_000)],
        ])->assertSessionHas('info');

        $this->assertSame(
            1,
            ProofPhoto::where('task_id', $task->id)->where('user_id', $siswa->id)->count()
        );
    }

    public function test_proof_photo_url_uses_asset_helper(): void
    {
        $photo = new ProofPhoto(['photo_path' => 'proof-photos/1/1/abc.jpg']);
        $this->assertSame(asset('storage/proof-photos/1/1/abc.jpg'), $photo->url);
    }
}
