<?php

namespace App\Livewire\Ppdb;


use Livewire\Component;
use App\Models\PpdbPendaftar;
use Illuminate\Support\Facades\Route;

class StatusPendaftaran extends Component
{
    /**
     * Nama route halaman formulir pendaftaran (menerima id periode).
     * Sesuaikan jika nama route Anda berbeda.
     */
    protected const ROUTE_FORM = 'ppdb.pendaftaran';

    public $nomor_pendaftaran = '';
    public $kode_akses = '';

    public $pendaftar = null;
    public $sudahMencari = false;

    public function cekStatus()
    {
        $this->validate([
            'nomor_pendaftaran' => 'required|string|max:255',
            'kode_akses' => 'required|string|max:64',
        ]);

        // Hapus session dan hasil pencarian sebelumnya
        session()->forget('ppdb_pendaftar_id');
        $this->pendaftar = null;

        $this->pendaftar = PpdbPendaftar::with([
            'periode.sekolah',
            'dokumens',
            'pembayarans',
        ])
            ->where('nomor_pendaftaran', $this->nomor_pendaftaran)
            ->where('kode_akses', $this->kode_akses)
            ->first();

        $this->sudahMencari = true;

        if (!$this->pendaftar) {
            $this->addError(
                'nomor_pendaftaran',
                'Nomor pendaftaran atau kode akses tidak sesuai.'
            );

            return;
        }

        // Simpan ID setelah data ditemukan.
        // Session ini juga yang membuka halaman formulir dalam mode terkunci.
        session([
            'ppdb_pendaftar_id' => $this->pendaftar->id,
        ]);
    }

    public function dokumenBelumLengkap(): array
    {
        $dokumenDiterima = $this->pendaftar->dokumens
            ->where('status_verifikasi', 'diterima')
            ->pluck('jenis_dokumen')
            ->unique()
            ->all();

        // Daftar dokumen wajib satu sumber dengan halaman upload dokumen
        return array_values(array_diff(DokumenForm::WAJIB, $dokumenDiterima));
    }

    public function jumlahDokumenWajib(): int
    {
        return count(DokumenForm::WAJIB);
    }

    public function pembayaranBelumLengkap(): bool
    {
        return !$this->pendaftar->pembayarans
            ->contains('status', 'diverifikasi');
    }

    /**
     * Tautan ke formulir pendaftaran (mode terkunci / hanya lihat).
     * Null jika pendaftar belum ada atau route belum terdaftar.
     */
    public function urlForm(): ?string
    {
        if (! $this->pendaftar || ! Route::has(self::ROUTE_FORM)) {
            return null;
        }

        return route(self::ROUTE_FORM, $this->pendaftar->ppdb_period_id);
    }

    public function render()
    {
        return view('livewire.ppdb.status-pendaftaran')
            ->layout('user.layouts.app');
    }
}
