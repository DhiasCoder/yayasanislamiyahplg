<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- HEADER --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-slate-800 mb-2">
                Selamat Datang, {{ Auth::user()->name }}
            </h1>
            <p class="text-slate-600">Semangat belajar hari ini! 🚀</p>
        </div>

        {{-- CARD RINGKASAN --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-600 mb-1">Kelas</p>
                        <p class="text-2xl font-bold text-slate-800">{{ Auth::user()->kelas?->nama_kelas ?? 'Belum ada kelas' }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-600 mb-1">Total Materi</p>
                        <p class="text-2xl font-bold text-slate-800">{{ $totalMateri }}</p>
                    </div>
                    <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-600 mb-1">Total Kuis</p>
                        <p class="text-2xl font-bold text-slate-800">{{ $totalKuis }}</p>
                    </div>
                    <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-600 mb-1">Status</p>
                        <p class="text-2xl font-bold text-slate-800">Aktif</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- LIST MATERI TERBARU --}}
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-800">Materi Terbaru</h2>
                        <p class="text-sm text-slate-500 mt-1">Pelajari materi terbaru</p>
                    </div>
                    <a href="{{ route('siswa.materi') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700 transition-colors">
                        Lihat Semua →
                    </a>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse($materi as $m)
                    <div class="px-6 py-4 hover:bg-slate-50 transition-colors group">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <h3 class="font-medium text-slate-800 mb-1 group-hover:text-blue-600 transition-colors">
                                    {{ $m->judul }}
                                </h3>
                                <p class="text-sm text-slate-500 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    {{ $m->created_at?->format('d M Y') ?? '-' }}
                                </p>
                            </div>
                            <a href="#" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors whitespace-nowrap">
                                Buka Materi
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="px-6 py-12 text-center">
                        <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <p class="text-slate-600 font-medium">Belum ada materi.</p>
                        <p class="text-sm text-slate-500 mt-1">Materi akan muncul di sini</p>
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- LIST KUIS TERBARU --}}
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-800">Kuis Terbaru</h2>
                        <p class="text-sm text-slate-500 mt-1">Uji pemahamanmu</p>
                    </div>
                    <a href="#" class="text-sm font-medium text-blue-600 hover:text-blue-700 transition-colors">
                        Lihat Semua →
                    </a>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse($kuis as $k)
                    <div class="px-6 py-4 hover:bg-slate-50 transition-colors group">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <h3 class="font-medium text-slate-800 mb-1 group-hover:text-emerald-600 transition-colors">
                                    {{ $k->judul }}
                                </h3>
                                <p class="text-sm text-slate-500 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    {{ $k->created_at?->format('d M Y') ?? '-' }}
                                </p>
                            </div>
                            @if ($k->questions->count() > 0)
                            {{-- Tombol Normal --}}
                            <a href="{{ route('murid.quiz.do', $k->id) }}"
                                class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors whitespace-nowrap">
                                Mulai Kuis
                            </a>
                            @else
                            {{-- Tombol Disabled --}}
                            <button disabled
                                class="px-4 py-2 bg-gray-400 text-white text-sm font-medium rounded-lg cursor-not-allowed whitespace-nowrap">
                                Kuis Belum Dimulai
                            </button>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="px-6 py-12 text-center">
                        <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                        </div>
                        <p class="text-slate-600 font-medium">Belum ada kuis.</p>
                        <p class="text-sm text-slate-500 mt-1">Kuis akan muncul di sini</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>