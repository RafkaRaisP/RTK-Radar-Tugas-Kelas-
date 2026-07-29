<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Task extends Model
{
    use HasFactory;

    // Kolom yang boleh diisi lewat form
    protected $fillable = [
        'user_id',
        'class_name',
        'title',
        'subject',
        'deadline',
        'description',
    ];

    // Konversi otomatis: deadline dibaca sebagai objek Carbon (tanggal)
    protected function casts(): array
    {
        return [
            'deadline' => 'datetime',
        ];
    }

    // Relasi: tugas ini dibuat oleh satu user (pembuat)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relasi: tugas ini bisa ditandai selesai oleh banyak user (many-to-many)
    public function completedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'task_user')
            ->withPivot('created_at');
    }
}
