<?php

namespace App\Livewire\Admin\Jadwal;

use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Pengajaran;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class JadwalManager extends Component
{
    public string $search = '';
    public string $filterKelas = '';
    public string $filterHari = '';

    public bool $showForm = false;
    public ?int $jadwalId = null;

    public $pengajaran_id = '';
    public string $hari = '';
    public string $jam_mulai = '';
    public string $jam_selesai = '';
    public bool $is_active = true;

    private function sekolahId(): int
    {
        $id = Auth::user()
            ->userRoles()
            ->whereHas('role', fn($q) => $q->where('nama_role', 'admin'))
            ->value('sekolah_id');

        abort_unless($id, 403, 'Sekolah Admin tidak ditemukan.');

        return (int) $id;
    }

    private function queryJadwal()
    {
        return Jadwal::query()->whereHas(
            'pengajaran.kelas',
            fn($q) => $q->where('sekolah_id', $this->sekolahId())
        );
    }

    private function queryPengajaran()
    {
        return Pengajaran::query()
            ->where('is_active', true)
            ->whereHas(
                'kelas',
                fn($q) =>
                $q->where('sekolah_id', $this->sekolahId())
            )
            ->whereHas(
                'guru',
                fn($q) =>
                $q->where('status', 'aktif')
            );
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'filterKelas', 'filterHari']);
    }

    public function resetForm(): void
    {
        $this->reset([
            'jadwalId',
            'pengajaran_id',
            'hari',
            'jam_mulai',
            'jam_selesai',
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
        $jadwal = $this->queryJadwal()->findOrFail($id);

        $this->jadwalId = $jadwal->id;
        $this->pengajaran_id = (string) $jadwal->pengajaran_id;
        $this->hari = $jadwal->hari;
        $this->jam_mulai = substr((string) $jadwal->jam_mulai, 0, 5);
        $this->jam_selesai = substr((string) $jadwal->jam_selesai, 0, 5);
        $this->is_active = (bool) $jadwal->is_active;

        $this->resetValidation();
        $this->showForm = true;
    }

    protected function hasScheduleConflict(
        Pengajaran $pengajaran,
        string $hari,
        string $jamMulai,
        string $jamSelesai,
        ?int $ignoreJadwalId = null
    ): bool {
        return Jadwal::query()
            ->where('is_active', true)
            ->where('hari', $hari)
            ->where('jam_mulai', '<', $jamSelesai)
            ->where('jam_selesai', '>', $jamMulai)
            ->when(
                $ignoreJadwalId,
                fn($q) =>
                $q->where('id', '!=', $ignoreJadwalId)
            )
            ->whereHas('pengajaran', function ($q) use ($pengajaran) {
                $q->where(function ($sub) use ($pengajaran) {
                    $sub->where('kelas_id', $pengajaran->kelas_id)
                        ->orWhere('guru_id', $pengajaran->guru_id);
                });
            })
            ->whereHas(
                'pengajaran.kelas',
                fn($q) =>
                $q->where('sekolah_id', $this->sekolahId())
            )
            ->exists();
    }

    public function save(): void
    {
        $data = $this->validate([
            'pengajaran_id' => ['required', 'integer'],
            'hari' => ['required', 'in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu'],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
            'is_active' => ['boolean'],
        ], [
            'pengajaran_id.required' => 'Penugasan guru wajib dipilih.',
            'hari.required' => 'Hari wajib dipilih.',
            'jam_mulai.required' => 'Jam mulai wajib diisi.',
            'jam_selesai.required' => 'Jam selesai wajib diisi.',
            'jam_selesai.after' => 'Jam selesai harus setelah jam mulai.',
        ]);

        $pengajaran = $this->queryPengajaran()
            ->with(['kelas', 'guru'])
            ->find($data['pengajaran_id']);

        if (! $pengajaran) {
            throw ValidationException::withMessages([
                'pengajaran_id' => 'Penugasan tidak valid untuk sekolah Anda.',
            ]);
        }

        // Cek bentrok hanya jika jadwal diset Aktif
        if ($data['is_active']) {
            $bentrok = $this->hasScheduleConflict(
                $pengajaran,
                $data['hari'],
                $data['jam_mulai'],
                $data['jam_selesai'],
                $this->jadwalId
            );

            if ($bentrok) {
                throw ValidationException::withMessages([
                    'jam_mulai' => 'Jadwal aktif bentrok dengan jadwal lain untuk kelas atau guru yang sama.',
                ]);
            }
        }

        DB::transaction(function () use ($data) {
            $jadwal = $this->jadwalId
                ? $this->queryJadwal()->findOrFail($this->jadwalId)
                : new Jadwal();

            $jadwal->fill($data);
            $jadwal->save();
        });

        $this->resetForm();

        session()->flash('success', 'Jadwal pelajaran berhasil disimpan.');
    }

    public function toggleStatus(int $id): void
    {
        $jadwal = $this->queryJadwal()->with('pengajaran')->findOrFail($id);
        $targetStatus = ! $jadwal->is_active;

        // Cegah mengaktifkan jadwal jika jamnya bentrok dengan jadwal aktif lain
        if ($targetStatus && $jadwal->pengajaran) {
            $bentrok = $this->hasScheduleConflict(
                $jadwal->pengajaran,
                $jadwal->hari,
                substr((string) $jadwal->jam_mulai, 0, 5),
                substr((string) $jadwal->jam_selesai, 0, 5),
                $jadwal->id
            );

            if ($bentrok) {
                session()->flash(
                    'error',
                    'Jadwal tidak dapat diaktifkan karena jamnya bentrok dengan jadwal aktif lain.'
                );
                return;
            }
        }

        $jadwal->update(['is_active' => $targetStatus]);

        session()->flash(
            'success',
            $targetStatus ? 'Jadwal pelajaran berhasil diaktifkan.' : 'Jadwal pelajaran berhasil dinonaktifkan.'
        );
    }

    public function delete(int $id): void
    {
        $jadwal = $this->queryJadwal()->findOrFail($id);
        $jadwal->delete();

        session()->flash('success', 'Jadwal pelajaran berhasil dihapus.');
    }

    public function render()
    {
        $sekolahId = $this->sekolahId();
        $urutanHari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

        $jadwals = $this->queryJadwal()
            ->with([
                'pengajaran.guru',
                'pengajaran.mataPelajaran',
                'pengajaran.kelas',
                'pengajaran.tahunAjaran',
            ])
            ->when(
                $this->filterKelas !== '',
                fn($q) =>
                $q->whereHas(
                    'pengajaran',
                    fn($sub) =>
                    $sub->where('kelas_id', (int) $this->filterKelas)
                )
            )
            ->when(
                $this->filterHari !== '',
                fn($q) =>
                $q->where('hari', $this->filterHari)
            )
            ->when(trim($this->search) !== '', function ($q) {
                $search = '%' . trim($this->search) . '%';

                $q->where(function ($sub) use ($search) {
                    $sub->where('hari', 'like', $search)
                        ->orWhereHas(
                            'pengajaran.guru',
                            fn($r) =>
                            $r->where('nama_lengkap', 'like', $search)
                        )
                        ->orWhereHas(
                            'pengajaran.mataPelajaran',
                            fn($r) =>
                            $r->where('nama', 'like', $search)
                        )
                        ->orWhereHas(
                            'pengajaran.kelas',
                            fn($r) =>
                            $r->where('nama_kelas', 'like', $search)
                        )
                        ->orWhereHas(
                            'pengajaran.tahunAjaran',
                            fn($r) =>
                            $r->where('nama', 'like', $search)
                        );
                });
            })
            ->orderByRaw("FIELD(hari, 'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu')")
            ->orderBy('jam_mulai')
            ->get();

        // Kelompokkan jadwal berdasarkan Kelas, lalu berdasarkan Hari
        $jadwalPerKelas = $jadwals
            ->groupBy(fn($item) => $item->pengajaran?->kelas_id ?? 0)
            ->sortBy(fn($items) => $items->first()?->pengajaran?->kelas?->nama_kelas ?? 'ZZZ')
            ->map(function ($items) use ($urutanHari) {
                $firstPengajaran = $items->first()?->pengajaran;

                $hariGroups = $items
                    ->groupBy('hari')
                    ->sortBy(fn($_, $hari) => array_search($hari, $urutanHari, true));

                return [
                    'kelas_id' => $firstPengajaran?->kelas_id,
                    'nama_kelas' => $firstPengajaran?->kelas?->nama_kelas ?? 'Tanpa Kelas',
                    'tahun_ajaran' => $firstPengajaran?->tahunAjaran?->nama ?? '-',
                    'total_sesi' => $items->count(),
                    'aktif_sesi' => $items->where('is_active', true)->count(),
                    'hari_groups' => $hariGroups,
                ];
            });

        $groupedPengajarans = collect();

        if ($this->showForm) {
            $groupedPengajarans = $this->queryPengajaran()
                ->with(['guru', 'mataPelajaran', 'kelas', 'tahunAjaran'])
                ->get()
                ->sortBy(fn($p) => ($p->kelas?->nama_kelas ?? '') . '-' . ($p->mataPelajaran?->nama ?? ''))
                ->groupBy(fn($p) => $p->kelas?->nama_kelas ?? 'Lainnya');
        }

        return view('livewire.admin.jadwal.jadwal-manager', [
            'jadwalPerKelas' => $jadwalPerKelas,
            'kelasList' => Kelas::where('sekolah_id', $sekolahId)
                ->orderBy('nama_kelas')
                ->get(['id', 'nama_kelas']),
            'groupedPengajarans' => $groupedPengajarans,
            'totalJadwal' => $this->queryJadwal()->count(),
            'jadwalAktif' => $this->queryJadwal()->where('is_active', true)->count(),
            'totalKelasTerjadwal' => $this->queryJadwal()
                ->join('pengajarans', 'jadwals.pengajaran_id', '=', 'pengajarans.id')
                ->distinct('pengajarans.kelas_id')
                ->count('pengajarans.kelas_id'),
        ])->layout('admin.layouts.app');
    }
}
