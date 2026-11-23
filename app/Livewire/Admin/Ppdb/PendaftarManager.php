<?php

namespace App\Livewire\Admin\Ppdb;

use App\Models\PpdbPeriods;
use App\Models\PpdbPendaftar;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class PendaftarManager extends Component
{
    use WithPagination;

    public $search = '';
    public $filterPeriode = '';
    public $filterStatus = '';

    public $pendaftarDetail = null;

    protected $paginationTheme = 'tailwind';

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

    public function updatedFilterPeriode()
    {
        $this->resetPage();
    }

    public function updatedFilterStatus()
    {
        $this->resetPage();
    }

    public function lihatDetail($id)
    {
        $this->pendaftarDetail = PpdbPendaftar::whereHas('periode', function ($query) {
            $query->where('sekolah_id', $this->sekolahId());
        })
            ->with(['periode', 'dokumens', 'pembayarans'])
            ->findOrFail($id);
    }

    public function tutupDetail()
    {
        $this->pendaftarDetail = null;
    }

    public function ubahStatus($id, $status)
    {
        abort_unless(
            in_array($status, [
                'terdaftar',
                'diverifikasi',
                'diterima',
                'ditolak',
                'daftar_ulang',
            ]),
            422
        );

        $pendaftar = PpdbPendaftar::whereHas('periode', function ($query) {
            $query->where('sekolah_id', $this->sekolahId());
        })->findOrFail($id);

        $pendaftar->update(['status' => $status]);

        if ($this->pendaftarDetail?->id === $pendaftar->id) {
            $this->pendaftarDetail->refresh();
        }

        session()->flash('success', 'Status pendaftar berhasil diperbarui.');
    }

    public function render()
    {
        $periodes = PpdbPeriods::where('sekolah_id', $this->sekolahId())
            ->latest()
            ->get();

        $pendaftars = PpdbPendaftar::with('periode')
            ->whereHas('periode', function ($query) {
                $query->where('sekolah_id', $this->sekolahId());
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nama_lengkap', 'like', '%' . $this->search . '%')
                        ->orWhere('nomor_pendaftaran', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterPeriode, function ($query) {
                $query->where('ppdb_period_id', $this->filterPeriode);
            })
            ->when($this->filterStatus, function ($query) {
                $query->where('status', $this->filterStatus);
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.ppdb.pendaftar-manager', compact(
            'periodes',
            'pendaftars'
        ))->layout('admin.layouts.app');
    }
}
