@php
$totalMapel = $mataPelajaran->count();
$totalMateri = $mataPelajaran->sum('materi_count');

// Nama mapel (huruf kecil) untuk fitur pencarian di sisi browser
$namaList = $mataPelajaran
->map(fn ($p) => strtolower($p->mataPelajaran?->nama_mata_pelajaran ?? ''))
->values();

// Variasi warna sampul kartu (semua tetap nuansa hijau)
$gradients = [
'from-brand-400 to-brand-600',
'from-emerald-400 to-teal-500',
'from-lime-400 to-brand-500',
'from-teal-400 to-brand-600',
'from-brand-300 to-emerald-500',
'from-green-400 to-emerald-600',
];

// Ikon otomatis berdasarkan nama mata pelajaran
$iconFor = function (string $nama) {
$n = strtolower($nama);

$map = [
['/matematika/', 'fa-calculator'],
['/inggris|arab|bahasa asing|jepang|mandarin/', 'fa-language'],
['/indonesia|literasi/', 'fa-book-open'],
['/fisika/', 'fa-atom'],
['/kimia/', 'fa-flask'],
['/biologi/', 'fa-dna'],
['/\bipa\b|sains/', 'fa-flask'],
['/\bips\b|geografi/', 'fa-earth-asia'],
['/sejarah/', 'fa-landmark'],
['/ekonomi/', 'fa-chart-line'],
['/akuntansi|keuangan/', 'fa-calculator'],
['/sosiologi/', 'fa-people-group'],
['/quran|qur\'an|tahfiz|tahfidz/', 'fa-book-quran'],
['/agama|islam|\bpai\b|akidah|fikih|akhlak/', 'fa-mosque'],
['/pkn|pancasila|kewarganegaraan/', 'fa-scale-balanced'],
['/jasmani|olahraga|pjok/', 'fa-person-running'],
['/seni|budaya/', 'fa-palette'],
['/prakarya|kewirausahaan|keterampilan/', 'fa-hammer'],
['/pemrograman|coding|basis data|web/', 'fa-code'],
['/jaringan|telekomunikasi/', 'fa-network-wired'],
['/informatika|komputer|\btik\b|\bdkv\b/', 'fa-laptop-code'],
];

foreach ($map as [$pola, $icon]) {
if (preg_match($pola, $n)) {
return $icon;
}
}

return 'fa-book';
};
@endphp

