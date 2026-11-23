<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RaporDetail extends Model
{
    use HasFactory;

    protected $table = 'rapor_details';

    protected $fillable = [
        'rapor_id',
        'mata_pelajaran_id',
        'nilai',
        'predikat',
        'deskripsi',
    ];

    protected $casts = [
        'nilai' => 'decimal:2',
    ];

    public function rapor(): BelongsTo
    {
        return $this->belongsTo(Rapor::class, 'rapor_id');
    }

    public function mataPelajaran(): BelongsTo
    {
        return $this->belongsTo(
            MataPelajaran::class,
            'mata_pelajaran_id'
        );
    }
}
