@php
    $namaMapel  = $pengajaran->mataPelajaran?->nama_mata_pelajaran ?? 'Mata Pelajaran';
    $guru       = $pengajaran->guru->nama_lengkap ?? null;

    $daftar     = $pengajaran->materi;
    $jml        = $daftar->count();
    $jmlVideo   = $daftar->filter(fn ($m) => filled($m->video_url))->count();
    $jmlFile    = $daftar->filter(fn ($m) => filled($m->file_path))->count();

    // Judul (huruf kecil) untuk pencarian di sisi browser
    $judulList  = $daftar->map(fn ($m) => strtolower($m->judul))->values();

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
<div class="space-y-6" x-data="{ q: '', items: @js($judulList) }">

    <a href="{{ route('siswa.mapel') }}"
        class="inline-flex items-center gap-2 text-sm font-medium text-brand-600 transition hover:text-brand-700">
        <i class="fa-solid fa-arrow-left text-xs"></i> Kembali ke Mata Pelajaran
    </a>

    {{-- Banner mata pelajaran --}}
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-500 to-brand-600 p-6 text-white shadow-lg shadow-brand-500/20 sm:p-8">
        <div class="pointer-events-none absolute -right-12 -top-12 h-52 w-52 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute -bottom-16 right-1/3 h-40 w-40 rounded-full bg-white/10"></div>

        <div class="relative flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-start gap-4">
                <span class="grid h-14 w-14 shrink-0 place-items-center rounded-2xl bg-white/90 text-2xl text-brand-600 shadow-md">
                    <i class="fa-solid {{ $iconFor($namaMapel) }}"></i>
                </span>

                <div class="min-w-0">
                    <p class="text-sm font-medium text-brand-100">Mata Pelajaran</p>
                    <h2 class="mt-0.5 text-2xl font-extrabold leading-tight sm:text-3xl">{{ $namaMapel }}</h2>

                    <div class="mt-3 flex items-center gap-2.5">
                        @if ($guru)
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($guru) }}&background=ffffff&color=23653c&bold=true"
                                alt="{{ $guru }}" class="h-8 w-8 rounded-full ring-2 ring-white/50">
                        @else
                            <span class="grid h-8 w-8 place-items-center rounded-full bg-white/20 text-xs">
                                <i class="fa-solid fa-user"></i>
                            </span>
                        @endif
                        <p class="text-sm text-brand-50">
                            Guru: <span class="font-semibold text-white">{{ $guru ?? 'Belum ditentukan' }}</span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid shrink-0 grid-cols-3 gap-3">
                <div class="rounded-2xl bg-white/15 px-4 py-3 text-center backdrop-blur">
                    <p class="text-2xl font-extrabold">{{ $jml }}</p>
                    <p class="text-xs text-brand-50">Materi</p>
                </div>
                <div class="rounded-2xl bg-white/15 px-4 py-3 text-center backdrop-blur">
                    <p class="text-2xl font-extrabold">{{ $jmlFile }}</p>
                    <p class="text-xs text-brand-50">Berkas</p>
                </div>
                <div class="rounded-2xl bg-white/15 px-4 py-3 text-center backdrop-blur">
                    <p class="text-2xl font-extrabold">{{ $jmlVideo }}</p>
                    <p class="text-xs text-brand-50">Video</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Judul daftar + pencarian --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-2">
            <h3 class="text-lg font-bold text-brand-900">Daftar Materi</h3>
            <span class="rounded-full bg-brand-100 px-2.5 py-0.5 text-xs font-semibold text-brand-700">{{ $jml }}</span>
        </div>

        @if ($jml >= 5)
            <div class="relative w-full sm:max-w-xs">
                <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-sm text-brand-400"></i>
                <input type="search" x-model="q" placeholder="Cari materi…"
                    class="w-full rounded-full border border-brand-100 bg-white py-2.5 pl-10 pr-4 text-sm outline-none transition
                           placeholder:text-slate-400 focus:border-brand-300 focus:ring-2 focus:ring-brand-200">
            </div>
        @endif
    </div>

    {{-- Daftar materi --}}
    <ol class="space-y-4">
        @forelse ($daftar as $materi)
            @php
                $ext  = filled($materi->file_path) ? strtoupper(pathinfo($materi->file_path, PATHINFO_EXTENSION)) : null;
                $baru = $materi->created_at?->gt(now()->subDays(7));
            @endphp

            <li wire:key="materi-{{ $materi->id }}"
                x-data="{ judul: @js(strtolower($materi->judul)) }"
                x-show="judul.includes(q.toLowerCase().trim())"
                x-transition.opacity>

                <article class="group rounded-2xl border border-brand-100 bg-white p-5 shadow-sm transition duration-300 hover:border-brand-300 hover:shadow-lg hover:shadow-brand-100">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

                        <div class="flex min-w-0 items-start gap-4">
                            <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-brand-100 text-base font-extrabold text-brand-700 transition group-hover:bg-brand-500 group-hover:text-white">
                                {{ $loop->iteration }}
                            </span>

                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h4 class="font-bold text-brand-900">{{ $materi->judul }}</h4>
                                    @if ($baru)
                                        <span class="rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-amber-700">Baru</span>
                                    @endif
                                </div>

                                <p class="mt-1 line-clamp-2 text-sm text-slate-500">
                                    {{ $materi->deskripsi ?: 'Tidak ada deskripsi.' }}
                                </p>

                                <div class="mt-3 flex flex-wrap items-center gap-2 text-xs">
                                    @if ($ext)
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-brand-50 px-2.5 py-1 font-medium text-brand-700">
                                            <i class="fa-solid fa-file-lines"></i> {{ $ext ?: 'File' }}
                                        </span>
                                    @endif

                                    @if (filled($materi->video_url))
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-brand-50 px-2.5 py-1 font-medium text-brand-700">
                                            <i class="fa-solid fa-circle-play"></i> Video
                                        </span>
                                    @endif

                                    @if (! $ext && blank($materi->video_url))
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-2.5 py-1 font-medium text-slate-500">
                                            <i class="fa-regular fa-file"></i> Tanpa lampiran
                                        </span>
                                    @endif

                                    @if ($materi->created_at)
                                        <span class="inline-flex items-center gap-1.5 text-slate-400">
                                            <i class="fa-regular fa-calendar"></i> {{ $materi->created_at->translatedFormat('d M Y') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="flex shrink-0 flex-wrap gap-2">
                            <a href="{{ route('materi.detail', $materi->id) }}"
                                class="inline-flex items-center gap-2 rounded-xl bg-brand-500 px-4 py-2 text-sm font-semibold text-white shadow-md shadow-brand-500/30 transition hover:bg-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-300 focus:ring-offset-2">
                                <i class="fa-solid fa-book-open text-xs"></i> Detail
                            </a>

                            <a href="{{ route('materi.diskusi', $materi->id) }}"
                                class="inline-flex items-center gap-2 rounded-xl border border-brand-200 bg-white px-4 py-2 text-sm font-semibold text-brand-700 transition hover:bg-brand-50">
                                <i class="fa-solid fa-comments text-xs"></i> Diskusi
                            </a>

                            <a href="{{ route('materi.absensi', $materi->id) }}"
                                class="inline-flex items-center gap-2 rounded-xl border border-brand-200 bg-white px-4 py-2 text-sm font-semibold text-brand-700 transition hover:bg-brand-50">
                                <i class="fa-solid fa-clipboard-user text-xs"></i> Absensi
                            </a>
                        </div>
                    </div>
                </article>
            </li>
        @empty
            <li class="rounded-2xl border border-dashed border-brand-200 bg-white/70 px-6 py-14 text-center">
                <span class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-brand-100 text-3xl text-brand-500">
                    <i class="fa-regular fa-folder-open"></i>
                </span>
                <p class="mt-4 font-semibold text-brand-900">Belum ada materi untuk mata pelajaran ini</p>
                <p class="mt-1 text-sm text-slate-500">Materi akan muncul di sini setelah diunggah oleh guru.</p>
            </li>
        @endforelse

        {{-- Hasil pencarian kosong --}}
        @if ($jml > 0)
            <li x-show="! items.some(n => n.includes(q.toLowerCase().trim()))" x-cloak
                class="rounded-2xl border border-dashed border-brand-200 bg-white/70 px-6 py-12 text-center">
                <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-brand-100 text-2xl text-brand-500">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </span>
                <p class="mt-4 font-semibold text-brand-900">Materi tidak ditemukan</p>
                <p class="mt-1 text-sm text-slate-500">Coba kata kunci lain.</p>
            </li>
        @endif
    </ol>
</div>