@php
$jumlahTerpilih = count($selected ?? []);
@endphp

{{-- Livewire: elemen root harus satu --}}
<div class="space-y-6">

    {{-- ================= HEADER ================= --}}
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-500 to-brand-600 p-6 text-white shadow-lg shadow-brand-500/20 sm:p-8">
        <div class="pointer-events-none absolute -right-12 -top-12 h-48 w-48 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute -bottom-16 right-1/3 h-40 w-40 rounded-full bg-white/10"></div>

        <div class="relative flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-extrabold sm:text-3xl">Manajemen Siswa</h1>
                <p class="mt-2 max-w-lg text-sm text-brand-50">
                    Kelola data induk, identitas, informasi orang tua, dan status akademik siswa di sekolah Anda.
                </p>

                <span class="mt-4 inline-flex items-center gap-2 rounded-full bg-white/15 px-3.5 py-1.5 text-xs font-medium backdrop-blur">
                    <i class="fa-solid fa-user-graduate"></i>
                    {{ $siswas->total() }} siswa terdaftar
                </span>
            </div>

            <button type="button" wire:click="create"
                class="inline-flex shrink-0 items-center justify-center gap-2 self-start rounded-2xl bg-white px-5 py-3 text-sm font-bold text-brand-700 shadow-md transition hover:bg-brand-50 active:scale-[0.99] sm:self-auto">
                <i class="fa-solid fa-plus text-xs"></i>
                Tambah Siswa
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
                    placeholder="Cari nama, NIS, NISN, atau NIK..."
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
            <table class="w-full min-w-[960px] text-left text-sm">
                <thead class="bg-brand-50 text-xs uppercase tracking-wide text-brand-700">
                    <tr>
                        <th class="w-11 px-5 py-3.5">
                            <input
                                type="checkbox"
                                wire:model.live="selectAll"
                                class="h-4 w-4 rounded border-brand-300 text-brand-600 focus:ring-brand-200"
                                aria-label="Pilih semua siswa">
                        </th>
                        <th class="w-16 px-5 py-3.5">No</th>
                        <th class="px-5 py-3.5">Siswa</th>
                        <th class="px-5 py-3.5">NIS / NISN</th>
                        <th class="px-5 py-3.5">Jenis Kelamin</th>
                        <th class="px-5 py-3.5 text-center">Tahun Masuk</th>
                        <th class="px-5 py-3.5 text-center">Status</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-brand-50">
                    @forelse ($siswas as $index => $siswa)
                    @php
                    $statusBadge = match ($siswa->status) {
                    'aktif' => ['bg' => 'bg-brand-100 text-brand-700', 'dot' => 'bg-brand-600'],
                    'lulus' => ['bg' => 'bg-sky-100 text-sky-700', 'dot' => 'bg-sky-600'],
                    'pindah' => ['bg' => 'bg-amber-100 text-amber-700', 'dot' => 'bg-amber-600'],
                    'keluar' => ['bg' => 'bg-red-100 text-red-700', 'dot' => 'bg-red-600'],
                    default => ['bg' => 'bg-slate-100 text-slate-600', 'dot' => 'bg-slate-500'],
                    };
                    @endphp

                    <tr wire:key="siswa-row-{{ $siswa->id }}" class="transition hover:bg-brand-50/40">
                        <td class="px-5 py-4">
                            <input
                                type="checkbox"
                                wire:model.live="selected"
                                value="{{ $siswa->id }}"
                                class="h-4 w-4 rounded border-brand-300 text-brand-600 focus:ring-brand-200"
                                aria-label="Pilih siswa {{ $siswa->nama_lengkap }}">
                        </td>

                        <td class="whitespace-nowrap px-5 py-4 text-slate-400">
                            {{ $siswas->firstItem() + $index }}
                        </td>

                        <td class="whitespace-nowrap px-5 py-4">
                            <div class="flex items-center gap-3">
                                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-brand-100 text-sm font-extrabold text-brand-700">
                                    {{ strtoupper(mb_substr($siswa->nama_lengkap, 0, 1)) }}
                                </span>
                                <div class="min-w-0">
                                    <p class="font-bold text-slate-800">{{ $siswa->nama_lengkap }}</p>
                                    <p class="mt-0.5 flex items-center gap-1.5 text-xs text-slate-500">
                                        <i class="fa-solid fa-phone text-[10px] text-brand-400"></i>
                                        {{ $siswa->no_hp ?: 'Belum ada no. HP' }}
                                    </p>
                                </div>
                            </div>
                        </td>

                        <td class="whitespace-nowrap px-5 py-4">
                            <div class="space-y-1 text-xs">
                                <div class="flex items-center gap-1.5">
                                    <span class="font-semibold text-slate-400">NIS:</span>
                                    <span class="font-mono font-bold text-slate-700">{{ $siswa->nis ?: '-' }}</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <span class="font-semibold text-slate-400">NISN:</span>
                                    <span class="font-mono text-slate-600">{{ $siswa->nisn ?: '-' }}</span>
                                </div>
                            </div>
                        </td>

                        <td class="whitespace-nowrap px-5 py-4">
                            @if ($siswa->jenis_kelamin === 'L')
                            <span class="inline-flex items-center gap-1.5 rounded-lg bg-sky-50 px-2.5 py-1 text-xs font-semibold text-sky-700">
                                <i class="fa-solid fa-mars text-[11px]"></i>
                                Laki-laki
                            </span>
                            @elseif ($siswa->jenis_kelamin === 'P')
                            <span class="inline-flex items-center gap-1.5 rounded-lg bg-pink-50 px-2.5 py-1 text-xs font-semibold text-pink-700">
                                <i class="fa-solid fa-venus text-[11px]"></i>
                                Perempuan
                            </span>
                            @else
                            <span class="text-xs text-slate-400">-</span>
                            @endif
                        </td>

                        <td class="whitespace-nowrap px-5 py-4 text-center">
                            @if ($siswa->tahun_masuk)
                            <span class="inline-block rounded-lg border border-brand-100 bg-brand-50/60 px-2.5 py-1 font-mono text-xs font-semibold text-slate-700">
                                {{ $siswa->tahun_masuk }}
                            </span>
                            @else
                            <span class="text-xs text-slate-400">-</span>
                            @endif
                        </td>

                        <td class="whitespace-nowrap px-5 py-4 text-center">
                            <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold {{ $statusBadge['bg'] }}">
                                <span class="h-1.5 w-1.5 rounded-full {{ $statusBadge['dot'] }}"></span>
                                {{ ucfirst($siswa->status) }}
                            </span>
                        </td>

                        <td class="whitespace-nowrap px-5 py-4 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <button type="button"
                                    wire:click="edit({{ $siswa->id }})"
                                    class="inline-flex items-center gap-1.5 rounded-xl border border-brand-200 bg-white px-3 py-1.5 text-xs font-semibold text-brand-700 transition hover:bg-brand-50"
                                    title="Edit">
                                    <i class="fa-solid fa-pen-to-square text-[11px]"></i>
                                    Edit
                                </button>

                                <button type="button"
                                    wire:click="confirmDelete({{ $siswa->id }})"
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
                        <td colspan="8" class="px-6 py-16 text-center">
                            <span class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-brand-100 text-2xl text-brand-500">
                                <i class="fa-solid fa-user-graduate"></i>
                            </span>
                            <p class="mt-4 font-bold text-brand-900">
                                {{ filled($search ?? null) ? 'Data siswa tidak ditemukan' : 'Belum ada data siswa' }}
                            </p>
                            <p class="mt-1 text-sm text-slate-500">
                                {{ filled($search ?? null) ? 'Coba gunakan kata kunci nama, NIS, NISN, atau NIK lain.' : 'Klik tombol "Tambah Siswa" untuk menambahkan data siswa baru.' }}
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
        @forelse ($siswas as $siswa)
        @php
        $statusBadge = match ($siswa->status) {
        'aktif' => ['bg' => 'bg-brand-100 text-brand-700', 'dot' => 'bg-brand-600'],
        'lulus' => ['bg' => 'bg-sky-100 text-sky-700', 'dot' => 'bg-sky-600'],
        'pindah' => ['bg' => 'bg-amber-100 text-amber-700', 'dot' => 'bg-amber-600'],
        'keluar' => ['bg' => 'bg-red-100 text-red-700', 'dot' => 'bg-red-600'],
        default => ['bg' => 'bg-slate-100 text-slate-600', 'dot' => 'bg-slate-500'],
        };
        @endphp

        <div wire:key="siswa-mobile-{{ $siswa->id }}"
            class="rounded-2xl border border-brand-100 bg-white p-4 shadow-sm transition hover:border-brand-200">
            <div class="flex items-start gap-3">
                <input
                    type="checkbox"
                    wire:model.live="selected"
                    value="{{ $siswa->id }}"
                    class="mt-1 h-4 w-4 rounded border-brand-300 text-brand-600 focus:ring-brand-200"
                    aria-label="Pilih siswa {{ $siswa->nama_lengkap }}">

                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-brand-100 text-sm font-extrabold text-brand-700">
                    {{ strtoupper(mb_substr($siswa->nama_lengkap, 0, 1)) }}
                </span>

                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-start justify-between gap-2">
                        <h3 class="truncate text-base font-extrabold text-slate-800">
                            {{ $siswa->nama_lengkap }}
                        </h3>

                        <span class="inline-flex shrink-0 items-center gap-1 rounded-full px-2.5 py-0.5 text-[11px] font-semibold {{ $statusBadge['bg'] }}">
                            <span class="h-1.5 w-1.5 rounded-full {{ $statusBadge['dot'] }}"></span>
                            {{ ucfirst($siswa->status) }}
                        </span>
                    </div>

                    <p class="mt-1 font-mono text-xs text-slate-500">
                        NIS: <strong class="text-slate-700">{{ $siswa->nis ?: '-' }}</strong>
                        <span class="mx-1 text-slate-300">·</span>
                        NISN: <strong class="text-slate-700">{{ $siswa->nisn ?: '-' }}</strong>
                    </p>

                    <div class="mt-2 flex flex-wrap items-center gap-2 text-xs text-slate-500">
                        @if ($siswa->jenis_kelamin === 'L')
                        <span class="inline-flex items-center gap-1 rounded-md bg-sky-50 px-2 py-0.5 font-semibold text-sky-700">
                            <i class="fa-solid fa-mars text-[10px]"></i> Laki-laki
                        </span>
                        @elseif ($siswa->jenis_kelamin === 'P')
                        <span class="inline-flex items-center gap-1 rounded-md bg-pink-50 px-2 py-0.5 font-semibold text-pink-700">
                            <i class="fa-solid fa-venus text-[10px]"></i> Perempuan
                        </span>
                        @endif

                        <span>Masuk: <strong class="font-semibold text-slate-700">{{ $siswa->tahun_masuk ?: '-' }}</strong></span>
                    </div>
                </div>
            </div>

            <div class="mt-4 flex gap-2 border-t border-brand-50 pt-3">
                <button type="button" wire:click="edit({{ $siswa->id }})"
                    class="inline-flex flex-1 items-center justify-center gap-1.5 rounded-xl border border-brand-200 bg-brand-50/50 py-2 text-xs font-semibold text-brand-700 transition hover:bg-brand-100">
                    <i class="fa-solid fa-pen-to-square text-[11px]"></i>
                    Edit
                </button>
                <button type="button" wire:click="confirmDelete({{ $siswa->id }})"
                    class="inline-flex flex-1 items-center justify-center gap-1.5 rounded-xl border border-red-200 bg-red-50/50 py-2 text-xs font-semibold text-red-600 transition hover:bg-red-100">
                    <i class="fa-solid fa-trash-can text-[11px]"></i>
                    Hapus
                </button>
            </div>
        </div>
        @empty
        <div class="rounded-2xl border border-dashed border-brand-200 bg-white/70 px-6 py-14 text-center">
            <span class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-brand-100 text-2xl text-brand-500">
                <i class="fa-solid fa-user-graduate"></i>
            </span>
            <p class="mt-4 font-bold text-brand-900">
                {{ filled($search ?? null) ? 'Data siswa tidak ditemukan' : 'Belum ada data siswa' }}
            </p>
            <p class="mt-1 text-sm text-slate-500">
                {{ filled($search ?? null) ? 'Coba gunakan kata kunci nama, NIS, NISN, atau NIK lain.' : 'Klik tombol "Tambah Siswa" untuk menambahkan data siswa baru.' }}
            </p>
        </div>
        @endforelse
    </div>

    {{-- ================= PAGINASI ================= --}}
    <div>{{ $siswas->links() }}</div>

    {{-- ================= MODAL TAMBAH / EDIT SISWA ================= --}}
    @if ($showModal)
    <div class="fixed inset-0 z-[60] flex items-end justify-center sm:items-center sm:p-4"
        wire:key="siswa-form-modal"
        x-data="{ show: false }" x-init="$nextTick(() => show = true)"
        x-on:keydown.escape.window="$wire.set('showModal', false)"
        role="dialog" aria-modal="true">

        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity duration-200"
            :class="show ? 'opacity-100' : 'opacity-0'"
            wire:click="$set('showModal', false)"></div>

        <div class="relative z-10 flex max-h-[92vh] w-full max-w-3xl flex-col overflow-hidden rounded-t-3xl bg-white shadow-2xl transition duration-200 sm:rounded-3xl"
            :class="show ? 'translate-y-0 opacity-100' : 'translate-y-4 opacity-0'"
            wire:click.stop>

            <form wire:submit="save" class="flex min-h-0 flex-1 flex-col overflow-hidden">
                {{-- Header Modal --}}
                <div class="flex shrink-0 items-center justify-between gap-3 bg-gradient-to-br from-brand-500 to-brand-600 px-6 py-5 text-white">
                    <div class="flex min-w-0 items-center gap-3.5">
                        <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-white text-lg font-extrabold text-brand-700 shadow-md">
                            <i class="fa-solid {{ $isEdit ? 'fa-user-pen' : 'fa-user-plus' }}"></i>
                        </span>
                        <div class="min-w-0">
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-brand-100">
                                {{ $isEdit ? 'Perbarui Data Induk' : 'Data Induk Siswa Baru' }}
                            </p>
                            <h2 class="truncate text-lg font-extrabold sm:text-xl">
                                {{ $isEdit ? 'Edit Data Siswa' : 'Tambah Siswa' }}
                            </h2>
                        </div>
                    </div>

                    <button type="button" wire:click="$set('showModal', false)" aria-label="Tutup"
                        class="grid h-9 w-9 shrink-0 place-items-center rounded-lg text-white/80 transition hover:bg-white/15 hover:text-white">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                {{-- Body Form --}}
                <div class="space-y-6 overflow-y-auto p-6">

                    {{-- Bagian 1: Identitas Siswa --}}
                    <div class="space-y-4">
                        <div class="flex items-center gap-2 border-b border-brand-100 pb-2">
                            <span class="grid h-6 w-6 place-items-center rounded-lg bg-brand-100 text-xs text-brand-700">
                                <i class="fa-solid fa-id-card"></i>
                            </span>
                            <h3 class="text-xs font-extrabold uppercase tracking-wider text-brand-800">Identitas Siswa</h3>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-brand-700">
                                    Nama Lengkap <span class="text-red-500">*</span>
                                </label>
                                <input type="text" wire:model="nama_lengkap"
                                    placeholder="Masukkan nama lengkap siswa..."
                                    class="w-full rounded-xl border border-brand-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition
                                           placeholder:text-slate-400 focus:border-brand-400 focus:ring-2 focus:ring-brand-200">
                                @error('nama_lengkap')
                                <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600">
                                    <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                                </p>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-brand-700">NIS</label>
                                <input type="text" wire:model="nis"
                                    placeholder="Nomor Induk Siswa"
                                    class="w-full rounded-xl border border-brand-200 bg-white px-3.5 py-2.5 font-mono text-sm text-slate-700 outline-none transition
                                           placeholder:font-sans placeholder:text-slate-400 focus:border-brand-400 focus:ring-2 focus:ring-brand-200">
                                @error('nis')
                                <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600">
                                    <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                                </p>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-brand-700">NISN</label>
                                <input type="text" wire:model="nisn"
                                    placeholder="Nomor Induk Siswa Nasional"
                                    class="w-full rounded-xl border border-brand-200 bg-white px-3.5 py-2.5 font-mono text-sm text-slate-700 outline-none transition
                                           placeholder:font-sans placeholder:text-slate-400 focus:border-brand-400 focus:ring-2 focus:ring-brand-200">
                                @error('nisn')
                                <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600">
                                    <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                                </p>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-brand-700">NIK</label>
                                <input type="text" wire:model="nik"
                                    placeholder="16 digit NIK"
                                    class="w-full rounded-xl border border-brand-200 bg-white px-3.5 py-2.5 font-mono text-sm text-slate-700 outline-none transition
                                           placeholder:font-sans placeholder:text-slate-400 focus:border-brand-400 focus:ring-2 focus:ring-brand-200">
                                @error('nik')
                                <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600">
                                    <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                                </p>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-brand-700">Jenis Kelamin</label>
                                <select wire:model="jenis_kelamin"
                                    class="w-full rounded-xl border border-brand-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition
                                           focus:border-brand-400 focus:ring-2 focus:ring-brand-200">
                                    <option value="">Pilih jenis kelamin</option>
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                                @error('jenis_kelamin')
                                <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600">
                                    <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                                </p>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-brand-700">Tempat Lahir</label>
                                <input type="text" wire:model="tempat_lahir"
                                    placeholder="Kota / Kabupaten kelahiran"
                                    class="w-full rounded-xl border border-brand-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition
                                           placeholder:text-slate-400 focus:border-brand-400 focus:ring-2 focus:ring-brand-200">
                                @error('tempat_lahir')
                                <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600">
                                    <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                                </p>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-brand-700">Tanggal Lahir</label>
                                <input type="date" wire:model="tanggal_lahir"
                                    class="w-full rounded-xl border border-brand-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition
                                           focus:border-brand-400 focus:ring-2 focus:ring-brand-200">
                                @error('tanggal_lahir')
                                <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600">
                                    <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                                </p>
                                @enderror
                            </div>

                            <div class="sm:col-span-2">
                                <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-brand-700">Agama</label>
                                <input type="text" wire:model="agama"
                                    placeholder="Contoh: Islam, Kristen, Katolik, Hindu, Buddha, Konghucu"
                                    class="w-full rounded-xl border border-brand-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition
                                           placeholder:text-slate-400 focus:border-brand-400 focus:ring-2 focus:ring-brand-200">
                                @error('agama')
                                <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600">
                                    <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                                </p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Bagian 2: Kontak & Alamat --}}
                    <div class="space-y-4">
                        <div class="flex items-center gap-2 border-b border-brand-100 pb-2">
                            <span class="grid h-6 w-6 place-items-center rounded-lg bg-brand-100 text-xs text-brand-700">
                                <i class="fa-solid fa-location-dot"></i>
                            </span>
                            <h3 class="text-xs font-extrabold uppercase tracking-wider text-brand-800">Kontak dan Alamat</h3>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-brand-700">Nomor HP Siswa</label>
                                <input type="text" wire:model="no_hp"
                                    placeholder="08xxxxxxxxxx"
                                    class="w-full rounded-xl border border-brand-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition
                                           placeholder:text-slate-400 focus:border-brand-400 focus:ring-2 focus:ring-brand-200">
                                @error('no_hp')
                                <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600">
                                    <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                                </p>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-brand-700">Nomor HP Orang Tua</label>
                                <input type="text" wire:model="no_hp_orang_tua"
                                    placeholder="08xxxxxxxxxx"
                                    class="w-full rounded-xl border border-brand-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition
                                           placeholder:text-slate-400 focus:border-brand-400 focus:ring-2 focus:ring-brand-200">
                                @error('no_hp_orang_tua')
                                <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600">
                                    <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                                </p>
                                @enderror
                            </div>

                            <div class="sm:col-span-2">
                                <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-brand-700">Alamat Lengkap</label>
                                <textarea wire:model="alamat" rows="2"
                                    placeholder="Masukkan alamat tempat tinggal siswa..."
                                    class="w-full rounded-xl border border-brand-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition
                                           placeholder:text-slate-400 focus:border-brand-400 focus:ring-2 focus:ring-brand-200"></textarea>
                                @error('alamat')
                                <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600">
                                    <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                                </p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Bagian 3: Data Orang Tua --}}
                    <div class="space-y-4">
                        <div class="flex items-center gap-2 border-b border-brand-100 pb-2">
                            <span class="grid h-6 w-6 place-items-center rounded-lg bg-brand-100 text-xs text-brand-700">
                                <i class="fa-solid fa-people-roof"></i>
                            </span>
                            <h3 class="text-xs font-extrabold uppercase tracking-wider text-brand-800">Data Orang Tua</h3>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-brand-700">Nama Ayah</label>
                                <input type="text" wire:model="nama_ayah"
                                    placeholder="Nama lengkap ayah"
                                    class="w-full rounded-xl border border-brand-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition
                                           placeholder:text-slate-400 focus:border-brand-400 focus:ring-2 focus:ring-brand-200">
                                @error('nama_ayah')
                                <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600">
                                    <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                                </p>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-brand-700">Nama Ibu</label>
                                <input type="text" wire:model="nama_ibu"
                                    placeholder="Nama lengkap ibu"
                                    class="w-full rounded-xl border border-brand-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition
                                           placeholder:text-slate-400 focus:border-brand-400 focus:ring-2 focus:ring-brand-200">
                                @error('nama_ibu')
                                <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600">
                                    <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                                </p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Bagian 4: Informasi Sekolah --}}
                    <div class="space-y-4">
                        <div class="flex items-center gap-2 border-b border-brand-100 pb-2">
                            <span class="grid h-6 w-6 place-items-center rounded-lg bg-brand-100 text-xs text-brand-700">
                                <i class="fa-solid fa-graduation-cap"></i>
                            </span>
                            <h3 class="text-xs font-extrabold uppercase tracking-wider text-brand-800">Informasi Sekolah</h3>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-brand-700">Tahun Masuk</label>
                                <input type="number" wire:model="tahun_masuk"
                                    min="1900" max="{{ date('Y') + 1 }}"
                                    placeholder="{{ date('Y') }}"
                                    class="w-full rounded-xl border border-brand-200 bg-white px-3.5 py-2.5 font-mono text-sm text-slate-700 outline-none transition
                                           placeholder:font-sans placeholder:text-slate-400 focus:border-brand-400 focus:ring-2 focus:ring-brand-200">
                                @error('tahun_masuk')
                                <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600">
                                    <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                                </p>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-brand-700">
                                    Status Siswa <span class="text-red-500">*</span>
                                </label>
                                <select wire:model="status"
                                    class="w-full rounded-xl border border-brand-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition
                                           focus:border-brand-400 focus:ring-2 focus:ring-brand-200">
                                    <option value="aktif">Aktif</option>
                                    <option value="lulus">Lulus</option>
                                    <option value="pindah">Pindah</option>
                                    <option value="keluar">Keluar</option>
                                </select>
                                @error('status')
                                <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600">
                                    <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                                </p>
                                @enderror
                            </div>
                        </div>
                    </div>

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
                            {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Siswa' }}
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

    {{-- ================= MODAL KONFIRMASI HAPUS SATU SISWA ================= --}}
    @if ($showDeleteModal)
    <div class="fixed inset-0 z-[60] flex items-end justify-center sm:items-center sm:p-4"
        x-data="{ show: false }" x-init="$nextTick(() => show = true)"
        x-on:keydown.escape.window="$wire.set('showDeleteModal', false)"
        role="dialog" aria-modal="true">

        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity duration-200"
            :class="show ? 'opacity-100' : 'opacity-0'"
            wire:click="$set('showDeleteModal', false)"></div>

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
                        <h2 class="text-lg font-extrabold">Hapus Data Siswa?</h2>
                    </div>
                </div>

                <button type="button" wire:click="$set('showDeleteModal', false)" aria-label="Tutup"
                    class="grid h-9 w-9 place-items-center rounded-lg text-white/80 transition hover:bg-white/15 hover:text-white">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="space-y-3 p-6">
                <p class="text-sm leading-relaxed text-slate-600">
                    Apakah Anda yakin ingin menghapus data siswa ini dari sistem?
                </p>
                <div class="flex items-start gap-2.5 rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs text-amber-800">
                    <i class="fa-solid fa-circle-info mt-0.5 text-amber-500"></i>
                    <p>Siswa yang sudah memiliki riwayat akademik (absensi, nilai, ujian, atau rapor) <strong>tidak dapat dihapus</strong>.</p>
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

    {{-- ================= MODAL KONFIRMASI HAPUS MASSAL ================= --}}
    @if ($showBulkDeleteModal)
    <div class="fixed inset-0 z-[60] flex items-end justify-center sm:items-center sm:p-4"
        x-data="{ show: false }" x-init="$nextTick(() => show = true)"
        x-on:keydown.escape.window="$wire.set('showBulkDeleteModal', false)"
        role="dialog" aria-modal="true">

        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity duration-200"
            :class="show ? 'opacity-100' : 'opacity-0'"
            wire:click="$set('showBulkDeleteModal', false)"></div>

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
                        <h2 class="text-lg font-extrabold">Hapus {{ $jumlahTerpilih }} Siswa Terpilih?</h2>
                    </div>
                </div>

                <button type="button" wire:click="$set('showBulkDeleteModal', false)" aria-label="Tutup"
                    class="grid h-9 w-9 place-items-center rounded-lg text-white/80 transition hover:bg-white/15 hover:text-white">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="space-y-3 p-6">
                <p class="text-sm leading-relaxed text-slate-600">
                    Apakah Anda yakin ingin menghapus semua data siswa yang dipilih?
                </p>
                <div class="flex items-start gap-2.5 rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs text-amber-800">
                    <i class="fa-solid fa-circle-info mt-0.5 text-amber-500"></i>
                    <p>Siswa yang sudah memiliki riwayat akademik akan <strong>otomatis dilewati</strong>.</p>
                </div>
            </div>

            <div class="flex flex-col-reverse gap-2 border-t border-slate-100 bg-white px-6 py-4 sm:flex-row sm:justify-end">
                <button type="button" wire:click="$set('showBulkDeleteModal', false)"
                    class="rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                    Batal
                </button>

                <button type="button" wire:click="deleteSelected" wire:loading.attr="disabled" wire:target="deleteSelected"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-red-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-red-500/20 transition hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-60">
                    <span wire:loading.remove wire:target="deleteSelected" class="inline-flex items-center gap-2">
                        <i class="fa-solid fa-trash-can text-xs"></i>
                        Ya, Hapus Terpilih
                    </span>
                    <span wire:loading wire:target="deleteSelected" class="inline-flex items-center gap-2">
                        <i class="fa-solid fa-circle-notch fa-spin text-xs"></i>
                        Menghapus...
                    </span>
                </button>
            </div>
        </div>
    </div>
    @endif

</div>