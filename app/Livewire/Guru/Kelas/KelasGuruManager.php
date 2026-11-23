<?php

namespace App\Livewire\Guru\Kelas;

use Livewire\Component;
use App\Models\Kelas;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

#[\Livewire\Attributes\Layout('guru.layouts.app')]
class KelasGuruManager extends Component
{
    use WithPagination;

    public $kelas_id, $nama_kelas;
    public $search = '';
    public $editMode = false;
    public $selectedKelas = [];
    public $selectAll = false;
    public $confirmingDelete = false;
    public $kelasIdToDelete;
    public $confirmingBulkDelete = false;
    public $showModal = false;

    protected $rules = [
        'nama_kelas' => 'required|string|max:100',
    ];

    // 🔹 Reset form
    public function resetForm()
    {
        $this->reset(['kelas_id', 'nama_kelas', 'editMode']);
        $this->resetValidation();
        $this->showModal = true;
    }

    // 🔹 Simpan data kelas
    public function save()
    {
        $this->validate();

        $guruId = Auth::id();

        if ($this->editMode) {
            $kelas = Kelas::where('id', $this->kelas_id)
                ->where('guru_id', $guruId)
                ->firstOrFail();

            $kelas->update(['nama_kelas' => $this->nama_kelas]);
        } else {
            Kelas::create([
                'nama_kelas' => $this->nama_kelas,
                'guru_id' => $guruId
            ]);
        }

        $this->resetForm();
        $this->showModal = false;
        session()->flash('message', 'Kelas berhasil disimpan!');
    }

    // 🔹 Edit kelas milik guru
    public function edit($id)
    {
        $guruId = Auth::id();

        $kelas = Kelas::where('id', $id)
            ->where('guru_id', $guruId)
            ->firstOrFail();

        $this->kelas_id = $kelas->id;
        $this->nama_kelas = $kelas->nama_kelas;
        $this->editMode = true;
        $this->showModal = true;
    }

    // 🔹 Konfirmasi hapus kelas
    public function confirmDelete($id)
    {
        $this->confirmingDelete = true;
        $this->kelasIdToDelete = $id;
    }

    // 🔹 Hapus setelah konfirmasi
    public function deleteKelasConfirmed()
    {
        Kelas::where('id', $this->kelasIdToDelete)
            ->where('guru_id', Auth::id())
            ->delete();

        $this->confirmingDelete = false;
        $this->selectedKelas = array_diff($this->selectedKelas, [$this->kelasIdToDelete]);
        $this->kelasIdToDelete = null;
    }

    // 🔹 Hapus banyak
    public function deleteSelectedConfirmed()
    {
        Kelas::whereIn('id', $this->selectedKelas)
            ->where('guru_id', Auth::id())
            ->delete();

        $this->selectedKelas = [];
        $this->selectAll = false;
        $this->confirmingBulkDelete = false;
    }

    // 🔹 Select All
    public function toggleSelectAll()
    {
        if ($this->selectAll) {
            $this->selectedKelas = Kelas::where('guru_id', Auth::id())
                ->where('nama_kelas', 'like', '%' . $this->search . '%')
                ->pluck('id')
                ->map(fn($id) => (string)$id)
                ->toArray();
        } else {
            $this->selectedKelas = [];
        }
    }

    // 🔹 Render tampilan
    public function render()
    {
        $kelasList = Kelas::where('guru_id', Auth::id())
            ->where('nama_kelas', 'like', '%' . $this->search . '%')
            ->orderBy('nama_kelas', 'asc')
            ->paginate(10);

        return view('livewire.guru.kelas.kelas-guru-manager', compact('kelasList'))
            ->title('Kelas Saya');
    }
}
