@php
$jumlahTerpilih = count($selectedRoles ?? []);
@endphp

{{-- Livewire: elemen root harus satu --}}
<div class="space-y-6">

    {{-- ================= HEADER ================= --}}
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-500 to-brand-600 p-6 text-white shadow-lg shadow-brand-500/20 sm:p-8">
        <div class="pointer-events-none absolute -right-12 -top-12 h-48 w-48 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute -bottom-16 right-1/3 h-40 w-40 rounded-full bg-white/10"></div>

        <div class="relative flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-extrabold sm:text-3xl">Manajemen Role</h1>
                <p class="mt-2 max-w-lg text-sm text-brand-50">
                    Kelola hak akses dan pembagian peran pengguna di dalam sistem.
                </p>

                <span class="mt-4 inline-flex items-center gap-2 rounded-full bg-white/15 px-3.5 py-1.5 text-xs font-medium backdrop-blur">
                    <i class="fa-solid fa-user-shield"></i>
                    {{ $roleList->total() }} role terdaftar
                </span>
            </div>

            <button type="button" wire:click="resetForm"
                class="inline-flex shrink-0 items-center justify-center gap-2 self-start rounded-2xl bg-white px-5 py-3 text-sm font-bold text-brand-700 shadow-md transition hover:bg-brand-50 active:scale-[0.99] sm:self-auto">
                <i class="fa-solid fa-plus text-xs"></i>
                Tambah Role
            </button>
        </div>
    </section>

    {{-- ================= NOTIFIKASI ================= --}}
    @if (session()->has('success'))
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="flex items-center gap-3 rounded-xl border border-brand-200 bg-brand-50 px-4 py-3 text-sm text-brand-800">
        <i class="fa-solid fa-circle-check text-brand-500"></i>
        <span class="flex-1">{{ session('success') }}</span>
        <button type="button" @click="show = false" class="text-brand-400 transition hover:text-brand-600" aria-label="Tutup">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
    @endif

    @if (session()->has('error'))
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="flex items-center gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
        <i class="fa-solid fa-circle-exclamation text-red-500"></i>
        <span class="flex-1">{{ session('error') }}</span>
        <button type="button" @click="show = false" class="text-red-400 transition hover:text-red-600" aria-label="Tutup">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
    @endif

    {{-- ================= FILTER & AKSI MASSAL ================= --}}
    <div class="rounded-2xl border border-brand-100 bg-white p-4 shadow-sm sm:p-5">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="relative flex-1 sm:max-w-md">
                <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-sm text-brand-400"></i>

                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Cari nama role..."
                    class="w-full rounded-xl border border-brand-100 bg-brand-50/50 py-3 pl-11 pr-11 text-sm text-slate-700 outline-none transition
                           placeholder:text-slate-400 focus:border-brand-300 focus:bg-white focus:ring-2 focus:ring-brand-200">

                <i wire:loading wire:target="search"
                    class="fa-solid fa-circle-notch fa-spin absolute right-4 top-1/2 -translate-y-1/2 text-sm text-brand-500"></i>
            </div>

            <button
                type="button"
                wire:click="confirmBulkDelete"
                @disabled($jumlahTerpilih===0)
                class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl border border-red-200 bg-red-50/50 px-4 py-2.5 text-xs font-semibold text-red-600 transition
                       hover:bg-red-100 disabled:cursor-not-allowed disabled:border-slate-200 disabled:bg-slate-50 disabled:text-slate-400 sm:text-sm">
                <i class="fa-solid fa-trash-can text-xs"></i>
                <span>Hapus Terpilih ({{ $jumlahTerpilih }})</span>
            </button>
        </div>
    </div>

    {{-- ================= TABEL DESKTOP ================= --}}
    <div class="hidden overflow-hidden rounded-2xl border border-brand-100 bg-white shadow-sm md:block">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-brand-50 text-xs uppercase tracking-wide text-brand-700">
                    <tr>
                        <th class="w-11 px-5 py-3.5">
                            <input
                                type="checkbox"
                                wire:model.live="selectAll"
                                wire:change="toggleSelectAll"
                                class="h-4 w-4 rounded border-brand-300 text-brand-600 focus:ring-brand-200"
                                aria-label="Pilih semua role">
                        </th>
                        <th class="w-16 px-5 py-3.5">No</th>
                        <th class="px-5 py-3.5">Nama Role</th>
                        <th class="px-5 py-3.5">Jumlah Pengguna</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-brand-50">
                    @forelse ($roleList as $index => $role)
                    <tr wire:key="role-{{ $role->id }}" class="transition hover:bg-brand-50/40">
                        <td class="px-5 py-4">
                            <input
                                type="checkbox"
                                wire:model.live="selectedRoles"
                                value="{{ $role->id }}"
                                class="h-4 w-4 rounded border-brand-300 text-brand-600 focus:ring-brand-200"
                                aria-label="Pilih role {{ $role->nama_role }}">
                        </td>

                        <td class="whitespace-nowrap px-5 py-4 text-slate-400">
                            {{ $roleList->firstItem() + $index }}
                        </td>

                        <td class="whitespace-nowrap px-5 py-4">
                            <div class="flex items-center gap-3">
                                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-brand-100 text-brand-700">
                                    <i class="fa-solid fa-user-shield"></i>
                                </span>
                                <span class="font-bold text-slate-800">
                                    {{ $role->nama_role }}
                                </span>
                            </div>
                        </td>

                        <td class="whitespace-nowrap px-5 py-4">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-sky-50 px-3 py-1 text-xs font-bold text-sky-700">
                                <i class="fa-solid fa-users text-[10px]"></i>
                                {{ $role->users_count }} Pengguna
                            </span>
                        </td>

                        <td class="whitespace-nowrap px-5 py-4 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <button type="button"
                                    wire:click="edit({{ $role->id }})"
                                    class="inline-flex items-center gap-1.5 rounded-xl border border-brand-200 bg-white px-3 py-1.5 text-xs font-semibold text-brand-700 transition hover:bg-brand-50"
                                    title="Edit">
                                    <i class="fa-solid fa-pen-to-square text-[11px]"></i>
                                    Edit
                                </button>

                                <button type="button"
                                    wire:click="confirmDelete({{ $role->id }})"
                                    class="inline-flex items-center gap-1.5 rounded-xl border border-red-200 bg-white px-3 py-1.5 text-xs font-semibold text-red-600 transition hover:bg-red-50"
                                    title="Hapus">
                                    <i class="fa-solid fa-trash-can text-[11px]"></i>
                                    Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <span class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-brand-100 text-2xl text-brand-500">
                                <i class="fa-solid fa-user-shield"></i>
                            </span>
                            <p class="mt-4 font-bold text-brand-900">Data role tidak ditemukan</p>
                            <p class="mt-1 text-sm text-slate-500">
                                {{ filled($search ?? null) ? 'Coba gunakan kata kunci pencarian nama role lain.' : 'Klik tombol "Tambah Role" untuk menambahkan peran baru.' }}
                            </p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ================= KARTU MOBILE ================= --}}
    <div class="space-y-3 md:hidden">
        @forelse ($roleList as $role)
        <div wire:key="role-mobile-{{ $role->id }}"
            class="rounded-2xl border border-brand-100 bg-white p-4 shadow-sm transition hover:border-brand-200">
            <div class="flex items-start gap-3">
                <input
                    type="checkbox"
                    wire:model.live="selectedRoles"
                    value="{{ $role->id }}"
                    class="mt-1 h-4 w-4 rounded border-brand-300 text-brand-600 focus:ring-brand-200"
                    aria-label="Pilih role {{ $role->nama_role }}">

                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-brand-100 text-brand-700">
                    <i class="fa-solid fa-user-shield"></i>
                </span>

                <div class="min-w-0 flex-1">
                    <h3 class="truncate text-base font-extrabold text-slate-800">
                        {{ $role->nama_role }}
                    </h3>
                    <span class="mt-1.5 inline-flex items-center gap-1.5 rounded-full bg-sky-50 px-2.5 py-0.5 text-xs font-bold text-sky-700">
                        <i class="fa-solid fa-users text-[10px]"></i>
                        {{ $role->users_count }} Pengguna
                    </span>
                </div>
            </div>

            <div class="mt-4 flex gap-2 border-t border-brand-50 pt-3">
                <button type="button" wire:click="edit({{ $role->id }})"
                    class="inline-flex flex-1 items-center justify-center gap-1.5 rounded-xl border border-brand-200 bg-brand-50/50 py-2 text-xs font-semibold text-brand-700 transition hover:bg-brand-100">
                    <i class="fa-solid fa-pen-to-square text-[11px]"></i>
                    Edit
                </button>
                <button type="button" wire:click="confirmDelete({{ $role->id }})"
                    class="inline-flex flex-1 items-center justify-center gap-1.5 rounded-xl border border-red-200 bg-red-50/50 py-2 text-xs font-semibold text-red-600 transition hover:bg-red-100">
                    <i class="fa-solid fa-trash-can text-[11px]"></i>
                    Hapus
                </button>
            </div>
        </div>
        @empty
        <div class="rounded-2xl border border-dashed border-brand-200 bg-white/70 px-6 py-14 text-center">
            <span class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-brand-100 text-2xl text-brand-500">
                <i class="fa-solid fa-user-shield"></i>
            </span>
            <p class="mt-4 font-bold text-brand-900">Data role tidak ditemukan</p>
            <p class="mt-1 text-sm text-slate-500">
                {{ filled($search ?? null) ? 'Coba gunakan kata kunci pencarian nama role lain.' : 'Klik tombol "Tambah Role" untuk menambahkan peran baru.' }}
            </p>
        </div>
        @endforelse
    </div>

    {{-- ================= PAGINASI ================= --}}
    <div>{{ $roleList->links() }}</div>

    {{-- ================= MODAL TAMBAH / EDIT ROLE ================= --}}
    @if ($showModal)
    <div class="fixed inset-0 z-[60] flex items-end justify-center sm:items-center sm:p-4"
        x-data="{ show: false }" x-init="$nextTick(() => show = true)"
        x-on:keydown.escape.window="$wire.set('showModal', false)"
        role="dialog" aria-modal="true">

        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity duration-200"
            :class="show ? 'opacity-100' : 'opacity-0'"
            wire:click="$set('showModal', false)"></div>

        <div class="relative z-10 flex w-full max-w-lg flex-col overflow-hidden rounded-t-3xl bg-white shadow-2xl transition duration-200 sm:rounded-3xl"
            :class="show ? 'translate-y-0 opacity-100' : 'translate-y-4 opacity-0'"
            wire:click.stop>

            <form wire:submit="save" class="flex flex-col">
                {{-- Header Modal --}}
                <div class="flex items-center justify-between gap-3 bg-gradient-to-br from-brand-500 to-brand-600 px-6 py-5 text-white">
                    <div class="flex min-w-0 items-center gap-3.5">
                        <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-white text-lg font-extrabold text-brand-700 shadow-md">
                            <i class="fa-solid {{ $editMode ? 'fa-pen-to-square' : 'fa-user-shield' }}"></i>
                        </span>
                        <div class="min-w-0">
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-brand-100">
                                {{ $editMode ? 'Perbarui Peran' : 'Peran Baru' }}
                            </p>
                            <h2 class="truncate text-lg font-extrabold sm:text-xl">
                                {{ $editMode ? 'Edit Role' : 'Tambah Role' }}
                            </h2>
                        </div>
                    </div>

                    <button type="button" wire:click="$set('showModal', false)" aria-label="Tutup"
                        class="grid h-9 w-9 shrink-0 place-items-center rounded-lg text-white/80 transition hover:bg-white/15 hover:text-white">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                {{-- Body Form --}}
                <div class="space-y-4 p-6">
                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-brand-700">
                            Nama Role <span class="text-red-500">*</span>
                        </label>

                        <input type="text"
                            wire:model="nama_role"
                            placeholder="Contoh: admin_akademik"
                            class="w-full rounded-xl border border-brand-200 bg-white px-3.5 py-2.5 font-mono text-sm text-slate-700 outline-none transition
                                   placeholder:font-sans placeholder:text-slate-400 focus:border-brand-400 focus:ring-2 focus:ring-brand-200">

                        <p class="mt-1.5 text-xs text-slate-400">
                            Gunakan huruf kecil tanpa spasi (pisahkan dengan garis bawah <code class="font-mono text-slate-600">_</code>).
                        </p>

                        @error('nama_role')
                        <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </p>
                        @enderror
                    </div>
                </div>

                {{-- Footer Modal --}}
                <div class="flex flex-col-reverse gap-2 border-t border-brand-100 bg-white px-6 py-4 sm:flex-row sm:justify-end">
                    <button type="button"
                        wire:click="$set('showModal', false)"
                        class="rounded-xl border border-brand-200 px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-brand-50">
                        Batal
                    </button>

                    <button type="submit" wire:loading.attr="disabled" wire:target="save"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-brand-500/20 transition hover:bg-brand-700 disabled:cursor-not-allowed disabled:opacity-60">
                        <span wire:loading.remove wire:target="save" class="inline-flex items-center gap-2">
                            <i class="fa-solid fa-floppy-disk text-xs"></i>
                            Simpan
                        </span>
                        <span wire:loading wire:target="save" class="inline-flex items-center gap-2">
                            <i class="fa-solid fa-circle-notch fa-spin text-xs"></i>
                            Menyimpan...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- ================= MODAL KONFIRMASI HAPUS ROLE ================= --}}
    @if ($confirmingDelete)
    <div class="fixed inset-0 z-[60] flex items-end justify-center sm:items-center sm:p-4"
        x-data="{ show: false }" x-init="$nextTick(() => show = true)"
        x-on:keydown.escape.window="$wire.set('confirmingDelete', false)"
        role="dialog" aria-modal="true">

        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity duration-200"
            :class="show ? 'opacity-100' : 'opacity-0'"
            wire:click="$set('confirmingDelete', false)"></div>

        <div class="relative z-10 flex w-full max-w-md flex-col overflow-hidden rounded-t-3xl bg-white shadow-2xl transition duration-200 sm:rounded-3xl"
            :class="show ? 'translate-y-0 opacity-100' : 'translate-y-4 opacity-0'"
            wire:click.stop>

            <div class="flex items-center justify-between gap-3 bg-gradient-to-br from-red-500 to-red-600 px-6 py-5 text-white">
                <div class="flex items-center gap-3.5">
                    <span class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-white text-lg font-extrabold text-red-600 shadow-md">
                        <i class="fa-solid fa-trash-can"></i>
                    </span>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-white/80">Konfirmasi Hapus</p>
                        <h2 class="text-lg font-extrabold">Hapus Role?</h2>
                    </div>
                </div>

                <button type="button" wire:click="$set('confirmingDelete', false)" aria-label="Tutup"
                    class="grid h-9 w-9 place-items-center rounded-lg text-white/80 transition hover:bg-white/15 hover:text-white">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="space-y-3 p-6">
                <p class="text-sm leading-relaxed text-slate-600">
                    Apakah Anda yakin ingin menghapus role ini dari sistem?
                </p>
                <div class="flex items-start gap-2.5 rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs text-amber-800">
                    <i class="fa-solid fa-circle-info mt-0.5 text-amber-500"></i>
                    <p>Role yang masih digunakan oleh akun pengguna <strong>tidak dapat dihapus</strong>.</p>
                </div>
            </div>

            <div class="flex flex-col-reverse gap-2 border-t border-slate-100 bg-white px-6 py-4 sm:flex-row sm:justify-end">
                <button type="button" wire:click="$set('confirmingDelete', false)"
                    class="rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                    Batal
                </button>

                <button type="button" wire:click="deleteConfirmed" wire:loading.attr="disabled" wire:target="deleteConfirmed"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-red-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-red-500/20 transition hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-60">
                    <span wire:loading.remove wire:target="deleteConfirmed" class="inline-flex items-center gap-2">
                        <i class="fa-solid fa-trash-can text-xs"></i>
                        Ya, Hapus
                    </span>
                    <span wire:loading wire:target="deleteConfirmed" class="inline-flex items-center gap-2">
                        <i class="fa-solid fa-circle-notch fa-spin text-xs"></i>
                        Menghapus...
                    </span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ================= MODAL KONFIRMASI HAPUS BANYAK ================= --}}
    @if ($confirmingBulkDelete)
    <div class="fixed inset-0 z-[60] flex items-end justify-center sm:items-center sm:p-4"
        x-data="{ show: false }" x-init="$nextTick(() => show = true)"
        x-on:keydown.escape.window="$wire.set('confirmingBulkDelete', false)"
        role="dialog" aria-modal="true">

        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity duration-200"
            :class="show ? 'opacity-100' : 'opacity-0'"
            wire:click="$set('confirmingBulkDelete', false)"></div>

        <div class="relative z-10 flex w-full max-w-md flex-col overflow-hidden rounded-t-3xl bg-white shadow-2xl transition duration-200 sm:rounded-3xl"
            :class="show ? 'translate-y-0 opacity-100' : 'translate-y-4 opacity-0'"
            wire:click.stop>

            <div class="flex items-center justify-between gap-3 bg-gradient-to-br from-red-500 to-red-600 px-6 py-5 text-white">
                <div class="flex items-center gap-3.5">
                    <span class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-white text-lg font-extrabold text-red-600 shadow-md">
                        <i class="fa-solid fa-trash-can"></i>
                    </span>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-white/80">Konfirmasi Hapus Massal</p>
                        <h2 class="text-lg font-extrabold">Hapus {{ $jumlahTerpilih }} Role Terpilih?</h2>
                    </div>
                </div>

                <button type="button" wire:click="$set('confirmingBulkDelete', false)" aria-label="Tutup"
                    class="grid h-9 w-9 place-items-center rounded-lg text-white/80 transition hover:bg-white/15 hover:text-white">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="space-y-3 p-6">
                <p class="text-sm leading-relaxed text-slate-600">
                    Apakah Anda yakin ingin menghapus semua role yang dipilih?
                </p>
                <div class="flex items-start gap-2.5 rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs text-amber-800">
                    <i class="fa-solid fa-circle-info mt-0.5 text-amber-500"></i>
                    <p>Role yang masih digunakan oleh akun pengguna akan <strong>otomatis dilewati</strong>.</p>
                </div>
            </div>

            <div class="flex flex-col-reverse gap-2 border-t border-slate-100 bg-white px-6 py-4 sm:flex-row sm:justify-end">
                <button type="button" wire:click="$set('confirmingBulkDelete', false)"
                    class="rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                    Batal
                </button>

                <button type="button" wire:click="deleteSelectedConfirmed" wire:loading.attr="disabled" wire:target="deleteSelectedConfirmed"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-red-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-red-500/20 transition hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-60">
                    <span wire:loading.remove wire:target="deleteSelectedConfirmed" class="inline-flex items-center gap-2">
                        <i class="fa-solid fa-trash-can text-xs"></i>
                        Ya, Hapus Terpilih
                    </span>
                    <span wire:loading wire:target="deleteSelectedConfirmed" class="inline-flex items-center gap-2">
                        <i class="fa-solid fa-circle-notch fa-spin text-xs"></i>
                        Menghapus...
                    </span>
                </button>
            </div>
        </div>
    </div>
    @endif

</div>