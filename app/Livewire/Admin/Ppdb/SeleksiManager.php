<?php

namespace App\Livewire\Admin\Ppdb;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\PpdbPendaftar;

class SeleksiManager extends Component
{
    use WithPagination;

    public $search = '';
    public $filterStatus = 'diverifikasi';

    public $konfirmasiId = null;
    public $konfirmasiAksi = null; // 'terima' | 'tolak'

    protected $queryString = [
        'search' => ['except' => ''],
    ];

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

    public function bukaKonfirmasi($id, $aksi)
    {
        abort_unless(in_array($aksi, ['terima', 'tolak']), 422);

        $pendaftar = $this->cariPendaftar($id);

        if ($pendaftar->status !== 'diverifikasi') {
            session()->flash('error', 'Pendaftar belum dapat diseleksi.');
            $this->dispatch('notify', message: 'Pendaftar belum dapat diseleksi.');
            return;
        }

        $this->konfirmasiId = $pendaftar->id;
        $this->konfirmasiAksi = $aksi;
    }

    public function tutupKonfirmasi()
    {
        $this->reset(['konfirmasiId', 'konfirmasiAksi']);
    }

    public function prosesKonfirmasi()
    {
        if (!$this->konfirmasiId || !in_array($this->konfirmasiAksi, ['terima', 'tolak'])) {
            $this->tutupKonfirmasi();
            return;
        }

        if ($this->konfirmasiAksi === 'terima') {
            $this->terima($this->konfirmasiId);
        } else {
            $this->tolak($this->konfirmasiId);
        }

        $this->tutupKonfirmasi();
    }

    public function terima($id)
    {
        $pendaftar = $this->cariPendaftar($id);

        if ($pendaftar->status !== 'diverifikasi') {
            session()->flash('error', 'Pendaftar belum dapat diseleksi.');
            $this->dispatch('notify', message: 'Pendaftar belum dapat diseleksi.');
            return;
        }

        $pendaftar->update([
            'status' => 'diterima',
        ]);

        session()->flash('success', 'Pendaftar berhasil dinyatakan diterima.');
    }

    public function tolak($id)
    {
        $pendaftar = $this->cariPendaftar($id);

        if ($pendaftar->status !== 'diverifikasi') {
            session()->flash('error', 'Pendaftar belum dapat diseleksi.');
            $this->dispatch('notify', message: 'Pendaftar belum dapat diseleksi.');
            return;
        }

        $pendaftar->update([
            'status' => 'ditolak',
        ]);

        session()->flash('success', 'Pendaftar berhasil dinyatakan ditolak.');
    }

    protected function cariPendaftar($id)
    {
        return PpdbPendaftar::whereHas('periode', function ($query) {
            $query->where('sekolah_id', $this->sekolahId());
        })->findOrFail($id);
    }

    public function render()
    {
        $pendaftars = PpdbPendaftar::with('periode.tahunAjaran')
            ->whereHas('periode', function ($query) {
                $query->where('sekolah_id', $this->sekolahId());
            })
            ->where('status', $this->filterStatus)
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

        return view('livewire.admin.ppdb.seleksi-manager', [
            'pendaftars' => $pendaftars,
            'pendaftarKonfirmasi' => $pendaftarKonfirmasi,
        ])->layout('admin.layouts.app');
    }
}
