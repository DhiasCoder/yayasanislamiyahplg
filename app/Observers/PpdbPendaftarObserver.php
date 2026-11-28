<?php

namespace App\Observers;

use App\Models\PpdbPendaftar;
use App\Services\PpdbToSiswaService;

class PpdbPendaftarObserver
{
    public function updated(PpdbPendaftar $pendaftar): void
    {
        if (! $pendaftar->wasChanged('status')) {
            return;
        }
        $triggerStatuses = ['daftar_ulang'];

        if (in_array($pendaftar->status, $triggerStatuses, true)) {
            app(PpdbToSiswaService::class)->convert($pendaftar);
        }
    }
}
