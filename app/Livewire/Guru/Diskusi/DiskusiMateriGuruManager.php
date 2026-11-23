<?php

namespace App\Livewire\Guru\Diskusi;

use App\Events\KomentarMateriEvent;
use App\Models\Diskusi_materi;
use App\Models\Guru;
use App\Models\Materi;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class DiskusiMateriGuruManager extends Component
{
    public Materi $materi;

    public string $pesan = '';

    public function mount(Materi $materi): void
    {
        $guru = Guru::where('user_id', Auth::id())->firstOrFail();

        $this->materi = Materi::whereHas(
            'pengajaran',
            fn ($query) => $query->where('guru_id', $guru->id)
        )
            ->with('pengajaran.kelas')
            ->findOrFail($materi->id);
    }

    public function kirimPesan(): void
    {
        $this->validate([
            'pesan' => ['required', 'string', 'max:5000'],
        ]);

        $isi = trim($this->pesan);

        if ($isi === '') {
            $this->addError('pesan', 'Pesan tidak boleh kosong.');
            return;
        }

        $komentar = Diskusi_materi::create([
            'materi_id' => $this->materi->id,
            'user_id' => Auth::id(),
            'parent_id' => null,
            'isi' => $isi,
        ]);

        // Gunakan event Pusher yang sama dengan diskusi siswa.
        broadcast(new KomentarMateriEvent(
            $komentar->load('materi.pengajaran.kelas', 'user')
        ))->toOthers();

        $this->reset('pesan');

        $this->dispatch('pesan-diskusi-terkirim');
    }

    #[On('echo:diskusi-materi.{materi.id},KomentarMateriEvent')]
    public function pesanMasuk(array $event): void
    {
        // Abaikan event yang bukan untuk materi yang sedang dibuka.
        if ((int) ($event['materi_id'] ?? 0) !== (int) $this->materi->id) {
            return;
        }

        // Livewire merender ulang riwayat pesan dari database.
        $this->dispatch('pesan-diskusi-terkirim');
    }

    public function render()
    {
        $diskusis = Diskusi_materi::with('user')
            ->where('materi_id', $this->materi->id)
            ->orderBy('created_at')
            ->get();

        return view(
            'livewire.guru.diskusi.diskusi-materi-guru-manager',
            compact('diskusis')
        )
            ->layout('guru.layouts.app')
            ->title('Diskusi Materi');
    }
}
