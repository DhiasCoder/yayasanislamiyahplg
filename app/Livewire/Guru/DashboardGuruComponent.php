<?php

namespace App\Livewire\Guru;

use Livewire\Component;

class DashboardGuruComponent extends Component
{
    public function render()
    {
        return view('livewire.guru.dashboard-guru-component', [
        ])
            ->layout('guru.layouts.app')
            ->title('Dashboard Guru');
    }
}
