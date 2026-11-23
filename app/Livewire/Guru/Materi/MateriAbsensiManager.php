<?php

namespace App\Livewire\Guru\Materi;

use App\Models\Absensi;
use App\Models\AbsensiSiswa;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Materi;
use App\Models\Pengajaran;
use App\Models\Siswa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class MateriAbsensiManager extends Component
{
    use WithFileUploads;

    public $materiId = null;
    public $pengajaran_id = '';
    public $judul = '';
    public $deskripsi = '';
    public $video_url = '';
    public $file;
    public $is_active = false;

    public $absensiId = null;
    public $search = '';
    public $filterPengajaran = '';

    public $absensiMateriId;
    public $jamMulai = '';
    public $jamSelesai = '';
    public function getGuruProperty(): Guru
    {
        $guru = Guru::where('user_id', Auth::id())->first();

        if (!$guru) {
            abort(403, 'Data guru tidak ditemukan untuk akun yang login.');
        }

        return $guru;
    }

    public function getPengajaransProperty()
    {
        return $this->guru->pengajarans()
            ->with('kelas')
            ->where('is_active', true)
            ->get();
    }

    private function guruMateriQuery()
    {
        $guruId = $this->guru->id;

        return Materi::whereHas('pengajaran', function ($query) use ($guruId) {
            $query->where('guru_id', $guruId);
        });
    }

    private function findOwnedMateri(int $id): Materi
    {
        return $this->guruMateriQuery()->findOrFail($id);
    }

    public function resetForm(): void
    {
        $this->reset([
            'materiId',
            'pengajaran_id',
            'judul',
            'deskripsi',
            'video_url',
            'file',
            'is_active',
        ]);

        $this->resetValidation();
    }

    public function simpanMateri(): void
    {
        $this->validate([
            'pengajaran_id' => [
                'required',
                Rule::exists('pengajarans', 'id')->where(
                    fn ($query) => $query
                        ->where('guru_id', $this->guru->id)
                        ->where('is_active', true)
                ),
            ],
            'judul' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'video_url' => ['nullable', 'url', 'max:2048'],
            'file' => [
                'nullable',
                'file',
                'mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,mp4,webm',
                'max:102400',
            ],
            'is_active' => ['boolean'],
        ]);

        $materi = null;
        $filePath = null;

        DB::transaction(function () use (&$materi, &$filePath) {
            if ($this->materiId) {
                $materi = $this->findOwnedMateri($this->materiId);

                if ((int) $materi->pengajaran_id !== (int) $this->pengajaran_id) {
                    abort(403, 'Pengajaran tidak sesuai dengan materi.');
                }
            }

            if ($this->file) {
                $filePath = $this->file->store('materi', 'public');
            }

            $materiData = [
                'pengajaran_id' => $this->pengajaran_id,
                'judul' => $this->judul,
                'deskripsi' => $this->deskripsi,
                'video_url' => $this->video_url ?: null,
                'is_active' => $this->is_active,
            ];

            if ($filePath) {
                $materiData['file_path'] = $filePath;
            }

            if ($materi) {
                $materi->update($materiData);
            } else {
                $materi = Materi::create($materiData);
            }
        });

        if ($filePath && isset($materi) && $materi->getOriginal('file_path')) {
            $oldPath = $materi->getOriginal('file_path');

            if ($oldPath && $oldPath !== $filePath) {
                Storage::disk('public')->delete($oldPath);
            }
        }
        $isEdit = !empty($this->materiId);
        $this->resetForm();

        session()->flash(
            'success',
            $isEdit
                ? 'Materi berhasil diperbarui.'
                : 'Materi berhasil dibuat. Sesi absensi belum dibuka.'
        );
    }

    public function editMateri(int $id): void
    {
        $materi = $this->findOwnedMateri($id);

        $this->materiId = $materi->id;
        $this->pengajaran_id = (string) $materi->pengajaran_id;
        $this->judul = $materi->judul;
        $this->deskripsi = $materi->deskripsi;
        $this->video_url = $materi->video_url;
        $this->is_active = $materi->is_active;

        $this->resetValidation();
    }

    public function hapusMateri(int $id): void
    {
        $materi = $this->findOwnedMateri($id);

        DB::transaction(function () use ($materi) {
            // Hapus sesi absensi yang terkait materi ini.
            Absensi::where('materi_id', $materi->id)->delete();

            $materi->delete();
        });

        if ($materi->file_path) {
            Storage::disk('public')->delete($materi->file_path);
        }

        if ((int) $this->absensiId === (int) $id) {
            $this->absensiId = null;
        }

        session()->flash('success', 'Materi berhasil dihapus.');
    }

    public function pilihAbsensi(int $id): void
    {
        $absensi = Absensi::whereHas('pengajaran', function ($query) {
            $query->where('guru_id', $this->guru->id);
        })->findOrFail($id);

        $this->absensiId = $absensi->id;
    }

    public function ubahStatusAbsensi(): void
    {
        $absensi = $this->absensiMilikGuru();

        $absensi->update([
            'status' => $absensi->status === 'dibuka' ? 'ditutup' : 'dibuka',
            'jam_selesai' => $absensi->status === 'dibuka'
                ? now()->format('H:i:s')
                : null,
        ]);
    }

    public function ubahStatusSiswa(int $absensiSiswaId, string $status): void
    {
        validator(
            ['status' => $status],
            ['status' => ['required', Rule::in(['hadir', 'izin', 'sakit', 'alpa'])]]
        )->validate();

        $absensi = $this->absensiMilikGuru();

        if ($absensi->status !== 'dibuka') {
            $this->addError('absensi', 'Buka sesi absensi terlebih dahulu.');
            return;
        }

        AbsensiSiswa::where('absensi_id', $absensi->id)
            ->whereHas('absensi.pengajaran', function ($query) {
                $query->where('guru_id', $this->guru->id);
            })
            ->whereKey($absensiSiswaId)
            ->firstOrFail()
            ->update([
                'status' => $status,
                'waktu_absen' => $status === 'hadir' ? now() : null,
            ]);
    }

    private function absensiMilikGuru(): Absensi
    {
        abort_if(!$this->absensiId, 404);

        return Absensi::whereHas('pengajaran', function ($query) {
            $query->where('guru_id', $this->guru->id);
        })->findOrFail($this->absensiId);
    }

    public function buatAbsensi(int $materiId): void
    {
        $guru = $this->guru;

        $materi = Materi::whereHas('pengajaran', function ($query) use ($guru) {
            $query->where('guru_id', $guru->id);
        })->findOrFail($materiId);

        // Jika sesi absensi sudah ada, langsung tampilkan.
        if ($materi->absensi) {
            $this->pilihAbsensi($materi->absensi->id);
            return;
        }

        $absensi = Absensi::create([
            'pengajaran_id' => $materi->pengajaran_id,
            'materi_id'     => $materi->id,
            'tanggal'       => now()->toDateString(),
            'jam_mulai'     => now()->format('H:i:s'),
            'kode_absensi'  => strtoupper(\Illuminate\Support\Str::random(6)),
            'status'        => 'dibuka',
        ]);

        $this->absensiId = $absensi->id;

        // Isi daftar siswa untuk sesi absensi yang baru dibuat.
        // Sesuaikan relasi ini jika struktur siswa-kelas di proyek berbeda.
        $siswaList = Siswa::whereHas('kelas', function ($query) use ($materi) {
            $query->where('kelas.id', $materi->pengajaran->kelas_id);
        })->get();

        foreach ($siswaList as $siswa) {
            AbsensiSiswa::firstOrCreate(
                [
                    'absensi_id' => $absensi->id,
                    'siswa_id'   => $siswa->id,
                ],
                [
                    'status' => 'alpa',
                ]
            );
        }

        session()->flash('success', 'Sesi absensi berhasil dibuat dan dibuka.');

        $this->dispatch('$refresh');
    }

    public function bukaModalAbsensi(int $materiId): void
    {
        $guru = $this->guru;

        $materi = Materi::whereHas('pengajaran', function ($query) use ($guru) {
            $query->where('guru_id', $guru->id);
        })->findOrFail($materiId);

        $this->absensiMateriId = $materi->id;
        $this->jamMulai = '';
        $this->jamSelesai = '';

        $this->resetValidation();

        $this->dispatch('open-absensi-modal');
    }

    public function simpanBukaAbsensi(): void
    {
        $this->validate([
            'absensiMateriId' => ['required', 'integer', 'exists:materis,id'],
            'jamMulai' => ['required', 'date_format:H:i'],
            'jamSelesai' => [
                'required',
                'date_format:H:i',
                'after:jamMulai',
            ],
        ], [
            'jamMulai.required' => 'Jam mulai wajib diisi.',
            'jamSelesai.required' => 'Jam selesai wajib diisi.',
            'jamSelesai.after' => 'Jam selesai harus setelah jam mulai.',
        ]);

        $guru = $this->guru;

        $materi = Materi::whereHas('pengajaran', function ($query) use ($guru) {
            $query->where('guru_id', $guru->id);
        })->findOrFail($this->absensiMateriId);

        if ($materi->absensi) {
            $absensi = $materi->absensi;

            if ($absensi->status === 'dibuka') {
                $this->addError('absensiMateriId', 'Sesi absensi sudah dibuka.');
                return;
            }

            $absensi->update([
                'tanggal' => now()->toDateString(),
                'jam_mulai' => $this->jamMulai,
                'jam_selesai' => $this->jamSelesai,
                'status' => 'dibuka',
            ]);
        } else {
            $absensi = Absensi::create([
                'pengajaran_id' => $materi->pengajaran_id,
                'materi_id' => $materi->id,
                'tanggal' => now()->toDateString(),
                'jam_mulai' => $this->jamMulai,
                'jam_selesai' => $this->jamSelesai,
                'kode_absensi' => strtoupper(\Illuminate\Support\Str::random(6)),
                'status' => 'dibuka',
            ]);
        }

        $this->absensiId = $absensi->id;

        $this->dispatch('close-absensi-modal');

        session()->flash('success', 'Sesi absensi berhasil dijadwalkan.');
    }

    public function render()
    {
        $materis = $this->guruMateriQuery()
            ->with(['pengajaran.kelas'])
            ->when($this->search, function ($query) {
                $query->where('judul', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterPengajaran, function ($query) {
                $query->where('pengajaran_id', $this->filterPengajaran);
            })
            ->latest()
            ->get();

        $absensi = null;

        if ($this->absensiId) {
            $absensi = $this->absensiMilikGuru()->load([
                'materi',
                'pengajaran.kelas',
                'siswaAbsensi.siswa',
            ]);
        }

        return view('livewire.guru.materi.materi-absensi-manager', [
            'materis' => $materis,
            'absensi' => $absensi,
        ])
            ->layout('guru.layouts.app')
            ->title('Materi Pembelajaran & Absensi');
    }
}
