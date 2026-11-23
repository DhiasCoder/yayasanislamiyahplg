<?php

namespace App\Livewire\Guru\Materi;

use Livewire\Component;
use App\Models\Materi;
use App\Models\Kelas;
use App\Models\User;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

#[\Livewire\Attributes\Layout('guru.layouts.app')]
class MateriGuruManager extends Component
{
    use WithPagination, WithFileUploads;

    public $materi_id, $judul, $deskripsi, $file_path, $video_url, $guru_id, $kelas_id, $is_active = true;
    public $search = '';
    public $editMode = false;
    public $selectedMateri = [];
    public $selectAll = false;
    public $confirmingDelete = false;
    public $materiIdToDelete;
    public $confirmingBulkDelete = false;
    public $showModal = false;

    protected $rules = [
        'judul' => 'required|string|max:255',
        'deskripsi' => 'nullable|string',
        'guru_id' => 'required|exists:users,id',
        'kelas_id' => 'required|exists:kelas,id',
        'file_path' => 'nullable|file|max:10240',
        'video_url' => 'nullable|url',
        'is_active' => 'boolean',
    ];

    // 🔹 Reset form
    public function resetForm()
    {
        $this->reset(['materi_id', 'judul', 'deskripsi', 'file_path', 'video_url', 'guru_id', 'kelas_id', 'is_active', 'editMode']);
        $this->resetValidation();
        $this->showModal = true;
    }

    // 🔹 Simpan / update materi
    public function save()
    {
        $this->validate();

        $data = [
            'judul' => $this->judul,
            'deskripsi' => $this->deskripsi,
            'guru_id' => $this->guru_id,
            'kelas_id' => $this->kelas_id,
            'video_url' => $this->video_url,
            'is_active' => $this->is_active,
        ];

        if ($this->file_path) {
            $data['file_path'] = $this->file_path->store('materi', 'public');
        }

        if ($this->editMode) {
            Materi::findOrFail($this->materi_id)->update($data);
        } else {
            Materi::create($data);
        }

        $this->resetForm();
        $this->showModal = false;
        session()->flash('message', 'Materi berhasil disimpan!');
    }

    // 🔹 Edit data
    public function edit($id)
    {
        $m = Materi::findOrFail($id);
        $this->fill($m->toArray());
        $this->editMode = true;
        $this->showModal = true;
    }

    // 🔹 Toggle aktif/nonaktif
    public function toggleStatus($id)
    {
        $m = Materi::findOrFail($id);
        $m->update(['is_active' => !$m->is_active]);
    }

    // 🔹 Konfirmasi hapus satu data
    public function confirmDelete($id)
    {
        $this->confirmingDelete = true;
        $this->materiIdToDelete = $id;
    }

    // 🔹 Hapus setelah konfirmasi
    public function deleteMateriConfirmed()
    {
        Materi::findOrFail($this->materiIdToDelete)->delete();
        $this->confirmingDelete = false;
        $this->selectedMateri = array_diff($this->selectedMateri, [$this->materiIdToDelete]);
        $this->materiIdToDelete = null;
    }

    // 🔹 Hapus banyak data
    public function confirmBulkDelete()
    {
        $this->confirmingBulkDelete = true;
    }

    public function deleteSelectedConfirmed()
    {
        Materi::whereIn('id', $this->selectedMateri)->delete();
        $this->selectedMateri = [];
        $this->selectAll = false;
        $this->confirmingBulkDelete = false;
    }

    // 🔹 Select All toggle
    public function toggleSelectAll()
    {
        if ($this->selectAll) {
            $this->selectedMateri = Materi::pluck('id')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selectedMateri = [];
        }
    }

    // 🔹 Render
    public function render()
    {
        $guruId = auth()->id();
        $materiList = Materi::with(['guru', 'kelas'])
            ->where('guru_id', $guruId)
            ->where('judul', 'like', '%' . $this->search . '%')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $kelasList = Kelas::where('guru_id', $guruId)->get();
        $guruList = User::where('id', $guruId)->get();
        $this->guru_id = $guruId;

        return view('livewire.guru.materi.materi-guru-manager', compact('materiList', 'kelasList', 'guruList'))
            ->title('Manajemen Materi');
    }
}
