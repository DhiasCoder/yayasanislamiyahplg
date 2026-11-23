<?php

namespace App\Livewire\SuperAdmin\TahunAjaran;

use App\Models\Sekolah;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class TahunAjaranManager extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $editMode = false;
    public $confirmingDelete = false;
    public $confirmingBulkDelete = false;
    public $selectAll = false;

    public $tahunAjaranId = null;
    public $selectedTahunAjaran = [];

    public $sekolah_id = '';
    public $nama = '';
    public $tanggal_mulai = '';
    public $tanggal_selesai = '';
    public $is_active = false;

    public function updatedSearch(): void
    {
        $this->resetPage();
        $this->selectedTahunAjaran = [];
        $this->selectAll = false;
    }

    public function updatedSelectAll($value): void
    {
        $this->selectedTahunAjaran = $value
            ? $this->filteredTahunAjaranIds()
            : [];
    }

    public function render()
    {
        return view('livewire.superadmin.tahun-ajaran.tahun-ajaran-manager', [
            'tahunAjaranList' => TahunAjaran::with('sekolah')
                ->when($this->search, function ($query) {
                    $query->where(function ($q) {
                        $q->where('nama', 'like', '%' . $this->search . '%')
                            ->orWhereHas('sekolah', function ($sekolahQuery) {
                                $sekolahQuery->where(
                                    'nama_sekolah',
                                    'like',
                                    '%' . $this->search . '%'
                                );
                            });
                    });
                })
                ->latest()
                ->paginate(10),

            'sekolahList' => Sekolah::orderBy('nama_sekolah')->get(),
        ])->layout('superadmin.layouts.app')
            ->title('Manajemen Tahun Ajaran');
    }

    public function resetForm(): void
    {
        $this->resetValidation();
        $this->reset([
            'tahunAjaranId',
            'nama',
            'tanggal_mulai',
            'tanggal_selesai',
        ]);

        $this->sekolah_id = '';
        $this->is_active = false;
        $this->editMode = false;
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $tahun = TahunAjaran::findOrFail($id);

        $this->resetValidation();
        $this->tahunAjaranId = $tahun->id;
        $this->sekolah_id = (string) $tahun->sekolah_id;
        $this->nama = $tahun->nama;
        $this->tanggal_mulai = $tahun->tanggal_mulai?->format('Y-m-d');
        $this->tanggal_selesai = $tahun->tanggal_selesai?->format('Y-m-d');
        $this->is_active = $tahun->is_active;

        $this->editMode = true;
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'sekolah_id' => ['required', 'exists:sekolahs,id'],
            'nama' => ['required', 'string', 'max:100'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => [
                'required',
                'date',
                'after_or_equal:tanggal_mulai',
            ],
            'is_active' => ['boolean'],
        ]);

        DB::transaction(function () use ($validated) {
            if ($validated['is_active']) {
                TahunAjaran::where('sekolah_id', $validated['sekolah_id'])
                    ->where('id', '!=', $this->tahunAjaranId ?? 0)
                    ->update(['is_active' => false]);
            }

            if ($this->editMode) {
                $tahun = TahunAjaran::findOrFail($this->tahunAjaranId);
                $tahun->update($validated);
            } else {
                TahunAjaran::create($validated);
            }
        });

        $message = $this->editMode
            ? 'Tahun ajaran berhasil diperbarui.'
            : 'Tahun ajaran berhasil ditambahkan.';

        $this->showModal = false;
        $this->resetFormState();
        $this->resetPage();

        session()->flash('success', $message);
    }

    private function resetFormState(): void
    {
        $this->resetValidation();
        $this->reset([
            'tahunAjaranId',
            'nama',
            'tanggal_mulai',
            'tanggal_selesai',
        ]);

        $this->sekolah_id = '';
        $this->is_active = false;
        $this->editMode = false;
    }

    private function filteredTahunAjaranIds(): array
    {
        return TahunAjaran::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nama', 'like', '%' . $this->search . '%')
                        ->orWhereHas('sekolah', function ($sq) {
                            $sq->where(
                                'nama_sekolah',
                                'like',
                                '%' . $this->search . '%'
                            );
                        });
                });
            })
            ->latest()
            ->limit(10)
            ->pluck('id')
            ->map(fn($id) => (string) $id)
            ->toArray();
    }

    public function confirmDelete(int $id): void
    {
        $this->tahunAjaranId = $id;
        $this->confirmingDelete = true;
    }

    public function deleteConfirmed(): void
    {
        $tahun = TahunAjaran::findOrFail($this->tahunAjaranId);

        if ($tahun->kelas()->exists()) {
            $this->confirmingDelete = false;

            session()->flash(
                'error',
                'Tahun ajaran tidak dapat dihapus karena masih digunakan oleh kelas.'
            );

            return;
        }

        $tahun->delete();
        $this->confirmingDelete = false;
        $this->tahunAjaranId = null;

        session()->flash('success', 'Tahun ajaran berhasil dihapus.');
    }

    public function confirmBulkDelete(): void
    {
        if (count($this->selectedTahunAjaran) > 0) {
            $this->confirmingBulkDelete = true;
        }
    }

    public function deleteSelectedConfirmed(): void
    {
        $deleted = 0;
        $skipped = 0;

        foreach ($this->selectedTahunAjaran as $id) {
            $tahun = TahunAjaran::find($id);

            if (!$tahun) {
                continue;
            }

            if ($tahun->kelas()->exists()) {
                $skipped++;
                continue;
            }

            $tahun->delete();
            $deleted++;
        }

        $this->confirmingBulkDelete = false;
        $this->selectedTahunAjaran = [];
        $this->selectAll = false;
        $this->resetPage();

        session()->flash(
            $skipped > 0 ? 'error' : 'success',
            "Berhasil menghapus {$deleted} tahun ajaran. {$skipped} data dilewati karena masih digunakan kelas."
        );
    }
}
