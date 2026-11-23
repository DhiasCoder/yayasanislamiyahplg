<?php

namespace App\Livewire\Admin\PenempatanSiswa;

use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithPagination;

class PenempatanSiswaManager extends Component
{
    use WithPagination;

    public $search = '';
    public $kelasId = '';
    public $kelasTujuanId = '';

    public $showModal = false;
    public $showRemoveModal = false;
    public $showBulkRemoveModal = false;

    public $selectedSiswa = [];
    public $selectedPenempatan = [];
    public $selectAll = false;
    public $removeSiswaId = null;

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

        return Kelas::query()
            ->where('sekolah_id', $sekolahId);
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

    public function updatedKelasId(): void
    {
        $this->resetPage();
        $this->selectedPenempatan = [];
        $this->selectAll = false;
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
        $this->selectedPenempatan = [];
        $this->selectAll = false;
    }

    public function updatedSelectAll($value): void
    {
        if (!$value || !$this->kelasId) {
            $this->selectedPenempatan = [];
            return;
        }

        $this->selectedPenempatan = $this->penempatanQuery()
            ->pluck('siswas.id')
            ->map(fn ($id) => (string) $id)
            ->toArray();
    }

    protected function penempatanQuery()
    {
        $kelas = $this->kelasQuery()->find($this->kelasId);

        if (!$kelas) {
            return Siswa::query()
                ->whereRaw('1 = 0')
                ->select('siswas.*');
        }

        return $this->siswaQuery()
            ->join('siswa_kelas', 'siswas.id', '=', 'siswa_kelas.siswa_id')
            ->where('siswa_kelas.kelas_id', $kelas->id)
            ->when(trim($this->search) !== '', function ($query) {
                $search = trim($this->search);

                $query->where(function ($q) use ($search) {
                    $q->where('siswas.nama_lengkap', 'like', "%{$search}%")
                        ->orWhere('siswas.nis', 'like', "%{$search}%")
                        ->orWhere('siswas.nisn', 'like', "%{$search}%");
                });
            })
            ->select('siswas.*');
    }

    public function openModal(): void
    {
        $this->selectedSiswa = [];
        $this->kelasTujuanId = $this->kelasId;
        $this->resetValidation();
        $this->showModal = true;
    }

    public function updatedKelasTujuanId(): void
    {
        $this->selectedSiswa = [];
    }

    protected function validatePlacement(): Kelas
    {
        $this->validate([
            'kelasTujuanId' => ['required', 'integer'],
            'selectedSiswa' => ['required', 'array', 'min:1'],
            'selectedSiswa.*' => ['required', 'integer'],
        ], [
            'kelasTujuanId.required' => 'Pilih kelas tujuan terlebih dahulu.',
            'selectedSiswa.required' => 'Pilih minimal satu siswa.',
            'selectedSiswa.min' => 'Pilih minimal satu siswa.',
        ]);

        return $this->kelasQuery()->findOrFail($this->kelasTujuanId);
    }

    public function savePlacement(): void
    {
        $kelas = $this->validatePlacement();

        $siswaIds = collect($this->selectedSiswa)
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $siswas = $this->siswaQuery()
            ->whereIn('id', $siswaIds)
            ->get();

        if ($siswas->count() !== $siswaIds->count()) {
            throw ValidationException::withMessages([
                'selectedSiswa' => 'Ada siswa yang tidak ditemukan di sekolah Anda.',
            ]);
        }

        DB::transaction(function () use ($siswas, $kelas) {
            $kelasSatuTahun = $this->kelasQuery()
                ->where('tahun_ajaran_id', $kelas->tahun_ajaran_id)
                ->pluck('id')
                ->all();

            foreach ($siswas as $siswa) {
                // Jika siswa sudah berada di kelas lain pada tahun ajaran
                // yang sama, pindahkan penempatannya.
                DB::table('siswa_kelas')
                    ->where('siswa_id', $siswa->id)
                    ->whereIn('kelas_id', $kelasSatuTahun)
                    ->where('kelas_id', '!=', $kelas->id)
                    ->delete();

                // Hindari duplikasi penempatan pada kelas yang sama.
                $sudahDitempatkan = DB::table('siswa_kelas')
                    ->where('siswa_id', $siswa->id)
                    ->where('kelas_id', $kelas->id)
                    ->exists();

                if (!$sudahDitempatkan) {
                $siswa->kelas()->attach($kelas->id, [
                    'tahun_ajaran_id' => $kelas->tahun_ajaran_id,
                ]);
            }
            }
        });

        session()->flash(
            'success',
            'Penempatan siswa berhasil disimpan. Siswa yang sudah berada di kelas lain pada tahun ajaran yang sama akan dipindahkan.'
        );

        $this->showModal = false;
        $this->selectedSiswa = [];
        $this->selectedPenempatan = [];
        $this->selectAll = false;
        $this->resetPage();
    }

