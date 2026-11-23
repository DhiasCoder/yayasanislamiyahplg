<?php

namespace App\Livewire\Guru\TugasUjian;

use App\Models\Ujian;
use App\Models\Pengajaran;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Support\Carbon;

#[\Livewire\Attributes\Layout('guru.layouts.app')]
class UjianEdit extends Component
{
    public Ujian $ujian;

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

    public function mount(Ujian $ujian)
    {
        $guru = Auth::user()->guru;
        abort_unless($guru, 403);

        abort_unless(
            $ujian->pengajaran()
                ->where('guru_id', $guru->id)
                ->exists(),
            403
        );

        $this->ujian = $ujian;

        $this->pengajaran_id = $ujian->pengajaran_id;
        $this->judul = $ujian->judul;
        $this->deskripsi = $ujian->deskripsi;
        $this->jenis = $ujian->jenis;

        $this->tanggal_mulai = $ujian->tanggal_mulai
            ? Carbon::parse($ujian->tanggal_mulai)->format('Y-m-d\TH:i')
            : '';

        $this->tanggal_selesai = $ujian->tanggal_selesai
            ? Carbon::parse($ujian->tanggal_selesai)->format('Y-m-d\TH:i')
            : '';

        $this->durasi = $ujian->durasi;
        $this->acak_soal = $ujian->acak_soal;
        $this->acak_jawaban = $ujian->acak_jawaban;
        $this->nilai_minimal = $ujian->nilai_minimal;
    }

    public function update()
    {
        $guru = Auth::user()->guru;
        abort_unless($guru, 403);

        $data = $this->validate([
            'pengajaran_id' => ['required', 'integer'],
            'judul' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'jenis' => ['required', 'string', 'max:50'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after:tanggal_mulai'],
            'durasi' => ['required', 'integer', 'min:1'],
            'acak_soal' => ['boolean'],
            'acak_jawaban' => ['boolean'],
            'nilai_minimal' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        $pengajaran = Pengajaran::where('id', $data['pengajaran_id'])
            ->where('guru_id', $guru->id)
            ->firstOrFail();

        $this->ujian->update([
            ...$data,
            'pengajaran_id' => $pengajaran->id,
        ]);

        session()->flash('success', 'Ujian berhasil diperbarui.');

        return redirect()->route('guru.tugas-ujian');
    }

    public function render()
    {
        $guru = Auth::user()->guru;
        abort_unless($guru, 403);

        return view('livewire.guru.tugas-ujian.ujian-edit', [
            'pengajarans' => Pengajaran::with(['kelas', 'mataPelajaran'])
                ->where('guru_id', $guru->id)
                ->where('is_active', true)
                ->get(),
        ]);
    }
}
