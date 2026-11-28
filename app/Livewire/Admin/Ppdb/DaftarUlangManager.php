<?php

namespace App\Livewire\Admin\Ppdb;

use App\Models\PpdbPendaftar;
use App\Services\PpdbToSiswaService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Throwable;

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
        $sekolahId = $this->sekolahId();
        $pendaftar = $this->cariPendaftar($id);

        if ($pendaftar->status !== 'diterima') {
            session()->flash('error', 'Pendaftar belum berstatus diterima.');
            return;
        }

        try {
            $hasil = DB::transaction(function () use ($pendaftar, $sekolahId) {
                $pendaftar->update([
                    'status' => 'daftar_ulang',
                ]);

                return app(PpdbToSiswaService::class)->convert($pendaftar, $sekolahId);
            });

            $infoAkun = $hasil['is_new'] && $hasil['default_password']
                ? " Akun siswa dibuat — Email: {$hasil['generated_email']} | Password: {$hasil['default_password']}"
                : " Terhubung ke akun siswa: {$hasil['generated_email']}";

            session()->flash('success', 'Daftar ulang berhasil dikonfirmasi.' . $infoAkun);
        } catch (Throwable $e) {
            report($e);
            session()->flash('error', 'Gagal mengonfirmasi daftar ulang: ' . $e->getMessage());
        }
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
                    $q->where('nama_lengkap', 'like', '%' . $this->search . '%')
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
