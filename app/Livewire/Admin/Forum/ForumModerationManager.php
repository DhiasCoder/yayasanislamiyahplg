<?php

namespace App\Livewire\Admin\Forum;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Komentar_forum;
use App\Models\User;

#[\Livewire\Attributes\Layout('admin.layouts.app')]
class ForumModerationManager extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedKomentar = [];
    public $selectAll = false;
    public $confirmingDelete = false;
    public $komentarIdToDelete;
    public $showModalKomentar = false;
    public $currentKomentar;

    // 🔹 Blokir akun
    public function blockUser($userId)
    {
        $user = User::findOrFail($userId);
        $user->update(['is_blocked' => true]);
        session()->flash('message', 'Akun pengguna berhasil diblokir.');
    }

    // 🔹 Konfirmasi hapus komentar
    public function confirmDelete($id)
    {
        $this->confirmingDelete = true;
        $this->komentarIdToDelete = $id;
    }

    // 🔹 Hapus komentar
    public function deleteKomentarConfirmed()
    {
        Komentar_forum::findOrFail($this->komentarIdToDelete)->update(['status' => 'dihapus']);
        $this->confirmingDelete = false;
        session()->flash('message', 'Komentar berhasil dihapus!');
    }

    // 🔹 Lihat komentar detail
    public function showKomentar($id)
    {
        $this->currentKomentar = Komentar_forum::with(['forum', 'user'])->findOrFail($id);
        $this->showModalKomentar = true;
    }

    // 🔹 Select all
    public function toggleSelectAll()
    {
        if ($this->selectAll) {
            $this->selectedKomentar = Komentar_forum::pluck('id')->toArray();
        } else {
            $this->selectedKomentar = [];
        }
    }

    public function render()
    {
        $komentars = Komentar_forum::with(['user', 'forum'])
            ->where('isi_komentar', 'like', '%' . $this->search . '%')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.forum.forum-moderation-manager', compact('komentars'))
            ->title('Moderasi Forum');
    }
}
