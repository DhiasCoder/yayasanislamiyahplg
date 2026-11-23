<?php

namespace App\Http\Controllers\Murid;

use App\Http\Controllers\Controller;
use App\Models\Tugas;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TugasFileController extends Controller
{
    public function show($id)
    {
        $siswa = Auth::user()->siswa;

        abort_unless($siswa, 403);

        $kelasIds = $siswa->kelas()->pluck('kelas.id');

        $tugas = Tugas::whereHas('pengajaran', function ($query) use ($kelasIds) {
            $query->whereIn('kelas_id', $kelasIds)
                ->where('is_active', true);
        })->findOrFail($id);

        abort_unless($tugas->file_soal, 404);

        abort_unless(
            Storage::disk('local')->exists($tugas->file_soal),
            404
        );

        return Storage::disk('local')->response(
            $tugas->file_soal
        );
    }
}
