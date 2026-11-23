<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Jadwal extends Model
{
    use HasFactory;

    protected $table = 'jadwals';

    protected $fillable = [
        'pengajaran_id',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'ruang',
    ];

    public function pengajaran(): BelongsTo
    {
        return $this->belongsTo(Pengajaran::class, 'pengajaran_id');
    }
}
