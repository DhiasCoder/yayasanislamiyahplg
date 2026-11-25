<?php

namespace App\Livewire\Admin\Guru;

use App\Models\Guru;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class GuruManager extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $showDetailModal = false;
    public $showDeleteModal = false;
    public $showBulkDeleteModal = false;
    public $isEdit = false;

    public $guruId = null;
    public $user_id = '';
    public $nip = '';
    public $nuptk = '';
    public $nama_lengkap = '';
    public $jenis_kelamin = '';
    public $tempat_lahir = '';
    public $tanggal_lahir = '';
    public $alamat = '';
    public $no_hp = '';
    public $status = 'aktif';

    public array $detailGuru = [];

    public $selected = [];
    public $selectAll = false;
    public $deleteId = null;

    protected function getSekolahId(): ?int
    {
        $user = Auth::user();

        if (!$user) {
            return null;
        }

        return $user->userRoles()
            ->whereHas(
                'role',
                fn($query) =>
                $query->where('nama_role', 'admin')
            )
            ->value('sekolah_id');
    }

    protected function guruQuery()
    {
        $sekolahId = $this->getSekolahId();

        if (!$sekolahId) {
            return Guru::query()->whereRaw('1 = 0');
        }

        return Guru::query()->where('sekolah_id', $sekolahId);
    }

    protected function rules(): array
    {
        return [
            'user_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id'),
            ],
            'nip' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('gurus', 'nip')->ignore($this->guruId),
            ],
            'nuptk' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('gurus', 'nuptk')->ignore($this->guruId),
            ],
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'jenis_kelamin' => [
                'required',
                Rule::in(['L', 'P']),
            ],
            'tempat_lahir' => ['nullable', 'string', 'max:255'],
            'tanggal_lahir' => [
                'nullable',
                'date',
                'before_or_equal:today',
            ],
            'alamat' => ['nullable', 'string'],
            'no_hp' => ['nullable', 'string', 'max:255'],
            'status' => [
                'required',
                Rule::in(['aktif', 'nonaktif']),
            ],
        ];
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
        $this->selected = [];
        $this->selectAll = false;
    }

    public function updatedSelectAll($value): void
    {
        if (!$value) {
            $this->selected = [];
            return;
        }

        $this->selected = $this->filteredGuruQuery()
            ->latest()
            ->paginate(10)
            ->pluck('id')
            ->map(fn($id) => (string) $id)
            ->toArray();
    }

    protected function filteredGuruQuery()
    {
        return $this->guruQuery()
            ->when(trim($this->search) !== '', function ($query) {
                $search = trim($this->search);

                $query->where(function ($q) use ($search) {
                    $q->where('nama_lengkap', 'like', "%{$search}%")
                        ->orWhere('nip', 'like', "%{$search}%")
                        ->orWhere('nuptk', 'like', "%{$search}%");
                });
            });
    }

    public function showDetail($id): void
    {
        $guru = $this->guruQuery()->with('user')->findOrFail($id);

        $this->detailGuru = [
            'id' => $guru->id,
            'nama_lengkap' => $guru->nama_lengkap,
            'akun_nama' => $guru->user?->name ?? '-',
            'akun_email' => $guru->user?->email ?? 'Belum terhubung ke akun',
            'nip' => $guru->nip ?: '-',
            'nuptk' => $guru->nuptk ?: '-',
            'jenis_kelamin' => match ($guru->jenis_kelamin) {
                'L' => 'Laki-laki',
                'P' => 'Perempuan',
                default => '-',
            },
            'tempat_lahir' => $guru->tempat_lahir ?: '-',
            'tanggal_lahir' => $guru->tanggal_lahir
                ? Carbon::parse($guru->tanggal_lahir)->translatedFormat('d F Y')
                : '-',
            'no_hp' => $guru->no_hp ?: '-',
            'alamat' => $guru->alamat ?: '-',
            'status' => $guru->status,
        ];

        $this->showDetailModal = true;
    }

    public function closeDetail(): void
    {
        $this->showDetailModal = false;
        $this->detailGuru = [];
    }

    public function editFromDetail(): void
    {
        $id = $this->detailGuru['id'] ?? null;
        $this->closeDetail();

        if ($id) {
            $this->edit($id);
        }
    }

    public function create(): void
    {
        $this->resetForm();
        $this->isEdit = false;
        $this->showModal = true;
    }

    public function edit($id): void
    {
        $guru = $this->guruQuery()->findOrFail($id);

        $this->resetForm();

        $this->guruId = $guru->id;
        $this->user_id = (string) $guru->user_id;
        $this->nip = $guru->nip ?? '';
        $this->nuptk = $guru->nuptk ?? '';
        $this->nama_lengkap = $guru->nama_lengkap;
        $this->jenis_kelamin = $guru->jenis_kelamin ?? '';
        $this->tempat_lahir = $guru->tempat_lahir ?? '';
        $this->tanggal_lahir = $guru->tanggal_lahir
            ? Carbon::parse($guru->tanggal_lahir)->format('Y-m-d')
            : '';
        $this->alamat = $guru->alamat ?? '';
        $this->no_hp = $guru->no_hp ?? '';
        $this->status = $guru->status;

        $this->isEdit = true;
        $this->showModal = true;
    }

    public function save(): void
    {
        $sekolahId = $this->getSekolahId();

        if (!$sekolahId) {
            session()->flash('error', 'Sekolah untuk akun Admin tidak ditemukan.');
            return;
        }

        $validated = $this->validate();

        $akunValid = User::whereKey($validated['user_id'])
            ->whereHas('userRoles', function ($query) use ($sekolahId) {
                $query->where('sekolah_id', $sekolahId)
                    ->whereHas(
                        'role',
                        fn($role) =>
                        $role->where('nama_role', 'guru')
                    );
            })
            ->exists();

        if (!$akunValid) {
            $this->addError(
                'user_id',
                'Pilih akun guru yang terdaftar pada sekolah ini.'
            );
            return;
        }

        $akunSudahDipakai = Guru::where('user_id', $validated['user_id'])
            ->when(
                $this->isEdit,
                fn($query) =>
                $query->where('id', '!=', $this->guruId)
            )
            ->exists();

        if ($akunSudahDipakai) {
            $this->addError('user_id', 'Akun ini sudah terhubung dengan profil guru lain.');
            return;
        }

        $data = [
            ...$validated,
            'sekolah_id' => $sekolahId,
            'tanggal_lahir' => $validated['tanggal_lahir'] ?: null,
            'nip' => $validated['nip'] ?: null,
            'nuptk' => $validated['nuptk'] ?: null,
        ];

        if ($this->isEdit) {
            $this->guruQuery()->findOrFail($this->guruId)->update($data);
        } else {
            Guru::create($data);
        }

        session()->flash(
            'success',
            $this->isEdit
                ? 'Data guru berhasil diperbarui.'
                : 'Data guru berhasil ditambahkan.'
        );

        $this->showModal = false;
        $this->resetForm();
        $this->resetPage();
    }

    public function confirmDelete($id): void
    {
        $this->guruQuery()->findOrFail($id);

        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    protected function hasTeachingAssignments(Guru $guru): bool
    {
        return Schema::hasTable('pengajarans')
            && Schema::hasColumn('pengajarans', 'guru_id')
            && DB::table('pengajarans')
            ->where('guru_id', $guru->id)
            ->exists();
    }

    public function delete(): void
    {
        $guru = $this->guruQuery()->findOrFail($this->deleteId);

        if ($this->hasTeachingAssignments($guru)) {
            session()->flash(
                'error',
                'Guru tidak dapat dihapus karena masih memiliki penugasan mengajar.'
            );
        } else {
            $guru->delete();

            session()->flash('success', 'Data guru berhasil dihapus.');
        }

        $this->showDeleteModal = false;
        $this->deleteId = null;
        $this->selected = [];
        $this->selectAll = false;
        $this->resetPage();
    }

    public function confirmBulkDelete(): void
    {
        if (empty($this->selected)) {
            session()->flash('error', 'Pilih setidaknya satu guru.');
            return;
        }

        $this->showBulkDeleteModal = true;
    }

    public function deleteSelected(): void
    {
        $deleted = 0;
        $skipped = 0;

        $gurus = $this->guruQuery()
            ->whereIn('id', $this->selected)
            ->get();

        DB::transaction(function () use ($gurus, &$deleted, &$skipped) {
            foreach ($gurus as $guru) {
                if ($this->hasTeachingAssignments($guru)) {
                    $skipped++;
                    continue;
                }

                $guru->delete();
                $deleted++;
            }
        });

        if ($deleted > 0 && $skipped > 0) {
            session()->flash(
                'success',
                "{$deleted} guru dihapus. {$skipped} guru dilewati karena masih memiliki penugasan."
            );
        } elseif ($deleted > 0) {
            session()->flash('success', "{$deleted} data guru berhasil dihapus.");
        } else {
            session()->flash(
                'error',
                'Tidak ada guru yang dihapus karena semua masih memiliki penugasan.'
            );
        }

        $this->showBulkDeleteModal = false;
        $this->selected = [];
        $this->selectAll = false;
        $this->resetPage();
    }

    protected function resetForm(): void
    {
        $this->reset([
            'guruId',
            'user_id',
            'nip',
            'nuptk',
            'nama_lengkap',
            'jenis_kelamin',
            'tempat_lahir',
            'tanggal_lahir',
            'alamat',
            'no_hp',
        ]);

        $this->status = 'aktif';
        $this->resetValidation();
    }

    public function render()
    {
        $akunGuru = collect();

        if ($this->showModal) {
            $sekolahId = $this->getSekolahId();

            if ($sekolahId) {
                $akunTerpakai = Guru::query()
                    ->whereNotNull('user_id')
                    ->when(
                        $this->isEdit,
                        fn($query) =>
                        $query->where('id', '!=', $this->guruId)
                    )
                    ->pluck('user_id');

                $akunGuru = User::query()
                    ->whereHas('userRoles', function ($query) use ($sekolahId) {
                        $query->where('sekolah_id', $sekolahId)
                            ->whereHas(
                                'role',
                                fn($role) =>
                                $role->where('nama_role', 'guru')
                            );
                    })
                    ->whereNotIn('id', $akunTerpakai)
                    ->orderBy('name')
                    ->get(['id', 'name', 'email']);
            }
        }

        return view('livewire.admin.guru.guru-manager', [
            'gurus' => $this->filteredGuruQuery()
                ->with('user')
                ->latest()
                ->paginate(10),
            'akunGuru' => $akunGuru,
        ])
            ->layout('admin.layouts.app')
            ->title('Manajemen Guru');
    }
}
