@php
$jumlahTerpilih = count($selectedPenempatan);
$jumlahCalonDipilih = count($selectedSiswa);
@endphp

{{-- Livewire: elemen root harus satu --}}
<div class="space-y-6">

    {{-- ================= HEADER ================= --}}
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-500 to-brand-600 p-6 text-white shadow-lg shadow-brand-500/20 sm:p-8">
        <div class="pointer-events-none absolute -right-12 -top-12 h-48 w-48 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute -bottom-16 right-1/3 h-40 w-40 rounded-full bg-white/10"></div>

        <div class="relative flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-extrabold sm:text-3xl">Penempatan Siswa di Kelas</h1>
                <p class="mt-2 max-w-md text-sm text-brand-50">
                    Atur penempatan dan pembagian kelas siswa sesuai tahun ajaran.
                </p>

                <span class="mt-4 inline-flex items-center gap-2 rounded-full bg-white/15 px-3.5 py-1.5 text-xs font-medium backdrop-blur">
                    <i class="fa-solid fa-school"></i>
                    {{ $kelasList->count() }} kelas tersedia
                </span>
            </div>

            <button type="button" wire:click="openModal"
                @disabled($kelasList->isEmpty())
                class="inline-flex shrink-0 items-center justify-center gap-2 self-start rounded-2xl bg-white px-5 py-3 text-sm font-bold text-brand-700 shadow-md transition
                hover:bg-brand-50 active:scale-[0.99] disabled:cursor-not-allowed disabled:opacity-60 sm:self-auto">
                <i class="fa-solid fa-user-plus text-xs"></i>
                Tempatkan Siswa
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

    {{-- ================= PILIH KELAS ================= --}}
    <div class="rounded-2xl border border-brand-100 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-4 md:flex-row md:items-center">
            <span class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-brand-100 text-brand-600">
                <i class="fa-solid fa-chalkboard"></i>
            </span>

            <div class="min-w-0 flex-1">
                <label for="kelasId" class="mb-1.5 block text-sm font-semibold text-slate-700">
                    Pilih Kelas
                </label>

                <select id="kelasId" wire:model.live="kelasId"
                    class="w-full rounded-xl border border-brand-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition md:max-w-lg
                           focus:border-brand-400 focus:ring-2 focus:ring-brand-200">
                    <option value="">Pilih kelas untuk melihat penempatan</option>

                    @foreach ($kelasList as $kelas)
                    <option value="{{ $kelas->id }}">
                        {{ $kelas->nama_kelas }}
                        — {{ $kelas->tahunAjaran?->nama ?? 'Tahun ajaran tidak tersedia' }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div wire:loading wire:target="kelasId" class="flex items-center gap-2 text-sm text-brand-600">
                <i class="fa-solid fa-circle-notch fa-spin"></i> Memuat...
            </div>
        </div>

        @if ($kelasList->isEmpty())
        <div class="mt-4 flex items-start gap-2.5 rounded-xl border border-amber-200 bg-amber-50 p-3.5 text-xs text-amber-800 sm:text-sm">
            <i class="fa-solid fa-triangle-exclamation mt-0.5 text-amber-500"></i>
            <p>
                Belum ada kelas untuk sekolah ini. Buat kelas terlebih dahulu pada menu <strong>Manajemen Kelas</strong>.
            </p>
        </div>
        @endif
    </div>

    @if ($kelasAktif)
    {{-- ================= RINGKASAN KELAS ================= --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="flex items-center gap-4 rounded-2xl border border-brand-100 bg-white p-5 shadow-sm">
            <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-brand-100 text-lg text-brand-600">
                <i class="fa-solid fa-door-open"></i>
            </span>
            <div class="min-w-0">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Kelas Terpilih</p>
                <p class="mt-0.5 truncate text-xl font-extrabold text-slate-800">
                    {{ $kelasAktif->nama_kelas }}
                </p>
            </div>
        </div>

        <div class="flex items-center gap-4 rounded-2xl border border-brand-100 bg-white p-5 shadow-sm">
            <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-sky-100 text-lg text-sky-600">
                <i class="fa-solid fa-calendar-days"></i>
            </span>
            <div class="min-w-0">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Tahun Ajaran</p>
                <p class="mt-0.5 truncate text-xl font-extrabold text-slate-800">
                    {{ $kelasAktif->tahunAjaran?->nama ?? '-' }}
                </p>
            </div>
        </div>

        <div class="flex items-center gap-4 rounded-2xl border border-brand-100 bg-white p-5 shadow-sm">
            <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-brand-100 text-lg text-brand-600">
                <i class="fa-solid fa-users"></i>
            </span>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Jumlah Siswa</p>
                <p class="mt-0.5 text-2xl font-extrabold text-brand-600">
                    {{ $siswas->total() }}
                </p>
            </div>
        </div>
    </div>

    {{-- ================= FILTER & AKSI MASSAL ================= --}}
    <div class="rounded-2xl border border-brand-100 bg-white p-4 shadow-sm sm:p-5">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="relative flex-1 sm:max-w-md">
                <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-sm text-brand-400"></i>

                <input type="search" wire:model.live.debounce.300ms="search"
                    placeholder="Cari nama, NIS, atau NISN..."
                    class="w-full rounded-xl border border-brand-100 bg-brand-50/50 py-3 pl-11 pr-11 text-sm outline-none transition
                               placeholder:text-slate-400 focus:border-brand-300 focus:bg-white focus:ring-2 focus:ring-brand-200">

                <i wire:loading wire:target="search"
                    class="fa-solid fa-circle-notch fa-spin absolute right-4 top-1/2 -translate-y-1/2 text-sm text-brand-500"></i>
            </div>

            <button type="button"
                wire:click="confirmBulkRemove"
                @disabled($jumlahTerpilih===0)
                class="inline-flex items-center justify-center gap-2 rounded-xl border border-red-200 bg-red-50/50 px-4 py-2.5 text-xs font-semibold text-red-600 transition
                           hover:bg-red-100 disabled:cursor-not-allowed disabled:border-slate-200 disabled:bg-slate-50 disabled:text-slate-400 sm:text-sm">
                <i class="fa-solid fa-user-minus text-xs"></i>
                Hapus Penempatan Terpilih ({{ $jumlahTerpilih }})
            </button>
        </div>
    </div>

    {{-- ================= TABEL SISWA DI KELAS ================= --}}
    <div class="overflow-hidden rounded-2xl border border-brand-100 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[720px] text-left text-sm">
                <thead class="bg-brand-50 text-xs uppercase tracking-wide text-brand-700">
                    <tr>
                        <th class="w-11 px-4 py-3.5">
                            <input type="checkbox" wire:model.live="selectAll"
                                class="h-4 w-4 rounded border-brand-300 text-brand-600 focus:ring-brand-200"
                                aria-label="Pilih semua siswa">
                        </th>
                        <th class="px-4 py-3.5">No</th>
                        <th class="px-4 py-3.5">Nama Siswa</th>
                        <th class="px-4 py-3.5">NIS</th>
                        <th class="px-4 py-3.5">NISN</th>
                        <th class="px-4 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-brand-50">
                    @forelse ($siswas as $index => $siswa)
                    <tr wire:key="penempatan-{{ $siswa->id }}" class="transition hover:bg-brand-50/40">
                        <td class="px-4 py-4">
                            <input type="checkbox"
                                wire:model.live="selectedPenempatan"
                                value="{{ $siswa->id }}"
                                class="h-4 w-4 rounded border-brand-300 text-brand-600 focus:ring-brand-200"
                                aria-label="Pilih {{ $siswa->nama_lengkap }}">
                        </td>

                        <td class="whitespace-nowrap px-4 py-4 text-slate-400">
                            {{ $siswas->firstItem() + $index }}
                        </td>

                        <td class="px-4 py-4">
                            <div class="flex items-center gap-3">
                                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-brand-100 text-sm font-extrabold text-brand-700">
                                    {{ strtoupper(mb_substr($siswa->nama_lengkap, 0, 1)) }}
                                </span>
                                <span class="font-semibold text-slate-800">
                                    {{ $siswa->nama_lengkap }}
                                </span>
                            </div>
                        </td>

                        <td class="whitespace-nowrap px-4 py-4 font-mono text-xs text-slate-600">
                            {{ $siswa->nis ?: '-' }}
                        </td>

                        <td class="whitespace-nowrap px-4 py-4 font-mono text-xs text-slate-600">
                            {{ $siswa->nisn ?: '-' }}
                        </td>

                        <td class="whitespace-nowrap px-4 py-4 text-right">
                            <button type="button" wire:click="confirmRemove({{ $siswa->id }})"
                                class="inline-flex items-center gap-1.5 rounded-xl border border-red-200 bg-white px-3 py-1.5 text-xs font-semibold text-red-600 transition hover:bg-red-50">
                                <i class="fa-solid fa-user-xmark text-[11px]"></i>
                                Keluarkan
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center">
                            <span class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-brand-100 text-2xl text-brand-500">
                                <i class="fa-solid fa-users-slash"></i>
                            </span>
                            <p class="mt-4 font-bold text-brand-900">Belum ada siswa di kelas ini</p>
                            <p class="mt-1 text-sm text-slate-500">Klik tombol "Tempatkan Siswa" untuk menambahkan siswa ke kelas ini.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>{{ $siswas->links() }}</div>
    @else
    {{-- ================= EMPTY STATE BELUM PILIH KELAS ================= --}}
    <div class="rounded-2xl border border-dashed border-brand-200 bg-white/70 px-6 py-14 text-center">
        <span class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-brand-100 text-3xl text-brand-500">
            <i class="fa-solid fa-chalkboard"></i>
        </span>
        <p class="mt-4 font-semibold text-brand-900">Pilih kelas terlebih dahulu</p>
        <p class="mt-1 text-sm text-slate-500">
            Daftar siswa dan penempatannya akan ditampilkan setelah kelas dipilih.
        </p>
    </div>
    @endif

    {{-- ================= MODAL TEMPATKAN SISWA ================= --}}
    @if ($showModal)
    <div class="fixed inset-0 z-[60] flex items-end justify-center sm:items-center sm:p-4"
        x-data="{ show: false }" x-init="$nextTick(() => show = true)"
        x-on:keydown.escape.window="$wire.set('showModal', false)"
        role="dialog" aria-modal="true">

        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity duration-200"
            :class="show ? 'opacity-100' : 'opacity-0'"
            wire:click="$set('showModal', false)"></div>

        <div class="relative z-10 flex max-h-[92vh] w-full max-w-2xl flex-col overflow-hidden rounded-t-3xl bg-white shadow-2xl transition duration-200 sm:rounded-3xl"
            :class="show ? 'translate-y-0 opacity-100' : 'translate-y-4 opacity-0'">

            <form wire:submit="savePlacement" class="flex min-h-0 flex-1 flex-col overflow-hidden">
                {{-- Header Modal --}}
                <div class="flex shrink-0 items-center justify-between gap-3 bg-gradient-to-br from-brand-500 to-brand-600 px-6 py-5 text-white">
                    <div class="flex min-w-0 items-center gap-3.5">
                        <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-white text-lg font-extrabold text-brand-700 shadow-md">
                            <i class="fa-solid fa-user-plus"></i>
                        </span>
                        <div class="min-w-0">
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-brand-100">
                                Pembagian Rombel
                            </p>
                            <h2 class="truncate text-lg font-extrabold sm:text-xl">
                                Tempatkan Siswa
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
                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-brand-700">
                            Kelas Tujuan <span class="text-red-500">*</span>
                        </label>

                        <select wire:model.live="kelasTujuanId"
                            class="w-full rounded-xl border border-brand-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition
                                   focus:border-brand-400 focus:ring-2 focus:ring-brand-200">
                            <option value="">Pilih kelas tujuan</option>
                            @foreach ($kelasList as $kelas)
                            <option value="{{ $kelas->id }}">
                                {{ $kelas->nama_kelas }}
                                — {{ $kelas->tahunAjaran?->nama ?? '-' }}
                            </option>
                            @endforeach
                        </select>

                        @error('kelasTujuanId')
                        <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </p>
                        @enderror
                    </div>

                    <div>
                        <div class="mb-2 flex items-center justify-between gap-3">
                            <label class="text-xs font-bold uppercase tracking-wider text-brand-700">
                                Pilih Siswa <span class="text-red-500">*</span>
                            </label>
                            <span class="rounded-full bg-brand-100 px-2.5 py-0.5 text-xs font-bold text-brand-700">
                                Dipilih: {{ $jumlahCalonDipilih }}
                            </span>
                        </div>

                        <div class="max-h-72 overflow-y-auto divide-y divide-brand-50 rounded-2xl border border-brand-200 bg-white">
                            @forelse ($calonSiswa as $calon)
                            <label wire:key="calon-{{ $calon->id }}"
                                class="flex cursor-pointer items-center gap-3 p-3.5 transition hover:bg-brand-50/50 has-[:checked]:bg-brand-50/80">
                                <input type="checkbox"
                                    wire:model.live="selectedSiswa"
                                    value="{{ $calon->id }}"
                                    class="h-4 w-4 rounded border-brand-300 text-brand-600 focus:ring-brand-200">

                                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-brand-100 text-xs font-extrabold text-brand-700">
                                    {{ strtoupper(mb_substr($calon->nama_lengkap, 0, 1)) }}
                                </span>

                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-semibold text-slate-800">
                                        {{ $calon->nama_lengkap }}
                                    </p>
                                    <p class="font-mono text-xs text-slate-500">
                                        NIS: {{ $calon->nis ?: '-' }}
                                        <span class="mx-1 text-slate-300">·</span>
                                        NISN: {{ $calon->nisn ?: '-' }}
                                    </p>
                                </div>
                            </label>
                            @empty
                            <div class="px-4 py-10 text-center">
                                <span class="mx-auto grid h-12 w-12 place-items-center rounded-xl bg-brand-50 text-lg text-brand-400">
                                    <i class="fa-solid fa-user-slash"></i>
                                </span>
                                <p class="mt-2.5 text-sm font-semibold text-slate-700">Tidak ada siswa tersedia</p>
                                <p class="mt-0.5 text-xs text-slate-400">Tidak ada siswa aktif yang tersedia untuk ditampilkan.</p>
                            </div>
                            @endforelse
                        </div>

                        <p class="mt-2 text-xs text-slate-400">
                            Maksimal 200 siswa ditampilkan. Siswa yang sudah berada di kelas tujuan tidak ditampilkan.
                        </p>

                        @error('selectedSiswa')
                        <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </p>
                        @enderror
                        @error('selectedSiswa.*')
                        <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </p>
                        @enderror
                    </div>

                    <div class="flex items-start gap-2.5 rounded-xl border border-sky-200 bg-sky-50 p-3.5 text-xs text-sky-800">
                        <i class="fa-solid fa-circle-info mt-0.5 text-sky-500"></i>
                        <p>
                            Jika siswa sudah berada di kelas lain pada tahun ajaran yang sama, penempatannya akan otomatis dipindahkan ke kelas tujuan.
                        </p>
                    </div>
                </div>

                {{-- Footer Modal --}}
                <div class="flex shrink-0 flex-col-reverse gap-2 border-t border-brand-100 bg-white px-6 py-4 sm:flex-row sm:justify-end">
                    <button type="button" wire:click="$set('showModal', false)"
                        class="rounded-xl border border-brand-200 px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-brand-50">
                        Batal
                    </button>

                    <button type="submit" wire:loading.attr="disabled" wire:target="savePlacement"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-brand-500/20 transition hover:bg-brand-700 disabled:cursor-not-allowed disabled:opacity-60">
                        <span wire:loading.remove wire:target="savePlacement" class="inline-flex items-center gap-2">
                            <i class="fa-solid fa-floppy-disk text-xs"></i>
                            Simpan Penempatan
                        </span>
                        <span wire:loading wire:target="savePlacement" class="inline-flex items-center gap-2">
                            <i class="fa-solid fa-circle-notch fa-spin text-xs"></i>
                            Menyimpan...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- ================= MODAL KONFIRMASI KELUARKAN SATU SISWA ================= --}}
    @if ($showRemoveModal)
    <div class="fixed inset-0 z-[60] flex items-end justify-center sm:items-center sm:p-4"
        x-data="{ show: false }" x-init="$nextTick(() => show = true)"
        x-on:keydown.escape.window="$wire.set('showRemoveModal', false)"
        role="dialog" aria-modal="true">

        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity duration-200"
            :class="show ? 'opacity-100' : 'opacity-0'"
            wire:click="$set('showRemoveModal', false)"></div>

        <div class="relative z-10 flex w-full max-w-md flex-col overflow-hidden rounded-t-3xl bg-white shadow-2xl transition duration-200 sm:rounded-3xl"
            :class="show ? 'translate-y-0 opacity-100' : 'translate-y-4 opacity-0'">

            <div class="flex items-center justify-between gap-3 bg-gradient-to-br from-red-500 to-red-600 px-6 py-5 text-white">
                <div class="flex items-center gap-3.5">
                    <span class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-white text-lg font-extrabold text-red-600 shadow-md">
                        <i class="fa-solid fa-user-xmark"></i>
                    </span>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-white/80">Konfirmasi Penempatan</p>
                        <h2 class="text-lg font-extrabold">Keluarkan Siswa?</h2>
                    </div>
                </div>

                <button type="button" wire:click="$set('showRemoveModal', false)" aria-label="Tutup"
                    class="grid h-9 w-9 place-items-center rounded-lg text-white/80 transition hover:bg-white/15 hover:text-white">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="space-y-3 p-6">
                <p class="text-sm leading-relaxed text-slate-600">
                    Penempatan siswa pada kelas ini akan dihapus.
                </p>
                <div class="flex items-start gap-2.5 rounded-xl border border-brand-100 bg-brand-50/60 p-3 text-xs text-slate-600">
                    <i class="fa-solid fa-circle-info mt-0.5 text-brand-500"></i>
                    <p>Data profil dan riwayat akademik siswa tetap aman dan tidak ikut terhapus.</p>
                </div>
            </div>

            <div class="flex flex-col-reverse gap-2 border-t border-slate-100 bg-white px-6 py-4 sm:flex-row sm:justify-end">
                <button type="button" wire:click="$set('showRemoveModal', false)"
                    class="rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                    Batal
                </button>
                <button type="button" wire:click="removePlacement" wire:loading.attr="disabled" wire:target="removePlacement"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-red-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-red-500/20 transition hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-60">
                    <span wire:loading.remove wire:target="removePlacement" class="inline-flex items-center gap-2">
                        <i class="fa-solid fa-user-xmark text-xs"></i>
                        Ya, Keluarkan
                    </span>
                    <span wire:loading wire:target="removePlacement" class="inline-flex items-center gap-2">
                        <i class="fa-solid fa-circle-notch fa-spin text-xs"></i>
                        Memproses...
                    </span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ================= MODAL KONFIRMASI HAPUS BANYAK PENEMPATAN ================= --}}
    @if ($showBulkRemoveModal)
    <div class="fixed inset-0 z-[60] flex items-end justify-center sm:items-center sm:p-4"
        x-data="{ show: false }" x-init="$nextTick(() => show = true)"
        x-on:keydown.escape.window="$wire.set('showBulkRemoveModal', false)"
        role="dialog" aria-modal="true">

        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity duration-200"
            :class="show ? 'opacity-100' : 'opacity-0'"
            wire:click="$set('showBulkRemoveModal', false)"></div>

        <div class="relative z-10 flex w-full max-w-md flex-col overflow-hidden rounded-t-3xl bg-white shadow-2xl transition duration-200 sm:rounded-3xl"
            :class="show ? 'translate-y-0 opacity-100' : 'translate-y-4 opacity-0'">

            <div class="flex items-center justify-between gap-3 bg-gradient-to-br from-red-500 to-red-600 px-6 py-5 text-white">
                <div class="flex items-center gap-3.5">
                    <span class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-white text-lg font-extrabold text-red-600 shadow-md">
                        <i class="fa-solid fa-users-slash"></i>
                    </span>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-white/80">Hapus Penempatan Massal</p>
                        <h2 class="text-lg font-extrabold">Hapus Penempatan Terpilih?</h2>
                    </div>
                </div>

                <button type="button" wire:click="$set('showBulkRemoveModal', false)" aria-label="Tutup"
                    class="grid h-9 w-9 place-items-center rounded-lg text-white/80 transition hover:bg-white/15 hover:text-white">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="space-y-3 p-6">
                <p class="text-sm leading-relaxed text-slate-600">
                    Penempatan <span class="font-bold text-red-600">{{ $jumlahTerpilih }} siswa</span> pada kelas ini akan dihapus.
                </p>
                <div class="flex items-start gap-2.5 rounded-xl border border-brand-100 bg-brand-50/60 p-3 text-xs text-slate-600">
                    <i class="fa-solid fa-circle-info mt-0.5 text-brand-500"></i>
                    <p>Data profil dan riwayat akademik seluruh siswa tetap tersimpan.</p>
                </div>
            </div>

            <div class="flex flex-col-reverse gap-2 border-t border-slate-100 bg-white px-6 py-4 sm:flex-row sm:justify-end">
                <button type="button" wire:click="$set('showBulkRemoveModal', false)"
                    class="rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                    Batal
                </button>
                <button type="button" wire:click="removeSelected" wire:loading.attr="disabled" wire:target="removeSelected"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-red-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-red-500/20 transition hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-60">
                    <span wire:loading.remove wire:target="removeSelected" class="inline-flex items-center gap-2">
                        <i class="fa-solid fa-trash-can text-xs"></i>
                        Hapus Penempatan ({{ $jumlahTerpilih }})
                    </span>
                    <span wire:loading wire:target="removeSelected" class="inline-flex items-center gap-2">
                        <i class="fa-solid fa-circle-notch fa-spin text-xs"></i>
                        Menghapus...
                    </span>
                </button>
            </div>
        </div>
    </div>
    @endif

</div>