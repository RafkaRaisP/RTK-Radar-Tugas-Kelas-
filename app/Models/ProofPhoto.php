<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProofPhoto extends Model
{
    protected $fillable = ['task_id', 'user_id', 'photo_path'];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getUrlAttribute(): string
    {
        // asset() ikut host request sekarang — tidak nempel APP_URL
        return asset('storage/'.$this->photo_path);
    }
}
