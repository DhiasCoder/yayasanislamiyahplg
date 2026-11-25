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
    public ?int $pengajaranId = null;

    public $guru_id = '';
    public $mata_pelajaran_id = '';
    public $kelas_id = '';
    public $tahun_ajaran_id = '';
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
            'pengajaranId',
            'guru_id',
            'mata_pelajaran_id',
            'kelas_id',
            'tahun_ajaran_id',
            'is_active',
        ]);

        $this->is_active = true;
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
        $item = $this->queryPenugasan()->findOrFail($id);

        $this->pengajaranId = $item->id;
        $this->guru_id = (string) $item->guru_id;
        $this->mata_pelajaran_id = (string) $item->mata_pelajaran_id;
        $this->kelas_id = (string) $item->kelas_id;
        $this->tahun_ajaran_id = (string) $item->tahun_ajaran_id;
        $this->is_active = (bool) $item->is_active;

        $this->resetValidation();
        $this->showForm = true;
    }

    public function updatedTahunAjaranId(): void
    {
        $this->kelas_id = '';
        $this->resetValidation('kelas_id');
    }

    public function save(): void
    {
        $sekolahId = $this->sekolahId();

        $data = $this->validate([
            'guru_id' => ['required', 'integer'],
            'mata_pelajaran_id' => ['required', 'integer'],
            'kelas_id' => ['required', 'integer'],
            'tahun_ajaran_id' => ['required', 'integer'],
            'is_active' => ['boolean'],
        ], [
            'guru_id.required' => 'Guru wajib dipilih.',
            'mata_pelajaran_id.required' => 'Mata pelajaran wajib dipilih.',
            'kelas_id.required' => 'Kelas wajib dipilih.',
            'tahun_ajaran_id.required' => 'Tahun ajaran wajib dipilih.',
        ]);

        // Pastikan semua pilihan berasal dari sekolah Admin.
        $guruValid = Guru::where('id', $data['guru_id'])
            ->where('sekolah_id', $sekolahId)
            ->exists();

        $mapelValid = MataPelajaran::where('id', $data['mata_pelajaran_id'])
            ->where('sekolah_id', $sekolahId)
            ->exists();

        $tahunValid = TahunAjaran::where('id', $data['tahun_ajaran_id'])
            ->where('sekolah_id', $sekolahId)
            ->exists();

        $kelasValid = Kelas::where('id', $data['kelas_id'])
            ->where('sekolah_id', $sekolahId)
            ->where('tahun_ajaran_id', $data['tahun_ajaran_id'])
            ->exists();

        if (! $guruValid || ! $mapelValid || ! $tahunValid || ! $kelasValid) {
            throw ValidationException::withMessages([
                'kelas_id' => 'Pilihan penugasan tidak valid untuk sekolah atau tahun ajaran ini.',
            ]);
        }

        // Cegah kombinasi penugasan yang sama.
        $duplikat = Pengajaran::query()
            ->where('guru_id', $data['guru_id'])
            ->where('mata_pelajaran_id', $data['mata_pelajaran_id'])
            ->where('kelas_id', $data['kelas_id'])
            ->where('tahun_ajaran_id', $data['tahun_ajaran_id'])
            ->when(
                $this->pengajaranId,
                fn($q) => $q->where('id', '!=', $this->pengajaranId)
            )
            ->exists();

        if ($duplikat) {
            throw ValidationException::withMessages([
                'kelas_id' => 'Penugasan dengan kombinasi guru, mata pelajaran, kelas, dan tahun ajaran ini sudah ada.',
            ]);
        }

        DB::transaction(function () use ($data) {
            $item = $this->pengajaranId
                ? $this->queryPenugasan()->findOrFail($this->pengajaranId)
                : new Pengajaran();

            $item->fill($data);
            $item->save();
        });

        $this->resetForm();
        session()->flash('success', 'Penugasan guru berhasil disimpan.');
    }

    public function toggleStatus(int $id): void
    {
        $item = $this->queryPenugasan()->findOrFail($id);

        $item->update(['is_active' => ! $item->is_active]);

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

    public function delete(int $id): void
    {
        $item = $this->queryPenugasan()->findOrFail($id);

        if ($this->hasRelatedRecords($item)) {
            session()->flash(
                'error',
                'Penugasan tidak dapat dihapus karena sudah digunakan pada materi, ujian, tugas, nilai, atau jadwal.'
            );

            return;
        }

        $item->delete();

        session()->flash('success', 'Penugasan guru berhasil dihapus.');
    }

    public function render()
    {
        $sekolahId = $this->sekolahId();

        $penugasans = $this->queryPenugasan()
            ->with(['guru', 'mataPelajaran', 'kelas', 'tahunAjaran'])
            ->when($this->search, function ($q) {
                $search = '%' . $this->search . '%';

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
            ->latest('id')
            ->paginate(10);

        return view('livewire.admin.penugasan-guru.penugasan-guru-manager', [
            'penugasans' => $penugasans,
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
