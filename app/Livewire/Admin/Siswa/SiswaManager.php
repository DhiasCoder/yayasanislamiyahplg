<?php

namespace App\Livewire\Admin\Siswa;

use App\Models\Siswa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class SiswaManager extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $showDeleteModal = false;
    public $showBulkDeleteModal = false;
    public $isEdit = false;
    public $siswaId = null;

    public $nama_lengkap = '';
    public $nis = '';
    public $nisn = '';
    public $nik = '';
    public $jenis_kelamin = '';
    public $tempat_lahir = '';
    public $tanggal_lahir = '';
    public $alamat = '';
    public $agama = '';
    public $no_hp = '';
    public $nama_ayah = '';
    public $nama_ibu = '';
    public $no_hp_orang_tua = '';
    public $tahun_masuk = '';
    public $status = 'aktif';

    public $selected = [];
    public $selectAll = false;
    public $deleteId = null;

    protected function getSekolahId(): ?int
    {
        $user = Auth::user();

        if (!$user) {
            return null;
        }

        return $user->userRoles()
            ->whereHas('role', function ($query) {
                $query->where('nama_role', 'admin');
            })
            ->value('sekolah_id');
    }

    protected function siswaQuery()
    {
        $sekolahId = $this->getSekolahId();

        if (!$sekolahId) {
            return Siswa::query()->whereRaw('1 = 0');
        }

        return Siswa::query()
            ->where('sekolah_id', $sekolahId);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
        $this->selected = [];
        $this->selectAll = false;
    }

    public function updatedSelectAll($value): void
    {
        $this->selected = $value
            ? $this->siswaQuery()
                ->where(function ($query) {
                    $search = trim($this->search);

                    if ($search !== '') {
                        $query->where('nama_lengkap', 'like', "%{$search}%")
                            ->orWhere('nis', 'like', "%{$search}%")
                            ->orWhere('nisn', 'like', "%{$search}%")
                            ->orWhere('nik', 'like', "%{$search}%");
                    }
                })
                ->latest()
                ->paginate(10)
                ->pluck('id')
                ->map(fn ($id) => (string) $id)
                ->toArray()
            : [];
    }

    protected function rules(): array
    {
        return [
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'nis' => ['nullable', 'string', 'max:255'],
            'nisn' => ['nullable', 'string', 'max:255'],
            'nik' => ['nullable', 'string', 'max:255'],
            'jenis_kelamin' => ['nullable', Rule::in(['L', 'P'])],
            'tempat_lahir' => ['nullable', 'string', 'max:255'],
            'tanggal_lahir' => ['nullable', 'date', 'before_or_equal:today'],
            'alamat' => ['nullable', 'string'],
            'agama' => ['nullable', 'string', 'max:255'],
            'no_hp' => ['nullable', 'string', 'max:255'],
            'nama_ayah' => ['nullable', 'string', 'max:255'],
            'nama_ibu' => ['nullable', 'string', 'max:255'],
            'no_hp_orang_tua' => ['nullable', 'string', 'max:255'],
            'tahun_masuk' => [
                'nullable',
                'integer',
                'min:1900',
                'max:' . (date('Y') + 1),
            ],
            'status' => [
                'required',
                Rule::in(['aktif', 'lulus', 'pindah', 'keluar']),
            ],
        ];
    }

    protected function messages(): array
    {
        return [
            'nama_lengkap.required' => 'Nama lengkap siswa wajib diisi.',
            'tanggal_lahir.before_or_equal' => 'Tanggal lahir tidak boleh melebihi hari ini.',
            'tahun_masuk.integer' => 'Tahun masuk harus berupa angka.',
            'status.required' => 'Status siswa wajib dipilih.',
        ];
    }

    public function create(): void
    {
        $this->resetForm();
        $this->isEdit = false;
        $this->showModal = true;
    }

    public function edit($id): void
    {
        $siswa = $this->siswaQuery()->findOrFail($id);

        $this->siswaId = $siswa->id;
        $this->nama_lengkap = $siswa->nama_lengkap;
        $this->nis = $siswa->nis ?? '';
        $this->nisn = $siswa->nisn ?? '';
        $this->nik = $siswa->nik ?? '';
        $this->jenis_kelamin = $siswa->jenis_kelamin ?? '';
        $this->tempat_lahir = $siswa->tempat_lahir ?? '';
        $this->tanggal_lahir = $siswa->tanggal_lahir?->format('Y-m-d') ?? '';
        $this->alamat = $siswa->alamat ?? '';
        $this->agama = $siswa->agama ?? '';
        $this->no_hp = $siswa->no_hp ?? '';
        $this->nama_ayah = $siswa->nama_ayah ?? '';
        $this->nama_ibu = $siswa->nama_ibu ?? '';
        $this->no_hp_orang_tua = $siswa->no_hp_orang_tua ?? '';
        $this->tahun_masuk = $siswa->tahun_masuk ?? '';
        $this->status = $siswa->status;

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
            'jenis_kelamin' => $validated['jenis_kelamin'] ?: null,
            'tanggal_lahir' => $validated['tanggal_lahir'] ?: null,
            'tahun_masuk' => $validated['tahun_masuk'] ?: null,
        ];

        if ($this->isEdit) {
            $siswa = $this->siswaQuery()->findOrFail($this->siswaId);
            $siswa->update($data);

            session()->flash('success', 'Data siswa berhasil diperbarui.');
        } else {
            Siswa::create($data);

            session()->flash('success', 'Data siswa berhasil ditambahkan.');
        }

        $this->showModal = false;
        $this->resetForm();
        $this->resetPage();
    }

    public function confirmDelete($id): void
    {
        $this->siswaQuery()->findOrFail($id);
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    protected function hasAcademicRecords(Siswa $siswa): bool
    {
        $tables = [
            'absensi_siswas',
            'ujian_results',
            'nilais',
            'rapors',
        ];

        foreach ($tables as $table) {
            if (
                Schema::hasTable($table)
                && Schema::hasColumn($table, 'siswa_id')
                && DB::table($table)->where('siswa_id', $siswa->id)->exists()
            ) {
                return true;
            }
        }

        return false;
    }

    public function delete(): void
    {
        $siswa = $this->siswaQuery()->findOrFail($this->deleteId);

        if ($this->hasAcademicRecords($siswa)) {
            session()->flash(
                'error',
                'Siswa memiliki riwayat akademik sehingga tidak dapat dihapus.'
            );
        } else {
            $siswa->kelas()->detach();
            $siswa->delete();

            session()->flash('success', 'Data siswa berhasil dihapus.');
        }

        $this->showDeleteModal = false;
        $this->deleteId = null;
        $this->selected = [];
        $this->selectAll = false;
    }

    public function confirmBulkDelete(): void
    {
        if (empty($this->selected)) {
            session()->flash('error', 'Pilih minimal satu siswa terlebih dahulu.');
            return;
        }

        $this->showBulkDeleteModal = true;
    }

    public function deleteSelected(): void
    {
        $deleted = 0;
        $skipped = 0;

        $siswas = $this->siswaQuery()
            ->whereIn('id', $this->selected)
            ->get();

        DB::transaction(function () use ($siswas, &$deleted, &$skipped) {
            foreach ($siswas as $siswa) {
                if ($this->hasAcademicRecords($siswa)) {
                    $skipped++;
                    continue;
                }

                $siswa->kelas()->detach();
                $siswa->delete();
                $deleted++;
            }
        });

        if ($deleted > 0) {
            session()->flash('success', "{$deleted} data siswa berhasil dihapus.");
        }

        if ($skipped > 0) {
            session()->flash(
                'error',
                "{$skipped} siswa dilewati karena memiliki riwayat akademik."
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
            'siswaId',
            'nama_lengkap',
            'nis',
            'nisn',
            'nik',
            'jenis_kelamin',
            'tempat_lahir',
            'tanggal_lahir',
            'alamat',
            'agama',
            'no_hp',
            'nama_ayah',
            'nama_ibu',
            'no_hp_orang_tua',
            'tahun_masuk',
        ]);

        $this->status = 'aktif';
        $this->resetValidation();
    }

    public function render()
    {
        $siswas = $this->siswaQuery()
            ->when(trim($this->search) !== '', function ($query) {
                $search = trim($this->search);

                $query->where(function ($q) use ($search) {
                    $q->where('nama_lengkap', 'like', "%{$search}%")
                        ->orWhere('nis', 'like', "%{$search}%")
                        ->orWhere('nisn', 'like', "%{$search}%")
                        ->orWhere('nik', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.siswa.siswa-manager', [
            'siswas' => $siswas,
        ])->layout('admin.layouts.app')
          ->title('Manajemen Siswa');
    }
}
