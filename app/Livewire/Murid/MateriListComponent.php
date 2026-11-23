<?php

namespace App\Livewire\Murid;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Materi;

#[\Livewire\Attributes\Layout('murid.layouts.app')]
class MateriListComponent extends Component
{
    public $search = '';

    public function render()
    {
        $murid = Auth::user();

        $materi = Materi::where('kelas_id', $murid->kelas_id)
            ->where('judul', 'like', '%' . $this->search . '%')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.murid.materi-list-component', [
            'materi' => $materi,
        ])->title('Daftar Materi');
    }
}
