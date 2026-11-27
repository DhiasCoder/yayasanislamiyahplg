@php
// Variasi warna sampul kartu (tetap nuansa hijau)
$gradients = [
'from-brand-400 to-brand-600',
'from-emerald-400 to-teal-500',
'from-lime-400 to-brand-500',
'from-teal-400 to-brand-600',
'from-brand-300 to-emerald-500',
'from-green-400 to-emerald-600',
];

// Badge aksen per hari
$badgeHariStyle = [
'Senin' => 'bg-brand-100 text-brand-700 border-brand-200',
'Selasa' => 'bg-sky-100 text-sky-700 border-sky-200',
'Rabu' => 'bg-indigo-100 text-indigo-700 border-indigo-200',
'Kamis' => 'bg-amber-100 text-amber-700 border-amber-200',
'Jumat' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
'Sabtu' => 'bg-purple-100 text-purple-700 border-purple-200',
'Minggu' => 'bg-rose-100 text-rose-700 border-rose-200',
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

// Indeks pencarian & filter hari di browser
$searchSlots = [];

foreach ($jadwalPerHari as $hari => $items) {
foreach ($items as $jadwal) {
$p = $jadwal->pengajaran;
$searchSlots[] = [
'hari' => $hari,
'nama' => strtolower($p?->mataPelajaran?->nama ?? $p?->mataPelajaran?->nama_mata_pelajaran ?? ''),
'guru' => strtolower($p?->guru?->nama_lengkap ?? ''),
];
}
}

foreach ($unscheduledPengajarans as $p) {
$searchSlots[] = [
'hari' => 'Belum Terjadwal',
'nama' => strtolower($p->mataPelajaran?->nama ?? $p->mataPelajaran?->nama_mata_pelajaran ?? ''),
'guru' => strtolower($p->guru?->nama_lengkap ?? ''),
];
}

$daysWithCount = $jadwalPerHari->map->count();
@endphp

{{-- Livewire: elemen root harus satu --}}
<div class="space-y-6"
    x-data="{
        q: '',
        selectedHari: 'all',
        slots: @js($searchSlots),
        dayCounts: @js($daysWithCount),
        matches(slot) {
            const term = this.q.toLowerCase().trim();
            const dayOk = this.selectedHari === 'all' || slot.hari === this.selectedHari;
            const textOk = !term || slot.nama.includes(term) || slot.guru.includes(term);
            return dayOk && textOk;
        },
        dayHasVisible(hari) {
            return this.slots.some(s => s.hari === hari && this.matches(s));
        },
        get anyVisible() {
            return this.slots.some(s => this.matches(s));
        }
    }">

    {{-- Banner ringkasan --}}
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-500 to-brand-600 p-6 text-white shadow-lg shadow-brand-500/20 sm:p-8">
        <div class="pointer-events-none absolute -right-12 -top-12 h-48 w-48 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute -bottom-16 right-1/3 h-40 w-40 rounded-full bg-white/10"></div>

        <div class="relative flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-sm font-medium text-brand-100">Jadwal Belajar Senin – Sabtu</p>
                <h2 class="mt-1 text-2xl font-extrabold sm:text-3xl">Mata Pelajaran</h2>
                <p class="mt-2 max-w-md text-sm text-brand-50">
                    Tersusun berdasarkan hari jadwal pelajaran agar kamu mudah memantau materi dan guru pengampu setiap harinya.
                </p>
            </div>

            <div class="grid grid-cols-3 gap-3 sm:flex">
                <div class="rounded-2xl bg-white/15 px-4 py-3 backdrop-blur sm:px-5">
                    <p class="text-2xl font-extrabold">{{ $totalMapel }}</p>
                    <p class="text-xs text-brand-50">Mata pelajaran</p>
                </div>
                <div class="rounded-2xl bg-white/15 px-4 py-3 backdrop-blur sm:px-5">
                    <p class="text-2xl font-extrabold">{{ $totalSesi }}</p>
                    <p class="text-xs text-brand-50">Sesi / minggu</p>
                </div>
                <div class="rounded-2xl bg-white/15 px-4 py-3 backdrop-blur sm:px-5">
                    <p class="text-2xl font-extrabold">{{ $totalMateri }}</p>
                    <p class="text-xs text-brand-50">Total materi</p>
                </div>
            </div>
        </div>
    </section>

    @if ($totalMapel > 0)
    {{-- Bar Filter Hari + Pencarian --}}
    <div class="flex flex-col gap-3 rounded-2xl border border-brand-100 bg-white p-4 shadow-sm lg:flex-row lg:items-center lg:justify-between">
        {{-- Tab Pilihan Hari --}}
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 lg:pb-0">
            <button type="button"
                @click="selectedHari = 'all'"
                :class="selectedHari === 'all'
                        ? 'bg-brand-600 text-white shadow-sm shadow-brand-500/20'
                        : 'bg-brand-50/70 text-slate-600 hover:bg-brand-100/70'"
                class="inline-flex shrink-0 items-center gap-1.5 rounded-xl px-3.5 py-2 text-xs font-bold transition">
                Semua Hari
            </button>

            @foreach ($jadwalPerHari as $hari => $daftarHariIni)
            <button type="button"
                @click="selectedHari = @js($hari)"
                :class="selectedHari === @js($hari)
                            ? 'bg-brand-600 text-white shadow-sm shadow-brand-500/20'
                            : 'bg-brand-50/70 text-slate-600 hover:bg-brand-100/70'"
                class="inline-flex shrink-0 items-center gap-1.5 rounded-xl px-3.5 py-2 text-xs font-bold transition">
                <span>{{ $hari }}</span>

                @if ($hari === $hariIni)
                <span class="h-1.5 w-1.5 rounded-full bg-amber-400" title="Hari Ini"></span>
                @endif

                <span class="rounded-full px-1.5 py-0.2 text-[10px]"
                    :class="selectedHari === @js($hari) ? 'bg-white/20 text-white' : 'bg-white text-brand-700'">
                    {{ $daftarHariIni->count() }}
                </span>
            </button>
            @endforeach
        </div>

        {{-- Pencarian --}}
        <div class="relative w-full lg:max-w-xs">
            <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-sm text-brand-400"></i>
            <input type="search" x-model="q" placeholder="Cari mapel atau guru…"
                class="w-full rounded-xl border border-brand-100 bg-brand-50/40 py-2 pl-10 pr-4 text-sm outline-none transition
                           placeholder:text-slate-400 focus:border-brand-300 focus:bg-white focus:ring-2 focus:ring-brand-200">
        </div>
    </div>

    {{-- ================= KELOMPOK JADWAL SENIN – SABTU ================= --}}
    <div class="space-y-8">
        @foreach ($jadwalPerHari as $hari => $daftarJadwal)
        @php
        $isToday = $hari === $hariIni;
        $badgeStyle = $badgeHariStyle[$hari] ?? 'bg-brand-100 text-brand-700 border-brand-200';
        @endphp

        {{-- Bagian Hari (Tampil jika ada kartu yang cocok) --}}
        <section wire:key="section-hari-{{ $hari }}"
            x-show="dayHasVisible(@js($hari))"
            x-transition.opacity
            class="space-y-4">

            {{-- Header Hari --}}
            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-brand-100 pb-3">
                <div class="flex items-center gap-2.5">
                    <span class="inline-flex items-center gap-2 rounded-xl border px-3.5 py-1.5 text-sm font-extrabold {{ $badgeStyle }}">
                        <i class="fa-solid fa-calendar-day text-xs"></i>
                        {{ $hari }}
                    </span>

                    @if ($isToday)
                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-0.5 text-[11px] font-bold text-amber-800">
                        <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                        Hari Ini
                    </span>
                    @endif
                </div>

                <span class="text-xs font-semibold text-slate-500">
                    {{ $daftarJadwal->count() }} mata pelajaran
                </span>
            </div>

            {{-- Grid Kartu Mata Pelajaran pada Hari Tersebut --}}
            <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                @foreach ($daftarJadwal as $jadwal)
                @php
                $pengajaran = $jadwal->pengajaran;
                $nama = $pengajaran->mataPelajaran?->nama
                ?? $pengajaran->mataPelajaran?->nama_mata_pelajaran
                ?? 'Mata Pelajaran';
                $guru = $pengajaran->guru?->nama_lengkap ?? null;
                $jmlMateri = (int) $pengajaran->materi_count;
                $jamMulai = substr((string) $jadwal->jam_mulai, 0, 5);
                $jamSelesai = substr((string) $jadwal->jam_selesai, 0, 5);
                $grad = $gradients[($loop->parent->index + $loop->index) % count($gradients)];
                @endphp

                <div wire:key="jadwal-mapel-{{ $jadwal->id }}"
                    x-show="matches({ hari: @js($hari), nama: @js(strtolower($nama)), guru: @js(strtolower($guru ?? '')) })"
                    x-transition.opacity>

                    <article class="group flex h-full flex-col overflow-hidden rounded-2xl border border-brand-100 bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-brand-200/60">
                        {{-- Sampul --}}
                        <div class="relative h-28 bg-gradient-to-br {{ $grad }} p-5">
                            <div class="pointer-events-none absolute -right-6 -top-6 h-28 w-28 rounded-full bg-white/15"></div>
                            <div class="pointer-events-none absolute -bottom-8 left-1/3 h-20 w-20 rounded-full bg-white/10"></div>

                            <span class="relative grid h-12 w-12 place-items-center rounded-2xl bg-white/95 text-xl text-brand-600 shadow-md">
                                <i class="fa-solid {{ $iconFor($nama) }}"></i>
                            </span>

                            <div class="absolute right-4 top-4 flex flex-col items-end gap-1.5">
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-white/95 px-3 py-1 font-mono text-xs font-bold text-brand-700 shadow-sm">
                                    <i class="fa-regular fa-clock text-[11px] text-brand-500"></i>
                                    {{ $jamMulai }} – {{ $jamSelesai }}
                                </span>

                                <span class="rounded-full bg-black/20 px-2.5 py-0.5 text-[11px] font-semibold text-white backdrop-blur-xs">
                                    {{ $jmlMateri }} materi
                                </span>
                            </div>
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
                @endforeach
            </div>
        </section>
        @endforeach

        {{-- Bagian Mata Pelajaran yang Belum Terjadwal (jika ada) --}}
        @if ($unscheduledPengajarans->isNotEmpty())
        <section x-show="dayHasVisible('Belum Terjadwal')" x-transition.opacity class="space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-200 pb-3">
                <span class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-100 px-3.5 py-1.5 text-sm font-extrabold text-slate-700">
                    <i class="fa-regular fa-calendar text-xs"></i>
                    Belum Terjadwal
                </span>
                <span class="text-xs font-semibold text-slate-500">
                    {{ $unscheduledPengajarans->count() }} mata pelajaran
                </span>
            </div>

            <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                @foreach ($unscheduledPengajarans as $pengajaran)
                @php
                $nama = $pengajaran->mataPelajaran?->nama
                ?? $pengajaran->mataPelajaran?->nama_mata_pelajaran
                ?? 'Mata Pelajaran';
                $guru = $pengajaran->guru?->nama_lengkap ?? null;
                $jmlMateri = (int) $pengajaran->materi_count;
                $grad = $gradients[$loop->index % count($gradients)];
                @endphp

                <div wire:key="unscheduled-mapel-{{ $pengajaran->id }}"
                    x-show="matches({ hari: 'Belum Terjadwal', nama: @js(strtolower($nama)), guru: @js(strtolower($guru ?? '')) })"
                    x-transition.opacity>

                    <article class="group flex h-full flex-col overflow-hidden rounded-2xl border border-brand-100 bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-brand-200/60">
                        <div class="relative h-28 bg-gradient-to-br {{ $grad }} p-5">
                            <span class="relative grid h-12 w-12 place-items-center rounded-2xl bg-white/95 text-xl text-brand-600 shadow-md">
                                <i class="fa-solid {{ $iconFor($nama) }}"></i>
                            </span>

                            <span class="absolute right-4 top-4 rounded-full bg-white/95 px-3 py-1 text-xs font-semibold text-brand-700 shadow-sm">
                                {{ $jmlMateri }} materi
                            </span>
                        </div>

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

                            <div class="mt-auto pt-5">
                                <a href="{{ route('siswa.mapel.detail', $pengajaran->id) }}"
                                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-brand-500 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-brand-500/30 transition hover:bg-brand-600">
                                    Lihat Materi
                                    <i class="fa-solid fa-arrow-right text-xs transition group-hover:translate-x-1"></i>
                                </a>
                            </div>
                        </div>
                    </article>
                </div>
                @endforeach
            </div>
        </section>
        @endif
    </div>

    {{-- Kosong saat memilih hari tertentu yang tidak memiliki jadwal --}}
    <div x-show="selectedHari !== 'all' && q.trim() === '' && (dayCounts[selectedHari] ?? 0) === 0"
        x-cloak
        class="rounded-2xl border border-dashed border-brand-200 bg-white/70 px-6 py-12 text-center">
        <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-brand-100 text-2xl text-brand-500">
            <i class="fa-regular fa-calendar-check"></i>
        </span>
        <p class="mt-4 font-semibold text-brand-900">
            Tidak ada jadwal pelajaran pada hari <span x-text="selectedHari"></span>
        </p>
        <p class="mt-1 text-sm text-slate-500">Pilih hari lain atau klik "Semua Hari" untuk melihat seluruh jadwal.</p>
    </div>

    {{-- Kosong saat pencarian tidak menemukan hasil --}}
    <div x-show="q.trim() !== '' && !anyVisible"
        x-cloak
        class="rounded-2xl border border-dashed border-brand-200 bg-white/70 px-6 py-12 text-center">
        <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-brand-100 text-2xl text-brand-500">
            <i class="fa-solid fa-magnifying-glass"></i>
        </span>
        <p class="mt-4 font-semibold text-brand-900">Mata pelajaran tidak ditemukan</p>
        <p class="mt-1 text-sm text-slate-500">Coba kata kunci mata pelajaran atau nama guru lain.</p>
    </div>
    @else
    <div class="rounded-2xl border border-dashed border-brand-200 bg-white/70 px-6 py-14 text-center">
        <span class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-brand-100 text-3xl text-brand-500">
            <i class="fa-solid fa-book-open"></i>
        </span>
        <p class="mt-4 font-semibold text-brand-900">Belum ada mata pelajaran untuk kelas kamu</p>
        <p class="mt-1 text-sm text-slate-500">Hubungi wali kelas atau admin jika seharusnya sudah ada.</p>
    </div>
    @endif
</div>