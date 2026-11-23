@php
$jumlahTerpilih = count($selected);
@endphp

{{-- Livewire: elemen root harus satu --}}
<div class="space-y-6">

    {{-- ================= HEADER ================= --}}
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-500 to-brand-600 p-6 text-white shadow-lg shadow-brand-500/20 sm:p-8">
        <div class="pointer-events-none absolute -right-12 -top-12 h-48 w-48 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute -bottom-16 right-1/3 h-40 w-40 rounded-full bg-white/10"></div>

        <div class="relative flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-extrabold sm:text-3xl">Manajemen Mata Pelajaran</h1>
                <p class="mt-2 max-w-md text-sm text-brand-50">
                    Kelola daftar mata pelajaran dan kelompok kurikulum di sekolah Anda.
                </p>

                <span class="mt-4 inline-flex items-center gap-2 rounded-full bg-white/15 px-3.5 py-1.5 text-xs font-medium backdrop-blur">
                    <i class="fa-solid fa-book-open"></i>
                    {{ $totalMataPelajaran }} mata pelajaran terdaftar
                </span>
            </div>

            <button type="button" wire:click="create"
                class="inline-flex shrink-0 items-center justify-center gap-2 self-start rounded-2xl bg-white px-5 py-3 text-sm font-bold text-brand-700 shadow-md transition hover:bg-brand-50 active:scale-[0.99] sm:self-auto">
                <i class="fa-solid fa-plus text-xs"></i>
                Tambah Mata Pelajaran
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

    {{-- ================= STATISTIK ================= --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div class="flex items-center gap-4 rounded-2xl border border-brand-100 bg-white p-5 shadow-sm">
            <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-brand-100 text-lg text-brand-600">
                <i class="fa-solid fa-book"></i>
            </span>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Total Mata Pelajaran</p>
                <p class="mt-0.5 text-2xl font-extrabold text-slate-800">
                    {{ $totalMataPelajaran }}
                </p>
            </div>
        </div>

        <div class="flex items-center gap-4 rounded-2xl border border-brand-100 bg-white p-5 shadow-sm">
            <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-brand-100 text-lg text-brand-600">
                <i class="fa-solid fa-circle-check"></i>
            </span>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Mata Pelajaran Aktif</p>
                <p class="mt-0.5 text-2xl font-extrabold text-brand-600">
                    {{ $mataPelajaranAktif }}
                </p>
            </div>
        </div>
    </div>

    {{-- ================= FILTER & AKSI MASSAL ================= --}}
    <div class="rounded-2xl border border-brand-100 bg-white p-4 shadow-sm sm:p-5">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="relative flex-1 sm:max-w-md">
                <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-sm text-brand-400"></i>

                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Cari kode, nama, atau kelompok..."
                    class="w-full rounded-xl border border-brand-100 bg-brand-50/50 py-3 pl-11 pr-11 text-sm outline-none transition
                           placeholder:text-slate-400 focus:border-brand-300 focus:bg-white focus:ring-2 focus:ring-brand-200">

                <i wire:loading wire:target="search"
                    class="fa-solid fa-circle-notch fa-spin absolute right-4 top-1/2 -translate-y-1/2 text-sm text-brand-500"></i>
            </div>

            <button type="button"
                wire:click="confirmBulkDelete"
                @disabled($jumlahTerpilih===0)
                class="inline-flex items-center justify-center gap-2 rounded-xl border border-red-200 bg-red-50/50 px-4 py-2.5 text-xs font-semibold text-red-600 transition
                       hover:bg-red-100 disabled:cursor-not-allowed disabled:border-slate-200 disabled:bg-slate-50 disabled:text-slate-400 sm:text-sm">
                <i class="fa-solid fa-trash-can text-xs"></i>
                Hapus Terpilih ({{ $jumlahTerpilih }})
            </button>
        </div>
    </div>

    {{-- ================= TABEL MATA PELAJARAN ================= --}}
    <div class="overflow-hidden rounded-2xl border border-brand-100 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[820px] text-left text-sm">
                <thead class="bg-brand-50 text-xs uppercase tracking-wide text-brand-700">
                    <tr>
                        <th class="w-11 px-4 py-3.5">
                            <input
                                type="checkbox"
                                wire:model.live="selectAll"
                                class="h-4 w-4 rounded border-brand-300 text-brand-600 focus:ring-brand-200"
                                aria-label="Pilih semua mata pelajaran">
                        </th>
                        <th class="px-4 py-3.5">Kode</th>
                        <th class="px-4 py-3.5">Nama Mata Pelajaran</th>
                        <th class="px-4 py-3.5">Kelompok</th>
                        <th class="px-4 py-3.5 text-center">Penugasan</th>
                        <th class="px-4 py-3.5 text-center">Status</th>
                        <th class="px-4 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-brand-50">
                    @forelse ($mataPelajarans as $mataPelajaran)
                    <tr wire:key="mata-pelajaran-{{ $mataPelajaran->id }}" class="transition hover:bg-brand-50/40">
                        <td class="px-4 py-4">
                            <input
                                type="checkbox"
                                wire:model.live="selected"
                                value="{{ $mataPelajaran->id }}"
                                class="h-4 w-4 rounded border-brand-300 text-brand-600 focus:ring-brand-200"
                                aria-label="Pilih {{ $mataPelajaran->nama }}">
                        </td>

                        <td class="whitespace-nowrap px-4 py-4">
                            @if ($mataPelajaran->kode)
                            <span class="inline-block rounded-lg border border-brand-200 bg-brand-50 px-2.5 py-1 font-mono text-xs font-bold text-brand-700">
                                {{ $mataPelajaran->kode }}
                            </span>
                            @else
                            <span class="text-slate-400">-</span>
                            @endif
                        </td>

                        <td class="whitespace-nowrap px-4 py-4">
                            <div class="flex items-center gap-3">
                                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl {{ $mataPelajaran->is_active ? 'bg-brand-100 text-brand-700' : 'bg-slate-100 text-slate-500' }}">
                                    <i class="fa-solid fa-book-open"></i>
                                </span>
                                <span class="font-semibold text-slate-800">
                                    {{ $mataPelajaran->nama }}
                                </span>
                            </div>
                        </td>

                        <td class="whitespace-nowrap px-4 py-4">
                            @if ($mataPelajaran->kelompok)
                            <span class="inline-block rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700">
                                {{ $mataPelajaran->kelompok }}
                            </span>
                            @else
                            <span class="text-slate-400">-</span>
                            @endif
                        </td>

                        <td class="whitespace-nowrap px-4 py-4 text-center">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-sky-50 px-3 py-1 text-xs font-semibold text-sky-700">
                                <i class="fa-solid fa-chalkboard-user text-[10px]"></i>
                                {{ $mataPelajaran->pengajarans_count }} Guru
                            </span>
                        </td>

                        <td class="whitespace-nowrap px-4 py-4 text-center">
                            @if ($mataPelajaran->is_active)
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-brand-100 px-3 py-1 text-xs font-semibold text-brand-700">
                                <span class="h-1.5 w-1.5 rounded-full bg-brand-600"></span>
                                Aktif
                            </span>
                            @else
                            <span class="inline-block rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                Nonaktif
                            </span>
                            @endif
                        </td>

                        <td class="whitespace-nowrap px-4 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button type="button"
                                    wire:click="edit({{ $mataPelajaran->id }})"
                                    class="inline-flex items-center gap-1.5 rounded-xl border border-brand-200 bg-white px-3 py-1.5 text-xs font-semibold text-brand-700 transition hover:bg-brand-50">
                                    <i class="fa-solid fa-pen-to-square text-[11px]"></i>
                                    Edit
                                </button>

                                <button type="button"
                                    wire:click="confirmDelete({{ $mataPelajaran->id }})"
                                    class="inline-flex items-center gap-1.5 rounded-xl border border-red-200 bg-white px-3 py-1.5 text-xs font-semibold text-red-600 transition hover:bg-red-50">
                                    <i class="fa-solid fa-trash-can text-[11px]"></i>
                                    Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center">
                            <span class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-brand-100 text-2xl text-brand-500">
                                <i class="fa-solid fa-book-open"></i>
                            </span>
                            <p class="mt-4 font-bold text-brand-900">Belum ada data mata pelajaran</p>
                            <p class="mt-1 text-sm text-slate-500">Klik tombol "Tambah Mata Pelajaran" untuk membuat mata pelajaran baru.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>{{ $mataPelajarans->links() }}</div>

    {{-- ================= MODAL TAMBAH / EDIT MATA PELAJARAN ================= --}}
    @if ($showModal)
    <div class="fixed inset-0 z-[60] flex items-end justify-center sm:items-center sm:p-4"
        x-data="{ show: false }" x-init="$nextTick(() => show = true)"
        x-on:keydown.escape.window="$wire.set('showModal', false)"
        role="dialog" aria-modal="true">

        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity duration-200"
            :class="show ? 'opacity-100' : 'opacity-0'"
            wire:click="$set('showModal', false)"></div>

        <div class="relative z-10 flex max-h-[92vh] w-full max-w-xl flex-col overflow-hidden rounded-t-3xl bg-white shadow-2xl transition duration-200 sm:rounded-3xl"
            :class="show ? 'translate-y-0 opacity-100' : 'translate-y-4 opacity-0'">

            <form wire:submit="save" class="flex min-h-0 flex-1 flex-col overflow-hidden">
                {{-- Header Modal --}}
                <div class="flex shrink-0 items-center justify-between gap-3 bg-gradient-to-br from-brand-500 to-brand-600 px-6 py-5 text-white">
                    <div class="flex min-w-0 items-center gap-3.5">
                        <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-white text-lg font-extrabold text-brand-700 shadow-md">
                            <i class="fa-solid {{ $isEdit ? 'fa-pen-to-square' : 'fa-book-medical' }}"></i>
                        </span>
                        <div class="min-w-0">
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-brand-100">
                                {{ $isEdit ? 'Perbarui Kurikulum' : 'Mata Pelajaran Baru' }}
                            </p>
                            <h2 class="truncate text-lg font-extrabold sm:text-xl">
                                {{ $isEdit ? 'Edit Mata Pelajaran' : 'Tambah Mata Pelajaran' }}
                            </h2>
                        </div>
                    </div>

                    <button type="button" wire:click="$set('showModal', false)" aria-label="Tutup"
                        class="grid h-9 w-9 shrink-0 place-items-center rounded-lg text-white/80 transition hover:bg-white/15 hover:text-white">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                {{-- Body Form --}}
                <div class="space-y-4 overflow-y-auto p-6">
                    @error('form')
                    <div class="flex items-start gap-2.5 rounded-xl border border-red-200 bg-red-50 p-3 text-xs font-medium text-red-700">
                        <i class="fa-solid fa-circle-exclamation mt-0.5 text-red-500"></i>
                        <p>{{ $message }}</p>
                    </div>
                    @enderror

                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-brand-700">
                            Kode Mata Pelajaran
                        </label>
                        <input
                            type="text"
                            wire:model="kode"
                            placeholder="Contoh: MTK"
                            class="w-full rounded-xl border border-brand-200 bg-white px-3.5 py-2.5 font-mono text-sm text-slate-700 outline-none transition
                                   placeholder:font-sans placeholder:text-slate-400 focus:border-brand-400 focus:ring-2 focus:ring-brand-200">
                        @error('kode')
                        <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-brand-700">
                            Nama Mata Pelajaran <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            wire:model="nama"
                            placeholder="Contoh: Matematika"
                            required
                            class="w-full rounded-xl border border-brand-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition
                                   placeholder:text-slate-400 focus:border-brand-400 focus:ring-2 focus:ring-brand-200">
                        @error('nama')
                        <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-brand-700">
                            Kelompok
                        </label>
                        <input
                            type="text"
                            wire:model="kelompok"
                            placeholder="Contoh: Umum, Kejuruan, atau Muatan Lokal"
                            class="w-full rounded-xl border border-brand-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition
                                   placeholder:text-slate-400 focus:border-brand-400 focus:ring-2 focus:ring-brand-200">
                        @error('kelompok')
                        <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </p>
                        @enderror
                    </div>

                    <label class="flex cursor-pointer items-start gap-3 rounded-2xl border border-brand-200 bg-brand-50/40 p-4 transition hover:bg-brand-50/80 has-[:checked]:border-brand-400 has-[:checked]:bg-brand-50">
                        <input
                            type="checkbox"
                            wire:model="is_active"
                            class="mt-0.5 h-4 w-4 rounded border-brand-300 text-brand-600 focus:ring-brand-200">
                        <span>
                            <span class="block text-sm font-semibold text-slate-800">
                                Mata pelajaran aktif
                            </span>
                            <span class="mt-0.5 block text-xs leading-relaxed text-slate-500">
                                Mata pelajaran aktif dapat dipilih saat mengatur penugasan mengajar guru.
                            </span>
                        </span>
                    </label>
                </div>

                {{-- Footer Modal --}}
                <div class="flex shrink-0 flex-col-reverse gap-2 border-t border-brand-100 bg-white px-6 py-4 sm:flex-row sm:justify-end">
                    <button type="button" wire:click="$set('showModal', false)"
                        class="rounded-xl border border-brand-200 px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-brand-50">
                        Batal
                    </button>

                    <button type="submit" wire:loading.attr="disabled" wire:target="save"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-brand-500/20 transition hover:bg-brand-700 disabled:cursor-not-allowed disabled:opacity-60">
                        <span wire:loading.remove wire:target="save" class="inline-flex items-center gap-2">
                            <i class="fa-solid fa-floppy-disk text-xs"></i>
                            {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Mata Pelajaran' }}
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

    {{-- ================= MODAL KONFIRMASI HAPUS SATU MATA PELAJARAN ================= --}}
    @if ($showDeleteModal)
    <div class="fixed inset-0 z-[60] flex items-end justify-center sm:items-center sm:p-4"
        x-data="{ show: false }" x-init="$nextTick(() => show = true)"
        x-on:keydown.escape.window="$wire.set('showDeleteModal', false)"
        role="dialog" aria-modal="true">

        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity duration-200"
            :class="show ? 'opacity-100' : 'opacity-0'"
            wire:click="$set('showDeleteModal', false)"></div>

        <div class="relative z-10 flex w-full max-w-md flex-col overflow-hidden rounded-t-3xl bg-white shadow-2xl transition duration-200 sm:rounded-3xl"
            :class="show ? 'translate-y-0 opacity-100' : 'translate-y-4 opacity-0'">

            <div class="flex items-center justify-between gap-3 bg-gradient-to-br from-red-500 to-red-600 px-6 py-5 text-white">
                <div class="flex items-center gap-3.5">
                    <span class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-white text-lg font-extrabold text-red-600 shadow-md">
                        <i class="fa-solid fa-trash-can"></i>
                    </span>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-white/80">Konfirmasi Hapus</p>
                        <h2 class="text-lg font-extrabold">Hapus Mata Pelajaran</h2>
                    </div>
                </div>

                <button type="button" wire:click="$set('showDeleteModal', false)" aria-label="Tutup"
                    class="grid h-9 w-9 place-items-center rounded-lg text-white/80 transition hover:bg-white/15 hover:text-white">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="space-y-3 p-6">
                <p class="text-sm leading-relaxed text-slate-600">
                    Apakah Anda yakin ingin menghapus data mata pelajaran ini?
                </p>
                <div class="flex items-start gap-2.5 rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs text-amber-800">
                    <i class="fa-solid fa-circle-info mt-0.5 text-amber-500"></i>
                    <p>Mata pelajaran yang masih digunakan dalam <strong>penugasan guru</strong> tidak dapat dihapus.</p>
                </div>
            </div>

            <div class="flex flex-col-reverse gap-2 border-t border-slate-100 bg-white px-6 py-4 sm:flex-row sm:justify-end">
                <button type="button" wire:click="$set('showDeleteModal', false)"
                    class="rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                    Batal
                </button>
                <button type="button" wire:click="delete" wire:loading.attr="disabled" wire:target="delete"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-red-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-red-500/20 transition hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-60">
                    <span wire:loading.remove wire:target="delete" class="inline-flex items-center gap-2">
                        <i class="fa-solid fa-trash-can text-xs"></i>
                        Ya, Hapus
                    </span>
                    <span wire:loading wire:target="delete" class="inline-flex items-center gap-2">
                        <i class="fa-solid fa-circle-notch fa-spin text-xs"></i>
                        Menghapus...
                    </span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ================= MODAL KONFIRMASI HAPUS BANYAK MATA PELAJARAN ================= --}}
    @if ($showBulkDeleteModal)
    <div class="fixed inset-0 z-[60] flex items-end justify-center sm:items-center sm:p-4"
        x-data="{ show: false }" x-init="$nextTick(() => show = true)"
        x-on:keydown.escape.window="$wire.set('showBulkDeleteModal', false)"
        role="dialog" aria-modal="true">

        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity duration-200"
            :class="show ? 'opacity-100' : 'opacity-0'"
            wire:click="$set('showBulkDeleteModal', false)"></div>

        <div class="relative z-10 flex w-full max-w-md flex-col overflow-hidden rounded-t-3xl bg-white shadow-2xl transition duration-200 sm:rounded-3xl"
            :class="show ? 'translate-y-0 opacity-100' : 'translate-y-4 opacity-0'">

            <div class="flex items-center justify-between gap-3 bg-gradient-to-br from-red-500 to-red-600 px-6 py-5 text-white">
                <div class="flex items-center gap-3.5">
                    <span class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-white text-lg font-extrabold text-red-600 shadow-md">
                        <i class="fa-solid fa-book"></i>
                    </span>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-white/80">Hapus Massal</p>
                        <h2 class="text-lg font-extrabold">Hapus Mata Pelajaran Terpilih?</h2>
                    </div>
                </div>

                <button type="button" wire:click="$set('showBulkDeleteModal', false)" aria-label="Tutup"
                    class="grid h-9 w-9 place-items-center rounded-lg text-white/80 transition hover:bg-white/15 hover:text-white">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="space-y-3 p-6">
                <p class="text-sm leading-relaxed text-slate-600">
                    Sebanyak <span class="font-bold text-red-600">{{ $jumlahTerpilih }} mata pelajaran</span> dipilih untuk dihapus.
                </p>
                <div class="flex items-start gap-2.5 rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs text-amber-800">
                    <i class="fa-solid fa-circle-info mt-0.5 text-amber-500"></i>
                    <p>Mata pelajaran yang masih digunakan dalam <strong>penugasan guru</strong> akan otomatis dilewati dan tidak dihapus.</p>
                </div>
            </div>

            <div class="flex flex-col-reverse gap-2 border-t border-slate-100 bg-white px-6 py-4 sm:flex-row sm:justify-end">
                <button type="button" wire:click="$set('showBulkDeleteModal', false)"
                    class="rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                    Batal
                </button>
                <button type="button" wire:click="bulkDelete" wire:loading.attr="disabled" wire:target="bulkDelete"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-red-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-red-500/20 transition hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-60">
                    <span wire:loading.remove wire:target="bulkDelete" class="inline-flex items-center gap-2">
                        <i class="fa-solid fa-trash-can text-xs"></i>
                        Hapus Terpilih ({{ $jumlahTerpilih }})
                    </span>
                    <span wire:loading wire:target="bulkDelete" class="inline-flex items-center gap-2">
                        <i class="fa-solid fa-circle-notch fa-spin text-xs"></i>
                        Menghapus...
                    </span>
                </button>
            </div>
        </div>
    </div>
    @endif

</div>