@php
$rupiah = fn ($n) => 'Rp ' . number_format((float) $n, 0, ',', '.');

// Gaya & label status pembayaran
$statusStyle = function ($status) {
$s = strtolower((string) $status);

return match (true) {
$s === 'menunggu'
=> ['bg-amber-100 text-amber-700', 'Menunggu verifikasi'],
in_array($s, ['diverifikasi', 'diterima', 'disetujui', 'terverifikasi', 'lunas', 'valid', 'verified', 'berhasil'])
=> ['bg-green-100 text-green-700', ucfirst($s)],
in_array($s, ['ditolak', 'gagal', 'tidak_valid', 'invalid'])
=> ['bg-red-100 text-red-700', ucfirst(str_replace('_', ' ', $s))],
default
=> ['bg-slate-100 text-slate-600', ucfirst(str_replace('_', ' ', $s))],
};
};

$metodeLabel = fn ($m) => match ($m) {
'tunai' => 'Tunai',
'transfer_manual' => 'Transfer Manual',
default => filled($m) ? ucfirst(str_replace('_', ' ', $m)) : null,
};

$iconCash = '
<rect x="2" y="6" width="20" height="12" rx="2" />
<circle cx="12" cy="12" r="2.5" />
<path d="M6 12h.01M18 12h.01" />';
$iconBank = '
<path d="M3 10l9-6 9 6" />
<path d="M5 10v8M9.5 10v8M14.5 10v8M19 10v8" />
<path d="M3 20h18" />';

// Ringkasan
$totalNominal = $pembayarans->sum('nominal');
$jmlTransaksi = $pembayarans->count();
$jmlMenunggu = $pembayarans->filter(fn ($p) => strtolower((string) $p->status) === 'menunggu')->count();
$jmlSelesai = $pembayarans->filter(fn ($p) => in_array(strtolower((string) $p->status), ['diverifikasi', 'diterima', 'disetujui', 'terverifikasi', 'lunas', 'valid', 'verified', 'berhasil']))->count();
@endphp

