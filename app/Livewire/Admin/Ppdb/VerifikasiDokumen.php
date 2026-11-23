<?php

namespace App\Livewire\Admin\Ppdb;

use App\Models\PpdbPendaftar;
use App\Models\PpdbDokumen;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class VerifikasiDokumen extends Component
{
    use WithPagination;

    public $search = '';
    public $filterStatus = '';

    public $pendaftarId = null;
    public $dokumenId = null;
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

    protected function cariPendaftar($id)
    {
        return PpdbPendaftar::whereHas('periode', function ($query) {
            $query->where('sekolah_id', $this->sekolahId());
        })
            ->with(['periode', 'dokumens'])
            ->findOrFail($id);
    }

    protected function cariDokumen($id)
    {
        return PpdbDokumen::whereHas('pendaftar.periode', function ($query) {
            $query->where('sekolah_id', $this->sekolahId());
        })->findOrFail($id);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterStatus()
    {
        $this->resetPage();
    }

    // Membuka seluruh dokumen milik satu pendaftar
    public function lihatDetail($id)
    {
        $this->cariPendaftar($id);

        $this->pendaftarId = $id;
        $this->dokumenId = null;
        $this->keterangan = '';
    }

    // Menampilkan preview satu dokumen
    public function lihatFile($id)
    {
        $dokumen = $this->cariDokumen($id);

        abort_unless(
            (int) $dokumen->pendaftar_id === (int) $this->pendaftarId,
            403
        );

        $this->dokumenId = $dokumen->id;
        $this->keterangan = $dokumen->keterangan ?? '';

        $this->resetValidation();
    }

    public function tutupPreview()
    {
        $this->reset(['dokumenId', 'keterangan']);
        $this->resetValidation();
    }

    public function tutupDetail()
    {
        $this->reset([
            'pendaftarId',
            'dokumenId',
            'keterangan',
        ]);

        $this->resetValidation();
    }

    // Verifikasi dokumen satu per satu
    public function verifikasi($status)
    {
        abort_unless(in_array($status, ['diterima', 'ditolak']), 422);

        $this->validate([
            'keterangan' => $status === 'ditolak'
                ? 'required|string|max:1000'
                : 'nullable|string|max:1000',
        ], [
            'keterangan.required' => 'Keterangan penolakan wajib diisi.',
        ]);

        $dokumen = $this->cariDokumen($this->dokumenId);

        abort_unless(
            (int) $dokumen->pendaftar_id === (int) $this->pendaftarId,
            403
        );

        $dokumen->update([
            'status_verifikasi' => $status,
            'keterangan' => $this->keterangan ?: null,
        ]);

        $this->tutupPreview();

        session()->flash(
            'success',
            'Dokumen berhasil ' .
                ($status === 'diterima' ? 'diterima.' : 'ditolak.')
        );
    }

    public function render()
    {
        $sekolahId = $this->sekolahId();

        $pendaftars = PpdbPendaftar::with(['periode', 'dokumens'])
            ->whereHas('periode', function ($query) use ($sekolahId) {
                $query->where('sekolah_id', $sekolahId);
            })
            ->whereHas('dokumens')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nama_lengkap', 'like', '%' . $this->search . '%')
                        ->orWhere('nomor_pendaftaran', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterStatus, function ($query) {
                $query->whereHas('dokumens', function ($q) {
                    $q->where('status_verifikasi', $this->filterStatus);
                });
            })
            ->latest()
            ->paginate(10);

        $pendaftarDetail = $this->pendaftarId
            ? $this->cariPendaftar($this->pendaftarId)
            : null;

        $dokumenPreview = $this->dokumenId
            ? $this->cariDokumen($this->dokumenId)
            : null;

        return view('livewire.admin.ppdb.verifikasi-dokumen', compact(
            'pendaftars',
            'pendaftarDetail',
            'dokumenPreview'
        ))->layout('admin.layouts.app');
    }
}
