<?php

namespace App\Livewire\Admin\MataPelajaran;

use App\Models\MataPelajaran;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class MataPelajaranManager extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'tailwind';

    public string $search = '';

    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public bool $showBulkDeleteModal = false;
    public bool $isEdit = false;

    public ?int $mataPelajaranId = null;
    public ?int $deleteId = null;

    public string $kode = '';
    public string $nama = '';
    public string $kelompok = '';
    public bool $is_active = true;

    public array $selected = [];
    public bool $selectAll = false;

    protected function getSekolahId(): ?int
    {
        $user = auth()->user();

        if (! $user) {
            return null;
        }

        $sekolahId = $user->userRoles()
            ->whereHas('role', fn ($query) =>
                $query->where('nama_role', 'admin')
            )
            ->value('sekolah_id');

        return $sekolahId ? (int) $sekolahId : null;
    }

    protected function mataPelajaranQuery()
    {
        return MataPelajaran::query()
            ->where('sekolah_id', $this->getSekolahId());
    }

    protected function filteredMataPelajaran()
    {
        return $this->mataPelajaranQuery()
            ->when(trim($this->search) !== '', function ($query) {
                $search = trim($this->search);

                $query->where(function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                        ->orWhere('kode', 'like', "%{$search}%")
                        ->orWhere('kelompok', 'like', "%{$search}%");
                });
            });
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
        $this->selected = [];
        $this->selectAll = false;
    }

    protected function rules(): array
    {
        return [
            'kode' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('mata_pelajarans', 'kode')
                    ->where('sekolah_id', $this->getSekolahId())
                    ->ignore($this->mataPelajaranId),
            ],
            'nama' => ['required', 'string', 'max:255'],
            'kelompok' => ['nullable', 'string', 'max:255'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    protected function messages(): array
    {
        return [
            'kode.unique' => 'Kode mata pelajaran sudah digunakan di sekolah ini.',
            'nama.required' => 'Nama mata pelajaran wajib diisi.',
        ];
    }

    public function create(): void
    {
        $this->resetForm();

        $this->isEdit = false;
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $mataPelajaran = $this->mataPelajaranQuery()->findOrFail($id);

        $this->mataPelajaranId = $mataPelajaran->id;
        $this->kode = $mataPelajaran->kode ?? '';
        $this->nama = $mataPelajaran->nama;
        $this->kelompok = $mataPelajaran->kelompok ?? '';
        $this->is_active = $mataPelajaran->is_active;

        $this->isEdit = true;
        $this->showModal = true;
    }

    public function save(): void
    {
        $sekolahId = $this->getSekolahId();

        if (! $sekolahId) {
            $this->addError('form', 'Sekolah untuk akun Admin tidak ditemukan.');
            return;
        }

        $validated = $this->validate();

        $data = [
            'sekolah_id' => $sekolahId,
            'kode' => filled($validated['kode'] ?? null)
                ? trim($validated['kode'])
                : null,
            'nama' => trim($validated['nama']),
            'kelompok' => filled($validated['kelompok'] ?? null)
                ? trim($validated['kelompok'])
                : null,
            'is_active' => $validated['is_active'],
        ];

        if ($this->isEdit) {
            $mataPelajaran = $this->mataPelajaranQuery()
                ->findOrFail($this->mataPelajaranId);

            $mataPelajaran->update($data);
        } else {
            MataPelajaran::create($data);
        }

        $message = $this->isEdit
            ? 'Mata pelajaran berhasil diperbarui.'
            : 'Mata pelajaran berhasil ditambahkan.';

        $this->showModal = false;
        $this->resetForm();

        session()->flash('success', $message);
    }

    public function confirmDelete(int $id): void
    {
        $this->mataPelajaranQuery()->findOrFail($id);

        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        $mataPelajaran = $this->mataPelajaranQuery()
            ->findOrFail($this->deleteId);

        if ($mataPelajaran->pengajarans()->exists()) {
            $this->showDeleteModal = false;

            session()->flash(
                'error',
                'Mata pelajaran tidak dapat dihapus karena masih digunakan dalam penugasan guru.'
            );

            return;
        }

        $mataPelajaran->delete();

        $this->showDeleteModal = false;
        $this->deleteId = null;

        session()->flash('success', 'Mata pelajaran berhasil dihapus.');
    }

    public function updatedSelectAll(bool $value): void
    {
        $this->selected = $value
            ? $this->filteredMataPelajaran()
                ->pluck('id')
                ->map(fn ($id) => (string) $id)
                ->toArray()
            : [];
    }

    public function confirmBulkDelete(): void
    {
        if (! empty($this->selected)) {
            $this->showBulkDeleteModal = true;
        }
    }

    public function bulkDelete(): void
    {
        $ids = collect($this->selected)
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $mataPelajarans = $this->mataPelajaranQuery()
            ->whereIn('id', $ids)
            ->get();

        $deleted = 0;
        $skipped = 0;

        foreach ($mataPelajarans as $mataPelajaran) {
            if ($mataPelajaran->pengajarans()->exists()) {
                $skipped++;
                continue;
            }

            $mataPelajaran->delete();
            $deleted++;
        }

        $this->selected = [];
        $this->selectAll = false;
        $this->showBulkDeleteModal = false;

        if ($skipped > 0) {
            session()->flash(
                'error',
                "{$deleted} mata pelajaran dihapus. {$skipped} dilewati karena masih digunakan dalam penugasan guru."
            );
        } else {
            session()->flash(
                'success',
                "{$deleted} mata pelajaran berhasil dihapus."
            );
        }
    }

    protected function resetForm(): void
    {
        $this->resetValidation();

        $this->mataPelajaranId = null;
        $this->kode = '';
        $this->nama = '';
        $this->kelompok = '';
        $this->is_active = true;
    }

    public function render()
    {
        return view('livewire.admin.mata-pelajaran.mata-pelajaran-manager', [
            'mataPelajarans' => $this->filteredMataPelajaran()
                ->withCount('pengajarans')
                ->latest()
                ->paginate(10),

            'totalMataPelajaran' => $this->mataPelajaranQuery()->count(),

            'mataPelajaranAktif' => $this->mataPelajaranQuery()
                ->where('is_active', true)
                ->count(),
        ])
            ->layout('admin.layouts.app')
            ->title('Manajemen Mata Pelajaran');
    }
}
