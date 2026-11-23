@php
// Gaya status pendaftaran: [kelas badge, label]
$gayaStatus = [
'draft' => ['bg-slate-100 text-slate-600', 'Draft'],
'terdaftar' => ['bg-sky-100 text-sky-700', 'Terdaftar'],
'diverifikasi' => ['bg-teal-100 text-teal-700', 'Diverifikasi'],
'diterima' => ['bg-brand-100 text-brand-700', 'Diterima'],
'ditolak' => ['bg-red-100 text-red-700', 'Ditolak'],
'daftar_ulang' => ['bg-violet-100 text-violet-700', 'Daftar Ulang'],
];

$badgeStatus = fn ($s) => $gayaStatus[$s] ?? ['bg-slate-100 text-slate-600', ucfirst(str_replace('_', ' ', (string) $s))];

// Pilihan status untuk filter cepat & ubah status
$pilihanStatus = [
'terdaftar' => 'Terdaftar',
'diverifikasi' => 'Diverifikasi',
'diterima' => 'Diterima',
'ditolak' => 'Ditolak',
'daftar_ulang' => 'Daftar Ulang',
];

$gayaDokumen = [
'menunggu' => 'bg-amber-100 text-amber-700',
'diterima' => 'bg-brand-100 text-brand-700',
'ditolak' => 'bg-red-100 text-red-700',
];

$namaJk = fn ($jk) => match ($jk) {
'L' => 'Laki-laki',
'P' => 'Perempuan',
default => $jk ?: '-',
};

$fmtTgl = fn ($t) => $t ? \Carbon\Carbon::parse($t)->translatedFormat('d F Y') : '-';
@endphp