{{-- Livewire: elemen root harus satu --}}
<div class="space-y-6" x-data="{ q: '', items: @js($namaList) }">

    {{-- Banner ringkasan --}}
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-500 to-brand-600 p-6 text-white shadow-lg shadow-brand-500/20 sm:p-8">
        <div class="pointer-events-none absolute -right-12 -top-12 h-48 w-48 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute -bottom-16 right-1/3 h-40 w-40 rounded-full bg-white/10"></div>

        <div class="relative flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-sm font-medium text-brand-100">Kelas kamu semester ini</p>
                <h2 class="mt-1 text-2xl font-extrabold sm:text-3xl">Mata Pelajaran</h2>
                <p class="mt-2 max-w-md text-sm text-brand-50">
                    Pilih mata pelajaran untuk melihat materi, tugas, dan diskusi dari guru pengampu.
                </p>
            </div>

            <div class="flex gap-3">
                <div class="rounded-2xl bg-white/15 px-5 py-3 backdrop-blur">
                    <p class="text-2xl font-extrabold">{{ $totalMapel }}</p>
                    <p class="text-xs text-brand-50">Mata pelajaran</p>
                </div>
                <div class="rounded-2xl bg-white/15 px-5 py-3 backdrop-blur">
                    <p class="text-2xl font-extrabold">{{ $totalMateri }}</p>
                    <p class="text-xs text-brand-50">Total materi</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Pencarian --}}
    @if ($totalMapel > 0)
    <div class="relative max-w-md">
        <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-sm text-brand-400"></i>
        <input type="search" x-model="q" placeholder="Cari mata pelajaran…"
            class="w-full rounded-full border border-brand-100 bg-white py-2.5 pl-10 pr-4 text-sm outline-none transition
                       placeholder:text-slate-400 focus:border-brand-300 focus:ring-2 focus:ring-brand-200">
    </div>
    @endif

    {{-- Daftar mata pelajaran --}}
    <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
        @forelse ($mataPelajaran as $pengajaran)
        @php
        $nama = $pengajaran->mataPelajaran?->nama ?? 'Mata Pelajaran';
        $guru = $pengajaran->guru->nama_lengkap ?? null;
        $jmlMateri = (int) $pengajaran->materi_count;
        $grad = $gradients[$loop->index % count($gradients)];
        @endphp

        <div wire:key="mapel-{{ $pengajaran->id }}"
            x-data="{ nama: @js(strtolower($nama)) }"
            x-show="nama.includes(q.toLowerCase().trim())"
            x-transition.opacity>

            <article class="group flex h-full flex-col overflow-hidden rounded-2xl border border-brand-100 bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-brand-200/60">

                {{-- Sampul --}}
                <div class="relative h-28 bg-gradient-to-br {{ $grad }} p-5">
                    <div class="pointer-events-none absolute -right-6 -top-6 h-28 w-28 rounded-full bg-white/15"></div>
                    <div class="pointer-events-none absolute -bottom-8 left-1/3 h-20 w-20 rounded-full bg-white/10"></div>

                    <span class="relative grid h-12 w-12 place-items-center rounded-2xl bg-white/90 text-xl text-brand-600 shadow-md">
                        <i class="fa-solid {{ $iconFor($nama) }}"></i>
                    </span>

                    <span class="absolute right-4 top-4 rounded-full bg-white/90 px-3 py-1 text-xs font-semibold text-brand-700 shadow-sm">
                        {{ $jmlMateri }} materi
                    </span>
                </div>

                {{-- Isi --}}
                <div class="flex flex-1 flex-col p-5">
                    <h3 class="text-lg font-bold leading-snug text-brand-900">{{ $nama }}</h3>

                    <div class="mt-3 flex items-center gap-2.5">
                        @if ($guru)
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($guru) }}&background=e1f4e6&color=23653c&bold=true"
                            alt="{{ $guru }}" class="h-9 w-9 rounded-full ring-2 ring-brand-100">
                        @else
                        <span class="grid h-9 w-9 place-items-center rounded-full bg-brand-50 text-sm text-brand-400 ring-2 ring-brand-100">
                            <i class="fa-solid fa-user"></i>
                        </span>
                        @endif

                        <div class="min-w-0">
                            <p class="text-[11px] uppercase tracking-wide text-slate-400">Guru pengampu</p>
                            <p class="truncate text-sm font-medium text-slate-700">{{ $guru ?? 'Belum ditentukan' }}</p>
                        </div>
                    </div>

                    @if ($jmlMateri === 0)
                    <p class="mt-3 text-xs text-slate-400">
                        <i class="fa-regular fa-clock mr-1"></i> Belum ada materi diunggah
                    </p>
                    @endif

                    <div class="mt-auto pt-5">
                        <a href="{{ route('siswa.mapel.detail', $pengajaran->id) }}"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-brand-500 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-brand-500/30 transition hover:bg-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-300 focus:ring-offset-2">
                            Lihat Materi
                            <i class="fa-solid fa-arrow-right text-xs transition group-hover:translate-x-1"></i>
                        </a>
                    </div>
                </div>
            </article>
        </div>
        @empty
        <div class="rounded-2xl border border-dashed border-brand-200 bg-white/70 px-6 py-14 text-center sm:col-span-2 xl:col-span-3">
            <span class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-brand-100 text-3xl text-brand-500">
                <i class="fa-solid fa-book-open"></i>
            </span>
            <p class="mt-4 font-semibold text-brand-900">Belum ada mata pelajaran untuk kelas kamu</p>
            <p class="mt-1 text-sm text-slate-500">Hubungi wali kelas atau admin jika seharusnya sudah ada.</p>
        </div>
        @endforelse

        {{-- Hasil pencarian kosong --}}
        @if ($totalMapel > 0)
        <div x-show="! items.some(n => n.includes(q.toLowerCase().trim()))" x-cloak
            class="rounded-2xl border border-dashed border-brand-200 bg-white/70 px-6 py-12 text-center sm:col-span-2 xl:col-span-3">
            <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-brand-100 text-2xl text-brand-500">
                <i class="fa-solid fa-magnifying-glass"></i>
            </span>
            <p class="mt-4 font-semibold text-brand-900">Mata pelajaran tidak ditemukan</p>
            <p class="mt-1 text-sm text-slate-500">Coba kata kunci lain.</p>
        </div>
        @endif
    </div>
</div>