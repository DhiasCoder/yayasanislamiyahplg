<?php

namespace App\Livewire\Murid\MataPelajaran;

use App\Models\Pengajaran;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DetailMataPelajaranMurid extends Component
{
    public $pengajaran;

    public function mount(int $pengajaran)
    {
        $siswa = Auth::user()->siswa;

        abort_unless($siswa, 404);

        $kelasIds = $siswa->kelas()
            ->where('kelas.is_active', true)
            ->pluck('kelas.id');

        $this->pengajaran = Pengajaran::with([
            'mataPelajaran',
            'guru',
            'materi' => fn ($query) => $query->where('is_active', true),
        ])
            ->whereIn('kelas_id', $kelasIds)
            ->where('is_active', true)
            ->findOrFail($pengajaran);
    }

    public function render()
    {
        return view(
            'livewire.murid.mata-pelajaran.detail-mata-pelajaran-murid',
            ['pengajaran' => $this->pengajaran]
        )->layout('murid.layouts.app');
    }
}
