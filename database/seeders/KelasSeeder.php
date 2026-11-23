<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kelas;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        $kelas = ['Kelas 10', 'Kelas 11', 'Kelas 12'];

        foreach ($kelas as $nama) {
            Kelas::create([
                'nama_kelas' => $nama
            ]);
        }
    }
}
