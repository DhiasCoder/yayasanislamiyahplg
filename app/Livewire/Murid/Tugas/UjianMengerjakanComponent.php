<?php

namespace App\Livewire\Murid\Tugas;

use App\Models\Ujian;
use App\Models\UjianResult;
use App\Models\UjianJawaban;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class UjianMengerjakanComponent extends Component
{
    public $ujianId;
    public $siswaId;
    public $resultId;

    public $questions = [];
    public $jawaban = [];

    public $currentIndex = 0;
    public $sisaDetik = 0;
    public $sudahSelesai = false;

    public function mount($ujianId)
    {
        $siswa = Auth::user()->siswa;

        abort_unless($siswa, 403, 'Profil siswa tidak ditemukan.');

        $this->siswaId = $siswa->id;
        $this->ujianId = $ujianId;

        $ujian = $this->ujianQuery()->findOrFail($ujianId);

        abort_if(
            !$ujian->tanggal_mulai || !$ujian->tanggal_selesai,
            403,
            'Jadwal ujian belum tersedia.'
        );

        abort_if(
            now()->lt($ujian->tanggal_mulai) ||
                now()->gt($ujian->tanggal_selesai),
            403,
            'Ujian belum dimulai atau sudah berakhir.'
        );

        abort_if($ujian->questions()->count() === 0, 403, 'Soal ujian belum tersedia.');

        $result = UjianResult::firstOrCreate(
            [
                'ujian_id' => $ujian->id,
                'siswa_id' => $this->siswaId,
            ],
            [
                'tanggal_mulai' => now(),
                'status' => 'sedang_mengerjakan',
            ]
        );

        abort_if(
            $result->status === 'selesai',
            403,
            'Ujian ini sudah pernah dikerjakan.'
        );

        if ($result->status === 'belum_mulai') {
            $result->update([
                'tanggal_mulai' => now(),
                'status' => 'sedang_mengerjakan',
            ]);
        }

        $this->resultId = $result->id;

        // Kunci jawaban tidak pernah dikirim ke browser.
        $this->questions = $ujian->questions()
            ->orderBy('id')
            ->get([
                'id',
                'pertanyaan',
                'tipe_soal',
                'opsi_a',
                'opsi_b',
                'opsi_c',
                'opsi_d',
                'bobot',
            ])
            ->toArray();

        $this->muatJawaban();
        $this->perbaruiWaktu();
    }

    protected function ujianQuery()
    {
        $kelasIds = Auth::user()->siswa
            ->kelas()
            ->pluck('kelas.id');

        return Ujian::whereHas('pengajaran', function ($query) use ($kelasIds) {
            $query->whereIn('kelas_id', $kelasIds)
                ->where('is_active', true);
        });
    }

    protected function muatJawaban()
    {
        $jawabans = UjianJawaban::where('ujian_result_id', $this->resultId)
            ->get();

        foreach ($jawabans as $item) {
            $this->jawaban[$item->ujian_question_id] = $item->jawaban;
        }
    }

    public function simpanJawaban($questionId)
    {
        $question = collect($this->questions)
            ->firstWhere('id', $questionId);

        abort_unless($question, 404);

        $result = $this->resultAktif();

        $value = $this->jawaban[$questionId] ?? null;

        if ($question['tipe_soal'] === 'pilihan_ganda') {
            $opsiValid = ['a', 'b', 'c', 'd'];

            if (
                $value !== null &&
                !in_array(strtolower(trim($value)), $opsiValid, true)
            ) {
                $this->addError(
                    "jawaban.$questionId",
                    'Pilihan jawaban tidak valid.'
                );

                return;
            }

            $value = $value !== null
                ? strtolower(trim($value))
                : null;
        } elseif ($question['tipe_soal'] === 'essay') {
            if ($value !== null && !is_string($value)) {
                abort(422);
            }

            if (is_string($value) && mb_strlen($value) > 10000) {
                $this->addError(
                    "jawaban.$questionId",
                    'Jawaban maksimal 10.000 karakter.'
                );
                return;
            }
        } else {
            abort(422, 'Tipe soal tidak dikenali.');
        }

        UjianJawaban::updateOrCreate(
            [
                'ujian_result_id' => $result->id,
                'ujian_question_id' => $questionId,
            ],
            [
                'jawaban' => $value,
            ]
        );

        $this->resetErrorBag("jawaban.$questionId");
    }

    public function keSoal($index)
    {
        $this->perbaruiWaktu();

        if ($this->sisaDetik <= 0) {
            $this->selesaikanUjian();
            return;
        }

        if (!isset($this->questions[$index])) {
            return;
        }

        $currentQuestion = $this->questions[$this->currentIndex];

        $this->simpanJawaban($currentQuestion['id']);

        $this->currentIndex = $index;
    }

    public function cekWaktu()
    {
        if ($this->sudahSelesai) {
            return;
        }

        $this->perbaruiWaktu();

        if ($this->sisaDetik <= 0) {
            $this->selesaikanUjian();
        }
    }


    protected function perbaruiWaktu()
    {
        $result = $this->resultAktif();
        $ujian = $this->ujianQuery()->findOrFail($this->ujianId);

        $tanggalMulai = \Carbon\Carbon::parse($result->tanggal_mulai);
        $batasWaktu = $tanggalMulai->addMinutes($ujian->durasi);

        $batasUjian = \Carbon\Carbon::parse($ujian->tanggal_selesai);

        if ($batasUjian->lt($batasWaktu)) {
            $batasWaktu = $batasUjian;
        }

        $this->sisaDetik = max(
            0,
            now()->diffInSeconds($batasWaktu, false)
        );
    }


    protected function resultAktif()
    {
        $result = UjianResult::where('id', $this->resultId)
            ->where('ujian_id', $this->ujianId)
            ->where('siswa_id', $this->siswaId)
            ->firstOrFail();

        abort_if(
            $result->status !== 'sedang_mengerjakan',
            403,
            'Ujian sudah tidak dapat dikerjakan.'
        );

        return $result;
    }

    public function selesaikanUjian()
    {
        $result = $this->resultAktif();

        DB::transaction(function () use ($result) {
            // Simpan semua jawaban terakhir dari halaman.
            foreach ($this->questions as $question) {
                $this->simpanJawaban($question['id']);
            }

            $jawabans = UjianJawaban::with('soal')
                ->where('ujian_result_id', $result->id)
                ->get();

            $totalBobot = 0;
            $totalBenar = 0;

            foreach ($jawabans as $jawaban) {
                $soal = $jawaban->soal;

                if (!$soal) {
                    continue;
                }

                $bobot = (float) $soal->bobot;
                $totalBobot += $bobot;

                if ($soal->tipe_soal === 'pilihan_ganda') {
                    $benar = $jawaban->jawaban !== null
                        && strtolower(trim($jawaban->jawaban)) ===
                        strtolower(trim($soal->jawaban_benar));

                    $jawaban->update([
                        'is_correct' => $benar,
                        'skor' => $benar ? $bobot : 0,
                    ]);

                    if ($benar) {
                        $totalBenar += $bobot;
                    }
                } else {
                    // Esai menunggu penilaian guru.
                    $jawaban->update([
                        'is_correct' => null,
                        'skor' => 0,
                    ]);
                }
            }

            // Nilai sementara dihitung dari seluruh bobot soal.
            // Esai bernilai 0 sampai dinilai guru.
            $skor = $totalBobot > 0
                ? ($totalBenar / $totalBobot) * 100
                : 0;

            $result->update([
                'skor' => round($skor, 2),
                'tanggal_selesai' => now(),
                'status' => 'selesai',
            ]);
        });

        $this->sudahSelesai = true;

        session()->flash(
            'success',
            'Ujian berhasil diselesaikan. Jawaban telah dikirim.'
        );

        return redirect()->route('siswa.tugas-ujian');
    }

    public function render()
    {
        return view('livewire.murid.tugas.ujian-mengerjakan-component')
            ->layout('murid.layouts.app');
    }
}
