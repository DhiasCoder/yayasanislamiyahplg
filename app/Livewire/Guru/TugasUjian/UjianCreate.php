<?php

namespace App\Livewire\Guru\TugasUjian;

use App\Models\Pengajaran;
use App\Models\Ujian;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

#[\Livewire\Attributes\Layout('guru.layouts.app')]
class UjianCreate extends Component
{
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

    public function simpan()
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

        $ujian = Ujian::create([
            ...$data,
            'pengajaran_id' => $pengajaran->id,
        ]);

        session()->flash('success', 'Ujian berhasil dibuat. Silakan tambahkan soal.');

        return redirect()->route('guru.ujian.soal', $ujian->id);
    }

    public function render()
    {
        $guru = Auth::user()->guru;
        abort_unless($guru, 403);

        return view('livewire.guru.tugas-ujian.ujian-create', [
            'pengajarans' => Pengajaran::with(['kelas', 'mataPelajaran'])
                ->where('guru_id', $guru->id)
                ->where('is_active', true)
                ->get(),
        ]);
    }
}
