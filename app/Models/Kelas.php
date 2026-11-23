<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas';

    protected $fillable = [
        'sekolah_id',
        'tahun_ajaran_id',
        'jurusan_id',
        'wali_kelas_id',
        'nama_kelas',
        'tingkat',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class, 'sekolah_id');
    }

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }

    public function jurusan(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class, 'jurusan_id');
    }

    public function waliKelas(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'wali_kelas_id');
    }

    public function siswa(): BelongsToMany
    {
        return $this->belongsToMany(
            Siswa::class,
            'siswa_kelas',
            'kelas_id',
            'siswa_id'
        );
    }

    public function materi(): HasManyThrough
    {
        return $this->hasManyThrough(
            Materi::class,
            Pengajaran::class,
            'kelas_id',
            'pengajaran_id',
            'id',
            'id'
        );
    }

    public function ujian(): HasMany
    {
        return $this->hasMany(Ujian::class, 'kelas_id');
    }

    public function pengajarans(): HasMany
    {
        return $this->hasMany(Pengajaran::class, 'kelas_id');
    }
}