{{-- Livewire: elemen root harus satu --}}
<div class="min-h-screen bg-gradient-to-b from-green-50 via-white to-green-50/40 px-4 py-8 sm:py-12">
    <div class="mx-auto max-w-5xl space-y-6">

        {{-- ================= HEADER ================= --}}
        <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-green-500 to-emerald-600 p-6 text-white shadow-lg shadow-green-600/20 sm:p-8">
            <div class="pointer-events-none absolute -right-12 -top-12 h-52 w-52 rounded-full bg-white/10"></div>
            <div class="pointer-events-none absolute -bottom-16 right-1/3 h-40 w-40 rounded-full bg-white/10"></div>

            <div class="relative">
                <p class="text-xs font-bold uppercase tracking-widest text-green-100">Pendaftaran Peserta Didik Baru</p>
                <h1 class="mt-2 text-2xl font-extrabold sm:text-3xl">Informasi Pembayaran</h1>

                <div class="mt-3 inline-flex items-center gap-2 rounded-full bg-white/15 px-4 py-1.5 text-sm backdrop-blur">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="5" width="18" height="14" rx="2" />
                        <path d="M3 10h18M7 15h4" />
                    </svg>
                    <span class="text-green-50">No. Pendaftaran</span>
                    <span class="font-mono font-bold tracking-wide">{{ $pendaftar->nomor_pendaftaran }}</span>
                </div>

                {{-- Tahapan --}}
                <ol class="mt-6 flex items-center gap-2 text-xs font-semibold sm:gap-3">
                    <li class="flex items-center gap-2">
                        <span class="grid h-7 w-7 place-items-center rounded-full bg-white text-green-600">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 13l4 4L19 7" />
                            </svg>
                        </span>
                        <span class="hidden sm:inline">Formulir</span>
                    </li>
                    <li class="h-0.5 w-8 rounded-full bg-white/60 sm:w-14"></li>
                    <li class="flex items-center gap-2">
                        <span class="grid h-7 w-7 place-items-center rounded-full bg-white text-green-600">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 13l4 4L19 7" />
                            </svg>
                        </span>
                        <span class="hidden sm:inline">Dokumen</span>
                    </li>
                    <li class="h-0.5 w-8 rounded-full bg-white/60 sm:w-14"></li>
                    <li class="flex items-center gap-2">
                        <span class="grid h-7 w-7 place-items-center rounded-full bg-white text-green-700 ring-4 ring-white/30">3</span>
                        <span>Pembayaran</span>
                    </li>
                </ol>
            </div>
        </section>

        {{-- ================= NOTIFIKASI ================= --}}
        @if (session('success'))
        <div x-data="{ show: true }" x-show="show"
            class="flex items-start gap-3 rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800" role="status">
            <svg class="mt-0.5 h-5 w-5 shrink-0 text-green-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="9" />
                <path d="M8 12l3 3 5-6" />
            </svg>
            <span class="flex-1">{{ session('success') }}</span>
            <button type="button" @click="show = false" class="text-green-500 transition hover:text-green-700" aria-label="Tutup">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round">
                    <path d="M6 6l12 12M18 6L6 18" />
                </svg>
            </button>
        </div>
        @endif

        <div class="grid items-start gap-6 lg:grid-cols-3">

            {{-- ================= KOLOM UTAMA ================= --}}
            <div class="min-w-0 space-y-6 lg:col-span-2">

                {{-- Form pembayaran --}}
                <form wire:submit="simpan"
                    x-data="{ drag: false, uploading: false, progress: 0 }"
                    x-on:livewire-upload-start="uploading = true; progress = 0"
                    x-on:livewire-upload-finish="uploading = false"
                    x-on:livewire-upload-error="uploading = false"
                    x-on:livewire-upload-progress="progress = $event.detail.progress"
                    class="rounded-3xl border border-green-100 bg-white p-5 shadow-sm sm:p-6">

                    <div class="mb-5 flex items-center gap-3">
                        <span class="grid h-11 w-11 place-items-center rounded-2xl bg-green-100 text-green-600">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">{!! $iconCash !!}</svg>
                        </span>
                        <div>
                            <h2 class="text-lg font-extrabold text-slate-800">Kirim Bukti Pembayaran</h2>
                            <p class="text-sm text-slate-500">Isi detail pembayaran lalu unggah buktinya.</p>
                        </div>
                    </div>

                    <div class="space-y-5">

                        {{-- Jenis pembayaran --}}
                        <div>
                            <label for="jenis_pembayaran" class="pay-label">Jenis Pembayaran</label>
                            <input id="jenis_pembayaran" type="text" wire:model="jenis_pembayaran"
                                placeholder="Contoh: Biaya pendaftaran"
                                class="pay-input @error('jenis_pembayaran') is-invalid @enderror">
                            @error('jenis_pembayaran')
                            <p class="pay-error" role="alert">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Nominal --}}
                        <div>
                            <label for="nominal" class="pay-label">Nominal</label>
                            <div class="relative">
                                <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-sm font-bold text-green-700">Rp</span>
                                <input id="nominal" type="number" min="0" inputmode="numeric" wire:model="nominal"
                                    placeholder="0"
                                    class="pay-input pl-11 @error('nominal') is-invalid @enderror">
                            </div>
                            <p class="mt-1.5 text-xs text-slate-400" x-data
                                x-text="'Terbaca: Rp ' + Number($wire.nominal || 0).toLocaleString('id-ID')">
                            </p>
                            @error('nominal')
                            <p class="pay-error" role="alert">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Tanggal --}}
                        <div>
                            <div class="mb-1.5 flex items-center justify-between">
                                <label for="tanggal_bayar" class="pay-label !mb-0">Tanggal Pembayaran</label>
                                <button type="button" x-data
                                    @click="$wire.tanggal_bayar = new Date(Date.now() - new Date().getTimezoneOffset() * 60000).toISOString().slice(0, 16)"
                                    class="text-xs font-semibold text-green-600 transition hover:text-green-700">
                                    Isi waktu sekarang
                                </button>
                            </div>
                            <input id="tanggal_bayar" type="datetime-local" wire:model="tanggal_bayar"
                                class="pay-input @error('tanggal_bayar') is-invalid @enderror">
                            @error('tanggal_bayar')
                            <p class="pay-error" role="alert">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Metode --}}
                        <div>
                            <span class="pay-label">Metode Pembayaran</span>

                            <div class="grid gap-3 sm:grid-cols-2">
                                @foreach ([
                                'tunai' => ['Tunai', 'Bayar langsung ke panitia', $iconCash],
                                'transfer_manual' => ['Transfer Manual', 'Transfer lalu unggah bukti', $iconBank],
                                ] as $value => [$judulMetode, $deskMetode, $iconMetode])
                                <label class="relative block cursor-pointer">
                                    <input type="radio" wire:model="metode_pembayaran" value="{{ $value }}" class="peer sr-only">

                                    <div class="flex items-start gap-3 rounded-2xl border-2 border-slate-200 bg-white p-4 pr-12 transition
                                                    hover:border-green-300 peer-checked:border-green-500 peer-checked:bg-green-50 peer-focus-visible:ring-2 peer-focus-visible:ring-green-300">
                                        <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-green-100 text-green-600">
                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">{!! $iconMetode !!}</svg>
                                        </span>
                                        <span>
                                            <span class="block text-sm font-bold text-slate-800">{{ $judulMetode }}</span>
                                            <span class="mt-0.5 block text-xs text-slate-500">{{ $deskMetode }}</span>
                                        </span>
                                    </div>

                                    <span class="pointer-events-none absolute right-4 top-4 h-5 w-5 rounded-full border-2 border-slate-300 bg-white transition
                                                     peer-checked:border-green-500 peer-checked:bg-green-500 peer-checked:shadow-[inset_0_0_0_3px_#fff]"></span>
                                </label>
                                @endforeach
                            </div>

                            @error('metode_pembayaran')
                            <p class="pay-error" role="alert">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Bukti --}}
                        <div>
                            <span class="pay-label">Bukti Pembayaran</span>

                            <label for="pay-file"
                                @dragover.prevent="drag = true"
                                @dragleave.prevent="drag = false"
                                @drop.prevent="drag = false; $refs.berkas.files = $event.dataTransfer.files; $refs.berkas.dispatchEvent(new Event('change', { bubbles: true }))"
                                :class="drag ? 'border-green-500 bg-green-100/60' : 'border-green-300 bg-green-50/40 hover:bg-green-50'"
                                class="flex cursor-pointer flex-col items-center justify-center gap-2 rounded-2xl border-2 border-dashed px-4 py-8 text-center transition
                                       @error('bukti_pembayaran') !border-red-300 !bg-red-50 @enderror">

                                @if ($bukti_pembayaran)
                                @php
                                $bisaPratinjau = method_exists($bukti_pembayaran, 'isPreviewable') && $bukti_pembayaran->isPreviewable();
                                @endphp

                                @if ($bisaPratinjau)
                                <img src="{{ $bukti_pembayaran->temporaryUrl() }}" alt="Pratinjau bukti"
                                    class="h-28 w-28 rounded-xl border border-green-200 object-cover shadow-sm">
                                @else
                                <span class="grid h-14 w-14 place-items-center rounded-2xl bg-green-100 text-green-600">
                                    <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M7 3h7l5 5v12a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1z" />
                                        <path d="M14 3v5h5" />
                                    </svg>
                                </span>
                                @endif

                                <p class="max-w-full break-all text-sm font-semibold text-green-800">{{ $bukti_pembayaran->getClientOriginalName() }}</p>
                                <p class="text-xs text-slate-500">Klik atau seret file lain untuk mengganti</p>
                                @else
                                <span class="grid h-14 w-14 place-items-center rounded-2xl bg-white text-green-500 shadow-sm ring-1 ring-green-100">
                                    <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 16V4m0 0l-4 4m4-4l4 4" />
                                        <path d="M4 16v3a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-3" />
                                    </svg>
                                </span>
                                <p class="text-sm font-semibold text-slate-700">
                                    <span class="text-green-600">Klik untuk memilih file</span> atau seret ke sini
                                </p>
                                <p class="text-xs text-slate-400">JPG, PNG, atau PDF</p>
                                @endif
                            </label>

                            <input id="pay-file" x-ref="berkas" type="file" wire:model="bukti_pembayaran"
                                accept=".jpg,.jpeg,.png,.pdf" class="sr-only">

                            {{-- Progres unggah --}}
                            <div x-show="uploading" x-cloak class="mt-3">
                                <div class="mb-1 flex items-center justify-between text-xs text-green-700">
                                    <span>Mengunggah file...</span>
                                    <span x-text="progress + '%'"></span>
                                </div>
                                <div class="h-2 overflow-hidden rounded-full bg-green-100">
                                    <div class="h-full rounded-full bg-gradient-to-r from-green-400 to-green-600 transition-all"
                                        :style="'width: ' + progress + '%'"></div>
                                </div>
                            </div>

                            @error('bukti_pembayaran')
                            <p class="pay-error" role="alert">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end border-t border-green-100 pt-5">
                        <button type="submit" :disabled="uploading" wire:loading.attr="disabled" wire:target="simpan"
                            class="inline-flex items-center gap-2 rounded-xl bg-green-600 px-6 py-2.5 text-sm font-semibold text-white shadow-md shadow-green-600/30 transition hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60">
                            <span wire:loading.remove wire:target="simpan" class="inline-flex items-center gap-2">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 2L11 13" />
                                    <path d="M22 2l-7 20-4-9-9-4 20-7z" />
                                </svg>
                                Kirim Pembayaran
                            </span>
                            <span wire:loading wire:target="simpan" class="inline-flex items-center gap-2">
                                <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                                    <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="3" opacity=".25" />
                                    <path d="M21 12a9 9 0 0 0-9-9" stroke="currentColor" stroke-width="3" stroke-linecap="round" />
                                </svg>
                                Mengirim...
                            </span>
                        </button>
                    </div>
                </form>

                {{-- Riwayat pembayaran --}}
                <section class="rounded-3xl border border-green-100 bg-white p-5 shadow-sm sm:p-6">
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <h2 class="text-lg font-extrabold text-slate-800">Riwayat Pembayaran</h2>
                        <span class="rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-bold text-green-700">{{ $jmlTransaksi }}</span>
                    </div>

                    <div class="space-y-3">
                        @forelse ($pembayarans as $pembayaran)
                        @php
                        [$kelasStatus, $labelStatus] = $statusStyle($pembayaran->status);
                        $metode = $metodeLabel($pembayaran->metode_pembayaran ?? null);
                        $tanggal = null;
                        if (filled($pembayaran->tanggal_bayar ?? null)) {
                        try {
                        $tanggal = \Carbon\Carbon::parse($pembayaran->tanggal_bayar)->translatedFormat('d M Y, H:i');
                        } catch (\Throwable $e) {
                        $tanggal = $pembayaran->tanggal_bayar;
                        }
                        }
                        @endphp

                        <div wire:key="pembayaran-{{ $pembayaran->id }}"
                            class="flex items-center gap-4 rounded-2xl border border-green-100 p-4 transition hover:border-green-300 hover:bg-green-50/40">

                            <span class="grid h-12 w-12 shrink-0 place-items-center rounded-xl bg-green-100 text-green-600">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    {!! ($pembayaran->metode_pembayaran ?? null) === 'transfer_manual' ? $iconBank : $iconCash !!}
                                </svg>
                            </span>

                            <div class="min-w-0 flex-1">
                                <p class="truncate font-semibold text-slate-800">{{ $pembayaran->jenis_pembayaran }}</p>

                                @if ($metode || $tanggal)
                                <p class="mt-0.5 text-xs text-slate-500">
                                    {{ $metode }}@if ($metode && $tanggal) · @endif{{ $tanggal }}
                                </p>
                                @endif
                            </div>

                            <div class="shrink-0 text-right">
                                <p class="font-extrabold text-green-800">{{ $rupiah($pembayaran->nominal) }}</p>
                                <span class="mt-1 inline-block rounded-full px-2.5 py-0.5 text-[11px] font-semibold {{ $kelasStatus }}">
                                    {{ $labelStatus }}
                                </span>
                            </div>
                        </div>
                        @empty
                        <div class="rounded-2xl border border-dashed border-green-200 bg-green-50/30 px-6 py-10 text-center">
                            <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-green-100 text-green-500">
                                <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">{!! $iconCash !!}</svg>
                            </span>
                            <p class="mt-3 font-semibold text-slate-700">Belum ada pembayaran</p>
                            <p class="mt-1 text-sm text-slate-500">Pembayaran yang Anda kirim akan tampil di sini.</p>
                        </div>
                        @endforelse
                    </div>
                </section>
            </div>

            {{-- ================= KOLOM SAMPING ================= --}}
            <aside class="space-y-6 lg:sticky lg:top-24">

                {{-- Ringkasan --}}
                <section class="overflow-hidden rounded-3xl border border-green-100 bg-white shadow-sm">
                    <div class="bg-gradient-to-br from-green-500 to-emerald-600 p-5 text-white">
                        <p class="text-xs font-semibold uppercase tracking-wide text-green-100">Total Pembayaran Dikirim</p>
                        <p class="mt-1 text-2xl font-extrabold">{{ $rupiah($totalNominal) }}</p>
                    </div>

                    <dl class="divide-y divide-green-50 text-sm">
                        <div class="flex items-center justify-between px-5 py-3">
                            <dt class="text-slate-500">Jumlah transaksi</dt>
                            <dd class="font-bold text-slate-800">{{ $jmlTransaksi }}</dd>
                        </div>
                        <div class="flex items-center justify-between px-5 py-3">
                            <dt class="text-slate-500">Menunggu verifikasi</dt>
                            <dd class="rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-bold text-amber-700">{{ $jmlMenunggu }}</dd>
                        </div>
                        <div class="flex items-center justify-between px-5 py-3">
                            <dt class="text-slate-500">Terverifikasi</dt>
                            <dd class="rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-bold text-green-700">{{ $jmlSelesai }}</dd>
                        </div>
                    </dl>
                </section>

                {{-- Petunjuk --}}
                <section class="rounded-3xl border border-green-100 bg-green-50 p-5">
                    <h3 class="flex items-center gap-2 font-extrabold text-green-900">
                        <svg class="h-5 w-5 text-green-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="9" />
                            <path d="M12 8h.01M11 12h1v4h1" />
                        </svg>
                        Petunjuk
                    </h3>

                    <ul class="mt-3 space-y-2 text-sm text-green-900/80">
                        <li class="flex gap-2"><span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-green-500"></span> Isi nominal sesuai jumlah yang benar-benar dibayarkan.</li>
                        <li class="flex gap-2"><span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-green-500"></span> Unggah bukti pembayaran yang jelas dan terbaca (JPG, PNG, atau PDF).</li>
                        <li class="flex gap-2"><span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-green-500"></span> Status akan berubah setelah panitia memverifikasi pembayaran Anda.</li>
                        <li class="flex gap-2"><span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-green-500"></span> Simpan nomor pendaftaran untuk mengecek status kapan saja.</li>
                    </ul>
                </section>
            </aside>
        </div>
    </div>

    <style>
        .pay-label {
            display: block;
            font-size: 0.8125rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 0.4rem;
        }

        .pay-input {
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

        .pay-input::placeholder {
            color: #94a3b8;
        }

        .pay-input:hover {
            border-color: #86c99b;
        }

        .pay-input:focus {
            border-color: #16a34a;
            box-shadow: 0 0 0 3px rgb(22 163 74 / 15%);
        }

        .pay-input.is-invalid {
            border-color: #f87171;
            background-color: #fef2f2;
        }

        .pay-error {
            margin-top: 0.4rem;
            font-size: 0.75rem;
            color: #dc2626;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
</div>