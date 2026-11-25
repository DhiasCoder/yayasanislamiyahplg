@php
$gayaHari = [
'Senin' => 'bg-brand-100 text-brand-700 border-brand-200',
'Selasa' => 'bg-sky-100 text-sky-700 border-sky-200',
'Rabu' => 'bg-indigo-100 text-indigo-700 border-indigo-200',
'Kamis' => 'bg-amber-100 text-amber-700 border-amber-200',
'Jumat' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
'Sabtu' => 'bg-purple-100 text-purple-700 border-purple-200',
'Minggu' => 'bg-rose-100 text-rose-700 border-rose-200',
];

$badgeHari = fn ($h) => $gayaHari[$h] ?? 'bg-brand-100 text-brand-700 border-brand-200';
@endphp

{{-- Livewire: elemen root harus satu --}}
<div class="space-y-6">

    {{-- ================= HEADER ================= --}}
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-500 to-brand-600 p-6 text-white shadow-lg shadow-brand-500/20 sm:p-8">
        <div class="pointer-events-none absolute -right-12 -top-12 h-48 w-48 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute -bottom-16 right-1/3 h-40 w-40 rounded-full bg-white/10"></div>

        <div class="relative flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-extrabold sm:text-3xl">Jadwal Pelajaran</h1>
                <p class="mt-2 max-w-lg text-sm text-brand-50">
                    Atur waktu kegiatan belajar mengajar berdasarkan penugasan guru, kelas, dan mata pelajaran.
                </p>

                <span class="mt-4 inline-flex items-center gap-2 rounded-full bg-white/15 px-3.5 py-1.5 text-xs font-medium backdrop-blur">
                    <i class="fa-solid fa-clock"></i>
                    {{ $totalJadwal }} jadwal terdaftar
                </span>
            </div>

            <button type="button"
                wire:click="create"
                wire:loading.attr="disabled"
                wire:target="create"
                class="inline-flex shrink-0 items-center justify-center gap-2 self-start rounded-2xl bg-white px-5 py-3 text-sm font-bold text-brand-700 shadow-md transition hover:bg-brand-50 active:scale-[0.99] disabled:opacity-70 sm:self-auto">
                <i wire:loading.remove wire:target="create" class="fa-solid fa-plus text-xs"></i>
                <i wire:loading wire:target="create" class="fa-solid fa-circle-notch fa-spin text-xs"></i>
                Tambah Jadwal
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

    {{-- ================= STATISTIK & PENCARIAN ================= --}}
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-12">
        <div class="flex items-center gap-4 rounded-2xl border border-brand-100 bg-white p-4 shadow-sm sm:p-5 lg:col-span-4">
            <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-brand-100 text-lg text-brand-600">
                <i class="fa-solid fa-calendar-week"></i>
            </span>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Total Jadwal</p>
                <p class="mt-0.5 text-2xl font-extrabold text-slate-800">{{ $totalJadwal }}</p>
            </div>
        </div>

        <div class="flex items-center rounded-2xl border border-brand-100 bg-white p-4 shadow-sm sm:p-5 lg:col-span-8">
            <div class="relative w-full">
                <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-sm text-brand-400"></i>

                <input
                    type="search"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Cari hari, guru, mata pelajaran, atau kelas..."
                    class="w-full rounded-xl border border-brand-100 bg-brand-50/50 py-3 pl-11 pr-11 text-sm text-slate-700 outline-none transition
                           placeholder:text-slate-400 focus:border-brand-300 focus:bg-white focus:ring-2 focus:ring-brand-200">

                <i wire:loading wire:target="search"
                    class="fa-solid fa-circle-notch fa-spin absolute right-4 top-1/2 -translate-y-1/2 text-sm text-brand-500"></i>
            </div>
        </div>
    </div>

    {{-- ================= TABEL DESKTOP ================= --}}
    <div class="hidden overflow-hidden rounded-2xl border border-brand-100 bg-white shadow-sm md:block">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[960px] text-left text-sm">
                <thead class="bg-brand-50 text-xs uppercase tracking-wide text-brand-700">
                    <tr>
                        <th class="w-16 px-5 py-3.5">No</th>
                        <th class="px-5 py-3.5">Hari / Waktu</th>
                        <th class="px-5 py-3.5">Mata Pelajaran</th>
                        <th class="px-5 py-3.5">Guru</th>
                        <th class="px-5 py-3.5">Kelas</th>
                        <th class="px-5 py-3.5">Tahun Ajaran</th>
                        <th class="px-5 py-3.5 text-center">Status</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-brand-50">
                    @forelse ($jadwals as $jadwal)
                    @php
                    $pengajaran = $jadwal->pengajaran;
                    $jamMulai = substr((string) $jadwal->jam_mulai, 0, 5);
                    $jamSelesai = substr((string) $jadwal->jam_selesai, 0, 5);
                    @endphp

                    <tr wire:key="jadwal-{{ $jadwal->id }}" class="transition hover:bg-brand-50/40">
                        <td class="whitespace-nowrap px-5 py-4 text-slate-400">
                            {{ $jadwals->firstItem() + $loop->index }}
                        </td>

                        <td class="whitespace-nowrap px-5 py-4">
                            <div class="flex flex-col items-start gap-1.5">
                                <span class="inline-flex items-center gap-1.5 rounded-lg border px-2.5 py-0.5 text-xs font-bold {{ $badgeHari($jadwal->hari) }}">
                                    <i class="fa-solid fa-calendar-day text-[10px]"></i>
                                    {{ $jadwal->hari }}
                                </span>
                                <span class="inline-flex items-center gap-1.5 font-mono text-xs font-semibold text-slate-600">
                                    <i class="fa-regular fa-clock text-brand-500"></i>
                                    {{ $jamMulai }} – {{ $jamSelesai }}
                                </span>
                            </div>
                        </td>

                        <td class="whitespace-nowrap px-5 py-4">
                            <div class="flex items-center gap-2.5">
                                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-brand-100 text-xs text-brand-700">
                                    <i class="fa-solid fa-book-open"></i>
                                </span>
                                <span class="font-bold text-slate-800">
                                    {{ $pengajaran?->mataPelajaran?->nama ?? '-' }}
                                </span>
                            </div>
                        </td>

                        <td class="whitespace-nowrap px-5 py-4">
                            <div class="flex items-center gap-2 text-slate-700">
                                <i class="fa-solid fa-chalkboard-user text-xs text-brand-500"></i>
                                <span class="font-medium">{{ $pengajaran?->guru?->nama_lengkap ?? '-' }}</span>
                            </div>
                        </td>

                        <td class="whitespace-nowrap px-5 py-4">
                            <span class="inline-flex items-center gap-1.5 rounded-lg border border-brand-100 bg-brand-50/60 px-2.5 py-1 text-xs font-semibold text-brand-800">
                                <i class="fa-solid fa-door-open text-[10px] text-brand-500"></i>
                                {{ $pengajaran?->kelas?->nama_kelas ?? '-' }}
                            </span>
                        </td>

                        <td class="whitespace-nowrap px-5 py-4 text-xs font-medium text-slate-600">
                            {{ $pengajaran?->tahunAjaran?->nama ?? '-' }}
                        </td>

                        <td class="whitespace-nowrap px-5 py-4 text-center">
                            @if ($jadwal->is_active)
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-brand-100 px-3 py-1 text-xs font-semibold text-brand-700">
                                <span class="h-1.5 w-1.5 rounded-full bg-brand-600"></span>
                                Aktif
                            </span>
                            @else
                            <span class="inline-block rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                Tidak Aktif
                            </span>
                            @endif
                        </td>

                        <td class="whitespace-nowrap px-5 py-4 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <button type="button"
                                    wire:click="edit({{ $jadwal->id }})"
                                    wire:loading.attr="disabled"
                                    wire:target="edit({{ $jadwal->id }})"
                                    class="inline-flex items-center gap-1.5 rounded-xl border border-brand-200 bg-white px-3 py-1.5 text-xs font-semibold text-brand-700 transition hover:bg-brand-50 disabled:opacity-60">
                                    <i wire:loading.remove wire:target="edit({{ $jadwal->id }})" class="fa-solid fa-pen-to-square text-[11px]"></i>
                                    <i wire:loading wire:target="edit({{ $jadwal->id }})" class="fa-solid fa-circle-notch fa-spin text-[11px]"></i>
                                    Edit
                                </button>

                                <button type="button"
                                    wire:click="delete({{ $jadwal->id }})"
                                    wire:confirm="Yakin ingin menghapus jadwal ini?"
                                    wire:loading.attr="disabled"
                                    wire:target="delete({{ $jadwal->id }})"
                                    class="inline-flex items-center gap-1.5 rounded-xl border border-red-200 bg-white px-3 py-1.5 text-xs font-semibold text-red-600 transition hover:bg-red-50 disabled:opacity-60">
                                    <i wire:loading.remove wire:target="delete({{ $jadwal->id }})" class="fa-solid fa-trash-can text-[11px]"></i>
                                    <i wire:loading wire:target="delete({{ $jadwal->id }})" class="fa-solid fa-circle-notch fa-spin text-[11px]"></i>
                                    Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-16 text-center">
                            <span class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-brand-100 text-2xl text-brand-500">
                                <i class="fa-solid fa-calendar-xmark"></i>
                            </span>
                            <p class="mt-4 font-bold text-brand-900">
                                {{ filled($search ?? null) ? 'Jadwal pelajaran tidak ditemukan' : 'Belum ada jadwal pelajaran' }}
                            </p>
                            <p class="mt-1 text-sm text-slate-500">
                                {{ filled($search ?? null) ? 'Coba gunakan kata kunci hari, guru, mata pelajaran, atau kelas lain.' : 'Klik tombol "Tambah Jadwal" untuk mengatur jadwal pelajaran baru.' }}
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
        @forelse ($jadwals as $jadwal)
        @php
        $pengajaran = $jadwal->pengajaran;
        $jamMulai = substr((string) $jadwal->jam_mulai, 0, 5);
        $jamSelesai = substr((string) $jadwal->jam_selesai, 0, 5);
        @endphp

        <div wire:key="jadwal-mobile-{{ $jadwal->id }}"
            class="rounded-2xl border border-brand-100 bg-white p-4 shadow-sm transition hover:border-brand-200">
            <div class="flex items-start justify-between gap-2">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 rounded-lg border px-2.5 py-0.5 text-xs font-bold {{ $badgeHari($jadwal->hari) }}">
                        <i class="fa-solid fa-calendar-day text-[10px]"></i>
                        {{ $jadwal->hari }}
                    </span>

                    <span class="inline-flex items-center gap-1 font-mono text-xs font-semibold text-slate-600">
                        <i class="fa-regular fa-clock text-brand-500"></i>
                        {{ $jamMulai }} – {{ $jamSelesai }}
                    </span>
                </div>

                @if ($jadwal->is_active)
                <span class="inline-flex shrink-0 items-center gap-1 rounded-full bg-brand-100 px-2.5 py-0.5 text-[11px] font-semibold text-brand-700">
                    <span class="h-1.5 w-1.5 rounded-full bg-brand-600"></span>
                    Aktif
                </span>
                @else
                <span class="shrink-0 rounded-full bg-slate-100 px-2.5 py-0.5 text-[11px] font-semibold text-slate-600">
                    Tidak Aktif
                </span>
                @endif
            </div>

            <h3 class="mt-3 text-base font-extrabold text-slate-800">
                {{ $pengajaran?->mataPelajaran?->nama ?? '-' }}
            </h3>

            <div class="mt-2 space-y-1 text-xs text-slate-600">
                <p class="flex items-center gap-1.5">
                    <i class="fa-solid fa-chalkboard-user w-4 text-brand-500"></i>
                    <span class="font-medium">{{ $pengajaran?->guru?->nama_lengkap ?? '-' }}</span>
                </p>
                <p class="flex items-center gap-1.5">
                    <i class="fa-solid fa-door-open w-4 text-brand-500"></i>
                    <span>{{ $pengajaran?->kelas?->nama_kelas ?? '-' }}</span>
                    <span class="text-slate-300">·</span>
                    <span class="text-slate-500">{{ $pengajaran?->tahunAjaran?->nama ?? '-' }}</span>
                </p>
            </div>

            <div class="mt-4 flex gap-2 border-t border-brand-50 pt-3">
                <button type="button"
                    wire:click="edit({{ $jadwal->id }})"
                    wire:loading.attr="disabled"
                    wire:target="edit({{ $jadwal->id }})"
                    class="inline-flex flex-1 items-center justify-center gap-1.5 rounded-xl border border-brand-200 bg-brand-50/50 py-2 text-xs font-semibold text-brand-700 transition hover:bg-brand-100 disabled:opacity-60">
                    <i wire:loading.remove wire:target="edit({{ $jadwal->id }})" class="fa-solid fa-pen-to-square text-[11px]"></i>
                    <i wire:loading wire:target="edit({{ $jadwal->id }})" class="fa-solid fa-circle-notch fa-spin text-[11px]"></i>
                    Edit
                </button>

                <button type="button"
                    wire:click="delete({{ $jadwal->id }})"
                    wire:confirm="Yakin ingin menghapus jadwal ini?"
                    wire:loading.attr="disabled"
                    wire:target="delete({{ $jadwal->id }})"
                    class="inline-flex flex-1 items-center justify-center gap-1.5 rounded-xl border border-red-200 bg-red-50/50 py-2 text-xs font-semibold text-red-600 transition hover:bg-red-100 disabled:opacity-60">
                    <i wire:loading.remove wire:target="delete({{ $jadwal->id }})" class="fa-solid fa-trash-can text-[11px]"></i>
                    <i wire:loading wire:target="delete({{ $jadwal->id }})" class="fa-solid fa-circle-notch fa-spin text-[11px]"></i>
                    Hapus
                </button>
            </div>
        </div>
        @empty
        <div class="rounded-2xl border border-dashed border-brand-200 bg-white/70 px-6 py-14 text-center">
            <span class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-brand-100 text-2xl text-brand-500">
                <i class="fa-solid fa-calendar-xmark"></i>
            </span>
            <p class="mt-4 font-bold text-brand-900">
                {{ filled($search ?? null) ? 'Jadwal pelajaran tidak ditemukan' : 'Belum ada jadwal pelajaran' }}
            </p>
            <p class="mt-1 text-sm text-slate-500">
                {{ filled($search ?? null) ? 'Coba gunakan kata kunci hari, guru, mata pelajaran, atau kelas lain.' : 'Klik tombol "Tambah Jadwal" untuk mengatur jadwal pelajaran baru.' }}
            </p>
        </div>
        @endforelse
    </div>

    {{-- ================= PAGINASI ================= --}}
    <div>{{ $jadwals->links() }}</div>

    {{-- ================= MODAL TAMBAH / EDIT JADWAL ================= --}}
    @if ($showForm)
    <div class="fixed inset-0 z-[60] flex items-end justify-center sm:items-center sm:p-4"
        x-data="{ show: false }" x-init="$nextTick(() => show = true)"
        x-on:keydown.escape.window="$wire.resetForm()"
        role="dialog" aria-modal="true">

        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity duration-200"
            :class="show ? 'opacity-100' : 'opacity-0'"
            wire:click="resetForm"></div>

        <div class="relative z-10 flex max-h-[92vh] w-full max-w-xl flex-col overflow-hidden rounded-t-3xl bg-white shadow-2xl transition duration-200 sm:rounded-3xl"
            :class="show ? 'translate-y-0 opacity-100' : 'translate-y-4 opacity-0'"
            wire:click.stop>

            <form wire:submit="save" class="flex min-h-0 flex-1 flex-col overflow-hidden">
                {{-- Header Modal --}}
                <div class="flex shrink-0 items-center justify-between gap-3 bg-gradient-to-br from-brand-500 to-brand-600 px-6 py-5 text-white">
                    <div class="flex min-w-0 items-center gap-3.5">
                        <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-white text-lg font-extrabold text-brand-700 shadow-md">
                            <i class="fa-solid {{ $jadwalId ? 'fa-pen-to-square' : 'fa-calendar-plus' }}"></i>
                        </span>
                        <div class="min-w-0">
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-brand-100">
                                {{ $jadwalId ? 'Perbarui Waktu Belajar' : 'Waktu Belajar Baru' }}
                            </p>
                            <h2 class="truncate text-lg font-extrabold sm:text-xl">
                                {{ $jadwalId ? 'Edit Jadwal Pelajaran' : 'Tambah Jadwal Pelajaran' }}
                            </h2>
                        </div>
                    </div>

                    <button type="button" wire:click="resetForm" aria-label="Tutup"
                        class="grid h-9 w-9 shrink-0 place-items-center rounded-lg text-white/80 transition hover:bg-white/15 hover:text-white">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                {{-- Body Form --}}
                <div class="space-y-4 overflow-y-auto p-6">
                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-brand-700">
                            Penugasan Guru <span class="text-red-500">*</span>
                        </label>
                        <select wire:model="pengajaran_id"
                            class="w-full rounded-xl border border-brand-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition
                                       focus:border-brand-400 focus:ring-2 focus:ring-brand-200">
                            <option value="">Pilih penugasan guru</option>
                            @foreach ($pengajarans as $pengajaran)
                            <option value="{{ $pengajaran->id }}">
                                {{ $pengajaran->kelas?->nama_kelas ?? '-' }}
                                — {{ $pengajaran->mataPelajaran?->nama ?? '-' }}
                                — {{ $pengajaran->guru?->nama_lengkap ?? '-' }}
                                ({{ $pengajaran->tahunAjaran?->nama ?? '-' }})
                            </option>
                            @endforeach
                        </select>
                        @error('pengajaran_id')
                        <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-brand-700">
                            Hari <span class="text-red-500">*</span>
                        </label>
                        <select wire:model="hari"
                            class="w-full rounded-xl border border-brand-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition
                                       focus:border-brand-400 focus:ring-2 focus:ring-brand-200">
                            <option value="">Pilih hari</option>
                            @foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $hariPilihan)
                            <option value="{{ $hariPilihan }}">{{ $hariPilihan }}</option>
                            @endforeach
                        </select>
                        @error('hari')
                        <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-brand-700">
                                Jam Mulai <span class="text-red-500">*</span>
                            </label>
                            <input type="time" wire:model="jam_mulai"
                                class="w-full rounded-xl border border-brand-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition
                                           focus:border-brand-400 focus:ring-2 focus:ring-brand-200">
                            @error('jam_mulai')
                            <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600">
                                <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                            </p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-brand-700">
                                Jam Selesai <span class="text-red-500">*</span>
                            </label>
                            <input type="time" wire:model="jam_selesai"
                                class="w-full rounded-xl border border-brand-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition
                                           focus:border-brand-400 focus:ring-2 focus:ring-brand-200">
                            @error('jam_selesai')
                            <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600">
                                <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                            </p>
                            @enderror
                        </div>
                    </div>

                    <label class="flex cursor-pointer items-start gap-3 rounded-2xl border border-brand-200 bg-brand-50/40 p-4 transition hover:bg-brand-50/80 has-[:checked]:border-brand-400 has-[:checked]:bg-brand-50">
                        <input type="checkbox" wire:model="is_active"
                            class="mt-0.5 h-4 w-4 rounded border-brand-300 text-brand-600 focus:ring-brand-200">
                        <span>
                            <span class="block text-sm font-semibold text-slate-800">Jadwal aktif</span>
                            <span class="mt-0.5 block text-xs leading-relaxed text-slate-500">
                                Jadwal aktif akan ditampilkan sebagai jadwal pelajaran yang berlaku.
                            </span>
                        </span>
                    </label>
                </div>

                {{-- Footer Modal --}}
                <div class="flex shrink-0 flex-col-reverse gap-2 border-t border-brand-100 bg-white px-6 py-4 sm:flex-row sm:justify-end">
                    <button type="button" wire:click="resetForm"
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

</div>