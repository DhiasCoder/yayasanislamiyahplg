<?php

namespace App\Livewire\Admin\TahunAjaran;

use App\Models\TahunAjaran;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class TahunAjaranManagers extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showForm = false;
    public ?int $tahunAjaranId = null;

    public string $nama = '';
    public string $tanggal_mulai = '';
    public string $tanggal_selesai = '';
    public bool $is_active = false;

    public array $selected = [];
    public bool $selectAll = false;
    public bool $showDeleteModal = false;
    public ?int $deleteId = null;
    public bool $showBulkDeleteModal = false;

    protected string $paginationTheme = 'tailwind';

    private function sekolahId(): int
    {
        $sekolahId = Auth::user()
            ->userRoles()
            ->whereHas('role', fn($q) => $q->where('nama_role', 'admin'))
            ->value('sekolah_id');

        abort_unless($sekolahId, 403, 'Sekolah admin tidak ditemukan.');

        return (int) $sekolahId;
    }

    private function queryTahunAjaran()
    {
        return TahunAjaran::query()
            ->where('sekolah_id', $this->sekolahId());
    }

    private function currentPageIds(): array
    {
        return $this->queryTahunAjaran()
            ->when($this->search, function ($query) {
                $query->where('nama', 'like', '\%' . $this->search . '%');
            })
            ->orderByDesc('tanggal_mulai')
            ->paginate(10)
            ->pluck('id')
            ->map(fn($id) => (string)$id)
            ->toArray();
    }

    public function updatingSearch(): void
    {
        $this->reset(['selected', 'selectAll']);
        $this->resetPage();
    }

    public function updatingPage(): void
    {
        $this->reset(['selected', 'selectAll']);
    }

    public function updatedSelectAll(bool $value): void
    {
        $this->selected = $value ? $this->currentPageIds() : [];
    }

    public function updatedSelected(): void
    {
        $pageIds = $this->currentPageIds();
        $selectedStrings = array_map('strval', $this->selected);

        $this->selectAll = count($pageIds) > 0
            && count(array_diff($pageIds, $selectedStrings)) === 0;
    }

    public function resetForm(): void
    {
        $this->reset([
            'tahunAjaranId',
            'nama',
            'tanggal_mulai',
            'tanggal_selesai',
            'is_active',
        ]);

        $this->is_active = false;
        $this->resetValidation();
        $this->showForm = false;
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $tahun = $this->queryTahunAjaran()->findOrFail($id);

        $this->tahunAjaranId = $tahun->id;
        $this->nama = $tahun->nama;
        $this->tanggal_mulai = $tahun->tanggal_mulai->format('Y-m-d');
        $this->tanggal_selesai = $tahun->tanggal_selesai->format('Y-m-d');
        $this->is_active = (bool)$tahun->is_active;

        $this->resetValidation();
        $this->showForm = true;
    }

    public function save(): void
    {
        $sekolahId = $this->sekolahId();

        $validated = $this->validate([
            'nama' => [
                'required',
                'string',
                'max:50',
                Rule::unique('tahun_ajarans', 'nama')
                    ->where('sekolah_id', $sekolahId)
                    ->ignore($this->tahunAjaranId),
            ],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => [
                'required',
                'date',
                'after_or_equal:tanggal_mulai',
            ],
            'is_active' => ['boolean'],
        ], [
            'nama.required' => 'Nama tahun ajaran wajib diisi.',
            'nama.unique' => 'Nama tahun ajaran sudah digunakan di sekolah ini.',
            'tanggal_mulai.required' => 'Tanggal mulai wajib diisi.',
            'tanggal_selesai.required' => 'Tanggal selesai wajib diisi.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai.',
        ]);

        DB::transaction(function () use ($validated, $sekolahId) {
            if ($validated['is_active']) {
                $this->queryTahunAjaran()->update([
                    'is_active' => false,
                ]);
            }

            $data = [
                'nama' => $validated['nama'],
                'tanggal_mulai' => $validated['tanggal_mulai'],
                'tanggal_selesai' => $validated['tanggal_selesai'],
                'is_active' => $validated['is_active'],
            ];

            if ($this->tahunAjaranId) {
                $tahun = $this->queryTahunAjaran()
                    ->findOrFail($this->tahunAjaranId);

                $tahun->update($data);
            } else {
                TahunAjaran::create([
                    ...$data,
                    'sekolah_id' => $sekolahId,
                ]);
            }
        });

        $this->resetForm();

        session()->flash('success', 'Tahun ajaran berhasil disimpan.');
    }

    public function activate(int $id): void
    {
        DB::transaction(function () use ($id) {
            $tahun = $this->queryTahunAjaran()->findOrFail($id);

            $this->queryTahunAjaran()->update([
                'is_active' => false,
            ]);

            $tahun->update(['is_active' => true]);
        });

        session()->flash('success', 'Tahun ajaran berhasil diaktifkan.');
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function delete(?int $id = null): void
    {
        $targetId = $id ?? $this->deleteId;

        if (!$targetId) {
            $this->showDeleteModal = false;
            return;
        }

        $tahun = $this->queryTahunAjaran()->findOrFail($targetId);

        $this->reset(['deleteId', 'showDeleteModal']);

        if ($tahun->kelas()->exists()) {
            session()->flash(
                'error',
                'Tahun ajaran tidak dapat dihapus karena sudah digunakan oleh kelas.'
            );

            return;
        }

        $tahun->delete();

        $this->selected = array_values(
            array_filter($this->selected, fn($item) => (int) $item !== (int)$targetId)
        );
        $this->updatedSelected();

        session()->flash('success', 'Tahun ajaran berhasil dihapus.');
    }

    public function confirmBulkDelete(): void
    {
        if (empty($this->selected)) {
            session()->flash('error', 'Pilih minimal satu tahun ajaran terlebih dahulu.');
            return;
        }

        $this->showBulkDeleteModal = true;
    }

    public function deleteSelected(): void
    {
        if (empty($this->selected)) {
            $this->showBulkDeleteModal = false;
            return;
        }

        $items = $this->queryTahunAjaran()
            ->whereIn('id', $this->selected)
            ->withCount('kelas')
            ->get();

        $berhasil = 0;
        $dilewati = 0;

        foreach ($items as $tahun) {
            if ($tahun->kelas_count > 0) {
                $dilewati++;
                continue;
            }

            $tahun->delete();
            $berhasil++;
        }

        $this->reset(['selected', 'selectAll', 'showBulkDeleteModal']);

        if ($berhasil > 0 && $dilewati > 0) {
            session()->flash(
                'success',
                "{$berhasil} tahun ajaran berhasil dihapus, {$dilewati} dilewati karena sudah digunakan oleh kelas."
            );
        } elseif ($berhasil > 0) {
            session()->flash('success', "{$berhasil} tahun ajaran berhasil dihapus.");
        } else {
            session()->flash(
                'error',
                'Tahun ajaran terpilih tidak dapat dihapus karena sudah digunakan oleh kelas.'
            );
        }
    }

    public function render()
    {
        $tahunAjarans = $this->queryTahunAjaran()
            ->when($this->search, function ($query) {
                $query->where('nama', 'like', '\%' . $this->search . '%');
            })
            ->withCount('kelas')
            ->orderByDesc('tanggal_mulai')
            ->paginate(10);

        return view('livewire.admin.tahun-ajaran.tahun-ajaran-managers', [
            'tahunAjarans' => $tahunAjarans,
            'totalTahunAjaran' => $this->queryTahunAjaran()->count(),
            'tahunAktif' => $this->queryTahunAjaran()
                ->where('is_active', true)
                ->count(),
        ])->layout('admin.layouts.app');
    }
}
