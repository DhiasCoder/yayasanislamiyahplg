<?php

namespace App\Livewire\Admin\Absensi;
use App\Models\Absensi;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class AbsensiManager extends Component
{
    use WithPagination;

    public $search = '';
    public $tanggal = '';
    public $status = '';

    protected $paginationTheme = 'tailwind';

    protected function sekolahId()
    {
        return Auth::user()
            ->userRoles()
            ->whereHas('role', fn ($q) =>
                $q->where('nama_role', 'admin')
            )
            ->value('sekolah_id');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingTanggal()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function resetFilter()
    {
        $this->reset(['search', 'tanggal', 'status']);
        $this->resetPage();
    }

    public function render()
    {
        $sekolahId = $this->sekolahId();

        $query = Absensi::query()
            ->with([
                'pengajaran.kelas',
                'pengajaran.mataPelajaran',
            ])
            ->whereHas('pengajaran.kelas', fn ($q) =>
                $q->where('sekolah_id', $sekolahId)
            )
            ->when($this->tanggal, fn ($q) =>
                $q->whereDate('tanggal', $this->tanggal)
            )
            ->when($this->search, function ($q) {
                $search = $this->search;

                $q->whereHas('pengajaran.kelas', fn ($kelas) =>
                    $kelas->where('nama_kelas', 'like', "%{$search}%")
                )->orWhereHas('pengajaran.mataPelajaran', fn ($mapel) =>
                    $mapel->where('nama_mata_pelajaran', 'like', "%{$search}%")
                );
            })
            ->latest('tanggal');

        $absensis = $query->paginate(10);

        return view('livewire.admin.absensi.absensi-manager', [
            'absensis' => $absensis,
        ])->layout('admin.layouts.app');
    }
}