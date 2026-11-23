<?php

namespace App\Livewire\Murid\Nilai;

use Livewire\Component;
use App\Models\Nilai;
use App\Models\PengumpulanTugas;
use App\Models\UjianResult;
use Illuminate\Support\Facades\Auth;

class NilaiSaya extends Component
{
    public $filter = 'semua';

    public function render()
    {
        $siswa = Auth::user()->siswa;
        abort_if(!$siswa, 403);

        // Nilai umum
        $nilaiUmum = Nilai::with([
            'pengajaran.mataPelajaran',
        ])
            ->where('siswa_id', $siswa->id)
            ->whereNotNull('nilai')
            ->get()
            ->map(fn($item) => [
                'kategori' => 'nilai',
                'jenis' => $item->jenis_nilai,
                'judul' => $item->keterangan ?: $item->jenis_nilai,
                'mapel' => $item->pengajaran?->mataPelajaran?->nama ?? '-',
                'nilai' => (float) $item->nilai,
                'tanggal' => $item->created_at,
                'keterangan' => $item->keterangan,
            ]);

        // Nilai tugas
        $nilaiTugas = PengumpulanTugas::with([
            'tugas.pengajaran.mataPelajaran',
        ])
            ->where('siswa_id', $siswa->id)
            ->whereNotNull('nilai')
            ->get()
            ->map(fn($item) => [
                'kategori' => 'tugas',
                'jenis' => 'Tugas',
                'judul' => $item->tugas?->judul ?? 'Tugas',
                'mapel' => $item->tugas?->pengajaran?->mataPelajaran?->nama ?? '-',
                'nilai' => (float) $item->nilai,
                'tanggal' => $item->tanggal_pengumpulan,
                'keterangan' => $item->feedback_guru,
            ]);

        // Nilai kuis dan ujian
        $nilaiUjian = UjianResult::with([
            'ujian.pengajaran.mataPelajaran',
        ])
            ->where('siswa_id', $siswa->id)
            ->where('status', 'selesai')
            ->whereNotNull('skor')
            ->get()
            ->map(function ($item) {
                $jenis = strtolower($item->ujian?->jenis ?? 'ujian');

                $kategori = str_contains($jenis, 'kuis')
                    ? 'kuis'
                    : 'ujian';

                return [
                    'kategori' => $kategori,
                    'jenis' => $item->ujian?->jenis ?? 'Ujian',
                    'judul' => $item->ujian?->judul ?? 'Ujian',
                    'mapel' => $item->ujian?->pengajaran?->mataPelajaran?->nama ?? '-',
                    'nilai' => (float) $item->skor,
                    'tanggal' => $item->tanggal_selesai,
                    'keterangan' => null,
                ];
            });

        $semuaNilai = $nilaiUmum
            ->concat($nilaiTugas)
            ->concat($nilaiUjian)
            ->sortByDesc(fn($item) => $item['tanggal']?->timestamp ?? 0)
            ->values();

        $nilaiSaya = $this->filter === 'semua'
            ? $semuaNilai
            : $semuaNilai->where('kategori', $this->filter)->values();

        return view('livewire.murid.nilai.nilai-saya', [
            'nilaiSaya' => $nilaiSaya,
            'totalNilai' => $semuaNilai->count(),
            'rataRata' => $semuaNilai->count()
                ? round($semuaNilai->avg('nilai'), 2)
                : 0,
        ])->layout('murid.layouts.app');
    }
}
