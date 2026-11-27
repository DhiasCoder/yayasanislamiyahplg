@php
$gayaHari = [
'Senin' => ['badge' => 'bg-brand-100 text-brand-700 border-brand-200', 'dot' => 'bg-brand-500'],
'Selasa' => ['badge' => 'bg-sky-100 text-sky-700 border-sky-200', 'dot' => 'bg-sky-500'],
'Rabu' => ['badge' => 'bg-indigo-100 text-indigo-700 border-indigo-200', 'dot' => 'bg-indigo-500'],
'Kamis' => ['badge' => 'bg-amber-100 text-amber-700 border-amber-200', 'dot' => 'bg-amber-500'],
'Jumat' => ['badge' => 'bg-emerald-100 text-emerald-700 border-emerald-200', 'dot' => 'bg-emerald-500'],
'Sabtu' => ['badge' => 'bg-purple-100 text-purple-700 border-purple-200', 'dot' => 'bg-purple-500'],
'Minggu' => ['badge' => 'bg-rose-100 text-rose-700 border-rose-200', 'dot' => 'bg-rose-500'],
];

$styleHari = fn ($h) => $gayaHari[$h] ?? ['badge' => 'bg-brand-100 text-brand-700 border-brand-200', 'dot' => 'bg-brand-500'];
$hasActiveFilter = trim($search) !== '' || $filterKelas !== '' || $filterHari !== '';
@endphp

