@php
$fmt = fn ($tgl) => $tgl ? \Carbon\Carbon::parse($tgl)->translatedFormat('d M Y, H:i') : '-';

// Gaya status: [kelas badge, kelas titik]
$gayaStatus = [
'Berlangsung' => ['bg-brand-100 text-brand-700', 'bg-brand-500'],
'Terjadwal' => ['bg-amber-100 text-amber-700', 'bg-amber-400'],
'Selesai' => ['bg-slate-100 text-slate-600', 'bg-slate-400'],
'Nonaktif' => ['bg-red-100 text-red-700', 'bg-red-400'],
];

$totalTugas = $aktivitas->where('jenis', 'Tugas')->count();
$totalUjian = $aktivitas->where('jenis', '!=', 'Tugas')->count();
$berlangsung = $aktivitas->where('status', 'Berlangsung')->count();

$filterAktif = filled($search ?? null) || ($filterJenis ?? 'semua') !== 'semua';

$pilihanFilter = [
'semua' => ['Semua', 'fa-layer-group'],
'tugas' => ['Tugas', 'fa-file-arrow-up'],
'ujian' => ['Ujian', 'fa-clipboard-check'],
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
                <h1 class="text-2xl font-extrabold sm:text-3xl">Tugas &amp; Ujian</h1>
                <p class="mt-2 max-w-md text-sm text-brand-50">
                    Kelola tugas, kuis, dan ujian untuk siswa.
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('guru.tugas.create') }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-brand-700 shadow-md transition hover:bg-brand-50">
                    <i class="fa-solid fa-plus text-xs"></i>
                    Buat Tugas
                </a>

                <a href="{{ route('guru.ujian.create') }}"
                    class="inline-flex items-center gap-2 rounded-xl border border-white/40 bg-white/15 px-4 py-2.5 text-sm font-semibold text-white backdrop-blur transition hover:bg-white/25">
                    <i class="fa-solid fa-file-circle-plus text-xs"></i>
                    Buat Ujian
                </a>
            </div>
        </div>
    </section>

    {{-- ================= STATISTIK ================= --}}
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="flex items-center gap-4 rounded-2xl border border-brand-100 bg-white p-5 shadow-sm">
            <span class="grid h-12 w-12 shrink-0 place-items-center rounded-xl bg-brand-100 text-xl text-brand-600">
                <i class="fa-solid fa-layer-group"></i>
            </span>
            <div>
                <p class="text-sm text-slate-500">Total Aktivitas</p>
                <p class="text-2xl font-extrabold text-brand-900">{{ $aktivitas->count() }}</p>
            </div>
        </div>

        <div class="flex items-center gap-4 rounded-2xl border border-brand-100 bg-white p-5 shadow-sm">
            <span class="grid h-12 w-12 shrink-0 place-items-center rounded-xl bg-brand-100 text-xl text-brand-600">
                <i class="fa-solid fa-file-arrow-up"></i>
            </span>
            <div>
                <p class="text-sm text-slate-500">Tugas</p>
                <p class="text-2xl font-extrabold text-brand-900">{{ $totalTugas }}</p>
            </div>
        </div>

        <div class="flex items-center gap-4 rounded-2xl border border-brand-100 bg-white p-5 shadow-sm">
            <span class="grid h-12 w-12 shrink-0 place-items-center rounded-xl bg-sky-100 text-xl text-sky-600">
                <i class="fa-solid fa-clipboard-check"></i>
            </span>
            <div>
                <p class="text-sm text-slate-500">Ujian</p>
                <p class="text-2xl font-extrabold text-brand-900">{{ $totalUjian }}</p>
            </div>
        </div>

        <div class="flex items-center gap-4 rounded-2xl border border-brand-100 bg-white p-5 shadow-sm">
            <span class="grid h-12 w-12 shrink-0 place-items-center rounded-xl bg-amber-100 text-xl text-amber-600">
                <i class="fa-solid fa-bolt"></i>
            </span>
            <div>
                <p class="text-sm text-slate-500">Berlangsung</p>
                <p class="text-2xl font-extrabold text-brand-900">{{ $berlangsung }}</p>
            </div>
        </div>
    </div>

    {{-- ================= FILTER & PENCARIAN ================= --}}
    <div class="rounded-2xl border border-brand-100 bg-white p-4 shadow-sm sm:p-5">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center">
            <div class="relative flex-1">
                <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-sm text-brand-400"></i>

                <input type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Cari judul tugas atau ujian..."
                    class="w-full rounded-xl border border-brand-100 bg-brand-50/50 py-3 pl-11 pr-11 text-sm outline-none transition
                           placeholder:text-slate-400 focus:border-brand-300 focus:bg-white focus:ring-2 focus:ring-brand-200">

                <i wire:loading wire:target="search,filterJenis"
                    class="fa-solid fa-circle-notch fa-spin absolute right-4 top-1/2 -translate-y-1/2 text-sm text-brand-500"></i>
            </div>

            <div class="inline-flex w-full gap-1 rounded-xl bg-brand-100/60 p-1 lg:w-auto">
                @foreach ($pilihanFilter as $nilai => [$label, $ikon])
                <button type="button"
                    wire:click="$set('filterJenis', '{{ $nilai }}')"
                    class="flex flex-1 items-center justify-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold transition lg:flex-none
                            {{ ($filterJenis ?? 'semua') === $nilai
                                ? 'bg-white text-brand-700 shadow-sm'
                                : 'text-slate-500 hover:text-brand-700' }}">
                    <i class="fa-solid {{ $ikon }} text-xs"></i>
                    {{ $label }}
                </button>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ================= DAFTAR AKTIVITAS ================= --}}
    <div class="overflow-hidden rounded-2xl border border-brand-100 bg-white shadow-sm">
        <div class="flex items-center justify-between gap-3 border-b border-brand-100 bg-gradient-to-r from-brand-50 to-white px-5 py-4">
            <div>
                <h2 class="font-extrabold text-brand-900">Daftar Tugas &amp; Ujian</h2>
                <p class="mt-0.5 text-xs text-slate-500">Aktivitas pembelajaran yang kamu kelola.</p>
            </div>

            <span class="rounded-full bg-brand-100 px-2.5 py-0.5 text-xs font-bold text-brand-700">{{ $aktivitas->count() }}</span>
        </div>

        @forelse ($aktivitas as $item)
        @php
        $adalahTugas = $item['jenis'] === 'Tugas';
        [$kelasStatus, $titikStatus] = $gayaStatus[$item['status']] ?? ['bg-slate-100 text-slate-600', 'bg-slate-400'];

        $batas = $item['deadline'] ? \Carbon\Carbon::parse($item['deadline']) : null;
        $tampilRelatif = $batas && in_array($item['status'], ['Berlangsung', 'Terjadwal'], true);
        @endphp

        <div wire:key="{{ $item['jenis'] }}-{{ $item['id'] }}"
            class="relative flex flex-col gap-4 border-b border-brand-50 p-5 pl-6 transition last:border-0 hover:bg-brand-50/40 lg:flex-row lg:items-center lg:justify-between">

            <span class="absolute inset-y-0 left-0 w-1.5 {{ $adalahTugas ? 'bg-brand-500' : 'bg-sky-400' }}"></span>

            <div class="flex min-w-0 items-start gap-4">
                <span class="grid h-12 w-12 shrink-0 place-items-center rounded-xl text-lg
                        {{ $adalahTugas ? 'bg-brand-100 text-brand-600' : 'bg-sky-100 text-sky-600' }}">
                    <i class="fa-solid {{ $adalahTugas ? 'fa-file-arrow-up' : 'fa-clipboard-list' }}"></i>
                </span>

                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <h3 class="font-bold text-brand-900">{{ $item['judul'] }}</h3>

                        <span class="rounded-full px-2.5 py-0.5 text-[11px] font-semibold
                                {{ $adalahTugas ? 'bg-brand-100 text-brand-700' : 'bg-sky-100 text-sky-700' }}">
                            {{ $item['jenis'] }}
                        </span>
                    </div>

                    <p class="mt-1 flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-slate-500">
                        <span class="inline-flex items-center gap-1.5">
                            <i class="fa-solid fa-book text-xs text-brand-400"></i>
                            {{ $item['mapel'] }}
                        </span>
                        <span class="text-brand-300">·</span>
                        <span class="inline-flex items-center gap-1.5">
                            <i class="fa-solid fa-school text-xs text-brand-400"></i>
                            {{ $item['kelas'] }}
                        </span>
                    </p>

                    <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-xs text-slate-400">
                        <span class="inline-flex items-center gap-1.5">
                            <i class="fa-regular fa-calendar"></i>
                            Mulai: {{ $fmt($item['tanggal_mulai']) }}
                        </span>

                        <span class="inline-flex items-center gap-1.5">
                            <i class="fa-regular fa-clock"></i>
                            {{ $adalahTugas ? 'Deadline' : 'Selesai' }}: {{ $fmt($item['deadline']) }}
                            @if ($tampilRelatif)
                            <span class="font-medium text-slate-500">({{ $batas->diffForHumans() }})</span>
                            @endif
                        </span>

                        <span class="inline-flex items-center gap-1.5 font-medium text-brand-700">
                            <i class="fa-solid fa-users"></i>
                            {{ $item['jumlah_pengumpulan'] }} {{ $adalahTugas ? 'pengumpulan' : 'peserta' }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="flex shrink-0 flex-wrap items-center gap-3 lg:justify-end">
                <span class="inline-flex items-center gap-2 rounded-full px-3 py-1.5 text-xs font-semibold {{ $kelasStatus }}">
                    <span class="relative flex h-2 w-2">
                        @if ($item['status'] === 'Berlangsung')
                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full opacity-75 {{ $titikStatus }}"></span>
                        @endif
                        <span class="relative inline-flex h-2 w-2 rounded-full {{ $titikStatus }}"></span>
                    </span>
                    {{ $item['status'] }}
                </span>

                @if ($adalahTugas)
                <a href="{{ route('guru.tugas.edit', $item['id']) }}"
                    class="inline-flex items-center gap-2 rounded-xl border border-brand-200 bg-white px-4 py-2 text-sm font-semibold text-brand-700 transition hover:bg-brand-50">
                    <i class="fa-solid fa-sliders text-xs"></i>
                    Kelola
                </a>
                @else
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('guru.ujian.edit', $item['id']) }}"
                        class="inline-flex items-center gap-2 rounded-xl border border-brand-200 bg-white px-3.5 py-2 text-xs font-semibold text-brand-700 transition hover:bg-brand-50">
                        <i class="fa-solid fa-pen-to-square"></i>
                        Edit
                    </a>

                    <a href="{{ route('guru.ujian.soal', $item['id']) }}"
                        class="inline-flex items-center gap-2 rounded-xl bg-brand-500 px-3.5 py-2 text-xs font-semibold text-white shadow-sm shadow-brand-500/30 transition hover:bg-brand-600">
                        <i class="fa-solid fa-list-check"></i>
                        Kelola Soal
                    </a>
                </div>
                @endif
            </div>
        </div>
        @empty
        <div class="px-5 py-16 text-center">
            <span class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-brand-100 text-2xl text-brand-500">
                <i class="fa-solid {{ $filterAktif ? 'fa-magnifying-glass' : 'fa-folder-open' }}"></i>
            </span>

            <h3 class="mt-4 font-bold text-brand-900">
                {{ $filterAktif ? 'Tidak ada hasil yang cocok' : 'Belum ada tugas atau ujian' }}
            </h3>

            <p class="mt-1 text-sm text-slate-500">
                {{ $filterAktif
                        ? 'Coba ubah kata kunci atau filter yang dipakai.'
                        : 'Tugas dan ujian yang kamu buat akan muncul di sini.' }}
            </p>
        </div>
        @endforelse
    </div>

</div>