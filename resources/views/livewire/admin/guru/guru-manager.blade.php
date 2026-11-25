@php
$gayaStatus = [
'aktif' => ['bg-brand-100 text-brand-700', 'Aktif'],
'nonaktif' => ['bg-slate-100 text-slate-600', 'Nonaktif'],
];

$badgeStatus = fn ($s) => $gayaStatus[$s] ?? ['bg-slate-100 text-slate-600', ucfirst((string) $s)];

$namaJk = fn ($jk) => match ($jk) {
'L' => 'Laki-laki',
'P' => 'Perempuan',
default => '-',
};

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
                <h1 class="text-2xl font-extrabold sm:text-3xl">Manajemen Guru</h1>
                <p class="mt-2 max-w-md text-sm text-brand-50">
                    Kelola data tenaga pendidik dan akun guru di sekolah Anda.
                </p>

                <span class="mt-4 inline-flex items-center gap-2 rounded-full bg-white/15 px-3.5 py-1.5 text-xs font-medium backdrop-blur">
                    <i class="fa-solid fa-chalkboard-user"></i>
                    {{ $gurus->total() }} guru terdaftar
                </span>
            </div>

            <button type="button" wire:click="create"
                class="inline-flex shrink-0 items-center justify-center gap-2 self-start rounded-2xl bg-white px-5 py-3 text-sm font-bold text-brand-700 shadow-md transition hover:bg-brand-50 active:scale-[0.99] sm:self-auto">
                <i class="fa-solid fa-plus text-xs"></i>
                Tambah Guru
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
                <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-sm text-brand-400"></i>

                <input
                    type="search"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Cari nama, NIP, atau NUPTK..."
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

    {{-- ================= TABEL DESKTOP ================= --}}
    <div class="hidden overflow-hidden rounded-2xl border border-brand-100 bg-white shadow-sm md:block">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[920px] text-left text-sm">
                <thead class="bg-brand-50 text-xs uppercase tracking-wide text-brand-700">
                    <tr>
                        <th class="w-11 px-4 py-3.5">
                            <input type="checkbox" wire:model.live="selectAll"
                                class="h-4 w-4 rounded border-brand-300 text-brand-600 focus:ring-brand-200"
                                aria-label="Pilih semua guru">
                        </th>
                        <th class="px-4 py-3.5">Nama Guru</th>
                        <th class="px-4 py-3.5">NIP</th>
                        <th class="px-4 py-3.5">NUPTK</th>
                        <th class="px-4 py-3.5">Jenis Kelamin</th>
                        <th class="px-4 py-3.5">Status</th>
                        <th class="px-4 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-brand-50">
                    @forelse ($gurus as $guru)
                    @php [$kelasStatus, $labelStatus] = $badgeStatus($guru->status); @endphp

                    <tr wire:key="guru-{{ $guru->id }}" class="transition hover:bg-brand-50/40">
                        <td class="px-4 py-4">
                            <input type="checkbox" wire:model.live="selected"
                                value="{{ $guru->id }}"
                                class="h-4 w-4 rounded border-brand-300 text-brand-600 focus:ring-brand-200"
                                aria-label="Pilih {{ $guru->nama_lengkap }}">
                        </td>

                        <td class="px-4 py-4">
                            <div class="flex items-center gap-3">
                                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-brand-100 text-sm font-extrabold text-brand-700">
                                    {{ strtoupper(mb_substr($guru->nama_lengkap, 0, 1)) }}
                                </span>
                                <div class="min-w-0">
                                    <p class="truncate font-semibold text-slate-800">{{ $guru->nama_lengkap }}</p>
                                    <p class="truncate text-xs text-slate-500">{{ $guru->user?->email ?? 'Tidak ada email' }}</p>
                                </div>
                            </div>
                        </td>

                        <td class="whitespace-nowrap px-4 py-4 font-mono text-xs text-slate-600">
                            {{ $guru->nip ?: '-' }}
                        </td>

                        <td class="whitespace-nowrap px-4 py-4 font-mono text-xs text-slate-600">
                            {{ $guru->nuptk ?: '-' }}
                        </td>

                        <td class="whitespace-nowrap px-4 py-4 text-slate-600">
                            {{ $namaJk($guru->jenis_kelamin) }}
                        </td>

                        <td class="px-4 py-4">
                            <span class="inline-block rounded-full px-3 py-1 text-xs font-semibold {{ $kelasStatus }}">
                                {{ $labelStatus }}
                            </span>
                        </td>

                        <td class="whitespace-nowrap px-4 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                {{-- Tombol Detail --}}
                                <button type="button" wire:click="showDetail({{ $guru->id }})"
                                    wire:loading.attr="disabled" wire:target="showDetail({{ $guru->id }})"
                                    class="inline-flex items-center gap-1.5 rounded-xl border border-sky-200 bg-sky-50/60 px-3 py-1.5 text-xs font-semibold text-sky-700 transition hover:bg-sky-100 disabled:opacity-60">
                                    <i wire:loading.remove wire:target="showDetail({{ $guru->id }})" class="fa-solid fa-eye text-[11px]"></i>
                                    <i wire:loading wire:target="showDetail({{ $guru->id }})" class="fa-solid fa-circle-notch fa-spin text-[11px]"></i>
                                    Detail
                                </button>

                                {{-- Tombol Edit --}}
                                <button type="button" wire:click="edit({{ $guru->id }})"
                                    wire:loading.attr="disabled" wire:target="edit({{ $guru->id }})"
                                    class="inline-flex items-center gap-1.5 rounded-xl border border-brand-200 bg-white px-3 py-1.5 text-xs font-semibold text-brand-700 transition hover:bg-brand-50 disabled:opacity-60">
                                    <i wire:loading.remove wire:target="edit({{ $guru->id }})" class="fa-solid fa-pen-to-square text-[11px]"></i>
                                    <i wire:loading wire:target="edit({{ $guru->id }})" class="fa-solid fa-circle-notch fa-spin text-[11px]"></i>
                                    Edit
                                </button>

                                <button type="button" wire:click="confirmDelete({{ $guru->id }})"
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
                                <i class="fa-solid fa-chalkboard-user"></i>
                            </span>
                            <p class="mt-4 font-bold text-brand-900">Data guru belum tersedia</p>
                            <p class="mt-1 text-sm text-slate-500">Klik tombol "Tambah Guru" untuk menambahkan data guru baru.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ================= KARTU MOBILE ================= --}}
    <div class="space-y-3 md:hidden">
        @forelse ($gurus as $guru)
        @php [$kelasStatus, $labelStatus] = $badgeStatus($guru->status); @endphp

        <div wire:key="guru-mobile-{{ $guru->id }}"
            class="rounded-2xl border border-brand-100 bg-white p-4 shadow-sm transition hover:border-brand-200">
            <div class="flex items-start gap-3">
                <input type="checkbox" wire:model.live="selected"
                    value="{{ $guru->id }}"
                    class="mt-1 h-4 w-4 rounded border-brand-300 text-brand-600 focus:ring-brand-200"
                    aria-label="Pilih {{ $guru->nama_lengkap }}">

                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-brand-100 text-sm font-extrabold text-brand-700">
                    {{ strtoupper(mb_substr($guru->nama_lengkap, 0, 1)) }}
                </span>

                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-start justify-between gap-2">
                        <h3 class="truncate text-base font-extrabold text-slate-800">
                            {{ $guru->nama_lengkap }}
                        </h3>
                        <span class="inline-block shrink-0 rounded-full px-2.5 py-0.5 text-[11px] font-semibold {{ $kelasStatus }}">
                            {{ $labelStatus }}
                        </span>
                    </div>

                    <p class="mt-0.5 truncate text-xs text-slate-500">{{ $guru->user?->email ?? 'Tidak ada email' }}</p>

                    <p class="mt-1.5 font-mono text-xs text-slate-500">
                        NIP: <strong class="text-slate-700">{{ $guru->nip ?: '-' }}</strong>
                        <span class="mx-1 text-slate-300">·</span>
                        {{ $namaJk($guru->jenis_kelamin) }}
                    </p>
                </div>
            </div>

            <div class="mt-4 flex gap-2 border-t border-brand-50 pt-3">
                <button type="button" wire:click="showDetail({{ $guru->id }})"
                    wire:loading.attr="disabled" wire:target="showDetail({{ $guru->id }})"
                    class="inline-flex flex-1 items-center justify-center gap-1.5 rounded-xl border border-sky-200 bg-sky-50/60 py-2 text-xs font-semibold text-sky-700 transition hover:bg-sky-100 disabled:opacity-60">
                    <i wire:loading.remove wire:target="showDetail({{ $guru->id }})" class="fa-solid fa-eye text-[11px]"></i>
                    <i wire:loading wire:target="showDetail({{ $guru->id }})" class="fa-solid fa-circle-notch fa-spin text-[11px]"></i>
                    Detail
                </button>

                <button type="button" wire:click="edit({{ $guru->id }})"
                    wire:loading.attr="disabled" wire:target="edit({{ $guru->id }})"
                    class="inline-flex flex-1 items-center justify-center gap-1.5 rounded-xl border border-brand-200 bg-brand-50/50 py-2 text-xs font-semibold text-brand-700 transition hover:bg-brand-100 disabled:opacity-60">
                    <i wire:loading.remove wire:target="edit({{ $guru->id }})" class="fa-solid fa-pen-to-square text-[11px]"></i>
                    <i wire:loading wire:target="edit({{ $guru->id }})" class="fa-solid fa-circle-notch fa-spin text-[11px]"></i>
                    Edit
                </button>

                <button type="button" wire:click="confirmDelete({{ $guru->id }})"
                    class="inline-flex flex-1 items-center justify-center gap-1.5 rounded-xl border border-red-200 bg-red-50/50 py-2 text-xs font-semibold text-red-600 transition hover:bg-red-100">
                    <i class="fa-solid fa-trash-can text-[11px]"></i>
                    Hapus
                </button>
            </div>
        </div>
        @empty
        <div class="rounded-2xl border border-dashed border-brand-200 bg-white/70 px-6 py-14 text-center">
            <span class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-brand-100 text-2xl text-brand-500">
                <i class="fa-solid fa-chalkboard-user"></i>
            </span>
            <p class="mt-4 font-bold text-brand-900">Data guru belum tersedia</p>
            <p class="mt-1 text-sm text-slate-500">Klik tombol "Tambah Guru" untuk menambahkan data guru baru.</p>
        </div>
        @endforelse
    </div>

    <div>{{ $gurus->links() }}</div>

    {{-- ================= MODAL DETAIL GURU ================= --}}
    @if ($showDetailModal && !empty($detailGuru))
    @php [$detailKelasStatus, $detailLabelStatus] = $badgeStatus($detailGuru['status'] ?? 'aktif'); @endphp
    <div class="fixed inset-0 z-[60] flex items-end justify-center sm:items-center sm:p-4"
        x-data="{ show: false }" x-init="$nextTick(() => show = true)"
        x-on:keydown.escape.window="$wire.closeDetail()"
        role="dialog" aria-modal="true">

        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity duration-200"
            :class="show ? 'opacity-100' : 'opacity-0'"
            wire:click="closeDetail"></div>

        <div class="relative z-10 flex max-h-[92vh] w-full max-w-2xl flex-col overflow-hidden rounded-t-3xl bg-white shadow-2xl transition duration-200 sm:rounded-3xl"
            :class="show ? 'translate-y-0 opacity-100' : 'translate-y-4 opacity-0'"
            wire:click.stop>

            {{-- Header Modal Detail --}}
            <div class="flex shrink-0 items-center justify-between gap-3 bg-gradient-to-br from-brand-500 to-brand-600 px-6 py-5 text-white">
                <div class="flex min-w-0 items-center gap-3.5">
                    <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-white text-lg font-extrabold text-brand-700 shadow-md">
                        {{ strtoupper(mb_substr($detailGuru['nama_lengkap'] ?? 'G', 0, 1)) }}
                    </span>
                    <div class="min-w-0">
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-brand-100">
                            Profil Lengkap Tenaga Pendidik
                        </p>
                        <h2 class="truncate text-lg font-extrabold sm:text-xl">
                            {{ $detailGuru['nama_lengkap'] }}
                        </h2>
                    </div>
                </div>

                <button type="button" wire:click="closeDetail" aria-label="Tutup"
                    class="grid h-9 w-9 shrink-0 place-items-center rounded-lg text-white/80 transition hover:bg-white/15 hover:text-white">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            {{-- Isi Detail --}}
            <div class="space-y-6 overflow-y-auto p-6">

                {{-- Ringkasan Akun & Status --}}
                <div class="flex flex-col justify-between gap-3 rounded-2xl border border-brand-100 bg-brand-50/50 p-4 sm:flex-row sm:items-center">
                    <div class="flex items-center gap-3">
                        <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-white text-brand-600 shadow-sm">
                            <i class="fa-solid fa-envelope"></i>
                        </span>
                        <div class="min-w-0">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Akun Pengguna Terhubung</p>
                            <p class="truncate text-sm font-bold text-slate-800">{{ $detailGuru['akun_nama'] }}</p>
                            <p class="truncate text-xs text-slate-500">{{ $detailGuru['akun_email'] }}</p>
                        </div>
                    </div>

                    <span class="inline-flex w-fit items-center gap-1.5 rounded-full px-3.5 py-1 text-xs font-bold {{ $detailKelasStatus }}">
                        <span class="h-1.5 w-1.5 rounded-full {{ ($detailGuru['status'] ?? '') === 'aktif' ? 'bg-brand-600' : 'bg-slate-500' }}"></span>
                        {{ $detailLabelStatus }}
                    </span>
                </div>

                {{-- Identitas Kepegawaian --}}
                <div>
                    <div class="mb-3 flex items-center gap-2 border-b border-brand-100 pb-2">
                        <span class="grid h-6 w-6 place-items-center rounded-lg bg-brand-100 text-xs text-brand-700">
                            <i class="fa-solid fa-id-badge"></i>
                        </span>
                        <h3 class="text-xs font-extrabold uppercase tracking-wider text-brand-800">Identitas Kepegawaian</h3>
                    </div>

                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div class="rounded-xl border border-slate-100 bg-slate-50/70 p-3.5">
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">NIP</p>
                            <p class="mt-1 font-mono text-sm font-bold text-slate-800">{{ $detailGuru['nip'] }}</p>
                        </div>

                        <div class="rounded-xl border border-slate-100 bg-slate-50/70 p-3.5">
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">NUPTK</p>
                            <p class="mt-1 font-mono text-sm font-bold text-slate-800">{{ $detailGuru['nuptk'] }}</p>
                        </div>
                    </div>
                </div>

                {{-- Biodata & Kontak --}}
                <div>
                    <div class="mb-3 flex items-center gap-2 border-b border-brand-100 pb-2">
                        <span class="grid h-6 w-6 place-items-center rounded-lg bg-brand-100 text-xs text-brand-700">
                            <i class="fa-solid fa-user"></i>
                        </span>
                        <h3 class="text-xs font-extrabold uppercase tracking-wider text-brand-800">Biodata & Kontak</h3>
                    </div>

                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div class="rounded-xl border border-slate-100 bg-slate-50/70 p-3.5">
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Jenis Kelamin</p>
                            <p class="mt-1 text-sm font-semibold text-slate-800">{{ $detailGuru['jenis_kelamin'] }}</p>
                        </div>

                        <div class="rounded-xl border border-slate-100 bg-slate-50/70 p-3.5">
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Nomor HP</p>
                            <p class="mt-1 font-mono text-sm font-semibold text-slate-800">{{ $detailGuru['no_hp'] }}</p>
                        </div>

                        <div class="rounded-xl border border-slate-100 bg-slate-50/70 p-3.5">
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Tempat Lahir</p>
                            <p class="mt-1 text-sm font-semibold text-slate-800">{{ $detailGuru['tempat_lahir'] }}</p>
                        </div>

                        <div class="rounded-xl border border-slate-100 bg-slate-50/70 p-3.5">
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Tanggal Lahir</p>
                            <p class="mt-1 text-sm font-semibold text-slate-800">{{ $detailGuru['tanggal_lahir'] }}</p>
                        </div>

                        <div class="rounded-xl border border-slate-100 bg-slate-50/70 p-3.5 sm:col-span-2">
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Alamat Lengkap</p>
                            <p class="mt-1 text-sm leading-relaxed text-slate-700">{{ $detailGuru['alamat'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer Modal Detail --}}
            <div class="flex shrink-0 flex-col-reverse gap-2 border-t border-brand-100 bg-white px-6 py-4 sm:flex-row sm:justify-end">
                <button type="button" wire:click="closeDetail"
                    class="rounded-xl border border-brand-200 px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-brand-50">
                    Tutup
                </button>

                <button type="button" wire:click="editFromDetail"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-brand-500/20 transition hover:bg-brand-700">
                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                    Edit Data
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ================= MODAL TAMBAH / EDIT GURU ================= --}}
    @if ($showModal)
    <div class="fixed inset-0 z-[60] flex items-end justify-center sm:items-center sm:p-4"
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
                                {{ $isEdit ? 'Perbarui Data' : 'Data Pendidik Baru' }}
                            </p>
                            <h2 class="truncate text-lg font-extrabold sm:text-xl">
                                {{ $isEdit ? 'Edit Data Guru' : 'Tambah Guru' }}
                            </h2>
                        </div>
                    </div>

                    <button type="button" wire:click="$set('showModal', false)" aria-label="Tutup"
                        class="grid h-9 w-9 shrink-0 place-items-center rounded-lg text-white/80 transition hover:bg-white/15 hover:text-white">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                {{-- Body Form --}}
                <div class="grid grid-cols-1 gap-4 overflow-y-auto p-6 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-brand-700">
                            Akun Guru <span class="text-red-500">*</span>
                        </label>
                        <select wire:model="user_id"
                            class="w-full rounded-xl border border-brand-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition
                                   focus:border-brand-400 focus:ring-2 focus:ring-brand-200">
                            <option value="">Pilih akun guru</option>
                            @foreach ($akunGuru as $akun)
                            <option value="{{ $akun->id }}">
                                {{ $akun->name }} — {{ $akun->email }}
                            </option>
                            @endforeach
                        </select>
                        @error('user_id')
                        <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </p>
                        @enderror

                        @if ($akunGuru->isEmpty())
                        <div class="mt-2.5 flex items-start gap-2.5 rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs text-amber-800">
                            <i class="fa-solid fa-triangle-exclamation mt-0.5 text-amber-500"></i>
                            <p>
                                Belum ada akun guru yang tersedia. Buat akun dan tetapkan role guru
                                untuk sekolah ini melalui menu <strong>Akun dan Role Pengguna</strong>.
                            </p>
                        </div>
                        @endif
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-brand-700">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input wire:model="nama_lengkap" type="text" placeholder="Masukkan nama lengkap beserta gelar"
                            class="w-full rounded-xl border border-brand-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition
                                   placeholder:text-slate-400 focus:border-brand-400 focus:ring-2 focus:ring-brand-200">
                        @error('nama_lengkap')
                        <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-brand-700">
                            Jenis Kelamin <span class="text-red-500">*</span>
                        </label>
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
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-brand-700">NIP</label>
                        <input wire:model="nip" type="text" placeholder="Nomor Induk Pegawai (opsional)"
                            class="w-full rounded-xl border border-brand-200 bg-white px-3.5 py-2.5 font-mono text-sm text-slate-700 outline-none transition
                                   placeholder:font-sans placeholder:text-slate-400 focus:border-brand-400 focus:ring-2 focus:ring-brand-200">
                        @error('nip')
                        <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-brand-700">NUPTK</label>
                        <input wire:model="nuptk" type="text" placeholder="Nomor Unik Pendidik (opsional)"
                            class="w-full rounded-xl border border-brand-200 bg-white px-3.5 py-2.5 font-mono text-sm text-slate-700 outline-none transition
                                   placeholder:font-sans placeholder:text-slate-400 focus:border-brand-400 focus:ring-2 focus:ring-brand-200">
                        @error('nuptk')
                        <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-brand-700">Tempat Lahir</label>
                        <input wire:model="tempat_lahir" type="text" placeholder="Kota/Kabupaten kelahiran"
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
                        <input wire:model="tanggal_lahir" type="date"
                            class="w-full rounded-xl border border-brand-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition
                                   focus:border-brand-400 focus:ring-2 focus:ring-brand-200">
                        @error('tanggal_lahir')
                        <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-brand-700">Nomor HP</label>
                        <input wire:model="no_hp" type="text" placeholder="08xxxxxxxxxx"
                            class="w-full rounded-xl border border-brand-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition
                                   placeholder:text-slate-400 focus:border-brand-400 focus:ring-2 focus:ring-brand-200">
                        @error('no_hp')
                        <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-brand-700">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select wire:model="status"
                            class="w-full rounded-xl border border-brand-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition
                                   focus:border-brand-400 focus:ring-2 focus:ring-brand-200">
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>
                        @error('status')
                        <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </p>
                        @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-brand-700">Alamat</label>
                        <textarea wire:model="alamat" rows="3" placeholder="Alamat lengkap tempat tinggal..."
                            class="w-full rounded-xl border border-brand-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition
                                   placeholder:text-slate-400 focus:border-brand-400 focus:ring-2 focus:ring-brand-200"></textarea>
                        @error('alamat')
                        <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </p>
                        @enderror
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
                            {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Guru' }}
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

    {{-- ================= MODAL KONFIRMASI HAPUS SATU GURU ================= --}}
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
                        <h2 class="text-lg font-extrabold">Hapus Data Guru</h2>
                    </div>
                </div>

                <button type="button" wire:click="$set('showDeleteModal', false)" aria-label="Tutup"
                    class="grid h-9 w-9 place-items-center rounded-lg text-white/80 transition hover:bg-white/15 hover:text-white">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="p-6">
                <p class="text-sm leading-relaxed text-slate-600">
                    Apakah Anda yakin ingin menghapus data guru ini? Data guru hanya akan dihapus jika <span class="font-semibold text-slate-800">tidak memiliki penugasan mengajar</span>.
                </p>
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

    {{-- ================= MODAL KONFIRMASI HAPUS BANYAK GURU ================= --}}
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
                        <i class="fa-solid fa-users-slash"></i>
                    </span>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-white/80">Hapus Massal</p>
                        <h2 class="text-lg font-extrabold">Hapus Guru Terpilih</h2>
                    </div>
                </div>

                <button type="button" wire:click="$set('showBulkDeleteModal', false)" aria-label="Tutup"
                    class="grid h-9 w-9 place-items-center rounded-lg text-white/80 transition hover:bg-white/15 hover:text-white">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="space-y-3 p-6">
                <p class="text-sm leading-relaxed text-slate-600">
                    Sebanyak <span class="font-bold text-red-600">{{ $jumlahTerpilih }} guru</span> dipilih untuk dihapus.
                </p>
                <div class="flex items-start gap-2.5 rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs text-amber-800">
                    <i class="fa-solid fa-circle-info mt-0.5 text-amber-500"></i>
                    <p>Guru yang masih memiliki penugasan mengajar aktif akan otomatis dilewati dan tidak dihapus.</p>
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
                        Hapus Terpilih ({{ $jumlahTerpilih }})
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