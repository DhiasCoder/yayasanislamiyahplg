<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tugas extends Model
{
    use HasFactory;

    protected $table = 'tugas';

    protected $fillable = [
        'pengajaran_id',
        'guru_id',
        'judul',
        'deskripsi',
        'file_soal',
        'tanggal_mulai',
        'deadline',
        'nilai_maksimal',
        'boleh_terlambat',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'datetime',
            'deadline' => 'datetime',
            'nilai_maksimal' => 'decimal:2',
            'boleh_terlambat' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function pengajaran(): BelongsTo
    {
        return $this->belongsTo(Pengajaran::class, 'pengajaran_id');
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    public function pengumpulan(): HasMany
    {
        return $this->hasMany(PengumpulanTugas::class, 'tugas_id');
    }
}