{{-- Livewire: elemen root harus satu --}}
<div class="space-y-6"
    x-data="{
        deleteModalOpen: false,
        deleteId: null,
        deleteSummary: '',
        openDelete(id, summary) {
            this.deleteId = id;
            this.deleteSummary = summary;
            this.deleteModalOpen = true;
        },
        closeDelete() {
            this.deleteModalOpen = false;
            this.deleteId = null;
            this.deleteSummary = '';
        }
    }">

    {{-- ================= HEADER ================= --}}
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-500 to-brand-600 p-6 text-white shadow-lg shadow-brand-500/20 sm:p-8">
        <div class="pointer-events-none absolute -right-12 -top-12 h-48 w-48 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute -bottom-16 right-1/3 h-40 w-40 rounded-full bg-white/10"></div>

        <div class="relative flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-extrabold sm:text-3xl">Jadwal Pelajaran</h1>
                <p class="mt-2 max-w-lg text-sm text-brand-50">
                    Kelola jadwal kegiatan belajar mengajar yang tersusun rapi per kelas dan hari.
                </p>

                <div class="mt-4 flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3.5 py-1.5 text-xs font-medium backdrop-blur">
                        <i class="fa-solid fa-clock"></i>
                        {{ $totalJadwal }} sesi terjadwal
                    </span>
                    <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3.5 py-1.5 text-xs font-medium backdrop-blur">
                        <i class="fa-solid fa-door-open"></i>
                        {{ $totalKelasTerjadwal }} kelas aktif
                    </span>
                </div>
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

    {{-- ================= STATISTIK RINGKAS ================= --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="flex items-center gap-4 rounded-2xl border border-brand-100 bg-white p-4 shadow-sm sm:p-5">
            <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-brand-100 text-lg text-brand-600">
                <i class="fa-solid fa-calendar-week"></i>
            </span>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Total Jadwal</p>
                <p class="mt-0.5 text-2xl font-extrabold text-slate-800">{{ $totalJadwal }}</p>
            </div>
        </div>

        <div class="flex items-center gap-4 rounded-2xl border border-brand-100 bg-white p-4 shadow-sm sm:p-5">
            <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-brand-100 text-lg text-brand-600">
                <i class="fa-solid fa-circle-check"></i>
            </span>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Jadwal Aktif</p>
                <p class="mt-0.5 text-2xl font-extrabold text-brand-600">{{ $jadwalAktif }}</p>
            </div>
        </div>

        <div class="flex items-center gap-4 rounded-2xl border border-brand-100 bg-white p-4 shadow-sm sm:p-5">
            <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-brand-100 text-lg text-brand-600">
                <i class="fa-solid fa-door-open"></i>
            </span>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Kelas Terjadwal</p>
                <p class="mt-0.5 text-2xl font-extrabold text-slate-800">{{ $totalKelasTerjadwal }}</p>
            </div>
        </div>
    </div>

    {{-- ================= FILTER & PENCARIAN ================= --}}
    <div class="rounded-2xl border border-brand-100 bg-white p-4 shadow-sm sm:p-5">
        <div class="grid grid-cols-1 gap-3 md:grid-cols-12">
            {{-- Pencarian --}}
            <div class="relative md:col-span-5">
                <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-sm text-brand-400"></i>
                <input
                    type="search"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Cari guru, mata pelajaran, atau kelas..."
                    class="w-full rounded-xl border border-brand-100 bg-brand-50/50 py-2.5 pl-11 pr-10 text-sm text-slate-700 outline-none transition
                           placeholder:text-slate-400 focus:border-brand-300 focus:bg-white focus:ring-2 focus:ring-brand-200">
                <i wire:loading wire:target="search"
                    class="fa-solid fa-circle-notch fa-spin absolute right-3.5 top-1/2 -translate-y-1/2 text-sm text-brand-500"></i>
            </div>

            {{-- Filter Kelas --}}
            <div class="md:col-span-3">
                <select wire:model.live="filterKelas"
                    class="w-full rounded-xl border border-brand-100 bg-brand-50/50 px-3.5 py-2.5 text-sm text-slate-700 outline-none transition
                           focus:border-brand-300 focus:bg-white focus:ring-2 focus:ring-brand-200">
                    <option value="">Semua Kelas</option>
                    @foreach ($kelasList as $kls)
                    <option value="{{ $kls->id }}">{{ $kls->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Hari --}}
            <div class="md:col-span-2">
                <select wire:model.live="filterHari"
                    class="w-full rounded-xl border border-brand-100 bg-brand-50/50 px-3.5 py-2.5 text-sm text-slate-700 outline-none transition
                           focus:border-brand-300 focus:bg-white focus:ring-2 focus:ring-brand-200">
                    <option value="">Semua Hari</option>
                    @foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $h)
                    <option value="{{ $h }}">{{ $h }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Reset Filter --}}
            <div class="flex items-center md:col-span-2">
                <button type="button"
                    wire:click="resetFilters"
                    @disabled(! $hasActiveFilter)
                    class="inline-flex w-full items-center justify-center gap-1.5 rounded-xl border border-brand-200 bg-white px-3.5 py-2.5 text-xs font-semibold text-slate-600 transition
                           hover:bg-brand-50 disabled:cursor-not-allowed disabled:border-slate-200 disabled:bg-slate-50 disabled:text-slate-400">
                    <i class="fa-solid fa-rotate-left text-[11px]"></i>
                    Reset Filter
                </button>
            </div>
        </div>
    </div>

    {{-- ================= DAFTAR JADWAL DIKELOMPOKKAN PER KELAS ================= --}}
    <div class="space-y-6">
        @forelse ($jadwalPerKelas as $kelasId => $kelompok)
        <section wire:key="kelas-group-{{ $kelasId }}"
            class="overflow-hidden rounded-3xl border border-brand-100 bg-white shadow-sm">

            {{-- Header Kartu Kelas --}}
            <div class="flex flex-col justify-between gap-3 border-b border-brand-100 bg-gradient-to-r from-brand-50/90 via-brand-50/40 to-white px-5 py-4 sm:flex-row sm:items-center sm:px-6">
                <div class="flex items-center gap-3.5">
                    <span class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-brand-600 text-base font-extrabold text-white shadow-sm shadow-brand-500/20">
                        <i class="fa-solid fa-door-open"></i>
                    </span>
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="text-lg font-extrabold text-slate-800">
                                {{ $kelompok['nama_kelas'] }}
                            </h2>
                            <span class="rounded-full border border-brand-200 bg-white px-2.5 py-0.5 text-[11px] font-bold text-brand-700">
                                {{ $kelompok['tahun_ajaran'] }}
                            </span>
                        </div>
                        <p class="mt-0.5 text-xs text-slate-500">
                            <strong class="text-brand-700">{{ $kelompok['aktif_sesi'] }}</strong> dari {{ $kelompok['total_sesi'] }} sesi pelajaran aktif
                        </p>
                    </div>
                </div>

                <button type="button"
                    wire:click="create"
                    class="inline-flex items-center justify-center gap-1.5 self-start rounded-xl border border-brand-200 bg-white px-3.5 py-2 text-xs font-bold text-brand-700 transition hover:bg-brand-50 sm:self-auto">
                    <i class="fa-solid fa-plus text-[10px]"></i>
                    Tambah Sesi
                </button>
            </div>

            {{-- Grid Hari di Dalam Kelas --}}
            <div class="grid grid-cols-1 gap-4 p-5 md:grid-cols-2 xl:grid-cols-3 sm:p-6">
                @foreach ($kelompok['hari_groups'] as $hari => $daftarSesi)
                @php $warnaHari = $styleHari($hari); @endphp

                <div wire:key="kelas-{{ $kelasId }}-hari-{{ $hari }}"
                    class="flex flex-col rounded-2xl border border-slate-200/80 bg-slate-50/60 p-4">

                    {{-- Sub-Header Hari --}}
                    <div class="mb-3 flex items-center justify-between border-b border-slate-200/70 pb-2.5">
                        <span class="inline-flex items-center gap-2 rounded-lg border px-2.5 py-1 text-xs font-extrabold {{ $warnaHari['badge'] }}">
                            <span class="h-2 w-2 rounded-full {{ $warnaHari['dot'] }}"></span>
                            {{ $hari }}
                        </span>

                        <span class="text-[11px] font-semibold text-slate-400">
                            {{ $daftarSesi->count() }} mapel
                        </span>
                    </div>

                    {{-- Daftar Slot Pelajaran pada Hari Tersebut --}}
                    <div class="space-y-2.5">
                        @foreach ($daftarSesi as $jadwal)
                        @php
                        $pengajaran = $jadwal->pengajaran;
                        $jamMulai = substr((string) $jadwal->jam_mulai, 0, 5);
                        $jamSelesai = substr((string) $jadwal->jam_selesai, 0, 5);
                        $ringkasan = ($kelompok['nama_kelas'])
                        . ' · ' . $jadwal->hari . " ({$jamMulai}–{$jamSelesai})"
                        . ' — ' . ($pengajaran?->mataPelajaran?->nama ?? '-');
                        @endphp

                        <div wire:key="jadwal-card-{{ $jadwal->id }}"
                            @class([ 'group rounded-xl border bg-white p-3.5 transition' , 'border-brand-100 shadow-xs hover:border-brand-300'=> $jadwal->is_active,
                            'border-slate-200 bg-slate-50/80 opacity-75' => ! $jadwal->is_active,
                            ])>

                            {{-- Baris Atas: Jam & Status --}}
                            <div class="flex items-center justify-between gap-2">
                                <span class="inline-flex items-center gap-1.5 rounded-md bg-brand-50 px-2 py-0.5 font-mono text-xs font-bold text-brand-700">
                                    <i class="fa-regular fa-clock text-[11px] text-brand-500"></i>
                                    {{ $jamMulai }} – {{ $jamSelesai }}
                                </span>

                                @if ($jadwal->is_active)
                                <span class="inline-flex items-center gap-1 rounded-full bg-brand-100 px-2.5 py-0.5 text-[11px] font-bold text-brand-700">
                                    <span class="h-1.5 w-1.5 rounded-full bg-brand-600"></span>
                                    Aktif
                                </span>
                                @else
                                <span class="inline-flex items-center gap-1 rounded-full bg-slate-200/80 px-2.5 py-0.5 text-[11px] font-semibold text-slate-600">
                                    Nonaktif
                                </span>
                                @endif
                            </div>

                            {{-- Mata Pelajaran & Guru --}}
                            <div class="mt-2.5">
                                <h3 class="text-sm font-extrabold leading-snug text-slate-800">
                                    {{ $pengajaran?->mataPelajaran?->nama ?? '-' }}
                                </h3>
                                <p class="mt-1 flex items-center gap-1.5 text-xs text-slate-500">
                                    <i class="fa-solid fa-chalkboard-user text-[11px] text-brand-500"></i>
                                    <span class="truncate font-medium text-slate-600">
                                        {{ $pengajaran?->guru?->nama_lengkap ?? '-' }}
                                    </span>
                                </p>
                            </div>

                            {{-- Baris Aksi Cepat --}}
                            <div class="mt-3 flex items-center justify-between gap-1.5 border-t border-slate-100 pt-2.5">
                                <button type="button"
                                    wire:click="toggleStatus({{ $jadwal->id }})"
                                    wire:loading.attr="disabled"
                                    wire:target="toggleStatus({{ $jadwal->id }})"
                                    @class([ 'inline-flex items-center gap-1 rounded-lg border px-2.5 py-1 text-[11px] font-semibold transition disabled:opacity-60' , 'border-amber-200 bg-amber-50/70 text-amber-700 hover:bg-amber-100'=> $jadwal->is_active,
                                    'border-brand-200 bg-brand-50 text-brand-700 hover:bg-brand-100' => ! $jadwal->is_active,
                                    ])>
                                    <i class="fa-solid {{ $jadwal->is_active ? 'fa-ban' : 'fa-check' }} text-[10px]"></i>
                                    {{ $jadwal->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>

                                <div class="flex items-center gap-1">
                                    <button type="button"
                                        wire:click="edit({{ $jadwal->id }})"
                                        wire:loading.attr="disabled"
                                        wire:target="edit({{ $jadwal->id }})"
                                        title="Edit jadwal"
                                        class="inline-flex items-center gap-1 rounded-lg border border-brand-200 bg-white px-2.5 py-1 text-[11px] font-semibold text-brand-700 transition hover:bg-brand-50 disabled:opacity-60">
                                        <i wire:loading.remove wire:target="edit({{ $jadwal->id }})" class="fa-solid fa-pen-to-square text-[10px]"></i>
                                        <i wire:loading wire:target="edit({{ $jadwal->id }})" class="fa-solid fa-circle-notch fa-spin text-[10px]"></i>
                                        Edit
                                    </button>

                                    <button type="button"
                                        @click="openDelete({{ $jadwal->id }}, @js($ringkasan))"
                                        title="Hapus jadwal"
                                        class="inline-flex items-center justify-center rounded-lg border border-red-200 bg-white px-2 py-1 text-[11px] font-semibold text-red-600 transition hover:bg-red-50">
                                        <i class="fa-solid fa-trash-can text-[10px]"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </section>
        @empty
        <div class="rounded-3xl border border-dashed border-brand-200 bg-white px-6 py-16 text-center shadow-sm">
            <span class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-brand-100 text-2xl text-brand-500">
                <i class="fa-solid fa-calendar-xmark"></i>
            </span>
            <p class="mt-4 font-bold text-brand-900">
                {{ $hasActiveFilter ? 'Jadwal pelajaran tidak ditemukan' : 'Belum ada jadwal pelajaran' }}
            </p>
            <p class="mt-1 text-sm text-slate-500">
                {{ $hasActiveFilter ? 'Coba ubah filter kelas, hari, atau kata kunci pencarian Anda.' : 'Klik tombol "Tambah Jadwal" untuk mulai menyusun jadwal pelajaran.' }}
            </p>
        </div>
        @endforelse
    </div>

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
                            <option value="">Pilih penugasan guru & kelas</option>
                            @foreach ($groupedPengajarans as $namaKelas => $itemsPengajaran)
                            <optgroup label="Kelas {{ $namaKelas }}">
                                @foreach ($itemsPengajaran as $pengajaran)
                                <option value="{{ $pengajaran->id }}">
                                    {{ $pengajaran->mataPelajaran?->nama ?? '-' }}
                                    — {{ $pengajaran->guru?->nama_lengkap ?? '-' }}
                                    ({{ $pengajaran->tahunAjaran?->nama ?? '-' }})
                                </option>
                                @endforeach
                            </optgroup>
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
                                Jadwal aktif akan ditampilkan sebagai jadwal pelajaran yang sedang berlaku.
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

    {{-- ================= MODAL KONFIRMASI HAPUS (INSTAN) ================= --}}
    <div x-show="deleteModalOpen"
        x-cloak
        x-on:keydown.escape.window="if (deleteModalOpen) closeDelete()"
        class="fixed inset-0 z-[60] flex items-end justify-center sm:items-center sm:p-4"
        role="dialog" aria-modal="true">

        <div x-show="deleteModalOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="closeDelete()"
            class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm"></div>

        <div x-show="deleteModalOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="translate-y-4 opacity-0 sm:scale-95"
            x-transition:enter-end="translate-y-0 opacity-100 sm:scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="translate-y-0 opacity-100 sm:scale-100"
            x-transition:leave-end="translate-y-4 opacity-0 sm:scale-95"
            @click.stop
            class="relative z-10 flex w-full max-w-md flex-col overflow-hidden rounded-t-3xl bg-white shadow-2xl sm:rounded-3xl">

            <div class="flex items-center justify-between gap-3 bg-gradient-to-br from-red-500 to-red-600 px-6 py-5 text-white">
                <div class="flex items-center gap-3.5">
                    <span class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-white text-lg font-extrabold text-red-600 shadow-md">
                        <i class="fa-solid fa-trash-can"></i>
                    </span>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-white/80">Konfirmasi Hapus</p>
                        <h2 class="text-lg font-extrabold">Hapus Jadwal Pelajaran?</h2>
                    </div>
                </div>

                <button type="button" @click="closeDelete()" aria-label="Tutup"
                    class="grid h-9 w-9 place-items-center rounded-lg text-white/80 transition hover:bg-white/15 hover:text-white">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="space-y-3 p-6">
                <p class="text-sm leading-relaxed text-slate-600">
                    Apakah Anda yakin ingin menghapus sesi jadwal pelajaran berikut?
                </p>

                <div class="rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-xs font-bold text-slate-800"
                    x-text="deleteSummary"></div>
            </div>

            <div class="flex flex-col-reverse gap-2 border-t border-slate-100 bg-white px-6 py-4 sm:flex-row sm:justify-end">
                <button type="button" @click="closeDelete()"
                    class="rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                    Batal
                </button>

                <button type="button"
                    @click="$wire.delete(deleteId).then(() => closeDelete())"
                    wire:loading.attr="disabled"
                    wire:target="delete"
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

</div>