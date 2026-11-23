@php
$gayaDokumen = [
'menunggu' => ['bg-amber-100 text-amber-700', 'fa-hourglass-half'],
'diterima' => ['bg-brand-100 text-brand-700', 'fa-circle-check'],
'ditolak' => ['bg-red-100 text-red-700', 'fa-circle-xmark'],
];

$badgeDokumen = fn ($s) => $gayaDokumen[$s] ?? ['bg-slate-100 text-slate-600', 'fa-circle'];
$namaJenis = fn ($j) => ucfirst(str_replace('_', ' ', (string) $j));

$pilihanFilter = [
'' => 'Semua',
'menunggu' => 'Menunggu',
'diterima' => 'Diterima',
'ditolak' => 'Ditolak',
];
@endphp

{{-- Livewire: elemen root harus satu --}}
<div class="space-y-6"
    x-data="{ konfirmasi: null }"
    x-on:keydown.escape.window="
        if (konfirmasi) { konfirmasi = null }
        else if ($wire.dokumenPreview) { $wire.tutupPreview() }
        else if ($wire.pendaftarDetail) { $wire.tutupDetail() }
    ">

    {{-- ================= HEADER ================= --}}
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-500 to-brand-600 p-6 text-white shadow-lg shadow-brand-500/20 sm:p-8">
        <div class="pointer-events-none absolute -right-12 -top-12 h-48 w-48 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute -bottom-16 right-1/3 h-40 w-40 rounded-full bg-white/10"></div>

        <div class="relative">
            <h1 class="text-2xl font-extrabold sm:text-3xl">Verifikasi Dokumen</h1>
            <p class="mt-2 max-w-md text-sm text-brand-50">
                Kelola dan periksa dokumen calon peserta didik.
            </p>

            <span class="mt-4 inline-flex items-center gap-2 rounded-full bg-white/15 px-3.5 py-1.5 text-xs font-medium backdrop-blur">
                <i class="fa-solid fa-file-circle-check"></i>
                {{ $pendaftars->total() }} pendaftar dengan dokumen
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
        <div class="relative sm:max-w-lg">
            <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-sm text-brand-400"></i>

            <input type="text" wire:model.live.debounce.300ms="search"
                placeholder="Cari nama atau nomor pendaftaran..."
                class="w-full rounded-xl border border-brand-100 bg-brand-50/50 py-3 pl-11 pr-11 text-sm outline-none transition
                       placeholder:text-slate-400 focus:border-brand-300 focus:bg-white focus:ring-2 focus:ring-brand-200">

            <i wire:loading wire:target="search,filterStatus"
                class="fa-solid fa-circle-notch fa-spin absolute right-4 top-1/2 -translate-y-1/2 text-sm text-brand-500"></i>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <span class="mr-1 text-xs font-semibold uppercase tracking-wide text-slate-400">Status dokumen</span>

            @foreach ($pilihanFilter as $nilai => $label)
            <button type="button" wire:click="$set('filterStatus', '{{ $nilai }}')"
                class="rounded-full px-4 py-1.5 text-xs font-semibold transition
                        {{ ($filterStatus ?? '') === (string) $nilai
                            ? 'bg-brand-500 text-white shadow-sm shadow-brand-500/30'
                            : 'bg-brand-50 text-brand-700 hover:bg-brand-100' }}">
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
                        <th class="px-5 py-3.5">No.</th>
                        <th class="px-5 py-3.5">Pendaftar</th>
                        <th class="px-5 py-3.5 text-center">Total</th>
                        <th class="px-5 py-3.5">Status Dokumen</th>
                        <th class="px-5 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-brand-50">
                    @forelse ($pendaftars as $index => $pendaftar)
                    @php
                    $total = $pendaftar->dokumens->count();
                    $menunggu = $pendaftar->dokumens->where('status_verifikasi', 'menunggu')->count();
                    $diterima = $pendaftar->dokumens->where('status_verifikasi', 'diterima')->count();
                    $ditolak = $pendaftar->dokumens->where('status_verifikasi', 'ditolak')->count();
                    @endphp

                    <tr wire:key="pendaftar-{{ $pendaftar->id }}" class="transition hover:bg-brand-50/40">
                        <td class="px-5 py-4 text-slate-400">{{ $pendaftars->firstItem() + $index }}</td>

                        <td class="px-5 py-4">
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

                        <td class="px-5 py-4 text-center text-base font-extrabold text-brand-900">{{ $total }}</td>

                        <td class="min-w-56 px-5 py-4">
                            {{-- Bar proporsi --}}
                            <div class="flex h-2 overflow-hidden rounded-full bg-slate-100">
                                @if ($total > 0)
                                <span class="bg-brand-500" style="width: {{ ($diterima / $total) * 100 }}%"></span>
                                <span class="bg-amber-400" style="width: {{ ($menunggu / $total) * 100 }}%"></span>
                                <span class="bg-red-400" style="width: {{ ($ditolak / $total) * 100 }}%"></span>
                                @endif
                            </div>

                            <div class="mt-2 flex flex-wrap gap-1.5 text-[11px] font-semibold">
                                <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2 py-0.5 text-amber-700">
                                    <i class="fa-solid fa-hourglass-half text-[9px]"></i> {{ $menunggu }}
                                </span>
                                <span class="inline-flex items-center gap-1 rounded-full bg-brand-100 px-2 py-0.5 text-brand-700">
                                    <i class="fa-solid fa-circle-check text-[9px]"></i> {{ $diterima }}
                                </span>
                                <span class="inline-flex items-center gap-1 rounded-full bg-red-100 px-2 py-0.5 text-red-700">
                                    <i class="fa-solid fa-circle-xmark text-[9px]"></i> {{ $ditolak }}
                                </span>
                            </div>
                        </td>

                        <td class="px-5 py-4">
                            <div class="flex justify-center">
                                <button type="button" wire:click="lihatDetail({{ $pendaftar->id }})"
                                    class="inline-flex items-center gap-2 rounded-xl {{ $menunggu > 0 ? 'bg-brand-500 text-white shadow-sm shadow-brand-500/30 hover:bg-brand-600' : 'border border-brand-200 bg-white text-brand-700 hover:bg-brand-50' }} px-4 py-2 text-xs font-semibold transition">
                                    <i class="fa-solid {{ $menunggu > 0 ? 'fa-clipboard-check' : 'fa-eye' }}"></i>
                                    {{ $menunggu > 0 ? 'Periksa' : 'Lihat Detail' }}
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <span class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-brand-100 text-2xl text-brand-500">
                                <i class="fa-solid fa-folder-open"></i>
                            </span>
                            <p class="mt-4 font-bold text-brand-900">Belum ada pendaftar yang mengunggah dokumen</p>
                            <p class="mt-1 text-sm text-slate-500">Dokumen yang diunggah pendaftar akan tampil di sini.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-brand-100 p-4">
            {{ $pendaftars->links() }}
        </div>
    </div>

    {{-- ================= MODAL DETAIL PENDAFTAR ================= --}}
    @if ($pendaftarDetail)
    @php
    $dMenunggu = $pendaftarDetail->dokumens->where('status_verifikasi', 'menunggu')->count();
    $dDiterima = $pendaftarDetail->dokumens->where('status_verifikasi', 'diterima')->count();
    $dDitolak = $pendaftarDetail->dokumens->where('status_verifikasi', 'ditolak')->count();
    @endphp

    <div class="fixed inset-0 z-[60] flex items-end justify-center sm:items-center sm:p-4"
        x-data="{ show: false }" x-init="$nextTick(() => show = true)"
        role="dialog" aria-modal="true">

        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity duration-200"
            :class="show ? 'opacity-100' : 'opacity-0'"
            wire:click="tutupDetail"></div>

        <div class="relative z-10 flex max-h-[92vh] w-full max-w-4xl flex-col overflow-hidden rounded-t-3xl bg-white shadow-2xl transition duration-200 sm:rounded-3xl"
            :class="show ? 'translate-y-0 opacity-100' : 'translate-y-4 opacity-0'">

            {{-- Header --}}
            <div class="flex shrink-0 items-center justify-between gap-3 bg-gradient-to-br from-brand-500 to-brand-600 px-6 py-5 text-white">
                <div class="flex min-w-0 items-center gap-4">
                    <span class="grid h-14 w-14 shrink-0 place-items-center rounded-2xl bg-white text-xl font-extrabold text-brand-700 shadow-md">
                        {{ strtoupper(mb_substr($pendaftarDetail->nama_lengkap, 0, 1)) }}
                    </span>

                    <div class="min-w-0">
                        <p class="text-xs font-semibold uppercase tracking-wide text-brand-100">Detail Dokumen Pendaftar</p>
                        <h2 class="truncate text-xl font-extrabold">{{ $pendaftarDetail->nama_lengkap }}</h2>
                        <p class="font-mono text-sm text-brand-50">{{ $pendaftarDetail->nomor_pendaftaran }}</p>
                    </div>
                </div>

                <button type="button" wire:click="tutupDetail" aria-label="Tutup"
                    class="grid h-9 w-9 shrink-0 place-items-center rounded-lg text-white/80 transition hover:bg-white/15 hover:text-white">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            {{-- Isi --}}
            <div class="space-y-5 overflow-y-auto p-6">

                {{-- Ringkasan --}}
                <div class="grid grid-cols-3 gap-3">
                    <div class="rounded-xl bg-amber-50 px-4 py-3">
                        <p class="text-xs font-semibold text-amber-700">Menunggu</p>
                        <p class="mt-1 text-2xl font-extrabold text-amber-700">{{ $dMenunggu }}</p>
                    </div>
                    <div class="rounded-xl bg-brand-50 px-4 py-3">
                        <p class="text-xs font-semibold text-brand-700">Diterima</p>
                        <p class="mt-1 text-2xl font-extrabold text-brand-700">{{ $dDiterima }}</p>
                    </div>
                    <div class="rounded-xl bg-red-50 px-4 py-3">
                        <p class="text-xs font-semibold text-red-700">Ditolak</p>
                        <p class="mt-1 text-2xl font-extrabold text-red-700">{{ $dDitolak }}</p>
                    </div>
                </div>

                {{-- Daftar dokumen --}}
                <div>
                    <h3 class="mb-3 flex items-center gap-2 font-extrabold text-brand-900">
                        <i class="fa-solid fa-folder-open text-brand-500"></i>
                        Dokumen yang Diunggah
                    </h3>

                    <div class="space-y-3">
                        @forelse ($pendaftarDetail->dokumens as $dokumen)
                        @php [$kelasDok, $ikonDok] = $badgeDokumen($dokumen->status_verifikasi); @endphp

                        <div wire:key="detail-dokumen-{{ $dokumen->id }}"
                            class="rounded-2xl border border-brand-100 p-4 transition hover:border-brand-300 hover:bg-brand-50/30">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <div class="flex min-w-0 items-center gap-3">
                                    <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-brand-100 text-brand-600">
                                        <i class="fa-solid fa-file-lines"></i>
                                    </span>

                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-800">{{ $namaJenis($dokumen->jenis_dokumen) }}</p>
                                        <p class="truncate text-xs text-slate-400">{{ $dokumen->nama_file ?? basename($dokumen->file_path) }}</p>
                                    </div>
                                </div>

                                <div class="flex shrink-0 flex-wrap items-center gap-2">
                                    <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold {{ $kelasDok }}">
                                        <i class="fa-solid {{ $ikonDok }} text-[10px]"></i>
                                        {{ ucfirst($dokumen->status_verifikasi) }}
                                    </span>

                                    <button type="button" wire:click="lihatFile({{ $dokumen->id }})"
                                        class="inline-flex items-center gap-2 rounded-xl px-3.5 py-2 text-xs font-semibold transition
                                                    {{ $dokumen->status_verifikasi === 'menunggu'
                                                        ? 'bg-brand-500 text-white shadow-sm shadow-brand-500/30 hover:bg-brand-600'
                                                        : 'border border-brand-200 bg-white text-brand-700 hover:bg-brand-50' }}">
                                        <i class="fa-solid {{ $dokumen->status_verifikasi === 'menunggu' ? 'fa-clipboard-check' : 'fa-eye' }}"></i>
                                        {{ $dokumen->status_verifikasi === 'menunggu' ? 'Verifikasi' : 'Lihat File' }}
                                    </button>
                                </div>
                            </div>

                            @if ($dokumen->status_verifikasi === 'ditolak' && filled($dokumen->keterangan ?? null))
                            <p class="mt-3 flex items-start gap-2 rounded-lg bg-red-50 px-3 py-2 text-xs text-red-700">
                                <i class="fa-solid fa-circle-info mt-0.5"></i>
                                <span><span class="font-semibold">Alasan penolakan:</span> {{ $dokumen->keterangan }}</span>
                            </p>
                            @endif
                        </div>
                        @empty
                        <div class="rounded-2xl border border-dashed border-brand-200 bg-brand-50/30 px-6 py-10 text-center text-sm text-slate-400">
                            Belum ada dokumen yang diunggah.
                        </div>
                        @endforelse
                    </div>
                </div>
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

    {{-- ================= MODAL PRATINJAU & VERIFIKASI ================= --}}
    @if ($dokumenPreview)
    @php
    $extension = strtolower(pathinfo($dokumenPreview->file_path, PATHINFO_EXTENSION));
    $previewUrl = route('dokumen.preview', $dokumenPreview->id);
    [$kelasPrev, $ikonPrev] = $badgeDokumen($dokumenPreview->status_verifikasi);
    $menungguPrev = $dokumenPreview->status_verifikasi === 'menunggu';
    @endphp

    <div class="fixed inset-0 z-[70] flex items-end justify-center sm:items-center sm:p-4"
        x-data="{ show: false }" x-init="$nextTick(() => show = true)"
        role="dialog" aria-modal="true">

        <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm transition-opacity duration-200"
            :class="show ? 'opacity-100' : 'opacity-0'"
            wire:click="tutupPreview"></div>

        <div class="relative z-10 flex max-h-[95vh] w-full max-w-5xl flex-col overflow-hidden rounded-t-3xl bg-white shadow-2xl transition duration-200 sm:rounded-3xl"
            :class="show ? 'translate-y-0 opacity-100' : 'translate-y-4 opacity-0'">

            {{-- Header --}}
            <div class="flex shrink-0 items-center justify-between gap-3 border-b border-brand-100 bg-gradient-to-r from-brand-50 to-white px-6 py-4">
                <div class="flex min-w-0 items-center gap-3">
                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-brand-100 text-brand-600">
                        <i class="fa-solid fa-file-lines"></i>
                    </span>
                    <div class="min-w-0">
                        <h2 class="font-extrabold text-brand-900">{{ $namaJenis($dokumenPreview->jenis_dokumen) }}</h2>
                        <p class="truncate text-xs text-slate-500">{{ $dokumenPreview->nama_file ?? basename($dokumenPreview->file_path) }}</p>
                    </div>
                </div>

                <button type="button" wire:click="tutupPreview" aria-label="Tutup"
                    class="grid h-9 w-9 shrink-0 place-items-center rounded-lg text-slate-400 transition hover:bg-brand-50 hover:text-brand-600">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="grid min-h-0 flex-1 overflow-y-auto lg:grid-cols-5">

                {{-- Pratinjau file --}}
                <div class="bg-brand-50/60 p-4 lg:col-span-3">
                    <div class="mb-3 flex items-center justify-between">
                        <p class="text-sm font-bold text-slate-700">Pratinjau File</p>

                        <a href="{{ $previewUrl }}" target="_blank"
                            class="inline-flex items-center gap-1.5 text-xs font-semibold text-brand-600 transition hover:text-brand-700">
                            Buka tab baru <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                        </a>
                    </div>

                    <div class="flex min-h-[350px] items-center justify-center overflow-hidden rounded-2xl border border-brand-100 bg-white lg:min-h-[500px]">
                        @if (in_array($extension, ['jpg', 'jpeg', 'png', 'webp']))
                        <img src="{{ $previewUrl }}" alt="Dokumen pendaftar" class="max-h-[500px] w-full object-contain">
                        @elseif ($extension === 'pdf')
                        <iframe src="{{ $previewUrl }}" title="Preview PDF" class="h-[500px] w-full"></iframe>
                        @else
                        <div class="p-6 text-center">
                            <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-brand-100 text-2xl text-brand-500">
                                <i class="fa-solid fa-file-circle-question"></i>
                            </span>
                            <p class="mt-3 text-sm text-slate-500">Format file belum mendukung pratinjau.</p>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Panel verifikasi --}}
                <div class="space-y-5 p-5 lg:col-span-2">

                    <div class="space-y-3 rounded-2xl border border-brand-100 bg-brand-50/40 p-4">
                        <div>
                            <p class="text-xs text-slate-400">Nama Pendaftar</p>
                            <p class="font-semibold text-slate-800">{{ $pendaftarDetail->nama_lengkap }}</p>
                        </div>

                        <div>
                            <p class="text-xs text-slate-400">Nomor Pendaftaran</p>
                            <p class="font-mono text-sm text-brand-700">{{ $pendaftarDetail->nomor_pendaftaran }}</p>
                        </div>

                        <div>
                            <p class="mb-1 text-xs text-slate-400">Status Dokumen</p>
                            <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold {{ $kelasPrev }}">
                                <i class="fa-solid {{ $ikonPrev }} text-[10px]"></i>
                                {{ ucfirst($dokumenPreview->status_verifikasi) }}
                            </span>
                        </div>
                    </div>

                    @if ($menungguPrev)
                    <div>
                        <label for="keterangan" class="mb-1.5 block text-sm font-semibold text-slate-700">Keterangan</label>

                        <textarea id="keterangan" wire:model="keterangan" rows="4"
                            placeholder="Isi alasan jika dokumen ditolak..."
                            class="w-full resize-none rounded-xl border bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:ring-2
                                           {{ $errors->has('keterangan') ? 'border-red-300 bg-red-50 focus:border-red-400 focus:ring-red-100' : 'border-brand-200 focus:border-brand-400 focus:ring-brand-200' }}"></textarea>

                        @error('keterangan')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2 border-t border-brand-100 pt-4">
                        <button type="button" @click="konfirmasi = 'diterima'"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-brand-500 to-brand-600 px-4 py-3 text-sm font-semibold text-white shadow-md shadow-brand-500/30 transition hover:from-brand-600 hover:to-brand-700">
                            <i class="fa-solid fa-circle-check"></i>
                            Terima Dokumen
                        </button>

                        <button type="button" @click="konfirmasi = 'ditolak'"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-red-500 to-red-600 px-4 py-3 text-sm font-semibold text-white shadow-md shadow-red-500/30 transition hover:from-red-600 hover:to-red-700">
                            <i class="fa-solid fa-circle-xmark"></i>
                            Tolak Dokumen
                        </button>
                    </div>
                    @else
                    <div class="rounded-2xl border border-brand-100 bg-brand-50/40 p-4">
                        <p class="flex items-center gap-2 text-xs font-semibold text-brand-700">
                            <i class="fa-solid fa-circle-check"></i>
                            Dokumen ini sudah diverifikasi.
                        </p>

                        <p class="mt-2 text-sm text-slate-700">
                            {{ $dokumenPreview->keterangan ?: 'Tidak ada keterangan.' }}
                        </p>
                    </div>
                    @endif

                    <button type="button" wire:click="tutupPreview"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-brand-200 bg-white px-4 py-3 text-sm font-semibold text-slate-600 transition hover:bg-brand-50">
                        <i class="fa-solid fa-arrow-left text-xs"></i>
                        Kembali ke Daftar Dokumen
                    </button>
                </div>
            </div>
        </div>

        {{-- ============ KONFIRMASI TERIMA / TOLAK ============ --}}
        <div x-show="konfirmasi" x-cloak
            class="fixed inset-0 z-[80] flex items-end justify-center p-4 sm:items-center"
            role="dialog" aria-modal="true">

            <div x-show="konfirmasi"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click="konfirmasi = null"
                class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm"></div>

            <div x-show="konfirmasi"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95"
                class="relative z-10 w-full max-w-md overflow-hidden rounded-3xl bg-white shadow-2xl">

                <div class="h-1.5 bg-gradient-to-r"
                    :class="konfirmasi === 'ditolak' ? 'from-red-400 to-red-500' : 'from-brand-400 to-brand-600'"></div>

                <div class="p-6 text-center sm:p-7">
                    <span class="mx-auto grid h-16 w-16 place-items-center rounded-full text-2xl text-white shadow-lg"
                        :class="konfirmasi === 'ditolak'
                                ? 'bg-gradient-to-br from-red-400 to-red-500 shadow-red-500/30'
                                : 'bg-gradient-to-br from-brand-400 to-brand-600 shadow-brand-500/30'">
                        <i class="fa-solid" :class="konfirmasi === 'ditolak' ? 'fa-circle-xmark' : 'fa-circle-check'"></i>
                    </span>

                    <h3 class="mt-4 text-xl font-extrabold text-brand-900"
                        x-text="konfirmasi === 'ditolak' ? 'Tolak dokumen ini?' : 'Terima dokumen ini?'"></h3>

                    <p class="mt-1.5 text-sm text-slate-500">
                        {{ $namaJenis($dokumenPreview->jenis_dokumen) }} milik
                        <strong class="text-slate-700">{{ $pendaftarDetail->nama_lengkap }}</strong>
                        akan ditandai
                        <strong x-text="konfirmasi === 'ditolak' ? 'ditolak' : 'diterima'"></strong>.
                    </p>

                    {{-- Pengingat keterangan saat menolak --}}
                    <div x-show="konfirmasi === 'ditolak' && ! $wire.keterangan" x-cloak
                        class="mt-4 flex items-start gap-2.5 rounded-xl border border-amber-200 bg-amber-50 px-3.5 py-3 text-left text-xs text-amber-800">
                        <i class="fa-solid fa-triangle-exclamation mt-0.5"></i>
                        <p>Keterangan belum diisi. Sebaiknya tulis alasan penolakan agar pendaftar tahu apa yang perlu diperbaiki.</p>
                    </div>

                    <div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-center">
                        <button type="button" @click="konfirmasi = null"
                            class="inline-flex items-center justify-center rounded-xl border border-brand-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-brand-50">
                            Batal
                        </button>

                        <button type="button"
                            @click="$wire.verifikasi(konfirmasi).then(() => konfirmasi = null)"
                            wire:loading.attr="disabled" wire:target="verifikasi"
                            class="inline-flex items-center justify-center gap-2 rounded-xl px-6 py-2.5 text-sm font-semibold text-white shadow-md transition disabled:cursor-not-allowed disabled:opacity-60"
                            :class="konfirmasi === 'ditolak'
                                    ? 'bg-gradient-to-r from-red-500 to-red-600 shadow-red-500/30 hover:from-red-600 hover:to-red-700'
                                    : 'bg-gradient-to-r from-brand-500 to-brand-600 shadow-brand-500/30 hover:from-brand-600 hover:to-brand-700'">
                            <span wire:loading.remove wire:target="verifikasi">
                                <span x-text="konfirmasi === 'ditolak' ? 'Ya, Tolak' : 'Ya, Terima'"></span>
                            </span>
                            <span wire:loading wire:target="verifikasi" class="inline-flex items-center gap-2">
                                <i class="fa-solid fa-circle-notch fa-spin text-xs"></i> Memproses...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>