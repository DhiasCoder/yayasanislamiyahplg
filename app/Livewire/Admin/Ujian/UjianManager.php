<?php

namespace App\Livewire\Admin\Ujian;

use App\Models\Ujian;
use App\Models\UjianQuestion;
use App\Models\UjianResult;
use App\Models\Pengajaran;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class UjianManager extends Component
{
    use WithPagination;

    public $tab = 'ujian';
    public $search = '';

    public $ujianId = null;
    public $showUjianForm = false;

    public $pengajaran_id = '';
    public $judul = '';
    public $deskripsi = '';
    public $jenis = 'kuis';
    public $tanggal_mulai = '';
    public $tanggal_selesai = '';
    public $durasi = 60;
    public $acak_soal = false;
    public $acak_jawaban = false;
    public $nilai_minimal = 75;
    public $status = 'draft';

    public $selectedUjianId = null;
    public $showQuestionForm = false;
    public $questionId = null;

    public $pertanyaan = '';
    public $tipe_soal = 'pilihan_ganda';
    public $opsi_a = '';
    public $opsi_b = '';
    public $opsi_c = '';
    public $opsi_d = '';
    public $jawaban_benar = '';
    public $bobot = 1;

    private function sekolahId()
    {
        return Auth::user()
            ->userRoles()
            ->whereHas('role', fn ($q) =>
                $q->where('nama_role', 'admin')
            )
            ->value('sekolah_id');
    }

    private function queryUjian()
    {
        return Ujian::query()
            ->whereHas('pengajaran.kelas', fn ($q) =>
                $q->where('sekolah_id', $this->sekolahId())
            );
    }

    private function queryPengajaran()
    {
        return Pengajaran::query()
            ->with(['guru', 'mataPelajaran', 'kelas', 'tahunAjaran'])
            ->where('is_active', true)
            ->whereHas('kelas', fn ($q) =>
                $q->where('sekolah_id', $this->sekolahId())
            );
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function gantiTab($tab)
    {
        if (! in_array($tab, ['ujian', 'soal', 'hasil'], true)) {
            return;
        }

        $this->tab = $tab;
        $this->resetPage();
    }

    public function pilihUjian($id, $tab = 'soal')
    {
        $ujian = $this->queryUjian()->findOrFail($id);

        $this->selectedUjianId = $ujian->id;
        $this->tab = in_array($tab, ['soal', 'hasil'], true)
            ? $tab
            : 'soal';

        $this->resetPage();
    }

    public function resetUjianForm()
    {
        $this->reset([
            'ujianId',
            'pengajaran_id',
            'judul',
            'deskripsi',
            'tanggal_mulai',
            'tanggal_selesai',
        ]);

        $this->jenis = 'kuis';
        $this->durasi = 60;
        $this->acak_soal = false;
        $this->acak_jawaban = false;
        $this->nilai_minimal = 75;
        $this->status = 'draft';
        $this->resetValidation();
        $this->showUjianForm = true;
    }

    public function editUjian($id)
    {
        $ujian = $this->queryUjian()->findOrFail($id);

        $this->ujianId = $ujian->id;
        $this->pengajaran_id = $ujian->pengajaran_id;
        $this->judul = $ujian->judul;
        $this->deskripsi = $ujian->deskripsi;
        $this->jenis = $ujian->jenis;
        $this->tanggal_mulai = $ujian->tanggal_mulai?->format('Y-m-d\TH:i');
        $this->tanggal_selesai = $ujian->tanggal_selesai?->format('Y-m-d\TH:i');
        $this->durasi = $ujian->durasi;
        $this->acak_soal = $ujian->acak_soal;
        $this->acak_jawaban = $ujian->acak_jawaban;
        $this->nilai_minimal = $ujian->nilai_minimal;
        $this->status = $ujian->status;

        $this->resetValidation();
        $this->showUjianForm = true;
    }

    public function simpanUjian()
    {
        $this->validate([
            'pengajaran_id' => [
                'required',
                Rule::exists('pengajarans', 'id')->where(
                    fn ($q) => $q->where('is_active', true)
                ),
            ],
            'judul' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'jenis' => [
                'required',
                Rule::in(['latihan', 'kuis', 'UTS', 'UAS', 'ujian']),
            ],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => [
                'required',
                'date',
                'after:tanggal_mulai',
            ],
            'durasi' => ['required', 'integer', 'min:1'],
            'acak_soal' => ['boolean'],
            'acak_jawaban' => ['boolean'],
            'nilai_minimal' => ['required', 'numeric', 'min:0', 'max:100'],
            'status' => [
                'required',
                Rule::in(['draft', 'published', 'closed']),
            ],
        ]);

        // Pastikan pengajaran benar-benar milik sekolah admin.
        $this->queryPengajaran()->findOrFail($this->pengajaran_id);

        $data = [
            'pengajaran_id' => $this->pengajaran_id,
            'judul' => $this->judul,
            'deskripsi' => $this->deskripsi,
            'jenis' => $this->jenis,
            'tanggal_mulai' => $this->tanggal_mulai,
            'tanggal_selesai' => $this->tanggal_selesai,
            'durasi' => $this->durasi,
            'acak_soal' => $this->acak_soal,
            'acak_jawaban' => $this->acak_jawaban,
            'nilai_minimal' => $this->nilai_minimal,
            'status' => $this->status,
        ];

        if ($this->ujianId) {
            $ujian = $this->queryUjian()->findOrFail($this->ujianId);

            // Hindari mengubah ujian yang sudah memiliki hasil pengerjaan.
            if ($ujian->results()->exists()) {
                $data = collect($data)
                    ->except(['pengajaran_id', 'jenis'])
                    ->all();
            }

            $ujian->update($data);
            session()->flash('success', 'Ujian berhasil diperbarui.');
        } else {
            Ujian::create($data);
            session()->flash('success', 'Ujian berhasil ditambahkan.');
        }

        $this->showUjianForm = false;
        $this->resetPage();
    }

    public function hapusUjian($id)
    {
        $ujian = $this->queryUjian()->findOrFail($id);

        // Ujian yang sudah memiliki hasil tidak dihapus agar riwayat aman.
        if ($ujian->results()->exists()) {
            session()->flash(
                'error',
                'Ujian tidak dapat dihapus karena sudah memiliki hasil siswa.'
            );

            return;
        }

        $ujian->delete();

        if ($this->selectedUjianId == $id) {
            $this->selectedUjianId = null;
            $this->tab = 'ujian';
        }

        session()->flash('success', 'Ujian berhasil dihapus.');
    }

    public function resetQuestionForm()
    {
        $this->reset([
            'questionId',
            'pertanyaan',
            'opsi_a',
            'opsi_b',
            'opsi_c',
            'opsi_d',
            'jawaban_benar',
        ]);

        $this->tipe_soal = 'pilihan_ganda';
        $this->bobot = 1;
        $this->resetValidation();
        $this->showQuestionForm = true;
    }

    public function editQuestion($id)
    {
        $soal = UjianQuestion::query()
            ->where('ujian_id', $this->queryUjian()
                ->whereKey($this->selectedUjianId)
                ->value('id'))
            ->findOrFail($id);

        $this->questionId = $soal->id;
        $this->pertanyaan = $soal->pertanyaan;
        $this->tipe_soal = $soal->tipe_soal;
        $this->opsi_a = $soal->opsi_a;
        $this->opsi_b = $soal->opsi_b;
        $this->opsi_c = $soal->opsi_c;
        $this->opsi_d = $soal->opsi_d;
        $this->jawaban_benar = $soal->jawaban_benar;
        $this->bobot = $soal->bobot;

        $this->resetValidation();
        $this->showQuestionForm = true;
    }

    public function simpanQuestion()
    {
        $ujian = $this->queryUjian()->findOrFail($this->selectedUjianId);

        if ($ujian->results()->exists()) {
            session()->flash(
                'error',
                'Soal tidak dapat diubah setelah siswa mulai memiliki hasil ujian.'
            );

            return;
        }

        $rules = [
            'pertanyaan' => ['required', 'string'],
            'tipe_soal' => [
                'required',
                Rule::in(['pilihan_ganda', 'essay']),
            ],
            'bobot' => ['required', 'numeric', 'min:0.01', 'max:999.99'],
        ];

        if ($this->tipe_soal === 'pilihan_ganda') {
            $rules += [
                'opsi_a' => ['required', 'string'],
                'opsi_b' => ['required', 'string'],
                'opsi_c' => ['required', 'string'],
                'opsi_d' => ['required', 'string'],
                'jawaban_benar' => [
                    'required',
                    Rule::in(['A', 'B', 'C', 'D']),
                ],
            ];
        }

        $this->validate($rules);

        $data = [
            'ujian_id' => $ujian->id,
            'pertanyaan' => $this->pertanyaan,
            'tipe_soal' => $this->tipe_soal,
            'opsi_a' => $this->tipe_soal === 'pilihan_ganda' ? $this->opsi_a : null,
            'opsi_b' => $this->tipe_soal === 'pilihan_ganda' ? $this->opsi_b : null,
            'opsi_c' => $this->tipe_soal === 'pilihan_ganda' ? $this->opsi_c : null,
            'opsi_d' => $this->tipe_soal === 'pilihan_ganda' ? $this->opsi_d : null,
            'jawaban_benar' => $this->tipe_soal === 'pilihan_ganda'
                ? $this->jawaban_benar
                : null,
            'bobot' => $this->bobot,
        ];

        if ($this->questionId) {
            UjianQuestion::where('ujian_id', $ujian->id)
                ->findOrFail($this->questionId)
                ->update($data);
        } else {
            UjianQuestion::create($data);
        }

        $this->showQuestionForm = false;
        session()->flash('success', 'Soal berhasil disimpan.');
    }

    public function hapusQuestion($id)
    {
        $ujian = $this->queryUjian()->findOrFail($this->selectedUjianId);

        if ($ujian->results()->exists()) {
            session()->flash(
                'error',
                'Soal tidak dapat dihapus karena ujian sudah memiliki hasil siswa.'
            );

            return;
        }

        UjianQuestion::where('ujian_id', $ujian->id)
            ->findOrFail($id)
            ->delete();

        session()->flash('success', 'Soal berhasil dihapus.');
    }

    public function render()
    {
        $pengajarans = $this->queryPengajaran()->get();

        $ujians = $this->queryUjian()
            ->with(['pengajaran.guru', 'pengajaran.mataPelajaran', 'pengajaran.kelas'])
            ->withCount(['questions', 'results'])
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('judul', 'like', '%' . $this->search . '%')
                        ->orWhere('jenis', 'like', '%' . $this->search . '%')
                        ->orWhereHas('pengajaran.mataPelajaran', fn ($mapel) =>
                            $mapel->where('nama', 'like', '%' . $this->search . '%')
                        );
                });
            })
            ->latest()
            ->paginate(10);

        $ujianTerpilih = $this->selectedUjianId
            ? $this->queryUjian()
                ->with(['pengajaran.kelas', 'pengajaran.mataPelajaran'])
                ->find($this->selectedUjianId)
            : null;

        $soals = $ujianTerpilih
            ? $ujianTerpilih->questions()->latest()->get()
            : collect();

        $hasil = $ujianTerpilih
            ? UjianResult::with('siswa')
                ->where('ujian_id', $ujianTerpilih->id)
                ->latest()
                ->paginate(10)
            : null;

        return view('livewire.admin.ujian.ujian-manager', [
            'pengajarans' => $pengajarans,
            'ujians' => $ujians,
            'ujianTerpilih' => $ujianTerpilih,
            'soals' => $soals,
            'hasil' => $hasil,
        ])->layout('admin.layouts.app');
    }
}
