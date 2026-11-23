@php
// Gaya status: [kelas badge, kelas titik, label]
$gayaStatus = [
'draft' => ['bg-slate-100 text-slate-600', 'bg-slate-400', 'Draft'],
'dibuka' => ['bg-brand-100 text-brand-700', 'bg-brand-500', 'Dibuka'],
'ditutup' => ['bg-red-100 text-red-700', 'bg-red-400', 'Ditutup'],
];

$pilihanStatus = [
'draft' => ['Draft', 'Belum dibuka untuk pendaftar', 'fa-pen-ruler'],
'dibuka' => ['Dibuka', 'Pendaftaran dapat diakses', 'fa-door-open'],
'ditutup' => ['Ditutup', 'Pendaftaran sudah selesai', 'fa-lock'],
];

$input = 'w-full rounded-xl border bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:ring-2';
$normal = 'border-brand-200 focus:border-brand-400 focus:ring-brand-200';
$salah = 'border-red-300 bg-red-50 focus:border-red-400 focus:ring-red-100';
@endphp

{{-- Livewire: elemen root harus satu --}}
<div class="space-y-6"
    x-data="{ konfirmasi: false, hapusId: null, hapusNama: '' }"
    x-on:keydown.escape.window="konfirmasi = false">

    {{-- ================= HEADER ================= --}}
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-500 to-brand-600 p-6 text-white shadow-lg shadow-brand-500/20 sm:p-8">
        <div class="pointer-events-none absolute -right-12 -top-12 h-48 w-48 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute -bottom-16 right-1/3 h-40 w-40 rounded-full bg-white/10"></div>

        <div class="relative flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-extrabold sm:text-3xl">Periode PPDB</h1>
                <p class="mt-2 max-w-md text-sm text-brand-50">
                    Kelola periode penerimaan peserta didik baru.
                </p>

                <span class="mt-4 inline-flex items-center gap-2 rounded-full bg-white/15 px-3.5 py-1.5 text-xs font-medium backdrop-blur">
                    <i class="fa-solid fa-layer-group"></i>
                    {{ $periodes->total() }} periode terdaftar
                </span>
            </div>

            <button wire:click="tambah"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-white px-5 py-2.5 text-sm font-semibold text-brand-700 shadow-md transition hover:bg-brand-50">
                <i class="fa-solid fa-plus text-xs"></i>
                Tambah Periode
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
    <div x-data="{ show: true }" x-show="show"
        class="flex items-center gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">
        <i class="fa-solid fa-circle-exclamation text-red-500"></i>
        <span class="flex-1">{{ session('error') }}</span>
        <button type="button" @click="show = false" class="text-red-400 transition hover:text-red-600" aria-label="Tutup">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
    @endif

    {{-- ================= PENCARIAN ================= --}}
    <div class="rounded-2xl border border-brand-100 bg-white p-4 shadow-sm sm:p-5">
        <div class="relative sm:max-w-md">
            <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-sm text-brand-400"></i>

            <input type="text" wire:model.live.debounce.300ms="search"
                placeholder="Cari nama periode..."
                class="w-full rounded-xl border border-brand-100 bg-brand-50/50 py-3 pl-11 pr-11 text-sm outline-none transition
                       placeholder:text-slate-400 focus:border-brand-300 focus:bg-white focus:ring-2 focus:ring-brand-200">

            <i wire:loading wire:target="search"
                class="fa-solid fa-circle-notch fa-spin absolute right-4 top-1/2 -translate-y-1/2 text-sm text-brand-500"></i>
        </div>
    </div>

    {{-- ================= DAFTAR PERIODE ================= --}}
    <div class="overflow-hidden rounded-2xl border border-brand-100 bg-white shadow-sm">
        <div class="flex items-center justify-between gap-3 border-b border-brand-100 bg-gradient-to-r from-brand-50 to-white px-5 py-4">
            <div>
                <h2 class="font-extrabold text-brand-900">Daftar Periode</h2>
                <p class="mt-0.5 text-xs text-slate-500">Atur status untuk membuka atau menutup pendaftaran.</p>
            </div>
        </div>

        @forelse ($periodes as $index => $periode)
        @php
        $mulai = \Carbon\Carbon::parse($periode->tanggal_mulai);
        $selesai = \Carbon\Carbon::parse($periode->tanggal_selesai);

        // Pendaftaran hanya bisa diakses jika status "dibuka" DAN hari ini berada dalam rentang tanggal
        $dalamRentang = now()->between($mulai->copy()->startOfDay(), $selesai->copy()->endOfDay());

        [$kelasStatus, $titikStatus, $labelStatus] = $gayaStatus[$periode->status]
        ?? ['bg-slate-100 text-slate-600', 'bg-slate-400', ucfirst((string) $periode->status)];
        @endphp

        <div wire:key="periode-{{ $periode->id }}"
            class="relative flex flex-col gap-4 border-b border-brand-50 p-5 pl-6 transition last:border-0 hover:bg-brand-50/40 xl:flex-row xl:items-center xl:justify-between">

            <span class="absolute inset-y-0 left-0 w-1.5 {{ $titikStatus }}"></span>

            {{-- Info periode --}}
            <div class="flex min-w-0 items-start gap-4">
                <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-brand-100 text-sm font-extrabold text-brand-700">
                    {{ $periodes->firstItem() + $index }}
                </span>

                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <h3 class="font-bold text-brand-900">{{ $periode->nama }}</h3>

                        <span class="inline-flex items-center gap-2 rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $kelasStatus }}">
                            <span class="relative flex h-1.5 w-1.5">
                                @if ($periode->status === 'dibuka' && $dalamRentang)
                                <span class="absolute inline-flex h-full w-full animate-ping rounded-full opacity-75 {{ $titikStatus }}"></span>
                                @endif
                                <span class="relative inline-flex h-1.5 w-1.5 rounded-full {{ $titikStatus }}"></span>
                            </span>
                            {{ $labelStatus }}
                        </span>
                    </div>

                    <div class="mt-1.5 flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-slate-500">
                        <span class="inline-flex items-center gap-1.5">
                            <i class="fa-solid fa-calendar-days text-xs text-brand-400"></i>
                            {{ $periode->tahunAjaran->nama ?? '-' }}
                        </span>

                        <span class="inline-flex items-center gap-1.5">
                            <i class="fa-regular fa-calendar text-xs text-brand-400"></i>
                            {{ $mulai->translatedFormat('d M Y') }}
                            <span class="text-slate-300">s/d</span>
                            {{ $selesai->translatedFormat('d M Y') }}
                        </span>
                    </div>

                    {{-- Petunjuk kondisi periode --}}
                    @if ($periode->status === 'dibuka')
                    @if ($dalamRentang)
                    <p class="mt-2 inline-flex items-center gap-1.5 text-xs font-medium text-brand-700">
                        <i class="fa-solid fa-circle-check"></i>
                        Pendaftaran aktif · berakhir {{ $selesai->endOfDay()->diffForHumans() }}
                    </p>
                    @else
                    <p class="mt-2 inline-flex items-center gap-1.5 rounded-lg bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-700">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        Status dibuka, tetapi hari ini di luar rentang tanggal sehingga pendaftaran belum dapat diakses.
                    </p>
                    @endif
                    @endif
                </div>
            </div>

            {{-- Aksi --}}
            <div class="flex shrink-0 flex-wrap items-center gap-3 xl:justify-end">

                {{-- Ubah status --}}
                <div class="inline-flex rounded-xl bg-brand-100/60 p-1" role="group" aria-label="Ubah status periode">
                    @foreach ($pilihanStatus as $nilai => [$judulStatus])
                    <button type="button"
                        wire:click="ubahStatus({{ $periode->id }}, '{{ $nilai }}')"
                        @disabled($periode->status === $nilai)
                        class="rounded-lg px-3 py-1.5 text-xs font-semibold transition
                        {{ $periode->status === $nilai
                                        ? 'cursor-default bg-white text-brand-700 shadow-sm'
                                        : 'text-slate-500 hover:text-brand-700' }}">
                        {{ $judulStatus }}
                    </button>
                    @endforeach
                </div>

                <div class="flex gap-2">
                    <button type="button" wire:click="edit({{ $periode->id }})"
                        class="inline-flex items-center gap-2 rounded-xl border border-brand-200 bg-white px-3.5 py-2 text-xs font-semibold text-brand-700 transition hover:bg-brand-50">
                        <i class="fa-solid fa-pen-to-square"></i>
                        Edit
                    </button>

                    <button type="button"
                        @click="konfirmasi = true; hapusId = {{ $periode->id }}; hapusNama = @js($periode->nama)"
                        class="grid h-9 w-9 place-items-center rounded-xl bg-red-50 text-sm text-red-600 transition hover:bg-red-100"
                        title="Hapus periode" aria-label="Hapus {{ $periode->nama }}">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="px-5 py-16 text-center">
            <span class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-brand-100 text-2xl text-brand-500">
                <i class="fa-solid fa-folder-open"></i>
            </span>

            <h3 class="mt-4 font-bold text-brand-900">
                {{ filled($search ?? null) ? 'Periode tidak ditemukan' : 'Belum ada periode PPDB' }}
            </h3>

            <p class="mt-1 text-sm text-slate-500">
                {{ filled($search ?? null)
                        ? 'Coba gunakan kata kunci lain.'
                        : 'Klik "Tambah Periode" untuk membuat periode penerimaan pertama.' }}
            </p>
        </div>
        @endforelse
    </div>

    <div>{{ $periodes->links() }}</div>

    {{-- ================= MODAL FORM PERIODE ================= --}}
    @if ($showForm)
    <div class="fixed inset-0 z-[60] flex items-end justify-center sm:items-center sm:p-4"
        x-data="{ show: false }" x-init="$nextTick(() => show = true)"
        x-on:keydown.escape.window="$wire.set('showForm', false)"
        role="dialog" aria-modal="true">

        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity duration-200"
            :class="show ? 'opacity-100' : 'opacity-0'"
            wire:click="$set('showForm', false)"></div>

        <div class="relative z-10 flex max-h-[92vh] w-full max-w-xl flex-col overflow-hidden rounded-t-3xl bg-white shadow-2xl transition duration-200 sm:rounded-3xl"
            :class="show ? 'translate-y-0 opacity-100' : 'translate-y-4 opacity-0'">

            {{-- Header --}}
            <div class="flex shrink-0 items-center justify-between gap-3 border-b border-brand-100 bg-gradient-to-r from-brand-50 to-white px-6 py-4">
                <div class="flex items-center gap-3">
                    <span class="grid h-10 w-10 place-items-center rounded-xl bg-brand-100 text-brand-600">
                        <i class="fa-solid {{ $periodeId ? 'fa-pen-to-square' : 'fa-plus' }}"></i>
                    </span>
                    <div>
                        <h2 class="text-lg font-extrabold text-brand-900">
                            {{ $periodeId ? 'Edit Periode' : 'Tambah Periode' }}
                        </h2>
                        <p class="text-xs text-slate-500">Isi informasi periode penerimaan.</p>
                    </div>
                </div>

                <button type="button" wire:click="$set('showForm', false)" aria-label="Tutup"
                    class="grid h-9 w-9 place-items-center rounded-lg text-slate-400 transition hover:bg-brand-50 hover:text-brand-600">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form wire:submit="simpan" class="flex min-h-0 flex-1 flex-col">
                <div class="space-y-5 overflow-y-auto p-6">

                    <div>
                        <label for="nama" class="mb-1.5 block text-sm font-semibold text-slate-700">Nama Periode</label>

                        <input id="nama" type="text" wire:model="nama"
                            placeholder="Contoh: PPDB Gelombang 1"
                            class="{{ $input }} {{ $errors->has('nama') ? $salah : $normal }}">

                        @error('nama')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="tahun_ajaran_id" class="mb-1.5 block text-sm font-semibold text-slate-700">Tahun Ajaran</label>

                        <select id="tahun_ajaran_id" wire:model="tahun_ajaran_id"
                            class="{{ $input }} {{ $errors->has('tahun_ajaran_id') ? $salah : $normal }}">
                            <option value="">Pilih Tahun Ajaran</option>

                            @foreach ($tahunAjarans as $tahun)
                            <option value="{{ $tahun->id }}">{{ $tahun->nama }}</option>
                            @endforeach
                        </select>

                        @error('tahun_ajaran_id')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div x-data class="space-y-3">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label for="tanggal_mulai" class="mb-1.5 block text-sm font-semibold text-slate-700">Tanggal Mulai</label>

                                <input id="tanggal_mulai" type="date" wire:model="tanggal_mulai"
                                    class="{{ $input }} {{ $errors->has('tanggal_mulai') ? $salah : $normal }}">

                                @error('tanggal_mulai')
                                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="tanggal_selesai" class="mb-1.5 block text-sm font-semibold text-slate-700">Tanggal Selesai</label>

                                <input id="tanggal_selesai" type="date" wire:model="tanggal_selesai"
                                    class="{{ $input }} {{ $errors->has('tanggal_selesai') ? $salah : $normal }}">

                                @error('tanggal_selesai')
                                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div x-show="$wire.tanggal_mulai && $wire.tanggal_selesai && $wire.tanggal_selesai < $wire.tanggal_mulai" x-cloak
                            class="flex items-start gap-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                            <i class="fa-solid fa-triangle-exclamation mt-0.5"></i>
                            <p>Tanggal selesai lebih awal dari tanggal mulai. Periksa kembali rentang periode.</p>
                        </div>
                    </div>

                    {{-- Status --}}
                    <div>
                        <span class="mb-1.5 block text-sm font-semibold text-slate-700">Status</span>

                        <div class="grid gap-3 sm:grid-cols-3">
                            @foreach ($pilihanStatus as $nilai => [$judulStatus, $deskStatus, $ikonStatus])
                            <label class="relative block cursor-pointer">
                                <input type="radio" wire:model="status" value="{{ $nilai }}" class="peer sr-only">

                                <div class="h-full rounded-2xl border-2 border-slate-200 bg-white p-3.5 pr-9 transition
                                                    hover:border-brand-300 peer-checked:border-brand-500 peer-checked:bg-brand-50 peer-focus-visible:ring-2 peer-focus-visible:ring-brand-300">
                                    <span class="grid h-9 w-9 place-items-center rounded-lg bg-brand-100 text-brand-600">
                                        <i class="fa-solid {{ $ikonStatus }}"></i>
                                    </span>
                                    <span class="mt-2 block text-sm font-bold text-slate-800">{{ $judulStatus }}</span>
                                    <span class="mt-0.5 block text-[11px] leading-snug text-slate-500">{{ $deskStatus }}</span>
                                </div>

                                <span class="pointer-events-none absolute right-3 top-3 h-5 w-5 rounded-full border-2 border-slate-300 bg-white transition
                                                     peer-checked:border-brand-500 peer-checked:bg-brand-500 peer-checked:shadow-[inset_0_0_0_3px_#fff]"></span>
                            </label>
                            @endforeach
                        </div>

                        @error('status')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Footer --}}
                <div class="flex shrink-0 justify-end gap-2 border-t border-brand-100 bg-white px-6 py-4">
                    <button type="button" wire:click="$set('showForm', false)"
                        class="rounded-xl border border-brand-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-brand-50">
                        Batal
                    </button>

                    <button type="submit" wire:loading.attr="disabled" wire:target="simpan"
                        class="inline-flex items-center gap-2 rounded-xl bg-brand-500 px-6 py-2.5 text-sm font-semibold text-white shadow-md shadow-brand-500/30 transition
                                   hover:bg-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-300 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60">
                        <span wire:loading.remove wire:target="simpan" class="inline-flex items-center gap-2">
                            <i class="fa-solid fa-floppy-disk text-xs"></i>
                            Simpan
                        </span>
                        <span wire:loading wire:target="simpan" class="inline-flex items-center gap-2">
                            <i class="fa-solid fa-circle-notch fa-spin text-xs"></i>
                            Menyimpan...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- ================= MODAL KONFIRMASI HAPUS ================= --}}
    <div x-show="konfirmasi" x-cloak
        class="fixed inset-0 z-[70] flex items-end justify-center p-4 sm:items-center"
        role="dialog" aria-modal="true">

        <div x-show="konfirmasi"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="konfirmasi = false"
            class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm"></div>

        <div x-show="konfirmasi"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95"
            class="relative z-10 w-full max-w-md overflow-hidden rounded-3xl bg-white shadow-2xl">

            <div class="h-1.5 bg-gradient-to-r from-red-400 to-red-500"></div>

            <div class="p-6 text-center sm:p-7">
                <span class="mx-auto grid h-16 w-16 place-items-center rounded-full bg-gradient-to-br from-red-400 to-red-500 text-2xl text-white shadow-lg shadow-red-500/30">
                    <i class="fa-solid fa-trash"></i>
                </span>

                <h3 class="mt-4 text-xl font-extrabold text-brand-900">Hapus periode ini?</h3>
                <p class="mt-1.5 text-sm text-slate-500">
                    Periode <strong class="text-slate-700" x-text="hapusNama"></strong>
                    akan dihapus dan tidak dapat dikembalikan.
                </p>

                <div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-center">
                    <button type="button" @click="konfirmasi = false"
                        class="inline-flex items-center justify-center rounded-xl border border-brand-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-brand-50">
                        Batal
                    </button>

                    <button type="button"
                        @click="$wire.hapus(hapusId).then(() => konfirmasi = false)"
                        wire:loading.attr="disabled" wire:target="hapus"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-red-500 to-red-600 px-6 py-2.5 text-sm font-semibold text-white shadow-md shadow-red-500/30 transition
                               hover:from-red-600 hover:to-red-700 disabled:cursor-not-allowed disabled:opacity-60">
                        <span wire:loading.remove wire:target="hapus" class="inline-flex items-center gap-2">
                            <i class="fa-solid fa-trash text-xs"></i> Ya, Hapus
                        </span>
                        <span wire:loading wire:target="hapus" class="inline-flex items-center gap-2">
                            <i class="fa-solid fa-circle-notch fa-spin text-xs"></i> Menghapus...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>