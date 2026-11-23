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
@endphp

{{-- Livewire: elemen root harus satu --}}
<div class="space-y-6">

    {{-- ================= HEADER ================= --}}
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-500 to-brand-600 p-6 text-white shadow-lg shadow-brand-500/20 sm:p-8">
        <div class="pointer-events-none absolute -right-12 -top-12 h-48 w-48 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute -bottom-16 right-1/3 h-40 w-40 rounded-full bg-white/10"></div>

        <div class="relative">
            <h1 class="text-2xl font-extrabold sm:text-3xl">Seleksi PPDB</h1>
            <p class="mt-2 max-w-md text-sm text-brand-50">
                Kelola hasil seleksi calon peserta didik baru.
            </p>

            <span class="mt-4 inline-flex items-center gap-2 rounded-full bg-white/15 px-3.5 py-1.5 text-xs font-medium backdrop-blur">
                <i class="fa-solid fa-user-check"></i>
                {{ $pendaftars->total() }} calon siswa
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

    {{-- ================= FILTER ================= --}}
    <div class="rounded-2xl border border-brand-100 bg-white p-4 shadow-sm sm:p-5">
        <div class="relative">
            <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-sm text-brand-400"></i>

            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Cari nama atau nomor pendaftaran..."
                class="w-full rounded-xl border border-brand-100 bg-brand-50/50 py-3 pl-11 pr-11 text-sm outline-none transition
                       placeholder:text-slate-400 focus:border-brand-300 focus:bg-white focus:ring-2 focus:ring-brand-200">

            <i wire:loading wire:target="search"
                class="fa-solid fa-circle-notch fa-spin absolute right-4 top-1/2 -translate-y-1/2 text-sm text-brand-500"></i>
        </div>
    </div>

    {{-- ================= TABEL SELEKSI ================= --}}
    <div class="overflow-hidden rounded-2xl border border-brand-100 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-brand-50 text-xs uppercase tracking-wide text-brand-700">
                    <tr>
                        <th class="px-4 py-3.5">No</th>
                        <th class="px-4 py-3.5">Calon Siswa</th>
                        <th class="px-4 py-3.5">Periode</th>
                        <th class="px-4 py-3.5">Status</th>
                        <th class="px-4 py-3.5 text-center">Aksi Seleksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-brand-50">
                    @forelse ($pendaftars as $index => $pendaftar)
                    @php [$kelasStatus, $labelStatus] = $badgeStatus($pendaftar->status); @endphp

                    <tr wire:key="seleksi-{{ $pendaftar->id }}" class="transition hover:bg-brand-50/40">
                        <td class="px-4 py-4 text-slate-400">
                            {{ $pendaftars->firstItem() + $index }}
                        </td>

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

                        <td class="px-4 py-4 text-slate-600">
                            {{ $pendaftar->periode->nama ?? '-' }}
                        </td>

                        <td class="px-4 py-4">
                            <span class="inline-block rounded-full px-3 py-1 text-xs font-semibold {{ $kelasStatus }}">
                                {{ $labelStatus }}
                            </span>
                        </td>

                        <td class="whitespace-nowrap px-4 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <button
                                    type="button"
                                    wire:click="bukaKonfirmasi({{ $pendaftar->id }}, 'terima')"
                                    class="inline-flex items-center gap-1.5 rounded-xl bg-brand-600 px-3.5 py-2 text-xs font-semibold text-white shadow-sm shadow-brand-500/20 transition hover:bg-brand-700">
                                    <i class="fa-solid fa-check text-[11px]"></i>
                                    Terima
                                </button>

                                <button
                                    type="button"
                                    wire:click="bukaKonfirmasi({{ $pendaftar->id }}, 'tolak')"
                                    class="inline-flex items-center gap-1.5 rounded-xl bg-red-600 px-3.5 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-red-700">
                                    <i class="fa-solid fa-xmark text-[11px]"></i>
                                    Tolak
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <span class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-brand-100 text-2xl text-brand-500">
                                <i class="fa-solid fa-clipboard-list"></i>
                            </span>
                            <p class="mt-4 font-bold text-brand-900">Tidak ada pendaftar yang menunggu seleksi</p>
                            <p class="mt-1 text-sm text-slate-500">Calon peserta didik yang siap diseleksi akan tampil di sini.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>{{ $pendaftars->links() }}</div>

    {{-- ================= MODAL KONFIRMASI SELEKSI ================= --}}
    @if ($konfirmasiId && $pendaftarKonfirmasi)
    @php $isTerima = $konfirmasiAksi === 'terima'; @endphp

    <div class="fixed inset-0 z-[60] flex items-end justify-center sm:items-center sm:p-4"
        x-data="{ show: false }" x-init="$nextTick(() => show = true)"
        x-on:keydown.escape.window="$wire.tutupKonfirmasi()"
        role="dialog" aria-modal="true">

        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity duration-200"
            :class="show ? 'opacity-100' : 'opacity-0'"
            wire:click="tutupKonfirmasi"></div>

        <div class="relative z-10 flex w-full max-w-md flex-col overflow-hidden rounded-t-3xl bg-white shadow-2xl transition duration-200 sm:rounded-3xl"
            :class="show ? 'translate-y-0 opacity-100' : 'translate-y-4 opacity-0'">

            {{-- Header Modal --}}
            <div class="flex items-center justify-between gap-3 bg-gradient-to-br {{ $isTerima ? 'from-brand-500 to-brand-600' : 'from-red-500 to-red-600' }} px-6 py-5 text-white">
                <div class="flex items-center gap-3.5">
                    <span class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-white text-lg font-extrabold {{ $isTerima ? 'text-brand-600' : 'text-red-600' }} shadow-md">
                        <i class="fa-solid {{ $isTerima ? 'fa-user-check' : 'fa-user-xmark' }}"></i>
                    </span>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-white/80">Konfirmasi Seleksi</p>
                        <h2 class="text-lg font-extrabold">
                            {{ $isTerima ? 'Terima Calon Siswa' : 'Tolak Calon Siswa' }}
                        </h2>
                    </div>
                </div>

                <button type="button" wire:click="tutupKonfirmasi" aria-label="Tutup"
                    class="grid h-9 w-9 place-items-center rounded-lg text-white/80 transition hover:bg-white/15 hover:text-white">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            {{-- Isi Modal --}}
            <div class="space-y-4 p-6">
                <p class="text-sm leading-relaxed text-slate-600">
                    @if ($isTerima)
                    Apakah kamu yakin ingin menyatakan calon peserta didik berikut <span class="font-bold text-brand-700">DITERIMA</span>?
                    @else
                    Apakah kamu yakin ingin menyatakan calon peserta didik berikut <span class="font-bold text-red-600">DITOLAK</span>?
                    @endif
                </p>

                {{-- Kartu Ringkasan Calon Siswa --}}
                <div class="rounded-2xl border {{ $isTerima ? 'border-brand-100 bg-brand-50/40' : 'border-red-100 bg-red-50/40' }} p-4">
                    <div class="flex items-center gap-3">
                        <span class="grid h-11 w-11 shrink-0 place-items-center rounded-full {{ $isTerima ? 'bg-brand-100 text-brand-700' : 'bg-red-100 text-red-700' }} text-sm font-extrabold">
                            {{ strtoupper(mb_substr($pendaftarKonfirmasi->nama_lengkap, 0, 1)) }}
                        </span>
                        <div class="min-w-0">
                            <p class="truncate font-bold text-slate-800">{{ $pendaftarKonfirmasi->nama_lengkap }}</p>
                            <p class="font-mono text-xs {{ $isTerima ? 'text-brand-600' : 'text-red-600' }}">
                                {{ $pendaftarKonfirmasi->nomor_pendaftaran }}
                            </p>
                            @if ($pendaftarKonfirmasi->periode)
                            <p class="mt-0.5 text-xs text-slate-500">
                                Periode: {{ $pendaftarKonfirmasi->periode->nama }}
                            </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer Modal --}}
            <div class="flex flex-col-reverse gap-2 border-t border-slate-100 bg-white px-6 py-4 sm:flex-row sm:justify-end">
                <button type="button" wire:click="tutupKonfirmasi"
                    class="rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                    Batal
                </button>

                <button type="button"
                    wire:click="prosesKonfirmasi"
                    wire:loading.attr="disabled"
                    wire:target="prosesKonfirmasi"
                    class="inline-flex items-center justify-center gap-2 rounded-xl {{ $isTerima ? 'bg-brand-600 hover:bg-brand-700 shadow-brand-500/20' : 'bg-red-600 hover:bg-red-700 shadow-red-500/20' }} px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition disabled:cursor-not-allowed disabled:opacity-60">
                    <span wire:loading.remove wire:target="prosesKonfirmasi" class="inline-flex items-center gap-2">
                        <i class="fa-solid {{ $isTerima ? 'fa-check' : 'fa-xmark' }} text-xs"></i>
                        {{ $isTerima ? 'Ya, Terima Siswa' : 'Ya, Tolak Siswa' }}
                    </span>
                    <span wire:loading wire:target="prosesKonfirmasi" class="inline-flex items-center gap-2">
                        <i class="fa-solid fa-circle-notch fa-spin text-xs"></i>
                        Memproses...
                    </span>
                </button>
            </div>
        </div>
    </div>
    @endif

</div>