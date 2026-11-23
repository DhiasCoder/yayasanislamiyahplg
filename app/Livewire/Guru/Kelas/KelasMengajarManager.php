<?php

namespace App\Livewire\Guru\Kelas;

use App\Models\Pengajaran;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

#[\Livewire\Attributes\Layout('guru.layouts.app')]
class KelasMengajarManager extends Component
{
    public $search = '';

    public function render()
    {
        $guru = Auth::user()->guru;

        abort_unless($guru, 404);

        $pengajarans = Pengajaran::query()
            ->with([
                'kelas.siswa',
                'mataPelajaran',
                'tahunAjaran',
            ])
            ->where('guru_id', $guru->id)
            ->where('is_active', true)
            ->when($this->search, function ($query) {
                $query->whereHas('kelas', function ($kelas) {
                    $kelas->where('nama_kelas', 'like', '%' . $this->search . '%');
                })->orWhereHas('mataPelajaran', function ($mapel) {
                    $mapel->where('nama', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->get();

        return view('livewire.guru.kelas.kelas-mengajar-manager', [
            'pengajarans' => $pengajarans,
        ]);
    }
}
