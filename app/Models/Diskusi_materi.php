<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Diskusi_materi extends Model
{
    use HasFactory;

    protected $table = 'diskusi_materis';

    protected $fillable = [
        'materi_id',
        'user_id',
        'parent_id',
        'isi',
    ];

    public function materi(): BelongsTo
    {
        return $this->belongsTo(Materi::class, 'materi_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(
            Diskusi_materi::class,
            'parent_id'
        );
    }

    public function replies(): HasMany
    {
        return $this->hasMany(
            Diskusi_materi::class,
            'parent_id'
        );
    }
}
