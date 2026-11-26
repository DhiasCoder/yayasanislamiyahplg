<?php

namespace App\Livewire\Admin\PenugasanGuru;

use App\Models\Guru;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Pengajaran;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithPagination;

class PenugasanGuruManager extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showForm = false;
    public bool $isEditMode = false;

    public $guru_id = '';
    public $mata_pelajaran_id = '';
    public $tahun_ajaran_id = '';
    public array $selectedKelasIds = [];
    public array $lockedKelasIds = [];
    public bool $is_active = true;

    protected string $paginationTheme = 'tailwind';

    private function sekolahId(): int
    {
        $id = Auth::user()
            ->userRoles()
            ->whereHas('role', fn($q) => $q->where('nama_role', 'admin'))
            ->value('sekolah_id');

        abort_unless($id, 403, 'Sekolah Admin tidak ditemukan.');

        return (int) $id;
    }

    private function queryPenugasan()
    {
        return Pengajaran::query()->whereHas(
            'kelas',
            fn($q) => $q->where('sekolah_id', $this->sekolahId())
        );
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function resetForm(): void
    {
        $this->reset([
            'isEditMode',
            'guru_id',
            'mata_pelajaran_id',
            'tahun_ajaran_id',
            'selectedKelasIds',
            'lockedKelasIds',
        ]);

        $this->is_active = true;
        $this->resetValidation();
        $this->showForm = false;
    }

    public function create(): void
    {
        $this->resetForm();
        $this->isEditMode = false;
        $this->showForm = true;
    }

    public function editGroup(int $guruId, int $mapelId, int $tahunId): void
    {
        $this->resetForm();

        $this->isEditMode = true;
        $this->guru_id = (string) $guruId;
        $this->mata_pelajaran_id = (string) $mapelId;
        $this->tahun_ajaran_id = (string) $tahunId;

        $this->syncExistingKelasForSelection();
        $this->showForm = true;
    }

    public function updatedTahunAjaranId(): void
    {
        $this->selectedKelasIds = [];
        $this->lockedKelasIds = [];
        $this->resetValidation('selectedKelasIds');
        $this->syncExistingKelasForSelection();
    }

    public function updatedGuruId(): void
    {
        $this->syncExistingKelasForSelection();
    }

    public function updatedMataPelajaranId(): void
    {
        $this->syncExistingKelasForSelection();
    }

    protected function syncExistingKelasForSelection(): void
    {
        if (! $this->guru_id || ! $this->mata_pelajaran_id || ! $this->tahun_ajaran_id) {
            $this->selectedKelasIds = [];
            $this->lockedKelasIds = [];
            return;
        }

        $existingItems = $this->queryPenugasan()
            ->where('guru_id', (int) $this->guru_id)
            ->where('mata_pelajaran_id', (int) $this->mata_pelajaran_id)
            ->where('tahun_ajaran_id', (int) $this->tahun_ajaran_id)
            ->get();

        $this->selectedKelasIds = $existingItems
            ->pluck('kelas_id')
            ->map(fn($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        $this->lockedKelasIds = $existingItems
            ->filter(fn(Pengajaran $item) => $this->hasRelatedRecords($item))
            ->pluck('kelas_id')
            ->map(fn($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        if ($existingItems->isNotEmpty()) {
            $this->is_active = $existingItems->contains(fn($item) => (bool) $item->is_active);
        }
    }

    public function toggleKelas(int $kelasId): void
    {
        if (in_array($kelasId, $this->lockedKelasIds, true)) {
            return;
        }

        if (in_array($kelasId, $this->selectedKelasIds, true)) {
            $this->selectedKelasIds = array_values(
                array_filter($this->selectedKelasIds, fn($id) => (int) $id !== $kelasId)
            );
        } else {
            $this->selectedKelasIds[] = $kelasId;
            $this->selectedKelasIds = array_values(array_unique(array_map('intval', $this->selectedKelasIds)));
        }

        $this->resetValidation('selectedKelasIds');
    }

    public function selectAllKelas(): void
    {
        if (! $this->tahun_ajaran_id) {
            return;
        }

        $allIds = Kelas::where('sekolah_id', $this->sekolahId())
            ->where('tahun_ajaran_id', (int) $this->tahun_ajaran_id)
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->all();

        $this->selectedKelasIds = array_values(array_unique(array_merge($this->lockedKelasIds, $allIds)));
        $this->resetValidation('selectedKelasIds');
    }

    public function clearKelasSelection(): void
    {
        $this->selectedKelasIds = $this->lockedKelasIds;
    }

    public function save(): void
    {
        $sekolahId = $this->sekolahId();

        $data = $this->validate([
            'guru_id' => ['required', 'integer'],
            'mata_pelajaran_id' => ['required', 'integer'],
            'tahun_ajaran_id' => ['required', 'integer'],
            'selectedKelasIds' => ['required', 'array', 'min:1'],
            'selectedKelasIds.*' => ['integer'],
            'is_active' => ['boolean'],
        ], [
            'guru_id.required' => 'Guru wajib dipilih.',
            'mata_pelajaran_id.required' => 'Mata pelajaran wajib dipilih.',
            'tahun_ajaran_id.required' => 'Tahun ajaran wajib dipilih.',
            'selectedKelasIds.required' => 'Pilih minimal satu kelas yang diajar.',
            'selectedKelasIds.min' => 'Pilih minimal satu kelas yang diajar.',
        ]);

        $selectedIds = array_values(array_unique(array_map('intval', $data['selectedKelasIds'])));

        $guruValid = Guru::where('id', $data['guru_id'])
            ->where('sekolah_id', $sekolahId)
            ->exists();

        $mapelValid = MataPelajaran::where('id', $data['mata_pelajaran_id'])
            ->where('sekolah_id', $sekolahId)
            ->exists();

        $tahunValid = TahunAjaran::where('id', $data['tahun_ajaran_id'])
            ->where('sekolah_id', $sekolahId)
            ->exists();

        $validKelasCount = Kelas::whereIn('id', $selectedIds)
            ->where('sekolah_id', $sekolahId)
            ->where('tahun_ajaran_id', $data['tahun_ajaran_id'])
            ->count();

        if (! $guruValid || ! $mapelValid || ! $tahunValid || $validKelasCount !== count($selectedIds)) {
            throw ValidationException::withMessages([
                'selectedKelasIds' => 'Pilihan guru, mata pelajaran, atau kelas tidak valid untuk sekolah ini.',
            ]);
        }

        $protectedClasses = [];

        DB::transaction(function () use ($data, $selectedIds, &$protectedClasses) {
            $existingItems = $this->queryPenugasan()
                ->with('kelas')
                ->where('guru_id', $data['guru_id'])
                ->where('mata_pelajaran_id', $data['mata_pelajaran_id'])
                ->where('tahun_ajaran_id', $data['tahun_ajaran_id'])
                ->get()
                ->keyBy('kelas_id');

            // Tambah atau perbarui kelas yang dipilih
            foreach ($selectedIds as $kelasId) {
                if ($existingItems->has($kelasId)) {
                    $existingItems->get($kelasId)->update([
                        'is_active' => $data['is_active'],
                    ]);
                } else {
                    Pengajaran::create([
                        'guru_id' => $data['guru_id'],
                        'mata_pelajaran_id' => $data['mata_pelajaran_id'],
                        'kelas_id' => $kelasId,
                        'tahun_ajaran_id' => $data['tahun_ajaran_id'],
                        'is_active' => $data['is_active'],
                    ]);
                }
            }

            // Hapus kelas yang tidak dipilih (kecuali sudah punya relasi jadwal/materi/ujian/nilai)
            foreach ($existingItems as $kelasId => $item) {
                if (in_array((int) $kelasId, $selectedIds, true)) {
                    continue;
                }

                if ($this->hasRelatedRecords($item)) {
                    $protectedClasses[] = $item->kelas?->nama_kelas ?? "ID {$kelasId}";
                    continue;
                }

                $item->delete();
            }
        });

        $this->resetForm();

        if (! empty($protectedClasses)) {
            session()->flash(
                'success',
                'Penugasan disimpan. Kelas ' . implode(', ', $protectedClasses) . ' tetap dipertahankan karena sudah memiliki jadwal/materi/ujian.'
            );
        } else {
            session()->flash('success', 'Distribusi kelas penugasan guru berhasil disimpan.');
        }
    }

    public function toggleGroupStatus(int $guruId, int $mapelId, int $tahunId, bool $targetStatus): void
    {
        $this->queryPenugasan()
            ->where('guru_id', $guruId)
            ->where('mata_pelajaran_id', $mapelId)
            ->where('tahun_ajaran_id', $tahunId)
            ->update(['is_active' => $targetStatus]);

        session()->flash('success', 'Status penugasan berhasil diperbarui.');
    }

    protected function hasRelatedRecords(Pengajaran $item): bool
    {
        $tables = [
            'materis',
            'ujians',
            'tugas',
            'kuis',
            'nilais',
            'jadwals',
            'jadwal_pelajarans',
        ];

        foreach ($tables as $table) {
            if (
                Schema::hasTable($table)
                && Schema::hasColumn($table, 'pengajaran_id')
                && DB::table($table)->where('pengajaran_id', $item->id)->exists()
            ) {
                return true;
            }
        }

        return false;
    }

    public function deleteGroup(int $guruId, int $mapelId, int $tahunId): void
    {
        $items = $this->queryPenugasan()
            ->where('guru_id', $guruId)
            ->where('mata_pelajaran_id', $mapelId)
            ->where('tahun_ajaran_id', $tahunId)
            ->get();

        $deleted = 0;
        $skipped = 0;

        DB::transaction(function () use ($items, &$deleted, &$skipped) {
            foreach ($items as $item) {
                if ($this->hasRelatedRecords($item)) {
                    $skipped++;
                    continue;
                }

                $item->delete();
                $deleted++;
            }
        });

        if ($deleted > 0 && $skipped > 0) {
            session()->flash(
                'success',
                "{$deleted} kelas penugasan dihapus. {$skipped} kelas dilewati karena sudah digunakan pada jadwal/materi/ujian."
            );
        } elseif ($deleted > 0) {
            session()->flash('success', 'Penugasan guru berhasil dihapus.');
        } else {
            session()->flash(
                'error',
                'Penugasan tidak dapat dihapus karena seluruh kelas sudah digunakan pada jadwal, materi, ujian, atau nilai.'
            );
        }
    }

    public function render()
    {
        $sekolahId = $this->sekolahId();

        // Paginasi berdasarkan grup unik (Guru + Mapel + Tahun Ajaran) agar tidak tampil ganda
        $groups = $this->queryPenugasan()
            ->select('guru_id', 'mata_pelajaran_id', 'tahun_ajaran_id')
            ->selectRaw('MAX(id) as latest_id')
            ->selectRaw('MAX(is_active) as any_active')
            ->when($this->search, function ($q) {
                $search = '%' . trim($this->search) . '%';

                $q->where(function ($sub) use ($search) {
                    $sub->whereHas(
                        'guru',
                        fn($r) =>
                        $r->where('nama_lengkap', 'like', $search)
                    )->orWhereHas(
                        'mataPelajaran',
                        fn($r) =>
                        $r->where('nama', 'like', $search)
                    )->orWhereHas(
                        'kelas',
                        fn($r) =>
                        $r->where('nama_kelas', 'like', $search)
                    )->orWhereHas(
                        'tahunAjaran',
                        fn($r) =>
                        $r->where('nama', 'like', $search)
                    );
                });
            })
            ->groupBy('guru_id', 'mata_pelajaran_id', 'tahun_ajaran_id')
            ->orderByDesc('latest_id')
            ->paginate(10);

        // Ambil detail relasi & daftar kelas untuk item di halaman aktif (1 query saja)
        $pageItems = collect();

        if ($groups->isNotEmpty()) {
            $pageItems = $this->queryPenugasan()
                ->with(['guru', 'mataPelajaran', 'kelas', 'tahunAjaran'])
                ->where(function ($q) use ($groups) {
                    foreach ($groups as $g) {
                        $q->orWhere(function ($sub) use ($g) {
                            $sub->where('guru_id', $g->guru_id)
                                ->where('mata_pelajaran_id', $g->mata_pelajaran_id)
                                ->where('tahun_ajaran_id', $g->tahun_ajaran_id);
                        });
                    }
                })
                ->get()
                ->groupBy(fn($item) => "{$item->guru_id}-{$item->mata_pelajaran_id}-{$item->tahun_ajaran_id}");
        }

        $groups->getCollection()->transform(function ($group) use ($pageItems) {
            $key = "{$group->guru_id}-{$group->mata_pelajaran_id}-{$group->tahun_ajaran_id}";
            $items = ($pageItems->get($key) ?? collect())
                ->sortBy(fn($item) => $item->kelas?->nama_kelas ?? '')
                ->values();

            $first = $items->first();

            $group->guru = $first?->guru;
            $group->mataPelajaran = $first?->mataPelajaran;
            $group->tahunAjaran = $first?->tahunAjaran;
            $group->kelasItems = $items;
            $group->is_active = (bool) $group->any_active;

            return $group;
        });

        return view('livewire.admin.penugasan-guru.penugasan-guru-manager', [
            'groups' => $groups,
            'gurus' => $this->showForm
                ? Guru::where('sekolah_id', $sekolahId)
                ->where('status', 'aktif')
                ->orderBy('nama_lengkap')
                ->get()
                : collect(),
            'mataPelajarans' => $this->showForm
                ? MataPelajaran::where('sekolah_id', $sekolahId)
                ->where('is_active', true)
                ->orderBy('nama')
                ->get()
                : collect(),
            'tahunAjarans' => $this->showForm
                ? TahunAjaran::where('sekolah_id', $sekolahId)
                ->orderByDesc('tanggal_mulai')
                ->get()
                : collect(),
            'kelasOptions' => $this->showForm && $this->tahun_ajaran_id
                ? Kelas::where('sekolah_id', $sekolahId)
                ->where('tahun_ajaran_id', $this->tahun_ajaran_id)
                ->orderBy('nama_kelas')
                ->get()
                : collect(),
            'totalPenugasan' => $this->queryPenugasan()->count(),
            'penugasanAktif' => $this->queryPenugasan()
                ->where('is_active', true)
                ->count(),
        ])->layout('admin.layouts.app');
    }
}
