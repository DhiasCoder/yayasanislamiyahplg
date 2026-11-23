<?php

namespace App\Livewire\Guru\Kelas;

use App\Models\Pengajaran;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

#[\Livewire\Attributes\Layout('guru.layouts.app')]
class DetailKelasMengajar extends Component
{
    public $pengajaran;

    public function mount(int $pengajaran)
    {
        $guru = Auth::user()->guru;

        abort_unless($guru, 404);

        // Pastikan penugasan memang milik guru yang login
        $this->pengajaran = Pengajaran::with([
            'kelas.siswa',
            'mataPelajaran',
            'guru',
            'tahunAjaran',
        ])
            ->where('guru_id', $guru->id)
            ->where('is_active', true)
            ->findOrFail($pengajaran);
    }

    public function render()
    {
        $siswa = $this->pengajaran->kelas->siswa()
            ->orderBy('nama_lengkap')
            ->get();

        return view('livewire.guru.kelas.detail-kelas-mengajar', [
            'siswa' => $siswa,
        ]);
    }
}
