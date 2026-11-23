<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Yayasan Pendidikan') }} - Beranda</title>
    <meta name="description" content="Profil Yayasan beserta unit pendidikan SMP, SMA, dan SMK. Akses SIMAK dan E-Learning.">

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif']
                    },
                    colors: {
                        brand: {
                            50: '#f2faf4',
                            100: '#e1f4e6',
                            200: '#c3e8cd',
                            300: '#95d5a8',
                            400: '#5fba7d',
                            500: '#3a9d5d',
                            600: '#2a7e49',
                            700: '#23653c',
                            800: '#1f5132',
                            900: '#1a432b',
                        }
                    }
                }
            }
        }
    </script>
</head>

<body class="font-sans text-slate-700 bg-white antialiased">

    {{-- ================= NAVBAR ================= --}}
    <header class="fixed top-0 inset-x-0 z-50 bg-white/85 backdrop-blur border-b border-brand-100">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="#beranda" class="flex items-center gap-2">
                <span class="w-9 h-9 rounded-xl bg-brand-500 text-white grid place-items-center font-extrabold">Y</span>
                <span class="font-bold text-brand-800 leading-tight">
                    Yayasan <span class="text-brand-500">Pendidikan</span>
                </span>
            </a>

            <ul class="hidden md:flex items-center gap-8 text-sm font-medium">
                <li><a href="#beranda" class="hover:text-brand-600 transition">Beranda</a></li>
                <li><a href="#profil" class="hover:text-brand-600 transition">Profil</a></li>
                <li><a href="#unit" class="hover:text-brand-600 transition">Unit Pendidikan</a></li>
                <li><a href="#layanan" class="hover:text-brand-600 transition">Layanan</a></li>
                <li><a href="#kontak" class="hover:text-brand-600 transition">Kontak</a></li>
            </ul>

            <div class="hidden md:flex items-center gap-3">
                @if (Route::has('login'))
                @auth
                <a href="{{ url('/login') }}" class="px-5 py-2 rounded-full bg-brand-500 text-white text-sm font-semibold hover:bg-brand-600 transition">Dashboard</a>
                @else
                <a href="{{ url('/login') }}" class="px-5 py-2 rounded-full bg-brand-500 text-white text-sm font-semibold hover:bg-brand-600 transition shadow-sm shadow-brand-500/30">Masuk</a>
                @endauth
                @endif
            </div>

            <button id="menu-btn" type="button" aria-label="Buka menu"
                class="md:hidden p-2 rounded-lg text-brand-700 hover:bg-brand-50">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </nav>

        <div id="mobile-menu" class="hidden md:hidden border-t border-brand-100 bg-white">
            <ul class="px-4 py-3 space-y-1 text-sm font-medium">
                <li><a href="#beranda" class="block px-3 py-2 rounded-lg hover:bg-brand-50">Beranda</a></li>
                <li><a href="#profil" class="block px-3 py-2 rounded-lg hover:bg-brand-50">Profil</a></li>
                <li><a href="#unit" class="block px-3 py-2 rounded-lg hover:bg-brand-50">Unit Pendidikan</a></li>
                <li><a href="#layanan" class="block px-3 py-2 rounded-lg hover:bg-brand-50">Layanan</a></li>
                <li><a href="#kontak" class="block px-3 py-2 rounded-lg hover:bg-brand-50">Kontak</a></li>
                @if (Route::has('login'))
                <li class="pt-2">
                    @auth
                    <a href="{{ url('/dashboard') }}" class="block text-center px-4 py-2.5 rounded-full bg-brand-500 text-white font-semibold">Dashboard</a>
                    @else
                    <a href="{{ route('login') }}" class="block text-center px-4 py-2.5 rounded-full bg-brand-500 text-white font-semibold">Masuk</a>
                    @endauth
                </li>
                @endif
            </ul>
        </div>
    </header>

    {{-- ================= HERO ================= --}}
    <section id="beranda" class="relative pt-28 pb-20 lg:pt-36 lg:pb-28 overflow-hidden bg-gradient-to-b from-brand-50 via-white to-white">
        <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-brand-200/50 blur-3xl"></div>
        <div class="absolute top-40 -left-24 w-72 h-72 rounded-full bg-brand-100 blur-3xl"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-2 gap-12 items-center">
            <div class="text-center lg:text-left">
                <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-brand-100 text-brand-700 text-xs font-semibold">
                    <span class="w-2 h-2 rounded-full bg-brand-500 animate-pulse"></span>
                    Penerimaan Peserta Didik Baru {{ date('Y') }}/{{ date('Y') + 1 }}
                </span>

                <h1 class="mt-5 text-4xl sm:text-5xl lg:text-6xl font-extrabold text-brand-900 leading-tight">
                    Membangun Generasi <span class="text-brand-500">Unggul</span>, Berakhlak &amp; Berprestasi
                </h1>

                <p class="mt-5 text-base sm:text-lg text-slate-600 max-w-xl mx-auto lg:mx-0">
                    Yayasan kami menaungi jenjang SMP, SMA, dan SMK dengan sistem akademik terpadu
                    melalui layanan SIMAK dan E-Learning yang mudah diakses kapan saja.
                </p>

                <div class="mt-8 flex flex-col sm:flex-row gap-3 justify-center lg:justify-start">
                    @if (Route::has('login'))
                    <a href="{{ route('login') }}" class="px-7 py-3 rounded-full bg-brand-500 text-white font-semibold hover:bg-brand-600 transition shadow-lg shadow-brand-500/30">
                        Masuk SIMAK &amp; E-Learning
                    </a>
                    @endif
                    <a href="#unit" class="px-7 py-3 rounded-full bg-white text-brand-700 font-semibold border border-brand-200 hover:bg-brand-50 transition">
                        Lihat Unit Pendidikan
                    </a>
                </div>

                <dl class="mt-10 grid grid-cols-3 gap-4 max-w-md mx-auto lg:mx-0">
                    <div>
                        <dt class="text-xs text-slate-500">Unit Sekolah</dt>
                        <dd class="text-2xl font-extrabold text-brand-700">3</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-500">Siswa Aktif</dt>
                        <dd class="text-2xl font-extrabold text-brand-700">1.500+</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-500">Tenaga Pendidik</dt>
                        <dd class="text-2xl font-extrabold text-brand-700">120+</dd>
                    </div>
                </dl>
            </div>

            {{-- Ilustrasi --}}
            <div class="relative mx-auto w-full max-w-md lg:max-w-none">
                <div class="relative rounded-[2rem] bg-gradient-to-br from-brand-200 to-brand-400 p-6 sm:p-8 shadow-2xl shadow-brand-500/20">
                    <div class="rounded-2xl bg-white p-6 space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-brand-100 text-brand-600 grid place-items-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0v7m-6-4.5V12" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-bold text-brand-900">Sistem Akademik Terpadu</p>
                                <p class="text-xs text-slate-500">Satu akun untuk semua layanan</p>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between rounded-xl bg-brand-50 px-4 py-3">
                                <span class="text-sm font-medium">Nilai &amp; Rapor</span>
                                <span class="text-xs px-2 py-1 rounded-full bg-brand-500 text-white">SIMAK</span>
                            </div>
                            <div class="flex items-center justify-between rounded-xl bg-brand-50 px-4 py-3">
                                <span class="text-sm font-medium">Materi &amp; Tugas Online</span>
                                <span class="text-xs px-2 py-1 rounded-full bg-brand-500 text-white">E-Learning</span>
                            </div>
                            <div class="flex items-center justify-between rounded-xl bg-brand-50 px-4 py-3">
                                <span class="text-sm font-medium">Absensi &amp; Jadwal</span>
                                <span class="text-xs px-2 py-1 rounded-full bg-brand-500 text-white">SIMAK</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="hidden sm:block absolute -bottom-5 -left-5 bg-white rounded-2xl shadow-xl px-5 py-3 border border-brand-100">
                    <p class="text-xs text-slate-500">Akses</p>
                    <p class="font-bold text-brand-700">24/7 Online</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ================= PROFIL YAYASAN ================= --}}
    <section id="profil" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl mx-auto text-center">
                <p class="text-sm font-semibold text-brand-500 uppercase tracking-wider">Profil Yayasan</p>
                <h2 class="mt-2 text-3xl sm:text-4xl font-extrabold text-brand-900">Tentang Kami</h2>
                <p class="mt-4 text-slate-600">
                    Kami berkomitmen menghadirkan pendidikan berkualitas yang berlandaskan nilai
                    keimanan, karakter, dan keterampilan untuk masa depan peserta didik.
                </p>
            </div>

            <div class="mt-12 grid lg:grid-cols-2 gap-8">
                <div class="rounded-3xl bg-brand-50 p-8">
                    <h3 class="text-xl font-bold text-brand-800">Sejarah Singkat</h3>
                    <p class="mt-3 text-slate-600 leading-relaxed">
                        Yayasan didirikan dengan semangat mencerdaskan kehidupan bangsa. Berawal dari satu
                        lembaga pendidikan, kini berkembang menaungi tiga jenjang: SMP, SMA, dan SMK
                        dengan fasilitas dan tenaga pendidik yang terus ditingkatkan.
                    </p>
                    <p class="mt-3 text-slate-500 text-sm italic">
                        *Silakan sesuaikan teks ini dengan sejarah resmi yayasan.
                    </p>
                </div>

                <div class="grid sm:grid-cols-2 gap-6">
                    <div class="rounded-3xl border border-brand-100 p-6 hover:shadow-lg hover:shadow-brand-100 transition">
                        <div class="w-11 h-11 rounded-xl bg-brand-100 text-brand-600 grid place-items-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.5 12s3.5-7 9.5-7 9.5 7 9.5 7-3.5 7-9.5 7-9.5-7-9.5-7z" />
                            </svg>
                        </div>
                        <h3 class="mt-4 font-bold text-brand-800">Visi</h3>
                        <p class="mt-2 text-sm text-slate-600">
                            Menjadi yayasan pendidikan terpercaya yang melahirkan generasi cerdas, berkarakter, dan berdaya saing.
                        </p>
                    </div>

                    <div class="rounded-3xl border border-brand-100 p-6 hover:shadow-lg hover:shadow-brand-100 transition">
                        <div class="w-11 h-11 rounded-xl bg-brand-100 text-brand-600 grid place-items-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="mt-4 font-bold text-brand-800">Misi</h3>
                        <ul class="mt-2 text-sm text-slate-600 space-y-1 list-disc list-inside">
                            <li>Pembelajaran berkualitas &amp; modern</li>
                            <li>Penguatan akhlak &amp; karakter</li>
                            <li>Pemanfaatan teknologi pendidikan</li>
                        </ul>
                    </div>

                    <div class="sm:col-span-2 rounded-3xl bg-gradient-to-r from-brand-500 to-brand-400 p-6 text-white">
                        <h3 class="font-bold text-lg">Nilai-Nilai Kami</h3>
                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach (['Religius', 'Disiplin', 'Jujur', 'Mandiri', 'Berprestasi', 'Peduli'] as $nilai)
                            <span class="px-3 py-1 rounded-full bg-white/20 text-sm font-medium">{{ $nilai }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ================= UNIT PENDIDIKAN ================= --}}
    <section id="unit" class="py-20 bg-brand-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl mx-auto text-center">
                <p class="text-sm font-semibold text-brand-500 uppercase tracking-wider">Unit Pendidikan</p>
                <h2 class="mt-2 text-3xl sm:text-4xl font-extrabold text-brand-900">SMP, SMA &amp; SMK</h2>
                <p class="mt-4 text-slate-600">Tiga jenjang pendidikan di bawah naungan yayasan.</p>
            </div>

            @php
            $units = [
            [
            'nama' => 'SMP',
            'judul' => 'Sekolah Menengah Pertama',
            'desc' => 'Fondasi karakter dan literasi dasar untuk siswa dalam masa transisi menuju remaja yang mandiri.',
            'fitur' => ['Kurikulum Merdeka', 'Pembinaan Karakter & Ibadah', 'Ekstrakurikuler Beragam'],
            'url' => '#',
            ],
            [
            'nama' => 'SMA',
            'judul' => 'Sekolah Menengah Atas',
            'desc' => 'Persiapan akademik menuju perguruan tinggi dengan peminatan IPA, IPS, dan pengembangan potensi siswa.',
            'fitur' => ['Peminatan IPA & IPS', 'Bimbingan Masuk PTN', 'Olimpiade & Riset'],
            'url' => '#',
            ],
            [
            'nama' => 'SMK',
            'judul' => 'Sekolah Menengah Kejuruan',
            'desc' => 'Pendidikan vokasi siap kerja dengan kompetensi keahlian dan kerja sama industri.',
            'fitur' => ['Praktik Kerja Lapangan', 'Sertifikasi Kompetensi', 'Kemitraan Dunia Industri'],
            'url' => '#',
            ],
            ];
            @endphp

            <div class="mt-12 grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($units as $u)
                <article class="group bg-white rounded-3xl p-7 border border-brand-100 hover:-translate-y-1 hover:shadow-xl hover:shadow-brand-200/60 transition duration-300 flex flex-col">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-brand-400 to-brand-600 text-white grid place-items-center text-lg font-extrabold shadow-md shadow-brand-500/30">
                        {{ $u['nama'] }}
                    </div>
                    <h3 class="mt-5 text-xl font-bold text-brand-900">{{ $u['judul'] }}</h3>
                    <p class="mt-2 text-sm text-slate-600 leading-relaxed">{{ $u['desc'] }}</p>

                    <ul class="mt-5 space-y-2 text-sm flex-1">
                        @foreach ($u['fitur'] as $f)
                        <li class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-brand-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>{{ $f }}</span>
                        </li>
                        @endforeach
                    </ul>

                    <a href="{{ $u['url'] }}" class="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-brand-600 group-hover:text-brand-700">
                        Selengkapnya
                        <svg class="w-4 h-4 group-hover:translate-x-1 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6l6 6-6 6" />
                        </svg>
                    </a>
                </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ================= LAYANAN ================= --}}
    <section id="layanan" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl mx-auto text-center">
                <p class="text-sm font-semibold text-brand-500 uppercase tracking-wider">Layanan Digital</p>
                <h2 class="mt-2 text-3xl sm:text-4xl font-extrabold text-brand-900">SIMAK &amp; E-Learning</h2>
            </div>

            <div class="mt-12 grid md:grid-cols-2 gap-8">
                <div class="rounded-3xl bg-gradient-to-br from-brand-50 to-brand-100 p-8 border border-brand-100">
                    <span class="px-3 py-1 rounded-full bg-brand-500 text-white text-xs font-semibold">SIMAK</span>
                    <h3 class="mt-4 text-2xl font-bold text-brand-900">Sistem Informasi Akademik</h3>
                    <p class="mt-2 text-slate-600">Kelola dan pantau data akademik secara transparan.</p>
                    <ul class="mt-5 grid grid-cols-2 gap-3 text-sm">
                        @foreach (['Data Siswa', 'Jadwal Pelajaran', 'Absensi', 'Nilai & Rapor', 'Pembayaran', 'Pengumuman'] as $i)
                        <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-brand-500"></span>{{ $i }}</li>
                        @endforeach
                    </ul>
                </div>

                <div class="rounded-3xl bg-gradient-to-br from-brand-500 to-brand-600 p-8 text-white">
                    <span class="px-3 py-1 rounded-full bg-white/20 text-xs font-semibold">E-Learning</span>
                    <h3 class="mt-4 text-2xl font-bold">Pembelajaran Daring</h3>
                    <p class="mt-2 text-brand-50">Belajar fleksibel dengan materi dan tugas online.</p>
                    <ul class="mt-5 grid grid-cols-2 gap-3 text-sm">
                        @foreach (['Materi Digital', 'Tugas & Kuis', 'Ujian Online', 'Forum Diskusi', 'Video Pembelajaran', 'Penilaian Otomatis'] as $i)
                        <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-white"></span>{{ $i }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- ================= CTA ================= --}}
    <section class="px-4 sm:px-6 lg:px-8 pb-20 bg-white">
        <div class="max-w-5xl mx-auto rounded-3xl bg-brand-100 px-6 py-12 sm:px-12 text-center">
            <h2 class="text-2xl sm:text-3xl font-extrabold text-brand-900">Siap mengakses layanan akademik?</h2>
            <p class="mt-3 text-slate-600">Masuk menggunakan akun yang telah diberikan oleh sekolah.</p>
            @if (Route::has('login'))
            <a href="{{ route('login') }}" class="mt-6 inline-block px-8 py-3 rounded-full bg-brand-500 text-white font-semibold hover:bg-brand-600 transition shadow-lg shadow-brand-500/30">
                Masuk Sekarang
            </a>
            @endif
        </div>
    </section>

    {{-- ================= FOOTER / KONTAK ================= --}}
    <footer id="kontak" class="bg-brand-900 text-brand-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 grid sm:grid-cols-2 lg:grid-cols-4 gap-10">
            <div class="lg:col-span-2">
                <div class="flex items-center gap-2">
                    <span class="w-9 h-9 rounded-xl bg-brand-500 text-white grid place-items-center font-extrabold">Y</span>
                    <span class="font-bold text-white">Yayasan Pendidikan</span>
                </div>
                <p class="mt-4 text-sm text-brand-200 max-w-sm">
                    Menaungi SMP, SMA, dan SMK dengan komitmen mencetak generasi unggul dan berakhlak mulia.
                </p>
            </div>

            <div>
                <h4 class="font-semibold text-white">Navigasi</h4>
                <ul class="mt-4 space-y-2 text-sm">
                    <li><a href="#profil" class="hover:text-white">Profil</a></li>
                    <li><a href="#unit" class="hover:text-white">Unit Pendidikan</a></li>
                    <li><a href="#layanan" class="hover:text-white">Layanan</a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-semibold text-white">Kontak</h4>
                <ul class="mt-4 space-y-2 text-sm text-brand-200">
                    <li>Jl. Contoh No. 123, Kota Anda</li>
                    <li>(0711) 000-000</li>
                    <li>info@yayasan.sch.id</li>
                </ul>
            </div>
        </div>
        <div class="border-t border-brand-800 py-5 text-center text-xs text-brand-300">
            &copy; {{ date('Y') }} Yayasan Pendidikan. Hak cipta dilindungi.
        </div>
    </footer>

    <script>
        const btn = document.getElementById('menu-btn');
        const menu = document.getElementById('mobile-menu');
        btn.addEventListener('click', () => menu.classList.toggle('hidden'));
        menu.querySelectorAll('a').forEach(a => a.addEventListener('click', () => menu.classList.add('hidden')));
    </script>
</body>

</html>