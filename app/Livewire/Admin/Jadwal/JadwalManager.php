<?php

namespace App\Livewire\Admin\Jadwal;

use App\Models\Jadwal;
use App\Models\Pengajaran;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithPagination;

class JadwalManager extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showForm = false;
    public ?int $jadwalId = null;

    public $pengajaran_id = '';
    public string $hari = '';
    public string $jam_mulai = '';
    public string $jam_selesai = '';
    public string $ruang = '';

    protected string $paginationTheme = 'tailwind';

    private function sekolahId(): int
    {
        $id = Auth::user()
            ->userRoles()
            ->whereHas('role', fn ($q) => $q->where('nama_role', 'admin'))
            ->value('sekolah_id');

        abort_unless($id, 403, 'Sekolah Admin tidak ditemukan.');

        return (int) $id;
    }

    private function queryJadwal()
    {
        return Jadwal::query()->whereHas(
            'pengajaran.kelas',
            fn ($q) => $q->where('sekolah_id', $this->sekolahId())
        );
    }

    private function queryPengajaran()
    {
        return Pengajaran::query()
            ->where('is_active', true)
            ->whereHas('kelas', fn ($q) =>
                $q->where('sekolah_id', $this->sekolahId())
            )
            ->whereHas('guru', fn ($q) =>
                $q->where('status', 'aktif')
            );
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function resetForm(): void
    {
        $this->reset([
            'jadwalId',
            'pengajaran_id',
            'hari',
            'jam_mulai',
            'jam_selesai',
            'ruang',
        ]);

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
        $this->ruang = $jadwal->ruang ?? '';

        $this->resetValidation();
        $this->showForm = true;
    }

    public function save(): void
    {
        $data = $this->validate([
            'pengajaran_id' => ['required', 'integer'],
            'hari' => ['required', 'in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu'],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
            'ruang' => ['nullable', 'string', 'max:100'],
        ], [
            'pengajaran_id.required' => 'Penugasan guru wajib dipilih.',
            'hari.required' => 'Hari wajib dipilih.',
            'jam_mulai.required' => 'Jam mulai wajib diisi.',
            'jam_selesai.required' => 'Jam selesai wajib diisi.',
            'jam_selesai.after' => 'Jam selesai harus setelah jam mulai.',
            'ruang.max' => 'Nama ruang maksimal 100 karakter.',
        ]);

        $pengajaran = $this->queryPengajaran()
            ->with(['kelas', 'guru'])
            ->find($data['pengajaran_id']);

        if (! $pengajaran) {
            throw ValidationException::withMessages([
                'pengajaran_id' => 'Penugasan tidak valid untuk sekolah Anda.',
            ]);
        }

        // Cegah jadwal bertumpuk untuk kelas atau guru yang sama.
        $bentrok = Jadwal::query()
            ->where('hari', $data['hari'])
            ->where('jam_mulai', '<', $data['jam_selesai'])
            ->where('jam_selesai', '>', $data['jam_mulai'])
            ->when($this->jadwalId, fn ($q) =>
                $q->where('id', '!=', $this->jadwalId)
            )
            ->whereHas('pengajaran', function ($q) use ($pengajaran) {
                $q->where(function ($sub) use ($pengajaran) {
                    $sub->where('kelas_id', $pengajaran->kelas_id)
                        ->orWhere('guru_id', $pengajaran->guru_id);
                });
            })
            ->whereHas('pengajaran.kelas', fn ($q) =>
                $q->where('sekolah_id', $this->sekolahId())
            )
            ->exists();

        if ($bentrok) {
            throw ValidationException::withMessages([
                'jam_mulai' => 'Jadwal bentrok dengan jadwal lain untuk kelas atau guru yang sama.',
            ]);
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

    public function delete(int $id): void
    {
        $jadwal = $this->queryJadwal()->findOrFail($id);
        $jadwal->delete();

        session()->flash('success', 'Jadwal berhasil dihapus.');
    }

    public function render()
    {
        $jadwals = $this->queryJadwal()
            ->with([
                'pengajaran.guru',
                'pengajaran.mataPelajaran',
                'pengajaran.kelas',
                'pengajaran.tahunAjaran',
            ])
            ->when($this->search, function ($q) {
                $search = '%' . $this->search . '%';

                $q->where(function ($sub) use ($search) {
                    $sub->where('hari', 'like', $search)
                        ->orWhere('ruang', 'like', $search)
                        ->orWhereHas('pengajaran.guru', fn ($r) =>
                            $r->where('nama_lengkap', 'like', $search)
                        )
                        ->orWhereHas('pengajaran.mataPelajaran', fn ($r) =>
                            $r->where('nama', 'like', $search)
                        )
                        ->orWhereHas('pengajaran.kelas', fn ($r) =>
                            $r->where('nama_kelas', 'like', $search)
                        )
                        ->orWhereHas('pengajaran.tahunAjaran', fn ($r) =>
                            $r->where('nama', 'like', $search)
                        );
                });
            })
            ->orderByRaw(
                "FIELD(hari, 'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu')"
            )
            ->orderBy('jam_mulai')
            ->paginate(10);

        return view('livewire.admin.jadwal.jadwal-manager', [
            'jadwals' => $jadwals,
            'pengajarans' => $this->queryPengajaran()
                ->with(['guru', 'mataPelajaran', 'kelas', 'tahunAjaran'])
                ->orderBy('kelas_id')
                ->get(),
            'totalJadwal' => $this->queryJadwal()->count(),
        ])->layout('admin.layouts.app');
    }
}
