@php
$predikat = match (true) {
$totalNilai == 0 => ['label' => 'Belum Dinilai', 'icon' => 'fa-hourglass-half', 'bg' => 'bg-slate-100 text-slate-500', 'text' => 'text-slate-600'],
$rataRata >= 85 => ['label' => 'Sangat Baik', 'icon' => 'fa-award', 'bg' => 'bg-emerald-100 text-emerald-600', 'text' => 'text-emerald-600'],
$rataRata >= 75 => ['label' => 'Baik', 'icon' => 'fa-thumbs-up', 'bg' => 'bg-brand-100 text-brand-600', 'text' => 'text-brand-700'],
default => ['label' => 'Perlu Peningkatan', 'icon' => 'fa-chart-line', 'bg' => 'bg-amber-100 text-amber-600', 'text' => 'text-amber-600'],
};

$jenisBadge = [
'tugas' => ['bg' => 'bg-sky-50 text-sky-700 border-sky-200', 'icon' => 'fa-file-pen'],
'kuis' => ['bg' => 'bg-amber-50 text-amber-700 border-amber-200', 'icon' => 'fa-bolt'],
'ujian' => ['bg' => 'bg-indigo-50 text-indigo-700 border-indigo-200', 'icon' => 'fa-file-signature'],
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
                <h1 class="text-2xl font-extrabold sm:text-3xl">Nilai Saya</h1>
                <p class="mt-2 max-w-lg text-sm text-brand-50">
                    Pantau rekap hasil penilaian tugas, kuis, ujian, dan evaluasi belajar kamu di semua mata pelajaran.
                </p>

                <div class="mt-4 flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3.5 py-1.5 text-xs font-medium backdrop-blur">
                        <i class="fa-solid fa-clipboard-check"></i>
                        {{ $totalNilai }} penilaian tercatat
                    </span>

                    <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3.5 py-1.5 text-xs font-medium backdrop-blur">
                        <i class="fa-solid fa-star"></i>
                        Rata-rata {{ number_format($rataRata, 2) }}
                    </span>
                </div>
            </div>
        </div>
    </section>

    {{-- ================= STATISTIK ================= --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="flex items-center gap-4 rounded-2xl border border-brand-100 bg-white p-5 shadow-sm">
            <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-brand-100 text-lg text-brand-600">
                <i class="fa-solid fa-list-check"></i>
            </span>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Total Penilaian</p>
                <p class="mt-0.5 text-2xl font-extrabold text-slate-800">{{ $totalNilai }}</p>
            </div>
        </div>

        <div class="flex items-center gap-4 rounded-2xl border border-brand-100 bg-white p-5 shadow-sm">
            <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-emerald-100 text-lg text-emerald-600">
                <i class="fa-solid fa-chart-simple"></i>
            </span>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Rata-rata Nilai</p>
                <p class="mt-0.5 text-2xl font-extrabold text-emerald-600">{{ number_format($rataRata, 2) }}</p>
            </div>
        </div>

        <div class="flex items-center gap-4 rounded-2xl border border-brand-100 bg-white p-5 shadow-sm">
            <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl text-lg {{ $predikat['bg'] }}">
                <i class="fa-solid {{ $predikat['icon'] }}"></i>
            </span>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Capaian Akademik</p>
                <p class="mt-0.5 text-lg font-extrabold {{ $predikat['text'] }}">{{ $predikat['label'] }}</p>
            </div>
        </div>
    </div>

    {{-- ================= FILTER & DAFTAR NILAI ================= --}}
    <div class="rounded-2xl border border-brand-100 bg-white p-5 shadow-sm sm:p-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex flex-wrap items-center gap-2">
                @foreach ([
                'semua' => ['label' => 'Semua', 'icon' => 'fa-layer-group'],
                'tugas' => ['label' => 'Tugas', 'icon' => 'fa-file-pen'],
                'kuis' => ['label' => 'Kuis', 'icon' => 'fa-bolt'],
                'ujian' => ['label' => 'Ujian', 'icon' => 'fa-file-signature'],
                'nilai' => ['label' => 'Nilai Lainnya', 'icon' => 'fa-star'],
                ] as $key => $tab)
                <button
                    type="button"
                    wire:click="$set('filter', '{{ $key }}')"
                    class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-xs font-bold transition sm:text-sm
                            {{ $filter === $key
                                ? 'bg-brand-600 text-white shadow-sm shadow-brand-500/20'
                                : 'border border-brand-100 bg-brand-50/50 text-slate-600 hover:bg-brand-100/70 hover:text-brand-700' }}">
                    <i class="fa-solid {{ $tab['icon'] }} text-xs"></i>
                    <span>{{ $tab['label'] }}</span>
                </button>
                @endforeach
            </div>

            <div wire:loading wire:target="filter" class="inline-flex items-center gap-2 text-xs font-semibold text-brand-600">
                <i class="fa-solid fa-circle-notch fa-spin"></i>
                <span>Memuat data...</span>
            </div>
        </div>

        {{-- ================= TABEL DESKTOP ================= --}}
        <div wire:loading.class="pointer-events-none opacity-60"
            class="mt-5 hidden overflow-hidden rounded-2xl border border-brand-100 transition-opacity duration-200 md:block">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[820px] text-left text-sm">
                    <thead class="bg-brand-50 text-xs uppercase tracking-wide text-brand-700">
                        <tr>
                            <th class="w-16 px-5 py-3.5">No</th>
                            <th class="px-5 py-3.5">Penilaian</th>
                            <th class="px-5 py-3.5">Jenis</th>
                            <th class="px-5 py-3.5">Mata Pelajaran</th>
                            <th class="px-5 py-3.5">Tanggal</th>
                            <th class="px-5 py-3.5 text-center">Nilai</th>
                            <th class="px-5 py-3.5">Keterangan</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-brand-50">
                        @forelse ($nilaiSaya as $item)
                        @php
                        $jenisKey = strtolower($item['jenis'] ?? '');
                        $badge = $jenisBadge[$jenisKey] ?? ['bg' => 'bg-brand-50 text-brand-700 border-brand-200', 'icon' => 'fa-star'];
                        $nilaiNum = (float) ($item['nilai'] ?? 0);
                        $nilaiTone = $nilaiNum >= 75
                        ? 'border-brand-200 bg-brand-50 text-brand-700'
                        : 'border-amber-200 bg-amber-50 text-amber-700';
                        @endphp

                        <tr wire:key="nilai-{{ $item['kategori'] }}-{{ $loop->index }}" class="transition hover:bg-brand-50/40">
                            <td class="whitespace-nowrap px-5 py-4 text-slate-400">
                                {{ $loop->iteration }}
                            </td>

                            <td class="px-5 py-4">
                                <p class="font-bold text-slate-800">{{ $item['judul'] }}</p>
                            </td>

                            <td class="whitespace-nowrap px-5 py-4">
                                <span class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-semibold {{ $badge['bg'] }}">
                                    <i class="fa-solid {{ $badge['icon'] }} text-[10px]"></i>
                                    {{ ucfirst($item['jenis']) }}
                                </span>
                            </td>

                            <td class="whitespace-nowrap px-5 py-4">
                                <span class="inline-flex items-center gap-1.5 font-medium text-slate-700">
                                    <i class="fa-solid fa-book-open text-xs text-brand-500"></i>
                                    {{ $item['mapel'] }}
                                </span>
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-xs font-medium text-slate-500">
                                @if ($item['tanggal'])
                                <span class="inline-flex items-center gap-1.5">
                                    <i class="fa-regular fa-calendar text-brand-400"></i>
                                    {{ \Carbon\Carbon::parse($item['tanggal'])->translatedFormat('d M Y') }}
                                </span>
                                @else
                                <span>-</span>
                                @endif
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-center">
                                <span class="inline-block min-w-[4.25rem] rounded-xl border px-3 py-1.5 text-sm font-extrabold {{ $nilaiTone }}">
                                    {{ number_format($nilaiNum, 2) }}
                                </span>
                            </td>

                            <td class="px-5 py-4 text-xs leading-relaxed text-slate-500">
                                {{ $item['keterangan'] ?: '-' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center">
                                <span class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-brand-100 text-2xl text-brand-500">
                                    <i class="fa-solid fa-clipboard-question"></i>
                                </span>
                                <p class="mt-4 font-bold text-brand-900">Belum ada nilai tersedia</p>
                                <p class="mt-1 text-sm text-slate-500">
                                    Hasil penilaian untuk kategori yang dipilih akan muncul di sini setelah dinilai guru.
                                </p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ================= KARTU MOBILE ================= --}}
        <div wire:loading.class="pointer-events-none opacity-60"
            class="mt-5 space-y-3 transition-opacity duration-200 md:hidden">
            @forelse ($nilaiSaya as $item)
            @php
            $jenisKey = strtolower($item['jenis'] ?? '');
            $badge = $jenisBadge[$jenisKey] ?? ['bg' => 'bg-brand-50 text-brand-700 border-brand-200', 'icon' => 'fa-star'];
            $nilaiNum = (float) ($item['nilai'] ?? 0);
            $nilaiTone = $nilaiNum >= 75
            ? 'border-brand-200 bg-brand-50 text-brand-700'
            : 'border-amber-200 bg-amber-50 text-amber-700';
            @endphp

            <div wire:key="nilai-mobile-{{ $item['kategori'] }}-{{ $loop->index }}"
                class="rounded-2xl border border-brand-100 bg-white p-4 shadow-sm transition hover:border-brand-200">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <span class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-0.5 text-[11px] font-semibold {{ $badge['bg'] }}">
                            <i class="fa-solid {{ $badge['icon'] }} text-[10px]"></i>
                            {{ ucfirst($item['jenis']) }}
                        </span>

                        <h3 class="mt-2 break-words text-base font-extrabold text-slate-800">
                            {{ $item['judul'] }}
                        </h3>

                        <p class="mt-1 flex items-center gap-1.5 text-xs font-medium text-brand-700">
                            <i class="fa-solid fa-book-open text-brand-500"></i>
                            {{ $item['mapel'] }}
                        </p>
                    </div>

                    <div class="shrink-0 text-right">
                        <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Nilai</span>
                        <span class="mt-1 inline-block rounded-xl border px-3 py-1.5 text-base font-extrabold {{ $nilaiTone }}">
                            {{ number_format($nilaiNum, 2) }}
                        </span>
                    </div>
                </div>

                <div class="mt-3 flex flex-wrap items-center justify-between gap-2 border-t border-brand-50 pt-3 text-xs text-slate-500">
                    <span class="inline-flex items-center gap-1.5">
                        <i class="fa-regular fa-calendar text-brand-400"></i>
                        {{ $item['tanggal'] ? \Carbon\Carbon::parse($item['tanggal'])->translatedFormat('d M Y') : '-' }}
                    </span>

                    @if (! empty($item['keterangan']))
                    <span class="italic text-slate-600">{{ $item['keterangan'] }}</span>
                    @endif
                </div>
            </div>
            @empty
            <div class="rounded-2xl border border-dashed border-brand-200 bg-brand-50/30 px-6 py-14 text-center">
                <span class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-brand-100 text-2xl text-brand-500">
                    <i class="fa-solid fa-clipboard-question"></i>
                </span>
                <p class="mt-4 font-bold text-brand-900">Belum ada nilai tersedia</p>
                <p class="mt-1 text-sm text-slate-500">
                    Hasil penilaian untuk kategori yang dipilih akan muncul di sini setelah dinilai guru.
                </p>
            </div>
            @endforelse
        </div>
    </div>

</div>