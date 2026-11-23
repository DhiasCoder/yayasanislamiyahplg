<?php

namespace App\Livewire\Admin\Pengguna;

use App\Models\Guru;
use App\Models\Role;
use App\Models\Siswa;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class PenggunaManagers extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'tailwind';

    public string $search = '';

    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public bool $isEdit = false;

    public ?int $penggunaId = null;
    public ?int $deleteId = null;

    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $role_id = '';

    protected function getSekolahId(): ?int
    {
        $user = auth()->user();

        if (! $user) {
            return null;
        }

        $id = $user->userRoles()
            ->whereHas('role', fn ($query) =>
                $query->where('nama_role', 'admin')
            )
            ->value('sekolah_id');

        return $id ? (int) $id : null;
    }

    protected function penggunaQuery()
    {
        return User::query()
            ->whereHas('userRoles', function ($query) {
                $query->where('sekolah_id', $this->getSekolahId())
                    ->whereHas('role', fn ($roleQuery) =>
                        $roleQuery->whereIn('nama_role', ['guru', 'siswa'])
                    );
            });
    }

    protected function roleQuery()
    {
        // Admin sekolah hanya boleh membuat akun Guru dan Siswa.
        return Role::query()
            ->whereIn('nama_role', ['guru', 'siswa']);
    }

    protected function rules(): array
    {
        $user = $this->isEdit
            ? $this->penggunaQuery()->findOrFail($this->penggunaId)
            : null;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user?->id),
            ],
            'password' => $this->isEdit
                ? ['nullable', 'string', 'min:8']
                : ['required', 'string', 'min:8'],
            'role_id' => [
                'required',
                'integer',
                Rule::exists('roles', 'id')
                    ->whereIn('nama_role', ['guru', 'siswa']),
            ],
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'Nama pengguna wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah digunakan akun lain.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'role_id.required' => 'Role wajib dipilih.',
            'role_id.exists' => 'Role yang dipilih tidak valid.',
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        $this->resetForm();

        $this->isEdit = false;
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $user = $this->penggunaQuery()
            ->with('userRoles.role')
            ->findOrFail($id);

        $userRole = $user->userRoles
            ->first(fn ($item) =>
                (int) $item->sekolah_id === $this->getSekolahId()
            );

        if (! $userRole || ! $userRole->role) {
            session()->flash('error', 'Role akun tidak ditemukan.');
            return;
        }

        $this->penggunaId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role_id = (string) $userRole->role_id;
        $this->password = '';

        $this->isEdit = true;
        $this->showModal = true;
    }

    public function save(): void
    {
        if (! $this->getSekolahId()) {
            $this->addError('form', 'Sekolah untuk akun Admin tidak ditemukan.');
            return;
        }

        $validated = $this->validate();

        $role = $this->roleQuery()->findOrFail((int) $validated['role_id']);

        DB::transaction(function () use ($validated, $role) {
            if ($this->isEdit) {
                $user = $this->penggunaQuery()
                    ->lockForUpdate()
                    ->findOrFail($this->penggunaId);

                $currentRole = $user->userRoles()
                    ->where('sekolah_id', $this->getSekolahId())
                    ->firstOrFail();

                // Jangan sampai profil Guru/Siswa menjadi tidak sesuai role.
                if ((int) $currentRole->role_id !== (int) $role->id) {
                    $hasGuruProfile = Guru::where('user_id', $user->id)->exists();
                    $hasSiswaProfile = Siswa::where('user_id', $user->id)->exists();

                    if ($hasGuruProfile || $hasSiswaProfile) {
                        throw new \RuntimeException(
                            'Role tidak dapat diubah karena akun sudah terhubung dengan profil Guru atau Siswa.'
                        );
                    }
                }

                $user->name = trim($validated['name']);
                $user->email = trim($validated['email']);

                if (filled($validated['password'] ?? null)) {
                    $user->password = Hash::make($validated['password']);
                }

                $user->save();

                // Hanya memperbarui penetapan role milik sekolah Admin.
                $currentRole->update([
                    'role_id' => $role->id,
                ]);
            } else {
                $user = User::create([
                    'name' => trim($validated['name']),
                    'email' => trim($validated['email']),
                    'password' => Hash::make($validated['password']),
                ]);

                UserRole::create([
                    'user_id' => $user->id,
                    'role_id' => $role->id,
                    'sekolah_id' => $this->getSekolahId(),
                ]);
            }
        });

        $message = $this->isEdit
            ? 'Akun pengguna berhasil diperbarui.'
            : 'Akun pengguna berhasil ditambahkan.';

        $this->showModal = false;
        $this->resetForm();

        session()->flash('success', $message);
    }

    public function confirmDelete(int $id): void
    {
        $user = $this->penggunaQuery()->findOrFail($id);

        if ($user->id === auth()->id()) {
            session()->flash('error', 'Anda tidak dapat menghapus akun sendiri.');
            return;
        }

        $this->deleteId = $user->id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        $user = $this->penggunaQuery()->findOrFail($this->deleteId);

        if ($user->id === auth()->id()) {
            $this->showDeleteModal = false;
            session()->flash('error', 'Anda tidak dapat menghapus akun sendiri.');
            return;
        }

        // Profil akademik harus dikelola dari menu Guru/Siswa,
        // bukan dihapus secara tidak sengaja dari menu akun.
        if (
            Guru::where('user_id', $user->id)->exists()
            || Siswa::where('user_id', $user->id)->exists()
        ) {
            $this->showDeleteModal = false;

            session()->flash(
                'error',
                'Akun masih terhubung dengan profil Guru atau Siswa. Lepaskan atau kelola keterkaitannya terlebih dahulu.'
            );

            return;
        }

        DB::transaction(function () use ($user) {
            $user->userRoles()->delete();
            $user->delete();
        });

        $this->showDeleteModal = false;
        $this->deleteId = null;

        session()->flash('success', 'Akun pengguna berhasil dihapus.');
    }

    protected function resetForm(): void
    {
        $this->resetValidation();

        $this->penggunaId = null;
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->role_id = '';
    }

    public function render()
    {
        return view('livewire.admin.pengguna.pengguna-manager', [
            'penggunas' => $this->penggunaQuery()
                ->with('userRoles.role')
                ->when(trim($this->search) !== '', function ($query) {
                    $search = trim($this->search);

                    $query->where(function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
                })
                ->latest()
                ->paginate(10),

            'roles' => $this->roleQuery()->orderBy('nama_role')->get(),

            'totalPengguna' => $this->penggunaQuery()->count(),
            'totalGuru' => $this->penggunaQuery()
                ->whereHas('userRoles.role', fn ($query) =>
                    $query->where('nama_role', 'guru')
                )
                ->count(),
            'totalSiswa' => $this->penggunaQuery()
                ->whereHas('userRoles.role', fn ($query) =>
                    $query->where('nama_role', 'siswa')
                )
                ->count(),
        ])
            ->layout('admin.layouts.app')
            ->title('Akun dan Role Pengguna');
    }
}
