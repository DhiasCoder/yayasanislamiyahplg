<?php

namespace App\Livewire\Murid\Materi;

use App\Models\Materi;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DetailMateriMurid extends Component
{
    public $materi;

    public function mount(int $materi)
    {
        $siswa = Auth::user()->siswa;

        abort_unless($siswa, 404);

        $kelasIds = $siswa->kelas()
            ->where('kelas.is_active', true)
            ->pluck('kelas.id');

        $this->materi = Materi::whereHas('pengajaran', function ($query) use ($kelasIds) {
            $query->whereIn('kelas_id', $kelasIds)
                ->where('is_active', true);
        })
            ->where('is_active', true)
            ->findOrFail($materi);
    }

    public function render()
    {
        return view('livewire.murid.materi.detail-materi-murid')
            ->layout('murid.layouts.app');
    }
}
