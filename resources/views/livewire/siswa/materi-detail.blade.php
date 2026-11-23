<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 py-8">
    @php
    use Illuminate\Support\Str;

    function getYoutubeId($url) {
    if (!$url) return null;

    // watch?v=xxxx
    if (Str::contains($url, 'watch?v=')) {
    return Str::before(Str::after($url, 'v='), '&');
    }

    // youtu.be/xxxx
    if (Str::contains($url, 'youtu.be/')) {
    return Str::after($url, 'youtu.be/');
    }

    // embed/xxxx
    if (Str::contains($url, 'embed/')) {
    return Str::after($url, 'embed/');
    }

    return null;
    }

    $videoId = getYoutubeId($materi->video_url);

    $path = asset('storage/' . $materi->file_path);

    $isPdf = Str::endsWith($materi->file_path, '.pdf');
    $isOffice = Str::endsWith($materi->file_path, [
    '.doc', '.docx', '.xls', '.xlsx', '.ppt', '.pptx'
    ]);
    @endphp

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- Tombol Kembali --}}
        <div>
            <a href="{{ route('murid.dashboard') }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-lg hover:bg-slate-50 hover:border-slate-300 transition-all shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span class="font-medium">Kembali</span>
            </a>
        </div>

        {{-- Header dengan Judul --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="h-2 bg-gradient-to-r from-blue-500 to-blue-600"></div>
            <div class="p-6">
                <div class="flex items-start gap-4">
                    <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h1 class="text-3xl font-bold text-slate-800 mb-2">{{ $materi->judul }}</h1>
                        <p class="text-slate-600">Pelajari materi ini dengan seksama</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Info Cards Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 mb-1">Guru Pengajar</p>
                        <p class="font-semibold text-slate-800">{{ $materi->guru->name }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 mb-1">Kelas</p>
                        <p class="font-semibold text-slate-800">{{ $materi->kelas->nama_kelas }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 {{ $materi->is_active ? 'bg-green-100' : 'bg-red-100' }} rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 {{ $materi->is_active ? 'text-green-600' : 'text-red-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 mb-1">Status Materi</p>
                        <p class="font-semibold {{ $materi->is_active ? 'text-green-600' : 'text-red-600' }}">
                            {{ $materi->is_active ? 'Aktif' : 'Tidak Aktif' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Deskripsi --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 bg-slate-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                    </svg>
                </div>
                <h2 class="text-xl font-semibold text-slate-800">Deskripsi</h2>
            </div>
            <div class="bg-slate-50 rounded-lg p-4">
                <p class="text-slate-700 leading-relaxed">{{ $materi->deskripsi }}</p>
            </div>
        </div>

        {{-- Video Pembelajaran --}}
        @if ($videoId)
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h2 class="text-xl font-semibold text-slate-800">Video Pembelajaran</h2>
            </div>

            <div class="aspect-video w-full rounded-xl overflow-hidden shadow-md border border-slate-200">
                <iframe
                    src="https://www.youtube.com/embed/{{ $videoId }}"
                    class="w-full h-full"
                    frameborder="0"
                    allowfullscreen>
                </iframe>
            </div>
        </div>
        @endif

        {{-- Dokumen Materi --}}
        @if ($materi->file_path)
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h2 class="text-xl font-semibold text-slate-800">Dokumen Materi</h2>
                </div>

                <a href="{{ $path }}" download
                    class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    <span class="font-medium">Download</span>
                </a>
            </div>

            {{-- Preview PDF --}}
            @if ($isPdf)
            <div class="rounded-lg overflow-hidden border border-slate-200 shadow-inner">
                <iframe
                    src="{{ $path }}"
                    class="w-full h-[600px]">
                </iframe>
            </div>
            @endif

            {{-- Preview DOCX / XLSX / PPTX --}}
            @if ($isOffice)
            <div class="rounded-lg overflow-hidden border border-slate-200 shadow-inner">
                <iframe
                    src="https://view.officeapps.live.com/op/embed.aspx?src={{ urlencode($path) }}"
                    class="w-full h-[600px]">
                </iframe>
            </div>
            @endif
        </div>
        @endif
        @livewire('murid.materi-diskusi', ['materi_id' => $materi->id])
    </div>
</div>