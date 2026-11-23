@php
$rupiah = fn ($n) => 'Rp ' . number_format((float) $n, 0, ',', '.');
$namaJenis = fn ($j) => ucfirst(str_replace('_', ' ', (string) $j));

// Gaya & label badge status (dokumen, pembayaran, maupun pendaftaran)
$badge = function ($status) {
$s = strtolower((string) $status);
$label = ucfirst(str_replace('_', ' ', $s));

return match (true) {
$s === 'menunggu'
=> ['bg-amber-100 text-amber-700', 'Menunggu verifikasi'],
str_contains($s, 'tolak') || str_contains($s, 'gagal')
=> ['bg-red-100 text-red-700', $label],
in_array($s, ['diterima', 'diverifikasi', 'terverifikasi', 'disetujui', 'lulus', 'lunas', 'valid'])
=> ['bg-green-100 text-green-700', $label],
$s === 'terdaftar'
=> ['bg-emerald-100 text-emerald-700', 'Terdaftar'],
default
=> ['bg-slate-100 text-slate-600', $label],
};
};

$icons = [
'user' => '
<circle cx="12" cy="8" r="4" />
<path d="M4 20c0-4 4-6 8-6s8 2 8 6" />',
'doc' => '
<path d="M7 3h7l5 5v12a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1z" />
<path d="M14 3v5h5" />',
'card' => '
<rect x="3" y="5" width="18" height="14" rx="2" />
<path d="M3 10h18M7 15h4" />',
];

// Data turunan (hanya jika pendaftar ditemukan)
$kurangDok = $pendaftar ? $this->dokumenBelumLengkap() : [];
$jmlWajib = $pendaftar ? $this->jumlahDokumenWajib() : 0;
$jmlTerima = max($jmlWajib - count($kurangDok), 0);
$dokLengkap = $pendaftar && empty($kurangDok);
$bayarBelum = $pendaftar ? $this->pembayaranBelumLengkap() : true;
$urlForm = $pendaftar ? $this->urlForm() : null;
@endphp

