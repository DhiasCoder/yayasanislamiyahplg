<?php

namespace App\Livewire\Guru\TugasUjian;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Tugas;
use App\Models\Pengajaran;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;

class TugasForm extends Component
{
    use WithFileUploads;

    public $tugasId;

    public $pengajaran_id;
    public $judul;
    public $deskripsi;
    public $file_soal;
    public $fileLama;
    public $tanggal_mulai;
    public $deadline;
    public $nilai_maksimal = 100;
    public $boleh_terlambat = false;

    public function mount($tugas = null)
    {
        $guru = Auth::user()->guru;

        abort_if(!$guru, 403);

        if ($tugas) {
            $data = Tugas::where('guru_id', $guru->id)
                ->findOrFail($tugas);

            $this->tugasId = $data->id;
            $this->pengajaran_id = $data->pengajaran_id;
            $this->judul = $data->judul;
            $this->deskripsi = $data->deskripsi;
            $this->fileLama = $data->file_soal;
            $this->tanggal_mulai = $data->tanggal_mulai
                ? Carbon::parse($data->tanggal_mulai)->format('Y-m-d\TH:i')
                : '';

            $this->deadline = $data->deadline
                ? Carbon::parse($data->deadline)->format('Y-m-d\TH:i')
                : '';

            $this->nilai_maksimal = $data->nilai_maksimal;
            $this->boleh_terlambat = $data->boleh_terlambat;
        }
    }

    public function simpan()
    {
        $guru = Auth::user()->guru;

        $validated = $this->validate([
            'pengajaran_id' => 'required|exists:pengajarans,id',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'file_soal' => 'nullable|file|max:10240',
            'tanggal_mulai' => 'required|date',
            'deadline' => 'required|date|after:tanggal_mulai',
            'nilai_maksimal' => 'required|numeric|min:1',
            'boleh_terlambat' => 'boolean',
        ]);

        // Pastikan pengajaran milik guru yang login
        $pengajaran = Pengajaran::where('guru_id', $guru->id)
            ->where('id', $this->pengajaran_id)
            ->firstOrFail();

        $data = [
            'pengajaran_id' => $pengajaran->id,
            'guru_id' => $guru->id,
            'judul' => $this->judul,
            'deskripsi' => $this->deskripsi,
            'tanggal_mulai' => $this->tanggal_mulai,
            'deadline' => $this->deadline,
            'nilai_maksimal' => $this->nilai_maksimal,
            'boleh_terlambat' => $this->boleh_terlambat,
        ];

        if ($this->file_soal) {
            $data['file_soal'] = $this->file_soal
                ->store('tugas/soal', 'public');
        }

        if ($this->tugasId) {
            $tugas = Tugas::where('guru_id', $guru->id)
                ->findOrFail($this->tugasId);

            $fileLama = $tugas->file_soal;

            $tugas->update($data);

            if ($this->file_soal && $fileLama) {
                Storage::disk('public')->delete($fileLama);
            }

            session()->flash('success', 'Tugas berhasil diperbarui.');
        } else {
            Tugas::create($data);

            session()->flash('success', 'Tugas berhasil dibuat.');
        }

        return redirect()->route('guru.tugas-ujian');
    }

    public function render()
    {
        $guru = Auth::user()->guru;

        $pengajaran = Pengajaran::with(['kelas', 'mataPelajaran'])
            ->where('guru_id', $guru->id)
            ->where('is_active', true)
            ->get();

        return view('livewire.guru.tugas-ujian.tugas-form', [
            'pengajaran' => $pengajaran,
        ])->layout('guru.layouts.app');
    }
}
