@php
    // ---------- File materi ----------
    $fileUrl  = $materi->file_path ? asset('storage/' . $materi->file_path) : null;
    $fileName = $materi->file_path ? basename($materi->file_path) : null;
    $ext      = $materi->file_path ? strtolower(pathinfo($materi->file_path, PATHINFO_EXTENSION)) : '';

    $isPdf    = $ext === 'pdf';
    $isImage  = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
    $isVideo  = in_array($ext, ['mp4', 'webm']);
    $isAudio  = in_array($ext, ['mp3', 'wav', 'm4a']);
    $isOffice = in_array($ext, ['doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx']);

    [$typeLabel, $typeIcon] = match (true) {
        $isPdf                                   => ['PDF', 'fa-file-pdf'],
        in_array($ext, ['doc', 'docx'])          => ['Dokumen Word', 'fa-file-word'],
        in_array($ext, ['ppt', 'pptx'])          => ['Presentasi', 'fa-file-powerpoint'],
        in_array($ext, ['xls', 'xlsx', 'csv'])   => ['Spreadsheet', 'fa-file-excel'],
        $isImage                                 => ['Gambar', 'fa-file-image'],
        $isVideo                                 => ['Video', 'fa-file-video'],
        $isAudio                                 => ['Audio', 'fa-file-audio'],
        in_array($ext, ['zip', 'rar', '7z'])     => ['Arsip', 'fa-file-zipper'],
        default                                  => [strtoupper($ext) ?: 'File', 'fa-file'],
    };

    // ---------- Video YouTube ----------
    // Mendukung: watch?v=, youtu.be/, /embed/, /shorts/, /live/
    $youtubeId = null;
    if ($materi->video_url) {
        preg_match(
            '~(?:youtu\.be/|youtube(?:-nocookie)?\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/|live/|v/))([A-Za-z0-9_-]{11})~',
            $materi->video_url,
            $m
        );
        $youtubeId = $m[1] ?? null;
    }

    $backUrl = Route::has('siswa.materi') ? route('siswa.materi') : url()->previous();
@endphp

