<?php

namespace App\Livewire\Murid\MataPelajaran;

use App\Models\Pengajaran;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MataPelajaranMuridManager extends Component
{
    public function render()
    {
        $user = Auth::user();

        $siswa = $user->siswa;

        $mataPelajaran = collect();

        if ($siswa) {
            $kelasIds = $siswa->kelas()
                ->where('kelas.is_active', true)
                ->pluck('kelas.id');

            $mataPelajaran = Pengajaran::query()
                ->with(['mataPelajaran', 'guru'])
                ->withCount('materi')
                ->whereIn('kelas_id', $kelasIds)
                ->where('is_active', true)
                ->whereHas('mataPelajaran')
                ->get();
        }

        return view(
            'livewire.murid.mata-pelajaran.mata-pelajaran-murid-manager',
            ['mataPelajaran' => $mataPelajaran]
        )->layout('murid.layouts.app');
    }
}
