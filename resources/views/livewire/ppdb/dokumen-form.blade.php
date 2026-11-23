@php
// $jenisDokumen, $wajib, dan $kurang dikirim dari komponen DokumenForm

// Dokumen yang ditolak tidak dihitung sebagai "sudah diunggah"
$terunggah = $dokumens->where('status_verifikasi', '!=', 'ditolak')
->pluck('jenis_dokumen')->unique()->values()->all();

$jmlWajib = count($wajib);
$jmlSudah = $jmlWajib - count($kurang);
$lengkap = empty($kurang);

// Gaya & label status verifikasi
$statusStyle = function ($status) {
$s = strtolower((string) $status);

return match (true) {
$s === 'menunggu'
=> ['bg-amber-100 text-amber-700', 'Menunggu verifikasi'],
in_array($s, ['diterima', 'disetujui', 'terverifikasi', 'valid', 'verified'])
=> ['bg-green-100 text-green-700', ucfirst($s)],
in_array($s, ['ditolak', 'tidak_valid', 'invalid'])
=> ['bg-red-100 text-red-700', ucfirst(str_replace('_', ' ', $s))],
default
=> ['bg-slate-100 text-slate-600', ucfirst(str_replace('_', ' ', $s))],
};
};
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
                <h1 class="mt-2 text-2xl font-extrabold sm:text-3xl">Upload Dokumen PPDB</h1>

                <div class="mt-3 inline-flex items-center gap-2.5 rounded-full bg-white/15 py-1.5 pl-1.5 pr-4 backdrop-blur">
                    <span class="grid h-7 w-7 place-items-center rounded-full bg-white text-xs font-extrabold text-green-700">
                        {{ strtoupper(mb_substr($pendaftar->nama_lengkap, 0, 1)) }}
                    </span>
                    <span class="text-sm font-medium">{{ $pendaftar->nama_lengkap }}</span>
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
                        <span class="grid h-7 w-7 place-items-center rounded-full bg-white text-green-700 ring-4 ring-white/30">2</span>
                        <span>Dokumen</span>
                    </li>
                    <li class="h-0.5 w-8 rounded-full bg-white/30 sm:w-14"></li>
                    <li class="flex items-center gap-2 text-green-100">
                        <span class="grid h-7 w-7 place-items-center rounded-full bg-white/20">3</span>
                        <span class="hidden sm:inline">Pembayaran</span>
                    </li>
                </ol>
            </div>
        </section>

        {{-- ================= KREDENSIAL ================= --}}
        @if (session()->has('ppdb_kredensial'))
        <section class="overflow-hidden rounded-3xl border border-green-200 bg-white shadow-sm">
            <div class="flex items-start gap-4 border-b border-green-100 bg-green-50 p-5">
                <span class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-green-500 text-white shadow-md shadow-green-500/30">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 13l4 4L19 7" />
                    </svg>
                </span>
                <div>
                    <h2 class="font-extrabold text-green-900">Pendaftaran Berhasil!</h2>
                    <p class="mt-0.5 text-sm text-green-800">
                        Simpan informasi berikut untuk mengecek status pendaftaran. Disarankan untuk menyalin atau menangkap layar.
                    </p>
                </div>
            </div>

            <div class="grid gap-4 p-5 sm:grid-cols-2">
                @foreach ([
                ['Nomor Pendaftaran', session('ppdb_kredensial.nomor_pendaftaran')],
                ['Kode Akses', session('ppdb_kredensial.kode_akses')],
                ] as [$labelKredensial, $nilaiKredensial])
                <div x-data="{
                                copied: false,
                                salin(teks) {
                                    if (navigator.clipboard) { navigator.clipboard.writeText(teks); }
                                    this.copied = true;
                                    setTimeout(() => this.copied = false, 1800);
                                }
                            }"
                    class="rounded-2xl border border-green-100 bg-green-50/50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">{{ $labelKredensial }}</p>

                    <div class="mt-1.5 flex items-center justify-between gap-3">
                        <p class="break-all font-mono text-lg font-extrabold tracking-wider text-green-800">{{ $nilaiKredensial }}</p>

                        <button type="button" @click="salin(@js($nilaiKredensial))"
                            class="inline-flex shrink-0 items-center gap-1.5 rounded-lg border border-green-200 bg-white px-3 py-1.5 text-xs font-semibold text-green-700 transition hover:bg-green-50">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" x-show="! copied">
                                <rect x="9" y="9" width="11" height="11" rx="2" />
                                <path d="M5 15V6a2 2 0 0 1 2-2h9" />
                            </svg>
                            <svg class="h-3.5 w-3.5 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" x-show="copied" x-cloak>
                                <path d="M5 13l4 4L19 7" />
                            </svg>
                            <span x-text="copied ? 'Tersalin' : 'Salin'">Salin</span>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </section>
        @endif

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

        @if (session('error'))
        <div x-data="{ show: true }" x-show="show"
            class="flex items-start gap-3 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
            <svg class="mt-0.5 h-5 w-5 shrink-0 text-red-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="9" />
                <path d="M12 8v5M12 16h.01" />
            </svg>
            <span class="flex-1">{{ session('error') }}</span>
            <button type="button" @click="show = false" class="text-red-400 transition hover:text-red-600" aria-label="Tutup">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round">
                    <path d="M6 6l12 12M18 6L6 18" />
                </svg>
            </button>
        </div>
        @endif

        <div class="grid items-start gap-6 lg:grid-cols-3">

            {{-- ================= KOLOM UTAMA ================= --}}
            <div class="min-w-0 space-y-6 lg:col-span-2">

                {{-- Form upload --}}
                <form id="dok-form" wire:submit="simpan"
                    x-data="{ drag: false, uploading: false, progress: 0 }"
                    x-on:livewire-upload-start="uploading = true; progress = 0"
                    x-on:livewire-upload-finish="uploading = false"
                    x-on:livewire-upload-error="uploading = false"
                    x-on:livewire-upload-progress="progress = $event.detail.progress"
                    class="scroll-mt-24 rounded-3xl border border-green-100 bg-white p-5 shadow-sm sm:p-6">

                    <div class="mb-5 flex items-center gap-3">
                        <span class="grid h-11 w-11 place-items-center rounded-2xl bg-green-100 text-green-600">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 16V4m0 0l-4 4m4-4l4 4" />
                                <path d="M4 16v3a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-3" />
                            </svg>
                        </span>
                        <div>
                            <h2 class="text-lg font-extrabold text-slate-800">Unggah Dokumen Baru</h2>
                            <p class="text-sm text-slate-500">Pilih jenis dokumen lalu unggah filenya.</p>
                        </div>
                    </div>

                    <div class="space-y-5">
                        <div>
                            <label for="jenis_dokumen" class="dok-label">Jenis Dokumen</label>
                            <select id="jenis_dokumen" wire:model="jenis_dokumen"
                                class="dok-input @error('jenis_dokumen') is-invalid @enderror">
                                <option value="">Pilih dokumen</option>
                                @foreach ($jenisDokumen as $value => $label)
                                <option value="{{ $value }}">{{ $label }}{{ in_array($value, $wajib, true) ? '' : ' (opsional)' }}</option>
                                @endforeach
                            </select>
                            @error('jenis_dokumen')
                            <p class="dok-error" role="alert">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <span class="dok-label">File</span>

                            <label for="dok-file"
                                @dragover.prevent="drag = true"
                                @dragleave.prevent="drag = false"
                                @drop.prevent="drag = false; $refs.berkas.files = $event.dataTransfer.files; $refs.berkas.dispatchEvent(new Event('change', { bubbles: true }))"
                                :class="drag ? 'border-green-500 bg-green-100/60' : 'border-green-300 bg-green-50/40 hover:bg-green-50'"
                                class="flex cursor-pointer flex-col items-center justify-center gap-2 rounded-2xl border-2 border-dashed px-4 py-8 text-center transition
                                       @error('file') !border-red-300 !bg-red-50 @enderror">

                                @if ($file)
                                @php
                                $bisaPratinjau = method_exists($file, 'isPreviewable') && $file->isPreviewable();
                                @endphp

                                @if ($bisaPratinjau)
                                <img src="{{ $file->temporaryUrl() }}" alt="Pratinjau"
                                    class="h-24 w-24 rounded-xl border border-green-200 object-cover shadow-sm">
                                @else
                                <span class="grid h-14 w-14 place-items-center rounded-2xl bg-green-100 text-green-600">
                                    <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M7 3h7l5 5v12a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1z" />
                                        <path d="M14 3v5h5" />
                                    </svg>
                                </span>
                                @endif

                                <p class="max-w-full break-all text-sm font-semibold text-green-800">{{ $file->getClientOriginalName() }}</p>
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
                                <p class="text-xs text-slate-400">PDF, JPG, atau PNG · maksimal 5 MB</p>
                                @endif
                            </label>

                            <input id="dok-file" x-ref="berkas" type="file" wire:model="file"
                                accept=".pdf,.jpg,.jpeg,.png" class="sr-only">

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

                            @error('file')
                            <p class="dok-error" role="alert">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end border-t border-green-100 pt-5">
                        <button type="submit" :disabled="uploading" wire:loading.attr="disabled" wire:target="simpan"
                            class="inline-flex items-center gap-2 rounded-xl bg-green-600 px-6 py-2.5 text-sm font-semibold text-white shadow-md shadow-green-600/30 transition hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60">
                            <span wire:loading.remove wire:target="simpan" class="inline-flex items-center gap-2">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 16V4m0 0l-4 4m4-4l4 4" />
                                    <path d="M4 16v3a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-3" />
                                </svg>
                                Upload Dokumen
                            </span>
                            <span wire:loading wire:target="simpan" class="inline-flex items-center gap-2">
                                <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                                    <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="3" opacity=".25" />
                                    <path d="M21 12a9 9 0 0 0-9-9" stroke="currentColor" stroke-width="3" stroke-linecap="round" />
                                </svg>
                                Mengunggah...
                            </span>
                        </button>
                    </div>
                </form>

                {{-- Dokumen yang diunggah --}}
                <section class="rounded-3xl border border-green-100 bg-white p-5 shadow-sm sm:p-6">
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <h2 class="text-lg font-extrabold text-slate-800">Dokumen yang Diunggah</h2>
                        <span class="rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-bold text-green-700">{{ $dokumens->count() }}</span>
                    </div>

                    <div class="space-y-3">
                        @forelse ($dokumens as $dokumen)
                        @php
                        [$kelasStatus, $labelStatus] = $statusStyle($dokumen->status_verifikasi);
                        $ekstensi = strtoupper(pathinfo($dokumen->nama_file, PATHINFO_EXTENSION)) ?: 'FILE';
                        @endphp

                        <div wire:key="dokumen-{{ $dokumen->id }}"
                            class="flex items-center gap-4 rounded-2xl border border-green-100 p-4 transition hover:border-green-300 hover:bg-green-50/40">

                            <span class="grid h-12 w-12 shrink-0 place-items-center rounded-xl bg-green-100 text-xs font-extrabold tracking-wide text-green-700">
                                {{ $ekstensi }}
                            </span>

                            <div class="min-w-0 flex-1">
                                <p class="truncate font-semibold text-slate-800" title="{{ $dokumen->nama_file }}">{{ $dokumen->nama_file }}</p>

                                <div class="mt-1 flex flex-wrap items-center gap-x-2 gap-y-1">
                                    <span class="text-xs text-slate-500">
                                        {{ ucfirst(str_replace('_', ' ', $dokumen->jenis_dokumen)) }}
                                    </span>
                                    <span class="rounded-full px-2.5 py-0.5 text-[11px] font-semibold {{ $kelasStatus }}">
                                        {{ $labelStatus }}
                                    </span>
                                </div>
                            </div>

                            @if ($dokumen->status_verifikasi === 'menunggu')
                            <button type="button" wire:click="hapus({{ $dokumen->id }})"
                                wire:confirm="Hapus dokumen ini?"
                                class="inline-flex shrink-0 items-center gap-1.5 rounded-lg border border-red-200 bg-white px-3 py-2 text-xs font-semibold text-red-600 transition hover:bg-red-50">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 7h16M9 7V4h6v3M6 7l1 13h10l1-13" />
                                </svg>
                                <span class="hidden sm:inline">Hapus</span>
                            </button>
                            @endif
                        </div>
                        @empty
                        <div class="rounded-2xl border border-dashed border-green-200 bg-green-50/30 px-6 py-10 text-center">
                            <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-green-100 text-green-500">
                                <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7z" />
                                </svg>
                            </span>
                            <p class="mt-3 font-semibold text-slate-700">Belum ada dokumen</p>
                            <p class="mt-1 text-sm text-slate-500">Dokumen yang Anda unggah akan tampil di sini.</p>
                        </div>
                        @endforelse
                    </div>
                </section>

                {{-- Lanjut ke pembayaran --}}
                <section class="rounded-3xl border bg-white p-5 shadow-sm sm:p-6 {{ $lengkap ? 'border-green-100' : 'border-amber-200' }}">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                        <div class="flex items-start gap-3">
                            <span class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl {{ $lengkap ? 'bg-green-100 text-green-600' : 'bg-amber-100 text-amber-600' }}">
                                @if ($lengkap)
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M5 13l4 4L19 7" />
                                </svg>
                                @else
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="5" y="11" width="14" height="9" rx="2" />
                                    <path d="M8 11V8a4 4 0 0 1 8 0v3" />
                                </svg>
                                @endif
                            </span>

                            <div>
                                <h2 class="font-extrabold text-slate-800">
                                    {{ $lengkap ? 'Dokumen sudah lengkap' : 'Dokumen belum lengkap' }}
                                </h2>
                                <p class="mt-0.5 text-sm text-slate-500">
                                    @if ($lengkap)
                                    Semua dokumen wajib sudah diunggah. Anda dapat melanjutkan ke pembayaran.
                                    @else
                                    Unggah {{ count($kurang) }} dokumen wajib lagi untuk dapat melanjutkan ke pembayaran.
                                    @endif
                                </p>
                            </div>
                        </div>

                        <button type="button" wire:click="lanjut" wire:loading.attr="disabled" wire:target="lanjut"
                            @disabled(! $lengkap)
                            @unless ($lengkap) title="Lengkapi dokumen wajib terlebih dahulu" aria-disabled="true" @endunless
                            class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl px-6 py-3 text-sm font-semibold transition focus:outline-none
                                {{ $lengkap
                                    ? 'bg-green-600 text-white shadow-md shadow-green-600/30 hover:bg-green-700 focus:ring-2 focus:ring-green-400 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60'
                                    : 'cursor-not-allowed bg-slate-200 text-slate-400' }}">
                            <span wire:loading.remove wire:target="lanjut" class="inline-flex items-center gap-2">
                                @unless ($lengkap)
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="5" y="11" width="14" height="9" rx="2" />
                                    <path d="M8 11V8a4 4 0 0 1 8 0v3" />
                                </svg>
                                @endunless
                                Lanjut ke Pembayaran
                                @if ($lengkap)
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M5 12h14m-6-6l6 6-6 6" />
                                </svg>
                                @endif
                            </span>
                            <span wire:loading wire:target="lanjut" class="inline-flex items-center gap-2">
                                <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                                    <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="3" opacity=".25" />
                                    <path d="M21 12a9 9 0 0 0-9-9" stroke="currentColor" stroke-width="3" stroke-linecap="round" />
                                </svg>
                                Memproses...
                            </span>
                        </button>
                    </div>

                    {{-- Daftar dokumen yang masih kurang --}}
                    @unless ($lengkap)
                    <div class="mt-4 border-t border-amber-100 pt-4">
                        <p class="mb-2 text-xs font-bold uppercase tracking-wide text-amber-700">Belum diunggah</p>

                        <div class="flex flex-wrap gap-2">
                            @foreach ($kurang as $jenis)
                            <button type="button"
                                wire:click="$set('jenis_dokumen', '{{ $jenis }}')"
                                x-data
                                @click="document.getElementById('dok-form')?.scrollIntoView({ behavior: 'smooth', block: 'center' })"
                                class="inline-flex items-center gap-1.5 rounded-full border border-amber-200 bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-800 transition hover:bg-amber-100">
                                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 5v14M5 12h14" />
                                </svg>
                                {{ $jenisDokumen[$jenis] ?? $jenis }}
                            </button>
                            @endforeach
                        </div>
                    </div>
                    @endunless
                </section>
            </div>

            {{-- ================= KOLOM SAMPING ================= --}}
            <aside class="space-y-6 lg:sticky lg:top-24">

                {{-- Kelengkapan --}}
                <section class="rounded-3xl border border-green-100 bg-white p-5 shadow-sm">
                    <div class="flex items-center justify-between gap-3">
                        <h3 class="font-extrabold text-slate-800">Kelengkapan Dokumen</h3>
                        <span class="text-xs font-bold {{ $lengkap ? 'text-green-700' : 'text-amber-700' }}">{{ $jmlSudah }}/{{ $jmlWajib }} wajib</span>
                    </div>

                    <div class="mt-3 h-2 overflow-hidden rounded-full bg-green-100">
                        <div class="h-full rounded-full bg-gradient-to-r from-green-400 to-green-600 transition-all duration-500"
                            style="width: {{ $jmlWajib ? ($jmlSudah / $jmlWajib) * 100 : 0 }}%"></div>
                    </div>

                    <ul class="mt-4 space-y-2">
                        @foreach ($jenisDokumen as $value => $label)
                        @php
                        $ada = in_array($value, $terunggah, true);
                        $isWajib = in_array($value, $wajib, true);
                        @endphp

                        <li>
                            <button type="button"
                                wire:click="$set('jenis_dokumen', '{{ $value }}')"
                                x-data
                                @click="document.getElementById('dok-form')?.scrollIntoView({ behavior: 'smooth', block: 'center' })"
                                class="flex w-full items-center gap-3 rounded-xl border px-3 py-2.5 text-left text-sm transition
                                        {{ $ada
                                            ? 'border-green-200 bg-green-50 text-green-800'
                                            : 'border-slate-200 bg-white text-slate-600 hover:border-green-300 hover:bg-green-50/50' }}">
                                @if ($ada)
                                <span class="grid h-5 w-5 shrink-0 place-items-center rounded-full bg-green-500 text-white">
                                    <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M5 13l4 4L19 7" />
                                    </svg>
                                </span>
                                @else
                                <span class="h-5 w-5 shrink-0 rounded-full border-2 border-dashed border-slate-300"></span>
                                @endif

                                <span class="flex-1 font-medium">{{ $label }}</span>

                                @if (! $isWajib)
                                <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-500">Opsional</span>
                                @elseif (! $ada)
                                <span class="text-[11px] font-semibold text-green-600">Unggah</span>
                                @endif
                            </button>
                        </li>
                        @endforeach
                    </ul>
                </section>

                {{-- Ketentuan --}}
                <section class="rounded-3xl border border-green-100 bg-green-50 p-5">
                    <h3 class="flex items-center gap-2 font-extrabold text-green-900">
                        <svg class="h-5 w-5 text-green-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="9" />
                            <path d="M12 8h.01M11 12h1v4h1" />
                        </svg>
                        Ketentuan File
                    </h3>

                    <ul class="mt-3 space-y-2 text-sm text-green-900/80">
                        <li class="flex gap-2"><span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-green-500"></span> Semua dokumen wajib harus diunggah sebelum lanjut ke pembayaran.</li>
                        <li class="flex gap-2"><span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-green-500"></span> Format PDF, JPG, atau PNG, maksimal 5 MB per file.</li>
                        <li class="flex gap-2"><span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-green-500"></span> Pastikan tulisan terbaca jelas dan tidak terpotong.</li>
                        <li class="flex gap-2"><span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-green-500"></span> Dokumen yang ditolak perlu diunggah ulang. Dokumen yang masih menunggu verifikasi bisa dihapus dan diganti.</li>
                    </ul>
                </section>
            </aside>
        </div>
    </div>

    <style>
        .dok-label {
            display: block;
            font-size: 0.8125rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 0.4rem;
        }

        .dok-input {
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

        .dok-input:hover {
            border-color: #86c99b;
        }

        .dok-input:focus {
            border-color: #16a34a;
            box-shadow: 0 0 0 3px rgb(22 163 74 / 15%);
        }

        .dok-input.is-invalid {
            border-color: #f87171;
            background-color: #fef2f2;
        }

        select.dok-input {
            appearance: none;
            -webkit-appearance: none;
            padding-right: 2.4rem;
            background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'><path d='M5.5 7.5 10 12l4.5-4.5' stroke='%2316a34a' stroke-width='1.8' fill='none' stroke-linecap='round' stroke-linejoin='round'/></svg>");
            background-repeat: no-repeat;
            background-position: right 0.8rem center;
            background-size: 1.1rem;
        }

        .dok-error {
            margin-top: 0.4rem;
            font-size: 0.75rem;
            color: #dc2626;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
</div>