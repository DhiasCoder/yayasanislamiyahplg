<?php

namespace App\Livewire\Murid;

use Livewire\Component;
use App\Models\Materi;

#[\Livewire\Attributes\Layout('murid.layouts.app')]
class MateriDetail extends Component
{
    public $materi;

    public function mount($id)
    {
        $this->materi = Materi::findOrFail($id);
    }

    public function render()
    {
        return view('livewire.murid.materi-detail')->title('Detail Materi');
    }
}
