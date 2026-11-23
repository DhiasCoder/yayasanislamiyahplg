<?php

namespace App\Livewire\Murid\Tugas;

use App\Models\Tugas;
use App\Models\Ujian;
use App\Models\PengumpulanTugas;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class TugasUjianComponent extends Component
{
    use WithFileUploads;

    /**
     * true = tampilkan kunci jawaban pada rincian jawaban siswa.
     * Biarkan false jika guru tidak ingin kunci terlihat oleh siswa.
     */
    protected const TAMPILKAN_KUNCI = false;

    public $tab = 'tugas';

    public $tugasDetail = null;
    public $fileJawaban;
    public $catatanSiswa = '';

    public $siswaId;
    public $kelasIds = [];
    public $ujianDetail = null;

    public function mount()
    {
        $siswa = Auth::user()->siswa;

        abort_if(!$siswa, 403, 'Profil siswa tidak ditemukan.');

        $this->siswaId = $siswa->id;

        $this->kelasIds = $siswa->kelas()
            ->pluck('kelas.id')
            ->toArray();
    }

    public function pilihTab($tab)
    {
        abort_unless(in_array($tab, ['tugas', 'ujian']), 422);

        $this->tab = $tab;
        $this->tutupDetail();
    }

    protected function tugasQuery()
    {
        return Tugas::query()
            ->where('is_active', true)
            ->whereHas('pengajaran', function ($query) {
                $query->whereIn('kelas_id', $this->kelasIds)
                    ->where('is_active', true);
            });
    }

    protected function ujianQuery()
    {
        return Ujian::query()
            ->whereHas('pengajaran', function ($query) {
                $query->whereIn('kelas_id', $this->kelasIds)
                    ->where('is_active', true);
            });
    }

    public function lihatTugas($id)
    {
        $this->resetValidation();

        $this->tugasDetail = $this->tugasQuery()
            ->with([
                'pengajaran.mataPelajaran',
                'pengajaran.guru',
                'pengumpulan' => function ($query) {
                    $query->where('siswa_id', $this->siswaId);
                },
            ])
            ->findOrFail($id);

        $pengumpulan = $this->tugasDetail->pengumpulan->first();

        $this->catatanSiswa = $pengumpulan?->catatan_siswa ?? '';
        $this->fileJawaban = null;
    }

    public function kumpulkanTugas()
    {
        abort_if(!$this->tugasDetail, 404);

        $this->validate([
            'fileJawaban' => 'required|file|mimes:pdf,doc,docx,zip,jpg,jpeg,png|max:10240',
            'catatanSiswa' => 'nullable|string|max:1000',
        ], [
            'fileJawaban.required' => 'File jawaban wajib diunggah.',
            'fileJawaban.mimes' => 'Format file harus PDF, DOC, DOCX, ZIP, JPG, JPEG, atau PNG.',
            'fileJawaban.max' => 'Ukuran file maksimal 10 MB.',
        ]);

        $tugas = $this->tugasQuery()->findOrFail($this->tugasDetail->id);

        if ($tugas->tanggal_mulai && now()->lt($tugas->tanggal_mulai)) {
            $this->addError('fileJawaban', 'Tugas belum dapat dikumpulkan.');
            return;
        }

        if (
            $tugas->deadline &&
            now()->gt($tugas->deadline) &&
            !$tugas->boleh_terlambat
        ) {
            $this->addError('fileJawaban', 'Batas pengumpulan tugas sudah berakhir.');
            return;
        }

        $pengumpulan = PengumpulanTugas::where('tugas_id', $tugas->id)
            ->where('siswa_id', $this->siswaId)
            ->first();

        // 1. Cek dulu sebelum file disimpan ke disk
        if ($pengumpulan && $pengumpulan->nilai !== null) {
            $this->addError(
                'fileJawaban',
                'Tugas sudah dinilai dan tidak dapat dikumpulkan ulang.'
            );

            return;
        }

        $path = $this->fileJawaban->store('pengumpulan-tugas', 'public');

        if ($pengumpulan?->file_jawaban) {
            Storage::disk('public')->delete($pengumpulan->file_jawaban);
        }

        if ($pengumpulan) {
            $pengumpulan->update([
                'file_jawaban' => $path,
                'catatan_siswa' => $this->catatanSiswa,
                'tanggal_pengumpulan' => now(),
                'status' => 'dikumpulkan',
            ]);
        } else {
            PengumpulanTugas::create([
                'tugas_id' => $tugas->id,
                'siswa_id' => $this->siswaId,
                'file_jawaban' => $path,
                'catatan_siswa' => $this->catatanSiswa,
                'tanggal_pengumpulan' => now(),
                'status' => 'dikumpulkan',
            ]);
        }

        $this->tugasDetail->refresh();
        $this->tugasDetail->load([
            'pengajaran.mataPelajaran',
            'pengajaran.guru',
            'pengumpulan' => fn($query) => $query->where('siswa_id', $this->siswaId),
        ]);

        $this->reset(['fileJawaban', 'catatanSiswa']);

        session()->flash('success', 'Tugas berhasil dikumpulkan.');
    }

    public function lihatUjian($id)
    {
        $this->ujianDetail = $this->ujianQuery()
            ->with([
                'pengajaran.mataPelajaran',
                'pengajaran.guru',
                'questions' => fn($query) => $query->orderBy('id'),
                'results' => function ($query) {
                    $query->where('siswa_id', $this->siswaId)
                        ->with('jawabans');
                },
            ])
            ->findOrFail($id);
    }

    public function tutupDetail()
    {
        $this->reset([
            'tugasDetail',
            'ujianDetail',
            'fileJawaban',
            'catatanSiswa',
        ]);

        $this->resetValidation();
    }

    /**
     * Rincian jawaban siswa untuk ujian yang sedang dibuka di modal detail.
     * Sumber data: UjianResult -> jawabans (UjianJawaban) dan Ujian -> questions (UjianQuestion).
     *
     * Mengembalikan null bila siswa belum punya hasil ujian.
     */
    protected function rincianUjian(): ?array
    {
        $ujian = $this->ujianDetail;
        $hasil = $ujian?->results->first();

        if (! $ujian || ! $hasil) {
            return null;
        }

        $jawabanPerSoal = $hasil->jawabans->keyBy('ujian_question_id');

        $ringkasan = ['total' => 0, 'dijawab' => 0, 'kosong' => 0, 'benar' => 0, 'salah' => 0];
        $daftar = [];

        foreach ($ujian->questions as $urutan => $soal) {
            $baris = $jawabanPerSoal->get($soal->id);
            $teks = $baris?->jawaban;
            $dijawab = filled($teks);
            $tipe = $soal->tipe_soal;

            $opsi = [];
            foreach (['a', 'b', 'c', 'd'] as $huruf) {
                $nilai = $soal->{'opsi_' . $huruf};

                if (filled($nilai)) {
                    $opsi[$huruf] = (string) $nilai;
                }
            }

            // Jawaban pilihan ganda tersimpan sebagai teks opsi, cari huruf yang dipilih
            $hurufDipilih = null;
            if ($tipe === 'pilihan_ganda' && $dijawab) {
                $ketemu = array_search((string) $teks, $opsi, true);
                $hurufDipilih = $ketemu === false ? null : $ketemu;
            }

            // Kunci jawaban (bisa berupa huruf atau teks opsi)
            $kunciMentah = filled($soal->jawaban_benar)
                ? strtolower(trim((string) $soal->jawaban_benar))
                : null;

            // Benar / salah hanya untuk pilihan ganda (essay dinilai manual lewat poin).
            // Utamakan hasil penilaian tersimpan (is_correct), cadangannya bandingkan dengan kunci.
            $benar = null;
            if ($tipe === 'pilihan_ganda' && $dijawab) {
                if ($baris->is_correct !== null) {
                    $benar = (bool) $baris->is_correct;
                } elseif ($kunciMentah !== null) {
                    $benar = $kunciMentah === strtolower((string) $hurufDipilih)
                        || $kunciMentah === strtolower(trim((string) $teks));
                }
            }

            $kunciHuruf = null;
            if (self::TAMPILKAN_KUNCI && $tipe === 'pilihan_ganda' && $kunciMentah !== null) {
                if (isset($opsi[$kunciMentah])) {
                    $kunciHuruf = $kunciMentah;
                } else {
                    $cari = array_search((string) $soal->jawaban_benar, $opsi, true);
                    $kunciHuruf = $cari === false ? null : $cari;
                }
            }

            $status = match (true) {
                ! $dijawab       => 'kosong',
                $benar === true  => 'benar',
                $benar === false => 'salah',
                default          => 'dijawab',
            };

            $ringkasan['total']++;
            $dijawab ? $ringkasan['dijawab']++ : $ringkasan['kosong']++;
            if ($status === 'benar') {
                $ringkasan['benar']++;
            } elseif ($status === 'salah') {
                $ringkasan['salah']++;
            }

            $daftar[] = [
                'no'         => $urutan + 1,
                'tipe'       => $tipe,
                'pertanyaan' => $soal->pertanyaan,
                'opsi'       => $opsi,
                'jawaban'    => $dijawab ? (string) $teks : null,
                'huruf'      => $hurufDipilih,
                'status'     => $status,
                'kunciHuruf' => $kunciHuruf,
                'skor'       => $baris?->skor,   // poin yang diperoleh (null = belum dinilai)
                'bobot'      => $soal->bobot,    // poin maksimal soal
            ];
        }

        return [
            'tersedia'     => true,
            'ringkasan'    => $ringkasan,
            'adaPenilaian' => ($ringkasan['benar'] + $ringkasan['salah']) > 0,
            'soal'         => $daftar,
        ];
    }

    public function render()
    {
        $tugas = $this->tugasQuery()
            ->with([
                'pengajaran.mataPelajaran',
                'pengajaran.guru',
                'pengumpulan' => fn($query) => $query
                    ->where('siswa_id', $this->siswaId),
            ])
            ->latest()
            ->get();

        $ujians = $this->ujianQuery()
            ->with([
                'pengajaran.mataPelajaran',
                'pengajaran.guru',
                'results' => fn($query) => $query
                    ->where('siswa_id', $this->siswaId),
            ])
            ->latest()
            ->get();

        $rincian = $this->ujianDetail ? $this->rincianUjian() : null;

        return view('livewire.murid.tugas.tugas-ujian-component', compact(
            'tugas',
            'ujians',
            'rincian'
        ))->layout('murid.layouts.app');
    }
}
