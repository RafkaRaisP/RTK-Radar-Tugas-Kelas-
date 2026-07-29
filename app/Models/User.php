<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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

    // Relasi: satu user bisa menandai banyak tugas sebagai selesai (many-to-many)
    public function completedTasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'task_user')
            ->withPivot('created_at')
            ->orderByPivot('created_at');
    }
}
