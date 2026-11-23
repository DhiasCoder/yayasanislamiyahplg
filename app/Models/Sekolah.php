<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sekolah extends Model
{
    protected $fillable = [
        'yayasan_id',
        'nama_sekolah',
        'kode_sekolah',
        'jenjang',
        'alamat',
        'email',
        'telepon',
        'logo',
        'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function yayasan(): BelongsTo
    {
        return $this->belongsTo(Yayasan::class);
    }
}
