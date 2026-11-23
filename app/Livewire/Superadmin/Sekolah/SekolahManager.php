<?php

namespace App\Livewire\SuperAdmin\Sekolah;

use App\Livewire\SuperAdmin\Sekolah\SekolahManager as CurrentComponent;
use App\Models\Sekolah as SekolahModel;
use App\Models\Yayasan;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class SekolahManager extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $editMode = false;
    public $confirmingDelete = false;
    public $confirmingBulkDelete = false;
    public $selectAll = false;
    public $selectedSekolah = [];
    public $sekolahId = null;

    public $yayasan_id = '';
    public $nama_sekolah = '';
    public $kode_sekolah = '';
    public $jenjang = 'SMP';
    public $alamat = '';
    public $email = '';
    public $telepon = '';
    public $is_active = true;

    protected function rules(): array
    {
        return [
            'yayasan_id' => ['required', 'exists:yayasans,id'],
            'nama_sekolah' => ['required', 'string', 'max:255'],
            'kode_sekolah' => [
                'required',
                'string',
                'max:255',
                Rule::unique('sekolahs', 'kode_sekolah')
                    ->ignore($this->sekolahId),
            ],
            'jenjang' => ['required', Rule::in(['SMP', 'SMA', 'SMK'])],
            'alamat' => ['nullable', 'string'],
            'email' => ['nullable', 'email', 'max:255'],
            'telepon' => ['nullable', 'string', 'max:30'],
            'is_active' => ['boolean'],
        ];
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
        $this->selectedSekolah = [];
        $this->selectAll = false;
    }

    public function render()
    {
        $sekolahList = SekolahModel::with('yayasan')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nama_sekolah', 'like', '%' . $this->search . '%')
                        ->orWhere('kode_sekolah', 'like', '%' . $this->search . '%')
                        ->orWhere('jenjang', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.superadmin.sekolah.sekolah-manager', [
            'sekolahList' => $sekolahList,
            'yayasanList' => Yayasan::orderBy('nama_yayasan')->get(),
        ])
            ->layout('superadmin.layouts.app')
            ->title('Manajemen Sekolah');
    }

    public function resetForm(): void
    {
        $this->resetValidation();

        $this->reset([
            'sekolahId',
            'nama_sekolah',
            'kode_sekolah',
            'alamat',
            'email',
            'telepon',
        ]);

        $this->yayasan_id = Yayasan::query()->value('id') ?? '';
        $this->jenjang = 'SMP';
        $this->is_active = true;
        $this->editMode = false;
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $sekolah = SekolahModel::findOrFail($id);

        $this->resetValidation();
        $this->sekolahId = $sekolah->id;
        $this->yayasan_id = $sekolah->yayasan_id;
        $this->nama_sekolah = $sekolah->nama_sekolah;
        $this->kode_sekolah = $sekolah->kode_sekolah;
        $this->jenjang = $sekolah->jenjang;
        $this->alamat = $sekolah->alamat ?? '';
        $this->email = $sekolah->email ?? '';
        $this->telepon = $sekolah->telepon ?? '';
        $this->is_active = (bool) $sekolah->is_active;

        $this->editMode = true;
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate();

        if ($this->editMode && $this->sekolahId) {
            $sekolah = SekolahModel::findOrFail($this->sekolahId);
            $sekolah->update($validated);
            $message = 'Data sekolah berhasil diperbarui.';
        } else {
            SekolahModel::create($validated);
            $message = 'Sekolah berhasil ditambahkan.';
        }

        $this->showModal = false;
        $this->resetPage();
        $this->resetFormState();

        session()->flash('success', $message);
    }

    private function resetFormState(): void
    {
        $this->resetValidation();

        $this->reset([
            'sekolahId',
            'nama_sekolah',
            'kode_sekolah',
            'alamat',
            'email',
            'telepon',
        ]);

        $this->yayasan_id = '';
        $this->jenjang = 'SMP';
        $this->is_active = true;
        $this->editMode = false;
    }

    public function toggleSelectAll(): void
    {
        if ($this->selectAll) {
            $this->selectedSekolah = $this->filteredSchoolIds();
        } else {
            $this->selectedSekolah = [];
        }
    }


    private function filteredSchoolIds(): array
    {
        return SekolahModel::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nama_sekolah', 'like', '%' . $this->search . '%')
                        ->orWhere('kode_sekolah', 'like', '%' . $this->search . '%')
                        ->orWhere('jenjang', 'like', '%' . $this->search . '%');
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
        $this->sekolahId = $id;
        $this->confirmingDelete = true;
    }

    public function deleteSekolahConfirmed(): void
    {
        $sekolah = SekolahModel::findOrFail($this->sekolahId);

        if ($this->hasRelatedData($sekolah->id)) {
            $this->confirmingDelete = false;

            session()->flash(
                'error',
                'Sekolah tidak dapat dihapus karena masih memiliki data terkait.'
            );

            return;
        }

        $sekolah->delete();

        $this->confirmingDelete = false;
        $this->sekolahId = null;
        $this->selectedSekolah = [];
        $this->selectAll = false;

        session()->flash('success', 'Sekolah berhasil dihapus.');
    }

    public function confirmBulkDelete(): void
    {
        if (empty($this->selectedSekolah)) {
            return;
        }

        $this->confirmingBulkDelete = true;
    }

    public function deleteSelectedConfirmed(): void
    {
        $deleted = 0;
        $skipped = 0;

        foreach ($this->selectedSekolah as $id) {
            $sekolah = SekolahModel::find($id);

            if (!$sekolah) {
                continue;
            }

            if ($this->hasRelatedData($sekolah->id)) {
                $skipped++;
                continue;
            }

            $sekolah->delete();
            $deleted++;
        }

        $this->confirmingBulkDelete = false;
        $this->selectedSekolah = [];
        $this->selectAll = false;
        $this->resetPage();

        session()->flash(
            $skipped > 0 ? 'error' : 'success',
            "Berhasil menghapus {$deleted} sekolah. {$skipped} sekolah dilewati karena masih memiliki data terkait."
        );
    }

    private function hasRelatedData(int $sekolahId): bool
    {
        $tables = [
            'user_roles',
            'tahun_ajarans',
            'jurusans',
            'gurus',
            'siswas',
            'mata_pelajarans',
            'pengumumans',
            'ppdb_periods',
        ];

        foreach ($tables as $table) {
            if (DB::table($table)->where('sekolah_id', $sekolahId)->exists()) {
                return true;
            }
        }

        return false;
    }
}
