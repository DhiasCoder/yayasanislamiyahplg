<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PpdbDokumen extends Model
{
    protected $table = 'ppdb_dokumens';

    protected $fillable = [
        'pendaftar_id',
        'jenis_dokumen',
        'nama_file',
        'file_path',
        'nomor_urut',
        'status_verifikasi',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'nomor_urut' => 'integer',
        ];
    }

    public function pendaftar(): BelongsTo
    {
        return $this->belongsTo(PpdbPendaftar::class, 'pendaftar_id');
    }
}
