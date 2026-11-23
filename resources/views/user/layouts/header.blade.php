<header class="bg-white border-b border-gray-200 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="h-16 flex items-center justify-between">

            <a href="{{ url('/') }}" class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center">
                    <span class="text-white font-bold text-lg">S</span>
                </div>

                <div>
                    <h1 class="font-bold text-gray-900 text-lg">
                        Sistem Informasi Sekolah
                    </h1>
                    <p class="text-xs text-gray-500">
                        Penerimaan Peserta Didik Baru
                    </p>
                </div>
            </a>

            <nav class="hidden sm:flex items-center gap-6 text-sm font-medium">
                <a href="{{ url('/') }}"
                    class="text-gray-600 hover:text-indigo-600 transition">
                    Beranda
                </a>

                <a href="{{ route('ppdb.cek-status') }}"
                    class="text-gray-600 hover:text-indigo-600 transition">
                    Cek Status Pendaftaran
                </a>
            </nav>

        </div>
    </div>
</header>