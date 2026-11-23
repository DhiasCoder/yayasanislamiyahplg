@php
$jumlahTerpilih = count($selectedDiskusi);
@endphp

{{-- Livewire: elemen root harus satu --}}
<div class="space-y-6">

    {{-- ================= HEADER ================= --}}
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-500 to-brand-600 p-6 text-white shadow-lg shadow-brand-500/20 sm:p-8">
        <div class="pointer-events-none absolute -right-12 -top-12 h-48 w-48 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute -bottom-16 right-1/3 h-40 w-40 rounded-full bg-white/10"></div>

        <div class="relative flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-extrabold sm:text-3xl">Moderasi Diskusi Materi</h1>
                <p class="mt-2 max-w-lg text-sm text-brand-50">
                    Pantau dan kelola diskusi siswa maupun guru pada setiap materi pembelajaran.
                </p>

                <span class="mt-4 inline-flex items-center gap-2 rounded-full bg-white/15 px-3.5 py-1.5 text-xs font-medium backdrop-blur">
                    <i class="fa-solid fa-comments"></i>
                    {{ $totalDiskusi }} total diskusi tercatat
                </span>
            </div>
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
                <i class="fa-solid fa-comments"></i>
            </span>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Total Diskusi</p>
                <p class="mt-0.5 text-2xl font-extrabold text-slate-800">{{ $totalDiskusi }}</p>
            </div>
        </div>

        <div class="flex items-center gap-4 rounded-2xl border border-brand-100 bg-white p-5 shadow-sm">
            <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-sky-100 text-lg text-sky-600">
                <i class="fa-solid fa-book-open"></i>
            </span>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Materi dengan Diskusi</p>
                <p class="mt-0.5 text-2xl font-extrabold text-sky-600">{{ $totalMateriDiskusi }}</p>
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
                    placeholder="Cari isi diskusi, pengguna, atau materi..."
                    class="w-full rounded-xl border border-brand-100 bg-brand-50/50 py-3 pl-11 pr-11 text-sm outline-none transition
                           placeholder:text-slate-400 focus:border-brand-300 focus:bg-white focus:ring-2 focus:ring-brand-200">

                <i wire:loading wire:target="search"
                    class="fa-solid fa-circle-notch fa-spin absolute right-4 top-1/2 -translate-y-1/2 text-sm text-brand-500"></i>
            </div>

            <button type="button"
                wire:click="deleteSelected"
                wire:confirm="Yakin ingin menghapus semua diskusi terpilih beserta balasannya?"
                @disabled($jumlahTerpilih===0)
                class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl border border-red-200 bg-red-50/50 px-4 py-2.5 text-xs font-semibold text-red-600 transition
                       hover:bg-red-100 disabled:cursor-not-allowed disabled:border-slate-200 disabled:bg-slate-50 disabled:text-slate-400 sm:text-sm">
                <i class="fa-solid fa-trash-can text-xs"></i>
                Hapus Terpilih ({{ $jumlahTerpilih }})
            </button>
        </div>
    </div>

    {{-- ================= TABEL DISKUSI ================= --}}
    <div class="overflow-hidden rounded-2xl border border-brand-100 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[940px] text-left text-sm">
                <thead class="bg-brand-50 text-xs uppercase tracking-wide text-brand-700">
                    <tr>
                        <th class="w-11 px-4 py-3.5">
                            <input
                                type="checkbox"
                                wire:model.live="selectAll"
                                wire:change="toggleSelectAll"
                                class="h-4 w-4 rounded border-brand-300 text-brand-600 focus:ring-brand-200"
                                aria-label="Pilih semua diskusi">
                        </th>
                        <th class="px-4 py-3.5">Pengguna</th>
                        <th class="px-4 py-3.5">Materi</th>
                        <th class="px-4 py-3.5">Isi Diskusi</th>
                        <th class="px-4 py-3.5">Jenis</th>
                        <th class="px-4 py-3.5">Tanggal</th>
                        <th class="px-4 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-brand-50">
                    @forelse ($diskusis as $diskusi)
                    @php
                    $namaPengguna = $diskusi->user?->name ?? 'Pengguna tidak ditemukan';
                    @endphp

                    <tr wire:key="diskusi-{{ $diskusi->id }}" class="transition hover:bg-brand-50/40">
                        <td class="px-4 py-4">
                            <input
                                type="checkbox"
                                wire:model.live="selectedDiskusi"
                                value="{{ $diskusi->id }}"
                                class="h-4 w-4 rounded border-brand-300 text-brand-600 focus:ring-brand-200"
                                aria-label="Pilih diskusi dari {{ $namaPengguna }}">
                        </td>

                        <td class="whitespace-nowrap px-4 py-4">
                            <div class="flex items-center gap-3">
                                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-brand-100 text-sm font-extrabold text-brand-700">
                                    {{ strtoupper(mb_substr($namaPengguna, 0, 1)) }}
                                </span>
                                <div class="min-w-0">
                                    <p class="font-semibold text-slate-800">{{ $namaPengguna }}</p>
                                    <p class="font-mono text-xs text-slate-400">ID: {{ $diskusi->user_id }}</p>
                                </div>
                            </div>
                        </td>

                        <td class="px-4 py-4">
                            <p class="max-w-[200px] truncate font-semibold text-slate-800" title="{{ $diskusi->materi?->judul }}">
                                {{ $diskusi->materi?->judul ?? 'Materi tidak ditemukan' }}
                            </p>
                            <p class="mt-0.5 text-xs font-medium text-brand-600">
                                {{ $diskusi->materi?->pengajaran?->mataPelajaran?->nama ?? 'Mata pelajaran -' }}
                            </p>
                        </td>

                        <td class="px-4 py-4">
                            <p class="max-w-[260px] text-sm leading-relaxed text-slate-600">
                                {{ \Illuminate\Support\Str::limit($diskusi->isi, 100) }}
                            </p>
                        </td>

                        <td class="whitespace-nowrap px-4 py-4">
                            @if ($diskusi->parent_id)
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-sky-100 px-3 py-1 text-xs font-semibold text-sky-700">
                                <i class="fa-solid fa-reply text-[10px]"></i>
                                Balasan
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-brand-100 px-3 py-1 text-xs font-semibold text-brand-700">
                                <i class="fa-solid fa-comment-dots text-[10px]"></i>
                                Diskusi Utama
                            </span>
                            @endif
                        </td>

                        <td class="whitespace-nowrap px-4 py-4 text-xs font-medium text-slate-500">
                            {{ $diskusi->created_at?->translatedFormat('d M Y, H:i') ?? '-' }}
                        </td>

                        <td class="whitespace-nowrap px-4 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button type="button"
                                    wire:click="showDiskusi({{ $diskusi->id }})"
                                    class="inline-flex items-center gap-1.5 rounded-xl border border-brand-200 bg-white px-3 py-1.5 text-xs font-semibold text-brand-700 transition hover:bg-brand-50">
                                    <i class="fa-solid fa-eye text-[11px]"></i>
                                    Detail
                                </button>

                                <button type="button"
                                    wire:click="confirmDelete({{ $diskusi->id }})"
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
                                <i class="fa-solid fa-comments"></i>
                            </span>
                            <p class="mt-4 font-bold text-brand-900">Diskusi belum ditemukan</p>
                            <p class="mt-1 text-sm text-slate-500">Belum ada diskusi atau kata kunci pencarian tidak cocok.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>{{ $diskusis->links() }}</div>

    {{-- ================= MODAL DETAIL DISKUSI ================= --}}
    @if ($showModalDiskusi && $currentDiskusi)
    @php
    $namaPenulis = $currentDiskusi->user?->name ?? 'Pengguna tidak ditemukan';
    @endphp
    <div class="fixed inset-0 z-[60] flex items-end justify-center sm:items-center sm:p-4"
        x-data="{ show: false }" x-init="$nextTick(() => show = true)"
        x-on:keydown.escape.window="$wire.set('showModalDiskusi', false)"
        role="dialog" aria-modal="true">

        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity duration-200"
            :class="show ? 'opacity-100' : 'opacity-0'"
            wire:click="$set('showModalDiskusi', false)"></div>

        <div class="relative z-10 flex max-h-[92vh] w-full max-w-2xl flex-col overflow-hidden rounded-t-3xl bg-white shadow-2xl transition duration-200 sm:rounded-3xl"
            :class="show ? 'translate-y-0 opacity-100' : 'translate-y-4 opacity-0'"
            wire:click.stop>

            {{-- Header Modal --}}
            <div class="flex shrink-0 items-center justify-between gap-3 bg-gradient-to-br from-brand-500 to-brand-600 px-6 py-5 text-white">
                <div class="flex min-w-0 items-center gap-3.5">
                    <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-white text-lg font-extrabold text-brand-700 shadow-md">
                        <i class="fa-solid fa-comments"></i>
                    </span>
                    <div class="min-w-0">
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-brand-100">
                            {{ $currentDiskusi->parent_id ? 'Balasan Diskusi' : 'Diskusi Utama' }}
                        </p>
                        <h2 class="truncate text-lg font-extrabold sm:text-xl">
                            Detail Diskusi
                        </h2>
                    </div>
                </div>

                <button type="button" wire:click="$set('showModalDiskusi', false)" aria-label="Tutup"
                    class="grid h-9 w-9 shrink-0 place-items-center rounded-lg text-white/80 transition hover:bg-white/15 hover:text-white">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            {{-- Body Modal --}}
            <div class="space-y-4 overflow-y-auto p-6">
                {{-- Info Materi --}}
                <div class="flex items-start gap-3.5 rounded-2xl border border-brand-100 bg-brand-50/60 p-4">
                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-brand-100 text-brand-700">
                        <i class="fa-solid fa-book-open"></i>
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-brand-700">Materi Terkait</p>
                        <p class="mt-0.5 text-sm font-bold text-slate-800">
                            {{ $currentDiskusi->materi?->judul ?? 'Materi tidak ditemukan' }}
                        </p>
                        <p class="mt-1 text-xs text-slate-500">
                            {{ $currentDiskusi->materi?->pengajaran?->mataPelajaran?->nama ?? '-' }}
                            <span class="mx-1 text-slate-300">·</span>
                            Kelas {{ $currentDiskusi->materi?->pengajaran?->kelas?->nama_kelas ?? '-' }}
                        </p>
                    </div>
                </div>

                {{-- Info Pengguna --}}
                <div class="flex items-center justify-between gap-3 rounded-2xl border border-slate-100 bg-slate-50/70 p-4">
                    <div class="flex min-w-0 items-center gap-3">
                        <span class="grid h-11 w-11 shrink-0 place-items-center rounded-full bg-brand-100 text-sm font-extrabold text-brand-700">
                            {{ strtoupper(mb_substr($namaPenulis, 0, 1)) }}
                        </span>
                        <div class="min-w-0">
                            <p class="truncate text-sm font-bold text-slate-800">{{ $namaPenulis }}</p>
                            <p class="mt-0.5 flex items-center gap-1.5 text-xs text-slate-500">
                                <i class="fa-regular fa-clock text-slate-400"></i>
                                {{ $currentDiskusi->created_at?->translatedFormat('d F Y, H:i') ?? '-' }}
                            </p>
                        </div>
                    </div>

                    @if ($currentDiskusi->parent_id)
                    <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-sky-100 px-3 py-1 text-xs font-semibold text-sky-700">
                        <i class="fa-solid fa-reply text-[10px]"></i>
                        Balasan
                    </span>
                    @else
                    <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-brand-100 px-3 py-1 text-xs font-semibold text-brand-700">
                        <i class="fa-solid fa-comment-dots text-[10px]"></i>
                        Diskusi Utama
                    </span>
                    @endif
                </div>

                {{-- Isi Diskusi --}}
                <div>
                    <p class="mb-1.5 text-xs font-bold uppercase tracking-wider text-brand-700">Isi Diskusi</p>
                    <div class="whitespace-pre-line break-words rounded-2xl border border-brand-100 bg-white p-4 text-sm leading-relaxed text-slate-700">{{ $currentDiskusi->isi }}</div>
                </div>

                @if ($currentDiskusi->parent_id)
                <div class="flex items-start gap-2.5 rounded-xl border border-sky-200 bg-sky-50 p-3.5 text-xs text-sky-800">
                    <i class="fa-solid fa-reply mt-0.5 text-sky-500"></i>
                    <p>
                        Komentar ini merupakan balasan untuk diskusi utama dengan <strong class="font-mono">ID #{{ $currentDiskusi->parent_id }}</strong>.
                    </p>
                </div>
                @endif
            </div>

            {{-- Footer Modal --}}
            <div class="flex shrink-0 justify-end border-t border-brand-100 bg-white px-6 py-4">
                <button type="button" wire:click="$set('showModalDiskusi', false)"
                    class="rounded-xl border border-brand-200 px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-brand-50">
                    Tutup
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ================= MODAL KONFIRMASI HAPUS DISKUSI ================= --}}
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
                        <h2 class="text-lg font-extrabold">Hapus Diskusi?</h2>
                    </div>
                </div>

                <button type="button" wire:click="$set('confirmingDelete', false)" aria-label="Tutup"
                    class="grid h-9 w-9 place-items-center rounded-lg text-white/80 transition hover:bg-white/15 hover:text-white">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="space-y-3 p-6">
                <p class="text-sm leading-relaxed text-slate-600">
                    Apakah Anda yakin ingin menghapus diskusi ini?
                </p>
                <div class="flex items-start gap-2.5 rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs text-amber-800">
                    <i class="fa-solid fa-triangle-exclamation mt-0.5 text-amber-500"></i>
                    <p>Diskusi yang dihapus beserta <strong>seluruh balasannya</strong> tidak dapat dikembalikan.</p>
                </div>
            </div>

            <div class="flex flex-col-reverse gap-2 border-t border-slate-100 bg-white px-6 py-4 sm:flex-row sm:justify-end">
                <button type="button" wire:click="$set('confirmingDelete', false)"
                    class="rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                    Batal
                </button>

                <button type="button" wire:click="deleteDiskusiConfirmed" wire:loading.attr="disabled" wire:target="deleteDiskusiConfirmed"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-red-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-red-500/20 transition hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-60">
                    <span wire:loading.remove wire:target="deleteDiskusiConfirmed" class="inline-flex items-center gap-2">
                        <i class="fa-solid fa-trash-can text-xs"></i>
                        Ya, Hapus
                    </span>
                    <span wire:loading wire:target="deleteDiskusiConfirmed" class="inline-flex items-center gap-2">
                        <i class="fa-solid fa-circle-notch fa-spin text-xs"></i>
                        Menghapus...
                    </span>
                </button>
            </div>
        </div>
    </div>
    @endif

</div>