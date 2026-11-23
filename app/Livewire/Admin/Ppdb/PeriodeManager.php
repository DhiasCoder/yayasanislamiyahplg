<?php

namespace App\Livewire\Admin\Ppdb;

use App\Models\PpdbPeriods;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class PeriodeManager extends Component
{
    use WithPagination;

    public $periodeId;
    public $tahun_ajaran_id;
    public $nama;
    public $tanggal_mulai;
    public $tanggal_selesai;
    public $status = 'draft';

    public $search = '';
    public $showForm = false;

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

    protected function rules()
    {
        return [
            'tahun_ajaran_id' => ['required', 'exists:tahun_ajarans,id'],
            'nama' => ['required', 'string', 'max:255'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'status' => ['required', 'in:draft,dibuka,ditutup'],
        ];
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function resetForm()
    {
        $this->reset([
            'periodeId',
            'tahun_ajaran_id',
            'nama',
            'tanggal_mulai',
            'tanggal_selesai',
        ]);

        $this->status = 'draft';
        $this->resetValidation();
    }

    public function tambah()
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit($id)
    {
        $periode = PpdbPeriods::where('sekolah_id', $this->sekolahId())
            ->findOrFail($id);

        $this->periodeId = $periode->id;
        $this->tahun_ajaran_id = $periode->tahun_ajaran_id;
        $this->nama = $periode->nama;
        $this->tanggal_mulai = $periode->tanggal_mulai->format('Y-m-d');
        $this->tanggal_selesai = $periode->tanggal_selesai->format('Y-m-d');
        $this->status = $periode->status;

        $this->showForm = true;
    }

    public function simpan()
    {
        $this->validate();

        $data = [
            'sekolah_id' => $this->sekolahId(),
            'tahun_ajaran_id' => $this->tahun_ajaran_id,
            'nama' => $this->nama,
            'tanggal_mulai' => $this->tanggal_mulai,
            'tanggal_selesai' => $this->tanggal_selesai,
            'status' => $this->status,
        ];

        if ($this->periodeId) {
            $periode = PpdbPeriods::where('sekolah_id', $this->sekolahId())
                ->findOrFail($this->periodeId);

            $periode->update($data);
        } else {
            PpdbPeriods::create($data);
        }

        $this->showForm = false;
        $this->resetForm();

        session()->flash('success', 'Periode PPDB berhasil disimpan.');
    }

    public function ubahStatus($id, $status)
    {
        abort_unless(in_array($status, ['draft', 'dibuka', 'ditutup']), 422);

        $periode = PpdbPeriods::where('sekolah_id', $this->sekolahId())
            ->findOrFail($id);

        $periode->update(['status' => $status]);

        session()->flash('success', 'Status periode berhasil diperbarui.');
    }

    public function hapus($id)
    {
        $periode = PpdbPeriods::where('sekolah_id', $this->sekolahId())
            ->findOrFail($id);

        if ($periode->pendaftars()->exists()) {
            session()->flash('error', 'Periode tidak dapat dihapus karena sudah memiliki pendaftar.');
            return;
        }

        $periode->delete();

        session()->flash('success', 'Periode PPDB berhasil dihapus.');
    }

    public function render()
    {
        return view('livewire.admin.ppdb.periode-manager', [
            'periodes' => PpdbPeriods::with('tahunAjaran')
                ->where('sekolah_id', $this->sekolahId())
                ->when($this->search, function ($query) {
                    $query->where('nama', 'like', '%' . $this->search . '%');
                })
                ->latest()
                ->paginate(10),

            'tahunAjarans' => TahunAjaran::all(),
        ])->layout('admin.layouts.app');
    }
}
