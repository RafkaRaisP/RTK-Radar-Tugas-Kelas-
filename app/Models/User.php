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

    // Daftar kelas yang tersedia di aplikasi
    const CLASS_OPTIONS = [
        'XI RPL 1',
        'XI RPL 2',
        'XI RPL 3',
        'XI RPL 4',
        'XI TKJ 1',
        'XI TKJ 2',
        'XI DKV 1',
        'XI DKV 2',
        'XI Transmisi 1',
    ];

    // Kolom yang boleh diisi lewat form (laravel mass assignment)
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
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
