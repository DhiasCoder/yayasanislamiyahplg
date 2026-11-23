@php
// Gaya status pembayaran: [kelas badge, label]
$gayaStatus = [
'menunggu' => ['bg-amber-100 text-amber-700', 'Menunggu'],
'diverifikasi' => ['bg-brand-100 text-brand-700', 'Diverifikasi'],
'ditolak' => ['bg-red-100 text-red-700', 'Ditolak'],
];

$badgeStatus = fn ($s) => $gayaStatus[$s] ?? ['bg-slate-100 text-slate-600', ucfirst(str_replace('_', ' ', (string) $s))];

// Pilihan status untuk filter cepat
$pilihanStatus = [
'menunggu' => 'Menunggu',
'diverifikasi' => 'Diverifikasi',
'ditolak' => 'Ditolak',
];

$fmtTgl = fn ($t) => $t ? \Carbon\Carbon::parse($t)->translatedFormat('d M Y, H:i') : '-';
$fmtRupiah = fn ($n) => 'Rp ' . number_format((float) $n, 0, ',', '.');

$pembayaranTerpilih = $pembayaranId
? collect($pembayarans->items())->firstWhere('id', (int) $pembayaranId)
: null;
@endphp

{{-- Livewire: elemen root harus satu --}}
<div class="space-y-6">

    {{-- ================= HEADER ================= --}}
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-500 to-brand-600 p-6 text-white shadow-lg shadow-brand-500/20 sm:p-8">
        <div class="pointer-events-none absolute -right-12 -top-12 h-48 w-48 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute -bottom-16 right-1/3 h-40 w-40 rounded-full bg-white/10"></div>

        <div class="relative">
            <h1 class="text-2xl font-extrabold sm:text-3xl">Verifikasi Pembayaran</h1>
            <p class="mt-2 max-w-md text-sm text-brand-50">
                Kelola dan verifikasi pembayaran pendaftaran peserta didik baru.
            </p>

            <span class="mt-4 inline-flex items-center gap-2 rounded-full bg-white/15 px-3.5 py-1.5 text-xs font-medium backdrop-blur">
                <i class="fa-solid fa-money-bill-wave"></i>
                {{ $pembayarans->total() }} pembayaran
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
        <div class="relative">
            <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-sm text-brand-400"></i>

            <input type="text" wire:model.live.debounce.300ms="search"
                placeholder="Cari nama atau nomor pendaftaran..."
                class="w-full rounded-xl border border-brand-100 bg-brand-50/50 py-3 pl-11 pr-11 text-sm outline-none transition
                       placeholder:text-slate-400 focus:border-brand-300 focus:bg-white focus:ring-2 focus:ring-brand-200">

            <i wire:loading wire:target="search,filterStatus"
                class="fa-solid fa-circle-notch fa-spin absolute right-4 top-1/2 -translate-y-1/2 text-sm text-brand-500"></i>
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

    {{-- ================= TABEL PEMBAYARAN ================= --}}
    <div class="overflow-hidden rounded-2xl border border-brand-100 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-brand-50 text-xs uppercase tracking-wide text-brand-700">
                    <tr>
                        <th class="px-4 py-3.5">No</th>
                        <th class="px-4 py-3.5">Pendaftar</th>
                        <th class="px-4 py-3.5">Jenis Pembayaran</th>
                        <th class="px-4 py-3.5">Nominal</th>
                        <th class="px-4 py-3.5">Tanggal Bayar</th>
                        <th class="px-4 py-3.5">Status</th>
                        <th class="px-4 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-brand-50">
                    @forelse ($pembayarans as $index => $pembayaran)
                    @php
                    [$kelasStatus, $labelStatus] = $badgeStatus($pembayaran->status);
                    $namaPendaftar = $pembayaran->pendaftar->nama_lengkap ?? '-';
                    @endphp

                    <tr wire:key="pembayaran-{{ $pembayaran->id }}" class="transition hover:bg-brand-50/40">
                        <td class="px-4 py-4 text-slate-400">{{ $pembayarans->firstItem() + $index }}</td>

                        <td class="px-4 py-4">
                            <div class="flex items-center gap-3">
                                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-brand-100 text-sm font-extrabold text-brand-700">
                                    {{ strtoupper(mb_substr($namaPendaftar, 0, 1)) }}
                                </span>
                                <div class="min-w-0">
                                    <p class="truncate font-semibold text-slate-800">{{ $namaPendaftar }}</p>
                                    <p class="font-mono text-xs text-brand-600">{{ $pembayaran->pendaftar->nomor_pendaftaran ?? '-' }}</p>
                                </div>
                            </div>
                        </td>

                        <td class="px-4 py-4 text-slate-600">{{ $pembayaran->jenis_pembayaran }}</td>

                        <td class="whitespace-nowrap px-4 py-4 font-semibold text-slate-800">
                            {{ $fmtRupiah($pembayaran->nominal) }}
                        </td>

                        <td class="whitespace-nowrap px-4 py-4 text-slate-600">
                            {{ $fmtTgl($pembayaran->tanggal_bayar) }}
                        </td>

                        <td class="px-4 py-4">
                            <span class="inline-block rounded-full px-3 py-1 text-xs font-semibold {{ $kelasStatus }}">
                                {{ $labelStatus }}
                            </span>
                        </td>

                        <td class="px-4 py-4">
                            <div class="flex items-center justify-center">
                                <button type="button" wire:click="pilihPembayaran({{ $pembayaran->id }})"
                                    class="inline-flex items-center gap-2 rounded-xl border border-brand-200 bg-white px-3.5 py-2 text-xs font-semibold text-brand-700 transition hover:bg-brand-50">
                                    <i class="fa-solid fa-clipboard-check"></i>
                                    Periksa
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center">
                            <span class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-brand-100 text-2xl text-brand-500">
                                <i class="fa-solid fa-receipt"></i>
                            </span>
                            <p class="mt-4 font-bold text-brand-900">Belum ada data pembayaran</p>
                            <p class="mt-1 text-sm text-slate-500">Data pembayaran pendaftar akan muncul di sini.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>{{ $pembayarans->links() }}</div>

    {{-- ================= MODAL PERIKSA PEMBAYARAN ================= --}}
    @if ($pembayaranId)
    @php
    [$kelasStatusM, $labelStatusM] = $pembayaranTerpilih
    ? $badgeStatus($pembayaranTerpilih->status)
    : ['bg-white text-brand-700', 'Menunggu'];
    @endphp

    <div class="fixed inset-0 z-[60] flex items-end justify-center sm:items-center sm:p-4"
        x-data="{ show: false }" x-init="$nextTick(() => show = true)"
        x-on:keydown.escape.window="$wire.set('pembayaranId', null)"
        role="dialog" aria-modal="true">

        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity duration-200"
            :class="show ? 'opacity-100' : 'opacity-0'"
            wire:click="$set('pembayaranId', null)"></div>

        <div class="relative z-10 flex max-h-[92vh] w-full max-w-xl flex-col overflow-hidden rounded-t-3xl bg-white shadow-2xl transition duration-200 sm:rounded-3xl"
            :class="show ? 'translate-y-0 opacity-100' : 'translate-y-4 opacity-0'">

            {{-- Header --}}
            <div class="flex shrink-0 items-center justify-between gap-3 bg-gradient-to-br from-brand-500 to-brand-600 px-6 py-5 text-white">
                <div class="flex min-w-0 items-center gap-4">
                    <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-white text-lg font-extrabold text-brand-700 shadow-md">
                        <i class="fa-solid fa-file-invoice-dollar"></i>
                    </span>

                    <div class="min-w-0">
                        <p class="text-xs font-semibold uppercase tracking-wide text-brand-100">Verifikasi Transaksi</p>
                        <h2 class="truncate text-lg font-extrabold sm:text-xl">Periksa Pembayaran</h2>
                        @if ($pembayaranTerpilih)
                        <p class="truncate text-xs text-brand-50">
                            {{ $pembayaranTerpilih->pendaftar->nama_lengkap ?? '-' }}
                            · <span class="font-mono">{{ $pembayaranTerpilih->pendaftar->nomor_pendaftaran ?? '-' }}</span>
                        </p>
                        @endif
                    </div>
                </div>

                <div class="flex shrink-0 items-center gap-3">
                    @if ($pembayaranTerpilih)
                    <span class="hidden rounded-full bg-white px-3 py-1 text-xs font-bold text-brand-700 sm:inline-block">
                        {{ $labelStatusM }}
                    </span>
                    @endif

                    <button type="button" wire:click="$set('pembayaranId', null)" aria-label="Tutup"
                        class="grid h-9 w-9 place-items-center rounded-lg text-white/80 transition hover:bg-white/15 hover:text-white">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            </div>

            {{-- Isi --}}
            <div class="space-y-5 overflow-y-auto p-6">

                @if ($pembayaranTerpilih)
                <section>
                    <h3 class="mb-3 flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-brand-700">
                        <i class="fa-solid fa-circle-info text-brand-500"></i> Rincian Pembayaran
                    </h3>

                    <dl class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div class="rounded-xl border border-brand-100 bg-brand-50/30 px-4 py-3">
                            <dt class="text-xs text-slate-400">Jenis Pembayaran</dt>
                            <dd class="mt-0.5 text-sm font-semibold text-slate-800">{{ $pembayaranTerpilih->jenis_pembayaran }}</dd>
                        </div>

                        <div class="rounded-xl border border-brand-100 bg-brand-50/30 px-4 py-3">
                            <dt class="text-xs text-slate-400">Nominal</dt>
                            <dd class="mt-0.5 text-sm font-extrabold text-brand-700">{{ $fmtRupiah($pembayaranTerpilih->nominal) }}</dd>
                        </div>

                        <div class="rounded-xl border border-brand-100 bg-brand-50/30 px-4 py-3 sm:col-span-2">
                            <dt class="text-xs text-slate-400">Tanggal Bayar</dt>
                            <dd class="mt-0.5 text-sm font-semibold text-slate-800">{{ $fmtTgl($pembayaranTerpilih->tanggal_bayar) }}</dd>
                        </div>
                    </dl>
                </section>
                @endif

                <section>
                    <label for="keterangan_pembayaran" class="mb-2 flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-brand-700">
                        <i class="fa-solid fa-comment-dots text-brand-500"></i> Keterangan Verifikasi
                    </label>

                    <textarea id="keterangan_pembayaran" wire:model="keterangan" rows="4"
                        placeholder="Isi keterangan atau alasan jika pembayaran ditolak..."
                        class="w-full rounded-xl border border-brand-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition
                               placeholder:text-slate-400 focus:border-brand-400 focus:ring-2 focus:ring-brand-200"></textarea>

                    @error('keterangan')
                    <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        {{ $message }}
                    </p>
                    @enderror
                </section>
            </div>

            {{-- Footer --}}
            <div class="flex shrink-0 flex-col-reverse gap-2 border-t border-brand-100 bg-white px-6 py-4 sm:flex-row sm:items-center sm:justify-end">
                <button type="button" wire:click="$set('pembayaranId', null)"
                    class="rounded-xl border border-brand-200 px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-brand-50">
                    Batal
                </button>

                <button type="button" wire:click="verifikasi('ditolak')"
                    wire:loading.attr="disabled" wire:target="verifikasi"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-red-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-60">
                    <i class="fa-solid fa-xmark text-xs"></i>
                    Tolak
                </button>

                <button type="button" wire:click="verifikasi('diverifikasi')"
                    wire:loading.attr="disabled" wire:target="verifikasi"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-brand-500/20 transition hover:bg-brand-700 disabled:cursor-not-allowed disabled:opacity-60">
                    <i class="fa-solid fa-check text-xs"></i>
                    Verifikasi
                </button>
            </div>
        </div>
    </div>
    @endif
</div>