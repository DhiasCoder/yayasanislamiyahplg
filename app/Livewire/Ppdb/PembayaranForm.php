<?php

namespace App\Livewire\Ppdb;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\PpdbPendaftar;
use Illuminate\Support\Facades\Storage;

class PembayaranForm extends Component
{
    use WithFileUploads;

    public $pendaftar;
    public $jenis_pembayaran = '';
    public $nominal;
    public $tanggal_bayar;
    public $metode_pembayaran = '';
    public $bukti_pembayaran;

    public function mount()
    {
        $id = session('ppdb_pendaftar_id');

        abort_if(!$id, 403);

        $this->pendaftar = PpdbPendaftar::findOrFail($id);
    }

    public function simpan()
    {
        $this->validate([
            'jenis_pembayaran' => 'required|string|max:255',
            'nominal' => 'required|numeric|min:1',
            'tanggal_bayar' => 'nullable|date',
            'metode_pembayaran' => 'required|in:tunai,transfer_manual',
            'bukti_pembayaran' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $path = $this->bukti_pembayaran
            ? $this->bukti_pembayaran->store(
                'ppdb/pembayaran/' . $this->pendaftar->id,
                'public'
            )
            : null;

        $this->pendaftar->pembayarans()->create([
            'jenis_pembayaran' => $this->jenis_pembayaran,
            'nominal' => $this->nominal,
            'tanggal_bayar' => $this->tanggal_bayar,
            'metode_pembayaran' => $this->metode_pembayaran,
            'bukti_pembayaran' => $path,
            'status' => 'menunggu',
        ]);

        $this->reset([
            'jenis_pembayaran',
            'nominal',
            'tanggal_bayar',
            'metode_pembayaran',
            'bukti_pembayaran',
        ]);

        session()->flash('success', 'Informasi pembayaran berhasil dikirim.');
    }

    public function render()
    {
        return view('livewire.ppdb.pembayaran-form', [
            'pembayarans' => $this->pendaftar->pembayarans()->latest()->get(),
        ])->layout('user.layouts.app');
    }
}
