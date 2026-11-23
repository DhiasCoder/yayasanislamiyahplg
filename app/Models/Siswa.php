<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Siswa extends Model
{
    protected $table = 'siswas';

    protected $fillable = [
        'user_id',
        'sekolah_id',
        'nis',
        'nisn',
        'nik',
        'nama_lengkap',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'agama',
        'no_hp',
        'nama_ayah',
        'nama_ibu',
        'no_hp_orang_tua',
        'tahun_masuk',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
            'tahun_masuk' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class);
    }

    public function kelas(): BelongsToMany
    {
        return $this->belongsToMany(
            Kelas::class,
            'siswa_kelas',
            'siswa_id',
            'kelas_id'
        )->withPivot('tahun_ajaran_id')->withTimestamps();
    }

    public function nilais(): HasMany
    {
        return $this->hasMany(Nilai::class, 'siswa_id');
    }

    public function absensiSiswas(): HasMany
    {
        return $this->hasMany(AbsensiSiswa::class, 'siswa_id');
    }
}