{{-- Livewire: elemen root harus satu --}}
<div class="min-h-screen bg-gradient-to-b from-green-50 via-white to-green-50/40 px-4 py-8 sm:py-12">
    <div class="mx-auto max-w-4xl space-y-6">

        {{-- ================= HEADER ================= --}}
        <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-green-500 to-emerald-600 p-6 text-white shadow-lg shadow-green-600/20 sm:p-8">
            <div class="pointer-events-none absolute -right-12 -top-12 h-52 w-52 rounded-full bg-white/10"></div>
            <div class="pointer-events-none absolute -bottom-16 right-1/3 h-40 w-40 rounded-full bg-white/10"></div>

            <div class="relative">
                <p class="text-xs font-bold uppercase tracking-widest text-green-100">Pendaftaran Peserta Didik Baru</p>
                <h1 class="mt-2 text-2xl font-extrabold sm:text-3xl">Cek Status Pendaftaran</h1>
                <p class="mt-2 text-sm text-green-50">Masukkan nomor pendaftaran dan kode akses.</p>
            </div>
        </section>

        {{-- ================= FORM CEK ================= --}}
        <form wire:submit="cekStatus" class="rounded-3xl border border-green-100 bg-white p-5 shadow-sm sm:p-6">
            <div class="mb-5 flex items-center gap-3">
                <span class="grid h-11 w-11 place-items-center rounded-2xl bg-green-100 text-green-600">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="7" />
                        <path d="M20 20l-3.5-3.5" />
                    </svg>
                </span>
                <div>
                    <h2 class="text-lg font-extrabold text-slate-800">Data Pendaftaran</h2>
                    <p class="text-sm text-slate-500">Nomor dan kode akses diberikan setelah pendaftaran berhasil.</p>
                </div>
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="nomor_pendaftaran" class="st-label">Nomor Pendaftaran</label>
                    <input id="nomor_pendaftaran" type="text" wire:model="nomor_pendaftaran"
                        placeholder="Contoh: PPDB-2026-ABCD1234" autocomplete="off"
                        class="st-input @error('nomor_pendaftaran') is-invalid @enderror">
                    @error('nomor_pendaftaran')
                    <p class="st-error" role="alert">
                        <svg class="h-3.5 w-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="9" />
                            <path d="M12 8v5M12 16h.01" />
                        </svg>
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                <div x-data="{ show: false }">
                    <label for="kode_akses" class="st-label">Kode Akses</label>
                    <div class="relative">
                        <input id="kode_akses" wire:model="kode_akses" :type="show ? 'text' : 'password'"
                            placeholder="Kode akses Anda" autocomplete="off"
                            class="st-input pr-11 @error('kode_akses') is-invalid @enderror">
                        <button type="button" @click="show = ! show"
                            class="absolute inset-y-0 right-0 grid w-11 place-items-center text-slate-400 transition hover:text-green-600"
                            :aria-label="show ? 'Sembunyikan kode akses' : 'Tampilkan kode akses'">
                            <svg x-show="! show" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                            <svg x-show="show" x-cloak class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 3l18 18" />
                                <path d="M10.6 6.1A9.8 9.8 0 0 1 12 6c6.4 0 10 6 10 6a17 17 0 0 1-3.2 3.9M6.6 6.7A16.6 16.6 0 0 0 2 12s3.6 7 10 7a9.7 9.7 0 0 0 4.2-.9" />
                                <path d="M9.9 9.9a3 3 0 0 0 4.2 4.2" />
                            </svg>
                        </button>
                    </div>
                    @error('kode_akses')
                    <p class="st-error" role="alert">
                        <svg class="h-3.5 w-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="9" />
                            <path d="M12 8v5M12 16h.01" />
                        </svg>
                        {{ $message }}
                    </p>
                    @enderror
                </div>
            </div>

            <div class="mt-6 flex justify-end border-t border-green-100 pt-5">
                <button type="submit" wire:loading.attr="disabled" wire:target="cekStatus"
                    class="inline-flex items-center gap-2 rounded-xl bg-green-600 px-6 py-2.5 text-sm font-semibold text-white shadow-md shadow-green-600/30 transition hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60">
                    <span wire:loading.remove wire:target="cekStatus" class="inline-flex items-center gap-2">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="7" />
                            <path d="M20 20l-3.5-3.5" />
                        </svg>
                        Cek Pendaftaran
                    </span>
                    <span wire:loading wire:target="cekStatus" class="inline-flex items-center gap-2">
                        <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                            <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="3" opacity=".25" />
                            <path d="M21 12a9 9 0 0 0-9-9" stroke="currentColor" stroke-width="3" stroke-linecap="round" />
                        </svg>
                        Mencari...
                    </span>
                </button>
            </div>
        </form>

        {{-- ================= HASIL ================= --}}
        @if ($pendaftar)
        @php
        $adaBayar = $pendaftar->pembayarans->isNotEmpty();

        $tahap = [
        [
        'judul' => 'Formulir',
        'ket' => 'Terdaftar',
        'selesai' => true,
        'icon' => 'user',
        ],
        [
        'judul' => 'Dokumen',
        'ket' => $dokLengkap ? 'Seluruh dokumen diterima' : $jmlTerima . '/' . $jmlWajib . ' dokumen diterima',
        'selesai' => $dokLengkap,
        'icon' => 'doc',
        ],
        [
        'judul' => 'Pembayaran',
        'ket' => ! $bayarBelum ? 'Sudah diverifikasi' : ($adaBayar ? 'Menunggu verifikasi' : 'Belum ada pembayaran'),
        'selesai' => ! $bayarBelum,
        'icon' => 'card',
        ],
        ];

        // Tahap pertama yang belum selesai ditandai sebagai "sedang berjalan"
        $tahapAktif = collect($tahap)->search(fn ($t) => ! $t['selesai']);

        [$kelasStatusDaftar, $labelStatusDaftar] = $badge($pendaftar->status);

        $namaSekolah = $pendaftar->periode?->sekolah?->nama_sekolah
        ?? $pendaftar->periode?->sekolah?->nama
        ?? '-';
        @endphp

        <div wire:key="hasil-{{ $pendaftar->id }}" class="scroll-mt-24 space-y-6"
            x-data x-init="$nextTick(() => $el.scrollIntoView({ behavior: 'smooth', block: 'start' }))">

            {{-- Ringkasan pendaftar --}}
            <section class="overflow-hidden rounded-3xl border border-green-100 bg-white shadow-sm">
                <div class="flex items-center gap-4 bg-gradient-to-br from-green-500 to-emerald-600 p-5 text-white sm:p-6">
                    <span class="grid h-14 w-14 shrink-0 place-items-center rounded-2xl bg-white text-xl font-extrabold text-green-700 shadow-md">
                        {{ strtoupper(mb_substr($pendaftar->nama_lengkap, 0, 1)) }}
                    </span>

                    <div class="min-w-0">
                        <p class="text-xs font-semibold uppercase tracking-wide text-green-100">Informasi Pendaftaran</p>
                        <h2 class="truncate text-xl font-extrabold">{{ $pendaftar->nama_lengkap }}</h2>
                        <p class="mt-0.5 truncate text-sm text-green-50">{{ $namaSekolah }}</p>
                    </div>
                </div>

                <dl class="grid divide-y divide-green-50 text-sm sm:grid-cols-3 sm:divide-x sm:divide-y-0">
                    <div class="px-5 py-4"
                        x-data="{
                                copied: false,
                                salin(teks) {
                                    if (navigator.clipboard) { navigator.clipboard.writeText(teks); }
                                    this.copied = true;
                                    setTimeout(() => this.copied = false, 1800);
                                }
                            }">
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Nomor Pendaftaran</dt>
                        <dd class="mt-1 flex items-center gap-2">
                            <span class="break-all font-mono font-bold text-green-800">{{ $pendaftar->nomor_pendaftaran }}</span>
                            <button type="button" @click="salin(@js($pendaftar->nomor_pendaftaran))"
                                class="shrink-0 rounded-md p-1 text-slate-400 transition hover:bg-green-50 hover:text-green-600"
                                :title="copied ? 'Tersalin' : 'Salin'" aria-label="Salin nomor pendaftaran">
                                <svg x-show="! copied" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="9" y="9" width="11" height="11" rx="2" />
                                    <path d="M5 15V6a2 2 0 0 1 2-2h9" />
                                </svg>
                                <svg x-show="copied" x-cloak class="h-4 w-4 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M5 13l4 4L19 7" />
                                </svg>
                            </button>
                        </dd>
                    </div>

                    <div class="px-5 py-4">
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Tanggal Daftar</dt>
                        <dd class="mt-1 font-semibold text-slate-700">
                            {{ $pendaftar->created_at?->translatedFormat('d F Y') ?? '-' }}
                        </dd>
                    </div>

                    <div class="px-5 py-4">
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Status Pendaftaran</dt>
                        <dd class="mt-1">
                            <span class="inline-block rounded-full px-3 py-1 text-xs font-bold {{ $kelasStatusDaftar }}">{{ $labelStatusDaftar }}</span>
                        </dd>
                    </div>
                </dl>

                @if ($urlForm)
                <div class="flex flex-col gap-3 border-t border-green-100 bg-green-50/50 px-5 py-3.5 sm:flex-row sm:items-center sm:justify-between">
                    <p class="flex items-center gap-2 text-sm text-slate-600">
                        <svg class="h-4 w-4 shrink-0 text-amber-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="5" y="11" width="14" height="9" rx="2" />
                            <path d="M8 11V8a4 4 0 0 1 8 0v3" />
                        </svg>
                        Formulir pendaftaran Anda sudah terkunci dan hanya bisa dilihat.
                    </p>

                    <a href="{{ $urlForm }}"
                        class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl border border-green-200 bg-white px-4 py-2 text-sm font-semibold text-green-700 transition hover:bg-green-50">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z" />
                            <circle cx="12" cy="12" r="3" />
                        </svg>
                        Lihat Formulir
                    </a>
                </div>
                @endif
            </section>

            {{-- Tahapan pendaftaran --}}
            <section class="grid gap-3 sm:grid-cols-3">
                @foreach ($tahap as $i => $t)
                @php $aktif = $tahapAktif === $i; @endphp

                <div class="flex items-center gap-3 rounded-2xl border p-4
                            {{ $t['selesai']
                                ? 'border-green-200 bg-green-50'
                                : ($aktif ? 'border-amber-200 bg-amber-50' : 'border-slate-200 bg-white') }}">

                    <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl
                                {{ $t['selesai']
                                    ? 'bg-green-500 text-white shadow-md shadow-green-500/30'
                                    : ($aktif ? 'bg-amber-100 text-amber-600' : 'bg-slate-100 text-slate-400') }}">
                        @if ($t['selesai'])
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 13l4 4L19 7" />
                        </svg>
                        @else
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">{!! $icons[$t['icon']] !!}</svg>
                        @endif
                    </span>

                    <div class="min-w-0">
                        <p class="text-sm font-extrabold {{ $t['selesai'] ? 'text-green-900' : ($aktif ? 'text-amber-900' : 'text-slate-500') }}">
                            {{ $t['judul'] }}
                        </p>
                        <p class="text-xs {{ $t['selesai'] ? 'text-green-700' : ($aktif ? 'text-amber-700' : 'text-slate-400') }}">
                            {{ $t['ket'] }}
                        </p>
                    </div>
                </div>
                @endforeach
            </section>

            {{-- Status dokumen & pembayaran --}}
            <div class="grid gap-6 lg:grid-cols-2">

                <section class="rounded-3xl border border-green-100 bg-white p-5 shadow-sm">
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <h3 class="flex items-center gap-2 font-extrabold text-slate-800">
                            <span class="grid h-8 w-8 place-items-center rounded-lg bg-green-100 text-green-600">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">{!! $icons['doc'] !!}</svg>
                            </span>
                            Status Dokumen
                        </h3>
                        <span class="rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-bold text-green-700">{{ $pendaftar->dokumens->count() }}</span>
                    </div>

                    <div class="space-y-2">
                        @forelse ($pendaftar->dokumens as $dokumen)
                        @php [$kelasDok, $labelDok] = $badge($dokumen->status_verifikasi); @endphp

                        <div class="flex items-center justify-between gap-3 rounded-xl border border-green-100 px-4 py-3">
                            <span class="min-w-0 truncate text-sm font-medium text-slate-700">{{ $namaJenis($dokumen->jenis_dokumen) }}</span>
                            <span class="shrink-0 rounded-full px-2.5 py-0.5 text-[11px] font-semibold {{ $kelasDok }}">{{ $labelDok }}</span>
                        </div>
                        @empty
                        <div class="rounded-xl border border-dashed border-green-200 bg-green-50/30 px-4 py-8 text-center text-sm text-slate-500">
                            Belum ada dokumen yang diunggah.
                        </div>
                        @endforelse
                    </div>
                </section>

                <section class="rounded-3xl border border-green-100 bg-white p-5 shadow-sm">
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <h3 class="flex items-center gap-2 font-extrabold text-slate-800">
                            <span class="grid h-8 w-8 place-items-center rounded-lg bg-green-100 text-green-600">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">{!! $icons['card'] !!}</svg>
                            </span>
                            Status Pembayaran
                        </h3>
                        <span class="rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-bold text-green-700">{{ $pendaftar->pembayarans->count() }}</span>
                    </div>

                    <div class="space-y-2">
                        @forelse ($pendaftar->pembayarans as $pembayaran)
                        @php [$kelasBayar, $labelBayar] = $badge($pembayaran->status); @endphp

                        <div class="flex items-center justify-between gap-3 rounded-xl border border-green-100 px-4 py-3">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium text-slate-700">{{ $pembayaran->jenis_pembayaran }}</p>
                                <p class="text-xs font-bold text-green-800">{{ $rupiah($pembayaran->nominal) }}</p>
                            </div>
                            <span class="shrink-0 rounded-full px-2.5 py-0.5 text-[11px] font-semibold {{ $kelasBayar }}">{{ $labelBayar }}</span>
                        </div>
                        @empty
                        <div class="rounded-xl border border-dashed border-green-200 bg-green-50/30 px-4 py-8 text-center text-sm text-slate-500">
                            Belum ada pembayaran.
                        </div>
                        @endforelse
                    </div>
                </section>
            </div>

            {{-- Tindakan selanjutnya --}}
            <section class="rounded-3xl border border-green-100 bg-white p-5 shadow-sm sm:p-6">
                <h3 class="mb-4 text-lg font-extrabold text-slate-800">Tindakan Selanjutnya</h3>

                <div class="space-y-3">

                    {{-- Dokumen --}}
                    @if (count($kurangDok) > 0)
                    <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4">
                        <p class="text-sm font-bold text-amber-900">
                            Dokumen pendaftaran belum lengkap atau belum seluruhnya diterima.
                        </p>

                        <p class="mt-2 text-xs font-semibold text-amber-800">Dokumen yang perlu dilengkapi:</p>
                        <div class="mt-1.5 flex flex-wrap gap-1.5">
                            @foreach ($kurangDok as $jenis)
                            <span class="rounded-full border border-amber-200 bg-white px-2.5 py-1 text-xs font-semibold text-amber-800">
                                {{ $namaJenis($jenis) }}
                            </span>
                            @endforeach
                        </div>

                        <a href="{{ route('ppdb.dokumen') }}"
                            class="mt-4 inline-flex items-center gap-2 rounded-xl bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow-md shadow-green-600/30 transition hover:bg-green-700">
                            Lengkapi Dokumen
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 12h14m-6-6l6 6-6 6" />
                            </svg>
                        </a>
                    </div>
                    @else
                    <div class="flex items-center gap-3 rounded-2xl border border-green-200 bg-green-50 p-4 text-sm font-medium text-green-800">
                        <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-green-500 text-white">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 13l4 4L19 7" />
                            </svg>
                        </span>
                        Seluruh dokumen wajib sudah diterima.
                    </div>
                    @endif

                    {{-- Pembayaran --}}
                    @if ($bayarBelum)
                    <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4">
                        <p class="text-sm font-bold text-amber-900">Pembayaran belum diverifikasi.</p>
                        <p class="mt-1 text-xs text-amber-800">
                            Silakan lengkapi pembayaran atau periksa kembali bukti pembayaran.
                        </p>

                        <a href="{{ route('ppdb.pembayaran') }}"
                            class="mt-4 inline-flex items-center gap-2 rounded-xl bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow-md shadow-green-600/30 transition hover:bg-green-700">
                            Lengkapi Pembayaran
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 12h14m-6-6l6 6-6 6" />
                            </svg>
                        </a>
                    </div>
                    @else
                    <div class="flex items-center gap-3 rounded-2xl border border-green-200 bg-green-50 p-4 text-sm font-medium text-green-800">
                        <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-green-500 text-white">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 13l4 4L19 7" />
                            </svg>
                        </span>
                        Pembayaran sudah diverifikasi.
                    </div>
                    @endif

                    {{-- Formulir terkunci --}}
                    @if ($urlForm)
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <div class="flex items-start gap-3">
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-white text-amber-500 shadow-sm ring-1 ring-slate-200">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="5" y="11" width="14" height="9" rx="2" />
                                    <path d="M8 11V8a4 4 0 0 1 8 0v3" />
                                </svg>
                            </span>
                            <div>
                                <p class="text-sm font-bold text-slate-800">Data formulir sudah terkunci</p>
                                <p class="mt-0.5 text-xs text-slate-500">
                                    Anda dapat melihat data yang telah dikirim, namun tidak dapat mengubahnya.
                                    Hubungi panitia jika ada data yang perlu diperbaiki.
                                </p>

                                <a href="{{ $urlForm }}"
                                    class="mt-3 inline-flex items-center gap-2 rounded-xl border border-green-200 bg-white px-4 py-2 text-sm font-semibold text-green-700 transition hover:bg-green-50">
                                    Lihat Formulir
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M5 12h14m-6-6l6 6-6 6" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </section>
        </div>
        @endif
    </div>

    <style>
        .st-label {
            display: block;
            font-size: 0.8125rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 0.4rem;
        }

        .st-input {
            width: 100%;
            border: 1px solid #d3e3d8;
            border-radius: 0.75rem;
            padding: 0.7rem 0.9rem;
            font-size: 0.875rem;
            color: #1e293b;
            background: #fff;
            outline: none;
            transition: border-color .15s, box-shadow .15s, background-color .15s;
        }

        .st-input::placeholder {
            color: #94a3b8;
        }

        .st-input:hover {
            border-color: #86c99b;
        }

        .st-input:focus {
            border-color: #16a34a;
            box-shadow: 0 0 0 3px rgb(22 163 74 / 15%);
        }

        .st-input.is-invalid {
            border-color: #f87171;
            background-color: #fef2f2;
        }

        .st-error {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            margin-top: 0.35rem;
            font-size: 0.75rem;
            color: #dc2626;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
</div>