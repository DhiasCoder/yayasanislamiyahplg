<?php

namespace App\Livewire\Guru\TugasUjian;

use App\Models\Tugas;
use App\Models\Ujian;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

#[\Livewire\Attributes\Layout('guru.layouts.app')]
class TugasUjianManager extends Component
{
    public $search = '';
    public $filterJenis = 'semua';

    public function updatingSearch()
    {
        // Jika nanti menggunakan pagination, reset halaman di sini.
    }

    public function render()
    {
        $guru = Auth::user()->guru;
        abort_unless($guru, 404);

        $aktivitas = collect();

        // Ambil tugas yang dibuat guru
        if (in_array($this->filterJenis, ['semua', 'tugas'])) {
            $tugas = Tugas::query()
                ->with(['pengajaran.kelas', 'pengajaran.mataPelajaran'])
                ->withCount('pengumpulan')
                ->where('guru_id', $guru->id)
                ->when($this->search, function ($query) {
                    $query->where('judul', 'like', '%' . $this->search . '%');
                })
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'judul' => $item->judul,
                        'deskripsi' => $item->deskripsi,
                        'jenis' => 'Tugas',
                        'kelas' => $item->pengajaran?->kelas?->nama_kelas ?? '-',
                        'mapel' => $item->pengajaran?->mataPelajaran?->nama ?? '-',
                        'tanggal_mulai' => $item->tanggal_mulai,
                        'deadline' => $item->deadline,
                        'status' => $this->statusTugas($item),
                        'jumlah_pengumpulan' => $item->pengumpulan_count,
                        'nilai_maksimal' => $item->nilai_maksimal,
                    ];
                });

            $aktivitas = $aktivitas->concat($tugas);
        }

        // Ambil ujian berdasarkan pengajaran milik guru
        if (in_array($this->filterJenis, ['semua', 'ujian'])) {
            $ujian = Ujian::query()
                ->with(['pengajaran.kelas', 'pengajaran.mataPelajaran'])
                ->withCount('results')
                ->whereHas('pengajaran', function ($query) use ($guru) {
                    $query->where('guru_id', $guru->id);
                })
                ->when($this->search, function ($query) {
                    $query->where('judul', 'like', '%' . $this->search . '%');
                })
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'judul' => $item->judul,
                        'deskripsi' => $item->deskripsi,
                        'jenis' => strtoupper($item->jenis),
                        'kelas' => $item->pengajaran?->kelas?->nama_kelas ?? '-',
                        'mapel' => $item->pengajaran?->mataPelajaran?->nama ?? '-',
                        'tanggal_mulai' => $item->tanggal_mulai,
                        'deadline' => $item->tanggal_selesai,
                        'status' => $this->statusUjian($item),
                        'jumlah_pengumpulan' => $item->results_count,
                        'nilai_maksimal' => 100,
                    ];
                });

            $aktivitas = $aktivitas->concat($ujian);
        }

        $aktivitas = $aktivitas
            ->sortByDesc('tanggal_mulai')
            ->values();

        return view('livewire.guru.tugas-ujian.tugas-ujian-manager', [
            'aktivitas' => $aktivitas,
        ]);
    }

    private function statusTugas($tugas): string
    {
        if (!$tugas->is_active) {
            return 'Nonaktif';
        }

        if (now()->lt($tugas->tanggal_mulai)) {
            return 'Terjadwal';
        }

        if (now()->gt($tugas->deadline)) {
            return 'Selesai';
        }

        return 'Berlangsung';
    }

    private function statusUjian($ujian): string
    {
        if ($ujian->status === 'draft') {
            return 'Draft';
        }

        if ($ujian->status !== 'aktif') {
            return ucfirst($ujian->status);
        }

        if (now()->lt($ujian->tanggal_mulai)) {
            return 'Terjadwal';
        }

        if (now()->gt($ujian->tanggal_selesai)) {
            return 'Selesai';
        }

        return 'Berlangsung';
    }
}
