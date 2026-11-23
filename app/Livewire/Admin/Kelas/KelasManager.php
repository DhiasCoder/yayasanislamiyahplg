<?php

namespace App\Livewire\Admin\Kelas;

use App\Models\Guru;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class KelasManager extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $showDeleteModal = false;
    public $showBulkDeleteModal = false;
    public $isEdit = false;

    public $kelasId = null;
    public $tahun_ajaran_id = '';
    public $jurusan_id = '';
    public $wali_kelas_id = '';
    public $nama_kelas = '';
    public $tingkat = '';
    public $is_active = true;

    public $deleteId = null;
    public $selected = [];
    public $selectAll = false;

    protected function getSekolahId(): ?int
    {
        $user = Auth::user();

        if (!$user) {
            return null;
        }

        return $user->userRoles()
            ->whereHas('role', fn ($query) =>
                $query->where('nama_role', 'admin')
            )
            ->value('sekolah_id');
    }

    protected function kelasQuery()
    {
        $sekolahId = $this->getSekolahId();

        if (!$sekolahId) {
            return Kelas::query()->whereRaw('1 = 0');
        }

        return Kelas::query()->where('sekolah_id', $sekolahId);
    }

    protected function rules(): array
    {
        $sekolahId = $this->getSekolahId();

        return [
            'tahun_ajaran_id' => [
                'required',
                'integer',
                Rule::exists('tahun_ajarans', 'id')
                    ->where('sekolah_id', $sekolahId),
            ],
            'jurusan_id' => [
                'nullable',
                'integer',
                Rule::exists('jurusans', 'id')
                    ->where('sekolah_id', $sekolahId),
            ],
            'wali_kelas_id' => [
                'nullable',
                'integer',
                Rule::exists('gurus', 'id')
                    ->where('sekolah_id', $sekolahId),
            ],
            'nama_kelas' => [
                'required',
                'string',
                'max:100',
                Rule::unique('kelas', 'nama_kelas')
                    ->where('sekolah_id', $sekolahId)
                    ->where('tahun_ajaran_id', $this->tahun_ajaran_id)
                    ->ignore($this->kelasId),
            ],
            'tingkat' => [
                'required',
                Rule::in(['10', '11', '12', '7', '8', '9']),
            ],
            'is_active' => ['boolean'],
        ];
    }

    protected function messages(): array
    {
        return [
            'tahun_ajaran_id.required' => 'Tahun ajaran wajib dipilih.',
            'jurusan_id.exists' => 'Jurusan tidak valid untuk sekolah ini.',
            'wali_kelas_id.exists' => 'Wali kelas tidak valid untuk sekolah ini.',
            'nama_kelas.unique' => 'Nama kelas sudah digunakan pada tahun ajaran tersebut.',
            'tingkat.required' => 'Tingkat kelas wajib dipilih.',
            'tingkat.in' => 'Tingkat kelas tidak valid.',
        ];
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
        $this->selected = [];
        $this->selectAll = false;
    }

    public function updatedSelectAll($value): void
    {
        if (!$value) {
            $this->selected = [];
            return;
        }

        $this->selected = $this->filteredKelasQuery()
            ->orderBy('nama_kelas')
            ->paginate(10)
            ->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->toArray();
    }

    protected function filteredKelasQuery()
    {
        return $this->kelasQuery()
            ->with(['tahunAjaran', 'jurusan', 'waliKelas'])
            ->withCount('siswa')
            ->when(trim($this->search) !== '', function ($query) {
                $search = trim($this->search);

                $query->where(function ($q) use ($search) {
                    $q->where('nama_kelas', 'like', "%{$search}%")
                        ->orWhereHas('tahunAjaran', fn ($ta) =>
                            $ta->where('nama', 'like', "%{$search}%")
                        )
                        ->orWhereHas('jurusan', fn ($jurusan) =>
                            $jurusan->where('nama_jurusan', 'like', "%{$search}%")
                        )
                        ->orWhereHas('waliKelas', fn ($guru) =>
                            $guru->where('nama_lengkap', 'like', "%{$search}%")
                        );
                });
            });
    }

    public function create(): void
    {
        $this->resetForm();

        $this->isEdit = false;

        // Gunakan tahun ajaran aktif jika tersedia.
        $tahunAktif = $this->tahunAjaranQuery()
            ->where('is_active', true)
            ->first();

        $this->tahun_ajaran_id = $tahunAktif
            ? (string) $tahunAktif->id
            : '';

        $this->showModal = true;
    }

    public function edit($id): void
    {
        $kelas = $this->kelasQuery()->findOrFail($id);

        $this->resetForm();

        $this->kelasId = $kelas->id;
        $this->tahun_ajaran_id = (string) $kelas->tahun_ajaran_id;
        $this->jurusan_id = $kelas->jurusan_id
            ? (string) $kelas->jurusan_id : '';
        $this->wali_kelas_id = $kelas->wali_kelas_id
            ? (string) $kelas->wali_kelas_id : '';
        $this->nama_kelas = $kelas->nama_kelas;
        $this->tingkat = (string) $kelas->tingkat;
        $this->is_active = (bool) $kelas->is_active;

        $this->isEdit = true;
        $this->showModal = true;
    }

    public function save(): void
    {
        $sekolahId = $this->getSekolahId();

        if (!$sekolahId) {
            session()->flash('error', 'Sekolah untuk akun Admin tidak ditemukan.');
            return;
        }

        $validated = $this->validate();

        $data = [
            ...$validated,
            'sekolah_id' => $sekolahId,
            'jurusan_id' => $validated['jurusan_id'] ?: null,
            'wali_kelas_id' => $validated['wali_kelas_id'] ?: null,
            'is_active' => (bool) $validated['is_active'],
        ];

        if ($this->isEdit) {
            $this->kelasQuery()
                ->findOrFail($this->kelasId)
                ->update($data);

            $message = 'Data kelas berhasil diperbarui.';
        } else {
            Kelas::create($data);

            $message = 'Kelas berhasil ditambahkan.';
        }

        session()->flash('success', $message);

        $this->showModal = false;
        $this->resetForm();
        $this->selected = [];
        $this->selectAll = false;
        $this->resetPage();
    }

    protected function tahunAjaranQuery()
    {
        $sekolahId = $this->getSekolahId();

        return TahunAjaran::query()
            ->where('sekolah_id', $sekolahId);
    }

    protected function jurusanQuery()
    {
        $sekolahId = $this->getSekolahId();

        return Jurusan::query()
            ->where('sekolah_id', $sekolahId);
    }

    protected function guruQuery()
    {
        $sekolahId = $this->getSekolahId();

        return Guru::query()
            ->where('sekolah_id', $sekolahId);
    }

    public function confirmDelete($id): void
    {
        $this->kelasQuery()->findOrFail($id);

        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    protected function canDelete(Kelas $kelas): bool
    {
        return !$kelas->siswa()->exists()
            && !$kelas->pengajarans()->exists();
    }

    public function delete(): void
    {
        $kelas = $this->kelasQuery()->findOrFail($this->deleteId);

        if (!$this->canDelete($kelas)) {
            session()->flash(
                'error',
                'Kelas tidak dapat dihapus karena masih memiliki siswa atau penugasan mengajar.'
            );
        } else {
            $kelas->delete();

            session()->flash('success', 'Data kelas berhasil dihapus.');
        }

        $this->showDeleteModal = false;
        $this->deleteId = null;
        $this->selected = [];
        $this->selectAll = false;
        $this->resetPage();
    }

    public function confirmBulkDelete(): void
    {
        if (empty($this->selected)) {
            session()->flash('error', 'Pilih setidaknya satu kelas.');
            return;
        }

        $this->showBulkDeleteModal = true;
    }

    public function deleteSelected(): void
    {
        $deleted = 0;
        $skipped = 0;

        $kelasList = $this->kelasQuery()
            ->whereIn('id', $this->selected)
            ->get();

        DB::transaction(function () use ($kelasList, &$deleted, &$skipped) {
            foreach ($kelasList as $kelas) {
                if (!$this->canDelete($kelas)) {
                    $skipped++;
                    continue;
                }

                $kelas->delete();
                $deleted++;
            }
        });

        if ($deleted > 0 && $skipped > 0) {
            session()->flash(
                'success',
                "{$deleted} kelas dihapus; {$skipped} kelas dilewati karena masih digunakan."
            );
        } elseif ($deleted > 0) {
            session()->flash('success', "{$deleted} kelas berhasil dihapus.");
        } else {
            session()->flash(
                'error',
                'Tidak ada kelas yang dihapus karena masih digunakan.'
            );
        }

        $this->showBulkDeleteModal = false;
        $this->selected = [];
        $this->selectAll = false;
        $this->resetPage();
    }

    protected function resetForm(): void
    {
        $this->reset([
            'kelasId',
            'tahun_ajaran_id',
            'jurusan_id',
            'wali_kelas_id',
            'nama_kelas',
            'tingkat',
        ]);

        $this->is_active = true;
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.admin.kelas.kelas-manager', [
            'kelasList' => $this->filteredKelasQuery()
                ->orderBy('nama_kelas')
                ->paginate(10),

            'tahunAjaranList' => $this->tahunAjaranQuery()
                ->orderByDesc('tanggal_mulai')
                ->get(),

            'jurusanList' => $this->jurusanQuery()
                ->orderBy('nama_jurusan')
                ->get(),

            'guruList' => $this->guruQuery()
                ->orderBy('nama_lengkap')
                ->get(),
        ])
            ->layout('admin.layouts.app')
            ->title('Manajemen Kelas');
    }
}
