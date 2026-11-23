<?php

namespace App\Livewire\Admin\Diskusi;

use App\Models\Diskusi_materi;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class DiskusiMateriManager extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedDiskusi = [];
    public $selectAll = false;

    public $confirmingDelete = false;
    public $diskusiIdToDelete = null;

    public $showModalDiskusi = false;
    public $currentDiskusi = null;

    public function updatedSearch()
    {
        $this->resetPage();
        $this->selectedDiskusi = [];
        $this->selectAll = false;
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

    private function queryDiskusi()
    {
        return Diskusi_materi::query()
            ->whereHas('materi.pengajaran.kelas', function ($q) {
                $q->where('sekolah_id', $this->sekolahId());
            });
    }

    public function showDiskusi($id)
    {
        $this->currentDiskusi = $this->queryDiskusi()
            ->with([
                'user',
                'materi.pengajaran.guru',
                'materi.pengajaran.mataPelajaran',
                'materi.pengajaran.kelas',
            ])
            ->findOrFail($id);

        $this->showModalDiskusi = true;
    }

    public function confirmDelete($id)
    {
        $this->queryDiskusi()->findOrFail($id);

        $this->diskusiIdToDelete = $id;
        $this->confirmingDelete = true;
    }

    public function deleteDiskusiConfirmed()
    {
        $diskusi = $this->queryDiskusi()
            ->findOrFail($this->diskusiIdToDelete);

        $this->deleteBesertaBalasan($diskusi);

        $this->confirmingDelete = false;
        $this->diskusiIdToDelete = null;
        $this->selectedDiskusi = [];
        $this->selectAll = false;

        session()->flash('success', 'Diskusi dan balasannya berhasil dihapus.');
    }

    private function deleteBesertaBalasan(Diskusi_materi $diskusi)
    {
        // Kumpulkan seluruh balasan secara bertingkat.
        $ids = [$diskusi->id];
        $parentIds = [$diskusi->id];

        while (!empty($parentIds)) {
            $childIds = $this->queryDiskusi()
                ->whereIn('parent_id', $parentIds)
                ->pluck('id')
                ->all();

            $childIds = array_values(array_diff($childIds, $ids));

            if (empty($childIds)) {
                break;
            }

            $ids = array_merge($ids, $childIds);
            $parentIds = $childIds;
        }

        // Hapus dari balasan terdalam menuju komentar utama.
        foreach (array_reverse($ids) as $id) {
            $this->queryDiskusi()->whereKey($id)->delete();
        }
    }

    public function toggleSelectAll()
    {
        if ($this->selectAll) {
            $this->selectedDiskusi = $this->queryDiskusi()
                ->where(function ($q) {
                    $q->where('isi', 'like', '%' . $this->search . '%')
                        ->orWhereHas('user', function ($user) {
                            $user->where('name', 'like', '%' . $this->search . '%');
                        })
                        ->orWhereHas('materi', function ($materi) {
                            $materi->where('judul', 'like', '%' . $this->search . '%');
                        });
                })
                ->pluck('id')
                ->map(fn ($id) => (string) $id)
                ->toArray();
        } else {
            $this->selectedDiskusi = [];
        }
    }

    public function deleteSelected()
    {
        $ids = collect($this->selectedDiskusi)
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $diskusis = $this->queryDiskusi()
            ->whereIn('id', $ids)
            ->get();

        foreach ($diskusis as $diskusi) {
            // Balasan mungkin sudah ikut terhapus bersama induknya.
            if ($this->queryDiskusi()->whereKey($diskusi->id)->exists()) {
                $this->deleteBesertaBalasan($diskusi);
            }
        }

        $this->selectedDiskusi = [];
        $this->selectAll = false;

        session()->flash('success', 'Diskusi terpilih berhasil dihapus beserta balasannya.');
    }

   protected function getListeners(): array
    {
        $sekolahId = $this->sekolahId();

        return [
            "echo:admin-diskusi.{$sekolahId},.KomentarMateriEvent" => '$refresh',
        ];
    }

    public function render()
    {
        $diskusis = $this->queryDiskusi()
            ->with([
                'user',
                'materi',
                'materi.pengajaran.kelas',
                'materi.pengajaran.mataPelajaran',
            ])
            ->where(function ($q) {
                $q->where('isi', 'like', '%' . $this->search . '%')
                    ->orWhereHas('user', function ($user) {
                        $user->where('name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('materi', function ($materi) {
                        $materi->where('judul', 'like', '%' . $this->search . '%');
                    });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.diskusi.diskusi-materi-manager', [
            'diskusis' => $diskusis,
            'totalDiskusi' => $this->queryDiskusi()->count(),
            'totalMateriDiskusi' => $this->queryDiskusi()
                ->distinct('materi_id')
                ->count('materi_id'),
        ])->layout('admin.layouts.app');
    }
}
