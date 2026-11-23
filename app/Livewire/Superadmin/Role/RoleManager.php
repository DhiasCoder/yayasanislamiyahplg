<?php

namespace App\Livewire\SuperAdmin\Role;

use App\Models\Role;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;

class RoleManager extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $editMode = false;
    public $confirmingDelete = false;
    public $confirmingBulkDelete = false;
    public $selectAll = false;

    public $roleId = null;
    public $nama_role = '';
    public $selectedRoles = [];

    public function updatedSearch(): void
    {
        $this->resetPage();
        $this->selectedRoles = [];
        $this->selectAll = false;
    }

    public function render()
    {
        $roleList = Role::withCount('users')
            ->when($this->search, function ($query) {
                $query->where(
                    'nama_role',
                    'like',
                    '%' . $this->search . '%'
                );
            })
            ->latest()
            ->paginate(10);

        return view('livewire.superadmin.role.role-manager', [
            'roleList' => $roleList,
        ])->layout('superadmin.layouts.app')
            ->title('Manajemen Role');
    }

    public function resetForm(): void
    {
        $this->resetValidation();
        $this->reset(['roleId', 'nama_role']);

        $this->editMode = false;
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $role = Role::findOrFail($id);

        $this->resetValidation();
        $this->roleId = $role->id;
        $this->nama_role = $role->nama_role;

        $this->editMode = true;
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'nama_role' => [
                'required',
                'string',
                'max:100',
                Rule::unique('roles', 'nama_role')->ignore($this->roleId),
            ],
        ]);

        if ($this->editMode) {
            Role::findOrFail($this->roleId)->update($validated);
            $message = 'Role berhasil diperbarui.';
        } else {
            Role::create($validated);
            $message = 'Role berhasil ditambahkan.';
        }

        $this->showModal = false;
        $this->resetFormState();
        $this->resetPage();

        session()->flash('success', $message);
    }

    private function resetFormState(): void
    {
        $this->resetValidation();
        $this->reset(['roleId', 'nama_role']);
        $this->editMode = false;
    }

    public function toggleSelectAll(): void
    {
        $this->selectedRoles = $this->selectAll
            ? $this->filteredRoleIds()
            : [];
    }

    private function filteredRoleIds(): array
    {
        return Role::query()
            ->when($this->search, function ($query) {
                $query->where(
                    'nama_role',
                    'like',
                    '%' . $this->search . '%'
                );
            })
            ->latest()
            ->limit(10)
            ->pluck('id')
            ->map(fn($id) => (string) $id)
            ->toArray();
    }

    public function confirmDelete(int $id): void
    {
        $this->roleId = $id;
        $this->confirmingDelete = true;
    }

    public function deleteConfirmed(): void
    {
        $role = Role::findOrFail($this->roleId);

        if ($role->userRoles()->exists()) {
            $this->confirmingDelete = false;
            session()->flash(
                'error',
                'Role tidak dapat dihapus karena masih digunakan pengguna.'
            );
            return;
        }

        $role->delete();

        $this->confirmingDelete = false;
        $this->roleId = null;

        session()->flash('success', 'Role berhasil dihapus.');
    }

    public function confirmBulkDelete(): void
    {
        if (count($this->selectedRoles) > 0) {
            $this->confirmingBulkDelete = true;
        }
    }

    public function deleteSelectedConfirmed(): void
    {
        $deleted = 0;
        $skipped = 0;

        foreach ($this->selectedRoles as $id) {
            $role = Role::find($id);

            if (!$role) {
                continue;
            }

            if ($role->userRoles()->exists()) {
                $skipped++;
                continue;
            }

            $role->delete();
            $deleted++;
        }

        $this->confirmingBulkDelete = false;
        $this->selectedRoles = [];
        $this->selectAll = false;
        $this->resetPage();

        session()->flash(
            $skipped > 0 ? 'error' : 'success',
            "Berhasil menghapus {$deleted} role. {$skipped} role dilewati karena masih digunakan."
        );
    }
}
