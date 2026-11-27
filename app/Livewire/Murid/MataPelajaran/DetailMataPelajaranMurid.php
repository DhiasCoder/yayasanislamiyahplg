<?php

namespace App\Livewire\Murid\MataPelajaran;

use App\Models\Jadwal;
use App\Models\Pengajaran;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DetailMataPelajaranMurid extends Component
{
    public $pengajaran;
    public $jadwals;

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
            'materi' => fn($query) => $query->where('is_active', true),
        ])
            ->whereIn('kelas_id', $kelasIds)
            ->where('is_active', true)
            ->findOrFail($pengajaran);

        $this->jadwals = Jadwal::query()
            ->where('pengajaran_id', $this->pengajaran->id)
            ->where('is_active', true)
            ->orderByRaw("FIELD(hari, 'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu')")
            ->orderBy('jam_mulai')
            ->get();
    }

    public function render()
    {
        return view('livewire.murid.mata-pelajaran.detail-mata-pelajaran-murid', [
            'pengajaran' => $this->pengajaran,
            'jadwals' => $this->jadwals,
        ])->layout('murid.layouts.app');
    }
}
