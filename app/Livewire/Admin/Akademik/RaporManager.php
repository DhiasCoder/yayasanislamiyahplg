<?php

namespace App\Livewire\Admin\Akademik;

use App\Models\Rapor;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class RaporManager extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterStatus = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
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

    public function ubahStatus(int $id): void
    {
        $rapor = Rapor::whereHas('siswa', fn ($q) =>
                $q->where('sekolah_id', $this->sekolahId())
            )
            ->findOrFail($id);

        $rapor->status = $rapor->status === 'draft'
            ? 'published'
            : 'draft';

        $rapor->save();

        session()->flash('success', 'Status rapor berhasil diperbarui.');
    }

    public function render()
    {
        $rapors = Rapor::with(['siswa', 'kelas', 'tahunAjaran'])
            ->whereHas('siswa', fn ($q) =>
                $q->where('sekolah_id', $this->sekolahId())
            )
            ->when($this->filterStatus, fn ($q) =>
                $q->where('status', $this->filterStatus)
            )
            ->when($this->search, function ($q) {
                $search = $this->search;

                $q->whereHas('siswa', fn ($s) =>
                    $s->where('nama_lengkap', 'like', "%{$search}%")
                );
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.akademik.rapor-manager', compact('rapors'));
    }
}
