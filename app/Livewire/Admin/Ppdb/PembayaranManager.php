<?php

namespace App\Livewire\Admin\Ppdb;

use App\Models\PpdbPembayaran;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class PembayaranManager extends Component
{
    use WithPagination;

    public $search = '';
    public $filterStatus = '';
    public $pembayaranId;
    public $keterangan = '';

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

    public function updatedFilterStatus()
    {
        $this->resetPage();
    }

    public function pilihPembayaran($id)
    {
        $pembayaran = PpdbPembayaran::whereHas('pendaftar.periode', function ($query) {
            $query->where('sekolah_id', $this->sekolahId());
        })->findOrFail($id);

        $this->pembayaranId = $pembayaran->id;
        $this->keterangan = $pembayaran->keterangan ?? '';
    }

    public function verifikasi($status)
    {
        abort_unless(in_array($status, ['diverifikasi', 'ditolak']), 422);

        $pembayaran = PpdbPembayaran::whereHas('pendaftar.periode', function ($query) {
            $query->where('sekolah_id', $this->sekolahId());
        })->findOrFail($this->pembayaranId);

        if ($status === 'ditolak' && trim($this->keterangan) === '') {
            $this->addError('keterangan', 'Keterangan penolakan wajib diisi.');
            return;
        }

        $pembayaran->update([
            'status' => $status,
            'keterangan' => $this->keterangan ?: null,
            'dicatat_oleh' => Auth::id(),
        ]);

        $this->reset(['pembayaranId', 'keterangan']);

        session()->flash('success', 'Status pembayaran berhasil diperbarui.');
    }

    public function render()
    {
        $pembayarans = PpdbPembayaran::with('pendaftar.periode')
            ->whereHas('pendaftar.periode', function ($query) {
                $query->where('sekolah_id', $this->sekolahId());
            })
            ->when($this->search, function ($query) {
                $query->whereHas('pendaftar', function ($q) {
                    $q->where('nama_lengkap', 'like', '%' . $this->search . '%')
                        ->orWhere('nomor_pendaftaran', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterStatus, function ($query) {
                $query->where('status', $this->filterStatus);
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.ppdb.pembayaran-manager', compact('pembayarans'))
            ->layout('admin.layouts.app');
    }
}
