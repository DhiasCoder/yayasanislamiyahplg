<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Materi;
use App\Models\Ujian;
use App\Models\Kelas;
use App\Models\Diskusi_materi;
use Illuminate\Support\Facades\Auth;

class DashboardComponent extends Component
{
    public $stats = [];

    public function render()
    {
        $user = Auth::user();

        // Ambil sekolah yang menjadi tanggung jawab Admin
        $sekolahId = null;

        if ($user && method_exists($user, 'userRoles')) {
            $sekolahId = $user->userRoles()
                ->whereHas('role', function ($query) {
                    $query->where('nama_role', 'admin');
                })
                ->value('sekolah_id');
        }

        // Fallback aman jika relasi userRoles tidak tersedia di model User
        if (empty($sekolahId) && $user && !empty($user->sekolah_id)) {
            $sekolahId = $user->sekolah_id;
        }

        // Default aman: tidak menampilkan data lintas sekolah
        $this->stats = [
            'guru' => 0,
            'siswa' => 0,
            'kelas' => 0,
            'materi' => 0,
            'ujian' => 0,
        ];

        $latestDiskusi = collect();

        if ($sekolahId) {
            $this->stats = [
                'guru' => Guru::where('sekolah_id', $sekolahId)
                    ->count(),

                'siswa' => Siswa::where('sekolah_id', $sekolahId)
                    ->count(),

                'kelas' => Kelas::where('sekolah_id', $sekolahId)
                    ->count(),

                'materi' => Materi::whereHas('pengajaran', function ($query) use ($sekolahId) {
                    $query->whereHas('kelas', function ($kelasQuery) use ($sekolahId) {
                        $kelasQuery->where('sekolah_id', $sekolahId);
                    });
                })->count(),

                'ujian' => Ujian::whereHas('pengajaran.kelas', function ($query) use ($sekolahId) {
                    $query->where('sekolah_id', $sekolahId);
                })->count(),
            ];

            $latestDiskusi = Diskusi_materi::with('user', 'materi')
                ->whereHas('materi.pengajaran.kelas', function ($query) use ($sekolahId) {
                    $query->where('sekolah_id', $sekolahId);
                })
                ->latest()
                ->take(5)
                ->get();
        }

        return view('livewire.admin.dashboard-component', [
            'latestDiskusi' => $latestDiskusi,
        ])
            ->layout('admin.layouts.app')
            ->title('Dashboard Admin');
    }
}
