<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Materi extends Model
{
    use HasFactory;

    protected $table = 'materis';

    protected $fillable = [
        'pengajaran_id',
        'judul',
        'deskripsi',
        'file_path',
        'video_url',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function pengajaran(): BelongsTo
    {
        return $this->belongsTo(Pengajaran::class, 'pengajaran_id');
    }

    public function diskusi(): HasMany
    {
        return $this->hasMany(Diskusi_materi::class, 'materi_id');
    }

    public function absensi()
    {
        return $this->hasOne(Absensi::class, 'materi_id');
    }
}
