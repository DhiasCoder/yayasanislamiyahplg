<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 p-6">
    <div class="max-w-7xl mx-auto">
        {{-- Header --}}
        <div class="mb-8">
            <div class="flex items-center gap-4 mb-4">
                <a href="{{ route('murid.dashboard') }}"
                    class="flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-lg hover:bg-slate-50 hover:border-slate-300 transition-all shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span class="font-medium">Kembali</span>
                </a>
            </div>
            <h1 class="text-3xl font-bold text-slate-800 mb-2">Daftar Kuis</h1>
            <p class="text-slate-600">Uji pemahamanmu dengan mengerjakan kuis</p>
        </div>

        {{-- Search Bar --}}
        <div class="mb-6">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" wire:model.live="search"
                    class="w-full pl-12 pr-4 py-3 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all shadow-sm"
                    placeholder="Cari kuis berdasarkan judul atau kelas..." />
            </div>
        </div>

        {{-- Grid Kuis --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($quizzes as $item)
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-lg hover:border-emerald-200 transition-all duration-300 group">

                {{-- Header Card dengan Gradient --}}
                <div class="h-2 bg-gradient-to-r from-emerald-500 to-emerald-600"></div>

                <div class="p-6">
                    {{-- Icon & Badge --}}
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center group-hover:bg-emerald-200 transition-colors">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                        </div>
                        <span class="px-3 py-1 bg-slate-100 text-slate-700 text-xs font-medium rounded-full">
                            {{ $item->kelas->nama }}
                        </span>
                    </div>

                    {{-- Judul --}}
                    <h3 class="text-lg font-semibold text-slate-800 mb-4 line-clamp-2 group-hover:text-emerald-600 transition-colors">
                        {{ $item->judul }}
                    </h3>

                    {{-- Info Kuis --}}
                    <div class="flex items-center gap-4 mb-6">
                        <div class="flex items-center gap-2 text-sm text-slate-600">
                            <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500">Total Soal</p>
                                <p class="font-semibold text-slate-800">{{ $item->questions->count() }}</p>
                            </div>
                        </div>
                    </div>

                    @if ($item->questions->count() > 0)
                    {{-- Tombol Normal --}}
                    <a href="{{ route('murid.quiz.do', $item->id) }}"
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
            <div class="col-span-full">
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-12 text-center">
                    <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-700 mb-2">Tidak ada kuis ditemukan</h3>
                    <p class="text-slate-500">Coba ubah kata kunci pencarian Anda</p>
                </div>
            </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="mt-8">
            {{ $quizzes->links() }}
        </div>

    </div>
</div>