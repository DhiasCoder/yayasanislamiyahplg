@php
$input = 'w-full rounded-xl border border-brand-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-brand-400 focus:ring-2 focus:ring-brand-200';

$statusColor = [
'hadir' => 'border-brand-300 bg-brand-50 text-brand-700',
'izin' => 'border-sky-200 bg-sky-50 text-sky-700',
'sakit' => 'border-amber-200 bg-amber-50 text-amber-700',
'alpa' => 'border-red-200 bg-red-50 text-red-700',
];
@endphp

{{-- Livewire: elemen root harus satu --}}
<div class="space-y-6">

    {{-- ================= HEADER ================= --}}
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-500 to-brand-600 p-6 text-white shadow-lg shadow-brand-500/20 sm:p-8">
        <div class="pointer-events-none absolute -right-12 -top-12 h-48 w-48 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute -bottom-16 right-1/3 h-40 w-40 rounded-full bg-white/10"></div>

        <div class="relative flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-extrabold sm:text-3xl">Materi Pembelajaran &amp; Absensi</h1>
                <p class="mt-2 max-w-lg text-sm text-brand-50">
                    Kelola bahan ajar, lampiran dokumen, video pembelajaran, dan sesi absensi siswa di setiap kelas.
                </p>

                <span class="mt-4 inline-flex items-center gap-2 rounded-full bg-white/15 px-3.5 py-1.5 text-xs font-medium backdrop-blur">
                    <i class="fa-solid fa-book-open"></i>
                    {{ $materis->count() }} materi terdaftar
                </span>
            </div>

            <button type="button" wire:click="resetForm" x-data @click="$dispatch('open-materi-modal')"
                class="inline-flex shrink-0 items-center justify-center gap-2 self-start rounded-2xl bg-white px-5 py-3 text-sm font-bold text-brand-700 shadow-md transition hover:bg-brand-50 active:scale-[0.99] sm:self-auto">
                <i class="fa-solid fa-plus text-xs"></i>
                Tambah Materi
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
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="flex items-center gap-4 rounded-2xl border border-brand-100 bg-white p-5 shadow-sm">
            <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-brand-100 text-lg text-brand-600">
                <i class="fa-solid fa-book-open"></i>
            </span>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Total Materi</p>
                <p class="mt-0.5 text-2xl font-extrabold text-slate-800">{{ $materis->count() }}</p>
            </div>
        </div>

        <div class="flex items-center gap-4 rounded-2xl border border-brand-100 bg-white p-5 shadow-sm">
            <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-emerald-100 text-lg text-emerald-600">
                <i class="fa-solid fa-earth-asia"></i>
            </span>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Materi Dipublikasikan</p>
                <p class="mt-0.5 text-2xl font-extrabold text-emerald-600">{{ $materis->where('is_active', true)->count() }}</p>
            </div>
        </div>

        <div class="flex items-center gap-4 rounded-2xl border border-brand-100 bg-white p-5 shadow-sm">
            <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-slate-100 text-lg text-slate-500">
                <i class="fa-solid fa-pen-ruler"></i>
            </span>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Materi Draft</p>
                <p class="mt-0.5 text-2xl font-extrabold text-slate-600">{{ $materis->where('is_active', false)->count() }}</p>
            </div>
        </div>
    </div>

    {{-- ================= DAFTAR MATERI ================= --}}
    <div class="rounded-2xl border border-brand-100 bg-white p-5 shadow-sm sm:p-6">
        <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg font-extrabold text-slate-800">Daftar Materi</h2>
                <p class="mt-0.5 text-xs text-slate-500 sm:text-sm">Materi yang dikelola untuk setiap kelas pengajaran.</p>
            </div>

            <div class="relative w-full sm:max-w-xs">
                <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-sm text-brand-400"></i>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari judul materi..."
                    class="w-full rounded-xl border border-brand-100 bg-brand-50/50 py-2.5 pl-11 pr-10 text-sm text-slate-700 outline-none transition
                           placeholder:text-slate-400 focus:border-brand-300 focus:bg-white focus:ring-2 focus:ring-brand-200">
                <i wire:loading wire:target="search"
                    class="fa-solid fa-circle-notch fa-spin absolute right-3.5 top-1/2 -translate-y-1/2 text-sm text-brand-500"></i>
            </div>
        </div>

        <div wire:loading.class="pointer-events-none opacity-60"
            class="grid grid-cols-1 gap-5 transition-opacity duration-200 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($materis as $materi)
            @php
            $ext = filled($materi->file_path) ? strtoupper(pathinfo($materi->file_path, PATHINFO_EXTENSION)) : null;
            $sesi = $materi->absensi;
            @endphp

            <article wire:key="materi-card-{{ $materi->id }}"
                class="group flex flex-col overflow-hidden rounded-2xl border border-brand-100 bg-white shadow-sm transition duration-200 hover:-translate-y-1 hover:border-brand-200 hover:shadow-md">

                {{-- Header kartu --}}
                <div class="flex items-start justify-between gap-3 border-b border-brand-100 bg-gradient-to-r from-brand-50/80 to-white p-5">
                    <div class="flex min-w-0 items-start gap-3">
                        <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-brand-100 text-lg text-brand-700 transition-colors duration-200 group-hover:bg-brand-600 group-hover:text-white">
                            <i class="fa-solid {{ $materi->file_path ? 'fa-file-lines' : 'fa-book' }}"></i>
                        </span>

                        <div class="min-w-0">
                            <h3 class="break-words font-extrabold leading-snug text-slate-800">{{ $materi->judul }}</h3>
                            <p class="mt-1 flex items-center gap-1.5 text-xs font-medium text-brand-600">
                                <i class="fa-solid fa-school text-brand-500"></i>
                                {{ $materi->pengajaran?->kelas?->nama_kelas ?? 'Kelas belum tersedia' }}
                            </p>
                        </div>
                    </div>

                    @if ($materi->is_active)
                    <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-brand-100 px-2.5 py-1 text-xs font-semibold text-brand-700">
                        <span class="h-1.5 w-1.5 rounded-full bg-brand-600"></span>
                        Publik
                    </span>
                    @else
                    <span class="shrink-0 rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">Draft</span>
                    @endif
                </div>

                {{-- Isi kartu --}}
                <div class="flex-1 space-y-4 p-5">
                    <p class="line-clamp-3 min-h-[3.5rem] text-sm leading-6 text-slate-600">
                        {{ $materi->deskripsi ?: 'Tidak ada deskripsi materi.' }}
                    </p>

                    <div class="flex flex-wrap items-center gap-2 text-xs">
                        @if ($materi->file_path)
                        <a href="{{ asset('storage/' . $materi->file_path) }}" target="_blank" rel="noopener noreferrer"
                            class="inline-flex items-center gap-1.5 rounded-xl border border-sky-200 bg-sky-50 px-3 py-1.5 font-semibold text-sky-700 transition hover:bg-sky-100">
                            <i class="fa-solid fa-file-arrow-down text-[11px]"></i>
                            Dokumen {{ $ext ? '(' . $ext . ')' : '' }}
                        </a>
                        @else
                        <span class="inline-flex items-center gap-1.5 rounded-xl bg-slate-100 px-3 py-1.5 font-medium text-slate-400">
                            <i class="fa-regular fa-file"></i> Belum ada file
                        </span>
                        @endif

                        @if ($materi->video_url)
                        <a href="{{ $materi->video_url }}" target="_blank" rel="noopener noreferrer"
                            class="inline-flex items-center gap-1.5 rounded-xl border border-red-200 bg-red-50 px-3 py-1.5 font-semibold text-red-600 transition hover:bg-red-100">
                            <i class="fa-solid fa-circle-play text-[11px]"></i>
                            Video
                        </a>
                        @endif
                    </div>

                    {{-- Status sesi absensi --}}
                    <div class="flex items-center gap-2 rounded-xl border border-brand-100/80 bg-brand-50/50 px-3.5 py-2.5 text-xs">
                        @if ($sesi && $sesi->status === 'dibuka')
                        <span class="relative flex h-2.5 w-2.5">
                            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-brand-400 opacity-75"></span>
                            <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-brand-500"></span>
                        </span>
                        <span class="font-semibold text-brand-700">Absensi sedang dibuka</span>
                        @elseif ($sesi)
                        <span class="h-2.5 w-2.5 rounded-full bg-slate-400"></span>
                        <span class="font-medium text-slate-500">Absensi ditutup</span>
                        @else
                        <span class="h-2.5 w-2.5 rounded-full bg-slate-300"></span>
                        <span class="text-slate-400">Belum ada sesi absensi</span>
                        @endif
                    </div>
                </div>

                {{-- Aksi --}}
                <div class="flex flex-wrap items-center gap-2 border-t border-brand-50 bg-slate-50/60 p-4">
                    <button type="button" wire:click="editMateri({{ $materi->id }})" x-data @click="$dispatch('open-materi-modal')"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-brand-200 bg-white px-3 py-1.5 text-xs font-semibold text-brand-700 transition hover:bg-brand-50">
                        <i class="fa-solid fa-pen-to-square text-[11px]"></i> Edit
                    </button>

                    @if ($sesi)
                    <button type="button" wire:click="pilihAbsensi({{ $sesi->id }})"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-brand-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm shadow-brand-500/20 transition hover:bg-brand-700">
                        <i class="fa-solid fa-clipboard-user text-[11px]"></i> Kelola Absensi
                    </button>
                    @else
                    <button type="button" wire:click="bukaModalAbsensi({{ $materi->id }})"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-brand-300 bg-brand-100 px-3 py-1.5 text-xs font-semibold text-brand-700 transition hover:bg-brand-200">
                        <i class="fa-solid fa-plus text-[11px]"></i> Atur &amp; Buka Absensi
                    </button>
                    @endif

                    <a href="{{ route('guru.diskusi', $materi->id) }}"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-sky-200 bg-sky-50/70 px-3 py-1.5 text-xs font-semibold text-sky-700 transition hover:bg-sky-100">
                        <i class="fa-solid fa-comments text-[11px]"></i> Diskusi
                    </a>

                    <button type="button" wire:click="hapusMateri({{ $materi->id }})"
                        wire:confirm="Yakin ingin menghapus materi ini beserta sesi absensinya?"
                        class="ml-auto inline-flex items-center gap-1.5 rounded-xl border border-red-200 bg-white px-3 py-1.5 text-xs font-semibold text-red-600 transition hover:bg-red-50">
                        <i class="fa-solid fa-trash-can text-[11px]"></i> Hapus
                    </button>
                </div>
            </article>
            @empty
            <div class="col-span-full rounded-2xl border border-dashed border-brand-200 bg-brand-50/30 px-6 py-16 text-center">
                <span class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-brand-100 text-2xl text-brand-500">
                    <i class="fa-regular fa-folder-open"></i>
                </span>
                <h3 class="mt-4 font-bold text-brand-900">
                    {{ filled($search ?? null) ? 'Materi tidak ditemukan' : 'Belum ada materi' }}
                </h3>
                <p class="mt-1 text-sm text-slate-500">
                    {{ filled($search ?? null) ? 'Coba gunakan kata kunci pencarian lain.' : 'Tambahkan materi pembelajaran untuk kelas yang Anda ajar.' }}
                </p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- ================= PANEL ABSENSI ================= --}}
    @if ($absensi)
    @php
    $rekap = $absensi->siswaAbsensi->countBy('status');
    $dibuka = $absensi->status === 'dibuka';
    @endphp

    <div class="overflow-hidden rounded-2xl border border-brand-100 bg-white shadow-sm">
        {{-- Header panel --}}
        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-brand-100 bg-gradient-to-r from-brand-50 to-white p-5 sm:p-6">
            <div class="flex items-start gap-3.5">
                <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-gradient-to-br from-brand-500 to-brand-600 text-lg text-white shadow-md shadow-brand-500/20">
                    <i class="fa-solid fa-clipboard-user"></i>
                </span>

                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-lg font-extrabold text-slate-800">Absensi: {{ $absensi->materi?->judul ?? 'Pertemuan' }}</h2>
                        @if ($dibuka)
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-brand-600 px-3 py-0.5 text-[11px] font-semibold text-white">
                            <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-white"></span> Dibuka
                        </span>
                        @else
                        <span class="rounded-full bg-slate-200 px-3 py-0.5 text-[11px] font-semibold text-slate-600">Ditutup</span>
                        @endif
                    </div>

                    <p class="mt-1 flex flex-wrap items-center gap-2 text-xs font-medium text-slate-500 sm:text-sm">
                        <span class="inline-flex items-center gap-1.5 text-brand-700">
                            <i class="fa-solid fa-door-open text-xs"></i>
                            {{ $absensi->pengajaran?->kelas?->nama_kelas ?? '-' }}
                        </span>
                        <span class="text-slate-300">·</span>
                        <span class="inline-flex items-center gap-1.5">
                            <i class="fa-regular fa-calendar text-brand-500 text-xs"></i>
                            {{ $absensi->tanggal ? \Carbon\Carbon::parse($absensi->tanggal)->translatedFormat('d M Y') : '-' }}
                        </span>
                    </p>
                </div>
            </div>

            <button type="button" wire:click="ubahStatusAbsensi" wire:confirm="Yakin ingin mengubah status sesi absensi?"
                class="inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition
                           {{ $dibuka ? 'bg-red-600 shadow-red-500/20 hover:bg-red-700' : 'bg-brand-600 shadow-brand-500/20 hover:bg-brand-700' }}">
                <i class="fa-solid {{ $dibuka ? 'fa-lock' : 'fa-lock-open' }} text-xs"></i>
                {{ $dibuka ? 'Tutup Absensi' : 'Buka Absensi' }}
            </button>
        </div>

        <div class="p-5 sm:p-6">
            @error('absensi')
            <div class="mb-4 flex items-center gap-2 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <i class="fa-solid fa-circle-exclamation text-red-500"></i>
                <span>{{ $message }}</span>
            </div>
            @enderror

            {{-- Rekap kehadiran --}}
            <div class="mb-5 grid grid-cols-2 gap-3 sm:grid-cols-4">
                <div class="rounded-2xl border border-brand-200 bg-brand-50/70 px-4 py-3.5">
                    <p class="text-xs font-semibold uppercase tracking-wide text-brand-600">Hadir</p>
                    <p class="mt-0.5 text-2xl font-extrabold text-brand-700">{{ $rekap['hadir'] ?? 0 }}</p>
                </div>
                <div class="rounded-2xl border border-sky-200 bg-sky-50/70 px-4 py-3.5">
                    <p class="text-xs font-semibold uppercase tracking-wide text-sky-600">Izin</p>
                    <p class="mt-0.5 text-2xl font-extrabold text-sky-700">{{ $rekap['izin'] ?? 0 }}</p>
                </div>
                <div class="rounded-2xl border border-amber-200 bg-amber-50/70 px-4 py-3.5">
                    <p class="text-xs font-semibold uppercase tracking-wide text-amber-600">Sakit</p>
                    <p class="mt-0.5 text-2xl font-extrabold text-amber-700">{{ $rekap['sakit'] ?? 0 }}</p>
                </div>
                <div class="rounded-2xl border border-red-200 bg-red-50/70 px-4 py-3.5">
                    <p class="text-xs font-semibold uppercase tracking-wide text-red-600">Alpa</p>
                    <p class="mt-0.5 text-2xl font-extrabold text-red-700">{{ $rekap['alpa'] ?? 0 }}</p>
                </div>
            </div>

            {{-- Tabel siswa --}}
            <div class="overflow-hidden rounded-2xl border border-brand-100">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[540px] text-left text-sm">
                        <thead class="bg-brand-50 text-xs uppercase tracking-wide text-brand-700">
                            <tr>
                                <th class="w-16 px-5 py-3.5">No</th>
                                <th class="px-5 py-3.5">Nama Siswa</th>
                                <th class="px-5 py-3.5">Status Kehadiran</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-brand-50">
                            @forelse ($absensi->siswaAbsensi as $item)
                            @php
                            $namaSiswa = $item->siswa?->nama_lengkap ?? $item->siswa?->user?->name ?? 'Siswa';
                            @endphp

                            <tr wire:key="absensi-siswa-{{ $item->id }}" class="transition hover:bg-brand-50/40">
                                <td class="whitespace-nowrap px-5 py-3.5 text-slate-400">{{ $loop->iteration }}</td>
                                <td class="whitespace-nowrap px-5 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($namaSiswa) }}&background=e1f4e6&color=23653c&bold=true"
                                            alt="{{ $namaSiswa }}" class="h-9 w-9 rounded-full ring-2 ring-brand-100">
                                        <span class="font-semibold text-slate-800">{{ $namaSiswa }}</span>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-5 py-3.5">
                                    <select wire:change="ubahStatusSiswa({{ $item->id }}, $event.target.value)"
                                        @disabled(! $dibuka)
                                        class="rounded-xl border px-3.5 py-2 text-xs font-bold outline-none transition focus:ring-2 focus:ring-brand-200 disabled:cursor-not-allowed disabled:opacity-70 sm:text-sm {{ $statusColor[$item->status] ?? 'border-slate-200 bg-white text-slate-600' }}">
                                        @foreach (['hadir', 'izin', 'sakit', 'alpa'] as $status)
                                        <option value="{{ $status }}" @selected($item->status === $status)>{{ ucfirst($status) }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-6 py-12 text-center text-slate-500">
                                    <i class="fa-regular fa-face-meh mb-2 block text-2xl text-brand-300"></i>
                                    Belum ada siswa pada kelas ini.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @unless ($dibuka)
            <div class="mt-4 flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-xs text-slate-600">
                <i class="fa-solid fa-circle-info text-brand-500"></i>
                <span>Sesi ditutup. Buka kembali absensi untuk mengubah status kehadiran siswa.</span>
            </div>
            @endunless
        </div>
    </div>
    @endif

    {{-- ================= MODAL TAMBAH / EDIT MATERI ================= --}}
    <div x-data="{ open: false }"
        x-on:open-materi-modal.window="open = true"
        x-on:close-materi-modal.window="open = false"
        x-on:keydown.escape.window="open = false"
        x-show="open" x-cloak
        role="dialog" aria-modal="true"
        class="fixed inset-0 z-[60] flex items-end justify-center sm:items-center sm:p-4">

        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" @click="open = false"></div>

        <div x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            class="relative z-10 flex max-h-[92vh] w-full max-w-2xl flex-col overflow-hidden rounded-t-3xl bg-white shadow-2xl sm:rounded-3xl">

            <form wire:submit="simpanMateri" class="flex min-h-0 flex-1 flex-col overflow-hidden">
                {{-- Header Modal --}}
                <div class="flex shrink-0 items-center justify-between gap-3 bg-gradient-to-br from-brand-500 to-brand-600 px-6 py-5 text-white">
                    <div class="flex min-w-0 items-center gap-3.5">
                        <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-white text-lg font-extrabold text-brand-700 shadow-md">
                            <i class="fa-solid {{ $materiId ? 'fa-pen-to-square' : 'fa-book-medical' }}"></i>
                        </span>
                        <div class="min-w-0">
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-brand-100">
                                {{ $materiId ? 'Perbarui Bahan Ajar' : 'Bahan Ajar Baru' }}
                            </p>
                            <h2 class="truncate text-lg font-extrabold sm:text-xl">
                                {{ $materiId ? 'Edit Materi' : 'Tambah Materi Pertemuan' }}
                            </h2>
                        </div>
                    </div>

                    <button type="button" @click="open = false" aria-label="Tutup modal"
                        class="grid h-9 w-9 shrink-0 place-items-center rounded-lg text-white/80 transition hover:bg-white/15 hover:text-white">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                {{-- Body Form --}}
                <div class="space-y-4 overflow-y-auto p-6">
                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-brand-700">
                            Kelas / Pengajaran <span class="text-red-500">*</span>
                        </label>
                        <select wire:model="pengajaran_id" class="{{ $input }}">
                            <option value="">Pilih kelas</option>
                            @foreach ($this->pengajarans as $pengajaran)
                            <option value="{{ $pengajaran->id }}">{{ $pengajaran->kelas?->nama_kelas ?? 'Kelas' }}</option>
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
                            Judul Materi <span class="text-red-500">*</span>
                        </label>
                        <input wire:model="judul" type="text" placeholder="Contoh: Pertemuan 1 - Pengenalan" class="{{ $input }}">
                        @error('judul')
                        <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-brand-700">
                            Deskripsi
                        </label>
                        <textarea wire:model="deskripsi" rows="3" placeholder="Tuliskan ringkasan atau keterangan materi pembelajaran..." class="{{ $input }} resize-none"></textarea>
                        @error('deskripsi')
                        <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-brand-700">
                            Dokumen / Video
                        </label>
                        <input wire:model="file" type="file" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.mp4,.webm"
                            class="block w-full rounded-xl border border-dashed border-brand-300 bg-brand-50/40 text-sm text-slate-600 transition
                                   file:mr-4 file:cursor-pointer file:border-0 file:bg-brand-100 file:px-4 file:py-2.5 file:text-xs file:font-bold file:text-brand-700 hover:file:bg-brand-200">
                        <p class="mt-1.5 text-xs text-slate-500">Format dokumen atau video, maksimal 100 MB.</p>
                        @error('file')
                        <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </p>
                        @enderror

                        <div wire:loading wire:target="file" class="mt-2 inline-flex items-center gap-2 text-xs font-medium text-brand-600">
                            <i class="fa-solid fa-circle-notch fa-spin"></i> Mengunggah file...
                        </div>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-brand-700">
                            Tautan Video (Opsional)
                        </label>
                        <input wire:model="video_url" type="url" placeholder="https://youtube.com/..." class="{{ $input }}">
                        <p class="mt-1.5 text-xs text-slate-500">Link YouTube akan otomatis tampil sebagai pemutar di halaman siswa.</p>
                        @error('video_url')
                        <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </p>
                        @enderror
                    </div>

                    <label class="flex cursor-pointer items-start gap-3 rounded-2xl border border-brand-200 bg-brand-50/40 p-4 transition hover:bg-brand-50/80 has-[:checked]:border-brand-400 has-[:checked]:bg-brand-50">
                        <input wire:model="is_active" type="checkbox"
                            class="mt-0.5 h-4 w-4 rounded border-brand-300 text-brand-600 focus:ring-brand-200">
                        <span>
                            <span class="block text-sm font-semibold text-slate-800">Publikasikan materi</span>
                            <span class="mt-0.5 block text-xs leading-relaxed text-slate-500">
                                Materi aktif dapat langsung diakses dan dipelajari oleh siswa di kelas terkait.
                            </span>
                        </span>
                    </label>
                </div>

                {{-- Footer Modal --}}
                <div class="flex shrink-0 flex-col-reverse gap-2 border-t border-brand-100 bg-white px-6 py-4 sm:flex-row sm:justify-end">
                    <button type="button" @click="open = false"
                        class="rounded-xl border border-brand-200 px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-brand-50">
                        Batal
                    </button>

                    <button type="submit" wire:loading.attr="disabled" wire:target="simpanMateri,file"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-brand-500/20 transition hover:bg-brand-700 disabled:cursor-not-allowed disabled:opacity-60">
                        <span wire:loading.remove wire:target="simpanMateri" class="inline-flex items-center gap-2">
                            <i class="fa-solid fa-floppy-disk text-xs"></i>
                            {{ $materiId ? 'Simpan Perubahan' : 'Simpan Materi' }}
                        </span>
                        <span wire:loading wire:target="simpanMateri" class="inline-flex items-center gap-2">
                            <i class="fa-solid fa-circle-notch fa-spin text-xs"></i>
                            Menyimpan...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ================= MODAL PENGATURAN ABSENSI ================= --}}
    <div x-data="{ open: false }"
        x-on:open-absensi-modal.window="open = true"
        x-on:close-absensi-modal.window="open = false"
        x-on:keydown.escape.window="open = false"
        x-show="open" x-cloak
        role="dialog" aria-modal="true"
        class="fixed inset-0 z-[60] flex items-end justify-center sm:items-center sm:p-4">

        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" @click="open = false"></div>

        <div x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            class="relative z-10 flex w-full max-w-md flex-col overflow-hidden rounded-t-3xl bg-white shadow-2xl sm:rounded-3xl">

            <form wire:submit="simpanBukaAbsensi" class="flex flex-col">
                {{-- Header Modal --}}
                <div class="flex items-center justify-between gap-3 bg-gradient-to-br from-brand-500 to-brand-600 px-6 py-5 text-white">
                    <div class="flex items-center gap-3.5">
                        <span class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-white text-lg font-extrabold text-brand-700 shadow-md">
                            <i class="fa-solid fa-clock"></i>
                        </span>
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-brand-100">Jadwal Kehadiran</p>
                            <h2 class="text-lg font-extrabold">Pengaturan Sesi Absensi</h2>
                        </div>
                    </div>

                    <button type="button" @click="open = false" aria-label="Tutup modal"
                        class="grid h-9 w-9 place-items-center rounded-lg text-white/80 transition hover:bg-white/15 hover:text-white">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                {{-- Body Form --}}
                <div class="space-y-4 p-6">
                    <p class="text-sm leading-relaxed text-slate-600">
                        Tentukan jam mulai dan batas waktu pengisian absensi siswa untuk pertemuan ini.
                    </p>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-brand-700">
                                Jam Mulai <span class="text-red-500">*</span>
                            </label>
                            <input type="time" wire:model="jamMulai" required class="{{ $input }}">
                            @error('jamMulai')
                            <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600">
                                <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                            </p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-brand-700">
                                Jam Selesai <span class="text-red-500">*</span>
                            </label>
                            <input type="time" wire:model="jamSelesai" required class="{{ $input }}">
                            @error('jamSelesai')
                            <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600">
                                <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                            </p>
                            @enderror
                        </div>
                    </div>

                    @error('absensiMateriId')
                    <p class="flex items-center gap-1.5 text-xs font-medium text-red-600">
                        <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                    </p>
                    @enderror
                </div>

                {{-- Footer Modal --}}
                <div class="flex flex-col-reverse gap-2 border-t border-brand-100 bg-white px-6 py-4 sm:flex-row sm:justify-end">
                    <button type="button" @click="open = false"
                        class="rounded-xl border border-brand-200 px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-brand-50">
                        Batal
                    </button>

                    <button type="submit" wire:loading.attr="disabled" wire:target="simpanBukaAbsensi"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-brand-500/20 transition hover:bg-brand-700 disabled:cursor-not-allowed disabled:opacity-60">
                        <span wire:loading.remove wire:target="simpanBukaAbsensi" class="inline-flex items-center gap-2">
                            <i class="fa-solid fa-door-open text-xs"></i>
                            Buka Sesi Absensi
                        </span>
                        <span wire:loading wire:target="simpanBukaAbsensi" class="inline-flex items-center gap-2">
                            <i class="fa-solid fa-circle-notch fa-spin text-xs"></i>
                            Memproses...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>