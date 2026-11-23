<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PpdbPembayaran extends Model
{
    protected $table = 'ppdb_pembayarans';

    protected $fillable = [
        'pendaftar_id',
        'jenis_pembayaran',
        'nominal',
        'tanggal_bayar',
        'metode_pembayaran',
        'nomor_kuitansi',
        'bukti_pembayaran',
        'dicatat_oleh',
        'status',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'nominal' => 'decimal:2',
            'tanggal_bayar' => 'datetime',
        ];
    }

    public function pendaftar(): BelongsTo
    {
        return $this->belongsTo(PpdbPendaftar::class, 'pendaftar_id');
    }

    public function pencatat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }
}
