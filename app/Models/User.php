<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    // Daftar kelas per tahun ajaran (angkatan):
    //   2026/2027 = kelas X, 2025/2026 = kelas XI, 2024/2025 = kelas XII.
    // Aturan: class_name harus selalu sejalan dengan academic_year_id.
    const CLASS_BY_YEAR = [
        '2026/2027' => [
            'X RPL 1',
            'X RPL 2',
            'X RPL 3',
            'X TKJ 1',
            'X TKJ 2',
            'X DKV 1',
            'X DKV 2',
            'X Transmisi 1',
        ],
        '2025/2026' => [
            'XI RPL 1',
            'XI RPL 2',
            'XI RPL 3',
            'XI RPL 4',
            'XI TKJ 1',
            'XI TKJ 2',
            'XI DKV 1',
            'XI DKV 2',
            'XI Transmisi 1',
        ],
        '2024/2025' => [
            'XII RPL 1',
            'XII RPL 2',
            'XII RPL 3',
            'XII TKJ 1',
            'XII TKJ 2',
            'XII DKV 1',
            'XII DKV 2',
            'XII Transmisi 1',
        ],
    ];

    // Semua kelas (gabungan semua tahun ajaran) — untuk admin
    const CLASS_OPTIONS = [
        'X RPL 1',
        'X RPL 2',
        'X RPL 3',
        'X TKJ 1',
        'X TKJ 2',
        'X DKV 1',
        'X DKV 2',
        'X Transmisi 1',
        'XI RPL 1',
        'XI RPL 2',
        'XI RPL 3',
        'XI RPL 4',
        'XI TKJ 1',
        'XI TKJ 2',
        'XI DKV 1',
        'XI DKV 2',
        'XI Transmisi 1',
        'XII RPL 1',
        'XII RPL 2',
        'XII RPL 3',
        'XII TKJ 1',
        'XII TKJ 2',
        'XII DKV 1',
        'XII DKV 2',
        'XII Transmisi 1',
    ];

    // Kelas yang tersedia pada tahun ajaran tertentu
    public static function classesForYearName(?string $yearName): array
    {
        if (! $yearName) {
            return static::CLASS_OPTIONS;
        }

        return static::CLASS_BY_YEAR[$yearName] ?? [];
    }

    public static function classesForYearId($yearId): array
    {
        if (! $yearId) {
            return static::CLASS_OPTIONS;
        }

        $yearName = \App\Models\AcademicYear::where('id', $yearId)->value('name');

        return static::classesForYearName($yearName);
    }

    // Tahun ajaran (nama) yang menjadi induk sebuah kelas
    public static function yearNameForClass(?string $className): ?string
    {
        foreach (static::CLASS_BY_YEAR as $yearName => $classes) {
            if (in_array($className, $classes, true)) {
                return $yearName;
            }
        }

        return null;
    }

    // Nama tahun ajaran → id, dipakai untuk menurunkan tahun dari kelas
    public static function yearIdForClass(?string $className): ?int
    {
        $yearName = static::yearNameForClass($className);
        if (! $yearName) {
            return null;
        }

        return \App\Models\AcademicYear::where('name', $yearName)->value('id');
    }

    // Kelompok kelas per tahun ajaran untuk <optgroup> di form/filter
    public static function classGroups(): array
    {
        return collect(static::CLASS_BY_YEAR)
            ->map(fn ($classes, $yearName) => [
                'label' => match ($yearName) {
                    '2026/2027' => 'Kelas X — Angkatan 2026/2027',
                    '2025/2026' => 'Kelas XI — Angkatan 2025/2026',
                    '2024/2025' => 'Kelas XII — Angkatan 2024/2025',
                    default => 'Tahun Ajaran '.$yearName,
                },
                'year' => $yearName,
                'classes' => $classes,
            ])
            ->values()
            ->all();
    }

    // Daftar kelas sesuai cakupan user (admin bebas semua, lainnya tahun ajarannya sendiri)
    public static function classOptionsFor(?self $user): array
    {
        if (! $user || $user->role === 'admin') {
            return static::CLASS_OPTIONS;
        }

        return static::classesForYearName($user->academicYear?->name);
    }

    // Kolom yang boleh diisi lewat form (laravel mass assignment)
    protected $fillable = [
        'nis',
        'name',
        'username',
        'email',
        'password',
        'role',
        'previous_role',
        'class_name',
        'academic_year_id',
        'pj_subject',
        'status',
    ];

    // Kolom yang disembunyikan saat data user dikirim ke frontend
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Konversi otomatis tipe data kolom
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relasi: satu user punya banyak tugas (tugas yang dibuat oleh user ini)
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    // Relasi: user terdaftar di satu tahun ajaran
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    // Relasi: satu user bisa menandai banyak tugas sebagai selesai (many-to-many)
    public function completedTasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'task_user')
            ->withPivot('created_at', 'status', 'confirmed_by')
            ->orderByPivot('created_at');
    }

    public function pjAssignments(): HasMany
    {
        return $this->hasMany(PjAssignment::class);
    }

    public function isPjMapel(): bool
    {
        return $this->role === 'pj_mapel';
    }

    public function isPjOf(string $subject): bool
    {
        return $this->isPjMapel() && $this->pj_subject === $subject;
    }

    // Naikkan jadi PJ: simpan role asli dulu supaya bisa dikembalikan saat dicabut
    public function promoteToPj(string $pjSubject): void
    {
        $attributes = ['pj_subject' => $pjSubject];

        if ($this->role !== 'pj_mapel') {
            $attributes['previous_role'] = $this->role;
            $attributes['role'] = 'pj_mapel';
        }

        $this->update($attributes);
    }

    // Cabut status PJ: kembalikan role asli (default siswa)
    public function demoteFromPj(): void
    {
        $this->update([
            'role' => $this->previous_role ?? 'siswa',
            'previous_role' => null,
            'pj_subject' => null,
        ]);
    }

    public function isBanned(): bool
    {
        return $this->status === 'banned';
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    public function proofPhotos(): HasMany
    {
        return $this->hasMany(ProofPhoto::class);
    }
}
