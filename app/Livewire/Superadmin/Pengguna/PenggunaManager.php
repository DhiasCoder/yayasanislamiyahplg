<?php

namespace App\Livewire\SuperAdmin\Pengguna;

use App\Models\User;
use App\Models\Role;
use App\Models\Sekolah;
use App\Models\UserRole;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component as LivewireComponent;
use Livewire\WithPagination;

class PenggunaManager extends LivewireComponent
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $editMode = false;
    public $confirmingDelete = false;
    public $penggunaId = null;

    public $name = '';
    public $email = '';
    public $password = '';
    public $role_id = '';
    public $sekolah_id = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $penggunaList = User::with([
            'userRoles.role',
            'userRoles.sekolah',
        ])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.superadmin.pengguna.pengguna-manager', [
            'penggunaList' => $penggunaList,
            'roleList' => Role::orderBy('nama_role')->get(),
            'sekolahList' => Sekolah::orderBy('nama_sekolah')->get(),
        ])->layout('superadmin.layouts.app')
            ->title('Manajemen Pengguna');
    }

    public function resetForm(): void
    {
        $this->resetValidation();

        $this->reset([
            'penggunaId',
            'name',
            'email',
            'password',
            'role_id',
            'sekolah_id',
        ]);

        $this->editMode = false;
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $pengguna = User::with('userRoles')->findOrFail($id);
        $userRole = $pengguna->userRoles->first();

        $this->resetValidation();

        $this->penggunaId = $pengguna->id;
        $this->name = $pengguna->name;
        $this->email = $pengguna->email;
        $this->password = '';
        $this->role_id = $userRole?->role_id ?? '';
        $this->sekolah_id = $userRole?->sekolah_id ?? '';

        $this->editMode = true;
        $this->showModal = true;
    }

    public function save(): void
    {
        $role = Role::findOrFail($this->role_id);

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->penggunaId),
            ],
            'password' => [
                $this->editMode ? 'nullable' : 'required',
                'string',
                'min:8',
            ],
            'role_id' => ['required', 'exists:roles,id'],
            'sekolah_id' => [
                Rule::requiredIf($role->nama_role !== 'super_admin'),
                'nullable',
                'exists:sekolahs,id',
            ],
        ]);

        $dataUser = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if (!empty($validated['password'])) {
            $dataUser['password'] = Hash::make($validated['password']);
        }

        if ($this->editMode) {
            $pengguna = User::findOrFail($this->penggunaId);
            $pengguna->update($dataUser);
        } else {
            $pengguna = User::create($dataUser);
        }

        // Satu akun menggunakan satu role dan satu sekolah.
        UserRole::where('user_id', $pengguna->id)->delete();

        UserRole::create([
            'user_id' => $pengguna->id,
            'role_id' => $role->id,
            'sekolah_id' => $role->nama_role === 'super_admin'
                ? null
                : $validated['sekolah_id'],
        ]);

        $this->showModal = false;
        $this->resetFormState();
        $this->resetPage();

        session()->flash(
            'success',
            $this->editMode
                ? 'Pengguna berhasil diperbarui.'
                : 'Pengguna berhasil ditambahkan.'
        );
    }

    private function resetFormState(): void
    {
        $this->resetValidation();

        $this->reset([
            'penggunaId',
            'name',
            'email',
            'password',
            'role_id',
            'sekolah_id',
        ]);

        $this->editMode = false;
    }

    public function confirmDelete(int $id): void
    {
        abort_if(auth()->id() === $id, 403, 'Anda tidak dapat menghapus akun sendiri.');

        $this->penggunaId = $id;
        $this->confirmingDelete = true;
    }

    public function deleteConfirmed(): void
    {
        abort_if(auth()->id() === $this->penggunaId, 403);

        $pengguna = User::findOrFail($this->penggunaId);

        $pengguna->delete();

        $this->confirmingDelete = false;
        $this->penggunaId = null;

        session()->flash('success', 'Pengguna berhasil dihapus.');
    }
}
