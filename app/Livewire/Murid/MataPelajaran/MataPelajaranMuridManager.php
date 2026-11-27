<?php

namespace App\Livewire\Murid\MataPelajaran;

use App\Models\Jadwal;
use App\Models\Pengajaran;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MataPelajaranMuridManager extends Component
{
    public function render()
    {
        $user = Auth::user();
        $siswa = $user?->siswa;

        $daftarHari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $jadwalPerHari = collect($daftarHari)->mapWithKeys(fn($hari) => [$hari => collect()]);
        $unscheduledPengajarans = collect();
        $totalMapel = 0;
        $totalMateri = 0;
        $totalSesi = 0;

        if ($siswa) {
            $kelasIds = $siswa->kelas()
                ->where('kelas.is_active', true)
                ->pluck('kelas.id');

            $pengajarans = Pengajaran::query()
                ->with(['mataPelajaran', 'guru', 'kelas'])
                ->withCount(['materi' => fn($q) => $q->where('is_active', true)])
                ->whereIn('kelas_id', $kelasIds)
                ->where('is_active', true)
                ->whereHas('mataPelajaran')
                ->get()
                ->keyBy('id');

            $totalMapel = $pengajarans->count();
            $totalMateri = $pengajarans->sum('materi_count');

            if ($pengajarans->isNotEmpty()) {
                $jadwals = Jadwal::query()
                    ->whereIn('pengajaran_id', $pengajarans->keys())
                    ->where('is_active', true)
                    ->orderBy('jam_mulai')
                    ->get()
                    ->each(function (Jadwal $jadwal) use ($pengajarans) {
                        $jadwal->setRelation('pengajaran', $pengajarans->get($jadwal->pengajaran_id));
                    })
                    ->filter(fn(Jadwal $jadwal) => $jadwal->pengajaran !== null);

                $totalSesi = $jadwals->count();

                $groupedByHari = $jadwals->groupBy('hari');

                foreach ($daftarHari as $hari) {
                    $jadwalPerHari[$hari] = ($groupedByHari->get($hari) ?? collect())->values();
                }

                // Jika ada jadwal di hari Minggu, ikut ditampilkan
                if ($groupedByHari->has('Minggu') && $groupedByHari->get('Minggu')->isNotEmpty()) {
                    $jadwalPerHari['Minggu'] = $groupedByHari->get('Minggu')->values();
                }

                // Mata pelajaran aktif yang belum memiliki jadwal
                $scheduledPengajaranIds = $jadwals->pluck('pengajaran_id')->unique();
                $unscheduledPengajarans = $pengajarans
                    ->whereNotIn('id', $scheduledPengajaranIds)
                    ->values();
            }
        }

        $mapHariIso = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu',
        ];

        return view('livewire.murid.mata-pelajaran.mata-pelajaran-murid-manager', [
            'jadwalPerHari' => $jadwalPerHari,
            'unscheduledPengajarans' => $unscheduledPengajarans,
            'totalMapel' => $totalMapel,
            'totalMateri' => $totalMateri,
            'totalSesi' => $totalSesi,
            'hariIni' => $mapHariIso[now()->dayOfWeekIso] ?? '',
        ])->layout('murid.layouts.app');
    }
}
