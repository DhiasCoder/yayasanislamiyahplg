<?php

namespace App\Livewire\Murid;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Materi;
use App\Models\Ujian;

#[\Livewire\Attributes\Layout('murid.layouts.app')]
class DashboardMurid extends Component
{
    public function render()
    {
        $murid = Auth::user();

        $materiQuery = Materi::whereHas('pengajaran', function ($query) use ($murid) {
            $query->where('kelas_id', $murid->kelas_id)
                  ->where('is_active', true);
        });

        $kuisQuery = Ujian::whereHas('pengajaran', function ($query) use ($murid) {
            $query->where('kelas_id', $murid->kelas_id)
                  ->where('is_active', true);
        })->where('status', 'published');

        $totalMateri = (clone $materiQuery)->count();
        $totalKuis = (clone $kuisQuery)->count();

        $materi = (clone $materiQuery)
            ->with('pengajaran')
            ->latest()
            ->take(5)
            ->get();

        $kuis = (clone $kuisQuery)
            ->with('questions')
            ->latest()
            ->take(5)
            ->get();

        return view('livewire.siswa.dashboard-murid', [
            'materi' => $materi,
            'kuis' => $kuis,
            'totalMateri' => $totalMateri,
            'totalKuis' => $totalKuis,
        ])->title('Dashboard Murid');
    }
}
