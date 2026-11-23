<?php

namespace App\Livewire\Murid;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\DiskusiMateri;
use App\Events\KomentarMateriEvent;

class MateriDiskusi extends Component
{
    public $materi_id;
    public $pesan;

    protected function getListeners()
    {
        return [
            "echo:diskusi-materi.{$this->materi_id},.KomentarMateriEvent"
            => "refreshKomentar",
        ];
    }

    public function mount($materi_id)
    {
        $this->materi_id = $materi_id;
    }

    public function kirimKomentar()
    {
        $komentar = DiskusiMateri::create([
            'materi_id' => $this->materi_id,
            'user_id'   => Auth::id(),
            'isi'       => $this->pesan,
        ]);

        broadcast(new KomentarMateriEvent($komentar))->toOthers();

        $this->pesan = '';
    }

    public function refreshKomentar()
    {
        $this->dispatch('$refresh');
    }

    public function render()
    {
        return view('livewire.murid.materi-diskusi', [
            'komentar' => DiskusiMateri::where('materi_id', $this->materi_id)
                ->with('user')
                ->orderBy('created_at', 'asc')
                ->latest()
                ->get(),
        ]);
    }
}
