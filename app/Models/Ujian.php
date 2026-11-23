<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ujian extends Model
{
    use HasFactory;

    protected $table = 'ujians';

    protected $fillable = [
        'pengajaran_id',
        'judul',
        'deskripsi',
        'jenis',
        'tanggal_mulai',
        'tanggal_selesai',
        'durasi',
        'acak_soal',
        'acak_jawaban',
        'nilai_minimal',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'datetime',
            'tanggal_selesai' => 'datetime',
            'durasi' => 'integer',
            'acak_soal' => 'boolean',
            'acak_jawaban' => 'boolean',
            'nilai_minimal' => 'decimal:2',
        ];
    }

    public function pengajaran(): BelongsTo
    {
        return $this->belongsTo(Pengajaran::class, 'pengajaran_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(UjianQuestion::class, 'ujian_id');
    }

    public function results(): HasMany
    {
        return $this->hasMany(UjianResult::class, 'ujian_id');
    }
}
