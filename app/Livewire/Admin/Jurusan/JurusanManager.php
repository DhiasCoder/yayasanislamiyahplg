<?php

namespace App\Livewire\Admin\Jurusan;

use App\Models\Jurusan;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class JurusanManager extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'tailwind';

    public string $search = '';

    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public bool $showBulkDeleteModal = false;
    public bool $isEdit = false;

    public ?int $jurusanId = null;
    public ?int $deleteId = null;

    public string $kode = '';
    public string $nama_jurusan = '';
    public string $deskripsi = '';
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

    protected function jurusanQuery()
    {
        $sekolahId = $this->getSekolahId();

        return Jurusan::query()
            ->where('sekolah_id', $sekolahId);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    protected function rules(): array
    {
        $sekolahId = $this->getSekolahId();

        return [
            'kode' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('jurusans', 'kode')
                    ->where('sekolah_id', $sekolahId)
                    ->ignore($this->jurusanId),
            ],
            'nama_jurusan' => [
                'required',
                'string',
                'max:255',
            ],
            'deskripsi' => [
                'nullable',
                'string',
            ],
            'is_active' => [
                'required',
                'boolean',
            ],
        ];
    }

    protected function messages(): array
    {
        return [
            'kode.unique' => 'Kode jurusan sudah digunakan di sekolah ini.',
            'nama_jurusan.required' => 'Nama jurusan wajib diisi.',
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
        $jurusan = $this->jurusanQuery()->findOrFail($id);

        $this->jurusanId = $jurusan->id;
        $this->kode = $jurusan->kode ?? '';
        $this->nama_jurusan = $jurusan->nama_jurusan;
        $this->deskripsi = $jurusan->deskripsi ?? '';
        $this->is_active = $jurusan->is_active;

        $this->isEdit = true;
        $this->showModal = true;
    }

    public function save(): void
    {
        if (! $this->getSekolahId()) {
            $this->addError('form', 'Sekolah untuk akun Admin tidak ditemukan.');
            return;
        }

        $validated = $this->validate();

        $data = [
            'kode' => filled($validated['kode'] ?? null)
                ? trim($validated['kode'])
                : null,
            'nama_jurusan' => trim($validated['nama_jurusan']),
            'deskripsi' => filled($validated['deskripsi'] ?? null)
                ? trim($validated['deskripsi'])
                : null,
            'is_active' => $validated['is_active'],
            'sekolah_id' => $this->getSekolahId(),
        ];

        if ($this->isEdit) {
            $jurusan = $this->jurusanQuery()
                ->findOrFail($this->jurusanId);

            $jurusan->update($data);
        } else {
            Jurusan::create($data);
        }

        $this->showModal = false;
        $this->resetForm();

        session()->flash(
            'success',
            $this->isEdit
                ? 'Jurusan berhasil diperbarui.'
                : 'Jurusan berhasil ditambahkan.'
        );
    }

    public function confirmDelete(int $id): void
    {
        $this->jurusanQuery()->findOrFail($id);

        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        $jurusan = $this->jurusanQuery()->findOrFail($this->deleteId);

        if ($jurusan->kelas()->exists()) {
            $this->showDeleteModal = false;

            session()->flash(
                'error',
                'Jurusan tidak dapat dihapus karena masih digunakan oleh kelas.'
            );

            return;
        }

        $jurusan->delete();

        $this->showDeleteModal = false;
        $this->deleteId = null;

        session()->flash('success', 'Jurusan berhasil dihapus.');
    }

    public function updatedSelectAll(bool $value): void
    {
        $this->selected = $value
            ? $this->filteredJurusan()->pluck('id')
                ->map(fn ($id) => (string) $id)
                ->toArray()
            : [];
    }

    public function confirmBulkDelete(): void
    {
        $this->showBulkDeleteModal = true;
    }

    public function bulkDelete(): void
    {
        if (empty($this->selected)) {
            $this->showBulkDeleteModal = false;
            return;
        }

        $ids = collect($this->selected)
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $jurusans = $this->jurusanQuery()
            ->whereIn('id', $ids)
            ->get();

        $deleted = 0;
        $skipped = 0;

        foreach ($jurusans as $jurusan) {
            if ($jurusan->kelas()->exists()) {
                $skipped++;
                continue;
            }

            $jurusan->delete();
            $deleted++;
        }

        $this->selected = [];
        $this->selectAll = false;
        $this->showBulkDeleteModal = false;

        if ($skipped > 0) {
            session()->flash(
                'error',
                "{$deleted} jurusan dihapus. {$skipped} jurusan dilewati karena masih digunakan oleh kelas."
            );
        } else {
            session()->flash(
                'success',
                "{$deleted} jurusan berhasil dihapus."
            );
        }
    }

    protected function filteredJurusan()
    {
        return $this->jurusanQuery()
            ->when(trim($this->search) !== '', function ($query) {
                $search = trim($this->search);

                $query->where(function ($q) use ($search) {
                    $q->where('nama_jurusan', 'like', "%{$search}%")
                        ->orWhere('kode', 'like', "%{$search}%")
                        ->orWhere('deskripsi', 'like', "%{$search}%");
                });
            });
    }

    protected function resetForm(): void
    {
        $this->resetValidation();

        $this->jurusanId = null;
        $this->kode = '';
        $this->nama_jurusan = '';
        $this->deskripsi = '';
        $this->is_active = true;
    }

    public function render()
    {
        return view('livewire.admin.jurusan.jurusan-manager', [
            'jurusans' => $this->filteredJurusan()
                ->withCount('kelas')
                ->latest()
                ->paginate(10),

            'totalJurusan' => $this->jurusanQuery()->count(),
            'jurusanAktif' => $this->jurusanQuery()
                ->where('is_active', true)
                ->count(),
        ])
            ->layout('admin.layouts.app')
            ->title('Manajemen Jurusan');
    }
}
