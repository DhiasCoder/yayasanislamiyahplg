<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiskusiMateri extends Model
{
    protected $table = 'diskusi_materis';
    protected $fillable = ['materi_id', 'user_id', 'isi'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function materi()
    {
        return $this->belongsTo(Materi::class);
    }
}
