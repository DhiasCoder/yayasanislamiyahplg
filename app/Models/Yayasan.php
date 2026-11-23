<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Yayasan extends Model
{
    protected $fillable = [
        'nama_yayasan',
        'alamat',
        'email',
        'telepon',
        'logo',
    ];

    public function sekolahs(): HasMany
    {
        return $this->hasMany(Sekolah::class);
    }
}
