<?php

namespace App\Livewire\Admin\Akademik;

use App\Models\Nilai;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class NilaiManager extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    private function sekolahId()
    {
        return Auth::user()
            ->userRoles()
            ->whereHas('role', fn ($q) =>
                $q->where('nama_role', 'admin')
            )
            ->value('sekolah_id');
    }

    public function hapus(int $id): void
    {
        $sekolahId = $this->sekolahId();

        $nilai = Nilai::whereHas('siswa', fn ($q) =>
                $q->where('sekolah_id', $sekolahId)
            )
            ->findOrFail($id);

        $nilai->delete();

        session()->flash('success', 'Data nilai berhasil dihapus.');
    }

    public function render()
    {
        $sekolahId = $this->sekolahId();

        $nilais = Nilai::with([
                'siswa',
                'pengajaran.mataPelajaran',
                'tahunAjaran',
            ])
            ->whereHas('siswa', fn ($q) =>
                $q->where('sekolah_id', $sekolahId)
            )
            ->when($this->search, function ($q) {
                $search = $this->search;

                $q->where(function ($sub) use ($search) {
                    $sub->whereHas('siswa', fn ($s) =>
                        $s->where('nama_lengkap', 'like', "%{$search}%")
                    )->orWhereHas('pengajaran.mataPelajaran', fn ($m) =>
                        $m->where('nama_mata_pelajaran', 'like', "%{$search}%")
                    );
                });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.akademik.nilai-manager', compact('nilais'));
    }
}
