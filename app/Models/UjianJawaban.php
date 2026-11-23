<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UjianJawaban extends Model
{
    protected $table = 'ujian_jawabans';

    protected $fillable = [
        'ujian_result_id',
        'ujian_question_id',
        'jawaban',
        'is_correct',
        'skor',
    ];

    protected function casts(): array
    {
        return [
            'is_correct' => 'boolean',
            'skor' => 'decimal:2',
        ];
    }

    public function hasilUjian(): BelongsTo
    {
        return $this->belongsTo(UjianResult::class, 'ujian_result_id');
    }

    public function soal(): BelongsTo
    {
        return $this->belongsTo(UjianQuestion::class, 'ujian_question_id');
    }
}
