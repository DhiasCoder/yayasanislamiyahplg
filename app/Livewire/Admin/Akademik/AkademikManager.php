<?php

namespace App\Livewire\Admin\Akademik;

use Livewire\Component;

class AkademikManager extends Component
{
    public string $tab = 'nilai';

    public function pilihTab(string $tab): void
    {
        if (in_array($tab, ['nilai', 'rapor', 'rekap'])) {
            $this->tab = $tab;
        }
    }

    public function render()
    {
        return view('livewire.admin.akademik.akademik-manager')
            ->layout('admin.layouts.app');
    }
}
