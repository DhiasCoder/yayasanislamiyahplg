{{--
    Variabel opsional dari halaman:
    $title    : judul halaman
    $subtitle : kalimat pendek di bawah judul
    $actions  : slot untuk tombol di sisi kanan judul
--}}
<main id="mainContent"
    class="lg:ml-64 min-h-[calc(100vh-4rem)] flex flex-col transition-[margin] duration-300 ease-in-out">

    <div class="flex-1 px-4 sm:px-6 lg:px-8 py-6 pb-28 lg:pb-8">
        <div class="max-w-7xl mx-auto">

            <!-- Judul halaman -->
            <div class="mb-6 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
                <div>
                    <nav class="text-xs sm:text-sm text-slate-500 flex items-center gap-2" aria-label="Breadcrumb">
                        <a href="{{ Route::has('siswa.dashboard') ? route('siswa.dashboard') : url('/') }}"
                            class="hover:text-brand-600 transition">
                            <i class="fa-solid fa-house text-brand-400 mr-1"></i>Beranda
                        </a>
                        <i class="fa-solid fa-chevron-right text-[9px] text-brand-300"></i>
                        <span class="text-brand-700 font-medium">{{ $title ?? 'Dashboard' }}</span>
                    </nav>

                    <h1 class="mt-1.5 text-2xl sm:text-3xl font-extrabold text-brand-900">{{ $title ?? 'Dashboard' }}</h1>

                    @isset($subtitle)
                    <p class="mt-1 text-sm text-slate-500">{{ $subtitle }}</p>
                    @endisset
                </div>

                @isset($actions)
                <div class="flex items-center gap-2">{{ $actions }}</div>
                @endisset
            </div>

            <!-- Isi halaman -->
            {{ $slot }}
        </div>
    </div>

    @include('murid.layouts.footer')
</main>