<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicYear extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'is_active'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // ID tahun ajaran aktif, fallback ke tahun pertama jika belum ada yang aktif
    public static function currentId(): ?int
    {
        return static::where('is_active', true)->value('id') ?? static::value('id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function pjAssignments(): HasMany
    {
        return $this->hasMany(PjAssignment::class);
    }
}
