<?php

namespace App\Livewire\Admin\Ppdb;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\PpdbPendaftar;

class DaftarUlangManager extends Component
{
    use WithPagination;

    public $search = '';
    public $konfirmasiId = null;

    protected function sekolahId()
    {
        $sekolahId = DB::table('user_roles')
            ->where('user_id', Auth::id())
            ->whereNotNull('sekolah_id')
            ->value('sekolah_id');

        abort_if(!$sekolahId, 403, 'Akun belum terhubung dengan sekolah.');

        return $sekolahId;
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    protected function cariPendaftar($id)
    {
        return PpdbPendaftar::whereHas('periode', function ($query) {
            $query->where('sekolah_id', $this->sekolahId());
        })->findOrFail($id);
    }

    public function bukaKonfirmasi($id)
    {
        $pendaftar = $this->cariPendaftar($id);

        if ($pendaftar->status !== 'diterima') {
            session()->flash('error', 'Pendaftar belum berstatus diterima.');
            return;
        }

        $this->konfirmasiId = $pendaftar->id;
    }

    public function tutupKonfirmasi()
    {
        $this->reset('konfirmasiId');
    }

    public function prosesKonfirmasi()
    {
        if (!$this->konfirmasiId) {
            $this->tutupKonfirmasi();
            return;
        }

        $this->konfirmasiDaftarUlang($this->konfirmasiId);
        $this->tutupKonfirmasi();
    }

    public function konfirmasiDaftarUlang($id)
    {
        $pendaftar = $this->cariPendaftar($id);

        if ($pendaftar->status !== 'diterima') {
            session()->flash('error', 'Pendaftar belum berstatus diterima.');
            return;
        }

        $pendaftar->update([
            'status' => 'daftar_ulang',
        ]);

        session()->flash('success', 'Daftar ulang berhasil dikonfirmasi.');
    }

    public function render()
    {
        $pendaftars = PpdbPendaftar::with('periode.tahunAjaran')
            ->whereHas('periode', function ($query) {
                $query->where('sekolah_id', $this->sekolahId());
            })
            ->whereIn('status', ['diterima', 'daftar_ulang'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nama_lengkap', 'like', '\%' . $this->search . '%')
                        ->orWhere('nomor_pendaftaran', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate(10);

        $pendaftarKonfirmasi = $this->konfirmasiId
            ? PpdbPendaftar::with('periode')
            ->whereHas('periode', function ($query) {
                $query->where('sekolah_id', $this->sekolahId());
            })
            ->find($this->konfirmasiId)
            : null;

        return view('livewire.admin.ppdb.daftar-ulang-manager', [
            'pendaftars' => $pendaftars,
            'pendaftarKonfirmasi' => $pendaftarKonfirmasi,
        ])->layout('admin.layouts.app');
    }
}
