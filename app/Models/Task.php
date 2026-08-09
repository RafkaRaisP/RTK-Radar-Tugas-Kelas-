<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

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

    // Konversi otomatis: deadline disimpan sebagai instan UTC mutlak.
    // Input ber-offset (mis. 12:27+07:00) → disimpan 05:27; dibaca kembali sebagai UTC.
    protected function deadline(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => Carbon::parse($value),
            set: fn ($value) => Carbon::parse($value)->utc(),
        );
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