<div class="space-y-6">

    <a href="{{ $backUrl }}"
        class="inline-flex items-center gap-2 text-sm font-medium text-brand-600 hover:text-brand-700 transition">
        <i class="fa-solid fa-arrow-left text-xs"></i> Kembali ke daftar materi
    </a>

    <div class="grid items-start gap-6 lg:grid-cols-3">

        {{-- ================= KOLOM UTAMA ================= --}}
        <div class="min-w-0 space-y-6 lg:col-span-2">

            {{-- Judul & deskripsi --}}
            <section class="rounded-2xl border border-brand-100 bg-white p-6 shadow-sm">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-brand-100 px-3 py-1 text-xs font-semibold text-brand-700">
                    <i class="fa-solid fa-book-open text-[10px]"></i> Materi
                </span>

                <h2 class="mt-3 text-2xl font-extrabold leading-tight text-brand-900 sm:text-3xl">
                    {{ $materi->judul }}
                </h2>

                <p class="mt-4 whitespace-pre-line leading-relaxed text-slate-600">
                    {{ filled($materi->deskripsi) ? $materi->deskripsi : 'Tidak ada deskripsi.' }}
                </p>
            </section>

            {{-- Video --}}
            @if ($materi->video_url)
                <section class="overflow-hidden rounded-2xl border border-brand-100 bg-white shadow-sm">
                    <div class="flex items-center justify-between gap-3 border-b border-brand-100 px-5 py-3.5">
                        <h3 class="flex items-center gap-2 font-bold text-brand-900">
                            <span class="grid h-8 w-8 place-items-center rounded-lg bg-brand-100 text-brand-600">
                                <i class="fa-solid fa-circle-play"></i>
                            </span>
                            Video Pembelajaran
                        </h3>

                        <a href="{{ $materi->video_url }}" target="_blank" rel="noopener"
                            class="text-sm font-semibold text-brand-600 transition hover:text-brand-700">
                            Buka di YouTube <i class="fa-solid fa-arrow-up-right-from-square ml-1 text-xs"></i>
                        </a>
                    </div>

                    @if ($youtubeId)
                        <div class="aspect-video w-full bg-slate-900">
                            <iframe
                                class="h-full w-full"
                                src="https://www.youtube-nocookie.com/embed/{{ $youtubeId }}?rel=0"
                                title="Video: {{ $materi->judul }}"
                                loading="lazy"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                referrerpolicy="strict-origin-when-cross-origin"
                                allowfullscreen></iframe>
                        </div>
                    @else
                        {{-- Bukan link YouTube: tampilkan sebagai tautan --}}
                        <div class="p-6 text-center">
                            <p class="text-sm text-slate-500">Video ini tidak dapat ditampilkan langsung di halaman.</p>
                            <a href="{{ $materi->video_url }}" target="_blank" rel="noopener"
                                class="mt-4 inline-flex items-center gap-2 rounded-xl bg-brand-500 px-5 py-2.5 text-sm font-semibold text-white shadow-md shadow-brand-500/30 transition hover:bg-brand-600">
                                <i class="fa-solid fa-play"></i> Tonton Video
                            </a>
                        </div>
                    @endif
                </section>
            @endif

            {{-- File materi --}}
            @if ($fileUrl)
                <section class="overflow-hidden rounded-2xl border border-brand-100 bg-white shadow-sm">
                    <div class="flex flex-col gap-3 border-b border-brand-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex min-w-0 items-center gap-3">
                            <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-brand-100 text-xl text-brand-600">
                                <i class="fa-solid {{ $typeIcon }}"></i>
                            </span>
                            <div class="min-w-0">
                                <p class="truncate font-bold text-brand-900" title="{{ $fileName }}">{{ $fileName }}</p>
                                <p class="text-xs text-slate-500">{{ $typeLabel }}</p>
                            </div>
                        </div>

                        <div class="flex shrink-0 gap-2">
                            <a href="{{ $fileUrl }}" target="_blank" rel="noopener"
                                class="inline-flex items-center gap-2 rounded-xl bg-brand-500 px-4 py-2 text-sm font-semibold text-white shadow-md shadow-brand-500/30 transition hover:bg-brand-600">
                                <i class="fa-solid fa-up-right-from-square text-xs"></i> Buka
                            </a>
                            <a href="{{ $fileUrl }}" download
                                class="inline-flex items-center gap-2 rounded-xl border border-brand-200 bg-white px-4 py-2 text-sm font-semibold text-brand-700 transition hover:bg-brand-50">
                                <i class="fa-solid fa-download text-xs"></i> Unduh
                            </a>
                        </div>
                    </div>

                    {{-- Pratinjau sesuai jenis file --}}
                    @if ($isPdf)
                        <iframe src="{{ $fileUrl }}#toolbar=1&navpanes=0" title="{{ $fileName }}"
                            class="h-[70vh] w-full bg-brand-50" loading="lazy"></iframe>
                        <p class="border-t border-brand-100 bg-brand-50/60 px-5 py-2.5 text-xs text-slate-500">
                            Jika PDF tidak tampil di perangkat Anda, gunakan tombol <strong>Buka</strong> atau <strong>Unduh</strong>.
                        </p>

                    @elseif ($isImage)
                        <div class="bg-brand-50/60 p-4">
                            <img src="{{ $fileUrl }}" alt="{{ $materi->judul }}" loading="lazy"
                                class="mx-auto max-h-[75vh] w-auto rounded-xl object-contain">
                        </div>

                    @elseif ($isVideo)
                        <video controls preload="metadata" class="aspect-video w-full bg-slate-900">
                            <source src="{{ $fileUrl }}" type="video/{{ $ext }}">
                            Browser Anda tidak mendukung pemutar video.
                        </video>

                    @elseif ($isAudio)
                        <div class="bg-brand-50/60 p-6">
                            <audio controls preload="metadata" class="w-full">
                                <source src="{{ $fileUrl }}">
                                Browser Anda tidak mendukung pemutar audio.
                            </audio>
                        </div>

                    @elseif ($isOffice && app()->environment('production'))
                        {{-- Viewer Office hanya jalan jika URL file bisa diakses publik --}}
                        <iframe src="https://view.officeapps.live.com/op/embed.aspx?src={{ urlencode($fileUrl) }}"
                            title="{{ $fileName }}" class="h-[70vh] w-full bg-brand-50" loading="lazy"></iframe>

                    @else
                        <div class="px-6 py-10 text-center">
                            <span class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-brand-100 text-3xl text-brand-500">
                                <i class="fa-solid {{ $typeIcon }}"></i>
                            </span>
                            <p class="mt-4 font-semibold text-brand-900">Pratinjau belum tersedia untuk jenis file ini</p>
                            <p class="mt-1 text-sm text-slate-500">Silakan buka atau unduh file untuk mempelajarinya.</p>
                        </div>
                    @endif
                </section>
            @endif

            {{-- Jika tidak ada file maupun video --}}
            @if (! $fileUrl && ! $materi->video_url)
                <section class="rounded-2xl border border-dashed border-brand-200 bg-white/70 px-6 py-12 text-center">
                    <span class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-brand-100 text-3xl text-brand-500">
                        <i class="fa-regular fa-folder-open"></i>
                    </span>
                    <p class="mt-4 font-semibold text-brand-900">Belum ada berkas atau video</p>
                    <p class="mt-1 text-sm text-slate-500">Guru belum melampirkan file maupun video untuk materi ini.</p>
                </section>
            @endif
        </div>

        {{-- ================= KOLOM SAMPING ================= --}}
        <aside class="space-y-6 lg:sticky lg:top-24">

            {{-- Info materi --}}
            <section class="rounded-2xl border border-brand-100 bg-white p-5 shadow-sm">
                <h3 class="font-bold text-brand-900">Detail Materi</h3>

                <dl class="mt-4 space-y-3 text-sm">
                    @if ($materi->created_at)
                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-slate-500"><i class="fa-regular fa-calendar mr-2 text-brand-400"></i>Diunggah</dt>
                            <dd class="font-medium text-slate-700">{{ $materi->created_at->translatedFormat('d F Y') }}</dd>
                        </div>
                    @endif

                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-slate-500"><i class="fa-regular fa-file mr-2 text-brand-400"></i>Berkas</dt>
                        <dd class="font-medium text-slate-700">{{ $fileUrl ? $typeLabel : 'Tidak ada' }}</dd>
                    </div>

                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-slate-500"><i class="fa-regular fa-circle-play mr-2 text-brand-400"></i>Video</dt>
                        <dd class="font-medium text-slate-700">{{ $materi->video_url ? 'Tersedia' : 'Tidak ada' }}</dd>
                    </div>
                </dl>
            </section>

            {{-- Aksi --}}
            <section class="space-y-3 rounded-2xl border border-brand-100 bg-white p-5 shadow-sm">
                <h3 class="font-bold text-brand-900">Aktivitas</h3>

                <a href="{{ route('materi.diskusi', $materi->id) }}"
                    class="group flex items-center gap-3 rounded-xl border border-brand-100 p-3 transition hover:border-brand-300 hover:bg-brand-50">
                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-brand-100 text-brand-600 transition group-hover:bg-brand-500 group-hover:text-white">
                        <i class="fa-solid fa-comments"></i>
                    </span>
                    <span class="min-w-0 flex-1">
                        <span class="block text-sm font-semibold text-slate-800">Diskusi</span>
                        <span class="block text-xs text-slate-500">Tanya jawab seputar materi</span>
                    </span>
                    <i class="fa-solid fa-chevron-right text-xs text-brand-300 transition group-hover:translate-x-0.5 group-hover:text-brand-500"></i>
                </a>

                <a href="{{ route('materi.absensi', $materi->id) }}"
                    class="group flex items-center gap-3 rounded-xl border border-brand-100 p-3 transition hover:border-brand-300 hover:bg-brand-50">
                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-brand-100 text-brand-600 transition group-hover:bg-brand-500 group-hover:text-white">
                        <i class="fa-solid fa-clipboard-user"></i>
                    </span>
                    <span class="min-w-0 flex-1">
                        <span class="block text-sm font-semibold text-slate-800">Absensi</span>
                        <span class="block text-xs text-slate-500">Catat kehadiranmu</span>
                    </span>
                    <i class="fa-solid fa-chevron-right text-xs text-brand-300 transition group-hover:translate-x-0.5 group-hover:text-brand-500"></i>
                </a>
            </section>
        </aside>
    </div>
</div>