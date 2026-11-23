<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Absensi extends Model
{
    use HasFactory;

    protected $table = 'absensis';

    protected $fillable = [
        'pengajaran_id',
        'materi_id',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'kode_absensi',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
        ];
    }

    public function pengajaran(): BelongsTo
    {
        return $this->belongsTo(Pengajaran::class, 'pengajaran_id');
    }

    public function siswaAbsensi(): HasMany
    {
        return $this->hasMany(AbsensiSiswa::class, 'absensi_id');
    }

    public function materi(): BelongsTo
    {
        return $this->belongsTo(Materi::class, 'materi_id');
    }

    public function detailAbsensis(): HasMany
    {
        return $this->hasMany(AbsensiSiswa::class, 'absensi_id');
    }
}