    public function confirmRemove($siswaId): void
    {
        $kelas = $this->kelasQuery()->findOrFail($this->kelasId);

        $this->siswaQuery()
            ->whereHas('kelas', fn ($query) =>
                $query->where('kelas.id', $kelas->id)
            )
            ->findOrFail($siswaId);

        $this->removeSiswaId = $siswaId;
        $this->showRemoveModal = true;
    }

    public function removePlacement(): void
    {
        $kelas = $this->kelasQuery()->findOrFail($this->kelasId);

        $siswa = $this->siswaQuery()
            ->whereHas('kelas', fn ($query) =>
                $query->where('kelas.id', $kelas->id)
            )
            ->findOrFail($this->removeSiswaId);

        $siswa->kelas()->detach($kelas->id);

        session()->flash('success', 'Penempatan siswa berhasil dihapus.');

        $this->showRemoveModal = false;
        $this->removeSiswaId = null;
        $this->selectedPenempatan = [];
        $this->selectAll = false;
        $this->resetPage();
    }

    public function confirmBulkRemove(): void
    {
        if (!$this->kelasId || empty($this->selectedPenempatan)) {
            session()->flash('error', 'Pilih kelas dan minimal satu siswa.');
            return;
        }

        $this->showBulkRemoveModal = true;
    }

    public function removeSelected(): void
    {
        $kelas = $this->kelasQuery()->findOrFail($this->kelasId);

        $siswas = $this->siswaQuery()
            ->whereIn('id', $this->selectedPenempatan)
            ->whereHas('kelas', fn ($query) =>
                $query->where('kelas.id', $kelas->id)
            )
            ->get();

        DB::transaction(function () use ($siswas, $kelas) {
            foreach ($siswas as $siswa) {
                $siswa->kelas()->detach($kelas->id);
            }
        });

        session()->flash(
            'success',
            $siswas->count() . ' penempatan siswa berhasil dihapus.'
        );

        $this->showBulkRemoveModal = false;
        $this->selectedPenempatan = [];
        $this->selectAll = false;
        $this->resetPage();
    }

    public function render()
    {
        $kelasList = $this->kelasQuery()
            ->with('tahunAjaran')
            ->orderBy('nama_kelas')
            ->get();

        $kelasAktif = $this->kelasId
            ? $this->kelasQuery()->with('tahunAjaran')->find($this->kelasId)
            : null;

        // Hanya tampilkan siswa yang belum berada di kelas tujuan.
        // Siswa dari kelas lain pada tahun ajaran yang sama tetap dapat dipilih.
        $calonSiswa = collect();

        if ($this->showModal && $this->kelasTujuanId) {
            $kelasTujuan = $this->kelasQuery()->find($this->kelasTujuanId);

            if ($kelasTujuan) {
                $calonSiswa = $this->siswaQuery()
                    ->where('status', 'aktif')
                    ->whereDoesntHave('kelas', function ($query) use ($kelasTujuan) {
                        $query->where('kelas.id', $kelasTujuan->id);
                    })
                    ->when(trim($this->search) !== '', function ($query) {
                        $search = trim($this->search);

                        $query->where(function ($q) use ($search) {
                            $q->where('nama_lengkap', 'like', "%{$search}%")
                                ->orWhere('nis', 'like', "%{$search}%")
                                ->orWhere('nisn', 'like', "%{$search}%");
                        });
                    })
                    ->orderBy('nama_lengkap')
                    ->limit(200)
                    ->get();
            }
        }

        return view('livewire.admin.penempatan-siswa.penempatan-siswa-manager', [
            'kelasList' => $kelasList,
            'kelasAktif' => $kelasAktif,
            'siswas' => $this->penempatanQuery()->orderBy('siswas.nama_lengkap')->paginate(10),
            'calonSiswa' => $calonSiswa,
        ])
            ->layout('admin.layouts.app')
            ->title('Penempatan Siswa di Kelas');
    }
}
