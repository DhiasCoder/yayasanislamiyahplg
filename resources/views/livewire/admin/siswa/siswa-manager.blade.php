
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Manajemen Siswa</h1>
            <p class="mt-1 text-sm text-gray-500">
                Kelola data siswa di sekolah Anda.
            </p>
        </div>

        <button wire:click="create"
            class="inline-flex items-center justify-center gap-2 rounded-lg bg-green-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-green-700">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Siswa
        </button>
    </div>

    {{-- Flash messages --}}
    @if (session()->has('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    {{-- Main card --}}
    <div class="overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm">
        <div class="flex flex-col gap-3 border-b border-gray-100 p-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-gray-800">Daftar Siswa</h2>
                <p class="mt-1 text-sm text-gray-500">
                    Total {{ $siswas->total() }} siswa
                </p>
            </div>

            <div class="relative w-full sm:max-w-xs">
                <svg class="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="7" stroke-width="2"/>
                    <path stroke-linecap="round" stroke-width="2" d="m16 16 4 4"/>
                </svg>

                <input type="text"
                    wire:model.lazy="search"
                    placeholder="Cari nama, NIS, NISN, atau NIK..."
                    class="w-full rounded-lg border border-gray-200 py-2.5 pl-10 pr-4 text-sm text-gray-700 outline-none transition focus:border-green-500 focus:ring-2 focus:ring-green-100">
            </div>
        </div>

        @if (count($selected) > 0)
            <div class="flex flex-col gap-3 border-b border-blue-100 bg-blue-50 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-blue-700">
                    {{ count($selected) }} siswa dipilih
                </p>

                <button wire:click="confirmBulkDelete"
                    class="rounded-lg border border-red-200 bg-white px-3 py-2 text-sm font-medium text-red-600 transition hover:bg-red-50">
                    Hapus yang Dipilih
                </button>
            </div>
        @endif

        {{-- Desktop table --}}
        <div class="hidden overflow-x-auto md:block">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="w-12 px-5 py-3 text-left">
                            <input type="checkbox"
                                wire:model="selectAll"
                                class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                        </th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">No.</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Siswa</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">NIS / NISN</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Jenis Kelamin</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Tahun Masuk</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @forelse ($siswas as $index => $siswa)
                        <tr wire:key="siswa-row-{{ $siswa->id }}" class="transition hover:bg-gray-50">
                            <td class="px-5 py-4">
                                <input type="checkbox"
                                    wire:model="selected"
                                    value="{{ $siswa->id }}"
                                    class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                            </td>

                            <td class="px-5 py-4 text-sm text-gray-500">
                                {{ $siswas->firstItem() + $index }}
                            </td>

                            <td class="px-5 py-4">
                                <div class="font-medium text-gray-800">{{ $siswa->nama_lengkap }}</div>
                                <div class="mt-1 text-xs text-gray-500">{{ $siswa->no_hp ?: 'Tidak ada nomor HP' }}</div>
                            </td>

                            <td class="px-5 py-4 text-sm text-gray-600">
                                <div>NIS: {{ $siswa->nis ?: '-' }}</div>
                                <div class="mt-1 text-xs text-gray-400">NISN: {{ $siswa->nisn ?: '-' }}</div>
                            </td>

                            <td class="px-5 py-4 text-sm text-gray-600">
                                @if ($siswa->jenis_kelamin === 'L')
                                    Laki-laki
                                @elseif ($siswa->jenis_kelamin === 'P')
                                    Perempuan
                                @else
                                    -
                                @endif
                            </td>

                            <td class="px-5 py-4 text-sm text-gray-600">
                                {{ $siswa->tahun_masuk ?: '-' }}
                            </td>

                            <td class="px-5 py-4">
                                @php
                                    $statusClass = match ($siswa->status) {
                                        'aktif' => 'bg-green-100 text-green-700',
                                        'lulus' => 'bg-blue-100 text-blue-700',
                                        'pindah' => 'bg-yellow-100 text-yellow-700',
                                        'keluar' => 'bg-red-100 text-red-700',
                                        default => 'bg-gray-100 text-gray-600',
                                    };
                                @endphp

                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $statusClass }}">
                                    {{ ucfirst($siswa->status) }}
                                </span>
                            </td>

                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-2">
                                    <button wire:click="edit({{ $siswa->id }})"
                                        class="rounded-lg border border-gray-200 px-3 py-1.5 text-sm font-medium text-gray-600 transition hover:border-green-300 hover:bg-green-50 hover:text-green-700">
                                        Edit
                                    </button>

                                    <button wire:click="confirmDelete({{ $siswa->id }})"
                                        class="rounded-lg border border-red-200 px-3 py-1.5 text-sm font-medium text-red-600 transition hover:bg-red-50">
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-12 text-center">
                                <p class="font-medium text-gray-700">Data siswa belum tersedia</p>
                                <p class="mt-1 text-sm text-gray-500">Tambahkan siswa atau ubah kata pencarian.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile cards --}}
        <div class="divide-y divide-gray-100 md:hidden">
            @forelse ($siswas as $siswa)
                <div wire:key="siswa-mobile-{{ $siswa->id }}" class="space-y-3 p-4">
                    <div class="flex items-start gap-3">
                        <input type="checkbox"
                            wire:model="selected"
                            value="{{ $siswa->id }}"
                            class="mt-1 rounded border-gray-300 text-green-600 focus:ring-green-500">

                        <div class="min-w-0 flex-1">
                            <div class="font-semibold text-gray-800">{{ $siswa->nama_lengkap }}</div>
                            <div class="mt-1 text-sm text-gray-500">
                                NIS: {{ $siswa->nis ?: '-' }}
                                <span class="mx-1">·</span>
                                NISN: {{ $siswa->nisn ?: '-' }}
                            </div>

                            <div class="mt-2 flex flex-wrap items-center gap-2">
                                <span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $siswa->status === 'aktif' ? 'bg-green-100 text-green-700' : ($siswa->status === 'lulus' ? 'bg-blue-100 text-blue-700' : ($siswa->status === 'pindah' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700')) }}">
                                    {{ ucfirst($siswa->status) }}
                                </span>

                                <span class="text-xs text-gray-500">
                                    Tahun masuk: {{ $siswa->tahun_masuk ?: '-' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pl-7">
                        <button wire:click="edit({{ $siswa->id }})"
                            class="rounded-lg border border-gray-200 px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50">
                            Edit
                        </button>

                        <button wire:click="confirmDelete({{ $siswa->id }})"
                            class="rounded-lg border border-red-200 px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50">
                            Hapus
                        </button>
                    </div>
                </div>
            @empty
                <div class="px-4 py-10 text-center text-sm text-gray-500">
                    Data siswa belum tersedia.
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if ($siswas->hasPages())
            <div class="border-t border-gray-100 px-4 py-4">
                {{ $siswas->links() }}
            </div>
        @endif
    </div>

    {{-- Modal tambah / edit --}}
    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-gray-900/50 p-4"
            wire:key="siswa-form-modal">
            <div class="my-8 w-full max-w-3xl rounded-2xl bg-white shadow-xl"
                @click.stop>
                <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                    <div>
                        <h2 class="text-lg font-bold text-gray-800">
                            {{ $isEdit ? 'Edit Data Siswa' : 'Tambah Siswa' }}
                        </h2>
                        <p class="mt-1 text-sm text-gray-500">Lengkapi informasi siswa berikut.</p>
                    </div>

                    <button wire:click="$set('showModal', false)"
                        class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form wire:submit="save">
                    <div class="max-h-[65vh] space-y-6 overflow-y-auto px-6 py-5">
                        {{-- Identitas --}}
                        <div>
                            <h3 class="mb-4 text-sm font-semibold text-gray-800">Identitas Siswa</h3>

                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div class="sm:col-span-2">
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Nama Lengkap <span class="text-red-500">*</span></label>
                                    <input type="text" wire:model="nama_lengkap"
                                        class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100"
                                        placeholder="Masukkan nama lengkap">
                                    @error('nama_lengkap') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700">NIS</label>
                                    <input type="text" wire:model="nis"
                                        class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100">
                                    @error('nis') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700">NISN</label>
                                    <input type="text" wire:model="nisn"
                                        class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100">
                                    @error('nisn') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700">NIK</label>
                                    <input type="text" wire:model="nik"
                                        class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100">
                                    @error('nik') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Jenis Kelamin</label>
                                    <select wire:model="jenis_kelamin"
                                        class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100">
                                        <option value="">Pilih jenis kelamin</option>
                                        <option value="L">Laki-laki</option>
                                        <option value="P">Perempuan</option>
                                    </select>
                                    @error('jenis_kelamin') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Tempat Lahir</label>
                                    <input type="text" wire:model="tempat_lahir"
                                        class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100">
                                    @error('tempat_lahir') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Tanggal Lahir</label>
                                    <input type="date" wire:model="tanggal_lahir"
                                        class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100">
                                    @error('tanggal_lahir') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Agama</label>
                                    <input type="text" wire:model="agama"
                                        class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100">
                                    @error('agama') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Kontak --}}
                        <div>
                            <h3 class="mb-4 text-sm font-semibold text-gray-800">Kontak dan Alamat</h3>

                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Nomor HP Siswa</label>
                                    <input type="text" wire:model="no_hp"
                                        class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100">
                                    @error('no_hp') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Nomor HP Orang Tua</label>
                                    <input type="text" wire:model="no_hp_orang_tua"
                                        class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100">
                                    @error('no_hp_orang_tua') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div class="sm:col-span-2">
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Alamat</label>
                                    <textarea wire:model="alamat" rows="3"
                                        class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100"
                                        placeholder="Masukkan alamat lengkap"></textarea>
                                    @error('alamat') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Data orang tua --}}
                        <div>
                            <h3 class="mb-4 text-sm font-semibold text-gray-800">Data Orang Tua</h3>

                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Nama Ayah</label>
                                    <input type="text" wire:model="nama_ayah"
                                        class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100">
                                    @error('nama_ayah') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Nama Ibu</label>
                                    <input type="text" wire:model="nama_ibu"
                                        class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100">
                                    @error('nama_ibu') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Status --}}
                        <div>
                            <h3 class="mb-4 text-sm font-semibold text-gray-800">Informasi Sekolah</h3>

                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Tahun Masuk</label>
                                    <input type="number" wire:model="tahun_masuk"
                                        min="1900" max="{{ date('Y') + 1 }}"
                                        class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100"
                                        placeholder="{{ date('Y') }}">
                                    @error('tahun_masuk') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Status Siswa <span class="text-red-500">*</span></label>
                                    <select wire:model="status"
                                        class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100">
                                        <option value="aktif">Aktif</option>
                                        <option value="lulus">Lulus</option>
                                        <option value="pindah">Pindah</option>
                                        <option value="keluar">Keluar</option>
                                    </select>
                                    @error('status') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col-reverse gap-3 border-t border-gray-100 px-6 py-4 sm:flex-row sm:justify-end">
                        <button type="button" wire:click="$set('showModal', false)"
                            class="rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50">
                            Batal
                        </button>

                        <button type="submit" wire:loading.attr="disabled" wire:target="save"
                            class="rounded-lg bg-green-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-green-700 disabled:opacity-50">
                            <span wire:loading.remove wire:target="save">
                                {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Siswa' }}
                            </span>
                            <span wire:loading wire:target="save">Menyimpan...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Modal hapus satu siswa --}}
    @if ($showDeleteModal)
        <div class="fixed inset-0 z-[60] flex items-center justify-center bg-gray-900/50 p-4">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 7h12m-10 0V5a1 1 0 011-1h4a1 1 0 011 1v2m-7 0l1 13h6l1-13M10 11v5m4-5v5"/>
                    </svg>
                </div>

                <h3 class="mt-4 text-center text-lg font-bold text-gray-800">Hapus Data Siswa?</h3>
                <p class="mt-2 text-center text-sm text-gray-500">
                    Data yang memiliki riwayat akademik tidak dapat dihapus. Tindakan ini tidak bisa dibatalkan.
                </p>

                <div class="mt-6 flex justify-center gap-3">
                    <button wire:click="$set('showDeleteModal', false)"
                        class="rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50">
                        Batal
                    </button>

                    <button wire:click="delete" wire:loading.attr="disabled" wire:target="delete"
                        class="rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-red-700 disabled:opacity-50">
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal hapus massal --}}
    @if ($showBulkDeleteModal)
        <div class="fixed inset-0 z-[60] flex items-center justify-center bg-gray-900/50 p-4">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 7h12m-10 0V5a1 1 0 011-1h4a1 1 0 011 1v2m-7 0l1 13h6l1-13"/>
                    </svg>
                </div>

                <h3 class="mt-4 text-center text-lg font-bold text-gray-800">Hapus Siswa Terpilih?</h3>
                <p class="mt-2 text-center text-sm text-gray-500">
                    {{ count($selected) }} siswa dipilih. Siswa yang memiliki riwayat akademik akan dilewati.
                </p>

                <div class="mt-6 flex justify-center gap-3">
                    <button wire:click="$set('showBulkDeleteModal', false)"
                        class="rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50">
                        Batal
                    </button>

                    <button wire:click="deleteSelected" wire:loading.attr="disabled" wire:target="deleteSelected"
                        class="rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-red-700 disabled:opacity-50">
                        Ya, Hapus Terpilih
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
