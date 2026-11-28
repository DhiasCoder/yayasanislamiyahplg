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
        'ppdb_pendaftar_id',
        'nis',
        'nisn',
        'nik',
        'nama_lengkap',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'agama',
        'kewarganegaraan',
        'anak_ke',
        'status_orang_tua',
        'bakat_hobi',
        'alamat',
        'status_tempat_tinggal',
        'jarak_rumah',
        'no_telepon',
        'no_hp',
        'email',
        'berat_badan',
        'tinggi_badan',
        'golongan_darah',
        'asal_sekolah',
        'nomor_ijazah',
        'tanggal_ijazah',
        'jumlah_skhun',
        'nama_ayah',
        'tempat_lahir_ayah',
        'tanggal_lahir_ayah',
        'pendidikan_ayah',
        'pekerjaan_ayah',
        'penghasilan_ayah',
        'nama_ibu',
        'tempat_lahir_ibu',
        'tanggal_lahir_ibu',
        'pendidikan_ibu',
        'pekerjaan_ibu',
        'penghasilan_ibu',
        'nama_wali',
        'hubungan_wali',
        'pendidikan_wali',
        'pekerjaan_wali',
        'penghasilan_wali',
        'alamat_orang_tua_wali',
        'kewarganegaraan_orang_tua',
        'no_hp_orang_tua',
        'tahun_masuk',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
            'tanggal_ijazah' => 'date',
            'tanggal_lahir_ayah' => 'date',
            'tanggal_lahir_ibu' => 'date',
            'anak_ke' => 'integer',
            'tahun_masuk' => 'integer',
            'berat_badan' => 'decimal:2',
            'tinggi_badan' => 'decimal:2',
            'jumlah_skhun' => 'decimal:2',
            'penghasilan_ayah' => 'decimal:2',
            'penghasilan_ibu' => 'decimal:2',
            'penghasilan_wali' => 'decimal:2',
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

    public function ppdbPendaftar(): BelongsTo
    {
        return $this->belongsTo(PpdbPendaftar::class, 'ppdb_pendaftar_id');
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
