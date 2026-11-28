<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PpdbPendaftar extends Model
{
    protected $table = 'ppdb_pendaftars';

    protected $fillable = [
        'ppdb_period_id',
        'nomor_pendaftaran',
        'kode_akses',
        'nama_lengkap',
        'nik',
        'nisn',
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
        'nama_ibu',
        'nama_wali',
        'hubungan_wali',
        'alamat_orang_tua_wali',
        'tempat_lahir_ayah',
        'tanggal_lahir_ayah',
        'tempat_lahir_ibu',
        'tanggal_lahir_ibu',
        'pekerjaan_ayah',
        'pekerjaan_ibu',
        'pekerjaan_wali',
        'penghasilan_ayah',
        'penghasilan_ibu',
        'penghasilan_wali',
        'pendidikan_ayah',
        'pendidikan_ibu',
        'pendidikan_wali',
        'kewarganegaraan_orang_tua',
        'masuk_kelas',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
            'tanggal_ijazah' => 'date',
            'tanggal_lahir_ayah' => 'date',
            'tanggal_lahir_ibu' => 'date',
            'berat_badan' => 'decimal:2',
            'tinggi_badan' => 'decimal:2',
            'jumlah_skhun' => 'decimal:2',
            'penghasilan_ayah' => 'decimal:2',
            'penghasilan_ibu' => 'decimal:2',
            'penghasilan_wali' => 'decimal:2',
            'anak_ke' => 'integer',
        ];
    }

    public function periode(): BelongsTo
    {
        return $this->belongsTo(PpdbPeriods::class, 'ppdb_period_id');
    }

    public function dokumens(): HasMany
    {
        return $this->hasMany(PpdbDokumen::class, 'pendaftar_id');
    }

    public function pembayarans(): HasMany
    {
        return $this->hasMany(PpdbPembayaran::class, 'pendaftar_id');
    }
    public function siswa(): HasOne
    {
        return $this->hasOne(Siswa::class, 'ppdb_pendaftar_id');
    }
}
