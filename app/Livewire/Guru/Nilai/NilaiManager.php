<?php

namespace App\Livewire\Guru\Nilai;

use Livewire\Component;
use App\Models\Pengajaran;
use App\Models\Tugas;
use App\Models\Ujian;
use App\Models\PengumpulanTugas;
use App\Models\UjianResult;
use Illuminate\Support\Facades\Auth;

class NilaiManager extends Component
{
    public $tabAktif = 'tugas';

    public $pengajaran_id = '';
    public $tugas_id = '';
    public $ujian_id = '';

    public $nilaiInputs = [];
    public $feedbackInputs = [];

    private function guru()
    {
        $guru = Auth::user()->guru;

        abort_if(!$guru, 403);

        return $guru;
    }

    public function updatedPengajaranId()
    {
        $this->tugas_id = '';
        $this->ujian_id = '';
        $this->nilaiInputs = [];
        $this->feedbackInputs = [];
    }

    public function updatedTugasId()
    {
        $this->nilaiInputs = [];
        $this->feedbackInputs = [];

        if (!$this->tugas_id) {
            return;
        }

        $tugas = Tugas::where('guru_id', $this->guru()->id)
            ->where('pengajaran_id', $this->pengajaran_id)
            ->findOrFail($this->tugas_id);

        foreach ($tugas->pengumpulan as $pengumpulan) {
            $this->nilaiInputs[$pengumpulan->id] = $pengumpulan->nilai;
            $this->feedbackInputs[$pengumpulan->id] = $pengumpulan->feedback_guru;
        }
    }

    public function simpanNilaiTugas()
    {
        $guru = $this->guru();
        $this->resetErrorBag();
        $tugas = Tugas::where('guru_id', $guru->id)
            ->where('pengajaran_id', $this->pengajaran_id)
            ->findOrFail($this->tugas_id);

        $pengumpulan = $tugas->pengumpulan()->get();

        foreach ($pengumpulan as $item) {
            $nilai = $this->nilaiInputs[$item->id] ?? null;

            if ($nilai === null || $nilai === '') {
                continue;
            }

            if (!is_numeric($nilai) || $nilai < 0 || $nilai > $tugas->nilai_maksimal) {
                $this->addError(
                    "nilaiInputs.{$item->id}",
                    "Nilai harus antara 0 sampai {$tugas->nilai_maksimal}."
                );

                continue;
            }

            $item->update([
                'nilai' => $nilai,
                'feedback_guru' => $this->feedbackInputs[$item->id] ?? null,
            ]);
        }

        if (count($this->getErrorBag()) > 0) {
            return;
        }

        session()->flash('success', 'Nilai tugas berhasil disimpan.');
    }

    public function render()
    {
        $guru = $this->guru();

        $pengajaranList = Pengajaran::with(['kelas', 'mataPelajaran'])
            ->where('guru_id', $guru->id)
            ->where('is_active', true)
            ->get();

        $tugasList = collect();
        $ujianList = collect();
        $pengumpulan = collect();
        $hasilUjian = collect();

        if ($this->pengajaran_id) {
            $pengajaran = $pengajaranList->firstWhere(
                'id',
                (int) $this->pengajaran_id
            );

            if ($pengajaran) {
                $tugasList = Tugas::where('guru_id', $guru->id)
                    ->where('pengajaran_id', $pengajaran->id)
                    ->latest()
                    ->get();

                $ujianList = Ujian::where('pengajaran_id', $pengajaran->id)
                    ->withCount('results')
                    ->latest()
                    ->get();

                if ($this->tugas_id) {
                    $pengumpulan = PengumpulanTugas::with('siswa.user')
                        ->where('tugas_id', $this->tugas_id)
                        ->latest()
                        ->get();
                }

                if ($this->ujian_id) {
                    $ujian = $ujianList->firstWhere(
                        'id',
                        (int) $this->ujian_id
                    );

                    if ($ujian) {
                        $hasilUjian = UjianResult::with('siswa.user')
                            ->where('ujian_id', $ujian->id)
                            ->latest()
                            ->get();
                    }
                }
            }
        }

        return view('livewire.guru.nilai.nilai-manager', [
            'pengajaranList' => $pengajaranList,
            'tugasList' => $tugasList,
            'ujianList' => $ujianList,
            'pengumpulan' => $pengumpulan,
            'hasilUjian' => $hasilUjian,
        ])->layout('guru.layouts.app');
    }
}
