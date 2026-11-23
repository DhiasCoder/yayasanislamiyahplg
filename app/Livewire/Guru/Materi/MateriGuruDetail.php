<?php

namespace App\Livewire\Guru\Materi;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Materi;
use App\Models\DiskusiMateri;
use App\Events\KomentarMateriEvent;

#[\Livewire\Attributes\Layout('guru.layouts.app')]
class MateriGuruDetail extends Component
{
    public $materi;
    public $comment;
    public $materi_id;
    public $pesan;

    protected function getListeners()
    {
        return [
            "echo:diskusi-materi.{$this->materi_id},.KomentarMateriEvent"
            => "refreshKomentar",
        ];
    }

    public function mount($id)
    {
        $this->materi_id = $id;
        $this->materi = Materi::findOrFail($id);
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
        $this->js(<<<'JS'
            console.log('[PUSHER] Event komentar diterima oleh Livewire Guru');
            console.log('[PUSHER] Materi ID:', $wire.materi_id);
        JS);

        $this->dispatch('$refresh');
    }

    public function render()
    {
        return view('livewire.guru.materi.materi-guru-detail', [
            'komentar' => DiskusiMateri::where('materi_id', $this->materi_id)
                ->with('user')
                ->latest()
                ->get(),
        ]);
    }
}
