<?php

namespace App\Livewire\Superadmin;

use Livewire\Component;

class DashboardSuperadmin extends Component
{
    public function render()
    {
        return view('livewire.superadmin.dashboard-superadmin')
            ->layout('superadmin.layouts.app')
            ->title('Dashboard Super Admin');;
    }
}
