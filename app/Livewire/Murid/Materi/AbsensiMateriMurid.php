<?php

namespace App\Livewire\Murid\Materi;

use App\Models\Materi;
use App\Models\Absensi;
use App\Models\AbsensiSiswa;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

#[\Livewire\Attributes\Layout('murid.layouts.app')]
class AbsensiMateriMurid extends Component
{
    public $materi;
    public $absensi;
    public $statusAbsensi;

    public function mount(int $materi)
    {
        $siswa = Auth::user()->siswa;

        abort_unless($siswa, 404);

        // Pastikan materi memang milik kelas siswa
        $kelasIds = $siswa->kelas()
            ->where('kelas.is_active', true)
            ->pluck('kelas.id');

        $this->materi = Materi::whereHas('pengajaran', function ($query) use ($kelasIds) {
            $query->whereIn('kelas_id', $kelasIds)
                ->where('is_active', true);
        })
            ->where('is_active', true)
            ->findOrFail($materi);

        $this->loadAbsensi();
    }

    public function loadAbsensi()
    {
        $this->absensi = Absensi::where('materi_id', $this->materi->id)
            ->where('status', 'dibuka')
            ->latest()
            ->first();

        $this->statusAbsensi = null;

        if ($this->absensi) {
            $this->statusAbsensi = AbsensiSiswa::where(
                'absensi_id',
                $this->absensi->id
            )
                ->where('siswa_id', Auth::user()->siswa->id)
                ->value('status');
        }
    }

    public function absenHadir()
    {
        $this->loadAbsensi();

        if (!$this->absensi) {
            $this->addError('absensi', 'Belum ada sesi absensi yang dibuka.');
            return;
        }

        if ($this->statusAbsensi) {
            $this->addError('absensi', 'Kehadiran kamu sudah tercatat.');
            return;
        }

        AbsensiSiswa::create([
            'absensi_id' => $this->absensi->id,
            'siswa_id' => Auth::user()->siswa->id,
            'status' => 'hadir',
            'waktu_absen' => now(),
        ]);

        $this->loadAbsensi();

        session()->flash('success', 'Kehadiran berhasil dicatat.');
    }

    public function render()
    {
        return view(
            'livewire.murid.materi.absensi-materi-murid'
        );
    }
}
