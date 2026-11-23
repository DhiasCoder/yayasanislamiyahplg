<?php

namespace App\Livewire\Guru\TugasUjian;

use App\Models\Ujian;
use App\Models\UjianQuestion;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

#[\Livewire\Attributes\Layout('guru.layouts.app')]
class UjianQuestionManager extends Component
{
    /**
     * Nilai enum tipe_soal pada tabel ujian_questions.
     */
    public const TIPE_SOAL = ['pilihan_ganda', 'essay'];

    public Ujian $ujian;

    public $tipe_soal = 'pilihan_ganda';
    public $pertanyaan = '';
    public $opsi_a = '';
    public $opsi_b = '';
    public $opsi_c = '';
    public $opsi_d = '';
    public $jawaban_benar = 'a';
    public $bobot = 10;

    public $questionId = null;
    public $modeEdit = false;

    public function mount(Ujian $ujian)
    {
        $guru = Auth::user()->guru;
        abort_unless($guru, 403);

        // Pastikan ujian ini milik pengajaran guru yang login.
        abort_unless(
            $ujian->pengajaran()
                ->where('guru_id', $guru->id)
                ->exists(),
            403
        );

        $this->ujian = $ujian;
    }

    protected function rules(): array
    {
        // Opsi dan kunci jawaban hanya wajib untuk soal pilihan ganda
        $pilihanGanda = $this->tipe_soal === 'pilihan_ganda';
        $wajibPg = $pilihanGanda ? 'required' : 'nullable';

        return [
            'tipe_soal' => ['required', 'in:' . implode(',', self::TIPE_SOAL)],
            'pertanyaan' => ['required', 'string'],
            'opsi_a' => [$wajibPg, 'string'],
            'opsi_b' => [$wajibPg, 'string'],
            'opsi_c' => [$wajibPg, 'string'],
            'opsi_d' => [$wajibPg, 'string'],
            'jawaban_benar' => [$wajibPg, 'in:a,b,c,d'],
            'bobot' => ['required', 'numeric', 'min:1'],
        ];
    }

    /**
     * Atribut yang disimpan ke database sesuai tipe soal.
     * Soal essay tidak memiliki opsi maupun kunci jawaban.
     */
    protected function atributSoal(array $data): array
    {
        $pilihanGanda = $data['tipe_soal'] === 'pilihan_ganda';

        return [
            'pertanyaan' => $data['pertanyaan'],
            'tipe_soal' => $data['tipe_soal'],
            'opsi_a' => $pilihanGanda ? $data['opsi_a'] : null,
            'opsi_b' => $pilihanGanda ? $data['opsi_b'] : null,
            'opsi_c' => $pilihanGanda ? $data['opsi_c'] : null,
            'opsi_d' => $pilihanGanda ? $data['opsi_d'] : null,
            'jawaban_benar' => $pilihanGanda ? $data['jawaban_benar'] : null,
            'bobot' => $data['bobot'],
        ];
    }

    public function simpan()
    {
        $data = $this->validate();

        UjianQuestion::create(
            ['ujian_id' => $this->ujian->id] + $this->atributSoal($data)
        );

        $this->resetForm();

        session()->flash('success', 'Soal berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $soal = $this->ujian->questions()->findOrFail($id);

        $this->resetValidation();

        $this->questionId = $soal->id;
        $this->tipe_soal = $soal->tipe_soal ?? 'pilihan_ganda';
        $this->pertanyaan = $soal->pertanyaan;
        $this->opsi_a = $soal->opsi_a ?? '';
        $this->opsi_b = $soal->opsi_b ?? '';
        $this->opsi_c = $soal->opsi_c ?? '';
        $this->opsi_d = $soal->opsi_d ?? '';
        $this->jawaban_benar = $soal->jawaban_benar ?? 'a';
        $this->bobot = $soal->bobot;

        $this->modeEdit = true;
    }

    public function update()
    {
        $soal = $this->ujian->questions()
            ->findOrFail($this->questionId);

        // Tipe soal tidak boleh diubah jika sudah ada jawaban siswa
        if ($soal->jawabans()->exists()) {
            $this->tipe_soal = $soal->tipe_soal;
        }

        $data = $this->validate();

        $soal->update($this->atributSoal($data));

        $this->resetForm();

        session()->flash('success', 'Soal berhasil diperbarui.');
    }

    public function hapus($id)
    {
        $this->ujian->questions()->findOrFail($id)->delete();

        // Jika soal yang dihapus sedang diedit, kosongkan formulir
        if ((int) $this->questionId === (int) $id) {
            $this->resetForm();
        }

        session()->flash('success', 'Soal berhasil dihapus.');
    }

    public function batalEdit()
    {
        $this->resetForm();
    }

    private function resetForm()
    {
        // tipe_soal sengaja tidak direset agar guru bisa menambah beberapa soal sejenis berturut-turut
        $this->reset([
            'pertanyaan',
            'opsi_a',
            'opsi_b',
            'opsi_c',
            'opsi_d',
            'questionId',
            'modeEdit',
        ]);

        $this->jawaban_benar = 'a';
        $this->bobot = 10;
        $this->resetValidation();
    }

    public function publikasikan()
    {
        // Pastikan ujian memiliki minimal satu soal
        $jumlahSoal = $this->ujian->questions()->count();

        if ($jumlahSoal < 1) {
            session()->flash('error', 'Tambahkan minimal satu soal sebelum mempublikasikan ujian.');
            return;
        }

        // Pastikan jadwal ujian tersedia
        if (!$this->ujian->tanggal_mulai || !$this->ujian->tanggal_selesai) {
            session()->flash('error', 'Tanggal mulai dan selesai ujian harus diisi.');
            return;
        }

        // Pastikan tanggal mulai lebih awal dari tanggal selesai
        $tanggalMulai = \Carbon\Carbon::parse($this->ujian->tanggal_mulai);
        $tanggalSelesai = \Carbon\Carbon::parse($this->ujian->tanggal_selesai);

        if ($tanggalMulai->gte($tanggalSelesai)) {
            session()->flash('error', 'Tanggal selesai harus setelah tanggal mulai.');
            return;
        }

        // Pastikan ujian masih draft
        if ($this->ujian->status !== 'draft') {
            session()->flash('error', 'Ujian ini sudah dipublikasikan.');
            return;
        }

        $this->ujian->update([
            'status' => 'published',
        ]);

        session()->flash('success', 'Ujian berhasil dipublikasikan.');

        return redirect()->route('guru.tugas-ujian');
    }

    public function render()
    {
        // Tipe soal dikunci saat mengedit soal yang sudah dijawab siswa
        $tipeTerkunci = $this->modeEdit
            && $this->questionId
            && UjianQuestion::whereKey($this->questionId)->whereHas('jawabans')->exists();

        return view('livewire.guru.tugas-ujian.ujian-question-manager', [
            // Urut sesuai id agar nomor soal sama dengan yang dilihat siswa
            'soals' => $this->ujian->questions()->orderBy('id')->get(),
            'tipeTerkunci' => $tipeTerkunci,
        ]);
    }
}
