<?php

namespace App\Livewire\Admin\Materi;

use App\Models\Materi;
use App\Models\Pengajaran;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class MateriManager extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $showForm = false;
    public $materiId = null;

    public $pengajaran_id = '';
    public $judul = '';
    public $deskripsi = '';
    public $file;
    public $video_url = '';
    public $is_active = true;

    public function updatedSearch()
    {
        $this->resetPage();
    }

    private function sekolahId()
    {
        return Auth::user()
            ->userRoles()
            ->whereHas('role', fn ($q) => $q->where('nama_role', 'admin'))
            ->value('sekolah_id');
    }

    private function queryMateri()
    {
        return Materi::whereHas('pengajaran.kelas', function ($q) {
            $q->where('sekolah_id', $this->sekolahId());
        });
    }

    private function queryPengajaran()
    {
        return Pengajaran::with([
                'guru',
                'mataPelajaran',
                'kelas',
                'tahunAjaran',
            ])
            ->where('is_active', true)
            ->whereHas('kelas', function ($q) {
                $q->where('sekolah_id', $this->sekolahId());
            })
            ->whereHas('guru', function ($q) {
                $q->where('status', 'aktif');
            })
            ->orderBy('kelas_id');
    }

    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit($id)
    {
        $materi = $this->queryMateri()->findOrFail($id);

        $this->materiId = $materi->id;
        $this->pengajaran_id = $materi->pengajaran_id;
        $this->judul = $materi->judul;
        $this->deskripsi = $materi->deskripsi;
        $this->video_url = $materi->video_url;
        $this->is_active = $materi->is_active;
        $this->file = null;

        $this->resetValidation();
        $this->showForm = true;
    }

    public function resetForm()
    {
        $this->reset([
            'materiId',
            'pengajaran_id',
            'judul',
            'deskripsi',
            'file',
            'video_url',
        ]);

        $this->is_active = true;
        $this->resetValidation();
    }

    public function save()
    {
        $materiLama = $this->materiId
            ? $this->queryMateri()->findOrFail($this->materiId)
            : null;

        $validated = $this->validate([
            'pengajaran_id' => [
                'required',
                Rule::exists('pengajarans', 'id')->where(function ($query) {
                    $query->whereIn('kelas_id', function ($subQuery) {
                        $subQuery->select('id')
                            ->from('kelas')
                            ->where('sekolah_id', $this->sekolahId());
                    })->where('is_active', true);
                }),
            ],
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,jpg,jpeg,png|max:10240',
            'video_url' => 'nullable|url|max:2048',
            'is_active' => 'boolean',
        ]);

        $data = [
            'pengajaran_id' => $validated['pengajaran_id'],
            'judul' => $validated['judul'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'video_url' => $validated['video_url'] ?? null,
            'is_active' => $validated['is_active'],
        ];

        if ($this->file) {
            $data['file_path'] = $this->file->store('materi', 'public');
        }

        if ($materiLama) {
            if ($this->file && $materiLama->file_path) {
                Storage::disk('public')->delete($materiLama->file_path);
            }

            $materiLama->update($data);
            session()->flash('success', 'Materi berhasil diperbarui.');
        } else {
            Materi::create($data);
            session()->flash('success', 'Materi berhasil ditambahkan.');
        }

        $this->resetForm();
        $this->showForm = false;
    }

    public function toggleStatus($id)
    {
        $materi = $this->queryMateri()->findOrFail($id);

        $materi->update([
            'is_active' => !$materi->is_active,
        ]);

        session()->flash('success', 'Status materi berhasil diperbarui.');
    }

    public function delete($id)
    {
        $materi = $this->queryMateri()->findOrFail($id);

        if ($materi->diskusi()->exists()) {
            session()->flash(
                'error',
                'Materi tidak dapat dihapus karena sudah memiliki diskusi.'
            );

            return;
        }

        if ($materi->file_path) {
            Storage::disk('public')->delete($materi->file_path);
        }

        $materi->delete();

        session()->flash('success', 'Materi berhasil dihapus.');
    }

    public function render()
    {
        $materis = $this->queryMateri()
            ->with([
                'pengajaran.guru',
                'pengajaran.mataPelajaran',
                'pengajaran.kelas',
                'pengajaran.tahunAjaran',
            ])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('judul', 'like', '%' . $this->search . '%')
                        ->orWhereHas('pengajaran.mataPelajaran', function ($sub) {
                            $sub->where('nama', 'like', '%' . $this->search . '%');
                        })
                        ->orWhereHas('pengajaran.guru', function ($sub) {
                            $sub->where('nama_lengkap', 'like', '%' . $this->search . '%');
                        })
                        ->orWhereHas('pengajaran.kelas', function ($sub) {
                            $sub->where('nama_kelas', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.materi.materi-manager', [
            'materis' => $materis,
            'pengajarans' => $this->queryPengajaran()->get(),
            'totalMateri' => $this->queryMateri()->count(),
            'materiAktif' => $this->queryMateri()->where('is_active', true)->count(),
        ])->layout('admin.layouts.app');
    }
}
