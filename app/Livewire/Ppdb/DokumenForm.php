<?php

namespace App\Livewire\Ppdb;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\PpdbPendaftar;
use App\Models\PpdbDokumen;
use Illuminate\Support\Facades\Storage;

class DokumenForm extends Component
{
    use WithFileUploads;

    /**
     * Semua jenis dokumen yang bisa diunggah.
     */
    public const JENIS_DOKUMEN = [
        'kartu_keluarga'         => 'Kartu Keluarga',
        'akta_kelahiran'         => 'Akta Kelahiran',
        'surat_keterangan_lulus' => 'Surat Keterangan Lulus',
        'ktp_orang_tua'          => 'KTP Orang Tua',
        'foto_3x4'               => 'Foto 3x4',
        'foto_2x3'               => 'Foto 2x3',
        'ijazah'                 => 'Ijazah',
        'dokumen_lainnya'        => 'Dokumen Lainnya',
    ];

    /**
     * Dokumen yang WAJIB ada sebelum boleh lanjut ke pembayaran.
     * Ubah daftar ini jika ada dokumen yang bersifat opsional.
     */
    public const WAJIB = [
        'kartu_keluarga',
        'akta_kelahiran',
        'surat_keterangan_lulus',
        'ktp_orang_tua',
        'foto_3x4',
        'foto_2x3',
        'ijazah',
    ];

    public $pendaftar;
    public $jenis_dokumen = '';
    public $file;
    public $nomor_urut = 1;

    public function mount()
    {
        $id = session('ppdb_pendaftar_id');

        abort_if(!$id, 403);

        $this->pendaftar = PpdbPendaftar::findOrFail($id);
    }

    public function simpan()
    {
        $this->validate([
            'jenis_dokumen' => 'required|in:' . implode(',', array_keys(self::JENIS_DOKUMEN)),
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'nomor_urut' => 'nullable|integer|min:1|max:255',
        ]);

        $path = $this->file->store(
            'ppdb/' . $this->pendaftar->id,
            'public'
        );

        $this->pendaftar->dokumens()->create([
            'jenis_dokumen' => $this->jenis_dokumen,
            'nama_file' => $this->file->getClientOriginalName(),
            'file_path' => $path,
            'nomor_urut' => $this->nomor_urut,
            'status_verifikasi' => 'menunggu',
        ]);

        $this->reset(['jenis_dokumen', 'file']);
        $this->nomor_urut = 1;

        session()->flash('success', 'Dokumen berhasil diunggah.');
    }

    public function hapus($id)
    {
        $dokumen = $this->pendaftar->dokumens()->findOrFail($id);

        if ($dokumen->status_verifikasi !== 'menunggu') {
            session()->flash('error', 'Dokumen yang sudah diverifikasi tidak dapat dihapus.');
            return;
        }

        Storage::disk('public')->delete($dokumen->file_path);
        $dokumen->delete();

        session()->flash('success', 'Dokumen berhasil dihapus.');
    }

    /**
     * Daftar jenis dokumen wajib yang belum diunggah.
     * Dokumen yang ditolak tidak dihitung, jadi pendaftar harus mengunggah ulang.
     */
    protected function dokumenWajibKurang(): array
    {
        $terunggah = $this->pendaftar->dokumens()
            ->where('status_verifikasi', '!=', 'ditolak')
            ->pluck('jenis_dokumen')
            ->unique()
            ->all();

        return array_values(array_diff(self::WAJIB, $terunggah));
    }

    public function lanjut()
    {
        $kurang = $this->dokumenWajibKurang();

        // Pengaman di server: tombol di view hanyalah tampilan
        if (! empty($kurang)) {
            $nama = array_map(
                fn($jenis) => self::JENIS_DOKUMEN[$jenis] ?? $jenis,
                $kurang
            );

            session()->flash(
                'error',
                'Dokumen wajib belum lengkap: ' . implode(', ', $nama) . '.'
            );

            return;
        }

        return redirect()->route('ppdb.pembayaran');
    }

    public function render()
    {
        return view('livewire.ppdb.dokumen-form', [
            'dokumens'     => $this->pendaftar->dokumens()->latest()->get(),
            'jenisDokumen' => self::JENIS_DOKUMEN,
            'wajib'        => self::WAJIB,
            'kurang'       => $this->dokumenWajibKurang(),
        ])->layout('user.layouts.app');
    }
}
