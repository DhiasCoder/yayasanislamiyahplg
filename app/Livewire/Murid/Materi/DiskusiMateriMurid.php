<?php

namespace App\Livewire\Murid\Materi;

use App\Models\Materi;
use App\Models\Diskusi_materi;
use App\Events\KomentarMateriEvent;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DiskusiMateriMurid extends Component
{
    public $materi;
    public $isi = '';

    protected function getListeners()
    {
        return [
            "echo:diskusi-materi.{$this->materi->id},.KomentarMateriEvent"
                => 'refreshKomentar',
        ];
    }

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

    public function kirimDiskusi()
    {
        $this->validate([
            'isi' => ['required', 'string', 'max:2000'],
        ]);

        $komentar = Diskusi_materi::create([
            'materi_id' => $this->materi->id,
            'user_id' => Auth::id(),
            'parent_id' => null,
            'isi' => $this->isi,
        ]);

        // Kirim event ke pengguna lain yang membuka materi yang sama.
        broadcast(new KomentarMateriEvent($komentar))->toOthers();

        $this->reset('isi');

        session()->flash('success', 'Diskusi berhasil dikirim.');
    }

    public function refreshKomentar()
    {
        $this->dispatch('$refresh');
    }

    public function render()
    {
        $diskusi = Diskusi_materi::with('user')
            ->where('materi_id', $this->materi->id)
            ->whereNull('parent_id')
            ->oldest()
            ->get();

        return view('livewire.murid.materi.diskusi-materi-murid', [
            'diskusi' => $diskusi,
        ])->layout('murid.layouts.app');
    }
}
