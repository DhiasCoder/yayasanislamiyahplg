<?php

namespace App\Livewire\Admin\Pendaftaran;

use App\Models\PpdbPeriods;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class PeriodePendaftaranManager extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterStatus = '';

    public bool $showModal = false;
    public ?int $periodeId = null;

    public string $nama = '';
    public $tahun_ajaran_id = '';
    public string $tanggal_mulai = '';
    public string $tanggal_selesai = '';
    public string $status = 'draft';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    private function sekolahId(): int
    {
        return (int) Auth::user()
            ->userRoles()
            ->whereHas('role', fn ($q) =>
                $q->where('nama_role', 'admin')
            )
            ->value('sekolah_id');
    }

    public function resetForm(): void
    {
        $this->reset([
            'periodeId',
            'nama',
            'tahun_ajaran_id',
            'tanggal_mulai',
            'tanggal_selesai',
        ]);

        $this->status = 'draft';
        $this->resetValidation();
    }

    public function tambah(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $periode = PpdbPeriods::where('sekolah_id', $this->sekolahId())
            ->findOrFail($id);

        $this->periodeId = $periode->id;
        $this->nama = $periode->nama;
        $this->tahun_ajaran_id = (string) $periode->tahun_ajaran_id;
        $this->tanggal_mulai = $periode->tanggal_mulai->format('Y-m-d');
        $this->tanggal_selesai = $periode->tanggal_selesai->format('Y-m-d');
        $this->status = $periode->status;

        $this->resetValidation();
        $this->showModal = true;
    }

    public function simpan(): void
    {
        $sekolahId = $this->sekolahId();

        $validated = $this->validate([
            'nama' => ['required', 'string', 'max:255'],
            'tahun_ajaran_id' => [
                'required',
                'integer',
                Rule::exists('tahun_ajarans', 'id'),
            ],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => [
                'required',
                'date',
                'after_or_equal:tanggal_mulai',
            ],
            'status' => [
                'required',
                Rule::in(['draft', 'dibuka', 'ditutup']),
            ],
        ], [
            'nama.required' => 'Nama periode wajib diisi.',
            'tahun_ajaran_id.required' => 'Tahun ajaran wajib dipilih.',
            'tanggal_mulai.required' => 'Tanggal mulai wajib diisi.',
            'tanggal_selesai.required' => 'Tanggal selesai wajib diisi.',
            'tanggal_selesai.after_or_equal' =>
                'Tanggal selesai tidak boleh sebelum tanggal mulai.',
        ]);

        $validated['sekolah_id'] = $sekolahId;

        if ($this->periodeId) {
            $periode = PpdbPeriods::where('sekolah_id', $sekolahId)
                ->findOrFail($this->periodeId);

            $periode->update($validated);
        } else {
            PpdbPeriods::create($validated);
        }

        $this->showModal = false;
        $this->resetForm();

        session()->flash('success', 'Periode pendaftaran berhasil disimpan.');
    }

    public function hapus(int $id): void
    {
        PpdbPeriods::where('sekolah_id', $this->sekolahId())
            ->findOrFail($id)
            ->delete();

        session()->flash('success', 'Periode pendaftaran berhasil dihapus.');
    }

    public function render()
    {
        $sekolahId = $this->sekolahId();

        $periodes = PpdbPeriods::with('tahunAjaran')
            ->where('sekolah_id', $sekolahId)
            ->when($this->search, function ($q) {
                $search = $this->search;

                $q->where('nama', 'like', "%{$search}%");
            })
            ->when($this->filterStatus, fn ($q) =>
                $q->where('status', $this->filterStatus)
            )
            ->latest()
            ->paginate(10);

        $tahunAjarans = TahunAjaran::query()
            ->orderByDesc('id')
            ->get();

        return view(
            'livewire.admin.pendaftaran.periode-pendaftaran-manager',
            compact('periodes', 'tahunAjarans')
        )->layout('admin.layouts.app')
        ->title('Periode Pendaftaran');
    }
}
