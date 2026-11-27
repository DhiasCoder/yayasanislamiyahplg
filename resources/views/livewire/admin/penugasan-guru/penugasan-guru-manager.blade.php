@php
// Kelompokkan item di halaman aktif berdasarkan Guru agar tidak tampil berulang
$groupedByGuru = $groups->getCollection()->groupBy(fn ($g) => $g->guru_id ?? 0);
@endphp

{{-- Livewire: elemen root harus satu --}}
<div class="space-y-6"
    x-data="{
        deleteModalOpen: false,
        deleteGuruId: null,
        deleteMapelId: null,
        deleteTahunId: null,
        deleteSummary: '',
        openDelete(guruId, mapelId, tahunId, summary) {
            this.deleteGuruId = guruId;
            this.deleteMapelId = mapelId;
            this.deleteTahunId = tahunId;
            this.deleteSummary = summary;
            this.deleteModalOpen = true;
        },
        closeDelete() {
            this.deleteModalOpen = false;
            this.deleteGuruId = null;
            this.deleteMapelId = null;
            this.deleteTahunId = null;
            this.deleteSummary = '';
        }
    }">

    {{-- ================= HEADER ================= --}}
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-500 to-brand-600 p-6 text-white shadow-lg shadow-brand-500/20 sm:p-8">
        <div class="pointer-events-none absolute -right-12 -top-12 h-48 w-48 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute -bottom-16 right-1/3 h-40 w-40 rounded-full bg-white/10"></div>

        <div class="relative flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-extrabold sm:text-3xl">Penugasan Guru</h1>
                <p class="mt-2 max-w-lg text-sm text-brand-50">
                    Atur distribusi guru pengampu mata pelajaran ke satu atau beberapa kelas sekaligus tanpa duplikasi data.
                </p>

                <span class="mt-4 inline-flex items-center gap-2 rounded-full bg-white/15 px-3.5 py-1.5 text-xs font-medium backdrop-blur">
                    <i class="fa-solid fa-chalkboard-user"></i>
                    {{ $totalPenugasan }} penugasan kelas terdaftar
                </span>
            </div>

            <button type="button"
                wire:click="create"
                wire:loading.attr="disabled"
                wire:target="create"
                class="inline-flex shrink-0 items-center justify-center gap-2 self-start rounded-2xl bg-white px-5 py-3 text-sm font-bold text-brand-700 shadow-md transition hover:bg-brand-50 active:scale-[0.99] disabled:opacity-70 sm:self-auto">
                <i wire:loading.remove wire:target="create" class="fa-solid fa-plus text-xs"></i>
                <i wire:loading wire:target="create" class="fa-solid fa-circle-notch fa-spin text-xs"></i>
                Tambah Penugasan
            </button>
        </div>
    </section>

    {{-- ================= NOTIFIKASI ================= --}}
    @if (session()->has('success'))
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4500)" x-show="show"
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
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5500)" x-show="show"
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

    {{-- ================= STATISTIK & PENCARIAN SEJAJAR ================= --}}
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-12">
        <div class="flex items-center gap-4 rounded-2xl border border-brand-100 bg-white p-4 shadow-sm sm:p-5 lg:col-span-3">
            <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-brand-100 text-lg text-brand-600">
                <i class="fa-solid fa-clipboard-list"></i>
            </span>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Total Kelas</p>
                <p class="mt-0.5 text-2xl font-extrabold text-slate-800">{{ $totalPenugasan }}</p>
            </div>
        </div>

        <div class="flex items-center gap-4 rounded-2xl border border-brand-100 bg-white p-4 shadow-sm sm:p-5 lg:col-span-3">
            <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-brand-100 text-lg text-brand-600">
                <i class="fa-solid fa-circle-check"></i>
            </span>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Aktif</p>
                <p class="mt-0.5 text-2xl font-extrabold text-brand-600">{{ $penugasanAktif }}</p>
            </div>
        </div>

        <div class="flex items-center rounded-2xl border border-brand-100 bg-white p-4 shadow-sm sm:p-5 lg:col-span-6">
            <div class="relative w-full">
                <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-sm text-brand-400"></i>

                <input type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Cari nama guru, mata pelajaran, kelas, atau tahun ajaran..."
                    class="w-full rounded-xl border border-brand-100 bg-brand-50/50 py-3 pl-11 pr-11 text-sm text-slate-700 outline-none transition
                           placeholder:text-slate-400 focus:border-brand-300 focus:bg-white focus:ring-2 focus:ring-brand-200">

                @if (trim($search) !== '')
                <button type="button"
                    wire:click="$set('search', '')"
                    wire:loading.remove
                    wire:target="search"
                    aria-label="Hapus pencarian"
                    class="absolute right-3.5 top-1/2 grid h-6 w-6 -translate-y-1/2 place-items-center rounded-full text-slate-400 transition hover:bg-slate-200/70 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>
                @endif

                <i wire:loading wire:target="search"
                    class="fa-solid fa-circle-notch fa-spin absolute right-4 top-1/2 -translate-y-1/2 text-sm text-brand-500"></i>
            </div>
        </div>
    </div>

    {{-- ================= DAFTAR PENUGASAN DIKELOMPOKKAN PER GURU ================= --}}
    <div class="space-y-4">
        @forelse ($groupedByGuru as $guruId => $mapelGroups)
        @php
        $guruObj = $mapelGroups->first()?->guru;
        $namaGuru = $guruObj?->nama_lengkap ?? 'Guru Tidak Diketahui';
        $totalMapel = $mapelGroups->count();
        $totalKelas = $mapelGroups->sum(fn ($g) => $g->kelasItems->count());
        @endphp

        <section wire:key="guru-card-{{ $guruId }}"
            class="overflow-hidden rounded-3xl border border-brand-100 bg-white shadow-sm transition hover:border-brand-200">

            {{-- Header Kartu Guru --}}
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-brand-100/80 bg-gradient-to-r from-brand-50/80 via-brand-50/30 to-white px-5 py-4 sm:px-6">
                <div class="flex items-center gap-3.5">
                    <span class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-brand-600 text-base font-extrabold text-white shadow-sm shadow-brand-500/20">
                        {{ strtoupper(mb_substr($namaGuru, 0, 1)) }}
                    </span>
                    <div>
                        <h2 class="text-base font-extrabold text-slate-800 sm:text-lg">
                            {{ $namaGuru }}
                        </h2>
                        <p class="mt-0.5 text-xs text-slate-500">
                            Mengampu <strong class="text-brand-700">{{ $totalMapel }}</strong> mata pelajaran di <strong class="text-brand-700">{{ $totalKelas }}</strong> kelas
                        </p>
                    </div>
                </div>

                <span class="inline-flex items-center gap-1.5 rounded-full border border-brand-200 bg-white px-3 py-1 text-xs font-bold text-brand-700">
                    <i class="fa-solid fa-layer-group text-[11px] text-brand-500"></i>
                    {{ $totalKelas }} Kelas
                </span>
            </div>

            {{-- Daftar Mata Pelajaran & Kelas yang Diajar Guru Ini --}}
            <div class="divide-y divide-slate-100">
                @foreach ($mapelGroups as $group)
                @php
                $daftarKelasTeks = $group->kelasItems
                ->map(fn ($i) => $i->kelas?->nama_kelas ?? '-')
                ->implode(', ');

                $ringkasanHapus = $namaGuru
                . ' — ' . ($group->mataPelajaran?->nama ?? '-')
                . ' (Kelas: ' . $daftarKelasTeks . ')';

                $groupKey = "{$group->guru_id}-{$group->mata_pelajaran_id}-{$group->tahun_ajaran_id}";
                @endphp

                <div wire:key="group-row-{{ $groupKey }}"
                    class="flex flex-col justify-between gap-4 p-5 transition hover:bg-brand-50/25 lg:flex-row lg:items-center sm:px-6">

                    {{-- Kiri: Info Mapel + Tahun + Badge Kelas --}}
                    <div class="min-w-0 flex-1 space-y-3">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="grid h-8 w-8 shrink-0 place-items-center rounded-xl bg-brand-100 text-xs text-brand-700">
                                <i class="fa-solid fa-book-open"></i>
                            </span>

                            <h3 class="text-sm font-extrabold text-slate-800 sm:text-base">
                                {{ $group->mataPelajaran?->nama ?? '-' }}
                            </h3>

                            <span class="inline-flex items-center gap-1 rounded-lg bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-600">
                                <i class="fa-regular fa-calendar text-[11px] text-slate-400"></i>
                                {{ $group->tahunAjaran?->nama ?? '-' }}
                            </span>

                            @if ($group->is_active)
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-brand-100 px-2.5 py-0.5 text-[11px] font-bold text-brand-700">
                                <span class="h-1.5 w-1.5 rounded-full bg-brand-600"></span>
                                Aktif
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-0.5 text-[11px] font-semibold text-slate-500">
                                Tidak Aktif
                            </span>
                            @endif
                        </div>

                        {{-- Daftar Pill Kelas --}}
                        <div class="flex flex-wrap items-center gap-1.5 pl-0 sm:pl-10">
                            <span class="mr-1 text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                                Kelas:
                            </span>

                            @foreach ($group->kelasItems as $itemKelas)
                            <span @class([ 'inline-flex items-center gap-1 rounded-lg border px-2.5 py-1 text-xs font-bold' , 'border-brand-200 bg-brand-50 text-brand-700'=> $itemKelas->is_active,
                                'border-slate-200 bg-slate-100 text-slate-500' => ! $itemKelas->is_active,
                                ])>
                                <i class="fa-solid fa-door-open text-[10px] opacity-70"></i>
                                {{ $itemKelas->kelas?->nama_kelas ?? '-' }}
                            </span>
                            @endforeach

                            <button type="button"
                                wire:click="editGroup({{ $group->guru_id }}, {{ $group->mata_pelajaran_id }}, {{ $group->tahun_ajaran_id }})"
                                wire:loading.attr="disabled"
                                wire:target="editGroup({{ $group->guru_id }}, {{ $group->mata_pelajaran_id }}, {{ $group->tahun_ajaran_id }})"
                                class="inline-flex items-center gap-1 rounded-lg border border-dashed border-brand-300 bg-white px-2.5 py-1 text-xs font-semibold text-brand-600 transition hover:border-brand-400 hover:bg-brand-50 disabled:opacity-60">
                                <i wire:loading.remove wire:target="editGroup({{ $group->guru_id }}, {{ $group->mata_pelajaran_id }}, {{ $group->tahun_ajaran_id }})" class="fa-solid fa-plus text-[10px]"></i>
                                <i wire:loading wire:target="editGroup({{ $group->guru_id }}, {{ $group->mata_pelajaran_id }}, {{ $group->tahun_ajaran_id }})" class="fa-solid fa-circle-notch fa-spin text-[10px]"></i>
                                Atur Kelas
                            </button>
                        </div>
                    </div>

                    {{-- Kanan: Tombol Aksi --}}
                    <div class="flex shrink-0 items-center gap-2 border-t border-slate-100 pt-3 lg:border-t-0 lg:pt-0">
                        <button type="button"
                            wire:click="editGroup({{ $group->guru_id }}, {{ $group->mata_pelajaran_id }}, {{ $group->tahun_ajaran_id }})"
                            wire:loading.attr="disabled"
                            wire:target="editGroup({{ $group->guru_id }}, {{ $group->mata_pelajaran_id }}, {{ $group->tahun_ajaran_id }})"
                            class="inline-flex flex-1 items-center justify-center gap-1.5 rounded-xl border border-brand-200 bg-white px-3.5 py-2 text-xs font-semibold text-brand-700 transition hover:bg-brand-50 disabled:opacity-60 lg:flex-initial">
                            <i wire:loading.remove wire:target="editGroup({{ $group->guru_id }}, {{ $group->mata_pelajaran_id }}, {{ $group->tahun_ajaran_id }})" class="fa-solid fa-sliders text-[11px]"></i>
                            <i wire:loading wire:target="editGroup({{ $group->guru_id }}, {{ $group->mata_pelajaran_id }}, {{ $group->tahun_ajaran_id }})" class="fa-solid fa-circle-notch fa-spin text-[11px]"></i>
                            Atur Kelas
                        </button>

                        <button type="button"
                            wire:click="toggleGroupStatus({{ $group->guru_id }}, {{ $group->mata_pelajaran_id }}, {{ $group->tahun_ajaran_id }}, {{ $group->is_active ? 'false' : 'true' }})"
                            wire:confirm="Ubah status penugasan ini?"
                            wire:loading.attr="disabled"
                            @class([ 'inline-flex flex-1 items-center justify-center gap-1.5 rounded-xl border px-3.5 py-2 text-xs font-semibold transition disabled:opacity-60 lg:flex-initial' , 'border-amber-200 bg-amber-50/70 text-amber-700 hover:bg-amber-100'=> $group->is_active,
                            'border-brand-200 bg-brand-50 text-brand-700 hover:bg-brand-100' => ! $group->is_active,
                            ])>
                            <i class="fa-solid {{ $group->is_active ? 'fa-ban' : 'fa-check' }} text-[11px]"></i>
                            {{ $group->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                        </button>

                        <button type="button"
                            @click="openDelete({{ $group->guru_id }}, {{ $group->mata_pelajaran_id }}, {{ $group->tahun_ajaran_id }}, @js($ringkasanHapus))"
                            title="Hapus penugasan mata pelajaran ini"
                            aria-label="Hapus penugasan"
                            class="inline-flex items-center justify-center rounded-xl border border-red-200 bg-white px-3 py-2 text-xs font-semibold text-red-600 transition hover:bg-red-50">
                            <i class="fa-solid fa-trash-can text-xs"></i>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </section>
        @empty
        <div class="rounded-3xl border border-dashed border-brand-200 bg-white px-6 py-16 text-center shadow-sm">
            <span class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-brand-100 text-2xl text-brand-500">
                <i class="fa-solid fa-chalkboard-user"></i>
            </span>
            <p class="mt-4 font-bold text-brand-900">
                {{ trim($search) !== '' ? 'Penugasan guru tidak ditemukan' : 'Belum ada data penugasan guru' }}
            </p>
            <p class="mt-1 text-sm text-slate-500">
                {{ trim($search) !== '' ? 'Coba gunakan kata kunci guru, mata pelajaran, atau kelas lain.' : 'Klik tombol "Tambah Penugasan" untuk menetapkan guru ke mata pelajaran dan kelas.' }}
            </p>
        </div>
        @endforelse
    </div>

    {{-- ================= PAGINASI ================= --}}
    <div>{{ $groups->links() }}</div>

    {{-- ================= MODAL TAMBAH / ATUR KELAS PENUGASAN ================= --}}
    @if ($showForm)
    @php
    $selectedGuruObj = $isEditMode ? $gurus->firstWhere('id', (int) $guru_id) : null;
    $selectedMapelObj = $isEditMode ? $mataPelajarans->firstWhere('id', (int) $mata_pelajaran_id) : null;
    $selectedTahunObj = $isEditMode ? $tahunAjarans->firstWhere('id', (int) $tahun_ajaran_id) : null;
    @endphp

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
                            <i class="fa-solid {{ $isEditMode ? 'fa-layer-group' : 'fa-chalkboard-user' }}"></i>
                        </span>
                        <div class="min-w-0">
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-brand-100">
                                {{ $isEditMode ? 'Distribusi Kelas Mengajar' : 'Penugasan Baru' }}
                            </p>
                            <h2 class="truncate text-lg font-extrabold sm:text-xl">
                                {{ $isEditMode ? 'Atur Kelas yang Diajar' : 'Tambah Penugasan Guru' }}
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
                    @if ($isEditMode)
                    {{-- Kartu Ringkasan Guru & Mapel saat Mode Atur Kelas --}}
                    <div class="rounded-2xl border border-brand-200 bg-brand-50/50 p-4">
                        <div class="flex items-center gap-3">
                            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-brand-600 text-sm font-extrabold text-white">
                                {{ strtoupper(mb_substr($selectedGuruObj?->nama_lengkap ?? 'G', 0, 1)) }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-extrabold text-slate-800">
                                    {{ $selectedGuruObj?->nama_lengkap ?? '-' }}
                                </p>
                                <p class="mt-0.5 flex flex-wrap items-center gap-1.5 text-xs text-slate-600">
                                    <span class="font-semibold text-brand-700">{{ $selectedMapelObj?->nama ?? '-' }}</span>
                                    <span class="text-slate-300">·</span>
                                    <span>{{ $selectedTahunObj?->nama ?? '-' }}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    @else
                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-brand-700">
                            Tahun Ajaran <span class="text-red-500">*</span>
                        </label>
                        <select wire:model.live="tahun_ajaran_id"
                            class="w-full rounded-xl border border-brand-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition
                                           focus:border-brand-400 focus:ring-2 focus:ring-brand-200">
                            <option value="">Pilih tahun ajaran</option>
                            @foreach ($tahunAjarans as $tahun)
                            <option value="{{ $tahun->id }}">
                                {{ $tahun->nama }}{{ $tahun->is_active ? ' (Aktif)' : '' }}
                            </option>
                            @endforeach
                        </select>
                        @error('tahun_ajaran_id')
                        <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-brand-700">
                                Guru <span class="text-red-500">*</span>
                            </label>
                            <select wire:model.live="guru_id"
                                class="w-full rounded-xl border border-brand-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition
                                               focus:border-brand-400 focus:ring-2 focus:ring-brand-200">
                                <option value="">Pilih guru</option>
                                @foreach ($gurus as $guru)
                                <option value="{{ $guru->id }}">{{ $guru->nama_lengkap }}</option>
                                @endforeach
                            </select>
                            @error('guru_id')
                            <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600">
                                <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                            </p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-brand-700">
                                Mata Pelajaran <span class="text-red-500">*</span>
                            </label>
                            <select wire:model.live="mata_pelajaran_id"
                                class="w-full rounded-xl border border-brand-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition
                                               focus:border-brand-400 focus:ring-2 focus:ring-brand-200">
                                <option value="">Pilih mata pelajaran</option>
                                @foreach ($mataPelajarans as $mapel)
                                <option value="{{ $mapel->id }}">{{ $mapel->nama }}</option>
                                @endforeach
                            </select>
                            @error('mata_pelajaran_id')
                            <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600">
                                <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                            </p>
                            @enderror
                        </div>
                    </div>
                    @endif

                    {{-- PILIHAN TOMBOL MULTI-KELAS --}}
                    <div>
                        <div class="mb-2 flex flex-wrap items-center justify-between gap-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-brand-700">
                                Pilih Kelas yang Diajar <span class="text-red-500">*</span>
                                <span class="ml-1 rounded-full bg-brand-100 px-2 py-0.5 text-[11px] font-extrabold text-brand-700">
                                    {{ count($selectedKelasIds) }} dipilih
                                </span>
                            </label>

                            <div class="flex items-center gap-2">
                                <span wire:loading wire:target="tahun_ajaran_id,guru_id,mata_pelajaran_id"
                                    class="inline-flex items-center gap-1 text-[11px] font-medium text-brand-600">
                                    <i class="fa-solid fa-circle-notch fa-spin"></i> Memuat...
                                </span>

                                @if ($tahun_ajaran_id && $kelasOptions->isNotEmpty())
                                <button type="button" wire:click="selectAllKelas"
                                    class="text-xs font-semibold text-brand-600 transition hover:text-brand-800">
                                    Pilih Semua
                                </button>
                                <span class="text-slate-300">|</span>
                                <button type="button" wire:click="clearKelasSelection"
                                    class="text-xs font-semibold text-slate-500 transition hover:text-slate-700">
                                    Reset
                                </button>
                                @endif
                            </div>
                        </div>

                        @if (! $tahun_ajaran_id)
                        <div class="rounded-2xl border border-dashed border-brand-200 bg-brand-50/40 px-4 py-6 text-center text-xs text-slate-500">
                            Pilih <strong>Tahun Ajaran</strong> terlebih dahulu untuk menampilkan tombol daftar kelas.
                        </div>
                        @elseif ($kelasOptions->isEmpty())
                        <div class="rounded-2xl border border-dashed border-amber-200 bg-amber-50/60 px-4 py-6 text-center text-xs text-amber-800">
                            Belum ada data kelas pada tahun ajaran ini.
                        </div>
                        @else
                        <div class="grid grid-cols-2 gap-2.5 sm:grid-cols-3">
                            @foreach ($kelasOptions as $kelas)
                            @php
                            $isSelected = in_array((int) $kelas->id, $selectedKelasIds, true);
                            $isLocked = in_array((int) $kelas->id, $lockedKelasIds, true);
                            @endphp

                            <button type="button"
                                wire:key="btn-kelas-{{ $kelas->id }}"
                                wire:click="toggleKelas({{ $kelas->id }})"
                                @disabled($isLocked)
                                title="{{ $isLocked ? 'Kelas ini sudah memiliki jadwal/materi/ujian sehingga tidak dapat dilepas' : 'Klik untuk memilih/membatalkan kelas ' . $kelas->nama_kelas }}"
                                @class([ 'flex items-center justify-between gap-2 rounded-xl border px-3.5 py-2.5 text-left text-xs font-bold transition' , 'border-brand-600 bg-brand-600 text-white shadow-sm shadow-brand-500/20'=> $isSelected && ! $isLocked,
                                'border-brand-300 bg-brand-100 text-brand-800 cursor-not-allowed' => $isLocked,
                                'border-brand-200 bg-white text-slate-700 hover:border-brand-400 hover:bg-brand-50' => ! $isSelected && ! $isLocked,
                                ])>
                                <span class="truncate">{{ $kelas->nama_kelas }}</span>

                                @if ($isLocked)
                                <i class="fa-solid fa-lock shrink-0 text-[11px] text-brand-700"></i>
                                @elseif ($isSelected)
                                <i class="fa-solid fa-circle-check shrink-0 text-sm text-white"></i>
                                @else
                                <span class="h-3.5 w-3.5 shrink-0 rounded-full border border-slate-300"></span>
                                @endif
                            </button>
                            @endforeach
                        </div>

                        @if (! empty($lockedKelasIds))
                        <p class="mt-2 flex items-center gap-1.5 text-[11px] text-slate-500">
                            <i class="fa-solid fa-lock text-brand-600"></i>
                            Kelas bertanda gembok sudah memiliki jadwal, materi, ujian, atau nilai sehingga tidak dapat dilepas.
                        </p>
                        @endif
                        @endif

                        @error('selectedKelasIds')
                        <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </p>
                        @enderror
                    </div>

                    <label class="flex cursor-pointer items-start gap-3 rounded-2xl border border-brand-200 bg-brand-50/40 p-4 transition hover:bg-brand-50/80 has-[:checked]:border-brand-400 has-[:checked]:bg-brand-50">
                        <input type="checkbox" wire:model="is_active"
                            class="mt-0.5 h-4 w-4 rounded border-brand-300 text-brand-600 focus:ring-brand-200">
                        <span>
                            <span class="block text-sm font-semibold text-slate-800">Penugasan aktif</span>
                            <span class="mt-0.5 block text-xs leading-relaxed text-slate-500">
                                Guru dapat menggunakan seluruh kelas terpilih ini untuk kegiatan pembelajaran dan penilaian.
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
                            Simpan Penugasan
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
                        <h2 class="text-lg font-extrabold">Hapus Penugasan Guru?</h2>
                    </div>
                </div>

                <button type="button" @click="closeDelete()" aria-label="Tutup"
                    class="grid h-9 w-9 place-items-center rounded-lg text-white/80 transition hover:bg-white/15 hover:text-white">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="space-y-3 p-6">
                <p class="text-sm leading-relaxed text-slate-600">
                    Apakah Anda yakin ingin menghapus penugasan berikut?
                </p>

                <div class="rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-xs font-bold text-slate-800"
                    x-text="deleteSummary"></div>

                <div class="flex items-start gap-2.5 rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs text-amber-800">
                    <i class="fa-solid fa-circle-info mt-0.5 text-amber-500"></i>
                    <p>Kelas yang sudah memiliki materi, ujian, tugas, nilai, atau jadwal akan otomatis dilewati dan tidak dihapus.</p>
                </div>
            </div>

            <div class="flex flex-col-reverse gap-2 border-t border-slate-100 bg-white px-6 py-4 sm:flex-row sm:justify-end">
                <button type="button" @click="closeDelete()"
                    class="rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                    Batal
                </button>

                <button type="button"
                    @click="$wire.deleteGroup(deleteGuruId, deleteMapelId, deleteTahunId).then(() => closeDelete())"
                    wire:loading.attr="disabled"
                    wire:target="deleteGroup"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-red-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-red-500/20 transition hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-60">
                    <span wire:loading.remove wire:target="deleteGroup" class="inline-flex items-center gap-2">
                        <i class="fa-solid fa-trash-can text-xs"></i>
                        Ya, Hapus
                    </span>
                    <span wire:loading wire:target="deleteGroup" class="inline-flex items-center gap-2">
                        <i class="fa-solid fa-circle-notch fa-spin text-xs"></i>
                        Menghapus...
                    </span>
                </button>
            </div>
        </div>
    </div>

</div>