<?php

namespace App\Livewire\Admin\Akademik;

use App\Models\Siswa;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class RekapAkademikManager extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    private function sekolahId()
    {
        return Auth::user()
            ->userRoles()
            ->whereHas('role', fn ($q) =>
                $q->where('nama_role', 'admin')
            )
            ->value('sekolah_id');
    }

    public function render()
    {
        $siswas = Siswa::query()
            ->where('sekolah_id', $this->sekolahId())
            ->when($this->search, fn ($q) =>
                $q->where('nama_lengkap', 'like', '%' . $this->search . '%')
            )
            ->withAvg('nilais', 'nilai')
            ->withCount([
                'absensiSiswas as jumlah_hadir' => fn ($q) =>
                    $q->where('status', 'hadir'),
                'absensiSiswas as jumlah_izin' => fn ($q) =>
                    $q->where('status', 'izin'),
                'absensiSiswas as jumlah_sakit' => fn ($q) =>
                    $q->where('status', 'sakit'),
                'absensiSiswas as jumlah_alpa' => fn ($q) =>
                    $q->where('status', 'alpa'),
            ])
            ->paginate(10);

        return view('livewire.admin.akademik.rekap-akademik-manager', compact('siswas'));
    }
}
