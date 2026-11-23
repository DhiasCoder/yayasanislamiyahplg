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
            <h1 class="text-3xl font-bold text-slate-800 mb-2">Daftar Materi</h1>
            <p class="text-slate-600">Jelajahi semua materi pembelajaran yang tersedia</p>
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
                    class="w-full pl-12 pr-4 py-3 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all shadow-sm"
                    placeholder="Cari materi berdasarkan judul, kelas, atau guru..." />
            </div>
        </div>
        {{-- Grid Materi --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($materi as $item)
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-lg hover:border-blue-200 transition-all duration-300 group">

                {{-- Header Card dengan Gradient --}}
                <div class="h-2 bg-gradient-to-r from-blue-500 to-blue-600"></div>

                <div class="p-6">
                    {{-- Icon & Badge --}}
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <span class="px-3 py-1 bg-slate-100 text-slate-700 text-xs font-medium rounded-full">
                            {{ $item->kelas->nama }}
                        </span>
                    </div>

                    {{-- Judul --}}
                    <h3 class="text-lg font-semibold text-slate-800 mb-3 line-clamp-2 group-hover:text-blue-600 transition-colors">
                        {{ $item->judul }}
                    </h3>

                    {{-- Info Guru --}}
                    <div class="flex items-center gap-2 text-sm text-slate-600 mb-6">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span class="truncate">{{ $item->guru->name ?? 'Guru tidak ditemukan' }}</span>
                    </div>
                    {{-- Button --}}
                    <a href="{{ route('murid.materi.detail', $item->id) }}"
                        class="block w-full px-4 py-2.5 bg-blue-600 text-white text-center font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-sm hover:shadow-md">
                        Lihat Materi
                    </a>
                </div>
            </div>
            @empty
            <div class="col-span-full">
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-12 text-center">
                    <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-700 mb-2">Tidak ada materi ditemukan</h3>
                    <p class="text-slate-500">Coba ubah kata kunci pencarian Anda</p>
                </div>
            </div>
            @endforelse
        </div>
        {{-- Pagination --}}
        <div class="mt-8">
            {{ $materi->links() }}
        </div>

    </div>
</div>