{{-- Livewire: elemen root harus satu --}}
<div class="space-y-6">

    {{-- ================= HEADER ================= --}}
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-500 to-brand-600 p-6 text-white shadow-lg shadow-brand-500/20 sm:p-8">
        <div class="pointer-events-none absolute -right-12 -top-12 h-48 w-48 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute -bottom-16 right-1/3 h-40 w-40 rounded-full bg-white/10"></div>

        <div class="relative">
            <h1 class="text-2xl font-extrabold sm:text-3xl">Data Pendaftar</h1>
            <p class="mt-2 max-w-md text-sm text-brand-50">
                Kelola dan pantau calon peserta didik baru.
            </p>

            <span class="mt-4 inline-flex items-center gap-2 rounded-full bg-white/15 px-3.5 py-1.5 text-xs font-medium backdrop-blur">
                <i class="fa-solid fa-users"></i>
                {{ $pendaftars->total() }} pendaftar
            </span>
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

    {{-- ================= FILTER ================= --}}
    <div class="space-y-4 rounded-2xl border border-brand-100 bg-white p-4 shadow-sm sm:p-5">
        <div class="flex flex-col gap-3 lg:flex-row">
            <div class="relative lg:flex-1">
                <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-sm text-brand-400"></i>

                <input type="text" wire:model.live.debounce.300ms="search"
                    placeholder="Cari nama atau nomor pendaftaran..."
                    class="w-full rounded-xl border border-brand-100 bg-brand-50/50 py-3 pl-11 pr-11 text-sm outline-none transition
                           placeholder:text-slate-400 focus:border-brand-300 focus:bg-white focus:ring-2 focus:ring-brand-200">

                <i wire:loading wire:target="search,filterPeriode,filterStatus"
                    class="fa-solid fa-circle-notch fa-spin absolute right-4 top-1/2 -translate-y-1/2 text-sm text-brand-500"></i>
            </div>

            <select wire:model.live="filterPeriode"
                class="w-full rounded-xl border border-brand-100 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition
                       focus:border-brand-300 focus:ring-2 focus:ring-brand-200 lg:w-56">
                <option value="">Semua Periode</option>
                @foreach ($periodes as $periode)
                <option value="{{ $periode->id }}">{{ $periode->nama }}</option>
                @endforeach
            </select>
        </div>

        {{-- Filter status cepat --}}
        <div class="-mx-1 flex gap-2 overflow-x-auto px-1 pb-1">
            <button type="button" wire:click="$set('filterStatus', '')"
                class="shrink-0 rounded-full px-4 py-1.5 text-xs font-semibold transition
                    {{ ($filterStatus ?? '') === '' ? 'bg-brand-500 text-white shadow-sm shadow-brand-500/30' : 'bg-brand-50 text-brand-700 hover:bg-brand-100' }}">
                Semua Status
            </button>

            @foreach ($pilihanStatus as $nilai => $label)
            <button type="button" wire:click="$set('filterStatus', '{{ $nilai }}')"
                class="shrink-0 rounded-full px-4 py-1.5 text-xs font-semibold transition
                        {{ ($filterStatus ?? '') === $nilai ? 'bg-brand-500 text-white shadow-sm shadow-brand-500/30' : 'bg-brand-50 text-brand-700 hover:bg-brand-100' }}">
                {{ $label }}
            </button>
            @endforeach
        </div>
    </div>

    {{-- ================= TABEL PENDAFTAR ================= --}}
    <div class="overflow-hidden rounded-2xl border border-brand-100 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-brand-50 text-xs uppercase tracking-wide text-brand-700">
                    <tr>
                        <th class="px-4 py-3.5">No</th>
                        <th class="px-4 py-3.5">Pendaftar</th>
                        <th class="px-4 py-3.5">Asal Sekolah</th>
                        <th class="px-4 py-3.5">Periode</th>
                        <th class="px-4 py-3.5">Status</th>
                        <th class="px-4 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-brand-50">
                    @forelse ($pendaftars as $index => $pendaftar)
                    @php [$kelasStatus, $labelStatus] = $badgeStatus($pendaftar->status); @endphp

                    <tr wire:key="pendaftar-{{ $pendaftar->id }}" class="transition hover:bg-brand-50/40">
                        <td class="px-4 py-4 text-slate-400">{{ $pendaftars->firstItem() + $index }}</td>

                        <td class="px-4 py-4">
                            <div class="flex items-center gap-3">
                                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-brand-100 text-sm font-extrabold text-brand-700">
                                    {{ strtoupper(mb_substr($pendaftar->nama_lengkap, 0, 1)) }}
                                </span>
                                <div class="min-w-0">
                                    <p class="truncate font-semibold text-slate-800">{{ $pendaftar->nama_lengkap }}</p>
                                    <p class="font-mono text-xs text-brand-600">{{ $pendaftar->nomor_pendaftaran }}</p>
                                </div>
                            </div>
                        </td>

                        <td class="px-4 py-4 text-slate-600">{{ $pendaftar->asal_sekolah ?? '-' }}</td>
                        <td class="px-4 py-4 text-slate-600">{{ $pendaftar->periode->nama ?? '-' }}</td>

                        <td class="px-4 py-4">
                            <span class="inline-block rounded-full px-3 py-1 text-xs font-semibold {{ $kelasStatus }}">{{ $labelStatus }}</span>
                        </td>

                        <td class="px-4 py-4">
                            <div class="flex items-center justify-center">
                                <button type="button" wire:click="lihatDetail({{ $pendaftar->id }})"
                                    class="inline-flex items-center gap-2 rounded-xl border border-brand-200 bg-white px-3.5 py-2 text-xs font-semibold text-brand-700 transition hover:bg-brand-50">
                                    <i class="fa-solid fa-eye"></i>
                                    Detail
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center">
                            <span class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-brand-100 text-2xl text-brand-500">
                                <i class="fa-solid fa-user-slash"></i>
                            </span>
                            <p class="mt-4 font-bold text-brand-900">Data pendaftar tidak ditemukan</p>
                            <p class="mt-1 text-sm text-slate-500">Coba ubah kata kunci atau filter yang dipakai.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>{{ $pendaftars->links() }}</div>

    {{-- ================= MODAL DETAIL PENDAFTAR ================= --}}
    @if ($pendaftarDetail)
    @php [$kelasStatusD, $labelStatusD] = $badgeStatus($pendaftarDetail->status); @endphp

    <div class="fixed inset-0 z-[60] flex items-end justify-center sm:items-center sm:p-4"
        x-data="{ show: false }" x-init="$nextTick(() => show = true)"
        x-on:keydown.escape.window="$wire.tutupDetail()"
        role="dialog" aria-modal="true">

        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity duration-200"
            :class="show ? 'opacity-100' : 'opacity-0'"
            wire:click="tutupDetail"></div>

        <div class="relative z-10 flex max-h-[92vh] w-full max-w-3xl flex-col overflow-hidden rounded-t-3xl bg-white shadow-2xl transition duration-200 sm:rounded-3xl"
            :class="show ? 'translate-y-0 opacity-100' : 'translate-y-4 opacity-0'">

            {{-- Header --}}
            <div class="flex shrink-0 items-center justify-between gap-3 bg-gradient-to-br from-brand-500 to-brand-600 px-6 py-5 text-white">
                <div class="flex min-w-0 items-center gap-4">
                    <span class="grid h-14 w-14 shrink-0 place-items-center rounded-2xl bg-white text-xl font-extrabold text-brand-700 shadow-md">
                        {{ strtoupper(mb_substr($pendaftarDetail->nama_lengkap, 0, 1)) }}
                    </span>

                    <div class="min-w-0">
                        <p class="text-xs font-semibold uppercase tracking-wide text-brand-100">Detail Pendaftar</p>
                        <h2 class="truncate text-xl font-extrabold">{{ $pendaftarDetail->nama_lengkap }}</h2>
                        <p class="font-mono text-sm text-brand-50">{{ $pendaftarDetail->nomor_pendaftaran }}</p>
                    </div>
                </div>

                <div class="flex shrink-0 items-center gap-3">
                    <span class="hidden rounded-full bg-white px-3 py-1 text-xs font-bold text-brand-700 sm:inline-block">{{ $labelStatusD }}</span>

                    <button type="button" wire:click="tutupDetail" aria-label="Tutup"
                        class="grid h-9 w-9 place-items-center rounded-lg text-white/80 transition hover:bg-white/15 hover:text-white">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            </div>

            {{-- Isi --}}
            <div class="space-y-6 overflow-y-auto p-6">

                {{-- Data pribadi --}}
                <section>
                    <h3 class="mb-3 flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-brand-700">
                        <i class="fa-solid fa-user text-brand-500"></i> Data Pribadi
                    </h3>

                    <dl class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        @foreach ([
                        'NIK' => $pendaftarDetail->nik ?? '-',
                        'NISN' => $pendaftarDetail->nisn ?? '-',
                        'Jenis Kelamin' => $namaJk($pendaftarDetail->jenis_kelamin),
                        'Tempat, Tanggal Lahir' => ($pendaftarDetail->tempat_lahir ?? '-') . ', ' . $fmtTgl($pendaftarDetail->tanggal_lahir),
                        'Asal Sekolah' => $pendaftarDetail->asal_sekolah ?? '-',
                        'Alamat' => $pendaftarDetail->alamat ?? '-',
                        ] as $label => $nilai)
                        <div class="rounded-xl border border-brand-100 bg-brand-50/30 px-4 py-3 {{ $label === 'Alamat' ? 'sm:col-span-2' : '' }}">
                            <dt class="text-xs text-slate-400">{{ $label }}</dt>
                            <dd class="mt-0.5 break-words text-sm font-semibold text-slate-800">{{ $nilai ?: '-' }}</dd>
                        </div>
                        @endforeach
                    </dl>
                </section>

                {{-- Kontak & orang tua --}}
                <section>
                    <h3 class="mb-3 flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-brand-700">
                        <i class="fa-solid fa-address-book text-brand-500"></i> Kontak &amp; Orang Tua
                    </h3>

                    <dl class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        @foreach ([
                        'Nomor HP' => $pendaftarDetail->no_hp ?? '-',
                        'Email' => $pendaftarDetail->email ?? '-',
                        'Nama Ayah' => $pendaftarDetail->nama_ayah ?? '-',
                        'Nama Ibu' => $pendaftarDetail->nama_ibu ?? '-',
                        ] as $label => $nilai)
                        <div class="rounded-xl border border-brand-100 bg-brand-50/30 px-4 py-3">
                            <dt class="text-xs text-slate-400">{{ $label }}</dt>
                            <dd class="mt-0.5 break-words text-sm font-semibold text-slate-800">{{ $nilai ?: '-' }}</dd>
                        </div>
                        @endforeach
                    </dl>
                </section>

                {{-- Status pendaftaran --}}
                <section>
                    <h3 class="mb-3 flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-brand-700">
                        <i class="fa-solid fa-flag text-brand-500"></i> Status Pendaftaran
                    </h3>

                    <div class="flex flex-wrap gap-2">
                        @foreach ($pilihanStatus as $nilai => $label)
                        @php $aktif = $pendaftarDetail->status === $nilai; @endphp

                        <button type="button"
                            wire:click="ubahStatus({{ $pendaftarDetail->id }}, '{{ $nilai }}')"
                            wire:loading.attr="disabled" wire:target="ubahStatus"
                            @disabled($aktif)
                            class="inline-flex items-center gap-2 rounded-xl border-2 px-4 py-2 text-sm font-semibold transition disabled:cursor-default
                                        {{ $aktif
                                            ? 'border-brand-500 bg-brand-50 text-brand-700'
                                            : 'border-slate-200 bg-white text-slate-500 hover:border-brand-300 hover:text-brand-700' }}">
                            @if ($aktif)
                            <i class="fa-solid fa-circle-check text-brand-500"></i>
                            @endif
                            {{ $label }}
                        </button>
                        @endforeach
                    </div>
                </section>

                {{-- Dokumen --}}
                <section>
                    <div class="mb-3 flex items-center justify-between gap-3">
                        <h3 class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-brand-700">
                            <i class="fa-solid fa-folder-open text-brand-500"></i> Dokumen yang Diunggah
                        </h3>
                        <span class="rounded-full bg-brand-100 px-2.5 py-0.5 text-xs font-bold text-brand-700">{{ $pendaftarDetail->dokumens->count() }}</span>
                    </div>

                    <div class="space-y-2">
                        @forelse ($pendaftarDetail->dokumens as $dokumen)
                        <div class="flex items-center justify-between gap-3 rounded-xl border border-brand-100 p-3">
                            <div class="flex min-w-0 items-center gap-3">
                                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-brand-100 text-brand-600">
                                    <i class="fa-solid fa-file-lines"></i>
                                </span>
                                <span class="truncate text-sm font-medium text-slate-700">
                                    {{ ucfirst(str_replace('_', ' ', $dokumen->jenis_dokumen)) }}
                                </span>
                            </div>

                            <div class="flex shrink-0 items-center gap-2">
                                <span class="rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $gayaDokumen[$dokumen->status_verifikasi] ?? 'bg-slate-100 text-slate-600' }}">
                                    {{ ucfirst($dokumen->status_verifikasi) }}
                                </span>

                                @if (filled($dokumen->file_path ?? null))
                                <a href="{{ Storage::disk('public')->url($dokumen->file_path) }}" target="_blank"
                                    class="grid h-8 w-8 place-items-center rounded-lg bg-brand-50 text-xs text-brand-700 transition hover:bg-brand-100"
                                    title="Lihat dokumen" aria-label="Lihat dokumen">
                                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                </a>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div class="rounded-xl border border-dashed border-brand-200 bg-brand-50/30 px-4 py-8 text-center text-sm text-slate-400">
                            Belum ada dokumen.
                        </div>
                        @endforelse
                    </div>
                </section>
            </div>

            {{-- Footer --}}
            <div class="flex shrink-0 justify-end border-t border-brand-100 bg-white px-6 py-4">
                <button type="button" wire:click="tutupDetail"
                    class="rounded-xl border border-brand-200 px-5 py-2 text-sm font-semibold text-slate-600 transition hover:bg-brand-50">
                    Tutup
                </button>
            </div>
        </div>
    </div>
    @endif
</div>